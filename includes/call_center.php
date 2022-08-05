<?php

	function get_call_center($data1, $data2) {

	global $wpdb;
		
		$start_date = $data1;
		$end_date = $data2;

		$order_status_selection = array('wc-failed', 'wc-cancelled', 'wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
		$order_type_selection = array('shop_order');


		// get the table rows
		$header = array('Project', 'Customer Number', 'Order Number',  'Reason Code', 'Reason Description', '2nd Reason Code', '2nd Reason Description ', 'Comment Detail', 'Order Date', 'Customer First', 'Customer Last');


		$project = get_option('options_legacy_scs_client_id');


		$sql = "
			select p.ID, p.post_date, p.post_type, p.post_status
			from {$wpdb->posts} p
			where 1=1
			and p.post_type in ('".implode("','", $order_type_selection)."')
			and p.post_status in ('".implode("','", $order_status_selection)."') 
			and p.post_date >= '$data1 00:00:00' and p.post_date <= '$data2 23:59:59'
			order by p.post_date desc
		";


		$project = get_option('options_legacy_scs_client_id');

		$orders_ids = $wpdb->get_results($sql);

		foreach ($orders_ids as $o) {

			$oid = $o->ID;

			$order_date = date('m/d/Y',strtotime($o->post_date));
			$order_status = str_replace("wc-", "", $o->post_status);

			$customer_user = get_post_meta($oid,'_customer_user',true);
			$billing_first_name = get_post_meta($oid,'_billing_first_name',true);
			$billing_last_name = get_post_meta($oid,'_billing_last_name',true);

			$first_dispostion = get_post_meta($oid,'first_dispostion',true);
			$second_dispostion = get_post_meta($oid,'second_dispostion',true);

			if (!empty($first_dispostion)) {
				$dispostion1 = get_post($first_dispostion);
				$reason1_code = $dispostion1->post_name;
				$reason1_desc = $dispostion1->post_title;

			} else {
				$reason1_code = '';
				$reason1_desc = '';
			}

			if (!empty($second_dispostion)) {
				$dispostion2 = get_post($second_dispostion);
				$reason1_code = $dispostion2->post_name;
				$reason1_desc = $dispostion2->post_title;

			} else {
				$reason2_code = '';
				$reason2_desc = '';
			}

			$dispostion_reason = get_post_meta($oid,'dispostion_reason',true);


			if (!empty($first_dispostion) || !empty($second_dispostion) || !empty($dispostion_reason)) {
				$rows[] = array($project, $customer_user, $oid,  $reason1_code, $reason1_desc, $reason2_code, $reason2_desc, $dispostion_reason, $order_date, $billing_first_name, $billing_last_name);
			}

		}




		$units = array('', '', '',  '', '', '', '', '', '', '', '');
		$total = array('', '', '',  '', '', '', '', '', '', '', '');

		// Array data
		$result['all']['array']['header']	 = $header;
		$result['all']['array']['unit']		 = $units;
		$result['all']['array']['total']	 = $total;
		$result['all']['array']['rows']		 = $rows;
		$result['all']['array']['sheetname']	 = 'Worksheet';
		$result['all']['array']['title']	  = 'Call Center Comments by Date with Details';
		

		return json_encode($result);

	}

?>
