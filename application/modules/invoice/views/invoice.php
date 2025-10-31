 <!-- date between search -->
 <div class="row">
     <div class="col-sm-12">
         <div class="panel panel-default">
             <div class="panel-body">
                 <div class="col-sm-8">
                     <?php echo form_open('', array('class' => 'form-inline', 'method' => 'get')) ?>
                     <?php

                        $today = date('Y-m-d');
                        ?>
                     <div class="form-group">
                         <label class="" for="from_date"><?php echo display('start_date') ?></label>
                         <input type="text" name="from_date" class="form-control datepicker" id="from_date" value=""
                             placeholder="<?php echo display('start_date') ?>">
                     </div>

                     <div class="form-group">
                         <label class="" for="to_date"><?php echo display('end_date') ?></label>
                         <input type="text" name="to_date" class="form-control datepicker" id="to_date"
                             placeholder="<?php echo display('end_date') ?>" value="">
                     </div>

                     <button type="button" id="btn-filter"
                         class="btn btn-success"><?php echo display('find') ?></button>

                     <?php echo form_close() ?>
                 </div>

                 <div class="col-sm-4 text-right">

                     <span class="newtooltiop" data-toggle="tooltip" data-html="true" data-placement="left"
                         title="** How to show invoice edit option ?<br><br>
                    1. To show new invoice edit button in manage sales please go to 'Settings -> Software Setting -> Settings'. <br><br>

                    2. Then uncheck 'Auto Approve Invoice Voucher' option from the setting list & click on save button.<br><br>

                    3. Then create new invoice now you can edit your new invoice (For this you have to approve all your vouchers manually form Accounts -> Voucher Approval 
                    otherwise you don't get those data in accounts report).<br><br>

                    N:B: Please do not edit any auto generated voucher if you do your system calculations can be wrong.<br>">
                         <i class="fa fa-question-circle fa-2x" aria-hidden="true"></i>
                     </span>
                 </div>


             </div>
         </div>
     </div>
 </div>
 <div class="row">
 </div>
 <!-- Manage Invoice report -->
 <div class="row">
     <div class="col-sm-12">
         <div class="panel panel-bd lobidrag">
             <div class="panel-heading">
                 <div class="panel-title">
                     <span><?php echo display('manage_invoice') ?></span>
                     <span class="padding-lefttitle">
                         <?php if ($this->permission1->method('new_invoice', 'create')->access()) { ?>
                         <a href="<?php echo base_url('add_invoice') ?>" class="btn btn-success m-b-5 m-r-2"><i
                                 class="ti-plus"> </i> <?php echo display('new_invoice') ?> </a>
                         <?php } ?>


                         <?php if ($this->permission1->method('gui_pos', 'create')->access()) { ?>
                         <a href="<?php echo base_url('gui_pos') ?>" class="btn btn-success m-b-5 m-r-2"><i
                                 class="ti-plus"> </i> <?php echo display('pos_invoice') ?> </a>
                         <?php } ?>
                     </span>
                 </div>
             </div>
             <div class="panel-body">
                 <div class="table-responsive">
                     <table class="table table-hover table-bordered" cellspacing="0" width="100%" id="InvList">
                         <thead>
                             <tr>
                                 <th><?php echo display('sl') ?></th>
                                 <th><?php echo display('invoice_no') ?></th>
                                 <th><?php echo display('sale_by') ?></th>
                                 <th><?php echo display('customer_name') ?></th>
                                 <th><?php echo display('date') ?></th>
                                 <th><?php echo display('total_amount') ?></th>
                                 <th class="text-center"><?php echo display('action') ?></th>
                             </tr>
                         </thead>
                         <tbody>

                         </tbody>
                         <tfoot>
                             <th colspan="5" class="text-right"><?php echo display('total') ?>:</th>

                             <th></th>
                             <th></th>
                         </tfoot>
                     </table>

                 </div>


             </div>
         </div>
         <input type="hidden" id="total_invoice" value="<?php echo $total_invoice; ?>" name="">

     </div>

     <div id="add0" class="modal fade" role="dialog">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                     <strong><?php echo display('delivery_note') ?></strong>
                 </div>
                 <div class="modal-body" id="invoice_note_show">


                 </div>


             </div>
         </div>
     </div>
 </div>

 <script type="text/javascript">
 (function ($) {
     'use strict';

     var deleteConfirmMessage = <?php echo json_encode(display('are_you_sure_to_delete')); ?> || 'Are you sure you want to delete?';
     var deleteEndpoint = <?php echo json_encode(base_url('invoice/invoice/bdtask_delete_invoice')); ?>;

     function notify(type, message) {
         if (window.toastr && typeof toastr[type] === 'function') {
             toastr[type](message);
         } else if (type === 'error') {
             alert(message);
         } else {
             console.log(message);
         }
     }

     $(document).on('click', '.js-delete-invoice', function (event) {
         event.preventDefault();
         var $button = $(this);
         var invoiceId = $button.data('invoice');

         if (!invoiceId) {
             return;
         }

         var hasApprovedVoucher = parseInt($button.data('approved'), 10) === 1;
         var hasReturnAdjustment = parseInt($button.data('retadjust'), 10) === 1;

         var confirmationText = deleteConfirmMessage || 'Are you sure you want to delete?';
         if (hasApprovedVoucher && hasReturnAdjustment) {
             confirmationText = 'This invoice has approved vouchers and return adjustments. Deleting it will remove those records as well. Continue?';
         } else if (hasApprovedVoucher) {
             confirmationText = 'This invoice has approved vouchers. Deleting it will remove the approved vouchers. Continue?';
         } else if (hasReturnAdjustment) {
             confirmationText = 'This invoice has return adjustments linked to it. Deleting it will remove those adjustments. Continue?';
         }

         if (!confirm(confirmationText)) {
             return;
         }

         var csrfToken = $('[name="csrf_test_name"]').val();
         var forceDelete = (hasApprovedVoucher || hasReturnAdjustment) ? 1 : 0;
         $button.prop('disabled', true);

         $.ajax({
             url: deleteEndpoint,
             type: 'POST',
             dataType: 'json',
             data: {
                 invoice_id: invoiceId,
                 csrf_test_name: csrfToken,
                 force_delete: forceDelete
             },
             complete: function () {
                 $button.prop('disabled', false);
             },
             success: function (response) {
                 if (response && response.csrf_test_name) {
                     $('[name="csrf_test_name"]').val(response.csrf_test_name);
                 }

                 if (response && response.status) {
                     notify('success', response.message || <?php echo json_encode(display('delete_successfully')); ?>);
                     var table = $('#InvList').DataTable();
                     table.ajax.reload(null, false);
                 } else {
                     notify('error', (response && response.message) || <?php echo json_encode(display('please_try_again')); ?>);
                 }
             },
             error: function () {
                 notify('error', <?php echo json_encode(display('please_try_again')); ?>);
             }
         });
     });
 })(jQuery);
 </script>

 <style>
.tooltip-inner {
    font-size: 14px;
    max-width: 450px !important;
    text-align: left;
}
 </style>
