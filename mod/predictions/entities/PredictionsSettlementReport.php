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
            // HACK
            $this->title = "Settlement report";
            $this->report = 
            array(array( option_name => 'Yes', tr_url => ' ', owner_name => ' ', owner_url => ' ', tr_created => 1283286938, price => 0.45, win => 100 ));
        }
        
        public function get($name) {
        	if ("report" == $name) {
        	    $serialized_arr = parent::get($name);
        	    return unserialize($serialized_arr);
        	}
        	else
                return parent::get($name);
        }
}
 
function committee_init() {
   register_entity_type('object', PredictionsSettlementReport::SUBTYPE );
   // This operation only affects the db on the first call for this subtype
   // If you change the class name, you'll have to hand-edit the db
   add_subtype('object', PredictionsSettlementReport::SUBTYPE, 'PredictionsSettlementReport');
}
 
register_elgg_event_handler('init', 'system', 'committee_init');
?>