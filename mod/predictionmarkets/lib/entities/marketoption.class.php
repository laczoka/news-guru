<?php
/**
 * marketoption.class.php, market option class definition
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
class marketoption
{

	/**
	* returns a new instance initialized with values from an associative array provided
	* @param array $row associative array of key/value pairs to initialize the fields of the instance with
	* @return marketoption
	*/
	public static function createFromAssoc($row)
	{
		$option = new marketoption();
		$option->marketId = $row["marketId"];
		$option->label = $row["label"];
		$option->shares = $row["shares"];
		$option->value = $row["value"];
		$option->id = $row["id"];
		$option->description = $row["description"];
		$option->visible = $row["visible"];
		$option->open = $row["open"];
		return $option;
	}

	/**
	* the id of the market option
	* @access public
	* @var integer
	*/	
	public $id = 0;
	/**
	* the id of the market
	* @access public
	* @var integer
	*/	
	public $marketId = 0;
	/**
	* the label of the market option
	* @access public
	* @var string
	*/	
	public $label = "";
	/**
	* sold shares
	* @access public
	* @var double
	*/	
	public $shares = 0;
	/**
	* percentage value of the shares of this option in relation to all shares of the market
	* @access public
	* @var double
	*/	
	public $value = 0;
	/**
	* a description of the market option
	* @access public
	* @var integer
	*/	
	public $description = "";
	/**
	* determines whether the market option should be visible for the user
	* @access public
	* @var bool
	*/	
	public $visible = true;
	/**
	* determines whether the market option is available for betting
	* @access public
	* @var bool
	*/	
	public $open = true;
}

?>