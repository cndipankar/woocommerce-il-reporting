<?php

    function get_mojo_sku_site_data($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;


	$order_status_selection = array('wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'subscription-init', 'subscription-renewal');	// All values: onepay; multipay-init; multipay-pay; subscription-init; subscription-subscription; subscription-trial; subscription-renewal; backorder


        // get the table rows
        $header = array(
		'Version Name',
		'SKU',
		'Product Name',
		'Views',
		'Orders',
		'Units',
		'Conversion Rate SKU',
		'Conversion Rate Order',
		'Average Sale w P&H',
		'Average Sale w/o P&H',
		'Total Sales w P&H',
		'Total Sales w/o P&H'
	);
		
	$sql = "
		select p.ID, p.post_date 
		from {$wpdb->posts} p
		where 1=1
		and p.post_type in ('".implode("','", $order_type_selection)."')
		and p.post_status in ('".implode("','", $order_status_selection)."') 
		and p.post_date >= '$data1 00:00:00' and p.post_date <= '$data2 23:59:59' 
	";

	$orders = $wpdb->get_results($sql);
	$products_ar = [];
	$orders_ar = [];

	$total_order_count = 0;

	if ( $orders ) {

		foreach($orders as $o){

			$oid = $o->ID;

			$test_order = get_post_meta($oid,'_test_order',true);
			if ($test_order == 'yes')
				continue;

			$order_type = getOrderTypeDetailed($oid);
			if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
				continue;

			$total_order_count ++;

			$funnel = getFunnel($oid);
			$c = trim(getFunnel($oid, 'c'));
			$order_date = date('d/m/Y',strtotime($o->post_date)); 
			$order_timestamp = strtotime(date('Y-m-d',strtotime($o->post_date))); 

			if (empty($orders_ar[$funnel]['orders'])){
				$orders_ar[$funnel]['orders'] = 1;
			} else {
				$orders_ar[$funnel]['orders']++;
			}

			$user_agent = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_customer_user_agent' and post_id=$oid");
			$items = $wpdb->get_col("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_type='line_item' and order_id=$oid");

			foreach($items as $item){

				//$pid = $wpdb->get_var("select max(meta_value) from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key in ('_product_id', '_variation_id') and order_item_id=$item");
				//$sku = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_sku' and post_id=$pid");
				//$sku_name = $wpdb->get_var("select post_title from {$wpdb->posts} where ID = $pid");

				$sku = getItemSKU($item);
				$qty = getItemQty($item);
				$sku_name = getItemDescription($item);

				$orderItemValues = getOrderItemValues($item, 1);
				$orderItemShippingValues = getOrderItemShippingValues($oid, $item);

				if ( empty($products_ar[$funnel][$sku]) ) {
					$products_ar[$funnel][$sku]['name'] = $sku_name;
					$products_ar[$funnel][$sku]['date'] = $order_date;
					$products_ar[$funnel][$sku]['orders'] = 0;
					$products_ar[$funnel][$sku]['units'] = 0;
					$products_ar[$funnel][$sku]['val_item'] = 0;
					$products_ar[$funnel][$sku]['val_shipping'] = 0;
				}

				$products_ar[$funnel][$sku]['orders']++;
				$products_ar[$funnel][$sku]['units'] = $products_ar[$funnel][$sku]['units'] + $qty;

				$products_ar[$funnel][$sku]['val_item'] = $products_ar[$funnel][$sku]['val_item'] + $orderItemValues['items'];
				$products_ar[$funnel][$sku]['val_shipping'] = $products_ar[$funnel][$sku]['val_shipping'] +  $orderItemShippingValues;
				$products_ar[$funnel][$sku]['val_tax'] = $products_ar[$funnel][$sku]['val_tax'] + $orderItemValues['tax'];	
					
			}
		}
	}

	krsort($products_ar, SORT_STRING);


	foreach ( $products_ar as $kfunnel => $data1) {
			foreach ( $data1 as $ksku => $val) {

				$views = $orders_ar[$kfunnel]['orders'];
				
				$total_sales_w_ph = $val['val_item'] + $val['val_shipping'];
				$total_sales_wo_ph = $val['val_item'];

				$average_sale_w_ph = 0;
				$average_sale_wo_ph = 0;
				if ( $val['orders'] > 0 ) {
					$average_sale_w_ph  = $total_sales_w_ph / $val['orders'];
					$average_sale_wo_ph = $total_sales_wo_ph / $val['orders'];
				}


				$conversion_rate_sku = 0;
				if ( $views > 0 ) {
					$conversion_rate_sku = $val['orders'] / $views * 100;
				}


				$conversion_rate_order = 0;
				if ( $total_order_count > 0 ) {
					$conversion_rate_order = $val['orders'] / $total_order_count * 100;
				}


				$rows[] = [
					$kfunnel,
					$ksku,
					$val['name'],
					$views,
					$val['orders'],
					$val['units'],
					$conversion_rate_sku,
					$conversion_rate_order,
					$average_sale_w_ph,
					$average_sale_wo_ph,
					$total_sales_w_ph,
					$total_sales_wo_ph
				];


			}
	}


	$units = array('', '', '', '', '', '', '%', '%', '$', '$', '$', '$');
        $total = array('', '', '',  1,  1,  1, '', '', 1, 1, 1, 1);

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
