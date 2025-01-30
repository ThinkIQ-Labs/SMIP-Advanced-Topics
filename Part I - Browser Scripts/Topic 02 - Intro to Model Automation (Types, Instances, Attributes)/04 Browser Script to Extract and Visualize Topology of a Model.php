<?php

// {
//   "data": {
//     "script": {
//       "displayName": "Topology Report",
//       "relativeName": "topology_report",
//       "description": "To extract and visualize the topology of a model.",
//       "outputType": "BROWSER",
//       "scriptType": "PHP"
//     }
//   }
// }

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.core.js',            array('version' => 'auto', 'relative' => false));
// HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.tiqGraphQL.js',      array('version' => 'auto', 'relative' => false));
// HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.components.min.js',  array('version' => 'auto', 'relative' => false));
// HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.charts.min.js',      array('version' => 'auto', 'relative' => false));

require_once 'thinkiq_context.php';
$context = new Context();

use Joomla\CMS\Factory;
$user = Factory::getUser();

?>

<script src="https://unpkg.com/@hpcc-js/wasm@2.20.0/dist/graphviz.umd.js"></script>
<script src="https://unpkg.com/d3-graphviz@5.6.0/build/d3-graphviz.js"></script>

<div id="app">

    <div class="row">            
        <div class="col-12">
            <h1 class="pb-2 pt-2" style="font-size:2.5rem; color:#126181;">
                {{pageTitle}}
                <a v-if="true" class="float-end btn btn-sm btn-link mt-2" style="font-size:1rem; color:#126181;" v-bind:href="`/index.php?option=com_modeleditor&view=script&id=${context.std_inputs.script_id}`" target="_blank">source</a>
            </h1>
            <hr style="border-color:#126181; border-width:medium;" />
        </div>   
    </div>

    <div id="graph"></div>

    <table class="table">
        <thead>
            <tr>
            <th scope="col">Count</th>
            <th scope="col">Link Type</th>
            <th scope="col">From Type</th>
            <th scope="col">To Type</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="aLink in ExistingLinksByCountDesc">
                <th scope="row">{{aLink.count}}</th>
                <td>{{aLink.linkTypeName}}</td>
                <td>{{aLink.subjectTypeName}}</td>
                <td>{{aLink.objectTypeName}}</td>
            </tr>
        </tbody>
    </table>

</div>


<script>
    var WinDoc = window.document;
    
    // we need a clipboard so we can copy / paste
    var clipboard = navigator.clipboard;

    var app = createApp({
        // el: "#app",
        data() {
            return {
                clipboard: clipboard,
                pageTitle: "Extract Topology from Model",
                context:<?php echo json_encode($context)?>,
                user:<?php echo json_encode($user)?>,
                types: [],
                objects: [],
                links: [],
                existingLinks: [],
                d3: d3
            }
        },
        mounted: async function () {
            WinDoc.title = this.pageTitle;
            await this.LoadModelAsync();
        },
        computed: {
            ExistingLinksByCountDesc: function(){
                return this.existingLinks.sort((a,b)=>a.count <= b.count ? 1 : -1);
            }
        },
        methods: {

            LoadModelAsync: async function(){

                let query = `
                query q1 {
                    tiqTypes {
                        id
                        displayName
                    }
                    objects(filter: { typeId: { isNull: false } }) {
                        id
                        displayName
                        typeId
                        typeName
                        partOf{
                            typeId
                            typeName
                        }
                    }
                    relationships{
                        relationshipTypeName
                        subjectId
                        objectId
                        relationshipType{
                            displayName
                        }
                    }
                }
                `;

                let aResponse = await tiqJSHelper.invokeGraphQLAsync(query);
                this.types = aResponse.data.tiqTypes;
                this.objects = aResponse.data.objects;
                this.links = aResponse.data.relationships;

                // build contains linkages
                this.objects.forEach(aObject => {
                    let existingLink = this.existingLinks.find(x=>
                        x.subjectTypeRelativeName == (aObject.partOf == null ? "root" : aObject.partOf.typeName) && 
                        x.objectTypeRelativeName == aObject.typeName && 
                        x.linkTypeName == "Contains"
                    );
                    if(existingLink==null){
                        this.existingLinks.push({
                            subjectTypeRelativeName : aObject.partOf == null ? "root" : aObject.partOf.typeName,
                            subjectTypeName : aObject.partOf == null ? "Root" : this.types.find(x=>x.id==aObject.partOf.typeId).displayName,
                            objectTypeRelativeName : aObject.typeName,
                            objectTypeName : this.types.find(x=>x.id==aObject.typeId).displayName,
                            linkTypeName : "Contains",
                            count : 1
                        });
                    } else {
                        existingLink.count++;
                    }
                });

                // build relationship linkages
                this.links.forEach(aRelationship => {
                    let aSubject = this.objects.find(x=>x.id==aRelationship.subjectId);
                    let aObject = this.objects.find(x=>x.id==aRelationship.objectId);
                    let existingLink = this.existingLinks.find(x=>
                        x.subjectTypeRelativeName == aSubject.typeName && 
                        x.objectTypeRelativeName == aObject.typeName && 
                        x.linkTypeName == aRelationship.relationshipType.displayName
                    );
                    if(existingLink==null){
                        this.existingLinks.push({
                            subjectTypeRelativeName : aSubject.typeName,
                            subjectTypeName : this.types.find(x=>x.id==aSubject.typeId).displayName,
                            objectTypeRelativeName : aObject.typeName,
                            objectTypeName : this.types.find(x=>x.id==aObject.typeId).displayName,
                            linkTypeName : aRelationship.relationshipType.displayName,
                            count : 1
                        });
                    } else {
                        existingLink.count++;
                    }
                });

                // build digraph string
                let diagram = "digraph G {";
                this.existingLinks.forEach(aLink => {
                    diagram += `"${aLink.subjectTypeName}" -> "${aLink.objectTypeName}" [label="${aLink.linkTypeName} (${aLink.count})"]`;
                });
                diagram += "}";

                // render diagram
                this.d3.select("#graph")
                    .graphviz()
                        .dot(diagram)
                        .render();


            },
        },
    })
    .mount('#app');
</script>
