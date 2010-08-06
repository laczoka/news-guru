<?php require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/output_helper.php' ?>
<?php require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/predictions.php' ?>

<?php $user = $vars['user'] ?>
<?php $open_transactions = $vars['open_transactions']?>
<?php $size = 100 ;?>
<br />
<div style="font-size: 16px; font-weight: bold">Free Cash Balance&nbsp&nbsp<span style="color: green">$<?php echo $user->opendollars ?></span></div>
<br />

<table style="width:100%;">
<thead style="padding-bottom:10px;">
<tr>
    <th><b>Market</b></th><th><b>Your choice</b></th><th><b>Trade time</b></th><th><b>Your<br />price</b></th><th><b>Current<br />value</b></th><th><b>If right<br />You win</b></th>
    <th><b>If Wrong<br />You Lose</b></th><th><b>Actions</b></th>
</tr>
</thead>
<tbody>
<?php foreach ($open_transactions as $tr): ?>
<?php $market = get_entity($tr->market) ?>
<?php $ev = $size * (1/$tr->price) ;?>
<tr>
    <td><a href="<?php echo $market->getURL()?>"><?php echo trim_text($market->title,15) ?></a></td>
    <td><a href="<?php echo $tr->getURL()?>"><?php echo ($tr->option == 'option1') ? $market->option1 : $market->option2; ?></a></td>
    <td><?php echo str_replace(" ","<br />",date("d/M H:i:s",$tr->getTimeCreated())) ?></td>
    <td><?php echo round($tr->price * 100) ?>%</td>
    <td>+$<?php echo round(two_options_current_bet_fair_value($tr, $market),0) ?></td>
    <td>+$<?php echo round($ev, 0) ?></td>
    <td>-$<?php echo $size ?></td>
    <td><?php echo elgg_view('transaction/b_tradeout', array("transaction_id"=> $tr->guid)) ?>
        <?php echo elgg_view('transaction/b_bet', array("label" => "Bet again $100", "market"=> $tr->market, "option" => $tr->option )) ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php ?>