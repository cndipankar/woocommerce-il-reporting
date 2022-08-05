<?php


    function get_mojo_site_version($data1, $data2) {

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
		'Version',
		'Visitors',
		'Orders',
		'Conversion Rate',
		'AOV',
		'Total Sales',
		'Total Items',
		'Total Shipping',
		'Total Tax',
	);

	$sql = "
		select p.ID, p.post_date, p.post_status, p.post_type, IFNULL(c.idvisit, '') idvisit, IFNULL(c.idvisitor, '') idvisitor
		from {$wpdb->posts} p
		left join {$wpdb->prefix}matomo_log_conversion c on c.idorder = p.ID
		where 1=1
		and p.post_type in ('".implode("','", $order_type_selection)."')
		and p.post_status in ('".implode("','", $order_status_selection)."') 
		and p.post_date >= '$data1 00:00:00' and p.post_date <= '$data2 23:59:59' 
		order by p.ID desc
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
			if ( $affiliate_type == 'main' ) {
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


			if (!array_key_exists($funnel, $orders)) {
				$orders[$funnel]['funnel'] = $site_version;
				$orders[$funnel]['visitors'] = 0;
				$orders[$funnel]['orders'] = 0;
				$orders[$funnel]['total'] = 0;
				$orders[$funnel]['items'] = 0;
				$orders[$funnel]['shipping'] = 0;
				$orders[$funnel]['tax'] = 0;
			}

			$orders[$funnel]['orders']++;
			$orders[$funnel]['total'] = $orders[$funnel]['total'] + $val_total;
			$orders[$funnel]['items'] = $orders[$funnel]['items'] + $val_items;
			$orders[$funnel]['shipping'] = $orders[$funnel]['shipping'] + $val_shipping;
			$orders[$funnel]['tax'] = $orders[$funnel]['tax'] + $val_tax;

	}



	if ( $data1 < $min_date ) {
		$data1 = $min_date;
	}

	/*
	$sql = "
		select v.custom_dimension_1, COUNT(DISTINCT(v.idvisitor)) visitors
		from {$wpdb->prefix}matomo_log_visit v
		where 1=1
		and v.visit_first_action_time >= '$data1 00:00:00' and v.visit_first_action_time <= '$data2 23:59:59' 
		group by v.custom_dimension_1
	";
	*/

	$sql = "
		SELECT custom_dimension_1, SUM(visitors) visitors FROM
		(
		select DATE_FORMAT(v.visit_first_action_time, '%Y-%c-%d') visit_first_action_time, v.custom_dimension_1, COUNT(DISTINCT(v.idvisitor)) visitors
		from {$wpdb->prefix}matomo_log_visit v
		where 1=1
		and v.visit_first_action_time >= '$data1 00:00:00' and v.visit_first_action_time <= '$data2 23:59:59' 
		group by DATE_FORMAT(v.visit_first_action_time, '%Y-%c-%d'), v.custom_dimension_1
		order by DATE_FORMAT(v.visit_first_action_time, '%Y-%c-%d')
		) A
		GROUP BY custom_dimension_1
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


		if (!array_key_exists($funnel, $orders)) {
			$orders[$funnel]['funnel'] = $site_version;
			$orders[$funnel]['visitors'] = 0;
			$orders[$funnel]['orders'] = 0;
			$orders[$funnel]['total'] = 0;
			$orders[$funnel]['items'] = 0;
			$orders[$funnel]['shipping'] = 0;
			$orders[$funnel]['tax'] = 0;
		}

		$orders[$funnel]['visitors'] = $orders[$funnel]['visitors'] + $v->visitors;

	}



	foreach($orders as $key => $o) {

		if ( (int)$o['orders'] > 0 ) {
			$aov = round( (int)$o['total'] / (int)$o['orders'], 6 );
		} else {
			$aov = 0;
		}

		if ( (int)$o['visitors'] > 0 ) {
			$conversion = round( (int)$o['orders'] / (int)$o['visitors'] * 100, 6 );
		} else {
			$conversion = 0;
		}


		$rows[]=[
			$o['funnel'] . " ($key)",
			$o['visitors'],
			$o['orders'],
			$conversion,
			$aov,
			$o['total'],
			$o['items'],
			$o['shipping'],
			$o['tax']
		];

	}




	$total = array('', '1', '1', '', '1', '1', 1, 1, 1);
	$units = array('', '', '', '%', '$', '$', '$', '$', '$');


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
