<?php

require_once 'thinkiq_context.php';
$context = new Context();

use TiqUtilities\Model\Attribute;

$newAttr = new Attribute(123456);                            // get attribute by ID
// var_dump($newAttr); die;                                  // to make sure we have the correct attribute

$newAttr->insertTimeseries(['2'], ['2024-01-01 10:00']);            // insert timeseries data: values first, timestamps second

$vs = $newAttr->getTimeseries('2024-01-01', '2024-01-02');    // retrieve value stream
// var_dump($vs); die;                                        // this should include the added vst
