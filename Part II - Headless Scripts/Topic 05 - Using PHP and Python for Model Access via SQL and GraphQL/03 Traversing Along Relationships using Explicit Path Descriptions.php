<?php

// Load Model Abstraction Library namespaces
use TiqUtilities\Model\Type;
use TiqUtilities\Model\Node;
use TiqUtilities\Model\GenericObject;
use TiqUtilities\Model\Equipment;
use TiqUtilities\Model\Attribute;
use TiqUtilities\Model\Account;
use TiqUtilities\Model\Script;
use TiqUtilities\Model\MeasurementUnit;
use TiqUtilities\MaterialLedger\LedgerEntry;
use TiqUtilities\TimeSeries\TimeSeries;
use TiqUtilities\GraphQL\GraphQL;

// Sources:
// https://doeken.org/blog/tree-traversal-in-php

class TiqTraverser
{
    
    const CAN_FEED_DOWN = array("TYPE" => "R", "NAME" => "can_feed", "DIR" => "DOWN");
    const CAN_FEED_UP = array("TYPE" => "R", "NAME" => "can_feed", "DIR" => "UP");
    const CAN_PRODUCE = array("TYPE" => "R", "NAME" => "can_produce", "DIR" => "DOWN");
    const CAN_CONSUME = array("TYPE" => "R", "NAME" => "can_consume", "DIR" => "UP");
    const NODE_2_ACCOUNT = array("TYPE" => "N2A");

    public Node $origin;

    public $route = array("links" => [], "stops" => []);
    public $traversalTree = null;
    public $traversalTreeCost = 0;
    public $traversals = [];

    private $failedTraversals = [];

    public function __construct($aOrigin)
	{
		$this->origin = $aOrigin;
    }

    public function addToRoute($aLinkSketch, $aStopSketch){
        $this->route['links'][] = $aLinkSketch;
        $this->route['stops'][] = $aStopSketch;
    }

    public function resetRoute(){
        $this->route = array("links" => [], "stops" => []);
    }

    private function AddToTreeNodeArray($aArray, $aNode){
        $aArray[] = new Node($aNode->node_id);
        foreach($aNode->children as $aChildNode){
            $this->AddToTreeNodeArray($aArray, $aChildNode);
        }
        if(count($aNode->children)==0) $this->traversals[] = $aArray;
    }

    public function GetTraversals(){
        $this->traversals = [];
        $this->AddToTreeNodeArray([], $this->traversalTree);
        return $this->traversals;
    }

    private function InspectTarget($aTargetId, $depth, $echoOn = false){
        $aNode = new Node($aTargetId);
        $this->traversalTreeCost++;

        if ($echoOn) echo "Level $depth target inspection: $aNode->display_name ";
        $aStopSketch = $this->route['stops'][$depth];
        foreach(array_keys($aStopSketch) as $aKey){
            if($aNode->$aKey != $aStopSketch[$aKey]){
                if ($echoOn) echo " xxx fail xxx" . PHP_EOL;
                return false;
            }
        }
        if ($echoOn) echo " PASS" . PHP_EOL;
        return true;
    }

    private function TraverseFromLeaf($aLeaf, $echoOn = false){
        $aNode = new Node($aLeaf->node_id);
        $this->traversalTreeCost++;

        if(count($this->route['links'])==$aLeaf->depth){
            return;
        }

        // get traversal
        switch($this->route['links'][$aLeaf->depth]['TYPE']){
            case "R":
                // we're traversing along relationships
                // up or down
                $seek_up = true;
                $seek_down = true;
                if(array_search('DIR', array_keys($this->route['links'][$aLeaf->depth]))){
                    if($this->route['links'][$aLeaf->depth]['DIR']=="UP"){
                        $seek_down = false;
                    } else {
                        $seek_up = false;
                    }
                }

                $relationships = [];
                if($seek_down){
                    $aNode->getRelationshipsDown();
                    $this->traversalTreeCost++;
                    $relationships[] = $aNode->relationshipsDown;
                }
                if($seek_up){
                    $aNode->getRelationshipsUp();
                    $this->traversalTreeCost++;
                    $relationships[] = $aNode->relationshipsUp;
                }

                for($i=0; $i<count($relationships); $i++){
                    // filter on relationship type
                    $name_filter = "";
                    if(array_search('NAME', array_keys($this->route['links'][$aLeaf->depth]))){
                        $name_filter = $this->route['links'][$aLeaf->depth]['NAME'];
                    }
                    foreach(array_keys($relationships[$i]) as $aRelationshipName){
                        if ($echoOn) echo "Level $aLeaf->depth link inspection: $aRelationshipName ";
                        if($aRelationshipName == $name_filter || $name_filter == ''){
                            // it's a valid link
                            if ($echoOn) echo " PASS" . PHP_EOL;
                            foreach($relationships[$i][$aRelationshipName] as $aTarget){
                                if($this->InspectTarget($aTarget->id, $aLeaf->depth, $echoOn)){
                                    // continue traversal

                                    $aNewLeaf = new stdClass();
                                    $aNewLeaf->node_id = $aTarget->id;
                                    $aNewLeaf->display_name = $aTarget->display_name;
                                    $aNewLeaf->depth = $aLeaf->depth + 1;
                                    $aNewLeaf->parent = $aLeaf;
                                    $aNewLeaf->children = [];

                                    $aLeaf->children[] = $aNewLeaf;
                                    //
                                    // RECURSION!!!
                                    $this->TraverseFromLeaf($aNewLeaf, $echoOn);
                                    // RECURSION!!!
                                    //
                                } else {
                                    $this->failedTraversals[] = $aLeaf;
                                }
                            }
                        } else {
                            if ($echoOn) echo " xxx fail xxx" . PHP_EOL;
                        }
                    }
                }

                break;
            default:
                break;
        }

    }

    public function traverseGraph($removeFailedTraversals = true, $echoOn = false){

        $this->failedTraversals = [];
        $this->traversalTreeCost = 0;

        $aRoot = new stdClass();
        $aRoot->node_id = $this->origin->id;
        $aRoot->display_name = $this->origin->display_name;
        $aRoot->depth = 0;
        $aRoot->parent = null;
        $aRoot->children = [];
       
        $this->TraverseFromLeaf($aRoot, $echoOn);

        if ($echoOn) echo "RemoveFailedTraversals: $removeFailedTraversals" . PHP_EOL;
        if($removeFailedTraversals){
            // remove branch
            foreach($this->failedTraversals as $aLeaf){
                $aBranchRoot = $aLeaf->parent;
                while(count($aBranchRoot->children)==1 && $aBranchRoot->parent!=null){
                    $aBranchRoot = $aBranchRoot->parent;
                }
                $node_ids = array_column($aBranchRoot->children, 'node_id');
                array_splice($aBranchRoot->children, array_search($aLeaf->node_id, $node_ids), 1);
            }
        }

        $this->traversalTree = $aRoot;
    }
}

?>
