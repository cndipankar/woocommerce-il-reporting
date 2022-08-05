<?php


class WC_REST_Custom_Reports_Controller {

	protected $namespace = 'wc/v3';

	protected $rest_get_orders = 'custom-reports-get-orders';

	protected $key = "04e1b9b67b554bf29f53b19d2ac9c25b";
	protected $productID = 13110;
	protected $contentVersionId0 = 60000;


	public function custom_reports_get_orders( $data ) {

		global $woocommerce;
		global $wpdb;

		$parameters = $data->get_params();
		$authorization = $data->get_header("Authorization");


		// Check authorization

		$key = "";
		if ( !empty($authorization) ) {
			$key = trim( str_replace("ApiKey", "", $authorization) );
		}

		if ( empty($key) || $key != $this->key ) {
			return array("error" => "Invalid security key");
		}


		// Check parameters

		$data1 = (!empty($parameters['create_from']) ? $parameters['create_from'] : '');
		$data2 = (!empty($parameters['create_to']) ? $parameters['create_to'] : '');

		$data1 = str_replace( "T", "", $data1 );
		$data2 = str_replace( "T", "", $data2 );


		if ( (bool)strtotime($data1) == false || (bool)strtotime($data2) == false ) {
			return array("error" => "Invalid parameters");
		}

		$order_status_selection = array('wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
		$order_type_selection = array('shop_order');
		$order_flow_selection = array('onepay', 'multipay-init', 'subscription-init', 'subscription-renewal', 'backorder');	// All values: onepay; multipay-init; multipay-pay; subscription-init; subscription-subscription; subscription-trial; subscription-renewal; backorder

		$sql = "
			select p.ID, p.post_date, p.post_type, p.post_status, m.meta_value paid_date
			from {$wpdb->posts} p, {$wpdb->postmeta} m
			where p.ID = m.post_id
			and m.meta_key = '_paid_date'
			and p.post_type in ('".implode("','", $order_type_selection)."')
			and p.post_status in ('".implode("','", $order_status_selection)."') 
			and m.meta_value >= '$data1' and m.meta_value <= '$data2'
			order by p.post_date desc
		";
//return array("debug" => $sql);

		$orders_ids = $wpdb->get_results($sql);

		$orders = array();
		foreach( $orders_ids as $o ) {

			$oid = $o->ID;

			$order['orderId'] = $o->ID;
			$order['orderNumber'] = $o->ID;
			$order['extendedOrderNumber'] = '';

			$order['productId'] = $this->productID;

			//$funnel = trim(getFunnel($oid, 'c'));
			//$funnel_traffic_source = getFunnel($oid, 's');


			$funnel = get_post_meta($oid,'order_custom_affiliate_slug',true);

			if ( !empty($funnel) ) {
				$funnel_traffic_source = $wpdb->get_var("select traffic_source from {$wpdb->prefix}custom_affiliate where custom_slug = '$funnel'");
				$funnel_name = $wpdb->get_var("select ca_title from {$wpdb->prefix}custom_affiliate where custom_slug = '$funnel'");
			} else {
				$funnel_traffic_source = 'main';
				$funnel_name = 'main';
			}

			if ( $funnel_traffic_source == 'main' ) {
				$contentVersionId = $this->contentVersionId0;
			} else {
				$contentVersionId = $this->contentVersionId0 + (int)$wpdb->get_var("select ca_id from {$wpdb->prefix}custom_affiliate where custom_slug = '$funnel'");
			}


			$order['c'] 				= $funnel;
			$order['traffic_source'] 		= $funnel_traffic_source;
			$order['funnel_name'] 			= $funnel_name;
			$order['contentVersionId'] 		= $contentVersionId;

			$order['transactionFee'] 		= '';
			$order['firstPaymentTransactionFee'] 	= '';
			$order['isTaxIncludedInPrices'] 	= '';

			$order['createDate'] 			= str_replace( " ", "T", $o->post_date );
			$order['closeDate'] 			= str_replace( " ", "T", $o->post_date );
			$order['processingDate'] 		= str_replace( " ", "T", $o->post_date );
			$order['status'] 			= 'processed';

			$order['validationStatus']['orderValidationResult']		= "valid";
			$order['validationStatus']['creditCardValidationResult']	= "valid";
			$order['validationStatus']['billingValidationResult']		= "valid";
			$order['validationStatus']['shippingValidationResult']		= "valid";
			$order['validationStatus']['promoCodeValidationResult']		= "valid";
			$order['validationStatus']['additionalDataValidationResult']	= "valid";

			$order['isDebug']			= "";
			$order['reconciliationStatus']		= "not_required";
			$order['orderSource']			= "sites";


			$order['customerInfo']['shippingAddress']['firstName'] 		= get_post_meta($oid,'_shipping_first_name',true);
			$order['customerInfo']['shippingAddress']['lastName'] 		= get_post_meta($oid,'_shipping_last_name',true);
			$order['customerInfo']['shippingAddress']['email'] 		= get_post_meta($oid,'_billing_email',true);
			$order['customerInfo']['shippingAddress']['company'] 		= "";
			$order['customerInfo']['shippingAddress']['address1'] 		= get_post_meta($oid,'_shipping_address_1',true);
			$order['customerInfo']['shippingAddress']['address2'] 		= "";
			$order['customerInfo']['shippingAddress']['phone'] 		= get_post_meta($oid,'_billing_phone',true);
			$order['customerInfo']['shippingAddress']['city'] 		= get_post_meta($oid,'_shipping_city',true);
			$order['customerInfo']['shippingAddress']['zip'] 		= get_post_meta($oid,'_shipping_postcode',true);
			$order['customerInfo']['shippingAddress']['stateCode'] 		= get_post_meta($oid,'_shipping_state',true);
			$order['customerInfo']['shippingAddress']['countryCode']	= get_post_meta($oid,'_shipping_country',true);

			$order['customerInfo']['billingAddress']['firstName'] 		= get_post_meta($oid,'_billing_first_name',true);
			$order['customerInfo']['billingAddress']['lastName'] 		= get_post_meta($oid,'_billing_last_name',true);
			$order['customerInfo']['billingAddress']['email'] 		= get_post_meta($oid,'_billing_email',true);
			$order['customerInfo']['billingAddress']['company'] 		= "";
			$order['customerInfo']['billingAddress']['address1'] 		= get_post_meta($oid,'_billing_address_1',true);
			$order['customerInfo']['billingAddress']['address2'] 		= "";
			$order['customerInfo']['billingAddress']['phone'] 		= get_post_meta($oid,'_billing_phone',true);
			$order['customerInfo']['billingAddress']['city'] 		= get_post_meta($oid,'_billing_city',true);
			$order['customerInfo']['billingAddress']['zip'] 		= get_post_meta($oid,'_billing_postcodee',true);
			$order['customerInfo']['billingAddress']['stateCode'] 		= get_post_meta($oid,'_billing_state',true);
			$order['customerInfo']['billingAddress']['countryCode']		= get_post_meta($oid,'_billing_country',true);

			$order['customerInfo']['addressPriority']			= "shipping";
			$order['customerInfo']['zipMismatchConfirmed']			= "";
			$order['customerInfo']['isSubscribedEmail']			= "";
			$order['customerInfo']['promoCode']				= "";
			$order['customerInfo']['promoCodeType']				= "";



			$sql = "
				select  i.order_item_id, i.order_item_name, i.order_item_type
				from {$wpdb->prefix}woocommerce_order_items i
				where i.order_id = $oid
				and i.order_item_type in ('line_item')
				order by i.order_item_type, i.order_item_id
			";

			$items_ids = $wpdb->get_results($sql);

			$total_price = 0;
			$total_tax = 0;
			$total_shipping = 0;
			$total_total = 0;
			$total_discount = 0;
			$total_first_payment = 0;

			$items = array();

			foreach($items_ids as $i) {

				$orderItemValues 		= getOrderItemValues($i->order_item_id, 1);
				$instalmentValues 		= getInstalmentItemValues($i->order_item_id, 1);
				$orderItemShippingValues 	= getOrderItemShippingValues($oid, $i->order_item_id);
//$item['debug'] 	= $orderItemShippingValues;

				$price				= $orderItemValues['items'] + $instalmentValues['items'] + $orderItemValues['trial'];
				$tax				= $orderItemValues['tax'] + $instalmentValues['tax'];
				// $shipping			= round( $orderItemValues['shipping'] + $orderItemShippingValues, 2 );
				$shipping 			= $orderItemShippingValues;

				//$total			= $orderItemValues['items'] + $orderItemValues['tax'] + $orderItemValues['shipping'];
				$total				= $orderItemValues['items'] + $orderItemValues['tax'] + $shipping;

				$discount			= $orderItemValues['coupon'];
				$first_payment			= $orderItemValues['items'];

				$total_price = $total_price  + $price;
				$total_tax = $total_tax + $tax;
				$total_shipping = $total_shipping + $shipping;
				$total_total = $total_total + $total;
				$total_discount = $total_discount + $discount;
				$total_first_payment = $total_first_payment + $first_payment;

				$item['orderItemId'] 		= $i->order_item_id;
				$item['offerName'] 		= $i->order_item_name;
				$item['sku'] 			= getItemSKU($i->order_item_id);
				$item['quantity'] 		= getItemQty($i->order_item_id);
				$item['price']			= round($price, 4);
				$item['tax']			= round($tax, 4);
				$item['shipping']		= round($shipping, 4);
				$item['discount']		= round($discount, 4);
				$item['bonus']			= 0;
				$item['total']			= round($total, 4);
				$item['firstPayment']		= round($first_payment, 4);
				$item['title']			= getItemDescription($i->order_item_id);
				$item['isModifiedByPromoCode']	= 0;
				$item['paymentPlan']		= "";
				$item['offerId']		= "";

				$items[] = $item;


			}

			$order['items'] = $items;


			//$order['orderTotalPrice'] 		= round($total_price, 2);
			$order['orderTotalPrice'] 		= round($total_total, 2);

			$order['orderShippingPrice'] 		= round($total_shipping, 2);
			$order['orderTaxPrice'] 		= round($total_tax, 2);
			$order['orderBonusPrice']		= 0;
			$order['firstPaymentAmount']		= round($total_first_payment, 2);
			$order['discount']  			= round($total_discount, 2);



			$order['paymentInfo']['paymentMethod']		= "merchant";
			$order['paymentInfo']['merchantType']		= "vantiv";
			$order['paymentInfo']['cardToken']		= "";
			$order['paymentInfo']['payPalPaymentInfo']	= "";
			$order['paymentInfo']['breadPaymentInfo']	= "";

			$merchantTransactions = array();

			$merchantTransaction['mojoTransactionId'] 		= get_post_meta($oid, '_transaction_id', true);
			$merchantTransaction['transactionType']			= 'authorize';
			$merchantTransaction['transactionId'] 			= get_post_meta($oid, '_transaction_id', true);
			$merchantTransaction['authCode'] 			= '0';
			$merchantTransaction['transactionResult'] 		= 'approved';
			$merchantTransaction['merchantTransactionResult'] 	= '000';
			$merchantTransaction['merchantResultDetails'] 		= 'Approved';
			$merchantTransaction['amount'] 				= 0;
			$merchantTransaction['additionalFeeAmount'] 		= 0;
			$merchantTransaction['isCanceled'] 			= '';
			$merchantTransaction['merchantResponseReasonCode'] 	= 'M';
			$merchantTransaction['avsResponse'] 			= '';
			$merchantTransaction['avsResponseCode'] 		= '00';
			$merchantTransaction['createDateTime'] 			= str_replace( " ", "T", $o->post_date );

			$merchantTransactions[] = $merchantTransaction;
			$order['paymentInfo']['merchantTransactions']	= $merchantTransactions;



			$orders[] = $order;

		}

		$result = array( "items" => $orders, "count" => count($orders), firstItemIndex=>0 );


		return $result;
	}



	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_get_orders,
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'custom_reports_get_orders' ),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_get_orders,
			array(
				'methods' => 'GET',
				'callback' => array( $this, 'custom_reports_get_orders' ),
				'permission_callback' => '__return_true',
			)
		);

	}



}