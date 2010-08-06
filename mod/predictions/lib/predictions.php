<?php

function two_options_current_bet_fair_value($transaction, $market)
{
	// sensitivity factor
	$factor = 100.0;
	// Get the integral of the weighted price
    if ($transaction->option == 'option1') {
        $ev = $transaction->size * (1/$transaction->price) ;
    } else {
        $ev = $transaction->size * (1/$transaction->price) ;
    }

    // How far approximately will we move?
    if ($transaction->option == 'option1') {
        $stretch =  $ev * $market->value1;
    } else {
        $stretch =  $ev * $market->value2;
    }

    // Get the approximate price difference
    if ($transaction->option == 'option1') {
        $diff = $stretch/$transaction->size * ($market->value2 / $factor);
    } else {
        $diff = $stretch/$transaction->size * ($market->value1 / $factor);
    }


    // Get the approximate trade out value
    if ($transaction->option == 'option1') {
        $fair = ($market->value1 - $diff/2.0 )*$ev;
    } else {
        $fair = ($market->value2 - $diff/2.0 )*$ev;
    }

    // 2nd approximation
    if ($transaction->option == 'option1') {
        $stretch2 =  $fair;
    } else {
        $stretch2 =  $fair;
    }

    $f = $stretch2 / ($transaction->size * $factor);
    if ($transaction->option == 'option1') {
        $diff2 = $market->value1 - (( $market->value1 - $f) / ( 1 - $f))  ;
    } else {
        $diff2 = $market->value2 - (( $market->value2 - $f) / ( 1 - $f))  ;
    }

    if ($transaction->option == 'option1') {
        $fair2 = ($market->value1 - $diff2/2.0 )*$ev;
    } else {
        $fair2 = ($market->value2 - $diff2/2.0 )*$ev;
    }
	return $fair2;
}

?>