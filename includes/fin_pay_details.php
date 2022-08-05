<?php

    function get_fin_pay_details($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

	//$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request');
	$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'multipay-pay', 'subscription-init', 'subscription-renewal');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal

        // get the table rows
        $header = ['ProjectNumber','Payment#','Order#','Order Date', 'Deposit_Date', 'Doc Status', 'Order Type', 'Order Source', 'Traffic Source', 'Zip_Code','State','FirstName','LastName','PaymentNo', 'Script', 'Payment Plan', 'Installment number', 'Payment Code','Pmt_Type','Qty Ordered','Amount','Discount', 'Product w/o Discount','Product Amt','S&P Amt','TaxAmt', 'Brand', 'SKU','Product Name', 'Doc ShipDate', 'Tracking number', 'Customer CreationDate '];
        $units = array('','','','','','','','','','','','','', '', '', '', '', '', '', '' , '$','$','$','$','$','$', '', '', '', '', '');
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
			$amount_without_discount = $Amount + abs($discount_amt);

			//$ProductName = $item->order_item_name;
			$ProductName = getItemDescription($item->order_item_id);

			$category = getItemCategory($item->order_item_id);

			if ( empty($parentID) ) {
				$parentID = $OrderNo;
			}

			if ($Amount > 0 || $discount_amt != 0 || $Pmt_Type == 'item' || $Pmt_Type == 'line_item') {
				$rows[]=[$ProjectNumber, $OrderNo, $parentID, $OrderDate, $Deposit_Date, $DocStatus, $Source, $Telemarketer, $funnel_traffic_source, $Zip_Code, $State, $FirstName, $LastName, $PaymentNo, $Script, $payment_plan, $instalmentText, $PaymentCode, $Pmt_Type, $Qty_Ordered, $Amount, $discount_amt, $product_without_discount, $product_amt, $s_h_amt, $tax_amt, $category, $BackEndCode, $ProductName, $ShipDate, $tracking_number, $CustomerCreationDate];
			}


		}

	}


	$total = ['', '', '', '','', '',  '', '', '', '', '', '', '', '', '',  '', '', '', '', '', 1, 1, 1, 1, 1, 1, '', '', '', '', ''];


        // Array data
        $result['all']['array']['header']       = $header;
        $result['all']['array']['unit']         = $units;
        $result['all']['array']['total']        = $total;
        $result['all']['array']['rows']         = $rows;
        $result['all']['array']['sheetname']    = 'Worksheet';
        $result['all']['array']['title']        = 'Payments Deposited (item)';      

        return json_encode($result);

    }

?>
