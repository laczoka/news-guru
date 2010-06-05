<?php
/**
 * marketlog.class.php, market log class definition
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */

/**
 * Describes an entry in the market log table
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */
class marketlog
{

	/**
	* Constructor
	* @param integer $marketId the subject of the operation 
	* @param integer $userId the user performing the operation
	* @param string $action the type of operation performed, valid values: 'create','edit','suspend','void','settle','flag','buypos','sellpos','comment'
	* @param string $details details about the operation
	* @return void
	*/
	function __construct($marketId = 0, $userId = 0, $action = "", $details = "")
	{
		$this->marketId = $marketId;
		$this->userId = $userId;
		$this->action = $action;
		$this->details = $details;
		$this->timestamp = time();
	}

	/**
	* returns a new instance initialized with values from an associative array provided
	* @param array $row associative array of key/value pairs to initialize the fields of the instance with
	* @return marketlog
	*/
	public static function createFromAssoc($row)
	{
		$marketlog = new marketlog();
		
		$marketlog->marketId = $row["marketId"];
		$marketlog->timestamp = $row["timestamp"];
		$marketlog->userId = $row["userId"];
		$marketlog->action = $row["action"];
		$marketlog->details = $row["details"];
		$marketlog->username = $row["username"];
			
		return $marketlog;
	}


	/**
	 * the subject of the operation 
	 * @access public
	 * @var integer
	 */
	public $marketId = 0;
	
	/**
	 * the user performing the operation
	 * @access public
	 * @var int
	 */
	public $userId = 0;
	
	/**
	 * the user performing the operation
	 * @access public
	 * @var string
	 */
	public $username = "";
	
	/**
	 * the type of operation performed, valid values: 'create','edit','suspend','void','settle','flag','buypos','sellpos','comment'
	 * @access public
	 * @var string
	 */
	public $action = "";
	
	/**
	 * details of the action
	 * @access public
	 * @var string
	 */
	public $details = "";
	
	/**
	 * the time of the action being performed
	 * @access public
	 * @var datetime
	 */
	public $datetime = null;
	
}

?>