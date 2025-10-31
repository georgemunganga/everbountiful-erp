 <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo $title ?> </h4>
                        </div>
                    </div>
                   
                    <div class="panel-body">
                        <style>
                        .mini-stat-grid { display:flex; flex-wrap:wrap; margin:-6px; }
                        .mini-stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:0; padding:8px 12px 28px 12px; margin:6px; box-shadow:0 1px 1px rgba(0,0,0,0.04); flex:1 1 calc(33.333% - 12px); min-width:200px; position:relative; }
                        .mini-stat-card .msc-title { font-size:11px; color:#6b7280; margin-bottom:2px; text-transform:uppercase; letter-spacing:.04em; font-weight:600; }
                        .mini-stat-card .msc-value { font-size:18px; font-weight:700; color:#111827; line-height:1.1; }
                        .mini-stat-card .msc-icon { position:absolute; right:10px; bottom:8px; color:#9ca3af; font-size:16px; }
                        .mini-stat-card .msc-link { display:inline-block; margin-top:6px; font-size:12px; color:#319000; text-decoration:none; }
                        .mini-stat-card .msc-link:hover { text-decoration:underline; }
                        .mini-stat-card.accent-blue { border-top:3px solid #319000; }
                        .mini-stat-card.accent-rose { border-top:3px solid #319000; }
                        .mini-stat-card.accent-amber { border-top:3px solid #f59e0b; }
                        @media (max-width: 991px) { .mini-stat-card { flex:1 1 calc(50% - 12px); } }
                        @media (max-width: 575px) { .mini-stat-card { flex:1 1 100%; } }
                        </style>

                        <div class="mini-stat-grid" style="margin-bottom:12px;">
                            <div class="mini-stat-card accent-blue">
                                <i class="msc-icon fa fa-users"></i>
                                <div class="msc-title"><?php echo html_escape(display('total_customer') ?: 'Total Customers'); ?></div>
                                <div class="msc-value"><?php echo html_escape($total_customer ?? 0); ?></div>
                                <!-- <a class="msc-link" href="<?php echo base_url('customer_list'); ?>"><?php echo display('see_all') ?: 'See all'; ?></a> -->
                            </div>
                            <div class="mini-stat-card accent-rose">
                                <i class="msc-icon fa fa-exclamation-circle"></i>
                                <div class="msc-title"><?php echo html_escape('Owing Customers'); ?></div>
                                <div class="msc-value"><?php echo html_escape($customers_owing_count ?? 0); ?></div>
                                <!-- <a class="msc-link" href="<?php echo base_url('credit_customer'); ?>"><?php echo display('see_all') ?: 'See all'; ?></a> -->
                            </div>
                            <div class="mini-stat-card accent-amber">
                                <i class="msc-icon fa fa-credit-card"></i>
                                <div class="msc-title"><?php echo html_escape('Customers You Owe'); ?></div>
                                <div class="msc-value"><?php echo html_escape($customers_you_owe_count ?? 0); ?></div>
                                <!-- <a class="msc-link" href="<?php echo base_url('paid_customer'); ?>"><?php echo display('see_all') ?: 'See all'; ?></a> -->
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px;">
                            <div class="col-sm-6">
                                <a href="<?php echo base_url('invoice_wise_due_report'); ?>" class="btn btn-warning btn-sm">
                                    <i class="fa fa-exclamation-triangle"></i> Unpaid/Partially Paid Invoices
                                </a>
                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="<?php echo base_url('add_customer'); ?>" class="btn btn-success btn-sm text-white">
                                    <i class="ti-plus"></i> <?php echo html_escape(display('add_customer') ?: 'Add Customer'); ?>
                                </a>
                            </div>
                        </div>
    
<div class="table-responsive">
                <table class="table table-bordered" id="CustomerList"  width="100%">
                    <thead>
 
                        <tr>
                            <th><?php echo display('sl') ?></th>
                            <th><?php echo display('customer_name') ?></th>
                            <th><?php echo display('address1'); ?></th>
                            <th><?php echo display('mobile_no') ?></th>
                            <th><?php echo display('email'); ?></th>
                            <th><?php echo html_escape('Customer Group'); ?></th>
                            <th><?php echo display('zip'); ?></th>
                            <th><?php echo display('country'); ?></th>
                            <th><?php echo display('balance') ?></th>
                            <th width="50px;"><?php echo display('action') ?> 
                            </th>
                        </tr>
                    </thead>
                    <tbody id="customer_tablebody">
                       
                    </tbody>
                    
                  </table>  
              </div>
                  
            </div>
         

        </div>
    </div>
</div>
 


