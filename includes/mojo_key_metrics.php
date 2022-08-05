<?php

    function get_mojo_key_metrics($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

        // get the table rows
        $header = array();
        $units = array();
        $total = array();
        $rows = array();


	$order_status_selection = array('wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
	$order_type_selection = array('shop_order');
	$order_flow_selection = array('onepay', 'multipay-init', 'subscription-init', 'subscription-renewal');	// All values: onepay; multipay-init; multipay-pay; subscription-init; subscription-subscription; subscription-trial; subscription-renewal; backorder

	$header = array(
		'Date',
		'Version',
		'Visits',
		'Visitors',
		'Orders',
		'Units Sold',
		'Total Sales',
		'Total Items',
		'Total Shipping',
		'Total Tax',
		'Average Sale for Total Sales',
		'Average Sale Items', 
		'Conversion Rate',
	);

	$sql = "
		select p.ID, p.post_date, p.post_status, p.post_type, IFNULL(c.idvisit, '') idvisit, IFNULL(c.idvisitor, '') idvisitor
		from {$wpdb->posts} p
		left join {$wpdb->prefix}matomo_log_conversion c on c.idorder = p.ID
		where 1=1
		and p.post_type in ('".implode("','", $order_type_selection)."')
		and p.post_status in ('".implode("','", $order_status_selection)."') 
		and p.post_date >= '$data1 00:00:00' and p.post_date <= '$data2 23:59:59' 
		order by p.post_date
	";

	$orders_ids = $wpdb->get_results($sql);

	$project = get_option('options_legacy_scs_client_id');
		

	$min_date = '';

	$orders = array();

	foreach($orders_ids as $o) {

			$oid = $o->ID;

			$test_order = get_post_meta($oid,'_test_order',true);
			if ($test_order == 'yes')
				continue;

			$order_type = getOrderTypeDetailed($oid);
			if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
				continue;

			$funnel = trim(getFunnel($oid, 'c'));
			$site_version = '';

			if ( $funnel == 'organic' ) {
				$affiliate_type = 'main';
			} else if ( empty($funnel) ) {
				$affiliate_type = 'main';
			} else if ( !empty($funnel) ) {
				$affiliate_type = $wpdb->get_var("select affiliate_type from {$wpdb->prefix}custom_affiliate where custom_slug = '$funnel'");
				$site_version = $wpdb->get_var("select ca_title from {$wpdb->prefix}custom_affiliate where custom_slug = '$funnel'");
			}
			if ( empty($affiliate_type) || $affiliate_type == 'main' ) {
				$funnel = 'main';
			}
			if ( $funnel == 'main' && empty($site_version) ) {
				$site_version = 'Main';
			}


			$orderValues = getOrderValues($oid);

			$val_items = $orderValues['items'];
			$val_shipping = $orderValues['shipping'];
			$val_tax = $orderValues['tax'];

			$val_total = $val_items + $val_shipping + $val_tax;

			$date = date('Y-m-d', strtotime($o->post_date));
			if ( empty($min_date) || $min_date > $date ) {
				$min_date = $date;
			}

			if (!array_key_exists($date, $orders) || (array_key_exists($date, $orders) && !array_key_exists($funnel, $orders[$date]) )) {
				$orders[$date][$funnel]['funnel'] = $site_version;
				$orders[$date][$funnel]['visitors'] = 0;
				$orders[$date][$funnel]['visits'] = 0;
				$orders[$date][$funnel]['orders'] = 0;
				$orders[$date][$funnel]['units'] = 0;
				$orders[$date][$funnel]['total'] = 0;
				$orders[$date][$funnel]['items'] = 0;
				$orders[$date][$funnel]['shipping'] = 0;
				$orders[$date][$funnel]['tax'] = 0;
			}

			$orders[$date][$funnel]['orders']++;
			$orders[$date][$funnel]['total'] = $orders[$date][$funnel]['total'] + $val_total;
			$orders[$date][$funnel]['items'] = $orders[$date][$funnel]['items'] + $val_items;
			$orders[$date][$funnel]['shipping'] = $orders[$date][$funnel]['shipping'] + $val_shipping;
			$orders[$date][$funnel]['tax'] = $orders[$date][$funnel]['tax'] + $val_tax;



			$sql = "
				select  i.order_item_id, i.order_item_name, i.order_item_type
				from {$wpdb->prefix}woocommerce_order_items i
				where i.order_id = $oid
				and i.order_item_type in ('line_item', 'shipping', 'coupon')
				order by i.order_item_type, i.order_item_id
			";

			$items_ids = $wpdb->get_results($sql);


			$order_number_of_sku = 0;
			$order_number_of_item = 0;

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


				$order_number_of_sku = $order_number_of_sku + $number_of_sku;
				$order_number_of_item =$order_number_of_item + $number_of_sku;

			}
		


			$orders[$date][$funnel]['units'] = $orders[$date][$funnel]['units'] + $order_number_of_sku;


	}



	// Unique visitors

	if ( $data1 < $min_date ) {
		$data1 = $min_date;
	}

	$sql = "
		select DATE_FORMAT(v.visit_first_action_time, '%Y-%c-%d') visit_first_action_time, v.custom_dimension_1, COUNT(DISTINCT(v.idvisitor)) visitors
		from {$wpdb->prefix}matomo_log_visit v
		where 1=1
		and v.visit_first_action_time >= '$data1 00:00:00' and v.visit_first_action_time <= '$data2 23:59:59' 
		group by DATE_FORMAT(v.visit_first_action_time, '%Y-%c-%d'), v.custom_dimension_1
		order by DATE_FORMAT(v.visit_first_action_time, '%Y-%c-%d')
	";


	$visitors_ids = $wpdb->get_results($sql);

	foreach($visitors_ids as $v) {

		$funnel = $v->custom_dimension_1;
		$site_version = '';

		if ( $funnel == 'organic' ) {
			$affiliate_type = 'main';
		} else if ( empty($funnel) ) {
			$affiliate_type = 'main';
		} else if ( !empty($funnel) ) {
			$affiliate_type = $wpdb->get_var("select affiliate_type from {$wpdb->prefix}custom_affiliate where custom_slug = '$funnel'");
			$site_version = $wpdb->get_var("select ca_title from {$wpdb->prefix}custom_affiliate where custom_slug = '$funnel'");
		}
		if ( empty($affiliate_type) || $affiliate_type == 'main' ) {
			$funnel = 'main';
		}
		if ( $funnel == 'main' && empty($site_version) ) {
			$site_version = 'Main';
		}

		$date = date('Y-m-d', strtotime($v->visit_first_action_time));

		if (!array_key_exists($date, $orders) || (array_key_exists($date, $orders) && !array_key_exists($funnel, $orders[$date]) )) {
			$orders[$date][$funnel]['funnel'] = $site_version;
			$orders[$date][$funnel]['visitors'] = 0;
			$orders[$date][$funnel]['visits'] = 0;
			$orders[$date][$funnel]['orders'] = 0;
			$orders[$date][$funnel]['units'] = 0;
			$orders[$date][$funnel]['total'] = 0;
			$orders[$date][$funnel]['items'] = 0;
			$orders[$date][$funnel]['shipping'] = 0;
			$orders[$date][$funnel]['tax'] = 0;
		}

		$orders[$date][$funnel]['visitors'] = $orders[$date][$funnel]['visitors'] + $v->visitors;

	}




	// Visits

	$sql = "
		select DATE_FORMAT(v.visit_first_action_time, '%Y-%c-%d') visit_first_action_time, v.custom_dimension_1, COUNT(*) visits
		from {$wpdb->prefix}matomo_log_visit v
		where 1=1
		and v.visit_first_action_time >= '$data1 00:00:00' and v.visit_first_action_time <= '$data2 23:59:59' 
		group by DATE_FORMAT(v.visit_first_action_time, '%Y-%c-%d'), v.custom_dimension_1
		order by DATE_FORMAT(v.visit_first_action_time, '%Y-%c-%d')
	";


	$visitors_ids = $wpdb->get_results($sql);

	foreach($visitors_ids as $v) {

		$funnel = $v->custom_dimension_1;
		$site_version = '';

		if ( $funnel == 'organic' ) {
			$affiliate_type = 'main';
		} else if ( empty($funnel) ) {
			$affiliate_type = 'main';
		} else if ( !empty($funnel) ) {
			$affiliate_type = $wpdb->get_var("select affiliate_type from {$wpdb->prefix}custom_affiliate where custom_slug = '$funnel'");
			$site_version = $wpdb->get_var("select ca_title from {$wpdb->prefix}custom_affiliate where custom_slug = '$funnel'");
		}
		if ( empty($affiliate_type) || $affiliate_type == 'main' ) {
			$funnel = 'main';
		}
		if ( $funnel == 'main' && empty($site_version) ) {
			$site_version = 'Main';
		}


		$date = date('Y-m-d', strtotime($v->visit_first_action_time));

		if (!array_key_exists($date, $orders) || (array_key_exists($date, $orders) && !array_key_exists($funnel, $orders[$date]) )) {
			$orders[$date][$funnel]['funnel'] = $site_version;
			$orders[$date][$funnel]['visitors'] = 0;
			$orders[$date][$funnel]['visits'] = 0;
			$orders[$date][$funnel]['orders'] = 0;
			$orders[$date][$funnel]['units'] = 0;
			$orders[$date][$funnel]['total'] = 0;
			$orders[$date][$funnel]['items'] = 0;
			$orders[$date][$funnel]['shipping'] = 0;
			$orders[$date][$funnel]['tax'] = 0;
		}

		$orders[$date][$funnel]['visits'] = $orders[$date][$funnel]['visits'] + $v->visits;

	}


	ksort($orders);

	foreach($orders as $d => $orderFunnel) {
		foreach($orderFunnel as $f => $o) {


			if ($o['orders'] > 0) {
				$aov = $o['total'] / $o['orders'];
				$aot = $o['items'] / $o['orders'];
			} else {
				$aov = 0;
				$aot = 0;
			}

			if ($o['visitors'] > 0) {
				$conversion = round($o['orders'] / $o['visitors'] * 100, 2);
			} else {
				$conversion = 0;
			}


			$rows[]=[
				$d,
				$o['funnel'] . " ($f)",
				$o['visits'],
				$o['visitors'],
				$o['orders'],
				$o['units'], 
				$o['total'],
				$o['items'],
				$o['shipping'],
				$o['tax'],
				$aov,
				$aot,
				$conversion,
			];


		}
	}




	$total = array('', '', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '');
	$units = array('', '', '', '', '', '', '$', '$', '$', '$', '$', '$', '%');


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
