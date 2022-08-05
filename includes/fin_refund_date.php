<?php


    function get_fin_refund_date($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

        // get the table rows
        $header = array(
		'Project',
 		'Order Number', 
		'Order Date', 
		'ProcessDate', 
		'Script', 
		'Order Type', 
		'Order Source', 
		'Traffic Source', 
		'Payment Plan',
		'Installment numbers', 
		'Sku & Description', 
		'Number Of SKU',
		'Qty Ordered', 
		'Product w/o Discount', 
		'Product Amt', 
		'Discount Amt', 
		'S&P Amt', 
		'Tax Amt', 
		'Total Amt', 
		'Refund 1/ Return 1', 
		'Refund Qty', 
		'Refund ProductAmount', 
		'Refund ShipFee', 
		'Refund Tax', 
		'Over or Under', 
		'Total Refunded', 
		'Product Description', 
		'Customer First', 
		'Customer Last', 
		'Customer Address', 
		'Customer Apt', 
		'Customer City', 
		'Customer State', 
		'Customer Zip', 
		'Customer Phone', 
		'Customer Email', 
		'Ship to First', 
		'Ship to Last', 
		'Ship to Address', 
		'Ship to Apt', 
		'Ship to City', 
		'Ship to State', 
		'Ship to Zip', 
		'Ship Date'
		);
		
        	$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');

		$sql = "
			select ID ,post_date, post_type, post_parent, post_date 
			from {$wpdb->posts} 
			where post_date >= '$data1 00:00:00' and post_date <= '$data2 23:59:59' 
			and post_status in ('".implode("','", $order_status_selection)."')
			and post_type='shop_order_refund'
		";
		$orders_ids = $wpdb->get_results($sql);

		$project = get_option('options_legacy_scs_client_id');

		foreach ($orders_ids as $o) {

			if ( empty(get_post_meta($o->ID,'_refund_amount',true)) || get_post_meta($o->ID,'_refund_amount',true) == 0.00  ) 
				continue;

			$oid = $o->post_parent;
			$refund_oid = $o->ID;

			$test_order = get_post_meta($oid, '_test_order',true);
			if ($test_order == 'yes')
				continue;

			$test_order = get_post_meta($refund_oid, '_test_order',true);
			if ($test_order == 'yes')
				continue;


			$first_name = get_post_meta($oid,'_billing_first_name',true);
			$last_name = get_post_meta($oid,'_billing_last_name',true);
			$address = get_post_meta($oid,'_billing_address_1',true);
			$apt = get_post_meta($oid,'_billing_address_2',true);
			$city = get_post_meta($oid,'_billing_city',true);
			$state = get_post_meta($oid,'_billing_state',true);
			$zip_code = get_post_meta($oid,'_billing_postcode',true);
			$phone =  get_post_meta($oid,'_billing_phone',true); 
			$email =  get_post_meta($oid,'_billing_email',true);


			$shipmentInfo = getShipment($oid);
			$date_shipped = $shipmentInfo['ship_date'];

			$items = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_id = $refund_oid order by i.order_item_type");
			if (!$items) {
				$refund_oid = $oid;
				$items = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_id = $refund_oid order by i.order_item_type");
			}
		
			$initial_order = get_post($oid);
			$order_date = date('m/d/Y',strtotime($initial_order->post_date));

			$payment_plan = getPaymentPlan($oid);
			$instalmentText = '';
			$instalmentNumber = getInstalmentNumber($oid); 
			$instalmentNumbers = getInstalmentNumbers($oid);
			if ( !empty($instalmentNumbers) ) {
				$instalmentText = $instalmentNumbers; 
			}

			$ship_to_first = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_first_name' and post_id=$oid");
			$ship_to_last = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_last_name' and post_id=$oid");
			$ship_to_address = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_address_1' and post_id=$oid");
			$ship_to_apt = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_address_2' and post_id=$oid");
			$ship_to_city = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_city' and post_id=$oid");
			$ship_to_state = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_state' and post_id=$oid");
			$ship_to_zip = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_postcode' and post_id=$oid");
			$ship_to_country = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_country' and post_id=$oid");


			$source = getOrderType($oid);
			$telemarket = getSource($oid);
			$funnel_traffic_source = getFunnel($oid, 's');


			$script = get_post_meta($oid,'scriptcode',true);
			if (empty($script )) {
				$script  = 'WEB';
			}





			foreach ($items as $item) {

				$itemType = getItemType($item);
				if ($itemType == 'tax')
					continue;

				$refund_return = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_qty' and order_item_id=$item");
				if (empty($refund_return)) 
					$refund_return = 1;


				$sku = getItemSKU($item);
				$product_description = getItemDescription($item);
				$number_refunded_item = getItemQty($item);

				if (getItemType($item) == 'shipping') {
					$sku = 'Shipping & Processing';
					$product_description = 'Shipping & Processing';
				}

				$ProcessDate = date('m/d/Y',strtotime($o->post_date));

				$OrderItemValues = getOrderItemValues($item, true);

				$product_amount = $OrderItemValues['items'];
				$tax = $OrderItemValues['tax'];
				$shipping = $OrderItemValues['shipping'];

				$total_refunded = $product_amount + $tax + $shipping;



				// get initial item value

				$pid = getItemProductID($item);

				$item_value = 0;
				$item_tax = 0;
				$item_shipping = 0;

				$refunded_items = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_id = $oid order by i.order_item_type");
				foreach ($refunded_items as $refunded_item) {
					if ($pid == getItemProductID($refunded_item)) {

						$refundedOrderItemValues = getOrderItemValues($refunded_item, true);

						$item_value = $refundedOrderItemValues['items'];
						$item_tax = $refundedOrderItemValues['tax'];
						$item_shipping = $refundedOrderItemValues['shipping'];
						$discount = $refundedOrderItemValues['coupon'];
						$product_without_discount = $refundedOrderItemValues['items_without_discount'];

						$item_total = $item_value + $item_tax + $item_shipping;

						$number_of_sku = getItemQty($refunded_item);
	
						$legacy_product_quantity = (int)get_post_meta($pid, 'legacy_product_quantity', true);
						if (empty($legacy_product_quantity)) {
							$number_of_item = $number_of_sku;
						} else {
							$number_of_item = $number_of_sku * (int)$legacy_product_quantity;
						}
		
						break;
					}
				}

				$over_or_under = ($item_value + $item_tax + $item_shipping) - abs($total_refunded);


				// Get returned items

				$return_data = get_post_meta($oid, 'mwb_wrma_return_product', true);

				$ret_qty = 0;
				if (!empty($return_data)) {
					foreach ($return_data as $ret_date => $ret_info) {
						if (!empty($ret_info['products'])) {
							foreach ($ret_info['products'] as $ret_product) {
								$ret_pid = $ret_ppid = $ret_product['product_id'];
								if($ret_product['variation_id'])
									$ret_pid = $ret_product['variation_id'];
								if ( $pid == $ret_pid)
									$ret_qty = $ret_product['qty'];
							}
						}
					}
				}
				
				$rows[] = array(
					$project,
 					$oid, 
					$order_date, 
					$ProcessDate, 
					$script, 
					$source, 
					$telemarket, 
					$funnel_traffic_source, 
					$payment_plan,
					$instalmentText, 
					$sku, 
					$number_of_sku,
					$number_of_item, 
					$product_without_discount, 
					$item_value, 
					$discount, 
					$item_shipping, 
					$item_tax, 
					$item_total, 
					abs($refund_return) . ' / ' . $ret_qty, 
					abs($number_refunded_item), 
					abs($product_amount),  
					abs($shipping), 
					abs($tax), 
					$over_or_under, 
					abs($total_refunded), 
					$product_description, 
					$first_name, 
					$last_name, 
					$address, 
					$apt, 
					$city, 
					$state, 
					$zip_code, 
					$phone, 
					$email, 
					$ship_to_first, 
					$ship_to_last, 
					$ship_to_address, 
					$ship_to_apt, 
					$ship_to_city, 
					$ship_to_state, 
					$ship_to_zip, 
					$date_shipped
				);
				
			}

		}


        $units = ['', '', '', '', '', '', '', '', '', '', '', '', '', '$', '$', '$', '$', '$', '$', '', '', '$','$','$','$','$','','','','','','','','','','','','','', '', '', '', '', '', ''];
        $total = ['', '', '', '', '', '', '', '', '', '', '', 1, 1, 1, 1, 1, 1, 1, 1, '', 1, 1, 1, 1, 1, 1 , '','','','','','','','','','','','','', '', '', '', '', '', ''];


        // Array data
        $result['all']['array']['header']       = $header;
        $result['all']['array']['unit']         = $units;
        $result['all']['array']['total']        = $total;
        $result['all']['array']['rows']         = $rows;
        $result['all']['array']['sheetname']    = 'Worksheet';
        $result['all']['array']['title']        = 'Payments Refunded by Process date ';
        

        return json_encode($result);

    }

?>