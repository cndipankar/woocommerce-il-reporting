<?php

    function get_fin_pay_uncollected($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

	//$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request');
	$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'multipay-pay', 'subscription-init', 'subscription-renewal');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal


        $units = array('','','','','','','','','','','','','','','','','','','','','$','$','$','$','$','$','$','$','$','$','$','','','','');
        $total = array();
        $rows = array();

        // get the table rows
        $header = [
		'ProjectNumber', 
		'Order#', 
		'Parent Order#', 
		'Order Date', 
		'Deposit_Date', 
		'Doc Status', 
		'Order Type', 
		'Order Source', 
		'Traffic Source', 
		'Zip_Code',
		'State',
		'FirstName',
		'LastName',
		'PaymentNo',
		'Payment Method',
		'Pmt_Type','Script',
		'Payment Plan', 
		'Installment numbers',
		'Qty Ordered',
		'Item Amount',
		'Paid Amount',
		'Unpaid Amount', 
		'Unpaid Products', 
		'Unpaid Tax', 
		'Product w/o Discount', 
		'Discount', 
		'Product Amt',
		'S&P Amt',
		'TaxAmt', 
		'SKU',
		'Product Name', 
		'Doc ShipDate', 
		'Customer CreationDate'
	];

	$ProjectNumber = get_option('options_legacy_scs_client_id');
	
	$sql = "select p1.ID as sid,p2.ID as oid,p2.post_date as order_date, m.meta_value as paid_date   
	from {$wpdb->posts} p1
	inner join {$wpdb->posts} p2 on p1.post_parent=p2.ID
	left join {$wpdb->postmeta} m on p2.ID = m.post_id and m.meta_key = '_paid_date'
	where p1.post_type='shop_subscription' and p1.post_status='wc-active' order by oid desc";

	//echo $sql;die();
	$trial_orders_ids = $wpdb->get_results($sql);

	foreach ($trial_orders_ids as $o) {
	
		$oid = $o->oid;
		$sid = $o->sid;

		$test_order = get_post_meta($oid,'_test_order',true);
		if ($test_order == 'yes')
			continue;

		$test_order = get_post_meta($sid,'_test_order',true);
		if ($test_order == 'yes')
			continue;

		if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
			continue;

		$PaymentMethod = ucfirst(get_post_meta($sid,'_payment_method',true));

		$shipped_info = getShipment($oid);
		$ShipDate = $shipped_info['ship_date'];

		if(!$ShipDate) continue;
		
		$Deposit_Date =  $o->paid_date;
		if (!empty($Deposit_Date)) {
			$Deposit_Date = date("m/d/Y", strtotime($Deposit_Date));		
		}

		$Zip_Code = get_post_meta($oid,'_billing_postcode',true);
		$State = get_post_meta($oid,'_billing_state',true);
		$FirstName = get_post_meta($oid,'_billing_first_name',true);
		$LastName = get_post_meta($oid,'_billing_last_name',true);
		$OrderNo = $oid;

		$parentID = getParent($oid);

		// Payment
		$PaymentNo = " ".get_post_meta($oid,'_transaction_id',true);
		$PaymentCode = getPaymentMethod($oid);
		
		$sql = "select  order_item_id
			from {$wpdb->prefix}woocommerce_order_items 
			where order_id = $sid
			and order_item_type ='line_item'";
		
		$items_ids = $wpdb->get_col($sql);
		
		$sql="select meta_value 
			from {$wpdb->prefix}woocommerce_order_itemmeta oim
			inner join {$wpdb->prefix}woocommerce_order_items oi on oi.order_item_id = oim.order_item_id
			where order_item_type='shipping' and meta_key='cost' and order_id=$oid
		";
		$shipping_value = $wpdb->get_var($sql);

		$source = getOrderType($oid);
		$telemarket = getSource($oid);
		$funnel_traffic_source = getFunnel($oid, 's');

		$payment_plan = getPaymentPlan($oid);
		$instalmentNumber = getInstalmentNumber($oid); 
		$instalmentNumbers = getInstalmentNumbers($oid);
		$instalmentText = '';
		if ( !empty($instalmentNumber) || !empty($instalmentNumbers) ) {
			$instalmentText = $instalmentNumber . '/' . $instalmentNumbers;
		}

		
		$Script = getScript($oid);
		$OrderDate = $o->order_date; 

		$DocStatus = str_replace("wc-", "", get_post_status($oid));

		$userID = get_post_meta($oid,'_customer_user',true);
		$CustomerCreationDate =  $wpdb->get_var("select user_registered from {$wpdb->prefix}users where ID = $userID");
		if (!empty($CustomerCreationDate)) {
			$CustomerCreationDate = date("m/d/Y", strtotime($CustomerCreationDate));
		}
		foreach($items_ids  as $item){
			
			$Qty_Ordered = getItemQty($item);
			
			$OrderItemValues = getOrderItemValues($item, 1);
			$Amount = $OrderItemValues['items'] + $shipping_value + $OrderItemValues['tax'];
			$paid_amount = $shipping_value;
			$unpaid_amount =  $OrderItemValues['items'] + $OrderItemValues['tax'];
			$discount_amt = $OrderItemValues['coupon'];
			$product_amt = $OrderItemValues['items'];
			$s_h_amt = $shipping_value;
			$tax_amt = $OrderItemValues['tax'];
			$product_without_discount = $OrderItemValues['items_without_discount'];
			$BackEndCode = getItemSKU($item);
			$ProductName = getItemDescription($item);
			

			$orderItemShippingValues = getOrderItemShippingValues($oid, $item);

			$tax_shipping = 0;
			if ( $orderItemShippingValues > 0 ) {
				$orderTaxIDs = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_item_type = 'tax' and i.order_id = $oid");
				foreach ($orderTaxIDs as $orderTaxID) {
					$tax_rate = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='rate_percent' and order_item_id=$orderTaxID");
					$tax_shipping = $tax_shipping + round($tax_rate * $orderItemShippingValues / 100, 6);
				}
			}

			
			if ($unpaid_amount > 0) {
				$rows[]=[
					$ProjectNumber, 
					$OrderNo, 
					$parentID, 
					$OrderDate, 
					$Deposit_Date, 
					$DocStatus, 
					$source, 
					$telemarket, 
					$funnel_traffic_source, 
					$Zip_Code, 
					$State, 
					$FirstName, 
					$LastName, 
					$PaymentNo, 
					$PaymentMethod, 
					$PaymentCode, 
					$Script, 
					$payment_plan, 
					$instalmentText, 
					$Qty_Ordered, 
					$Amount, 
					$paid_amount, 
					$unpaid_amount, 
					$unpaid_amount_items, 
					$unpaid_amount_tax, 
					$product_without_discount, 
					$discount_amt, 
					$product_amt, 
					$s_h_amt, 
					$tax_amt, 
					$BackEndCode, 
					$ProductName, 
					$ShipDate, 
					$CustomerCreationDate
				];

			}
		}


		}

	


	

	// 3 payments order

	$sql = "
		select p.ID, p.post_date, p.post_status 
		from {$wpdb->posts} p
		where p.ID in 
		(
			select i.post_parent
			from {$wpdb->posts} i
			where 1=1 
			and i.post_type in ('shop_order')
			and i.post_status in ('wc-scheduled-payment') 
		)
		and p.post_date >= '$data1 00:00:00' and p.post_date <= '$data2 23:59:59' 
		order by p.ID desc
	";
	
	$orders_ids = $wpdb->get_results($sql);

	foreach($orders_ids as $o) {

		$oid = $o->ID;

		$test_order = get_post_meta($oid,'_test_order',true);
		if ($test_order == 'yes')
			continue;

		if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
			continue;


		$Zip_Code = get_post_meta($oid,'_billing_postcode',true);
		$State = get_post_meta($oid,'_billing_state',true);
		$FirstName = get_post_meta($oid,'_billing_first_name',true);
		$LastName = get_post_meta($oid,'_billing_last_name',true);
		$OrderNo = $oid;

		$source = getOrderType($oid);
		$telemarket = getSource($oid);
		$funnel_traffic_source = getFunnel($oid, 's');

		$payment_plan = getPaymentPlan($oid);
		$instalmentNumber = getInstalmentNumber($oid); 
		$instalmentNumbers = getInstalmentNumbers($oid);
		$instalmentText = '';
		if ( !empty($instalmentNumber) || !empty($instalmentNumbers) ) {
			$instalmentText = $instalmentNumber . '/' . $instalmentNumbers;
		}

		
		$Script = getScript($oid);

		$OrderDate = date('m/d/Y',strtotime($o->post_date));

		$shipped_info = getShipment($oid);
		$ShipDate = $shipped_info['ship_date'];

		$Deposit_Date =  $customer_first_name = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_paid_date' and post_id=$oid");
		if (!empty($Deposit_Date)) {
			$Deposit_Date = date("m/d/Y", strtotime($Deposit_Date));		
		}

		$parentID = getParent($oid);

		// Payment
		$PaymentNo = " ".get_post_meta($oid,'_transaction_id',true);
		$PaymentCode = getPaymentMethod($oid);
		$PaymentMethod = ucfirst(get_post_meta($oid,'_payment_method',true));


		$DocStatus = str_replace("wc-", "", $o->post_status);
		$userID = get_post_meta($oid,'_customer_user',true);
		$CustomerCreationDate =  $wpdb->get_var("select user_registered from {$wpdb->prefix}users where ID = $userID");
		if (!empty($CustomerCreationDate)) {
			$CustomerCreationDate = date("m/d/Y", strtotime($CustomerCreationDate));
		}


		$sql = "
			select  i.order_item_id, i.order_item_name, i.order_item_type
			from {$wpdb->prefix}woocommerce_order_items i
			where i.order_id = $oid
			and i.order_item_type in ('line_item', 'shipping', 'coupon')
			order by i.order_item_type, i.order_item_id
		";

		$items_ids = $wpdb->get_results($sql);


		$product_amt = 0;
		$s_h_amt = 0;
		$tax_amt = 0;
		$discount_amt = 0;
		$product_without_discount = 0;
		$trial_amt = 0;
		$instalment_amt = 0;

		$BackEndCode = '';
		$ProductName = '';

		$Qty_Ordered = 0;

		foreach($items_ids as $item) {

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


			if (getItemType($item->order_item_id) == 'shipping') {
				$exist_sp_line = 1;
			}


			$payment_amount = $product_amt + $s_h_amt + $tax_amt;


			$Qty_Ordered = $Qty_Ordered + $number_of_sku;

			$itemType = getItemType($item->order_item_id);

			if ($itemType == 'line_item') {
				$BackEndCode = $sku . ' x ' . $number_of_sku;

				$ProductName = $sku_desc . ' (' . $number_of_sku . ')';
			}


			$orderItemShippingValues = 0;
			$tax_shipping = 0;
			if (getItemType($item->order_item_id) == 'line_item') {
				$orderItemShippingValues = getOrderItemShippingValues($oid, $item->order_item_id);
				if ( $orderItemShippingValues > 0 ) {
					$orderTaxIDs = $wpdb->get_col("select i.order_item_id from {$wpdb->prefix}woocommerce_order_items i where i.order_item_type = 'tax' and i.order_id = $oid");
					foreach ($orderTaxIDs as $orderTaxID) {
						$tax_rate = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='rate_percent' and order_item_id=$orderTaxID");
						$tax_shipping = $tax_shipping + round($tax_rate * $orderItemShippingValues / 100, 6);
					}
				}
			}


			$Amount = $product_amt + $s_h_amt + $tax_amt + $tax_shipping + $orderItemShippingValues + $instalment_amt;
			$paid_amount = $product_amt + $s_h_amt + $tax_amt + $tax_shipping + $orderItemShippingValues;

			$unpaid_amount = $instalment_amt;
			$unpaid_amount_items = $instalment_amt;
			$unpaid_amount_tax = 0;


			if ($unpaid_amount > 0) {

				$rows[]=[
					$ProjectNumber, 
					$OrderNo, 
					$parentID, 
					$OrderDate, 
					$Deposit_Date, 
					$DocStatus, 
					$source, 
					$telemarket, 
					$funnel_traffic_source, 
					$Zip_Code, 
					$State, 
					$FirstName, 
					$LastName, 
					$PaymentNo, 
					$PaymentMethod, 
					$PaymentCode, 
					$Script, 
					$payment_plan, 
					$instalmentText, 
					$Qty_Ordered, 
					$Amount, 
					$paid_amount, 
					$unpaid_amount, 
					$unpaid_amount_items, 
					$unpaid_amount_tax, 
					$product_without_discount, 
					$discount_amt, 
					$product_amt, 
					$s_h_amt + $orderItemShippingValues, 
					$tax_amt + $tax_shipping, 
					$BackEndCode, 
					$ProductName, 
					$ShipDate, 
					$CustomerCreationDate
				];

			}



		}





	
	}



	$total = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '', '', '', ''];



        // Array data
        $result['all']['array']['header']       = $header;
        $result['all']['array']['unit']         = $units;
        $result['all']['array']['total']        = $total;
        $result['all']['array']['rows']         = $rows;
        $result['all']['array']['sheetname']    = 'Worksheet';
        $result['all']['array']['title']        = 'Payments Uncollected (item)';      

        return json_encode($result);

    }

?>
