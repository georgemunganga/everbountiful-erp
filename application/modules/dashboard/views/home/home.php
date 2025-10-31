<?php 
if(isset($_POST['btnSearch']))
{
   $postdate = $_POST['alldata'];
}
$searchdate =(!empty($postdate)?$postdate:date('F Y'));

// Range helpers from controller
$selected_range       = isset($selected_range) ? $selected_range : 'this_month';
$selected_range_label = isset($selected_range_label) ? $selected_range_label : 'This Month';
$range_start          = isset($range_start) ? $range_start : date('Y-m-01');
$range_end            = isset($range_end) ? $range_end : date('Y-m-t');
?>

<div class="row dash-toolbar">
    <div class="col-xs-12">
        <div class="btn-group" role="group" aria-label="Date filters">
            <?php 
            $filters = [
                'today'       => 'Today',
                'last_week'   => 'Last Week',
                'this_month'  => 'This Month',
                'last_month'  => 'Last Month',
                'this_year'   => 'This Year',
                'last_year'   => 'Last Year',
            ];
            foreach ($filters as $key => $label): 
                $active = ($selected_range === $key) ? 'btn-primary' : 'btn-default';
            ?>
            <a class="btn <?php echo $active; ?>" href="<?php echo base_url('home?range=' . $key); ?>"><?php echo html_escape($label); ?></a>
            <?php endforeach; ?>
        </div>
        <form class="form-inline" method="get" action="<?php echo base_url('home'); ?>" style="display:inline-block; margin-left:15px;">
            <label style="margin-right:6px; font-weight:normal; color:#6b7280;">From</label>
            <input type="date" class="form-control" name="from" value="<?php echo html_escape($range_start); ?>" style="height:30px; padding:2px 6px;">
            <label style="margin:0 6px; font-weight:normal; color:#6b7280;">To</label>
            <input type="date" class="form-control" name="to" value="<?php echo html_escape($range_end); ?>" style="height:30px; padding:2px 6px;">
            <button type="submit" class="btn btn-primary btn-sm" style="margin-left:6px; height:30px; line-height:1.4; padding:4px 10px;">
                <?php echo html_escape(display('filter') ?: 'Filter'); ?>
            </button>
            <a href="<?php echo base_url('home'); ?>" class="btn btn-default btn-sm" style="margin-left:6px; height:30px; line-height:1.4; padding:4px 10px;">Reset</a>
        </form>
        <div class="text-muted" style="display:inline-block; margin-left:10px; vertical-align:middle;">
            Showing: <?php echo html_escape($selected_range_label); ?> (<?php echo html_escape($range_start); ?> to <?php echo html_escape($range_end); ?>)
        </div>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="small-box bg-green whitecolor">
            <div class="inner">
                <h4><span class="count-number"><?php echo html_escape($total_customer) ?></span></h4>
                <p><?php echo display('total_customer')?></p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <?php if($this->permission1->method('manage_customer','read')->access()){ ?>
            <a href="<?php echo base_url('customer_list') ?>"
                class="small-box-footer"><?php echo display('total_customer')?></a>
            <?php }else{?>
            <a href="javascript:void(0)" class="small-box-footer"><?php echo display('total_customer')?></a>
            <?php }?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="small-box bg-green whitecolor">
            <div class="inner">
                <h4><span class="count-number"><?php echo html_escape($total_product) ?></span></h4>

                <p><?php echo display('total_product')?></p>
            </div>
            <div class="icon">
                <i class="fa fa-shopping-bag"></i>
            </div>
            <?php if($this->permission1->method('manage_product','read')->access()){ ?>
            <a href="<?php echo base_url('product_list') ?>"
                class="small-box-footer"><?php echo display('total_product')?></a>
            <?php }else{?>
            <a href="javascript:void(0)" class="small-box-footer"><?php echo display('total_product')?></a>
            <?php }?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="small-box bg-green whitecolor">
            <div class="inner">
                <h4><span class="count-number"><?php echo html_escape($total_suppliers)?></span></h4>

                <p><?php echo display('total_supplier')?></p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
            <?php if($this->permission1->method('manage_supplier','read')->access()){ ?>
            <a href="<?php echo base_url('supplier_list') ?>"
                class="small-box-footer"><?php echo display('total_supplier')?> </a>
            <?php }else{?>
            <a href="javascript:void(0)" class="small-box-footer"><?php echo display('total_supplier')?></a>
            <?php }?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-3">
        <div class="small-box bg-green whitecolor">
            <div class="inner">
                <h4><span class="count-number"><?php echo html_escape($total_sales) ?></span> </h4>

                <p><?php echo display('total_invoice')?></p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
            <?php if($this->permission1->method('manage_invoice','read')->access()){ ?>
            <a href="<?php echo base_url('invoice_list') ?>"
                class="small-box-footer"><?php echo display('total_invoice')?> </a>
            <?php }else{?>
            <a href="javascript:void(0)" class="small-box-footer"><?php echo display('total_invoice')?></a>
            <?php }?>
        </div>
    </div>
</div>
<hr>

<?php if ($this->session->userdata('isAdmin')){?>

<style>
/* Mini stat cards (compact, 5 per row on large screens) */
.mini-stat-grid { display:flex; flex-wrap:wrap; margin:-8px; }
.mini-stat-card {
    background:#fff; border:1px solid #e5e7eb; border-radius:0px; padding:12px 14px 40px 14px; margin:8px;
    box-shadow:0 1px 2px rgba(0,0,0,0.05); flex:1 1 calc(20% - 16px); min-width:210px; position:relative;
}
.mini-stat-card .msc-title { font-size:12px; color:#6b7280; margin-bottom:4px; text-transform:uppercase; letter-spacing:.04em; font-weight: bold; }
.mini-stat-card .msc-value { font-size:22px; font-weight:700; color:#111827; }
.mini-stat-card .msc-sub { font-size:12px; color:#374151; margin-top:2px; }
.mini-stat-card .msc-icon { position:absolute; right:12px; bottom:10px; top:auto; color:#9ca3af; font-size:18px; }
.mini-stat-card .msc-link { display:inline-block; margin-top:6px; font-size:12px; color:#319000; text-decoration:none; }
.mini-stat-card .msc-link:hover { text-decoration:underline; }
.mini-stat-card.accent-blue { border-top:3px solid #319000; }
.mini-stat-card.accent-green { border-top:3px solid #fec802; }
.mini-stat-card.accent-rose { border-top:3px solid #319000; }
.mini-stat-card.accent-amber { border-top:3px solid #f59e0b; }
.mini-stat-card.accent-purple { border-top:3px solid #8b5cf6; }
/* Vertical spacing helpers */
.dash-toolbar { margin-bottom: 12px; }
.dash-toolbar .btn-group { margin-bottom: 8px; }
.mini-stat-grid { margin-top: 8px; margin-bottom: 16px; }
.panel.panel-bd { margin-top: 12px; }
@media (max-width: 1199px) { .mini-stat-card { flex:1 1 calc(25% - 16px); } }
@media (max-width: 991px) { .mini-stat-card { flex:1 1 calc(33.333% - 16px); } }
@media (max-width: 767px) { .mini-stat-card { flex:1 1 calc(50% - 16px); } }
@media (max-width: 479px) { .mini-stat-card { flex:1 1 100%; } }
</style>

<div class="mini-stat-grid">
    <!-- Total Sales (range) -->
    <div class="mini-stat-card accent-blue">
        <i class="msc-icon fa fa-line-chart"></i>
        <div class="msc-title"><?php echo display('total_sales') ?: 'Total Sales'; ?> — <?php echo html_escape($selected_range_label); ?></div>
        <div class="msc-value"><?php echo html_escape($total_sales_count_range ?? 0); ?></div>
        <div class="msc-sub">
            <?php 
                $amt = isset($total_sales_amount_range) ? $total_sales_amount_range : '0.00';
                $label = display('amount') ?: 'Amount';
                echo html_escape($label . ': ' . (($position == 0) ? "$currency $amt" : "$amt $currency"));
            ?>
        </div>
        <a class="msc-link" href="<?php echo base_url('sales_report'); ?>"><?php echo display('see_all') ?: 'See all'; ?></a>
    </div>

    <!-- Total Production (range) -->
    <div class="mini-stat-card accent-green">
        <i class="msc-icon fa fa-industry"></i>
        <div class="msc-title"><?php echo html_escape(display('productions') ?: 'Total Production'); ?> — <?php echo html_escape($selected_range_label); ?></div>
        <div class="msc-value"><?php echo html_escape($total_production_range ?? 0); ?></div>
        <?php if (!empty($latest_consumption)) { ?>
        <div class="msc-sub">
            <?php 
                $lc_name = $latest_consumption['product_name'] ?? '';
                $lc_qty  = isset($latest_consumption['quantity_out']) ? (float)$latest_consumption['quantity_out'] : '';
                $lc_unit = $latest_consumption['unit_name'] ?? '';
                if ($lc_name !== '') {
                    echo 'Latest consumed: ' . html_escape($lc_name) . ' ' . html_escape($lc_qty) . ' ' . html_escape($lc_unit);
                }
            ?>
        </div>
        <?php } ?>
        <a class="msc-link" href="<?php echo base_url('productions'); ?>"><?php echo display('see_all') ?: 'See all'; ?></a>
    </div>

    <!-- Total Mortality (range) -->
    <div class="mini-stat-card accent-rose">
        <i class="msc-icon fa fa-heartbeat"></i>
        <div class="msc-title"><?php echo html_escape(display('mortality') ?: 'Total Mortality'); ?> — <?php echo html_escape($selected_range_label); ?></div>
        <div class="msc-value"><?php echo html_escape($total_mortality_range ?? 0); ?></div>
        <a class="msc-link" href="<?php echo base_url('productions'); ?>"><?php echo display('see_all') ?: 'See all'; ?></a>
    </div>

    <!-- Total Feed Stock (snapshot) -->
    <div class="mini-stat-card accent-green">
        <i class="msc-icon fa fa-cubes"></i>
        <div class="msc-title"><?php echo html_escape(display('total_instock') ?: 'Total Feed Stock'); ?></div>
        <div class="msc-value"><?php echo html_escape($total_feed_instock ?? 0); ?></div>
        <a class="msc-link" href="<?php echo base_url('feed-usages'); ?>"><?php echo display('see_all') ?: 'See all'; ?></a>
    </div>

    <!-- Available Products (current availability + last two bought) -->
    <div class="mini-stat-card accent-rose">
        <i class="msc-icon fa fa-archive"></i>
        <div class="msc-title"><?php echo html_escape(display('total_product') ?: 'Available Products'); ?></div>
        <div class="msc-value"><?php echo html_escape($available_products_count ?? 0); ?></div>
        <?php if (!empty($last_two_purchased)) { 
            $names = array();
            foreach ($last_two_purchased as $p) { if (!empty($p['product_name'])) { $names[] = $p['product_name']; } }
            if (!empty($names)) { ?>
                <div class="msc-sub"><?php echo 'Last bought: ' . html_escape(implode(', ', array_slice($names, 0, 2))); ?></div>
        <?php } } ?>
        <a class="msc-link" href="<?php echo base_url('stock/consumption'); ?>"><?php echo display('see_all') ?: 'See all'; ?></a>
    </div>
</div>
<div class="row d-flex flex-wrap">
    <!-- This month progress -->
    <div class="col-sm-12 col-md-7 d-flex">
        <div class="panel panel-bd flex-fill w-100">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 class="best-sale-title"> <?php echo display('best_sale_product') ?></h4>
                    <a href="<?php echo base_url(); ?>dashboard/home/see_all_best_sales"
                        class="btn btn-success text-white best-sale-seeall">See All</a>
                </div>
            </div>
            <div class="panel-body">
                <canvas id="lineChart" height="160"></canvas>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-5 d-flex">
        <div class="panel panel-bd flex-fill w-100">
            <div class="panel-heading">
                <?php echo form_open_multipart('','name="form1" id="form1"')?>
                <div class="row">
                    <div class="col-sm-10 marginpadding-right0">
                        <input type="text" class="form-control " value="<?php echo $searchdate;?>" name="alldata"
                            id="alldata">
                    </div>
                    <div class="col-sm-2 marginpaddingleft0">
                        <button type="submit" name="btnSearch" class="btn filterbutton"><i class="fa fa-search"></i>
                            <?php echo display('filter')?></button>
                    </div>
                </div>
                <?php echo form_close();?>
            </div>
            <div class="panel-body">
                <canvas id="expense_statement_pie_chart"></canvas>
                <!-- <div id="chartContainer" class="piechartcontainer"></div> -->
            </div>
        </div>
    </div>

    <div class="col-md-8 d-flex">
        <div class="panel panel-bd flex-fill w-100">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 class="charttitle"></h4>
                </div>
                <div class="panel-body">
                    <canvas id="yearlyreport" width="600" height="350"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Total Report -->
    <div class="col-md-4 d-flex">
        <div class="panel panel-bd lobidisable flex-fill w-100">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('todays_overview') ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="message_inner">
                    <div class="message_widgets">

                        <table class="table table-bordered table-striped table-hover">
                            <tr>
                                <th><?php echo display('todays_report') ?></th>
                                <th><?php echo display('money') ?></th>
                            </tr>
                            <tr>
                                <th><?php echo display('total_sales') ?></th>
                                <td><?php echo html_escape((($position == 0) ? "$currency $sales_amount" : "$sales_amount $currency")) ?>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo display('total_purchase') ?></th>
                                <td><?php echo html_escape((($position == 0) ? "$currency $todays_total_purchase" : "$todays_total_purchase $currency")) ?>
                                </td>
                            </tr>

                        </table>

                        <table class="table table-bordered table-striped table-hover">
                            <tr>
                                <th><?php echo display('last_sales') ?></th>
                                <th><?php echo display('money') ?></th>
                            </tr>
                            <?php
                                        if ($todays_sale_product) {
                                            ?>
                            <?php foreach($todays_sale_product as $tsale){?>
                            <tr>
                                <th><?php echo $tsale['product_name']?></th>
                                <td><?php echo (($position == 0) ? $currency.' '.$tsale['price'] : $tsale['price'].' '.$currency) ?>
                                </td>
                            </tr>

                            <?php }} ?>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- sales due -->
    <div class="col-sm-12 col-md-6 d-flex">
        <div class="panel panel-bd flex-fill w-100">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 class="charttitle"> <?php echo display('sales_due') ?></h4>
                    <a href="<?php echo base_url(); ?>invoice_wise_due_report"
                        class="btn btn-success text-white best-sale-seeall">See All</a>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive todayssaletitle">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th><?php echo display('customer_name') ?></th>
                                <th><?php echo display('voucher_no') ?></th>
                                <th><?php echo display('due_amount') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $ttl_due =0;
                                $ttlpdue_amount =0;
                            if ($todays_sales_due) {
                            $sl = 0;
                            
                            foreach ($todays_sales_due as $sales_due) {
                            $sl++;
                            ?>
                            <tr>
                                <td><?php echo $sl; ?></td>
                                <td> <?php echo html_escape($sales_due->customer_name); ?></td>
                                <td>
                                    <a
                                        href="<?php echo base_url() . 'invoice_details/'; ?><?php echo html_escape($sales_due->invoice_id); ?>">
                                        <?php echo html_escape($sales_due->invoice); ?>
                                    </a>
                                </td>
                                <td class="text-right">
                                    <?php
                                    $ttl_due += $sales_due->due_amount; 
                                    echo html_escape(number_format($sales_due->due_amount, '2','.',',')); 
                                    ?>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                ?>
                            <tr>
                                <th class="text-center" colspan="5"><?php echo display('not_found'); ?></th>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" align="right">&nbsp;<b><?php echo display('total') ?>:</b></td>
                                <td class="text-right">
                                    <?php
                                        $ttl_dueamount_float = html_escape(number_format($ttl_due, '2', '.',','));
                                        echo (($position == 0) ? "$currency $ttl_dueamount_float" : "$ttl_dueamount_float $currency"); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Purchase due -->
    <div class="col-sm-12 col-md-6 d-flex">
        <div class="panel panel-bd flex-fill w-100">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 class="charttitle"> <?php echo display('purchase_due') ?></h4>
                    <a href="<?php echo base_url(); ?>purchase_report"
                        class="btn btn-success text-white best-sale-seeall">See All</a>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive todayssaletitle">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th><?php echo display('supplier_name') ?></th>
                                <th><?php echo display('purchase_id') ?></th>
                                <th><?php echo display('due_amount') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ttl_amount = $ttl_paid = $ttl_due = $ttl_discout = $ttl_receipt = 0;
                            $todays = date('Y-m-d');
                            if ($todays_purchase_due) {
                            $sl = 0;
                            foreach ($todays_purchase_due as $purchase_due) {
                            $sl++;
                            ?>
                            <tr>
                                <td><?php echo $sl; ?></td>
                                <td><?php echo html_escape($purchase_due->supplier_name); ?></td>
                                <td>
                                    <a
                                        href="<?php echo base_url() . 'purchase_details/'; ?><?php echo html_escape($purchase_due->purchase_id); ?>">
                                        <?php echo html_escape($purchase_due->purchase_id); ?>
                                    </a>
                                </td>
                                <td class="text-right">
                                    <?php
                                    $ttlpdue_amount += $purchase_due->due_amount; 
                                    echo html_escape(number_format($purchase_due->due_amount, '2','.',',')); 
                                    ?>
                                </td>
                            </tr>
                            <?php
                                }
                            } else {
                                ?>
                            <tr>
                                <th class="text-center" colspan="5"><?php echo display('not_found'); ?></th>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" align="right">&nbsp;<b><?php echo display('total') ?>:</b></td>
                                <td class="text-right">
                                    <?php
                                    $ttlpdue_amount_float = html_escape(number_format($ttlpdue_amount, '2', '.',','));
                                    echo (($position == 0) ? "$currency $ttlpdue_amount_float" : "$ttlpdue_amount_float $currency"); ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- This today transaction progress -->
    <div class="col-sm-12 col-md-12 d-flex">
        <div class="panel panel-bd flex-fill w-100">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 class="charttitle"> <?php echo display('todays_sales_report') ?></h4>
                    <a href="<?php echo base_url(); ?>sales_report"
                        class="btn btn-success text-white best-sale-seeall">See All</a>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive todayssaletitle">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th><?php echo display('customer_name') ?></th>
                                <th><?php echo display('invoice_no') ?></th>
                                <th><?php echo display('total_amount') ?></th>
                                <th><?php echo display('paid_ammount') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                         $ttl_amount = $ttl_paid = $ttl_due = $ttl_discout = $ttl_receipt = 0;
                                        $todays = date('Y-m-d');
                                        if ($todays_sales_report) {
                                            $sl = 0;
                     foreach ($todays_sales_report as $single) {
                     

                                                $sl++;
                                                ?>
                            <tr>
                                <td><?php echo $sl; ?></td>
                                <td>

                                    <?php echo html_escape($single->customer_name); ?>

                                </td>
                                <td>
                                    <a
                                        href="<?php echo base_url() . 'invoice_details/'; ?><?php echo html_escape($single->invoice_id); ?>">
                                        <?php echo html_escape($single->invoice); ?>
                                    </a>
                                </td>
                                <td class="text-right">
                                    <?php
                                                        $ttl_amount += $single->total_amount; 
                                                        echo html_escape(number_format($single->total_amount, '2','.',',')); 
                                                        ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                                        $ttl_paid += $single->paid_amount;
                                                        echo html_escape(number_format($single->paid_amount, '2', '.', ',')); ?>
                                </td>





                            </tr>
                            <?php
                                            }
                                        } else {
                                            ?>
                            <tr>
                                <th class="text-center" colspan="5"><?php echo display('not_found'); ?></th>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>

                            <tr>
                                <td colspan="3" align="right">&nbsp;<b><?php echo display('total') ?>:</b></td>
                                <td class="text-right">
                                    <?php
                                                $ttl_amount_float = html_escape(number_format($ttl_amount, '2', '.',','));
                                                echo (($position == 0) ? "$currency $ttl_amount_float" : "$ttl_amount_float $currency"); ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                                $ttl_paid_float = html_escape(number_format($ttl_paid, '2', '.',','));
                                                echo (($position == 0) ? "$currency $ttl_paid_float" : "$ttl_paid_float $currency"); ?>
                                </td>



                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>


        </div>

    </div>

    <input type="hidden" id="totalsalep"
        value="<?php echo html_escape($this->home_model->total_sales_amount($searchdate) ?? 0); ?>" name="">
    <input type="hidden" id="totalplurchasep"
        value="<?php echo html_escape($this->home_model->total_purchase_amount($searchdate) ?? 0); ?>" name="">
    <input type="hidden" id="totalexpensep"
        value="<?php echo html_escape($this->home_model->total_expense_amount($searchdate) ?? 0); ?>" name="">
    <input type="hidden" id="totalemployeesalaryp"
        value="<?php echo html_escape($this->home_model->total_employee_salary($searchdate) ?? 0); ?>" name="">
    <input type="hidden" id="totalservicep"
        value="<?php echo html_escape($this->home_model->total_service_amount($searchdate) ?? 0); ?>" name="">
    <input type="hidden" id="month" value="<?php echo html_escape($month ?? ''); ?>" name="">
    <input type="hidden" id="tlvmonthsale" value="<?php echo html_escape($tlvmonthsale ?? 0); ?>" name="">
    <input type="hidden" id="tlvmonthpurchase" value="<?php echo html_escape($tlvmonthpurchase ?? 0); ?>" name="">
    <input type="hidden" id="salspurhcaselabel"
        value="<?php echo display("sales_and_purchase_report_summary") . ' - ' . date("Y"); ?>" name="">
    <input type="hidden" id="bestsalelabel" value='<?php echo html_escape($chart_label ?? ''); ?>' name="">
    <input type="hidden" id="bestsaledata" value='<?php echo html_escape($chart_data ?? ''); ?>' name="">

    <?php 
$seperatedData = explode(',', $chart_data ?? '0'); 
$value = isset($seperatedData[0]) && is_numeric($seperatedData[0]) ? $seperatedData[0] + 10 : 10; 
?>
    <input type="hidden" value='<?php echo html_escape($value); ?>' name="" id="bestsalemax">

</div>
<?php }?>



<script src="<?php echo base_url() ?>assets/js/Chart.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/js/canvasjs.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/js/dashboard.js" type="text/javascript"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('expense_statement_pie_chart');

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Total Sale', 'Total Purchase', 'Total Expense', 'Employee Salary', 'Service'],
        datasets: [{
            label: 'Expense Statement',
            data: [200, 120, 100, 150, 85],
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(15, 165, 127)',
                'rgb(75, 192, 192)',
            ],
            hoverOffset: 4,
        }, ],
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Expense Statement'
            }
        }
    },
});
</script>
