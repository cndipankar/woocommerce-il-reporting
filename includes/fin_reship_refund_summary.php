<?php

function get_fin_reship_refund_summary($data1, $data2) {

	global $wpdb;
		
	$start_date = $data1;
	$end_date = $data2;

	$rows=[];
	$ProjectNumber = get_option('options_legacy_scs_client_id');

	$header = ['Project', 'Ship Week', 'SKU', 'Description', 'Ship Count', 'Refund Count', 'Refund %', 'Return Count', 'Return %', 'Reship Count', 'Reship %'];
	
	$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'multipay-pay', 'subscription-init', 'subscription-renewal', 'backorder');	// All values: onepay , multipay-init , multipay-pay, subscription-init , subscription-subscription , subscription-renewal , backorder

	
	
	$sql ="
		select p.ID, p.post_date, p.post_status 
		from {$wpdb->posts} p
		left join {$wpdb->postmeta} m on p.ID = m.post_id and m.meta_key = '_paid_date'
		where 1=1
		and p.post_type in ('".implode("','", $order_type_selection)."')
		and p.post_status in ('".implode("','", $order_status_selection)."')
		and p.post_date >= '$data1 00:00:00' and p.post_date <= DATE_ADD('$data2 23:59:59', INTERVAL +30 DAY)
		order by m.meta_value desc
	";

	//echo $sql;die();
	$orders_ids = $wpdb->get_results($sql);

	$values = array();

	foreach($orders_ids as $o) {

		$oid = $o->ID;

		$shipInfo = getShipment($oid);

		$test_order = get_post_meta($oid,'_test_order',true);
		if ($test_order == 'yes')
			continue;

		if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
			continue;


		if (empty($shipInfo['ship_date'])) {
			continue;
		}

		$ship_date = date("Y-m-d", strtotime($shipInfo['ship_date']));
		if ($ship_date > "$data2 23:59:59" || $ship_date < "$data1 00:00:00") {
			continue;
		}



		$post_date = $o->post_date;

		$dt = explode("-", $post_date);
		$mktime = mktime(0, 0, 0, $dt[1], $dt[2], $dt[0]);
		$week = (int)date('W', $mktime);
		$year = (int)date('Y', $mktime);

		$sql = "
			select  i.order_item_id, i.order_item_name, i.order_item_type
			from {$wpdb->prefix}woocommerce_order_items i, {$wpdb->posts} p
			where p.id = i.order_id
			and i.order_id = $oid
			and p.post_status in ('".implode("','", $order_status_selection)."') 
			and i.order_item_type in ('line_item')
			order by i.order_item_type, i.order_item_id
		";

		$items_ids = $wpdb->get_results($sql);

		foreach($items_ids as $item) {
			if (getItemType($item->order_item_id) != 'shipping') {

				$sku = getItemSKU($item->order_item_id);
				$name = getItemDescription($item->order_item_id);
				$qty = getItemQty($item->order_item_id);

				if (empty($values[$week . ' ' . $year][$sku.'|'.$name])) {
					$values[$week . ' ' . $year][$sku.'|'.$name]['shipped'] = 0;
					$values[$week . ' ' . $year][$sku.'|'.$name]['refund'] = 0;
					$values[$week . ' ' . $year][$sku.'|'.$name]['return'] = 0;
					$values[$week . ' ' . $year][$sku.'|'.$name]['reship'] = 0;
				}
				$values[$week . ' ' . $year][$sku.'|'.$name]['shipped'] = $values[$week . ' ' . $year][$sku]['shipped'] + $qty;

			}				
		}


	}



	$sql = "
		select ID, post_date, post_type, post_parent, post_date 
		from {$wpdb->posts} 
		where post_date >= '$data1 00:00:00' and post_date <= '$data2 23:59:59' 
		and post_status in ('".implode("','", $order_status_selection)."')
		and post_type='shop_order_refund'
	";

	$orders_ids = $wpdb->get_results($sql);

	foreach ($orders_ids as $o) {

		if ( empty(get_post_meta($o->ID,'_refund_amount',true)) || get_post_meta($o->ID,'_refund_amount',true) == 0.00  )
			continue;

		$refund_oid = $o->ID;
		$oid = $o->post_parent;

		$post_date = $o->post_date;

		$dt = explode("-", $post_date);
		$mktime = mktime(0, 0, 0, $dt[1], $dt[2], $dt[0]);
		$week = (int)date('W', $mktime);
		$year = (int)date('Y', $mktime);


		$items = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_id = $refund_oid order by i.order_item_type");
		if (!$items) {
			$refund_oid = $oid;
			$items = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_id = $refund_oid order by i.order_item_type");
		}

		foreach ($items as $item) {

			$itemType = getItemType($item);
			if ($itemType == 'tax' || $itemType == 'shipping')
				continue;

			$sku = getItemSKU($item);
			$name = getItemDescription($item);
			$qty = abs(getItemQty($item));

			$ret_qty = 0;
			$returnItems = getReturnItems($oid);
			foreach ($returnItems as $returnItem) {
				if ($returnItem['sku'] == $sku) {
					$ret_qty = $returnItem['qty'];
				}
			}


			if (empty($values[$week . ' ' . $year][$sku.'|'.$name])) {
				$values[$week . ' ' . $year][$sku.'|'.$name]['shipped'] = 0;
				$values[$week . ' ' . $year][$sku.'|'.$name]['refund'] = 0;
				$values[$week . ' ' . $year][$sku.'|'.$name]['return'] = 0;
				$values[$week . ' ' . $year][$sku.'|'.$name]['reship'] = 0;
			}
			$values[$week . ' ' . $year][$sku.'|'.$name]['refund'] = $values[$week . ' ' . $year][$sku]['refund'] + $qty;
			$values[$week . ' ' . $year][$sku.'|'.$name]['return'] = $values[$week . ' ' . $year][$sku]['return'] + $ret_qty;



		}



	}


	foreach ($values as $week => $val) {
		foreach ($val as $sku => $d) {
			$s = explode('|', $sku);
			if ( (int)$d['shipped'] > 0 ) {
				$refund_pr = $d['refund'] / $d['shipped'] * 100;
				$return_pr = $d['return'] / $d['shipped'] * 100;
				$reship_pr = $d['reship'] / $d['shipped'] * 100;
			} else {
				$refund_pr = 0;
				$return_pr = 0;
				$reship_pr = 0;
			}
			$rows[] = [$ProjectNumber, $week, $s[0], $s[1], $d['shipped'], $d['refund'],  $refund_pr, $d['return'], $return_pr, $d['reship'], $reship_pr];

		}
	}



	
	
	
	$units = ['','','','','','','%','','%','','%'];
	$total = ['','','','','','','','','','',''];
	
		


		// Array data
	$result['all']['array']['header']	   = $header;
	$result['all']['array']['unit']		 = $units;
	$result['all']['array']['total']		= $total;
	$result['all']['array']['rows']		 = $rows;
	$result['all']['array']['sheetname']	= 'Worksheet';
	$result['all']['array']['title']		= 'Reship and Refund Summary by Inventory SKU and Ship Week';
		

	return json_encode($result);


}

?>
