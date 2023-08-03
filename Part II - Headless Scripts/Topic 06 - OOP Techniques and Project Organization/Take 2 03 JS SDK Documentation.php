<?php

    use Joomla\CMS\HTML\HTMLHelper;

    $primary_domain = 'https://' . $_SERVER['HTTP_HOST'];

    HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.core.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));
    // HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.tiqGraphQL.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));
    // HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.components.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));
    // HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.charts.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));

    require_once 'thinkiq_context.php';
    $context = new Context();

    TiqUtilities\Model\Script::includeScript('api_demo.api_demo__hyphen__javascript_sdk');

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

    <h1>
        Echo
        <span class="float-start me-2"><i :class="showEcho ? 'fa fa-caret-up' : 'fa fa-caret-down'" @click="showEcho=!showEcho" ></i></span>
        <span class="h6">Returns an echo. Strings, numerics, proper json.</span>
    </h1>
    <div :hidden="!showEcho">
        <label class="me-2">Input: </label>
        <input type="text" v-model="echoInput" />
        <br />
        <pre>
<code>{{JSON.stringify(ValidateAndReturnJson(echoInput), null, 2)}}
            </code>
        </pre>        
        <button @click="EchoAsync">Click</button>
        <button @click="echoOutput=null">Clear</button>
        <pre>
<code>{{JSON.stringify(echoOutput, null, 2)}}
            </code>
        </pre>
    </div>

    <hr />

    <h1>
        GetLibraryNames
        <span class="float-start me-2"><i :class="showGetLibraryNames ? 'fa fa-caret-up' : 'fa fa-caret-down'" @click="showGetLibraryNames=!showGetLibraryNames" ></i></span>
        <span class="h6">Returns the names of all libraries in this SMIP.</span>
    </h1>
    <div :hidden="!showGetLibraryNames">
        <button @click="GetLibraryNamesAsync">Click</button>
        <button @click="getLibraryNames=[]">Clear</button>
        <pre>
<code>{{JSON.stringify(getLibraryNames, null, 2)}}
            </code>
        </pre>
    </div>

    <hr />

    <h1>
        GetLibraryByName
        <span class="float-start me-2"><i :class="showGetLibraryByName ? 'fa fa-caret-up' : 'fa fa-caret-down'" @click="showGetLibraryByName=!showGetLibraryByName" ></i></span>
        <span class="h6">Returns a libraries from this SMIP by name.</span>
    </h1>
    <div :hidden="!showGetLibraryByName">
        <label class="me-2">Input: </label>
        <input type="text" v-model="getLibraryByNameInput" />
        <br />
        <button @click="GetLibraryByNameAsync">Click</button>
        <button @click="getLibraryByName={}">Clear</button>
        <pre>
<code>{{JSON.stringify(getLibraryByName, null, 2)}}
            </code>
        </pre>
    </div>

    <hr />

</div>

<script>
    var WinDoc = window.document;

    var app = new Vue({
        el: "#app",
        data() {
            return {
                pageTitle: 'API Demo.JS - a JavaScript SDK for Accessing the SMIP',
                pageTitleShort: 'API Demo.JS Docs',
                context:<?php echo json_encode($context)?>,

                echoInput: '{"a":[23, 23, "asd"]}',
                echoOutput: null,
                showEcho: false,

                getLibraryNames: [],
                showGetLibraryNames: false,

                getLibraryByNameInput: 'ThinkIQ Base Library',
                getLibraryByName: {},
                showGetLibraryByName: false

            }
        },
        mounted: function(){
            WinDoc.title = this.pageTitleShort;
        },
        methods: {
            ValidateAndReturnJson: function(aText){
                try{
                    return JSON.parse(aText);
                } catch (e){
                    return "That's not valid json.";
                }
            },
            EchoAsync: async function(){
                this.echoOutput = await ApiDemoSdk.EchoAsync(JSON.parse(this.echoInput));
            },

            GetLibraryNamesAsync: async function(){
                this.getLibraryNames = await ApiDemoSdk.GetLibraryNamesAsync();
            },

            GetLibraryByNameAsync: async function(){
                this.getLibraryByName = await ApiDemoSdk.GetLibraryByNameAsync(this.getLibraryByNameInput);
            }
            
        }
    });

</script>