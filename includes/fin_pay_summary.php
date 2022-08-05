<?php

    function get_fin_pay_summary($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

	//$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request');
	$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'multipay-pay', 'subscription-init', 'subscription-renewal');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal

        // get the table rows
        $header = ['Project', 'Order Type', 'Order Source','Payment Type', 'Product', 'ShipFee', 'Tax', 'Payments'];
        $rows = array();

	$ProjectNumber = get_option('options_legacy_scs_client_id');

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

	$order_current = 0;

	foreach ($orders_ids as $o) {

		$oid = $o->ID;


		$test_order = get_post_meta($oid,'_test_order',true);
		if ($test_order == 'yes') 
			continue;

		if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) ) 
			continue;


		$Source = getOrderType($oid);
		$telemarket = getSource($oid);

		if ($Source == 'EXCHANGE')
			continue;


		$PaymentMethod = ucfirst(get_post_meta($oid,'_payment_method',true));

		if ($order_current != $oid) {
			$order_current = $oid;
			if (empty($values[$Source][$telemarket][$PaymentMethod]['count'])) {
				$values[$Source][$telemarket][$PaymentMethod]['count'] = 1;
			} else {
				$values[$Source][$telemarket][$PaymentMethod]['count'] = $values[$Source][$telemarket][$PaymentMethod]['count'] + 1;
			}
		}


		$sql = "
			select  order_item_id, order_item_name, order_item_type 
			from {$wpdb->prefix}woocommerce_order_items i
			where i.order_id = $oid
			order by i.order_item_type
		";

		$items = $wpdb->get_results($sql);
		foreach($items as $item) {

			$Qty_Ordered =  getItemQty($item->order_item_id);

			$OrderItemValues = getOrderItemValues($item->order_item_id, 1);

			if (empty($values[$Source][$telemarket][$PaymentMethod]['item'])) {
				$values[$Source][$telemarket][$PaymentMethod]['item'] = $OrderItemValues['items'];
			} else {
				$values[$Source][$telemarket][$PaymentMethod]['item'] = $values[$Source][$telemarket][$PaymentMethod]['item'] + $OrderItemValues['items'];
			}


			if (empty($values[$Source][$telemarket][$PaymentMethod]['sp'])) {
				$values[$Source][$telemarket][$PaymentMethod]['sp'] = $OrderItemValues['shipping'];
			} else {
				$values[$Source][$telemarket][$PaymentMethod]['sp'] = $values[$Source][$telemarket][$PaymentMethod]['sp'] + $OrderItemValues['shipping'];
			}


			if (empty($values[$Source][$telemarket][$PaymentMethod]['tax'])) {
				$values[$Source][$telemarket][$PaymentMethod]['tax'] = $OrderItemValues['tax'];
			} else {
				$values[$Source][$telemarket][$PaymentMethod]['tax'] = $values[$Source][$telemarket][$PaymentMethod]['tax'] + $OrderItemValues['tax'];
			}


			if (empty($values[$Source][$telemarket][$PaymentMethod]['qty'])) {
				$values[$Source][$telemarket][$PaymentMethod]['qty'] = $qty;
			} else {
				$values[$Source][$telemarket][$PaymentMethod]['qty'] = $values[$Source][$telemarket][$PaymentMethod]['qty'] + $Qty_Ordered;
			}


		}

		
	}


	foreach ($values as $key_Source=>$data1) {
		foreach ($data1 as $key_telemarket=>$data) {
			foreach ($data as $key_PaymentMethod=>$val) {
				$rows[]=[$ProjectNumber, $key_Source, $key_telemarket, $key_PaymentMethod, (float)$val['item'], (float)$val['sp'], (float)$val['tax'], (int)$val['count']];
			}
		}
	}
	$total = array('', '', '', '', 1, 1, 1, 1);
        $units = array('','','', '','$','$','$','');



        // Array data
        $result['all']['array']['header']       = $header;
        $result['all']['array']['unit']         = $units;
        $result['all']['array']['total']        = $total;
        $result['all']['array']['rows']         = $rows;
        $result['all']['array']['sheetname']    = 'Worksheet';
        $result['all']['array']['title']        = 'Payments Deposited Summary';
        
        return json_encode($result);

    }

?>
