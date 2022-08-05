<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://builtmighty.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Il_Reporting
 * @subpackage Woocommerce_Il_Reporting/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Il_Reporting
 * @subpackage Woocommerce_Il_Reporting/includes
 * @author     Built Mighty <developers@builtmighty.com>
 */
class Woocommerce_Il_Reporting_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woocommerce-il-reporting',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
