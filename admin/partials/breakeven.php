<input type="hidden" name="get_breakeven" id="get_breakeven" value="<?=wp_create_nonce('get_breakeven')?>">
    <input type="hidden" name="breakeven_report" id="breakeven_report" value="<?=wp_create_nonce('breakeven_report')?>">

    <input type="hidden" name="data_start" id="data_start" value="">
    <input type="hidden" name="data_end" id="data_end" value="">

    <div class="main_wrapper">
            <div class="module_1280">
                <div class="sub_header">
                    <p class="sub_header_title"></p>
                </div>
                <div class="modules_line d_flex flex_md_column justify_end">
                    <div class="module_406 m_15 m_sm_auto transparent_bg">
                        <form class="form d_flex justify_between wrap_sm transparent_bg" action="">
                            <div class="form_chek_wrapper"><input class="form_check" type="checkbox" id="break_even_store" value="1" checked data-role="none" /><label class="form_label_check" for="break_even_store">Store</label></div>
                            <div class="form_chek_wrapper"><input class="form_check" type="checkbox" id="break_even_main" value="1" checked data-role="none" /><label class="form_label_check" for="break_even_main">Main</label></div>
                            <div class="form_chek_wrapper"><input class="form_check" type="checkbox" id="break_even_affiliates" value="1" checked data-role="none" /><label class="form_label_check" for="break_even_affiliates">Affiliates</label></div>
                            <div class="form_chek_wrapper"><input class="form_check" type="checkbox" id="break_even_all" value="1" checked data-role="none" /><label class="form_label_check" for="break_even_all">All</label></div>
                        </form>
                    </div>
                    <div class="module_406 m_15 m_sm_auto center">
				<a class="download_link" href="#" data-ajax="false"><img src='<?=plugin_dir_url( dirname( __FILE__ ) )?>/../images/icon-download-small.png' alt="download" /></a>
                    </div>
                    <div class="module_406 pos_relative m_15 m_sm_auto">
                        <div class="range border_light border_radius" id="reportrange"><span></span></div>
                    </div>
                </div>
                <div class="modules_line d_flex flex_md_column block_margin">
                    <div class="module_1280 m_15 m_sm_auto p_md_15" id="table_slider_store">
                        <p class="module_title">Store</p>
                        <div class="module_table_slider module_table_slider2 d_flex border_radius border_light pos_relative module_big_table">
                            <div class="module_table_block total_product_name">
                                <div class="module_table_title border_light">Product name</div>
                                <div class="module_table_column" id="break_even_store_name">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Offer Code</div>
                                <div class="module_table_column" id="break_even_store_sku">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Units Sold</div>
                                <div class="module_table_column" id="break_even_store_qty">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Price</div>
                                <div class="module_table_column" id="break_even_store_price">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Shipping</div>
                                <div class="module_table_column" id="break_even_store_shipping">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Revenue</div>
                                <div class="module_table_column" id="break_even_store_revenue">
                                </div>
                            </div>
                            <div class="module_table_block total_equal module_table_block_total">
                                <div class="module_table_title border_light darken_blue">Total</div>
                                <div class="module_table_column" id="break_store_mobile_section">
                                </div>
                            </div>
                            <div class="module_btn_wrapper d_flex justify_between"><button class="module_slider_prev">prev</button><button class="module_slider_next">next</button></div>
                        </div>
                    </div>
                </div>
                <div class="modules_line d_flex flex_md_column block_margin">
                    <div class="module_1280 m_15 m_sm_auto p_md_15" id="table_slider_main">
                        <p class="module_title">Main</p>
                        <div class="module_table_slider module_table_slider3 d_flex border_radius border_light pos_relative module_big_table">
                            <div class="module_table_block total_product_name">
                                <div class="module_table_title border_light">Product name</div>
                                <div class="module_table_column" id="break_even_main_name">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Offer Code</div>
                                <div class="module_table_column" id="break_even_main_sku">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Units Sold</div>
                                <div class="module_table_column" id="break_even_main_qty">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Price</div>
                                <div class="module_table_column" id="break_even_main_price">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Shipping</div>
                                <div class="module_table_column" id="break_even_main_shipping">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Revenue</div>
                                <div class="module_table_column" id="break_even_main_revenue">
                                </div>
                            </div>
                            <div class="module_table_block total_equal module_table_block_total">
                                <div class="module_table_title border_light darken_blue">Total</div>
                                <div class="module_table_column" id="break_main_mobile_section">
                                </div>
                            </div>
                            <div class="module_btn_wrapper d_flex justify_between"><button class="module_slider_prev">prev</button><button class="module_slider_next">next</button></div>
                        </div>
                    </div>
                </div>
                <div class="modules_line d_flex flex_md_column block_margin">
                    <div class="module_1280 m_15 m_sm_auto p_md_15" id="table_slider_affiliates">
                        <p class="module_title">Affiliates</p>
                        <div class="module_table_slider module_table_slider4 d_flex border_radius border_light pos_relative module_big_table">
                            <div class="module_table_block total_product_name">
                                <div class="module_table_title border_light">Product name</div>
                                <div class="module_table_column" id="break_even_affiliate_name">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Offer Code</div>
                                <div class="module_table_column" id="break_even_affiliate_sku">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Units Sold</div>
                                <div class="module_table_column" id="break_even_affiliate_qty">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Price</div>
                                <div class="module_table_column" id="break_even_affiliate_price">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Shipping</div>
                                <div class="module_table_column" id="break_even_affiliate_shipping">
                                </div>
                            </div>
                            <div class="module_table_block total_equal">
                                <div class="module_table_title border_light">Revenue</div>
                                <div class="module_table_column" id="break_even_affiliate_revenue">
                                </div>
                            </div>
                            <div class="module_table_block total_equal module_table_block_total">
                                <div class="module_table_title border_light darken_blue">Total</div>
                                <div class="module_table_column" id="break_affiliate_mobile_section">
                                </div>
                            </div>
                            <div class="module_btn_wrapper d_flex justify_between"><button class="module_slider_prev">prev</button><button class="module_slider_next">next</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


