<?php

if (($argc < 2) || !isset($argv)) {
	echo "Missing market #id.\nUsage: open_settled_market marketid\n";
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

if ($market->status != "settled") {
	echo "Market #".$market_id." is ".$market->status.". Expected: settled.\n"; 
    exit (-1);
}

$market->status = "suspended";

$e = elgg_get_entities_from_metadata(array(
        'type'                  => "object",
        'subtype'               => "transaction",
        'limit'                 =>  0,
        'offset'                =>  0, 
        "metadata_name_value_pairs" => array( "name" => "market", "value" => $market->guid) )
     );

echo "Total number of transacions ".count($e)." \n";

$i = 0;
foreach ($e as $tr) {
	if ($tr->status == 'settled') {
		$tr->status = 'open';
		//ALSO DEDUCT THE WIN FROM THE OWNER!
		$option = $market->outcome == $market->option1 ? "option1" : "option2";
		echo "Outcome $option ";
		echo "vs Tr $tr->option\n";
		if ($tr->option == $option) {
		  $owner = get_entity($tr->owner_guid);
		  $owner->opendollars -= round($tr->settlementPrice);
		  $i++;
		}
	}
}
echo "Reversed $i wins\n";
$market->save();

echo "Market #".$market_id." has been opened.\n";
