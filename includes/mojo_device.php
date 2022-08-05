<?php

    function get_mojo_device($data1, $data2) {

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
		'Project',
		'Order ID',
		'Order Date',
		'Status',
		'Order Type',
		'Order Source',
		'Traffic Source',
		'Visit ID',
		'Visit Date',
		'Visit Time',
		'Total Sale w S&P',
		'Items',
		'S&P',
		'Tax',
		'Device Type',
		'Device Model',
		'Device OS',
		'Browser',
		'Visitor IP',
		'Production Version',
	);

	$sql = "
		select p.ID, p.post_date, p.post_status, p.post_type, c.idvisit, c.idvisitor
		from {$wpdb->posts} p
		left join {$wpdb->prefix}matomo_log_conversion c on c.idorder = p.ID and c.idorder is not null
		where 1=1
		and p.post_type in ('".implode("','", $order_type_selection)."')
		and p.post_status in ('".implode("','", $order_status_selection)."') 
		and p.post_date >= '$data1 00:00:00' and p.post_date <= '$data2 23:59:59' 
		order by p.ID desc
	";

	$orders_ids = $wpdb->get_results($sql);

	$project = get_option('options_legacy_scs_client_id');
		


	foreach($orders_ids as $o) {

			$oid = $o->ID;

			$test_order = get_post_meta($oid,'_test_order',true);
			if ($test_order == 'yes')
				continue;

			$order_type = getOrderTypeDetailed($oid);
			if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
				continue;

			$status = str_replace("wc-", "", $o->post_status);
			$order_date = date('m/d/Y',strtotime($o->post_date));

			$source = getOrderType($oid);
			$funnel_traffic_source = getFunnel($oid, 's');
			$telemarket = getSource($oid);

			$funnel = getFunnel($oid);
			$orderValues = getOrderValues($oid);

			$val_items = $orderValues['items'];
			$val_shipping = $orderValues['shipping'];
			$val_tax = $orderValues['tax'];

			$val_total = $val_items + $val_shipping + $val_tax;

			$idvisit = $o->idvisit;
			$idvisitor = $o->idvisitor;


			$visitDetails = getVisitDetails($idvisit);



			$rows[]=[
				$project,
				$oid,
				$order_date,
				$status,
				$source,
				$telemarket,
				$funnel_traffic_source,
				$idvisit,
				$visitDetails['visit_date'],
				$visitDetails['visit_time'],
				$val_total,
				$val_items,
				$val_shipping,
				$val_tax,
				$visitDetails['device'],
				$visitDetails['brand'],
				$visitDetails['os'],
				$visitDetails['browser'],
				$visitDetails['ip'],
				$funnel
			];

	}

	$total = array('', '', '', '', '', '', '', '', '', '', 1, 1, 1, 1, '', '', '', '', '');
	$units = array('', '', '', '', '', '', '', '', '', '', '$', '$', '$', '$', '', '', '', '', '');


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
