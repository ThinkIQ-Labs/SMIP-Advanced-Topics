<?php

use Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.core.js', array('version' => 'auto', 'relative' => false));
HTMLHelper::_('script', 'media/com_thinkiq/js/dist/tiq.tiqGraphQL.min.js', array('version' => 'auto', 'relative' => false));

require_once 'thinkiq_context.php';
$context = new Context();
?>

<div id="app">

    <div class="row">            
        <div class="col-12">
            <h1 class="pb-2 pt-2 text-center">{{pageTitle}}</h1>
            <p class="pb-4 text-center">
                <a v-bind:href="`index.php?option=com_modeleditor&view=script&id=${context.std_inputs.script_id}`" target="_blank">source</a>
            </p>
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
    window.document.title = "GraphQL Demo"
    var app = new Vue({
        el: "#app",
        data() {
            return {
                pageTitle: "Units of Measure in the ThinkIQ Platform",
                context:<?php echo json_encode($context)?>,
                quantities: [],
            }
        },
        mounted: async function () {
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
