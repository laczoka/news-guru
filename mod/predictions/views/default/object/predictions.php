<?php echo elgg_view_title('<a href="' . $vars['entity']->getURL() . '">'
        . $vars['entity']->guid . ' : ' .$vars['entity']->title . '</a>');

global $CONFIG;
include_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/engine/start.php");

$page_viewer = get_loggedin_user();
$size = 100.0;
$volume = 0.0;
$m = $vars['entity'];

/*
$e = elgg_get_entities(array('type' => 'object', 'subtype' => 'transaction', limit => 0,
    'offset' => 0, 'full_view' => FALSE));


foreach ($e as $t) {
    if ($m->guid == $t->market) {
        $volume += $size;
    }
}
*/

$owner = get_entity($m->owner_guid);

?>

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

                echo round(100*$vars['entity']->value1, 0)
                        . '%</td><td><input style="margin: 0px" type="submit" value="Bet 100" />';

                ?>
            </span>
        </form>
    </td>
<?php if ($page_viewer->guid == 2  || $page_viewer->guid == $m->owner_guid || $page_viewer->isAdmin() ) { ?>
<td>
<form action="<?php echo $vars['url']; ?>action/predictions/settle" method="post">
        <span>

            <input type="hidden" name="market"  value="<?php echo $vars['entity']->guid ?>" />
            <input type="hidden" name="option"  value="option1" />

            <?php echo elgg_view('input/securitytoken');

            echo '<input style="margin: 0px" type="submit" value="settle" />';

            ?>
        </span>
    </form>
</td>
<?php } ?>

</tr>

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

            echo round(100*$vars['entity']->value2-0.0000001,0)
                    . '%</td><td><input style="margin: 0px" type="submit" value="Bet 100" />';

            ?>
        </span>
    </form>
</td>
<?php if ($page_viewer->guid == 2  || $page_viewer->guid == $m->owner_guid || $page_viewer->isAdmin()) { ?>
<td>
<form action="<?php echo $vars['url']; ?>action/predictions/settle" method="post">
        <span>

            <input type="hidden" name="market"  value="<?php echo $vars['entity']->guid ?>" />
            <input type="hidden" name="option"  value="option2" />

            <?php echo elgg_view('input/securitytoken');

            echo '<input style="margin: 0px" type="submit" value="settle" />';

            ?>
        </span>
    </form>
</td>
<?php } ?>

</tr>

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


<br/>

<?php if ($page_viewer->guid == 2 || $page_viewer->isAdmin()) { ?>
<br/>
<form action="<?php echo $vars['url']; ?>action/predictions/void" method="post">
        <span>

            <input type="hidden" name="market"  value="<?php echo $vars['entity']->guid ?>" />

            <?php echo elgg_view('input/securitytoken');

            echo '<input style="margin: 0px" type="submit" value="void" />';

            ?>
        </span>
    </form>
    <a href="<?php echo $vars['url']; ?>mod/predictions/edit.php?predictions=<?php echo $vars['entity']->getGUID(); ?>"><?php echo elgg_echo("edit"); ?></a>  &nbsp;
<?php } ?>

<?php if ($page_viewer->guid == 2  || $page_viewer->guid == $m->owner_guid  || $page_viewer->isAdmin()) { ?>
    <a href="<?php echo $vars['url']; ?>mod/predictions/suspend.php?predictions=<?php echo $vars['entity']->getGUID(); ?>"><?php echo elgg_echo("Suspend"); ?></a>  &nbsp;
<?php } ?>

<?php $m->volume = empty($volume)?(empty($m->volume)?0:$m->volume):$volume; echo '<br/>Betting Volume: $'. $m->volume; ?>

<?php echo '<br/>Question by : ' . $owner->username . ' <br/>' . elgg_view('output/tags', array('tags' => $vars['entity']->tags)); ?>


</div>
