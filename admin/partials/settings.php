<?php

	$report_cron = new Report();
	$crons = $report_cron->getCrons();

	$current_user = wp_get_current_user();
	$userID = $current_user->ID;

?>


<br>

<div class="container">

	<div class="row">
		<div class="col-12 mb-4">
			<h1><b>Schedule Reports</b></h1>
		</div>
	</div>

	<div class="row">
		<div class="col-12 mb-4">
			<a href="?page=custom_exports_cron_edit" class="btn btn-primary">Add Schedule Report</a>
		</div>
	</div>

	<div class="row">
		<div class="col-md-3">
			<b>Report</b>
		</div>
		<div class="col-md-1 center">
			<b>Frequency</b>
		</div>
		<div class="col-md-2 center">
			<b>Interval</b>
		</div>
		<div class="col-md-4">
			<b>Email</b>
		</div>
		<div class="col-md-1 center">
			<b>Hour</b>
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
		<div class="col-md-1 center">
			<?=$cron->report_frequency?>
		</div>
		<div class="col-md-2 center">
			<?=$cron->report_interval?>
		</div>
		<div class="col-md-4">
			<?=$cron->report_emails?>
		</div>
		<div class="col-md-1 center">
			<?=$cron->report_hour?>:<?=$cron->report_minute?>
		</div>
		<div class="col-md-1 center">
			<a href="<?=admin_url('/admin.php')?>?page=custom_exports_cron_edit&id=<?=$cron->ID?>"  class=""><img src="<?=plugin_dir_url( __FILE__ )?>../images/edit-64.png" style="width: 24px;"></a>
			&nbsp;
			<a href="<?=admin_url('/admin.php')?>?page=woocommerce_il_reporting_delete&id=<?=$cron->ID?>" class=""><img src="<?=plugin_dir_url( __FILE__ )?>../images/delete-64.png" style="width: 24px;" onclick="if (confirm('Are you sure you want to delete?')) return true; else return false;"></a>
		</div>
	</div>
<?php
	}
}
?>


</div>