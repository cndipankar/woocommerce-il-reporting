<?php

/**
 * Fired during plugin activation
 *
 * @link       https://builtmighty.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Il_Reporting
 * @subpackage Woocommerce_Il_Reporting/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Il_Reporting
 * @subpackage Woocommerce_Il_Reporting/includes
 * @author     Built Mighty <developers@builtmighty.com>
 */
class Woocommerce_Il_Reporting_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		/**
		 * Check if WooCommerce is active
		 **/
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			add_action( 'admin_notices', 'il_reporting_woocommerce_deactivated' );
			exit;
		}

		global $wpdb;		   
		$plugin_name_db_version = '1.0';


		// Create Admin Cron Table

		$table_name = $wpdb->prefix . "custom_report_cron";

		$charset_collate = $wpdb->get_charset_collate();
            
		$sql_cron = "CREATE TABLE IF NOT EXISTS $table_name (
                	ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			report_code varchar(200) NOT NULL DEFAULT '',
			report_interval varchar(100) NOT NULL DEFAULT '',
			report_frequency varchar(100) NOT NULL DEFAULT '',
			report_emails longtext NOT NULL DEFAULT '',
			report_hour varchar(2) NOT NULL DEFAULT 0,
			report_minute varchar(2) NOT NULL DEFAULT 0,
                	user_id bigint(20) unsigned,
			PRIMARY KEY (ID),
			KEY report_code (report_code),
			KEY user_id (user_id)
		) $charset_collate;";



		// Create Cron Queue Table

		$table_name = $wpdb->prefix . "custom_report";

		$sql_report = "CREATE TABLE IF NOT EXISTS $table_name (
                	ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			project varchar(20) NOT NULL DEFAULT '',
                	cron_id bigint(20) unsigned,
                	user_id bigint(20) unsigned,
			report_code varchar(200) NOT NULL DEFAULT '',
			report_data1 DATETIME,
			report_data2 DATETIME,
			report_emails longtext NOT NULL DEFAULT '',
			report_scheduled DATETIME,
			report_start DATETIME,
			report_end DATETIME,
			report_priority int,
			report_complete int NOT NULL DEFAULT -1,
			PRIMARY KEY (ID),
			KEY project (project),
			KEY cron_id (cron_id),
			KEY user_id (user_id),
			KEY report_code (report_code),
			KEY report_data1 (report_data1),
			KEY report_data2 (report_data2),
			KEY report_emails (report_emails),
			KEY report_scheduled (report_scheduled),
			KEY report_start (report_start),
			KEY report_end (report_end),
			KEY report_priority (report_priority),
			KEY report_complete (report_complete)
		) $charset_collate;";


		// Create Report Settings Table

		$table_name = $wpdb->prefix . "custom_report_settings";

		$sql_settings = "CREATE TABLE IF NOT EXISTS $table_name (
                	ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			type varchar(20) NOT NULL DEFAULT '',
			code varchar(200) NOT NULL DEFAULT '',
			name varchar(200) NOT NULL DEFAULT '',
			PRIMARY KEY (ID),
			KEY type (type),
			KEY code (code),
			KEY name (name)
		) $charset_collate;";

           	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

           	dbDelta( $sql_cron );
           	dbDelta( $sql_report );
           	dbDelta( $sql_settings );


		global $wpdb;


		$table_name = $wpdb->prefix . "custom_report_settings";

		$wpdb->query("TRUNCATE TABLE $table_name");
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_orders_details', 'name'=> 'Customer and Order Detail (Item)' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_orders_details_by_order', 'name'=> 'Customer and Order Detail (Order)' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_pay_details', 'name'=> 'Payments Deposited (Item)' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_pay_details_by_order', 'name'=> 'Payments Deposited (Order)' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_pay_summary', 'name'=> 'Payments Deposited Summary' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_pay_uncollected', 'name'=> 'Cash Uncollected (Item)' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_pay_uncollected_by_order', 'name'=> 'Cash Uncollected (Order)' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_orders_unshipped', 'name'=> 'Orders Not Shipped (Item)' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_orders_unshipped_by_order', 'name'=> 'Orders Not Shipped (Order)' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_refund_date', 'name'=> 'Payments Refunded' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_refund_type', 'name'=> 'Payments Deposited & Refunded' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_drtv_returns', 'name'=> 'Daily Returns Report' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_reship_refund_summary', 'name'=> 'Reship and Refund Summary by Inventory SKU and Ship Week' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_cont_active', 'name'=> 'Active Subscription Count by Start Date' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_count_analysis', 'name'=> 'Subscription Analysis with Stick Rate By Month by Script' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_tax_details', 'name'=> 'Sales Tax Detail Report' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_tax_summary', 'name'=> 'Sales Tax Summary Report' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_ilm_shipped', 'name'=> 'Order Shipped by Ship Date' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_ship', 'name'=> 'Shipped Component Summary Report' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_backorder', 'name'=> 'Back Orders By SKU' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_tele', 'name'=> 'Order Details by Date, Promo and Media' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'fin_orders', 'name'=> 'Order Events Over Time Based on Order Date with Telemarketer' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'report', 'code'=> 'call_center', 'name'=> 'Call Center Comments' ), array( '%s', '%s', '%s' ) );

		$wpdb->insert( $table_name, array('type'=>'interval', 'code'=> 'yesterday', 'name'=> 'Yesterday' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'interval', 'code'=> 'last_7_days', 'name'=> 'Last 7 Days' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'interval', 'code'=> 'last_14_days', 'name'=> 'Last 14 Days' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'interval', 'code'=> 'last_30_days', 'name'=> 'Last 30 Days' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'interval', 'code'=> 'this_week', 'name'=> 'This Week' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'interval', 'code'=> 'last_week', 'name'=> 'Last Week' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'interval', 'code'=> 'this_month', 'name'=> 'This Month' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'interval', 'code'=> 'last_month', 'name'=> 'Last month' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'interval', 'code'=> 'this_year', 'name'=> 'This Year' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'interval', 'code'=> 'last_year', 'name'=> 'Last Year' ), array( '%s', '%s', '%s' ) );

		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '00', 'name'=> '12 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '01', 'name'=> '1 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '02', 'name'=> '2 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '03', 'name'=> '3 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '04', 'name'=> '4 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '05', 'name'=> '5 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '06', 'name'=> '6 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '07', 'name'=> '7 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '08', 'name'=> '8 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '09', 'name'=> '9 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '10', 'name'=> '10 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '11', 'name'=> '11 am' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '12', 'name'=> '12 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '13', 'name'=> '1 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '14', 'name'=> '2 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '15', 'name'=> '3 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '16', 'name'=> '4 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '17', 'name'=> '5 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '18', 'name'=> '6 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '19', 'name'=> '7 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '20', 'name'=> '8 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '21', 'name'=> '9 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '22', 'name'=> '10 pm' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'hour', 'code'=> '23', 'name'=> '11 pm' ), array( '%s', '%s', '%s' ) );

		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '00', 'name'=> '00' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '05', 'name'=> '05' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '10', 'name'=> '10' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '15', 'name'=> '15' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '20', 'name'=> '20' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '25', 'name'=> '25' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '30', 'name'=> '30' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '35', 'name'=> '35' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '40', 'name'=> '40' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '45', 'name'=> '45' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '50', 'name'=> '50' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'minute', 'code'=> '55', 'name'=> '55' ), array( '%s', '%s', '%s' ) );

		$wpdb->insert( $table_name, array('type'=>'frequency', 'code'=> 'daily', 'name'=> 'Daily' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'frequency', 'code'=> 'monthly', 'name'=> 'Monthly' ), array( '%s', '%s', '%s' ) );
		$wpdb->insert( $table_name, array('type'=>'frequency', 'code'=> 'annual', 'name'=> 'Annual' ), array( '%s', '%s', '%s' ) );


		// Create WP Cron Event

		if( !wp_next_scheduled( 'idealliving_reports_cronjob' ) ) {  
			wp_schedule_event( time(),  'five_minutes', 'idealliving_reports_cronjob' );  
		}



	}

	/**
	 * WooCommerce Deactivated Notice.
	 */
	private function il_reporting_woocommerce_deactivated() {
		/* translators: %s: WooCommerce link */
		echo '<div class="error"><p>' . sprintf( esc_html__( 'Ideal Living Reporting plugin requires %s to be installed and active.', 'woocommerce-il-reporting' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
	}

}
