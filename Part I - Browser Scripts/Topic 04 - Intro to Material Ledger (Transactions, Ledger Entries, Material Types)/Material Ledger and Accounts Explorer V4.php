<?php

use Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.core.min.js', array('version' => 'auto', 'relative' => false));
// HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.tiqGraphQL.min.js', array('version' => 'auto', 'relative' => false));
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.components.min.js', array('version' => 'auto', 'relative' => false));
// HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.charts.min.js', array('version' => 'auto', 'relative' => false));

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


    <div class="row">
        <div class="col-2">
            <div class="card mb-4" >
                <div class="card-body">
                    <h5 class="card-title">Accounts</h5>
                    <div class="card-text mb-2">
                        <div class="mb-4">
                            <input type="text" v-model="accountsFilter" style="width:16rem;"/>
                            <i class="ml-2 fa fa-search-plus" aria-hidden="true"></i>
                        </div>
                        <div class="list-group" style="max-height: 54rem; overflow-y:auto;">
                            <button v-for="aAccount in accountsFiltered" class="list-group-item list-group-item-action" 
                                v-bind:class="{ active: activeAccount==null ? false : activeAccount.displayName==aAccount.displayName }" 
                                @click="onSelectAccount(aAccount)">
                                {{aAccount.displayName}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-10">

            <duration-picker
                ref="datePicker"
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

            <hr style="border-color:#126181; border-width:medium;" />

            <table v-if="activeAccount!=null" class="table table-sm">
                <thead>
                    <tr>
                        <th scope="col" colspan="1"></th>
                        <th style="border-left: 1px solid #dee2e6;" scope="col" colspan="4">Source</th>
                        <th style="border-left: 1px solid #dee2e6;" scope="col" colspan="3">{{activeAccount.displayName}}</th>
                        <th style="border-left: 1px solid #dee2e6;" scope="col" colspan="4">Target</th>
                        <th style="border-left: 1px solid #dee2e6;" scope="col" colspan="1"></th>
                </tr>
                    <tr>
                        <th scope="col">ts</th>

                        <th style="border-left: 1px solid #dee2e6;" scope="col">Account</th>
                        <th scope="col">Start Balance</th>
                        <th scope="col">End Balance</th>
                        <th scope="col">Amount</th>

                        <th style="border-left: 1px solid #dee2e6;" scope="col">Start Balance</th>
                        <th scope="col">End Balance</th>
                        <th scope="col">Amount</th>

                        <th style="border-left: 1px solid #dee2e6;" scope="col">Account</th>
                        <th scope="col">Start Balance</th>
                        <th scope="col">End Balance</th>
                        <th scope="col">Amount</th>

                        <th style="border-left: 1px solid #dee2e6;" scope="col"></th>

                    </tr>
                </thead>
                <tbody v-if="movements.length>0">
                    <template v-for="aMovement in movements">
                        <template v-for="aIndex in aMovement.maxActivityCount">
                            <tr @click="toggleIsActive(aMovement)" @mouseover="aMovement.isHover = true" @mouseleave="aMovement.isHover = false" 
                            :style="aMovement.isHover ? 'background-color: aliceblue;' : ''">
                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''">
                                    <span v-if="aIndex==1" :style="aMovement.isActive ? 'color: red;' : ''">{{aMovement.tsPretty}}</span>
                                </td>

                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" 
                                    
                                    style="border-left: 1px solid #dee2e6;">
                                    <button v-if="aMovement.sourceActivities.length >= aIndex" style="cursor: pointer; border:none; padding: revert;"  :style="aMovement.isActive ? 'color: red;' : ''" class="list-group-item list-group-item-action" @click="onSelectActivity(aMovement.sourceActivities[aIndex-1], aMovement.transactionId)">
                                        {{accounts.find(x=>x.id==aMovement.sourceActivities[aIndex-1].accountId).displayName}}
                                    </button>
                                </td>
                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" >
                                    <span  v-if="aMovement.sourceActivities.length >= aIndex" :style="aMovement.isActive ? 'color: red;' : ''">{{Math.round(aMovement.sourceActivities[aIndex-1].startBalance*100)/100}}</span>
                                </td>
                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''">
                                    <span  v-if="aMovement.sourceActivities.length >= aIndex"  :style="aMovement.isActive ? 'color: red;' : ''">{{Math.round(aMovement.sourceActivities[aIndex-1].endBalance*100)/100}}</span>
                                </td>
                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" >
                                    <span  v-if="aMovement.sourceActivities.length >= aIndex"  :style="aMovement.isActive ? 'color: red;' : ''">
                                        {{Math.round(aMovement.sourceActivities[aIndex-1].amount*100)/100}}
                                        <span v-if="aMovement.sourceActivities[aIndex-1]!=null">
                                            <i v-if="aMovement.sourceActivities[aIndex-1].document!=null" 
                                                class="fa fa-book float-end" v-bind:class="{ 'text-success' : aMovement.sourceActivities[aIndex-1].showDoc }" 
                                                style="cursor:pointer" aria-hidden="true" @click="aMovement.sourceActivities[aIndex-1].showDoc = !aMovement.sourceActivities[aIndex-1].showDoc"
                                                data-toggle="tooltip" title="Material Data"></i>
                                            <i class="fa fa-pencil float-end" v-bind:class="{ 'text-success' : aMovement.sourceActivities[aIndex-1].showDetail }" 
                                                style="cursor:pointer" aria-hidden="true" @click="aMovement.sourceActivities[aIndex-1].showDetail = !aMovement.sourceActivities[aIndex-1].showDetail"
                                                data-toggle="tooltip" title="Transaction Details"></i>
                                        </span>
                                        <i v-if="aMovement.sourceActivities[aIndex-1]!=null" class="ml-1 fa float-end" v-bind:class="{ 'fa-long-arrow-up text-success': aMovement.sourceActivities[aIndex-1].amount>0, 'fa-long-arrow-down text-danger': aMovement.sourceActivities[aIndex-1].amount<0}" aria-hidden="true"></i>
                                    </span>
                                </td>

                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" style="border-left: 1px solid #dee2e6;">
                                    <span  v-if="aMovement.coreActivities.length >= aIndex" :style="aMovement.isActive ? 'color: red;' : ''">{{Math.round(aMovement.coreActivities[aIndex-1].startBalance*100)/100}}</span>
                                </td>
                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" >
                                    <span  v-if="aMovement.coreActivities.length >= aIndex" :style="aMovement.isActive ? 'color: red;' : ''">{{Math.round(aMovement.coreActivities[aIndex-1].endBalance*100)/100}}</span>
                                </td>
                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" >
                                    <span  v-if="aMovement.coreActivities.length >= aIndex" :style="aMovement.isActive ? 'color: red;' : ''">
                                        {{Math.round(aMovement.coreActivities[aIndex-1].amount*100)/100}}
                                        <span v-if="aMovement.coreActivities[aIndex-1]!=null">
                                            <i v-if="aMovement.coreActivities[aIndex-1].document!=null" 
                                                class="fa fa-book float-end" v-bind:class="{ 'text-success' : aMovement.coreActivities[aIndex-1].showDoc }" 
                                                style="cursor:pointer" aria-hidden="true" @click="aMovement.coreActivities[aIndex-1].showDoc = !aMovement.coreActivities[aIndex-1].showDoc"
                                                data-toggle="tooltip" title="Material Data"></i>
                                            <i class="fa fa-pencil float-end" v-bind:class="{ 'text-success' : aMovement.coreActivities[aIndex-1].showDetail }" 
                                                style="cursor:pointer" aria-hidden="true" @click="aMovement.coreActivities[aIndex-1].showDetail = !aMovement.coreActivities[aIndex-1].showDetail"
                                                data-toggle="tooltip" title="Transaction Details"></i>
                                        </span>
                                        <i v-if="aMovement.coreActivities[aIndex-1]!=null" class="ml-1 fa float-end" v-bind:class="{ 'fa-long-arrow-up text-success': aMovement.coreActivities[aIndex-1].amount>0, 'fa-long-arrow-down text-danger': aMovement.coreActivities[aIndex-1].amount<0}" aria-hidden="true"></i>
                                    </span>
                                </td>

                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" style="cursor: pointer; border-left: 1px solid #dee2e6;">
                                    <button v-if="aMovement.targetActivities.length >= aIndex" style="border:none; padding: revert;"  :style="aMovement.isActive ? 'color: red;' : ''" class="list-group-item list-group-item-action" @click="onSelectActivity(aMovement.targetActivities[aIndex-1], aMovement.transactionId)">
                                        {{accounts.find(x=>x.id==aMovement.targetActivities[aIndex-1].accountId).displayName}}
                                    </button>
                                </td>
                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" >
                                    <span  v-if="aMovement.targetActivities.length >= aIndex" :style="aMovement.isActive ? 'color: red;' : ''">{{Math.round(aMovement.targetActivities[aIndex-1].startBalance*100)/100}}</span>
                                </td>
                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" >
                                    <span  v-if="aMovement.targetActivities.length >= aIndex" :style="aMovement.isActive ? 'color: red;' : ''">{{Math.round(aMovement.targetActivities[aIndex-1].endBalance*100)/100}}</span>
                                </td>
                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" >
                                    <span  v-if="aMovement.targetActivities.length >= aIndex" :style="aMovement.isActive ? 'color: red;' : ''">
                                        {{Math.round(aMovement.targetActivities[aIndex-1].amount*100)/100}}
                                        <span v-if="aMovement.targetActivities[aIndex-1]!=null">
                                            <i v-if="aMovement.targetActivities[aIndex-1].document!=null" 
                                                class="fa fa-book float-end" v-bind:class="{ 'text-success' : aMovement.targetActivities[aIndex-1].showDoc }" 
                                                style="cursor:pointer" aria-hidden="true" @click="aMovement.targetActivities[aIndex-1].showDoc = !aMovement.targetActivities[aIndex-1].showDoc"
                                                data-toggle="tooltip" title="Material Data"></i>
                                            <i class="fa fa-pencil float-end" v-bind:class="{ 'text-success' : aMovement.targetActivities[aIndex-1].showDetail }" 
                                                style="cursor:pointer" aria-hidden="true" @click="aMovement.targetActivities[aIndex-1].showDetail = !aMovement.targetActivities[aIndex-1].showDetail"
                                                data-toggle="tooltip" title="Transaction Details"></i>
                                        </span>
                                        <i v-if="aMovement.targetActivities[aIndex-1]!=null" class="ml-1 fa float-end" v-bind:class="{ 'fa-long-arrow-up text-success': aMovement.targetActivities[aIndex-1].amount>0, 'fa-long-arrow-down text-danger': aMovement.targetActivities[aIndex-1].amount<0}" aria-hidden="true"></i> 
                                    </span>
                                </td>

                                <td :style="aIndex < aMovement.maxActivityCount || aMovement.showDetail(aIndex) ? 'border-bottom: 0px solid #dee2e6;' : ''" style="border-left: 1px solid #dee2e6;">
                                    <span v-if="aIndex==1">
                                        <i class="fa fa-ellipsis-v" style="cursor: pointer;" id="dropdownMenu2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                            <button class="dropdown-item" type="button">
                                                <!-- applications/material-flow/?accountId=197462&date=2023-12-07T20%3A00%3A00-05%3A00 -->
                                                <a v-bind:href="`applications/material-flow/?accountId=${activeAccount.id}&date=${encodeURIComponent(aMovement.ts)}`" target="_blank">
                                                    <i class="fa fa-code-fork mr-1" aria-hidden="true" ></i>
                                                Material Flow</a>
                                            </button>
                                            <button class="dropdown-item" type="button" @click="DeleteTransactionAsync(aMovement.transactionId)">
                                                <i class="fa fa-trash mr-1" aria-hidden="true" ></i>
                                                Delete Transaction
                                            </button>
                                            <button hidden class="dropdown-item" type="button">Edit Transactions</button>
                                        </div>
                                    </span>
                                </td>
                            </tr>

                            <tr v-show="aMovement.showDetail(aIndex)">
                                <td :style="aIndex < aMovement.maxActivityCount ? 'border-bottom: 0px solid #dee2e6;' : ''"></td>
                                <td colspan=4 :style="aIndex < aMovement.maxActivityCount ? 'border-bottom: 0px solid #dee2e6;' : ''" style="border-left: 1px solid #dee2e6;">
                                    <div v-if="aMovement.sourceActivities.length >= aIndex">
                                    <div v-if="aMovement.sourceActivities[aIndex-1].showDetail" class="form-group px-4">
                                        <label class="">Account</label>
                                        <input type="text" class="form-control" v-model="aMovement.sourceActivities[aIndex-1].account.displayName" />
                                        <label v-if="aMovement.sourceActivities[aIndex-1].material!=null" class="">Material Type</label>
                                        <input v-if="aMovement.sourceActivities[aIndex-1].material!=null" type="text" class="form-control" v-model="aMovement.sourceActivities[aIndex-1].material.displayName" />
                                        <label class="">Balance Start</label>
                                        <input type="text" class="form-control" v-model="aMovement.sourceActivities[aIndex-1].startBalance" />
                                        <label>Balance End</label>
                                        <input type="text" class="form-control" v-model="aMovement.sourceActivities[aIndex-1].endBalance" />
                                        <label>Amount</label>
                                        <input type="text" class="form-control" v-model="aMovement.sourceActivities[aIndex-1].amount" />
                                        <button disabled class="btn btn-primary float-end mt-4">Update (soon)</button>
                                    </div>
                                    </div>
                                </td>
                                <td colspan=3 :style="aIndex < aMovement.maxActivityCount ? 'border-bottom: 0px solid #dee2e6;' : ''" style="border-left: 1px solid #dee2e6;">
                                    <div v-if="aMovement.coreActivities.length >= aIndex">
                                    <div v-if="aMovement.coreActivities[aIndex-1].showDetail" class="form-group px-4">
                                        <label class="">Account</label>
                                        <input type="text" class="form-control" v-model="aMovement.coreActivities[aIndex-1].account.displayName" />
                                        <label v-if="aMovement.coreActivities[aIndex-1].material!=null" class="">Material Type</label>
                                        <input v-if="aMovement.coreActivities[aIndex-1].material!=null" type="text" class="form-control" v-model="aMovement.coreActivities[aIndex-1].material.displayName" />
                                        <label class="">Balance Start</label>
                                        <input type="text" class="form-control" v-model="aMovement.coreActivities[aIndex-1].startBalance" />
                                        <label>Balance End</label>
                                        <input type="text" class="form-control" v-model="aMovement.coreActivities[aIndex-1].endBalance" />
                                        <label>Amount</label>
                                        <input type="text" class="form-control" v-model="aMovement.coreActivities[aIndex-1].amount" />
                                        <button disabled class="btn btn-primary float-end mt-4">Update (soon)</button>
                                    </div>
                                    </div>
                                </td>
                                <td colspan=4 :style="aIndex < aMovement.maxActivityCount ? 'border-bottom: 0px solid #dee2e6;' : ''" style="border-left: 1px solid #dee2e6;">
                                    <div v-if="aMovement.targetActivities.length >= aIndex">
                                    <div v-if="aMovement.targetActivities[aIndex-1].showDetail" class="form-group px-4">
                                        <label class="">Account</label>
                                        <input type="text" class="form-control" v-model="aMovement.targetActivities[aIndex-1].account.displayName" />
                                        <label v-if="aMovement.targetActivities[aIndex-1].material!=null" class="">Material Type</label>
                                        <input v-if="aMovement.targetActivities[aIndex-1].material!=null" type="text" class="form-control" v-model="aMovement.targetActivities[aIndex-1].material.displayName" />
                                        <label class="">Balance Start</label>
                                        <input type="text" class="form-control" v-model="aMovement.targetActivities[aIndex-1].startBalance" />
                                        <label>Balance End</label>
                                        <input type="text" class="form-control" v-model="aMovement.targetActivities[aIndex-1].endBalance" />
                                        <label>Amount</label>
                                        <input type="text" class="form-control" v-model="aMovement.targetActivities[aIndex-1].amount" />
                                        <button disabled class="btn btn-primary float-end mt-4">Update (soon)</button>
                                    </div>
                                    </div>
                                </td>
                                <td :style="aIndex < aMovement.maxActivityCount ? 'border-bottom: 0px solid #dee2e6;' : ''" style="cursor: pointer; border-left: 1px solid #dee2e6;"></td>
                            </tr>
                            <tr v-show="aMovement.showDoc(aIndex)">
                                <td></td>
                                <td colspan=4 style="border-left: 1px solid #dee2e6;">
                                    <div v-if="aMovement.source!=null">
                                    <pre v-if="aMovement.source.showDoc" class="language-json">
        <code v-if="aMovement.source.document!=null" class="language-json">{{JSON.stringify(JSON.parse(aMovement.source.document), null, 4)}}</code>
                                    </pre>
                                    </div>
                                </td>
                                <td colspan=3 style="border-left: 1px solid #dee2e6;">
                                    <div v-if="aMovement.core!=null">
                                    <pre v-if="aMovement.core.showDoc" class="language-json">
        <code v-if="aMovement.core.document!=null" class="language-json">{{JSON.stringify(JSON.parse(aMovement.core.document), null, 4)}}</code>
                                    </pre>
                                    </div>
                                </td>
                                <td colspan=4 style="border-left: 1px solid #dee2e6;">
                                    <div v-if="aMovement.target!=null">
                                    <pre v-if="aMovement.target.showDoc" class="language-json">
        <code v-if="aMovement.target.document!=null" class="language-json">{{JSON.stringify(JSON.parse(aMovement.target.document), null, 4)}}</code>
                                    </pre>
                                    </div>
                                </td>
                                <td style="cursor: pointer; border-left: 1px solid #dee2e6;"></td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>


        </div>
    </div>

</div>

<script>
    var WinDoc = window.document;

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

    var appTimePeriods = [
        {"id":"1","name":"Last 30 minutes","duration":"PT30M","end_date":"now"},
        {"id":"2","name":"Last 1 hour","duration":"PT1H","end_date":"now"},
        {"id":"3","name":"Last 2 hour","duration":"PT2H","end_date":"now"},
        {"id":"4","name":"Last 6 hour","duration":"PT6H","end_date":"now"},
        {"id":"5","name":"Last 12 hour","duration":"PT12H","end_date":"now"},
        {"id":"6","name":"Last 24 hour","duration":"PT24H","end_date":"now"},
        {"id":"7","name":"Last 48 hour","duration":"PT48H","end_date":"now"},
        {"id":"8","name":"Last 7 days","duration":"PD7D","end_date":"now"}
    ];

    var app=createApp({
        // el: "#app",
        data() {
            return { 
                tz: JSON.stringify(appTimeZones),
                tp: JSON.stringify(appTimePeriods),
                startDate: core.moment().tz(appTimeZones[0].value).add(-6,'h'),
                endDate: core.moment().tz(appTimeZones[0].value),
                activeTimePeriod: 0,
                
                pageTitle: "Material Ledger and Accounts Explorer",
                context:<?php echo json_encode($context)?>,
                user:<?php echo json_encode($user)?>,
                accounts:[],
                accountsFilter:'',
                activeAccount:null,
                movements:[]

            }
        },
        computed:{
            activeTimeZone: function(){
                return this.$refs.datePicker.timezone=='0' ? null : appTimeZones.find(x=>x.id==this.$refs.datePicker.timezone);
            },
            accountsFiltered: function(){
                if(this.accountsFilter=='') return this.accounts;
                let accountsFilters = this.accountsFilter.toLowerCase().split(' ').filter(x=>x!='');
                let returnDict = {};
                accountsFilters.forEach(aFilter=>{
                    this.accounts.filter(aAccount=>aAccount.displayName.toLowerCase().includes(aFilter)).forEach(aFilteredAccount=>{
                        returnDict[aFilteredAccount.id]=aFilteredAccount;
                    })
                })
                return Object.values(returnDict);
            },
            activeMovement: function(){
                return this.movements.find(x=>x.isActive);
            }
        },
        async mounted(){
            WinDoc.title = this.pageTitle;

            // let dp=document.getElementById('duration_picker');
            // dp.children[2].className='col-lg-2'; // time duration dropdown
            // dp.children[3].className='col-lg-2'; // time zone
            // dp.children[4].className='col-lg-2'; // pan/zoom controls

            await this.getAccountsAsync();
        },
        methods: {
            toggleIsActive: function(aMovement){
                if(this.activeMovement){
                    if(this.activeMovement.transactionId != aMovement.transactionId){
                        this.activeMovement.isActive = false;
                        aMovement.isActive = true;
                    }
                } else {
                    aMovement.isActive = true;
                }
            },
            getAccountsAsync: async function(){
                this.activeAccount=null;
                let getAccountsQuery = `
                    query q1 {
                        accounts {
                            id
                            displayName
                            relativeName
                            fqn
                            partOf {
                                id
                                displayName
                                relativeName
                                fqnList
                            }
                        }
                    }
                `;
                let accounts = (await tiqJSHelper.invokeGraphQLAsync(getAccountsQuery)).data.accounts.sort((a,b)=>a.displayName.toLowerCase()>b.displayName.toLowerCase()?1:-1);
                this.accounts = accounts;
            },
            onSelectActivity: function(aActivity, aTransactionId){
                if(aActivity!=null){
                    this.toggleIsActive(this.movements.find(x=>x.transactionId==aTransactionId));
                    this.onSelectAccount(aActivity.account);
                }
            },
            onSelectAccount: async function(aAccount){
                let previousMovementId = this.activeMovement ? this.activeMovement.transactionId : null;
                this.activeAccount=aAccount;
                this.movements = [];
                let getMovementsQuery = `
                    query q1 {
                        accounts(condition: { id: "${aAccount.id}" }) {
                            id
                            displayName
                            ledgerEntries(
                                filter: {
                                    startTimestamp: { greaterThanOrEqualTo: "${this.startDate.toISOString()}" }
                                    and: { startTimestamp: { lessThanOrEqualTo: "${this.endDate.toISOString()}" } }
                                }
                                orderBy: START_TIMESTAMP_DESC
                            ) {
                                endTimestamp
                                transaction {
                                    id
                                    ledgerEntries {
                                        startTimestamp
                                        endTimestamp
                                        accountId
                                        account{
                                            id
                                            displayName
                                        }
                                        amount
                                        startBalance
                                        endBalance
                                        document
                                        materialId
                                        material{
                                            id
                                            displayName
                                        }
                                        materialAttributes
                                    }
                                }
                            }
                        }
                    }
                `;

                let aResponse = await tiqJSHelper.invokeGraphQLAsync(getMovementsQuery);
                let accountActivity = aResponse.data.accounts[0].ledgerEntries;

                accountActivity.forEach(aActivity=>{
                    let transaction = aActivity.transaction;
                    let coreActivities = transaction.ledgerEntries.filter(x=>x.accountId==aAccount.id);
                    coreActivities.forEach(aCoreActivity=>{
                        aCoreActivity.showDetail=false;
                        aCoreActivity.showDoc=false;
                    });
                    
                    let sourceActivities = [];
                    let targetActivities = [];

                    transaction.ledgerEntries.filter(x=>x.accountId!=aAccount.id).forEach(aActivity => {
                        aActivity.account = this.accounts.find(x=>x.id==aActivity.accountId);
                        aActivity.showDetail = false;
                        aActivity.showDoc = false;
                        if(aActivity.amount<0){

                            sourceActivities.push(aActivity);

                        } else if(aActivity.amount>0){

                            targetActivities.push(aActivity);

                        } else{

                            console.log('amount=0 - this doesnt makes sense');
                            sourceActivities.push(aActivity);

                        }
                    });
                    // if(coreActivities[0].amount>0 && transaction.ledgerEntries.length>1){
                    //     //incoming
                    //     sourceActivities=transaction.ledgerEntries.filter(x=>x.accountId!=aAccount.id);
                    //     sourceActivities.forEach(aSourceActivity=>{
                    //         aSourceActivity.account = this.accounts.find(x=>x.id==aSourceActivity.accountId);
                    //         aSourceActivity.showDetail = false;
                    //         aSourceActivity.showDoc = false;
                    //         sourceActivities.push(aSourceActivity);
                    //     });
                        
                    // } else if(coreActivities[0].amount<0 && transaction.ledgerEntries.length>1){
                    //     //outgoing
                    //     targetActivities=transaction.ledgerEntries.filter(x=>x.accountId!=aAccount.id);
                    //     targetActivities.forEach(aTargetActivity =>{
                    //         aTargetActivity.account = this.accounts.find(x=>x.id==aTargetActivity.accountId);
                    //         aTargetActivity.showDetail = false;
                    //         aTargetActivity.showDoc = false;
                    //         targetActivities.push(aTargetActivity);
                    //     });
                    // }
                    this.movements.push({
                        ts: aActivity.endTimestamp,
                        tsPretty: moment(aActivity.endTimestamp).format('MM/DD HH:mm:ss.SSS'),
                        transactionId: transaction.id,
                        sourceActivities: sourceActivities,
                        coreActivities: coreActivities,
                        targetActivities: targetActivities,
                        maxActivityCount: Math.max(sourceActivities.length, coreActivities.length, targetActivities.length),
                        isActive: false,
                        isHover: false,
                        showDoc: function(i){  // i is 1-based, so 1, 2, 3
                            if(this.sourceActivities.length>=i){
                                if(this.sourceActivities[i-1].showDoc) return true;
                            }
                            if(this.coreActivities.length>=i){
                                if(this.coreActivities[i-1].showDoc) return true;
                            }
                            if(this.targetActivities.length>=i){
                                if(this.targetActivities[i-1].showDoc) return true;
                            }
                            return false;
                        },
                        showDetail: function(i){  // i is 1-based, so 1, 2, 3
                            if(this.sourceActivities.length>=i){
                                if(this.sourceActivities[i-1].showDetail) return true;
                            }
                            if(this.coreActivities.length>=i){
                                if(this.coreActivities[i-1].showDetail) return true;
                            }
                            if(this.targetActivities.length>=i){
                                if(this.targetActivities[i-1].showDetail) return true;
                            }
                            return false;
                        }
                    });
                });
                if(previousMovementId!=null){
                    let activeMovement = this.movements.find(x=>x.transactionId==previousMovementId);
                    if(activeMovement){
                        activeMovement.isActive = true;
                    }
                }
            },
            duration_picker_onDateChange: async function (start_date, end_date) {
                let sd = core.moment(start_date);
                this.startDate = this.activeTimeZone==null ? sd : sd.tz(this.activeTimeZone.value);
                let ed = core.moment(end_date)
                this.endDate = this.activeTimeZone==null ? ed : ed.tz(this.activeTimeZone.value);

                if(this.activeAccount){
                    await this.onSelectAccount(this.activeAccount);
                }
            },
            DeleteAllTransactionsAsync: async function(){
                todo = this.movements.length;
                done = 0;
                for(aMovement of this.movements){
                    done++;
                    console.log(`${done} of ${todo}: aMovement.transactionId`);
                    await this.DeleteTransactionAsync(aMovement.transactionId, false);
                }
                await this.onSelectAccount(this.activeAccount);
            },
            DeleteTransactionAsync: async function (aTransactionId, refreshModel = true){
                let entriesQuery = `
                    query q1 {
                        transaction(id:"${aTransactionId}"){
                            ledgerEntries{
                                id
                            }
                        }
                    }`;
                let aEntriesQueryResponse = await tiqJSHelper.invokeGraphQLAsync(entriesQuery);

                for(let i=0; i<aEntriesQueryResponse.data.transaction.ledgerEntries.length; i++){
                    let deleteLedgerEntryQuery = `
                        mutation m1 {
                            deleteLedgerEntry(input: { id: "${aEntriesQueryResponse.data.transaction.ledgerEntries[i].id}" }) {
                                deletedLedgerEntryNodeId
                            }
                        }                
                    `;
                    let aResponse = await tiqJSHelper.invokeGraphQLAsync(deleteLedgerEntryQuery);
                }

                let deleteTransactionQuery = `
                    mutation m1 {
                        deleteTransaction(input: { id: "${aTransactionId}" }) {
                            deletedTransactionNodeId
                        }
                    }`;
                let aResponse = await tiqJSHelper.invokeGraphQLAsync(deleteTransactionQuery);

                if(refreshModel){
                    await this.onSelectAccount(this.activeAccount);
                }
            }
        }
    })
    .mount('#app');

</script>
