<?php
use Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.core.js', array('version' => 'auto', 'relative' => false));
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.charts.js', array('version' => 'auto', 'relative' => false));
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.components.js', array('version' => 'auto', 'relative' => false));
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.tiqGraphQL.min.js', array('version' => 'auto', 'relative' => false));

?>

<div id="app">
    <div class="row">            
        <div class="col-12">
            <h1 class="pb-2 text-center">TIQ Component Examples</h1>
            <p class="pb-4 text-center">
                <a href="?option=com_modeleditor&view=script&id=19308760" target="_blank">source</a>
            </p>
        </div>            
    </div> 

    <div class="row">
        <duration-picker
            ref="datePicker"
            :show-player-controls='true'
            :show-zoom-controls='true'
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
            @on_date_change="duration_picker_onDateChange"
        ></duration-picker>
    </div>

    <div class="row">
        <div v-for="aInt in attributeIds" class="col-6">
            <line-chart
                :line-chart-name="`line_chart_${aInt}`"
                :ref="`line_chart_${aInt}`"
                :attribute-ids="[aInt]"
                :start-date="startDate.format()"
                :end-date="endDate.format()"
                :max-num-of-points="100"
                :width="null"
                :height="500"
                :show-error="true"
                loader-mode="Small"
                :display-mode-bar="true"
            ></line-chart>
        </div>
    </div>

        <div class="row mb-4 mt-4">
        <div class="col-4">
            <h4>Concepts</h4>
            <p>There are a couple of important principles to effectively mesh-up ThinkIQ components using Vue.js. This page utilizes a date range picker and a couple of charts. The two most central 
            data items to control are the start and end time, including their timezone. This is controlled by the vue app. ThinkIQ uses moment.js with its excellent timezone support. The controls 
            are responsible for picking times and fetching time series data; they prefer regular well formated timestamps with timezone offset, for instance '2022-02-02T10:30:20-08:00'</p>

            <h4>Vue.app Time Objects and Display Components</h4>
            <p>We have 2 moment.js objects in our vue.app.data structure:</p>
            <pre><code>
startDate: core.moment().tz(appTimeZones[0].value).add(-2,'h') 
endDate: core.moment().tz(appTimeZones[0].value)
            </code></pre>
            <p>To take care of converting these time zone aware objects into well behaved timestamp strings, we bind them into the date picker and the chart chontrols like so:</p>
            <pre><code>
:start-date="startDate.format()"
:end-date="endDate.format()"
            </code></pre>
        </div>
        <div class="col-4">
            <h4>Time-Periods and Time-Zones</h4>
            <p>Notice how we have dropdowns with non-typical time-periods and time-zones: we can pass lists of custom settings like these into our view components as serialized json arrays.</p>
            <pre><code>
// in a script tag</br>
var appTimeZones = [
    {
        "id":"1",
        "name":"Niagra Falls Power Station",
        "value":"America/New_York"
    },
    {
        "id":"2",
        "name":"ThinkIQ HQ",
        "value":"America/Los_Angeles"
    }
];

var appTimePeriods = [
    {"id":"1","name":"Last 30 seconds","duration":"PT30S","end_date":"now"},
    {"id":"2","name":"Last 1 minute","duration":"PT1M","end_date":"now"},
    {"id":"3","name":"Last 2 minute","duration":"PT2M","end_date":"now"},
    {"id":"4","name":"Last 5 minutes","duration":"PT5M","end_date":"now"},
    {"id":"5","name":"Last 1 hour","duration":"PT1H","end_date":"now"},
    {"id":"6","name":"Last 2 hour","duration":"PT2H","end_date":"now"},
    {"id":"7","name":"Last 6 hour","duration":"PT6H","end_date":"now"},
    {"id":"8","name":"Last 12 hour","duration":"PT12H","end_date":"now"}
];

// in the vue.app.data structure
tz: JSON.stringify(appTimeZones),
tp: JSON.stringify(appTimePeriods),

// on the display component
:timezones="tz" 
:time-periods="tp"
            </code></pre>
        </div>
        <div class="col-4">
            <h4>Controlling the Page Start-Up</h4>
            <p>There is one minor draw-back, when we choose to apply "default picks" for active-timezone and active-time-period. Those don't sync up at the start of the page. Instead, we have to 
            initialize them with matching values in the vue.app.data structure. Once the page is running, however, things go smoothly by utilizing the onDateChange trigger of the date picker 
            component. The method triggers when times, dates, or time zones are changed. Be careful not to overfetch data when a user picks a start-date several months in the past. Below are two 
            code snippets for applying time changes and updating the active timezone:</p>
            <pre><code>
computed: {
    activeTimeZone: function(){
        return appTimeZones.find(x=>x.id==this.$refs.datePicker.timezone);
    }
},
methods: {
    duration_picker_onDateChange: function (start_date, end_date) {
        this.startDate = core.moment(start_date).tz(this.activeTimeZone.value);
        this.endDate = core.moment(end_date).tz(this.activeTimeZone.value);
    },
            </code></pre>
            <hr>
            <br/>
            <h4>Appendix: TimeZones in PostgreSQL</h4>
            <pre><code>
SELECT floatvalue, timestamp, timestamp AT TIME ZONE 'America/New_York' 
FROM tag_history_floats LIMIT 100
            </code></pre>
            <hr>
            <br/>
            <h4>Appendix: TimeZones in PhP</h4>
            <pre><code>
// creates a new UTC DateTime object
$t = new DateTime();

// converts the DateTime object to a timezone
$tz = new DateTimeZone('America/Chicago');
$t->setTimezone($tz);

// creates a DateTime object from
// a local timestamp string and a timezone
$startDate = new DateTime("2021-12-02 09:00:00", $tz);
            </code></pre>
        </div>
    </div>

</div>

<script>

    var appTimeZones = [
        {
            "id":"1",
            "name":"Niagra Falls Power Station",
            "value":"America/New_York"
        },
        {
            "id":"2",
            "name":"ThinkIQ HQ",
            "value":"America/Los_Angeles"
        }
    ];

    var appTimePeriods = [
        {"id":"1","name":"Last 30 seconds","duration":"PT30S","end_date":"now"},
        {"id":"2","name":"Last 1 minute","duration":"PT1M","end_date":"now"},
        {"id":"3","name":"Last 2 minute","duration":"PT2M","end_date":"now"},
        {"id":"4","name":"Last 5 minutes","duration":"PT5M","end_date":"now"},
        {"id":"5","name":"Last 1 hour","duration":"PT1H","end_date":"now"},
        {"id":"6","name":"Last 2 hour","duration":"PT2H","end_date":"now"},
        {"id":"7","name":"Last 6 hour","duration":"PT6H","end_date":"now"},
        {"id":"8","name":"Last 12 hour","duration":"PT12H","end_date":"now"}
    ];

    var app = new core.Vue({
        el: "#app",
        data(){
            return {
                tz: JSON.stringify(appTimeZones),
                tp: JSON.stringify(appTimePeriods),
                // startDateTime: - a moment js object with timezone
                // endDateTime: - a moment js object with timezone
                attributeIds:['18349562','163'],
                startDate: core.moment().tz(appTimeZones[0].value).add(-2,'h'),
                endDate: core.moment().tz(appTimeZones[0].value),
                activeTimePeriod: 6
            }
        },
        computed: {
            activeTimeZone: function(){
                return this.$refs.datePicker.timezone=='0' ? null : appTimeZones.find(x=>x.id==this.$refs.datePicker.timezone);
            }
        },
        methods: {
            duration_picker_onDateChange: function (start_date, end_date) {
                let sd = core.moment(start_date);
                this.startDate = this.activeTimeZone==null ? sd : sd.tz(this.activeTimeZone.value);
                let ed = core.moment(end_date)
                this.endDate = this.activeTimeZone==null ? ed : ed.tz(this.activeTimeZone.value);
            },
        }
    });


</script>