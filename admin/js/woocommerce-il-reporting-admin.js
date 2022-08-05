(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */





  // slider
    let methods = {
      init: function(options){

        this.btnPrev = this.find('.module_slider_prev');
        this.btnNext = this.find('.module_slider_next');

        let slides = this.find(options.slides);
        let children = this.find(options.children);
        let countSlieders = children.length/slides.length - 2;
        let left = parseInt(children.css('left'));


        let fixleft = {};
        fixleft[options.name] = left;

        let window_width = 0;
        let window_height = 0;


        let btnWrapper = this.find(options.btnWrapper);
        $(window).on('load resize',function(){

          console.log(" start on load resize ...");

          if($(window).width() != window_width || $(window).height() != window_height){

             window_width = $(window).width();
             window_height = $(window).height();

             left = parseInt(children.css('left'));

             fixleft[options.name] = left;

             console.log("options.name width: " + $(`#${options.name}`).width());
             console.log(options.name + " " + options.leftColumn);
             console.log("leftColumn: " + $(`#${options.name} ${options.leftColumn}`).width());
             let widthSLide = ($(`#${options.name}`).width()) - ($(`#${options.name} ${options.leftColumn}`).width())-30;
             console.log('widthSLide',widthSLide);
             if($(window).width()>options.maxWidht){
               $(children).removeAttr('style');
               $(children).css('position','static');
               $(slides).css('width','100%');
               btnWrapper.css('display','none');
             }else {
               $(children).css('position','relative');
               btnWrapper.css('display','flex');
             }

          }
          console.log(options.name + " end on load resize ...");

          // Fix for iOS 
          let slideElement = $(slides).find(children);
          slideElement.css('left',fixleft[options.name]);

        });



	// button next
        this.btnNext.on('click',function(){     
          let widthSLide = ($(`#${options.name}`).width()) - ($(options.leftColumn).width())-30;
          console.log("next button...");
          if((Math.round(fixleft[options.name], 2))<(Math.round(widthSLide*countSlieders*(-1),2))) return 
          fixleft[options.name]-=widthSLide;
          let slideElement = $(slides).find(children);
          slideElement.css('left',fixleft[options.name]);
        });
	$(".module_table_slider").on("swipeleft",function(e,data) {    
          if ($(this).parent().attr('id') == options.name) {
              let widthSLide = ($(`#${options.name}`).width()) - ($(options.leftColumn).width())-30;
              console.log("swipeleft...");
              if((Math.round(fixleft[options.name], 2))<(Math.round(widthSLide*countSlieders*(-1),2))) return 
              fixleft[options.name]-=widthSLide;
              let slideElement = $(slides).find(children);
              slideElement.css('left',fixleft[options.name]);
          }
	});


        // btn prev
        this.btnPrev.on('click',function(){
          if(fixleft[options.name]===0) return;
          let widthSLide = ($(`#${options.name}`).width()) - ($(options.leftColumn).width())-30;
          console.log("prev  button...");
          if((Math.round(fixleft[options.name], 2))>=0) return 
          fixleft[options.name]+=widthSLide;
          let slideElement = $(slides).find(children);     
          slideElement.css('left',fixleft[options.name]);
        });
	$(".module_table_slider").on("swiperight",function(e,data) {             
          if ($(this).parent().attr('id') == options.name) {
              if(fixleft[options.name]===0) return;
              let widthSLide = ($(`#${options.name}`).width()) - ($(options.leftColumn).width())-30;
              console.log("swiperight...");
              if((Math.round(fixleft[options.name], 2))>=0) return 
              fixleft[options.name]+=widthSLide;
              let slideElement = $(slides).find(children);     
              slideElement.css('left',fixleft[options.name]);
          }
	});


        return 
      }
    }
  $.fn.ModuleSLider = function(method) {
    return methods.init.apply( this, arguments );
  };














 function cb(start, end) {
     $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'), $("#websiteID").val());
     if ($('.nav-tab-active').text() == 'Breakeven') {
        callBreakEven(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), $("#get_breakeven").val());
     }
	 if ($('.nav-tab-active').text() == 'Dashboard') {
        callDashboardOrders(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), $("#get_dashboard").val());
     }
	 if ($('.nav-tab-active').text() == 'Reports') {
		var exp_orders = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_orders" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_orders").attr("href", exp_orders);   
		var exp_products = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_products" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_products").attr("href", exp_products);      

		var exp_fin_pay_details = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_pay_details" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_pay_details").attr("href", exp_fin_pay_details); 
		var exp_fin_pay_details_by_order = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_pay_details_by_order" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_pay_details_by_order").attr("href", exp_fin_pay_details_by_order); 

		var exp_fin_pay_uncollected = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_pay_uncollected" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_pay_uncollected").attr("href", exp_fin_pay_uncollected); 
		var exp_fin_pay_uncollected_by_order = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_pay_uncollected_by_order" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_pay_uncollected_by_order").attr("href", exp_fin_pay_uncollected_by_order); 

		var exp_fin_pay_summary = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_pay_summary" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_pay_summary").attr("href", exp_fin_pay_summary); 
		var exp_fin_refund_date = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_refund_date" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_refund_date").attr("href", exp_fin_refund_date); 
		var exp_fin_refund_type = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_refund_type" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_refund_type").attr("href", exp_fin_refund_type); 
		var exp_fin_cont_active = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_cont_active" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_cont_active").attr("href", exp_fin_cont_active); 
		var exp_fin_backorder = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_backorder" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_backorder").attr("href", exp_fin_backorder); 
		var exp_fin_count_analysis = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_count_analysis" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_count_analysis").attr("href", exp_fin_count_analysis); 
		var exp_fin_drtv_returns = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_drtv_returns" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_drtv_returns").attr("href", exp_fin_drtv_returns); 
		var exp_fin_drtv_inventory = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_drtv_inventory" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_drtv_inventory").attr("href", exp_fin_drtv_inventory); 
		var exp_fin_ilm_shipped = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_ilm_shipped" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_ilm_shipped").attr("href", exp_fin_ilm_shipped); 
		var exp_fin_inventory = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_inventory" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_inventory").attr("href", exp_fin_inventory); 
		var exp_fin_orders = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_orders" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_orders").attr("href", exp_fin_orders); 
		var exp_fin_pay_deposit = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_pay_deposit" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_pay_deposit").attr("href", exp_fin_pay_deposit); 
		var exp_fin_ship = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_ship" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_ship").attr("href", exp_fin_ship); 
		var exp_fin_tele = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_tele" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_tele").attr("href", exp_fin_tele); 
		var exp_fin_tax_summary = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_tax_summary" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_tax_summary").attr("href", exp_fin_tax_summary); 
		var exp_fin_tax_details = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_tax_details" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_tax_details").attr("href", exp_fin_tax_details); 
		var exp_fin_month_kit = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_month_kit" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_month_kit").attr("href", exp_fin_month_kit); 
		var exp_fin_orders_details = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_orders_details" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_orders_details").attr("href", exp_fin_orders_details); 
		var exp_fin_orders_details_by_order = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_orders_details_by_order" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_orders_details_by_order").attr("href", exp_fin_orders_details_by_order); 

		var exp_fin_orders_unshipped = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_orders_unshipped" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_orders_unshipped").attr("href", exp_fin_orders_unshipped); 
		var exp_fin_orders_unshipped_by_order = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_orders_unshipped_by_order" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_orders_unshipped_by_order").attr("href", exp_fin_orders_unshipped_by_order); 

		var exp_fin_reship_refund_summary = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_fin_reship_refund_summary" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_fin_reship_refund_summary").attr("href", exp_fin_reship_refund_summary); 

		var exp_call_center = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_call_center" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_call_center").attr("href", exp_call_center); 

		var exp_mojo_device = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_mojo_device" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_mojo_device").attr("href", exp_mojo_device); 
		var exp_mojo_weekly_metrics = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_mojo_weekly_metrics" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_mojo_weekly_metrics").attr("href", exp_mojo_weekly_metrics); 
		var exp_mojo_site_version = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_mojo_site_version" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_mojo_site_version").attr("href", exp_mojo_site_version); 
		var exp_mojo_sku_breakdown = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_mojo_sku_breakdown" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_mojo_sku_breakdown").attr("href", exp_mojo_sku_breakdown); 
		var exp_mojo_key_metrics = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_mojo_key_metrics" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_mojo_key_metrics").attr("href", exp_mojo_key_metrics); 
		var exp_mojo_sku_site = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_mojo_sku_site" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_mojo_sku_site").attr("href", exp_mojo_sku_site); 
		var exp_mojo_orders = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=exp_mojo_orders" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#exp_mojo_orders").attr("href", exp_mojo_orders); 



		var run_fin_orders_details = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_orders_details" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_orders_details").attr("href", run_fin_orders_details); 
		var run_fin_orders_details_by_order = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_orders_details_by_order" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_orders_details_by_order").attr("href", run_fin_orders_details_by_order); 

		var run_fin_pay_details = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_pay_details" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_pay_details").attr("href", run_fin_pay_details); 
		var run_fin_pay_details_by_order = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_pay_details_by_order" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_pay_details_by_order").attr("href", run_fin_pay_details_by_order); 
		var run_fin_pay_summary = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_pay_summary" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_pay_summary").attr("href", run_fin_pay_summary); 

		var run_fin_pay_uncollected = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_pay_uncollected" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_pay_uncollected").attr("href", run_fin_pay_uncollected); 
		var run_fin_pay_uncollected_by_order = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_pay_uncollected_by_order" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_pay_uncollected_by_order").attr("href", run_fin_pay_uncollected_by_order); 

		var run_fin_orders_unshipped = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_orders_unshipped" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_orders_unshipped").attr("href", run_fin_orders_unshipped); 
		var run_fin_orders_unshipped_by_order = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_orders_unshipped_by_order" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_orders_unshipped_by_order").attr("href", run_fin_orders_unshipped_by_order); 

		var run_fin_refund_date = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_refund_date" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_refund_date").attr("href", run_fin_refund_date); 
		var run_fin_refund_type = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_refund_type" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_refund_type").attr("href", run_fin_refund_type); 
		var run_fin_drtv_returns = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_drtv_returns" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_drtv_returns").attr("href", run_fin_drtv_returns); 
		var run_fin_reship_refund_summary = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_reship_refund_summary" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_reship_refund_summary").attr("href", run_fin_reship_refund_summary); 

		var run_fin_cont_active = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_cont_active" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_cont_active").attr("href", run_fin_cont_active); 
		var run_fin_count_analysis = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_count_analysis" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_count_analysis").attr("href", run_fin_count_analysis); 

		var run_fin_tax_details = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_tax_details" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_tax_details").attr("href", run_fin_tax_details); 
		var run_fin_tax_summary = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_tax_summary" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_tax_summary").attr("href", run_fin_tax_summary); 

		var run_fin_ilm_shipped = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_ilm_shipped" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_ilm_shipped").attr("href", run_fin_ilm_shipped); 
		var run_fin_ship = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_ship" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_ship").attr("href", run_fin_ship); 

		var run_fin_backorder = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_backorder" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_backorder").attr("href", run_fin_backorder); 
		var run_fin_tele = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_tele" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_tele").attr("href", run_fin_tele); 
		var run_fin_orders = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_fin_orders" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_fin_orders").attr("href", run_fin_orders); 

		var run_call_center = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_export_csv&type=run_call_center" + "&data1=" + start.format('YYYY-MM-DD') + "&data2=" + end.format('YYYY-MM-DD') + "&_wpnonce=" + $("#get_rep_export_csv").val();
		$("#run_call_center").attr("href", run_call_center); 

     }
     $("#data_start").val(start.format('YYYY-MM-DD'));
     $("#data_end").val(end.format('YYYY-MM-DD'));
 }






  $(window).load(function() {

	$('#table_slider').hide();



	var start = moment().subtract(29, 'days');
	var end = moment();

	var first_order = new Date(2018, 0, 1, 0, 0, 0, 0);

	var current_year = new Date().getFullYear();
	var year_2019_start = new Date(current_year, 0, 1, 0, 0, 0, 0);
	var year_2019_end = new Date(current_year, 11, 31, 23, 59, 59, 0);

	current_year = current_year - 1;
	var year_2018_start = new Date(current_year, 0, 1, 0, 0, 0, 0);
	var year_2018_end = new Date(current_year, 11, 31, 23, 59, 59, 0);




	$('#reportrange').daterangepicker({
		startDate: start,
		endDate: end,
		ranges: {
        		'Today': [moment(), moment()],
        		'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        		'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        		'Last 14 Days': [moment().subtract(13, 'days'), moment()],
        		'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        		'Last Week': [moment().subtract(1, 'week').startOf('isoWeek'), moment().subtract(1, 'week').endOf('isoWeek')],
        		'This Month': [moment().startOf('month'), moment().endOf('month')],
        		'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        		'This Year': [year_2019_start, year_2019_end],
        		'Last Year': [year_2018_start, year_2018_end],
        		'Lifetime': [first_order, moment()],
		},
		applyButtonClasses: 'btn btn_primary',
		cancelButtonClasses: 'btn btn_secondary'
	}, cb);

	cb(start, end);

 
	$("body").on('DOMSubtreeModified', ".drp-selected", function() {

		try {
			$('.start_date').text($('.drp-selected').text().match(/\d+\/+\d+\/+\d+/));
			$('.end_date').text($('.drp-selected').text().match(/ \d+\/+\d+\/+\d+/g));
		} catch (error) {

		};
 	});

	  
	if ($('.reportrange-left').length > 0) {
		$('.wrapps_calendar').css('left', '410px');
	}
	  
	  

	$('#show-affiliate-details').click(function(e) {
		e.preventDefault();

		console.log("show-affiliate-details click");

		if ($("#table_slider").is(":visible")) {
		 	$('#table_slider').hide();
		} else {
		 	$('#table_slider').show();

			let res = ($('#table_slider').width()) - ($('.module_table_slider .module_table_title').width())-30;
			console.log("res: " + res);
			$('#table_slider .module_table_slider .module_table_column').width(res);
			$('#table_slider .module_table_slider .module_table_row').width(res-12);
			$('#table_slider .module_table_slider .module_table_row').css('left',0);

			if($(window).width()>520){
				$(".module_table_row").removeAttr('style');
				$(".module_table_row").css('position','static');
				$(".module_table_column").css('width','100%');
			}

		}
	});


	$('#result_container').on('click', '.report-pages-links', function(event) {
		
		event.preventDefault();

		var scope = $(this).attr("data-overlay");
		var curent_page = parseInt($('#pageno').val());
		var total_pages = parseInt($('#pagetot').val());

		switch(scope) {
			case 'first':
				$('#pageno').val('1');
				break;
			case 'previous':
				curent_page = curent_page - 1;
				if (curent_page < 1) {
					curent_page = 1;
				}
				$('#pageno').val(curent_page);
				break;
			case 'next':
				curent_page = curent_page + 1;
				if (curent_page > total_pages ) {
					curent_page = total_pages;
				}
				$('#pageno').val(curent_page);
				break;
			case 'last':
					$('#pageno').val(total_pages );	  
				break;
			default:
		}

		$("#get_orders_form").submit();

	});


	$('#result_container').on('change', '#select_page', function(event) {

		event.preventDefault();

		var curent_page = $("select#select_page option").filter(":selected").val();
;
		$('#pageno').val(curent_page);	

		$("#get_orders_form").submit();

	});



   	$("#get_orders_form").on('submit', function(event) {

        event.preventDefault();
        
        var filters = "&req_search=" + encodeURIComponent($("#req_search").val()) + "&rec_status=" + $("#rec_status").val() + "&rec_payment=" + $("#rec_payment").val();

		var columns = "";
		if ($("#f_status").is(":checked")) {
        	columns = columns + "&f_status=1";
		}
		if ($("#f_date").is(":checked")) {
        	columns = columns + "&f_date=1";
		}
		if ($("#f_value").is(":checked")) {
        	columns = columns + "&f_value=1";
		}
		if ($("#f_affiliate").is(":checked")) {
        	columns = columns + "&f_affiliate=1";
		}
		if ($("#f_fname").is(":checked")) {
        	columns = columns + "&f_fname=1";
		}
		if ($("#f_lname").is(":checked")) {
        	columns = columns + "&f_lname=1";
		}	
		if ($("#f_phone").is(":checked")) {
        	columns = columns + "&f_phone=1";
		}
		if ($("#f_email").is(":checked")) {
        	columns = columns + "&f_email=1";
		}
		if ($("#f_address").is(":checked")) {
        	columns = columns + "&f_address=1";
		}
		if ($("#f_city").is(":checked")) {
        	columns = columns + "&f_city=1";
		}	
		if ($("#f_state").is(":checked")) {
        	columns = columns + "&f_state=1";
		}
		if ($("#f_zip").is(":checked")) {
        	columns = columns + "&f_zip=1";
		}																				 

        callARSOrders($("#data_start").val(), $("#data_end").val(), $("#get_rep_orders").val(), filters, columns, $("#pageno").val(), $("#rows").val());
    });



   	$("#get_products_form").on('submit', function(event) {

        event.preventDefault();
        
		var type = "";
		if ($("#f_type").is(":checked")) {
        	type = type + "sp";
		} else {
			type = type + "item";
		}

        callARSProducts($("#data_start").val(), $("#data_end").val(), $("#get_rep_products").val(), type);
    });



	$("#break_even_store").change(function() {
		if ($(this).is(':checked')) {
			$('#table_slider_store').show();	
		} else {
			$('#table_slider_store').hide();
			$("#break_even_all").prop('checked', false);
		}
   	});

   	$("#break_even_main").change(function() {
		if ($(this).is(':checked')) {
			$('#table_slider_main').show();	
		} else {
			$('#table_slider_main').hide();
			$("#break_even_all").prop('checked', false);
		}
   	});

   	$("#break_even_affiliates").change(function() {
		if ($(this).is(':checked')) {
			$('#table_slider_affiliates').show();	
		} else {
			$('#table_slider_affiliates').hide();
			$("#break_even_all").prop('checked', false);
		}
   	});

   	$("#break_even_all").change(function() {
		if ($(this).is(':checked')) {
			$('#table_slider_store').show();	
			$('#table_slider_main').show();
			$('#table_slider_affiliates').show();

			$("#break_even_store").prop('checked', true);
			$("#break_even_main").prop('checked', true);
			$("#break_even_affiliates").prop('checked', true);
		} else {
		}
   	});
 
  });




        
    

	function numberWithCommas(x) {
	    var parts = x.toString().split(".");
	    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	    return parts.join(".");
	}


  function callDashboardOrders(data1, data2, wpnonce) {

      var token = '';
      var url = "/wp-admin/admin.php?page=custom-exports-options&action=get_dashboard" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + wpnonce;
	//console.log(url);

      $.ajax({
      type: "GET",
      url: url,
      async: true,
      contentType: 'application/json; charset=utf-8',
      success: function(data){ 

			var dataArray = JSON.parse(data);

			$('#orders-store-count').html(dataArray['orders']['store']['count']);
			$('#orders-store-val').html("$" + numberWithCommas(parseFloat(dataArray['orders']['store']['value']).toFixed(2)) );

			$('#orders-main-count').html(dataArray['orders']['main']['count']);
			$('#orders-main-val').html("$" + numberWithCommas(parseFloat(dataArray['orders']['main']['value']).toFixed(2)) );

			$('#orders-affiliate-count').html(dataArray['orders']['affiliate']['count']);
			$('#orders-affiliate-val').html("$" + numberWithCommas(parseFloat(dataArray['orders']['affiliate']['value']).toFixed(2)) );

			//var totalOrders = parseInt(dataArray['orders']['store']['count']) + parseInt(dataArray['orders']['main']['count']) + parseInt(dataArray['orders']['affiliate']['count']);
			var totalOrders = parseInt(dataArray['orders']['store']['value']) + parseInt(dataArray['orders']['main']['value']) + parseInt(dataArray['orders']['affiliate']['value']);

			if (totalOrders > 0) {
				//var percentStore = parseInt(dataArray['orders']['store']['count']) / totalOrders;
				//var percentMain = parseInt(dataArray['orders']['main']['count']) / totalOrders;
				//var percentAffiliate = parseInt(dataArray['orders']['affiliate']['count']) / totalOrders;
				var percentStore = parseInt(dataArray['orders']['store']['value']) / totalOrders;
				var percentMain = parseInt(dataArray['orders']['main']['value']) / totalOrders;
				var percentAffiliate = parseInt(dataArray['orders']['affiliate']['value']) / totalOrders;
			} else {
				var percentStore = 0;
				var percentMain = 0;
				var percentAffiliate = 0;
			}

			$('.first').circleProgress({
			 startAngle: -Math.PI / 4*2,
			 value: percentStore,
			 size: 120,
			 fill: {
			   gradient: ["#1B3B71"]
			 }
			}).on('circle-animation-progress', function(event, progress) {
			 $(this).find('strong').html(Math.round(percentStore * 100 * progress) + '<i>%</i>');
			});


			$('.second').circleProgress({
			  startAngle: -Math.PI / 2,
			  value: percentMain,
			  size: 120,
			  fill: {
			    gradient: ["#1B3B71"]
			  }
			 }).on('circle-animation-progress', function(event, progress) {
			  $(this).find('strong').html(Math.round(percentMain * 100 * progress) + '<i>%</i>');
			 });


			 $('.third').circleProgress({
			  startAngle: -Math.PI / 4*2,
			  value: percentAffiliate,
			  size: 120,
			  fill: {
			    gradient: ["#1B3B71"]
			  }
			 }).on('circle-animation-progress', function(event, progress) {
			  $(this).find('strong').html(Math.round(percentAffiliate * 100 * progress) + '<i>%</i>');
			 });


			$('#affiliate-table-name').html('');
			$('#affiliate-table-orders').html('');
			$('#affiliate-table-value').html('');


			var affiliate_name_html   = '';
			var affiliate_orders_html = '';
			var affiliate_value_html  = '';

			var slider_no = 0;
			$.each(dataArray['affiliates'] , function (index, affiliate_data) {
				slider_no = slider_no + 1;
				if (affiliate_data['name'].length >= 30) {
					affiliate_name_html 	 = affiliate_name_html   + '<div class="module_table_row border_light text_left padding-left-10 f12" id="row_affiliate_name_'+slider_no+'">'  + affiliate_data['name']  + '</div>';
				} else if (affiliate_data['name'].length >= 25) {
					affiliate_name_html 	 = affiliate_name_html   + '<div class="module_table_row border_light text_left padding-left-10 f13" id="row_affiliate_name_'+slider_no+'">'  + affiliate_data['name']  + '</div>';
				} else if (affiliate_data['name'].length >= 20) {
					affiliate_name_html 	 = affiliate_name_html   + '<div class="module_table_row border_light text_left padding-left-10 f15" id="row_affiliate_name_'+slider_no+'">'  + affiliate_data['name']  + '</div>';
				} else {
					affiliate_name_html 	 = affiliate_name_html   + '<div class="module_table_row border_light text_left padding-left-10" id="row_affiliate_name_'+slider_no+'">'  + affiliate_data['name']  + '</div>';
				}
				affiliate_orders_html = affiliate_orders_html + '<div class="module_table_row border_light text_left_mobile text_center_desktop padding-left-10" id="row_affiliate_orders_'+slider_no+'">'  + affiliate_data['count'] + '</div>';
				affiliate_value_html  = affiliate_value_html  + '<div class="module_table_row border_light text_left_mobile text_center_desktop padding-left-10" id="row_affiliate_value_'+slider_no+'">$' + numberWithCommas(affiliate_data['value']) + '</div>';

			});

			$('#affiliate-table-name').html(affiliate_name_html);
			$('#affiliate-table-orders').html(affiliate_orders_html);
			$('#affiliate-table-value').html(affiliate_value_html);


			$('#table_slider').show();

			let res = ($('#table_slider').width()) - ($('.module_table_slider .module_table_title').width())-30;
			console.log("res: " + res);
			$('#table_slider .module_table_slider .module_table_column').width(res);
			$('#table_slider .module_table_slider .module_table_row').width(res-12);
			$('#table_slider .module_table_slider .module_table_row').css('left',0);

			if($(window).width()>520){
				$(".module_table_row").removeAttr('style');
				$(".module_table_row").css('position','static');
				$(".module_table_column").css('width','100%');
			}

			if($('#table_slider').length){
			console.log("$('#table_slider').ModuleSLider line 508");
			$('#table_slider').ModuleSLider({
			  name: 'table_slider',
			  slides: '.module_table_column',
			  children: '.module_table_row',
			  leftColumn:'.module_table_slider .module_table_title',
			  maxWidht: 520,
			  btnWrapper: '.module_btn_wrapper',
			  with: $(window).on('load resize',function(){
			    let res = ($('#table_slider').width()) - ($('.module_table_slider .module_table_title').width())-30;
			    $('#table_slider .module_table_slider .module_table_column').width(res);
			    $('#table_slider .module_table_slider .module_table_row').width(res-12);
			    $('#table_slider .module_table_slider .module_table_row').css('left',0);
			    return res;
			  })
			});
			};

			$('#table_slider').hide();



               }
      });



	var CSVurl = "/wp-admin/admin.php?page=custom-exports-options&action=dashboard_report" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + $("#dashboard_report").val();
	$("#download_csv").attr("href", CSVurl);

	var CSVAflurl = "/wp-admin/admin.php?page=custom-exports-options&action=dashboard_affiliates_report" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + $("#dashboard_affiliates_report").val();
	$("#download_afl_csv").attr("href", CSVAflurl);

  }








  function callBreakEven(data1, data2, wpnonce) {

      var token = '';
      var url = "/wp-admin/admin.php?page=custom-exports-options&action=get_breakeven" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + wpnonce;

      $.ajax({
      type: "GET",
      url: url,
      async: true,
      contentType: 'application/json; charset=utf-8',
      success: function(data){ 

			var dataArray = JSON.parse(data);

			var main_name_html 	 = "";
			var main_sku_html 	 = "";
			var main_qty_html 	 = "";
			var main_price_html 	 = "";
			var main_shipping_html 	 = "";
			var main_revenue_html 	 = "";
			var main_mobile_html 	 = "";

			var main_revenue_total = 0;
			$.each(dataArray["main"] , function (index, main_data) {
				var sku = main_data["name"];
				if (!sku.match("^X")) {
					main_name_html 	 = main_name_html     + '<div class="module_table_row border_light text_left p_15 text_nowrap">' + main_data["name"] + '</div>';
					main_sku_html 	 = main_sku_html      + '<div class="module_table_row border_light text_left p_15 text_nowrap">' + main_data["sku"] + '</div>';
					main_qty_html 	 = main_qty_html      + '<div class="module_table_row border_light text_left p_15 text_nowrap">' + parseFloat(main_data["qty"]).toFixed(0) + '</div>';
					main_price_html 	 = main_price_html    + '<div class="module_table_row border_light text_left p_15 text_nowrap">$' + parseFloat(main_data["price"]).toFixed(2) + '</div>';
					main_shipping_html 	 = main_shipping_html + '<div class="module_table_row border_light text_left p_15 text_nowrap">$' + parseFloat(main_data["shipping"]).toFixed(2) + '</div>';
					main_revenue_html 	 = main_revenue_html  + '<div class="module_table_row border_light text_left p_15 text_nowrap">$' + numberWithCommas(parseFloat(main_data["revenue"]).toFixed(2)) + '</div>';
					main_mobile_html	 = main_mobile_html   + '<div class="module_table_row border_light text_left p_15 text_nowrap darken_blue"> </div>';
					main_revenue_total    = main_revenue_total + parseFloat(main_data["revenue"]);
				}
			});

			main_name_html 	 = main_name_html    + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			main_sku_html 	 = main_sku_html     + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			main_qty_html 	 = main_qty_html     + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			main_price_html 	 = main_price_html   + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			main_shipping_html 	 = main_shipping_html + '<div class="module_table_row border_light text_left p_15 text_nowrap total_info darken_blue no_sm_blue">Total</div>';
			main_revenue_html 	 = main_revenue_html + '<div class="module_table_row border_light text_left p_15 text_nowrap total_price darken_blue no_sm_blue">$' +  numberWithCommas(parseFloat(main_revenue_total).toFixed(2)) + '</div>';
			main_mobile_html	 = main_mobile_html  + '<div class="module_table_row border_light text_left p_15 text_nowrap darken_blue" id="break_even_main_total">$' +  numberWithCommas(parseFloat(main_revenue_total).toFixed(2)) + '</div>';


			$('#break_even_main_name').html(main_name_html);
			$('#break_even_main_sku').html(main_sku_html);
			$('#break_even_main_qty').html(main_qty_html);
			$('#break_even_main_price').html(main_price_html);
			$('#break_even_main_shipping').html(main_shipping_html);
			$('#break_even_main_revenue').html(main_revenue_html);
			$('#break_main_mobile_section').html(main_mobile_html);


			var store_name_html 	 = "";
			var store_sku_html 	 = "";
			var store_qty_html 	 = "";
			var store_price_html 	 = "";
			var store_shipping_html 	 = "";
			var store_revenue_html 	 = "";
			var store_mobile_html 	 = "";

			var store_revenue_total = 0;
			$.each(dataArray["store"] , function (index, affiliate_data) {
				sku = affiliate_data["name"];
				if (!sku.match("^X")) {
					store_name_html 	 = store_name_html     + '<div class="module_table_row border_light text_left p_15 text_nowrap">' + affiliate_data["name"] + '</div>';
					store_sku_html 	 = store_sku_html      + '<div class="module_table_row border_light text_left p_15 text_nowrap">' + affiliate_data["sku"] + '</div>';
					store_qty_html 	 = store_qty_html      + '<div class="module_table_row border_light text_left p_15 text_nowrap">' + parseFloat(affiliate_data["qty"]).toFixed(0) + '</div>';
					store_price_html 	 = store_price_html    + '<div class="module_table_row border_light text_left p_15 text_nowrap">$' + parseFloat(affiliate_data["price"]).toFixed(2) + '</div>';
					store_shipping_html 	 = store_shipping_html + '<div class="module_table_row border_light text_left p_15 text_nowrap">$' + parseFloat(affiliate_data["shipping"]).toFixed(2) + '</div>';
					store_revenue_html 	 = store_revenue_html  + '<div class="module_table_row border_light text_left p_15 text_nowrap">$' + numberWithCommas(parseFloat(affiliate_data["revenue"]).toFixed(2)) + '</div>';
					store_mobile_html	 = store_mobile_html   + '<div class="module_table_row border_light text_left p_15 text_nowrap darken_blue"> </div>';
					store_revenue_total   = store_revenue_total + parseFloat(affiliate_data["revenue"]);
				}
			});

			store_name_html 	 = store_name_html    + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			store_sku_html 	 = store_sku_html     + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			store_qty_html 	 = store_qty_html     + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			store_price_html 	 = store_price_html   + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			store_shipping_html 	 = store_shipping_html + '<div class="module_table_row border_light text_left p_15 text_nowrap total_info darken_blue no_sm_blue">Total</div>';
			store_revenue_html 	 = store_revenue_html + '<div class="module_table_row border_light text_left p_15 text_nowrap total_price darken_blue no_sm_blue">$' + numberWithCommas(parseFloat(store_revenue_total).toFixed(2)) + '</div>';
			store_mobile_html	 = store_mobile_html  + '<div class="module_table_row border_light text_left p_15 text_nowrap darken_blue" id="break_even_store_total">$' +  numberWithCommas(parseFloat(store_revenue_total).toFixed(2)) + '</div>';

			$('#break_even_store_name').html(store_name_html);
			$('#break_even_store_sku').html(store_sku_html);
			$('#break_even_store_qty').html(store_qty_html);
			$('#break_even_store_price').html(store_price_html);
			$('#break_even_store_shipping').html(store_shipping_html);
			$('#break_even_store_revenue').html(store_revenue_html);
			$('#break_store_mobile_section').html(store_mobile_html);



			var affiliate_name_html 	 = "";
			var affiliate_sku_html 	 = "";
			var affiliate_qty_html 	 = "";
			var affiliate_price_html 	 = "";
			var affiliate_shipping_html 	 = "";
			var affiliate_revenue_html 	 = "";
			var affiliate_mobile_html 	 = "";

			var affiliate_revenue_total = 0;
			$.each(dataArray["affiliate"] , function (index, affiliate_data) {
				var sku = affiliate_data["name"];
				if (!sku.match("^X")) {
					affiliate_name_html 	 = affiliate_name_html     + '<div class="module_table_row border_light text_left p_15 text_nowrap">' + affiliate_data["name"] + '</div>';
					affiliate_sku_html 	 = affiliate_sku_html      + '<div class="module_table_row border_light text_left p_15 text_nowrap">' + affiliate_data["sku"] + '</div>';
					affiliate_qty_html 	 = affiliate_qty_html      + '<div class="module_table_row border_light text_left p_15 text_nowrap">' + parseFloat(affiliate_data["qty"]).toFixed(0) + '</div>';
					affiliate_price_html 	 = affiliate_price_html    + '<div class="module_table_row border_light text_left p_15 text_nowrap">$' + parseFloat(affiliate_data["price"]).toFixed(2) + '</div>';
					affiliate_shipping_html 	 = affiliate_shipping_html + '<div class="module_table_row border_light text_left p_15 text_nowrap">$' + parseFloat(affiliate_data["shipping"]).toFixed(2) + '</div>';
					affiliate_revenue_html 	 = affiliate_revenue_html  + '<div class="module_table_row border_light text_left p_15 text_nowrap">$' + numberWithCommas(parseFloat(affiliate_data["revenue"]).toFixed(2)) + '</div>';
					affiliate_mobile_html	 = affiliate_mobile_html   + '<div class="module_table_row border_light text_left p_15 text_nowrap darken_blue"> </div>';
					affiliate_revenue_total    = affiliate_revenue_total + parseFloat(affiliate_data["revenue"]);
				}
			});

			affiliate_name_html 	 = affiliate_name_html    + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			affiliate_sku_html 	 = affiliate_sku_html     + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			affiliate_qty_html 	 = affiliate_qty_html     + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			affiliate_price_html 	 = affiliate_price_html    + '<div class="module_table_row border_light text_left p_15 text_nowrap"> </div>';
			affiliate_shipping_html 	 = affiliate_shipping_html + '<div class="module_table_row border_light text_left p_15 text_nowrap total_info darken_blue no_sm_blue">Total</div>';
			affiliate_revenue_html 	 = affiliate_revenue_html  + '<div class="module_table_row border_light text_left p_15 text_nowrap total_price darken_blue no_sm_blue">$' +  numberWithCommas(parseFloat(affiliate_revenue_total).toFixed(2)) + '</div>';
			affiliate_mobile_html	 = affiliate_mobile_html   + '<div class="module_table_row border_light text_left p_15 text_nowrap darken_blue" id="break_even_affiliate_total">$' +  numberWithCommas(parseFloat(affiliate_revenue_total).toFixed(2)) + '</div>';

			$('#break_even_affiliate_name').html(affiliate_name_html);
			$('#break_even_affiliate_sku').html(affiliate_sku_html);
			$('#break_even_affiliate_qty').html(affiliate_qty_html);
			$('#break_even_affiliate_price').html(affiliate_price_html);
			$('#break_even_affiliate_shipping').html(affiliate_shipping_html);
			$('#break_even_affiliate_revenue').html(affiliate_revenue_html);
			$('#break_affiliate_mobile_section').html(affiliate_mobile_html);



			// table_slider_store

			let res = ($('#table_slider_store').width()) - ($('.module_table_slider .module_table_title').width())-30;

			$('#table_slider_store .module_table_slider .module_table_column').width(res);
			$('#table_slider_store .module_table_slider .module_table_row').width(res-32);
			$('#table_slider_store .module_table_slider .module_table_row').css('left',0);


			if($(window).width()>767){
				$(".module_table_row").removeAttr('style');
				$(".module_table_row").css('position','static');
				$(".module_table_column").css('width','100%');
			}

			if($('#table_slider_store').length){
			$('#table_slider_store').ModuleSLider({
			  name: 'table_slider_store',
			  slides: '.module_table_column',
			  children: '.module_table_row',
			  leftColumn:'.module_table_slider .module_table_title',
			  maxWidht: 767,
			  btnWrapper: '.module_btn_wrapper',
			  with: $(window).on('load resize',function(){
			    console.log('refrest store table...');
			    let res = ($('#table_slider_store').width()) - ($('.module_table_slider .module_table_title').width())-30;
			    $('#table_slider_store .module_table_slider .module_table_column').width(res);
			    $('#table_slider_store .module_table_slider .module_table_row').width(res-32);
			    $('#table_slider_store .module_table_slider .module_table_row').css('left',0);
			    return res;
			  })
			});
			};


			// table_slider_main

			$('#table_slider_main .module_table_slider .module_table_column').width(res);
			$('#table_slider_main .module_table_slider .module_table_row').width(res-32);
			$('#table_slider_main .module_table_slider .module_table_row').css('left',0);


			if($(window).width()>767){
				$(".module_table_row").removeAttr('style');
				$(".module_table_row").css('position','static');
				$(".module_table_column").css('width','100%');
			}

			if($('#table_slider_main').length){
			  $('#table_slider_main').ModuleSLider({
			    name: 'table_slider_main',
			    slides: '.module_table_column',
			    children: '.module_table_row',
			    leftColumn:'.module_table_slider .module_table_title',
			    maxWidht: 767,
			    btnWrapper: '.module_btn_wrapper',
			    with: $(window).on('load resize',function(){
			      console.log('refrest main table...');
			      let res = ($('#table_slider_main').width()) - ($('.module_table_slider .module_table_title').width())-30;
			      $('#table_slider_main .module_table_slider .module_table_column').width(res);
			      $('#table_slider_main .module_table_slider .module_table_row').width(res-32);
			      $('#table_slider_main .module_table_slider .module_table_row').css('left',0);
			      return res;
			    })
			  });
			};


			// table_slider_affiliates

			$('#table_slider_affiliates .module_table_slider .module_table_column').width(res);
			$('#table_slider_affiliates .module_table_slider .module_table_row').width(res-32);
			$('#table_slider_affiliates .module_table_slider .module_table_row').css('left',0);

			if($(window).width()>767){
				$(".module_table_row").removeAttr('style');
				$(".module_table_row").css('position','static');
				$(".module_table_column").css('width','100%');
			}

			if($('#table_slider_affiliates').length){
			  $('#table_slider_affiliates').ModuleSLider({
			    name: 'table_slider_affiliates',
			    slides: '.module_table_column',
			    children: '.module_table_row',
			    leftColumn:'.module_table_slider .module_table_title',
			    maxWidht: 767,
			    btnWrapper: '.module_btn_wrapper',
			    with: $(window).on('load resize',function(){
			      console.log('refrest affiliates table...');
			      let res = ($('#table_slider_affiliates').width()) - ($('.module_table_slider .module_table_title').width())-30;
			      $('#table_slider_affiliates .module_table_slider .module_table_column').width(res);
			      $('#table_slider_affiliates .module_table_slider .module_table_row').width(res-32);
			      $('#table_slider_affiliates .module_table_slider .module_table_row').css('left',0);
			      return res;
			    })
			  });
			}



               }
      });



	$(".download_link").prop("href", "/wp-admin/admin.php?page=custom-exports-options&action=breakeven_report" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + $("#breakeven_report").val());


  }




  function callARSOrders(data1, data2, wpnonce, filters = "", columns="", pageno=1, rows=100) {


      var token = '';
      var url = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_orders" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + wpnonce + filters + columns + '&pageno=' + pageno + '&rows=' + rows;
	  var url_csv = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_orders_csv" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + $('#get_rep_orders_csv').val() + filters + columns;

	  var header= [];
	  var line;
	  var table;

	  $('#result_container').empty();
	  $('#download_container').hide();
	  $('#result_container').html('<div class="row"><div class="col-md-12 center"><h1><b><span class="red">Please wait!</span></b></h1></div></div>');

      $.ajax({
      type: "GET",
      url: url,
      async: true,
      contentType: 'application/json; charset=utf-8',
      success: function(data){ 

			var dataArray = JSON.parse(data);

			var result_summary = '';
			var orders_count = 0;
			if (dataArray['summary'] !== undefined) {
				if (dataArray['summary']['count'] !== undefined) {
					orders_count = dataArray['summary']['count'];
					result_summary = result_summary + '<div class="row"><div class="col-6"><b>Sales found:</b></div><div class="col-6 right">' + dataArray['summary']['count'] + '</div></div>';
				}
				if (dataArray['summary']['total'] !== undefined) {
					if (dataArray['summary']['total'] === null) {
						dataArray['summary']['total'] = 0;
					}
					result_summary = result_summary + '<div class="row"><div class="col-6"><b>Total:</b></div><div class="col-6 right">' + parseFloat(dataArray['summary']['total']).toFixed(2) + '</div></div>';
				}
				if (dataArray['summary']['items'] !== undefined) {
					if (dataArray['summary']['items'] === null) {
						dataArray['summary']['items'] = 0;
					}
					result_summary = result_summary + '<div class="row"><div class="col-6"><b>Items:</b></div><div class="col-6 right">' + parseFloat(dataArray['summary']['items']).toFixed(2) + '</div></div>';
				}
				if (dataArray['summary']['shipping'] !== undefined) {
					if (dataArray['summary']['shipping'] === null) {
						dataArray['summary']['shipping'] = 0;
					}
					result_summary = result_summary + '<div class="row"><div class="col-6"><b>Shipping:</b></div><div class="col-6 right">' + parseFloat(dataArray['summary']['shipping']).toFixed(2) + '</div></div>';
				}				
			};

			var result_conditions = '';
			if (dataArray['conditions'] !== undefined) {
				$.each(dataArray['conditions'], function (index, condition) {
					var cond_array = condition.split(':');
					result_conditions = result_conditions + '<div class="row"><div class="col-6"><b>' + cond_array[0] + '</b></div><div class="col-6">' + cond_array[1] + '</div></div>';
				});
			};

			if (orders_count > 0) {
				$("#download_orders_link").attr("href", url_csv);
				$('#download_container').show();
			} else {
				$("#download_orders_link").attr("href", '#');
				$('#download_container').hide();
			}

			$('#result_container').empty();
			

			if (dataArray['orders'] !== undefined) {
				table = '';
				$.each(dataArray['orders'], function (index, order) {
					line = '<tr>';
					if (order['orderId'] !== undefined) {
						line = line + '<td>' + '<a href="/wp-admin/post.php?post=' + order['ID'] + '&action=edit" target="_blank">' + order['orderId'] + '</a>' + '</td>';
						header[0] = 'Order ID';  
					}
					if (order['status'] !== undefined) {
						line = line + '<td class="center">' + order['status'] + '</td>'; 
						header[1] = 'Status';  
					}					
					if (order['order_date'] !== undefined) {
						line = line + '<td class="center">' + order['order_date'] + '</td>'; 
						header[2] = 'Date';
					}	
					if (order['total'] !== undefined) {
						line = line + '<td class="right">' + parseFloat(order['total']).toFixed(2) + '</td>'; 
						header[3] = 'Value';
					}
					if (order['fname'] !== undefined) {
						line = line + '<td>' + order['fname'] + '</td>'; 
						header[4] = 'First Name';
					}
					if (order['lname'] !== undefined) {
						line = line + '<td>' + order['lname'] + '</td>'; 
						header[5] = 'Last Name';
					}
					if (order['phone'] !== undefined) {
						line = line + '<td>' + order['phone'] + '</td>'; 
						header[6] = 'Phone';
					}
					if (order['email'] !== undefined) {
						line = line + '<td>' + order['email'] + '</td>'; 
						header[7] = 'Email';
					}
					if (order['address'] !== undefined) {
						line = line + '<td>' + order['address'] + '</td>'; 
						header[8] = 'Address';
					}
					if (order['city'] !== undefined) {
						line = line + '<td>' + order['city'] + '</td>'; 
						header[9] = 'City';
					}	
					if (order['state'] !== undefined) {
						line = line + '<td>' + order['state'] + '</td>'; 
						header[10] = 'State';
					}
					if (order['zip'] !== undefined) {
						line = line + '<td>' + order['zip'] + '</td>'; 
						header[11] = 'ZIP';
					}																																											
					line = line + '</tr>';
					table = table + line;
				});


				line = '<tr>';
				$.each(header, function (index, h) {
					if (h !== undefined) {
						line = line + '<th>' + h + '</th>';
					}
				});
				line = line + '</tr>';


				// Pages

				var container_pages = '';

				var orders_per_page = $('#rows').val();
				var total_pages = Math.ceil(orders_count / orders_per_page);

				$('#pagetot').val(total_pages);

				var curent_page = $('#pageno').val();

				var prev_page = curent_page - 1;
				if (prev_page <=0) {
					prev_page = 1
				}

				var next_page = curent_page + 1;
				if (next_page >= total_pages) {
					next_page = total_pages;
				}
				
				var url_first = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_orders" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + wpnonce + filters + columns + '&pageno=' + '1' + '&rows=' + rows;
				var url_last  = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_orders" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + wpnonce + filters + columns + '&pageno=' + total_pages + '&rows=' + rows;

				var url_prev  = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_orders" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + wpnonce + filters + columns + '&pageno=' + prev_page + '&rows=' + rows;
				var url_next  = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_orders" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + wpnonce + filters + columns + '&pageno=' + next_page + '&rows=' + rows;

				var first    = '<div class="col-md-2 col-xl-2 center"><a href="' + url_first + '" data-overlay="first" class="report-pages-links"><< first page</a></div>';
				var previous = '<div class="col-md-2 col-xl-2 center"><a href="' + url_prev + '" data-overlay="previous" class="report-pages-links">< previous page</a></div>';
				var selector = '<div class="col-md-4 col-xl-2 center">Page <select name="select_page" data-overlay="select" id="select_page">';
				for (let i = 1; i <= total_pages; i++) {
					if (i == curent_page) {
						selector = selector + '<option value="' + i + '" selected >' + i + '</option>';
					} else {
						selector = selector + '<option value="' + i + '">' + i + '</option>';
					}
				}
				selector     = selector + '</select> of ' + total_pages + '</div>';
				var next     = '<div class="col-md-2 col-xl-2 center"><a href="' + url_next + '" data-overlay="next" class="report-pages-links">next page ></a></div>';
				var last     = '<div class="col-md-2 col-xl-2 center"><a href="' + url_last + '" data-overlay="last" class="report-pages-links">last page >></a></div>';
				
				container_pages = '<div class="row mt-2 mb-2 report-pages"><div class="col-md-0 col-xl-1"></div>' + first + previous + selector + next + last + '<div class="col-md-0 col-xl-1"></div></div>';

				table = '<div class="row mb-3"><div class="col-md-1"></div><div class="col-md-4">' + result_conditions + '</div><div class="col-md-2"></div><div class="col-md-4">' + result_summary + '</div><div class="col-md-1"></div></div>' + '<table class="report-orders" width="100%" align="center">' + line + table + '</table>';

				if (total_pages > 1) {
					table = table + container_pages;
				}

				$('#result_container').append(table);
			}



	  }


      });

  }



  function callARSProducts(data1, data2, wpnonce, type = "") {


      var token = '';
      var url = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_products" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + wpnonce + "&type=" + type;
	  var url_csv = "/wp-admin/admin.php?page=custom-exports-options&action=get_rep_products_csv" + "&data1=" + data1 + "&data2=" + data2 + "&_wpnonce=" + $('#get_rep_products_csv').val() + "&type=" + type;

	  var line;
	  var table;
	  var total = 0;
	  var products_count = 0;

	  $('#result_container').empty();
	  $('#download_container').hide();
	  $('#result_container').html('<div class="row"><div class="col-md-12 center"><h1><b><span class="red">Please wait!</span></b></h1></div></div>');

      $.ajax({
      type: "GET",
      url: url,
      async: true,
      contentType: 'application/json; charset=utf-8',
      success: function(data){ 

			var dataArray = JSON.parse(data);

			var result_summary = '';
			var orders_count = 0;

			$('#result_container').empty();
			

			if (dataArray['products'] !== undefined) {
				table = '';
				$.each(dataArray['products'], function (index, order) {

					products_count = products_count + 1;
					total = total + parseFloat(order['revenue']);

					line = '<tr>';
					//if (order['name'] !== undefined) {
					//	line = line + '<td>' + order['name'] + '</td>';
					//}
					if (order['sku'] !== undefined) {
						line = line + '<td>' + order['sku'] + '</td>';
					}					
					if (order['price'] !== undefined) {
						line = line + '<td class="right">' + parseFloat(order['price']).toFixed(2) + '</td>'; 
					}	
					if (order['qty'] !== undefined) {
						line = line + '<td class="center">' + parseInt(order['qty']) + '</td>'; 
					}
					if (order['revenue'] !== undefined) {
						line = line + '<td class="right">' + parseFloat(order['revenue']).toFixed(2) + '</td>'; 
					}
					line = line + '</tr>';
					table = table + line;
				});

				var header = '<tr>';
				//header = header + '<th>Product name</th>';
				header = header + '<th>SKU</th>';
				header = header + '<th>Price</th>';
				header = header + '<th>Units sold</th>';
				header = header + '<th>Revenue</th>';;
				header = header + '</tr>';

				var footer = '<tr>';
				footer = footer + '<th colspan="3" class="right">Total Revenue</th>';
				footer = footer + '<th class="right">' + total.toFixed(2) + '</th>';
				footer = footer + '</tr>';

				table = '<table class="report-orders" width="100%" align="center">' + header + table + footer + '</table>';			

				$('#result_container').append(table);



				if (products_count > 0) {
					$("#download_orders_link").attr("href", url_csv);
					$('#download_container').show();
				} else {
					$("#download_orders_link").attr("href", '#');
					$('#download_container').hide();
				}

			}



	  }


      });

  }





 






})( jQuery );


