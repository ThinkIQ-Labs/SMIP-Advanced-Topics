 <?php

require_once 'thinkiq_context.php';
$context = new Context();

use TiqUtilities\Model\Type;

$allTypes = Type::getNodeSet("types")["set"];

foreach($allTypes as $aType){
    echo "$aType->display_name" . PHP_EOL;
    $aType->getInstances();
    foreach($aType->instances as $aInstance){
        echo "\t$aInstance->display_name" . PHP_EOL;
    }
}
