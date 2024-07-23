<?php

// {
//   "data": {
//     "script": {
//       "displayName": "VST Notepad",
//       "relativeName": "vst_notepad",
//       "description": "Handy tool to explore, edit and create value stream data of internal attributes.",
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
            <span class="my-2 me-2" data-toggle="tooltip" title="Pick attribute to get started.">
                <tree-picker 
                    :picker-name='`attribute_picker`'
                    display-mode='instance'
                    content='Select a target attribute'
                    :height='500'
                    default-expand-levels='0'
                    :default-root-node-fqn='null'
                    :default-root-node-id='null'
                    :prune-branches='false'
                    :branch-types='["organization","place,equipment","gateway","connector","opcua_object","object","material","person","attribute"]'
                    :leaf-types='["attribute","tag"]'
                    @on-select="OnSelectAttributeAsync"
                ></tree-picker>
            </span>
            <span v-if="!activeAttribute">Pick attribute to get started</span>
            <br/>
            <div v-if="activeAttribute">
                <div>
                    Active Attribute: {{activeAttribute.displayName}} <button class="btn btn-sm btn-primary ms-2" @click="FetchAttributeAsync(activeAttribute.id)">Refresh</button><br />
                    Data Type: {{activeAttribute.dataType}}<br />
                </div>
                <div v-if="activeAttribute.currentValue">
                    <b>Current Value</b><br />
                    Value: {{activeAttribute.currentValue.value}}<br />
                    TS: {{activeAttribute.currentValue.timestamp}}<br />
                </div>
                <div v-if="activeAttribute.dataType=='ENUMERATION'">
                    <b>Enumeration</b><br />
                    Type: {{activeAttribute.enumerationType.displayName}}
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(aChoice,i) in activeAttribute.enumerationValues">
                                <th scope="row">{{activeAttribute.enumerationType.enumerationNames[i]}}</th>
                                <td>{{aChoice}}</td>
                            </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-10">
            <duration-picker
                ref="datePicker"
                :key = "durationPickerKey"
                :show-player-controls='true'
                :show-zoom-controls='false'
                :show-pan-controls='true'
                
                :time-periods="tp"
                :active-time-period="activeTimePeriod"
                :show-time-periods='true'

                :timezones="tz" 
                :active-timezone="1" 
                :show-timezones="true"            
                
                :show-end='true'
                :show-start-time='true'
                :show-end-time='true'

                :zoom-in-factor='0.5'
                :zoom-out-factor='0.5'
                :interval-delay='5'
                :date-diff-in-min='60'

                mode='dateTime'
                :start-date="startDate.format()"
                :end-date="endDate.format()"
                @on-date-change="Duration_picker_onDateChange"
            ></duration-picker>
            <button class="btn btn-light btn-sm" @click="CopyTimePeriod">Copy Time Period</button>
            <button class="btn btn-light btn-sm" @click="PasteTimePeriod">Paste Time Period</button>

            <div v-if="activeAttribute" class="my-4">
                <div>
                    <hr />
                    <h5>
                        <span class="mx-3" :style="activeNewDataMode.name=='single record' ? 'font-weight:500;' : 'font-weight:400; cursor: pointer;'" @click="ActivateNewDataMode('single record')">Insert Single Value</span>
                        <span class="mx-3" :style="activeNewDataMode.name=='generate series' ? 'font-weight:500;' : 'font-weight:400; cursor: pointer;'"  @click="ActivateNewDataMode('generate series')">Generate Series</span>
                        <span class="mx-3" :style="activeNewDataMode.name=='paste from excel' ? 'font-weight:500;' : 'font-weight:400; cursor: pointer;'"  @click="ActivateNewDataMode('paste from excel')">Paste from Excel</span>
                    </h5>
                    <hr />
                    <div v-if="activeNewDataMode.name=='single record'">
                        <div class="my-1">
                            <label style="width:120px;">New Timestamp</label>
                            <input type="string" @input="newDateTimeMoment=null" v-model="newDateTimeString"/>
                            <button class="btn btn-sm btn-primary ms-2" @click="ValidateTimestamp(false)">Parse as {{activeTimeZone.value}}</button>
                            <button class="btn btn-sm btn-primary ms-2" @click="ValidateTimestamp(true)">Parse as UTC</button>
                            ISO: {{newDateTimeMoment ? newDateTimeMoment.toISOString() : '---'}}
                        </div>
                        <div class="my-1">
                            <label style="width:120px;">Status</label><input type="number" v-model="newStatus"/>
                        </div>
                        <div class="my-1">
                            <label style="width:120px;">Value</label><input type="string" v-model="newValue"/>
                            <button :disabled="newDateTimeMoment==null" class="btn btn-sm btn-primary ms-4" @click="InsertNewRecordAsync">Insert New Record</button>
                            <span v-if="mutationResponse">{{JSON.stringify(mutationResponse)}}</span>
                        </div>
                        <hr />
                    </div>
                    <div v-if="activeNewDataMode.name=='generate series'">
                        <div class="my-1">
                            <label style="width:120px;">Seed Timestamp</label>
                            <input type="string" @input="newDateTimeMoment=null" v-model="newDateTimeString"/>
                            <button class="btn btn-sm btn-primary ms-2" @click="ValidateTimestamp(false)">Parse as {{activeTimeZone.value}}</button>
                            <button class="btn btn-sm btn-primary ms-2" @click="ValidateTimestamp(true)">Parse as UTC</button>
                            ISO: {{newDateTimeMoment ? newDateTimeMoment.toISOString() : '---'}}
                        </div>
                        <div class="my-1">
                            <label style="width:120px;">
                                Interval
                                <i class="fa fa-circle-info" data-toggle="tooltip" title="Period of Time: ISO 8601 duration, for instance P1DT8H for 1 day and 8 hours."></i>
                            </label>
                            <input type="string" v-model="newSeriesInterval"/>
                        </div>
                        <div class="my-1">
                            <label style="width:120px;">
                                Element Count 
                                <i class="fa fa-circle-info" data-toggle="tooltip" title="Number of Elements to create in the series."></i>
                            </label>
                            <input type="number" v-model="newSeriesCount"/>
                            <button :disabled="newDateTimeMoment==null || newSeriesCount < 1 || moment.duration(newSeriesInterval).valueOf()==0" class="btn btn-sm btn-primary ms-4" @click="GenerateSeries">Generate Series</button>
                            <span v-if="mutationResponse">{{JSON.stringify(mutationResponse)}}</span>
                        </div>
                        <table v-if="newSeries.length>0" class="table table-sm">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Value</th>
                                    <th scope="col">Status</th>
                                <th scope="col">ts (ISO)</th>
                                <th scope="col">ts (locale)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(aVst,i) in newSeries">
                                    <th scope="row">{{i+1}}</th>
                                    <td>{{aVst.value}}</td>
                                    <td>{{aVst.status}}</td>
                                    <td>{{aVst.timestamp.toISOString()}}</td>
                                    <td>{{aVst.timestamp.format('YYYY-MM-DD HH:mm:SS')}}</td>
                                </tr>
                        </table>
                        <button class="btn btn-sm btn-primary m-2" @click="InsertNewSeriesAsync">Write Series to Attribute</button>
                        <hr />
                    </div>
                    <div v-if="activeNewDataMode.name=='paste from excel'">
                        <span> paste from excel </span>
                        <hr />
                    </div>
                </div>

                <div style="overflow-y: automatic; max-height: 800px;">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ts (Postgres)</th>
                                <th scope="col">ts (locale)</th>
                                <th scope="col">Status</th>
                                <th scope="col">Value</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="aVst in tsData">
                                <th scope="row">{{aVst.ts}}</th>
                                <td>{{moment(aVst.ts).tz(activeTimeZone.value).format('YYYY-MM-DD HH:mm:SS')}}</td>
                                <td>{{aVst.status}}</td>
                                <td>{{aVst[this.fieldToRetrieve]}}</td>
                                <td>
                                    <i class="fa fa-ellipsis-v" style="cursor: pointer;" id="dropdownMenu2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                        <button class="dropdown-item" type="button" @click="DeleteRecordAsync(aVst)">
                                            <i class="fa fa-trash mr-1" aria-hidden="true" ></i>
                                            Delete Record
                                        </button>
                                    </div>
                                </td>
                            </tr>
                    </table>
                </div>

            </div>

        </col>
    </div>

</div>

<script>
    var WinDoc = window.document;
    
    var pageWidth = window.screen.width;

    // we need a clipboard so we can copy / paste
    var clipboard = navigator.clipboard;

    // collection of timezones for the duration picker
    var appTimeZones = [
        {
            "id":"1",
            "name":"US Eastern",
            "value":"America/New_York"
        },
        {
            "id":"2",
            "name":"US Central",
            "value":"America/Chicago"
        },
        {
            "id":"3",
            "name":"US Mountain",
            "value":"America/Denver"
        },
        {
            "id":"4",
            "name":"US Pacific",
            "value":"America/Los_Angeles"
        },
        {
            "id":"5",
            "name":"ThinkIQ Asia",
            "value":"Asia/Kolkata"
        },
        {
            "id":"6",
            "name":"Europe Amsterdam",
            "value":"Europe/Amsterdam"
        },
        {
            "id":"7",
            "name":"Europe Berlin",
            "value":"Europe/Berlin"
        },
        {
            "id":"8",
            "name":"UTC",
            "value":"utc"
        }
    ];

    // collection of time periods for the duration picker
    var appTimePeriods = [
        {"id":"1","name":"Last 30 minutes","duration":"PT30M","end_date":"now"},
        {"id":"2","name":"Last 1 hour","duration":"PT1H","end_date":"now"},
        {"id":"3","name":"Last 2 hour","duration":"PT2H","end_date":"now"},
        {"id":"4","name":"Last 6 hour","duration":"PT6H","end_date":"now"},
        {"id":"5","name":"Last 12 hour","duration":"PT12H","end_date":"now"},
        {"id":"6","name":"Last 24 hour","duration":"PT24H","end_date":"now"},
        {"id":"7","name":"Last 48 hour","duration":"PT48H","end_date":"now"},
        {"id":"8","name":"Last 7 days","duration":"PT168H","end_date":"now"}
    ];

    // this is the coolest delay function ever
    // use like this to wait 100ms: await delay(100);
    // https://levelup.gitconnected.com/how-to-turn-settimeout-and-setinterval-into-promises-6a4977f0ace3
    function delay(time) {
        return new Promise(resolve => setTimeout(resolve, time));
    }

    var app = createApp({
        // el: "#app",
        data() {
            return {
                moment: moment,
                pageWidth: pageWidth,
                clipboard: clipboard,
                pageTitle: "VST Notepad",
                context:<?php echo json_encode($context)?>,
                user:<?php echo json_encode($user)?>,

                // stuff for duration picker
                tz: JSON.stringify(appTimeZones),
                tp: JSON.stringify(appTimePeriods),
                startDate: moment().tz(appTimeZones[0].value).add(-1,'d'),
                endDate: moment().tz(appTimeZones[0].value),
                // startDate: moment('2021-08-01').tz(appTimeZones[0].value),
                // endDate: moment('2021-09-01').tz(appTimeZones[0].value),
                activeTimePeriod: 6,

                activeAttribute: null,
                tsData: [],

                newValue: 0,
                newStatus: 0,
                newDateTimeString: moment().format('YYYY-MM-DD HH:00:00'),
                newDateTimeMoment: null,
                
                newSeries: [],
                newSeriesCount: 3,
                newSeriesInterval: 'PT8H',

                mutationResponse: null,

                newDataModes: [
                    {
                        name: "single record",
                        isActive: true
                    },
                    {
                        name: "generate series",
                        isActive: false
                    },
                    {
                        name: "paste from excel",
                        isActive: false
                    },

                ],

                
            }
        },
        mounted: async function () {
            WinDoc.title = this.pageTitle;

        },
        computed: {
            activeTimeZone: function(){
                return this.$refs.datePicker.timezone=='0' ? null : appTimeZones.find(x=>x.id==this.$refs.datePicker.timezone);
            },
            activeNewDataMode: function(){
                return this.newDataModes.find(x=>x.isActive);
            },
            fieldToRetrieve: function(){
                switch(this.activeAttribute.dataType){
                    case "FLOAT":
                        return "floatvalue";
                    case "ENUMERATION":
                        return "intvalue";
                    default:
                        return "null";
                }
            }
        },
        methods: {
            GenerateSeries: async function(){
                this.newSeries = [];
                let newSeries = [];
                let timestamp = this.newDateTimeMoment.clone();
                for(let i=0; i<this.newSeriesCount; i++){
                    newSeries.push({
                        value: i,
                        status: 0,
                        timestamp: i==0 ? timestamp : newSeries[i-1].timestamp.clone().add(this.newSeriesInterval)
                    });
                }
                this.newSeries = newSeries;
            },
            ActivateNewDataMode: async function(aModeName){
                if(this.activeNewDataMode.name!=aModeName){
                    this.newDataModes.forEach(x=>x.isActive=false);
                    this.newDataModes.find(x=>x.name==aModeName).isActive = true;
                }
            },
            DeleteRecordAsync: async function(aVst){
                    let query = `
                        mutation m1 {
                            replaceTimeSeriesRange(
                                input: {
                                    attributeOrTagId: "${this.activeAttribute.id}"
                                    startTime: "${aVst.ts}"
                                    endTime: "${aVst.ts}"
                                }
                            ){
                                json
                            }
                        } 
                    `;
                    let aResponse = await tiqJSHelper.invokeGraphQLAsync(query);
                    this.mutationResponse = aResponse.data.replaceTimeSeriesRange;
                    await this.FetchAttributeAsync();
            },
            InsertNewSeriesAsync: async function(){
                    let valueArray = this.newSeries.map(x=>`{value:"${x.value}",status: "0",timestamp:"${x.timestamp.toISOString()}"}`).join(',');
                    let query = `
                        mutation m1 {
                            replaceTimeSeriesRange(
                                input: {
                                    attributeOrTagId: "${this.activeAttribute.id}"
                                    entries: [${valueArray}]
                                }
                            ){
                                json
                            }
                        } 
                    `;
                    console.log(query);
                    let aResponse = await tiqJSHelper.invokeGraphQLAsync(query);
                    if(aResponse.errors){
                        this.mutationResponse = aResponse.errors;
                    } else {
                        this.mutationResponse = aResponse.data.replaceTimeSeriesRange;
                        await this.FetchAttributeAsync();
                    }
            },
            InsertNewRecordAsync: async function(){
                    let query = `
                        mutation m1 {
                            replaceTimeSeriesRange(
                                input: {
                                    attributeOrTagId: "${this.activeAttribute.id}"
                                    entries: { 
                                        value: "${this.newValue}", 
                                        status: "${this.newStatus}", 
                                        timestamp: "${this.newDateTimeMoment.toISOString()}" 
                                    }
                                }
                            ){
                                json
                            }
                        } 
                    `;
                    let aResponse = await tiqJSHelper.invokeGraphQLAsync(query);
                    if(aResponse.errors){
                        this.mutationResponse = aResponse.errors;
                    } else {
                        this.mutationResponse = aResponse.data.replaceTimeSeriesRange;
                        await this.FetchAttributeAsync();
                    }
            },
            ValidateTimestamp: function(isUtc){
                let aMoment = null;
                if(isUtc){
                    aMoment = moment.utc(this.newDateTimeString);
                } else {
                    aMoment = moment(this.newDateTimeString).tz(this.activeTimeZone.value);
                }
                if(aMoment.isValid()){
                    this.newDateTimeMoment = aMoment;
                } 
            },
            CopyTimePeriod: function(){
                clipboard.writeText(`{
                    "start":"${this.startDate.toISOString()}",
                    "end":"${this.endDate.toISOString()}",
                    "tz":"${this.activeTimeZone.value}"
                }`);
            },
            PasteTimePeriod: async function(){
                let data = JSON.parse(await clipboard.readText());
                let aTimeZone = appTimeZones.find(x=>x.value == data.tz);
                this.$refs.datePicker.timezone=aTimeZone.id;
                await delay(100);
                this.startDate=moment(data.start).tz(aTimeZone.value);
                await delay(100);
                this.endDate=moment(data.end).tz(aTimeZone.value);
            },
            Duration_picker_onDateChange: async function (start_date, end_date) {
                // console.log('duration_picker_onDateChange', start_date, end_date);
                let sd = moment(start_date);
                this.startDate = this.activeTimeZone==null ? sd : sd.tz(this.activeTimeZone.value);
                let ed = moment(end_date)
                this.endDate = this.activeTimeZone==null ? ed : ed.tz(this.activeTimeZone.value);

                await this.FetchTsDataAsync();
            },
            OnSelectAttributeAsync: async function(a){
                // console.log(a);
                if(a.type_name!="attribute"){
                    alert('Select attribute.');
                } else {
                    await this.FetchAttributeAsync(a.id);
                }
            },
            FetchAttributeAsync: async function(aAttributeId){
                if(aAttributeId){

                } else {
                    if(this.activeAttribute){
                        aAttributeId = this.activeAttribute.id;
                    } else {
                        return false;
                    }
                }
                let query = `
                    query q1{
                        attribute(id:"${aAttributeId}"){
                            id 
                            displayName 
                            dataType 
                            dataSource 
                            enumerationValues
                            enumerationType{
                                displayName
                                enumerationNames
                            }
                            currentValue{ 
                                value 
                                timestamp 
                            } 
                        }
                    }
                `;
                let aResponse = await tiqJSHelper.invokeGraphQLAsync(query);
                let aAttribute = aResponse.data.attribute;
                console.log(aAttribute);
                if(aAttribute.dataSource!="INTERNAL"){
                    alert('Selected attribute must be INTERNAL. No config, tags, or expression attributes are allowed.');
                } else {
                    this.activeAttribute = aAttribute;
                    await this.FetchTsDataAsync();
                }
            },
            FetchTsDataAsync: async function(){
                if(this.activeAttribute!=null){
                    this.tsData = [];
                    // let query = `
                    //     query q1{
                    //         attribute(id:"${this.activeAttribute.id}"){
                    //             getTimeSeries(startTime:"${this.startDate.toISOString()}" endTime:"${this.endDate.toISOString()}"){
                    //                 ts
                    //                 status
                    //                 ${this.fieldToRetrieve}
                    //             }
                    //         }
                    //     }
                    // `;
                    // let aResponse = await tiqJSHelper.invokeGraphQLAsync(query);
                    // this.tsData = aResponse.data.attribute.getTimeSeries;

                    let response = await fetch(`./index.php?option=com_thinkiq&task=thinkiq.getTimeseries&ids=${this.activeAttribute.id}&start=${this.startDate.valueOf()}&end=${this.endDate.valueOf()}`);
                    let json = await response.json();
                    let attrData = json[Object.keys(json)[0]];
                    console.log(json);
                    let tsData = [];
                    attrData.values.forEach((aValue, i) => {
                        tsData.push({
                            [this.fieldToRetrieve]: aValue,
                            status: attrData.statuses[i],
                            ts: attrData.timestamps[i]
                        });
                    });
                    this.tsData = tsData;
                }
            }
        },
    })
    .mount('#app');
</script>
