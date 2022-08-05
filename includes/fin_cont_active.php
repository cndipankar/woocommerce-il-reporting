<?php

    function get_fin_cont_active($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

        // get the table rows
        $header = ['Creation Year - Month','Active Count'];
        $units = array();
        $total = array();

	$res=$wpdb->get_results("
		select YEAR(post_date) year, MONTH(post_date) month, count(*) as count
		from {$wpdb->posts} 
		where post_type='shop_subscription' and post_status='wc-active' 
		group by YEAR(post_date), MONTH(post_date)
		order by YEAR(post_date), MONTH(post_date)
	");


        foreach ($res as $r) {

		$date = $r->year . '-' . $r->month;

		$rows[] = [$date,  $r->count];
	}
		


        // Array data
        $result['all']['array']['header']       = $header;
        $result['all']['array']['unit']         = $units;
        $result['all']['array']['total']        = $total;
        $result['all']['array']['rows']         = $rows;
        $result['all']['array']['sheetname']    = 'Worksheet';
        $result['all']['array']['title']        = 'Active Subscription Count by Start Date';
        

        return json_encode($result);

    }

?>
