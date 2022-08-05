<?php

function get_fin_tax_details($data1, $data2) {

	global $wpdb;
		
	$start_date = $data1;
	$end_date = $data2;

        // get the table rows

    $header=[
		'Project',
		'Order Number',
		'Order Date',
		'State',
		'Zip Code',
		'SKU # and Description',
		'Sku Quantity',
		'Item Quantity',
		'Tax Label',
		'Tax Rate',
		'Gross Amount',
		'Products',
		'Shipping',
		'Tax',
		'Customer Name',
		'Customer Address 1',
		'Customer Address 2',
		'Customer City',
		'Customer Zip Code',
		'Customer State',
		'Customer Country'
	];


	
	$order_status_selection = array('wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'subscription-init', 'subscription-renewal');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal


	$project = get_option('options_legacy_scs_client_id');

	$sql = "
		select p.ID, p.post_date, p.post_status 
		from {$wpdb->posts} p 
		left join {$wpdb->postmeta} m on p.ID = m.post_id and m.meta_key = '_paid_date'
		where 1=1
		and p.post_type in ('".implode("','", $order_type_selection)."')
		and p.post_status in ('".implode("','", $order_status_selection)."') 
		and m.meta_value >= '$data1 00:00:00' and m.meta_value <= '$data2 23:59:59'
		order by ID desc
	";
	$orders_ids = $wpdb->get_results($sql);

	foreach($orders_ids as $o){

		$oid = $o->ID;

		$test_order = get_post_meta($oid,'_test_order',true);
		if ($test_order == 'yes')
			continue;

		if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
			continue;


		$order_date = date('m/d/Y',strtotime($o->post_date));
		$state = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_shipping_state'");
		$zip_code = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_shipping_postcode'");
		

		$label_details = "";
		$rate_details = "";
		$total_rate = "";


		$taxes = $wpdb->get_results("select order_item_id, order_item_name from {$wpdb->prefix}woocommerce_order_items where order_item_type='tax' and order_id=$oid");
		foreach ($taxes as $tax_details) {
			$tax_name = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='label' and order_item_id=" . $tax_details->order_item_id);
			$label_details .= "; " . $tax_name;
			if ( $tax_name == 'Special Sales Tax' ) {
				$label_details .= " ($tax_details->order_item_name)";
			}
			$this_tax_rate = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='rate_percent' and order_item_id=" . $tax_details->order_item_id);
			$rate_details .= "; " . $this_tax_rate;
			$total_rate = $total_rate + $this_tax_rate;
		}

		$items = $wpdb->get_col("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_type='line_item' and order_id=$oid");

		foreach ($items as $item) {

			$custom_tax_name = '';
			$custom_tax_rate = '';

			//$item_tax_value = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_line_tax' and order_item_id=$item");


			$sku = getItemSKU($item);
			$title = getItemDescription($item);
			$sku_and_desc = $sku.' '.$title;

			$qty = getItemQty($item);

			$legacy_product_quantity = (int)get_post_meta(max($pid, $vid), 'legacy_product_quantity', true);
			if (empty($legacy_product_quantity)) {
				$number_of_item = $qty;
			} else {
				$number_of_item = $qty * (int)$legacy_product_quantity;
			}


			$orderItemValues = getOrderItemValues($item, 1);

			$shipping_cost = $orderItemValues['shipping'];
			$gross_amount = $orderItemValues['items'] + $orderItemValues['shipping'];
			$items_amount = $orderItemValues['items'];


			$tax_data = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_line_tax_data' and order_item_id=$item");
			$tax_data = unserialize($tax_data);

			$item_tax_value = 0;
			if (!empty($tax_data) && !empty($tax_data['total']) ) {
				foreach ($tax_data['total'] as $tax_key => $tax_val) {
					$item_tax_value = $item_tax_value + $tax_val;
				}
			}
			

			
			$first_name = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_first_name'");
			$last_name = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_last_name'");
			$customer_name = $first_name.' '.$last_name;
			$customer_address_1 = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_address_1'");
			$customer_address_2 = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_address_2'");
			$customer_city = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_city'");
			$customer_zip_code = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_postcode'");
			$customer_state = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_state'");
			$customer_country = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_billing_country'");
			

			if ( strlen($label_details) > 2 ) {
				$custom_tax_name = substr($label_details, 2);
			}

			/*
			if ( strlen($rate_details) > 2 ) {
				$custom_tax_rate = substr($rate_details, 2);
			}
			*/

			$custom_tax_rate = $total_rate;


			$rows[] = array(
					$project, 
					$oid, 
					$order_date, 
					$state, 
					$zip_code, 
					$sku_and_desc, 
					$qty, 
					$number_of_item, 
					$custom_tax_name, 
					$custom_tax_rate, 
					$gross_amount, 
					$items_amount, 
					$shipping_cost, 
					$item_tax_value, 
					$customer_name, 
					$customer_address_1, 
					$customer_address_2, 
					$customer_city, 
					$customer_zip_code, 
					$customer_state, 
					$customer_country
				);
		}

		$ship_id = $wpdb->get_var("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_type='shipping' and order_id=$oid");

		$orderItemValues = getOrderItemValues($ship_id, 1);

		$shipping_cost = $orderItemValues['shipping'];
		$shipping_tax = $orderItemValues['tax'];



		
		$rows[] = array(
			$project, 
			$oid, 
			$order_date, 
			$state, 
			$zip_code, 
			'Shipping', 
			'', 
			'', 
			'', 
			0, 
			$shipping_cost, 
			0, 
			$shipping_cost, 
			$shipping_tax, 
			$customer_name, 
			$customer_address_1, 
			$customer_address_2, 
			$customer_city, 
			$customer_zip_code, 
			$customer_state, 
			$customer_country
		);

	}

    $units = ['','','','','','','','','','','$','$','$','$','','','','','','','',''];
    $total = ['','','','','','','','','','', 1, 1, 1, 1,'','','','','','','',''];


	// Array data
	$result['all']['array']['header']       = $header;
	$result['all']['array']['unit']         = $units;
	$result['all']['array']['total']        = $total;
	$result['all']['array']['rows']         = $rows;
	$result['all']['array']['sheetname']    = 'Worksheet';
	$result['all']['array']['title']        = '';    
	return json_encode($result);

}

?>
