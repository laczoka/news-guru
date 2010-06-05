<?php

/**
 * marketcalculator.class.php, market calculator class definition
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 */
 
/**
 * the marketcalculator class implements some Hanson Logarithmic MSR
 * routines to calculate price, position and stake size
 * for prediction markets
 * also see: http://blog.oddhead.com/2006/10/30/implementing-hansons-market-maker/
 * @author Constantinus van der Kruijs <constantinus@vanderkruijs.net>
 * @copyright (C) 2010 Constantinus van der Kruijs
 * @version 1.0
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License version 3
 * @package predictionmarkets
 * @subpackage entities
 */
class marketcalculator 
{

	/**
	 * Returns the size of the stake in shares in return for the given price.
	 * @param marketoption[] $options an array of market options on which the calculation is based
	 * @param integer $selectedindex the index of the option which is chosen to calculate
	 * @param double $price the investment made
     * @return double
     */
	public function calculateStakeSize($options, $selectedindex, $price)
	{
		$b = 10000.0;
		
		$stake_sum1 = $this->stake_sum($options, $b);
		$stake_sum2 = $this->stake_sum($options, $b, $selectedindex);
		
		$exp = exp(($price + ($b * log($stake_sum1))) / $b );
		
		$log = log($exp - $stake_sum2);
		
		return 
			$b * $log
			- $options[$selectedindex]->shares;
	}
	
	private function stake_sum($options, $b, $ommitedindex = -1)
	{
		$result = 0.0;
		for ($i = 0; $i < count($options); $i++)
		{
			if ($i != $ommitedindex)
				$result += exp($options[$i]->shares / $b);
		}
		return $result;
	}

	/**
	 * Returns the price of an given amount of shares
	 * @param marketoption[] $options an array of market options on which the calculation is based
	 * @param integer $selectedindex the index of the option which is chosen to calculate
	 * @param double $shares the amount of shares, if negative it represents a selling transaction, otherwise buying.
     * @return double
     */
	public function calculateStakeCost($options, $selectedindex, $shares)
	{
		$b = 10000.0;
		
		$cost_sum1 = $this->cost_sum($options, $b);
		$cost_sum2 = $this->cost_sum($options, $b, $selectedindex, $shares);

		return $b * log($cost_sum1) - $b * log($cost_sum2);
	}

	private function cost_sum($options, $b, $selectedindex = -1, $shares = 0)
	{
		$result = 0.0;
		for ($i = 0; $i < count($options); $i++)
		{
			if ($i == $selectedindex)
				$result += exp(($options[$i]->shares + $shares) / $b);
			else
				$result += exp($options[$i]->shares / $b);
		}
		return $result;
	}

}

?>