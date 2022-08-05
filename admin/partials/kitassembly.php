<?php

<h2>Kit & Assembly</h2>
<form action="<?php echo admin_url( 'admin.php?page=custom-exports-options' ) ?>&action=download_kitassembly&_wpnonce=<?php echo wp_create_nonce( 'download_kitassembly' )?>" method="post" id="download_kitassembly" >
    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Export to CSV"></p>
</form>
<table class="widefat fixed">
    <thead>
        <tr class="alternate">
            <?php
            $header_row = array(
                'SkuInKit',
                'KitSkuDescription',
                'KitSku',
                'SkuDescription',
                'QtyInKit',
            );
            foreach ($header_row as $th) {
                echo '<th>'.$th.'</th>';
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
            $args = array(
                'post_type' => 'product',
            );
            $products = wc_get_products( $args );

            foreach( $products as $product ) {
                setup_postdata($product);
                $offers = get_field('offers', $product->get_id());
                if( $offers ){
                    $offer_count = 1;
                    foreach( $offers as $offer ){
                        setup_postdata($offer);
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
                        $row_class = ''; 
                        if($offer_count % 2 == 0){ 
                            $row_class = "alternate";  
                        }
                        echo '<tr class="'.$row_class.'">';
                            echo '<td>'.$sku_in_kit.'</td>';
                            echo '<td>'.$kit_sku_description.'</td>';
                            echo '<td>'.$kit_sku.'</td>';
                            echo '<td>'.$sku_description.'</td>';
                            echo '<td>TODO Need in Offer?</td>';
                        echo '</tr>';
                        $offer_count++;
                    }
                    wp_reset_postdata();                                        
                }
            }
            wp_reset_postdata();  
        ?>
    </tbody>
</table>


?>