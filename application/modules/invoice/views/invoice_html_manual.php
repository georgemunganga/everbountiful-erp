<link
    href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap"
    rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap"
    rel="stylesheet">
<style>
    *,
    ::after,
    ::before {
        box-sizing: border-box;
    }

    body {
        padding: 0;
        font-family: Lato, "Helvetica Neue", Arial, Helvetica, sans-serif;
    }
</style>


<div class="row">
    <div class="col-sm-5">
        <div class="panel panel-bd">
            <div id="printableArea">
                <div class="panel-body">
                    <div class="invoice-wrap"
                        style="max-width:272.12598425px;background:#fff;margin-right:auto;margin-left:auto;font-size:14px;color:#5b5b5b">
                        <div style="text-align: center; margin-bottom: 10px;">
                            <div style="border: 1px solid #000;font-weight: 700;font-size: 17px;color: #000;">
                                <?php echo $company_info[0]['company_name'] ?></div>
                        </div>

                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center"
                            style="color: #000;font-size: 11px;margin-bottom: 10px;">

                            <tbody>
                                <tr>
                                    <th style="text-align: left;"><?php echo display('date'); ?></th>
                                    <th style="text-align: right;"><?php echo  $final_date; ?></th>
                                </tr>
                                <tr>
                                    <th style="text-align: left;"><?php echo display('invoice_no'); ?></th>
                                    <th style="text-align: right;"><?php echo $invoice_no; ?></th>
                                </tr>
                                <?php if (!empty($status_label)) { ?>
                                <tr>
                                    <th style="text-align: left;"><?php echo display('status'); ?></th>
                                    <th style="text-align: right;"><?php echo html_escape($status_label); ?></th>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <th style="text-align: left;"><?php echo display('customer'); ?></th>
                                    <th style="text-align: right;"><?php echo $customer_name; ?></th>
                                </tr>
                                <?php if ($customer_mobile != '') { ?>
                                    <tr>
                                        <th style="text-align: left;"><?php echo display('phone'); ?></th>
                                        <th style="text-align: right;"><?php echo $customer_mobile; ?></th>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center"
                            style="color: #000;font-size: 11px;border-collapse: collapse;margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <th style="background-color: #ccc;border: 1px solid #000;">
                                        <div><?php echo display('item'); ?></div>

                                    </th>
                                    <th style="background-color: #ccc;border: 1px solid #000;">
                                        <div><?php echo display('qty2'); ?></div>

                                    </th>
                                    <th style="background-color: #ccc;border: 1px solid #000;">
                                        <div><?php echo display('batch'); ?></div>

                                    </th>
                                    <th style="background-color: #ccc;border: 1px solid #000;">
                                        <div><?php echo display('price'); ?></div>

                                    </th>
                                    <th style="background-color: #ccc;border: 1px solid #000;">
                                        <div><?php echo display('disc'); ?></div>

                                    </th>
                                    <?php if (!empty($invoice_all_data[0]['vat_amnt'])) { ?>
                                        <th style="background-color: #ccc;border: 1px solid #000;">
                                            <div><?php echo display('vat'); ?></div>

                                        </th>
                                    <?php } ?>
                                    <th style="background-color: #ccc;border: 1px solid #000;">
                                        <div><?php echo display('tot_price'); ?></div>

                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="7" style="border: 1px solid #000;">
                                        <div style="position: relative;">
                                            <div style="height: 3px;background-color: #000;width: 99%;margin: auto;">
                                            </div>
                                            <div
                                                style="height: 1px;background-color: #fff;width: 100%;position: absolute;z-index: 9;top: 1px;left: 0;">
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sl = 1;
                                $s_total = 0;
                                $itemrow = 0;
                                $total_price_with_dis = 0;
                                foreach ($invoice_all_data as $invoice_data) { ?>
                                    <tr>
                                        <th style="border: 1px solid #000;">
                                            <?php echo html_escape($invoice_data['product_name']); ?></th>
                                        <th style="border: 1px solid #000;">
                                            <?php echo html_escape($invoice_data['quantity']); ?></th>
                                        <th style="border: 1px solid #000;">
                                            <?php echo html_escape($invoice_data['batch_id']); ?></th>
                                        <th style="border: 1px solid #000;">
                                            <?php echo html_escape($invoice_data['rate']); ?>
                                        </th>
                                        <th style="border: 1px solid #000;">
                                            <?php echo html_escape($invoice_data['discount']); ?></th>
                                        <?php if (!empty($invoice_data['vat_amnt'])) { ?>
                                            <th style="border: 1px solid #000;">
                                                <?php echo html_escape($invoice_data['vat_amnt']); ?>
                                            </th>
                                        <?php } ?>

                                        <th style="border: 1px solid #000;">
                                            <?php echo html_escape($invoice_data['total_price']); ?></th>
                                    </tr>
                                    <?php
                                    $itemrow += $invoice_data['rate'] * $invoice_data['quantity'];
                                    $total_price_with_dis += $invoice_data['total_price'];

                                    ?>
                                    <tr>
                                        <th colspan="7" style="border: 1px solid #000;text-align: center;">

                                            <div>
                                                <?php echo html_escape($invoice_data['product_name']) . '(' . html_escape($invoice_data['product_model']) . ')'; ?>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" style="border: 1px solid #000;">
                                            <div style="position: relative;">
                                                <div style="height: 3px;background-color: #000;width: 99%;margin: auto;">
                                                </div>
                                                <div
                                                    style="height: 1px;background-color: #fff;width: 100%;position: absolute;z-index: 9;top: 1px;left: 0;">
                                                </div>
                                            </div>
                                        </th>
                                    </tr>

                                <?php $sl++;
                                } ?>

                            </tbody>
                        </table>

                        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center"
                            style="color: #000;font-size: 11px;margin-bottom: 20px;">
                            <tbody>
                                <tr>
                                    <th style="text-align: left;"><?php echo number_format($itemrow, 2, '.', ','); ?>
                                    </th>
                                    <th style="text-align: right;padding: 3px 10px 3px 0px">
                                        <?php echo display('tot_before_dis'); ?></th>
                                </tr>
                                <tr>
                                    <th style="text-align: left;"><?php echo $all_discount ?></th>
                                    <th style="text-align: right;padding: 3px 10px 3px 0px">
                                        <?php echo display('discounts'); ?></th>
                                </tr>
                                <tr>
                                    <th style="text-align: left;">
                                        <?php echo number_format($itemrow - $all_discount, 2, '.', ',') ?></th>
                                    <th style="text-align: right;padding: 3px 10px 3px 0px">
                                        <?php echo display('tot_with_dis'); ?></th>
                                </tr>
                                <?php if ($total_vat > 0) { ?>
                                    <tr>
                                        <th style="text-align: left;"><?php echo $total_vat ?></th>
                                        <th style="text-align: right;padding: 3px 10px 3px 0px">
                                            <?php echo display('vat_val'); ?></th>
                                    </tr>
                                <?php }
                                if ($total_tax > 0) { ?>
                                    <tr>
                                        <th style="text-align: left;"><?php echo $total_tax ?></th>
                                        <th style="text-align: right;padding: 3px 10px 3px 0px">
                                            <?php echo display('tax_vat'); ?></th>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <th style="text-align: left;"><span
                                            style="font-size: 16px;font-weight: 900;"><?php echo $grand_total ?></span>
                                    </th>
                                    <th style="text-align: right;padding: 3px 10px 3px 0px">
                                        <?php echo display('grand_total'); ?></th>
                                </tr>
                            </tbody>
                        </table>
                        <div style="margin-bottom: 12px;">
                            <div
                                style="font-size: 11px;font-weight: 700;color: #000;text-align: center;margin-bottom: 15px;">
                                <?php echo display('return_receipt_text'); ?></div>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center"
                                style="color: #000;font-size: 13px;">
                                <thead>
                                    <tr align="center">
                                        <?php foreach ($p_method_list as $method_list) { ?>
                                            <th style="border-bottom: 1px solid #000;width: 12%;text-align: center;">
                                                <?php echo $method_list->HeadName ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php foreach ($p_method_list as $method_list) {

                                        ?>
                                            <th style="text-align: center;">
                                                <?php if ($method_list->Debit != null) {
                                                    echo $method_list->Debit;
                                                } else {
                                                    echo '0.00';
                                                } ?>
                                            </th>
                                        <?php }
                                        ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div style="text-align: left;">

                            <ul style="font-size: 11px;color: #000;font-weight: 700;">
                                <?php if (!empty($terms_list)) {
                                    foreach ($terms_list as $terms) { ?>
                                        <li><?php echo $terms->description ?></li>
                                <?php }
                                } ?>
                            </ul>
                        </div>
                        <?php
                        $web_setting = $this->db->select("*")->from("web_setting")->get()->row();
                        if ($web_setting->is_qr == 1) {

                            $text = base64_encode(display('invoice_no') . ': ' . $invoice_no . ' ' . display('customer_name') . ': ' . $customer_name);

                            $CI = &get_instance();
                            $CI->load->library('ciqrcode');

                            // Correct file path for saving the QR code
                            $filePath = FCPATH . 'assets/img/qr/' . $invoice_no . '.png';

                            // Set the parameters for the QR code
                            $params['data']     = base_url("assets/data/pdf/invoice/") . $invoice_no . '.pdf';
                            $params['level']    = 'H';
                            $params['size']     = 2;
                            $params['savename'] = $filePath;  // Corrected: Use FCPATH, not base_url()

                            // Generate the QR code
                            $CI->ciqrcode->generate($params);

                            // Correct file path for displaying the image
                            $imagePath = base_url('assets/img/qr/' . $invoice_no . '.png');

                        ?>
                            <div style="text-align: center;">
                                <?php $text = base64_encode(display('invoice_no') . ': ' . $invoice_no . ' ' . display('customer_name') . ': ' . $customer_name);
                                ?>
                                <img src="<?php echo $imagePath; ?>" />
                            </div>
                        <?php } ?>
                        <table width="70%" border="0" cellpadding="0" cellspacing="0" align="center"
                            style="color: #000;font-size: 13px;margin-top: 20px;">
                            <tbody>
                                <tr>
                                    <th style="text-align: left;"><?php echo $users_name; ?></th>
                                    <th style="text-align: right;"><?php echo display('order_by') ?></th>

                                </tr>
                                <tr>
                                    <?php $create_at = $this->db->select('CreateDate')
                                        ->from('acc_vaucher')
                                        ->where('referenceNo', $invoice_no)
                                        ->get()
                                        ->row(); ?>
                                    <th style="text-align: left;">
                                        <?php echo date("H:i:s", strtotime($create_at->CreateDate)); ?></th>

                                    <th style="text-align: right;"><?php echo display('order_time');

                                                                    ?></th>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel-footer text-left">
                <a class="btn btn-success" href="#" onclick="printDiv('printableArea')"><span
                        class="fa fa-print"></span></a>

            </div>
        </div>
    </div>
</div>

<!-- Payments + Inline Payment for Invoice -->
<div class="row" style="margin-top:10px;">
  <div class="col-sm-12">
    <h4>Payments Applied</h4>
    <?php if (!empty($payments)) { ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Date</th>
            <th>Voucher No</th>
            <th>Method</th>
            <th class="text-right">Amount</th>
          </tr>
        </thead>
        <tbody>
        <?php $ptotal = 0.0; foreach ($payments as $p) { 
          $pdate = !empty($p->VDate) ? date('d-M-Y', strtotime($p->VDate)) : '';
          $vno = isset($p->VNo) ? $p->VNo : '';
          $mth = isset($p->MethodName) ? $p->MethodName : '';
          $amt = isset($p->Credit) ? (float)$p->Credit : 0.0;
          $ptotal += $amt;
        ?>
          <tr>
            <td><?php echo html_escape($pdate); ?></td>
            <td><?php echo html_escape($vno); ?></td>
            <td><?php echo html_escape($mth); ?></td>
            <td class="text-right"><?php echo ($position == 0) ? $currency.' '.number_format($amt,2) : number_format($amt,2).' '.$currency; ?></td>
          </tr>
        <?php } ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-right">Total Paid</th>
            <th class="text-right"><?php echo ($position == 0) ? $currency.' '.number_format($ptotal,2) : number_format($ptotal,2).' '.$currency; ?></th>
          </tr>
        </tfoot>
      </table>
    <?php } else { ?>
      <div class="alert alert-info" style="margin-bottom:0;">No payments recorded for this invoice yet.</div>
    <?php } ?>
    <?php if (isset($raw_due) && (float)$raw_due > 0) { ?>
      <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalRecordPaymentInvoice" data-invoice="<?php echo (int)$invoice_id; ?>" data-due="<?php echo (float)$raw_due; ?>">Record Payment</button>
    <?php } ?>
  </div>
</div>

<!-- Inline Payment Modal (Invoice page) -->
<div class="modal fade" id="modalRecordPaymentInvoice" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Record Payment</h4>
      </div>
      <div class="modal-body">
        <form id="inlinePaymentFormInvoice">
          <input type="hidden" id="CSRF_TOKEN" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <input type="hidden" name="voucher_no" id="inv_voucher_no" value="<?php echo (int)$invoice_id; ?>">
          <input type="hidden" name="customer_id" id="inv_customer_id" value="<?php echo isset($customer_id) ? (int)$customer_id : 0; ?>">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label>Date</label>
                <input type="date" class="form-control" name="dtpDate" id="inv_date" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" min="0" class="form-control" name="txtAmount" id="inv_amount" value="<?php echo isset($raw_due) ? number_format((float)$raw_due, 2, '.', '') : '0.00'; ?>" required>
                <small class="text-muted">Max due: <span id="inv_due_hint"><?php echo isset($raw_due) ? number_format((float)$raw_due, 2, '.', '') : '0.00'; ?></span></small>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label>Payment Method</label>
                <select name="multipaytype[]" id="inv_method" class="form-control" required>
                  <option value="">Select method</option>
                  <?php if (!empty($pay_methods)) { foreach ($pay_methods as $mid => $mname) { ?>
                    <option value="<?php echo html_escape($mid); ?>"><?php echo html_escape($mname); ?></option>
                  <?php } } ?>
                </select>
                <input type="hidden" name="pamount_by_method[]" id="inv_method_amount" value="<?php echo isset($raw_due) ? number_format((float)$raw_due, 2, '.', '') : '0.00'; ?>">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Remarks</label>
            <textarea class="form-control" name="txtRemarks" id="inv_remarks" rows="2" placeholder="Optional note..."></textarea>
          </div>
          <div class="text-right">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Save Payment</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var baseUrl = '<?php echo base_url(); ?>';
  $('#modalRecordPaymentInvoice').on('show.bs.modal', function(e){
    var $t = $(e.relatedTarget);
    var inv = $t && $t.data('invoice') ? String($t.data('invoice')) : '<?php echo (int)$invoice_id; ?>';
    var due = $t && $t.data('due') ? parseFloat($t.data('due')) : parseFloat($('#inv_due_hint').text()||0);
    $('#inv_voucher_no').val(inv);
    $('#inv_amount').val(due.toFixed(2));
    $('#inv_method_amount').val(due.toFixed(2));
    $('#inv_due_hint').text(due.toFixed(2));
  });
  $('#inlinePaymentFormInvoice').on('submit', function(ev){
    ev.preventDefault();
    var amount = parseFloat($('#inv_amount').val()||0);
    var due = parseFloat($('#inv_due_hint').text()||0);
    if (amount <= 0) { alert('Enter payment amount'); return; }
    if (amount > due + 0.0001) { alert('Amount cannot exceed due'); return; }
    $('#inv_method_amount').val(amount.toFixed(2));
    var data = {
      voucher_no: $('#inv_voucher_no').val(),
      dtpDate: $('#inv_date').val(),
      customer_id: $('#inv_customer_id').val(),
      txtRemarks: $('#inv_remarks').val(),
      txtAmount: amount.toFixed(2),
      'multipaytype[]': $('#inv_method').val(),
      'pamount_by_method[]': amount.toFixed(2),
      csrf_test_name: $('#CSRF_TOKEN').val()
    };
    $.ajax({
      url: baseUrl + 'account/accounts/create_customer_receive',
      type: 'POST',
      dataType: 'json',
      data: data,
      success: function(res){
        if (res && res.status === true) {
          if (window.toastr) { toastr.success(res.message || 'Payment recorded'); }
          $('#modalRecordPaymentInvoice').modal('hide');
          setTimeout(function(){ window.location.reload(); }, 200);
        } else {
          var msg = (res && (res.exception || res.message)) ? res.exception || res.message : 'Failed to save payment';
          alert(msg);
        }
      },
      error: function(xhr){
        var msg = 'Request failed';
        try { if (xhr && xhr.responseText) { msg = xhr.responseText; } } catch(e) {}
        alert(msg);
      }
    });
  });
})();
</script>
