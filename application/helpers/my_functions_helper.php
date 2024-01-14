<?php

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_filter('before_create_proposal', '_format_data_sales_incoterms_feature');
hooks()->add_filter('before_proposal_updated', '_format_data_sales_incoterms_feature');

// BOF VK, Handle proposal incoterms data
// VK Mod: Add

function _format_data_sales_incoterms_feature($data)
{	
	$incoterms = array();

	if (isset($data['data']['base_currency'])) {
        unset($data['data']['base_currency']);
    }

    if (isset($data['data']['exchange_currency'])) {
    	$incoterms['exchange_rate'] = $data['data']['exchange_currency'];
        unset($data['data']['exchange_currency']);        
    }

    if (isset($data['data']['incoterms'])) {
        unset($data['data']['incoterms']);
    }

    if (isset($data['data']['fob_port'])) {
    	$incoterms['fob_port'] = $data['data']['fob_port'];
        unset($data['data']['fob_port']);
    }

    if (isset($data['data']['cfr_port'])) {
    	$incoterms['cfr_port'] = $data['data']['cfr_port'];
        unset($data['data']['cfr_port']);
    }

    if (isset($data['data']['container_type'])) {
    	$incoterms['container_type'] = $data['data']['container_type'];
        unset($data['data']['container_type']);
    }

    $data['incoterms'] = $incoterms;

    return $data;
}

function handle_proposal_incoterms_save($proposalid, $data)
{	
	$CI = &get_instance();

	$CI->db->where('proposal_id', $proposalid);
	$CI->db->delete('proposal_ports');

	if (is_array($data) && count($data)) {
        $CI->db->insert('proposal_ports', 
        	array(
        		'proposal_id' => $proposalid,
        		'fob_port' => (isset($data['fob_port']) && is_array($data['fob_port']) ? json_encode($data['fob_port']) : NULL),
        		'cfr_port' => (isset($data['cfr_port']) && is_array($data['cfr_port']) ? json_encode($data['cfr_port']) : NULL),
        		'container_type' => (isset($data['container_type']) && is_array($data['container_type']) ? json_encode($data['container_type']) : NULL),
        		'exchange_rate' => (isset($data['exchange_rate']) ? $data['exchange_rate'] : 0) 
        	)
    	);
    }
}

function get_invoice_incoterms_data($invoiceid)
{	
	$CI = &get_instance();

	$CI->db->where('invoice_id', $invoiceid);
	$row_array = $CI->db->get('invoice_ports')->row_array();

	$row_data = array();

	if ($row_array) {
        $row_data = array(
    		'fob_port' => (!empty($row_array['fob_port']) ? json_decode($row_array['fob_port'], true) : array()),
    		'cfr_port' => (!empty($row_array['cfr_port']) ? json_decode($row_array['cfr_port'], true) : array()),
    		'container_type' => (!empty($row_array['container_type']) ? json_decode($row_array['container_type'], true) : array()),
    		'exchange_rate' => $row_array['exchange_rate'],
    	);

    	$row_data['ports'] = array();

    	$freights = array();

    	if ($row_data['fob_port']) {
    		$freights = $row_data['fob_port'];
    	}

    	if ($row_data['cfr_port']) {
    		$freights = array_values(array_unique(array_merge($freights, $row_data['cfr_port'])));
    	}

    	foreach ($freights as $freight) {
	    	$CI->db->where('id', $freight);
	        $freight_row = $CI->db->get('freights')->row_array();

	        if ($freight_row) {
		        $row_data['ports'][] = array(
		        	'id' => $freight,
		        	'name' => $freight_row['port'],
		        	'fcl_20' => $freight_row['fcl_20'],
		        	'fcl_40' => $freight_row['fcl_40'],
		        	'air' => $freight_row['air']
		        );
		    }
	    }
    }

    return $row_data;
}

function get_proposal_incoterms_data($proposalid)
{	
	$CI = &get_instance();

	$CI->db->where('proposal_id', $proposalid);
	$row_array = $CI->db->get('proposal_ports')->row_array();

	$row_data = array();

	if ($row_array) {
        $row_data = array(
    		'fob_port' => (!empty($row_array['fob_port']) ? json_decode($row_array['fob_port'], true) : array()),
    		'cfr_port' => (!empty($row_array['cfr_port']) ? json_decode($row_array['cfr_port'], true) : array()),
    		'container_type' => (!empty($row_array['container_type']) ? json_decode($row_array['container_type'], true) : array()),
    		'exchange_rate' => $row_array['exchange_rate'],
    	);

    	$row_data['ports'] = array();

    	$freights = array();

    	if ($row_data['fob_port']) {
    		$freights = $row_data['fob_port'];
    	}

    	if ($row_data['cfr_port']) {
    		$freights = array_values(array_unique(array_merge($freights, $row_data['cfr_port'])));
    	}

    	foreach ($freights as $freight) {
	    	$CI->db->where('id', $freight);
	        $freight_row = $CI->db->get('freights')->row_array();

	        if ($freight_row) {
		        $row_data['ports'][] = array(
		        	'id' => $freight,
		        	'name' => $freight_row['port'],
		        	'fcl_20' => $freight_row['fcl_20'],
		        	'fcl_40' => $freight_row['fcl_40'],
		        	'air' => $freight_row['air']
		        );
		    }
	    }
    }

    return $row_data;
}

function delete_itemable_incoterms_data($proposalid)
{	
	$CI = &get_instance();

	$CI->db->where('proposal_id', $proposalid);
	$CI->db->delete('itemable_incoterms');
}
function genInvoice_port($invoice_port){
	
	$portData = [
		'invoiceid' => 0,
		'fob_port' => '',
		'cfr_port' => '',
		'container_type' => '',
		'rate_port' => 0,
		'exchange_rate' => 0.00
	];
	$isRate = 0;
	$fobPort = array();
	$cfrPort = array();
	$containerType = array();
	foreach($invoice_port as $portItem){
		if($portItem == 'rate'){
			$isRate = 1;
		}else{
			$portArrs = explode("-", $portItem);
			if($portArrs[0] == "fob"){
				if(!in_array($portArrs[1], $fobPort)){
					array_push($fobPort, $portArrs[1]);
				}
			}
			if($portArrs[0] == "cfr"){
				if(!in_array($portArrs[1], $cfrPort)){
					array_push($cfrPort, $portArrs[1]);
				}
			}
			$containerStr = str_replace($portArrs[0].'-'.$portArrs[1].'-',"",$portItem);
			switch($containerStr){
				case 'air':
					if(!in_array('AIR', $containerType)){
						array_push($containerType, 'AIR');
					}
					break;
				case '20-fcl':
					if(!in_array('20 FCL', $containerType)){
						array_push($containerType, '20 FCL');
					}
					break;
				case '40-fcl':
					if(!in_array('40 FCL', $containerType)){
						array_push($containerType, '40 FCL');
					}
					break;
			}
		}
	};
	$portData['fob_port'] = $fobPort;
	$portData['cfr_port'] = $cfrPort;
	$portData['container_type'] = $containerType;
	$portData['rate_port'] = $isRate;

	return $portData;
}
function handle_itemable_incoterms_update($proposalid, $invoiceid, $itemid, $item_incoterms, $incoterms){
	$freights = array();

	if (isset($incoterms['fob_port']) && is_array($incoterms['fob_port'])) {
		$freights = $incoterms['fob_port'];
	}

	if (isset($incoterms['cfr_port']) && is_array($incoterms['cfr_port'])) {
		$freights = array_values(array_unique(array_merge($freights, $incoterms['cfr_port'])));
	}

	$CI = &get_instance();
	$CI->db->insert(db_prefix().'invoice_ports', [
		'invoice_id' => $invoiceid,
		'fob_port' => json_encode($incoterms['fob_port']),
		'cfr_port' => json_encode($incoterms['cfr_port']),
		'container_type' => json_encode($incoterms['container_type']),
		'rate_port' => $incoterms['rate_port'],
		'exchange_rate' => $incoterms['exchange_rate']
	]);
	if($freights){
		$itemable_incoterms_data = array();
		foreach($freights as $freight) {
			$data = isset($item_incoterms[$freight])? $item_incoterms[$freight] : array();
			if($freight > 0 && $data){
				$CI->db->where('id', $freight);
        		$freight_row = $CI->db->get('freights')->row_array();
				if ($freight_row) {
		    	    $itemable_incoterms_data[] = array(
	    	    		'invoiceid' => $invoiceid,
						'proposalid' => $proposalid,
	    	    		'item_id' => $itemid,
	    	    		'freight_id' => $freight,
	    	    		'freight_port' => $freight_row['port'],
	    	    		'qty_fob_fcl_20' => $data['rate_fob_fcl_20_qty'],
	    	    		'qty_fob_fcl_40' => $data['rate_fob_fcl_40_qty'],
	    	    		'qty_fob_air' => $data['rate_fob_air_qty'],
	    	    		'qty_cfr_fcl_20' => $data['rate_cfr_fcl_20_qty'],
	    	    		'qty_cfr_fcl_40' => $data['rate_cfr_fcl_40_qty'],
	    	    		'qty_cfr_air' => $data['rate_cfr_air_qty'],
	    	    	);
					$CI->db->where('proposal_id', $proposalid)->where('item_id', $itemid)->where('freight_id', $freight);
					$CI->db->update(db_prefix().'itemable_incoterms', [
						'qty_fob_fcl_20' => $data['rate_fob_fcl_20_qty'],
	    	    		'qty_fob_fcl_40' => $data['rate_fob_fcl_40_qty'],
	    	    		'qty_fob_air' => $data['rate_fob_air_qty'],
	    	    		'qty_cfr_fcl_20' => $data['rate_cfr_fcl_20_qty'],
	    	    		'qty_cfr_fcl_40' => $data['rate_cfr_fcl_40_qty'],
	    	    		'qty_cfr_air' => $data['rate_cfr_air_qty'],	
					]);
		    	}
			}
		}
		// print_r($itemable_incoterms_data);
		// die;
	}

	// print_r($item_incoterms);
	// die;
}

function handle_itemable_incoterms_save($proposalid, $itemid, $incoterms, $item_incoterms)
{	
	$freights = array();

	if (isset($incoterms['fob_port']) && is_array($incoterms['fob_port'])) {
		$freights = $incoterms['fob_port'];
	}

	if (isset($incoterms['cfr_port']) && is_array($incoterms['cfr_port'])) {
		$freights = array_values(array_unique(array_merge($freights, $incoterms['cfr_port'])));
	}

	$CI = &get_instance();

	if ($freights) {
		$itemable_incoterms_data = array();
        foreach ($freights as $freight) {
        	$data = isset($item_incoterms[$freight]) ? $item_incoterms[$freight] : array();  	
        	if ($freight > 0 && $data) {
        		$CI->db->where('id', $freight);
        		$freight_row = $CI->db->get('freights')->row_array();

        		if ($freight_row) {
		    	    $itemable_incoterms_data[] = array(
	    	    		'proposal_id' => $proposalid,
	    	    		'item_id' => $itemid,
	    	    		'freight_id' => $freight,
	    	    		'freight_port' => $freight_row['port'],
	    	    		'rate_fob_fcl_20' => $data['rate_fob_fcl_20'],
	    	    		'rate_fob_fcl_40' => $data['rate_fob_fcl_40'],
	    	    		'rate_fob_air' => $data['rate_fob_air'],
	    	    		'rate_cfr_fcl_20' => $data['rate_cfr_fcl_20'],
	    	    		'rate_cfr_fcl_40' => $data['rate_cfr_fcl_40'],
	    	    		'rate_cfr_air' => $data['rate_cfr_air'],
	    	    	);
		    	}
	    	}	
        }

        if ($itemable_incoterms_data) {
        	$CI->db->insert_batch('itemable_incoterms', $itemable_incoterms_data);
        }
    }
}

function get_itemable_incoterms_data($proposalid)
{
	$CI = &get_instance();

	$CI->db->where('proposal_id', $proposalid);
	$result_array = $CI->db->get('itemable_incoterms')->result_array();

    return ($result_array) ? $result_array : array();
}