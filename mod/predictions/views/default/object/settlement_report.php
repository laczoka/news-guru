<?php $settlement_report = $vars['entity'];
      // this is a HACK should return deserialized stuff immediatelly
      $report_content = unserialize($vars['entity']->report);
      $market_creator_return = $report_content['market_creator_total_return'];
      $settlement_officer_return = $report_content['settlement_officer_total_return'];
      $report_transactions = $report_content['transactions']; 
      $market = get_entity($settlement_report->market);
      $market_creator = get_entity($market->owner_guid);
      $market_settled_by = get_entity($market->settled_by);
 ?>
<?php echo elgg_view_title('<a href="' . $settlement_report->getURL() . '">'
        . $settlement_report->title . '</a>'); ?>
<div class="contentWrapper">
<table class="two_column_table" style="margin: 10px;font-size: 16px;">
    <thead>
    	<tr>
    		<th scope="col" style="width:30%" ></th>
    		<th scope="col" style="width:70%" ></th>
    	</tr>
   	</thead>
    <tbody>
    <tr>
        <td style="font-weight: bold">Market &nbsp&nbsp</td><td ><a href="<?php $market->getURL() ?>"><?php echo $market->title ?></a></td>
    </tr>
    <tr>
        <td style="font-weight: bold">Created by</td>
        <td><a href="<?php echo $market_creator->getURL() ?>"><?php echo $market_creator->name ?></a>&nbsp
        	(<?php echo isset($market_creator_return) ? ($market_creator_return >= 0.0 ? "Won $".$market_creator_return : "Lost $".$market_creator_return) : "had no stakes" ?>)
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold">Created&nbsp&nbsp</td><td><?php echo date("d/M/Y H:i:s",$market->getTimeCreated()) ?></td>
    </tr>
    <?php if (isset($market->suspended_utc)): /* legacy markets may not have this attribute */?>
    <tr>
        <td style="font-weight: bold">Trading suspended&nbsp</td><td><?php echo date("d/M/Y H:i:s",$market->suspended_utc) ?></td>
    </tr>
    <?php endif; ?>
    <tr>
        <td style="font-weight: bold">Settled &nbsp&nbsp</td><td><?php echo date("d/M/Y H:i:s",$settlement_report->getTimeCreated()) ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold">Outcome &nbsp&nbsp</td><td><?php echo $market->outcome ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold">Settled by</td>
        <td><a href="<?php echo $market_settled_by->getURL() ?>"><?php echo $market_settled_by->name ?></a>&nbsp
        (<?php echo isset($settlement_officer_return) ? ($settlement_officer_return >= 0.0 ? "Won $".$settlement_officer_return : "Lost $".$settlement_officer_return) : "had no stakes" ?>)
        </td>
    </tr>
    <tr>
        <td style="font-weight: bold; border-bottom: dashed 1px; padding-top:1em; " colspan="2">Public belief at settlement</td>
    </tr>
    <tr>
        <td><?php echo $market->option1 ?></td><td style="font-weight:bold"><?php echo round($market->value1*100, 0) ?>%</td>
    </tr>
    <tr>
        <td><?php echo $market->option2 ?> </td><td style="font-weight:bold"> <?php echo round($market->value2*100, 0) ?>%</td>
    </tr>
    </tbody>
</table>
<br />
<table id="settlementReport" class="predictions_table tablesorter">
<thead>
<tr class="odd">
    <th scope="col" class="col_2_of_12">Owner</th>
    <th scope="col" class="col_3_of_12">Wager</th>
    <th scope="col" class="col_3_of_12">Bet placed</th>
    <th scope="col" class="col_2_of_12">Price (Stake)</th>
    <th scope="col" class="col_2_of_12_last">Win/Loss</th>
</tr>
</thead>
<tbody>
<?php $rowcnt = 0 ;?>
<?php foreach ($report_transactions as $win): ?>
<tr <?php echo ($rowcnt++ % 2) ? 'class="odd"' : 'class="even"' ?> >
    <td><a href="<?php echo $win['owner_url'] ?>"><?php echo $win['owner_name'] ?></a></td>
    <td><a href="<?php echo $win['tr_url'] ?>"><?php echo $win['option_name'] ?></a></td>
    <td ts="<?php echo $win['tr_created'] ?>"><?php echo str_replace(" ","<br />",date("d/M H:i:s",$win['tr_created'])) ?></td>
    <td><?php echo round($win['price'] * 100) ?>% &nbsp<?php echo "($".$win['stake'].")" ?></td>
    <td><?php echo ($win['win'] >= 0.0 ? "+$": "-$").abs($win['win']) ?></td> 
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<script type="text/javascript">

//add parser for +/-$[number] format 
$.tablesorter.addParser({ 
    // set a unique id 
    id: 'opendollar', 
    is: function(s) { 
        // return false so this parser is not auto detected 
        return false; 
    }, 
    format: function(s) { 
        // format your data for normalization 
        return s.replace("$","").replace("+",""); 
    }, 
    // set type, either numeric or text 
    type: 'numeric' 
});

$.tablesorter.addParser({ 
    // set a unique id 
    id: 'attrib-ts', 
    is: function(s) { 
        // return false so this parser is not auto detected 
        return false; 
    }, 
    format: function(s,table,cell) { 
        var ts = $(cell).attr("ts"); 
        return ts ? ts : 0; 
    }, 
    // set type, either numeric or text 
    type: 'numeric' 
});

$(document).ready(function() 
{ 
    $("#settlementReport").tablesorter({
        // initial sort by trade date, most recent first
        sortList: [[2,1]],
        // enable handling of "zebra" rows
        widgets: ['zebra'],

        // disable sorting on "Actions" tab
        // activate opendollar parser
        headers: { 2 : { sorter: 'attrib-ts' },
                   4 : { sorter: 'opendollar' }
                                            }}); 
}); 
</script>