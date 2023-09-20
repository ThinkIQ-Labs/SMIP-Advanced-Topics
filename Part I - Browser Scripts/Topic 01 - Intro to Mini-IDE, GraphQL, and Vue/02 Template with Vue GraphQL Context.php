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


    <div v-for="aQuantity in quantities">
        <h4>{{aQuantity.displayName}}</h4>
        <div v-for="aUom in aQuantity.measurementUnits" style="padding-left: 50px;">
            {{aUom.displayName}}
        </div>
    </div>

</div>

<script>
    var WinDoc = window.document;
    
    var app = new Vue({
        el: "#app",
        data() {
            return {
                pageTitle: "Units of Measure in the SMIP",
                context:<?php echo json_encode($context)?>,
                user:<?php echo json_encode($user)?>,
                quantities: [],
            }
        },
        mounted: async function () {
            WinDoc.title = this.pageTitle;
            await this.getQuantitiesAsync();
        },
        methods: {
            getQuantitiesAsync: async function () {

                let query = `
                    query MyQuery {
                        quantities {
                            displayName
                            measurementUnits {
                                displayName
                            }
                        }
                    }
                `;

                let aResponse = await tiqGraphQL.makeRequestAsync(query);
                this.quantities = aResponse.data.quantities;
            }
        },
    });
</script>
