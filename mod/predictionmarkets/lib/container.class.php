<?php
/**
 * container.class.php, container class definition.
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
 * the container represents the Business Logic layer of the prediction
 * markets system. it utilizes storage class instances to retrieve 
 * data in form of entities from the database (or any other storage
 * subsystem)
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 */
class container 
{

	private static function createStorage()
	{
		return new storage();
	}

	/**
     * Returns an array of markets. The list contains only markets which mach the given state, where 
	 * open markets with a suspend time in the past are interpreted and treated as being suspended.
	 * Provide the start and the count
	 * params to only retrieve a part from the list.
     * @param string $state the desired state the markets return will have
	 * @param string $creator filters the list of markets to match a given creator
	 * @param string $maincat filters the list of markets to match a given main category
	 * @param string $subcat filters the list of markets to match a given sub category
	 * @param string $tag filters the list of markets to match a tag
	 * @param string[] $orderby an array of string containing the fields the list will be sorted by. valid values are: "title", "creator", "settlement", "publication", "suspension", "maincat", "subcat". append an " DESC" to sort descending.
	 * @param integer $start skips a portion of the list
	 * @param integer $count limits the list to contain only a maximum number of markets
     * @return array of market
     */
	public static function getMarkets($state, $creator = "", $maincat = "", $subcat = "", $tag = "", $orderby = array(), $start = 0, $count = 0)
	{
		return container::createStorage()->getMarkets($state, $creator, $maincat, $subcat, $tag, $orderby, $start, $count);
	}

	/**
     * Returns the number of markets in the storage which match the given state, where 
	 * open markets with a suspend time in the past are interpreted and treated as being suspended.
     * @param string $state the desired state the markets return will have
	 * @param string $creator filters the list of markets to match a given creator
	 * @param string $maincat filters the list of markets to match a given main category
	 * @param string $subcat filters the list of markets to match a given sub category
	 * @param string $tag filters the list of markets to match a tag
	 * @return integer
     */
	public static function getMarketsCount($state, $creator = "", $maincat = "", $subcat = "", $tag = "")
	{
		return container::createStorage()->getMarketsCount($state, $creator, $maincat, $subcat, $tag);
	}
	
	/**
     * Returns a market instance retrieved from the storage matching the given market id or null if the market does not exist.
     * @param integer $marketId the id of the market to be returned
     * @return market|null
     */
	public static function getMarket($marketId)
	{
		return container::createStorage()->getMarket($marketId);
	}
	
	/**
     * Returns all positions a specific user bought from a certain market, or from all users if the userId
	 * parameter is omitted. The list is filtered by the optional state parameter
     * @param integer $marketId the id of the market from which the positions should be returned
	 * @param integer $userId the id of the user which positions of the market should be returned
	 * @param string $state determines the state of the positions which will be returned, can be one of these values: "Open", "Sold", "Won", "Lost"
     * @return marketposition[]
     */
	public static function getMarketPositions($marketId, $userId = 0, $state = "Open")
	{
		return container::createStorage()->getMarketPositions($marketId, $userId, $state);
	}
	
	/**
	* returns an array containing all used categories in the storage. the 
	* categories are returned as strings formatted to contain the main category, followed by ">", followed by the sub @category
	* @return string[] array of string containing all used categories
	*/
	public static function getAllCategories()
	{
		return container::createStorage()->getAllCategories();
	}
	
	/**
	* returns an array containing all used tags in the storage. 
	* @return string[] array of string containing all used tags
	*/
	public static function getAllTags()
	{
		return container::createStorage()->getAllTags();	
	}
	
	/**
	* returns an array containing all used creators in the storage. 
	* @return string[] array of string containing all used creators
	*/
	public static function getAllCreators()
	{
		return container::createStorage()->getAllCreators();	
	}
	
	/**
     * Returns a useraccount instance retrieved from the storage matching the given user id or null 
	 * if the user does not exist.
     * @param integer $userId the id of the user which will be returned
     * @param string $userName the name of the user which will be returned. will be used instead of the userId if provided.
     * @return user|null
     */
	public static function getUserAccount($userId, $userName = "")
	{
		return container::createStorage()->getUserAccount($userId, $userName);
	}
	
	/**
     * Creates and returns a useraccount instance 
     * @param integer $userId the id of the newly created user. must be provided since the id field has set to NOT autoincrement
	 * @param string $userName the name of the newly created user
     * @return user
     */
	public static function createUserAccount($userId, $userName)
	{
		return container::createStorage()->createUserAccount($userId, $userName);
	}
		/**
     * Returns all positions for a specific user identified by the userId parameter
	 * @param integer $userId the id of the user which positions will be returned
	 * @param string $userName the name of the user which will be returned. will be used instead of the userId if provided.
     * @param integer $public filters the list of returned positions by their visibility. valid values are 0: all, 1: only public. default is only public.
	 * @param string $state filters the list of returned positions by their state. valid values are "Open", "Sold", "Won" and "Lost"
     * @return marketposition[]
     */
	public static function getUserPositions($userId, $userName = "", $public = true, $state = "Open")
	{
		return container::createStorage()->getUserPositions($userId, $userName, $public, $state);
	}
	
	/**
     * Buys a new market position for the given user.
     * @param integer $marketId the id of the market to buy the position from
	 * @param integer $optionId the id of the option associated with the market to buy the position from
	 * @param integer $userId the id of the user who buys the position
	 * @param double $price the current price to buy the position. this price is validated to match the current price
	 * @param double $stake the investment made by the user to buy the position
	 * @param integer $public (1: yes, 0: no)
	 * @return operationresult
     */
	public static function buyMarketPosition($marketId, $optionId, $userId, $price, $stake, $public = 1)
	{
		
		$result = new operationresult();
		
		$storage = container::createStorage();
		// start transaction
		$storage->startTransaction();
		
		try
		{
		
			$marketId = (int)$marketId;
			$optionId = (int)$optionId;
			$userId = (int)$userId;
			$price = (double)$price;
			$stake = (double)$stake;
		
			// check user
			if ($result->success)
				$user = container::checkUserRight(&$storage, &$result, $userId, "PositionBuy");

			if ($reslt->success && $user->cash < $stake)
				$result = operationresult::$UserNotSolvent;

			// check market
			if ($result->success)
				$market = container::checkMarket(&$storage, &$result, $marketId);
			
			if ($result->success && $market->state != "Open")
				$result = operationresult::$MarketInvalidState;
			
			// check option
			if ($result->success)
				$option = container::checkOption(&$storage, &$result, $marketId, $optionId);
			
			if ($result->success && !$option->open)
				$result = operationresult::$OptionNotAvailable;
			
			if ($result->success && $option->value != $price)
				$result = operationresult::$OptionPriceMoved;
			
			if ($result->success)
			{
			
				
				// all prerequisites are met, so 
				// proceed with
				
				// - get all options
				$options = $storage->getMarketOptions($marketId);
				
				// - find the index of the selected option
				$optionIndex = -1;
				for ($i = 0; $i < count($options); $i++)
				{
					if ($options[$i]->id == $optionId)	
					{
						$optionIndex = $i;
						break;
					}
				}
				
				// - calculating the amount of shares to be bought
				$calculator = new marketcalculator();

				$shares = $calculator->calculateStakeSize(
					$options, $optionIndex, $stake
				);	
				
				// - increase the number of shares on the option 
				$storage->increaseOptionShares($optionId, $shares);

				// - recalculate option values
				// sum all shares
				$totalshares = 0.0;
				for ($i = 0; $i < count($options); $i++)
				{
					if ($i == $optionIndex)
						$options[$i]->shares += $shares;
					$totalshares += $options[$i]->shares;
				}
				// devide each part by sum and store new value
				for ($i = 0; $i < count($options); $i++)
				{
					$options[$i]->value = $options[$i]->shares / $totalshares;
					$storage->updateOptionValue($options[$i]->id, $options[$i]->value);
				}			

				// - creating a new position for the user
				$storage->openMarketPosition(
					$marketId, 
					$optionId,
					$userId, 
					$stake, 
					$options[$optionIndex]->value, 
					$shares,
					$public
				);
				
				// - reduce users cash
				$storage->reduceUserCash($userId, $stake);
				
				// - update all open market positions 
				$marketPositions = $storage->getMarketPositions($marketId, 0, "Open");
				for ($i = 0; $i < count($marketPositions); $i++)
				{
					for ($j = 0; $j < count($options); $j++)
					{
						if ($marketPositions[$i]->optionId == $options[$j]->id)
						{
							$p = $calculator->calculateStakeCost($options, $j, -$marketPositions[$i]->shares);
							$storage->updateMarketPosition($marketPositions[$i]->id, $p, $options[$j]->value);
							break;
						}
					}
				}
				
				if ($public)
				{
					
					$marketlog = new marketlog();
					$marketlog->marketId = $marketId;
					$marketlog->userId = $userId;
					$marketlog->action = "buypos";
					$marketlog->details =
						"bought " . number_format($shares, 2) . " shares of option '" . $options[$optionIndex]->label . "' @ " . number_format($price * 100, 1) . "% paying " . number_format($stake, 2);
						
					$storage->addMarketLogEntry($marketlog);
				
				}

			}

		}
		catch (Exception $e)
		{
			// in case of an exception, rollback all actions
			$result->success = false;
			$result->errno = 0;
			$result->errtext = $e->getMessage();
		}

		if ($result->success)
		{
			$storage->commitTransaction();
		}
		else
		{
			$storage->rollbackTransaction();
		}

		return $result;	
	
	}
	
	/**
     * Sells a specific market position
     * @param integer $marketId the id of the market to which the position belongs
	 * @param integer $positionId the id of the position to be sold
	 * @param integer $userId the id of the user who performs the action. this MUST be the user that owns the position.
	 * @param double $price the price the user whishes to sell the position for. this price is validated to match the current price.
	 * @return operationresult
     */
	public static function sellMarketPosition($marketId, $positionId, $userId, $price)
	{
		
		$result = new operationresult();
		
		$storage = container::createStorage();
		// start transaction
		$storage->startTransaction();

		try
		{
		
			$marketId = (int)$marketId;
			$positionId = (int)$positionId;
			$userId = (int)$userId;
			$price = (double)$price;
		
			// check position
			if ($result->success)
				$position = container::checkPosition(&$storage, &$result, $marketId, $userId, $positionId);
			
			$shares = $position->shares;
			$marketId = $position->marketId;
			$optionId = $position->optionId;
			
			// check user
			if ($result->success)
				$user = container::checkUserRight(&$storage, &$result, $userId, "PositionSell");

			// check market
			if ($result->success)
				$market = container::checkMarket(&$storage, &$result, $marketId);
			
			if ($result->success && $market->state != "Open")
				$result = operationresult::$MarketInvalidState;
			
			// check position
			if ($result->success)
				$option = container::checkOption(&$storage, &$result, $marketId, $optionId);
				
			if ($result->success && !$option->open)
				$result = operationresult::$OptionNotAvailable;
			
			if ($result->success && $option->value != $price)
				$result = operationresult::$OptionPriceMoved;

			if ($result->success)
			{
			
				// all prerequisites are met, so 
				// proceed with
				
				// - get all options
				$options = $storage->getMarketOptions($marketId);
				
				// - find the index of the selected option
				$optionIndex = -1;
				for ($i = 0; $i < count($options); $i++)
				{
					if ($options[$i]->id == $optionId)	
					{
						$optionIndex = $i;
						break;
					}
				}
				
				// - calculating the amount of shares to be bought
				$calculator = new marketcalculator();

				$stake = $calculator->calculateStakeCost(
					$options, $optionIndex, - $shares
				);	
				
				// - decrease the number of shares on the option 
				$storage->decreaseOptionShares($optionId, $shares);
				
				// - recalculate option values
				// sum all shares
				$totalshares = 0.0;
				for ($i = 0; $i < count($options); $i++)
				{
					if ($i == $optionIndex)
						$options[$i]->shares -= $shares;
					$totalshares += $options[$i]->shares;
				}
				// devide each part by sum and store new value
				for ($i = 0; $i < count($options); $i++)
				{
					$options[$i]->value = $options[$i]->shares / $totalshares;
					$storage->updateOptionValue($options[$i]->id, $options[$i]->value);
				}			

				// - close the position for the user
				$storage->sellMarketPosition(
					$positionId, $stake, $options[$optionIndex]->value
				);
				
				// - increase users cash
				$storage->increaseUserCash($userId, $stake);
				
				// - update all remaining open market positions 
				$marketPositions = $storage->getMarketPositions($marketId, 0, "Open");
				for ($i = 0; $i < count($marketPositions); $i++)
				{
					for ($j = 0; $j < count($options); $j++)
					{
						if ($marketPositions[$i]->optionId == $options[$j]->id)
						{
							$p = $calculator->calculateStakeCost($options, $j, -$marketPositions[$i]->shares);
							$storage->updateMarketPosition($marketPositions[$i]->id, $p, $options[$j]->value);
							break;
						}
					}
				}
				
				if ($position->public)
				{
					
					$marketlog = new marketlog();
					$marketlog->marketId = $marketId;
					$marketlog->userId = $userId;
					$marketlog->action = "sellpos";
					$marketlog->details =
						"sold " . number_format($shares, 2) . " shares of option '" . $options[$optionIndex]->label . "' @ " . number_format($price * 100, 1) . "% recieving " . number_format($stake, 2);
						
					$storage->addMarketLogEntry($marketlog);

				}
								
			}

		}
		catch (Exception $e)
		{
			// in case of an exception, rollback all actions
			$result->success = false;
			$result->errno = 0;
			$result->errtext = $e->getMessage();
		}

		if ($result->success)
		{
			$storage->commitTransaction();
		}
		else
		{
			$storage->rollbackTransaction();
		}
		
		return $result;	
	}

	/**
	* Settles a market and pays out all open winning positions.
	* @param integer $marketId the id of the market to be settled 
	* @param integer $optionId the id of the winning option
	* @param integer $userId the id of the user performing the action.
	* @param string $settlementTime a string containing a UTC time which is used as settlement time. positions bought after this time will be voided.
	* @param string $settlementDetails a description of the settlement, ideally containing a reference to a source on which the settlement is based
	* @return operationresult
	*/
	public static function settleMarket($marketId, $optionId, $userId, $settlementTime, $settlementDetails)
	{
		
		$result = new operationresult();
		
		$storage = container::createStorage();
		// start transaction
		$storage->startTransaction();

		try
		{
		
			$marketId = (int)$marketId;
			$optionId = (int)$optionId;
			$userId = (int)$userId;
			
			date_default_timezone_set('UTC'); 
			$settlementTime = strtotime($settlementTime);
					
			// check user
			if ($result->success)
				$user = container::checkUserRight(&$storage, &$result, $userId, "MarketSettle");

			// check market
			if ($result->success)
				$market = container::checkMarket(&$storage, &$result, $marketId);
				
			if ($result->success && ($market->state != "Open" && $market->state != "Suspended"))
				$result = operationresult::$MarketInvalidState;
		
			// check option
			if ($result->success)
				$option = container::checkOption(&$storage, &$result, $marketId, $optionId);
				
			if ($result->success && !$option->open)
				$result = operationresult::$OptionNotAvailable;

			if ($result->success)
			{
				
				// get all open positions
				$positions = $storage->getMarketPositions($marketId);
				
				// payout to owners of winning positions
				foreach($positions as $position)
				{
					$bought = strtotime($position->bought . " UTC");
					// void positions after settlement time
					if ($bought > $settlementTime)
					{
						$storage->settleMarketPosition($position->id, $option->value, 0, "Voided", $settlementTime);
					}
					else if ($position->optionId != $optionId)
					{
						$storage->settleMarketPosition($position->id, $option->value, 0, "Lost", $settlementTime);
					}
					else 
					{
						// calculate winning amount
						$amount = (1.0 / $position->buyvalue) * $position->buyprice;
						// add amount to user cash
						$storage->increaseUserCash($position->userId, $amount);
						// set position state
						$storage->settleMarketPosition(
							$position->id, $amount, $option->value, "Won", $settlementTime
						);
					}
				}
				
				// settle market
				$storage->settleMarket($marketId, $settlementTime);				
			
				$marketlog = new marketlog();
				$marketlog->marketId = $marketId;
				$marketlog->userId = $userId;
				$marketlog->action = "settle";
				$marketlog->details = "settled market: " . $settlementDetails;
					
				$storage->addMarketLogEntry($marketlog);

			}

		}
		catch (Exception $e)
		{
			// in case of an exception, rollback all actions
			$result->success = false;
			$result->errno = 0;
			$result->errtext = $e->getMessage();
		}

		if ($result->success)
		{
			$storage->commitTransaction();
		}
		else
		{
			$storage->rollbackTransaction();
		}
		
		return $result;	
	}
	
	/**
	* Saves a market. If the Id of the market is zero (0) a new market will be created, otherwise an 
	* existing market will be updated
	* @param market $market the market to be stored
	* @param integer $userId the id of the user performing the action.
	* @return operationresult
	*/
	public static function saveMarket($market, $userId)
	{

		$result = new operationresult();
		
		$storage = container::createStorage();
		// start transaction
		$storage->startTransaction();

		try
		{

			$userId = (int)$userId;
			if (get_class($market) != "market")
				$result = operationresult::$MarketNotFound;
			
			// check user
			if ($result->success)
				$user = container::checkUserRight(&$storage, &$result, $userId, "MarketCreate");

			// check market
			if ($result->success && $market->id != 0)
				$oldmarket = container::checkMarket(&$storage, &$result, $market->id);
				
			if ($oldmarket && $oldmarket->creator != $user->name)
				$result = operationresult::$MarketWrongOwner;
			
			$market->creator = $user->name;
			
			// TODO: market details validation
			
			if ($result->success)
			{
			
				$newmarket = $market->id == 0;
				
				// if this is a new market
				if ($newmarket)
				{
					// set type on 4
					if (empty($market->type))
						$market->type = 4;
					// spread some initial shares
					for ($i = 0; $i < count($market->options); $i++)
						$market->options[$i]->shares = $market->options[$i]->value * 100;
				}
			
				// update/insert market row
				$storage->saveMarket($market);
				
				if ($newmarket)
					$result->errtext = $market->id;
					
				$marketlog = new marketlog();
				$marketlog->marketId = $marketId;
				$marketlog->userId = $userId;
				$marketlog->action = $newmarket ? "create" : "edit";
				$marketlog->details = $newmarket ? "created the market" : "edited the market";; // TODO work out some desciptive text about what happened
					
				$storage->addMarketLogEntry($marketlog);

			}

		}
		catch (Exception $e)
		{
			// in case of an exception, rollback all actions
			$result->success = false;
			$result->errno = 0;
			$result->errtext = $e->getMessage();
		}

		if ($result->success)
		{
			$storage->commitTransaction();
		}
		else
		{
			$storage->rollbackTransaction();
		}
		
		return $result;	
	}

	/**
	* Checks the existence of a market option
	* @param storage $storage existing connection to be used to perform storage operations
	* @param storageoperationresult $result out parameter which will contain an error in case the method fails
	* @param integer $marketId the id of the market to which the option belongs
	* @param integer $optionId the id of the option which should be checked
	* @return marketoption|null in case the market option is found it will be returned. otherwise null is returned alongside with an error in the $result parameter
	*/
	private static function checkOption(&$storage, &$result, $marketId, $optionId)
	{
		$option = null;
		$currentoptions = $storage->getMarketOptions($marketId);
		foreach($currentoptions as $currentoption)
		{
			if ($currentoption->id == $optionId)
			{
				$option = $currentoption;
				break;
			}
		}
		
		if (empty($option))
			$result = operationresult::$OptionNotFound;

		if ($result->success)
			return $option;	
	}

	/**
	* Checks the existence of the user and whether the user has a certain privilege
	* @param storage $storage existing connection to be used to perform storage operations
	* @param storageoperationresult $result out parameter which will contain an error in case the method fails
	* @param integer $userId the id of the user to be checked
	* @param string $optionId the privilege of the user which the user must have
	* @return useraccount|null in case the user is found and has the given privilege it will be returned. otherwise null is returned alongside with an error in the $result parameter
	*/
	private static function checkUserRight(&$storage, &$result, $userId, $right)
	{
		$user = $storage->getUserAccount($userId);
		if (empty($user))
			$result = operationresult::$UserNotFound;
		else
		{
			$privileged = false;
			switch($right)
			{
				
				case "PositionBuy": $privileged = $user->allowPositionBuy; break;
				case "PositionSell": $privileged = $user->allowPositionBuy; break;
				
				case "MarketCreate": $privileged = $user->allowMarketCreate; break;
				case "MarketEdit": $privileged = $user->allowMarketEdit; break;
				case "MarketSuspend": $privileged = $user->allowMarketSuspend; break;
				case "MarketSettle": $privileged = $user->allowMarketSettle; break;
				case "MarketVoid": $privileged = $user->allowMarketVoid; break;
				case "MarketFlag": $privileged = $user->allowMarketFlag; break;
				case "MarketComment": $privileged = $user->allowMarketComment; break;
				
			}
			if (!$privileged)
				$result = operationresult::$UserInsufficientPrivileges;
		}

		if ($result->success)
			return $user;
	}

	/**
	* Checks the existence of a market
	* @param storage $storage existing connection to be used to perform storage operations
	* @param storageoperationresult $result out parameter which will contain an error in case the method fails
	* @param integer $marketId the id of the market to be checked
	* @return market|null in case the market is found it will be returned. otherwise null is returned alongside with an error in the $result parameter
	*/
	private static function checkMarket(&$storage, &$result, $marketId)
	{
		$market = $storage->getMarket($marketId);
		if (empty($market))
			$result = operationresult::$MarketNotFound;
		
		if ($result->success)
			return $market;
	}
		
	/**
	* Checks the existence of a marketposition, its owner and its state
	* @param storage $storage existing connection to be used to perform storage operations
	* @param storageoperationresult $result out parameter which will contain an error in case the method fails
	* @param integer $marketId the id of the market to which the position belongs
	* @param integer $userId the id of the user to which the position belongs
	* @param integer $positionId the id of the position to be checked.
	* @return marketposition|null in case the marketposition is found, its state is "Open" and the userId matches the given userId, it will be returned. otherwise null is returned alongside with an error in the $result parameter
	*/
	private static function checkPosition(&$storage, &$result, $marketId, $userId, $positionId)
	{
		$position = null;
		$currentpositions = $storage->getMarketPositions($marketId);
		foreach($currentpositions as $currentposition)
		{
			if ($currentposition->id == $positionId)
			{
				$position = $currentposition;
				if ($currentposition->userId != $userId)
					$result = operationresult::$PositionWrongOwner;
				else if ($currentposition->state != "Open")
					$result = operationresult::$PositionNotAvailable;
				break;
			}
		}
		
		if (empty($position))
			$result = operationresult::$PositionNotFound;
		
		if ($result->success)
			return $position;
		
	}
	
	/**
	* Adds a comment to the market log for the specified market
	* @param integer $marketId the id of the market the comment refers to
	* @param integer $userId the id of the user submitting the comment
	* @param string $text the comment text
	* @return operationresult
	*/
	public static function addMarketComment($marketId, $userId, $text) {

		$result = new operationresult();

		$storage = container::createStorage();
		// start transaction
		$storage->startTransaction();

		try
		{
			
			if ($result->success)
				$user = container::checkUserRight(&$storage, &$result, $userId, "MarketComment");
			
			if ($result->success)
				$market = container::checkMarket(&$storage, &$result, $marketId);

			if ($result->success)
			{
		
				$marketlog = new marketlog();
				$marketlog->marketId = (int)$marketId;
				$marketlog->userId = (int)$userId;
				$marketlog->action = "comment";
				$marketlog->details = $text;
				$storage->addMarketLogEntry($marketlog);
				
			}

		}
		catch (Exception $e)
		{
			// in case of an exception, rollback all actions
			$result->success = false;
			$result->errno = 0;
			$result->errtext = $e->getMessage();
		}

		if ($result->success)
		{
			$storage->commitTransaction();
		}
		else
		{
			$storage->rollbackTransaction();
		}
		
		return $result;	

	}
	
	/**
	* Adds a flag to the market log for the specified market
	* @param integer $marketId the id of the market the comment refers to
	* @param integer $userId the id of the user submitting the comment
	* @param string $text the comment text
	* @param boolean $suspend suspends the market after flagging
	* @return operationresult
	*/
	public static function flagMarket($marketId, $userId, $text, $suspend) {

		$result = new operationresult();

		$storage = container::createStorage();
		// start transaction
		$storage->startTransaction();

		try
		{

			$userId = (int)$userId;
			$marketId = (int)$marketId;

			// check user
			if ($result->success)
				$user = container::checkUserRight(&$storage, &$result, $userId, "MarketFlag");
			
			if ($result->success && $suspend)
				$user = container::checkUserRight(&$storage, &$result, $userId, "MarketSuspend");
			
			// check market
			if ($result->success)
				$market = container::checkMarket(&$storage, &$result, $marketId);

			if ($result->success && $suspend && $market->state != "Open")
				$result = operationresult::$MarketInvalidState;

			if ($result->success)
			{

				$marketlog = new marketlog();
				$marketlog->marketId = $marketId;
				$marketlog->userId = $userId;
				$marketlog->action = "flag";
				$marketlog->details = "flagged: " . $text;
				$storage->addMarketLogEntry($marketlog);

				if ($suspend)
				{
					$storage->setMarketState($marketId, "Suspended");
					$marketlog = new marketlog();
					$marketlog->marketId = $marketId;
					$marketlog->userId = $userId;
					$marketlog->action = "suspend";
					$marketlog->details = "suspended the market";
					$storage->addMarketLogEntry($marketlog);
				}
				
			}
			
			
		}
		catch (Exception $e)
		{
			// in case of an exception, rollback all actions
			$result->success = false;
			$result->errno = 0;
			$result->errtext = $e->getMessage();
		}

		if ($result->success)
		{
			$storage->commitTransaction();
		}
		else
		{
			$storage->rollbackTransaction();
		}
		
		return $result;	
		
	}

	/**
	* Return the entries from the log for a specific market
	* @param integer $marketId the id of the market to retrieve the log for
	* @param string[] $actions the types of operation performed, valid values: 'create','edit','suspend','void','settle','flag','buypos','sellpos','comment'
	* @param integer $start skips a portion of the list
	* @param integer $count limits the list to contain only a maximum number of markets
	*/
    public static function getMarketLog($marketId, $actions, $start = 0, $count = 0)
	{
		return container::createStorage()->getMarketLog($marketId, $actions, $start, $count);
	}
}


?>