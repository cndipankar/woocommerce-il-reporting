<?php

    function get_fin_inventory($data1, $data2) {

	global $wpdb;
		
        $start_date = $data1;
        $end_date = $data2;

        // get the table rows
        $header = array();
        $units = array();
        $total = array();
        $rows = array();


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
