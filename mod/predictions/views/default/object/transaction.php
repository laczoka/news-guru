<?php echo elgg_view_title($vars['entity']->title); ?>
<?php

// init
$t = $vars['entity'];
$m = get_entity($t->market);

$page_viewer = get_loggedin_user();
$size = 100.0;
$factor = 100.0;    // sensitivity factor

$option = $t->option;

?>

<div class="contentWrapper">





    <?php

    // Get the integral of the weighted price
    if ($option == 'option1') {
        $ev = $size * (1/$t->price) ;
    } else {
        $ev = $size * (1/(1-$t->price)) ;
    }

    // How far approximately will we move?
    if ($option == 'option1') {
        $stretch =  $ev * $m->value1;
    } else {
        $stretch =  $ev * $m->value1;
    }

    // Get the approximate price difference
    if ($option == 'option1') {
        $diff = $stretch/$size * ($m->value2 / $factor);
    } else {
        $diff = $stretch/$size * ($m->value2 / $factor);
    }


    // Get the approximate trade out value
    if ($option == 'option1') {
        $fair = ($m->value1 - $diff/2.0 )*$ev;
    } else {
        $fair = ($m->value1 - $diff/2.0 )*$ev;
    }

    // 2nd approximation
    if ($option == 'option1') {
        $stretch2 =  $fair;
    } else {
        $stretch2 =  $fair;
    }

    $f = $stretch2 / ($size * $factor);
    if ($option == 'option1') {
        $diff2 = $m->value1 - (( $m->value1 - $f) / ( 1 - $f))  ;
    } else {
        $diff2 = $m->value1 - (( $m->value1 - $f) / ( 1 - $f))  ;
    }

    if ($option == 'option1') {
        $fair2 = ($m->value1 - $diff2/2.0 )*$ev;
    } else {
        $fair2 = ($m->value1 - $diff2/2.0 )*$ev;
    }
    
    ?>

    <p><strong>Transaction ID:</strong> <?php echo $t->guid; ?></p>
    <p><strong>Market:</strong> <?php echo $m->title ; ?></p>
    <p><strong>Selection:</strong> <?php echo $option; ?></p>
    <p><strong>Your Price:</strong> <?php echo ($option == 'option1')?round($t->price*100.0,2):round($t->price*100.0,2); ?>%</p>
    <p><strong>Spot Price:</strong> <?php echo ($option == 'option1')?round($m->value1*100.0,2):round($m->value2*100.0,2); ?>%</p>
    <p><strong>Size:</strong> $<?php echo $vars['entity']->size; ?></p>

    <p><strong>Return if you win:</strong> +$<?php echo round($ev, 2) ?></p>
    <p><strong>Loss if you lose:</strong> -$<?php echo $size ?> </p>
    <p><strong>EV:</strong> +$<?php echo round($ev, 20) ?></p>
    <p><strong>Stretch:</strong> +$<?php echo round($stretch, 20) ?></p>
    <p><strong>Diff:</strong> +<?php echo round($diff, 40) ?></p>
    <p><strong>Fair:</strong> +$<?php echo round($fair, 20) ?></p>
    <p><strong>Stretch2:</strong> +$<?php echo round($stretch2, 20) ?></p>
    <p><strong>Diff2:</strong> +<?php echo round($diff2, 40) ?></p>
    <p><strong>Fair2:</strong> +$<?php echo round($fair2, 20) ?></p>

    <?php

    if (!empty($vars['entity']->status )) {
        echo '<strong>Status: </strong>' . $vars['entity']->status . '<br/>';
    }
    ?>

    <form action="<?php echo $vars['url']; ?>action/predictions/tradeout" method="post">

        <input type="hidden" name="transaction"  value="<?php echo $vars['entity']->guid ?>" /></p>
        <input type="hidden" name="price"  value="<?php echo $selected ?>" />

        <?php echo elgg_view('input/securitytoken');
        if ($t->status == 'open') {
            echo '<input type="submit" value="Trade Out" />';
        }


        ?>
    </form>

</div>
