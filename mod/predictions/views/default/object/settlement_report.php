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
<div class="yui-b">
    <div class="yui-gf">
        <div class="yui-u first boldc pad04em"><div>Market</div></div>
        <div class="yui-u pad04em"><div><a href="<?php $market->getURL() ?>"><?php echo $market->title ?></a></div></div>        
    </div>
</div>
<div class="yui-b" style="margin-top: 0.8em">
    <div class="yui-g">
        <div class="yui-g first">
            <div class="yui-u first boldc pad04em">
                <div>Created by</div>
                <div>Created</div>
            </div>
            <div class="yui-u pad04em">
                    <div><a href="<?php echo $market_creator->getURL() ?>"><?php echo $market_creator->name ?></a>&nbsp
                        (<?php echo isset($market_creator_return) ? ($market_creator_return >= 0.0 ? "Won $".$market_creator_return : "Lost $".abs($market_creator_return)) : "had no stakes" ?>)</div>
                    <div><?php echo date("d/M/Y H:i:s",$market->getTimeCreated()) ?></div>
            </div>        
        </div>    
        <div class="yui-g">
            <div class="yui-u first boldc pad04em">
                <div>Settled by</div>
                <div>Settled</div>
            </div>
            <div class="yui-u pad04em">
                <div><a href="<?php echo $market_settled_by->getURL() ?>"><?php echo $market_settled_by->name ?></a>&nbsp
                    (<?php echo isset($settlement_officer_return) ? ($settlement_officer_return >= 0.0 ? "Won $".$settlement_officer_return : "Lost $".abs($settlement_officer_return)) : "had no stakes" ?>)</div>
                <div><?php echo date("d/M/Y H:i:s",$settlement_report->getTimeCreated()) ?></div>
            </div>        
        </div>    
    </div>    
</div>
<div class="yui-b" style="margin-top: 0.8em">
    <div class="yui-gf">
        <div class="yui-u first boldc pad04em">
            <div>Trading suspended</div>
            <div>Outcome</div>
        </div>
        <div class="yui-u pad04em">
            <div><?php echo date("d/M/Y H:i:s",$market->suspended_utc) ?></div>
            <div><?php echo $market->outcome ?></div>
        </div>                
    </div>
</div>
<div style="font-weight: bold; border-bottom: dashed 1px; padding-top:1em; clear:both">Public belief at settlement</div>
<div class="yui-b" style="margin-top: 0.8em">
    <div class="yui-gf">
        <div class="yui-u first boldc pad04em">
            <div><?php echo $market->option1 ?></div>
            <div><?php echo $market->option2 ?></div>
        </div>
        <div class="yui-u pad04em">
            <div><?php echo round($market->value1*100, 0) ?>%</div>
            <div><?php echo round(($market->value2-0.0000001)*100, 0) ?>%</div>
        </div>                
    </div>
</div>
<table id="settlementReport" class="predictions_table tablesorter" style="margin-top: 0.8em">
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

        // activate opendollar parser
        // activate parser for "ts" attribute
        headers: { 2 : { sorter: 'attrib-ts' },
                   4 : { sorter: 'opendollar' }
                                            }}); 
}); 
</script>