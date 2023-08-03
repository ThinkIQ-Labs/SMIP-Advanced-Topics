<?php

// lookup the cloud_graph_repository_api script
// we need the file name to make web requests
$mv_tools_api = new TiqUtilities\Model\Script('api_demo.api_demo__hyphen__php_api');
$mv_tools_api_file_name = $mv_tools_api->script_file_name;

?>

<script>
    const GetJsonResponseAsync = async (aFunctionName, aArgument) => {
        // typical boiler plate to make a web request to a php script file
        let apiRoute = `/index.php?option=com_thinkiq&task=invokeScript`;
        let settings = { method: 'POST', headers: {} };
        let formData = new FormData();
        formData.append('script_name', '<?php echo $mv_tools_api_file_name; ?>');
        formData.append('output_type', 'browser');
        formData.append('function', aFunctionName);
        formData.append('argument', JSON.stringify(aArgument));
        settings.body = formData;
        let aResponse = await fetch(apiRoute, settings);
        let aResponseData = await aResponse.json();
        return aResponseData.data;

    };

    var ApiDemoSdk = {

        EchoAsync: async function(a = null){
            // returns what is put in: string, numbers, json
            let argument={
                hello: a
            };
            return await GetJsonResponseAsync('Echo', argument);
        },

        GetLibraryNamesAsync: async function(a = null){
            // returns the names of all library
            let argument={};
            return await GetJsonResponseAsync('GetLibraryNames', argument);
        },

        GetLibraryByNameAsync: async function(aLibraryName = 'ThinkIQ Base Library'){
            // returns a library by name
            let argument={
                libraryName: aLibraryName
            };
            return await GetJsonResponseAsync('GetLibraryByName', argument);
        },
       
    }

</script>