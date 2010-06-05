<?php
/**
 * operationresult.class.php, operation result class definition
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */

/**
 * Describes the result of an operation, providing a properties to collect
 * failure codes and description
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */
class operationresult
{

	/**
	* Constructor
	* @param boolean $success indicates whether the operation was performed successfully
	* @param integer $errno in case of a failure, contains a numeric value representing the error
	* @param string $errtext brief description of the error
	* @return void
	*/
	function __construct($success = true, $errno = 0, $errtext = "")
	{
		$this->success = $success;
		$this->errno = $errno;
		$this->errtext = $errtext;
	}

	/**
	 * Indicates whether the operation has been completed successfully
	 * @access public
	 * @var boolean
	 */
	public $success = false;
	
	/**
	 * An error code in case of failure
	 * @access public
	 * @var int
	 */
	public $errno = 0;
	
	/**
	 * A brief description of the error
	 * @access public
	 * @var string
	 */
	public $errtext = "";
	
	
	public static $OptionNotFound;
	public static $OptionPriceMoved;
	public static $OptionNotAvailable;
	
	public static $UserNotFound;
	public static $UserNotSolvent;
	public static $UserInsufficientPrivileges;
	
	public static $MarketNotFound;
	public static $MarketWrongOwner;
	public static $MarketInvalidState;
	
	public static $PositionNotFound;
	public static $PositionWrongOwner;
	public static $PositionNotAvailable;
	
}

operationresult::$OptionNotFound = new operationresult(false, 0, "Option not found");
operationresult::$OptionPriceMoved = new operationresult(false, 1, "Option price has moved");
operationresult::$OptionNotAvailable = new operationresult(false, 2, "Option has unexpected state");
	
operationresult::$UserNotFound = new operationresult(false, 3, "User not found");
operationresult::$UserNotSolvent = new operationresult(false, 4, "User not solvent");
operationresult::$UserInsufficientPrivileges = new operationresult(false, 5, "User privileges insufficient");
	
operationresult::$MarketNotFound = new operationresult(false, 6, "Market not found");
operationresult::$MarketWrongOwner = new operationresult(false, 7, "Market does not belong to user");
operationresult::$MarketInvalidState = new operationresult(false, 8, "Market has unexpected state");
	
operationresult::$PositionNotFound = new operationresult(false, 9, "Position not found");
operationresult::$PositionWrongOwner = new operationresult(false, 10, "Position does not belong to user");
operationresult::$PositionNotAvailable = new operationresult(false, 11, "Position has unexpected state");	


?>