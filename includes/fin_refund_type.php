<?php
function get_fin_refund_type($data1, $data2) {

	global $wpdb;
		
	$start_date = $data1;
	$end_date = $data2;

	$header = ['Project', 'Order Type' ,'Order Source','Payment Type','Count Collected','Collected Dollars','Count Refunded','Refunded Dollars','Refunded Tax Dollars','Refunded Ship Dollars','Refunded Product Dollars','Net Deposit'];
	

	$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'multipay-pay', 'subscription-init', 'subscription-renewal');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal


	$sql = "
		select p.ID, p.post_date, p.post_type, p.post_status, m.meta_value paid_date
		from {$wpdb->posts} p, {$wpdb->postmeta} m
		where p.ID = m.post_id
		and m.meta_key = '_paid_date'
		and p.post_type in ('".implode("','", $order_type_selection)."')
		and p.post_status in ('".implode("','", $order_status_selection)."') 
		and m.meta_value >= '$data1 00:00:00' and m.meta_value <= '$data2 23:59:59'
		order by m.meta_value desc, p.ID desc
	";

	$orders_ids = $wpdb->get_results($sql);

	$orders = [];

	foreach ($orders_ids as $o) {

		$oid = $o->ID;

		$order_type = getOrderType($oid);
		$telemarket = getSource($oid);


		if ($order_type == 'EXCHANGE')
			continue;

		$test_order = get_post_meta($oid,'_test_order',true);
		if ($test_order == 'yes')
			continue;

		if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
			continue;



		$PaymentMethod = ucfirst(get_post_meta($oid,'_payment_method',true));


		$sql = "
			select order_item_id, order_item_name, order_item_type 
			from {$wpdb->prefix}woocommerce_order_items i
			where i.order_id = $oid
			order by i.order_item_type
		";

		$items_ids = $wpdb->get_results($sql);

		$ammount = getOrderTotalValue($oid);
		/*foreach($items_ids as $item) {
			$OrderItemValues = getOrderItemValues($item->order_item_id);
			$ammount = $ammount + $OrderItemValues['items'] + $OrderItemValues['shipping'] + $OrderItemValues['tax'];
		}*/


		$orders[$oid]['cnt']     = 1;
		$orders[$oid]['type']    = $order_type;
		$orders[$oid]['source']  = $telemarket;
		$orders[$oid]['method']  = $PaymentMethod;
		$orders[$oid]['ammount'] = $ammount;
		$orders[$oid]['refund_cnt']      = 0;
		$orders[$oid]['refund_total']    = 0;
		$orders[$oid]['refund_items']    = 0;
		$orders[$oid]['refund_shipping'] = 0;
		$orders[$oid]['refund_tax']      = 0;
		$orders[$oid]['net'] = $ammount;
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

		$oid = $o->post_parent;
		$refund_oid = $o->ID;

		$items = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_id = $refund_oid order by i.order_item_type");
		if (!$items) {
			$refund_oid = $oid;
			$items = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_id = $refund_oid order by i.order_item_type");
		}


		$item_value = 0;
		$item_tax = 0;
		$item_shipping = 0;

		foreach ($items as $item) {

			$itemType = getItemType($item);
			if ($itemType == 'tax')
				continue;

			$refundedOrderItemValues = getOrderItemValues($item, true);

			$item_value = $item_value + abs($refundedOrderItemValues['items']);
			$item_tax = $item_tax + abs($refundedOrderItemValues['tax']);
			$item_shipping = $item_shipping + abs($refundedOrderItemValues['shipping']);

		}


		if (!array_key_exists($oid, $orders)) {

			$refund_order_type = getOrderType($oid);
			$refund_telemarket = getSource($oid);

			$refundPaymentMethod = ucfirst(get_post_meta($oid,'_payment_method',true));

			$orders[$oid]['type']    = 0;
			$orders[$oid]['type']    = $refund_order_type;
			$orders[$oid]['source']  = $refund_telemarket;
			$orders[$oid]['method']  = ucfirst($refundPaymentMethod);
			$orders[$oid]['ammount'] = 0;

		}

		$orders[$oid]['refund_cnt']      = $orders[$oid]['refund_cnt'] + 1;
		$orders[$oid]['refund_total']    = $orders[$oid]['refund_total'] + $item_value + $item_shipping + $item_tax;
		$orders[$oid]['refund_items']    = $orders[$oid]['refund_items'] + $item_value;
		$orders[$oid]['refund_shipping'] = $orders[$oid]['refund_shipping'] + $item_shipping;
		$orders[$oid]['refund_tax']      = $orders[$oid]['refund_tax'] + $item_tax;
		$orders[$oid]['net']		 = $orders[$oid]['ammount'] - $orders[$oid]['refund_total'];
	}
	

	$ProjectNumber = get_option('options_legacy_scs_client_id');
	
	$result_orders = [];
	foreach ($orders as $order) {
		if (empty($result_orders[$order['type']][$order['source']][$order['method']]['cnt'])) {
			$result_orders[$order['type']][$order['source']][$order['method']]['cnt']             = 0;
			$result_orders[$order['type']][$order['source']][$order['method']]['ammount']         = 0;
			$result_orders[$order['type']][$order['source']][$order['method']]['refund_cnt']      = 0;
			$result_orders[$order['type']][$order['source']][$order['method']]['refund_total']    = 0;
			$result_orders[$order['type']][$order['source']][$order['method']]['refund_items']    = 0;
			$result_orders[$order['type']][$order['source']][$order['method']]['refund_shipping'] = 0;
			$result_orders[$order['type']][$order['source']][$order['method']]['refund_tax']      = 0;
		}

		$result_orders[$order['type']][$order['source']][$order['method']]['cnt'] = $result_orders[$order['type']][$order['source']][$order['method']]['cnt'] + $order['cnt'];
		$result_orders[$order['type']][$order['source']][$order['method']]['ammount'] = $result_orders[$order['type']][$order['source']][$order['method']]['ammount'] + $order['ammount'];
		$result_orders[$order['type']][$order['source']][$order['method']]['refund_cnt'] = $result_orders[$order['type']][$order['source']][$order['method']]['refund_cnt'] + $order['refund_cnt'];
		$result_orders[$order['type']][$order['source']][$order['method']]['refund_total'] = $result_orders[$order['type']][$order['source']][$order['method']]['refund_total'] + $order['refund_total'];
		$result_orders[$order['type']][$order['source']][$order['method']]['refund_items'] = $result_orders[$order['type']][$order['source']][$order['method']]['refund_items'] + $order['refund_items'];
		$result_orders[$order['type']][$order['source']][$order['method']]['refund_shipping'] = $result_orders[$order['type']][$order['source']][$order['method']]['refund_shipping'] + $order['refund_shipping'];
		$result_orders[$order['type']][$order['source']][$order['method']]['refund_tax'] = $result_orders[$order['type']][$order['source']][$order['method']]['refund_tax'] + $order['refund_tax'];
		$result_orders[$order['type']][$order['source']][$order['method']]['net'] = $result_orders[$order['type']][$order['source']][$order['method']]['ammount'] - $result_orders[$order['type']][$order['source']][$order['method']]['refund_total'];
	}


	foreach ($result_orders as $type=>$result1) {
		foreach ($result1 as $source=>$result_method) {
			foreach ($result_method as $method=>$value) {
				$rows[] = [$ProjectNumber, $type, $source, $method, $value['cnt'], $value['ammount'], $value['refund_cnt'], $value['refund_total'], $value['refund_tax'], $value['refund_shipping'], $value['refund_items'], $value['net']];
			}
		}
	}


	$total = ['', '', '', '', 1, 1, 1, 1, 1, 1, 1, 1];
	
	$units = ['',$order_source,'','', '','$','','$','$','$','$','$'];



	// Array data
	$result['all']['array']['header']	 = $header;
	$result['all']['array']['unit']		 = $units;
	$result['all']['array']['total']	 = $total;
	$result['all']['array']['rows']		 = $rows;
	$result['all']['array']['sheetname']	 = 'Worksheet';
	$result['all']['array']['title']	 = 'Payments Deposited & Refunded by Order Type';
		

	return json_encode($result);

}

?>
