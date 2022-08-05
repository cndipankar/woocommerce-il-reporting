<?php

function get_rep_products_data($data1, $data2, $type="") {

    global $wpdb;

    $conditions[] = 'Data start: ' . $data1;
    $conditions[] = 'Data end: ' . $data2;

    $data1 = $data1 . ' 00:00:00';
    $data2 = $data2 . ' 23:59:59';

    $sql = "
    SELECT
    category, 
    item_name,
    sku,
    sum(qty) qty,
    sum(line_total) revenue,
    min(price) price,
    min(sp) sp
    FROM (
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
     0 aid,	
     '' as name, 
     '' as friendly_name,
     'paintzoom' as website,
     oi.order_item_name item_name, 
     IFNULL(m2.meta_value , 0) qty,
     IFNULL(m3.meta_value , 0) line_total,
     IFNULL(m4.meta_value , 0) line_subtotal, 
     IFNULL(d.sku, '') sku,
     IFNULL(d.min_price, 0) min_price,
     IFNULL(d.max_price, 0) max_price,
     IFNULL(my1.meta_value, 0) sku2,
     IFNULL(my2.meta_value, 0) price,
     IFNULL(t.name, 0) sp,
     IFNULL(mx.meta_value, 'main') category
     FROM {$wpdb->prefix}posts p
     LEFT JOIN {$wpdb->prefix}postmeta mx on p.ID = mx.post_id and mx.meta_key = '_order_category'
     LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi on p.ID = oi.order_id
     LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta m2 on oi.order_item_id = m2.order_item_id and m2.meta_key = '_qty'
     LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta m3 on oi.order_item_id = m3.order_item_id and m3.meta_key = '_line_total'
     LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta m4 on oi.order_item_id = m4.order_item_id and m4.meta_key = '_line_subtotal'
     LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta m1 on oi.order_item_id = m1.order_item_id and m1.meta_key = '_product_id'
     LEFT JOIN {$wpdb->prefix}wc_product_meta_lookup d on IFNULL(m1.meta_value, 0) = d.product_id
     LEFT JOIN {$wpdb->prefix}postmeta my1 on IFNULL(m1.meta_value, 0) = my1.post_id and my1.meta_key = '_sku'
     LEFT JOIN {$wpdb->prefix}postmeta my2 on IFNULL(m1.meta_value, 0) = my2.post_id and my2.meta_key = '_price'
     LEFT JOIN (select terms.name, tx.term_taxonomy_id, trel.object_id from {$wpdb->prefix}term_taxonomy tx, {$wpdb->prefix}terms terms, {$wpdb->prefix}term_relationships trel where tx.term_taxonomy_id = trel.term_taxonomy_id and terms.term_id = tx.term_id and tx.taxonomy = 'product_shipping_class') t on t.object_id = IFNULL(m1.meta_value, 0)
     WHERE p.post_type = 'shop_order' AND p.post_status in ('wc-completed', 'wc-shipped')
     AND oi.order_item_type = 'line_item'
     AND DATE(p.post_date) >= '$data1' and DATE(p.post_date) <= '$data2'
    ) A
    GROUP BY category, item_name, sku
 ";

 //return $sql;

 $products = array();

 $results = $wpdb->get_results($sql, OBJECT );
 foreach ($results as $row) {
     //return json_encode($row); die();

     if ($type == 'sp') {
         $tmp_main['name'] = $row->item_name;
         $tmp_main['sku'] = $row->sku;
         $tmp_main['qty'] = $row->qty;
         $tmp_main['price'] = $row->price + floatval($row->sp);
         $tmp_main['shipping'] = floatval($row->sp);
         $tmp_main['revenue'] = $row->revenue + floatval($row->sp) * $row->qty;
         $products[] = $tmp_main;
     } else {
        $tmp_main['name'] = $row->item_name;
        $tmp_main['sku'] = $row->sku;
        $tmp_main['qty'] = $row->qty;
        $tmp_main['price'] = $row->price;
        $tmp_main['shipping'] = floatval($row->sp);
        $tmp_main['revenue'] = $row->revenue;
        $products[] = $tmp_main;             
     }
 }

 $result['products'] = $products;

 return json_encode($result);


}



?>