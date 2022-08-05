<?php


class Report {

	public function __construct() {
	}

	public function getCrons($id=0) {

		global $wpdb;

		$affiliates = array();

		$sql = "
			select 
			   c.*, s.name
			from {$wpdb->prefix}custom_report_cron c, {$wpdb->prefix}custom_report_settings s
			where c.report_code = s.code and s.type = 'report'
		";

		if (!empty($id)) {
			$sql = $sql . " and c.ID = $id";
		}

		$cron = $wpdb->get_results($sql);

		return $cron;
	}


	public function getDownloads($id=0) {

		global $wpdb;

		$affiliates = array();

		$sql = "
			select 
			   r.*, s.name
			from {$wpdb->prefix}custom_report r, {$wpdb->prefix}custom_report_settings s
			where r.report_code = s.code and s.type = 'report'
			order by r.report_scheduled desc
		";

		if (!empty($id)) {
			$sql = $sql . " and r.ID = $id";
		}

		$cron = $wpdb->get_results($sql);

		return $cron;
	}


	public function getSettings($type='') {

		global $wpdb;

		$return = array();

		$sql = "
			select 
			   code, name
			from {$wpdb->prefix}custom_report_settings
			where type='$type'
		";

		$result = $wpdb->get_results($sql);
		foreach ($result as $data) {
			$return[$data->code] = $data->name;
		}

		return $return;

	}




	public function saveCron($data, &$error) {

		global $wpdb;

		$id = 0;

		if (array_key_exists('id', $data) && (int)$data['id'] > 0) {
			$sql = "UPDATE {$wpdb->prefix}custom_report_cron set report_code = '".$data['report_code']."', report_interval = '".$data['report_interval']."',  report_emails = '".$data['report_emails']."', report_hour = '".$data['report_hour']."', report_minute = '".$data['report_minute']."', report_frequency = '".$data['report_frequency']."' WHERE ID = " . $data['id'];
			$wpdb->query($wpdb->prepare($sql));
		} else {
			$current_user = wp_get_current_user();
			if ( empty($data['report_emails']) ) {
				$data['report_emails'] = $current_user->$current_user->user_email;
			}
			$userID = $current_user->ID;
			$sql = "INSERT INTO {$wpdb->prefix}custom_report_cron (report_code, report_interval, report_emails, report_hour, report_minute, report_frequency, user_id) values ('".$data['report_code']."', '".$data['report_interval']."', '".$data['report_emails']."', '".$data['report_hour']."', '".$data['report_minute']."', '".$data['report_frequency']."', ".(int)$userID.")";
			$wpdb->query($wpdb->prepare($sql));
		}

		return true;

	}



	public function saveRunCron($data, &$error) {

		global $wpdb;

		$id = 0;

		if ( empty($data['report_user_id']) ) {
			$data['report_user_id'] = 0;
		}
		$sql = "INSERT INTO {$wpdb->prefix}custom_report (project, cron_id, report_code, report_data1, report_data2, report_emails, report_scheduled, report_priority, user_id) values ('".$data['project']."', 0, '".$data['report_code']."', '".$data['report_data1']."', '".$data['report_data2']."', '".$data['report_emails']."', '".$data['report_scheduled']."', 0, " . (int)$data['report_user_id'] . ")";
		$wpdb->query($wpdb->prepare($sql));
		$id = $wpdb->insert_id;

		return true;

	}


	public function delete($id=0) {

		global $wpdb;

		$table_name = $wpdb->prefix.'custom_report_cron';
		$wpdb->delete( $table_name, array('id' => $id) );

	}

}


?>
