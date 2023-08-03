<?php

//Purpose: this script serves as an api that can ...

require_once 'thinkiq_context.php';
$context = new Context();

use Joomla\CMS\Response\JsonResponse; // Used for returning data to the client.

if (!defined('JPATH_BASE')) define('JPATH_BASE', dirname(__DIR__));

$f = isset($context->std_inputs->function) ? $context->std_inputs->function : '';
$a = isset($context->std_inputs->argument) ? json_decode($context->std_inputs->argument) : '';


use TiqUtilities\Model\Library;

function GetLibraryNames(){
    $allLibraries = Library::getNodeSet("libraries")["set"];
    $allLibrariesDisplayNames = array_column(json_decode(json_encode($allLibraries),TRUE), 'display_name');
    // $sourceLibrary = $allLibraries[array_search($sourceLibraryName, $allLibrariesDisplayNames)];
    return $allLibrariesDisplayNames;
}

function GetLibraryByName($aLibraryName){
    $allLibraries = Library::getNodeSet("libraries")["set"];
    $allLibrariesDisplayNames = array_column(json_decode(json_encode($allLibraries),TRUE), 'display_name');
    $aLibrary = $allLibraries[array_search($aLibraryName, $allLibrariesDisplayNames)];
    return $aLibrary;
}

switch ($f){
    case "Echo":

        $returnObject = $a->hello == null ? "Hello Echo." : $a->hello;
        die(new JsonResponse($returnObject));
        break;

    case "GetLibraryNames":

        $returnObject = GetLibraryNames();
        die(new JsonResponse($returnObject));
        break;

    case "GetLibraryByName":

        $aLibraryName = $a->libraryName;

        $returnObject = GetLibraryByName($aLibraryName);
        die(new JsonResponse($returnObject));
        break;
        
}
