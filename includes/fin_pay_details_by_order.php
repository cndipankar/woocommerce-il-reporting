<?php

    function get_fin_pay_details_by_order($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

	//$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request');
	$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'multipay-pay', 'subscription-init', 'subscription-renewal');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal

        // get the table rows
        $header = ['ProjectNumber','Payment#','Order#','Order Date', 'Deposit_Date', 'Doc Status', 'Order Type', 'Order Source', 'Traffic Source', 'Zip_Code','State','FirstName','LastName','PaymentNo', 'Script', 'Payment Plan', 'Installment number','Payment Code','Pmt_Type','Qty Ordered','Amount','Discount', 'Product w/o Discount','Product Amt','S&P Amt','TaxAmt', 'SKU','Product Name', 'Doc ShipDate', 'Tracking number', 'Customer CreationDate '];
	$units = array('','','','','','','','','','','','','','','','','','','','','$','$','$','$','$','$','','','');
        $total = array();
        $rows = array();


	$ProjectNumber = get_option('options_legacy_scs_client_id');

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
		
		$Source = getOrderType($oid);
		$funnel_traffic_source = getFunnel($oid, 's');

		if ($Source == 'EXCHANGE')
			continue;

		$test_order = get_post_meta($oid,'_test_order',true);
		if ($test_order == 'yes')
			continue;

		$order_type = getOrderTypeDetailed($oid);
		if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
			continue;


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
		$payment_plan = getPaymentPlan($oid);

		$Telemarketer = getSource($oid);

		$Script = getScript($oid);

		$OrderDate = $o->post_date;
		if (!empty($OrderDate)) {
			$OrderDate = date("m/d/Y", strtotime($OrderDate));
		}

		$DocStatus = str_replace("wc-", "", $o->post_status);

		$shipmentInfo = getShipment($oid);
		$ShipDate = $shipmentInfo['ship_date'];
		$tracking_number = $shipmentInfo['tracking_number'];

		$userID = get_post_meta($oid,'_customer_user',true);
		$CustomerCreationDate =  $wpdb->get_var("select user_registered from {$wpdb->prefix}users where ID = $userID");
		if (!empty($CustomerCreationDate)) {
			$CustomerCreationDate = date("m/d/Y", strtotime($CustomerCreationDate));
		}

		$instalmentNumber = getInstalmentNumber($oid);
		$instalmentNumbers = getInstalmentNumbers($oid);

		$instalmentText = '';
		if ( !empty($instalmentNumber) || !empty($instalmentNumbers) ) {
			$instalmentText = $instalmentNumber . '/' . $instalmentNumbers;
		}



		$sql = "
			select  order_item_id, order_item_name, order_item_type 
			from {$wpdb->prefix}woocommerce_order_items i
			where i.order_id = $oid
			order by i.order_item_type, i.order_item_id
		";

		$items_ids = $wpdb->get_results($sql);


		$sum_Qty_Ordered = 0;
		$sum_Amount = 0;
		$sum_discount_amt = 0;
		$sum_product_without_discount = 0;

		$sum_BackEndCode = '';
		$sum_ProductName = '';

		$sum_product_amt = 0;
		$sum_s_h_amt = 0;
		$sum_tax_amt = 0;
		$sum_product_without_discount = 0;


		foreach($items_ids as $item) {

			$BackEndCode = getItemSKU($item->order_item_id);

			$Pmt_Type = getItemType($item->order_item_id);

			$Qty_Ordered =  getItemQty($item->order_item_id);
			
			$OrderItemValues = getOrderItemValues($item->order_item_id, 1);
			$Amount = $OrderItemValues['items'] + $OrderItemValues['shipping'] + $OrderItemValues['tax'];

			$product_amt = $OrderItemValues['items'];
			$s_h_amt = $OrderItemValues['shipping'];
			$tax_amt = $OrderItemValues['tax'];
			$product_without_discount = $OrderItemValues['items_without_discount'];


			$discount_amt = $OrderItemValues['coupon'];
			$product_without_discount = $OrderItemValues['items_without_discount'];

			//$ProductName = $item->order_item_name;
			$ProductName = getItemDescription($item->order_item_id);

			$sum_Qty_Ordered = $sum_Qty_Ordered + $Qty_Ordered;
			$sum_Amount = $sum_Amount + $Amount;


			$sum_product_amt = $sum_product_amt + $product_amt;
			$sum_s_h_amt = $sum_s_h_amt + $s_h_amt;
			$sum_tax_amt = $sum_tax_amt + $tax_amt;
			$sum_product_without_discount = $sum_product_without_discount + $product_without_discount;


			$sum_discount_amt = $sum_discount_amt + $discount_amt;

			
			if ($Pmt_Type == 'line_item') {
				if (!empty($sum_BackEndCode)) {
					$sum_BackEndCode = $sum_BackEndCode . ", ";
				}
				$sum_BackEndCode = $sum_BackEndCode . $BackEndCode . ' x ' . $Qty_Ordered;

				if (!empty($sum_ProductName)) {
					$sum_ProductName = $sum_ProductName . ", ";
				}
				$sum_ProductName = $sum_ProductName . $ProductName . ' (' . $Qty_Ordered . ')';
			}

		}


		if ( empty($parentID) ) {
			$parentID = $OrderNo;
		}

		$Pmt_Type = 'order';
		$rows[]=[$ProjectNumber, $OrderNo, $parentID, $OrderDate, $Deposit_Date, $DocStatus, $Source, $Telemarketer, $funnel_traffic_source, $Zip_Code, $State, $FirstName, $LastName, $PaymentNo, $Script, $payment_plan, $instalmentText, $PaymentCode, $Pmt_Type, $sum_Qty_Ordered, $sum_Amount, $sum_discount_amt, $sum_product_without_discount, $sum_product_amt, $sum_s_h_amt, $sum_tax_amt, $sum_BackEndCode, $sum_ProductName, $ShipDate, $tracking_number, $CustomerCreationDate];

	}


	$total = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, 1, 1, 1, 1, 1, '', '', '', '', ''];


        // Array data
        $result['all']['array']['header']       = $header;
        $result['all']['array']['unit']         = $units;
        $result['all']['array']['total']        = $total;
        $result['all']['array']['rows']         = $rows;
        $result['all']['array']['sheetname']    = 'Worksheet';
        $result['all']['array']['title']        = 'Payments Deposited (order)';      

        return json_encode($result);

    }

?>
