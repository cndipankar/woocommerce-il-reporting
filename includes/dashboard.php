<?php

function get_dashboard_data($data1, $data2, $type='brief') {

    global $wpdb;

    $data1 = $data1 . ' 00:00:00';
    $data2 = $data2 . ' 23:59:59';

    $sql = "
        SELECT 
        p.ID wId, 
        CASE
            WHEN p.post_excerpt like '%ars%' THEN TRIM(REPLACE(p.post_excerpt, 'ars order #', ''))
            WHEN p.post_excerpt like '%mojo%' THEN TRIM(REPLACE(p.post_excerpt, 'mojo order #', ''))
            ELSE p.ID
        END orderId,
        DATE(p.post_date) order_date, 
        CASE
            WHEN p.post_excerpt like '%ars%' THEN 'ars'
            WHEN p.post_excerpt like '%mojo%' THEN 'mojo'
            ELSE 'woocommerce'
        END src, 
        IFNULL(m1.meta_value, 0)*1 total,
        IFNULL(m1.meta_value, 0)*1 - IFNULL(m2.meta_value, 0)*1 items,
        IFNULL(m2.meta_value, 0)*1 shipping,
        IFNULL(m3.meta_value, 0)*1 tax, 
        IFNULL(m4.meta_value, 'main') category,
        IFNULL(a.ca_id, 0)  aid,	
        IFNULL(x7.meta_value, '') as name, 
        IFNULL(a.ca_title, '') as friendly_name,
        'paintzoom' as website,
        IFNULL(x1.meta_value, 0)*1 recurent_payment,
        IFNULL(x2.meta_value, 0)*1 recurent_sp,
        IFNULL(x3.meta_value, '') recurent_start,
        IFNULL(x4.meta_value, '') recurent_end,
        IFNULL(x5.meta_value, 0) recurent_interval,
        IFNULL(x6.meta_value, '') recurent_period
        FROM {$wpdb->prefix}posts p  
        LEFT JOIN {$wpdb->prefix}postmeta m1 on p.ID = m1.post_id and m1.meta_key = '_order_total'
        LEFT JOIN {$wpdb->prefix}postmeta m2 on p.ID = m2.post_id and m2.meta_key = '_order_shipping'
        LEFT JOIN {$wpdb->prefix}postmeta m3 on p.ID = m3.post_id and m3.meta_key = '_order_tax'
        LEFT JOIN {$wpdb->prefix}postmeta m4 on p.ID = m4.post_id and m4.meta_key = '_order_category'
        LEFT JOIN {$wpdb->prefix}posts s on p.ID = s.post_parent and s.post_type = 'shop_subscription'
        LEFT JOIN {$wpdb->prefix}postmeta x1 on s.ID = x1.post_id and x1.meta_key = '_order_total'
        LEFT JOIN {$wpdb->prefix}postmeta x2 on s.ID = x2.post_id and x2.meta_key = '_order_shipping'
        LEFT JOIN {$wpdb->prefix}postmeta x3 on s.ID = x3.post_id and x3.meta_key = '_schedule_start'
        LEFT JOIN {$wpdb->prefix}postmeta x4 on s.ID = x4.post_id and x4.meta_key = '_schedule_end'
        LEFT JOIN {$wpdb->prefix}postmeta x5 on s.ID = x5.post_id and x5.meta_key = '_billing_interval'
        LEFT JOIN {$wpdb->prefix}postmeta x6 on s.ID = x6.post_id and x6.meta_key = '_billing_period'
        LEFT JOIN {$wpdb->prefix}postmeta x7 on p.ID = x7.post_id and x7.meta_key = 'order_custom_affiliate_slug'
        LEFT JOIN {$wpdb->prefix}custom_affiliate a on a.custom_slug = x7.meta_value 
        WHERE p.post_type = 'shop_order' AND p.post_status in ('wc-completed', 'wc-shipped')
        AND DATE(p.post_date) >= '$data1' and DATE(p.post_date) <= '$data2'
        ORDER BY p.post_date
        ";
    //return $sql;


    $all = array();

    $main = array();
    $store = array();
    $affiliate = array();
    $affiliates = array();

    $main['count'] = 0;
    $main['value'] = 0;
    $store['count'] = 0;
    $store['value'] = 0;
    $affiliate['count'] = 0;
    $affiliate['value'] = 0;

    $affl = array();

    $results = $wpdb->get_results($sql, OBJECT );
    foreach ($results as $row) {
        //return json_encode($row); die();

        $tmpdata = array();
        $tmpdata['orderId'] = $row->orderId;
        $tmpdata['order_date'] = $row->order_date;
        $tmpdata['value'] = $row->items + $row->shipping;
        $tmpdata['items'] = $row->items;
        $tmpdata['shipping'] = $row->shipping;
        $tmpdata['tax'] = $row->tax;
        $tmpdata['total'] = $row->items + $row->shipping + $row->tax;
        $tmpdata['aid'] = $row->aid;
        $tmpdata['source'] = $row->src;
        $tmpdata['name'] = $row->name;
        $tmpdata['friendly_name'] = $row->friendly_name;
        $tmpdata['category'] = $row->category;
        $tmpdata['website'] = $row->website;

        $all[] = $tmpdata;

        if ((int)$row->aid > 1) {
            $affiliate['count'] = $store['count'] + 1;
            $affiliate['value'] = $store['value'] + $row->items;
            if (!array_key_exists(trim($row->name), $affl)) {
                $affl[trim($row->name)]['count'] = 0;
                $affl[trim($row->name)]['value'] = 0;
            }
            $affl[trim($row->name)]['count'] = $affl['count'] + 1;
            $affl[trim($row->name)]['value'] = $affl['value'] + $row->items;

        } else if ($row->category == 'main') {
            $main['count'] = $main['count'] + 1;
            $main['value'] = $main['value'] + $row->items;
        } else if ($row->category == 'store') {
            $store['count'] = $store['count'] + 1;
            $store['value'] = $store['value'] + $row->items;
        } else {
            $main['count'] = $main['count'] + 1;
            $main['value'] = $main['value'] + $row->items;
        }
    }

    /*
    $affl['Google']['count'] = 1;
    $affl['Google']['value'] = 100;

    $affl['MSN']['count'] = 2;
    $affl['MSN']['value'] = 200;
    */

    foreach ($affl as $key => $value) {
        $tmp = array();
        $tmp['count'] = $value['count'];
        $tmp['value'] = $value['value'];
        $tmp['name'] = $key;
        $affiliates[] = $tmp;
    }



    if ($type == 'brief') {
        $result['orders']['main'] = $main;
        $result['orders']['store'] = $store;
        $result['orders']['affiliate'] = $affiliate;
        $result['affiliates'] = $affiliates;
    } else {
        $result = $all;
    }

    return json_encode($result);

}


?>