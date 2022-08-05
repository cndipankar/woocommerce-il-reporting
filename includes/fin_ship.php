<?php

function get_fin_ship($data1, $data2) {

	global $wpdb;
		
		$start_date = $data1;
		$end_date = $data2;

		$order_status_selection = array('wc-on-hold', 'wc-pending', 'wc-pending-deposit', 'wc-completed', 'wc-shipped', 'wc-processing', 'wc-refunded', 'wc-partial-payment', 'wc-scheduled-payment', 'wc-active', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled', 'wc-exchange-request', 'wc-pending-cancel');
		$order_type_selection = array('shop_order');
		$order_flow_selection = array('onepay', 'multipay-init', 'subscription-init', 'subscription-renewal', 'backorder');	// All values: onepay , multipay-init , multipay-pay , subscription-init , subscription-subscription , subscription-renewal



		// get the table rows
		$header = array(
			'ProjectNo',
			'Order No', 
			'Order Date', 
			'Status', 
			'Order Type',
			'Order Source',	
			'Traffic Source',
			'Payment Plan', 
			'SKU', 
			'Name', 
			'SKU Qty', 
			'Item Qty', 
			'Ship Date', 
			'Qty Shipped', 
		);
		$units = array();
		$total = array();
		$rows = array();
		
		$sql = "
			select p.ID, p.post_date, p.post_status 
			from {$wpdb->posts} p
			left join {$wpdb->postmeta} m on p.ID = m.post_id and m.meta_key = '_paid_date'
			where 1=1
			and p.post_type in ('".implode("','", $order_type_selection)."')
			and p.post_status in ('".implode("','", $order_status_selection)."')
			and p.post_date >= '$data1 00:00:00' and p.post_date <= DATE_ADD('$data2 23:59:59', INTERVAL +30 DAY)
			order by m.meta_value desc
		";
		//echo $sql; die();

		$project_no = get_option('options_legacy_scs_client_id');

		$orders_ids = $wpdb->get_results($sql);

		$tot_qty = 0;

		foreach($orders_ids as $o) {

			$oid = $o->ID;

			$status = str_replace("wc-", "", $o->post_status);


			$test_order = get_post_meta($oid, '_test_order', true);
			if ($test_order == 'yes')
				continue;

			if ( !in_array(getOrderTypeDetailed($oid), $order_flow_selection) )
				continue;



			$shipInfo = getShipment($oid);



			if ( empty($shipInfo['ship_date']) && $status!='shipped' ) {
				continue;
			}

			$ship_date = date("Y-m-d", strtotime($shipInfo['ship_date']));
			if ($ship_date > "$data2 23:59:59" || $ship_date < "$data1 00:00:00") {
				continue;
			}



			$ShipDate = $shipInfo['ship_date'];



			$Script = getScript($oid);

			$order_date = $o->post_date;
			if (!empty($order_date)) {
				$order_date = date("m/d/Y", strtotime($order_date));
			}


			$source = getOrderType($oid);
			$telemarket = getSource($oid);
			$funnel_traffic_source = getFunnel($oid, 's');

			$backorders = 0;
			if ( !empty(getOrderBackorders($oid)) )
				$backorders = 1;


			$sql = "
				select  i.order_item_id, i.order_item_name, i.order_item_type
				from {$wpdb->prefix}woocommerce_order_items i
				where i.order_id = $oid
				and i.order_item_type in ('line_item')
				order by i.order_item_type, i.order_item_id
			";

			$items_ids = $wpdb->get_results($sql);

			foreach($items_ids as $item) {
				if (getItemType($item->order_item_id) != 'shipping') {

					$sku = getItemSKU($item->order_item_id);
					$name = getItemDescription($item->order_item_id);

					$qty = getItemQty($item->order_item_id);

					$legacy_product_quantity = (int)get_post_meta(max($pid, $vid), 'legacy_product_quantity', true);
					if (empty($legacy_product_quantity)) {
						$number_of_item = $qty;
					} else {
						$number_of_item = $qty * (int)$legacy_product_quantity;
					}


					$qty_shipped = $qty;


					if ( !$backorders || ($backorders && !isItemBackorder($item->order_item_id)) ) {
				
						$rows[] = array(
							$project_no, 
							$oid, 
							$order_date, 
							$status, 
							$source,
							$telemarket,
							$funnel_traffic_source,
							$Script, 
							$sku, 
							$name, 
							$qty, 
							$number_of_item, 
							$ShipDate, 
							$qty_shipped, 
						);
					}

				}				
			}

		}

		$units=[];
		$total =['', '', '', '', '', '', '', '', '', '', 1, 1, '', 1];

		// Array data
		$result['all']['array']['header']	 = $header;
		$result['all']['array']['unit']		 = $units;
		$result['all']['array']['total']	 = $total;
		$result['all']['array']['rows']		 = $rows;
		$result['all']['array']['sheetname'] 	 = 'Worksheet';
		$result['all']['array']['title']	 = 'Shipped Component Report';
		

		return json_encode($result);

	}

?>
