<?php

	$report_cron = new Report();
	$crons = $report_cron->getDownloads();
	$reports = $report_cron->getSettings('report');

	$path = dirname(__FILE__ ) . '/../';

	$current_user = wp_get_current_user();
	$userID = $current_user->ID;

?>


<br>

<div class="container">

	<div class="row">
		<div class="col-12 mb-4">
			<h1><b>Download</b></h1>
		</div>
	</div>

	<div class="row">
		<div class="col-md-3">
			<b>Report</b>
		</div>
		<div class="col-md-3">
			<b>Email</b>
		</div>
		<div class="col-md-2 center">
			<b>Interval</b>
		</div>
		<div class="col-md-2 center">
			<b>Run time</b>
		</div>
		<div class="col-md-1 center">
			<b>Status</b>
		</div>
		<div class="col-md-1 center">
			<b></b>
		</div>

	</div>

<?php
foreach ($crons as $cron) {
	if ( $userID == $cron->user_id || is_admin() ) {
?>
	<div class="row">
		<div class="col-md-3">
			<?=$cron->name?>
		</div>
		<div class="col-md-3">
			<?=$cron->report_emails?>
		</div>
		<div class="col-md-2 center">
			<?=substr($cron->report_data1, 0, 10)?> - <?=substr($cron->report_data2, 0, 10)?>
		</div>
		<div class="col-md-2 center">
			<?=$cron->report_scheduled?>
		</div>
		<div class="col-md-1 center">
			<?php
				if ( $cron->report_complete == -1 ) {
					echo 'wait';
				} else if ( $cron->report_complete == 0 ) {
					echo 'running';
				} else if ( $cron->report_complete == 1) {
					echo 'complete';
				}
			?>
		</div>
		<div class="col-md-1 center">

			<?php
				if ( $cron->report_complete == 1) {

				$data1 = substr($cron->report_data1, 0, 10);
				$data2 = substr($cron->report_data2, 0, 10);

				$filename = $cron->project . " " . $reports[$cron->report_code] . " $data1-$data2 (" . $cron->ID . ").xlsx";
				$filename = str_replace(" ", "-", $filename);

				$filename = plugins_url() . "\\woocommerce-il-reporting\\public\\download\\" . $filename;

			?>

			<a href="<?=$filename?>"  class=""><img src="<?=plugin_dir_url( __FILE__ )?>../images/save-64.png" style="width: 24px;"></a>
			&nbsp;

			<?php
				}
			?>

			<a href="<?=admin_url('/admin.php')?>?page=woocommerce_il_reporting_delete&id=<?=$cron->ID?>" class=""><img src="<?=plugin_dir_url( __FILE__ )?>../images/delete-64.png" style="width: 24px;" onclick="if (confirm('Are you sure you want to delete?')) return true; else return false;"></a>
		</div>
	</div>
<?php
	}
}
?>


</div>