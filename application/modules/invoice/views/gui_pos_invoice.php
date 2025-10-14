<link href="<?php echo base_url('assets/css/gui_pos.css?v=1') ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/css/pos-style.css?v=1') ?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url() ?>my-assets/js/admin_js/pos_invoice.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>my-assets/js/admin_js/guibarcode.js" type="text/javascript"></script>
<script src="assets/js/perfect-scrollbar.min.js" type="text/javascript"></script>
<style>
.product_item_table_wrapper .product_name_td {
    width: 120px;
}

.product_item_table_wrapper .qty_td {
    width: 50px;
}

.product_item_table_wrapper .price_td {
    width: 90px;
}

.product_item_table_wrapper .total_price_td {
    width: 100px;
}

.product_item_table_wrapper .batch_no_td {
    width: 120px;
}

.product_item_table_wrapper .available_quantity_td {
    width: 70px;
}

@media screen and (max-width: 1600px) {}
</style>

<div class="pos_layout">
    <div class="top-bar">
        <ul class="nav nav-tabs" role="tablist">
            <li>
                <a class="btn_backto_home" href="<?php echo base_url('home') ?>"><i class="fa fa-home"
                        aria-hidden="true"></i>
                    Dashboard</a>
            </li>
            <li class="active">
                <a href="#home" role="tab" data-toggle="tab" class="home" id="new_sale">
                    New Sale </a>
            </li>
            <li class="onprocessg"><a href="#saleList" role="tab" data-toggle="tab" class="ongord" id="todays_salelist">
                    Todays sale </a>
            </li>
        </ul>
        <div class="tgbar d-flex">
            <a title="keyshort cut" href="" class="topbar-icon" id="keyshortcut" aria-hidden="true" data-toggle="modal"
                data-target="#cheetsheet"><i class="fa fa-keyboard-o"></i></a>
        </div>
    </div>
    <!-- Tab panes - tab-content -->
    <div class="main_content">
    <div class="tab-content">
        <div class="tab-pane fade active in" id="home">
            <div class="main_content_body">
                <!-- Left Section -->
                <div class="left_section mr-3">
                    <div class="row">
                        <!-- Filter Section -->
                        <div class="col-xs-4 col-sm-3 col-md-4 col-lg-3 col-xl-2 filter_section">
                            <div class="btn-check-group ">
                                <div style="border-radius: 6px;" onclick="check_category('all')" id="all"
                                    class="btn-check active btn btn-success btn-block"> All
                                </div>
                                <?php if ($categorylist) { ?>
                                <?php foreach ($categorylist as $categories) { ?>
                                <div style="border-radius: 6px;" class="btn-check btn btn-success btn-block text-wrap"
                                    id="cat_<?php echo $categories['category_id'] ?>"
                                    value="<?php echo $categories['category_id'] ?>"
                                    onclick="check_category(<?php echo $categories['category_id'] ?>)">
                                    <?php echo $categories['category_name'] ?>
                                </div>
                                <?php }
                                } ?>

                                <input name="url" type="hidden" id="posurl"
                                    value="<?php echo base_url("invoice/invoice/getitemlist") ?>" />
                                <input name="url" type="hidden" id="posurl_productname"
                                    value="<?php echo base_url("invoice/invoice/getitemlist_byname") ?>" />
                            </div>
                        </div>
                        <!-- Product Section -->
                        <div class="col-xs-8 col-sm-9 col-md-8 col-lg-9 col-xl-10 pl-0" id="style-3">

                            <div class="row search-bar">
                                <div class="col-sm-12">
                                    <!-- Actual search box -->
                                    <div class="form-group has-feedback has-search">
                                        <span
                                            class="ti-search form-control-feedback d-flex align-items-center justify-content-center"></span>
                                        <input type="text" class="form-control" id="product_name"
                                            placeholder="Search Product">
                                    </div>
                                </div>
                                <!-- <div class="col-sm-6">
                                    <form class="navbar-search">
                                        <label class="sr-only screen-reader-text" for="search">Search :</label>
                                        <div class="input-group">
                                            <select name="productlist" class="form-control filter-select"
                                                onchange="onselectimage(this.value)">
                                                <option value='' selected>Select Product</option>
                                                <?php if ($product_list) { ?>
                                                    <?php foreach ($product_list as $products) { ?>
                                                        <option value="<?php echo $products['product_id'] ?>">
                                                            <?php echo $products['product_name'] ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </form>
                                </div> -->
                            </div>

                            <div class="product_grid">
                                <div style=" padding: 4px;" class="row row-m-3" id="product_search">
                                    <?php $i = 0;
                                    if ($itemlist) {
                                        foreach ($itemlist as $item) {
                                            ?>

                                    <div class="col-xs-6 col-sm-3 col-md-4 col-lg-3 col-p-3">
                                        <div class="product-panel overflow-hidden border-0 shadow-sm bg-white"
                                            id="image-active_<?php echo $item->product_id ?>">
                                            <div class="item-image position-relative overflow-hidden">
                                                <div class="" id="image-active_count_<?php echo $item->product_id ?>">
                                                    <span id="active_pro_<?php echo $item->product_id ?>"
                                                        class="active_qty"></span>
                                                </div>
                                                <img src="<?php echo !empty($item->image) ? $item->image : 'assets/img/icons/default.jpg'; ?>"
                                                    onclick="onselectimage('<?php echo $item->product_id ?>')" alt=""
                                                    class="img-responsive">
                                            </div>
                                            <div class="panel-footer border-0 bg-white"
                                                onclick="onselectimage('<?php echo $item->product_id ?>')">
                                                <h3 class="item-details-title">
                                                    <?php echo $text = html_escape($item->product_name); ?>
                                                </h3>
                                                <p class="text-center"
                                                    style="color: #00A653; font-size: 12px; margin-top: 0;">Price:-
                                                    <?php echo $text = html_escape($item->price); ?>
                                                </p>

                                            </div>
                                        </div>
                                    </div>
                                    <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add to Item -->
                <div class="right_section">

                    <?php echo form_open_multipart('invoice/invoice/bdtask_manual_sales_insert', array('class' => 'form-vertical', 'id' => 'gui_sale_insert', 'name' => 'insert_pos_invoice')) ?>
                    <div class="d-flex align-items-center mb-4">
                        <!-- Customer Name -->
                        <div class="input-group mr-3 d-flex align-items-center">
                            <label class="pr-2" for="customer_name">Customer:</label>
                            <input type="text" class="form-control customerSelection" id="customer_name"
                                value="<?php echo $customer_name; ?>"
                                placeholder="<?php echo display('customer_name'); ?>" tabindex="3"
                                onkeyup="customer_autocomplete()" name="customer_name">
                        </div>
                        <!-- Add button -->
                        <div class="mr-3">
                            <input id="autocomplete_customer_id" class="customer_hidden_value" type="hidden"
                                name="customer_id" value="<?php echo $customer_id ?>">
                            <span class="input-group-btn">
                                <button style="border-radius: 4px;" class="client-add-btn btn btn-success" type="button"
                                    aria-hidden="true" data-toggle="modal" data-target="#cust_info"
                                    id="customermodal-link" tabindex="4"><i class="ti-plus"></i></button>
                            </span>
                        </div>
                        <!-- Draft List -->
                        <!-- Draft List -->
                        <div class="input-group d-flex align-items-center">
                            <label class="pr-2" for="draft_list">Draft:</label>
                            <select class="form-control" id="draft_list" name="draft_list" tabindex="3">
                                <option value="">Loading...</option>
                            </select>
                        </div>

                    </div>

                    <!-- Discount Modal -->
                    <div id="discount_modal" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content overflow-hidden">
                                <div class="modal-header">
                                    <a href="#" class="btn_close" data-dismiss="modal">&times;</a>
                                    <div style="width: 90%; font-size: 17px; font-weight: 600;"
                                        class="d-flex justify-content-between">
                                        <h4 class="title truncate pr-5">Discount</h4>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-12 mb-3">
                                            <div style="width: 100%;" class="input-group">
                                                <label for="discount">Discount</label>
                                                <input type="number" onchange="quantity_calculate(1);"
                                                    onkeyup="quantity_calculate(1);"
                                                    class="form-control total_discount customerSelection"
                                                    id="invoice_discount" value="" name="invoice_discount">
                                            </div>
                                        </div>


                                        <div class="col-sm-12 mt-3 d-flex justify-content-end">
                                            <div class="col-sm-4 px-0">
                                                <button style="border-radius: 6px;" type="button"
                                                    class="btn btn-success px-5 py-3 w-100"
                                                    onclick="applyProductUpdate()">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Cost Modal -->
                    <div id="shippint_cost_modal" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content overflow-hidden">
                                <div class="modal-header">
                                    <a href="#" class="btn_close" data-dismiss="modal">&times;</a>
                                    <div style="width: 90%; font-size: 17px; font-weight: 600;"
                                        class="d-flex justify-content-between">
                                        <h4 class="title truncate pr-5">Shipping Cost</h4>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-12 mb-3">
                                            <div style="width: 100%;" class="input-group">
                                                <label for="shipping_cost">Cost</label>
                                                <input type="text" id="shipping_cost"
                                                    class="form-control gui-foot text-right" name="shipping_cost"
                                                    onkeyup="quantity_calculate(1);" onchange="quantity_calculate(1);"
                                                    placeholder="0.00" />
                                            </div>
                                        </div>


                                        <div class="col-sm-12 mt-3 d-flex justify-content-end">
                                            <div class="col-sm-4 px-0">
                                                <button style="border-radius: 6px;" type="button"
                                                    onclick="applyProductUpdate()"
                                                    class="btn btn-success px-5 py-3 w-100">Update</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Multi Pay -->
                    <div id="multipay_modal" class="modal fade" role="dialog">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content overflow-hidden">
                                <div class="modal-header">
                                    <a href="#" class="btn_close" data-dismiss="modal">&times;</a>
                                    <div style="width: 90%; font-size: 17px; font-weight: 600;"
                                        class="d-flex justify-content-between">
                                        <h4 class="title truncate pr-5">Multi Pay</h4>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <div class="row px-4">
                                        <div class="col-sm-12 mb-5 payment_type_wrapper">
                                            <button data-target="#payment-type-cash" type="button"
                                                class="btn btn_custom btn_outline  p-3 flex align-items-center"><i
                                                    style="font-size: 20px; margin-right: 8px;"
                                                    class="fa fa-credit-card"
                                                    aria-hidden="true"></i><span>CASH</span></button>
                                            <button data-target="#payment-type-card" type="button"
                                                class="btn btn_custom btn_outline p-3 flex align-items-center ">
                                                <img style="width: 32px; margin-right: 8px;"
                                                    src="http://localhost/www/salesERPphp8.2/assets/static-img/bKash-logo.png"
                                                    alt="">
                                                <span>Card</span></button>
                                            <strong>Total Payable: </strong> <strong id="total_payable">K</strong>
                                        </div>
                                        <div class="col-sm-12 mb-3">
                                            <div class="input-group d-flex gap-10 position-relative">
                                                <label>Amount:</label>
                                                <input onkeyup="update_total_payable();"
                                                    onchange="update_total_payable();" style="border-radius: 4px;"
                                                    type="text" class="form-control customerSelection payment_amount"
                                                    id="payment-type-cash" name="cash_amount" placeholder="Cash Amount">
                                                <input onkeyup="update_total_payable();"
                                                    onchange="update_total_payable();" style="border-radius: 4px;"
                                                    type="text"
                                                    class="form-control customerSelection payment_amount d_none"
                                                    id="payment-type-card" name="card_amount" placeholder="Card Amount">
                                            </div>
                                        </div>

                                        <div class="col-sm-12 mt-3 d-flex justify-content-end">
                                            <div class="col-sm-6 px-0 d-flex gap-10">
                                                <button data-dismiss="modal" type="button"
                                                    class="btn btn-danger btn_custom px-5 py-3 w-100 flex align-items-center">
                                                    <i style="font-size: 20px; margin-right: 8px;" class="fa fa-trash"
                                                        aria-hidden="true"></i><span>Close</span></button>

                                                <button type="submit"
                                                    class="btn btn-success btn_custom px-5 py-3 w-100 flex align-items-center"><i
                                                        style="font-size: 20px; margin-right: 8px;"
                                                        class="fa fa-hospital-o" aria-hidden="true"></i><span>Print
                                                        POS</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- New table design start -->
                    <div class="table-responsive product_item_table_wrapper">
                        <table class="product_item_table" id="addinvoice">
                            <thead>
                                <tr class="">
                                    <th class="text-center text-nowrap">Edit</th>
                                    <th class="text-nowrap">Item</th>
                                    <th class="text-nowrap">Batch No</th>
                                    <th class="text-center text-nowrap">Av Qty.</th>
                                    <th class="text-center text-nowrap">Qty.</th>
                                    <th class="text-right text-nowrap">Price</th>
                                    <th class="text-right text-nowrap">Total</th>
                                    <th class="text-center text-nowrap">Action</th>
                                </tr>
                            </thead>

                            <tbody class="addinvoiceItem">
                            </tbody>

                        </table>
                    </div>

                    <div class="summary_section">
                        <!-- Summary table start-->
                        <div class="vat_summary">
                            <!-- item -1 -->
                            <div class="item d-flex justify-content-between align-items-center gap-10">
                                <strong>VAT</strong>
                                <input class="text-right" type="text" id="total_vat_amnt" name="total_vat_amnt"
                                    value="0.00" readonly="readonly" />
                            </div>

                            <!-- item-2 -->
                            <div class="item d-flex justify-content-between align-items-center gap-10">
                                <div class="d-flex">
                                    <button type="button" class="btn_edit" data-toggle="modal"
                                        data-target="#discount_modal" id="">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </button>
                                    <strong>Discount</strong>
                                </div>

                                <input type="text" id="total_discount_ammount" class="text-right" name="total_discount"
                                    value="0.00" readonly="readonly" />
                            </div>
                            <!-- item-3 -->
                            <div class="item d-flex justify-content-between align-items-center gap-10">
                                <strong>Item</strong>
                                <div class="text-right"><strong id="total_quantity">3</strong></div>
                            </div>
                            <!-- item-4 -->
                            <div class="item d-flex align-items-center">
                                <button type="button" class="btn_edit" data-toggle="modal"
                                    data-target="#shippint_cost_modal" id="">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </button>
                                <span>Shipping Cost</span>
                            </div>
                            <!-- item-5 -->
                            <div class="item d-flex justify-content-between align-items-center gap-10">
                                <div>
                                    <!-- <button type="button" class="btn_edit" data-toggle="modal"
                                        data-target="#coupon_modal" id="">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </button>
                                    <span>Coupon</span> -->
                                </div>
                                <div class="text-right">
                                    <!-- <strong>2%</strong> -->
                                </div>
                            </div>
                            <!-- item-6 -->
                            <div>
                                <div class="item d-flex justify-content-between align-items-center gap-10">
                                    <span>Previous</span>
                                    <input class="text-right" type="text" id="previous" name="previous" value="0.000"
                                        readonly="readonly" />

                                </div>
                            </div>
                        </div>
                        <!-- Summary table end -->

                        <p style="font-size: 18px; font-weight: 600; background-color:rgb(226, 228, 225); margin-bottom: 0;"
                            class="text-center text-success py-2">Grand Total: <input
                                style="border:none; background-color: transparent;" class="grand_total_price_input"
                                type="text" id="grandTotal" name="grand_total_price" value="0.00" />
                            $</p>
                        <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                        <table class="w-100 calculate_table">
                            <tr>
                                <td style="background-color: #2972FF; width: 30%; font-weight: 600; font-size: 15px;"
                                    role="button" data-toggle="modal" id="calculator_modal" data-target="#calculator"
                                    class="text-center text-white">
                                    <i class="fa fa-calculator" aria-hidden="true"></i> <span>Calculator</span>
                                </td>

                                <td style="background-color: #D43407; width: 30%; font-weight: 600; font-size: 15px;"
                                    role="button" data-toggle="modal" data-target="#multipay_modal"
                                    class="text-center text-white">
                                    <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
                                    <button onclick="setSaleType('multi-pay')"
                                        style="font-weight: 600; font-size: 15px; background: transparent;"
                                        type="button" class="border-0 text-white" id="">Multi
                                        Pay
                                    </button>
                                </td>


                                <td style="padding: 0; background-color: #9052F5; width: 20%; " rowspan="2"
                                    class="text-center text-white">
                                    <button class="w-100 h-100 btn_summary_submit text-center text-white" type="submit"
                                        name="sale_type" onclick="setSaleType('card')"
                                        style="background-color: #9052F5; height: 85px; font-weight: 600; font-size: 15px;">Card</button>
                                    <input type="hidden" name="sale_type" id="sale_type" value="">

                                </td>

                                <td style="padding: 0; background-color: #0C3D80; width: 20%;" rowspan="2">
                                    <button class="w-100 h-100 btn_summary_submit text-center text-white" type="submit"
                                        name="sale_type" onclick="setSaleType('cash')"
                                        style="background-color: #0C3D80;  height: 85px; font-weight: 600; font-size: 15px;">Cash</button>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0;">
                                    <button
                                        style="padding: 10px; background-color: #313F6B; width: 30%; font-weight: 600; font-size: 15px;"
                                        class="w-100 h-100 btn_summary_submit text-center text-white" type="submit"
                                        name="sale_type" onclick="setSaleType('credit_sale')">
                                        <i class="fa fa-credit-card" aria-hidden="true"></i>
                                        <span> Credit Sale </span>

                                    </button>
                                </td>
                                <td style="padding:0">
                                    <button
                                        style="padding: 10px; background-color:rgb(5, 179, 43); width: 30%; font-weight: 600; font-size: 15px;"
                                        class="w-100 h-100 btn_summary_submit text-center text-white" type="submit"
                                        name="sale_type" onclick="setSaleType('draft')">
                                        <i class="fa fa-credit-card" aria-hidden="true"></i>
                                        <span> Draft </span>
                                    </button>
                                </td>

                            </tr>

                        </table>
                        </form>
                    </div>

                    <!-- New table design end -->

                    <!-- Old Table Data Start -->

                    <div style="display: none; visibility: hidden;" class="old_section">
                        <h3 class="text-danger">Old Content</h3>
                        <div>
                            <form class="form-inline mb-3">
                                <div class="form-group">
                                    <input type="text" id="add_item" class="form-control"
                                        placeholder="Barcode or QR-code scan here">
                                </div>
                                <div class="form-group">
                                    <label class="mr-3 ml-3">OR</label>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="add_item_m"
                                        placeholder="Manual Input barcode">
                                </div>
                            </form>
                        </div>

                        <input type="hidden" name="csrf_test_name" id=""
                            value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="tax_type" id="tax_type" value="<?php echo $tax_type; ?>">

                        <div class="table-responsive guiproductdata">
                            <table class="table table-bordered table-hover table-sm nowrap gui-products-table"
                                id="addinvoice">
                                <thead>
                                    <tr>
                                        <th class="text-center gui_productname">
                                            <?php echo display('item_information') ?> <i class="text-danger">*</i>
                                        </th>
                                        <th class="text-center invoice_fields"><?php echo display('batch_no') ?><i
                                                class="text-danger">*</i></th>
                                        <th class="text-center"><?php echo display('available_qnty') ?></th>
                                        <th class="text-center"><?php echo display('quantity') ?> <i
                                                class="text-danger">*</i></th>
                                        <th class="text-center"><?php echo display('rate') ?> <i
                                                class="text-danger">*</i>
                                        </th>
                                        <?php if ($discount_type == 1) { ?>
                                        <th class="text-center" style="width: 90px;"><?php echo display('disc') ?></th>
                                        <?php } elseif ($discount_type == 2) { ?>
                                        <th class="text-center"><?php echo display('discount') ?> </th>
                                        <?php } elseif ($discount_type == 3) { ?>
                                        <th class="text-center"><?php echo display('fixed_dis') ?> </th>
                                        <?php } ?>
                                        <th class="text-center invoice_fields"><?php echo display('dis_val') ?> </th>
                                        <th class="text-center invoice_fields"><?php echo display('vat') . ' %' ?> </th>
                                        <th class="text-center invoice_fields"><?php echo display('vat_val') ?> </th>
                                        <th class="text-center"><?php echo display('total') ?></th>
                                        <th class="text-center"><?php echo display('action') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="addinvoiceItem">

                                </tbody>
                            </table>
                        </div>
                        <div class="footer">
                            <div class="form-group row guifooterpanel">
                                <div class="col-sm-12">
                                    <label for="date"
                                        class="col-sm-6 col-lg-6 col-xl-7 col-form-label"><?php echo display('invoice_discount') ?>:</label>
                                    <div class="col-sm-6 col-lg-5 col-xl-4">
                                        <input type="text" onkeyup="quantity_calculate(1);"
                                            onchange="quantity_calculate(1);" id="invoice_discount"
                                            class="form-control total_discount gui-foot text-right"
                                            name="invoice_discount" placeholder="0.00" />
                                        <input type="hidden" id="txfieldnum" value="<?php echo $taxnumber ?>" />
                                        <input type="hidden" name="paytype" value="1" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row guifooterpanel">
                                <div class="col-sm-12">
                                    <label for="date"
                                        class="col-sm-6 col-lg-6 col-xl-7 col-form-label"><?php echo display('total_discount') ?>:</label>
                                    <div class="col-sm-6 col-lg-5 col-xl-4">
                                        <input type="text" id="total_discount_ammount"
                                            class="form-control gui-foot text-right" name="total_discount" value="0.00"
                                            readonly="readonly" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row guifooterpanel">
                                <div class="col-sm-12">
                                    <label for="date"
                                        class="col-sm-6 col-lg-6 col-xl-7 col-form-label"><?php echo display('ttl_val') ?>:</label>
                                    <div class="col-sm-6 col-lg-5 col-xl-4">
                                        <input type="text" id="total_vat_amnt" class="form-control gui-foot text-right"
                                            name="total_vat_amnt" value="0.00" readonly="readonly" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row guifooterpanel">
                                <div class="col-sm-12">
                                    <label for="date"
                                        class="col-sm-6 col-lg-6 col-xl-7 col-form-label"><?php echo display('shipping_cost') ?>:</label>
                                    <div class="col-sm-6 col-lg-5 col-xl-4">
                                        <!-- <input type="text" id="shipping_cost" class="form-control gui-foot text-right"
                                            name="shipping_cost" onkeyup="quantity_calculate(1);"
                                            onchange="quantity_calculate(1);" placeholder="0.00" /> -->
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row guifooterpanel">
                                <div class="col-sm-12">
                                    <label for="date"
                                        class="col-sm-6 col-lg-6 col-xl-7 col-form-label"><?php echo display('grand_total') ?>:</label>
                                    <div class="col-sm-6 col-lg-5 col-xl-4"><input type="text" id="grandTotal"
                                            class="form-control gui-foot text-right grandTotalamnt"
                                            name="grand_total_price" value="0.00" readonly="readonly" />
                                        <input type="hidden" name="baseUrl" class="baseUrl"
                                            value="<?php echo base_url(); ?>" id="baseurl" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row guifooterpanel">
                                <div class="col-sm-12">
                                    <label for="date"
                                        class="col-sm-6 col-lg-6 col-xl-7 col-form-label"><?php echo display('previous'); ?>:</label>
                                    <div class="col-sm-6 col-lg-5 col-xl-4"><input type="text" id="previous"
                                            class="form-control gui-foot text-right" name="previous" value="0.00"
                                            readonly="readonly" /></div>
                                </div>
                            </div>
                            <div class="form-group row guifooterpanel">
                                <div class="col-sm-12">
                                    <label for="change"
                                        class="col-sm-6 col-lg-6 col-xl-7 col-form-label"><?php echo display('change'); ?>:</label>
                                    <div class="col-sm-6 col-lg-5 col-xl-4"><input type="text" id="change"
                                            class="form-control gui-foot text-right" name="change" value="0.00"
                                            readonly="readonly" /></div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                        <p hidden id="pay-amount"></p>
                        <p hidden id="change-amount"></p>
                        <div class="col-sm-12 table-bordered p-20">
                            <div id="adddiscount" class="display-none">
                                <div class="row no-gutters">
                                    <div class="form-group col-md-6">
                                        <label for="payments"
                                            class="col-form-label pb-2"><?php echo display('payment_type'); ?></label>

                                        <?php $card_type = 1020101;
                                        echo form_dropdown('multipaytype[]', $all_pmethod, (!empty($card_type) ? $card_type : null), 'onchange = "check_creditsale()" class="card_typesl postform resizeselect form-control "') ?>

                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="4digit"
                                            class="col-form-label pb-2"><?php echo display('paid_amount'); ?></label>

                                        <input type="text" id="pamount_by_method" class="form-control number pay "
                                            name="pamount_by_method[]" value="" onkeyup="changedueamount()"
                                            placeholder="0" />
                                    </div>
                                </div>

                                <div class="" id="add_new_payment">

                                </div>
                                <div class="form-group text-right">
                                    <div class="col-sm-12 pr-0">
                                        <button type="button" id="add_new_payment_type"
                                            class="btn btn-success w-md m-b-5"><?php echo display('new_p_method'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fixedclasspos">
                            <div class="bottomarea">
                                <div class="row">
                                    <div class="col-lg-8 col-xl-8">
                                        <div class="calculation d-lg-flex">
                                            <div class="cal-box d-lg-flex align-items-lg-center mr-4">
                                                <label
                                                    class="cal-label mr-2 mb-0"><?php echo display('net_total'); ?>:</label><span
                                                    class="amount" id="net_total_text">0.00</span>
                                                <input type="hidden" id="n_total"
                                                    class="form-control text-right guifooterfixedinput" name="n_total"
                                                    value="0" readonly="readonly" placeholder="" />
                                            </div>
                                            <div class="cal-box d-lg-flex align-items-lg-center mr-4">
                                                <div class="form-inline d-inline-flex align-items-center">
                                                    <label
                                                        class="cal-label mr-2 mb-0"><?php echo display('paid_ammount') ?>:</label>
                                                    <input type="text" class="form-control" id="paidAmount"
                                                        onkeyup="invoice_paidamount()" name="paid_amount"
                                                        onkeypress="invoice_paidamount()" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="cal-box d-lg-flex align-items-lg-center mr-4">
                                                <label
                                                    class="cal-label mr-2 mb-0"><?php echo display('due') ?>:</label><span
                                                    class="amount" id="due_text">0.00</span>
                                                <input type="hidden" id="dueAmmount"
                                                    class="form-control text-right guifooterfixedinput"
                                                    name="due_amount" value="0.00" readonly="readonly" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-xl-4 text-xl-right">
                                        <div class="action-btns d-flex justify-content-end">
                                            <input type="submit" id="add_invoice" class="btn btn-success btn-lg mr-2"
                                                name="add_invoice" value="Save Sale">
                                            <a href="#" class="btn btn-success btn-lg" data-toggle="modal"
                                                id="calculator_modal" data-target="#calculator"><i
                                                    class="fa fa-calculator" aria-hidden="true"></i> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Old Table Data End -->
                </div>
            </div>
        </div>
        <!-- Today Sale Tab Content -->
        <div class="tab-pane fade" id="saleList">
            <?php echo "Today Sale Test"; ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="table-responsive padding10" id="invoic_list">
                        <table id="gui_productinfo" class="table table-bordered  table-hover datatable ">
                            <thead>
                                <tr>
                                    <th><?php echo display('sl') ?></th>
                                    <th><?php echo display('invoice_no') ?></th>
                                    <th><?php echo display('invoice_id') ?></th>
                                    <th><?php echo display('customer_name') ?></th>
                                    <th><?php echo display('date') ?></th>
                                    <th><?php echo display('total_amount') ?></th>
                                    <th><?php echo display('action') ?></th>
                                </tr>
                            </thead>
                            <tbody id="gui_tbody">
                                <?php
                                $total = '0.00';
                                $sl = 1;
                                if ($todays_invoice) {
                                    foreach ($todays_invoice as $invoices_list) {
                                        ?>

                                <tr>
                                    <td><?php echo $sl; ?></td>
                                    <td>
                                        <a
                                            href="<?php echo base_url() . 'invoice_details/' . $invoices_list['invoice_id']; ?>">

                                            <?php echo html_escape($invoices_list['invoice']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a
                                            href="<?php echo base_url() . 'invoice_details/' . $invoices_list['invoice_id']; ?>">
                                            <?php echo $invoices_list['invoice_id'] ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo html_escape($invoices_list['customer_name']) ?>
                                    </td>

                                    <td><?php echo $invoices_list['date'] ?></td>
                                    <td class="text-right"><?php
                                            if ($position == 0) {
                                                echo $currency . $invoices_list['total_amount'];
                                            } else {
                                                echo $invoices_list['total_amount'] . $currency;
                                            }
                                            $total += $invoices_list['total_amount']; ?></td>
                                    <td>
                                        <center>
                                            <?php echo form_open() ?>

                                            <a href="<?php echo base_url() . 'invoice_details/' . $invoices_list['invoice_id']; ?>"
                                                class="btn btn-success btn-sm" data-toggle="tooltip"
                                                data-placement="left" title="<?php echo display('invoice') ?>"><i
                                                    class="fa fa-window-restore" aria-hidden="true"></i></a>
                                            <a href="<?php echo base_url() . 'invoice_pad_print/' . $invoices_list['invoice_id']; ?>"
                                                class="btn btn-primary btn-sm" data-toggle="tooltip"
                                                data-placement="left" title="<?php echo 'Pad Print' ?>"><i
                                                    class="fa fa-fax" aria-hidden="true"></i></a>

                                            <a href="<?php echo base_url() . 'pos_print/' . $invoices_list['invoice_id']; ?>"
                                                class="btn btn-warning btn-sm" data-toggle="tooltip"
                                                data-placement="left" title="<?php echo display('pos_invoice') ?>"><i
                                                    class="fa fa-fax" aria-hidden="true"></i></a>
                                            <?php if ($this->permission1->method('manage_invoice', 'update')->access()) { ?>

                                            <a href="<?php echo base_url() . 'invoice_edit/' . $invoices_list['invoice_id']; ?>"
                                                class="btn btn-success btn-sm" data-toggle="tooltip"
                                                data-placement="left" title="<?php echo display('update') ?>"><i
                                                    class="fa fa-pencil" aria-hidden="true"></i></a>
                                            <?php } ?>

                                            <?php echo form_close() ?>
                                        </center>
                                    </td>
                                </tr>

                                <?php
                                        $sl++;
                                    }
                                }
                                ?>
                            </tbody>

                        </table>

                        </tbody>

                        </table>

                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- detailsmodal -->
<div id="detailsmodal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content overflow-hidden">
            <div class="modal-header">
                <a href="#" class="close" data-dismiss="modal">&times;</a>
                <strong>
                    <center> <?php echo display('product_details') ?></center>
                </strong>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="panel panel-bd">

                            <div class="panel-body">
                                <span id="modalimg"></span><br>
                                <h4><?php echo display('product_name') ?> :<span id="modal_productname"></span></h4>
                                <h4><?php echo display('product_model') ?> :<span id="modal_productmodel"></span></h4>
                                <h4><?php echo display('price') ?> :<span id="modal_productprice"></span></h4>
                                <h4><?php echo display('unit') ?> :<span id="modal_productunit"></span></h4>
                                <h4><?php echo display('stock') ?> :<span id="modal_productstock"></span></h4>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="modal-footer">

        </div>

    </div>

</div>

<!-- Updated product Modal -->
<div id="updated_product" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content overflow-hidden">
            <div class="modal-header">
                <a href="#" class="btn_close" data-dismiss="modal">&times;</a>
                <div style="width: 90%; font-size: 17px; font-weight: 600;" class="d-flex justify-content-between">
                    <h4 class="title truncate pr-5" id="pmodel_product_name">Product Name</h4>
                    <p class="text-success mb-0">Instock</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4 mb-3">
                        <div style="width: 100%;" class="input-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control customerSelection" id="pmodel_quantity" value=""
                                name="quantity">
                        </div>
                    </div>

                    <div class="col-sm-4 mb-3">
                        <div style="width: 100%;" class="input-group">
                            <label for="item_qty">Item Qty</label>
                            <input type="number" class="form-control customerSelection" id="pmodel_item_qty" value=""
                                name="item_qty">
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <div style="width: 100%;" class="input-group">
                            <label for="rate">Rate <span class="text-danger">*</span></label>
                            <input type="number" class="form-control customerSelection" id="pmodel_rate" value=""
                                name="pmodel_rate">
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <div style="width: 100%;" class="input-group">
                            <label for="discount">Discount</label>
                            <input type="number" class="form-control customerSelection" id="pmodel_discount" value=""
                                name="discount">
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <div style="width: 100%;" class="input-group">
                            <label for="discount_value">Dis.Value</label>
                            <input type="number" class="form-control customerSelection" id="pmodel_discount_value"
                                value="" name="discount_value" readonly>
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <div style="width: 100%;" class="input-group">
                            <label for="vat">Vat%</label>
                            <input type="number" class="form-control customerSelection" id="pmodel_vat" value=""
                                name="vat">
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <div style="width: 100%;" class="input-group">
                            <label for="vat_value">Vat Value</label>
                            <input type="number" class="form-control customerSelection" id="pmodel_vat_value" value=""
                                name="vat_value" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-3">
                        <div style="font-size: 20px; font-weight: 700;" class="">Total : <span id="total_price_">
                            </span> </div>
                    </div>
                    <div class="col-sm-6 mt-3 d-flex justify-content-end">
                        <div class="col-sm-6 px-0">
                            <button type="button" class="btn btn-success px-5 py-3 w-100"
                                onclick="applyProductUpdate()">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Discount Modal -->
<div id="discount_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content overflow-hidden">
            <div class="modal-header">
                <a href="#" class="btn_close" data-dismiss="modal">&times;</a>
                <div style="width: 90%; font-size: 17px; font-weight: 600;" class="d-flex justify-content-between">
                    <h4 class="title truncate pr-5">Discount</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 mb-3">
                        <div style="width: 100%;" class="input-group">
                            <label for="discount">Discount</label>
                            <input type="number" onchange="quantity_calculate(1);" onkeyup="quantity_calculate(1);"
                                class="form-control total_discount customerSelection" id="invoice_discount" value=""
                                name="invoice_discount">
                        </div>
                    </div>

                    <div class="col-sm-12 mt-3 d-flex justify-content-end">
                        <div class="col-sm-4 px-0">
                            <button style="border-radius: 6px;" type="button" class="btn btn-success px-5 py-3 w-100"
                                onclick="applyProductUpdate()">Update</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shipping Cost Modal -->
<div id="shippint_cost_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content overflow-hidden">
            <div class="modal-header">
                <a href="#" class="btn_close" data-dismiss="modal">&times;</a>
                <div style="width: 90%; font-size: 17px; font-weight: 600;" class="d-flex justify-content-between">
                    <h4 class="title truncate pr-5">Shipping Cost</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-sm-12 mb-3">
                        <div style="width: 100%;" class="input-group">
                            <label for="shipping_cost">Cost</label>
                            <input type="text" id="shipping_cost" class="form-control gui-foot text-right"
                                name="shipping_cost" onkeyup="quantity_calculate(1);" onchange="quantity_calculate(1);"
                                placeholder="0.00" />
                        </div>
                    </div>

                    <div class="col-sm-12 mt-3 d-flex justify-content-end">
                        <div class="col-sm-4 px-0">
                            <button style="border-radius: 6px;" type="button" onclick="applyProductUpdate()"
                                class="btn btn-success px-5 py-3 w-100">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Coupon Modal -->
<div id="coupon_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content overflow-hidden">
            <div class="modal-header">
                <a href="#" class="btn_close" data-dismiss="modal">&times;</a>
                <div style="width: 90%; font-size: 17px; font-weight: 600;" class="d-flex justify-content-between">
                    <h4 class="title truncate pr-5">Coupon</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 mb-3">
                        <div style="width: 100%;" class="input-group">
                            <label for="discount">Code</label>
                            <input type="text" class="form-control customerSelection" id="discount" value=""
                                name="discount">
                        </div>
                    </div>


                    <div class="col-sm-12 mt-3 d-flex justify-content-end">
                        <div class="col-sm-4 px-0">
                            <button style="border-radius: 6px;" type="button"
                                class="btn btn-success px-5 py-3 w-100">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>




<div class="modal fade" id="printconfirmodal" tabindex="-1" role="dialog" aria-labelledby="printconfirmodal"
    aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content overflow-hidden">
            <div class="modal-header">
                <a href="" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
                <h4 class="modal-title" id="myModalLabel"><?php echo display('print') ?></h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('invoice_pos_print', array('class' => 'form-vertical', 'id' => '', 'name' => '')) ?>
                <div id="outputs" class="hide alert alert-danger"></div>
                <h3> <?php echo display('successfully_inserted') ?> </h3>
                <h4><?php echo display('do_you_want_to_print') ?> ??</h4>
                <input type="hidden" name="invoice_id" id="inv_id">
                <input type="hidden" name="url" value="<?php echo base_url('gui_pos'); ?>">
            </div>
            <div class="modal-footer">
                <button type="button" onclick="cancelprint()" class="btn btn-default"
                    data-dismiss="modal"><?php echo display('no') ?></button>
                <button type="submit" class="btn btn-primary" id="yes"><?php echo display('yes') ?></button>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>

<!-- Keyboard Shortcut -->
<div class="modal fade" id="cheetsheet" tabindex="-1" role="dialog" aria-labelledby="cheetsheet" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content overflow-hidden">
            <div class="modal-header">
                <a href="" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
                <h4 class="modal-title">Keyboard Shortcut</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>key</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">Submit Invoice</td>
                            <td class="text-center">ctrl+s</td>
                        </tr>
                        <tr>
                            <td class="text-center">Add New Customer</td>
                            <td class="text-center">shif+c</td>
                        </tr>
                        <tr>
                            <td class="text-center">Full Paid</td>
                            <td class="text-center">shif+f</td>
                        </tr>
                        <tr>
                            <td class="text-center">Today's Sale List</td>
                            <td class="text-center">shif+l</td>
                        </tr>
                        <tr>
                            <td class="text-center">New Sale</td>
                            <td class="text-center">shif+n</td>
                        </tr>
                        <tr>
                            <td class="text-center">Open Calculator</td>
                            <td class="text-center">alt+c</td>
                        </tr>
                        <tr>
                            <td class="text-center">Search Old Customer</td>
                            <td class="text-center">alt+n</td>
                        </tr>
                        <tr>
                            <td class="text-center">Invoice Discount</td>
                            <td class="text-center">ctrl+d</td>
                        </tr>
                        <tr>
                            <td class="text-center">Shipping Cost</td>
                            <td class="text-center">alt+s</td>
                        </tr>
                        <tr>
                            <td class="text-center">Paid Amount</td>
                            <td class="text-center">alt+p</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>



<script src="<?php echo base_url() ?>assets/js/perfect-scrollbar.min.js"></script>


<script>
$('.product-grid').each(function() {
    const ps = new PerfectScrollbar($(this)[0]);
});

function getDraftList()
{
        // Ajax call for draf list showing invoice_id
        $.ajax({
        url: 'invoice/invoice/get_invoice_id', // replace with actual URL
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            const $dropdown = $('#draft_list');
            $dropdown.empty(); // Clear existing options
            $dropdown.append('<option value="">Select Draft Invoice</option>');

            if (response.length > 0) {
                response.forEach(function(invoice) {
                    $dropdown.append(
                        `<option value="${invoice.invoice_id}">${invoice.invoice_id}</option>`
                        );
                });
            } else {
                $dropdown.append('<option value="">No drafts found</option>');
            }
        },
        error: function() {
            $('#draft_list').html('<option value="">Error loading drafts</option>');
        }
    });
}

$(document).ready(function() {

    getDraftList();

    $(".btn-check").click(function() {
        $(".btn-check").removeClass("active");
        $(this).addClass("active");
    });

});
</script>

<script>
document.querySelectorAll('.payment_type_wrapper button').forEach(button => {
    button.addEventListener('click', function() {
        // Remove 'active' class from all buttons
        document.querySelectorAll('.payment_type_wrapper button').forEach(btn => btn.classList.remove(
            'active'));
        this.classList.add('active');

        // Hide all inputs first
        document.querySelectorAll('.payment_amount').forEach(input => {
            input.classList.add('d_none');
        });

        // Show and focus the target input
        const targetInput = document.querySelector(this.getAttribute('data-target'));
        if (targetInput) {
            targetInput.classList.remove('d_none');
            targetInput.focus();
        }
    });
});

function setSaleType(type) {
    let grandTotal = document.getElementById("grandTotal").value;
    document.getElementById("total_payable").innerHTML = grandTotal;
    document.getElementById('sale_type').value = type;
}

// function total_payable_cash() {
//     let grandTotal = parseFloat(document.getElementById("grandTotal").value) || 0;
//     let cash_amount = parseFloat(document.getElementById("payment-type-cash").value) || 0;
//     let totalPayable = document.getElementById("total_payable");
//     let totalPayableAmount = document.getElementById("total_payable").textContent;
//     console.log("total-payable", totalPayableAmount);
//     console.log("cash-amount", cash_amount);
//     console.log("payable-amount", totalPayableAmount - cash_amount);
//     totalPayable.innerHTML = totalPayableAmount - cash_amount;
//     // totalPayable.innerHTML = due.toFixed(2); // Show 2 decimal places
// }

// function total_payable_card() {
//     let grandTotal = parseFloat(document.getElementById("grandTotal").value) || 0;
//     let card_amount = parseFloat(document.getElementById("payment-type-card").value) || 0;
//     let totalPayable = document.getElementById("total_payable");
//     let due = grandTotal - card_amount;
//     totalPayable.innerHTML = due.toFixed(2); // Show 2 decimal places
// }

function update_total_payable() {
    let grandTotal = parseFloat(document.getElementById("grandTotal").value) || 0;
    let cash_amount = parseFloat(document.getElementById("payment-type-cash").value) || 0;
    let card_amount = parseFloat(document.getElementById("payment-type-card").value) || 0;

    let totalPayable = document.getElementById("total_payable");

    let due = grandTotal - (cash_amount + card_amount);
    if (due < 0) due = 0; // Prevent negative due

    totalPayable.innerHTML = due.toFixed(2); // Show 2 decimal places

}


// $('#draft_list').on('change', function () {
//     const selectedInvoiceId = $(this).val();
//     console.log(selectedInvoiceId);
//     if (selectedInvoiceId) {
//         $.ajax({
//             url: 'invoice/invoice/get_invoice_data', // Update with actual route if needed
//             type: 'GET',
//             data: { invoice_id: selectedInvoiceId },
//             dataType: 'json',
//             success: function (response) {
//                 // Handle the response data here
//                 console.log('Invoice:', response.invoice);
//                 console.log('Invoice Details:', response.details);

//                 // You can now use this data to fill form fields, a table, etc.
//             },
//             error: function () {
//                 alert('Error fetching invoice data.');
//             }
//         });
//     }
// });


$('#draft_list').on('change', function() {
    const selectedInvoiceId = $(this).val();
    if (selectedInvoiceId) {
        $.ajax({
            url: 'invoice/invoice/get_invoice_data', // Update with actual route if needed
            type: 'GET',
            data: {
                invoice_id: selectedInvoiceId
            },
            dataType: 'json',
            success: function(response) {
                console.log('Invoice:', response.invoice);
                console.log('Invoice Details:', response.details);

                const invoice = response.invoice;
                const details = response.details;

                // First clear existing rows
                $('.addinvoiceItem').html('');

                // Populate the product table
                details.forEach(function(item) {
                    const product_id = item.product_id || '';
                    const product_name = item.product_name || '';
                    const product_model = item.product_model || '';
                    const batch_id = item.batch_id || '';
                    const available_quantity = item.available_quantity || '';
                    const quantity = item.quantity || '0';
                    const rate = parseFloat(item.rate || 0).toFixed(2);
                    const total_price = parseFloat(item.total_price || 0).toFixed(2);
                    const product_vat = item.vat_percent || '0';
                    const unit = item.unit || '';
                    const image = item.image || '';

                    const tr = `
        <tr id="row_${product_id}">
            <td class="text-center">
                <button type="button" data-toggle="modal" onclick="updateProductModal('${product_name}','${available_quantity}','${product_vat}','${unit}','${rate}','${product_id}')" data-target="#updated_product" 
                    class="btn_edit text-center">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                </button>
            </td>
            <td>
                <input type="text" name="product_name" onkeypress="invoice_productList('${product_id}');" class="form-control product_name_td productSelection" 
                    value="${product_name} - (${product_model})" placeholder="Product Name" readonly>
                <input type="hidden" class="form-control autocomplete_hidden_value product_id_${product_id}" name="product_id[]" id="SchoolHiddenId_${product_id}" value="${product_id}"/>
            </td>
            <td>
                <select name="issue_location_id[]" class="form-control issue_location_td" data-role="issue-location">
                    <option value="">Select Location</option>
                </select>
            </td>
            <td>
                <input type="text" name="available_quantity[]" class="form-control available_quantity_td text-right available_quantity_${product_id}" 
                    value="${available_quantity}" readonly id="available_quantity_${product_id}"/>
            </td>
            <td>
                <input type="text" name="product_quantity[]" onkeyup="quantity_calculate('${product_id}');" onchange="quantity_calculate('${product_id}');" 
                    class="total_qntt_${product_id} form-control qty_td text-right" id="total_qntt_${product_id}" placeholder="0.00" min="0" value="${quantity}" required/>
            </td>
            <td style="width:85px">
                <input type="text" name="product_rate[]" onkeyup="quantity_calculate('${product_id}');" onchange="quantity_calculate('${product_id}');" 
                    value="${rate}" id="price_item_${product_id}" class="price_item1 form-control price_td text-right" required placeholder="0.00" min="0"/>
            </td>
            <td style="display:none">
                <input type="text" name="discount[]" onkeyup="quantity_calculate('${product_id}');" onchange="quantity_calculate('${product_id}');" 
                    id="discount_${product_id}" class="form-control text-right" placeholder="0.00" min="0"/>
            </td>
            <td style="display:none">
                <input type="text" name="discountvalue[]" id="discount_value_${product_id}" class="form-control text-right" placeholder="0.00" min="0" readonly/>
            </td>
            <td style="display:none">
                <input type="text" name="vatpercent[]" onkeyup="quantity_calculate('${product_id}');" onchange="quantity_calculate('${product_id}');" 
                    id="vat_percent_${product_id}" value="${product_vat}" class="form-control text-right" placeholder="0.00" min="0"/>
            </td>
            <td style="display:none">
                <input type="text" name="vatvalue[]" id="vat_value_${product_id}" class="form-control text-right total_vatamnt" placeholder="0.00" min="0" readonly/>
            </td>
            <td class="text-right" style="width:100px">
                <input class="total_price form-control total_price_td text-right" type="text" name="total_price[]" id="total_price_${product_id}" value="${total_price}" tabindex="-1" readonly/>
            </td>
            <td>
                <input type="hidden" id="total_discount_${product_id}"/>
                <input type="hidden" id="all_discount_${product_id}" class="total_discount dppr"/>
                <a style="text-align: right;" class="btn btn-danger btn-xs" href="#" onclick="deleteRow(this, '${product_id}')">
                    <i class="fa fa-close"></i>
                </a>
                <a style="text-align: right;" class="btn btn-success btn-xs" href="#" onclick="detailsmodal('${product_name}','${available_quantity}','${product_model}','${unit}','${rate}','${image}')">
                    <i class="fa fa-eye"></i>
                </a>
            </td>
        </tr>
        `;

                    $('.addinvoiceItem').append(tr);
                });

                // Now fill the summary fields
                $('#total_discount_ammount').val(parseFloat(invoice.total_discount || 0).toFixed(
                2));
                $('#grandTotal').val(parseFloat(invoice.total_amount || 0).toFixed(2));
                $('#total_vat_amnt').val(parseFloat(invoice.total_vat || 0).toFixed(2));
                $('#previous').val(parseFloat(invoice.previous || 0).toFixed(2));
                $('#shipping_cost_input').val(parseFloat(invoice.shipping_cost || 0).toFixed(2));

                // If Item count field exists
                $('.vat_summary strong:contains("Item")').next().html('<strong>' + details.length +
                    '</strong>');
            },

            error: function() {
                alert('Error fetching invoice data.');
            }
        });
    }
});
</script>