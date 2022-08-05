<input type="hidden" name="get_dashboard" id="get_dashboard" value="<?=wp_create_nonce('get_dashboard')?>">
    <input type="hidden" name="dashboard_report" id="dashboard_report" value="<?=wp_create_nonce('dashboard_report')?>">
    <input type="hidden" name="dashboard_affiliates_report" id="dashboard_affiliates_report" value="<?=wp_create_nonce('dashboard_affiliates_report')?>">

    <input type="hidden" name="data_start" id="data_start" value="">
    <input type="hidden" name="data_end" id="data_end" value="">

    <div class="main_wrapper">
            <div class="module_1280">
                <div class="sub_header">
                    <p class="sub_header_title"></p>
                </div>
                <div class="modules_line d_flex flex_md_column justify_end">
                    <div class="module_406 m_15 m_sm_auto"></div>
                    <div class="module_406 m_15 m_sm_auto center">
				<a class="download_link" href="#" data-ajax="false" id="download_csv"><img src='<?=plugin_dir_url( dirname( __FILE__ ) )?>/../images/icon-download-small.png' alt="download" /></a>
                    </div>
                    <div class="module_406 pos_relative m_15 m_sm_auto">
                        <div class="range border_light border_radius" id="reportrange"><span></span></div>
                    </div>
                </div>
                <div class="modules_line d_flex flex_md_column justify_between block_margin">
                    <div class="module_406 m_15 m_sm_auto">
                        <p class="module_title">Store<div class="module_table d_flex border_radius border_light">
                                <div class="module_table_block total_orders">
                                    <div class="module_table_title text_center border_light">Total Orders</div>
                                    <div class="module_table_column">
                                        <div class="module_table_row text_center padding-left-10" id="orders-store-count">0</div>
                                    </div>
                                </div>
                                <div class="module_table_block total_sales">
                                    <div class="module_table_title text_center border_light">Total Sales</div>
                                    <div class="module_table_column">
                                        <div class="module_table_row text_center padding-left-10" id="orders-store-val">$0.00</div>
                                    </div>
                                </div>
                            </div>
                        </p>
                    </div>
                    <div class="module_406 m_15 m_sm_auto">
                        <p class="module_title">Main<div class="module_table d_flex border_radius border_light">
                                <div class="module_table_block total_orders">
                                    <div class="module_table_title text_center border_light">Total Orders</div>
                                    <div class="module_table_column">
                                        <div class="module_table_row border_light text_center padding-left-10" id="orders-main-count">0</div>
                                    </div>
                                </div>
                                <div class="module_table_block total_sales">
                                    <div class="module_table_title text_center border_light">Total Sales</div>
                                    <div class="module_table_column">
                                        <div class="module_table_row border_light text_center padding-left-10" id="orders-main-val">$0.00</div>
                                    </div>
                                </div>
                            </div>
                        </p>
                    </div>
                    <div class="module_406 m_15 m_sm_auto">
                        <p class="module_title d_flex justify_between">Affiliates<span class="module_budge show_background affiliate-details"><a href="#" id="show-affiliate-details" data-ajax="false">Show Detail</a></span>
                            <div class="module_table d_flex border_radius border_light">
                                <div class="module_table_block total_orders">
                                    <div class="module_table_title text_center border_light">Total Orders</div>
                                    <div class="module_table_column">
                                        <div class="module_table_row border_light text_center padding-left-10" id="orders-affiliate-count">0</div>
                                    </div>
                                </div>
                                <div class="module_table_block total_sales">
                                    <div class="module_table_title text_center border_light">Total Sales</div>
                                    <div class="module_table_column">
                                        <div class="module_table_row border_light text_center padding-left-10" id="orders-affiliate-val">$0.00</div>
                                    </div>
                                </div>
                            </div>
                        </p>
                    </div>
                </div>
                <div class="modules_line d_flex flex_md_column block_margin">
                    <div class="module_625 m_15 m_sm_auto ">
                        <p class="module_title">Revenue Stats</p>
                        <div class="circle_box_wrapper d_flex justify_between align_center border_light border_radius flex_sm_column_nw text_center">
                            <div class="circle_box">
                                <p class="circle_box_title">Store</p>
                                <div class="first circle" data-value="0.1" data-thickness="4"><strong></strong></div>
                            </div>
                            <div class="circle_box">
                                <p class="circle_box_title">Main</p>
                                <div class="second circle" data-value="0.1" data-thickness="4"><strong></strong></div>
                            </div>
                            <div class="circle_box">
                                <p class="circle_box_title">Affiliates</p>
                                <div class="third circle" data-value="0.1" data-thickness="4"><strong></strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="module_625 m_15 m_sm_auto order_md" id="table_slider">
                        <p class="module_title">
				Affiliates Detail
				<a class="header_menu_link icon_link download_link float-right download_csv_link" id="download_afl_csv" href="#" data-ajax="false" style="width: auto; height: auto; border: none;"><img src='<?=plugin_dir_url( dirname( __FILE__ ) )?>/../images/icon-download-small.png' alt="download" /></a>
                        </p>
                        <div class="module_table_slider module_table_slider1 d_flex border_radius border_light pos_relative">
                            <div class="module_table_block total_affiliates_afl">
                                <div class="module_table_title_responsive module_table_title text_center border_light">Affiliate</div>
                                <div class="module_table_column" id="affiliate-table-name">
                                </div>
                            </div>
                            <div class="module_table_block total_orders_afl">
                                <div class="module_table_title_responsive module_table_title text_center border_light">Total Orders</div>
                                <div class="module_table_column" id="affiliate-table-orders">
                                </div>
                            </div>
                            <div class="module_table_block total_sales_afl">
                                <div class="module_table_title_responsive module_table_title text_center border_light">Total Sales</div>
                                <div class="module_table_column" id="affiliate-table-value">
                                </div>
                            </div>
                            <div class="module_btn_wrapper d_flex justify_between"><button class="module_slider_prev">prev</button><button class="module_slider_next">next</button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




