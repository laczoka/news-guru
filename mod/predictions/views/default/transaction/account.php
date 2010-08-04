<?php require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/output_helper.php' ?>
<?php $user = $vars['user'] ?>
<?php $open_transactions = $vars['open_transactions']?>
<?php $size = 100 ;?>
<br />
<div>Free Cash Balance $ <?php echo $user->opendollars ?></div>
<br />

<table style="width:100%;">
<thead style="padding-bottom:10px;">
<tr>
    <th>Tr-Id</th><th>Market</th><th>Your choice</th><th>Trade time</th><th>Your price</th><th>If right<br />You win</th><th>If Wrong<br />You Lose</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach ($open_transactions as $tr): ?>
<?php $market = get_entity($tr->market) ?>
<?php $ev = $size * (1/$tr->price) ;?>
<tr>
    <td><?php echo $tr->guid ?></td>
    <td><a href="<?php echo $market->getURL()?>"><?php echo trim_text($market->title,15) ?></a></td>
    <td><?php echo ($tr->option == 'option1') ? $market->option1 : $market->option2; ?></td>
    <td><?php echo str_replace(" ","<br />",date("d/M/Y H:i:s",$tr->getTimeCreated())) ?></td>
    <td><?php echo round($tr->price * 100) ?>%</td>
    <td>+$<?php echo round($ev, 0) ?></td>
    <td>-$<?php echo $size ?></td>
    <td><?php echo elgg_view('transaction/b_tradeout', array("transaction"=> $tr)) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php ?>