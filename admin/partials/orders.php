<form id="get_orders_form">
  
  <input type="hidden" name="get_rep_orders" id="get_rep_orders" value="<?=wp_create_nonce('get_rep_orders')?>">
  <input type="hidden" name="get_rep_orders_csv" id="get_rep_orders_csv" value="<?=wp_create_nonce('get_rep_orders_csv')?>"> 

  <input type="hidden" name="data_start" id="data_start" value="">
  <input type="hidden" name="data_end" id="data_end" value="">
  <input type="hidden" name="pageno" id="pageno" value="1">
  <input type="hidden" name="pagetot" id="pagetot" value="1">

<div class="container report-container">
<div class="row">
      <div class="col-md-6">
        <p class="mb-0">Quick date selector</p>
          <div class="range border_light border_radius mw-100 reportrange-left" id="reportrange"><span></span></div>
      </div>
      <div class="col-md-6">	
        <p class="mb-1">Search string</p>
        <input type="text" class="mb-3 w-100 mw-100 form-control" name="req_search" id="req_search" value="">
      </div>	
  </div>
<div class="row">
      <div class="col-md-6">
        <p class="mb-1">Status</p>
      <select class="mb-3 w-100 mw-100 form-control" name="rec_status" id="rec_status">
                <option value="">all</option>
                <option value="completed">Completed</option>
                <option value="processing">Processing</option>
                <option value="pending">Pending payment</option>
                <option value="hold">On hold</option>
                <option value="cancelled">Cancelled</option>
                <option value="refunded">Refunded</option>
                <option value="failed">Failed</option>
                <option value="entered">Entered</option>
                <option value="shipped">Shipped</option>
                <option value="partially">Partially Paid</option>
                <option value="scheduled">Scheduled</option>
                <option value="deposit">Pending Deposit Payment</option>
           </select>
      </div>
      <div class="col-md-6">	
        <p class="mb-1">Payment</p>
      <select class="mb-3 w-100 mw-100 form-control" name="rec_payment" id="rec_payment">
                <option value="" selected="selected">all</option>
                <option value="card">Card</option>
                <option value="paypal">Paypal</option>
                <option value="affirm">Affirm</option>
           </select>
      </div>	
  </div>
<div class="row">
      <div class="col-md-6">	
        <p class="mb-1">Rows per page</p>
        <input type="text" class="mb-3 w-100 mw-100 form-control" name="rows" id="rows" value="100">
      </div>	
      <div class="col-md-6">
          <!--
        <p class="mb-1">Product</p>
          <input type="text" class="mb-3 w-100 mw-100 form-control" name="req_search" id="req_search" value="">
          -->
      </div>
  </div>
  <div class="row mb-2">
      <div class="col-md-2">
        <div class="report-form-check">
             <input type="checkbox" class="report-form-check-input" name="f_status" id="f_status" checked value="1">
             <label class="form-check-label" for="f_status">Status</label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="report-form-check">	
             <input type="checkbox" class="report-form-check-input" name="f_date" id="f_date" checked value="1">
             <label class="form-check-label" for="f_date">Date</label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="report-form-check">
             <input type="checkbox" class="report-form-check-input" name="f_value" id="f_value" checked value="1">
             <label class="form-check-label" for="f_value">Value</label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="report-form-check">	
             <input type="checkbox" class="report-form-check-input" name="f_affiliate" id="f_affiliate" value="1">
             <label class="form-check-label" for="f_affiliate">Affiliate</label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="report-form-check">
             <input type="checkbox" class="report-form-check-input" name="f_fname" id="f_fname" value="1">
             <label class="form-check-label" for="f_fname">First Name</label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="report-form-check">
             <input type="checkbox" class="report-form-check-input" name="f_lname" id="f_lname" value="1">
             <label class="form-check-label" for="f_lname">Last Name</label>
          </div>
      </div>
  </div>
  <div class="row mb-4">
      <div class="col-md-2">
        <div class="report-form-check">
             <input type="checkbox" class="report-form-check-input" name="f_phone" id="f_phone" value="1">
             <label class="form-check-label" for="f_phone">Phone</label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="report-form-check">	
             <input type="checkbox" class="report-form-check-input" name="f_email" id="f_email" value="1">
             <label class="form-check-label" for="f_email">Email</label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="report-form-check">
             <input type="checkbox" class="report-form-check-input" name="f_address" id="f_address" value="1">
             <label class="form-check-label" for="f_address">Address</label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="report-form-check">	
             <input type="checkbox" class="report-form-check-input" name="f_city" id="f_city" value="1">
             <label class="form-check-label" for="f_city">City</label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="report-form-check">
             <input type="checkbox" class="report-form-check-input" name="f_state" id="f_state" value="1">
             <label class="form-check-label" for="f_state">State</label>
          </div>
      </div>
      <div class="col-md-2">
        <div class="report-form-check">
             <input type="checkbox" class="report-form-check-input" name="f_zip" id="f_zip" value="1">
             <label class="form-check-label" for="f_zip">ZIP</label>
          </div>
      </div>
  </div>
  
  <div class="row mb-4">
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


