<?php

use TiqUtilities\GraphQL\GraphQL;

require_once 'load_cms.php';

require_once 'thinkiq_context.php';
$context = new Context();

if (!defined('JPATH_BASE')) define('JPATH_BASE', dirname(__DIR__));

require_once JPATH_BASE . '/components/com_thinkiq/config.php';
$config = new TiqConfig();

$aClient = new GraphQL();

$gqlQuery = "
    query q1 {
        libraries {
            id displayName
        }
    }
";

$gqlQueryResponse = $aClient->MakeRequest($gqlQuery);
var_dump($gqlQueryResponse);
