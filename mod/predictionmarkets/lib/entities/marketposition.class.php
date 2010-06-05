<?php
/**
 * marketposition.class.php, market position class definition
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */
 
/**
 * The market option class represents a market option entity, as
 * associated with a market
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */
class marketposition
{

	function __construct()
	{
		$this->bought = time();
		$this->sold = time();
	}

	/**
	* returns a new instance initialized with values from an associative array provided
	* @param array $row associative array of key/value pairs to initialize the fields of the instance with
	* @return marketposition
	*/
	public static function createFromAssoc($row)
	{
		$position = new marketposition();
		$position->id = $row["id"];
		$position->marketId = $row["marketId"];
		$position->marketTitle = $row["markettitle"];
		$position->optionId = $row["optionId"];
		$position->optionTitle = $row["optiontitle"];	
		$position->public = $row["public"];	
		$position->userId = $row["userId"];
		$position->bought = $row["bought"];
		$position->sold = $row["sold"];
		$position->buyprice = $row["buyprice"];
		$position->currprice = $row["currprice"];	
		$position->sellprice = $row["sellprice"];	
		$position->buyvalue = $row["buyvalue"];
		$position->currvalue = $row["currvalue"];	
		$position->sellvalue = $row["sellvalue"];		
		$position->shares = $row["shares"];	
		$position->state = $row["state"];	
		return $position;
	}

	/**
	* the id of the market position
	* @access public
	* @var integer
	*/	
	public $id = 0;
	/**
	* the id of the market associated with this position
	* @access public
	* @var integer
	*/	
	public $marketId = 0;
	/**
	* the title of the market associated with this position
	* @access public
	* @var string
	*/	
	public $marketTitle = "";
	/**
	* the id of the market option associated with this position.
	* @access public
	* @var integer
	*/	
	public $optionId = 0;
	/**
	* the title of the market option associated with this position.
	* @access public
	* @var string
	*/	
	public $optionTitle = "";
	/**
	* the id of the owner of the position
	* @access public
	* @var integer
	*/	
	public $userId = 0;
	/**
	* determines the visibility of the position
	* @access public
	* @var integer
	*/	
	public $public = false;
	/**
	* the state of the position (Open, Sold, Won, Lost)
	* @access public
	* @var string
	*/	
	public $state = "";
	/**
	* the amount of shares of the position
	* @access public
	* @var double
	*/	
	public $shares = 0;
	/**
	* the date and time at the moment the position was bought
	* @access public
	* @var time
	*/	
	public $bought = null;
	/**
	* the date and time at the moment the position was sold
	* @access public
	* @var time
	*/	
	public $sold = null;
	/**
	* the price of the position at the moment the position was bought.
	* @access public
	* @var double
	*/	
	public $buyprice = 0;
	/**
	* the price of the position at the moment the position was sold.
	* @access public
	* @var double
	*/	
	public $sellprice = 0;
	/**
	* the current price of the position
	* @access public
	* @var double
	*/	
	public $currprice = 0;
	/**
	* the price of the option at the moment the position was bought as percentage of the shares in the option in relation to all
	* options of the market. must be a value between 0.00 and 100.00
	* @access public
	* @var double
	*/	
	public $buyvalue = 0;
	/**
	* the price of the option at the moment the position was sold as percentage of the shares in the option in relation to all
	* options of the market. must be a value between 0.00 and 100.00
	* @access public
	* @var double
	*/	
	public $sellvalue = 0;
	/**
	* the price of the option at this moment as percentage of the shares in the option in relation to all
	* options of the market. must be a value between 0.00 and 100.00
	* @access public
	* @var double
	*/	
	public $currvalue = 0;
}

?>