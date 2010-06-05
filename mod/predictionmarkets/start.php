<?php
/**
 * the main entry point of the predictionmarkets modul. exposes several REST webservice functions.
 * these functions are defined in the servicefacade.php file.
 * these functions in turn use the static methods provided by the container class, which implements
 * the BL layer. the container in turn uses a storage instance, which provides access to the database.
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 */

/**
* load includes
*/
require_once("servicefacade.php");

/**
* Returns the currently authenticated Elgg user or null
* if no user authentication has been performed.
* @return ElggUser|null
*/
function getCurrentElggUser()
{
	global $CONFIG;

	$token = get_input('auth_token');
	$token = sanitise_string($token);

	$site = $CONFIG->site_id;
	$time = time();

	$user = get_data_row(
		"SELECT * from {$CONFIG->dbprefix}users_apisessions 
		WHERE token = '$token' AND site_guid = $site AND $time < expires"
	);
	
	$elgguser = null;
	
	if ($user && $user->user_guid)
	{
		$elgguser = get_entity($user->user_guid);
	}
	
	return $elgguser;	
}

/*
http://localhost/news-guru/services/api/rest/xml/?method=system.api.list
*/

expose_function(
	"predictionmarkets.marketlist", "predictionmarkets_marketlist", array(
		"state" => array("type" => "string", "required" => true),
		"creator" => array("type" => "string", "required" => true),
		"maincat" => array("type" => "string", "required" => true),
		"subcat" => array("type" => "string", "required" => true),
		"tag" => array("type" => "string", "required" => true),
		"orderby" => array("type" => "array", "required" => true),
		"start" => array("type" => "integer", "required" => true),
		"count" => array("type" => "integer", "required" => true)
	),
	"Returns a list of prediction markets with the given state",
	'GET', false, true
);
expose_function(
	"predictionmarkets.marketlistcount", "predictionmarkets_marketlistcount", array(
		"state" => array("type" => "string", "required" => true),
		"creator" => array("type" => "string", "required" => true),
		"maincat" => array("type" => "string", "required" => true),
		"subcat" => array("type" => "string", "required" => true),
		"tag" => array("type" => "string", "required" => true)
	),
	"Returns the number of markets with the given state",
	'GET', false, true
);
expose_function(
	"predictionmarkets.market", "predictionmarkets_market", array(
		"marketId" => array("type" => "integer", "required" => true)
	),
	"Returns a prediction market",
	'GET', false, true
);
expose_function(
	"predictionmarkets.marketpositions", "predictionmarkets_marketpositions", array(
		"marketId" => array("type" => "integer", "required" => true),
		"userId" => array("type" => "integer", "required" => false),
		"state" => array("type" => "string", "required" => false)
	),
	"Returns a list of marketpositions for a specific market",
	'GET', false, true
);
expose_function(
	"predictionmarkets.buymarketposition", "predictionmarkets_buymarketposition", array(
		"marketId" => array("type" => "integer", "required" => true),
		"optionId" => array("type" => "integer", "required" => true),
		"userId" => array("type" => "integer", "required" => true),
		"price" => array("type" => "float", "required" => true),
		"stake" => array("type" => "float", "required" => true),
		"public" => array("type" => "integer", "required" => false)
	),
	"Buys a position of a given option for the specified market",
	'POST', false, true
);
expose_function(
	"predictionmarkets.sellmarketposition", "predictionmarkets_sellmarketposition", array(
		"marketId" => array("type" => "integer", "required" => true),
		"positionId" => array("type" => "integer", "required" => true),
		"price" => array("type" => "float", "required" => true)
	),
	"Sells a position of a specific market",
	'POST', false, true
);
expose_function(
	"predictionmarkets.useraccount", "predictionmarkets_useraccount", array(
		"userId" => array("type" => "integer", "required" => false),
		"userName" => array("type" => "string", "required" => false)
	),
	"Returns account information for the currently authenticated user",
	'GET', false, true
);
expose_function(
	"predictionmarkets.userpositions", "predictionmarkets_userpositions", array(
		"userId" => array("type" => "integer", "required" => false),
		"userName" => array("type" => "string", "required" => false),
		"state" => array("type" => "string", "required" => false)
	),
	"Returns a list of marketpositions for the currently authenticated user",
	'GET', false, true
);
expose_function(
	"predictionmarkets.settlemarket", "predictionmarkets_settlemarket", array(
		"marketId" => array("type" => "integer", "required" => true),
		"optionId" => array("type" => "integer", "required" => true),
		"settlementTime" => array("type" => "string", "required" => true),
		"settlementText" => array("type" => "string", "required" => false)
	),
	"Returns a prediction market",
	'POST', false, true
);
expose_function(
	"predictionmarkets.categories", "predictionmarkets_categories", array(
		"parent" => array("type" => "string", "required" => false)
	),
	"Returns a list of categories",
	'GET', false, true
);
expose_function(
	"predictionmarkets.allcategories", "predictionmarkets_allcategories", array(
	),
	"Returns a list of all used categories",
	'GET', false, true
);
expose_function(
	"predictionmarkets.alltags", "predictionmarkets_alltags", array(
	),
	"Returns a list of all used tags",
	'GET', false, true
);
expose_function(
	"predictionmarkets.allcreators", "predictionmarkets_allcreators", array(
	),
	"Returns a list of all used creators",
	'GET', false, true
);
expose_function(
	"predictionmarkets.savemarket", "predictionmarkets_savemarket", array(
		
		"id" => array("type" => "integer", "required" => true),
		"title" => array("type" => "string", "required" => true),
		"description" => array("type" => "string", "required" => true),
		"maincat" => array("type" => "string", "required" => true),
		"subcat" => array("type" => "string", "required" => true),
		
		"settlementdetails" => array("type" => "string", "required" => true),
		"tags" => array("type" => "string", "required" => true),
		"publication" => array("type" => "string", "required" => true),
		"suspension" => array("type" => "string", "required" => true),
		
		"optioncount" => array("type" => "string", "required" => true),
		"optionids" => array("type" => "array", "required" => true),
		"optionlabels" => array("type" => "array", "required" => true),
		"optiondescriptions" => array("type" => "array", "required" => true),
		"optionvalues" => array("type" => "array", "required" => true),
		"optionopens" => array("type" => "array", "required" => true),
		"optionvisibles" => array("type" => "array", "required" => true),
		
		"imageurl" => array("type" => "string", "required" => false)

	),
	"Saves a market to the database",
	'POST', false, true
);
expose_function(
	"predictionmarkets.addmarketcomment", "predictionmarkets_addmarketcomment", array(
		"marketid" => array("type" => "integer", "required" => true),
		"text" => array("type" => "string", "required" => true)
	),
	"Adds a comment to the market",
	'POST', false, true
);
expose_function(
	"predictionmarkets.flagmarket", "predictionmarkets_flagmarket", array(
		"marketid" => array("type" => "integer", "required" => true),
		"text" => array("type" => "string", "required" => true),
		"suspend" => array("type" => "boolean", "required" => true)
	),
	"Flags the question",
	'POST', false, true
);
expose_function(
	"predictionmarkets.marketlog", "predictionmarkets_marketlog", array(
		"marketid" => array("type" => "integer", "required" => true),
		"actions" => array("type" => "array", "required" => true),
		"start" => array("type" => "integer", "required" => false),
		"count" => array("type" => "integer", "required" => false)
	),
	"Flags the question",
	'GET', false, true
);
?>
