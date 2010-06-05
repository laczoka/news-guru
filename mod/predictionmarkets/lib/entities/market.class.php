<?php
/**
 * market.class.php, market class definition
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */
 
/**
 * The market class represents a market entity
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */
class market 
{

	function __construct()
	{
		$this->suspension = time();
		$this->publication = time();
		$this->settlement = time();
		$this->tags = array();
		$this->options = array();
	}
	
	/**
	* returns a new instance initialized with values from an associative array provided
	* @param array $row associative array of key/value pairs to initialize the fields of the instance with
	* @return market
	*/
	public static function createFromAssoc($assoc)
	{
		$market = new market();
		$market->id = $assoc["id"];
		$market->type = $assoc["type"];
		$market->state = $assoc["state"];
		$market->creator = $assoc["creator"];

		$market->maincat = $assoc["maincat"];
		$market->subcat = $assoc["subcat"];

		$market->title = $assoc["title"];
		$market->description = $assoc["description"];
		$market->settlementdetails = $assoc["settlementdetails"];
		$market->imageurl = $assoc["imageurl"];
		
		$market->suspension = $assoc["suspension"];
		$market->publication = $assoc["publication"];
		$market->settlement = $assoc["settlement"];
		
		return $market;
	}
	

	/**
	* the id of the market
	* @access public
	* @var integer
	*/	
	public $id = 0;
	/**
	* the type of the market. valid values are 1 (yes/no) or 4 (multiple options, one answer)
	* @access public
	* @var integer
	*/
	public $type = 0;
	/**
	* the state of the market. valid values are "Created", "Open", "Suspended", "Voided", "Settled" or "Undefined"
	* @access public
	* @var string
	*/
	public $state = "Open";
	/**
	* the title of the market
	* @access public
	* @var string
	*/
	public $title = "";
	/**
	* the creator of the market
	* @access public
	* @var string
	*/
	public $creator = "";
	/**
	* the url of the image associated with the market
	* @access public
	* @var string
	*/
	public $imageurl = "";
	/**
	* the description of the market
	* @access public
	* @var string
	*/
	public $description = "";
	/**
	* the settlement details of the market
	* @access public
	* @var string
	*/
	public $settlementdetails = "";
	/**
	* the main category of the market
	* @access public
	* @var string
	*/
	public $maincat = "";
	/**
	* the sub category of the market
	* @access public
	* @var string
	*/
	public $subcat = "";

	/**
	* the publication date of the market
	* @access public
	* @var datetime
	*/
	public $publication = null;
	/**
	* the suspend date of the market
	* @access public
	* @var datetime
	*/
	public $suspension = null;
	/**
	* the settlement date of the market
	* @access public
	* @var datetime
	*/
	public $settlement = null;
	/**
	* the options associated with the market
	* @access public
	* @var array of marketoption
	*/
	public $options = null;
	/**
	* the tag associated with the market
	* @access public
	* @var array of string
	*/
	public $tags = null;

	
}
?>
