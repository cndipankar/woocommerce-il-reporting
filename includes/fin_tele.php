<?php

	function get_fin_tele($data1, $data2) {

	global $wpdb;
		
		$start_date = $data1;
		$end_date = $data2;

		$order_status_selection = array('wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
		$order_type_selection = array('shop_order');
		$order_flow_selection = array('onepay', 'multipay-init', 'subscription-init', 'subscription-renewal');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal



		// get the table rows
		$header = array(
			'Project', 
			'OrderNumber', 
 			'OrderDate', 
			'Order Type',
			'Order Source',
			'Traffic Source',
			'Script', 
			'Brand', 
			'Upsell', 
			'Phone', 
			'DetailStatus', 
			'Number Of SKU',
			'Number Of Item',
			'DetailSku', 
			'DetailDescription', 
			'ProductPrice', 
			'Ship', 
			'Tax', 
			'Payment Plan'
		);


		$project = get_option('options_legacy_scs_client_id');


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

		foreach ($orders_ids as $o) {

			$oid = $o->ID;

			$test_order = get_post_meta($oid,'_test_order',true);
			if ($test_order == 'yes')
				continue;

			if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
				continue;

		
			$source = getOrderType($oid);;

			$script = get_post_meta($oid,'scriptcode',true);
			if (empty($script )) {
				$script  = 'WEB';
			}

			$telemarket = getSource($oid);
			$funnel_traffic_source = getFunnel($oid, 's');


			$order_date = date('m/d/Y',strtotime($o->post_date));
			$order_status = str_replace("wc-", "", $o->post_status);

			$customer_phone = get_post_meta($oid,'_billing_phone',true);
			$payment_plan = getPaymentPlan($oid);			

			$sql = "
				select  order_item_id, order_item_name, order_item_type 
				from {$wpdb->prefix}woocommerce_order_items i
				where i.order_id = $oid
				order by i.order_item_type, i.order_item_id
			";

			$items_ids = $wpdb->get_results($sql);

			foreach ($items_ids as $item) {


				//if (getItemType($item->order_item_id) != 'line_item' && getItemType($item->order_item_id) != 'shipping') {
				if (getItemType($item->order_item_id) != 'line_item') {
					continue;
				}

				$isUpsell = isUpsell($item->order_item_id);
				$category = getItemCategory($item->order_item_id);

				$orderItemValues = getOrderItemValues($item->order_item_id, 1);

				$product_amt = $orderItemValues['items'];
				$s_h_amt = $orderItemValues['shipping'];
				$tax_amt = $orderItemValues['tax'];

				$sku = getItemSKU($item->order_item_id);
				$sku_desc = getItemDescription($item->order_item_id);

				$qty = getItemQty($item->order_item_id);

				$pid = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_product_id' and order_item_id=$item->order_item_id");
				$vid = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_variation_id' and order_item_id=$item->order_item_id");

				$legacy_product_quantity = (int)get_post_meta(max($pid, $vid), 'legacy_product_quantity', true);
				if (empty($legacy_product_quantity)) {
					$number_of_item = $qty;
				} else {
					$number_of_item = $qty * (int)$legacy_product_quantity;
				}


				$orderItemShippingValues = getOrderItemShippingValues($oid, $item->order_item_id);

				$tax_shipping = 0;
				if ( $orderItemShippingValues > 0 ) {
					$orderTaxIDs = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_item_type = 'tax' and i.order_id = $oid");
					foreach ($orderTaxIDs as $orderTaxID) {
						$tax_rate = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='rate_percent' and order_item_id=$orderTaxID");
						$tax_shipping = $tax_shipping + round($tax_rate * $orderItemShippingValues / 100, 6);
					}
				}

				//$rows[] = array($project, $oid, $telemarket, $funnel_traffic_source, $script, $category, $isUpsell, $customer_phone, $order_date, $order_status, $qty, $sku, $sku_desc, $product_amt, $s_h_amt, $tax_amt, $payment_plan);
				$rows[] = array(
						$project, 
						$oid, 
						$order_date, 
						$source,
						$telemarket , 
						$funnel_traffic_source, 
						$script, 
						$category, 
						$isUpsell, 
						$customer_phone, 
						$order_status, 
						$qty, 
						$number_of_item, 
						$sku, 
						$sku_desc, 
						$product_amt, 
						$orderItemShippingValues, 
						$tax_amt + $tax_shipping, 
						$payment_plan
					);

			}

		}



		$units = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '$', '$', '$');
		$total = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 1);

		// Array data
		$result['all']['array']['header']	 = $header;
		$result['all']['array']['unit']		 = $units;
		$result['all']['array']['total']	 = $total;
		$result['all']['array']['rows']		 = $rows;
		$result['all']['array']['sheetname']	 = 'Worksheet';
		$result['all']['array']['title']	  = 'Order Details by Date, Promo and Media';
		

		return json_encode($result);

	}

?>
