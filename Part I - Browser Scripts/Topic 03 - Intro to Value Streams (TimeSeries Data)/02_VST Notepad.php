<?php

use Joomla\CMS\HTML\HTMLHelper;

$primary_domain = 'https://' . $_SERVER['HTTP_HOST'];

HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.core.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));
// HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.tiqGraphQL.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));
HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.components.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));
HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.charts.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));

require_once 'thinkiq_context.php';
$context = new Context();

use Joomla\CMS\Factory;
$user = Factory::getUser();

use TiqUtilities\Model\Script;
Script::includeScript('mv_tiny_tools_library.vision_js_sdk');
Script::includeScript('mv_tiny_tools_library.components_colon__media_popup');
// Script::includeScript('mv_tiny_tools_library.components_colon__media_view__sol_w_annotations');


?>


<!-- paperjs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/paper.js/0.12.15/paper-full.min.js"
    integrity="sha512-ovjLI1ZcZe6bw+ImQ21r+sv8q/Vwob2kq7tFidK6E1LWfi0T4uobbmpfEU1//a9h9o5Kkt+MnMWf6rWlg0EiMw=="
    crossorigin="anonymous"></script>

<div id="app">

    <vision-media-popup-component :image-step-id="activeImageStepId" :image-name="activeImageName":vision-db-name="activeDatabaseName" ref="visionMediaPopUp"></vision-media-popup-component>

    <div class="row">            
        <div class="col-12">
            <h1 class="pb-2 pt-2" style="font-size:2.5rem; color:#126181;">
                {{pageTitle}}
                <a v-if="true" class="float-end btn btn-sm btn-link mt-2" style="font-size:1rem; color:#126181;" v-bind:href="`/index.php?option=com_modeleditor&view=script&id=${context.std_inputs.script_id}`" target="_blank">source</a>
            </h1>
            <div v-if="urlDatabaseName==null && databases.length>1">
                <button class="btn btn-sm me-3" v-bind:class="!aDb.isActive ? 'btn-light' : 'btn-secondary'" v-for="aDb in databases" @click="activateDatabase(aDb)">{{aDb.caption}}</button>
            </div>
            <hr style="border-color:#126181; border-width:medium;" />
        </div>   
    </div>

    <div class="row">
        <div class="col-2">
            <div v-if="activeGateway" class="my-2">
                <h5>Active Gateway:</h5>
                
                <span :hidden="!activeGateway.XSubject"><label class="me-2">Subject: </label>{{activeGateway.XSubject}}<br /></span>
                <label class="me-2">HostName: </label>{{activeGateway.HostName}} 
                
                <span class="ml-2">
                    <i class="fa fa-play" style="color:red;"></i>
                </span>
                <br />
                <div class="mx-4" v-for="aStep in activeGateway.steps">
                    
                    <label class="me-2">StepName: </label>{{aStep.StepName}} <br />
                    <div v-if="aStep.XMeasure != 'n/a'">
                        <label class="me-2">Measure: </label>{{aStep.XMeasure}}<br />
                    </div>

                    <div v-if="aStep.sampleImage!='n/a'" @click="showJsonModal(aStep)" style="cursor: pointer;" data-toggle="tooltip" v-bind:title="moment(aStep.imageStep.collection_time).tz(activeTimeZone.value).format()">
                        <div v-if="aStep.sampleImage.endsWith('.jpg')">
                            <img width="200" style="object-fit:contain;" :src="aStep.sampleImageSigned"/>
                        </div>
                        <div v-if="aStep.sampleImage.endsWith('.mp4')">
                            <video :src="aStep.sampleImageSigned" width="200" autoplay muted controls ></video>
                        </div>
                    </div>
                    <div v-else @click="showJsonModal(aStep)" style="cursor: pointer;" data-toggle="tooltip" v-bind:title="moment(aStep.imageStep.collection_time).tz(activeTimeZone.value).format()">
                        <img width="200" style="object-fit:contain;" src="https://cdn.icon-icons.com/icons2/2699/PNG/512/json_logo_icon_168490.png"/>
                    </div>
                </div>
                <hr style="border-top: 3px double #8c1b1b;"/>
            </div>
            <div v-if="showCameraPicker" class="my-2" style="overflow: auto;" v-bind:style="activeGateway ? 'max-height: 700px;' : 'max-height: 900px;'">
                <div v-for="aGateway in gateways">
                    
                    <span :hidden="!aGateway.XSubject"><label class="me-2">Subject: </label>{{aGateway.XSubject}}<br /></span>
                    <label class="me-2">HostName: </label>{{aGateway.HostName}} 
                    
                    <span class="ml-2" style="cursor:pointer;" @click="activateGateway(aGateway)">
                        <i class="fa fa-play" v-bind:style="aGateway.isActive ? 'color:red' : 'color:black'"></i>
                    </span>
                    <br />
                    <div class="mx-4" v-for="aStep in aGateway.steps">
                        
                        <label class="me-2">StepName: </label>{{aStep.StepName}} <br />
                        <div v-if="aStep.XMeasure != 'n/a'">
                            <label class="me-2">Measure: </label>{{aStep.XMeasure}}<br />
                        </div>
                        
                        <div v-if="aStep.sampleImage!='n/a'" @click="showJsonModal(aStep)" style="cursor: pointer;" data-toggle="tooltip" v-bind:title="moment(aStep.imageStep.collection_time).tz(activeTimeZone.value).format()">
                            <div v-if="aStep.sampleImage.endsWith('.jpg')">
                                <img width="200" style="object-fit:contain;" :src="aStep.sampleImageSigned"/>
                            </div>
                            <div v-if="aStep.sampleImage.endsWith('.mp4')">
                                <video :src="aStep.sampleImageSigned" width="200" autoplay muted controls ></video>
                            </div>
                        </div>
                        <div v-else @click="showJsonModal(aStep)" style="cursor: pointer;" data-toggle="tooltip" v-bind:title="moment(aStep.imageStep.collection_time).tz(activeTimeZone.value).format()">
                            <img width="200" style="object-fit:contain;" src="https://cdn.icon-icons.com/icons2/2699/PNG/512/json_logo_icon_168490.png"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-10">
            <duration-picker
                ref="datePicker"
                :key = "durationPickerKey"
                :show-player-controls='false'
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
                @on-date-change="duration_picker_onDateChange"
            ></duration-picker>

            <button class="btn btn-light btn-sm" @click="copyLink">Get Link</button>
            <button class="btn btn-light btn-sm" @click="cloneTab">Clone Tab</button>
            
            <button class="btn btn-light btn-sm" @click="copyTimePeriod">Copy Time Period</button>
            <button class="btn btn-light btn-sm" @click="pasteTimePeriod">Paste Time Period</button>

            <button class="btn btn-light btn-sm" @click="toggleTimestamps = !toggleTimestamps;">Toggle Timestamps</button>

            <button class="btn btn-light btn-sm" @click="ToggleAnnotationsAsync">Toggle Annotations</button>

            <button class="btn btn-light btn-sm" @click="ToggleQaAsync">Toggle QA {{toggleQa ? 'Off' : 'On'}}</button>

            <div v-if="activeGateway">
                <div class="row mt-4 flex-row flex-nowrap" style="overflow-x: scroll;max-width:1000px">
                    <div class="card col" style="min-width: 18rem;" v-for="aStepName in stepNames">
                        <div class="card-body">
                            <div class="card-header">
                                <h5>{{aStepName.stepName}}</h5>
                                <h6 v-if="activeGateway.steps.find(x=>x.StepName==aStepName.stepName).XMeasure!='n/a'">
                                    {{activeGateway.steps.find(x=>x.StepName==aStepName.stepName).XMeasure}}
                                </h6>
                            </div>
                            <div v-if="aStepName.functionName!=''">
                                <{{aStepName.functionName}}>
                            </div>
                            <div v-for="aImgName in aStepName.imgNames">
                                <div v-if="!(aImgName.imgName=='image_step.json' && aStepName.imgNames.length>1)">
                                    <label>
                                        <input class="mr-3" type="checkbox" v-model="aImgName.isActive"/>{{aImgName.imgName}} ({{aImgName.imgCount}} pics)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="activeGateway">
                <div style="overflow: auto; max-height: 850px;" class="row">
                    <div v-for="aPop in filteredPops" class="col">
                        <div class="my-1" style="margin-left: -4px; position:relative;" 
                                data-toggle="tooltip" v-bind:title="moment(aPop.imageStep.collection_time).tz(activeTimeZone.value).format()">
                            
                            <label style="position:relative; transform: translate(10px,0px); color: coral; z-index:5; width: 300px;" v-if="toggleTimestamps">
                                {{moment(aPop.imageStep.collection_time).milliseconds() != 0 ? moment(aPop.imageStep.collection_time).tz(activeTimeZone.value).format('MM/DD/YYYY h:mm:ss.SZ') : moment(aPop.imageStep.collection_time).tz(activeTimeZone.value).format()}}
                            </label>

                            <div @click="showJsonModal(aPop)" style="cursor: pointer;">
                                <video v-if="aPop.isMov" :src="mediaLinksSigned[aPop.mediaLink]" width="400" muted controls style="float: left; object-fit: contain;  border-style: solid; border-color: coral;"></video>
                                <img v-if=" aPop.isImg && !toggleAnnotations" :src="mediaLinksSigned[aPop.mediaLink]" width="400" style="float: left; object-fit: contain;  border-style: solid; border-color: coral;"></img>
                                
                                <div v-if=" aPop.isImg && toggleAnnotations" class="my-1"
                                    :style="`height:${aPop.canvasResolution.height * backingScale}px; width:${aPop.canvasResolution.width * backingScale}px;`" >
                                    <canvas v-bind:id="`cid_${aPop.imageStep.image_step_id}`"  
                                            resize  
                                            v-bind:style="{ backgroundImage: 'url(' + mediaLinksSigned[aPop.mediaLink] + ')' , left: aPop.canvasResolution.widthOffset / backingScale + 'px'}" 
                                            style="background-size: 100% 100%; position: absolute; border-style: solid; border-color: coral;">
                                    </canvas>
                                </div>

                                <!--<div v-if=" aPop.isImg && toggleAnnotations" style="height:250px; width:400px;">
                                    <vision-media-view-with-annotations-component 
                                        :id="aPop.imageStep.image_step_id" ref="`visionMediaViewComponent_${aPop.imageStep.image_step_id}`" 
                                        :h-px="250"
                                        :w-px="400"
                                        :config="{
                                            databaseName: activeDatabaseName,
                                            hostName: aPop.imageStep.host_name,
                                            stepName: aPop.imageStep.step_name,
                                            imageName: aPop.imgName,
                                            showAnnotations: true
                                        }" >
                                    </vision-media-view-with-annotations-component>
                                </div> -->

                                <div v-if="!aPop.isMov && !aPop.isImg" class="card" style="width:400px; height:200px; float: left; object-fit: contain;  border-style: solid; border-color: coral;">
                                    <pre>
<code>{{JSON.stringify(aPop.imageStep, null, 2)}}
</code>
                                    </pre>
                                </div>
                            </div>

                            <label class="my-3" style="position:relative; transform: translate(10px,0px); color: coral; z-index:5; width: 350px;" v-if="toggleQa">
                                <i class="mx-2 fa-2xl fa-thumbs-down" :class="aPop.annotations.filter(x=>!x.is_accurate).length>0 ? 'fa-solid' : 'fa-light'" style="cursor: pointer;" @click="TogglePopAnnotationAsync(aPop, 'isAccurate')"></i>
                                <i class="mx-2 fa-2xl fa-light fa-broom"></i>
                                <i class="mx-2 fa-2xl fa-light fa-cloud-sun"></i>
                            </label>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    var WinDoc = window.document;

    //https://developer.apple.com/library/archive/documentation/AudioVideo/Conceptual/HTML-canvas-guide/SettingUptheCanvas/SettingUptheCanvas.html
    function backingScale() {
        if ('devicePixelRatio' in window) {
            if (window.devicePixelRatio > 1) {
                return window.devicePixelRatio;
            }
        }
        return 1;
    }

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

    // we need a clipboard so we can copy / paste
    var clipboard = navigator.clipboard;

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

                // url parameters if available
                urlStart: <?php echo $_GET['start'] ?? 'null';  ?>,
                urlEnd: <?php echo $_GET['end'] ?? 'null';  ?>,
                urlTz: <?php echo $_GET['tz'] ?? 'null';  ?>,
                urlGatewayId: <?php echo $_GET['gatewayId'] ?? 'null';  ?>,
                urlDatabaseName: <?php echo $_GET['databaseName'] ?? 'null';  ?>,
                showCameraPicker: <?php echo $_GET['showCameraPicker'] ?? 'true';  ?>,

                // stuff for duration picker
                tz: JSON.stringify(appTimeZones),
                tp: JSON.stringify(appTimePeriods),
                startDate: core.moment().tz(appTimeZones[0].value).add(-1,'h'),
                endDate: core.moment().tz(appTimeZones[0].value),
                activeTimePeriod: 2,


                pageTitle: "Camera Dashboard",
                context: <?php echo json_encode($context)?>,
                activeUserId: <?php echo json_encode($user->id)?>,                
                // available databases and active db
                databaseNames: <?php echo json_encode($database_names)?>,
                databases: [],

                // available cameras and selected camera
                gateways: [],
                // stepnames available for the active camera
                stepNames: [],

                // we use a request id to make sure
                // that we don't cross fire with
                // mutliple overlapping requests
                requestId: 0,

                // pops are the images available for display
                pops: [],
                mediaLinksSigned: {},
                filteredPops: [],
                activePop:null,
                activeImageStepId: null,
                activeImageName: null,

                modalMode:0,
                durationPickerKey: 0,

                // to show timestamps on images
                toggleTimestamps:false,

                // to show annotations on images
                annotations: [],
                toggleAnnotations:false,

                // to show qa for images
                toggleQa:false,

                // to know if we're fetching data
                isLoading: false,

                // use the backingScale to support retina screens
                backingScale: 1
            }
        },
        mounted: async function () {
            WinDoc.title = this.pageTitle;
            // create database listing
            this.databaseNames = await VisionSdk.GetVisionDbNamesAsync();
            let dbs = [];
            this.databaseNames.sort((a,b)=>a>b?1:-1).forEach(aDbName=>{
                let aDb = {
                    name: aDbName,
                    isActive: aDbName==this.urlDatabaseName ? true : false,
                    caption: aDbName.replace("fdw_vision_", "")
                };
                dbs.push(aDb);
            });
            if(!dbs.find(x=>x.isActive)){
                dbs[0].isActive = true;
            }
            this.databases = dbs;

            await this.getVisionStackAsync(1,2);

            if(this.urlGatewayId && this.urlGatewayId!='undefined'){
                await this.activateGateway(this.gateways.find(x=>x.GatewayId==this.urlGatewayId));
            }
            await delay(100);
            let aTimeZone=null;
            if(this.urlTz){
                aTimeZone = appTimeZones.find(x=>x.id == this.urlTz).value;
                this.$refs.datePicker.timezone=this.urlTz;
            }
            await delay(100);
            if(this.urlStart){
                if(aTimeZone==null){
                    this.startDate=moment(this.urlStart);
                } else {
                    this.startDate=moment(this.urlStart).tz(aTimeZone);
                }
            }
            await delay(100);
            if(this.urlEnd){
                if(aTimeZone==null){
                    this.endDate=moment(this.urlEnd);
                } else {
                    this.endDate=moment(this.urlEnd).tz(aTimeZone);
                }
            }

        },
        computed: {
            activeTimeZone: function(){
                if(this.$refs.datePicker.timezone=='0'){
                    let aTz= {
                                "id":"0",
                                "name":"Browser",
                                "value": moment.tz.guess()
                            };
                    return aTz;
                } else {
                    return appTimeZones.find(x=>x.id==this.$refs.datePicker.timezone);
                }
            },
            activeGateway: function(){
                return this.gateways.find(x=>x.isActive);
            },
            activeDatabase: function(){
                return this.databases.find(x=>x.isActive);
            },
            activeDatabaseName: function(){
                return this.activeDatabase == null ? null : this.activeDatabase.name;
            },
            moment: function(){
                return moment;
            }
        },
        watch: {
            stepNames: {
                handler(newValue, oldValue) {
                    let filteredPops = [];

                    this.pops.forEach(aPop=>{
                        let aStepName = this.stepNames.find(x=>x.stepName == aPop.stepName);
                        let aImageName = aStepName.imgNames.find(x=>x.imgName == aPop.imgName);
                        if (aImageName.isActive){
                            filteredPops.push(aPop);
                        }
                    });

                    this.filteredPops = filteredPops;

                },
                deep: true
            },
            filteredPops:{
                handler(newValue, oldValue){
                    console.log('old', oldValue);
                    console.log('new', newValue);
                    if(this.toggleAnnotations){
                        this.DrawAnnotationsAsync();
                    }
                }
            }
        },
        methods: {
            ToggleAnnotationsAsync: async function(){
                this.toggleAnnotations = !this.toggleAnnotations;
                if(this.toggleAnnotations){
                    this.DrawAnnotationsAsync();
                }
            },
            ToggleQaAsync: async function(){
                this.toggleQa = !this.toggleQa;
            },
            GetImageResolution: function(imageStepData){
                try{
                    return imageStepData.step_output.media_info.default_jpg.resolution;
                } catch (e){}
                
                try{
                    return {
                        height: imageStepData.step_output.resolution.h,
                        width: imageStepData.step_output.resolution.w
                    };
                } catch (e){}

                return null;
            },
            GetCanvasResolution: function(imageResolution, wPx, hPx){
                if(imageResolution==null){
                        return {
                            width: wPx ,
                            widthOffset: 0,
                            height: hPx ,
                            heightOffset: 0,
                            scaling: 1
                        };
                } else {
                    let imageAspectRatio = imageResolution.width / imageResolution.height;
                    let frameAspectRatio = wPx / hPx;
                    if( imageAspectRatio > frameAspectRatio){
                        // width controls, height gets scaled
                        let scaling = wPx / imageResolution.width;
                        return {
                            width: wPx ,
                            widthOffset: 0,
                            height: imageResolution.height * scaling ,
                            heightOffset: (hPx-imageResolution.height * scaling)/2,
                            scaling: scaling
                        };
                    } else {
                        // height controls, width gets scaled
                        let scaling = hPx / imageResolution.height;
                        return {
                            width: imageResolution.width  * scaling,
                            widthOffset: (wPx-imageResolution.width * scaling)/2,
                            height: hPx,
                            heightOffset: 0,
                            scaling: scaling
                        };
                    }
                }
            },
            getLink: function(){
                let aLink=`${'<?php echo $primary_domain;?>'}/index.php?option=com_thinkiq&task=previewScript&script_name=thinkiq_mv_explorer_${this.context.std_inputs.script_id}.php&script_id=${this.context.std_inputs.script_id}&parent_id=${this.context.std_inputs.parent_id}&node_id=undefined&start=%22${this.startDate.toISOString()}%22&end=%22${this.endDate.toISOString()}%22&gatewayId=%22${this.gateways.find(x=>x.isActive)?.GatewayId}%22&tz=${this.activeTimeZone.id}&databaseName=%22${this.activeDatabase.name}%22`;
                return aLink;
            },
            copyLink: function(){
                clipboard.writeText(this.getLink());
            },
            cloneTab: function(){
                window.open(this.getLink(), '_blank');
            },
            copyTimePeriod: function(){
                clipboard.writeText(`{
                    "start":"${this.startDate.toISOString()}",
                    "end":"${this.endDate.toISOString()}",
                    "tz":"${this.activeTimeZone.value}"
                }`);
            },
            pasteTimePeriod: async function(){
                let data = JSON.parse(await clipboard.readText());
                let aTimeZone = appTimeZones.find(x=>x.value == data.tz);
                this.$refs.datePicker.timezone=aTimeZone.id;
                await delay(100);
                this.startDate=moment(data.start).tz(aTimeZone.value);
                await delay(100);
                this.endDate=moment(data.end).tz(aTimeZone.value);
            },
            getVisionStackAsync: async function (startDate, endDate) {
                this.isLoading = true;
                
                // get last 24h prior to most recent activity
                let last_image_steps = await VisionSdk.GetRecentImageStepsOverallByCountAsync(1, true, this.activeDatabaseName);
                let traffic = await VisionSdk.GetGatewaysAndStepsByTimeRangeAsync(moment(last_image_steps[0].collection_time).subtract(24,'h').format(), moment(last_image_steps[0].collection_time).format(), this.activeDatabaseName);

                let recent_image_steps = await VisionSdk.GetImageStepsByIdsAsync(traffic.map(x=>x.recent_image_step_id), this.activeDatabaseName);
                recent_image_steps_w_media = recent_image_steps.filter(x=>x.images[0]!=null);

                let recent_media_links_signed = await VisionSdk.GetMediaLinksSignedAsync(recent_image_steps_w_media.map(x=>x.images[0]));
                recent_media_links_signed.forEach((x,i)=>{
                    recent_image_steps_w_media[i].media_links_signed = [x];
                });

                // get flat list of host_names
                let hostNames = [...new Set(traffic.map(x=>x.host_name))];
                let gateways = [];
                for(let i=0; i<hostNames.length; i++){
                    let trafficFiltered = traffic.filter(x=>x.host_name==hostNames[i]);
                    //let XSubject = await VisionSdk.XGetSubjectByHostNameAsync(hostNames[i], this.activeDatabaseName);
                    let aGateway = {
                        HostName: trafficFiltered[0].host_name,
                        GatewayId: trafficFiltered[0].gateway_id,
                        // XSubject: XSubject.subject_name,
                        XSubject: false,
                        isActive : false,
                        steps : []
                    };
                    for(let j=0; j<trafficFiltered.length; j++){
                        let aStep = trafficFiltered[j];

                        let recent_image_step = recent_image_steps.find(x=>x.gateway_id==aGateway.GatewayId && x.step_name==aStep.step_name);
                        let recent_image_step_w_media = recent_image_steps_w_media.find(x=>x.gateway_id==aGateway.GatewayId && x.step_name==aStep.step_name);
                        if(recent_image_step){
                            aGateway.steps.push({
                                StepName: aStep.step_name,
                                XMeasure: 'n/a',
                                resultsCount: aStep.results_count,
                                imageStep: recent_image_step,
                                sampleImage: recent_image_step_w_media ? recent_image_step_w_media.images[0] : 'n/a',
                                sampleImageSigned: recent_image_step_w_media ? recent_image_step_w_media.media_links_signed[0] : 'n/a'
                            });

                            // lazy load the XMeasure
                            VisionSdk.XGetMeasureByHostNameAndStepNameAsync(aGateway.HostName, aStep.step_name, this.activeDatabaseName).then(aResult=>{
                                gateways.find(x=>x.HostName==aGateway.HostName).steps.find(x=>x.StepName==aStep.step_name).XMeasure = aResult ? aResult.measure_name : 'n/a';
                            });
                        }
                    }
                    gateways.push(aGateway);

                    // lazy load the XSubject
                    VisionSdk.XGetSubjectByHostNameAsync(hostNames[i], this.activeDatabaseName).then(aResult =>{
                        gateways.find(x=>x.HostName==aGateway.HostName).XSubject = aResult ? aResult.subject_name : 'n/a';
                    });
                }
                this.gateways = gateways.sort((a,b)=>a.HostName > b.HostName ? 1 : -1);

                // let dp=document.getElementById('duration_picker');
                // dp.children[2].className='col-lg-2'; // time duration dropdown
                // dp.children[3].className='col-lg-2'; // time zone
                // dp.children[4].className='col-lg-2'; // pan/zoom controls

                this.isLoading=false;
            },
            activateDatabase: async function(aDatabase){
                if(this.activeDatabase){
                    if(this.activeDatabaseName!=aDatabase.name){
                        this.activeDatabase.isActive = false;
                    }
                }
                aDatabase.isActive=true;
                await this.getVisionStackAsync();
            },
            activateGateway: async function(aGateway){
                if(this.activeGateway){
                    if(this.activeGateway.HostName!=aGateway.HostName){
                        this.activeGateway.isActive = false;
                    }
                }
                aGateway.isActive=true;
                await this.fetchStepOutputdata();

            },
            duration_picker_onDateChange: async function (start_date, end_date) {
                let sd = core.moment(start_date);
                this.startDate = this.activeTimeZone==null ? sd : sd.tz(this.activeTimeZone.value);
                let ed = core.moment(end_date)
                this.endDate = this.activeTimeZone==null ? ed : ed.tz(this.activeTimeZone.value);

                await this.fetchStepOutputdata();
            },
            fetchStepOutputdata: async function(){
                if(!this.activeGateway) return;
    
                this.isLoading = true;
                this.requestId++;
                let thisCallsRequestId = this.requestId;
                this.pops=[];
                this.annotations=[];
                this.mediaLinksSigned={};
                this.stepNames=[];

                // get image steps
                // let imageSteps = [];
                // for(let i=0; i<this.activeGateway.steps.length; i++){
                //     let steps = await VisionSdk.GetImageStepsByTimeRangeAsync(this.activeGateway.GatewayId, this.activeGateway.steps[i].StepName, this.startDate.format(), this.endDate.format(), true, this.activeDatabaseName);
                //     imageSteps = imageSteps.concat(steps);
                // }
                let imageSteps = await VisionSdk.GetImageStepsByTimeRangeAsync(this.activeGateway.GatewayId, null, this.startDate.format(), this.endDate.format(), true, this.activeDatabaseName);
                this.annotations = await VisionSdk.GetAnnotationsByTimeRangeAsync(this.activeGateway.GatewayId, null, this.startDate.format(), this.endDate.format(), this.activeDatabaseName);
                // traverse through response and build stepnames
                // build list of pops
                let pops=[];
                let mediaLinksSigned = {};

                this.activeGateway.steps.forEach(aStep =>{
                    
                    let aStepKey = aStep.StepName;

                    // create stepnameobject to append to stepnames
                    // for instance "gate_a_entry"
                    let aStepNameObject = {
                        functionName: '',
                        // imgNames are the images available per stepname
                        // for instance "scheduled.jpg", "annotated.jpg", etc...
                        imgNames:[
                            {
                                imgCount:0,
                                imgName: 'image_step.json',
                                isActive: false
                            }
                        ],
                        stepName: aStepKey
                    };

                    imageSteps.filter(x=>x.step_name == aStepKey).forEach(aImageStep => {
                        aStepNameObject.functionName = aImageStep.step_function;
                        // add data pop - so we can show json only
                        aStepNameObject.imgNames.find(x=>x.imgName == 'image_step.json').imgCount++;
                        let newPop = {
                            imageStep: aImageStep,
                            stepName: aStepKey,
                            imgName: 'image_step.json',
                            mediaLink: null,
                            isImg: false,
                            isMov: false,
                            imageStepData: {},
                            imageResolution: null,
                            canvasResolution: null,
                            annotations: this.annotations.filter(x=>x.collection_time==aImageStep.collection_time && x.step_name==aImageStep.step_name)
                        };
                        pops.push(newPop);

                        if(aImageStep.images.length>0){
                            // go through images
                            aImageStep.images.forEach(aImage =>{
                                let aImageName = '';
                                if(aImage){
                                    aImageName = aImage.split('_').slice(-1)[0];
                                    mediaLinksSigned[aImage]='';
                                } else {
                                    aImageName = 'n/a';
                                }
                                let aImageSelector = aStepNameObject.imgNames.find(x=>x.imgName == aImageName);
                                if(!aImageSelector){
                                    aImageSelector = {
                                        imgCount:0,
                                        imgName: aImageName,
                                        isActive: false
                                    }
                                    aStepNameObject.imgNames.push(aImageSelector);
                                }
                                aImageSelector.imgCount++;


                                // try to flip the image resolution
                                // this better be temporary
                                try{
                                    if(aImageStep.step_name=='forklift_safety'){
                                        let resW = aImageStep.step_output.media_info.default_jpg.resolution.width;
                                        let resH = aImageStep.step_output.media_info.default_jpg.resolution.height;
                                        aImageStep.step_output.media_info.default_jpg.resolution.width = resH;
                                        aImageStep.step_output.media_info.default_jpg.resolution.height = resW;
                                    }
                                } catch (e){
                                    console.log(e);
                                }

                                let newPop = {
                                    imageStep: aImageStep,
                                    stepName: aStepKey,
                                    imgName: aImageName,
                                    mediaLink: aImage,
                                    isImg: aImageName.endsWith('.jpg'),
                                    isMov: aImageName.endsWith('.mp4'),
                                    imageStepData: {},
                                    imageResolution: {},
                                    canvasResolution: {},
                                    annotations: this.annotations.filter(x=>x.collection_time==aImageStep.collection_time && x.step_name==aImageStep.step_name)
                                };
                                pops.push(newPop);
                            });
                        }
                    });

                    this.stepNames.push(aStepNameObject);

                });
                this.pops = pops.sort((a,b)=>a.imageStep.collection_time < b.imageStep.collection_time ? 1 : -1);
                this.mediaLinksSigned = mediaLinksSigned;

                let signedUrls = await VisionSdk.GetMediaLinksSignedAsync(Object.keys(mediaLinksSigned));

                Object.keys(mediaLinksSigned).forEach((aKey, i) =>{
                    mediaLinksSigned[aKey]= signedUrls[i];
                })

                this.mediaLinksSigned = mediaLinksSigned;

                this.isLoading = false;
                
            },
            TogglePopAnnotationAsync: async function(aPop, aVerb){
                switch(aVerb){
                    case 'isAccurate':
                        if(aPop.annotations.filter(x=>x.is_accurate==false).length==1){
                            // remove the annotation
                            let aAnnotationIndex = aPop.annotations.findIndex(x=>x.is_accurate==false);
                            let aAnnotation = aPop.annotations[aAnnotationIndex];
                            let aResult = await VisionSdk.DeleteAnnotationByIdAsync(aAnnotation.image_step_annotation_id, this.activeDatabaseName);

                            aPop.annotations.splice(aAnnotationIndex, 1);

                            let allAnnotationsIndex = this.annotations.findIndex(x=>x.image_step_annotation_id == aAnnotation.image_step_annotation_id);
                            this.annotations.splice(allAnnotationsIndex, 1);


                        } else {
                            // add an annotation
                            let aQcResult = {
                                image_step_id: aPop.imageStep.image_step_id
                            };
                            if(aPop.imageStep.step_output.active_learning){
                                aQcResult.active_learning = aPop.imageStep.step_output.active_learning;
                            }
                            let aComment = "Annotation by MV Explorer.";
                            let createAnnotationByImageStepId = await VisionSdk.CreateAnnotationByImageStepIdAsync(
                                aPop.imageStep.image_step_id, 
                                this.activeUserId, 
                                aComment, 
                                false, 
                                JSON.stringify(aQcResult), 
                                this.activeDatabaseName
                            );
                            this.annotations.push(createAnnotationByImageStepId);
                            aPop.annotations.push(createAnnotationByImageStepId);
                        }
                        break;
                    default:

                        break;
                }
            },
            DrawAnnotationsAsync: async function(){
                

                this.backingScale = backingScale();

                for(let i=0; i<this.filteredPops.length; i++){
                    const aPop = this.filteredPops[i];
                    
                    let imageResolution = this.GetImageResolution(aPop.imageStep);
                    aPop.imageResolution = imageResolution;

                    let canvasResolution = this.GetCanvasResolution(imageResolution, 400 / this.backingScale, 400 / this.backingScale);
                    aPop.canvasResolution = canvasResolution;

                    await delay(100);

                    const scope = new paper.PaperScope();
                    scope.setup(`cid_${aPop.imageStep.image_step_id}`);

                    scope.view.viewSize.width = this.backingScale == 1 ? aPop.canvasResolution.width - 0 : aPop.canvasResolution.width - 0;
                    scope.view.viewSize.height = this.backingScale == 1 ? aPop.canvasResolution.height - 0 : aPop.canvasResolution.height - 0;

                    // this.scope = new paper.PaperScope();
                    // this.scope.setup(this.canvasId);
                    if(aPop.imageStep.step_output.bboxes){
                        Object.keys(aPop.imageStep.step_output.bboxes).forEach((aKey,i)=>{
                            console.log("Group of bboxes: ", aKey, aPop.imageStep.step_output.bboxes[aKey]);
                            aPop.imageStep.step_output.bboxes[aKey].forEach((aBox) => {
                                console.log(aBox);
                                let newShape = new paper.Shape.Rectangle(
                                    new paper.Point(aBox[0] * aPop.canvasResolution.scaling, aBox[1] * aPop.canvasResolution.scaling), 
                                    new paper.Point((+aBox[0] + +aBox[2]) * aPop.canvasResolution.scaling, (+aBox[1] + +aBox[3]) * aPop.canvasResolution.scaling)
                                );

                                var text = new paper.PointText(new paper.Point(aBox[0] * aPop.canvasResolution.scaling, aBox[1] * aPop.canvasResolution.scaling));
                                text.justification = 'left';
                                text.content = aKey;

                                switch (i){
                                    case 0:
                                        newShape.strokeColor='red';
                                        text.fillColor = 'red';
                                        break;
                                    case 1:
                                        newShape.strokeColor='green';
                                        text.fillColor = 'green';
                                        break;
                                }
                            });
                        });
                    }
                }
            },

            showJsonModal: async function(aPop){
                this.activePop = aPop;
                this.activeImageStepId = aPop.imageStep.image_step_id;
                this.activeImageName = aPop.imgName;
                await delay(100);
                app.$refs.visionMediaPopUp.showJsonModal();
            }
        },
    })
    .component("vision-media-popup-component", visionMediaPopupComponent)
    .mount('#app');
</script>
