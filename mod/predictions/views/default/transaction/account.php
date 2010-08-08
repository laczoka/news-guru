<?php require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/output_helper.php' ?>
<?php require_once dirname(dirname(dirname(dirname(__FILE__)))).'/lib/predictions.php' ?>

<?php $user = $vars['user'] ?>
<?php $open_transactions = $vars['open_transactions']?>
<?php $size = 100 ;?>
<br />
<table style="margin: 10px;font-size: 16px;">
    <tbody>
    <tr>
        <td style="font-weight: bold">Free Cash Balance&nbsp&nbsp</td><td style="font-weight: bold;color: green">$<?php echo $user->opendollars ?></td></div>
    </tr>
    <tr>
        <td style="font-weight: bold">Total Net Asset&nbsp&nbsp</td><td style="font-weight: bold;color: blue">$<?php echo $user->report_total_net_asset_value ?>&nbsp<span style="font-size: 9px; font-style:italic; font-weight: normal">(estimate)</span></td>
    </tr>
    </tbody>
</table>
<br />

<table id="myAccount" class="predictions_table tablesorter">
<thead>
<tr class="odd">
    <th scope="col" class="col_3_of_12">Market</th>
    <th scope="col" class="col_2_of_12">Your choice</th>
    <th scope="col" class="col_1_of_12">Trade time</th>
    <th scope="col" class="col_1_of_12">Your price</th>
    <th scope="col" class="col_1_of_12">Current value</th>
    <th scope="col" class="col_1_of_12">Max. win</th>
    <th scope="col" class="col_1_of_12">Max. loss</th>
    <th scope="col" class="col_2_of_12_last">Actions</th>
</tr>
</thead>
<tbody>
<?php $rowcnt = 0 ;?>
<?php foreach ($open_transactions as $tr): ?>
<?php $market = get_entity($tr->market) ?>
<?php $ev = $size * (1/$tr->price) ;?>
<tr <?php echo ($rowcnt++ % 2) ? 'class="odd"' : 'class="even"' ?> >
    <td><a href="<?php echo $market->getURL()?>"><?php echo trim_text($market->title,22) ?></a></td>
    <td><a href="<?php echo $tr->getURL()?>"><?php echo ($tr->option == 'option1') ? $market->option1 : $market->option2; ?></a></td>
    <td><?php echo str_replace(" ","<br />",date("d/M H:i:s",$tr->getTimeCreated())) ?></td>
    <td><?php echo round($tr->price * 100) ?>%</td>
    <td>+$<?php echo round(two_options_current_bet_fair_value($tr, $market),0) ?></td>
    <td>+$<?php echo round($ev, 0) ?></td>
    <td>-$<?php echo $size ?></td>
    <td><?php echo elgg_view('transaction/b_action', array(label=> "Tradeout", width => "85%", action_url=> "/action/predictions/tradeout?transaction=".$tr->guid)) ?>
        <?php echo elgg_view('transaction/b_action', array(label => "Bet again $100", width => "85%", action_url=> "/action/predictions/bet?market=".$tr->market."&option=".$tr->option)) ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<script type="text/javascript">
$(document).ready(function() 
{ 
    $("#myAccount").tablesorter({
        // initial sort by trade date, most recent first
        sortList: [[2,1]],
        // enable handling of "zebra" rows
        widgets: ['zebra'],
        // disable sorting on "Actions" tab
        headers: { 7: { sorter:false }}
                                            }); 
}); 
</script>