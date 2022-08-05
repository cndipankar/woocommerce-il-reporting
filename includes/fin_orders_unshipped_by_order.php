<?php
  

	function get_fin_orders_unshipped_by_order($data1, $data2) {

	global $wpdb;
		
		$start_date = $data1;
		$end_date = $data2;

		//$order_status_selection = array('wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-cancelled', 'wc-failed', 'wc-on-hold', 'wc-pending', 'wc-pending-cancel');
		$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
		$order_type_selection = array('shop_order');
		$order_flow_selection = array('onepay', 'multipay-init', 'subscription-init', 'subscription-renewal');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal



		// get the table rows
		$header = array(
			'Project',
			'Order No.',
			'Order Date',
			'Status',
			'Order Type',
			'Order Source',
			'Traffic Source',
			'CustNumber',
			'Customer First Name',
			'Customer Last Name',
			'Customer Address',
			'Customer Apt.',
			'Customer City',
			'Customer  State',
			'Customer  Zip',
			'Customer Phone',
			'Customer Email',
			'Customer Create Date',
			'Payment Amount',
			'Trial Value',
			'Instalment Value',
			'Payment Method',
			'Script',
			'Payment Plan',
			'Installment numbers', 
			'Ship Method',
			'Number Of SKU',
			'Line Number',
			'Number Of Item',
			'Product w/o Discount',
			'Product Amt',
			'Discount Amt',
			'S&P Amt',
			'TaxAmt',
			'Sku',
			'SkuDesc',
			'Ship to First',
			'Ship to Last',
			'Ship to Address',
			'Ship to Apt',
			'Ship to City',
			'Ship to State',
			'Ship to Zip',
			'Ship To Country',
			'Subscription Shipment Number',
			'Active Subscription Customer',
			'Refund Amount',
			'Return Date',
			'Return Reason',
			'Order Cancel Reason'
		);
		
		
		$zap = "
			select p.ID, p.post_date, p.post_status, p.post_type
			from {$wpdb->posts} p, {$wpdb->postmeta} m 
			where p.id = m.post_id
			and p.post_type='shop_order' 
			and p.post_type in ('".implode("','", $order_type_selection)."')
			and p.post_status in ('".implode("','", $order_status_selection)."') 
			and m.meta_key = '_paid_date'
			and p.post_date >= '$data1 00:00:00' and p.post_date <= '$data2 23:59:59' 
			order by p.ID desc
		";

		$orders_ids = $wpdb->get_results($zap);
		
		$project = get_option('options_legacy_scs_client_id');
		

		foreach($orders_ids as $o){

			$oid = $o->ID;

			$test_order = get_post_meta($oid,'_test_order',true);
			if ($test_order == 'yes')
				continue;

			if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
				continue;



			$shipInfo = getShipment($oid);

			if (!empty($shipInfo['ship_date'])) {
				continue;
			}
			
			$cust_number = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_customer_user' and post_id=$oid");
			$customer_first_name = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_first_name' and post_id=$oid");
			$customer_last_name = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_last_name' and post_id=$oid");
			$customer_address = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_address_1' and post_id=$oid");
			$customer_apt = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_address_2' and post_id=$oid");
			$customer_city = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_city' and post_id=$oid");
			$customer_state = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_state' and post_id=$oid");
			$customer_zip = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_postcode' and post_id=$oid");
			$customer_phone =  $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_phone' and post_id=$oid");
			$customer_email =  $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_email' and post_id=$oid");

			$userID = get_post_meta($oid,'_customer_user',true);
			$customer_create_date =  $wpdb->get_var("select user_registered from {$wpdb->prefix}users where ID = $userID");
			if (!empty($customer_create_date)) {
				$customer_create_date = date("m/d/Y", strtotime($customer_create_date));
			}

			$source = getOrderType($oid);
			
			$order_no = $oid;

			$ship_status = str_replace("wc-", "", $o->post_status);
			$order_date = date('m/d/Y',strtotime($o->post_date));



			$telemarket = getSource($oid);
			$funnel_traffic_source = getFunnel($oid, 's');
			$instalmentText = '';
			$instalmentNumber = getInstalmentNumber($oid); 
			$instalmentNumbers = getInstalmentNumbers($oid);
			if ( !empty($instalmentNumbers) ) {
				$instalmentText = $instalmentNumbers; 
			}


		
			// Payment
			$PaymentCode = getPaymentMethod($oid);

			$script = get_post_meta($oid,'scriptcode',true);
			if (empty($script)) {
				$script = 'WEB';
			}


			$shipped_info = getShipment($oid);
			$ship_method = $shipped_info['ship_method'];
			$date_shipped = $shipped_info['ship_date'];

			
			$ship_to_first = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_first_name' and post_id=$oid");
			$ship_to_last = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_last_name' and post_id=$oid");
			$ship_to_address = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_address_1' and post_id=$oid");
			$ship_to_apt = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_address_2' and post_id=$oid");
			$ship_to_city = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_city' and post_id=$oid");
			$ship_to_state = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_state' and post_id=$oid");
			$ship_to_zip = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_postcode' and post_id=$oid");
			$ship_to_country = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_country' and post_id=$oid");

 
			$order_cancel_reason = get_post_meta($oid,'cancel_reason',true);
			$payment_plan = getPaymentPlan($oid);			



			$sql = "
				select  i.order_item_id, i.order_item_name, i.order_item_type
				from {$wpdb->prefix}woocommerce_order_items i
				where i.order_id = $oid
				and i.order_item_type in ('line_item', 'shipping', 'coupon')
				order by i.order_item_type, i.order_item_id
			";

			$items_ids = $wpdb->get_results($sql);


			$sum_product_amt = 0;
			$sum_s_h_amt = 0;
			$sum_tax_amt = 0;
			$sum_discount_amt = 0;
			$sum_product_without_discount = 0;
			$sum_payment_amount = 0;

			$sum_trial_amt = 0;
			$sum_instalment_amt = 0;


			$sum_number_of_item = 0;

			$sum_sku = '';
			$sum_sku_desc = '';

			$sum_refund_amount = 0;

			$sum_countinuity_shipment_number = 0;

			if (getActiveSubscriptions($cust_number) > 0) {
				$sum_active_continuity_customer = 'YES'; 
			} else {
				$sum_active_continuity_customer = 'NO'; 
			}


			$sum_return_date = '';
			$sum_return_reason = '';



			$line_number = 0;
			$exist_sp_line = 0;
			foreach($items_ids as $item) {

				// Items

				$line_number++;

				$pid = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_product_id' and order_item_id=$item->order_item_id");
				$vid = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_variation_id' and order_item_id=$item->order_item_id");


				$number_of_sku = getItemQty($item->order_item_id);

				$legacy_product_quantity = (int)get_post_meta(max($pid, $vid), 'legacy_product_quantity', true);
				if (empty($legacy_product_quantity)) {
					$number_of_item = $number_of_sku;
				} else {
					$number_of_item = $number_of_sku * (int)$legacy_product_quantity;
				}

		
				$orderItemValues = getOrderItemValues($item->order_item_id, 1);

				$product_amt = $orderItemValues['items'];
				$s_h_amt = $orderItemValues['shipping'];
				$tax_amt = $orderItemValues['tax'];
				$discount_amt = $orderItemValues['coupon'];
				$product_without_discount = $orderItemValues['items_without_discount'];

				$trial_amt = $orderItemValues['trial'];
				$instalment_amt = $orderItemValues['instalment'];


				$sku_desc = getItemDescription($item->order_item_id);
				$sku = getItemSKU($item->order_item_id);

				$itemType = getItemType($item->order_item_id);
				if ($itemType == 'shipping') {
					$exist_sp_line = 1;
				}


				$payment_amount = $product_amt + $s_h_amt + $tax_amt;



				$shipment_numbers = $wpdb->get_var("
					select count(*) cnt
					from {$wpdb->posts} o, {$wpdb->posts} s, {$wpdb->posts} p, {$wpdb->postmeta} m, {$wpdb->prefix}woocommerce_order_items i, {$wpdb->prefix}woocommerce_order_itemmeta im
					where o.ID = s.post_parent and s.ID = m.meta_value and p.id = m.post_id and p.id = i.order_id and i.order_item_id = im.order_item_id
					and m.meta_key = '_subscription_renewal' 
					and i.order_item_type = 'line_item'
					and (
						(im.meta_key = '_product_id' and im.meta_value = $pid)
						or
						(im.meta_key = '_variation_id' and im.meta_value = $vid	)
					)
					and im.meta_value > 0
					and o.ID = $oid
				");

				if ($shipment_numbers > 0) {
					$countinuity_shipment_number = $shipment_numbers;
				} else {
					$countinuity_shipment_number = 0;
				}



				$refund_amount = getItemRefund($oid, $item->order_item_id);


				// Get returned items

				$return_data = getItemReturn($oid, $item->order_item_id);
				$return_date = $return_data['date'];
				$return_reason = $return_data['reason'];



				$sum_product_amt = $sum_product_amt + $product_amt;
				$sum_s_h_amt = $sum_s_h_amt + $s_h_amt;
				$sum_tax_amt = $sum_tax_amt + $tax_amt;
				$sum_discount_amt = $sum_discount_amt + $discount_amt;

				$sum_trial_amt = $sum_trial_amt + $trial_amt;
				$sum_instalment_amt = $sum_instalment_amt + $instalment_amt;


				if ($itemType == 'line_item') {
					$sum_product_without_discount = $sum_product_without_discount + $product_without_discount;			
				} else {
					$sum_product_without_discount = $sum_product_without_discount + $product_amt;
				}

				$sum_payment_amount = $sum_payment_amount + $payment_amount;

				$sum_number_of_item = $sum_number_of_item + $number_of_item;
				$sum_number_of_SKU = $sum_number_of_SKU + $number_of_sku;


				if ($itemType == 'line_item') {
					if (!empty($sum_sku)) {
						$sum_sku = $sum_sku . ", ";
					}
					$sum_sku = $sum_sku . $sku . ' x ' . $number_of_item;

					if (!empty($sum_sku_desc)) {
						$sum_sku_desc = $sum_sku_desc . ", ";
					}
					$sum_sku_desc = $sum_sku_desc . $sku_desc . ' (' . $number_of_item . ')';
				}

				$sum_refund_amount = $sum_refund_amount + $refund_amount;

				$sum_countinuity_shipment_number = $sum_countinuity_shipment_number + $countinuity_shipment_number;


				if (!empty($return_date) && strpos($sum_return_date, $return_date) === false) {
					if (!empty($sum_return_date)) {
						$sum_return_date = $sum_return_date . ", ";
					}
					$sum_return_date = $sum_return_date . $return_date;
				}

				if (!empty($return_reason) && strpos($sum_return_reason, $return_reason) === false) {
					if (!empty($sum_return_reason)) {
						$sum_return_reason = $sum_return_reason . ", ";
					}
					$sum_return_reason = $sum_return_reason . $return_reason;
				}

			}



			$rows[]=[
				$project,
				$order_no,
				$order_date,
				$ship_status,
				$source,
				$telemarket,
				$funnel_traffic_source,
				$cust_number,
				$customer_first_name,
				$customer_last_name,
				$customer_address,
				$customer_apt,
				$customer_city,
				$customer_state,
				$customer_zip,
				$customer_phone,
				$customer_email,
				$customer_create_date,
				$sum_payment_amount,
				$sum_trial_amt,
				$sum_instalment_amt, 
				$PaymentCode,
				$script,
				$payment_plan,
				$instalmentText, 
				$ship_method,
				'',
				$sum_number_of_SKU,
				$sum_number_of_item,
				$sum_product_without_discount, 
				$sum_product_amt,
				$sum_discount_amt,
				$sum_s_h_amt,
				$sum_tax_amt,
				$sum_sku,
				$sum_sku_desc,
				$ship_to_first,
				$ship_to_last,
				$ship_to_address,
				$ship_to_apt,
				$ship_to_city,
				$ship_to_state,
				$ship_to_zip,
				$ship_to_country,
				$sum_countinuity_shipment_number,
				$sum_active_continuity_customer,
				$sum_refund_amount,
				$sum_return_date,
				$sum_return_reason,
				$order_cancel_reason
			];




		}

		$total = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 1, '', '', '', '', '', '', 1, 1, 1, 1, 1, 1, 1,'', '', '', '', '', '', '', '', '', '', '', '', 1, '', '', '');

		$units = [];
		for($i=0;$i<52;$i++){
			$units[$i]='';
		}
		$units[18]=$units[19]=$units[20]=$units[29]=$units[30]=$units[31]=$units[32]=$units[33]=$units[35]=$units[46]='$';
				

		// Array data
		$result['all']['array']['header']	 = $header;
		$result['all']['array']['unit']		 = $units;
		$result['all']['array']['total']	 = $total;
		$result['all']['array']['rows']		 = $rows;
		$result['all']['array']['sheetname']	 = 'Worksheet';
		$result['all']['array']['title']	 = 'Customer and Order Detail (order)';
		

		return json_encode($result);

	}

?>
