<?php

    function get_fin_orders($data1, $data2) {



	global $wpdb;
		
        	$start_date = $data1;
       	 	$end_date = $data2;

		$order_status_selection = array('wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel'); // 'wc-cancelled'
		$order_type_selection = array('shop_order');
		$order_flow_selection = array('onepay', 'multipay-init', 'multipay-pay', 'subscription-init', 'subscription-trial', 'subscription-renewal', 'backorder');	// All values: onepay; multipay-init; multipay-pay; subscription-init; subscription-subscription; subscription-trial; subscription-renewal; backorder

		$external_states_array = array('HI', 'AK');

	        $header = array(
			'Project',
			'DayOfWeek', 
			'OrderDate', 
			'Orders', 
			'Total Demand', 
			'Product Demand', 
			'Shipping Demand', 
			'Tax Demand', 
			'Avg Demand with Tax', 
			'Trial Demand', 
			'Instalment Demand', 
			'Total Demand Net of Tax', 
			'$ Pre Bill Cancel', 
			'$ Post Bill Cancel', 
			'Lost Demand', 
			'Gross Revenue', 
			'Gross Revenue PCT', 
			'Gross Cash', 
			'Refund', 
			'Refund PCT', 
			'Net Cash', 
			'Net Cash Over Demand', 

			'# Orders Processed', 
			'$ Orders Processed', 
			'Pre Bill Cancel', 

			'# Orders CC', 
			'$ Orders CC', 
			'CC PCT', 
			'# Orders Paypal', 
			'$ Orders Paypal',
			'Paypal PCT',
			'# Orders Affirm', 
			'$ Orders Affirm', 
			'Affirm PCT',

			'Count of Deposited', 
			'Orders Shipped', 
			'Canadian', 
			'Alaska, Hawaii', 
			'Pending Returns', 

			'# Refunds for Returns', 
			'$ Refunds', 
			'# Partial Refund', 
			'$ Partial Refund', 


		);

		$rows = array();

		$sql = "
			select p.ID, p.post_date, p.post_status, p.post_type
			from {$wpdb->posts} p
			where p.post_type='shop_order' 
			and p.post_type in ('".implode("','", $order_type_selection)."')
			and p.post_status in ('".implode("','", $order_status_selection)."') 
			and p.post_date >= '$data1 00:00:00' and p.post_date <= '$data2 23:59:59' 
			order by p.post_date
		";

		$orders_ids = $wpdb->get_results($sql);

		$project = get_option('options_legacy_scs_client_id');

		$curent_date = '';

		$order_count 		= 0;
		$order_count_cc 	= 0;
		$order_count_paypal 	= 0;
		$order_count_affirm 	= 0;

		$values['total'] 	= 0;
		$values['total_cc'] 	= 0;
		$values['total_paypal'] = 0;
		$values['total_affirm'] = 0;

		$values['items'] = 0;
		$values['shipping'] = 0;
		$values['tax'] = 0;
		$values['coupon'] = 0;

		$values['full_total'] = 0;
		$values['full_items'] = 0;
		$values['full_shipping'] = 0;
		$values['full_tax'] = 0;
		$values['full_coupon'] = 0;

		$values['trial'] = 0;
		$values['instalment'] = 0;

		$values['gross_cash'] = 0;
		$values['gross_revenue'] = 0;

		$values['refund'] = 0;

		$values['cancel_pre'] = 0;
		$values['cancel_post'] = 0;

		$values['lost_demand'] = 0;

		$values['processed_count'] = 0;
		$values['processed_value'] = 0;
		$values['cancel_pre_count'] = 0;

		$values['deposited_count'] = 0;
		$values['shipped_count'] = 0;
		$values['shipped_canada_count'] = 0;
		$values['shipped_external_count'] = 0;

		$values['returned_count'] = 0;

		$values['refund_full_count'] = 0;
		$values['refund_full_value'] = 0;

		$values['refund_partial_count'] = 0;
		$values['refund_partial_value'] = 0;

		$values['cancel_no'] = 0;
		$values['cancel_val_items'] = 0;
		$values['cancel_val_sp'] = 0;
		$values['cancel_val_tax'] = 0;
		$values['cancel_val_total'] = 0;

		$result_val = array();


		foreach ($orders_ids as $o){

			$oid = $o->ID;

			$test_order = get_post_meta($oid,'_test_order',true);
			if ($test_order == 'yes')
				continue;

			if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
				continue;


			$status = str_replace("wc-", "", $o->post_status);

			$date = date('Y-m-d', strtotime($o->post_date));
			$payment_method = getPaymentMethod($oid, 0);


			$pay_date = get_post_meta($oid,'_paid_date',true);



			if (empty($curent_date)) {
				$curent_date = $date;
			}


			if ($date <> $curent_date) {

				$result_row['day'] 			= date('l', strtotime($curent_date));
				$result_row['date'] 			= date('m/d/Y', strtotime($curent_date));

				$result_row['order_count'] 		= $order_count;
				$result_row['order_count_cc'] 		= $order_count_cc;
				$result_row['order_count_paypal'] 	= $order_count_paypal;
				$result_row['order_count_affirm'] 	= $order_count_affirm;
	

				$result_row['total'] 			= $values['total'];
				$result_row['total_cc'] 		= $values['total_cc'];
				$result_row['total_paypal'] 		= $values['total_paypal'];
				$result_row['total_affirm'] 		= $values['total_affirm'];

				$result_row['items'] 			= $values['items'];
				$result_row['shipping'] 		= $values['shipping'];
				$result_row['tax'] 			= $values['tax'];

				$result_row['trial'] 			= $values['trial'];
				$result_row['instalment'] 		= $values['instalment'];

				$result_row['total_over_time']		= $values['total'] + $result_row['trial'] + $result_row['instalment'];

				$result_row['gross_cash'] 		= $values['gross_cash'];
				$result_row['gross_revenue'] 		= $values['gross_revenue'];

				$result_row['refund'] 			= 0;

				$result_row['cancel_pre'] 		= $values['cancel_pre'];
				$result_row['cancel_post'] 		= $values['cancel_post'];

				$result_row['lost_demand']		= $values['lost_demand'];

				$result_row['processed_count'] 		= $result_row['order_count'] - $values['cancel_pre_count'];
				$result_row['processed_value'] 		= $result_row['total'] - $result_row['cancel_pre'];
				$result_row['cancel_pre_count'] 	= $values['cancel_pre_count'];

				$result_row['deposited_count'] 		= $values['deposited_count'];
				$result_row['shipped_count'] 		= $values['shipped_count'];
				$result_row['shipped_canada_count'] 	= $values['shipped_canada_count'];
				$result_row['shipped_external_count'] 	= $values['shipped_external_count'];

				$result_row['returned_count'] 		= $values['returned_count'];

				$result_row['refund_full_count'] 	= $values['refund_full_count'];
				$result_row['refund_full_value'] 	= $values['refund_full_value'];

				$result_row['refund_partial_count']	= $values['refund_partial_count'];
				$result_row['refund_partial_value']	= $values['refund_partial_value'];



				$result_val[date('m/d/Y', strtotime($curent_date))] = $result_row;


				$curent_date = $date;

				$order_count = 0;
				$order_count_cc = 0;
				$order_count_paypal = 0;
				$order_count_affirm = 0;

				$values['total'] = 0;
				$values['total_cc'] = 0;
				$values['total_paypal'] = 0;
				$values['total_affirm'] = 0;

				$values['items'] = 0;
				$values['shipping'] = 0;
				$values['tax'] = 0;
				$values['coupon'] = 0;

				$values['full_total'] = 0;
				$values['full_items'] = 0;
				$values['full_shipping'] = 0;
				$values['full_tax'] = 0;
				$values['full_coupon'] = 0;

				$values['trial'] = 0;
				$values['instalment'] = 0;

				$values['gross_cash'] = 0;
				$values['gross_revenue'] = 0;

				$values['cancel_pre'] = 0;
				$values['cancel_post'] = 0;

				$values['lost_demand'] = 0;

				$values['processed_count'] = 0;
				$values['processed_value'] = 0;
				$values['cancel_pre_count'] = 0;

				$values['deposited_count'] = 0;
				$values['shipped_count'] = 0;
				$values['shipped_canada_count'] = 0;
				$values['shipped_external_count'] = 0;

				$values['returned_count'] = 0;

				$values['refund_full_count'] = 0;
				$values['refund_full_value'] = 0;

				$values['refund_partial_count'] = 0;
				$values['refund_partial_value'] = 0;

				$values['cancel_no'] = 0;
				$values['cancel_val_items'] = 0;
				$values['cancel_val_sp'] = 0;
				$values['cancel_val_tax'] = 0;
				$values['cancel_val_total'] = 0;

			}


			$orderValues = getOrderValues($oid);
			foreach ($orderValues as $key => $value) {

				$values[$key] = $values[$key] + $value;
			}


			if (!empty($pay_date)) {
				$values['gross_cash'] = $values['gross_cash'] + $orderValues['total'];
				$values['deposited_count'] ++;
			}
			$values['gross_revenue'] = $values['gross_revenue'] + $orderValues['total'];


			if (empty($pay_date) && $status == 'pending') {
				$values['cancel_pre_count']++;
				$values['cancel_pre'] = $values['cancel_pre'] + $orderValues['total'];
				$values['gross_revenue'] = $values['gross_revenue'] - $orderValues['total'];
			}
			$values['cancel_post'] = $values['cancel_post'] + 0;
			$values['gross_revenue'] = $values['gross_revenue'] - 0;




			$order_count++;

			if ($payment_method == 'Credit Card') {
				$order_count_cc++;
				$values['total_cc'] = $values['total_cc'] + $orderValues['total'];
			} else if ($payment_method == 'Paypal') {
				$order_count_paypal++;
				$values['total_paypal'] = $values['total_paypal'] + $orderValues['total'];
			} else if ($payment_method == 'Affirm') {
				$order_count_affirm++;
				$values['total_affirm'] = $values['total_affirm'] + $orderValues['total'];
			} else {
				$order_count_cc++;
				$values['total_cc'] = $values['total_cc'] + $orderValues['total'];
			}




			$shipInfo = getShipment($oid);

			if (array_key_exists('ship_date', $shipInfo) && !empty($shipInfo['ship_date'])) {
				$values['shipped_count'] ++;
			}

			$ship_to_state = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_state' and post_id=$oid");
			if (in_array($ship_to_state, $external_states_array)) {
				$values['shipped_external_count']++;
			}

			$ship_to_country = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_country' and post_id=$oid");
			if ($ship_to_country == 'CA') {
				$values['shipped_canada_count']++;
			}



		}

		// last record

		if (!empty($curent_date)) {

			$result_row['day'] 			= date('l', strtotime($curent_date));
			$result_row['date'] 			= date('m/d/Y', strtotime($curent_date));

			$result_row['order_count'] 		= $order_count;
			$result_row['order_count_cc'] 		= $order_count_cc;
			$result_row['order_count_paypal'] 	= $order_count_paypal;
			$result_row['order_count_affirm'] 	= $order_count_affirm;

			$result_row['total'] 			= $values['total'];
			$result_row['total_cc'] 		= $values['total_cc'];
			$result_row['total_paypal'] 		= $values['total_paypal'];
			$result_row['total_affirm'] 		= $values['total_affirm'];

			$result_row['items'] 			= $values['items'];
			$result_row['shipping'] 		= $values['shipping'];
			$result_row['tax'] 			= $values['tax'];

			$result_row['trial'] 			= $values['trial'];
			$result_row['instalment'] 		= $values['instalment'];

			$result_row['total_over_time']		= $values['total'] + $result_row['trial'] = $result_row['instalment'];

			$result_row['gross_cash'] 		= $values['gross_cash'];

			$result_row['refund'] 			= 0;

			$result_row['cancel_pre'] 		= $values['cancel_pre'];
			$result_row['cancel_post'] 		= $values['cancel_post'];

			$result_row['lost_demand']		= $result_row['cancel_pre'] + $result_row['cancel_post'];

			$result_row['gross_revenue'] 		= $values['gross_revenue'];

			$result_row['processed_count'] 		= $result_row['order_count'] - $values['cancel_pre_count'];
			$result_row['processed_value'] 		= $result_row['total'] - $result_row['cancel_pre'];
			$result_row['cancel_pre_count'] 	= $values['cancel_pre_count'];

			$result_row['deposited_count'] 		= $values['deposited_count'];
			$result_row['shipped_count'] 		= $values['shipped_count'];
			$result_row['shipped_canada_count'] 	= $values['shipped_canada_count'];
			$result_row['shipped_external_count'] 	= $values['shipped_external_count'];

			$result_row['returned_count'] 		= $values['returned_count'];

			$result_row['refund_full_count'] 	= $values['refund_full_count'];
			$result_row['refund_full_value'] 	= $values['refund_full_value'];

			$result_row['refund_partial_count']	= $values['refund_partial_count'];
			$result_row['refund_partial_value']	= $values['refund_partial_value'];



			$result_val[date('m/d/Y', strtotime($curent_date))] = $result_row;

//echo "Add data" . "<br>\n"; print_r($result_row);

		}






		// REFUND

		$sql = "
			select ID, post_date, post_type, post_parent, post_date 
			from {$wpdb->posts} 
			where post_date >= '$data1 00:00:00' and post_date <= '$data2 23:59:59' 
			and post_type='shop_order_refund'
		";

		$orders_ids = $wpdb->get_results($sql);

		$curent_date = '';

		$refund_items_value = 0;
		$refund_items_tax = 0;
		$refund_items_shipping = 0;

		$values = array();

		$values['returned_count'] = 0;

		$values['refund_full_count'] = 0;
		$values['refund_full_value'] = 0;
		$values['refund_partial_count'] = 0;
		$values['refund_partial_value'] = 0;


		foreach ($orders_ids as $o) {

			if (empty(get_post_meta($o->ID,'_refund_amount',true))) 
				continue;

			$oid = $o->post_parent;
			$refund_oid = $o->ID;
			$date = date('Y-m-d', strtotime($o->post_date));

			$test_order = get_post_meta($oid,'_test_order',true);
			if ($test_order == 'yes')
				continue;

			if (empty($curent_date)) {
				$curent_date = $date;
			}

			$items = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_id = $refund_oid order by i.order_item_type");
			if (!$items) {
				$refund_oid = $oid;
				$items = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_id = $refund_oid order by i.order_item_type");
			}




			$refund_item_value = 0;
			$refund_item_tax = 0;
			$refund_item_shipping = 0;

			foreach ($items as $item) {

				$itemType = getItemType($item);
				if ($itemType == 'tax')
					continue;

				$refundedOrderItemValues = getOrderItemValues($item, true);

				$refund_item_value = $refund_item_value + abs($refundedOrderItemValues['items']);
				$refund_item_tax = $refund_item_tax + abs($refundedOrderItemValues['tax']);
				$refund_item_shipping = $refund_item_shipping + abs($refundedOrderItemValues['shipping']);

			}

			$refund_items_value = $refund_items_value + $refund_item_value;
			$refund_items_tax = $refund_items_tax + $refund_item_tax;
			$refund_items_shipping = $refund_items_shipping + $refund_item_shipping;



			$orderValues = getOrderValues($oid, true);
			if ($refund_item_value + $refund_item_tax + $refund_item_shipping >= $orderValues['items'] + $orderValues['tax'] + $orderValues['shipping']) {

				// full refund

				$values['refund_full_count']++;
				$values['refund_full_value'] = $values['refund_full_value'] + $refund_item_value + $refund_item_tax + $refund_item_shipping;

			} else {

				// partial refund

				$values['refund_partial_count']++;
				$values['refund_partial_value'] = $values['refund_partial_value'] + $refund_item_value + $refund_item_tax + $refund_item_shipping;

			}




			// Get returned items

			$return_data = get_post_meta($oid, 'mwb_wrma_return_product', true);
			$ret_qty = 0;
			if (!empty($return_data)) {
				foreach ($return_data as $ret_date => $ret_info) {
					if (!empty($ret_info['products'])) {
						foreach ($ret_info['products'] as $ret_product) {
							$ret_qty = $ret_qty + $ret_product['qty'];
						}
					}
				}
			}


			if ($ret_qty > 0) {
				$values['returned_count']++;
			}



			if ($date <> $curent_date) {

				if ( !array_key_exists(date('m/d/Y', strtotime($curent_date)), $result_val) ) {

					$result_row['day'] 			= date('l', strtotime($curent_date));
					$result_row['date'] 			= date('m/d/Y', strtotime($curent_date));

					$result_row['order_count'] 		= 0;
					$result_row['order_count_cc'] 		= 0;
					$result_row['order_count_paypal'] 	= 0;
					$result_row['order_count_affirm'] 	= 0;

					$result_row['total'] 			= 0;
					$result_row['total_cc'] 		= 0;
					$result_row['total_paypal'] 		= 0;
					$result_row['total_affirm'] 		= 0;

					$result_row['items'] 		= 0;
					$result_row['shipping'] 	= 0;
					$result_row['tax'] 		= 0;

					$result_row['trial'] 		= 0;
					$result_row['instalment'] 	= 0;

					$result_row['total_over_time']	= 0;

					$result_row['gross_cash'] 	= 0;
					$result_row['gross_revenue'] 	= 0;

					$result_row['cancel_pre'] 	= 0;
					$result_row['cancel_post'] 	= 0;

					$result_row['lost_demand']		= 0;

					$result_row['processed_count'] 		= 0;
					$result_row['processed_value'] 		= 0;
					$result_row['cancel_pre_count'] 	= 0;

					$result_row['deposited_count'] 		= 0;
					$result_row['shipped_count'] 		= 0;
					$result_row['shipped_canada_count'] 	= 0;
					$result_row['shipped_external_count'] 	= 0;

					$result_row['returned_count'] 		= 0;

					$result_row['refund_full_count'] 	= 0;
					$result_row['refund_full_value'] 	= 0;

					$result_row['refund_partial_count']	= 0;
					$result_row['refund_partial_value']	= 0;



					$result_val[date('m/d/Y', strtotime($curent_date))] = $result_row;

				}

				$result_val[date('m/d/Y', strtotime($curent_date))]['refund'] = $result_val[date('m/d/Y', strtotime($curent_date))]['refund'] + $refund_items_value + $refund_items_tax + $refund_items_shipping;

				$result_val[date('m/d/Y', strtotime($curent_date))]['returned_count'] = $result_val[date('m/d/Y', strtotime($curent_date))]['returned_count'] + $values['returned_count'];

				$result_val[date('m/d/Y', strtotime($curent_date))]['refund_full_count'] = $result_val[date('m/d/Y', strtotime($curent_date))]['refund_full_count'] + $values['refund_full_count'];
				$result_val[date('m/d/Y', strtotime($curent_date))]['refund_full_value'] = $result_val[date('m/d/Y', strtotime($curent_date))]['refund_full_value'] + $values['refund_full_value'];
				$result_val[date('m/d/Y', strtotime($curent_date))]['refund_partial_count'] = $result_val[date('m/d/Y', strtotime($curent_date))]['refund_partial_count'] + $values['refund_partial_count'];
				$result_val[date('m/d/Y', strtotime($curent_date))]['refund_partial_value'] = $result_val[date('m/d/Y', strtotime($curent_date))]['refund_partial_value'] + $values['refund_partial_value'];



				$refund_items_value = 0;
				$refund_items_tax = 0;
				$refund_items_shipping = 0;


				$values['returned_count'] = 0;

				$values['refund_full_count'] = 0;
				$values['refund_full_value'] = 0;
				$values['refund_partial_count'] = 0;
				$values['refund_partial_value'] = 0;


			}


		}


		// last record

		if (!empty($curent_date)) {

			if ( !array_key_exists(date('m/d/Y', strtotime($curent_date)), $result_val) ) {

				$result_row['day'] 			= date('l', strtotime($curent_date));
				$result_row['date'] 			= date('m/d/Y', strtotime($curent_date));

				$result_row['order_count'] 		= 0;
				$result_row['order_count_cc'] 		= 0;
				$result_row['order_count_paypal'] 	= 0;
				$result_row['order_count_affirm'] 	= 0;

				$result_row['total'] 			= 0;
				$result_row['total_cc'] 		= 0;
				$result_row['total_paypal'] 		= 0;
				$result_row['total_affirm'] 		= 0;

				$result_row['items'] 			= 0;
				$result_row['shipping'] 		= 0;
				$result_row['tax'] 			= 0;

				$result_row['trial'] 			= 0;
				$result_row['instalment'] 		= 0;

				$result_row['total_over_time']		= 0;

				$result_row['gross_cash'] 		= 0;
				$result_row['gross_revenue'] 		= 0;

				$result_row['cancel_pre'] 		= 0;
				$result_row['cancel_post'] 		= 0;

				$result_row['lost_demand']		= 0;

				$result_row['processed_count'] 		= 0;
				$result_row['processed_value'] 		= 0;
				$result_row['cancel_pre_count'] 	= 0;

				$result_row['deposited_count'] 		= 0;
				$result_row['shipped_count'] 		= 0;
				$result_row['shipped_canada_count'] 	= 0;
				$result_row['shipped_external_count'] 	= 0;

				$result_row['returned_count'] 		= 0;

				$result_row['refund_full_count'] 	= 0;
				$result_row['refund_full_value'] 	= 0;

				$result_row['refund_partial_count']	= 0;
				$result_row['refund_partial_value']	= 0;


				$result_val[date('m/d/Y', strtotime($curent_date))] = $result_row;

			}

			$result_val[date('m/d/Y', strtotime($curent_date))]['refund'] = $result_val[date('m/d/Y', strtotime($curent_date))]['refund'] + $refund_items_value + $refund_items_tax + $refund_items_shipping;

			$result_val[date('m/d/Y', strtotime($curent_date))]['returned_count'] = $result_val[date('m/d/Y', strtotime($curent_date))]['returned_count'] + $values['returned_count'];

			$result_val[date('m/d/Y', strtotime($curent_date))]['refund_full_count'] = $result_val[date('m/d/Y', strtotime($curent_date))]['refund_full_count'] + $values['refund_full_count'];
			$result_val[date('m/d/Y', strtotime($curent_date))]['refund_full_value'] = $result_val[date('m/d/Y', strtotime($curent_date))]['refund_full_value'] + $values['refund_full_value'];
			$result_val[date('m/d/Y', strtotime($curent_date))]['refund_partial_count'] = $result_val[date('m/d/Y', strtotime($curent_date))]['refund_partial_count'] + $values['refund_partial_count'];
			$result_val[date('m/d/Y', strtotime($curent_date))]['refund_partial_value'] = $result_val[date('m/d/Y', strtotime($curent_date))]['refund_partial_value'] + $values['refund_partial_value'];


		}


//die();















		// Report

		foreach ($result_val as $d => $result_row) {

			$row = array();

			$row[] = $project;

			$row[] = $result_row['day'];
			$row[] = $result_row['date'];
			$row[] = $result_row['order_count'];
			$row[] = $result_row['total'];
			$row[] = $result_row['items'];
			$row[] = $result_row['shipping'];

			$row[] = $result_row['tax'];
			if ($result_row['order_count'] != 0) {
				$row[] = round( ($result_row['items'] + $result_row['tax']) / $result_row['order_count'], 2);		// Avg Demand with Tax
			} else {
				$row[] = 0;
			}


			$row[] = $result_row['trial'];
			$row[] = $result_row['instalment'];

			$row[] = $result_row['total'] - $result_row['tax'];

			$row[] = $result_row['cancel_pre'];
			$row[] = $result_row['cancel_post'];

			$row[] = $result_row['lost_demand'];

			$row[] = $result_row['gross_revenue'];
			if ($result_row['total'] != 0) {
				$row[] = round($result_row['gross_revenue'] / $result_row['total'] * 100, 2);		// Gross Revenue PCT
			} else {
				$row[] = 0;
			}

			$row[] = $result_row['gross_cash'];

			$row[] = (-1) * $result_row['refund'];
			if ($result_row['gross_revenue'] != 0) {
				$row[] = round($result_row['refund'] / $result_row['gross_revenue'] * 100, 2);		// Refund PCT
			} else {
				$row[] = 0;
			}


			$row[] = $result_row['gross_cash'] + (-1) * $result_row['refund'];								// Net Cash
			if ($result_row['total'] != 0) {
				$row[] = round( ($result_row['gross_cash'] + (-1) * $result_row['refund']) / $result_row['total'] * 100, 2);		// Net Cash Over Demand
			} else {
				$row[] = 0;
			}

			$row[] = $result_row['processed_count'];
			$row[] = $result_row['processed_value']; 
			$row[] = $result_row['cancel_pre_count'];


			$row[] = $result_row['order_count_cc'];
			$row[] = $result_row['total_cc'];

			if ($result_row['total'] != 0) {
				$row[] = round($result_row['total_cc'] / $result_row['total'] * 100, 2);		// CC PCT
			} else {
				$row[] = 0;
			}


			$row[] = $result_row['order_count_paypal'];
			$row[] = $result_row['total_paypal'];

			if ($result_row['total'] != 0) {
				$row[] = round($result_row['total_paypal'] / $result_row['total'] * 100, 2);		// Paypal  PCT
			} else {
				$row[] = 0;
			}


			$row[] = $result_row['order_count_affirm'];
			$row[] = $result_row['total_affirm'];

			if ($result_row['total'] != 0) {
				$row[] = round($result_row['total_affirm'] / $result_row['total'] * 100, 2);		// Affirm PCT
			} else {
				$row[] = 0;
			}

			$row[] = $result_row['deposited_count'];
			$row[] = $result_row['shipped_count'];
			$row[] = $result_row['shipped_canada_count'];
			$row[] = $result_row['shipped_external_count'];
			$row[] = $result_row['returned_count'];

			$row[] = $result_row['refund_full_count'];
			$row[] = $result_row['refund_full_value']; 
			$row[] = $result_row['refund_partial_count'];
			$row[] = $result_row['refund_partial_value'];



			$rows[] = $row;
		}

//die();



        // get the table rows
        $units = array('', '', '', '', '$', '$', '$', '$', '$', '$', '$', '$', '$', '$', '$', '%', '$', '$', '%', '$', '%', '', '$', '', '', '$', '%', '', '$', '%', '', '$', '%', '', '', '', '', '', '', '$', '', '$');
        $total = array('', '', '',  1,   1,   1,   1,   1,  '',   1,   1,   1,   1,   1,   1,  '',   1,   1,  '',   1,  '',  1,   1,  1,  1,  1,   '',  1,  1,   '',  1,  1,   '',  1,  1,  1,  1,  1,  1,   1,  1,   1);


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
