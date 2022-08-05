<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://builtmighty.com
 * @since             1.0.0
 * @package           Woocommerce_Il_Reporting
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Ideal Living Reporting
 * Plugin URI:        https://builtmighty.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.27
 * Author:            Idealliving
 * Author URI:        https://idealliving.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-il-reporting
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOOCOMMERCE_IL_REPORTING_VERSION', '1.0.27' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-il-reporting-activator.php
 */
function activate_woocommerce_il_reporting() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-il-reporting-activator.php';
	Woocommerce_Il_Reporting_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-il-reporting-deactivator.php
 */
function deactivate_woocommerce_il_reporting() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-il-reporting-deactivator.php';
	Woocommerce_Il_Reporting_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_il_reporting' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_il_reporting' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-il-reporting.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_il_reporting() {

	$plugin = new Woocommerce_Il_Reporting();
	$plugin->run();

}
run_woocommerce_il_reporting();
