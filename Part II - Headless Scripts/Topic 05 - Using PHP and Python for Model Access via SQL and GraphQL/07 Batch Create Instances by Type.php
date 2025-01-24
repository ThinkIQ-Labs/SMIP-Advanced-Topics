<?php

// we need the context object to know where we are
require_once 'thinkiq_context.php';
$context = new Context();

use TiqUtilities\Model\Node; // to be able to get the root, i.e. where we're executing from
use TiqUtilities\Model\Type; // to lookup the type we want to use
use TiqUtilities\Model\GenericObject; // to create instances

$aRoot = new Node($context->std_inputs->node_id); // gets the object the script is called from
$aRoot->getParent(); // gets the parent of the root node

// echo $aRoot->display_name . PHP_EOL;
// echo $aRoot->parent->display_name . PHP_EOL;
// die;

$aType = Type::getInstance("some_library_relative_name.type_relative_name"); // get the type
// echo $aType->display_name;

for($i = 1; $i <= 5; $i++){ // this makes 5 instance, change to taste...

    $aNewInstance = new GenericObject(); // this creates a new "any" object. use this for equipment, place, org, ...
    $aNewInstance->display_name =  "Some New Instance" . "." . sprintf('%02d', $i);          // this gets us trailing zeros... "01, 02, 09, 10, 11, ..."
    // $aNewInstance->description = "no clue" . " " . $i;           // description is in the csv
    $aNewInstance->part_of_id = $aRoot->id;         // part of for a type is a library
    $aNewInstance->type_id = $aType->id;            // sub type of is the super type
    $aNewInstance->save(); 
    var_dump($aNewInstance); //die;
}
