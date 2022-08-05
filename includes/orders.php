<?php

function get_rep_orders_data($data1, $data2, $filters=array(), $columns=array(), $pageno=1, $rows=100 ) {

    global $wpdb;

    $conditions[] = 'Data start: ' . $data1;
    $conditions[] = 'Data end: ' . $data2;

    $data1 = $data1 . ' 00:00:00';
    $data2 = $data2 . ' 23:59:59';

    $fields = "
            p.ID as wId, 
                    CASE
                        WHEN p.post_excerpt like '%ars%' THEN TRIM(REPLACE(p.post_excerpt, 'ars order #', ''))
                        WHEN p.post_excerpt like '%mojo%' THEN TRIM(REPLACE(p.post_excerpt, 'mojo order #', ''))
                        ELSE p.ID
                    END as orderId, 
            ";
    $tables = " {$wpdb->prefix}posts p ";
    $where = " p.post_type = 'shop_order' AND DATE(p.post_date) >= '$data1' and DATE(p.post_date) <= '$data2' AND p.post_status <> 'trash' AND p.post_status <> 'auto-draft' ";
    $orderby = " p.post_date ";

    // filters
    if (strlen($filters['status']) > 0) {
        $where .= " AND p.post_status = 'wc-" . $filters['status'] . "' ";
        $conditions[] = 'Status: ' . $filters['status'];
    }
    
    if (strlen($filters['payment']) > 0) {
        $where .= " AND IFNULL(mf1.meta_value, '') = '" . $filters['payment'] . "' ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf1 on p.ID = mf1.post_id and mf1.meta_key = '_payment_method' ";
        $conditions[] = 'Payment: ' . $filters['payment'];
    }

    if (strlen($filters['search']) > 0) {
        $where .= " AND (";
        $where .= "IFNULL(mf2.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf3.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf4.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf5.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf6.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf7.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf8.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf9.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf10.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf11.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf12.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= " OR ";
        $where .= "IFNULL(mf13.meta_value, '') like '%" . $filters['search'] . "%'";
        $where .= ") ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf2 on p.ID = mf2.post_id and mf2.meta_key = '_shipping_address_1' ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf3 on p.ID = mf3.post_id and mf3.meta_key = '_shipping_address_2' ";            
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf4 on p.ID = mf4.post_id and mf4.meta_key = '_shipping_first_name' "; 
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf5 on p.ID = mf5.post_id and mf5.meta_key = '_shipping_last_name' "; 
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf6 on p.ID = mf6.post_id and mf6.meta_key = '_billing_address_1' ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf7 on p.ID = mf7.post_id and mf7.meta_key = '_billing_address_2' ";            
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf8 on p.ID = mf8.post_id and mf8.meta_key = '_billing_first_name' "; 
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf9 on p.ID = mf9.post_id and mf9.meta_key = '_billing_last_name' "; 
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf10 on p.ID = mf10.post_id and mf10.meta_key = '_billing_email' "; 
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf11 on p.ID = mf11.post_id and mf11.meta_key = '_billing_phone' "; 
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf12 on p.ID = mf12.post_id and mf12.meta_key = '_shipping_email' "; 
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta mf13 on p.ID = mf13.post_id and mf13.meta_key = '_shipping_phone' "; 
        $conditions[] = 'Search string: ' . $filters['search'];
    }        


    // columns
    if ($columns['status'] == 1) {
        $fields .= "p.post_status as status, ";
    }
    if ($columns['order_date'] == 1) {
        $fields .= "DATE(p.post_date) as order_date, ";
    }   
    if ($columns['value'] == 1) {
        $fields .= "IFNULL(m1.meta_value, 0)*1 as total, ";
        $fields .= "IFNULL(m1.meta_value, 0)*1 - IFNULL(m2.meta_value, 0)*1 as items, "; 
        $fields .= "IFNULL(m2.meta_value, 0)*1 as shipping, ";
        $fields .= "IFNULL(m3.meta_value, 0)*1 as tax, ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m1 on p.ID = m1.post_id and m1.meta_key = '_order_total' ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m2 on p.ID = m2.post_id and m2.meta_key = '_order_shipping' ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m3 on p.ID = m3.post_id and m3.meta_key = '_order_tax' ";
    }       
    if ($columns['affiliate'] == 1) {
        $fields .= "";        // TO DO
    }   
    if ($columns['fname'] == 1) {
        $fields .= "IFNULL(m4.meta_value, '') as fname, ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m4 on p.ID = m4.post_id and m4.meta_key = '_billing_first_name' ";
    } 
    if ($columns['lname'] == 1) {
        $fields .= "IFNULL(m5.meta_value, '') as lname, ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m5 on p.ID = m5.post_id and m5.meta_key = '_billing_last_name' ";
    }       
    if ($columns['phone'] == 1) {
        $fields .= "IFNULL(m6.meta_value, '') as phone, ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m6 on p.ID = m6.post_id and m6.meta_key = '_billing_phone' ";
    }  
    if ($columns['email'] == 1) {
        $fields .= "IFNULL(m7.meta_value, '') as email, ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m7 on p.ID = m7.post_id and m7.meta_key = '_billing_email' ";
    }  
    if ($columns['address'] == 1) {
        $fields .= "IFNULL(m8.meta_value, '') as address1, ";
        $fields .= "IFNULL(m9.meta_value, '') as address2, ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m8 on p.ID = m8.post_id and m8.meta_key = '_billing_address_1' ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m9 on p.ID = m9.post_id and m9.meta_key = '_billing_address_2' ";
    }  
    if ($columns['city'] == 1) {
        $fields .= "IFNULL(m10.meta_value, '') as city, ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m10 on p.ID = m10.post_id and m10.meta_key = '_billing_city' ";
    }  
    if ($columns['state'] == 1) {
        $fields .= "IFNULL(m11.meta_value, '') as state, ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m11 on p.ID = m11.post_id and m11.meta_key = '_billing_state' ";
    }  
    if ($columns['zip'] == 1) {
        $fields .= "IFNULL(m12.meta_value, '') as zip, ";
        $tables .= "LEFT JOIN {$wpdb->prefix}postmeta m12 on p.ID = m12.post_id and m12.meta_key = '_billing_postcode' ";
    }                     
      
    $fields = substr(trim($fields), 0, -1);
    
    if ($pageno > 0) {
        $start = ($pageno-1) * $rows;
    } else {
        $start = $pageno * $rows;
    }
    $limit = "$start, $rows";

    $sql = "SELECT $fields FROM $tables WHERE $where ORDER BY $orderby LIMIT $limit";


    $tables2 = " {$wpdb->prefix}posts p ";
    $fields2  = "count(*) as count, ";
    $fields2 .= "SUM(IFNULL(m1.meta_value, 0)*1) as total, ";
    $fields2 .= "SUM(IFNULL(m1.meta_value, 0)*1) - SUM(IFNULL(m2.meta_value, 0)*1) as items, "; 
    $fields2 .= "SUM(IFNULL(m2.meta_value, 0)*1) as shipping, ";
    $fields2 .= "SUM(IFNULL(m3.meta_value, 0)*1) as tax, ";
    $tables2 .= "LEFT JOIN {$wpdb->prefix}postmeta m1 on p.ID = m1.post_id and m1.meta_key = '_order_total' ";
    $tables2 .= "LEFT JOIN {$wpdb->prefix}postmeta m2 on p.ID = m2.post_id and m2.meta_key = '_order_shipping' ";
    $tables2 .= "LEFT JOIN {$wpdb->prefix}postmeta m3 on p.ID = m3.post_id and m3.meta_key = '_order_tax' ";
    
    $fields2 = substr(trim($fields2), 0, -1);

    $sql2 = "SELECT $fields2 FROM $tables2 WHERE $where";

    //return $sql;


    $orders = array();


    $results = $wpdb->get_results($sql, ARRAY_A );

    $count = 0;
    $total = 0;
    $items = 0;
    $shipping = 0;
    $tax = 0;

    foreach ($results as $row) {
        //return json_encode($row); die();

        $tmpdata = array();

        $count ++;
        $total = $total + $row['total'];
        $items = $items + $row['items'];
        $shipping = $shipping + $row['shipping'];
        $tax = $tax + $row['tax'];

        $tmpdata['orderId'] = $row['orderId'];
        $tmpdata['ID'] = $row['wId'];

        if (array_key_exists('status', $row)) {
            $tmpdata['status'] = str_replace('wc-', '', $row['status']);
        }   
        if (array_key_exists('order_date', $row)) {
            $tmpdata['order_date'] = $row['order_date'];
         }         
        if (array_key_exists('total', $row)) {
           $tmpdata['total'] = $row['total'];
        }
        if (array_key_exists('items', $row)) {
            $tmpdata['items'] = $row['items'];
        }
        if (array_key_exists('shipping', $row)) {
            $tmpdata['shipping'] = $row['shipping'];
        }
        if (array_key_exists('tax', $row)) {
            $tmpdata['tax'] = $row['tax'];
        }
        if (array_key_exists('fname', $row)) {
           $tmpdata['fname'] = $row['fname'];
        }
        if (array_key_exists('lname', $row)) {
           $tmpdata['lname'] = $row['lname'];
        }
         if (array_key_exists('phone', $row)) {
           $tmpdata['phone'] = $row['phone'];
        }
        if (array_key_exists('email', $row)) {
           $tmpdata['email'] = $row['email'];
        }
        if (array_key_exists('address1', $row)) {
           $tmpdata['address'] = trim($row['address1'] . ' ' . $row['address2']);
        }                 
        if (array_key_exists('city', $row)) {
           $tmpdata['city'] = $row['city'];
        } 
        if (array_key_exists('state', $row)) {
           $tmpdata['state'] = $row['state'];
        } 
        if (array_key_exists('zip', $row)) {
           $tmpdata['zip'] = $row['zip'];
        }             
  
        $orders[] = $tmpdata;

    }


    $results2 = $wpdb->get_results($sql2, ARRAY_A );
    foreach ($results2 as $row) {
        $tmpdata = array();

        if (array_key_exists('count', $row)) {
            $tmpdata['count'] = $row['count'];
        }  
        if (array_key_exists('total', $row)) {
            $tmpdata['total'] = $row['total'];
        } 
        if (array_key_exists('items', $row)) {
            $tmpdata['items'] = $row['items'];
        } 
        if (array_key_exists('shipping', $row)) {
            $tmpdata['shipping'] = $row['shipping'];
        }  
        if (array_key_exists('tax', $row)) {
            $tmpdata['tax'] = $row['tax'];
        }   
        
        $result['summary'] = $tmpdata;
    }

    $result['orders'] = $orders;
    $result['conditions'] = $conditions;

    return json_encode($result);

}


?>