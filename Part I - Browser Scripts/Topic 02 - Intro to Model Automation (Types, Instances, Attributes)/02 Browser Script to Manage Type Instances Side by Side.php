<?php

// {
//   "data": {
//     "script": {
//       "displayName": "Instance Dashboard",
//       "relativeName": "instance_dashboard",
//       "description": "To compare instances of types side by side.",
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
            <div class="card" :style="`height: ${expandTiqTypes?600:activeType?150:50}px;`">
                <div class="card-header">
                    Type
                    <i :class="`fa-light fa-2xl fa-caret-${expandTiqTypes?'up':'down'} float-end`" style="transform: translateY(13px);" @click="()=>{expandTiqTypes = !expandTiqTypes;}"></i>
                </div>
                <div v-if="activeType" class="card-body">
                    Name: <span v-if="activeType">{{activeType.displayName}}</span></br>
                    Inherits from: <span v-if="activeType">{{activeType.subTypeOf==null ? 'n/a' : activeType.subTypeOf.displayName}}</span></br>
                    Library: <span v-if="activeType">{{activeType.partOf.displayName}}</span></br>
                    Instances: <span v-if="activeType">{{FilteredInstances.length}} of {{activeType.objectsByTypeId.length}}</span></br>
                </div>
                <div v-if="expandTiqTypes" class="card-body">
                    <span class="float-start mt-2">
                        Pick from Type Tree
                    </span>
                    <span class="float-end me-2">
                        <tree-picker
                            :picker-name='type_picker'
                            display-mode='type'
                            :height='500'
                            content='Pick Type to Explore.'
                            default-expand-levels='0'
                            :default-root-node-fqn='null'
                            :default-root-node-id='null'
                            :prune-branches='false'
                            :branch-types='["organization","place,equipment","gateway","connector","opcua_object","object","material","person","attribute"]'
                            :leaf-types='["attribute","tag"]'
                            @on-select="OnSelectTypeAsync"
                        ></tree-picker>
                    </span><br />
                    <hr />
                    Pick from Usage Board</br>
                    <div class="mt-2" :style="`overflow-y: auto; max-height: ${activeType ? 350 : 450}px;`">
                        <template v-for="aTiqType in tiqTypes.filter(x=>x.objectsByTypeId.length > 0)">
                            <button class="btn btn-sm btn-light" style="width:95%;" @click="OnSelectTypeAsync({id: aTiqType.id})">
                                <label class="float-start" data-toggle="tooltip" :title="`Library: ${aTiqType.partOf==null ? '-' : aTiqType.partOf.displayName}`">{{aTiqType.displayName}}</label>
                                <label class="float-end">(x{{aTiqType.objectsByTypeId.length}})</label>
                            </button></br>
                        </template>
                    </div>
                </div>
            </div>

            <div v-if="activeType" class="card my-2">
                <div class="card-header">
                    Instance Names
                    <i :class="`fa-light fa-2xl fa-caret-${expandInstanceNames?'up':'down'} float-end`" style="transform: translateY(13px);" @click="()=>{expandInstanceNames = !expandInstanceNames;}"></i>
                    <button class="btn btn-link float-end mx-2" style="transform: translateY(-5px);" @click="()=>{instanceNames.forEach(x=>{x.checked=!x.checked;})}">
                        flip checks
                    </button>
                </div>
                <div class="card-body" :style="`overflow-y: auto; max-height: ${expandInstanceNames?400:100}px;`">
                    <template v-for="aInstanceName in instanceNames">
                        <input type="checkbox" v-model="aInstanceName.checked"/>{{aInstanceName.name}}</br>
                    </template>
                </div>
            </div>

            <div v-if="activeType" class="card my-2">
                <div class="card-header">
                    Parent Names
                    <i :class="`fa-light fa-2xl fa-caret-${expandParentNames?'up':'down'} float-end`" style="transform: translateY(13px); cursor: pointer;" @click="()=>{expandParentNames = !expandParentNames;}"></i>
                    <button class="btn btn-link float-end" style="transform: translateY(-5px);" @click="()=>{parentNames.forEach(x=>{x.checked=!x.checked;})}">
                        flip checks
                    </button>
                </div>
                <div class="card-body" :style="`overflow-y: auto; max-height: ${expandParentNames?400:100}px;`">
                    <template v-for="aParentName in parentNames">
                        <input type="checkbox" v-model="aParentName.checked"/>{{aParentName.name}}</br>
                    </template>
                </div>
            </div>

            <div v-if="activeType" class="card my-2">
                <div class="card-header">
                    Grandparent Names 
                    <i :class="`fa-light fa-2xl fa-caret-${expandGrandParentNames?'up':'down'} float-end`" style="transform: translateY(13px); cursor: pointer;" @click="()=>{expandGrandParentNames = !expandGrandParentNames;}"></i>
                    <button class="btn btn-link float-end" style="transform: translateY(-5px);" @click="()=>{grandParentNames.forEach(x=>{x.checked=!x.checked;})}">
                        flip checks
                    </button>
                </div>
                <div class="card-body" :style="`overflow-y: auto; max-height: ${expandGrandParentNames?400:100}px;`">
                    <template v-for="aGrandParentName in grandParentNames">
                        <input type="checkbox" v-model="aGrandParentName.checked"/>{{aGrandParentName.name}}</br>
                    </template>
                </div>
            </div>

            <div v-if="activeType" class="card my-2">
                <div class="card-header">
                    Attributes
                    <i :class="`fa-light fa-2xl fa-caret-${expandTypeToAttributeTypes?'up':'down'} float-end`" style="transform: translateY(13px); cursor: pointer;" @click="()=>{expandTypeToAttributeTypes = !expandTypeToAttributeTypes;}"></i>
                    <button class="btn btn-link float-end" style="transform: translateY(-5px);" @click="()=>{typeToAttributeTypes.forEach(x=>{x.checked=!x.checked;})}">
                        flip checks
                    </button>
                    <button class="btn btn-light float-end" style="transform: translateY(-5px);" @click="()=>{typeToAttributeTypesByImportance = !typeToAttributeTypesByImportance;}">
                        <i class="fa fa-arrow-up-a-z" :style="`background-color: ${typeToAttributeTypesByImportance ? '' : 'lightgray'};`"></i> vs <i class="fa fa-arrow-up-wide-short" :style="`background-color: ${typeToAttributeTypesByImportance ? 'lightgray' : ''};`"></i>
                    </button>
                </div>
                <div class="card-body" :style="`overflow-y: auto; max-height: ${expandTypeToAttributeTypes?400:100}px;`">
                    <template v-for="aTypeToAttributeType in SortedTypeToAttributeTypesByImportance">
                        <input type="checkbox" v-model="aTypeToAttributeType.checked"/>
                        <span :style="`${aTypeToAttributeType.sourceCategory=='DYNAMIC' ? 'color:red;' : ''}`">{{aTypeToAttributeType.name}}</span> 
                        <label :style="`${aTypeToAttributeType.sourceCategory=='DYNAMIC' ? 'color:red;' : ''}`" class="float-end"><{{aTypeToAttributeType.dataType}}></label>
                        </br>
                    </template>
                </div>
            </div>

        </div>

        <div class="col-10">
            <div class="" style="overflow: auto; max-height:800px;">
                <table v-if="activeType" class="table table-sm">
                    <thead>
                        <tr style="position: sticky; top: 0; z-index: 100; background: white;">
                            <th scope="col" style="position: sticky; left: 0; z-index: 10; background: white;"> </th>
                            <th scope="col" v-for="aInstance in FilteredInstances">
                                {{aInstance.displayName}}
                                <a class="btn btn-link btn-sm" data-toggle="tooltip" :title="`Browse to ${aInstance.displayName}`" :href="`./applications/model-explorer?tab=instance_tab&instance_id=${aInstance.id}`" target="_blank">
                                    <i style="transform: translateY(-3px);" class="fa fa-external-link"></i>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody :set="tempAttributes = []">
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;">Parent</th>
                            <td v-for="aInstance in FilteredInstances">{{aInstance.parentName}}</th>
                        </tr>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;">GrandParent</th>
                            <td v-for="aInstance in FilteredInstances">{{aInstance.grandParentName}}</th>
                        </tr>
                        <tr>
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;"></th>
                            <td v-for="aInstance in FilteredInstances">
                                <button class="btn btn-link btn-sm" data-toggle="tooltip" :title="`copy id: ${aInstance.id}`" @click="clipboard.writeText(aInstance.id)">id</button>
                                <button class="btn btn-link btn-sm" data-toggle="tooltip" :title="`copy fqn: ${aInstance.fqn}`" @click="clipboard.writeText(aInstance.fqn)">fqn</button>
                            </th>
                        </tr>
                        <tr v-for="(aTypeToAttributeType,r) in typeToAttributeTypes.filter(x=>x.checked)" :set="tempAttributes[r] = []">
                            <th scope="row" style="position: sticky; left: 0; z-index: 10; background: white;">{{aTypeToAttributeType.name}}</th>
                            <td v-for="(aInstance,i) in FilteredInstances" :set="tempAttributes[r][i] = aInstance.attributes.find(x=>x.displayName==aTypeToAttributeType.name)">
                                <label v-if="! tempAttributes[r][i].isEditMode" class="w-100" 
                                    @mouseover="tempAttributes[r][i].showEdit = tempAttributes[r][i].allowEdit ? true : false" 
                                    @mouseout="tempAttributes[r][i].showEdit = false" 
                                    :style="`border-width:${tempAttributes[r][i].showEdit ? 'thin' : ''}; border-style:${tempAttributes[r][i].showEdit ? 'dashed' : ''} ;`"
                                >
                                    <span v-if="['INTERNAL','TAG','EXPRESSION'].includes(tempAttributes[r][i].dataSource)">
                                        <span style="color:red;" v-if="tempAttributes[r][i].currentValue == null" data-toggle="tooltip" title="ts: no current value">
                                            {{tempAttributes[r][i].displayValue}}
                                        </span>
                                        <span style="color:red;" v-else data-toggle="tooltip" :title="`V:${tempAttributes[r][i].currentValue.value} T:${tempAttributes[r][i].currentValue.timestamp}`">
                                            {{tempAttributes[r][i].displayValue}}
                                        </span>
                                    </span>
                                    <span v-else>
                                        {{tempAttributes[r][i].displayValue}}
                                    </span>
                                    <i v-show="tempAttributes[r][i].showEdit" 
                                        class="fa fa-sm fa-pencil float-end" 
                                        style="transform: translateY(9px) translateX(-3px); cursor:pointer;"
                                        @click="tempAttributes[r][i].isEditMode = true"
                                    ></i>
                                </label>
                                <label v-if="tempAttributes[r][i].isEditMode" class="w-100" 
                                    style="border-width: 'thin'; border-style: 'dashed';"
                                >
                                    <input style="text" v-model="tempAttributes[r][i].displayValueEdit"/>
                                    <i class="fa fa-sm fa-cancel float-end" 
                                        style="transform: translateY(9px) translateX(-3px); cursor:pointer;"
                                        @click="tempAttributes[r][i].isEditMode=false"
                                    ></i>
                                    <i class="fa fa-sm fa-save float-end" 
                                        style="transform: translateY(9px) translateX(-3px); cursor:pointer;"
                                        @click="UpdateAttributeAsync(tempAttributes[r][i])"
                                    ></i>
                                </label>
                            </td>
                        </tr>
                    <tbody>
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
                allowEditMode: false,
                pageWidth: pageWidth,
                clipboard: clipboard,
                pageTitle: "Manage Instances of Types Side by Side",
                context:<?php echo json_encode($context)?>,
                user:<?php echo json_encode($user)?>,
                tiqTypes: [],
                expandTiqTypes: true,
                activeType: null,
                instanceNames: [],
                expandInstanceNames: false,
                parentNames: [],
                expandParentNames: false,
                grandParentNames: [],
                expandGrandParentNames: false,
                typeToAttributeTypes: [],
                expandTypeToAttributeTypes: false,
                typeToAttributeTypesByImportance: true,
            }
        },
        mounted: async function () {
            WinDoc.title = this.pageTitle;
            await this.LoadTiqTypesAsync();
        },
        computed: {
            FilteredInstances: function(){
                let instances = [];
                this.activeType.objectsByTypeId.forEach(aInstance => {
                    let useInstance = true;

                    if(! this.instanceNames.find(x=>x.name == aInstance.displayName).checked){
                        useInstance = false;
                    }

                    if(! this.parentNames.find(x=>x.name == aInstance.parentName).checked){
                        useInstance = false;
                    }

                    if(! this.grandParentNames.find(x=>x.name == aInstance.grandParentName).checked){
                        useInstance = false;
                    }

                    if(useInstance){
                        instances.push(aInstance);
                    }
                });
                return instances;
            },
            SortedTypeToAttributeTypesByImportance: function(){
                if(this.typeToAttributeTypesByImportance){
                    return this.typeToAttributeTypes.sort((a,b) => a.importance >= b.importance ? 1 : -1);
                } else {
                    return this.typeToAttributeTypes.sort((a,b) => a.name > b.name ? 1 : -1);
                }
            }
        },
        methods: {
            UpdateAttributeAsync: async function(aAttribute){

                let aFieldName = null;
                let aValue = null;
                let proceed = false;

                switch(aAttribute.dataType){
                    case "STRING":
                        aFieldName = 'stringValue';
                        aValue = `"${aAttribute.displayValueEdit}"`;
                        proceed = true;
                        break;
                    case "BOOL":
                        aFieldName = 'boolValue';
                        aValue = JSON.parse(aAttribute.displayValueEdit);
                        if(typeof(aValue)=='boolean'){
                            proceed = true;
                        }
                        break;
                    case "INT":
                        aFieldName = 'intValue';
                        aValue = parseInt(aAttribute.displayValueEdit);
                        if(!isNaN(aValue)){
                            // GraphQL needs a string to patch BigInt fields
                            aValue = `"${aValue}"`;
                            proceed = true;
                        }
                        break;
                    case "FLOAT":
                        aFieldName = 'floatValue';
                        aValue = parseFloat(aAttribute.displayValueEdit);
                        if(!isNaN(aValue)){
                            proceed = true;
                        }
                        break;
                    case "ENUMERATION":
                        aFieldName = 'enumerationName';
                        // not supported
                        break;
                    case "OBJECT":
                        aFieldName = 'objectValue';
                        // not supported
                        break;
                    case "REFERENCE":
                        aFieldName = 'referencedAttribute';
                        // not supported
                        break;
                    default:
                        aFieldName = `unknown data type: ${aAttribute.dataType}`;
                        break;
                }

                if(proceed){

                    let query = `
                        mutation m1 {
                            updateAttribute(
                            input: { id: "${aAttribute.id}", patch: { ${aFieldName}: ${aValue} } }
                            ) {
                                clientMutationId
                                attribute {
                                    ${aFieldName}
                                }
                            }
                        }                
                    `;

                    console.log(query);

                    let aResponse = await tiqJSHelper.invokeGraphQLAsync(query);
                    aAttribute.displayValue = aResponse.data.updateAttribute.attribute[aFieldName];
                    aAttribute.displayValueEdit = aResponse.data.updateAttribute.attribute[aFieldName];
                    console.log(aResponse);
                } else {
                    console.log('not supported...');
                    // undo
                    aAttribute.displayValueEdit = aAttribute.displayValue;
                }

                aAttribute.showEdit = false;
                aAttribute.isEditMode = false;

            },
            LoadTiqTypesAsync: async function(){
                let query = `
query q1 {
    tiqTypes {
        id
        displayName
        partOf {
            id
            displayName
        }
        objectsByTypeId {
            id
        }
    }
}
                `;
                let aResponse = await tiqJSHelper.invokeGraphQLAsync(query);
                let tiqTypes = aResponse.data.tiqTypes;
                this.tiqTypes = tiqTypes.sort((a,b) => a.objectsByTypeId.length <= b.objectsByTypeId.length ? 1 : -1);
            },
            OnSelectTypeAsync: async function(a){
                // console.log(a);

                let query = `
query q1 {
  tiqType(id: "${a.id}") {
    id
    displayName
    partOf {
      id
      displayName
    }
    subTypeOf {
      id
      displayName
    }
    typeToAttributeTypes {
      id
      displayName
      dataType
      sourceCategory
      importance
      enumerationType {
        enumerationNames
      }
    }
    objectsByTypeId {
      id
      displayName
      relativeName
      fqn
      parentObject {
        id
        displayName
        parentObject {
          id
          displayName
        }
      }
      attributes {
        id
        displayName
        dataType
        intValue
        floatValue
        stringValue
        objectValue
        enumerationName
        enumerationValue
        enumerationValues
        enumerationType {
          enumerationNames
        }
        boolValue
        dataSource
        currentValue{
          value
          timestamp
          status
        }
        referencedAttribute{
            id
            displayName
        }
      }
    }
  }
}                `;

                let aResponse = await tiqJSHelper.invokeGraphQLAsync(query);
                let tiqType = aResponse.data.tiqType;
                let instanceNames = [];
                let parentNames = [];
                let grandParentNames = [];
                let typeToAttributeTypes = [];
                tiqType.objectsByTypeId.forEach(aInstance => {
                    
                    if(instanceNames.filter(x=>x.name == aInstance.displayName).length == 0){
                        instanceNames.push({
                            name: aInstance.displayName,
                            checked: true
                        })
                    }

                    let parentName = aInstance.parentObject == null ? 'n/a' : aInstance.parentObject.displayName;
                    aInstance.parentName = parentName;
                    if(parentNames.filter(x=>x.name == parentName).length == 0){
                        parentNames.push({
                            name: parentName,
                            checked: true
                        })
                    }

                    let grandParentName = aInstance.parentObject == null ? 'n/a' : aInstance.parentObject.parentObject == null ? 'n/a' : aInstance.parentObject.parentObject.displayName;
                    aInstance.grandParentName = grandParentName;
                    if(grandParentNames.filter(x=>x.name == grandParentName).length == 0){
                        grandParentNames.push({
                            name: grandParentName,
                            checked: true
                        })
                    }

                    tiqType.typeToAttributeTypes.forEach(aTypeToAttributeType => {

                        if(typeToAttributeTypes.filter(x=>x.name == aTypeToAttributeType.displayName).length == 0){
                            typeToAttributeTypes.push({
                                name: aTypeToAttributeType.displayName,
                                importance: aTypeToAttributeType.importance,
                                checked: true,
                                dataType: aTypeToAttributeType.dataType,
                                sourceCategory: aTypeToAttributeType.sourceCategory
                            })
                        }

                        let aAttribute = aInstance.attributes.find(x=>x.displayName == aTypeToAttributeType.displayName);
                        if(aAttribute){
                            let aValue = null;
                            if(['INTERNAL','TAG','EXPRESSION'].includes(aAttribute.dataSource)){
                                aValue = aAttribute.currentValue==null ? '-' : aAttribute.currentValue.value;
                                // resolve enumerations
                                if(aAttribute.dataType=='ENUMERATION' && aAttribute.currentValue!=null){
                                    // get the index
                                    let aIndex = aAttribute.enumerationValues.findIndex(x=>x==aAttribute.currentValue.value)
                                    aValue = aAttribute.enumerationType.enumerationNames[aIndex];
                                }
                                aAttribute.allowEdit = false;
                            } else {
                                switch(aAttribute.dataType){
                                    case "STRING":
                                        aValue = aAttribute.stringValue;
                                        aAttribute.allowEdit = this.allowEditMode ? true : false;
                                        break;
                                    case "BOOL":
                                        aValue = JSON.stringify(aAttribute.boolValue);
                                        aAttribute.allowEdit = this.allowEditMode ? true : false;
                                        break;
                                    case "INT":
                                        aValue = aAttribute.intValue;
                                        aAttribute.allowEdit = this.allowEditMode ? true : false;
                                        break;
                                    case "FLOAT":
                                        aValue = aAttribute.floatValue;
                                        aAttribute.allowEdit = this.allowEditMode ? true : false;
                                        break;
                                    case "ENUMERATION":
                                        aValue = aAttribute.enumerationName;
                                        // aAttribute.allowEdit = this.allowEditMode ? true : false;
                                        break;
                                    case "OBJECT":
                                        aValue = aAttribute.objectValue==null ? '' : aAttribute.objectValue.replaceAll('","', '", "');
                                        // aAttribute.allowEdit = this.allowEditMode ? true : false;
                                        break;
                                    case "REFERENCE":
                                        aValue = aAttribute.referencedAttribute == null ? '' : aAttribute.referencedAttribute.displayName;
                                        // aAttribute.allowEdit = this.allowEditMode ? true : false;
                                        break;
                                    default:
                                        aValue = `unknown data type: ${aAttribute.dataType}`;
                                        break;
                                }
                            }
                            aAttribute.displayValue = aValue;
                            
                        } else {
                            aAttribute = {
                                displayName : aTypeToAttributeType.displayName,
                                displayValue : '__deleted__',
                                allowEdit : false,
                            }
                            aInstance.attributes.push(aAttribute);
                        }

                        aAttribute.showEdit = false;
                        aAttribute.isEditMode = false;
                        aAttribute.displayValueEdit = aAttribute.displayValue;
                    });
                });
                this.activeType = tiqType;
                this.instanceNames = instanceNames.sort((a,b) => a.name > b.name ? 1 : -1);
                this.parentNames = parentNames.sort((a,b) => a.name > b.name ? 1 : -1);
                this.grandParentNames = grandParentNames.sort((a,b) => a.name > b.name ? 1 : -1);
                this.typeToAttributeTypes = typeToAttributeTypes;
                this.expandTiqTypes = false;
            }
        },
    })
    .mount('#app');
</script>
