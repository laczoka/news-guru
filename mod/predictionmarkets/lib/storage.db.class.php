<?php
/**
 * storage.db.class.php, db storage class definition.
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 */
 
/**
*/
require_once("entities/operationresult.class.php");
require_once("entities/market.class.php");
require_once("entities/marketlog.class.php");
require_once("entities/marketoption.class.php");
require_once("entities/marketposition.class.php");
require_once("entities/useraccount.class.php");

/**
 * the storage provides methods to access persited objects
 * from the storage subsystem.
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 */
class storage
{

	public static $DBHost = "localhost";
	public static $DBUser = "root";
	public static $DBPassword ="";
	public static $DBDatabase = "predictionmarkets";
	public static $DBTablePrefix = "predictionmarkets_";

	private $_conn = null;
	
	/**
	* Constructor, initializes the mysql connection associated with the instance
	*/
	public function __construct() {
		$this->_conn = mysql_connect(
			storage::$DBHost, 
			storage::$DBUser, 
			storage::$DBPassword, 
			true
		);
		mysql_select_db(
			storage::$DBDatabase, 
			$this->_conn
		);
	}
	
	/**
	* Destructor, closes the mysql connection associated with the instance
	*/
	public function __destruct() {
		if ($this->_conn)
			mysql_close($this->_conn);
	}
	
	/**
	* Starts a transactional scope. the scope must be ended by either committing or rolling back any
	* of the actions performed after calling this method.
	* @return void
	*/
	public function startTransaction() {
		mysql_query("START TRANSACTION", $this->_conn);
	}

	/**
	* Commits any pending changes to the storage made since the startTransaction has been called.
	* @return void
	*/
	public function commitTransaction()	{
		mysql_query("COMMIT", $this->_conn);
	}
	
	/**
	* Rolls back any pending changes to the storage made since the startTransaction has been called.
	* @return void
	*/
	public function rollbackTransaction() {
		mysql_query("ROLLBACK", $this->_conn);
	}	

	/**
     * Returns an array of markets. The list contains only markets which mach the given state. Provide the start and the count
	 * params to only retrieve a part from the list.
	 * The markets do not contain the description- and settlementdetails text, 
	 * nor any tags. only the strongest option is attached.
     * @param string $state the desired state the markets return will have
	 * @param string $creator filters the list of markets to match a given creator
	 * @param string $maincat filters the list of markets to match a given main category
	 * @param string $subcat filters the list of markets to match a given sub category
	 * @param string $tag filters the list of markets to match a tag
	 * @param string[] $orderby an array of string containing the fields the list will be sorted by. valid values are: "title", "creator", "settlement", "publication", "suspension", "maincat", "subcat". append an " DESC" to sort descending.
	 * @param integer $start skips a portion of the list
	 * @param integer $count limits the list to contain only a maximum number of markets
     * @return market[]
     */
	public function getMarkets($state, $creator, $maincat, $subcat, $tag, $orderby = array(), $start = 0, $count = 0) {
	
		$results = array();
		
		$state = mysql_escape_string($state);
		$creator = mysql_escape_string($creator);
		$maincat = mysql_escape_string($maincat);
		$subcat = mysql_escape_string($subcat);
		$tag = mysql_escape_string($tag);
		$start = (int)$start;
		$count = (int)$count;
		$orderby = is_array($orderby) ? $orderby : array();
				
		$q = "SELECT 
				m.id, m.title, m.creator, 
				IF (m.state = 'Open' AND m.suspension < UTC_TIMESTAMP(), 'Suspended', m.state) as state,
				m.type, m.imageurl, m.maincat, m.subcat,
				m.settlement, m.publication, m.suspension
			  FROM " . storage::$DBTablePrefix . "markets m
				" . (empty($tag) || $tag == "any" ? "" : "INNER JOIN " . storage::$DBTablePrefix . "markettags t
					ON t.marketId = m.Id AND t.tagname = '" . $tag . "'") . "
			  WHERE 
			  (
			  		
					(
						'" . $state . "' = 'Suspended' 
						AND (
							m.state = 'Suspended' 
							OR (m.state = 'Open' AND m.suspension < UTC_TIMESTAMP())
						)
					)
					OR 
					(
						'" . $state . "' <> 'Suspended' 
						AND (
							m.state = '" . $state . "' 
							AND (m.state <> 'Open' OR m.suspension > UTC_TIMESTAMP())
						)
					)
					
			  )
				AND (m.publication < UTC_TIMESTAMP())
				   " . (empty($creator) || $creator == "any" ? "" : " AND m.creator = '$creator'") . "
				   " . (empty($maincat) || $maincat == "any" ? "" : " AND m.maincat = '$maincat'") . "
				   " . (empty($subcat) || $subcat == "any" ? "" : " AND m.subcat = '$subcat'") . "
			  ORDER BY ";
	
		$validfields = array("title", "creator", "settlement", "publication", "suspension", "maincat", "subcat");
						
		if (!empty($orderby) && count($orderby) > 0)
		{
			foreach($orderby as $o)
			{
				$dir = "ASC";
				if (strtoupper(substr($o, -5)) == " DESC")
				{
					$dir = "DESC";
					$o = substr($o, 0, -5);
				}
				else if (strtoupper(substr($o, -4)) == " ASC")
				{
					$dir = "ASC";
					$o = substr($o, 0, -4);
				}
				$o = strtolower($o);
				if (in_array($o, $validfields, true))
					$q .= " m.$o $dir,";	
			}
		}

		$q .= "	m.publication DESC";
		
		if ($count)
			$q .= " LIMIT " . $start . ", " . $count;

		$r = mysql_query($q, $this->_conn);

		while ($row = mysql_fetch_assoc($r))
		{
			$market = market::createFromAssoc($row);
			// $market->tags = $this->getMarketTags($market->id);
			// $market->options = $this->getMarketOptions($market->id);
			$market->options = array();
			$option = $this->getStrongestMarketOptions($market->id);
			if ($option)
				$market->options[] = $option;
			$results[] = $market;
		}

	    return $results;
	}
	
	/**
     * Returns the number of markets in the storage which match the given state
     * @param string $state the desired state the markets return will have
	 * @param string $creator filters the list of markets to match a given creator
	 * @param string $maincat filters the list of markets to match a given main category
	 * @param string $subcat filters the list of markets to match a given sub category
	 * @param string $tag filters the list of markets to match a tag
	 * @return integer
     */
	public function getMarketsCount($state, $creator, $maincat, $subcat, $tag) {
	
		$results = 0;
		
		$state = mysql_escape_string($state);
		$creator = mysql_escape_string($creator);
		$maincat = mysql_escape_string($maincat);
		$subcat = mysql_escape_string($subcat);
		$tag = mysql_escape_string($tag);
		
		$q = "SELECT COUNT(*) c 
			  FROM " . storage::$DBTablePrefix . "markets m
				" . (empty($tag) || $tag == "any" ? "" : "INNER JOIN " . storage::$DBTablePrefix . "markettags t
					ON t.marketId = m.Id AND t.tagname = '" . $tag . "'") . "
			  WHERE
			  (
			  		
					(
						'" . $state . "' = 'Suspended' 
						AND (
							m.state = 'Suspended' 
							OR (m.state = 'Open' AND m.suspension < UTC_TIMESTAMP())
						)
					)
					OR 
					(
						'" . $state . "' <> 'Suspended' 
						AND (
							m.state = '" . $state . "' 
							AND (m.state <> 'Open' OR m.suspension > UTC_TIMESTAMP())
						)
					)
					
			  )
				AND (m.publication < UTC_TIMESTAMP())
				   " . (empty($creator) || $creator == "any" ? "" : " AND m.creator = '$creator'") . "
				   " . (empty($maincat) || $maincat == "any" ? "" : " AND m.maincat = '$maincat'") . "
				   " . (empty($subcat) || $subcat == "any" ? "" : " AND m.subcat = '$subcat'") . "
			";
		
		$r = mysql_query($q, $this->_conn);

		if ($row = mysql_fetch_assoc($r))
			$results = (int)$row["c"];
		
	    return $results;
	}
	
	/**
     * Returns a market instance retrieved from the storage matching the given market id or null if the market does not exist.
     * @param integer $marketId the id of the market to be returned
     * @return market|null
     */
	public function getMarket($marketId){
	
		$marketId = (int)$marketId;
		
		$q = "SELECT 
				id, hubdubId, title, creator, 
				IF (state = 'Open' AND suspension < UTC_TIMESTAMP(),
					'Suspended', state) as state,
				type, description, settlementdetails, imageurl, maincat, subcat,
				settlement, publication, suspension
			 FROM " . storage::$DBTablePrefix . "markets 
			 WHERE id = " . $marketId;
		$r = mysql_query($q, $this->_conn);

		$market = null;
		
		if ($row = mysql_fetch_assoc($r))
		{
			$market = market::createFromAssoc($row);
			$market->tags = $this->getMarketTags($market->id);
			$market->options = $this->getMarketOptions($market->id);
		}
		
	    return $market;
	}
	
	/**
     * Returns all tags associated with a given market identified by the marketId parameter
     * @param integer $marketId the id of the market for which the tags should be returned
     * @return string[]
     */
	public function getMarketTags($marketId) {
		$results = array();
		$q = "SELECT * 
			FROM " . storage::$DBTablePrefix . "markettags 
			WHERE marketId = " . $marketId;
		$r = mysql_query($q, $this->_conn);
		while ($row = mysql_fetch_assoc($r))
			$results[] = $row["tagname"];
		return $results;
	}
	
	/**
     * Returns all options from a given market identified by the marketId parameter. 
	 * All options are included in the result, regardless the open/visible state.
     * @param integer $marketId the id of the market for which the options should be returned
     * @return marketoption[]
     */
	public function getMarketOptions($marketId) {
		$results = array();
		$q = "SELECT * 
			FROM " . storage::$DBTablePrefix . "marketoptions 
			WHERE marketId = " . $marketId;
		$r = mysql_query($q, $this->_conn);
		while ($row = mysql_fetch_assoc($r))
			$results[] = marketoption::createFromAssoc($row);
		return $results;
	}

	/**
     * Returns the strongest open and visible option from a specific question, eg the one with the highest
	 * value.
     * @param integer $marketId the id of the market for which the option should be returned
     * @return marketoption[]
     */
	public function getStrongestMarketOptions($marketId) 
	{
		$results = null;
		$q = "SELECT *
			FROM " . storage::$DBTablePrefix . "marketoptions 
			WHERE marketId = " . $marketId . "
			ORDER BY value DESC
			LIMIT 0, 1";
		$r = mysql_query($q, $this->_conn);
		while ($row = mysql_fetch_assoc($r))
			$results = marketoption::createFromAssoc($row);
		return $results;
	}

	/**
     * Returns all positions a specific user bought from a certain market, or from all users if the userId
	 * parameter is omitted. The list is filtered by the optional state parameter
     * @param integer $marketId the id of the market from which the positions should be returned
	 * @param integer $userId the id of the user which positions of the market should be returned
	 * @param string $state determines the state of the positions which will be returned, can be one of these values: "Open", "Sold", "Won", "Lost"
     * @return marketposition[]
     */
	public function getMarketPositions($marketId, $userId = 0, $state = "Open") 
	{
		
		$results = array();
		
		$marketId = (int)$marketId;
		$userId = (int)$userId;
		
		$q  = "SELECT p.*, o.label AS optiontitle, o.value AS currvalue, m.title AS markettitle
				FROM " . storage::$DBTablePrefix . "marketpositions p
				LEFT JOIN " . storage::$DBTablePrefix . "marketoptions o ON o.id = p.optionId
				LEFT JOIN " . storage::$DBTablePrefix . "markets m ON m.id = p.marketId
				WHERE p.marketId = " . $marketId . "
					 " . (($userId > 0) ? " AND p.userId = " . $userId : "") ."
					" . (!empty($state) ? " AND p.state = '$state' " : "" ) . "
				ORDER BY p.sold desc, p.bought	
				";
		$r = mysql_query($q, $this->_conn);

		while ($row = mysql_fetch_assoc($r))
			$results[] = marketposition::createFromAssoc($row);
		
	    return $results;
	}

	/**
	* returns an array containing all used categories in the storage. the 
	* categories are returned as strings formatted to contain the main category, followed by ">", followed by the sub @category
	* @return string[] array of string containing all used categories
	*/
	public function getAllCategories()
	{
		$results = array();
		$q  = "SELECT DISTINCT maincat, subcat
				FROM " . storage::$DBTablePrefix . "markets
				ORDER BY maincat, subcat";
		$r = mysql_query($q, $this->_conn);

		while ($row = mysql_fetch_assoc($r))
			$results[] = $row["maincat"] . ">" . $row["subcat"];
		
	    return $results;
	}
	
	/**
	* returns an array containing all used tags in the storage. 
	* @return string[] array of string containing all used tags
	*/
	public function getAllTags()
	{
		$results = array();
		$q  = "SELECT DISTINCT tagname
				FROM " . storage::$DBTablePrefix . "markettags
				ORDER BY tagname";
		$r = mysql_query($q, $this->_conn);

		while ($row = mysql_fetch_assoc($r))
			$results[] = htmlentities($row["tagname"]);
		
	    return $results;
	}	

	/**
	* returns an array containing all used creators in the storage. 
	* @return string[] array of string containing all used creators
	*/
	public function getAllCreators()
	{
		$results = array();
		$q  = "SELECT DISTINCT creator
				FROM " . storage::$DBTablePrefix . "markets
				ORDER BY creator";
		$r = mysql_query($q, $this->_conn);

		while ($row = mysql_fetch_assoc($r))
			$results[] = $row["creator"];
		
	    return $results;
	}	

	/**
     * Returns a useraccount instance retrieved from the storage matching the given user id or null 
	 * if the user does not exist.
     * @param integer $userId the id of the user which will be returned
	 * @param string $userName the name of the user which will be returned. will be used instead of the userId if provided.
     * @return user|null
     */
	public function getUserAccount($userId, $userName = "") {

		$result = null;
		
		$userId = (int)$userId;
		$userName = mysql_escape_string($userName);
		
		$q  = "SELECT u.*, SUM(p.currprice) as predictions
				FROM " . storage::$DBTablePrefix . "useraccounts u
				LEFT JOIN " . storage::$DBTablePrefix . "marketpositions p ON p.userId = u.userId AND state = 'Open' 
				WHERE " . (empty($userName) ? "u.userId = " . $userId : "u.userName= '" . $userName . "'") . "
				GROUP BY u.userId
				";
		
		$r = mysql_query($q, $this->_conn);

		if ($row = mysql_fetch_assoc($r))
			$result = useraccount::createFromAssoc($row);
		
	    return $result;
	}

	/**
     * Creates and returns a useraccount instance 
     * @param integer $userId the id of the newly created user. must be provided since the id field has set to NOT autoincrement
	 * @param string $userName the name of the newly created user
     * @return user
     */
	public function createUserAccount($userId, $userName) {

		$userId = (int)$userId;
		$userName = mysql_escape_string($userName);
		$q = "INSERT INTO " . storage::$DBTablePrefix . "useraccounts
				(userId, userName, cash) VALUES (" . $userId . ", '$userName', '1000')";
		mysql_query($q, $this->_conn);
		return $this->getUserAccount($userId);
	}

	/**
     * Returns all positions for a specific user identified by the userId parameter
	 * @param integer $userId the id of the user which positions will be returned
	 * @param string $userName the name of the user which will be returned. will be used instead of the userId if provided.
     * @param integer $public filters the list of returned positions by their visibility. valid values are 0: all, 1: only public. default is only public.
	 * @param string $state filters the list of returned positions by their state. valid values are "Open", "Sold", "Won" and "Lost"
     * @return marketposition[]
     */
	public function getUserPositions($userId, $userName = "", $public = true, $state = "Open") {
		
		$results = array();
		
		$userId = (int)$userId;
		$userName = mysql_escape_string($userName);
		$public = (int)$public;
		$state = mysql_escape_string($state);
		
		$q  = "SELECT p.*, o.label AS optiontitle, o.value AS currvalue, m.title AS markettitle
				FROM " . storage::$DBTablePrefix . "marketpositions p
				INNER JOIN " . storage::$DBTablePrefix . "useraccounts u ON u.userId = p.userId
				LEFT JOIN " . storage::$DBTablePrefix . "marketoptions o ON o.id = p.optionId
				LEFT JOIN " . storage::$DBTablePrefix . "markets m ON m.id = p.marketId
				WHERE " . (empty($userName) ? "u.userId = " . $userId : "u.userName= '" . $userName . "'") . "
					" . ($public ? " AND p.public = 1 " : "" ) . "
					" . (!empty($state) ? " AND p.state = '$state' " : "" ) . "
				ORDER BY p.sold desc, p.bought
				";
					
		$r = mysql_query($q, $this->_conn);

		while ($row = mysql_fetch_assoc($r))
			$results[] = marketposition::createFromAssoc($row);
		
	    return $results;
	}
	
	/**
     * Creates a new market position.
     * @param integer $marketId the id of the market for which a position is bought
	 * @param integer $optionId the id of the option which is bought
	 * @param integer $userid the id of the user buying the position
	 * @param double $price the price of the option at time of this transaction
	 * @param double $value the value of the position to be bought
	 * @param double $shares the amount of shares the position holds
     * @param integer $public the visibility of the position, valid values: 1: public, 0: private
	 * @return void
	 */
	public function openMarketPosition($marketId, $optionId, $userId, $price, $value, $shares, $public) {
	
		$marketId = (int)$marketId;
		$optionId = (int)$optionId;
		$userId = (int)$userId;
		
		$price = (double)$price;
		$shares = (double)$shares;
		$public = (int)$public;
		
		$q  = "INSERT INTO " . storage::$DBTablePrefix . "marketpositions
			(marketId, optionId, userId, bought, buyprice, buyvalue, shares, public)
		 	VALUES ($marketId, $optionId, $userId, UTC_TIMESTAMP(), $price, $value, $shares, $public)";
		
		mysql_query($q, $this->_conn);
		
	}

	/**
     * Closes a market position by setting its state to "Sold"
     * @param integer $positionId the id of the position to be closed
	 * @param double $price the selling price of the price
	 * @param double $value the selling value of the position
	 * @return void
     */
	public function sellMarketPosition($positionId, $price, $value) {
	
		$positionId = (int)$positionId;
		$price = (double)$price;
		$value = (double)$value;
		
		$q  = "UPDATE " . storage::$DBTablePrefix . "marketpositions
			SET sold = UTC_TIMESTAMP(), sellprice = " . $price . ", sellvalue = " . $value .", state = 'Sold'
			WHERE id = " . $positionId;

		mysql_query($q, $this->_conn);
		
	}
	
	/**
     * Settles a market position by setting its state to "Won", "Lost" or "Voided"
     * @param integer $positionId the id of the position to be settled
	 * @param double $price the settling price of the option
     * @param double $value the settling value of the position
	 * @param string $state the settling state of the position, valid values: "Won", "Lost", "Voided"
	 * @param timestamp $settlementTime (UTC)
	 * @return void
     */
	public function settleMarketPosition($positionId, $price, $value, $state, $settlementTime) {
	
		$positionId = (int)$positionId;
		$price = (double)$price;
		$value = (double)$value;
		$state = mysql_escape_string($state);
				
		$q  = "UPDATE " . storage::$DBTablePrefix . "marketpositions
			SET sold = '" . date("Y-m-d H:i:s", $settlementTime) . "', 
				sellprice = " . $price . ", 
				sellvalue = " . $value .", 
				state = '" . $state . "'
			WHERE id = " . $positionId;

		mysql_query($q, $this->_conn);
		
	}
	
	/**
     * Updates the currvalue and currprice of position
     * @param integer $positionId the id of the position to be updated
	 * @param double $price the new current price of the option
	 * @param double $value the new current price of the position
	 * @return void
     */
	public function updateMarketPosition($positionId, $price, $value) {
	
		$positionId = (int)$positionId;
		$price = (double)$price;
		$value = (double)$value;
		
		$q = "UPDATE " . storage::$DBTablePrefix . "marketpositions 
				SET 
					currprice = " . $price . ",
					currvalue = " . $value . "
				WHERE id = " . $positionId;

		mysql_query($q, $this->_conn);

	}
	
	/**
     * Changes the state of the market identified by the marketId parameter to "Settled", and
	 * adds the given time as settlement timestamp
     * @param integer $marketId the id of the market to be settled
	 * @param timestamp $settlementTime the settlement time in UTC
	 * @return void
     */
	public function settleMarket($marketId, $settlementTime){
	
		$marketId = (int)$marketId;
		
		$q = "UPDATE " . storage::$DBTablePrefix . "markets 
			 SET state = 'Settled', settlement = '" . date("Y-m-d H:i:s", $settlementTime) . "'
			 WHERE id = " . $marketId;
			 		
		mysql_query($q, $this->_conn);

	}

	/**
     * Increases the cash value stored for a specific user by the amount given
     * @param integer $userId the id of the user which cash should be increased
	 * @param double $amount the amount by which the users cash should be increased
	 * @return void
     */
	public function increaseUserCash($userId, $amount) {
	
		$userId = (int)$userId;
		$amount = (double)$amount;
		
		$q = "UPDATE " . storage::$DBTablePrefix . "useraccounts 
				SET cash = cash + " . $amount . "
				WHERE userId = " . $userId;
	
		mysql_query($q, $this->_conn);

	}

	/**
     * Reduces the cash value stored for a specific user by the amount given
     * @param integer $userId the id of the user which cash should be reduced
	 * @param double $amount the amount by which the users cash should be reduced
	 * @return void
     */
	public function reduceUserCash($userId, $amount) {
	
		$userId = (int)$userId;
		$amount = (double)$amount;
		
		$q = "UPDATE " . storage::$DBTablePrefix . "useraccounts 
				SET cash = cash - " . $amount . "
				WHERE userId = " . $userId;
	
		mysql_query($q, $this->_conn);

	}
	
	/**
     * Increases the volume (the amount of shares) stored with an option
     * @param integer $optionId the id of the option which volume should be increased
	 * @param double $amount the amount by which the volume of the option should be increased
     * @return void
     */
	public function increaseOptionShares($optionId, $amount) {
	
		$optionId = (int)$optionId;
		$amount = (double)$amount;
		
		$q = "UPDATE " . storage::$DBTablePrefix . "marketoptions 
				SET shares = shares + " . $amount . "
				WHERE id = " . $optionId;

		mysql_query($q, $this->_conn);

	}
	
	/**
     * Reduces the volume (the amount of shares) stored with an option
     * @param integer $optionId the id of the option which volume should be reduced
	 * @param double $amount the amount by which the volume of the option should be reduced
     * @return void
     */
	public function decreaseOptionShares($optionId, $amount) {
	
		$optionId = (int)$optionId;
		$amount = (double)$amount;
		
		$q = "UPDATE " . storage::$DBTablePrefix . "marketoptions 
				SET shares = shares - " . $amount . "
				WHERE id = " . $optionId;

		mysql_query($q, $this->_conn);

	}
	
	/**
     * Sets a new value for an option
     * @param integer $optionId the id of the option which value is changed
	 * @param double $value the new value for the option
     * @return void
     */
	public function updateOptionValue($optionId, $value) {
	
		$optionId = (int)$optionId;
		$value = (double)$value;
		
		$q = "UPDATE " . storage::$DBTablePrefix . "marketoptions 
				SET value = " . $value . "
				WHERE id = " . $optionId;

		mysql_query($q, $this->_conn);

	}
	
	/**
	* Stores a given market to the database
	* @param market $market market to be stored. in case the id property carries a value different from 0, an update will be performed. otherwise an insert. the market and its associated tags and options will be modified to have the corresponding ids assigned.
	* @return void
	*/
	public function saveMarket(&$market)
	{

		if (get_class($market) != "market")
			return;
		
		$q = "title = '" . mysql_escape_string($market->title) . "',
			type = 4,
			creator = '" . mysql_escape_string($market->creator) . "',
			state = '" . mysql_escape_string($market->state) . "',
			description = '" . mysql_escape_string($market->description) . "',
			imageurl = '" . mysql_escape_string($market->imageurl) . "',
			maincat = '" . mysql_escape_string($market->maincat) . "',
			subcat = '" . mysql_escape_string($market->subcat) . "',
			settlementdetails = '" . mysql_escape_string($market->settlementdetails) . "',
			publication = '" . date("Y-m-d H:i:s", $market->publication) . "',
			suspension = '" . date("Y-m-d H:i:s", $market->suspension) . "'";
			
		if ($market->id > 0)
		{
			$q = "UPDATE " . storage::$DBTablePrefix . "markets 
				SET " . $q . "
				WHERE id = " . (int)$market->id;
		}
		else
		{
			$q = "INSERT INTO " . storage::$DBTablePrefix . "markets 
				SET " . $q;
		}
		
		mysql_query($q, $this->_conn);
		
		if ($market->id == 0)	
		{
			$q = "SELECT @@IDENTITY as id";
			$r = mysql_query($q, $this->_conn);
			$row = mysql_fetch_assoc($r);
			$market->id = $row["id"];
		}
		
		$this->deleteMarketTags($market->id);
		
		for ($i = 0; $i < count($market->tags); $i++)
			$this->insertMarketTag($market->id, $market->tags[$i]);
		
		for ($i = 0; $i < count($market->options); $i++)
		{
			$market->options[$i]->marketId = $market->id;
			$this->saveMarketOption($market->options[$i]);
		}	
		
	}	

	private function deleteMarketTags($marketId)
	{
		$marketId = (int)$marketId;
		$q = "DELETE FROM " . storage::$DBTablePrefix . "markettags
			WHERE marketId = " . $marketId;
		mysql_query($q, $this->_conn);
	}
	
	private function insertMarketTag($marketId, $tag)
	{
		$marketId = (int)$marketId;
		$tag = mysql_escape_string($tag);
		$q = "INSERT INTO " . storage::$DBTablePrefix . "markettags
			SET marketId = " . $marketId . ", tagname = '" . $tag . "'";
		mysql_query($q, $this->_conn);
	}

	private function saveMarketOption(&$marketOption)
	{

		$q = "marketId = '" . mysql_escape_string($marketOption->marketId) . "',
			label = '" . mysql_escape_string($marketOption->label) . "',
			description = '" . mysql_escape_string($marketOption->description) . "',
			open = '" . (int)($marketOption->open) . "',
			visible = '" . (int)($marketOption->visible) . "'";
			
		if ($marketOption->id > 0)
		{
			$q = "UPDATE " . storage::$DBTablePrefix . "marketoptions 
				SET " . $q . "
				WHERE id = " . (int)$marketOption->id;
		}
		else
		{
			$q = "INSERT INTO " . storage::$DBTablePrefix . "marketoptions 
				SET " . $q . ",
					value = '" . (float)($marketOption->value) . "',
					shares = '" . (float)($marketOption->shares) . "'
				";
		}
		
		mysql_query($q, $this->_conn);
		
		if ($marketOption->id == 0)	
		{
			$q = "SELECT @@IDENTITY";
			$r = mysql_query($q, $this->_conn);
			$row = mysql_fetch_assoc($r);
			$marketOption->id = $row[0];
		}
		
		
	}
	
	/**
	* Adds a log entry to the database
	* @param $marketlog the market log entry to be added
	* @return void
	*/ 
	public function addMarketLogEntry($marketlog)
	{
		
		$q = "INSERT INTO " . storage::$DBTablePrefix . "marketlog
				SET timestamp = UTC_TIMESTAMP(), 
					marketId = " . (int)$marketlog->marketId . ",
					userId = " . (int)$marketlog->userId . ",
					action = '" . mysql_escape_string($marketlog->action) . "',
					details = '" . mysql_escape_string($marketlog->details) . "'";
					
		mysql_query($q, $this->_conn);

	}

	/**
	* Updates the state of the market entry
	* @param integer $marketId the id of the market to be updated
	* @param string $state the new state of the market
	* @return void
	*/
	public function setMarketState($marketId, $state)
	{

		$q = "UPDATE " . storage::$DBTablePrefix . "markets
			SET state = '" . mysql_escape_string($state) . "'
			WHERE id = " . (int)$marketId;

		mysql_query($q, $this->_conn);
				
	}
	
	/**
	* Return the entries from the log for a specific market
	* @param integer $marketId the id of the market to retrieve the log for
	* @param string[] $actions the types of operation performed, valid values: 'create','edit','suspend','void','settle','flag','buypos','sellpos','comment'
	* @param integer $start skips a portion of the list
	* @param integer $count limits the list to contain only a maximum number of markets
	*/
    public function getMarketLog($marketId, $actions, $start = 0, $count = 0)
	{

		$results = array();
		
		$marketId = (int)$marketId;
		$start = (int)$start;
		$count = (int)$count;
				
		$q = "SELECT l.*, u.username
			  FROM " . storage::$DBTablePrefix . "marketlog l
			  LEFT JOIN " . storage::$DBTablePrefix . "useraccounts u ON u.userId = l.userId
			  WHERE l.marketId = " . $marketId;
			  
		if ($actions)
		{
			$q .= " AND (";
			for ($i = 0; $i < count($actions); $i++)
			{
				$q .= "l.action = '" . mysql_escape_string($actions[$i]) . "'";
				if ($i < count($actions)-1)
					$q .= " OR ";
			}
			$q .= ")";
		}
		
		$q .= "ORDER BY timestamp DESC ";
			  		
		if ($count)
			$q .= " LIMIT " . $start . ", " . $count;
		
		$r = mysql_query($q, $this->_conn);

		while ($row = mysql_fetch_assoc($r))
			$results[] = marketlog::createFromAssoc($row);

	    return $results;
		
	}

}
?>