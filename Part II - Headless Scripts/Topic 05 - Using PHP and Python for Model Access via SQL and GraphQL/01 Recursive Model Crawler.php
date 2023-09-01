<?php

require_once 'thinkiq_context.php';
$context = new Context();

// utilize the php utilitie's Node class
use TiqUtilities\Model\Node;

// initialze counter to 1 (the root object)
$count = 1;

// set the recursion limit
$recursionLimit = 3;

// crawler function
function CrawlNode($aNode, $depth){
    
    global $count;
    global $recursionLimit;
    global $context;

    // report node display name
    echo "Depth: $depth Name: $aNode->display_name" . PHP_EOL;
    
    // use flush() to allow the mini-ide to show outputs
    $context->flush();
    sleep(0.01);

    // don't crawl past the recursion depth limit
    if($depth < $recursionLimit){

        // obtain the child nodes
        $aNode->getChildren();
        
        // iterate through the child nodes
        foreach($aNode->children as $aChild){
            $count++;
            
            // recursively start crawling the child node
            CrawlNode($aChild, $depth+1);
        }
    }
}

// use the location of the script to obtain the root node
$aRoot = new Node($context->std_inputs->node_id);

// start crawling at the root
CrawlNode($aRoot, 0);

// report the total count of elements
echo "Total count: $count" . PHP_EOL;

// use return_data to properly finish the script
$context->return_data();
