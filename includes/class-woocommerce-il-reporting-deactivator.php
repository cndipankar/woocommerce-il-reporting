<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://builtmighty.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Il_Reporting
 * @subpackage Woocommerce_Il_Reporting/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Il_Reporting
 * @subpackage Woocommerce_Il_Reporting/includes
 * @author     Built Mighty <developers@builtmighty.com>
 */
class Woocommerce_Il_Reporting_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		// find out when the last event was scheduled
		$timestamp = wp_next_scheduled ('idealliving_reports_cronjob');
		// unschedule previous event if any
		wp_unschedule_event ($timestamp, 'idealliving_reports_cronjob');


	}

}
