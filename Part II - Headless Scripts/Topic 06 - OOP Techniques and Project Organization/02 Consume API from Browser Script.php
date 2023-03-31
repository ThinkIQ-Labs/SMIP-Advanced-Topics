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

use TiqUtilities\Model\Script;
$fancy_api = new Script('some_library.fancy_api');
$fancy_api->script="";
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

    <input type="text" v-model="input" />
    <button class="btn btn-primary" @click="ClickMe">Click Me</button><br />
    You sent: {{output}}
</div>

<script>
    var WinDoc = window.document;
    
    var app = new Vue({
        el: "#app",
        data() {
            return {
                pageTitle: "Consume API Script",
                context:<?php echo json_encode($context)?>,
                user:<?php echo json_encode($user)?>,
                fancyAPI:<?php echo json_encode($fancy_api)?>,
                input: 'input text',
                output: ''
            }
        },
        mounted: async function () {
            WinDoc.title = this.pageTitle;
        },
        methods: {
            ClickMe: async function(){
                // retrieve output_step data
                let argument={
                    arg_1: this.input
                };
                let apiRoute = `/index.php?option=com_thinkiq&task=invokeScript`;
                let settings = { method: 'POST', headers: {} };
                let formData = new FormData();
                formData.append('script_name', this.fancyAPI.script_file_name);
                formData.append('output_type', 'browser');
                formData.append('function', 'helloWorld');
                formData.append('argument', JSON.stringify(argument));
                settings.body = formData;

                let aResponse = await fetch(apiRoute, settings);
                let aResponseData = await aResponse.json();
                this.output = aResponseData.data.arg_1;
            }
        },
    });
</script>
