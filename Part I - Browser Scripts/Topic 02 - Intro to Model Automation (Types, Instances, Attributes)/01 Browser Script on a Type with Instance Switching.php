<?php

use Joomla\CMS\HTML\HTMLHelper;

$primary_domain = 'https://' . $_SERVER['HTTP_HOST'];

HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.core.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));
HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.components.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));
HTMLHelper::_('script', "$primary_domain/media/com_thinkiq/js/dist/tiq.charts.js", array('version' => 'auto', 'relative' => false, 'detectDebug' => false));


require_once 'thinkiq_context.php';
$context = new Context();

use TiqUtilities\Model\GenericObject;
 
// $aHost = new GenericObject($context->std_inputs->node_id);
// $aHost->getAttributes(lazy_load:true);
// $aHost->getType();
// $aHost->type->getInstances();
// foreach($aHost->type->instances as $aInstance){
//     $aInstance->getAttributes(lazy_load:true);
// }

// using aTinyHost to keep the footprint in the DOM smaller
$aHost = new GenericObject($context->std_inputs->node_id);
$aTinyHost = new stdClass();
$aTinyHost->id = $aHost->id;
$aTinyHost->display_name = $aHost->display_name;
$aTinyHost->type = new stdClass();
$aTinyHost->type->instances = [];

$aHost->getType();
$aHost->type->getInstances();
foreach($aHost->type->instances as $aInstance){
    $aInstance->getAttributes(lazy_load:true);
    $aTinyInstance = new stdClass();
    $aTinyInstance->id = $aInstance->id;
    $aTinyInstance->display_name = $aInstance->display_name;
    $aTinyHost->type->instances[$aInstance->id] = $aTinyInstance;
}


?>

<div id="app">

    <div class="row">            
        <div class="col-12">
            <h1 class="pb-2 pt-2" style="font-size:2.5rem; color:#126181;">
                {{pageTitle}}: {{ActiveInstance ? ActiveInstance.display_name : ''}}
                <a v-if="true" class="float-end btn btn-sm btn-link mt-2" style="font-size:1rem; color:#126181;" v-bind:href="`/index.php?option=com_modeleditor&view=script&id=${context.std_inputs.script_id}`" target="_blank">source</a>
            </h1>
            <div>
                <button class="btn btn-sm me-3" v-bind:class="!aInstance.isActive ? 'btn-light' : 'btn-secondary'" v-for="aInstance in AllInstances" @click="activateInstance(aInstance)">{{aInstance.display_name}}</button>
            </div>
            <hr style="border-color:#126181; border-width:medium;" />
        </div>   
    </div>

    <div class="row">
        <div class="col-12" v-if="ActiveInstance">
            <div class="card w-25">
                <div class="card-body">
                    <h5 class="card-title">{{ActiveInstance.display_name}}</h5>
                    <p class="card-text" v-for="aAttribute in ActiveInstance.attributes">{{aAttribute.display_name}}: {{aAttribute.current_value}}</p>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    var WinDoc = window.document;

    var app = createApp({
        data() {
            return {

                pageTitle: "Type Based Browser Script with Instance Switch",
                context:<?php echo json_encode($context)?>,
                // host: <?php echo json_encode($aHost)?>,
                host: <?php echo json_encode($aTinyHost)?>,
            }
        },
        mounted: async function () {

            WinDoc.title = this.pageTitle;
            await this.activateInstance(this.host, true)

        },
        computed: {

            ActiveInstance: function(){
                return Object.values(this.host.type.instances).find(x=>x.isActive);
            },

            AllInstances: function(){
                return Object.values(this.host.type.instances).sort((a,b) => a.display_name > b.display_name ? 1 : -1);
            }

        },
        methods: {

            activateInstance:async function(aInstance, isStartUp = false){
                
                this.AllInstances.forEach(x=>x.isActive = false);
                this.AllInstances.find(x=>x.id == aInstance.id).isActive = true;

            },
        }
    })
    .mount('#app');

</script>
