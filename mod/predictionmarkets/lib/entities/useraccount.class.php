<?php
/**
 * useraccount.class.php, user account class definition
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */
 
/**
 * The user account class represents a user account, which provides
 * additional prediction market specific information to a user. The
 * user Id used is the same as the Elgg user id, therefor, a user
 * account needs to be associated with an excisting Elgg user entity.
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */
class useraccount
{

	/**
	* returns a new instance initialized with values from an associative array provided
	* @param array $row associative array of key/value pairs to initialize the fields of the instance with
	* @return useraccount
	*/
	public static function createFromAssoc($row)
	{
		$user = new useraccount();
		
		$user->id = $row["userId"];
		$user->name = $row["userName"];
		$user->cash = $row["cash"];
		$user->predictions = $row["predictions"];
		
		$user->allowMarketCreate = $row["allowMarketCreate"];
		$user->allowMarketEdit = $row["allowMarketEdit"];
		$user->allowMarketSuspend = $row["allowMarketSuspend"];
		$user->allowMarketSettle = $row["allowMarketSettle"];
		$user->allowMarketVoid = $row["allowMarketVoid"];
		$user->allowMarketDuplicate = $row["allowMarketDuplicate"];
	
		$user->allowMarketFlag = 1;
		$user->allowMarketComment = 1;
		$user->allowPositionBuy = 1;
		$user->allowPositionSell = 1;
			
		return $user;
	}

	/**
	* the Elgg user id
	* @access public
	* @var integer
	*/	
	public $id = 0;
	/**
	* the Elgg user name
	* @access public
	* @var string
	*/	
	public $name = "";
	/**
	* the amount of cash the user can use
	* @access public
	* @var double
	*/	
	public $cash = 0;
	/**
	* the total value of all currently open predictions
	* @access public
	* @var double
	*/	
	public $predictions = 0;
	
	/**
	* Right of the user to create markets
	* @access public
	* @var boolean
	*/	
	public $allowMarketCreate = false;
	/**
	* Right of the user to edit markets
	* @access public
	* @var boolean
	*/	
	public $allowMarketEdit = false;
	/**
	* Right of the user to suspend markets
	* @access public
	* @var boolean
	*/	
	public $allowMarketSuspend = false;
	/**
	* Right of the user to settle markets
	* @access public
	* @var boolean
	*/	
	public $allowMarketSettle = false;
	/**
	* Right of the user to void markets
	* @access public
	* @var boolean
	*/	
	public $allowMarketVoid = false;
	/**
	* Right of the user to duplicate markets
	* @access public
	* @var boolean
	*/	
	public $allowMarketDuplicate = false;
	/**
	* Right of the user to comment on markets
	* @access public
	* @var boolean
	*/	
	public $allowMarketComment = false;
	/**
	* Right of the user to flag markets
	* @access public
	* @var boolean
	*/	
	public $allowMarketFlag = false;
	/**
	* Right of the user to buy market positions
	* @access public
	* @var boolean
	*/	
	public $allowPositionBuy = true;
	/**
	* Right of the user to sell market positions
	* @access public
	* @var boolean
	*/	
	public $allowPositionSell = true;
}

?>