<?php

    function get_mojo_orders($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

        // get the table rows
        $header = array(
			'Order ID',
			'Order Date',
			'Order Time',
			'Reconciliation Status',
			'IP Address',
			'Domain',
			'Referring Domain',
			'Visited Url',
			'First Name',
			'Last Name',
			'Email Address',
			'SubTotal',
			'Shipping Total',
			'Tax',
			'Price Total',
			'Promo Code	Promo',
			'Code Type',
			'Ordered Quantity',
			'SKU List',
			'Product List',
			'Billing Address',
			'Billing City',
			'Billing State',
			'Billing Zip',
			'Billing Country',
			'Billing Phone',
			'Shipping Address',
			'Shipping City',
			'Shipping State',
			'Shipping Zip',
			'Shipping Country',
			'Shipping Phone',
			'Version Name',
			'Mobile',
			'Browser',
			'Device',
			'Mojo Source',
			'Shipping Method',
			'Payment Type',
			'Payment Plan Type',
			'Mobile Phone',
			'Subscribed Email',
			'Subscribed SMS',
			'Subscribed Phone',
			'Subscribed Post',
			'Transaction Fee',
			'Tax Rate'
		);
        
		$zap="select ID,post_date from {$wpdb->posts} where post_type='shop_order' and post_status in ('wc-shipped', 'wc-completed') and post_date > '$data1 00:00:00' and post_date < '$data2 23:59:59' order by post_date desc";

		$orders = $wpdb->get_results($zap);
		if ($orders) {
			foreach($orders as $o){
				$oid = $o->ID;
				$user_agent = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_customer_user_agent' and post_id=$oid");
				$items = $wpdb->get_col("select order_item_id from {$wpdb->prefix}woocommerce_order_items where order_item_type='line_item' and order_id=$oid");
				$subtotal=0;
				$sku_arr=$product_title_arr=[];
				$total_units_sold=0;
				foreach($items as $item){
					$qty = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_qty' and order_item_id=$item");
					$total_units_sold = $total_units_sold + $qty;
					
					$item_total = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_line_total' and order_item_id=$item");
					$total_items = $total_items + $item_total;
					$subtotal = $subtotal + $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_line_subtotal' and order_item_id=$item");
					$pid = $wpdb->get_var("select meta_value from {$wpdb->prefix}woocommerce_order_itemmeta where meta_key='_product_id' and order_item_id=$item");
					$sku_arr[] = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_sku' and post_id=$pid");
					$product_title_arr[] = $wpdb->get_var("select post_title from {$wpdb->posts} where ID=$pid");
				}
			
			
				$order_id=$oid;
				$order_date= date('m.d.Y',strtotime($o->post_date)); 
				$order_time= date('H:i A',strtotime($o->post_date)); 
				$reconciliation_status = ''; // NEED TO DO
				$ip = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_customer_ip_address' and post_id=$oid");
				$domain=''; // NOT NEED? $_SERVER['SERVER_NAME'];
				$referring_domain = '';
				$visited_url = ''; // NEED TO DO
				$first_name = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_first_name' and post_id=$oid");
				$last_name = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_last_name' and post_id=$oid");
				$email =  $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_email' and post_id=$oid");
				$shipping_total= $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_order_shipping' and post_id=$oid");
				$tax = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_order_tax' and post_id=$oid") + $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_order_shipping_tax' and post_id=$oid");
				$price_total = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_order_total' and post_id=$oid");
				$promo_code = ''; // NEED TO DO
				$promo_code_type = ''; // NEED TO DO
				$order_qty = $total_units_sold;
				$sku_list = implode(', ',$sku_arr);
				$product_list = implode(', ',$product_title_arr);
				$billing_address = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_address_1' and post_id=$oid"). ' '.$wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_address_2' and post_id=$oid");
				$billing_city = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_city' and post_id=$oid");
				$billing_state = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_state' and post_id=$oid");
				$billing_zip = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_postcode' and post_id=$oid");
				$billing_country = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_country' and post_id=$oid");
				$billing_phone =  $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_billing_phone' and post_id=$oid");
				$shipping_address = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_address_1' and post_id=$oid").' '.$wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_address_2' and post_id=$oid");
				$shipping_city = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_city' and post_id=$oid");
				$shipping_state = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_state' and post_id=$oid");
				$shipping_zip = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_postcode' and post_id=$oid");
				$shipping_country = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_shipping_country' and post_id=$oid");
				$shipping_phone = ''; // NEED TO DO
				//$version_name = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='order_custom_affiliate_slug' and post_id=$oid");
				$version_name = $wpdb->get_var("select a.ca_title from {$wpdb->postmeta} m, {$wpdb->prefix}custom_affiliate a where m.meta_value = a.custom_slug and m.meta_key='order_custom_affiliate_slug' and m.post_id=$oid");
				$mobile = order_is_mobile($user_agent);
				$browser = get_browser_name($user_agent); 
				$device = order_get_device($user_agent); 
				$mojo_source = ''; // NEED TO DO
				$shipping_method = ''; // NEED TO DO
				$payment_type = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_payment_method_title' and post_id=$oid");;
				$payment_plan_type = $wpdb->get_var("select meta_value from {$wpdb->postmeta} where meta_key='_payment_method' and post_id=$oid");
				$mobile_phone = ''; // NEED TO DO
				$subscribed_email = ''; // NEED TO DO
				$subscribed_sms = ''; // NEED TO DO
				$subscribed_phone = ''; // NEED TO DO
				$subscribed_post = ''; // NEED TO DO
				$transaction_fee = 0; // NEED TO DO
				if($price_total && $tax)
					$tax_rate = round(($tax/$price_total)*100,2);
				else
					$tax_rate = 0;

				$rows[]=[
					$order_id,
					$order_date,
					$order_time,
					$reconciliation_status,
					$ip,
					$domain,
					$referring_domain,
					$visited_url,
					$first_name,
					$last_name,
					$email,
					$subtotal,
					$shipping_total,
					$tax,
					$price_total,
					$promo_code,
					$promo_code_type,
					$order_qty,
					$sku_list,
					$product_list,
					$billing_address,
					$billing_city,
					$billing_state,
					$billing_zip,
					$billing_country,
					$billing_phone,
					$shipping_address,
					$shipping_city,
					$shipping_state,
					$shipping_zip,
					$shipping_country,
					$shipping_phone,
					$version_name,
					$mobile,
					$browser,
					$device,
					$mojo_source,
					$shipping_method,
					$payment_type,
					$payment_plan_type,
					$mobile_phone,
					$subscribed_email,
					$subscribed_sms,
					$subscribed_phone,
					$subscribed_post,
					$transaction_fee,
					$tax_rate
				];


			}
			
			
		}
		
		$units = array();
		for($i=0;$i<47;$i++){
			$units[$i]='';
		}
		$units[11]=$units[12]=$units[13]=$units[14]='$';
		$units[46]='%';
		
		
		$total = array();
        // Array data
        $result['all']['array']['header']       = $header;
        $result['all']['array']['unit']         = $units;
        $result['all']['array']['total']        = $total;
        $result['all']['array']['rows']         = $rows;
        $result['all']['array']['sheetname']    = 'Worksheet';
        $result['all']['array']['title']        = '';
        

        return json_encode($result);

    }
	
	function order_is_mobile($user_agent){
		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
			return 'Yes';
		else
			return 'No';
		
	}
	
	function get_browser_name($user_agent){
		$t = strtolower($user_agent);
		$t = " " . $t;
		if     (stripos($t, 'opera'     ) || stripos($t, 'opr/')     ) return 'Opera'            ;   
		elseif (stripos($t, 'edge'      )                           ) return 'Edge'             ;   
		elseif (stripos($t, 'chrome'    )                           ) return 'Chrome'           ;   
		elseif (stripos($t, 'safari'    )                           ) return 'Safari'           ;   
		elseif (stripos($t, 'firefox'   )                           ) return 'Firefox'          ;   
		elseif (stripos($t, 'msie'      ) || stripos($t, 'trident/7')) return 'Internet Explorer';
		return 'Unkown';
	}
	
	function order_get_device($user_agent){
		$iPod    = stripos($user_agent,"iPod");
		$iPhone  = stripos($user_agent,"iPhone");
		$iPad    = stripos($user_agent,"iPad");
		$Android = stripos($user_agent,"Android");
		$webOS   = stripos($user_agent,"webOS");

	//do something with this information
		if($iPhone ){
			return 'iPhone';
		}else if($iPod){
			return 'iPod';
		}else if($iPad){
			return 'iPad';
		}else if($Android){
			return 'Android';
		}else{
			return 'Other';
		}
	}
?>
