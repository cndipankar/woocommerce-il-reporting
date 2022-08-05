<form id="get_products_form">
  
  <input type="hidden" name="get_rep_products" id="get_rep_products" value="<?=wp_create_nonce('get_rep_products')?>">
  <input type="hidden" name="get_rep_products_csv" id="get_rep_products_csv" value="<?=wp_create_nonce('get_rep_products_csv')?>"> 

  <input type="hidden" name="data_start" id="data_start" value="">
  <input type="hidden" name="data_end" id="data_end" value="">


<div class="container report-container">
<div class="row">
      <div class="col-md-6">
        <p class="mb-0">Quick date selector</p>
          <div class="range border_light border_radius mw-100 reportrange-left" id="reportrange"><span></span></div>
      </div>
      <div class="col-md-6">	
        <div class="report-form-check">
             <p class="mb-0">&nbsp;</p>
             <input type="checkbox" class="report-form-check-input" name="f_type" id="f_type" checked value="1">
             <label class="form-check-label" for="f_type">Include S&P in price</label>
        </div>
      </div>	
</div>
  
  <div class="row mb-4 mt-4">
      <div class="col-md-4">
       </div>
      <div class="col-md-4">
          <input type="submit" value="Search" class="btn btn-primary w-100 mw-100" id="get_orders">
       </div>
      <div class="col-md-4">
       </div>
  </div>  
      
</div>

</form>

<div class="container report-container">
  <div id="result_container">
  </div>
  <div id="download_container" class=" mt-4">
      <div class="row">
          <div class="col-md-12 center">
              <a class="download_orders_link" href="#" data-ajax="false" id="download_orders_link"><img src='<?=plugin_dir_url( dirname( __FILE__ ) )?>/../images/icon-download-small.png' alt="download" /></a>
          </div>
      </div>
  </div>
</div>


