<?php

function get_fin_ilm_shipped($data1, $data2) {

	global $wpdb;
		
	$start_date = $data1;
	$end_date = $data2;


	$order_status_selection = array('wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'subscription-init', 'subscription-renewal');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal


	// get the table rows
	
	//$header = ['Project', 'Type of Order', 'Continuity Plan', 'Continuity Description', 'Shipped Document Count']; 
	$header = array(
			'Project', 
			'Date', 
			'Order Type', 
			'Order Source', 
			'Script', 
			'Shipped Order Count'
		); 
	
	$ProjectNumber = get_option('options_legacy_scs_client_id');

	$sql = "
		select p.ID, p.post_date, p.post_status, p.post_type 
		from {$wpdb->posts} p
		left join {$wpdb->postmeta} m on p.ID = m.post_id and m.meta_key = '_paid_date'
		where 1=1
		and p.post_type in ('".implode("','", $order_type_selection)."')
		and p.post_status in ('".implode("','", $order_status_selection)."')
		and p.post_date >= '$data1 00:00:00' and p.post_date <= DATE_ADD('$data2 23:59:59', INTERVAL +30 DAY)
		order by m.meta_value desc
	";
	//echo $sql; die();

	$orders = $wpdb->get_results($sql);

	$orders_by_sub_code = [];

	foreach ($orders as $o) {

		$oid = $o->ID;

		$status = str_replace("wc-", "", $o->post_status);

		$test_order = get_post_meta($oid,'_test_order',true);
		if ($test_order == 'yes')
			continue;

		if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
			continue;



		$shipInfo = getShipment($oid);

		if ( empty($shipInfo['ship_date']) && $status != 'shipped' ) {
			continue;
		}

		$ship_date = date("Y-m-d", strtotime($shipInfo['ship_date']));
		if ($ship_date > "$data2 23:59:59" || $ship_date < "$data1 00:00:00") {
			continue;
		}

		$script = getScript($oid);
		$orderType = getOrderType($oid);
		$telemarket = getSource($oid);


		if ($orderType != 'CNT') {

			if (empty($orders_by_sub_code[$ship_date]['Starts'][$telemarket][$script]))
				$orders_by_sub_code[$ship_date]['Starts'][$telemarket][$script] = 1;
			else
				$orders_by_sub_code[$ship_date]['Starts'][$telemarket][$script] ++;

		} else if ($orderType == 'CNT') {

			if (empty($orders_by_sub_code[$ship_date]['Continuities'][$telemarket][$script]))
				$orders_by_sub_code[$ship_date]['Continuities'][$telemarket][$script] = 1;
			else
				$orders_by_sub_code[$ship_date]['Continuities'][$telemarket][$script] ++;
		 }

	}

	ksort($orders_by_sub_code, SORT_STRING);

	foreach($orders_by_sub_code as $ship_date=>$data1){
		foreach($data1 as $type=>$data2){
			foreach($data2 as $source=>$scripts){
				foreach($scripts as $script=>$count){
					$rows[] = array(
						$ProjectNumber, 
						substr($ship_date, 5, 2) . '/' . substr($ship_date, 8, 2) . '/' . substr($ship_date, 0, 4), 
						$type, 
						$source, 
						$script, 
						$count
					);
				}
			}
		}
	}
	
	
	$total = ['', '', '', '', 1];


		// Array data
	$result['all']['array']['header']	= $header;
	$result['all']['array']['unit']		= $units;
	$result['all']['array']['total']	= $total;
	$result['all']['array']['rows']		= $rows;
	$result['all']['array']['sheetname']    = 'Worksheet';
	$result['all']['array']['title']	= 'ILM - Documents Shipped by Ship Date ';
		

	return json_encode($result);

}

?>
