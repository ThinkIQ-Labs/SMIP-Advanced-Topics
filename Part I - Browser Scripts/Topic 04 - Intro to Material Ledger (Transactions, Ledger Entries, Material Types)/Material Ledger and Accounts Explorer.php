<?php

use Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.core.min.js', array('version' => 'auto', 'relative' => false));
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.tiqGraphQL.min.js', array('version' => 'auto', 'relative' => false));
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.components.min.js', array('version' => 'auto', 'relative' => false));
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.charts.min.js', array('version' => 'auto', 'relative' => false));


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
                    <tr>
                        <td >{{aMovement.tsPretty}}
                            <a hidden v-bind:href="`?option=com_thinkiq&view=material_flow&memberid=${activeAccount.id}&date=${aMovement.ts}`" target="_blank">
                                <i class="fa fa-code-fork float-end" aria-hidden="true" style="cursor:pointer; color:black;" ></i>
                            </a>
                        </td>
                        <td style="cursor: pointer; border-left: 1px solid #dee2e6;">
                            <button v-if="aMovement.source!=null" style="border:none; padding: revert;" class="list-group-item list-group-item-action" @click="onSelectActivity(aMovement.source)">
                                {{accounts.find(x=>x.id==aMovement.source.accountId).displayName}}
                            </button>
                            <span v-else>--</span>
                        </td>
                        <td>{{aMovement.source==null?'--':Math.round(aMovement.source.startBalance*100)/100}}</td>
                        <td>{{aMovement.source==null?'--':Math.round(aMovement.source.endBalance*100)/100}}</td>
                        <td>
                            {{aMovement.source==null?'--':Math.round(aMovement.source.amount*100)/100}}
                            <span v-if="aMovement.source!=null">
                                <i v-if="aMovement.source.document!=null" 
                                    class="fa fa-book float-end" v-bind:class="{ 'text-success' : aMovement.source.showDoc }" 
                                    style="cursor:pointer" aria-hidden="true" @click="aMovement.source.showDoc = !aMovement.source.showDoc"
                                    data-toggle="tooltip" title="Material Data"></i>
                                <i class="fa fa-pencil float-end" v-bind:class="{ 'text-success' : aMovement.source.showDetail }" 
                                    style="cursor:pointer" aria-hidden="true" @click="aMovement.source.showDetail = !aMovement.source.showDetail"
                                    data-toggle="tooltip" title="Transaction Details"></i>
                            </span>
                            <i v-if="aMovement.source!=null" class="ml-1 fa float-end" v-bind:class="{ 'fa-long-arrow-up text-success': aMovement.source.amount>0, 'fa-long-arrow-down text-danger': aMovement.source.amount<0}" aria-hidden="true"></i>
                        </td>
                        <td style="border-left: 1px solid #dee2e6;">{{Math.round(aMovement.core.startBalance*100)/100}}</td>
                        <td>{{Math.round(aMovement.core.endBalance*100)/100}}</td>
                        <td>
                            {{Math.round(aMovement.core.amount*100)/100}}
                            <span v-if="aMovement.core!=null">
                                <i v-if="aMovement.core.document!=null" 
                                    class="fa fa-book float-end" v-bind:class="{ 'text-success' : aMovement.core.showDoc }" 
                                    style="cursor:pointer" aria-hidden="true" @click="aMovement.core.showDoc = !aMovement.core.showDoc"
                                    data-toggle="tooltip" title="Material Data"></i>
                                <i class="fa fa-pencil float-end" v-bind:class="{ 'text-success' : aMovement.core.showDetail }" 
                                    style="cursor:pointer" aria-hidden="true" @click="aMovement.core.showDetail = !aMovement.core.showDetail"
                                    data-toggle="tooltip" title="Transaction Details"></i>
                            </span>
                            <i v-if="aMovement.core!=null" class="ml-1 fa float-end" v-bind:class="{ 'fa-long-arrow-up text-success': aMovement.core.amount>0, 'fa-long-arrow-down text-danger': aMovement.core.amount<0}" aria-hidden="true"></i>
                        </td>
                        <td style="cursor: pointer; border-left: 1px solid #dee2e6;">
                            <button v-if="aMovement.target!=null" style="border:none; padding: revert;" class="list-group-item list-group-item-action" @click="onSelectActivity(aMovement.target)">
                                {{accounts.find(x=>x.id==aMovement.target.accountId).displayName}}
                            </button>
                            <span v-else>--</span>
                        </td>
                        <td>{{aMovement.target==null?'--':Math.round(aMovement.target.startBalance*100)/100}}</td>
                        <td>{{aMovement.target==null?'--':Math.round(aMovement.target.endBalance*100)/100}}</td>
                        <td>
                            {{aMovement.target==null?'--':Math.round(aMovement.target.amount*100)/100}}
                            <span v-if="aMovement.target!=null">
                                <i v-if="aMovement.target.document!=null" 
                                    class="fa fa-book float-end" v-bind:class="{ 'text-success' : aMovement.target.showDoc }" 
                                    style="cursor:pointer" aria-hidden="true" @click="aMovement.target.showDoc = !aMovement.target.showDoc"
                                    data-toggle="tooltip" title="Material Data"></i>
                                <i class="fa fa-pencil float-end" v-bind:class="{ 'text-success' : aMovement.target.showDetail }" 
                                    style="cursor:pointer" aria-hidden="true" @click="aMovement.target.showDetail = !aMovement.target.showDetail"
                                    data-toggle="tooltip" title="Transaction Details"></i>
                            </span>
                            <i v-if="aMovement.target!=null" class="ml-1 fa float-end" v-bind:class="{ 'fa-long-arrow-up text-success': aMovement.target.amount>0, 'fa-long-arrow-down text-danger': aMovement.target.amount<0}" aria-hidden="true"></i> 
                        </td>
                        <td style="border-left: 1px solid #dee2e6;">
                            <i class="fa fa-ellipsis-v" style="cursor: pointer;" id="dropdownMenu2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                <button class="dropdown-item" type="button">
                                    <a v-bind:href="`?option=com_thinkiq&view=material_flow&memberid=${activeAccount.id}&date=${aMovement.ts}`" target="_blank">
                                        <i class="fa fa-code-fork mr-1" aria-hidden="true" ></i>
                                    Material Flow</a>
                                </button>
                                <button hidden class="dropdown-item" type="button">Edit Transactions</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-show="aMovement.showDetail()">
                        <td></td>
                        <td colspan=4 style="border-left: 1px solid #dee2e6;">
                            <div v-if="aMovement.source!=null">
                            <div v-if="aMovement.source.showDetail" class="form-group px-4">
                                <label class="">Account</label>
                                <input type="text" class="form-control" v-model="aMovement.source.account.displayName" />
                                <label v-if="aMovement.core.material!=null" class="">Material Type</label>
                                <input v-if="aMovement.core.material!=null" type="text" class="form-control" v-model="aMovement.source.material.displayName" />
                                <label class="">Balance Start</label>
                                <input type="text" class="form-control" v-model="aMovement.source.startBalance" />
                                <label>Balance End</label>
                                <input type="text" class="form-control" v-model="aMovement.source.endBalance" />
                                <label>Amount</label>
                                <input type="text" class="form-control" v-model="aMovement.source.amount" />
                                <button disabled class="btn btn-primary float-end mt-4">Update (soon)</button>
                            </div>
                            </div>
                        </td>
                        <td colspan=3 style="border-left: 1px solid #dee2e6;">
                            <div v-if="aMovement.core!=null">
                            <div v-if="aMovement.core.showDetail" class="form-group px-4">
                                <label class="">Account</label>
                                <input type="text" class="form-control" v-model="aMovement.core.account.displayName" />
                                <label v-if="aMovement.core.material!=null" class="">Material Type</label>
                                <input v-if="aMovement.core.material!=null" type="text" class="form-control" v-model="aMovement.core.material.displayName" />
                                <label class="">Balance Start</label>
                                <input type="text" class="form-control" v-model="aMovement.core.startBalance" />
                                <label>Balance End</label>
                                <input type="text" class="form-control" v-model="aMovement.core.endBalance" />
                                <label>Amount</label>
                                <input type="text" class="form-control" v-model="aMovement.core.amount" />
                                <button disabled class="btn btn-primary float-end mt-4">Update (soon)</button>
                            </div>
                            </div>
                        </td>
                        <td colspan=4 style="border-left: 1px solid #dee2e6;">
                            <div v-if="aMovement.target">
                            <div v-if="aMovement.target.showDetail" class="form-group px-4">
                                <label class="">Account</label>
                                <input type="text" class="form-control" v-model="aMovement.target.account.displayName" />
                                <label v-if="aMovement.core.material!=null" class="">Material Type</label>
                                <input v-if="aMovement.core.material!=null" type="text" class="form-control" v-model="aMovement.target.material.displayName" />
                                <label class="">Balance Start</label>
                                <input type="text" class="form-control" v-model="aMovement.target.startBalance" />
                                <label>Balance End</label>
                                <input type="text" class="form-control" v-model="aMovement.target.endBalance" />
                                <label>Amount</label>
                                <input type="text" class="form-control" v-model="aMovement.target.amount" />
                                <button disabled class="btn btn-primary float-end mt-4">Update (soon)</button>
                            </div>
                            </div>
                        </td>
                        <td style="cursor: pointer; border-left: 1px solid #dee2e6;"></td>
                    </tr>
                    <tr v-show="aMovement.showDoc()">
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
                    </tr>                    </template>
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
                activeTimePeriod: 4,
                
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
                let accounts = (await tiqGraphQL.makeRequestAsync(getAccountsQuery)).data.accounts.sort((a,b)=>a.displayName.toLowerCase()>b.displayName.toLowerCase()?1:-1);
                this.accounts = accounts;
            },
            onSelectActivity: function(aActivity){
                if(aActivity!=null){
                    this.onSelectAccount(aActivity.account);
                }
            },
            onSelectAccount: async function(aAccount){
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
                                    }
                                }
                            }
                        }
                    }
                `;

                let aResponse = await tiqGraphQL.makeRequestAsync(getMovementsQuery);
                let accountActivity = aResponse.data.accounts[0].ledgerEntries;

                accountActivity.forEach(aActivity=>{
                    let transaction = aActivity.transaction;
                    let coreActivity = transaction.ledgerEntries.find(x=>x.accountId==aAccount.id);
                    coreActivity.showDetail=false;
                    coreActivity.showDoc=false;
                    let sourceActivity = null;
                    let targetActivity = null;
                    if(coreActivity.amount>0 && transaction.ledgerEntries.length>1){
                        //incoming
                        sourceActivity=transaction.ledgerEntries.find(x=>x.accountId!=aAccount.id);
                        sourceActivity.account = this.accounts.find(x=>x.id==sourceActivity.accountId);
                        sourceActivity.showDetail = false;
                        sourceActivity.showDoc = false;
                    } else if(coreActivity.amount<0 && transaction.ledgerEntries.length>1){
                        //outgoing
                        targetActivity=transaction.ledgerEntries.find(x=>x.accountId!=aAccount.id);
                        targetActivity.account = this.accounts.find(x=>x.id==targetActivity.accountId);
                        targetActivity.showDetail = false;
                        targetActivity.showDoc = false;
                    }
                    this.movements.push({
                        ts: aActivity.endTimestamp,
                        tsPretty: moment(aActivity.endTimestamp).format('MM/DD HH:mm:ss'),
                        source: sourceActivity,
                        core: coreActivity,
                        target: targetActivity,
                        showDoc: function(){
                            if(this.source!=null){
                                if(this.source.showDoc) return true;
                            }
                            if(this.core!=null){
                                if(this.core.showDoc) return true;
                            }
                            if(this.target!=null){
                                if(this.target.showDoc) return true;
                            }
                            return false;
                        },
                        showDetail: function(){
                            if(this.source!=null){
                                if(this.source.showDetail) return true;
                            }
                            if(this.core!=null){
                                if(this.core.showDetail) return true;
                            }
                            if(this.target!=null){
                                if(this.target.showDetail) return true;
                            }
                            return false;
                        }
                    });
                });

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

        }
    })
    .mount('#app');

</script>
