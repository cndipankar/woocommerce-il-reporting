<?php

function get_fin_tax_summary($data1, $data2) {

	global $wpdb;
	
	$start_date = $data1;
	$end_date = $data2;

        // get the table rows
       $header = [
		'Project',
		'Order Number',
		'Order Date',
		'Country',
		'State',
		'Zip Code',
		'Customer City',
		'Item Amount',
		'Shipping Amount',
		'Gross Amount',
		'Non Taxable Amount',
		'Exempt Amount',
		'Taxable Amount',
		'Country Rate',
		'Country Tax',
		'County Rate',
		'County Tax',
		'State Rate',
		'State Tax',
		'City Rate',
		'City Tax',
		'Special Rate',
		'Special Tax',
		'Total Rate', 
		'Total Tax'
		];
		
		$order_status_selection = array('wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled');
		$order_type_selection = array('shop_order');
		$order_flow_selection = array('onepay', 'multipay-init', 'subscription-init', 'subscription-renewal');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal

		$project = get_option('options_legacy_scs_client_id');

		$sap="
			select p.ID, p.post_date, p.post_status 
			from {$wpdb->posts} p 
			left join {$wpdb->postmeta} m on p.ID = m.post_id and m.meta_key = '_paid_date'
			where 1=1
			and p.post_type in ('".implode("','", $order_type_selection)."')
			and p.post_status in ('".implode("','", $order_status_selection)."') 
			and m.meta_value >= '$data1 00:00:00' and m.meta_value <= '$data2 23:59:59'
			order by ID desc
		";

		$orders_ids = $wpdb->get_results($sap);

		foreach ($orders_ids as $o){


			$oid = $o->ID;

			$test_order = get_post_meta($oid,'_test_order',true);
			if ($test_order == 'yes')
				continue;

			if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
				continue;


			$order_date = date('m/d/Y',strtotime($o->post_date));

			$country = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_shipping_country'");
			$state = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_shipping_state'");
			$zip_code = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_shipping_postcode'");
			$county_name=''; // need to do
			$city =  $wpdb->get_var("select meta_value from {$wpdb->postmeta} where post_id=$oid and meta_key='_shipping_city'");
			
	
		
			$no_taxable_amount = 0; 
			$exempt_amount = 0; 			

			$items = $wpdb->get_col("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_type='tax' and order_id=$oid");
			$country_tax = $state_tax = $city_tax = $total_tax = $special_tax = $county_tax = 0;
			$country_rate = $state_rate = $city_rate = $total_rate = $special_rate = $county_rate = 0;
			$special_rate_array = array();
			

			$orderValues = getOrderValues($oid);

			$value_shipping = $orderValues['shipping'];
			$value_items = $orderValues['items'];
			$gross = $orderValues['items'] + $orderValues['shipping'];


			$tax_for_shipping = 0;
			$total_rate = 0;

			foreach ($items as $item){

				$tax_amount = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='tax_amount' and order_item_id=$item");
				$tax_rate = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='rate_percent' and order_item_id=$item");
				$total_tax = $total_tax + $tax_amount;

				$shipping_tax_amount = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='shipping_tax_amount' and order_item_id=$item");
				if ($shipping_tax_amount > 0) {
					$tax_for_shipping = 1;
				}

				$total_tax = $total_tax + $shipping_tax_amount;

				// Tax with previous plugin

				$rate_id = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key = 'rate_id' and order_item_id = $item");
				$label = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key = 'label' and order_item_id = $item");

				$tax_row = $wpdb->get_row("select * from {$wpdb->prefix}woocommerce_tax_rates where tax_rate_id = $rate_id and tax_rate_name = '$label'");

				if (!empty($tax_row)) {
					if (!empty($tax_row->tax_rate_state)) {
						$state_tax = $state_tax + $tax_amount + $shipping_tax_amount;
					} else if (!empty($tax_row->tax_rate_country)) {
						$country_tax = $country_tax + $tax_amount + $shipping_tax_amount;
					}
				} else {
					$check_city_tax = $wpdb->get_row("select * from {$wpdb->prefix}woocommerce_tax_rate_locations where tax_rate_id = $rate_id");
					if(!empty($check_city_tax)) {
						$city_tax = $city_tax + $tax_amount + $shipping_tax_amount;
					}
				}

				// Tax with AvaTax
				$tax_type = $wpdb->get_var("select meta_value tax_type from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='label' and order_item_id=$item");
				if ( in_array($tax_type, array('State Sales Tax', 'State Use Tax', 'State Tax')) ) { 
					$state_tax = $state_tax + $tax_amount + $shipping_tax_amount;
					$state_rate = $tax_rate;
					$total_rate = $total_rate + $tax_rate;
				} else if ( in_array($tax_type, array('County Sales Tax', 'County Use Tax', 'County Tax')) ) { 
					$county_tax = $county_tax + $tax_amount + $shipping_tax_amount;
					$county_rate = $tax_rate;
					$total_rate = $total_rate + $tax_rate;
				} else if ( in_array($tax_type, array('City Sales Tax', 'City Use Tax', 'City Tax')) ) { 
					$city_tax = $city_tax + $tax_amount + $shipping_tax_amount;
					$city_rate = $tax_rate;
					$total_rate = $total_rate + $tax_rate;
				} else if ( in_array($tax_type, array('Special Sales Tax', 'Special Use Tax', 'Special Tax')) ) { 
					$special_tax = $special_tax + $tax_amount + $shipping_tax_amount;
					if ( !in_array($tax_rate, $special_rate_array) ) {
						$special_rate_array[] = $tax_rate;
					}
					$total_rate = $total_rate + $tax_rate;
				} else if ( in_array($tax_type, array('Country Sales Tax', 'Country Use Tax', 'Country Tax')) ) { 
					$country_tax = $country_tax + $tax_amount + $shipping_tax_amount;
					$country_rate = $tax_rate;
					$total_rate = $total_rate + $tax_rate;
				}


			}


			if ( $tax_for_shipping ) {
				$taxeble_amount = $gross;
				$no_taxable_amount = 0;
			} else {
				$taxeble_amount = $gross - $value_shipping;
				$no_taxable_amount = $value_shipping;
			}


			if (count($special_rate_array) == 0 ) {
				$special_rate = 0;
			} else if (count($special_rate_array) == 1 ) {
				$special_rate = $special_rate_array[0];
			} else {
				$special_rate = json_encode($special_rate_array);
				$special_rate = str_replace(["[", "]", "'", "{", "}", '"'], "", $special_rate);
			}

			$rows[] = array(
				$project, 
				$oid, 
				$order_date, 
				$country, 
				$state, 
				$zip_code, 
				$city, 
				$value_items, 
				$value_shipping, 
				$gross, 
				$no_taxable_amount, 
				$exempt_amount, 
				$taxeble_amount, 
				$country_rate, 
				$country_tax, 
				$county_rate, 
				$county_tax, 
				$state_rate, 
				$state_tax, 
				$city_rate, 
				$city_tax, 
				$special_rate, 
				$special_tax,
				$total_rate,  
				$total_tax
			);
		}
        $units = ['','', '', '', '', '', '', '$', '$', '$', '$', '$', '$', '', '$', '', '$', '', '$', '', '$', '', '$', '', '$'];
        $total = ['','', '', '', '', '' ,'' , 1, 1, 1, 1, 1, 1, '', 1, '', 1, '', 1, '', 1, '', 1, '', 1];
//die();

        // Array data
        $result['all']['array']['header']       = $header;
        $result['all']['array']['unit']         = $units;
        $result['all']['array']['total']        = $total;
        $result['all']['array']['rows']         = $rows;
        $result['all']['array']['sheetname']    = 'Worksheet';
        $result['all']['array']['title']        = 'Sales Tax Summary Report';
        

        return json_encode($result);

    }

?>
