<?php


function getFlateRateShipping() {
	global $wpdb;

	$sql = "select p.id from {$wpdb->posts} p, {$wpdb->postmeta} m where p.ID = m.post_id and p.post_type = 'pi_shipping_method' and m.meta_key = 'pi_priority' order by m.meta_value";

	$shipping_ids = $wpdb->get_results($sql);

	foreach($shipping_ids as $s) { 
		$shipping_id = $s->ID;
		$status = get_post_meta($shipping_id, 'pi_status', true);
		if ( $status == 'on' ) {
			$cost = get_post_meta($shipping_id, 'pi_cost', true);
			$metabox = get_post_meta($shipping_id, 'pi_metabox', true);
			$coupon = get_post_meta($shipping_id, 'pi_free_when_free_shipping_coupon', true);
			foreach ($metabox as $data) {
			}
		}
	}


}


function idealliving_reports_get_orders( $controllers ) {
	$controllers['wc/v3']['custom-reports-get-orders'] = 'WC_REST_Custom_Reports_Controller';
	return $controllers;
}



function getInstalmentItemValues ($item_id = 0) {

	global $wpdb;

	$return['items'] = 0;
	$return['shipping'] = 0;
	$return['tax'] = 0;


	// Products ID

	$pid = "";

	$sql = "
		select meta_value 
		from {$wpdb->prefix}woocommerce_order_itemmeta 
		where order_item_id in (
			select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_id = $item_id
		) 
		and meta_key in ('_product_id', '_variation_id') 
		and meta_value > 0
	";

	$product_ids = $wpdb->get_results($sql);

	foreach($product_ids as $p) { 

		if ( !empty($pid) )
			$pid .= ", ";

		$pid .= $p->meta_value;

	}


	// Order ID
	$oid = $wpdb->get_var("select order_id from wp_woocommerce_order_items where order_item_id = $item_id");


	$orderInstalments = getOrderInstalments($oid);
	foreach ($orderInstalments as $orderInstalment_id) {

		$sql = "
			select distinct i.order_item_id 
			from {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m 
			where i.order_item_id = m.order_item_id 
			and i.order_id = $orderInstalment_id 
			and m.meta_key in ('_product_id', '_variation_id') 
			and m.meta_value in ($pid)
		";

		$product_ids = $wpdb->get_results($sql);

		foreach($product_ids as $p) { 

			$item_id = $p->order_item_id;
			$orderItemValues = getOrderItemValues($item_id, 1);

			$return['items'] = $return['items'] + $orderItemValues['items'];
			$return['shipping'] = $return['shipping'] + $orderItemValues['shipping'];
			$return['tax'] = $return['tax'] + $orderItemValues['tax'];
		}


	}

	return $return;

}


function getOrderInstalments ($oid = 0) {

	global $wpdb;

	$return = array();

	$sql = "
		select p.ID
		from 
		{$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta im, 
		{$wpdb->posts} parent, {$wpdb->prefix}woocommerce_order_items parent_i, {$wpdb->prefix}woocommerce_order_itemmeta parent_im  
		where p.ID = i.order_id 
		and i.order_item_id = im.order_item_id 
		and p.post_parent = parent.ID 
		and parent.ID = parent_i.order_id 
		and parent_i.order_item_id = parent_im.order_item_id 
		and parent_im.meta_key = '_is_deposit' 
		and parent_im.meta_value = 'yes' 
		and parent.post_type = 'shop_order' 
		and parent.ID = $oid 
		and im.meta_key = '_original_order_id' 
		and im.meta_value = parent.ID 
		and p.post_type = 'shop_order'
	";


	$orders_ids = $wpdb->get_results($sql);

	foreach($orders_ids as $o) { 
		$return[] = $o->ID;
	}

	return $return;

}



function getOrderInstalmentValues($oid = 0) {

	global $wpdb;

	$return['items'] = 0;
	$return['shipping'] = 0;
	$return['tax'] = 0;

	$sql = "
		select p.ID
		from 
		{$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta im, 
		{$wpdb->posts} parent, {$wpdb->prefix}woocommerce_order_items parent_i, {$wpdb->prefix}woocommerce_order_itemmeta parent_im  
		where p.ID = i.order_id 
		and i.order_item_id = im.order_item_id 
		and p.post_parent = parent.ID 
		and parent.ID = parent_i.order_id 
		and parent_i.order_item_id = parent_im.order_item_id 
		and parent_im.meta_key = '_is_deposit' 
		and parent_im.meta_value = 'yes' 
		and parent.post_type = 'shop_order' 
		and parent.ID = $oid 
		and im.meta_key = '_original_order_id' 
		and im.meta_value = parent.ID 
		and p.post_type = 'shop_order'
	";


	$orders_ids = $wpdb->get_results($sql);

	foreach($orders_ids as $o) { 
		$orderValue = getOrderValues($o->ID);
		$return['items'] = $return['items'] + $orderValue['items'];
		$return['shipping'] = $return['shipping'] + $orderValue['shipping'];
		$return['tax'] = $return['tax'] + $orderValue['tax'];
	}

	return $return;

}




function getOrderBackorders ($oid = 0) {

	global $wpdb;

	$return = array();

	$sql = "
		SELECT b.ID
		FROM {$wpdb->posts} p, {$wpdb->posts} b, {$wpdb->postmeta} bm
		WHERE p.ID = b.post_parent
		AND b.ID = bm.post_id
		AND bm.meta_key='_wc_backorder_email_sent' 
		AND p.ID=$oid
		";

	$orders_ids = $wpdb->get_results($sql);

	foreach($orders_ids as $o) { 
		$return[] = $o->ID;
	}

	return $return;

}


function getOrderBackordersValue ($oid = 0) {

	global $wpdb;

	$return['items'] = 0;
	$return['shipping'] = 0;
	$return['tax'] = 0;
	$return['total'] = 0;

	$sql = "
		SELECT b.ID
		FROM {$wpdb->posts} p, {$wpdb->posts} b, {$wpdb->postmeta} bm
		WHERE p.ID = b.post_parent
		AND b.ID = bm.post_id
		AND bm.meta_key='_wc_backorder_email_sent' 
		AND p.ID=$oid
		";

	$orders_ids = $wpdb->get_results($sql);

	foreach($orders_ids as $o) { 

		$oid = $o->ID;

		$return['shipping'] = $return['shipping'] + get_post_meta($oid, '_order_shipping', true);
		$return['tax'] = $return['tax'] + get_post_meta($oid, '_order_tax', true);
		$return['total'] = $return['total'] + get_post_meta($oid, '_order_total', true);

	}

	$return['items'] = $return['total'] - $return['shipping'] - $return['tax'];

	return $return;

}


function getOrderBackordersPaidValue ($oid = 0) {

	global $wpdb;

	$return['items'] = 0;
	$return['shipping'] = 0;
	$return['tax'] = 0;
	$return['total'] = 0;

	$sql = "
		SELECT b.ID
		FROM {$wpdb->posts} p, {$wpdb->posts} b, {$wpdb->postmeta} bm
		WHERE p.ID = b.post_parent
		AND b.ID = bm.post_id
		AND bm.meta_key='_wc_backorder_email_sent' 
		AND p.ID=$oid
		";

	$orders_ids = $wpdb->get_results($sql);

	foreach($orders_ids as $o) { 

		$oid = $o->ID;

		if ( !empty(get_post_meta($oid, '_paid_date', true)) ) {
			$return['shipping'] = $return['shipping'] + get_post_meta($oid, '_order_shipping', true);
			$return['tax'] = $return['tax'] + get_post_meta($oid, '_order_tax', true);
			$return['total'] = $return['total'] + get_post_meta($oid, '_order_total', true);
		}

	}

	$return['items'] = $return['total'] - $return['shipping'] - $return['tax'];

	return $return;

}


function getOrderBackordersUnPaidValue ($oid = 0) {

	global $wpdb;

	$return['items'] = 0;
	$return['shipping'] = 0;
	$return['tax'] = 0;
	$return['total'] = 0;

	$sql = "
		SELECT b.ID
		FROM {$wpdb->posts} p, {$wpdb->posts} b, {$wpdb->postmeta} bm
		WHERE p.ID = b.post_parent
		AND b.ID = bm.post_id
		AND bm.meta_key='_wc_backorder_email_sent' 
		AND p.ID=$oid
		";

	$orders_ids = $wpdb->get_results($sql);

	foreach($orders_ids as $o) { 

		$oid = $o->ID;

		if ( empty(get_post_meta($oid, '_paid_date', true)) ) {
			$return['shipping'] = $return['shipping'] + get_post_meta($oid, '_order_shipping', true);
			$return['tax'] = $return['tax'] + get_post_meta($oid, '_order_tax', true);
			$return['total'] = $return['total'] + get_post_meta($oid, '_order_total', true);
		}

	}

	$return['items'] = $return['total'] - $return['shipping'] - $return['tax'];

	return $return;

}


function isItemBackorder ($item_id = 0) {


	global $wpdb;

	$oid = $wpdb->get_var("SELECT order_id from {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = $item_id ");

	$backorders = getOrderBackorders($oid);
	if ( empty($backorders) )
		return false;

	$pid = $wpdb->get_var("SELECT max(meta_value) from {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = $item_id and meta_key in ('_product_id', '_variation_id') and meta_value > 0 ");

	if ( empty($pid) )
		return false;

	foreach ($backorders as $backorder_id) {

		$backorder_pid = $wpdb->get_var("SELECT max(m.meta_value) from {$wpdb->prefix}woocommerce_order_itemmeta m, {$wpdb->prefix}woocommerce_order_items i WHERE m.order_item_id = i.order_item_id and i.order_id = $backorder_id and m.meta_key in ('_product_id', '_variation_id') and meta_value > 0 ");

		if ( (int)$pid == (int)$backorder_pid ) {
			return true;
		}
	}

	return false;

}


function getItemBackorderValues ($item_id = 0) {

	global $wpdb;


	$return['items'] = 0;
	$return['shipping'] = 0;
	$return['tax'] = 0;
	$return['total'] = 0;

	if ( !isItemBackorder($item_id) )
		return $return;


	$oid = $wpdb->get_var("SELECT order_id from {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = $item_id ");


	$backorders = getOrderBackorders($oid);
	if ( empty($backorders) )
		return $return;

	$pid = $wpdb->get_var("SELECT max(meta_value) from {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = $item_id and meta_key in ('_product_id', '_variation_id') ");


	foreach ($backorders as $backorder_id) {

		$backorder_pid = $wpdb->get_var("SELECT max(m.meta_value) from {$wpdb->prefix}woocommerce_order_itemmeta m, {$wpdb->prefix}woocommerce_order_items i WHERE m.order_item_id = i.order_item_id and i.order_id = $backorder_id and m.meta_key in ('_product_id', '_variation_id') ");

		if ( $pid == $backorder_pid ) {

			$backorder_order_item_id = $wpdb->get_var("SELECT m.order_item_id from {$wpdb->prefix}woocommerce_order_itemmeta m, {$wpdb->prefix}woocommerce_order_items i WHERE m.order_item_id = i.order_item_id and i.order_id = $backorder_id and m.meta_key in ('_product_id', '_variation_id') and m.meta_value = $backorder_pid and m.meta_value > 0 ");

			$backorderItemValues = getOrderItemValues($backorder_order_item_id, 1);

			$return['items'] = $return['items'] + $backorderItemValues['items'];
			$return['shipping'] = $return['shipping'] + $backorderItemValues['shipping'];
			$return['tax'] = $return['tax'] + $backorderItemValues['tax'];

		}
	}

	return $return;

}



function getItemBackorderPaidValues ($item_id = 0) {

	global $wpdb;


	$return['items'] = 0;
	$return['shipping'] = 0;
	$return['tax'] = 0;
	$return['total'] = 0;

	if ( !isItemBackorder($item_id) )
		return $return;


	$oid = $wpdb->get_var("SELECT order_id from {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = $item_id ");

	$backorders = getOrderBackorders($oid);
	if ( empty($backorders) )
		return $return;

	$pid = $wpdb->get_var("SELECT max(meta_value) from {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = $item_id and meta_key in ('_product_id', '_variation_id') ");


	foreach ($backorders as $backorder_id) {

		if ( !empty(get_post_meta($backorder_id, '_paid_date', true)) ) {

			$backorder_pid = $wpdb->get_var("SELECT max(m.meta_value) from {$wpdb->prefix}woocommerce_order_itemmeta m, {$wpdb->prefix}woocommerce_order_items i WHERE m.order_item_id = i.order_item_id and i.order_id = $backorder_id and m.meta_key in ('_product_id', '_variation_id') ");

			if ( $pid == $backorder_pid ) {

				$backorder_order_item_id = $wpdb->get_var("SELECT m.order_item_id from {$wpdb->prefix}woocommerce_order_itemmeta m, {$wpdb->prefix}woocommerce_order_items i WHERE m.order_item_id = i.order_item_id and i.order_id = $backorder_id and m.meta_key in ('_product_id', '_variation_id') and m.meta_value = $backorder_pid and m.meta_value > 0 ");

				$backorderItemValues = getOrderItemValues($backorder_order_item_id, 1);

				$return['items'] = $return['items'] + $backorderItemValues['items'];
				$return['shipping'] = $return['shipping'] + $backorderItemValues['shipping'];
				$return['tax'] = $return['tax'] + $backorderItemValues['tax'];

			}
		}
	}

	return $return;

}



function getItemBackorderUnPaidValues ($item_id = 0) {

	global $wpdb;


	$return['items'] = 0;
	$return['shipping'] = 0;
	$return['tax'] = 0;
	$return['total'] = 0;

	if ( !isItemBackorder($item_id) )
		return $return;


	$oid = $wpdb->get_var("SELECT order_id from {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = $item_id ");

	$backorders = getOrderBackorders($oid);
	if ( empty($backorders) )
		return $return;

	$pid = $wpdb->get_var("SELECT max(meta_value) from {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = $item_id and meta_key in ('_product_id', '_variation_id') ");


	foreach ($backorders as $backorder_id) {

		if ( empty(get_post_meta($backorder_id, '_paid_date', true)) ) {

			$backorder_pid = $wpdb->get_var("SELECT max(m.meta_value) from {$wpdb->prefix}woocommerce_order_itemmeta m, {$wpdb->prefix}woocommerce_order_items i WHERE m.order_item_id = i.order_item_id and i.order_id = $backorder_id and m.meta_key in ('_product_id', '_variation_id') ");

			if ( $pid == $backorder_pid ) {

				$backorder_order_item_id = $wpdb->get_var("SELECT m.order_item_id from {$wpdb->prefix}woocommerce_order_itemmeta m, {$wpdb->prefix}woocommerce_order_items i WHERE m.order_item_id = i.order_item_id and i.order_id = $backorder_id and m.meta_key in ('_product_id', '_variation_id') and m.meta_value = $backorder_pid and m.meta_value > 0 ");

				$backorderItemValues = getOrderItemValues($backorder_order_item_id, 1);

				$return['items'] = $return['items'] + $backorderItemValues['items'];
				$return['shipping'] = $return['shipping'] + $backorderItemValues['shipping'];
				$return['tax'] = $return['tax'] + $backorderItemValues['tax'];

			}
		}
	}

	return $return;

}



function getOrderTypeDetailed($oid = 0) {

	// onepay; multipay-init; multipay-pay; subscription-init; subscription-subscription; subscription-trial; subscription-renewal; backorder

	global $wpdb;

	$backorder =  $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_wc_backorder_email_sent' and post_id=$oid");

	$subscription_init = $wpdb->get_var("select count(*) from {$wpdb->posts} p, {$wpdb->posts} s where p.ID = s.post_parent and p.ID = $oid and p.post_type = 'shop_order' and s.post_type = 'shop_subscription'");
	$shop_subscription = $wpdb->get_var("select count(*) count from {$wpdb->posts} where ID = $oid and post_type = 'shop_subscription'");
	$subscription_child = $wpdb->get_var("select count(*) from {$wpdb->posts} p, {$wpdb->postmeta} m, {$wpdb->posts} s where p.ID = m.post_id and m.meta_key = '_subscription_renewal' and m.meta_value = s.ID and p.ID = $oid and p.post_type = 'shop_order' and s.post_type = 'shop_subscription'");

	$multipay_init = $wpdb->get_var("select count(*) from {$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta im where p.ID = i.order_id and i.order_item_id = im.order_item_id and p.ID = $oid and im.meta_key = '_is_deposit' and im.meta_value = 'yes' and p.post_type = 'shop_order'");
	$multipay_pay  = $wpdb->get_var("select count(*) from {$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta im, {$wpdb->posts} parent, {$wpdb->prefix}woocommerce_order_items parent_i, {$wpdb->prefix}woocommerce_order_itemmeta parent_im  where p.ID = i.order_id and i.order_item_id = im.order_item_id and p.post_parent = parent.ID and parent.ID = parent_i.order_id and parent_i.order_item_id = parent_im.order_item_id and parent_im.meta_key = '_is_deposit' and parent_im.meta_value = 'yes' and parent.post_type = 'shop_order' and p.ID = $oid and im.meta_key = '_original_order_id' and im.meta_value = parent.ID and p.post_type = 'shop_order'");


	$return = '';
	if (!empty($backorder)) {
		$return = 'backorder';
	} else if ( !empty($multipay_init) && empty($multipay_pay) ) {
		$return = 'multipay-init';
	} else if ( empty($multipay_init) && !empty($multipay_pay) ) {
		$return = 'multipay-pay';
	} else if ( !empty($multipay_init) && !empty($multipay_pay) ) {
		$return = 'error';
	} else if ( !empty($subscription_init) ) {
		$return = 'subscription-init';
	} else if ( !empty($shop_subscription) ) {
		$return = 'subscription-subscription';
	} else if ( !empty($subscription_child) ) {
		$return = 'subscription-renewal';
	} else if ( empty($multipay_init) && empty($multipay_pay) && empty($subscription_init) && empty($shop_subscription) && empty($subscription_child) ) {
		$return = 'onepay';
	} else {
		$return = 'error';
	}


	return $return;


}


function getCustomerID($oid) {

	global $wpdb;

	$cust_number_id = $wpdb->get_var("select c.customer_id from {$wpdb->prefix}wc_order_stats c left join {$wpdb->prefix}wc_customer_lookup d on c.customer_id=d.customer_id where c.order_id=$oid");

	if (empty($cust_number_id)) {
		$cust_number_id = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_customer_user' and post_id=$oid");
	}

	return $cust_number_id;

}



function getReturnItems($oid) {

	global $wpdb;

	$return = [];

	$return_data = get_post_meta($oid, 'mwb_wrma_return_product', true);

	if (!empty($return_data)) {
		foreach ($return_data as $ret_date => $ret_info) {
			if (!empty($ret_info['products'])) {
				$ret = [];
				foreach ($ret_info['products'] as $ret_product) {
					$ret_pid = $ret_product['product_id'];
					if(empty($ret_pid))
						$ret_pid = $ret_product['variation_id'];
					$ret_qty = $ret_product['qty'];

					$sku = $wpdb->get_var("select sku from {$wpdb->prefix}wc_product_meta_lookup where product_id=$ret_pid");

					$return[] = array('pid' => $ret_pid, 'sku' => $sku, "qty" => $ret_qty);
				}
			}
		}
	}


	return $return;
}



function isUpsell($id=0) {

	global $wpdb;

	$is_upsell = $wpdb->get_var("
		select meta_value 
		from {$wpdb->prefix}woocommerce_order_itemmeta 
		where order_item_id = $id
		and meta_key = 'is_upsell_purchase'
		limit 0, 1
	");


	if ($is_upsell) {
		return 'yes';
	} else {
		return 'no';
	}

}



function containUpsell($id=0) {

	global $wpdb;

	$is_upsell = $wpdb->get_var("
		select meta_value 
		from {$wpdb->prefix}woocommerce_order_itemmeta 
		where order_item_id in (
			select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_id = $id
		) 
		and meta_key = 'is_upsell_purchase'
		limit 0, 1
	");


	if ($is_upsell) {
		return 'yes';
	} else {
		return 'no';
	}

}


function getActiveSubscriptions($customer_id=0) {

	global $wpdb;

	$subscriptions = $wpdb->get_var("
		select count(*)
		from {$wpdb->posts} p, {$wpdb->postmeta} m
		where p.ID = m.post_id 
		and p.post_type = 'shop_subscription'
		and p.post_status = 'wc-active'
		and m.meta_key = '_customer_user'
		and m.meta_value = $customer_id
	");

	return $subscriptions;
}


function getOrderType($id=0) {

	$post = get_post($id);

	$subscription = get_post_meta($id, '_subscription_renewal', true);
	$rma_exchange  = get_post_meta($id, 'mwb_wrma_exchange_order', true);	

	$return = 'N/A';
	if ($post->post_type == 'shop_order' && !empty($subscription)) {
		$return = 'CNT';
	} else if ($post->post_type == 'shop_order' && !empty($rma_exchange)) {
		$return = 'EXCHANGE';
	} else if ( $post->post_type == 'shop_order' && $post->post_status == 'wc-scheduled-payment') {
		$return = 'MULTIPAY';
	} else if ($post->post_type == 'shop_subscription') {
		$return = 'SUBSCRIPTION';
	} else if ($post->post_type == 'shop_order_refund') {
		$return = 'REFUND';
	} else if ($post->post_type == 'shop_order') {
		$return = 'IN';
	}

	return $return;
}


function getPaymentPlan($oid=0) {

	/*
	global $wpdb;

	$return = 'one time';

	$child_shop_count = $wpdb->get_var("select count(*) count from {$wpdb->posts} where post_parent = $oid and post_type = 'shop_order'");
	$child_subscription_count = $wpdb->get_var("select count(*) count from {$wpdb->posts} where post_parent = $oid and post_type = 'shop_subscription'");
	$deposit_count = $wpdb->get_var("select count(*) count from {$wpdb->prefix}woocommerce_order_itemmeta where order_item_id in (select order_item_id from wp_woocommerce_order_items where order_id = $oid) and meta_key = '_is_deposit'");

	if ($deposit_count > 0 && $child_shop_count > 0) {
		$return = 'multipay';
	} else if ($child_subscription_count > 0) {
		$return = 'trial';
	}

	return $return;
	*/

	$return = getOrderTypeDetailed($oid);

	return $return;

}



function getInstalmentNumber($id=0) {

	global $wpdb;

	$return = 0;

	$post = get_post($id);
	$parentID = $post->post_parent;
	$post_date = $post->post_date;


	$child_shop_count = 0;

	if ( getOrderTypeDetailed($id) == 'multipay-init' ) {

		$child_shop_count = 1;

	} else if ( getOrderTypeDetailed($id) == 'multipay-pay' ) {

		$child_shop_count = $wpdb->get_var("select count(*) from {$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta im, {$wpdb->posts} parent, {$wpdb->prefix}woocommerce_order_items parent_i, {$wpdb->prefix}woocommerce_order_itemmeta parent_im  where p.ID = i.order_id and i.order_item_id = im.order_item_id and p.post_parent = parent.ID and parent.ID = parent_i.order_id and parent_i.order_item_id = parent_im.order_item_id and parent_im.meta_key = '_is_deposit' and parent_im.meta_value = 'yes' and parent.post_type = 'shop_order' and p.post_parent = $parentID and im.meta_key = '_original_order_id' and im.meta_value = parent.ID and p.post_type = 'shop_order' and p.post_date <= $post_date ");

		$child_shop_count ++;

	}

	$return = (int)$child_shop_count;


	return $return;

}


function getInstalmentNumbers($id=0) {

	global $wpdb;

	$return = 0;

	$post = get_post($id);
	$parentID = $post->post_parent;
	$post_date = $post->post_date;


	$child_shop_count = 0;

	if ( getOrderTypeDetailed($id) == 'multipay-init' ) {

		$child_shop_count = $wpdb->get_var("select count(*) from {$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta im, {$wpdb->posts} parent, {$wpdb->prefix}woocommerce_order_items parent_i, {$wpdb->prefix}woocommerce_order_itemmeta parent_im  where p.ID = i.order_id and i.order_item_id = im.order_item_id and p.post_parent = parent.ID and parent.ID = parent_i.order_id and parent_i.order_item_id = parent_im.order_item_id and parent_im.meta_key = '_is_deposit' and parent_im.meta_value = 'yes' and parent.post_type = 'shop_order' and p.post_parent = $id and im.meta_key = '_original_order_id' and im.meta_value = parent.ID and p.post_type = 'shop_order' ");
		$child_shop_count ++;

	} else if ( getOrderTypeDetailed($id) == 'multipay-pay' ) {

		$child_shop_count = $wpdb->get_var("select count(*) from {$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta im, {$wpdb->posts} parent, {$wpdb->prefix}woocommerce_order_items parent_i, {$wpdb->prefix}woocommerce_order_itemmeta parent_im  where p.ID = i.order_id and i.order_item_id = im.order_item_id and p.post_parent = parent.ID and parent.ID = parent_i.order_id and parent_i.order_item_id = parent_im.order_item_id and parent_im.meta_key = '_is_deposit' and parent_im.meta_value = 'yes' and parent.post_type = 'shop_order' and p.post_parent = $parentID and im.meta_key = '_original_order_id' and im.meta_value = parent.ID and p.post_type = 'shop_order' ");
		$child_shop_count ++;

	}

	$return = (int)$child_shop_count;


	return $return;

}



function getSubscriptionforItem($id=0, $item_id) {

	global $wpdb;

	$subscription_id = $wpdb->get_var("
		select IFNULL(s.ID, 0) ID
		from {$wpdb->posts} s, {$wpdb->postmeta} r, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta im
		where s.ID = r.meta_value 
		and r.meta_key = '_subscription_renewal'
		and s.id = i.order_id
		and i.order_item_id = im.order_item_id 
		and i.order_item_type = 'line_item'
		and (
			(im.meta_key = '_product_id' and im.meta_value = '$item_id')
			or
			(im.meta_key = '_variation_id' and im.meta_value = '$item_id')
		)
		and r.post_id = '$id'");

	return $subscription_id;
}


function getRenewalNumberforItem($id=0, $item_id=0) {

	global $wpdb;

	$subscription_id = getSubscriptionforItem($id, $item_id);	

	$return = 0;

	if (!empty($subscription_id)) {
		$return = $wpdb->get_var("select count(*) from {$wpdb->posts} p, {$wpdb->postmeta} m where p.ID = m.post_id and m.meta_key = '_subscription_renewal' and m.meta_value = $subscription_id and p.ID <= $id ");
	}

	return $return;
}


function getPaymentMethod($id=0, $details = 1) {

	global $wpdb;

	$affirm = ['AFFIRM'];
	$paypal = ['PPCP-GATEWAY'];
	$creditcard = ['WC_GATEWAY_WORLDPAY', 'STRIPE_CC', 'GLOBALPAYMENTS_GPAPI', 'AUTHORIZE_NET_CIM_CREDIT_CARD'];

	if (WC_Subscriptions_Renewal_Order::is_renewal($id) ) { 
		$id = WC_Subscriptions_Renewal_Order::get_parent_order_id($id);
	}

	$PaymentMethod = get_post_meta($id,'_payment_method',true);

	$return = '';

	if (in_array(strtoupper($PaymentMethod), $creditcard)) {
		$return = 'Credit Card';
	} else if (in_array(strtoupper($PaymentMethod), $paypal)) {
		$return = 'Paypal';
	} else if (in_array(strtoupper($PaymentMethod), $affirm)) {
		$return = 'Affirm';
	} else {
		$return = $PaymentMethod;
	}

	if ($details == 1 && strtoupper($PaymentMethod) == 'WC_GATEWAY_WORLDPAY') {

		$PaymentToken = get_post_meta($id,'_payment_tokens',true);

		if (!empty($PaymentToken) && is_array($PaymentToken)) {
			$payment_token = (int)$PaymentToken[0];
			$return = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_payment_tokenmeta where payment_token_id = $payment_token and meta_key = 'card_type'");
		}

	} else if ($details == 1 && strtoupper($PaymentMethod) == 'AUTHORIZE_NET_CIM_CREDIT_CARD') {
		$card_type = get_post_meta($id, '_wc_authorize_net_cim_credit_card_card_type', true );
		if (!empty($card_type)) {
			$return = strtoupper($card_type);
		}
	}

	return $return;
}



function getItemType($id=0) {

	global $wpdb;

	$itemData = $wpdb->get_results("select * from {$wpdb->prefix}woocommerce_order_items i where i.order_item_id = $id");

	$return = '';

	if (is_array($itemData) && count($itemData) > 0) {
		if ($itemData[0]->order_item_type == 'shipping') {
			$return = 'shipping';
		} else if ($itemData[0]->order_item_type == 'fee') {
			$return = 'fee';
		} else if ($itemData[0]->order_item_type == 'tax') {
			$return = 'tax';
		} else if ($itemData[0]->order_item_type == 'line_item' && ( strtoupper(str_replace(array(' ', '&'), '', $itemData[0]->order_item_name)) == 'SHIPPINGPROCESSING'  || strtoupper(str_replace(array(' ', '&'), '', $itemData[0]->order_item_name)) == 'EXPRESSSHIPPING' ) ) {
			$return = 'shipping';
		} else if ($itemData[0]->order_item_type == 'line_item') {
			$return = 'line_item';
		} else if ($itemData[0]->order_item_type == 'coupon') {
			$return = 'coupon';
		}
	}

	return $return;

}



function getItemProductID($id=0) {

	global $wpdb;

	$pid = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_variation_id' and order_item_id=$id");
	$parent_id = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_product_id' and order_item_id=$id");

	if(!$pid)
		$pid = $parent_id;

	$return = $pid;

	return $return;
}



function getItemCategory($id=0) {

	global $wpdb;

	$return = '';

	$productID = getItemProductID($id);

	$items = $wpdb->get_results("
		SELECT {$wpdb->prefix}terms.name FROM wp_terms
		JOIN wp_term_relationships ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
		WHERE wp_term_relationships.term_taxonomy_id = wp_terms.term_id
		AND wp_terms.term_id > 23
		AND wp_term_relationships.object_id = $productID 
	");

	if (is_array($items) && count($items) > 0) {
		foreach ($items as $itemData) {
			if (strpos(strtoupper($itemData->name), 'BRAND') !== false) {
				$return = $return  . str_replace('Brand ', '', $itemData->name) . ', ';
			}
		}
	}

	if (strlen($return) > 2) {
		$return = substr_replace($return, "", -2);
	}


	$itemType = getItemType($id);
	if ($itemType == 'shipping') {
		$return = 'Shipping';
	} else if ($itemType == 'tax') {
		$return = 'Tax';
	}

	return $return;
}



function _getFunnel($id=0, $type='') {

	global $wpdb;

	$web_source = array('checkout', 'subscription', 'admin');

	$return = '';

	if ($type == 'c') {

		$affiliate = $wpdb->get_var("
			select a.custom_slug 
			from {$wpdb->postmeta} m, {$wpdb->prefix}custom_affiliate a 
			where m.meta_value = a.custom_slug 
			and m.meta_key='order_custom_affiliate_slug' 
			and m.post_id=$id
		");

		if (empty($affiliate)) {
			$affiliate = '';
		}

		$return = $affiliate;

	} else if ($type == 's') {

		$affiliate = $wpdb->get_var("
			select a.traffic_source 
			from {$wpdb->postmeta} m, {$wpdb->prefix}custom_affiliate a 
			where m.meta_value = a.custom_slug 
			and m.meta_key='order_custom_affiliate_slug' 
			and m.post_id=$id
		");

		if (empty($affiliate)) {
			$affiliate = '';
		}

		if (empty($affiliate)) {
			$affiliate = 'main';
		}

		$return = $affiliate;


	} else {

		$affiliate = $wpdb->get_var("
			select a.ca_title 
			from {$wpdb->postmeta} m, {$wpdb->prefix}custom_affiliate a 
			where m.meta_value = a.custom_slug 
			and m.meta_key='order_custom_affiliate_slug' 
			and m.post_id=$id
		");

		$created_via = $wpdb->get_var("
			select m.meta_value
			from {$wpdb->postmeta} m 
			where m.meta_key='_created_via' 
			and m.post_id=$id
		");

		$order_key = $wpdb->get_var("
			select m.meta_value
			from {$wpdb->postmeta} m 
			where m.meta_key='_order_key' 
			and m.post_id=$id
		");

		$telemarker = '';
		if (!empty($order_key)) {
			$pos = strpos($order_key, '@');
			if ($pos !== false) {
				$telemarker = substr($order_key, $pos+1);
			}
		}


		if (!empty($affiliate)) {
			$return = $affiliate;
		} else if (!empty($telemarker)) {
			$return = $telemarker;
		//} else if (!empty($created_via) && !in_array($created_via, $web_source)) {
		//	$return = $created_via;
		}

	}

	return $return;
}



function getFunnel($id=0, $type='') {

	$orderTypeDetailed = getOrderTypeDetailed( $id );

	if ( $orderTypeDetailed == 'multipay-pay' || $orderTypeDetailed == 'backorder' || $orderTypeDetailed == 'subscription-subscription' || $orderTypeDetailed == 'subscription-trial' || $orderTypeDetailed == 'subscription-renewal' ) {

		$parent_id = getParent( $id );

		$return = _getFunnel( $parent_id, $type );

	} else {

		$return = _getFunnel( $id, $type );

	}

	return $return;

}



function getParent($id=0) {

	$orderTypeDetailed = getOrderTypeDetailed($id);

	if ( $orderTypeDetailed == 'onepay' || $orderTypeDetailed == 'multipay-init' || $orderTypeDetailed == 'subscription-init' ) {
		return 0;
	} else if ( $orderTypeDetailed == 'multipay-pay' || $orderTypeDetailed == 'subscription-subscription' || $orderTypeDetailed == 'backorder' ) {
		$order = wc_get_order($id);
		return (int)$order->get_parent_id();
	} else if ( $orderTypeDetailed == 'subscription-trial' || $orderTypeDetailed == 'subscription-renewal' ) {
		$subscription_id = get_post_meta($id, '_subscription_renewal', true);
		$subscription = wc_get_order($subscription_id);
		return (int)$subscription->get_parent_id();
	} else {
		return 0;
	}
	

}



function _getSource($id=0) {

	global $wpdb;

	$return = '';


	$order_key = $wpdb->get_var("
		select m.meta_value
		from {$wpdb->postmeta} m 
		where m.meta_key='_order_key' 
		and m.post_id=$id
	");

	$created_via = $wpdb->get_var("
		select m.meta_value
		from {$wpdb->postmeta} m 
		where m.meta_key='_created_via' 
		and m.post_id=$id
	");


	if (!empty($order_key)) {
		$pos = strpos($order_key, '@');
		if ($pos !== false) {
			$return = trim(str_replace('API', '', $created_via)) . ' - ' .substr($order_key, $pos+1);
		}
	}

	if ( empty($return) ) {
		$return = 'WEB';
	}

	$c = getFunnel($id, 'c');
	if ( strtoupper($c) == 'DTM' ) {
		$return = 'DTM';
	}


	return $return;

}



function getSource($id=0) {


	$orderTypeDetailed = getOrderTypeDetailed( $id );

	if ( $orderTypeDetailed == 'multipay-pay' || $orderTypeDetailed == 'backorder' || $orderTypeDetailed == 'subscription-subscription' || $orderTypeDetailed == 'subscription-trial' || $orderTypeDetailed == 'subscription-renewal' ) {

		$parent_id = getParent( $id );

		$return = _getSource( $parent_id );

	} else {

		$return = _getSource( $id );

	}

	return $return;

}



function getScript($id=0) {

	$return = get_post_meta($id, 'scriptcode', true);

	if (empty($return))
		$return = get_post_meta($id, 'order_custom_affiliate_scriptcode', true);

	if (empty($return))
		$return = 'WEB';

	return $return;
}


function getShipment($id=0) {

	$shipmentInfo = get_post_meta($id,'_wc_shipment_tracking_items',true);
	$return['ship_date'] = '';
	$return['ship_method']  = '';
	$return['tracking_number']  = '';

 	if (!empty($shipmentInfo) && is_array($shipmentInfo)) {
		foreach ($shipmentInfo as $shipment) {


			if (!empty($shipment['tracking_provider'])) {
				if (!empty($return['ship_method'])) {
					//$return['ship_method'] = $return['ship_method'] . "\r\n";
					$return['ship_method'] = $return['ship_method'] . ", ";
				}
				$return['ship_method'] = $return['ship_method'] . $shipment['tracking_provider'];
			}
			if (!empty($shipment['tracking_number'])) {
				if (!empty($return['tracking_number'])) {
					//$return['tracking_number'] = $return['tracking_number'] . "\r\n";
					$return['tracking_number'] = $return['tracking_number'] . ", ";
				}
				$return['tracking_number'] = $return['tracking_number'] . $shipment['tracking_number'];
			}
			if (!empty($shipment['custom_tracking_provider'])) {
				if (!empty($return['ship_method'])) {
					//$return['ship_method'] = $return['ship_method'] . "\r\n";
					$return['ship_method'] = $return['ship_method'] . ", ";
				}
				$return['ship_method'] = $return['ship_method'] . $shipment['custom_tracking_provider'];
			}
			if ( !empty($shipment['date_shipped']) && strpos(date('m/d/Y', $shipment['date_shipped']), $return['ship_date']) === false ) {
				if (!empty($return['ship_date'])) {
					//$return['ship_date'] = $return['ship_date'] . "\r\n";
					$return['ship_date'] = $return['ship_date'] . ", ";
				}
				$return['ship_date'] = $return['ship_date'] . date('m/d/Y', $shipment['date_shipped']);
			}

		}
	}

	return $return;
}


function getItemDescription($id=0) {

	global $wpdb;

	$pid = (int)$wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_product_id' and order_item_id=$id");
	$vid = (int)$wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_variation_id' and order_item_id=$id");

	$return = $wpdb->get_var("select post_title from {$wpdb->posts} where ID=$vid");
	if (empty($return)) {
		$return = $wpdb->get_var("select post_title from {$wpdb->posts} where ID=$pid");
	}


	if (getItemType($id) == 'shipping') {
		$return = $wpdb->get_var("
			select i.order_item_name
			from {$wpdb->prefix}woocommerce_order_items i
			where i.order_item_id = $id
		");
	} else 	if (getItemType($id) == 'coupon') {
		$return = $wpdb->get_var("
			select i.order_item_name
			from {$wpdb->prefix}woocommerce_order_items i
			where i.order_item_id = $id
		");
	} else 	if (getItemType($id) == 'tax') {
		$return = $wpdb->get_var("
			select i.order_item_name
			from {$wpdb->prefix}woocommerce_order_items i
			where i.order_item_id = $id
		");
	}


	return $return;
}



function getItemSKU($id=0) {

	global $wpdb;

	$pid = (int)$wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_product_id' and order_item_id=$id");
	$vid = (int)$wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_variation_id' and order_item_id=$id");

	$return = $wpdb->get_var("select sku from {$wpdb->prefix}wc_product_meta_lookup where product_id=$vid");
	if (empty($return)) {
		$return = $wpdb->get_var("select sku from {$wpdb->prefix}wc_product_meta_lookup where product_id=$pid");
	}


	if (getItemType($id) == 'shipping') {
		$return = 'Shipping';
	} else 	if (getItemType($id) == 'coupon') {
		$return = 'Coupon';
	} else 	if (getItemType($id) == 'tax') {
		$return = 'Tax';
	}


	return $return;
}



function getItemQty($id=0) {

	global $wpdb;

	$return = '';

	$return = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_qty' and order_item_id=$id");

	/*
	if (getItemType($id) == 'shipping') {
		if (empty($return))
			$return = 1;
	} else if (getItemType($id) == 'coupon') {
		if (empty($return))
			$return = 1;
	} else if (getItemType($id) == 'tax') {
		if (empty($return))
			$return = 1;
	}
	*/

	if (getItemType($id) == 'shipping') {
		$return = '';
	} else if (getItemType($id) == 'coupon') {
		$return = '';
	} else if (getItemType($id) == 'tax') {
		$return = '';
	}


	return $return;
}



function getOrderItemValues($id=0, $with_item_tax = 0) {

	global $wpdb;

	$result['items_without_discount'] = 0;

	$result['items'] = 0;
	$result['shipping'] = 0;
	$result['tax'] = 0;
	$result['coupon'] = 0;

	$result['full_items'] = 0;
	$result['full_shipping'] = 0;
	$result['full_tax'] = 0;
	$result['full_coupon'] = 0;

	$result['trial'] = 0;
	$result['instalment'] = 0;

	$result['shipping_for_item'] = 0;



	$keep_taxes = array();
	$valid_taxes = $wpdb->get_results("
		select 
			order_item_name 
		from {$wpdb->prefix}woocommerce_order_items
		where order_item_type = 'tax'
		and order_id in (
			select order_id from {$wpdb->prefix}woocommerce_order_items where order_item_id = $id 
		)
	");
	foreach ($valid_taxes as $valid_tax) {
		$keep_taxes[] = $valid_tax->order_item_name;
	}


	$is_refund = $wpdb->get_var("select count(*) from {$wpdb->prefix}woocommerce_order_items i, {$wpdb->posts} p where p.ID = i.order_id and i.order_item_id = $id and p.post_type = 'shop_order_refund' ");


	if ( $with_item_tax == 0) {

		$items = $wpdb->get_results("
			select 
				i.order_item_name, i.order_item_type, m.meta_key, m.meta_value as value, 
				CASE
				    WHEN m.meta_key = '_line_total' THEN IFNULL( (select meta_value from wp_woocommerce_order_itemmeta mx where mx.order_item_id = m.order_item_id and mx.meta_key = '_deposit_full_amount_ex_tax'),  m.meta_value)
				    ELSE m.meta_value
				END full_value
			from {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m 
			where i.order_item_id = m.order_item_id 
			and i.order_item_id = $id 
			and i.order_item_type in ('line_item', 'shipping', 'tax', 'coupon')
			and m.meta_key in ('_line_total', 'cost', 'tax_amount', 'discount_amount')
			order by i.order_item_id
		");

	} else {

		$items = $wpdb->get_results("
			select 
				i.order_item_name, i.order_item_type, m.meta_key, m.meta_value as value, 
				CASE
				    WHEN m.meta_key = '_line_total' THEN IFNULL( (select meta_value from wp_woocommerce_order_itemmeta mx where mx.order_item_id = m.order_item_id and mx.meta_key = '_deposit_full_amount_ex_tax'),  m.meta_value)
				    ELSE m.meta_value
				END full_value
			from {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m 
			where i.order_item_id = m.order_item_id 
			and i.order_item_id = $id 
			and i.order_item_type in ('line_item', 'shipping', 'coupon')
			and m.meta_key in ('_line_total', 'cost', 'discount_amount')
			order by i.order_item_id
		");


	}

	if (is_array($items) && count($items) > 0) {
		foreach ($items as $itemData) {
//echo $itemData->order_item_name . ' '; echo print_r($result); echo "<br>\n";

			if ($itemData->order_item_type == 'shipping') {
				$result['shipping'] = $result['shipping'] + (float)$itemData->value;
				$result['full_shipping'] = $result['full_shipping'] + (float)$itemData->full_value;
				//$result['items_without_discount'] = $result['items_without_discount'] + (float)$itemData->value; // discount is only for items

					if ($is_refund) {
						$result['tax'] = wc_get_order_item_meta( $id, 'total_tax', true );
						$result['full_tax'] = wc_get_order_item_meta( $id, 'total_tax', true );
					} else {
						$tax_data = wc_get_order_item_meta( $id, 'taxes', true );
						if ( is_array($tax_data) && array_key_exists("total", $tax_data) ) {
							foreach ($tax_data["total"] as $tax_key => $value) {
								if ( in_array($tax_key, $keep_taxes) ) {
									$result['tax'] = $result['tax'] + (float)$value;
								}
							}
						}
						if ( is_array($tax_data) && array_key_exists("subtotal", $tax_data) ) {
							foreach ($tax_data["subtotal"] as $tax_key => $value) {
								if ( in_array($tax_key, $keep_taxes) ) {
									$result['full_tax'] = $result['full_tax'] + (float)$value;
								}
							}
						}
					}


			} else if ($itemData->order_item_type == 'fee') {
				$result['items'] = $result['items'] + (float)$itemData->value;
				$result['full_items'] = $result['full_items'] + (float)$itemData->full_value;
				//$result['items_without_discount'] = $result['items_without_discount'] + (float)$itemData->value; // discount is only for items
			} else if ($itemData->order_item_type == 'line_item' && ( strtoupper(str_replace(array(' ', '&'), '', $itemData->order_item_name)) == 'SHIPPINGPROCESSING' || strtoupper(str_replace(array(' ', '&'), '', $itemData->order_item_name)) == 'EXPRESSSHIPPING' ) ) {
				if ($itemData->meta_key == '_line_total') {
					$result['shipping'] = $result['shipping'] + (float)$itemData->value;
					$result['full_shipping'] = $result['full_shipping'] + (float)$itemData->full_value;

					$tax_data = wc_get_order_item_meta( $id, '_line_tax_data', true );
					if ( is_array($tax_data) && array_key_exists("total", $tax_data) ) {
						foreach ($tax_data["total"] as $tax_key => $value) {
							$result['tax'] = $result['tax'] + (float)$value;
						}
					}
					if ( is_array($tax_data) && array_key_exists("subtotal", $tax_data) ) {
						foreach ($tax_data["subtotal"] as $tax_key => $value) {
							$result['full_tax'] = $result['full_tax'] + (float)$value;
						}
					}

				}
			} else if ($itemData->order_item_type == 'line_item') {
				if ($itemData->meta_key == '_line_total') {
					$result['items'] = $result['items'] + (float)$itemData->value;
					$result['full_items'] = $result['full_items'] + (float)$itemData->full_value;

					if ($is_refund) {
						$result['tax'] = wc_get_order_item_meta( $id, '_line_tax', true );
						$result['full_tax'] = wc_get_order_item_meta( $id, '_line_tax', true );
					} else {
						$tax_data = wc_get_order_item_meta( $id, '_line_tax_data', true );
						if ( is_array($tax_data) && array_key_exists("total", $tax_data) ) {
							foreach ($tax_data["total"] as $tax_key => $value) {
								if ( in_array($tax_key, $keep_taxes) ) {
									$result['tax'] = $result['tax'] + (float)$value;
								}
							}
						}
						if ( is_array($tax_data) && array_key_exists("subtotal", $tax_data) ) {
							foreach ($tax_data["subtotal"] as $tax_key => $value) {
								if ( in_array($tax_key, $keep_taxes) ) {
									$result['full_tax'] = $result['full_tax'] + (float)$value;
								}
							}
						}
					}
				}
			} else if ($itemData->order_item_type == 'tax') {
				$result['tax'] = $result['tax'] + (float)$itemData->value;
				$result['full_tax'] = $result['full_tax'] + (float)$itemData->full_value;
				//$result['items_without_discount'] = $result['items_without_discount'] + (float)$itemData->value; // discount is only for items
			} else if ($itemData->order_item_type == 'coupon') {
				$result['coupon'] = $result['coupon'] + (float)$itemData->value;
				$result['full_coupon'] = $result['full_coupon'] + (float)$itemData->full_value;
				//$result['items_without_discount'] = $result['items_without_discount'] + (float)$itemData->value; // discount is only for items
			}

//echo print_r($result); echo "<br>\n";

		}
	}



	// Items without discount
	$items = $wpdb->get_results("
		select 
			i.order_item_name, i.order_item_type, m.meta_key, m.meta_value value
		from {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m 
		where i.order_item_id = m.order_item_id 
		and i.order_item_id = $id 
		and i.order_item_type in ('line_item')
		and m.meta_key in ('_line_subtotal')
		order by i.order_item_id
	");
	if (is_array($items) && count($items) > 0) {
		foreach ($items as $itemData) {
			if (!( strtoupper(str_replace(array(' ', '&'), '', $itemData->order_item_name)) == 'SHIPPINGPROCESSING'  || strtoupper(str_replace(array(' ', '&'), '', $itemData->order_item_name)) == 'EXPRESSSHIPPING' ) ) {
				$result['items_without_discount'] = $result['items_without_discount'] + (float)$itemData->value;
			}
		}
	}



	$trial = $wpdb->get_var("
		select meta_value from wp_woocommerce_order_itemmeta where order_item_id in 
		(
		select sm.order_item_id
		from  {$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m, {$wpdb->prefix}posts sp, {$wpdb->prefix}woocommerce_order_items si, {$wpdb->prefix}woocommerce_order_itemmeta sm
		where p.id = i.order_id and i.order_item_id = m.order_item_id
		and p.id = sp.post_parent and sp.post_type = 'shop_subscription'
		and sp.id = si.order_id and si.order_item_id = sm.order_item_id
		and i.order_item_type = 'line_item'
		and m.meta_key in ('_product_id', '_variation_id')
		and i.order_item_id = $id
		and m.meta_value > 0
		and m.meta_value = sm.meta_value
		) and wp_woocommerce_order_itemmeta.meta_key = '_line_total'
	");

	$result['trial'] = floatval($trial);


	$deposit_full = $wpdb->get_var("
		select m.meta_value
		from  {$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m
		where p.id = i.order_id and i.order_item_id = m.order_item_id
		and i.order_item_type = 'line_item'
		and m.meta_key in ('_deposit_full_amount_ex_tax')
		and i.order_item_id = $id
	");

	if (floatval($deposit_full) > 0) {
		$result['instalment'] = floatval($deposit_full) - $result['items'];
	}



	$result['total'] = $result['items'] + $result['shipping'] + $result['tax'];
	$result['full_total'] = $result['full_items'] + $result['full_shipping'] + $result['full_tax'];
	//$result['items_without_discount'] = $result['items_without_discount'] + $result['shipping'] + $result['tax'];

	return $result;

}




function getOrderValues($id=0) {

	global $wpdb;

	$result['items_without_discount'] = 0;

	$result['items'] = 0;
	$result['shipping'] = 0;
	$result['tax'] = 0;
	$result['coupon'] = 0;

	$result['full_items'] = 0;
	$result['full_shipping'] = 0;
	$result['full_tax'] = 0;
	$result['full_coupon'] = 0;

	$result['trial'] = 0;
	$result['instalment'] = 0;


	$items = $wpdb->get_results("
		select 
			i.order_item_id, i.order_item_name, i.order_item_type, m.meta_key, m.meta_value value, 
			CASE
			    WHEN m.meta_key = '_line_total' THEN IFNULL( (select meta_value from wp_woocommerce_order_itemmeta mx where mx.order_item_id = m.order_item_id and mx.meta_key = '_deposit_full_amount_ex_tax'),  m.meta_value)
			    ELSE m.meta_value
			END full_value
		from {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m 
		where i.order_item_id = m.order_item_id 
		and i.order_id = $id 
		and i.order_item_type in ('line_item', 'shipping', 'coupon')
		and m.meta_key in ('_line_total', 'cost', 'discount_amount')
		order by i.order_item_id
	");


	if (is_array($items) && count($items) > 0) {
		foreach ($items as $itemData) {

			$is_refund = $wpdb->get_var("select count(*) from {$wpdb->prefix}woocommerce_order_items i, {$wpdb->posts} p where p.ID = i.order_id and i.order_item_id = " . $itemData->order_item_id . " and p.post_type = 'shop_order_refund' ");


			$keep_taxes = array();
			$valid_taxes = $wpdb->get_results("
				select 
					order_item_name 
				from {$wpdb->prefix}woocommerce_order_items
				where order_item_type = 'tax'
				and order_id in (
					select order_id from {$wpdb->prefix}woocommerce_order_items where order_item_id = ".$itemData->order_item_id." 
				)
			");
			foreach ($valid_taxes as $valid_tax) {
				$keep_taxes[] = $valid_tax->order_item_name;
			}


			if ($itemData->order_item_type == 'shipping') {
				$result['shipping'] = $result['shipping'] + (float)$itemData->value;
				$result['full_shipping'] = $result['full_shipping'] + (float)$itemData->full_value;
				//$result['items_without_discount'] = $result['items_without_discount'] + (float)$itemData->value; // discount is only for items

					if ($is_refund) {
						$result['tax'] = wc_get_order_item_meta( $itemData->order_item_id, 'total_tax', true );
						$result['full_tax'] = wc_get_order_item_meta( $itemData->order_item_id, 'total_tax', true );
					} else {
						$tax_data = wc_get_order_item_meta( $itemData->order_item_id, 'taxes', true );
						if ( is_array($tax_data) && array_key_exists("total", $tax_data) ) {
							foreach ($tax_data["total"] as $tax_key => $value) {
								if ( in_array($tax_key, $keep_taxes) ) {
									$result['tax'] = $result['tax'] + (float)$value;
								}
							}
						}
						if ( is_array($tax_data) && array_key_exists("subtotal", $tax_data) ) {
							foreach ($tax_data["subtotal"] as $tax_key => $value) {
								if ( in_array($tax_key, $keep_taxes) ) {
									$result['full_tax'] = $result['full_tax'] + (float)$value;
								}
							}
						}
					}

			} else if ($itemData->order_item_type == 'fee') {
				$result['items'] = $result['items'] + (float)$itemData->value;
				$result['full_items'] = $result['full_items'] + (float)$itemData->full_value;
				//$result['items_without_discount'] = $result['items_without_discount'] + (float)$itemData->value; // discount is only for items
			} else if ($itemData->order_item_type == 'line_item' && ( strtoupper(str_replace(array(' ', '&'), '', $itemData->order_item_name)) == 'SHIPPINGPROCESSING'  || strtoupper(str_replace(array(' ', '&'), '', $itemData->order_item_name)) == 'EXPRESSSHIPPING' ) ) {
				if ($itemData->meta_key == '_line_total') {
					$result['shipping'] = $result['shipping'] + (float)$itemData->value;
					$result['full_shipping'] = $result['full_shipping'] + (float)$itemData->full_value;
				} else if ($itemData->meta_key == '_line_tax') {
					$result['tax'] = $result['tax'] + (float)$itemData->value;
					$result['full_tax'] = $result['full_tax'] + (float)$itemData->full_value;
				}
			} else if ($itemData->order_item_type == 'line_item') {

				if ($itemData->meta_key == '_line_total') {
					$result['items'] = $result['items'] + (float)$itemData->value;
					$result['full_items'] = $result['full_items'] + (float)$itemData->full_value;

					$tax_data = wc_get_order_item_meta( $itemData->order_item_id, '_line_tax_data', true );
					if ( is_array($tax_data) && array_key_exists("total", $tax_data) ) {
						foreach ($tax_data["total"] as $tax_key => $value) {
							$result['tax'] = $result['tax'] + (float)$value;
						}
					}
					if ( is_array($tax_data) && array_key_exists("subtotal", $tax_data) ) {
						foreach ($tax_data["subtotal"] as $tax_key => $value) {
							$result['full_tax'] = $result['full_tax'] + (float)$value;
						}
					}
				}

			} else if ($itemData->order_item_type == 'coupon') {
				$result['coupon'] = $result['coupon'] + (float)$itemData->value;
				$result['full_coupon'] = $result['full_coupon'] + (float)$itemData->full_value;
				//$result['items_without_discount'] = $result['items_without_discount'] + (float)$itemData->value; // discount is only for items
			}

		}
	}


	// Items without discount
	$items = $wpdb->get_results("
		select 
			i.order_item_name, i.order_item_type, m.meta_key, m.meta_value value, 
			CASE
			    WHEN m.meta_key = '_line_total' THEN IFNULL( (select meta_value from wp_woocommerce_order_itemmeta mx where mx.order_item_id = m.order_item_id and mx.meta_key = '_deposit_full_amount_ex_tax'),  m.meta_value)
			    ELSE m.meta_value
			END full_value
		from {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m 
		where i.order_item_id = m.order_item_id 
		and i.order_id = $id 
		and i.order_item_type in ('line_item')
		and m.meta_key in ('_line_subtotal')
		order by i.order_item_id
	");
	if (is_array($items) && count($items) > 0) {
		foreach ($items as $itemData) {
			if (!( strtoupper(str_replace(array(' ', '&'), '', $itemData->order_item_name)) == 'SHIPPINGPROCESSING'  || strtoupper(str_replace(array(' ', '&'), '', $itemData->order_item_name)) == 'EXPRESSSHIPPING' ) ) {
				$result['items_without_discount'] = $result['items_without_discount'] + (float)$itemData->value;
			}
		}
	}



	$trial = $wpdb->get_var("
		select sum(meta_value) from wp_woocommerce_order_itemmeta where order_item_id in 
		(
		select sm.order_item_id
		from  {$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m, wp_posts sp, {$wpdb->prefix}woocommerce_order_items si, {$wpdb->prefix}woocommerce_order_itemmeta sm
		where p.id = i.order_id and i.order_item_id = m.order_item_id
		and p.id = sp.post_parent and sp.post_type = 'shop_subscription'
		and sp.id = si.order_id and si.order_item_id = sm.order_item_id
		and i.order_item_type = 'line_item'
		and m.meta_key in ('_product_id', '_variation_id')
		and p.id = $id
		and m.meta_value > 0
		and m.meta_value = sm.meta_value
		) and wp_woocommerce_order_itemmeta.meta_key = '_line_total'
	");

	$result['trial'] = floatval($trial);



	$deposit_full = $wpdb->get_var("
		select sum(m.meta_value)
		from  {$wpdb->posts} p, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta m
		where p.id = i.order_id and i.order_item_id = m.order_item_id
		and i.order_item_type = 'line_item'
		and m.meta_key in ('_deposit_full_amount_ex_tax')
		and p.id = $id
	");

	if (floatval($deposit_full) > 0) {
		$result['instalment'] = floatval($deposit_full) - $result['items'];
	}


	$result['total'] = $result['items'] + $result['shipping'] + $result['tax'];
	$result['full_total'] = $result['full_items'] + $result['full_shipping'] + $result['full_tax'];
	//$result['items_without_discount'] = $result['items_without_discount'] + $result['shipping'] + $result['tax'];

	return $result;

}



function getOrderItemDetails($oID=0, $itemID=0, $with_item_tax = 0) {

}


function getItemRefund($oid, $itemId=0) {

	global $wpdb;

	$refund_amount = 0;
	$refund_amount_partial = 0;
	$refund_amount_full = 0;

	$refund_amount_partial = abs((float)$wpdb->get_var("
		select sum(meta_value) meta_value
		from {$wpdb->prefix}woocommerce_order_itemmeta 
		where order_item_id in (
			select order_item_id from {$wpdb->prefix}woocommerce_order_itemmeta where order_item_id in (
				select order_item_id  from {$wpdb->prefix}woocommerce_order_items where order_id in (
					select id from {$wpdb->posts} where post_parent = $oid and post_type = 'shop_order_refund'
				) and order_item_type in ('line_item', 'shipping')
			) 
			and meta_key = '_refunded_item_id' 
			and meta_value = $itemId
		) 
		and meta_key in ('_line_total', '_line_tax', 'cost', 'total_tax')
	"));


// 'wc-return-approved' ???

	if (empty($refund_amount_partial)) {

		$refund_amount_full = abs((float)$wpdb->get_var("
			select sum(meta_value) from {$wpdb->prefix}woocommerce_order_itemmeta where order_item_id in (
				select order_item_id  from {$wpdb->prefix}woocommerce_order_items where order_id in (
					select id from {$wpdb->posts} where id = $oid and post_type = 'shop_order' and post_status in ('wc-refunded')
				) and order_item_type in ('line_item', 'shipping')
			) and meta_key in ('_line_total', '_line_tax', 'cost', 'total_tax')
			and order_item_id = $itemId
		"));

	}

	$refund_amount = $refund_amount_partial + $refund_amount_full;
	//echo "oid = $oid / itemId = $itemId / refund_amount_partial = $refund_amount_partial / refund_amount_full = $refund_amount_full";die();

	return $refund_amount;

}



function getItemReturn($oid=0, $itemId=0) {

	global $wpdb;

	$return_data = get_post_meta($oid, 'mwb_wrma_return_product', true);

	$return_date = '';
	$return_reason = '';
	if (!empty($return_data)) {
		foreach ($return_data as $ret_date => $ret_info) {
			$return_date = $ret_date;
			if (!empty($ret_info['products'])) {
				foreach ($ret_info['products'] as $ret_product) {
					if ($ret_product['item_id'] == $itemId) {
						if (!empty($ret_info['reason'])) {
							$return_reason = $ret_info['reason'];
						}
					}
				}
			}
		}
	}

	$result['date'] = $return_date;
	$result['reason'] = $return_reason;

	return $result;

}



function getOrderItemsValue($id=0) {
	$values = getOrderValues($id);
	if (array_key_exists('items', $values)) {
		return $values['items'];
	} else {
		return 0;
	}
}


function getOrderTaxValue($id=0) {
	$values = getOrderValues($id);
	if (array_key_exists('tax', $values)) {
		return $values['tax'];
	} else {
		return 0;
	}
}


function getOrderShippingValue($id=0) {
	$values = getOrderValues($id);
	if (array_key_exists('shipping', $values)) {
		return $values['shipping'];
	} else {
		return 0;
	}
}


function getOrderTotalValue($id=0) {
	$values = getOrderValues($id);
	if (array_key_exists('total', $values)) {
		return $values['total'];
	} else {
		return 0;
	}
}


function getFullItemsValue($id=0) {
	$values = getOrderValues($id);
	if (array_key_exists('full_items', $values)) {
		return $values['full_items'];
	} else {
		return 0;
	}
}


function getFullTaxValue($id=0) {
	$values = getOrderValues($id);
	if (array_key_exists('full_tax', $values)) {
		return $values['full_tax'];
	} else {
		return 0;
	}
}


function getFullShippingValue($id=0) {
	$values = getOrderValues($id);
	if (array_key_exists('full_shipping', $values)) {
		return $values['full_shipping'];
	} else {
		return 0;
	}
}


function getFullTotalValue($id=0) {
	$values = getOrderValues($id);
	if (array_key_exists('full_total', $values)) {
		return $values['full_total'];
	} else {
		return 0;
	}
}


function getOrderPaidValues($id=0, $date = '') {

	if (empty($date)) 
		$date = date("Y-m-d");

	$post = get_post($id);

	$result['items'] = 0;
	$result['shipping'] = 0;
	$result['tax'] = 0;
	$result['total'] = 0;

	$result['full_items'] = 0;
	$result['full_shipping'] = 0;
	$result['full_tax'] = 0;
	$result['full_total'] = 0;

	$paid_date = get_post_meta($id, '_paid_date', true);

	if (empty($paid_date))
		return $result;

	if ($date . ' 23:59:59' < $paid_date)
		return $result;

	$result = getOrderValues($id);

	return $result;

}


function getSubscriptions($id=0) {

	$orderList = $wpdb->get_results("select p.ID from {$wpdb->posts} p where p.post_parent = $id and p.post_type = 'shop_subscription'");
	foreach($orderList as $subscription) {
		$subscription->ID;
	}


}


function getVisitDetails($id=0) {

	global $wpdb;

	$browserFamilies = [
		'Android Browser'    => ['AN', 'MU'],
		'BlackBerry Browser' => ['BB'],
		'Baidu'              => ['BD', 'BS'],
		'Amiga'              => ['AV', 'AW'],
		'Chrome'             => [
			'1B', '2B', '7S', 'A0', 'AC', 'AD', 'AE', 'AH', 'AI',
			'AO', 'AS', 'BA', 'BM', 'BR', 'C2', 'C3', 'C5', 'C4',
			'C6', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CL',
			'CM', 'CN', 'CP', 'CR', 'CV', 'CW', 'DA', 'DD', 'DG',
			'DR', 'EC', 'EE', 'EU', 'EW', 'FA', 'FS', 'GB', 'GI',
			'H2', 'HA', 'HE', 'HH', 'HS', 'I3', 'IR', 'JB', 'KN',
			'KW', 'LF', 'LL', 'LO', 'M1', 'MA', 'MD', 'MR', 'MS',
			'MT', 'MZ', 'NM', 'NR', 'O0', 'O2', 'O3', 'OC', 'PB',
			'PT', 'QU', 'QW', 'RM', 'S4', 'S6', 'S8', 'S9', 'SB',
			'SG', 'SS', 'SU', 'SV', 'SW', 'SY', 'SZ', 'T1', 'TA',
			'TB', 'TG', 'TR', 'TS', 'TU', 'TV', 'UB', 'UR', 'VE',
			'VG', 'VI', 'VM', 'WP', 'WH', 'XV', 'YJ', 'YN', 'FH',
			'B1', 'BO', 'HB', 'PC', 'LA', 'LT', 'PD', 'HR',
		],
		'Firefox'            => [
			'AX', 'BI', 'BF', 'BH', 'BN', 'C0', 'CU', 'EI', 'F1',
			'FB', 'FE', 'FF', 'FM', 'FR', 'FY', 'GZ', 'I4', 'IF',
			'IW', 'LH', 'LY', 'MB', 'MN', 'MO', 'MY', 'OA', 'OS',
			'PI', 'PX', 'QA', 'QM', 'S5', 'SX', 'TF', 'TO', 'WF',
			'ZV',
		],
		'Internet Explorer'  => ['BZ', 'CZ', 'IE', 'IM', 'PS'],
		'Konqueror'          => ['KO'],
		'NetFront'           => ['NF'],
		'NetSurf'            => ['NE'],
		'Nokia Browser'      => ['DO', 'NB', 'NO', 'NV'],
		'Opera'              => ['O1', 'OG', 'OH', 'OI', 'OM', 'ON', 'OO', 'OP', 'OX'],
		'Safari'             => ['MF', 'S7', 'SF', 'SO'],
		'Sailfish Browser'   => ['SA'],
	];

	$deviceTypes = [
        	'desktop'               => 0,
        	'smartphone'            => 1,
        	'tablet'                => 2,
        	'feature phone'         => 3,
        	'console'               => 4,
        	'tv'                    => 5,
        	'car browser'           => 6,
        	'smart display'         => 7,
        	'camera'                => 8,
        	'portable media player' => 9,
        	'phablet'               => 10,
        	'smart speaker'         => 11,
        	'wearable'              => 12,
        	'peripheral'            => 13,
	];

    $deviceBrands = [
        '5E' => '2E',
        '2F' => 'F2 Mobile',
        '3Q' => '3Q',
        'J7' => '7 Mobile',
        '2Q' => '3GNET',
        '4G' => '4Good',
        '27' => '3GO',
        '04' => '4ife',
        '36' => '360',
        '88' => '8848',
        '41' => 'A1',
        '00' => 'Accent',
        'AE' => 'Ace',
        'AC' => 'Acer',
        '3K' => 'Acteck',
        'A9' => 'Advan',
        'AD' => 'Advance',
        '76' => 'Adronix',
        'AF' => 'AfriOne',
        'A3' => 'AGM',
        'J0' => 'AG Mobile',
        'AZ' => 'Ainol',
        'AI' => 'Airness',
        'AT' => 'Airties',
        'U0' => 'AIRON',
        '0A' => 'AIS',
        'AW' => 'Aiwa',
        '85' => 'Aiuto',
        'AK' => 'Akai',
        'Q3' => 'AKIRA',
        '1A' => 'Alba',
        'AL' => 'Alcatel',
        '20' => 'Alcor',
        '7L' => 'ALDI NORD',
        '6L' => 'ALDI SÜD',
        '3L' => 'Alfawise',
        '4A' => 'Aligator',
        'AA' => 'AllCall',
        '3A' => 'AllDocube',
        'A2' => 'Allview',
        'A7' => 'Allwinner',
        'A1' => 'Altech UEC',
        '66' => 'Altice',
        'A5' => 'altron',
        'KN' => 'Amazon',
        'AG' => 'AMGOO',
        '9A' => 'Amigoo',
        'AO' => 'Amoi',
        '54' => 'AMCV',
        '60' => 'Andowl',
        '7A' => 'Anry',
        'A0' => 'ANS',
        '74' => 'Anker',
        '3N' => 'Aoson',
        'O8' => 'AOC',
        'J2' => 'AOYODKG',
        '55' => 'AOpen',
        'AP' => 'Apple',
        'AR' => 'Archos',
        'AB' => 'Arian Space',
        'A6' => 'Ark',
        '5A' => 'ArmPhone',
        'AN' => 'Arnova',
        'AS' => 'ARRIS',
        'AQ' => 'Aspera',
        '40' => 'Artel',
        '21' => 'Artizlee',
        '8A' => 'Asano',
        '90' => 'Asanzo',
        '1U' => 'Astro',
        'A4' => 'Ask',
        'A8' => 'Assistant',
        'AU' => 'Asus',
        '6A' => 'AT&T',
        '2A' => 'Atom',
        'Z2' => 'Atvio',
        'AX' => 'Audiovox',
        'AJ' => 'AURIS',
        'ZA' => 'Avenzo',
        'AH' => 'AVH',
        'AV' => 'Avvio',
        'AY' => 'Axxion',
        'XA' => 'Axioo',
        'AM' => 'Azumi Mobile',
        'BO' => 'BangOlufsen',
        'BN' => 'Barnes & Noble',
        'BB' => 'BBK',
        '0B' => 'BB Mobile',
        'B6' => 'BDF',
        'BE' => 'Becker',
        'B5' => 'Beeline',
        'B0' => 'Beelink',
        'BL' => 'Beetel',
        'BQ' => 'BenQ',
        'BS' => 'BenQ-Siemens',
        '4Y' => 'Benzo',
        'BY' => 'BS Mobile',
        'BZ' => 'Bezkam',
        '9B' => 'Bellphone',
        '63' => 'Beyond',
        'BG' => 'BGH',
        '6B' => 'Bigben',
        'B8' => 'BIHEE',
        '1B' => 'Billion',
        'BA' => 'BilimLand',
        'BH' => 'BioRugged',
        'BI' => 'Bird',
        'BT' => 'Bitel',
        'B7' => 'Bitmore',
        'BK' => 'Bkav',
        '5B' => 'Black Bear',
        'BF' => 'Black Fox',
        'B2' => 'Blackview',
        'BP' => 'Blaupunkt',
        'BU' => 'Blu',
        'B3' => 'Bluboo',
        '2B' => 'Bluedot',
        'BD' => 'Bluegood',
        'LB' => 'Bluewave',
        'J8' => 'Bluebird',
        '7B' => 'Blloc',
        'UB' => 'Bleck',
        'Q2' => 'Blow',
        'BM' => 'Bmobile',
        'Y5' => 'BMAX',
        'B9' => 'Bobarry',
        'B4' => 'bogo',
        'BW' => 'Boway',
        'BX' => 'bq',
        '8B' => 'Brandt',
        'BV' => 'Bravis',
        'BR' => 'Brondi',
        'BJ' => 'BrightSign',
        'B1' => 'Bush',
        '4Q' => 'Bundy',
        'C9' => 'CAGI',
        'CT' => 'Capitel',
        'G3' => 'CG Mobile',
        '37' => 'CGV',
        'CP' => 'Captiva',
        'CF' => 'Carrefour',
        'CS' => 'Casio',
        'R4' => 'Casper',
        'CA' => 'Cat',
        'BC' => 'Camfone',
        'CJ' => 'Cavion',
        '4D' => 'Canal Digital',
        '02' => 'Cell-C',
        '34' => 'CellAllure',
        '7C' => 'Celcus',
        'CE' => 'Celkon',
        'CG' => 'Cellution',
        '62' => 'Centric',
        'C2' => 'Changhong',
        'CH' => 'Cherry Mobile',
        'C3' => 'China Mobile',
        'CI' => 'Chico Mobile',
        'HG' => 'CHIA',
        '1C' => 'Chuwi',
        'L8' => 'Clarmin',
        '25' => 'Claresta',
        'CD' => 'Cloudfone',
        '6C' => 'Cloudpad',
        'C0' => 'Clout',
        'CN' => 'CnM',
        'CY' => 'Coby Kyros',
        'XC' => 'Cobalt',
        'C6' => 'Comio',
        'CL' => 'Compal',
        'CQ' => 'Compaq',
        'C7' => 'ComTrade Tesla',
        'C8' => 'Concord',
        'CC' => 'ConCorde',
        'C5' => 'Condor',
        '4C' => 'Conquest',
        '3C' => 'Contixo',
        '8C' => 'Connex',
        '53' => 'Connectce',
        '9C' => 'Colors',
        'CO' => 'Coolpad',
        '4R' => 'CORN',
        '1O' => 'Cosmote',
        'CW' => 'Cowon',
        '75' => 'Covia',
        '33' => 'Clementoni',
        'CR' => 'CreNova',
        'CX' => 'Crescent',
        'CK' => 'Cricket',
        'CM' => 'Crius Mea',
        '0C' => 'Crony',
        'C1' => 'Crosscall',
        'CU' => 'Cube',
        'CB' => 'CUBOT',
        'CV' => 'CVTE',
        'C4' => 'Cyrus',
        'D5' => 'Daewoo',
        'DA' => 'Danew',
        'DT' => 'Datang',
        'D7' => 'Datawind',
        '7D' => 'Datamini',
        '6D' => 'Datalogic',
        'D1' => 'Datsun',
        'DB' => 'Dbtel',
        'DL' => 'Dell',
        'DE' => 'Denver',
        'DS' => 'Desay',
        'DW' => 'DeWalt',
        'DX' => 'DEXP',
        'DG' => 'Dialog',
        'DI' => 'Dicam',
        'D4' => 'Digi',
        'D3' => 'Digicel',
        'DH' => 'Digihome',
        'DD' => 'Digiland',
        'Q0' => 'DIGIFORS',
        '9D' => 'Ditecma',
        'D2' => 'Digma',
        '1D' => 'Diva',
        'D6' => 'Divisat',
        'X6' => 'DIXON',
        '5D' => 'DING DING',
        'DM' => 'DMM',
        'DN' => 'DNS',
        'DC' => 'DoCoMo',
        'DF' => 'Doffler',
        'D9' => 'Dolamee',
        'DO' => 'Doogee',
        'D0' => 'Doopro',
        'DV' => 'Doov',
        'DP' => 'Dopod',
        'DR' => 'Doro',
        'D8' => 'Droxio',
        'DJ' => 'Dragon Touch',
        'DU' => 'Dune HD',
        'EB' => 'E-Boda',
        'EJ' => 'Engel',
        '2E' => 'E-Ceros',
        'E8' => 'E-tel',
        'EP' => 'Easypix',
        'EQ' => 'Eagle',
        'EA' => 'EBEST',
        'E4' => 'Echo Mobiles',
        'ES' => 'ECS',
        '35' => 'ECON',
        'E6' => 'EE',
        'EK' => 'EKO',
        'EY' => 'Einstein',
        'EM' => 'Eks Mobility',
        '4K' => 'EKT',
        '7E' => 'ELARI',
        '03' => 'Electroneum',
        'Z8' => 'ELECTRONIA',
        'L0' => 'Element',
        'EG' => 'Elenberg',
        'EL' => 'Elephone',
        'JE' => 'Elekta',
        '4E' => 'Eltex',
        'ED' => 'Energizer',
        'E1' => 'Energy Sistem',
        '3E' => 'Enot',
        '8E' => 'Epik One',
        'E7' => 'Ergo',
        'EC' => 'Ericsson',
        '05' => 'Erisson',
        'ER' => 'Ericy',
        'EE' => 'Essential',
        'E2' => 'Essentielb',
        '6E' => 'eSTAR',
        'EN' => 'Eton',
        'ET' => 'eTouch',
        '1E' => 'Etuline',
        'EU' => 'Eurostar',
        'E9' => 'Evercoss',
        'EV' => 'Evertek',
        'E3' => 'Evolio',
        'EO' => 'Evolveo',
        'E0' => 'EvroMedia',
        'XE' => 'ExMobile',
        '4Z' => 'Exmart',
        'EH' => 'EXO',
        'EX' => 'Explay',
        'E5' => 'Extrem',
        'EF' => 'EXCEED',
        'QE' => 'EWIS',
        'EI' => 'Ezio',
        'EZ' => 'Ezze',
        '5F' => 'F150',
        'F6' => 'Facebook',
        'FA' => 'Fairphone',
        'FM' => 'Famoco',
        '17' => 'FarEasTone',
        '9R' => 'FaRao Pro',
        'FB' => 'Fantec',
        'FE' => 'Fengxiang',
        'F7' => 'Fero',
        'FI' => 'FiGO',
        'F1' => 'FinePower',
        'FX' => 'Finlux',
        'F3' => 'FireFly Mobile',
        'F8' => 'FISE',
        'FL' => 'Fly',
        'QC' => 'FLYCAT',
        'FN' => 'FNB',
        'FD' => 'Fondi',
        '0F' => 'Fourel',
        '44' => 'Four Mobile',
        'F0' => 'Fonos',
        'F2' => 'FORME',
        'F5' => 'Formuler',
        'FR' => 'Forstar',
        'RF' => 'Fortis',
        'FO' => 'Foxconn',
        'FT' => 'Freetel',
        'F4' => 'F&U',
        '1F' => 'FMT',
        'FG' => 'Fuego',
        'FU' => 'Fujitsu',
        'FW' => 'FNF',
        'GT' => 'G-TiDE',
        'G9' => 'G-Touch',
        '0G' => 'GFive',
        'GM' => 'Garmin-Asus',
        'GA' => 'Gateway',
        '99' => 'Galaxy Innovations',
        'GD' => 'Gemini',
        'GN' => 'General Mobile',
        '2G' => 'Genesis',
        'G2' => 'GEOFOX',
        'GE' => 'Geotel',
        'Q4' => 'Geotex',
        'GH' => 'Ghia',
        '2C' => 'Ghong',
        'GG' => 'Gigabyte',
        'GS' => 'Gigaset',
        'GZ' => 'Ginzzu',
        '1G' => 'Gini',
        'GI' => 'Gionee',
        'G4' => 'Globex',
        'U6' => 'Glofiish',
        'G7' => 'GoGEN',
        'GC' => 'GOCLEVER',
        'GB' => 'Gol Mobile',
        'GL' => 'Goly',
        'GX' => 'GLX',
        'G5' => 'Gome',
        'G1' => 'GoMobile',
        'GO' => 'Google',
        'G0' => 'Goophone',
        '6G' => 'Gooweel',
        'GR' => 'Gradiente',
        'GP' => 'Grape',
        'G6' => 'Gree',
        '3G' => 'Greentel',
        'GF' => 'Gretel',
        '82' => 'Gresso',
        'GU' => 'Grundig',
        'HF' => 'Hafury',
        'HA' => 'Haier',
        'HE' => 'HannSpree',
        'HK' => 'Hardkernel',
        'HS' => 'Hasee',
        'H6' => 'Helio',
        'ZH' => 'Hezire',
        'HL' => 'Hi-Level',
        '3H' => 'Hi',
        'H2' => 'Highscreen',
        'Q1' => 'High Q',
        '1H' => 'Hipstreet',
        'HI' => 'Hisense',
        'HC' => 'Hitachi',
        'H8' => 'Hitech',
        'W3' => 'HiMax',
        'H1' => 'Hoffmann',
        'H0' => 'Hometech',
        'HM' => 'Homtom',
        'HZ' => 'Hoozo',
        'H7' => 'Horizon',
        'HO' => 'Hosin',
        'H3' => 'Hotel',
        'HV' => 'Hotwav',
        'HW' => 'How',
        'WH' => 'Honeywell',
        'HP' => 'HP',
        'HT' => 'HTC',
        'HD' => 'Huadoo',
        'HU' => 'Huawei',
        'HX' => 'Humax',
        'HR' => 'Hurricane',
        'H5' => 'Huskee',
        'HY' => 'Hyrican',
        'HN' => 'Hyundai',
        '7H' => 'Hyve',
        '3I' => 'i-Cherry',
        'IJ' => 'i-Joy',
        'IM' => 'i-mate',
        'IO' => 'i-mobile',
        'OF' => 'iOutdoor',
        'IB' => 'iBall',
        'IY' => 'iBerry',
        '7I' => 'iBrit',
        'I2' => 'IconBIT',
        'IC' => 'iDroid',
        'IG' => 'iGet',
        'IH' => 'iHunt',
        'IA' => 'Ikea',
        '8I' => 'IKU Mobile',
        '2K' => 'IKI Mobile',
        'IK' => 'iKoMo',
        'I7' => 'iLA',
        '2I' => 'iLife',
        '1I' => 'iMars',
        'U4' => 'iMan',
        'IL' => 'IMO Mobile',
        'I3' => 'Impression',
        'FC' => 'INCAR',
        '2H' => 'Inch',
        '6I' => 'Inco',
        'IW' => 'iNew',
        'IF' => 'Infinix',
        'I0' => 'InFocus',
        'II' => 'Inkti',
        '81' => 'InfoKit',
        'I5' => 'InnJoo',
        '26' => 'Innos',
        'IN' => 'Innostream',
        'I4' => 'Inoi',
        'IQ' => 'INQ',
        'QN' => 'iQ&T',
        'IS' => 'Insignia',
        'IT' => 'Intek',
        'IX' => 'Intex',
        'IV' => 'Inverto',
        '32' => 'Invens',
        '4I' => 'Invin',
        'I1' => 'iOcean',
        'IP' => 'iPro',
        '8Q' => 'IQM',
        'I6' => 'Irbis',
        '5I' => 'Iris',
        'IR' => 'iRola',
        'IU' => 'iRulu',
        '9I' => 'iSWAG',
        '86' => 'IT',
        'IZ' => 'iTel',
        '0I' => 'iTruck',
        'I8' => 'iVA',
        'IE' => 'iView',
        '0J' => 'iVooMi',
        'UI' => 'ivvi',
        'I9' => 'iZotron',
        'JA' => 'JAY-Tech',
        'KJ' => 'Jiake',
        'J6' => 'Jeka',
        'JF' => 'JFone',
        'JI' => 'Jiayu',
        'JG' => 'Jinga',
        'VJ' => 'Jivi',
        'JK' => 'JKL',
        'JO' => 'Jolla',
        'UJ' => 'Juniper Systems',
        'J5' => 'Just5',
        'JV' => 'JVC',
        'JS' => 'Jesy',
        'KT' => 'K-Touch',
        'K4' => 'Kaan',
        'K7' => 'Kaiomy',
        'KL' => 'Kalley',
        'K6' => 'Kanji',
        'KA' => 'Karbonn',
        'K5' => 'KATV1',
        'K0' => 'Kata',
        'KZ' => 'Kazam',
        '9K' => 'Kazuna',
        'KD' => 'KDDI',
        'KS' => 'Kempler & Strauss',
        'K3' => 'Keneksi',
        'KX' => 'Kenxinda',
        'K1' => 'Kiano',
        'KI' => 'Kingsun',
        'KF' => 'KINGZONE',
        '46' => 'Kiowa',
        'KV' => 'Kivi',
        '64' => 'Kvant',
        '0K' => 'Klipad',
        'KC' => 'Kocaso',
        'KK' => 'Kodak',
        'KG' => 'Kogan',
        'KM' => 'Komu',
        'KO' => 'Konka',
        'KW' => 'Konrow',
        'KB' => 'Koobee',
        '7K' => 'Koolnee',
        'K9' => 'Kooper',
        'KP' => 'KOPO',
        'KR' => 'Koridy',
        'K2' => 'KRONO',
        'KE' => 'Krüger&Matz',
        '5K' => 'KREZ',
        'KH' => 'KT-Tech',
        'Z6' => 'KUBO',
        'K8' => 'Kuliao',
        '8K' => 'Kult',
        'KU' => 'Kumai',
        '6K' => 'Kurio',
        'KY' => 'Kyocera',
        'KQ' => 'Kyowon',
        '1K' => 'Kzen',
        'LQ' => 'LAIQ',
        'L6' => 'Land Rover',
        'L2' => 'Landvo',
        'LA' => 'Lanix',
        'LK' => 'Lark',
        'Z3' => 'Laurus',
        'LV' => 'Lava',
        'LC' => 'LCT',
        'L5' => 'Leagoo',
        'U3' => 'Leben',
        'LD' => 'Ledstar',
        'L1' => 'LeEco',
        '4B' => 'Leff',
        'L4' => 'Lemhoov',
        'LN' => 'Lenco',
        'LE' => 'Lenovo',
        'LT' => 'Leotec',
        'LP' => 'Le Pan',
        'L7' => 'Lephone',
        'LZ' => 'Lesia',
        'L3' => 'Lexand',
        'LX' => 'Lexibook',
        'LG' => 'LG',
        'LF' => 'Lifemaxx',
        'LJ' => 'L-Max',
        'LI' => 'Lingwin',
        '5L' => 'Linsar',
        'LW' => 'Linnex',
        'LO' => 'Loewe',
        'YL' => 'Loview',
        '1L' => 'Logic',
        'LM' => 'Logicom',
        '0L' => 'Lumigon',
        'LU' => 'Lumus',
        'L9' => 'Luna',
        'LR' => 'Luxor',
        'LY' => 'LYF',
        'LL' => 'Leader Phone',
        'QL' => 'LT Mobile',
        'MQ' => 'M.T.T.',
        'MN' => 'M4tel',
        'XM' => 'Macoox',
        '92' => 'MAC AUDIO',
        'MJ' => 'Majestic',
        '23' => 'Magnus',
        'NH' => 'Manhattan',
        '5M' => 'Mann',
        'MA' => 'Manta Multimedia',
        'Z0' => 'Mantra',
        'J4' => 'Mara',
        '2M' => 'Masstel',
        '50' => 'Matrix',
        '7M' => 'Maxcom',
        'ZM' => 'Maximus',
        '6X' => 'Maxtron',
        '0D' => 'MAXVI',
        'MW' => 'Maxwest',
        'M0' => 'Maze',
        'YM' => 'Maze Speed',
        '87' => 'Malata',
        '3D' => 'MDC Store',
        '09' => 'meanIT',
        'M3' => 'Mecer',
        '0M' => 'Mecool',
        'MC' => 'Mediacom',
        'MK' => 'MediaTek',
        'MD' => 'Medion',
        'M2' => 'MEEG',
        'MP' => 'MegaFon',
        'X0' => 'mPhone',
        '3M' => 'Meitu',
        'M1' => 'Meizu',
        '0E' => 'Melrose',
        'MU' => 'Memup',
        'ME' => 'Metz',
        'MX' => 'MEU',
        'MI' => 'MicroMax',
        'MS' => 'Microsoft',
        '1X' => 'Minix',
        'OM' => 'Mintt',
        'MO' => 'Mio',
        'M7' => 'Miray',
        '8M' => 'Mito',
        'MT' => 'Mitsubishi',
        'M5' => 'MIXC',
        '2D' => 'MIVO',
        '1Z' => 'MiXzo',
        'ML' => 'MLLED',
        'LS' => 'MLS',
        '4M' => 'Mobicel',
        'M6' => 'Mobiistar',
        'MH' => 'Mobiola',
        'MB' => 'Mobistel',
        '6W' => 'MobiWire',
        '9M' => 'Mobo',
        'M4' => 'Modecom',
        'MF' => 'Mofut',
        'MR' => 'Motorola',
        'MV' => 'Movic',
        'MM' => 'Mpman',
        'MZ' => 'MSI',
        '3R' => 'MStar',
        'M9' => 'MTC',
        'N4' => 'MTN',
        '72' => 'M-Tech',
        '9H' => 'M-Horse',
        '1R' => 'Multilaser',
        '1M' => 'MYFON',
        'MY' => 'MyPhone',
        '51' => 'Myros',
        'M8' => 'Myria',
        '6M' => 'Mystery',
        '3T' => 'MyTab',
        'MG' => 'MyWigo',
        'J3' => 'Mymaga',
        '07' => 'MyGica',
        '08' => 'Nabi',
        'N7' => 'National',
        'NC' => 'Navcity',
        '6N' => 'Navitech',
        '7V' => 'Navitel',
        'N3' => 'Navon',
        'NP' => 'Naomi Phone',
        'NE' => 'NEC',
        '8N' => 'Necnot',
        'NF' => 'Neffos',
        '1N' => 'Neomi',
        'NA' => 'Netgear',
        'NU' => 'NeuImage',
        'NW' => 'Newgen',
        'N9' => 'Newland',
        '0N' => 'Newman',
        'NS' => 'NewsMy',
        'ND' => 'Newsday',
        'HB' => 'New Balance',
        'XB' => 'NEXBOX',
        'NX' => 'Nexian',
        'N8' => 'NEXON',
        'N2' => 'Nextbit',
        'NT' => 'NextBook',
        '4N' => 'NextTab',
        'NG' => 'NGM',
        'NZ' => 'NG Optics',
        'NN' => 'Nikon',
        'NI' => 'Nintendo',
        'N5' => 'NOA',
        'N1' => 'Noain',
        'N6' => 'Nobby',
        '57' => 'Nubia',
        'JN' => 'NOBUX',
        'NB' => 'Noblex',
        'NK' => 'Nokia',
        'NM' => 'Nomi',
        '2N' => 'Nomu',
        'NR' => 'Nordmende',
        '7N' => 'NorthTech',
        '5N' => 'Nos',
        'NO' => 'Nous',
        'NQ' => 'Novex',
        'NJ' => 'NuAns',
        'NL' => 'NUU Mobile',
        'N0' => 'Nuvo',
        'NV' => 'Nvidia',
        'NY' => 'NYX Mobile',
        'O3' => 'O+',
        'OT' => 'O2',
        'O7' => 'Oale',
        'OC' => 'OASYS',
        'OB' => 'Obi',
        'OQ' => 'Oculus',
        'O1' => 'Odys',
        'O9' => 'Ok',
        'OA' => 'Okapia',
        'OD' => 'Onda',
        'ON' => 'OnePlus',
        'OX' => 'Onix',
        '3O' => 'ONYX BOOX',
        'O4' => 'ONN',
        '2O' => 'OpelMobile',
        'OH' => 'Openbox',
        'OP' => 'OPPO',
        'OO' => 'Opsson',
        'OR' => 'Orange',
        'O5' => 'Orbic',
        'OS' => 'Ordissimo',
        'OK' => 'Ouki',
        '0O' => 'OINOM',
        'QK' => 'OKWU',
        '56' => 'OKSI',
        'OE' => 'Oukitel',
        'OU' => 'OUYA',
        'OV' => 'Overmax',
        '30' => 'Ovvi',
        'O2' => 'Owwo',
        'OY' => 'Oysters',
        'O6' => 'Oyyu',
        'OZ' => 'OzoneHD',
        '7P' => 'P-UP',
        'PM' => 'Palm',
        'PN' => 'Panacom',
        'PA' => 'Panasonic',
        'PT' => 'Pantech',
        '94' => 'Packard Bell',
        'PB' => 'PCBOX',
        'PC' => 'PCD',
        'PD' => 'PCD Argentina',
        'PE' => 'PEAQ',
        'PG' => 'Pentagram',
        'PQ' => 'Pendoo',
        '93' => 'Perfeo',
        '1P' => 'Phicomm',
        '4P' => 'Philco',
        'PH' => 'Philips',
        '5P' => 'Phonemax',
        'PO' => 'phoneOne',
        'PI' => 'Pioneer',
        'PJ' => 'PiPO',
        '8P' => 'Pixelphone',
        '9O' => 'Pixela',
        'PX' => 'Pixus',
        'QP' => 'Pico',
        '9P' => 'Planet Computers',
        'PY' => 'Ployer',
        'P4' => 'Plum',
        '22' => 'Pluzz',
        'P8' => 'PocketBook',
        '0P' => 'POCO',
        'PV' => 'Point of View',
        'PL' => 'Polaroid',
        'Q6' => 'Polar',
        'PP' => 'PolyPad',
        'P5' => 'Polytron',
        'P2' => 'Pomp',
        'P0' => 'Poppox',
        'PS' => 'Positivo',
        '3P' => 'Positivo BGH',
        'P3' => 'PPTV',
        'FP' => 'Premio',
        'PR' => 'Prestigio',
        'P9' => 'Primepad',
        '6P' => 'Primux',
        '2P' => 'Prixton',
        'PF' => 'PROFiLO',
        'P6' => 'Proline',
        'P1' => 'ProScan',
        'P7' => 'Protruly',
        'R0' => 'ProVision',
        'PU' => 'PULID',
        'QH' => 'Q-Touch',
        'QB' => 'Q.Bell',
        'QI' => 'Qilive',
        'QM' => 'QMobile',
        'QT' => 'Qtek',
        'QA' => 'Quantum',
        'QU' => 'Quechua',
        'QO' => 'Qumo',
        'UQ' => 'Qubo',
        'QY' => 'Qnet Mobile',
        'R2' => 'R-TV',
        'RA' => 'Ramos',
        '0R' => 'Raspberry',
        'R9' => 'Ravoz',
        'RZ' => 'Razer',
        '95' => 'Rakuten',
        'RC' => 'RCA Tablets',
        '2R' => 'Reach',
        'RB' => 'Readboy',
        'RE' => 'Realme',
        'R8' => 'RED',
        'RD' => 'Reeder',
        'Z9' => 'REGAL',
        'RP' => 'Revo',
        'RI' => 'Rikomagic',
        'RM' => 'RIM',
        'RN' => 'Rinno',
        'RX' => 'Ritmix',
        'R7' => 'Ritzviva',
        'RV' => 'Riviera',
        '6R' => 'Rivo',
        'RR' => 'Roadrover',
        'R1' => 'Rokit',
        'RK' => 'Roku',
        'R3' => 'Rombica',
        'R5' => 'Ross&Moor',
        'RO' => 'Rover',
        'R6' => 'RoverPad',
        'RQ' => 'RoyQueen',
        'RT' => 'RT Project',
        'RG' => 'RugGear',
        'RU' => 'Runbo',
        'RL' => 'Ruio',
        'RY' => 'Ryte',
        'X5' => 'Saba',
        '8L' => 'S-TELL',
        '89' => 'Seatel',
        'X1' => 'Safaricom',
        'SG' => 'Sagem',
        '4L' => 'Salora',
        'SA' => 'Samsung',
        'S0' => 'Sanei',
        '12' => 'Sansui',
        'SQ' => 'Santin',
        'SY' => 'Sanyo',
        'S9' => 'Savio',
        'Y4' => 'SCBC',
        'CZ' => 'Schneider',
        'G8' => 'SEG',
        'SD' => 'Sega',
        '9G' => 'Selenga',
        'SV' => 'Selevision',
        'SL' => 'Selfix',
        '0S' => 'SEMP TCL',
        'S1' => 'Sencor',
        'SN' => 'Sendo',
        '01' => 'Senkatel',
        'S6' => 'Senseit',
        'EW' => 'Senwa',
        '24' => 'Seeken',
        '61' => 'Seuic',
        'SX' => 'SFR',
        'SH' => 'Sharp',
        '7S' => 'Shift Phones',
        'RS' => 'Shtrikh-M',
        '3S' => 'Shuttle',
        '13' => 'Sico',
        'SI' => 'Siemens',
        '1S' => 'Sigma',
        '70' => 'Silelis',
        'SJ' => 'Silent Circle',
        '10' => 'Simbans',
        '98' => 'Simply',
        '52' => 'Singtech',
        '31' => 'Siragon',
        '83' => 'Sirin labs',
        'GK' => 'SKG',
        'SW' => 'Sky',
        'SK' => 'Skyworth',
        '14' => 'Smadl',
        '19' => 'Smailo',
        'SR' => 'Smart Electronic',
        '49' => 'Smart',
        '47' => 'SmartBook',
        '3B' => 'Smartab',
        '80' => 'SMARTEC',
        'SC' => 'Smartfren',
        'S7' => 'Smartisan',
        '1Q' => 'Smotreshka',
        'SF' => 'Softbank',
        '9L' => 'SOLE',
        'JL' => 'SOLO',
        '16' => 'Solone',
        'OI' => 'Sonim',
        'SO' => 'Sony',
        'SE' => 'Sony Ericsson',
        'X2' => 'Soundmax',
        '8S' => 'Soyes',
        '77' => 'SONOS',
        'PK' => 'Spark',
        'FS' => 'SPC',
        '6S' => 'Spectrum',
        '43' => 'Spectralink',
        'SP' => 'Spice',
        '84' => 'Sprint',
        'QS' => 'SQOOL',
        'S4' => 'Star',
        'OL' => 'Starlight',
        '18' => 'Starmobile',
        '2S' => 'Starway',
        '45' => 'Starwind',
        'SB' => 'STF Mobile',
        'S8' => 'STK',
        'GQ' => 'STG Telecom',
        'S2' => 'Stonex',
        'ST' => 'Storex',
        '71' => 'StrawBerry',
        '96' => 'STRONG',
        '69' => 'Stylo',
        '9S' => 'Sugar',
        '06' => 'Subor',
        'SZ' => 'Sumvision',
        '0H' => 'Sunstech',
        'S3' => 'SunVan',
        '5S' => 'Sunvell',
        '5Y' => 'Sunny',
        'W8' => 'SUNWIND',
        'SU' => 'SuperSonic',
        '79' => 'SuperTab',
        'S5' => 'Supra',
        'ZS' => 'Suzuki',
        '0W' => 'Swipe',
        'SS' => 'SWISSMOBILITY',
        '1W' => 'Swisstone',
        'W7' => 'SWTV',
        'SM' => 'Symphony',
        '4S' => 'Syrox',
        'TM' => 'T-Mobile',
        'TK' => 'Takara',
        '73' => 'Tambo',
        '9N' => 'Tanix',
        'U5' => 'Taiga System',
        'T5' => 'TB Touch',
        'TC' => 'TCL',
        'T0' => 'TD Systems',
        'H4' => 'Technicolor',
        'Z5' => 'Technika',
        'TX' => 'TechniSat',
        'TT' => 'TechnoTrend',
        'TP' => 'TechPad',
        '9E' => 'Techwood',
        'T7' => 'Teclast',
        'TB' => 'Tecno Mobile',
        '91' => 'TEENO',
        '2L' => 'Tele2',
        'TL' => 'Telefunken',
        'TG' => 'Telego',
        'T2' => 'Telenor',
        'TE' => 'Telit',
        '65' => 'Telia',
        'PW' => 'Telpo',
        'TD' => 'Tesco',
        'TA' => 'Tesla',
        '9T' => 'Tetratab',
        'TZ' => 'teXet',
        '29' => 'Teknosa',
        'T4' => 'ThL',
        'TN' => 'Thomson',
        'O0' => 'Thuraya',
        'TI' => 'TIANYU',
        '8T' => 'Time2',
        'TQ' => 'Timovi',
        '2T' => 'Tinai',
        'TF' => 'Tinmo',
        'TH' => 'TiPhone',
        'Y3' => 'TOKYO',
        'T1' => 'Tolino',
        '0T' => 'Tone',
        'TY' => 'Tooky',
        'T9' => 'Top House',
        'DK' => 'Topelotek',
        '42' => 'Topway',
        'TO' => 'Toplux',
        '7T' => 'Torex',
        'TS' => 'Toshiba',
        'T8' => 'Touchmate',
        '5R' => 'Transpeed',
        'T6' => 'TrekStor',
        'T3' => 'Trevi',
        'TJ' => 'Trifone',
        'Q5' => 'Trident',
        '4T' => 'Tronsmart',
        '11' => 'True',
        'JT' => 'True Slim',
        'J1' => 'Trio',
        '5C' => 'TTEC',
        'TU' => 'Tunisie Telecom',
        '1T' => 'Turbo',
        'TR' => 'Turbo-X',
        '5X' => 'TurboPad',
        '5T' => 'TurboKids',
        'TV' => 'TVC',
        'TW' => 'TWM',
        'Z1' => 'TWZ',
        '6T' => 'Twoe',
        '15' => 'Tymes',
        'UC' => 'U.S. Cellular',
        'UG' => 'Ugoos',
        'U1' => 'Uhans',
        'UH' => 'Uhappy',
        'UL' => 'Ulefone',
        'UA' => 'Umax',
        'UM' => 'UMIDIGI',
        'UZ' => 'Unihertz',
        '3Z' => 'UZ Mobile',
        'UX' => 'Unimax',
        'US' => 'Uniscope',
        'U2' => 'UNIWA',
        'UO' => 'Unnecto',
        'UU' => 'Unonu',
        'UN' => 'Unowhy',
        'UK' => 'UTOK',
        '3U' => 'IUNI',
        'UT' => 'UTStarcom',
        '6U' => 'UTime',
        '5V' => 'VAIO',
        'WV' => 'VAVA',
        'VA' => 'Vastking',
        'VP' => 'Vargo',
        'VB' => 'VC',
        'VN' => 'Venso',
        'VQ' => 'Vega',
        '4V' => 'Verico',
        'V4' => 'Verizon',
        'VR' => 'Vernee',
        'VX' => 'Vertex',
        'VE' => 'Vertu',
        'VL' => 'Verykool',
        'V8' => 'Vesta',
        'VT' => 'Vestel',
        '48' => 'Vexia',
        'V6' => 'VGO TEL',
        'VD' => 'Videocon',
        'VW' => 'Videoweb',
        'VS' => 'ViewSonic',
        'V7' => 'Vinga',
        'V3' => 'Vinsoc',
        '0V' => 'Vipro',
        'VI' => 'Vitelcom',
        '8V' => 'Viumee',
        'V5' => 'Vivax',
        'VV' => 'Vivo',
        '6V' => 'VIWA',
        'VZ' => 'Vizio',
        '9V' => 'Vision Touch',
        'VK' => 'VK Mobile',
        'JM' => 'v-mobile',
        'V0' => 'VKworld',
        'VM' => 'Vodacom',
        'VF' => 'Vodafone',
        'V2' => 'Vonino',
        '1V' => 'Vontar',
        'VG' => 'Vorago',
        '2V' => 'Vorke',
        'V1' => 'Voto',
        'Z7' => 'VOX',
        'VO' => 'Voxtel',
        'VY' => 'Voyo',
        'VH' => 'Vsmart',
        'V9' => 'Vsun',
        'VU' => 'Vulcan',
        '3V' => 'VVETIME',
        'WA' => 'Walton',
        'WM' => 'Weimei',
        'WE' => 'WellcoM',
        'W6' => 'WELLINGTON',
        'WD' => 'Western Digital',
        'WT' => 'Westpoint',
        'WY' => 'Wexler',
        '3W' => 'WE',
        'WP' => 'Wieppo',
        'W2' => 'Wigor',
        'WI' => 'Wiko',
        'WF' => 'Wileyfox',
        'WS' => 'Winds',
        'WN' => 'Wink',
        '9W' => 'Winmax',
        'W5' => 'Winnovo',
        'WU' => 'Wintouch',
        'W0' => 'Wiseasy',
        '2W' => 'Wizz',
        'W4' => 'WIWA',
        'WL' => 'Wolder',
        'WG' => 'Wolfgang',
        'WO' => 'Wonu',
        'W1' => 'Woo',
        'WR' => 'Wortmann',
        'WX' => 'Woxter',
        'X3' => 'X-BO',
        'XT' => 'X-TIGI',
        'XV' => 'X-View',
        'X4' => 'X.Vision',
        'XG' => 'Xgody',
        'QX' => 'XGIMI',
        'XL' => 'Xiaolajiao',
        'XI' => 'Xiaomi',
        'XN' => 'Xion',
        'XO' => 'Xolo',
        'XR' => 'Xoro',
        'XS' => 'Xshitou',
        '4X' => 'Xtouch',
        'X8' => 'Xtratech',
        'YD' => 'Yandex',
        'YA' => 'Yarvik',
        'Y2' => 'Yes',
        'YE' => 'Yezz',
        'YK' => 'Yoka TV',
        'YO' => 'Yota',
        'YT' => 'Ytone',
        'Y1' => 'Yu',
        'Y0' => 'YUHO',
        'YN' => 'Yuno',
        'YU' => 'Yuandao',
        'YS' => 'Yusun',
        'YJ' => 'YASIN',
        'YX' => 'Yxtel',
        '0Z' => 'Zatec',
        '2Z' => 'Zaith',
        'PZ' => 'Zebra',
        'ZE' => 'Zeemi',
        'ZN' => 'Zen',
        'ZK' => 'Zenek',
        'ZL' => 'Zentality',
        'ZF' => 'Zfiner',
        'ZI' => 'Zidoo',
        'FZ' => 'ZIFRO',
        'ZX' => 'Ziox',
        'ZO' => 'Zonda',
        'ZP' => 'Zopo',
        'ZT' => 'ZTE',
        'ZU' => 'Zuum',
        'ZY' => 'Zync',
        'ZQ' => 'ZYQ',
        'Z4' => 'ZH&K',
        'OW' => 'öwn',
        // legacy brands, might be removed in future versions
        'WB' => 'Web TV',
        'XX' => 'Unknown',
    ];


    $operatingSystems = [
        'AIX' => 'AIX',
        'AND' => 'Android',
        'AMG' => 'AmigaOS',
        'ATV' => 'tvOS',
        'ARL' => 'Arch Linux',
        'BTR' => 'BackTrack',
        'SBA' => 'Bada',
        'BEO' => 'BeOS',
        'BLB' => 'BlackBerry OS',
        'QNX' => 'BlackBerry Tablet OS',
        'BMP' => 'Brew',
        'CAI' => 'Caixa Mágica',
        'CES' => 'CentOS',
        'COS' => 'Chrome OS',
        'CYN' => 'CyanogenMod',
        'DEB' => 'Debian',
        'DEE' => 'Deepin',
        'DFB' => 'DragonFly',
        'DVK' => 'DVKBuntu',
        'FED' => 'Fedora',
        'FEN' => 'Fenix',
        'FOS' => 'Firefox OS',
        'FIR' => 'Fire OS',
        'FRE' => 'Freebox',
        'BSD' => 'FreeBSD',
        'FYD' => 'FydeOS',
        'GNT' => 'Gentoo',
        'GRI' => 'GridOS',
        'GTV' => 'Google TV',
        'HPX' => 'HP-UX',
        'HAI' => 'Haiku OS',
        'IPA' => 'iPadOS',
        'HAR' => 'HarmonyOS',
        'HAS' => 'HasCodingOS',
        'IRI' => 'IRIX',
        'INF' => 'Inferno',
        'JME' => 'Java ME',
        'KOS' => 'KaiOS',
        'KNO' => 'Knoppix',
        'KBT' => 'Kubuntu',
        'LIN' => 'GNU/Linux',
        'LBT' => 'Lubuntu',
        'LOS' => 'Lumin OS',
        'VLN' => 'VectorLinux',
        'MAC' => 'Mac',
        'MAE' => 'Maemo',
        'MAG' => 'Mageia',
        'MDR' => 'Mandriva',
        'SMG' => 'MeeGo',
        'MCD' => 'MocorDroid',
        'MIN' => 'Mint',
        'MLD' => 'MildWild',
        'MOR' => 'MorphOS',
        'NBS' => 'NetBSD',
        'MTK' => 'MTK / Nucleus',
        'MRE' => 'MRE',
        'WII' => 'Nintendo',
        'NDS' => 'Nintendo Mobile',
        'OS2' => 'OS/2',
        'T64' => 'OSF1',
        'OBS' => 'OpenBSD',
        'OWR' => 'OpenWrt',
        'ORD' => 'Ordissimo',
        'PCL' => 'PCLinuxOS',
        'PSP' => 'PlayStation Portable',
        'PS3' => 'PlayStation',
        'RHT' => 'Red Hat',
        'ROS' => 'RISC OS',
        'ROK' => 'Roku OS',
        'RSO' => 'Rosa',
        'REM' => 'Remix OS',
        'REX' => 'REX',
        'RZD' => 'RazoDroiD',
        'SAB' => 'Sabayon',
        'SSE' => 'SUSE',
        'SAF' => 'Sailfish OS',
        'SEE' => 'SeewoOS',
        'SLW' => 'Slackware',
        'SOS' => 'Solaris',
        'SYL' => 'Syllable',
        'SYM' => 'Symbian',
        'SYS' => 'Symbian OS',
        'S40' => 'Symbian OS Series 40',
        'S60' => 'Symbian OS Series 60',
        'SY3' => 'Symbian^3',
        'TDX' => 'ThreadX',
        'TIZ' => 'Tizen',
        'TOS' => 'TmaxOS',
        'UBT' => 'Ubuntu',
        'WAS' => 'watchOS',
        'WTV' => 'WebTV',
        'WHS' => 'Whale OS',
        'WIN' => 'Windows',
        'WCE' => 'Windows CE',
        'WIO' => 'Windows IoT',
        'WMO' => 'Windows Mobile',
        'WPH' => 'Windows Phone',
        'WRT' => 'Windows RT',
        'XBX' => 'Xbox',
        'XBT' => 'Xubuntu',
        'YNS' => 'YunOs',
        'IOS' => 'iOS',
        'POS' => 'palmOS',
        'WOS' => 'webOS',
    ];

	$return['ip'] = '';
	$return['visit_date'] = '';
	$return['visit_time'] = '';
	$return['browser'] = '';
	$return['device'] = '';
	$return['brand'] = '';
	$return['os'] = '';

	$sql = "select * from {$wpdb->prefix}matomo_log_visit v where v.idvisit = $id";
	$visit_ids = $wpdb->get_results($sql);


	if (!empty($visit_ids) && is_array($visit_ids) && count($visit_ids) > 0) {
		$ip = @inet_ntop($visit_ids[0]->location_ip);
		$return['ip'] = ($ip === false ? '' : $ip); 

		$visit_first_action_time = $visit_ids[0]->visit_first_action_time;

		$return['visit_date'] = date('m/d/Y', strtotime($visit_first_action_time));
		$return['visit_time'] = date('h:m:s', strtotime($visit_first_action_time));

		$config_browser_name = $visit_ids[0]->config_browser_name;
		$config_browser_version = $visit_ids[0]->config_browser_version;

		foreach ($browserFamilies as $key => $family) {
			if (in_array($config_browser_name, $family)) {
				$return['browser'] = $key . ' ' . $config_browser_version;
			}
		}

		$config_device_brand = $visit_ids[0]->config_device_brand;
		$config_device_model = $visit_ids[0]->config_device_model;
		$config_device_type = $visit_ids[0]->config_device_type;

		if (in_array($config_device_type, $deviceTypes)) {
			$return['device'] = array_search($config_device_type, $deviceTypes);
		}

		if (array_key_exists($config_device_brand, $deviceBrands)) {
			$return['brand'] = $deviceBrands[$config_device_brand];
		}
		if ($config_device_model != 'generic desktop') {
			$return['brand'] = trim($return['brand'] . ' ' . $config_device_model);
		}

		$config_os = $visit_ids[0]->config_os;
		$config_os_version = $visit_ids[0]->config_os_version;


		if (array_key_exists($config_os, $operatingSystems)) {
			$return['os'] = $operatingSystems[$config_os];
			$return['os'] = trim($return['os'] . ' ' . $config_os_version);
		}


	}

	return $return;

}






function binaryToString($binary)
{
    $binaries = explode(' ', $binary);
 
    $string = null;
    foreach ($binaries as $binary) {
        $string .= pack('H*', dechex(bindec($binary)));
    }
 
    return $string;    
}



function get_order_zone($id) {

	global $woocommerce;
	global $wpdb;


	$order = wc_get_order($id);

	$shipping_country    = $order->get_shipping_country();
	$shipping_postcode   = $order->get_shipping_postcode();
	$shipping_state      = $order->get_shipping_state();

	$zone = WC_Shipping_Zones::get_zone_matching_package(array('destination' => array('country' => $shipping_country, 'state' => $shipping_state, 'postcode' => $shipping_postcode)));

	return (int)$zone->get_id();
}





function getOrderItemShippingValues($order_id, $item_id, $default_shipping_method = 'flat_rate') {

	global $woocommerce;
	global $wpdb;

	$shipping_cost = 0;
	$qty = 0;


	$product_id = $wpdb->get_var("
		select max(m.meta_value) product_id
		from {$wpdb->prefix}woocommerce_order_itemmeta m
		where 1=1
		and m.meta_key in ('_product_id')
		and m.order_item_id = $item_id
	");
	//and m.meta_key in ('_product_id', '_variation_id')

	$order_instance_id = $wpdb->get_var("
		select meta_value from wp_woocommerce_order_itemmeta 
		where order_item_id in (
			select order_item_id from wp_woocommerce_order_items where order_id = $order_id
		)
		and meta_key = 'instance_id'
		limit 0, 1
	");

	$qty = (int)$wpdb->get_var("
		select meta_value from wp_woocommerce_order_itemmeta 
		where order_item_id in (
			select order_item_id from wp_woocommerce_order_items where order_id = $order_id
		)
		and order_item_id = $item_id
		and meta_key = '_qty'
		limit 0, 1
	");


	$order_zone = get_order_zone($order_id);


	$product = wc_get_product($product_id);


	if ( empty($product ) ) {
		return 0;
	}

	$product_class_id = $product->get_shipping_class_id();
	$product_shipclass = $product->get_shipping_class();

	$shipping_zone = new WC_Shipping_Zone($order_zone);
	$shipping_methods = $shipping_zone->get_shipping_methods( true, 'values' );

	foreach ( $shipping_methods as $instance_id => $shipping_method ) {

		$rate_id = $shipping_method->get_rate_id();

		$method_id = explode( ':', $rate_id);
		$method_id = reset($method_id);

		if( $instance_id == $order_instance_id || (empty($order_instance_id) && $default_shipping_method === $method_id) ) {

			// Get shipping method settings data
			$data = $shipping_method->instance_settings;

			// For a defined shipping class
			if( isset($product_class_id) && ! empty($product_class_id) && isset($data['class_cost_'.$product_class_id]) && $data['class_cost_'.$product_class_id] > 0 ) {
				$cost = $data['class_cost_'.$product_class_id];
			}
			// For no defined shipping class when "no class cost" is defined
			elseif( isset($product_class_id) && empty($product_class_id) && isset($data['no_class_cost']) && $data['no_class_cost'] > 0 ) {
				$cost = $data['no_class_cost'];
			} 
			// When there is no defined shipping class and when "no class cost" is defined
			else {
				$cost = $data['cost'];
			}

			if ( $cost > 0) {
				$shipping_cost = $cost;
			}
		}

	}

	$order = wc_get_order( $order_id );
	$order_shipping_total = $order->get_total_shipping();

	$max_price = 0;
	$max_price_item_id = 0;
	foreach ( $order->get_items() as $id => $item ) {
		$quantity = $item->get_quantity();
		$subtotal = $item->get_subtotal();
		$total = $item->get_total();
		if ( $max_price < $total / $quantity ) {
			$max_price = $total / $quantity;
			$max_price_item_id = $id;
		}
	}

	if ( $max_price_item_id != $item_id ) {
		$shipping_cost = 0;
	}



	$project = get_option('options_legacy_scs_client_id');

	// Strange customization for shipping
	if ( $project == 'ATRU' && $product_shipclass == 'diffuser' ) {
		return 14.95 * $qty;
	} else if ( $project == 'ATRU' && $product_shipclass == 'diff-oil' ) {
		return 3.6 * $qty;
	}



	return $shipping_cost * $qty;

}


?>