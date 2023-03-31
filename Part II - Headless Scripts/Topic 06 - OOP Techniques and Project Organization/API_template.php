<?php

//Purpose: this script serves as an api that can ...

require_once 'thinkiq_context.php';
$context = new Context();

if (!defined('JPATH_BASE')) define('JPATH_BASE', dirname(__DIR__));

require_once JPATH_BASE . '/components/com_thinkiq/config.php';
$config = new TiqConfig();

use TiqUtilities\Model\Script;
use Joomla\CMS\Response\JsonResponse;


//Script::includeScript('sample_library.sample_class');


$f = isset($context->std_inputs->function) ? $context->std_inputs->function : '';
$a = isset($context->std_inputs->argument) ? json_decode($context->std_inputs->argument) : '';


/**
 * Function to do something with a period...
 *
 * @param $start_time string for start
 * @param $end_time string for end
*/
 function GetStuff($start_time, $end_time) {
    $sampleClass = new SampleClass();
    $sampleClass->LoadStuff($start_time, $end_time);
    return $sampleClass;
}

switch ($f){
    case "helloWorld":

        echo json_encode($a);
        break;


    case "GetStuff":

        $start_time=$a->startDate;
        $end_time=$a->endDate;

        $returnObject=GetStuff($start_time, $end_time);

        die(new JsonResponse($returnObject));

        break;
}
