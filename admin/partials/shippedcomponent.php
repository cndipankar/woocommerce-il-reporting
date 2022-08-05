<?php

<h2>Shipped Component</h2>
<p>Inline Table Testing then use query for export</p>
<table class="widefat fixed">
    <thead>
        <tr>
            <?php
            $header_row = array(
                'Component SKU',
                'Master SKU',
                'Qty',
                'Ship Date',
                'Order No',
                'Document',
                'Qty Shipped',
                'Status',
                'Component Qty',
                'Order Date',
                'Payment Plan'
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
                'limit' => 200,
                'meta_key' => 'order_source',
            ) );
            $orders = $query->get_orders();
            
            foreach( $orders as $order ) {
                $master_sku = $order->sku;
                $qty = $order->quantity;
                $date_created = $order->date_created;
                
                echo '<tr>';
                    echo '<td>Component SKU</td>';
                    echo '<td>'.$master_sku.'</td>';
                    echo '<td>'.$qty.'</td>';
                    echo '<td>Ship Date</td>';
                    echo '<td>Order No</td>';
                    echo '<td>Document</td>';
                    echo '<td>Qty Shipped</td>';
                    echo '<td>Status</td>';
                    echo '<td>Component Qty</td>';
                    echo '<td>'.$date_created.'</td>';
                    echo '<td>Payment Plan</td>';
                echo '</tr>';
            }
        ?>
    </tbody>
</table>


?>