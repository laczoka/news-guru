<?php echo elgg_view_title($vars['entity']->title); ?>

<div class="contentWrapper">

    <p><?php echo $vars['entity']->description; ?></p>

    <?php

    echo '<table style="border-spacing: 10px;">';

    echo '<tr><td>' . $vars['entity']->option1 . '</td>';


    ?>
    <td>
        <form action="<?php echo $vars['url']; ?>action/predictions/bet" method="post">
            <span>

                <input type="hidden" name="option"  value="option1" />
                <input type="hidden" name="market"  value="<?php echo $vars['entity']->guid ?>" />
                <input type="hidden" name="price"  value="<?php echo $vars['entity']->value1 ?>" />

                <?php echo elgg_view('input/securitytoken');

                echo round(100*$vars['entity']->value1, 2)
                        . '%</td><td><input style="margin: 0px" type="submit" value="Bet 100" />';

                ?>
            </span>
        </form>
    </td></tr>

<?php

echo '<tr><td>' . $vars['entity']->option2 . '</td>';


?>
<td>
    <form action="<?php echo $vars['url']; ?>action/predictions/bet" method="post">
        <span>

            <input type="hidden" name="option"  value="option2" />
            <input type="hidden" name="market"  value="<?php echo $vars['entity']->guid ?>" />
            <input type="hidden" name="price"  value="<?php echo $vars['entity']->value2 ?>" />

            <?php echo elgg_view('input/securitytoken');

            echo round(100*$vars['entity']->value2,2) 
                    . '%</td><td><input style="margin: 0px" type="submit" value="Bet 100" />';

            ?>
        </span>
    </form>
</td></tr>

<?php


echo '</table>';

if (!empty($vars['entity']->suspend)) {
    echo '<strong>Suspend Time: </strong>' . $vars['entity']->suspend . '<br/>';
}
if (!empty($vars['entity']->status )) {
    echo '<strong>Status: </strong>' . $vars['entity']->status . '<br/>';
}
if (!empty($vars['entity']->settlement )) {
    echo '<strong>Settlement Details: </strong>' . $vars['entity']->settlement . '<br/>';
}
?>


<?php if (isadminloggedin()) { ?>
<br/>
<form action="<?php echo $vars['url']; ?>action/predictions/settle" method="post">
        <span>

            <input type="hidden" name="market"  value="<?php echo $vars['entity']->guid ?>" />

            <?php echo elgg_view('input/securitytoken');

            echo '<input style="margin: 0px" type="submit" value="settle" />';

            ?>
        </span>
    </form> 
<?php } ?>

<?php if (isadminloggedin()) { ?>
<br/>
<form action="<?php echo $vars['url']; ?>action/predictions/void" method="post">
        <span>

            <input type="hidden" name="market"  value="<?php echo $vars['entity']->guid ?>" />

            <?php echo elgg_view('input/securitytoken');

            echo '<input style="margin: 0px" type="submit" value="void" />';

            ?>
        </span>
    </form>
<?php } ?>

<?php echo '<br/>' . elgg_view('output/tags', array('tags' => $vars['entity']->tags)); ?>

</div>
