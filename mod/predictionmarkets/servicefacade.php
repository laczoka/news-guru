<?php
/**
 * servicefacade.class.php, service facade method definitions, implementation of the methods exposed
 * by the Elgg REST Service for the module
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 */

/**
* the BL class
*/
require_once("lib/container.class.php");

/**
* the specific storage layer to be used.
* only db storage available now.
*/
require_once("lib/storage.db.class.php");

/**
* the specific market calculator helper class
* to be used.
*/
require_once("lib/marketcalculator.class.php");


/**
* apply elgg credentials to the static properties of the storage class
*/
if ($_SERVER["SERVER_ADDR"] != "192.168.1.2")
{
	storage::$DBHost = $CONFIG->dbhost;
	storage::$DBUser = $CONFIG->dbuser;
	storage::$DBPassword = $CONFIG->dbpass;
	storage::$DBDatabase = $CONFIG->dbname;
	storage::$DBTablePrefix = "predictionmarkets_";
}

function predictionmarkets_marketlist($state, $creator = "", $maincat = "", $subcat = "", $tag = "", $orderby = array(), $start = 0, $count = 0) {
	return container::getMarkets($state, $creator, $maincat, $subcat, $tag, $orderby, $start, $count);
}
function predictionmarkets_marketlistcount($state, $creator = "", $maincat = "", $subcat = "", $tag = "") {
	return container::getMarketsCount($state, $creator, $maincat, $subcat, $tag);
}
function predictionmarkets_market($marketId) {
	return container::getMarket($marketId);
}
function predictionmarkets_marketpositions($marketId, $userId = 0, $state = "Open") {
	return container::getMarketPositions($marketId, $userId, $state);
}
function predictionmarkets_buymarketposition($marketId, $optionId, $userId, $price, $stake, $public = 1) {
	return container::buyMarketPosition($marketId, $optionId, $userId, $price, $stake, $public);
}
function predictionmarkets_sellmarketposition($marketId, $positionId, $price) {
	$elgguser = getCurrentElggUser();
	return container::sellMarketPosition($marketId, $positionId, $elgguser->guid, $price);
}
function predictionmarkets_useraccount($userId = 0, $userName = "") {

	if (!empty($userId))
	{
		$result = container::getUserAccount($userId);
	}
	else if (!empty($userName))
	{
		$result = container::getUserAccount(0, $userName);
	}
	else
	{
		$elgguser = getCurrentElggUser();
		$userId = $elgguser->guid;
		$result = container::getUserAccount($userId);
		if (empty($result))
			$result = container::createUserAccount($userId, $elgguser->username);
	}
		
	return $result;

}
function predictionmarkets_userpositions($userId = 0, $userName = "", $state = "Open") {

	$elgguser = getCurrentElggUser();
	if (!empty($userName))
	{
		return container::getUserPositions(0, $userName, $elgguser->username != $userName, $state);
	}
	else 
	{
		if (empty($userId))
			$userId = $elgguser->guid;
		return container::getUserPositions($userId, "", $elgguser->guid != $userId, $state);
	}
	
}
function predictionmarkets_settlemarket($marketId, $optionId, $settlementTime, $settlementText) {
	$elgguser = getCurrentElggUser();
	return container::settleMarket($marketId, $optionId, $elgguser->guid, $settlementTime, $settlementText);
}
function predictionmarkets_categories($parent = "") {
	global $CONFIG;
	$site = $CONFIG->site;
	// TODO figure out about main/subcategories
	if ($parent != "" && $parent != "any")
		 return array();
	else
		return $site->categories;
}
function predictionmarkets_allcategories() {
	return container::getAllCategories();
}
function predictionmarkets_alltags() {
	return container::getAllTags();
}
function predictionmarkets_allcreators() {
	return container::getAllCreators();
}
function predictionmarkets_savemarket($id, $title, $description, $maincat, $subcat, $settlementdetails, $tags, $publication, $suspension, $optioncount, $optionids, $optionlabels, $optiondescriptions, $optionvalues, $optionopens, $optionvisibles, $imageurl = "") {

	date_default_timezone_set('UTC'); 
			
	$market = new market();
	$market->id = (int)$id;
	$market->title = prepareParam($title);
	$market->description = prepareParam($description);
	$market->maincat = prepareParam($maincat);
	$market->subcat = prepareParam($subcat);
	$market->imageurl = prepareParam($imageurl);
	$market->settlementdetails = prepareParam($settlementdetails);
	$market->tags = explode(",", prepareParam($tags));
	$market->publication = strtotime($publication);
	$market->suspension = strtotime($suspension);
	$market->options = array();

	for ($i = 0; $i < (int)$optioncount; $i++)
	{
		$option = new marketoption();
		$option->id = $optionids[$i];
		$option->marketId = $market->id;
		$option->label = prepareParam($optionlabels[$i]);
		$option->value = (double)$optionvalues[$i];
		$option->description = prepareParam($optiondescriptions[$i]);
		$option->visible = (int)$optionvisibles[$i];
		$option->open = (int)$optionopens[$i];
		
		$market->options[] = $option;
	}

	$elgguser = getCurrentElggUser();
	
	return container::saveMarket($market, $elgguser->guid);
	
}
function predictionmarkets_addmarketcomment($marketid, $text) {
	$elgguser = getCurrentElggUser();
	return container::addMarketComment($marketid, $elgguser->guid, $text);
}
function predictionmarkets_flagmarket($marketid, $text, $suspend) {
	$elgguser = getCurrentElggUser();
	return container::flagMarket($marketid, $elgguser->guid, $text, $suspend);
}
function predictionmarkets_marketlog($marketid, $actions, $start = 0, $count = 0) {
	return container::getMarketLog($marketid, $actions, $start, $count);
}

function prepareParam($value)
{
	return  str_replace("&#146;", "'", html_entity_decode(urldecode($value)));	
}	

?>