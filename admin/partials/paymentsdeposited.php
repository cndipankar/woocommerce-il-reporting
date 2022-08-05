<?php

<h2>Payments Deposited by Process Date with Source and Script</h2>
<strong>NOTES/TODO:</strong>
<ul>
    <li>Change query to payments deposited... currently checking status for scheduled-payment, this should change once a deposit was made, not sure what it will be though.</li>
    <li>Does this need a date range, if so need to add to query, currently getting all...</li>
    <li>How is this sorted, can't really tell...</li>
    <li>Telemarketer is the order_source</li>
</ul>

<strong>If SKU added/chosen, edit query to get all by that specific SKU and the Date<br/>
We still need to determine how we want to use these filters sku/date range, as I do not believe this is on the initial query which is querying orders by the status.<br/>
</strong>

<form action="<?php echo admin_url( 'admin.php?page=custom-exports-options' ) ?>&action=download_payments_deposited&_wpnonce=<?php echo wp_create_nonce( 'download_payments_deposited' )?>" method="post" id="download_monthly_payments_csv" >
    <p>
    (optional) SKU returns results only from the selected SKU... <br/>
    This SKU option is not included in the wp cron emailed reports <br/>
    What SKU is this, is this SKU saved as order meta? Need to know if we can use this value as a meta query or if we will need to loop through each product, and check based on the offer code/sku maybe...
    </p>
    <select name="product_sku" id="product_sku">
        <option value="default">Select a SKU</option>
        <?php
        $args = array( 
            'post_type' => 'product',
            'posts_per_page' => -1
        );
        $skus = wc_get_products($args);
        foreach( $skus as $product ) {
            $sku = $product->get_sku();
            ?>
            <option value="<?php echo $sku;?>"><?php echo $sku;?></option>
            <?php
        }
        ?>
    </select>
    <p>From:</p>
    <input type="date" name="dateFrom" value="<?php echo date('Y-m-d', strtotime("first day of last month")); ?>" />
    <p>To:</p>
    <input type="date" name="dateTo" value="<?php echo date('Y-m-d', strtotime("last day of last month")); ?>" />
    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Export to CSV"></p>
</form>

<p>Inline Table Testing then use query for export</p>
<table class="widefat fixed">
    <thead>
        <tr>
            <?php
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
            foreach ($header_row as $th) {
                echo '<th>'.$th.'</th>';
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
            $query = new WC_Order_Query( array(
                'limit' => 5,
                'status' => 'scheduled-payment',
            ) );
            $orders = $query->get_orders();
            
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
                    echo '<tr>';
                        echo '<td>'.$deposit_date.'</td>';
                        echo '<td>'.$postcode.'</td>';
                        echo '<td>'.$state.'</td>';
                        echo '<td>'.$first_name.'</td>';
                        echo '<td>'.$last_name.'</td>';
                        echo '<td>'.$order_id.'</td>';
                        echo '<td>'.$transaction_id.'</td>';
                        echo '<td>'.$payment_method.'</td>';
                        echo '<td>'.$payment_type.'</td>';
                        echo '<td>Qty Ordered</td>';
                        echo '<td>$'.$total.'</td>';
                        echo '<td>BackEndCode</td>';
                        echo '<td>'.$order_source.'</td>';
                        echo '<td>Script</td>';
                        echo '<td>Promo Description</td>';
                        echo '<td>Installment #</td>';
                        echo '<td>Order Date</td>';
                        echo '<td>Doc</td>';
                        echo '<td>Doc ShipDate</td>';
                        echo '<td>Customer CreationDate</td>';
                    echo '</tr>';
                }
                
                // Ship Fee Line
                $shipping = $order->get_total_shipping();
                echo '<tr>';
                    echo '<td>'.$deposit_date.'</td>';
                    echo '<td>'.$postcode.'</td>';
                    echo '<td>'.$state.'</td>';
                    echo '<td>'.$first_name.'</td>';
                    echo '<td>'.$last_name.'</td>';
                    echo '<td>'.$order_id.'</td>';
                    echo '<td>'.$transaction_id.'</td>';
                    echo '<td>'.$payment_method.'</td>';
                    echo '<td>SHIPFEE</td>';
                    echo '<td>1</td>';
                    echo '<td>$'.$shipping.'</td>';
                    echo '<td>BackEndCode</td>';
                    echo '<td>'.$order_source.'</td>';
                    echo '<td>Script</td>';
                    echo '<td>Promo Description</td>';
                    echo '<td>Installment #</td>';
                    echo '<td>Order Date</td>';
                    echo '<td>Doc</td>';
                    echo '<td>Doc ShipDate</td>';
                    echo '<td>Customer CreationDate</td>';
                echo '</tr>';
               
                // Tax Fee Line
                $tax = $order->get_total_tax();
                echo '<tr>';
                    echo '<td>'.$deposit_date.'</td>';
                    echo '<td>'.$postcode.'</td>';
                    echo '<td>'.$state.'</td>';
                    echo '<td>'.$first_name.'</td>';
                    echo '<td>'.$last_name.'</td>';
                    echo '<td>'.$order_id.'</td>';
                    echo '<td>'.$transaction_id.'</td>';
                    echo '<td>'.$payment_method.'</td>';
                    echo '<td>TAX</td>';
                    echo '<td>1</td>';
                    echo '<td>$'.$tax.'</td>';
                    echo '<td>BackEndCode</td>';
                    echo '<td>'.$order_source.'</td>';
                    echo '<td>Script</td>';
                    echo '<td>Promo Description</td>';
                    echo '<td>Installment #</td>';
                    echo '<td>Order Date</td>';
                    echo '<td>Doc</td>';
                    echo '<td>Doc ShipDate</td>';
                    echo '<td>Customer CreationDate</td>';
                echo '</tr>';
            }
        ?>
    </tbody>
</table>


?>