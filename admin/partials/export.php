
  <form id="report_form">
  
  <input type="hidden" name="get_rep_export_csv" id="get_rep_export_csv" value="<?=wp_create_nonce('get_rep_export_csv')?>"> 

  <input type="hidden" name="data_start" id="data_start" value="">
  <input type="hidden" name="data_end" id="data_end" value="">
  <input type="hidden" name="report_type" id="report_type" value="">

  <input type="hidden" name="page" id="page" value="custom-exports-options">
  <input type="hidden" name="action" id="action" value="get_rep_export_csv">


<div class="container report-container">






    <div class="row">
        <div class="col-md-7">
            <p class="mb-0">Quick date selector</p>
            <div class="range border_light border_radius mw-100 reportrange-left" id="reportrange"><span></span></div>
        </div>
    </div>
  

    <div class="row mt-0 mb-3">
        <div class="col-md-12">
		<p style="color: red; font-weight: bold">Due to the limitations on the running time of a report, for a larger volume of data (more than a week), it is necessary to use the "Schedule Report" button..</p>
        </div>
    </div>    


    <div class="row mt-4">
        <div class="col-md-9 border-div-1">


	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Customer and Order Detail (Item)
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_orders_details"  class="btn btn-primary w-100 mw-100 exp_fin_orders_details" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_orders_details"  class="btn btn-primary w-100 mw-100 run_fin_orders_details" >Schedule Report</a>
	        </div>
	    </div>
	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Customer and Order Detail (Order)
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_orders_details_by_order"  class="btn btn-primary w-100 mw-100 exp_fin_orders_details_by_order" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_orders_details_by_order"  class="btn btn-primary w-100 mw-100 run_fin_orders_details_by_order" >Schedule Report</a>
	        </div>
	    </div>


        </div>
    </div>



    <div class="row mt-4">
        <div class="col-md-9 border-div-1">

	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            <!--Monthly Payments Deposited by Process Date with Source and Script Details-->
	            Payments Deposited (Item)
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_pay_details"  class="btn btn-primary w-100 mw-100 exp_fin_pay_details" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_pay_details"  class="btn btn-primary w-100 mw-100 run_fin_pay_details" >Schedule Report</a>
	        </div>
	    </div>  
	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            <!--Monthly Payments Deposited by Process Date with Source and Script Details-->
	            Payments Deposited (Order)
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_pay_details_by_order"  class="btn btn-primary w-100 mw-100 exp_fin_pay_details_by_order" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_pay_details_by_order"  class="btn btn-primary w-100 mw-100 run_fin_pay_details_by_order" >Schedule Report</a>
	        </div>
	    </div>  
	    <div class="row mt-2 mb-2" style="">
	        <div class="col-md-6">
	            <!--Monthly Payments Deposited by Process Date with Source and Script Summary -->
			Payments Deposited Summary
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_pay_summary"  class="btn btn-primary w-100 mw-100 exp_fin_pay_summary" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_pay_summary"  class="btn btn-primary w-100 mw-100 run_fin_pay_summary" >Schedule Report</a>
	        </div>
	    </div> 

        </div>
    </div>



    <div class="row mt-4">
        <div class="col-md-9 border-div-1">

	    <div class="row mt-2 mb-2">
	        <div class="col-md-6 style="">
	            Cash Uncollected (Item)
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_pay_uncollected"  class="btn btn-primary w-100 mw-100 exp_fin_pay_uncollected" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_pay_uncollected"  class="btn btn-primary w-100 mw-100 run_fin_pay_uncollected" >Schedule Report</a>
	        </div>
	    </div>  
	    <div class="row mt-2 mb-2">
	        <div class="col-md-6 style="">
	            Cash Uncollected (Order)
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_pay_uncollected_by_order"  class="btn btn-primary w-100 mw-100 exp_fin_pay_uncollected_by_order" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_pay_uncollected_by_order"  class="btn btn-primary w-100 mw-100 run_fin_pay_uncollected_by_order" >Schedule Report</a>
	        </div>
	   </div>  

        </div>
    </div>



    <div class="row mt-4">
        <div class="col-md-9 border-div-1">

	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Orders Not Shipped (Item)
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_orders_unshipped"  class="btn btn-primary w-100 mw-100 exp_fin_orders_unshipped" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_orders_unshipped"  class="btn btn-primary w-100 mw-100 run_fin_orders_unshipped" >Schedule Report</a>
	        </div>
	    </div>
	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Orders Not Shipped (Order)
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_orders_unshipped_by_order"  class="btn btn-primary w-100 mw-100 exp_fin_orders_unshipped_by_order" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_orders_unshipped_by_order"  class="btn btn-primary w-100 mw-100 run_fin_orders_unshipped_by_order" >Schedule Report</a>
	        </div>
	    </div>

        </div>
    </div>



    <div class="row mt-4">
        <div class="col-md-9 border-div-1">

	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Payments Refunded
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_refund_date"  class="btn btn-primary w-100 mw-100 exp_fin_refund_date" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_refund_date"  class="btn btn-primary w-100 mw-100 run_fin_refund_date" >Schedule Report</a>
	        </div>
	    </div>  
	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Payments Deposited & Refunded
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_refund_type"  class="btn btn-primary w-100 mw-100 exp_fin_refund_type" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_refund_type"  class="btn btn-primary w-100 mw-100 run_fin_refund_type" >Schedule Report</a>
	        </div>
	    </div>  
	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Daily Returns Report
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_drtv_returns"  class="btn btn-primary w-100 mw-100 exp_fin_drtv_returns" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_drtv_returns"  class="btn btn-primary w-100 mw-100 run_fin_drtv_returns" >Schedule Report</a>
	        </div>
	    </div> 
	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Reship and Refund Summary by Inventory SKU and Ship Week
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_reship_refund_summary"  class="btn btn-primary w-100 mw-100 exp_fin_orders_details" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_reship_refund_summary"  class="btn btn-primary w-100 mw-100 run_fin_orders_details" >Schedule Report</a>
	        </div>
	    </div>


        </div>
    </div>



    <div class="row mt-4">
        <div class="col-md-9 border-div-1">

	    <div class="row mt-2 mb-2" style="">
	        <div class="col-md-6">
	            Active Subscription Count by Start Date
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_cont_active"  class="btn btn-primary w-100 mw-100 exp_fin_cont_active" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_cont_active"  class="btn btn-primary w-100 mw-100 run_fin_cont_active" >Schedule Report</a>
	        </div>
	    </div>  
	    <div class="row mt-2 mb-2" style="">
	        <div class="col-md-6">
	            Subscription Analysis with Stick Rate By Month by Script
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_count_analysis"  class="btn btn-primary w-100 mw-100 exp_fin_count_analysis" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_count_analysis"  class="btn btn-primary w-100 mw-100 run_fin_count_analysis" >Schedule Report</a>
	        </div>
	    </div> 

        </div>
    </div>



    <div class="row mt-4">
        <div class="col-md-9 border-div-1">

	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Sales Tax Detail Report
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_tax_details"  class="btn btn-primary w-100 mw-100 exp_fin_tax_details" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_tax_details"  class="btn btn-primary w-100 mw-100 run_fin_tax_details" >Schedule Report</a>
	        </div>
	    </div> 
	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Sales Tax Summary Report
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_tax_summary"  class="btn btn-primary w-100 mw-100 exp_fin_tax_summary" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_tax_summary"  class="btn btn-primary w-100 mw-100 run_fin_tax_summary" >Schedule Report</a>
	        </div>
	    </div> 

        </div>
    </div>




    <div class="row mt-4">
        <div class="col-md-9 border-div-1">

	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Order Shipped by Ship Date
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_ilm_shipped"  class="btn btn-primary w-100 mw-100 exp_fin_ilm_shipped" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_ilm_shipped"  class="btn btn-primary w-100 mw-100 run_fin_ilm_shipped" >Schedule Report</a>
	        </div>
	    </div>
	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Shipped Component Summary Report
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_ship"  class="btn btn-primary w-100 mw-100 exp_fin_ship" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_ship"  class="btn btn-primary w-100 mw-100 run_fin_ship" >Schedule Report</a>
	        </div>
	    </div>

        </div>
    </div>





    <div class="row mt-4">
        <div class="col-md-9 border-div-1">

	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Back Orders By SKU
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_backorder"  class="btn btn-primary w-100 mw-100 exp_fin_backorder" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_backorder"  class="btn btn-primary w-100 mw-100 run_fin_backorder" >Schedule Report</a>
	        </div>
	    </div> 
	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Order Details by Date, Promo and Media
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_tele"  class="btn btn-primary w-100 mw-100 exp_fin_tele" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_tele"  class="btn btn-primary w-100 mw-100 run_fin_tele" >Schedule Report</a>
	        </div>
	    </div>
	   <div class="row mt-2 mb-2">
	        <div class="col-md-6">
	            Order Events Over Time Based on Order Date with Telemarketer
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_fin_orders"  class="btn btn-primary w-100 mw-100 exp_fin_orders" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_fin_orders"  class="btn btn-primary w-100 mw-100 run_fin_orders" >Schedule Report</a>
	        </div>
	    </div>

        </div>
    </div>



    <div class="row mt-4">
        <div class="col-md-9 border-div-1">

	    <div class="row mt-2 mb-2">
	        <div class="col-md-6" style="">
	            Call Center Comments
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="exp_call_center"  class="btn btn-primary w-100 mw-100 exp_call_center" >Run Report</a>
	        </div>
	        <div class="col-md-3">
	            <a href="#" id="run_call_center"  class="btn btn-primary w-100 mw-100 run_call_center" >Schedule Report</a>
	        </div>
	    </div> 

        </div>
    </div>




<!-- 
    <div class="row mt-2 mb-2">
        <div class="col-md-7" style="color: red;">
            DRTV Monthly Inventory Adjustment Report
        </div>
        <div class="col-md-2">
            <a href="#" id="exp_fin_drtv_inventory"  class="btn btn-primary w-100 mw-100 exp_fin_drtv_inventory" >Download</a>
        </div>
    </div>
-->

<!--
    <div class="row mt-2 mb-2">
        <div class="col-md-7" style="color: red;">
            Inventory Balance Report
        </div>
        <div class="col-md-2">
            <a href="#" id="exp_fin_inventory"  class="btn btn-primary w-100 mw-100 exp_fin_inventory" >Download</a>
        </div>
    </div>
-->





    <div class="row mt-3 mb-3">
        <div class="col-md-7">
            <b>Site Version Reports</b>
        </div>
    </div>    
    <div class="row mt-2 mb-2">
        <div class="col-md-7">
            Device Report
        </div>
        <div class="col-md-2">
            <a href="#" id="exp_mojo_device"  class="btn btn-primary w-100 mw-100 exp_mojo_device" >Download</a>
        </div>
    </div>   
<!--
    <div class="row mt-2 mb-2">
        <div class="col-md-7">
            Weekly Web Metrics Summary
        </div>
        <div class="col-md-2">
            <a href="#" id="exp_mojo_weekly_metrics"  class="btn btn-primary w-100 mw-100 exp_mojo_weekly_metrics" >Download</a>
        </div>
    </div> 
-->
    <div class="row mt-2 mb-2">
        <div class="col-md-7">
            Site Version Summary 
        </div>
        <div class="col-md-2">
            <a href="#" id="exp_mojo_site_version"  class="btn btn-primary w-100 mw-100 exp_mojo_site_version" >Download</a>
        </div>
    </div> 
    <div class="row mt-2 mb-2">
        <div class="col-md-7">
            SKU Breakdown
        </div>
        <div class="col-md-2">
            <a href="#" id="exp_mojo_sku_breakdown"  class="btn btn-primary w-100 mw-100 exp_mojo_sku_breakdown" >Download</a>
        </div>
    </div> 
    <div class="row mt-2 mb-2">
        <div class="col-md-7">
            Key Metrics
        </div>
        <div class="col-md-2">
            <a href="#" id="exp_mojo_key_metrics"  class="btn btn-primary w-100 mw-100 exp_mojo_key_metrics" >Download</a>
        </div>
    </div>         
    <div class="row mt-2 mb-2">
        <div class="col-md-7">
            SKU by site version
        </div>
        <div class="col-md-2">
            <a href="#" id="exp_mojo_sku_site"  class="btn btn-primary w-100 mw-100 exp_mojo_sku_site" >Download</a>
        </div>
    </div>   
<!-- 
    <div class="row mt-2 mb-2">
        <div class="col-md-7">
            Orders Report
        </div>
        <div class="col-md-2">
            <a href="#" id="exp_mojo_orders"  class="btn btn-primary w-100 mw-100 exp_mojo_orders" >Download</a>
        </div>
    </div>  
-->
</div>

</form>




