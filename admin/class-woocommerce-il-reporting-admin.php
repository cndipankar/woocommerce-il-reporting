<?php

require plugin_dir_path( __FILE__ ) .'../Classes/autoload.php';

require plugin_dir_path( __FILE__ ) .'../includes/functions.php';

require plugin_dir_path( __FILE__ ) .'../includes/dashboard.php';
require plugin_dir_path( __FILE__ ) .'../includes/breakeven.php';
require plugin_dir_path( __FILE__ ) .'../includes/orders.php';
require plugin_dir_path( __FILE__ ) .'../includes/products.php';

require plugin_dir_path( __FILE__ ) .'../includes/fin_pay_details.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_pay_details_by_order.php';

require plugin_dir_path( __FILE__ ) .'../includes/fin_pay_uncollected.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_pay_uncollected_by_order.php';

require plugin_dir_path( __FILE__ ) .'../includes/fin_pay_summary.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_refund_date.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_refund_type.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_cont_active.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_backorder.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_count_analysis.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_drtv_returns.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_drtv_inventory.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_ilm_shipped.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_inventory.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_orders.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_pay_deposit.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_ship.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_tele.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_tax_summary.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_tax_details.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_month_kit.php';

require plugin_dir_path( __FILE__ ) .'../includes/fin_orders_details.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_orders_details_by_order.php';

require plugin_dir_path( __FILE__ ) .'../includes/fin_orders_unshipped.php';
require plugin_dir_path( __FILE__ ) .'../includes/fin_orders_unshipped_by_order.php';

require plugin_dir_path( __FILE__ ) .'../includes/fin_reship_refund_summary.php';

require plugin_dir_path( __FILE__ ) .'../includes/mojo_device.php';
require plugin_dir_path( __FILE__ ) .'../includes/mojo_weekly_metrics.php';
require plugin_dir_path( __FILE__ ) .'../includes/mojo_site_version.php';
require plugin_dir_path( __FILE__ ) .'../includes/mojo_sku_breakdown.php';
require plugin_dir_path( __FILE__ ) .'../includes/mojo_key_metrics.php';
require plugin_dir_path( __FILE__ ) .'../includes/mojo_sku_site.php';
require plugin_dir_path( __FILE__ ) .'../includes/mojo_orders.php';

require plugin_dir_path( __FILE__ ) .'../includes/call_center.php';

require plugin_dir_path( __FILE__ ) .'../includes/model_report.php';


use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://builtmighty.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Il_Reporting
 * @subpackage Woocommerce_Il_Reporting/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Il_Reporting
 * @subpackage Woocommerce_Il_Reporting/admin
 * @author     Built Mighty <developers@builtmighty.com>
 */
class Woocommerce_Il_Reporting_Admin {

    private $bootstrap_tabs = array('orders', 'products', 'export');

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

    private $alpha = array('', 'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ');

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Il_Reporting_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Il_Reporting_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-il-reporting-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'Nunito Sans', 'https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800,900&amp;display=swap', array(), $this->version, 'all' );	
		
		$tab = isset($_GET['tab']) ? $_GET['tab'] : '';
		
		if (in_array($tab, $this->bootstrap_tabs)) {
  		wp_enqueue_style( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css', array(), '4.5.0', 'all' );
	   }
     	
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Il_Reporting_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Il_Reporting_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-il-reporting-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'circle-progress', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-circle-progress/1.2.2/circle-progress.min.js', array( 'jquery' ), '1.2.2', true );
		wp_enqueue_script( 'moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'datepicker', plugin_dir_url( __FILE__ ) . 'js/datepicker.js', array( 'jquery' ), '1.0.47.110072190', true );
		
		$tab = isset($_GET['tab']) ? $_GET['tab'] : '';
		
		if (in_array($tab, $this->bootstrap_tabs)) {		
		  wp_enqueue_script( 'bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', array( 'jquery' ), '4.5.0', true );
		}
		
	}


	/**
	 * Undocumented function
	 *
	 * @param [str] $action
	 * @param [obj] $function
	 * @return void
	 */
    public function ideal_living_nounce_checker($action) {
	   // If download payments deposited
	   if ( isset( $_GET['action'] ) && $_GET['action'] == $action )  {
		   // Check for current user privileges 
		   if( !current_user_can( 'manage_options' ) ){ return false; }

		   // Check if we are in WP-Admin
		   if( !is_admin() ){ return false; }

		   // Nonce Check
		   $nonce = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
		   if ( ! wp_verify_nonce( $nonce, $action ) ) {
			   die( 'Security check error' );
		   }

		   // Export Monthly Payments CSV
		   return true;
	   }
   }

	/**
	 * Create Admin Page
	 */
    public function custom_exports_settings_menu() {

	add_menu_page(
		   'Reports',
		   'Reports',
		   'custom_report',
		   'custom-exports-options',
		   array(
			   $this,
			   'custom_exports_settings_page'
		   ),
	   );

	$addpage = add_submenu_page( 
		'', 
		'Report Cron Edit', 
		'Report Cron Edit', 
		'custom_report', 
		'custom_exports_cron_edit', 
		array( $this,'custom_exports_cron_edit_page'),
	);

	$addpage = add_submenu_page( 
		'', 
		'Report Cron Save', 
		'Report Cron Save', 
		'custom_report', 
		'woocommerce_il_reporting_save', 
		array( $this,'woocommerce_il_reporting_save_page'),
	);


	$addpage = add_submenu_page( 
		'', 
		'Report Cron Delete', 
		'Report Cron Delete', 
		'custom_report', 
		'woocommerce_il_reporting_delete', 
		array( $this,'woocommerce_il_reporting_delete_page'),
	);



   }

   /**
	* Admin Page Options/Display
	*/
    public function custom_exports_settings_page() {

		$default_tab = null;
		$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
		?>
		<div class="wrap">

			<nav class="nav-tab-wrapper">
				<!--
				<a href="?page=custom-exports-options" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>">Payments Deposited Summary</a>
				<a href="?page=custom-exports-options&tab=paymentsdeposited" class="nav-tab <?php if($tab==='paymentsdeposited'):?>nav-tab-active<?php endif; ?>">Payments Deposited</a>
                		<a href="?page=custom-exports-options&tab=shippedcomponent" class="nav-tab <?php if($tab==='shippedcomponent'):?>nav-tab-active<?php endif; ?>">Shipped Component</a>
                		<a href="?page=custom-exports-options&tab=kitassembly" class="nav-tab <?php if($tab==='kitassembly'):?>nav-tab-active<?php endif; ?>">Kit & Assembly</a>
                		<a href="?page=custom-exports-options&tab=breakeven" class="nav-tab <?php if($tab==='breakeven'):?>nav-tab-active<?php endif; ?>">Breakeven</a>
                		<a href="?page=custom-exports-options&tab=orders" class="nav-tab <?php if($tab==='orders'):?>nav-tab-active<?php endif; ?>">Orders</a>
                		<a href="?page=custom-exports-options&tab=products" class="nav-tab <?php if($tab==='products'):?>nav-tab-active<?php endif; ?>">Products</a>
                		<a href="?page=custom-exports-options&tab=dashboard" class="nav-tab <?php if($tab==='dashboard'):?>nav-tab-active<?php endif; ?>">Dashboard</a>
				-->
                		<a href="?page=custom-exports-options&tab=export" class="nav-tab <?php if($tab==='export' || $tab===null):?>nav-tab-active<?php endif; ?>">Reports</a>
	               		<a href="?page=custom-exports-options&tab=download" class="nav-tab <?php if($tab==='download'):?>nav-tab-active<?php endif; ?>">Download Reports</a>
                		<a href="?page=custom-exports-options&tab=settings" class="nav-tab <?php if($tab==='settings'):?>nav-tab-active<?php endif; ?>">Settings</a>
			</nav>

			<div class="tab-content">
            <?php switch($tab) :
            	case 'kitassembly':
					?>


					<?php include plugin_dir_path(__DIR__).'/admin/partials/kitassembly.php';?>


					<?php
					break;
            	case 'shippedcomponent':
					?>


					<?php include plugin_dir_path(__DIR__).'/admin/partials/shippedcomponent.php';?>


					<?php
					break;
				case 'paymentsdeposited':
					?>

					<?php include plugin_dir_path(__DIR__).'/admin/partials/paymentsdeposited.php';?>


					<?php
					break;
				case 'dashboard':
					?>


					<?php include plugin_dir_path(__DIR__).'/admin/partials/dashboard.php';?>


					<?php
					break;
				case 'breakeven':
					?>


					<?php include plugin_dir_path(__DIR__).'/admin/partials/breakeven.php';?>


					<?php
					break;

				case 'orders':
					?>


					<?php include plugin_dir_path(__DIR__).'/admin/partials/orders.php';?>


					<?php
					break;

                case 'products':
                        ?>
    
    
                        <?php include plugin_dir_path(__DIR__).'/admin/partials/products.php';?>
    
    
                        <?php
                        break;

                case 'export':
                        ?>
            
            
                        <?php include plugin_dir_path(__DIR__).'/admin/partials/export.php';?>
            
            
                        <?php
                        break;   

                case 'settings':
                        ?>
    
    
                        <?php include plugin_dir_path(__DIR__).'/admin/partials/settings.php';?>
    
    
                        <?php
                        break;


                case 'download':
                        ?>
    
    
                        <?php include plugin_dir_path(__DIR__).'/admin/partials/download.php';?>
    
    
                        <?php
                        break;
                       

				default:
					?>

					<!--
					<h2>Payments Deposited by Process Date with Source and Script Summary</h2>
					<form action="<?php echo admin_url( 'admin.php?page=custom-exports-options' ) ?>&action=download_monthly_payments_csv&_wpnonce=<?php echo wp_create_nonce( 'download_monthly_payments_csv' )?>" method="post" id="download_monthly_payments_csv" >
						<p>From:</p>
						<input type="date" name="dateFrom" value="<?php echo date('Y-m-d', strtotime("first day of last month")); ?>" />
						<p>To:</p>
						<input type="date" name="dateTo" value="<?php echo date('Y-m-d', strtotime("last day of last month")); ?>" />
						<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Export to CSV"></p>
					</form>
					-->

		                        <?php 
						include plugin_dir_path(__DIR__).'/admin/partials/export.php';
					?>

					<?php
					break;
			endswitch; ?>
			</div>
		</div>
		<?php



		// If download monthly payments
		if ($this->ideal_living_nounce_checker( 'download_monthly_payments_csv' )) {
			$this->monthly_payments_deposited_summary_report();
		}

		// If download payments deposited
		if ($this->ideal_living_nounce_checker( 'download_payments_deposited' )) {
			$this->payments_deposited_report();
		}
        
		// If download Kit Assembly
		if ($this->ideal_living_nounce_checker( 'download_kitassembly' )) {
			$this->kit_assembly_report();
		}

		// If get Dashboard data
		if ($this->ideal_living_nounce_checker( 'get_dashboard' )) {
			$this->get_dashboard();
		}

		// If get Breakeven data
		if ($this->ideal_living_nounce_checker( 'get_breakeven' )) {
			$this->get_breakeven();
		}

		// If download Dashboard
		if ($this->ideal_living_nounce_checker( 'dashboard_report' )) {
			$this->dashboard_report();
		}

		// If download Dashboard Affiliates
		if ($this->ideal_living_nounce_checker( 'dashboard_affiliates_report' )) {
			$this->dashboard_affiliates_report();
		}

		// If download Breakeven
		if ($this->ideal_living_nounce_checker( 'breakeven_report' )) {
			$this->breakeven_report();
		}
		
		// If get Orders data
		if ($this->ideal_living_nounce_checker( 'get_rep_orders' )) {
			$this->get_rep_orders();
		}

        // If download Orders data
		if ($this->ideal_living_nounce_checker( 'get_rep_orders_csv' )) {
			$this->get_rep_orders_csv();
		}

        // If get Products data
		if ($this->ideal_living_nounce_checker( 'get_rep_products' )) {
			$this->get_rep_products();
		}

        // If download Products data
		if ($this->ideal_living_nounce_checker( 'get_rep_products_csv' )) {
			$this->get_rep_products_csv();
		}

        // If download Orders data
		if ($this->ideal_living_nounce_checker( 'get_rep_export_csv' )) {
            if ( isset( $_GET['type'] ) && substr($_GET['type'], 0, 4) == 'run_' )  { 
                $this->run_rep_exp_orders_csv();
		} else if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_orders' )  { 
                $this->get_rep_exp_orders_csv();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_products' )  { 
                $this->get_rep_exp_orders_csv();

            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_pay_details' )  { 
                $this->exp_fin_pay_details();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_pay_details_by_order' )  { 
                $this->exp_fin_pay_details_by_order();

            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_pay_uncollected' )  { 
                $this->exp_fin_pay_uncollected();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_pay_uncollected_by_order' )  { 
                $this->exp_fin_pay_uncollected_by_order();

            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_pay_summary' )  { 
                $this->exp_fin_pay_summary();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_refund_date' )  { 
                $this->exp_fin_refund_date();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_refund_type' )  { 
                $this->exp_fin_refund_type();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_cont_active' )  { 
                $this->exp_fin_cont_active();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_backorder' )  { 
                $this->exp_fin_backorder();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_count_analysis' )  { 
                $this->exp_fin_count_analysis();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_drtv_returns' )  { 
                $this->exp_fin_drtv_returns();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_drtv_inventory' )  { 
                $this->exp_fin_drtv_inventory();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_ilm_shipped' )  { 
                $this->exp_fin_ilm_shipped();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_inventory' )  { 
                $this->exp_fin_inventory();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_orders' )  { 
                $this->exp_fin_orders();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_pay_deposit' )  { 
                $this->exp_fin_pay_deposit();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_ship' )  { 
                $this->exp_fin_ship();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_tele' )  { 
                $this->exp_fin_tele();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_tax_summary' )  { 
                $this->exp_fin_tax_summary();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_tax_details' )  { 
                $this->exp_fin_tax_details();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_month_kit' )  { 
                $this->exp_fin_month_kit();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_orders_details' )  { 
                $this->exp_fin_orders_details();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_orders_details_by_order' )  { 
                $this->exp_fin_orders_details_by_order();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_orders_unshipped' )  { 
                $this->exp_fin_orders_unshipped();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_orders_unshipped_by_order' )  { 
                $this->exp_fin_orders_unshipped_by_order();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_reship_refund_summary' )  { 
                $this->exp_fin_reship_refund_summary();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_fin_orders' )  { 
                $this->exp_fin_orders();                
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_mojo_device' )  { 
                $this->exp_mojo_device();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_mojo_weekly_metrics' )  { 
                $this->exp_mojo_weekly_metrics();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_mojo_site_version' )  { 
                $this->exp_mojo_site_version();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_mojo_sku_breakdown' )  { 
                $this->exp_mojo_sku_breakdown();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_mojo_key_metrics' )  { 
                $this->exp_mojo_key_metrics();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_mojo_sku_site' )  { 
                $this->get_rep_mojo_sku_site();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_mojo_orders' )  { 
                $this->exp_mojo_orders();
            } else  if ( isset( $_GET['type'] ) && $_GET['type'] == 'exp_call_center' )  { 
                $this->exp_call_center();
	    }
        }
    }



    /**
    * Download Headers
    */
    public function get_download_report_csv_headers($filename) {
        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
        header( 'Content-Description: File Transfer' );
        header( 'Content-Type: application/csv; charset=UTF-8' );
        header( "Content-Disposition: attachment; filename={$filename}" );
        header( 'Expires: 0' );
        header( 'Pragma: public' );
    }

    
    public function get_download_report_xls_headers($filename) {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header( 'Content-Description: File Transfer' );
        header( 'Content-Type: application/vnd.ms-excel' );        
        header( "Content-Disposition: attachment; filename={$filename}" );
        header( 'Expires: 0' );
        header( 'Pragma: public' );
        header('Cache-Control: max-age=0');
        header('Cache-Control: cache, must-revalidate');
    }


    public function writeWorksheetArray(&$sheet, $line=0, $data, $param=array('border' => 1, 'even-background' => 1)) {

        $row_no = $line;
        // Title

        if (array_key_exists('title', $data) && strlen( $data['title']) > 0) {
            $row_no = $row_no + 1;
            $sheet->setCellValueByColumnAndRow(1, $row_no, $data['title']);
            $max_col = 1;
            if (array_key_exists('header', $data)) {
                $max_col = count($data['header']);
            }
            $sheet->mergeCells($this->alpha[1] . $row_no . ':' . $this->alpha[$max_col] . $row_no);
            $sheet->getStyle($this->alpha[1] . "1" . ':' . $this->alpha[$max_col] . $row_no)->getAlignment()->setHorizontal('center');
            $sheet->getStyle($this->alpha[1] . "1" . ':' . $this->alpha[$max_col] . $row_no)->getFont()->setBold(true);
            $row_no = $row_no + 2;

        }

	$table_first_row = 0;
	$table_end_row = 0;
	$table_first_column = 0;
	$table_end_column = 0;


        // Rows - header
        $total = array();
        if (array_key_exists('header', $data)) {
            $col = 0;

	    if ($table_first_row == 0) {
		$table_first_row = $row_no;
	    }
	    $table_end_row = $row_no;
	    $table_first_column = 1;

            foreach($data['header'] as $value) {
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $row_no, $value);
                $sheet->getStyle($this->alpha[$col] . $row_no)->getFont()->setBold(true);
                if ($param['border'] == 1) {
                    $sheet->getStyle($this->alpha[$col] . $row_no)->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }
                $sheet->getStyle($this->alpha[$col] . $row_no)->getAlignment()->setHorizontal('center');
                $total[$col] = 0;
		$table_end_column = $col;
            }
            $row_no = $row_no + 1;
        } 

        $unit = array();
        if (!empty($data['unit'])) {
            $col = 0;
            foreach($data['unit'] as $value) {
                $col++;
                $unit[$col] = $value;
            }
        }  
        $summarize = array();
        if (!empty($data['total'])) {
            $col = 0;
            foreach($data['total'] as $value) {
                $col++;
                $summarize[$col] = $value;
            }
        }             
        
        // Rows - content
        if (!empty($data['rows'])) {
            foreach($data['rows'] as $row) {
                $col = 0;

		if ($table_first_row == 0) {
			$table_first_row = $row_no;
		}

                foreach($row as $value) {
                    $col++;
                    $sheet->setCellValueByColumnAndRow($col, $row_no, $value);
                    if ($unit[$col] == '$') {
			//echo $this->alpha[$col] . $row_no; die();
                        $sheet->getStyle($this->alpha[$col] . $row_no)->getNumberFormat()->setFormatCode('$ #,##0.00');
                    }
                    if ($unit[$col] == '%') {
                        $sheet->setCellValueByColumnAndRow($col, $row_no, $value / 100);
                        $sheet->getStyle($this->alpha[$col] . $row_no)->getNumberFormat()->setFormatCode('##0.00%');
                    }
                    if (is_numeric($value)) {
                        $total[$col] = $total[$col] + $value;
                    }
                    if($row_no % 2 == 0 &&  $param['even-background'] == 1 ){
                        $sheet->getStyle($this->alpha[$col] . $row_no)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D9D9D9');
                    }
                    if ($param['border'] == 1) {
                        $sheet->getStyle($this->alpha[$col] . $row_no)->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    }                     
                }
		$table_end_row = $row_no;
                $row_no++;
            }
        }                


        // Rows - total
        $col = 0;

      if ($table_end_row > $table_first_row) {
        foreach($total as $value) {
            $col++;

            if (strlen($summarize[$col])>0) { 
                $sheet->setCellValueByColumnAndRow($col, $row_no, "=SUBTOTAL(9, ".$this->alpha[$col].($table_first_row+1).":".$this->alpha[$col].$table_end_row.")");
                if ($unit[$col] == '$') {
                    $sheet->getStyle($this->alpha[$col] . $row_no)->getNumberFormat()->setFormatCode('$ #,##0.00');
                }
                if ($unit[$col] == '%') {
                    $sheet->setCellValueByColumnAndRow($col, $row_no, $value / 100);
                    $sheet->getStyle($this->alpha[$col] . $row_no)->getNumberFormat()->setFormatCode('##0.00%');
                }               
            }
            $sheet->getStyle($this->alpha[$col] . $row_no)->getFont()->setBold(true);
            if ($param['border'] == 1) {
                $sheet->getStyle($this->alpha[$col] . $row_no)->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }  
        }
        $row_no++;
      }


	$sheet->setAutoFilter($this->alpha[$table_first_column].$table_first_row.":".$this->alpha[$table_end_column].$table_end_row);


        // Autosize columns
        if (array_key_exists('header', $data)) {
            $max_col = count($data['header']);
            for ($i = 1; $i <= $max_col; $i++) {
                $sheet->getColumnDimension($this->alpha[$i])->setAutoSize(true);
            }
        }


    }    

    public function writeWorksheetScalar(&$sheet, $line=0, $data, $param=array('border' => 1, 'even-background' => 1) ) {

        $line1 = $line;
        $line2 = $line + 1;

        if (!empty($data)) {
            $col = 0;
            foreach($data as $value) {
                $col++;
                $sheet->setCellValueByColumnAndRow($col, $line1, $value['header']);
                $sheet->getStyle($this->alpha[$col] . $line1)->getFont()->setBold(true);
                if ($param['border'] == 1) {
                    $sheet->getStyle($this->alpha[$col] . $line1)->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }

                $sheet->setCellValueByColumnAndRow($col, $line2, $value['value']);
                if ($value['unit'] == '$') {
                    $sheet->getStyle($this->alpha[$col] . $line2)->getNumberFormat()->setFormatCode('$ #,##0.00');
                }
                if ($value['unit'] == '%') {
                    $sheet->getStyle($this->alpha[$col] . $line2)->getNumberFormat()->setFormatCode('##0.00%');
                }  
                if ($param['border'] == 1) {
                    $sheet->getStyle($this->alpha[$col] . $line2)->getBorders()->getOutline()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                }                              
            }
        }

    }    

    /**
    * Cron Job Schedule options
    */
    public function cron_job_custom_intervals($schedules) {
        // add a 'weekly' interval
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display' => __('Once Weekly')
        );
        $schedules['monthly'] = array(
            'interval' => 2635200,
            'display' => __('Once a month')
        );
        return $schedules;
    }

    /**
    * Export Monthly Payments CSV
    */
    public function monthly_payments_deposited_summary_report($interval = null){

        error_log(date('F d Y g:i:s') . " Monthly Deposited Summary : ".$interval." \n", 3, dirname(__FILE__ ).'/cron.log');

        if($interval):
            // Set upload directory and file name for creating the file.
            $upload_dir = wp_upload_dir();
            $filename = $upload_dir['basedir'].'/Monthly-Payments-Deposited-by-Process-Date-with-Source-and-Script-Summary_'.date('m-d-y').'.csv';

            // Create/Open file
            $fh = @fopen( $filename, 'w' );

            ob_start();
        else:
            // Query Filters
            $date_from = $_REQUEST['dateFrom'];
            $date_to = $_REQUEST['dateTo'];

            $filename = 'Monthly-Payments-Deposited-by-Process-Date-with-Source-and-Script-Summary_'.$date_from.'-'.$date_to.'.csv';
            $fh = @fopen( 'php://output', 'w' );
            // Get Content Headers for Downloading File
            $this->get_download_report_csv_headers($filename);

            // Clean current buffer prevent exporting html of current page...
            ob_end_clean();
        endif;

        $header_row = array(
            'Source',
            'Order Type',
            'Promo Description',
            'Product',
            'ShipFee',
            'Tax',
            'Qty'
        );
        // Add the headers to csv
        fputcsv( $fh, $header_row );
        if( have_rows('order_source', 'option') ):
            while( have_rows('order_source', 'option') ) : the_row();
                $source_name = get_sub_field('name');

                $query = new WC_Order_Query( array(
                    'limit' => -1,
                    'meta_key' => 'order_source',
                    'meta_value' => $source_name,
                ) );

                // Set download query args
                if($date_from && $date_to):
                    $args['date_created'] = $date_from.'...'.$date_to;
                endif;

                $orders = $query->get_orders();
                
                $total=0;
                $shipping=0;
                $tax=0;
                $order_count = 0;
                foreach( $orders as $order ) {
                    $total += $order->total;
                    $shipping += $order->shipping_total;
                    $tax += $order->total_tax;
                    $order_count++;
                }

                // Add the data to csv
                $row = array($source_name, 'TODO', 'TODO', '$'.$total, '$'.$shipping, '$'.$tax, $order_count);
                fputcsv($fh, $row);
            endwhile;
        endif;

        if($interval):
            ob_end_clean();
            // Get all recipients for the Weekly Payments Deposited Summary
            $to = array();
            if($interval == 'daily'):
                if(have_rows('payments_deposited_daily_recipients', 'option')):
                    while(have_rows('payments_deposited_daily_recipients', 'option')): the_row();
                        $to[] = get_sub_field('email');
                    endwhile;
                endif;
            elseif($interval == 'weekly'):
                if(have_rows('payments_deposited_summary_weekly_recipients', 'option')):
                    while(have_rows('payments_deposited_summary_weekly_recipients', 'option')): the_row();
                        $to[] = get_sub_field('email');
                    endwhile;
                endif;
            elseif($interval == 'monthly'):
                if(have_rows('payments_deposited_summary_monthly_recipients', 'option')):
                    while(have_rows('payments_deposited_summary_monthly_recipients', 'option')): the_row();
                        $to[] = get_sub_field('email');
                    endwhile;
                endif;
            endif;

            // Testing Cron
            error_log(date('F d Y g:i:s') . " Cron Triggered and passed the arg: ".$interval." \n", 3, dirname(__FILE__ ).'/cron.log');

            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: '. get_bloginfo( 'name' ) .' <'. get_bloginfo( 'admin_email' ) .'>'
            );
            $subject = 'Exported Report';
            $msg = 'Attached is your '.$interval.' Payments Deposited by Process Date with Source and Script Summary report!'; 

            // Send email with attachment from create_csv function
            wp_mail($to, $subject, $msg, $headers, $filename);

            // Delete the file... we no longer need it.
            if (file_exists($filename)):
                unlink($filename);
            endif;
        // Else we're downloading report
        else:
            fclose($fh);
            exit;
        endif;
    }

    /**
    * Export Payments Deposited CSV
    */
    public function payments_deposited_report($interval = null){

        error_log(date('F d Y g:i:s') . " Payments deposited : ".$interval." \n", 3, dirname(__FILE__ ).'/cron.log');

        if($interval):
            // Set upload directory and file name for creating the file.
            $upload_dir = wp_upload_dir();
            $filename = $upload_dir['basedir'].'/Payments-deposited_'.date('m-d-y').'.csv';

            // Create/Open file
            $fh = @fopen( $filename, 'w' );

            ob_start();
        else:
            // Query Filters
            $date_from = $_REQUEST['dateFrom'];
            $date_to = $_REQUEST['dateTo'];

            $product_sku = $_REQUEST['product_sku'];

            $filename = 'Payments Deposited by Process Date with Source and Script_'.$date_from.'-'.$date_to.'.csv';
            $fh = @fopen( 'php://output', 'w' );
            $this->get_download_report_csv_headers($filename);

            // Clean current buffer prevent exporting html of current page...
            ob_end_clean();
        endif;

        $header_row = array(
            'Deposit_Date',
            'Zip_Code',
            'State',
            'FirstName',
            'LastName',
            'Order#',
            'PaymentNo',
            'Payment Code',
            'Pmt_Type',
            'Qty Ordered',
            'Amount',
            'BackEndCode',
            'Telemarketer',
            'Script',
            'Promo Description',
            'Installment #',
            'Order Date',
            'Doc',
            'Doc ShipDate',
            'Customer CreationDate',
        );

        // Add the headers to csv
        fputcsv( $fh, $header_row );
        $args = array(
            'limit' => -1,
            'status' => 'scheduled-payment',
        );

        // Set download query args
        if($date_from && $date_to):
            $args['date_created'] = $date_from.'...'.$date_to;
        endif;



        $orders = wc_get_orders( $args );
        foreach( $orders as $order ) {
            $order_id = $order->ID;

            $deposit_date = 'TODO';
            $postcode = $order->get_billing_postcode();
            $state = $order->get_billing_state();
            $first_name = $order->get_billing_first_name();
            $last_name = $order->get_billing_last_name();

            $transaction_id = 'Transaction ID?';
            if($order->get_transaction_id):
                $transaction_id = $order->get_transaction_id;
            endif;

            $payment_method = 'Credit Card?';
            if($order->get_payment_method):
                $payment_method = $order->get_payment_method;
            endif;

            $payment_method = 'Credit Card?';
            if($order->get_payment_method):
                $payment_method = $order->get_payment_method;
            endif;

            $payment_type = 'What is this?';

            $total = '0.00';
            if($order->get_total()):
                $total = $order->get_total();
            endif;

            $order_source = '';
            if($order->get_meta('order_source')):
                $order_source = $order->get_meta('order_source');
            endif;

            $line_items = $order->get_items();
            foreach( $line_items as $item){
                $row = array($deposit_date, $postcode, $state, $first_name, $last_name, $order_id, $transaction_id, $payment_method, $payment_type, 'TODO', '$'.$total, 'TODO', $order_source, 'TODO', 'TODO', 'TODO', 'TODO', 'TODO', 'TODO', 'TODO');
                fputcsv($fh, $row);
            }
            
            // Ship Fee Line
            $shipping = $order->get_total_shipping();
            $row = array($deposit_date, $postcode, $state, $first_name, $last_name, $order_id, $transaction_id, $payment_method, 'SHIPFEE', '1', '$'.$shipping, 'TODO', $order_source, 'TODO', 'TODO', 'TODO', 'TODO', 'TODO', 'TODO', 'TODO');
            fputcsv($fh, $row);
            // Tax Fee Line
            $tax = $order->get_total_tax();
            $row = array($deposit_date, $postcode, $state, $first_name, $last_name, $order_id, $transaction_id, $payment_method, 'TAX', '1', '$'.$tax, 'TODO', $order_source, 'TODO', 'TODO', 'TODO', 'TODO', 'TODO', 'TODO', 'TODO');
            fputcsv($fh, $row);
        }

        if($interval):
            ob_end_clean();
            // Get all recipients for the Weekly Payments Deposited Summary
            $to = array();
            if($interval == 'daily'):
                if(have_rows('payments_deposited_daily_recipients', 'option')):
                    while(have_rows('payments_deposited_daily_recipients', 'option')): the_row();
                        $to[] = get_sub_field('email');
                    endwhile;
                endif;
            elseif($interval == 'weekly'):
                if(have_rows('payments_deposited_weekly_recipients', 'option')):
                    while(have_rows('payments_deposited_weekly_recipients', 'option')): the_row();
                        $to[] = get_sub_field('email');
                    endwhile;
                endif;
            elseif($interval == 'monthly'):
                if(have_rows('payments_deposited_monthly_recipients', 'option')):
                    while(have_rows('payments_deposited_monthly_recipients', 'option')): the_row();
                        $to[] = get_sub_field('email');
                    endwhile;
                endif;
            endif;

            // Testing Cron
            error_log(date('F d Y g:i:s') . " Cron Triggered and passed the arg: ".$interval." \n", 3, dirname(__FILE__ ).'/cron.log');

            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: '. get_bloginfo( 'name' ) .' <'. get_bloginfo( 'admin_email' ) .'>'
            );
            $subject = 'Exported Report';
            $msg = 'Attached is your '.$interval.' Payments Deposited report!'; 

            // Send email with attachment from create_csv function
            if ( !empty($to) ) {
            		wp_mail($to, $subject, $msg, $headers, $filename);
            }

            // Delete the file... we no longer need it.
            if (file_exists($filename)):
                unlink($filename);
            endif;
        // Else we're downloading report
        else:
            fclose($fh);
            exit;
        endif;
    }
    
    /* 
    * Triggered via a cron e.g. following command would run this function daily and pass the variable daily to determine the receipient list used
    * wp cron event schedule cron_kit_assembly_summary now daily --interval=daily
    */
    public function kit_assembly_report($interval = null){

        error_log(date('F d Y g:i:s') . " Kit Assembly : ".$interval." \n", 3, dirname(__FILE__ ).'/cron.log');
        
        if($interval):
            // Set upload directory and file name for creating the file.
            $upload_dir = wp_upload_dir();
            $filename = $upload_dir['basedir'].'/Kit-Assembly_'.date('m-d-y').'.csv';

            // Create/Open file
            $fh = @fopen( $filename, 'w' );

            ob_start();
        else:
            $filename = 'Kit Assembly.csv';
            $fh = @fopen( 'php://output', 'w' );
            $this->get_download_report_csv_headers($filename);

            // Clean current buffer prevent exporting html of current page...
            ob_end_clean();
        endif;

        // Add the headers to csv
        $header_row = array(
            'SkuInKit',
            'KitSkuDescription',
            'KitSku',
            'SkuDescription',
            'QtyInKit',
        );
        fputcsv( $fh, $header_row );
        $args = array(
            'post_type' => 'product',
        );
        $products = wc_get_products( $args );
        foreach( $products as $product ) {
            $offers = get_field('offers', $product->get_id());
            if( $offers ){
                $offer_count = 1;
                foreach( $offers as $offer ){
                    $offer_id = $offer->ID;
                    $sku_in_kit = '';
                    $kit_sku_description = '';
                    if($offer_count == 1):
                        $sku_in_kit = $product->get_sku();
                        $kit_sku_description = $product->get_title();
                    endif;
                    $kit_sku = get_field('skus', $offer_id);
                    $sku_description = get_field('title', $offer_id);
                    $qty = get_field('', $offer_id);
                    $row = array($sku_in_kit, $kit_sku_description, $kit_sku, $sku_description, 'TODO');
                    fputcsv($fh, $row);
                    $offer_count++;
                }
            }
        }

        if($interval):
            ob_end_clean();
            // Get all recipients for the Weekly Payments Deposited Summary
            $to = array();
            if($interval == 'daily'):
                if(have_rows('kit_and_assembly_daily_recipients', 'option')):
                    while(have_rows('kit_and_assembly_daily_recipients', 'option')): the_row();
                        $to[] = get_sub_field('email');
                    endwhile;
                endif;
            elseif($interval == 'weekly'):
                if(have_rows('kit_and_assembly_weekly_recipients', 'option')):
                    while(have_rows('kit_and_assembly_weekly_recipients', 'option')): the_row();
                        $to[] = get_sub_field('email');
                    endwhile;
                endif;
            elseif($interval == 'monthly'):
                if(have_rows('kit_and_assembly_monthly_recipients', 'option')):
                    while(have_rows('kit_and_assembly_monthly_recipients', 'option')): the_row();
                        $to[] = get_sub_field('email');
                    endwhile;
                endif;
            endif;

            // Testing Cron
            error_log(date('F d Y g:i:s') . " Cron Triggered and passed the arg: ".$interval." \n", 3, dirname(__FILE__ ).'/cron.log');

            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: '. get_bloginfo( 'name' ) .' <'. get_bloginfo( 'admin_email' ) .'>'
            );
            $subject = 'Exported Report';
            $msg = 'Attached is your report!'; 

            // Send email with attachment from create_csv function
            wp_mail($to, $subject, $msg, $headers, $filename);

            // Delete the file... we no longer need it.
            if (file_exists($filename)):
                unlink($filename);
            endif;
        // Else we're downloading report
        else:
            fclose($fh);
            exit;
        endif;
    }








	public function get_dashboard() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];

		$result = get_dashboard_data($data1, $data2, 'brief');

		ob_end_clean();

		echo $result;

		die();

	}


    public function dashboard_report() {

        $data1 = $_REQUEST['data1'];
        $data2 = $_REQUEST['data2'];
    
        $filename = "/orders-$data1-$data2.csv";
    
        $fh = @fopen( 'php://output', 'w' );
        $this->get_download_report_csv_headers($filename);
    
        ob_end_clean();
    
        $header_row = array(
            'orderID',
            'order_date',
            'value',
            'items',
            'shipping',
            'tax',
            'total',
            'aid',
            'source',
            'name',
            'friendly_name',
            'category',
            'website'
        );
    
        fputcsv( $fh, $header_row );
    
    
        $dashboard = json_decode(get_dashboard_data($data1, $data2, 'details'), true);
    
        foreach ($dashboard as $data) {
            $data_row = array(
                $data['orderId'],
                $data['order_date'],
                sprintf("%0.2f", $data['value']),
                sprintf("%0.2f", $data['items']),
                sprintf("%0.2f", $data['shipping']),
                sprintf("%0.2f", $data['tax']),
                sprintf("%0.2f", $data['total']),
                $data['aid'],
                $data['source'],
                $data['name'],
                $data['friendly_name'],
                $data['category'],
                $data['website']
            );
            fputcsv( $fh, $data_row );
        }
    
        fclose($fh);
    
    
        exit;
    
    }
    
    
    public function dashboard_affiliates_report() {
    
        $data1 = $_REQUEST['data1'];
        $data2 = $_REQUEST['data2'];
    
    
        $filename = "/affiliates-$data1-$data2.csv";
    
        $fh = @fopen( 'php://output', 'w' );
        $this->get_download_report_csv_headers($filename);
    
        ob_end_clean();
    
        $header_row = array(
            'Affiliate',
            'Total Orders',
            'Total Sales'
        );
    
        fputcsv( $fh, $header_row );
    
    
        $dashboard = json_decode(get_dashboard_data($data1, $data2, 'brief'), true);
    
        foreach ($dashboard['affiliates'] as $data) {
            $data_row = array(
                $data['name'],
                sprintf("%0.2f", $data['count']),
                sprintf("%0.2f", $data['value'])
            );
            fputcsv( $fh, $data_row );
        }
    
        fclose($fh);
    
    
        exit;
    
    }
    

	public function get_breakeven() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];

		$result = get_breakeven_data($data1, $data2);

		ob_end_clean();

		echo $result;

		die();

	}


	public function breakeven_report() {


		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];


		$filename = "/breakeven-$data1-$data2.csv";

		$fh = @fopen( 'php://output', 'w' );
		$this->get_download_report_csv_headers($filename);

		ob_end_clean();

		$header_row = array(
			'Category',
			'Product name',
			'Offer Code', 
			'Units Sold',
			'Price', 
			'Revenue'
		);

		fputcsv( $fh, $header_row );


		$breakeven = json_decode(get_breakeven_data($data1, $data2), true);




		foreach ($breakeven as $key => $categ) {
			foreach ($categ as $data) {
				$data_row = array(
					$key, 
					$data['name'],
					$data['sku'],
					sprintf("%d", $data['qty']),
					sprintf("%0.2f", $data['price']), 
					sprintf("%0.2f", $data['revenue'])
				);
				fputcsv( $fh, $data_row );
			}
		}
 

		fclose($fh);


		exit;
	}


	public function get_rep_orders() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		
		$filters['search']  = urldecode($_REQUEST['req_search'] ?? '');
		$filters['status']  = $_REQUEST['rec_status'] ?? '';
		$filters['payment'] = $_REQUEST['rec_payment'] ?? '';
		
		$columns['status']     = $_REQUEST['f_status'] ?? '';
		$columns['order_date'] = $_REQUEST['f_date'] ?? '';
		$columns['value']      = $_REQUEST['f_value'] ?? '';
		$columns['affiliate']  = $_REQUEST['f_affiliate'] ?? '';
		$columns['fname']      = $_REQUEST['f_fname'] ?? '';
		$columns['lname']      = $_REQUEST['f_lname'] ?? '';
		$columns['phone']      = $_REQUEST['f_phone'] ?? '';
		$columns['email']      = $_REQUEST['f_email'] ?? '';
		$columns['address']    = $_REQUEST['f_address'] ?? '';
		$columns['city']       = $_REQUEST['f_city'] ?? '';
		$columns['state']      = $_REQUEST['f_state'] ?? '';
		$columns['zip']        = $_REQUEST['f_zip'] ?? '';
		
        	$pageno = $_REQUEST['pageno'];
        	$rows = $_REQUEST['rows'];

		$result = get_rep_orders_data($data1, $data2, $filters, $columns, $pageno, $rows);
		
		ob_end_clean();

		echo $result;

		die();

	}
	
	    
	public function get_rep_orders_csv() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		
		$filters['search']  = urldecode($_REQUEST['req_search'] ?? '');
		$filters['status']  = $_REQUEST['rec_status'] ?? '';
		$filters['payment'] = $_REQUEST['rec_payment'] ?? '';
		
		$columns['status']     = 1;
		$columns['order_date'] = 1;
		$columns['value']      = 1;
        $columns['items']      = 1;
        $columns['shipping']      = 1;
		$columns['affiliate']  = 1;
		$columns['fname']      = 1;
		$columns['lname']      = 1;
		$columns['phone']      = 1;
		$columns['email']      = 1;
		$columns['address']    = 1;
		$columns['city']       = 1;
		$columns['state']      = 1;
		$columns['zip']        = 1;
		
		$result = get_rep_orders_data($data1, $data2, $filters, $columns, 0, 100000);

        $filename = "/orders-$data1-$data2.csv";

        $fh = @fopen( 'php://output', 'w' );
		$this->get_download_report_csv_headers($filename);

		ob_end_clean();

		$header_row = array(
			'Order ID', 
            'Status', 
            'Date', 
            'Total', 
            'Value',
            'Shipping',
            'First Name', 
            'Last Name', 
            'Phone', 
            'Email', 
            'Address', 
            'City', 
            'State', 
            'ZIP'
		);

		fputcsv( $fh, $header_row );

		$orders = json_decode($result, true);

		foreach ($orders['orders'] as $order) {
			$data_row = array(
				$order['orderId'], 
                $order['status'], 
                $order['order_date'], 
                sprintf("%0.2f",$order['total']), 
                sprintf("%0.2f",$order['items']), 
                sprintf("%0.2f",$order['shipping']),  
                $order['fname'], 
                $order['lname'], 
                $order['phone'], 
                $order['email'], 
                $order['address'], 
                $order['city'], 
                $order['state'], 
                $order['zip']
			);
			fputcsv( $fh, $data_row );
		}
 
		fclose($fh);


		exit;
	}


	public function get_rep_products() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		
        $type = $_REQUEST['type'];

		$result = get_rep_products_data($data1, $data2, $type);

		ob_end_clean();

		echo $result;

		die();

	}
    

	public function get_rep_products_csv() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		
		exit;
	}


	public function get_rep_exp_orders_csv() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];

        exit;
        
	}    
  


	public function get_rep_mojo_sku_site() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Report_SkuBySiteVersion-$data1-$data2.xlsx";
        $result = get_mojo_sku_site_data($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetScalar($sheet, 1, $data1['scalar']);
            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


	public function exp_fin_pay_details() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Payments-Deposited-(item)-$data1-$data2.xlsx";
        $result = get_fin_pay_details($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	} 



	public function exp_fin_pay_details_by_order() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Payments-Deposited-(order)-$data1-$data2.xlsx";
        $result = get_fin_pay_details_by_order($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  
 


	public function exp_fin_pay_uncollected() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Payments-Uncollected-(item)-$data1-$data2.xlsx";
        $result = get_fin_pay_uncollected($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

	$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	} 



	public function exp_fin_pay_uncollected_by_order() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Payments-Uncollected-(order)-$data1-$data2.xlsx";
        $result = get_fin_pay_uncollected_by_order($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

	$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  
 


	public function exp_fin_pay_summary() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Payments-Deposited-Summary-$data1-$data2.xlsx";
        $result = get_fin_pay_summary($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


	public function exp_fin_refund_date() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Payments-Refunded-$data1-$data2.xlsx";
        $result = get_fin_refund_date($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_refund_type() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Payments-Deposited-And-Refunded-by-Order-Type-$data1-$data2.xlsx";
        $result = get_fin_refund_type($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_cont_active() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Active-Subscription-$data1-$data2.xlsx";
        $result = get_fin_cont_active($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  
    

    public function exp_fin_backorder() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Back-Orders-By-SKU-$data1-$data2.xlsx";
        $result = get_fin_backorder($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_count_analysis() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Subscription-Analysis-$data1-$data2.xlsx";
        $result = get_fin_count_analysis($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_drtv_returns() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Daily-Returns-Report-$data1-$data2.xlsx";
        $result = get_fin_drtv_returns($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_drtv_inventory() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Report-$data1-$data2.xlsx";
        $result = get_fin_drtv_inventory($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_ilm_shipped() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Order-Shipped -$data1-$data2.xlsx";
        $result = get_fin_ilm_shipped($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_inventory() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Report-$data1-$data2.xlsx";
        $result = get_fin_inventory($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_orders() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Order-Events-$data1-$data2.xlsx";
        $result = get_fin_orders($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_pay_deposit() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Report-$data1-$data2.xlsx";
        $result = get_fin_pay_deposit($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_ship() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Shipped-Component-Summary-Report-$data1-$data2.xlsx";
        $result = get_fin_ship($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_tele() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Order-Details-$data1-$data2.xlsx";
        $result = get_fin_tele($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_tax_summary() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Tax-Summary-$data1-$data2.xlsx";
        $result = get_fin_tax_summary($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_tax_details() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Tax-Detail-$data1-$data2.xlsx";
        $result = get_fin_tax_details($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}      


    public function exp_fin_month_kit() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Report-$data1-$data2.xlsx";
        $result = get_fin_month_kit($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_fin_orders_details() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Customer-and-Order-Detail-(item)-$data1-$data2.xlsx";
        $result = get_fin_orders_details($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  



    public function exp_fin_orders_details_by_order() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Customer-and-Order-Detail-(order)-$data1-$data2.xlsx";
        $result = get_fin_orders_details_by_order($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}



    public function exp_fin_orders_unshipped() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Orders-Not-Shipped-(item)-$data1-$data2.xlsx";
        $result = get_fin_orders_unshipped($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

	$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  



    public function exp_fin_orders_unshipped_by_order() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Orders-Not-Shipped-(order)-$data1-$data2.xlsx";
        $result = get_fin_orders_unshipped_by_order($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

	$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}


    public function exp_fin_reship_refund_summary() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Reship-and-Refund-Summary-by-Inventory-SKU-$data1-$data2.xlsx";
        $result = get_fin_reship_refund_summary($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  




    public function exp_call_center() {

	$data1 = $_REQUEST['data1'];
	$data2 = $_REQUEST['data2'];
		

        $filename = "Call-center-comments-$data1-$data2.xlsx";
        $result = get_call_center($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

	$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_mojo_device() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Device-Report-$data1-$data2.xlsx";
        $result = get_mojo_device($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_mojo_weekly_metrics() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Report-Metrics-$data1-$data2.xlsx";
        $result = get_mojo_weekly_metrics($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_mojo_site_version() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Site-Version-$data1-$data2.xlsx";
        $result = get_mojo_site_version($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_mojo_sku_breakdown() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "SKU-Breakdown-$data1-$data2.xlsx";
        $result = get_mojo_sku_breakdown($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_mojo_key_metrics() {

		$data1 = $_REQUEST['data1'];
		$data2 = $_REQUEST['data2'];
		

        $filename = "Key-Metrics-$data1-$data2.xlsx";
        $result = get_mojo_key_metrics($data1, $data2);
        $data = json_decode($result, true);
        

        $spreadsheet = new Spreadsheet();


        foreach ($data as $ey => $data1) {
           
            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();            
            $sheet->setTitle($data1['array']['sheetname']);    

            $this->writeWorksheetArray($sheet, 3, $data1['array']);
        }

        $writer = new Xlsx($spreadsheet);

		$this->get_download_report_xls_headers($filename);
        ob_end_clean();

        $writer->save('php://output');

        exit;


	}  


    public function exp_mojo_orders() {

	//ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE );

	$data1 = $_REQUEST['data1'];
	$data2 = $_REQUEST['data2'];
		

        $filename = "Orders-$data1-$data2.xlsx";
        $result = get_mojo_orders($data1, $data2);
        $data = json_decode($result, true);
        
        $spreadsheet = new Spreadsheet();

        foreach ($data as $ey => $data1) {

            if ($spreadsheet->getSheetByName('Worksheet') == null) {
                $spreadsheet->createSheet();
            }
            $spreadsheet->setActiveSheetIndexByName('Worksheet');
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($data1['array']['sheetname']);    
            $this->writeWorksheetArray($sheet, 3, $data1['array']);


        }

        $writer = new Xlsx($spreadsheet);

	$this->get_download_report_xls_headers($filename);

        ob_end_clean();
	flush();


        $writer->save('php://output');

        exit;


	}  


    public function custom_exports_cron_edit_page() {


	$id = isset($_GET['id']) ? $_GET['id'] : '';

	$report_cron = new Report();
	$crons = $report_cron->getCrons($id);

	$report_code = '';
	$report_interval = '';
	$report_emails = '';
	$report_hour = '';
	$report_minute = '';
	if ( !empty($id) && !empty($crons) ) {
		$report_code = $crons[0]->report_code;
		$report_interval = $crons[0]->report_interval;
		$report_emails = $crons[0]->report_emails;
		$report_hour = $crons[0]->report_hour;
		$report_minute = $crons[0]->report_minute;
	}


	$reports = $report_cron->getSettings('report');
	$intervals = $report_cron->getSettings('interval');
	$hours = $report_cron->getSettings('hour');
	$minutes = $report_cron->getSettings('minute');
	$frequency = $report_cron->getSettings('frequency');



	?>

	<div class="container">

		<form action="<?=admin_url('/admin.php')?>?page=woocommerce_il_reporting_save" method="post">

		<?php
			if ( !empty($id) ) {
		?>
		<input type="hidden" id="id" name="id" value="<?=$id?>">
		<?php
			}
		?>


		<div class="row mt-4">
			<div class="col-2">
				Report:
			</div>
			<div class="col-4">
				<select name="report_code" id="report_code" style="width: 100%">
					<?php
						foreach ($reports as $key => $report) {
					?>
					<option value="<?=$key?>" <?=($key==$report_code ? 'selected="selected"' : '')?> ><?=$report?></option>
					<?php
						}
					?>
				</select>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-2">
				Interval:
			</div>
			<div class="col-4">
				<select name="report_interval" id="report_interval" style="width: 100%">
					<?php
						foreach ($intervals as $key => $interval) {
					?>
					<option value="<?=$key?>" <?=($key==$report_interval ? 'selected="selected"' : '')?> ><?=$interval?></option>
					<?php
						}
					?>
				</select>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-2">
				Email:
			</div>
			<div class="col-10">
				<input type="text" name="report_emails" id="report_emails" style="width: 100%" value="<?=$report_emails?>">
				<p><i>Comma-separated list of email addresses</i></p>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-2">
				Frequency:
			</div>
			<div class="col-10">
				<select name="report_frequency" id="report_frequency" style="width: 100%">
					<?php
						foreach ($frequency as $key => $f) {
					?>
					<option value="<?=$key?>" <?=($key==$report_frequency? 'selected="selected"' : '')?> ><?=$f?></option>
					<?php
						}
					?>
				</select>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-2">
				Hour/Minute:
			</div>
			<div class="col-1">
				<select name="report_hour" id="report_hour" style="width: 100%">
					<?php
						foreach ($hours as $key => $hour) {
					?>
					<option value="<?=$key?>" <?=($key==$report_hour ? 'selected="selected"' : '')?> ><?=$hour?></option>
					<?php
						}
					?>
				</select>
			</div>
			<div class="col-1">
				<select name="report_minute" id="report_minute" style="width: 100%">
					<?php
						foreach ($minutes as $key => $minute) {
					?>
					<option value="<?=$key?>" <?=($key==$report_minute ? 'selected="selected"' : '')?> ><?=$minute?></option>
					<?php
						}
					?>
				</select>

			</div>
		</div>
		<div class="row mt-4">
			<div class="col-6 center">
				<button type="submit" value="Submit" class="btn btn-primary" style="width: 100px;">Save</button>
			</div>
		</div>

	</form>


	</div>

	<?php

    }



    public function woocommerce_il_reporting_save_page() {

	$data['id'] = isset($_POST['id']) ? $_POST['id'] : '';

	$data['report_code'] = isset($_POST['report_code']) ? $_POST['report_code'] : '';
	$data['report_interval'] = isset($_POST['report_interval']) ? $_POST['report_interval'] : '';
	$data['report_emails'] = isset($_POST['report_emails']) ? $_POST['report_emails'] : '';
	$data['report_hour'] = isset($_POST['report_hour']) ? $_POST['report_hour'] : '';
	$data['report_minute'] = isset($_POST['report_minute']) ? $_POST['report_minute'] : '';
	$data['report_frequency'] = isset($_POST['report_frequency']) ? $_POST['report_frequency'] : '';

	$error = '';

	$report_cron = new Report();
	$report_cron->saveCron($data, $error);

	wp_redirect( admin_url('/admin.php') . "?page=custom-exports-options&tab=settings" ); 


    }



    public function woocommerce_il_reporting_delete_page() {

	$id = isset($_GET['id']) ? $_GET['id'] : 0;

	$report_cron = new Report();
	$report_cron->delete($id);

	wp_redirect( admin_url('/admin.php') . "?page=custom-exports-options&tab=settings" ); 

    }



    public function idealliving_reports_cronjob() {

		ini_set('max_execution_time', 60*60);
		//ini_set('memory_limit','256M');

		global $wpdb;

		$report_cron = new Report();
		$reports = $report_cron->getSettings('report');

		// Create new daily tasks

		/*
		$pst = new DateTimeZone('America/Los_Angeles');
		$now = (new DateTime('now', $pst))->format('Y-m-d H:i:s'); 
		$today = (new DateTime('now', $pst))->format('Y-m-d'); 

		$sql = "select * from wp_custom_report_cron where ID not in (select cron_id from wp_custom_report) and report_scheduled >= '$today'";
		*/


		$project_no = get_option('options_legacy_scs_client_id');

		$path = dirname(__FILE__ ) . '/../';

		$today = date('Y-m-d'); 
		$sql = "select * from {$wpdb->prefix}custom_report_cron where ID not in (select cron_id from {$wpdb->prefix}custom_report where report_scheduled >= '$today 00:00:00')";


		$crons = $wpdb->get_results($sql);
		foreach ($crons as $cron){

			if ($cron->report_interval == 'yesterday') {
				/*
				$data1 = (new DateTime('-1 day', $pst))->format('Y-m-d'); 
				$data2 = (new DateTime('-1 day', $pst))->format('Y-m-d'); 
				*/
				$data1 = date('Y-m-d',strtotime("-1 days"));
				$data2 = date('Y-m-d',strtotime("-1 days"));
			} else if ($cron->report_interval == 'last_7_days') {
				/*
				$data1 = (new DateTime('-1 day', $pst))->format('Y-m-d'); 
				$data2 = (new DateTime('-7 day', $pst))->format('Y-m-d'); 
				*/
				$data1 = date('Y-m-d',strtotime("-1 days"));
				$data2 = date('Y-m-d',strtotime("-7 days"));
			} else if ($cron->report_interval == 'last_14_days') {
				/*
				$data1 = (new DateTime('-1 day', $pst))->format('Y-m-d'); 
				$data2 = (new DateTime('-14 day', $pst))->format('Y-m-d');
				*/
				$data1 = date('Y-m-d',strtotime("-1 days"));
				$data2 = date('Y-m-d',strtotime("-14 days"));
			} else if ($cron->report_interval == 'last_30_days') {
				/*
				$data1 = (new DateTime('-1 day', $pst))->format('Y-m-d'); 
				$data2 = (new DateTime('-30 day', $pst))->format('Y-m-d');
				*/
				$data1 = date('Y-m-d',strtotime("-1 days"));
				$data2 = date('Y-m-d',strtotime("-30 days"));
			} else if ($cron->report_interval == 'this_week') {
				$data1 = date("Y-m-d", strtotime('this week monday'));
				$data2 = date('Y-m-d');
			} else if ($cron->report_interval == 'last_week') {
				$data1 = date("Y-m-d", strtotime("last week monday"));
				$data2 = date("Y-m-d", strtotime("last week sunday"));
			} else if ($cron->report_interval == 'this_month') {
				$data1 = date("Y-m-d", strtotime("first day of this month"));
				$data2 = date('Y-m-d');
			} else if ($cron->report_interval == 'last_month') {
				$data1 = date("Y-m-d", strtotime("first day of previous month"));
				$data2 = date("Y-m-d", strtotime("last day of previous month"));
			} else if ($cron->report_interval == 'this_year') {
				$data1 = date("Y-m-d", strtotime("first day of this year"));
				$data2 = date('Y-m-d');
			} else if ($cron->report_interval == 'last_year') {
				$data1 = date("Y-m-d", strtotime("first day of previous year"));
				$data2 = date("Y-m-d", strtotime("last day of previous year"));
			}


			//$report_scheduled = (new DateTime($today . " " . $cron->report_hour . ":" . $cron->report_minute, $pst))->format('Y-m-d H:i:s'); 

			$cron_id = (int)$cron->ID;
			$report_code = $cron->report_code;
			$report_data1 = $data1;
			$report_data2 = $data2;
			$report_emails = $cron->report_emails;
			$report_scheduled = $today . ' ' . $cron->report_hour . ':' . $cron->report_minute;
			$report_priority = 1;

			if ( ($cron->report_frequency == 'daily') || ($cron->report_frequency == 'monthly' && date('d') == '01' ) || ($cron->report_frequency == 'annual' && date('m-d') == '01-01' ) ) {

				$sql = "INSERT INTO {$wpdb->prefix}custom_report (project, cron_id, report_code, report_data1, report_data2, report_emails, report_scheduled, report_priority, user_id) values ('$project_no', '$cron_id', '$report_code', '$report_data1', '$report_data2', '$report_emails', '$report_scheduled', '$report_priority', 0)";

				$wpdb->query($wpdb->prepare($sql));

			}

		}


		// checked blocked tasks
		$sql = "select * from wp_custom_report where report_complete = 0 and  report_start < '" . $date = date('Y-m-d H:i:s', strtotime('-2 hour')) . "'";

		$crons = $wpdb->get_results($sql);

		foreach ($crons as $cron) {

			error_log(date('F d Y g:i:s') . "  Recover at " . date('Y-m-d H:i:s') . " cron " . (int)$cron->ID . " started at " . $cron->report_start . " \n", 3, $path . 'log.txt');

			$sql = "UPDATE {$wpdb->prefix}custom_report set report_complete = -1 WHERE ID = " . (int)$cron->ID;
			$wpdb->query($wpdb->prepare($sql));

		}


		// wait if run report
		$run_reports = $wpdb->get_var("select count(*) from wp_custom_report where report_complete = 0");



		if ($run_reports == 0) {

			error_log(date('F d Y g:i:s') . "  start run reports \n", 3, $path . 'log.txt');

			$sql = "select * from {$wpdb->prefix}custom_report where report_complete = -1 and report_scheduled >= '$today 00:00:00' order by report_priority";

			$crons = $wpdb->get_results($sql);


			foreach ($crons as $cron) {


				$sql = "UPDATE {$wpdb->prefix}custom_report set report_complete = 0, report_start = '" . date('Y-m-d H:i:s') . "' WHERE ID = " . (int)$cron->ID;
				$wpdb->query($wpdb->prepare($sql));


				$data1 = substr($cron->report_data1, 0, 10);
				$data2 = substr($cron->report_data2, 0, 10);

				$filename = $cron->project . " " . $reports[$cron->report_code] . " $data1-$data2 (" . $cron->ID . ").xlsx";
				$filename = str_replace(" ", "-", $filename);

				try {

					$function = 'get_' . $cron->report_code;
					$result = ($function)($data1, $data2);

					$data = json_decode($result, true);


					$spreadsheet = new Spreadsheet();


					foreach ($data as $ey => $data1) {
           
						if ($spreadsheet->getSheetByName('Worksheet') == null) {
							$spreadsheet->createSheet();
						}
						$spreadsheet->setActiveSheetIndexByName('Worksheet');
						$sheet = $spreadsheet->getActiveSheet();            
						$sheet->setTitle($data1['array']['sheetname']);    

						$this->writeWorksheetArray($sheet, 3, $data1['array']);

							
					}

					$writer = new Xlsx($spreadsheet);

					$writer->save($path . 'public/download/'.$filename);


					$sql = "UPDATE {$wpdb->prefix}custom_report set report_complete = 1, report_end = '" . date('Y-m-d H:i:s') . "' WHERE ID = " . (int)$cron->ID;
					$wpdb->query($wpdb->prepare($sql));

					$to = $cron->report_email;
					$subject = "Report $filename";
					$message = "The report $filename is ready.";
					$headers = "";
					$attachments = $path . 'public/download/'.$filename;
					
					wp_mail( $to, $subject, $message, $headers, $attachments );

				} catch(Exception $e) {
					error_log(date('F d Y g:i:s') . " Error " . $e->getMessage() . " \n", 3,  $path . 'log.txt');
				}

			}


		}



    }


	public function run_rep_exp_orders_csv() {

		$project_no = get_option('options_legacy_scs_client_id');

		$data['project'] = $project_no;
		$data['report_code'] = isset($_GET['type']) ? $_GET['type'] : '';
		$data['report_code'] = str_replace( 'run_', '', $data['report_code'] );

		$data['report_data1'] = isset($_GET['data1']) ? $_GET['data1'] : '';
		$data['report_data2'] = isset($_GET['data2']) ? $_GET['data2'] : '';
		$data['report_scheduled'] = date("Y-m-d H:i:s");

		$current_user = wp_get_current_user();
		$data['report_emails'] = $current_user->user_email;
		$data['report_user_id'] = $current_user->ID;

		$error = '';

		$report_cron = new Report();
		$report_cron->saveRunCron($data, $error);

		wp_redirect( admin_url('/admin.php') . "?page=custom-exports-options&tab=download" ); 

        exit;
        
	}  


}
