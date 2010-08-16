<?php

if (($argc != 4) || !isset($argv)) {
	echo "Usage: user_balance [credit|debit] [user_name|user_guid] [amount]\n";
    exit(-1);
}
include_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

elgg_set_ignore_access(TRUE);


$operation = $argv[1];
$user = is_numeric($argv[2]) ? get_entity($argv[2]) : get_user_by_username($argv[2]);
$amount = (double)$argv[3];

if ('credit' != $operation && 'debit' != $operation) {
    echo "Error: Unkown operation: ".$operation."\n";  
    exit (-1);
}
else if (!isset($user)) {
    echo "Error: User not found.\n";  
    exit (-1);
}

if (!is_numeric($amount)) {
	echo "Invalid amount: ".$argv[3]."\n"; 
    exit (-1);
}

echo "User $user->username has $".$user->opendollars."\n";

$user->opendollars += ($operation == "credit") ? $amount : -$amount ;
$user->save();

echo "User $user->username ($user->guid) has been {$operation}ed $amount.\n";
echo "New balance: $".$user->opendollars."\n";
