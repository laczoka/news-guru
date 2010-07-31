<?php

if (($argc < 2) || !isset($argv)) {
	echo "Missing market #id.\nUsage: open_suspended_market marketid\n";
    exit(-1);
}
include_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

// ignore access rules for the given market
elgg_set_ignore_access(TRUE);


$market_id = $argv[1];
$market = get_entity($market_id);

if (!$market) {
    echo "Error: Market #".$market_id." not found.\n";  
    exit (-1);
}
else if ($market->getSubtype() != "predictions") {
    echo "Error: Resulted object is not a market.\n";  
    exit (-1);
}

if ($market->status != "suspended") {
	echo "Market #".$market_id." is ".$market->status.". Expected: suspended.\n"; 
    exit (-1);
}

$market->status = "open";

$market->save();

echo "Market #".$market_id." has been opened.\n";
