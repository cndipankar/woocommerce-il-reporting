<?php

    function get_fin_backorder($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

	$order_status_selection = array('wc-backordered');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'subscription-init', 'subscription-renewal', 'backorder');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal , backorder




        // get the table rows
        //$header = array('SKU', 'Description', 'Ship Code', 'OrderNo', 'Qty', 'Name', 'Ref. Customer', 'Order Date', 'Phone', 'Email');
	$header = array(
			'Project',
			'OrderNo', 
			'Order Date', 
			'Order Type',
			'Order Source',	
			'Traffic Source',
			'SKU', 
			'Description', 
			'Qty', 
			'Name', 
			'Ref. Customer', 
			'Phone', 
			'Email'
		);
	
	
	$sql = "
		select p.ID, p.post_date, p.post_type, p.post_status, m.meta_value paid_date
		from {$wpdb->posts} p, {$wpdb->postmeta} m
		where p.ID = m.post_id
		and p.post_type in ('".implode("','", $order_type_selection)."')
		and p.post_status in ('".implode("','", $order_status_selection)."') 
		and m.meta_value >= '$data1 00:00:00' and m.meta_value <= '$data2 23:59:59'
		order by m.meta_value desc, p.ID desc
	";


	$project = get_option('options_legacy_scs_client_id');


	$orders_ids = $wpdb->get_results($sql);

	foreach ($orders_ids as $o) {

		$oid = $o->ID;

		$test_order = get_post_meta($oid,'_test_order',true);
		if ($test_order == 'yes')
			continue;

		if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
			continue;

		
		$source = getOrderType($oid);
		$telemarket = getSource($oid);
		$funnel_traffic_source = getFunnel($oid, 's');


		$name = get_post_meta($oid,'_billing_first_name',true).' '.get_post_meta($oid,'_billing_last_name',true);

		$ref_customer = get_post_meta($oid,'_customer_user',true);

		$order_date = date('m/d/Y',strtotime($o->post_date));
		$phone = get_post_meta($oid,'_billing_phone',true);
		$email = get_post_meta($oid,'_billing_email',true);



		$shipping_item_id = $wpdb->get_var("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_id=$oid and order_item_type='shipping'");
		$method_id = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='method_id' and order_item_id=$shipping_item_id");
		$instance_id = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='instance_id' and order_item_id=$shipping_item_id");

		$zone_id = $wpdb->get_col( "
			SELECT wszm.zone_id
			FROM {$wpdb->prefix}woocommerce_shipping_zone_methods as wszm
			WHERE wszm.instance_id = '$instance_id'
			AND wszm.method_id LIKE '$method_id'
		");
		$zone_id = reset($zone_id);

		if( empty($zone_id) ) {
			$zone_name = "";	// Error! doesn't exist
		} elseif( $zone_id == 0 ) {
			$zone_name = "";	// All Other countries
		} else {
			$zone_name = $wpdb->get_col( "
				SELECT wsz.zone_name
				FROM {$wpdb->prefix}woocommerce_shipping_zones as wsz
				WHERE wsz.zone_id = '$zone_id'
			");
			$zone_name = reset($zone_name);
		}

		$ship_code = $zone_name;




		$sql = "
			select  order_item_id, order_item_name, order_item_type 
			from {$wpdb->prefix}woocommerce_order_items i
			where i.order_id = $oid
			order by i.order_item_type, i.order_item_id
		";

		$items_ids = $wpdb->get_results($sql);

		foreach($items_ids as $item) {


			if (getItemType($item->order_item_id) != 'line_item') {
				continue;
			}


			$sku = getItemSKU($item->order_item_id);
			$description = getItemDescription($item->order_item_id);
			$qty = getItemQty($item->order_item_id);


			$rows[] = array(
				$project, 
				$oid, 
				$order_date, 
				$source,
				$telemarket,
				$funnel_traffic_source,
				$sku, 
				$description, 
				$qty, 
				$name, 
				$ref_customer, 
				$phone, 
				$email
			);
		}

	}



		
        $units = array('', '', '', '', '', '', '', '', '', '', '', '', '');
        $total = array('', '', '', '', '', '', '', '',  1, '', '', '', '');
        


        // Array data
        $result['all']['array']['header']       = $header;
        $result['all']['array']['unit']         = $units;
        $result['all']['array']['total']        = $total;
        $result['all']['array']['rows']         = $rows;
        $result['all']['array']['sheetname']    = 'Worksheet';
        $result['all']['array']['title']        = 'Back Orders by SKU';
        

        return json_encode($result);


    }

?>
