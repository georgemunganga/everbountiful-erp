<style>
  .inv-header { display:flex; justify-content:space-between; align-items:flex-start; }
  .inv-company { max-width:45%; }
  .inv-meta { text-align:right; max-width:45%; }
  .inv-summary td, .inv-summary th { border:0 !important; }
  .payments-applied h4 { margin-top:15px; }
</style>

<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-bd">
      <div class="panel-body" id="printableArea">
        <div class="inv-header">
          <div class="inv-company">
            <?php
            $logo_path = !empty($currency_details[0]['invoice_logo']) ? $currency_details[0]['invoice_logo'] : 'assets/default-logo.png';
            $imagePath = FCPATH . $logo_path;
            if (file_exists($imagePath)) { $imageData = base64_encode(file_get_contents($imagePath)); $imageSrc = 'data:image/png;base64,' . $imageData; } else { $imageSrc = ''; }
            ?>
            <?php if ($imageSrc) { ?><img width="150" src="<?php echo $imageSrc; ?>" alt="Logo"><?php } ?>
            <address>
              <strong style="font-size:18px;"><?php echo html_escape($company_info[0]['company_name'] ?? ''); ?></strong><br>
              <abbr><b><?php echo display('mobile') ?>:</b></abbr> <?php echo html_escape($company_info[0]['mobile'] ?? ''); ?><br>
              <abbr><b><?php echo display('email') ?>:</b></abbr> <?php echo html_escape($company_info[0]['email'] ?? ''); ?><br>
              <abbr><b><?php echo display('website') ?>:</b></abbr> <?php echo html_escape($company_info[0]['website'] ?? ''); ?><br>
            </address>
          </div>
          <div class="inv-meta">
            <div class="pull-right" style="margin-bottom:10px;">
              <?php if (isset($invoice_id)) { ?>
                <a href="<?php echo base_url('invoice_edit/' . $invoice_id); ?>" class="btn btn-primary btn-sm" title="Edit Invoice"><i class="fa fa-pencil"></i></a>
                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalMergeInvoices">Merge Invoices</button>
              <?php } ?>
            </div>
            <h2 style="margin:0;"><?php echo display('invoice'); ?></h2>
            <div>
              <abbr class="font-bold"><?php echo display('invoice_no') ?>:</abbr> <?php echo html_escape($invoice_no); ?><br>
              <abbr class="font-bold"><?php echo display('billing_date') ?>:</abbr> <?php echo date('d-M-Y', strtotime($final_date)); ?>
            </div>
            <div class="m-t-10">
              <span class="<?php echo isset($status_class) ? $status_class : ''; ?>"><?php echo isset($status_label) ? html_escape($status_label) : ''; ?></span>
            </div>
          </div>
        </div>

        <hr>
        <div class="row">
          <div class="col-xs-6">
            <strong><?php echo display('billing_to') ?></strong><br>
            <div><?php echo html_escape($customer_name); ?></div>
            <div><?php echo html_escape($customer_address); ?></div>
            <?php if (!empty($customer_mobile)) { ?><div><?php echo html_escape($customer_mobile); ?></div><?php } ?>
            <?php if (!empty($customer_email)) { ?><div><?php echo html_escape($customer_email); ?></div><?php } ?>
          </div>
          <div class="col-xs-6 text-right">
            <?php if (isset($raw_due) && (float)$raw_due > 0) { ?>
              <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalRecordPaymentInvoice" data-invoice="<?php echo (int)$invoice_id; ?>" data-due="<?php echo (float)$raw_due; ?>">Record Payment</button>
            <?php } ?>
            <?php if (isset($advance_credit_available)) { ?>
              <div class="text-right" style="margin-top:6px;">
                <small class="text-muted">Advance Credit Available:</small>
                <strong><?php echo html_escape($advance_credit_available); ?></strong>
                <?php if (!empty($advance_credit_applied) && (float)str_replace(',', '', $advance_credit_applied) > 0) { ?>
                  <div><small class="text-muted">Applied to this invoice:</small> <strong><?php echo html_escape($advance_credit_applied); ?></strong></div>
                <?php } ?>
              </div>
            <?php } ?>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th class="text-center"><?php echo display('sl'); ?></th>
                <th class="text-left"><?php echo display('product_name'); ?></th>
                <th class="text-center"><?php echo display('unit'); ?></th>
                <th class="text-center"><?php echo display('item_description'); ?></th>
                <th class="text-center"><?php echo display('serial_no'); ?></th>
                <th class="text-right"><?php echo display('quantity'); ?></th>
                <th class="text-right"><?php echo display('rate'); ?></th>
                <th class="text-right"><?php echo display('discount'); ?></th>
                <th class="text-right"><?php echo display('vat'); ?>%</th>
                <th class="text-right"><?php echo display('vat_val'); ?></th>
                <th class="text-right"><?php echo display('ammount'); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php $sl=1; $amount=0; if (!empty($invoice_all_data)) { foreach ($invoice_all_data as $item) { ?>
                <tr>
                  <td class="text-center"><?php echo $sl++; ?></td>
                  <td class="text-left"><?php echo html_escape($item['product_name']).' ('.html_escape($item['product_model']).')'; ?></td>
                  <td class="text-center"><?php echo html_escape($item['unit']); ?></td>
                  <td class="text-center"><?php echo html_escape($item['description']); ?></td>
                  <td class="text-center"><?php echo html_escape($item['serial_no']); ?></td>
                  <td class="text-right"><?php echo html_escape($item['quantity']); ?></td>
                  <td class="text-right"><?php $rate = html_escape($item['rate']); echo ($position==0)? "$currency ".$rate : $rate." $currency"; ?></td>
                  <td class="text-right"><?php $discount=html_escape($item['discount']); echo ($position==0)? "$currency ".$discount : $discount." $currency"; ?></td>
                  <td class="text-right"><?php echo html_escape($item['vat_amnt_per']); ?></td>
                  <td class="text-right"><?php $vat=html_escape($item['vat_amnt']); echo ($position==0)? "$currency ".$vat : $vat." $currency"; ?></td>
                  <td class="text-right"><?php $amount += $item['total_price']; $t = html_escape($item['total_price']); echo ($position==0)? "$currency ".$t : $t." $currency"; ?></td>
                </tr>
              <?php } } ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="5" class="text-left" style="border:0"><b><?php echo display('sub_total'); ?>:</b></td>
                <td class="text-right" style="border:0"><b><?php echo number_format($subTotal_quantity,2); ?></b></td>
                <td colspan="4" style="border:0"></td>
                <td class="text-right" style="border:0"><b><?php echo ($position==0)? $currency.' '.$subTotal_ammount : $subTotal_ammount.' '.$currency; ?></b></td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="row">
          <div class="col-xs-6">
            <p><b><?php echo display('in_words'); ?>:</b> <?php echo html_escape($am_inword); ?></p>
          </div>
          <div class="col-xs-6">
            <table class="table inv-summary">
              <?php if (!empty($total_discount_cal)) { ?>
              <tr>
                <th class="text-left"><?php echo display('total_discount'); ?></th>
                <td class="text-right"><?php echo ($position==0)? $currency.' '.$total_discount : $total_discount.' '.$currency; ?></td>
              </tr>
              <?php } ?>
              <?php if (!empty($total_vat)) { ?>
              <tr>
                <th class="text-left"><?php echo display('total_vat'); ?></th>
                <td class="text-right"><?php echo ($position==0)? $currency.' '.$total_vat : $total_vat.' '.$currency; ?></td>
              </tr>
              <?php } ?>
              <tr>
                <th class="text-left"><?php echo display('grand_total'); ?></th>
                <td class="text-right"><?php echo ($position==0)? $currency.' '.$total_amount : $total_amount.' '.$currency; ?></td>
              </tr>
              <tr>
                <th class="text-left"><?php echo display('paid_amount'); ?></th>
                <td class="text-right"><?php echo ($position==0)? $currency.' '.$paid_amount : $paid_amount.' '.$currency; ?></td>
              </tr>
              <tr>
                <th class="text-left"><?php echo display('due') ?>:</th>
                <td class="text-right"><?php echo ($position==0)? $currency.' '.$due_amount : $due_amount.' '.$currency; ?></td>
              </tr>
            </table>
          </div>
        </div>

        <div class="payments-applied">
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
                <?php $ptotal = 0.0; foreach ($payments as $p) { $pdate = !empty($p->VDate) ? date('d-M-Y', strtotime($p->VDate)) : ''; $vno = $p->VNo ?? ''; $mth = $p->MethodName ?? ''; $amt = isset($p->Credit) ? (float)$p->Credit : 0.0; $ptotal += $amt; ?>
                <tr>
                  <td><?php echo html_escape($pdate); ?></td>
                  <td><?php echo html_escape($vno); ?></td>
                  <td><?php echo html_escape($mth); ?></td>
                  <td class="text-right"><?php echo ($position==0)? $currency.' '.number_format($amt,2) : number_format($amt,2).' '.$currency; ?></td>
                </tr>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="3" class="text-right">Total Paid</th>
                  <th class="text-right"><?php echo ($position==0)? $currency.' '.number_format($ptotal,2) : number_format($ptotal,2).' '.$currency; ?></th>
                </tr>
              </tfoot>
            </table>
          <?php } else { ?>
            <div class="alert alert-info" style="margin-bottom:0;">No payments recorded for this invoice yet.</div>
          <?php } ?>
        </div>

      </div>
      <div class="panel-footer text-left">
        <button class="btn btn-success" onclick="printDivnew('printableArea')"><span class="fa fa-print"></span></button>
      </div>
    </div>
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
            <div class="col-sm-4"><div class="form-group"><label>Date</label><input type="date" class="form-control" name="dtpDate" id="inv_date" value="<?php echo date('Y-m-d'); ?>" required></div></div>
            <div class="col-sm-4"><div class="form-group"><label>Amount</label><input type="number" step="0.01" min="0" class="form-control" name="txtAmount" id="inv_amount" value="<?php echo isset($raw_due) ? number_format((float)$raw_due, 2, '.', '') : '0.00'; ?>" required><small class="text-muted">Max due: <span id="inv_due_hint"><?php echo isset($raw_due) ? number_format((float)$raw_due, 2, '.', '') : '0.00'; ?></span></small></div></div>
            <div class="col-sm-4"><div class="form-group"><label>Payment Method</label><select name="multipaytype[]" id="inv_method" class="form-control" required><option value="">Select method</option><?php if (!empty($pay_methods)) { foreach ($pay_methods as $mid => $mname) { ?><option value="<?php echo html_escape($mid); ?>"><?php echo html_escape($mname); ?></option><?php } } ?></select><input type="hidden" name="pamount_by_method[]" id="inv_method_amount" value="<?php echo isset($raw_due) ? number_format((float)$raw_due, 2, '.', '') : '0.00'; ?>"></div></div>
          </div>
          <div class="form-group"><label>Remarks</label><textarea class="form-control" name="txtRemarks" id="inv_remarks" rows="2" placeholder="Optional note..."></textarea></div>
          <div class="text-right"><button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Save Payment</button></div>
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
          if (window.toastr) { toastr.success('Updated'); } else { try { console.log('Updated'); } catch(e){} }
          #modalRecordPaymentInvoice.modal('hide');\n          $.ajax({ url: baseUrl + 'invoice/invoice/recalc_invoice_paid_due', type:'POST', dataType:'json', data:{ invoice_id: #inv_voucher_no.val(), csrf_test_name: #CSRF_TOKEN.val() }, complete:function(){ setTimeout(function(){ var u = window.location.href.split('#')[0]; var sep = u.indexOf('?')>-1 ? '&' : '?'; window.location.href = u + sep + 't=' + Date.now(); }, 800); } }); }, 800);
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

<!-- Merge Invoices Modal -->
<div class="modal fade" id="modalMergeInvoices" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Merge Invoices into <?php echo html_escape($invoice_no); ?></h4>
      </div>
      <div class="modal-body">
        <p>Select other invoices for this customer to merge into the current invoice.</p>
        <div id="mergeListHolder" class="table-responsive">
          <div class="text-muted">Loading...</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" id="btnDoMerge">Merge Selected</button>
      </div>
    </div>
  </div>
  <input type="hidden" id="merge_customer_id" value="<?php echo (int)$customer_id; ?>">
  <input type="hidden" id="merge_target_id" value="<?php echo (int)$invoice_id; ?>">
  <input type="hidden" id="CSRF_TOKEN_MERGE" value="<?php echo $this->security->get_csrf_hash(); ?>">
</div>

<script>
(function(){
  var baseUrl = '<?php echo base_url(); ?>';
  $('#modalMergeInvoices').on('show.bs.modal', function(){
    var cid = $('#merge_customer_id').val();
    var ex  = $('#merge_target_id').val();
    $('#mergeListHolder').html('<div class="text-muted">Loading...</div>');
    $.ajax({
      url: baseUrl + 'invoice/invoice/list_mergeable_invoices',
      type: 'GET',
      dataType: 'json',
      data: { customer_id: cid, exclude_id: ex },
      success: function(res){
        if (res && res.status && res.invoices && res.invoices.length){
          var html = '<table class="table table-bordered"><thead><tr><th></th><th>Invoice</th><th>Date</th><th class="text-right">Total</th><th class="text-right">Due</th></tr></thead><tbody>';
          res.invoices.forEach(function(r){
            html += '<tr>'+
              '<td><input type="checkbox" class="js-merge-id" value="'+r.invoice_id+'"></td>'+
              '<td>'+ (r.invoice_no||r.invoice_id) +'</td>'+
              '<td>'+ (r.date||'') +'</td>'+
              '<td class="text-right">'+ (r.total_amount||0) +'</td>'+
              '<td class="text-right">'+ (r.due_amount||0) +'</td>'+
            '</tr>';
          });
          html += '</tbody></table>';
          $('#mergeListHolder').html(html);
        } else {
          $('#mergeListHolder').html('<div class="text-muted">No other invoices to merge.</div>');
        }
      },
      error: function(){ $('#mergeListHolder').html('<div class="text-danger">Failed to load invoices.</div>'); }
    });
  });
  $('#btnDoMerge').on('click', function(){
    var ids = []; $('.js-merge-id:checked').each(function(){ ids.push($(this).val()); });
    if (!ids.length) { alert('Select invoices to merge.'); return; }
    var target = $('#merge_target_id').val();
    var csrf = $('#CSRF_TOKEN_MERGE').val();
    $.ajax({
      url: baseUrl + 'invoice/invoice/merge_invoices',
      type: 'POST',
      dataType: 'json',
      data: { target_id: target, 'merge_ids[]': ids, csrf_test_name: csrf },
      success: function(res){
        if (res && res.status){
          if (window.toastr) toastr.success('Merged');
          $('#modalMergeInvoices').modal('hide');
          setTimeout(function(){ window.location.reload(); }, 600);
        } else {
          alert((res && (res.exception||res.message)) ? (res.exception||res.message) : 'Unable to merge');
        }
      },
      error: function(){ alert('Request failed'); }
    });
  });
})();
</script>



