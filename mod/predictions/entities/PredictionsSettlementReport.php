<?php

class PredictionsSettlementReport extends ElggObject
{
    	const SUBTYPE = 'settlement_report';
        
    	protected function initialise_attributes() {
            parent::initialise_attributes();
            $this->attributes['subtype'] = self::SUBTYPE;
        }
 
        public function __construct($guid = null) {
            parent::__construct($guid);
        }
        
        /*
         * Report is stored as a serialized nested php array
         */
        public function get($name) {
        	if ("report" == $name) {
        		$report = unserialize(parent::get($name));
        		$i = 1;
        		while ($ser_tr_chunk = parent::get($name.$i)) {
        			$i=$i+1;
        			$tr_chunk = unserialize($ser_tr_chunk);
        			if (is_array($tr_chunk)) {
        				$report['transactions'] = array_merge($report['transactions'],$tr_chunk);
        			}
        		}
        	    return $report;
        	}
        	else
                return parent::get($name);
        }
        
        public function set($name, $value) {
        	if (("report" == $name) && is_array($value)) {
        		
        		$full_report = $value;
        		$transactions = $full_report['transactions'];
        		$tr_chunks = array_chunk($transactions, 50);
        		$full_report['transactions'] = $tr_chunks[0];
        		$value = serialize($full_report);
        		foreach ($tr_chunks as $i => $tr_chunk) {
        			if ($i == 0) continue;
        			parent::set($name.$i, serialize($tr_chunk));
        		}
        	} 
        	
        	parent::set($name, $value);      
        }
        
        
        public static function create($market_guid, $report_content) {
        	$report = new PredictionsSettlementReport();
        	$report->title = "Settlement report";
        	$report->access_id = ACCESS_LOGGED_IN;
        	$report->tags = array("settlement");
        	
            $report->market = $market_guid;
            $report->report = $report_content;

            return $report;
        }
}
 
function settlementreport_init() {
   register_entity_type('object', PredictionsSettlementReport::SUBTYPE );
   // This operation only affects the db on the first call for this subtype
   // If you change the class name, you'll have to hand-edit the db
   add_subtype('object', PredictionsSettlementReport::SUBTYPE, 'PredictionsSettlementReport');
}
 
register_elgg_event_handler('init', 'system', 'settlementreport_init');
?>