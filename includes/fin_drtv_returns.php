<?php

function get_fin_drtv_returns($data1, $data2) {

	global $wpdb;
		
	$start_date = $data1;
	$end_date = $data2;
	
	$header = [
		'Project #',
		'Received Date',
		'Return Reason',
		'RMA ID',
		'RMA Status',
		'Order ID',
		'Order Date',
		'Order Type',
		'Order Source',
		'Traffic Source',
		'Returned Product ID',
		'Returned Product Description',
		'Product Condition',
		'Rcvd Qty',
		'Sold To Name',
		'Address',
		'Postage Due',
	];
	
	$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');

	$ProjectNumber = get_option('options_legacy_scs_client_id');
	
	$zap ="
		select m.post_id, m.meta_value, str_to_date(m.meta_value,'%d-%m-%Y') as c_date 
		from {$wpdb->postmeta} m, {$wpdb->posts} p
		where p.ID = m.post_id
		and str_to_date(m.meta_value,'%d-%m-%Y') >= '$data1' and str_to_date(m.meta_value,'%d-%m-%Y') <= '$data2' 
		and p.post_status in ('".implode("','", $order_status_selection)."')
		and m.meta_key='mwb_pending_date'
	";
	//echo $zap;die();
	$orders_ids = $wpdb->get_results($zap);
	foreach($orders_ids as $o){
		$oid = $o->post_id;

		$test_order = get_post_meta($oid,'_test_order',true);
		if ($test_order == 'yes')
			continue;


		$recieved_date = date('n.d.Y',strtotime($o->c_date));
		$return_data=get_post_meta($oid,'mwb_wrma_return_product',true);
		$reason = $return_data[$o->meta_value]['reason'];

		//$rma_id = get_post_meta($oid,'mwb_wrma_id',true);
		$rma_id = $oid;

		$source = getOrderType($oid);
		$telemarket = getSource($oid);
		$funnel_traffic_source = getFunnel($oid, 's');

		$rma_status = $return_data[$o->meta_value]['status'];
		foreach($return_data[$o->meta_value]['products'] as $product){
			$pid=$ppid = $product['product_id'];
			if($product['variation_id'])
				$pid = $product['variation_id'];
			$sku  = get_post_meta($pid,'_sku',true);
			if(!$sku)
				$sku  = get_post_meta($ppid,'_sku',true);
			$returned_product_id = $sku;
			$returned_product_desc = $wpdb->get_var("select post_title from {$wpdb->posts} where ID=$ppid");

			$product_condition = '';
			if (strpos(strtoupper($reason), 'UNSELLABLE') !== false) {
				$product_condition = 'Unsellable';
			} else if (strpos(strtoupper($reason), 'SELLABLE') !== false) {
				$product_condition = 'Sellable';
			} 
			$disposition = ''; // need to do
			$qty = $product['qty'];
			$order_date = date('n/d/Y',strtotime($wpdb->get_var("select post_date from {$wpdb->posts} where ID=$oid")));
			$first_name = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_first_name'");
			$last_name = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_last_name'");
			$sold_to_name= $first_name.' '.$last_name;
			
			$customer_address = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_address_1'");
			$customer_address_2 = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_address_2'");
			if($customer_address_2)
				$customer_address = $customer_address.' '.$customer_address_2;
			$customer_city = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_city'");
			$customer_zip_code = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_postcode'");
			$customer_state = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_state'");
			$customer_country = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_country'");
			
			$address = $customer_address.', '.$customer_city .', '.$customer_state.' '.$customer_zip_code.' '.$customer_country;
			
			//$ship_id = $wpdb->get_var("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_type='shipping' and order_id=$oid");
			//$postage_due = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='cost' and order_item_id=$ship_id");

			$postage_due = 0;

			$rows[] = array(
				$ProjectNumber, 
				$recieved_date, 
				$reason, 
				$rma_id, 
				$rma_status, 
				$oid, 
				$order_date, 
				$source,
				$telemarket,
				$funnel_traffic_source, 
				$returned_product_id, 
				$returned_product_desc, 
				$product_condition, 
				$qty, 
				$sold_to_name, 
				$address, 
				$postage_due
			);
		}
	}
	
	// get the table rows
	
	$units = ['','','','','','','','','','','','','','','','','$'];
	$total = [];


		// Array data
	$result['all']['array']['header']       = $header;
	$result['all']['array']['unit']		= $units;
	$result['all']['array']['total']	= $total;
	$result['all']['array']['rows']		= $rows;
	$result['all']['array']['sheetname']    = 'Worksheet';
	$result['all']['array']['title']	= 'DRTV Ideal Living Daily Returns Report';
		

	return json_encode($result);

}

?>
