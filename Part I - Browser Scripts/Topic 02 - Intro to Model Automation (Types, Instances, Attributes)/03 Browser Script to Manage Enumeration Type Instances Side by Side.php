<?php

// {
//   "data": {
//     "script": {
//       "displayName": "Enumeration Dashboard",
//       "relativeName": "enumeration_dashboard",
//       "description": null,
//       "outputType": "BROWSER",
//       "scriptType": "PHP"
//     }
//   }
// }

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.core.js',            array('version' => 'auto', 'relative' => false));
// HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.tiqGraphQL.js',      array('version' => 'auto', 'relative' => false));
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.components.min.js',  array('version' => 'auto', 'relative' => false));
// HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.charts.min.js',      array('version' => 'auto', 'relative' => false));

require_once 'thinkiq_context.php';
$context = new Context();

use Joomla\CMS\Factory;
$user = Factory::getUser();

?>

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

    <div class="row" :style="`max-width:${pageWidth * 0.98}px;`">

        <div class="col-2">
            <div class="card" :style="`height: ${expandTiqEnumTypes ? 450 : activeEnumType ? 150 : 50 }px;`">
                <div class="card-header">
                    Enumeration Type
                    <i :class="`fa-light fa-2xl fa-caret-${expandTiqEnumTypes?'up':'down'} float-end`" style="transform: translateY(13px);" @click="()=>{expandTiqEnumTypes = !expandTiqEnumTypes;}"></i>
                </div>
                <div v-if="activeEnumType" class="card-body">
                    Name: <span v-if="activeEnumType">{{activeEnumType.displayName}}</span></br>
                    Library: <span v-if="activeEnumType">{{activeEnumType.partOf.displayName}}</span></br>
                    Attributes: <span v-if="activeEnumType"> {{FilteredAttributes.length}} of {{activeEnumType.attributes.length}}</span></br>
                </div>
                <div v-if="expandTiqEnumTypes" class="card-body">
                    <hr />
                    Pick from Usage Board</br>
                    <div class="mt-2" :style="`overflow-y: auto; max-height: ${activeEnumType ? 200 : 300}px;`">
                        <template v-for="aEnumTiqType in tiqEnumTypes.filter(x=>x.attributes.length > 0)">
                            <button class="btn btn-sm btn-light" style="width:95%;" @click="OnSelectEnumTypeAsync(aEnumTiqType)">
                                <label class="float-start" data-toggle="tooltip" :title="`Library: ${aEnumTiqType.partOf==null ? '-' : aEnumTiqType.partOf.displayName}`">{{aEnumTiqType.displayName}}</label>
                                <label class="float-end">(x{{aEnumTiqType.attributes.length}})</label>
                            </button></br>
                        </template>
                    </div>
                </div>
            </div>

            <div v-if="activeEnumType" class="card my-2">
                <div class="card-header">
                    Attribute Names
                    <i :class="`fa-light fa-2xl fa-caret-${expandAttributeNames?'up':'down'} float-end`" style="transform: translateY(13px);" @click="()=>{expandAttributeNames = !expandAttributeNames;}"></i>
                    <button class="btn btn-link float-end mx-2" style="transform: translateY(-5px);" @click="()=>{attributeNames.forEach(x=>{x.checked=!x.checked;})}">
                        flip checks
                    </button>
                </div>
                <div class="card-body" :style="`overflow-y: auto; max-height: ${expandAttributeNames?400:100}px;`">
                    <template v-for="aAttributeName in attributeNames">
                        <input type="checkbox" v-model="aAttributeName.checked"/>{{aAttributeName.name}}</br>
                    </template>
                </div>
            </div>

            <div v-if="activeEnumType" class="card my-2">
                <div class="card-header">
                    Data Source
                    <button class="btn btn-link float-end mx-2" style="transform: translateY(-5px);" @click="()=>{attributeDataSources.forEach(x=>{x.checked=!x.checked;})}">
                        flip checks
                    </button>
                </div>
                <div class="card-body">
                    <template v-for="aAttributeDataSource in attributeDataSources">
                        <input type="checkbox" v-model="aAttributeDataSource.checked"/>{{aAttributeDataSource.name}}</br>
                    </template>
                </div>
            </div>

            <div v-if="activeEnumType" class="card my-2">
                <div class="card-header">
                    Instance Names
                    <i :class="`fa-light fa-2xl fa-caret-${expandInstanceNames?'up':'down'} float-end`" style="transform: translateY(13px); cursor: pointer;" @click="()=>{expandInstanceNames = !expandInstanceNames;}"></i>
                    <button class="btn btn-link float-end" style="transform: translateY(-5px);" @click="()=>{instanceNames.forEach(x=>{x.checked=!x.checked;})}">
                        flip checks
                    </button>
                </div>
                <div class="card-body" :style="`overflow-y: auto; max-height: ${expandInstanceNames?400:100}px;`">
                    <template v-for="aAttributeName in instanceNames">
                        <input type="checkbox" v-model="aAttributeName.checked"/>{{aAttributeName.name}}</br>
                    </template>
                </div>
            </div>

            <div v-if="activeEnumType" class="card my-2">
                <div class="card-header">
                    Parent Names 
                    <i :class="`fa-light fa-2xl fa-caret-${expandInstanceParentNames?'up':'down'} float-end`" style="transform: translateY(13px); cursor: pointer;" @click="()=>{expandInstanceParentNames = !expandInstanceParentNames;}"></i>
                    <button class="btn btn-link float-end" style="transform: translateY(-5px);" @click="()=>{instanceParentNames.forEach(x=>{x.checked=!x.checked;})}">
                        flip checks
                    </button>
                </div>
                <div class="card-body" :style="`overflow-y: auto; max-height: ${expandInstanceParentNames?400:100}px;`">
                    <template v-for="aInstanceParentName in instanceParentNames">
                        <input type="checkbox" v-model="aInstanceParentName.checked"/>{{aInstanceParentName.name}}</br>
                    </template>
                </div>
            </div>

            <div v-if="activeEnumType" class="card my-2">
                <div class="card-header">
                    GrandParent Names 
                    <i :class="`fa-light fa-2xl fa-caret-${expandInstanceGrandParentNames?'up':'down'} float-end`" style="transform: translateY(13px); cursor: pointer;" @click="()=>{expandInstanceGrandParentNames = !expandInstanceGrandParentNames;}"></i>
                    <button class="btn btn-link float-end" style="transform: translateY(-5px);" @click="()=>{instanceGrandParentNames.forEach(x=>{x.checked=!x.checked;})}">
                        flip checks
                    </button>
                </div>
                <div class="card-body" :style="`overflow-y: auto; max-height: ${expandInstanceGrandParentNames?400:100}px;`">
                    <template v-for="aInstanceGrandParentName in instanceGrandParentNames">
                        <input type="checkbox" v-model="aInstanceGrandParentName.checked"/>{{aInstanceGrandParentName.name}}</br>
                    </template>
                </div>
            </div>

        </div>

        <div class="col-10">
            <div class="" style="overflow: auto; max-height:800px;">
                <table v-if="activeEnumType" class="table table-sm table-hover">
                    <thead>
                        <tr style="position: sticky; top: 0; z-index: 100; background: white;">
                            <th scope="col" style="position: sticky; left: 0; z-index: 10; background: white;"> </th>
                            <th scope="col" v-for="aAttribute in FilteredAttributes">
                                {{aAttribute.displayName}}
                                <a class="btn btn-link btn-sm" data-toggle="tooltip" :title="`Browse to Attribute ${aAttribute.displayName}`" :href="`./applications/model-explorer?tab=attribute_tab&attribute_id=${aAttribute.id}`" target="_blank">
                                    <i style="transform: translateY(-3px);" class="fa fa-light fa-external-link"></i>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;">Instance</th>
                            <td v-for="aAttribute in FilteredAttributes">
                                {{aAttribute.instanceName}}
                                <a class="btn btn-link btn-sm" data-toggle="tooltip" :title="`Browse to Instance ${aAttribute.instanceName}`" :href="`./applications/model-explorer?tab=instance_tab&instance_id=${aAttribute.onObject.id}`" target="_blank">
                                    <i style="transform: translateY(-3px);" class="fa fa-light fa-external-link"></i>
                                </a>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;">InstanceParent</th>
                            <td v-for="aAttribute in FilteredAttributes">{{aAttribute.instanceParentName}}</th>
                        </tr>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;">InstanceGrandParent</th>
                            <td v-for="aAttribute in FilteredAttributes">{{aAttribute.instanceGrandParentName}}</th>
                        </tr>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;"></th>
                            <td v-for="aAttribute in FilteredAttributes">
                                <button class="btn btn-link btn-sm" data-toggle="tooltip" :title="`copy id: ${aAttribute.id}`" @click="clipboard.writeText(aAttribute.id)">id</button>
                                <button class="btn btn-link btn-sm" data-toggle="tooltip" :title="`copy fqn: ${aAttribute.fqn}`" @click="clipboard.writeText(aAttribute.fqn)">fqn</button>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;"></th>
                            <td v-for="aAttribute in FilteredAttributes">
                            </th>
                        </tr>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;">Data Source</th>
                            <td v-for="aAttribute in FilteredAttributes">
                                {{aAttribute.dataSource}}
                            </th>
                        </tr>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;">Config Value</th>
                            <td v-for="aAttribute in FilteredAttributes">
                                {{aAttribute.enumerationValue==null ? '---' : `${aAttribute.enumerationValue} ("${GetEnumMatch(aAttribute.enumerationValue, aAttribute.enumerationValues)}"")`}}
                            </th>
                        </tr>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;">Current Value</th>
                            <td v-for="aAttribute in FilteredAttributes">
                                {{aAttribute.currentValue==null ? '---' : `${aAttribute.currentValue.value} ("${GetEnumMatch(aAttribute.currentValue.value, aAttribute.enumerationValues)}"")`}}
                                <i v-if="aAttribute.currentValue!=null" class="fa fa-light fa-clock" data-toggle="tooltip" :title="moment(aAttribute.currentValue.timestamp).toISOString()"></t>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;"></th>
                            <td v-for="aAttribute in FilteredAttributes">
                            </th>
                        </tr>
                        <tr v-for="(aEnumerationValue, i) in activeEnumType.defaultEnumerationValues">
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;">
                                {{activeEnumType.defaultEnumerationValues[i]}}: 
                                {{activeEnumType.enumerationNames[i]}}
                            </th>
                            <td v-for="aAttribute in FilteredAttributes">
                                {{aAttribute.enumerationValues[i]}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    var WinDoc = window.document;
    
    var pageWidth = window.screen.width;

    // we need a clipboard so we can copy / paste
    var clipboard = navigator.clipboard;

    var app = createApp({
        // el: "#app",
        data() {
            return {
                moment: moment,
                allowEditMode: false,
                pageWidth: pageWidth,
                clipboard: clipboard,
                pageTitle: "Manage Enumeration Attributes by Enumeration Type Side by Side",
                context:<?php echo json_encode($context)?>,
                user:<?php echo json_encode($user)?>,
                tiqEnumTypes: [],
                expandTiqEnumTypes: true,
                activeEnumType: null,
                attributeNames: [],
                expandAttributeNames: false,
                attributeDataSources: [],
                instanceNames: [],
                expandInstanceNames: false,
                instanceParentNames: [],
                expandInstanceParentNames: false,
                instanceGrandParentNames: [],
                expandInstanceGrandParentNames: false,
            }
        },
        mounted: async function () {
            WinDoc.title = this.pageTitle;
            await this.LoadTiqEnumTypesAsync();
        },
        computed: {
            FilteredAttributes: function(){
                let attributes = [];
                this.activeEnumType.attributes.forEach(aAttribute => {
                    let useAttribute = true;

                    if(! this.attributeNames.find(x=>x.name == aAttribute.displayName).checked){
                        useAttribute = false;
                    }

                    if(! this.attributeDataSources.find(x=>x.name == aAttribute.dataSource).checked){
                        useAttribute = false;
                    }

                    if(! this.instanceNames.find(x=>x.name == aAttribute.instanceName).checked){
                        useAttribute = false;
                    }

                    if(! this.instanceParentNames.find(x=>x.name == aAttribute.instanceParentName).checked){
                        useAttribute = false;
                    }

                    if(! this.instanceGrandParentNames.find(x=>x.name == aAttribute.instanceGrandParentName).checked){
                        useAttribute = false;
                    }

                    if(useAttribute){
                        attributes.push(aAttribute);
                    }
                });
                return attributes;
            },

        },
        methods: {
            GetEnumMatch: function(aValue, aEnumerationValues){
                let aIndex = aEnumerationValues.findIndex(x=>x==aValue);
                let enumMatch= aIndex == -1 ? '---' : this.activeEnumType.enumerationNames[aIndex];
                return enumMatch;
            },
            LoadTiqEnumTypesAsync: async function(){
                let query = `
query q1 {
    enumerationTypes {
        id
        displayName
        defaultEnumerationValues
        enumerationNames
        partOf {
            id
            displayName
        }
        attributes {
            id
            displayName
            fqn
            dataSource
            enumerationValue
            enumerationValues
            currentValue{
                timestamp
                value
            }
            onObject {
                id
                displayName
                parentObject {
                    id
                    displayName
                    parentObject {
                        id
                        displayName
                    }
                }
            }
        }
    }
}
                `;
                let aResponse = await tiqJSHelper.invokeGraphQLAsync(query);
                let tiqEnumTypes = aResponse.data.enumerationTypes;
                this.tiqEnumTypes = tiqEnumTypes.sort((a,b) => a.attributes.length <= b.attributes.length ? 1 : -1);
            },
            OnSelectEnumTypeAsync: function(aEnumType){
                
                let attributeNames = [];
                let attributeDataSources = [];
                let instanceNames = [];
                let instanceParentNames = [];
                let instanceGrandParentNames = [];

                aEnumType.attributes.forEach(aAttribute => {
                    
                    if(attributeNames.filter(x=>x.name == aAttribute.displayName).length == 0){
                        attributeNames.push({
                            name: aAttribute.displayName,
                            checked: true
                        })
                    }

                    if(attributeDataSources.filter(x=>x.name == aAttribute.dataSource).length == 0){
                        attributeDataSources.push({
                            name: aAttribute.dataSource,
                            checked: true
                        })
                    }

                    let instanceName = aAttribute.onObject.displayName;
                    aAttribute.instanceName = instanceName;
                    if(instanceNames.filter(x=>x.name == instanceName).length == 0){
                        instanceNames.push({
                            name: instanceName,
                            checked: true
                        })
                    }

                    let instanceParentName = aAttribute.onObject.parentObject == null ? 'n/a' : aAttribute.onObject.parentObject.displayName;
                    aAttribute.instanceParentName = instanceParentName;
                    if(instanceParentNames.filter(x=>x.name == instanceParentName).length == 0){
                        instanceParentNames.push({
                            name: instanceParentName,
                            checked: true
                        })
                    }

                    let instanceGrandParentName = aAttribute.onObject.parentObject == null ? 'n/a' : aAttribute.onObject.parentObject.parentObject == null ? 'n/a' : aAttribute.onObject.parentObject.parentObject.displayName;;
                    aAttribute.instanceGrandParentName = instanceGrandParentName;
                    if(instanceGrandParentNames.filter(x=>x.name == instanceGrandParentName).length == 0){
                        instanceGrandParentNames.push({
                            name: instanceGrandParentName,
                            checked: true
                        })
                    }

                });
                
                
                this.activeEnumType = aEnumType;

                this.attributeNames = attributeNames.sort((a,b) => a.name > b.name ? 1 : -1);
                this.attributeDataSources = attributeDataSources.sort((a,b) => a.name > b.name ? 1 : -1);
                this.instanceNames = instanceNames.sort((a,b) => a.name > b.name ? 1 : -1);
                this.instanceParentNames = instanceParentNames.sort((a,b) => a.name > b.name ? 1 : -1);
                this.instanceGrandParentNames = instanceGrandParentNames.sort((a,b) => a.name > b.name ? 1 : -1);
                
                this.expandTiqEnumTypes = false;
            }
        },
    })
    .mount('#app');
</script>
