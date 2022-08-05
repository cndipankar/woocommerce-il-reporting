<?php

    function get_fin_count_analysis($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

        $header = ['Next Shipment No', 'Active', 'Inactive', 'Cumulative Inactive', 'Total', 'Cancel % to Overall', 'Number of Shipments', 'Gross Product Dollars for Shipped Orders', 'S&H', 'Tax', 'Collected Dollars', 'Return Dollars', 'Returns by Shipment', 'Net Dollars', 'Retention %'];
		
		$zap = "
			select ID, post_date 
			from {$wpdb->posts} 
			where post_type='shop_subscription' 
			and post_status='wc-active' 
			order by post_date
		";

		$cont_orders = $wpdb->get_results($zap);
		//echo $zap;die();

		$number_of_shipments = $gross = $sp_total = $tax_total = $total_collected_dollars = $total_return_dollars = $total_net_dollars = $total_retention = 0;
		
		$res_ar=$month_total=[];
		
		$order_selection = array('wc-completed', 'wc-shipped', 'wc-return-approved', 'wc-return-requested', 'wc-return-cancelled');
		
		foreach($cont_orders as $o) {

			$cont_oid = $o->ID;
			$zap = "
				select distinct post_id, post_date 
				from {$wpdb->postmeta} pm 
				inner join {$wpdb->posts} p on p.ID = pm.post_id 
				where p.post_status in ('".implode("','", $order_selection)."') 
				and post_date >= '$data1 00:00:00' and post_date <= '$data2 23:59:59' 
				and meta_key = '_subscription_renewal'
				and meta_value = $cont_oid 
				order by post_id
			";
			//echo $zap;die();

			$shipmetnts = $wpdb->get_results($zap);
			//var_dump($shipmetnts_ids);die();

			$next_shipment_no = 1;
			foreach($shipmetnts as $sh){

				$paid_timestamp = date("n/1/Y",strtotime($sh->post_date));
				
				if(empty($res_ar[$paid_timestamp][$next_shipment_no]['number_of_shipments']))
					$res_ar[$paid_timestamp][$next_shipment_no]['number_of_shipments']=1;
				else
					$res_ar[$paid_timestamp][$next_shipment_no]['number_of_shipments']++;
				
				if(empty($month_total[$paid_timestamp]['number_of_shipments']))
					$month_total[$paid_timestamp]['number_of_shipments']=1;
				else
					$month_total[$paid_timestamp]['number_of_shipments']++;
				$number_of_shipments++;
				
				$oid = $sh->post_id;

				$sql = "select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_id  = $oid";

				$items = $wpdb->get_results($sql);
				foreach ($items as $item) {

					$val_item = (float)$wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_line_subtotal' and order_item_id=$item->order_item_id");
					$val_sp = (float)$wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='cost' and order_item_id=$item->order_item_id"); 
					$val_tax = (float)$wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='tax_amount' and order_item_id=$item->order_item_id");

					$amount = $val_item + $val_sp + $val_tax;


					if(empty($res_ar[$paid_timestamp][$next_shipment_no]['gross']))
						$res_ar[$paid_timestamp][$next_shipment_no]['gross'] = $amount;
					else
						$res_ar[$paid_timestamp][$next_shipment_no]['gross'] = $res_ar[$paid_timestamp][$next_shipment_no]['gross'] + $amount;

					if(empty($month_total[$paid_timestamp]['gross']))
						$month_total[$paid_timestamp]['gross'] = $amount;
					else
						$month_total[$paid_timestamp]['gross'] = $month_total[$paid_timestamp]['gross'] + $amount;

					$gross = $gross + $amount;



					if(empty($res_ar[$paid_timestamp][$next_shipment_no]['item']))
						$res_ar[$paid_timestamp][$next_shipment_no]['item'] = $val_item;
					else
						$res_ar[$paid_timestamp][$next_shipment_no]['item'] = $res_ar[$paid_timestamp][$next_shipment_no]['item'] + $val_item;
				
					if(empty($month_total[$paid_timestamp]['item']))
						$month_total[$paid_timestamp]['item'] = $val_item;
					else
						$month_total[$paid_timestamp]['item'] = $month_total[$paid_timestamp]['item'] + $val_item;

					$item_total = $item_total + $val_item;



					if(empty($res_ar[$paid_timestamp][$next_shipment_no]['sp']))
						$res_ar[$paid_timestamp][$next_shipment_no]['sp'] = $val_sp;
					else
						$res_ar[$paid_timestamp][$next_shipment_no]['sp'] = $res_ar[$paid_timestamp][$next_shipment_no]['sp'] + $val_sp;
				
					if(empty($month_total[$paid_timestamp]['sp']))
						$month_total[$paid_timestamp]['sp'] = $val_sp;
					else
						$month_total[$paid_timestamp]['sp'] = $month_total[$paid_timestamp]['sp'] + $val_sp;

					$sp_total = $sp_total + $val_sp;



					if(empty($res_ar[$paid_timestamp][$next_shipment_no]['tax']))
						$res_ar[$paid_timestamp][$next_shipment_no]['tax'] = $val_tax;
					else
						$res_ar[$paid_timestamp][$next_shipment_no]['tax'] = $res_ar[$paid_timestamp][$next_shipment_no]['tax'] + $val_tax;
				
					if(empty($month_total[$paid_timestamp]['tax']))
						$month_total[$paid_timestamp]['tax'] = $val_tax;
					else
						$month_total[$paid_timestamp]['tax'] = $month_total[$paid_timestamp]['tax'] + $val_tax;
					$tax_total = $tax_total + $val_tax;



				}

				$sql = "select * from {$wpdb->posts} where post_type='shop_order_refund' and post_parent = $oid";
				$refund_orders = $wpdb->get_results($sql);

				$refund_dollars = 0;
				$refund_ship_dollars = 0;
				$refund_tax_dollars = 0;
				$refund_product_dollars = 0;


				foreach($refund_orders as $refund_order) {

					$order_refunded = 0;


					$refund_order_id = $refund_order->ID;
					$full_order_cancelled=false;
					$items = $wpdb->get_col("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_type='line_item' and order_id=$refund_order_id");
					if(!$items){
						$refund_order_id = $oid;
						$items = $wpdb->get_col("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_type='line_item' and order_id=$refund_order_id");
					}


					$ship_id = $wpdb->get_var("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_type='shipping' and order_id = $refund_order_id");
					$ship_fee = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='cost' and order_item_id = $ship_id");
					if($ship_fee)
						$ship_fee = abs(1*$ship_fee);
					else
						$ship_fee = 0;
	

					$tax_id = $wpdb->get_var("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_type='tax' and order_id = $refund_order_id");
					$order_tax = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='cost' and order_item_id = $tax_id");
					if($order_tax)
						$order_tax = abs(1*$order_tax);
					else
						$order_tax = 0;


					$order_product_amount = 0;
					foreach($items as $item){
						$product_amount = abs($wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_line_total' and order_item_id=$item"));
						$order_product_amount = $order_product_amount + $product_amount;
						$tax = abs($wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_line_tax' and order_item_id=$item"));
						$order_tax = $order_tax + $tax;
						$order_refunded = $order_refunded + $product_amount;
					}

					$refund_dollars  = $refund_dollars + $order_refunded + $ship_fee + $order_tax;
					$refund_ship_dollars  = $refund_ship_dollars + $ship_fee;
					$refund_tax_dollars = $refund_tax_dollars + $order_tax;
					$refund_product_dollars = $refund_product_dollars + $order_product_amount;

				}


				if(empty($res_ar[$paid_timestamp][$next_shipment_no]['refund']))
					$res_ar[$paid_timestamp][$next_shipment_no]['refund'] = $refund_dollars;
				else
					$res_ar[$paid_timestamp][$next_shipment_no]['refund'] = $res_ar[$paid_timestamp][$next_shipment_no]['refund'] + $refund_dollars;
				
				if(empty($month_total[$paid_timestamp]['refund']))
					$month_total[$paid_timestamp]['refund'] = $refund_dollars;
				else
					$month_total[$paid_timestamp]['refund'] = $month_total[$paid_timestamp]['refund'] + $refund_dollars;
				$total_return_dollars = $total_return_dollars + $refund_dollars;



				$next_shipment_no ++;
			}
		}
		
		//var_dump($month_total);die();
		foreach ($res_ar as $k=>$month) {
			$rows[] = [$k];
			ksort($month);
			foreach ($month as $n => $val) {
				$collected_dollars = $val['gross'] - $val['sp'] - $val['tax'];
				$return_dollars = $val['refund'];

				$return_by_shipment = round((($return_dollars/$collected_dollars)*100),2); 
				$net_dollars = $collected_dollars - $return_dollars;

				$retention = 0;

				$rows[] = [$n, '', '', '', '', '', $val['number_of_shipments'], $val['gross'], $val['sp'], $val['tax'], $collected_dollars, $return_dollars, $return_by_shipment, $net_dollars, $retention];
			}
			$collected_dollars = $month_total[$k]['gross'] - $month_total[$k]['sp'] - $month_total[$k]['tax'];
			$total_collected_dollars = $total_collected_dollars + $collected_dollars;
			$return_dollars = $month_total[$k]['refund'];
			$total_return_dollars = $total_return_dollars + $return_dollars;

			$net_dollars = $collected_dollars - $return_dollars;
			$total_net_dollars = $total_net_dollars + $net_dollars;

			$retention = 0;

			$rows[] = ['Total '.$k, '', '', '', '', '', $month_total[$k]['number_of_shipments'], $month_total[$k]['gross'], $month_total[$k]['sp'], $month_total[$k]['tax'], $collected_dollars, $return_dollars, '', $net_dollars, ''];
			$rows[] = [];
		}
		//var_dump($rows);die();
		$units = ['','','','','','%','','$','$','$','$','$','%','$','%','%'];
		 
		$rows[] = ['TOTAL', '', '', '', '', '', $number_of_shipments, $gross, $sp_total, $tax_total, $total_collected_dollars, $total_return_dollars, '', $total_net_dollars, ''];

        // Array data
        $result['all']['array']['header']       = $header;
        $result['all']['array']['unit']         = $units;
        $result['all']['array']['total']        = $total;
        $result['all']['array']['rows']         = $rows;
        $result['all']['array']['sheetname']    = 'Worksheet';
        $result['all']['array']['title']        = 'Subscription Analysis with Stick Rate By Month by Script';
        

        return json_encode($result);

    }

?>