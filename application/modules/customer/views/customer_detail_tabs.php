<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-bd lobidrag">
      <div class="panel-heading">
        <div class="panel-title">
          <h4><?php echo html_escape($title); ?>: <?php echo html_escape($customer->customer_name); ?></h4>
        </div>
      </div>
      <div class="panel-body">
        <style>
          @media (min-width: 768px) {
            .vertical-tabs-container {
              display: flex;
              min-height: 500px;
            }
            .vertical-nav-tabs {
              flex: 0 0 220px;
              background-color: #f8f9fa;
              border-right: 1px solid #ddd;
              padding: 0;
              margin: 0;
              list-style: none;
            }
            .vertical-nav-tabs li {
              display: block;
              border-bottom: 1px solid #ddd;
            }
            .vertical-nav-tabs li a {
              display: block;
              padding: 12px 16px;
              color: #333;
              text-decoration: none;
              transition: all 0.2s ease;
            }
            .vertical-nav-tabs li a:hover {
              background: #e9ecef;
              color: #126e21;
            }
            .vertical-nav-tabs li.active a {
              background: #126e21;
              color: #fff;
              border-left: 4px solid #2d8000;
            }
            .vertical-tab-content {
              flex: 1;
              padding: 20px;
              background: #fff;
            }
          }
          @media (max-width: 767px) {
            .vertical-tabs-container {
              display: block;
            }
            .vertical-nav-tabs {
              display: flex;
              flex-wrap: wrap;
              overflow-x: auto;
              background: #f8f9fa;
              border-bottom: 1px solid #ddd;
              padding: 0;
              margin: 0 0 15px 0;
              list-style: none;
            }
            .vertical-nav-tabs li {
              flex: 0 0 auto;
              border-right: 1px solid #ddd;
            }
            .vertical-nav-tabs li a {
              display: block;
              padding: 10px 14px;
              color: #333;
              white-space: nowrap;
            }
            .vertical-nav-tabs li.active a {
              background: #126e21;
              color: #fff;
            }
            .vertical-tab-content {
              padding: 10px;
            }
          }
        </style>

        <?php
        $queryParams = $this->input->get() ?: array();
        $return_query = $queryParams;
        $return_query['group'] = 'payments';
        $return_payments_url = current_url() . '?' . http_build_query($return_query);
        $credit_notes = $credit_notes ?? array();
        $estimates = $estimates ?? array();
        $expenses = $expenses ?? array();
        $currency_symbol = isset($currency) && $currency !== '' ? $currency : 'ZMW';
        $currency_position = isset($position) ? (int) $position : 0;
        $contact_form_data = isset($contact_form_data) && is_array($contact_form_data) ? $contact_form_data : array();
        $credit_note_form_data = isset($credit_note_form_data) && is_array($credit_note_form_data) ? $credit_note_form_data : array();
        $estimate_form_data = isset($estimate_form_data) && is_array($estimate_form_data) ? $estimate_form_data : array();
        $expense_form_data = isset($expense_form_data) && is_array($expense_form_data) ? $expense_form_data : array();
        $credit_note_statuses = array('Issued', 'Applied', 'Void');
        $estimate_statuses = array('Draft', 'Sent', 'Accepted', 'Rejected', 'Cancelled');
        $formatCurrency = function ($amount, $showZero = true) use ($currency_symbol, $currency_position) {
          if ($amount === null || $amount === '') {
            return $showZero ? ($currency_position == 0 ? $currency_symbol . ' 0.00' : '0.00 ' . $currency_symbol) : '';
          }
          $amount = (float) $amount;
          if (!$showZero && abs($amount) < 0.0001) {
            return '';
          }
          $formatted = number_format($amount, 2);
          return $currency_position == 0 ? $currency_symbol . ' ' . $formatted : $formatted . ' ' . $currency_symbol;
        };
        ?>
        <?php $active_group = $this->input->get('group', true); $active_group = $active_group ? strtolower($active_group) : 'profile'; ?>
        <div class="vertical-tabs-container">
          <ul class="vertical-nav-tabs" role="tablist">
            <li class="<?php echo ($active_group==='profile'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=profile'); ?>">Profile</a></li>
            <li class="<?php echo ($active_group==='contacts'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=contacts'); ?>">Contacts</a></li>
            <li class="<?php echo ($active_group==='notes'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=notes'); ?>">Notes</a></li>
            <li class="<?php echo ($active_group==='statement'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=statement'); ?>">Statement</a></li>
            <li class="<?php echo ($active_group==='invoices'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=invoices'); ?>">Invoices</a></li>
            <li class="<?php echo ($active_group==='payments'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=payments'); ?>">Payments</a></li>
            <li class="<?php echo ($active_group==='credit_notes'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=credit_notes'); ?>">Credit Notes</a></li>
            <li class="<?php echo ($active_group==='estimates'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=estimates'); ?>">Estimates</a></li>
            <li class="<?php echo ($active_group==='expenses'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=expenses'); ?>">Expenses</a></li>
            <li class="<?php echo ($active_group==='reminders'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=reminders'); ?>">Reminders</a></li>
            <li class="<?php echo ($active_group==='map'?'active':''); ?>"><a href="<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=map'); ?>">Map</a></li>
          </ul>

          <div class="vertical-tab-content">
              <div class="tab-content">
              <div class="tab-pane<?php echo ($active_group==='profile'?' active in':''); ?>" id="profile" <?php echo ($active_group==='profile'?'':'style="display:none"'); ?> >
                <div class="row">
                  <div class="col-sm-6">
                    <table class="table table-striped">
                      <tbody>
                        <tr><th>Name</th><td><?php echo html_escape($customer->customer_name); ?></td></tr>
                        <tr><th>Phone</th><td><?php echo html_escape($customer->customer_phone); ?></td></tr>
                        <tr><th>Email</th><td><?php echo html_escape($customer->customer_email); ?></td></tr>
                        <tr><th>Address</th><td><?php echo html_escape($customer->customer_address); ?></td></tr>
                        <tr><th>City</th><td><?php echo html_escape($customer->city); ?></td></tr>
                        <tr><th>Country</th><td><?php echo html_escape($customer->country); ?></td></tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="col-sm-6">
                    <h5>Billing & Shipping</h5>
                    <table class="table table-striped">
                      <tbody>
                        <tr><th>Billing Address</th><td><?php echo html_escape($customer->billing_address ?? ''); ?></td></tr>
                        <tr><th>Delivery Address</th><td><?php echo html_escape($customer->shipping_address ?? ''); ?></td></tr>
                        <tr><th>VAT / Tax ID</th><td><?php echo html_escape($customer->vat_id ?? ''); ?></td></tr>
                        <tr><th>Balance</th><td><?php echo $formatCurrency($customer->balance ?? 0); ?></td></tr>
                      </tbody>
                    </table>
                    <a href="<?php echo base_url('customer/edit/'.$customer->customer_id); ?>" class="btn btn-primary">Edit Profile</a>
                  </div>
                </div>
              </div>

              <div class="tab-pane<?php echo ($active_group==='contacts'?' active in':''); ?>" id="contacts" <?php echo ($active_group==='contacts'?'':'style="display:none"'); ?> >
                <div class="row">
                  <div class="col-sm-4">
                    <?php echo form_open('customer/add_contact/'.$customer->customer_id); ?>
                      <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" name="name" value="<?php echo set_value('name', isset($contact_form_data['name']) ? $contact_form_data['name'] : ''); ?>" required>
                        <?php echo form_error('name','<div class="text-danger small">','</div>'); ?>
                      </div>
                      <div class="form-group">
                        <label>Email</label>
                          <input type="email" class="form-control" name="email" value="<?php echo set_value('email', isset($contact_form_data['email']) ? $contact_form_data['email'] : ''); ?>">
                      </div>
                      <div class="form-group">
                        <label>Phone</label>
                          <input type="text" class="form-control" name="phone" value="<?php echo set_value('phone', isset($contact_form_data['phone']) ? $contact_form_data['phone'] : ''); ?>">
                      </div>
                      <div class="form-group">
                        <label>Position</label>
                          <input type="text" class="form-control" name="position" value="<?php echo set_value('position', isset($contact_form_data['position']) ? $contact_form_data['position'] : ''); ?>">
                      </div>
                      <button type="submit" class="btn btn-primary btn-block">Save Contact</button>
                    <?php echo form_close(); ?>
                  </div>
                  <div class="col-sm-8">
                    <table class="table table-bordered">
                      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Position</th><th>Actions</th></tr></thead>
                      <tbody>
                        <?php if (!empty($contacts)) { foreach ($contacts as $contact) { ?>
                          <tr>
                            <td><?php echo html_escape($contact['name']); ?></td>
                            <td><?php echo html_escape($contact['email']); ?></td>
                            <td><?php echo html_escape($contact['phone']); ?></td>
                            <td><?php echo html_escape($contact['position']); ?></td>
                            <td>
                              <a href="<?php echo base_url('customer/delete_contact/'.$customer->customer_id.'/'.$contact['id']); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this contact?');">Delete</a>
                            </td>
                          </tr>
                        <?php } } else { ?>
                          <tr><td colspan="5" class="text-center">No contacts found</td></tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane<?php echo ($active_group==='notes'?' active in':''); ?>" id="notes" <?php echo ($active_group==='notes'?'':'style="display:none"'); ?> >
                <form method="post" action="<?php echo base_url('customer/add_note/'.$customer->customer_id); ?>">
                  <div class="form-group">
                    <label>Add Note</label>
                    <textarea class="form-control" name="note_text" rows="3" placeholder="Internal note..."></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Save Note</button>
                </form>
                <hr>
                <h5>Previous Notes</h5>
                <ul class="list-group">
                  <?php if (!empty($notes)) { foreach ($notes as $note) { ?>
                    <li class="list-group-item">
                      <strong><?php echo html_escape($note['created_by']); ?></strong>
                      <span class="text-muted"><?php echo date('d-m-Y H:i', strtotime($note['created_at'])); ?></span>
                      <p><?php echo nl2br(html_escape($note['note'])); ?></p>
                    </li>
                  <?php } } else { ?>
                    <li class="list-group-item text-center text-muted">No notes recorded.</li>
                  <?php } ?>
                </ul>
              </div>

              <div class="tab-pane<?php echo ($active_group==='statement'?' active in':''); ?>" id="statement" <?php echo ($active_group==='statement'?'':'style="display:none"'); ?> >
                <style>
                  .stmt-toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
                  .stmt-filters { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
                  .stmt-doc { background:#fff; border:1px solid #e5e7eb; border-radius:4px; padding:12px; box-shadow:0 1px 2px rgba(0,0,0,0.04); }
                </style>
                <div class="stmt-toolbar">
                  <div class="stmt-filters">
                    <div class="dropdown">
                      <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo html_escape(ucwords(str_replace('_',' ', $range ?? 'this_month'))); ?> <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu" style="margin-top:0;margin-bottom:0;">
                        <?php
                          $base = base_url('customer/customer_detail/'.$customer->customer_id.'?group=statement');
                          $opts = array(
                            'today' => 'Today',
                            'this_week' => 'This Week',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'this_year' => 'This Year',
                            'last_year' => 'Last Year',
                            'all_time' => 'All Time',
                            'period' => 'Period',
                          );
                          foreach ($opts as $key => $label):
                            $active = ($range === $key) ? 'selected active' : '';
                            $href = $base . ($key === 'period' ? '' : ('&range='.$key));
                        ?>
                        <li class="<?php echo $active; ?>">
                          <a role="option" tabindex="0" class="<?php echo $active; ?>" aria-selected="<?php echo $active ? 'true':'false'; ?>" href="<?php echo $href; ?>">
                            <span class="text"> <?php echo html_escape($label); ?> </span>
                          </a>
                        </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                      <form class="form-inline" method="get" action="<?php echo base_url('customer/customer_detail/'.$customer->customer_id); ?>" style="margin-left:5px; <?php echo ($range==='period') ? '' : 'display:none;'; ?>" id="stmt-period-form">
                      <input type="hidden" name="group" value="statement">
                      <div class="form-group">
                        <label style="margin-right:4px; font-weight:normal; color:#6b7280;">From</label>
                        <input type="date" name="from_date" class="form-control input-sm" value="<?php echo html_escape($from_date ?? ''); ?>" style="height:30px; padding:2px 6px;">
                      </div>
                      <div class="form-group" style="margin-left:6px;">
                        <label style="margin-right:4px; font-weight:normal; color:#6b7280;">To</label>
                        <input type="date" name="to_date" class="form-control input-sm" value="<?php echo html_escape($to_date ?? ''); ?>" style="height:30px; padding:2px 6px;">
                      </div>
                      <button type="submit" class="btn btn-primary btn-sm" style="margin-left:6px;">Filter</button>
                    </form>
                    <script>
                      $(function(){
                        $('.stmt-filters .dropdown-menu a').on('click', function(e){
                          var text = $(this).text().trim();
                          if (text === 'Period') {
                            e.preventDefault();
                            $('#stmt-period-form').show();
                          }
                        });
                      });
                    </script>
                  </div>
                  <div>
                    <a href="#" onclick="window.print(); return false;" class="btn btn-default btn-sm">Print</a>
                    <a href="<?php echo base_url('customer/customer_statement_pdf/'.$customer->customer_id); ?>?from_date=<?php echo urlencode($from_date ?? ''); ?>&to_date=<?php echo urlencode($to_date ?? ''); ?>" target="_blank" class="btn btn-primary btn-sm">Download PDF</a>
                  </div>
                </div>

                <div class="stmt-doc">
                  <h4 style="margin-top:0;">Account Statement</h4>
                    <?php
                      $statementData = (isset($statement) && is_array($statement)) ? $statement : array('summary' => array(), 'lines' => array());
                      $summaryDefaults = array(
                        'beginning'   => 0.0,
                        'invoiced'    => 0.0,
                        'paid'        => 0.0,
                        'balance_due' => 0.0,
                      );
                      $statementSummaryRaw = isset($statementData['summary']) && is_array($statementData['summary']) ? $statementData['summary'] : array();
                      $hasStatementSummary = !empty($statementSummaryRaw);
                      $statementSummary = array_merge($summaryDefaults, array_intersect_key($statementSummaryRaw, $summaryDefaults));
                      foreach ($statementSummary as $key => $value) {
                        $statementSummary[$key] = is_numeric($value) ? (float) $value : 0.0;
                      }

                      $statementLines = array();
                      if (isset($statementData['lines']) && is_array($statementData['lines'])) {
                        foreach ($statementData['lines'] as $entry) {
                          if (!is_array($entry)) {
                            continue;
                          }
                          $statementLines[] = array(
                            'date'        => isset($entry['date']) ? $entry['date'] : '',
                            'description' => isset($entry['description']) ? $entry['description'] : '',
                            'debit'       => isset($entry['debit']) && is_numeric($entry['debit']) ? (float) $entry['debit'] : 0.0,
                            'credit'      => isset($entry['credit']) && is_numeric($entry['credit']) ? (float) $entry['credit'] : 0.0,
                            'balance'     => isset($entry['balance']) && is_numeric($entry['balance']) ? (float) $entry['balance'] : 0.0,
                          );
                        }
                      }
                    ?>
                    <?php if ($hasStatementSummary) { ?>
                      <table class="table table-condensed table-bordered" style="margin-bottom:15px;">
                        <tr>
                          <th>Beginning Balance</th>
                          <td class="text-right"><?php echo $formatCurrency(isset($statementSummary['beginning']) ? $statementSummary['beginning'] : 0); ?></td>
                        </tr>
                        <tr>
                          <th>Total Invoiced</th>
                          <td class="text-right"><?php echo $formatCurrency(isset($statementSummary['invoiced']) ? $statementSummary['invoiced'] : 0); ?></td>
                        </tr>
                        <tr>
                          <th>Payments Received</th>
                          <td class="text-right"><?php echo $formatCurrency(isset($statementSummary['paid']) ? $statementSummary['paid'] : 0); ?></td>
                        </tr>
                        <tr>
                          <th>Balance Due</th>
                          <td class="text-right"><?php echo $formatCurrency(isset($statementSummary['balance_due']) ? $statementSummary['balance_due'] : 0); ?></td>
                        </tr>
                      </table>
                    <?php } ?>
                    <table class="table table-bordered table-striped">
                      <thead><tr><th>Date</th><th>Description</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead>
                      <tbody>
                        <?php if (!empty($statementLines)) {
                          foreach ($statementLines as $entry) {
                            $entryDate = !empty($entry['date']) ? date('d-m-Y', strtotime($entry['date'])) : '';
                            $debitAmount = isset($entry['debit']) ? (float)$entry['debit'] : 0.0;
                            $creditAmount = isset($entry['credit']) ? (float)$entry['credit'] : 0.0;
                            $balanceAmount = isset($entry['balance']) ? (float)$entry['balance'] : ($debitAmount - $creditAmount);
                            ?>
                            <tr>
                              <td><?php echo html_escape($entryDate); ?></td>
                              <td><?php echo html_escape($entry['description'] ?? ''); ?></td>
                              <td class="text-right"><?php echo $debitAmount > 0 ? $formatCurrency($debitAmount, false) : ''; ?></td>
                              <td class="text-right"><?php echo $creditAmount > 0 ? $formatCurrency($creditAmount, false) : ''; ?></td>
                              <td class="text-right"><?php echo $formatCurrency($balanceAmount); ?></td>
                            </tr>
                          <?php }
                        } else { ?>
                            <tr><td colspan="5" class="text-center">No activity for selected period.</td></tr>
                          <?php } ?>
                      </tbody>
                    </table>
                </div>
              </div>

              <div class="tab-pane<?php echo ($active_group==='invoices'?' active in':''); ?>" id="invoices" <?php echo ($active_group==='invoices'?'':'style="display:none"'); ?> >
                <div class="clearfix" style="margin-bottom:10px;">
                  <a href="<?php echo base_url('add_invoice?customer_id=' . urlencode($customer->customer_id)); ?>" class="btn btn-success">New Invoice</a>
                </div>
                <table class="table table-bordered table-striped">
                  <thead><tr><th>Invoice #</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Action</th></tr></thead>
                  <tbody>
                    <?php if (!empty($invoices)) { foreach ($invoices as $invoice) { ?>
                      <?php $invoice_number = isset($invoice['invoice_no']) ? $invoice['invoice_no'] : (isset($invoice['invoice']) ? $invoice['invoice'] : $invoice['invoice_id']); ?>
                      <tr>
                        <td>
                          <?php if (isset($invoice['source']) && $invoice['source'] === 'service') { ?>
                            <a href="<?php echo base_url('service/service/service_invoice_view/'.(int)$invoice['invoice_id']); ?>"><?php echo html_escape($invoice_number); ?></a>
                          <?php } else { ?>
                            <a href="<?php echo base_url('invoice_details/'.(int)$invoice['invoice_id'].'?customer_id=' . urlencode($customer->customer_id)); ?>"><?php echo html_escape($invoice_number); ?></a>
                          <?php } ?>
                        </td>
                        <td><?php echo date('d-m-Y', strtotime($invoice['date'])); ?></td>
                        <td class="text-right"><?php echo $formatCurrency($invoice['total_amount']); ?></td>
                        <td class="text-right"><?php echo $formatCurrency($invoice['paid_amount'], false); ?></td>
                        <td class="text-right"><?php echo $formatCurrency($invoice['due_amount'], false); ?></td>
                        <td><span class="<?php echo html_escape($invoice['status_class']); ?>"><?php echo html_escape($invoice['status_label']); ?></span></td>
                        <td>
                          <?php if (isset($invoice['source']) && $invoice['source'] === 'service') { ?>
                            <a href="<?php echo base_url('service/service/service_invoice_view/'.(int)$invoice['invoice_id']); ?>" class="btn btn-xs btn-info">View</a>
                            <a href="<?php echo base_url('edit_service_invoice/'.(int)$invoice['invoice_id']); ?>" class="btn btn-xs btn-primary">Edit</a>
                          <?php } else { ?>
                            <a href="<?php echo base_url('invoice_details/'.(int)$invoice['invoice_id'].'?customer_id=' . urlencode($customer->customer_id)); ?>" class="btn btn-xs btn-info">View</a>
                            <a href="<?php echo base_url('invoice_edit/'.(int)$invoice['invoice_id'].'?customer_id=' . urlencode($customer->customer_id)); ?>" class="btn btn-xs btn-primary">Edit</a>
                          <?php } ?>
                          <button type="button" class="btn btn-xs btn-danger js-delete-invoice" data-invoice="<?php echo (int)$invoice['invoice_id']; ?>">Delete</button>
                          <?php if ((!(isset($invoice['source']) && $invoice['source'] === 'service')) && !empty($invoice['due_amount']) && (float)$invoice['due_amount'] > 0) { ?>
                            <button type="button" class="btn btn-xs btn-success" data-toggle="modal" data-target="#modalRecordPayment" data-invoice="<?php echo (int)$invoice['invoice_id']; ?>" data-due="<?php echo (float)$invoice['due_amount']; ?>">Record Payment</button>
                          <?php } ?>
                        </td>
                      </tr>
                    <?php } } else { ?>
                      <tr><td colspan="7" class="text-center">No invoices found</td></tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

              <div class="tab-pane<?php echo ($active_group==='payments'?' active in':''); ?>" id="payments" <?php echo ($active_group==='payments'?'':'style="display:none"'); ?> >
                <div class="clearfix" style="margin-bottom:10px;">
                  <!-- Record Payment button (kept only once below); removed duplicate -->
                </div>
                <?php if (!empty($customer_receipts)) { ?>
                <style>
                  /* Ensure only the payments table is visible under the Payments tab */
                  #payments .table:not(.payments-table) { display: none !important; }
                </style>
                <div class="table-responsive">
                  <table class="table table-bordered table-striped payments-table">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Voucher</th>
                        <th>Invoice</th>
                        <th>Method</th>
                        <th class="text-right">Amount</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($customer_receipts as $rc) { ?>
                      <tr>
                        <td><?php echo !empty($rc['date']) ? date('d-m-Y', strtotime($rc['date'])) : ''; ?></td>
                        <td><?php echo html_escape($rc['VNo']); ?></td>
                        <td><a href="<?php echo base_url('invoice_details/' . (int)$rc['invoice_id']); ?>" target="_blank"><?php echo (int)$rc['invoice_id']; ?></a></td>
                        <td><?php echo html_escape($rc['HeadName']); ?></td>
                        <td class="text-right"><?php echo $formatCurrency($rc['amount']); ?></td>
                        <td>
                          <button type="button" class="btn btn-xs btn-primary js-edit-payment"
                            data-toggle="modal" data-target="#modalRecordPayment"
                            data-vno="<?php echo html_escape($rc['VNo']); ?>"
                            data-date="<?php echo html_escape($rc['date']); ?>"
                            data-amount="<?php echo (float)$rc['amount']; ?>"
                            data-method="<?php echo html_escape($rc['method_coa']); ?>"
                            data-invoice="<?php echo (int)$rc['invoice_id']; ?>">
                            Edit
                          </button>
                          <button type="button" class="btn btn-xs btn-danger js-delete-payment" data-vno="<?php echo html_escape($rc['VNo']); ?>">Delete</button>
                        </td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
                <?php } else { ?>
                  <p class="text-muted">No payments recorded for this period.</p>
                <?php } ?>
              </div>

              <div class="tab-pane<?php echo ($active_group==='payments'?' active in':''); ?>" id="payments" <?php echo ($active_group==='payments'?'':'style="display:none"'); ?> >
                <div class="clearfix" style="margin-bottom:10px;">
                  <button class="btn btn-success" data-toggle="modal" data-target="#modalRecordPayment">Record Payment</button>
                  <div class="help-block text-muted" style="margin-top:6px;">
                    Use this to record group payments. If no invoice is selected, the amount is automatically distributed across the customer's oldest unpaid invoices (FIFO). To pay a single invoice, open that invoice and use Record Payment on the invoice page, or use the Invoices tab action.
                  </div>
                  <?php if (!empty($payment_groups)) { ?>
                    <div class="panel panel-default" style="margin-top:10px;">
                      <div class="panel-heading"><strong>Bulk Payments (Lump sums)</strong></div>
                      <div class="panel-body" style="padding:8px;">
                        <table class="table table-condensed" style="margin:0;">
                          <thead>
                            <tr>
                              <th>Date</th>
                              <th>Group</th>
                              <th class="text-right">Total</th>
                              <th class="text-right" style="width:90px;">Details</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($payment_groups as $pg) { $gid = 'pg_'.substr(md5(($pg['label'] ?? '').'|'.($pg['date'] ?? '')),0,8); ?>
                              <tr>
                                <td><?php echo !empty($pg['date']) ? date('d-m-Y', strtotime($pg['date'])) : ''; ?></td>
                                <td><?php echo html_escape($pg['label']); ?></td>
                                <td class="text-right"><?php echo $formatCurrency($pg['total']); ?></td>
                                <td class="text-right">
                                  <button type="button" class="btn btn-xs btn-default" data-toggle="collapse" data-target="#<?php echo $gid; ?>" aria-expanded="false" aria-controls="<?php echo $gid; ?>">
                                    <span class="toggle-text">Show</span>
                                  </button>
                                </td>
                              </tr>
                              <tr id="<?php echo $gid; ?>" class="collapse">
                                <td colspan="4" style="background:#fafafa;">
                                  <?php if (!empty($pg['items'])) { ?>
                                    <table class="table table-condensed" style="margin:0;">
                                      <thead>
                                        <tr>
                                          <th style="width:140px;">Applied Date</th>
                                          <th>Applied To</th>
                                          <th class="text-right">Amount</th>
                                          <th class="text-right">Due (after apply)</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php foreach ($pg['items'] as $it) { ?>
                                          <tr>
                                            <td><?php echo !empty($it['date']) ? date('d-m-Y', strtotime($it['date'])) : ''; ?></td>
                                            <td class="text-muted">Invoice <?php echo html_escape($it['invoice_id']); ?> (Voucher <?php echo html_escape($it['voucher']); ?>)</td>
                                            <td class="text-right text-muted"><?php echo $formatCurrency($it['amount']); ?></td>
                                            <td class="text-right"><?php echo isset($it['due']) && $it['due'] !== null ? $formatCurrency($it['due']) : '<span class="text-muted">—</span>'; ?></td>
                                          </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table>
                                  <?php } else { ?>
                                    <div class="text-muted">No breakdown.</div>
                                  <?php } ?>
                                </td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <script>
                      (function(){
                        $(document).on('shown.bs.collapse hidden.bs.collapse', '.panel .collapse', function(){
                          var $btn = $("[data-target='#"+this.id+"']");
                          var $txt = $btn.find('.toggle-text');
                          if ($(this).hasClass('in')) { $txt.text('Hide'); } else { $txt.text('Show'); }
                        });
                      })();
                    </script>
                  <?php } ?>

                  <?php if (!empty($customer_receipts)) { ?>
                  <div class="table-responsive" style="margin-top:10px;">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Voucher</th>
                          <th>Invoice</th>
                          <th>Method</th>
                          <th class="text-right">Amount</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($customer_receipts as $rc) { ?>
                        <tr>
                          <td><?php echo !empty($rc['date']) ? date('d-m-Y', strtotime($rc['date'])) : ''; ?></td>
                          <td><?php echo html_escape($rc['VNo']); ?></td>
                          <td><a href="<?php echo base_url('invoice_details/' . (int)$rc['invoice_id']); ?>" target="_blank"><?php echo (int)$rc['invoice_id']; ?></a></td>
                          <td><?php echo html_escape($rc['HeadName']); ?></td>
                          <td class="text-right"><?php echo $formatCurrency($rc['amount']); ?></td>
                          <td>
                            <button type="button" class="btn btn-xs btn-primary js-edit-payment"
                              data-toggle="modal" data-target="#modalRecordPayment"
                              data-vno="<?php echo html_escape($rc['VNo']); ?>"
                              data-date="<?php echo html_escape($rc['date']); ?>"
                              data-amount="<?php echo (float)$rc['amount']; ?>"
                              data-method="<?php echo html_escape($rc['method_coa']); ?>"
                              data-invoice="<?php echo (int)$rc['invoice_id']; ?>">
                              Edit
                            </button>
                            <button type="button" class="btn btn-xs btn-danger js-delete-payment" data-vno="<?php echo html_escape($rc['VNo']); ?>">Delete</button>
                          </td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                  <?php } else { ?>
                    <p class="text-muted" style="margin-top:10px;">No payments recorded for this period.</p>
                  <?php } ?>
                </div>
                <?php if (!empty($payments)) { ?>
                <table class="table table-bordered table-striped">
                  <thead><tr><th>Date</th><th>Voucher</th><th>Narration</th><th>Debit</th><th>Credit</th></tr></thead>
                  <tbody>
                    <?php foreach ($payments as $payment) { ?>
                      <tr>
                        <td><?php echo date('d-m-Y', strtotime($payment['date'])); ?></td>
                        <td><?php echo html_escape($payment['voucher_no']); ?></td>
                        <td><?php echo html_escape($payment['Narration']); ?></td>
                        <td class="text-right"><?php echo $payment['Debit'] ? $formatCurrency($payment['Debit'], false) : ''; ?></td>
                        <td class="text-right"><?php echo $payment['Credit'] ? $formatCurrency($payment['Credit'], false) : ''; ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <?php } ?>
              </div>

              <div class="tab-pane<?php echo ($active_group==='credit_notes'?' active in':''); ?>" id="credit_notes" <?php echo ($active_group==='credit_notes'?'':'style="display:none"'); ?> >
                <div class="panel panel-default">
                  <div class="panel-heading"><strong>Add Credit Note</strong></div>
                  <div class="panel-body">
                    <?php echo form_open('customer/add_credit_note/'.$customer->customer_id); ?>
                      <div class="row">
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label>Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="number" value="<?php echo set_value('number', isset($credit_note_form_data['number']) ? $credit_note_form_data['number'] : ''); ?>" required>
                            <?php echo form_error('number','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" value="<?php echo set_value('date', isset($credit_note_form_data['date']) ? $credit_note_form_data['date'] : date('Y-m-d')); ?>" required>
                            <?php echo form_error('date','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo set_value('amount', isset($credit_note_form_data['amount']) ? $credit_note_form_data['amount'] : ''); ?>" required>
                            <?php echo form_error('amount','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                              <option value="">Select status</option>
                              <?php foreach ($credit_note_statuses as $status_option) { ?>
                                <option value="<?php echo html_escape($status_option); ?>" <?php echo set_select('status', $status_option, (isset($credit_note_form_data['status']) && $credit_note_form_data['status'] === $status_option)); ?>>
                                  <?php echo html_escape($status_option); ?>
                                </option>
                              <?php } ?>
                            </select>
                            <?php echo form_error('status','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label>Remarks</label>
                            <input type="text" class="form-control" name="remarks" value="<?php echo set_value('remarks', isset($credit_note_form_data['remarks']) ? $credit_note_form_data['remarks'] : ''); ?>">
                            <?php echo form_error('remarks','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary">Save Credit Note</button>
                    <?php echo form_close(); ?>
                  </div>
                </div>
                <table class="table table-bordered table-striped">
                  <thead><tr><th>Number</th><th>Date</th><th>Amount</th><th>Status</th><th>Remarks</th><th>Actions</th></tr></thead>
                  <tbody>
                    <?php if (!empty($credit_notes)) { foreach ($credit_notes as $note) { ?>
                      <tr>
                        <td><?php echo html_escape($note['number']); ?></td>
                        <td><?php echo !empty($note['date']) ? date('d-m-Y', strtotime($note['date'])) : ''; ?></td>
                        <td class="text-right"><?php echo $formatCurrency($note['amount']); ?></td>
                        <td><?php echo html_escape($note['status']); ?></td>
                        <td><?php echo html_escape($note['remarks']); ?></td>
                        <td>
                          <a href="<?php echo base_url('customer/delete_credit_note/'.$customer->customer_id.'/'.$note['id']); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this credit note?');">Delete</a>
                        </td>
                      </tr>
                    <?php } } else { ?>
                      <tr><td colspan="6" class="text-center">No credit notes.</td></tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

              <div class="tab-pane<?php echo ($active_group==='estimates'?' active in':''); ?>" id="estimates" <?php echo ($active_group==='estimates'?'':'style="display:none"'); ?> >
                <div class="panel panel-default">
                  <div class="panel-heading"><strong>Add Estimate</strong></div>
                  <div class="panel-body">
                    <?php echo form_open('customer/add_estimate/'.$customer->customer_id); ?>
                      <div class="row">
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label>Estimate # <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="number" value="<?php echo set_value('number', isset($estimate_form_data['number']) ? $estimate_form_data['number'] : ''); ?>" required>
                            <?php echo form_error('number','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" value="<?php echo set_value('date', isset($estimate_form_data['date']) ? $estimate_form_data['date'] : date('Y-m-d')); ?>" required>
                            <?php echo form_error('date','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo set_value('amount', isset($estimate_form_data['amount']) ? $estimate_form_data['amount'] : ''); ?>" required>
                            <?php echo form_error('amount','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                              <option value="">Select status</option>
                              <?php foreach ($estimate_statuses as $status_option) { ?>
                                <option value="<?php echo html_escape($status_option); ?>" <?php echo set_select('status', $status_option, (isset($estimate_form_data['status']) && $estimate_form_data['status'] === $status_option)); ?>>
                                  <?php echo html_escape($status_option); ?>
                                </option>
                              <?php } ?>
                            </select>
                            <?php echo form_error('status','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label>Notes</label>
                            <input type="text" class="form-control" name="notes" value="<?php echo set_value('notes', isset($estimate_form_data['notes']) ? $estimate_form_data['notes'] : ''); ?>">
                            <?php echo form_error('notes','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary">Save Estimate</button>
                    <?php echo form_close(); ?>
                  </div>
                </div>
                <table class="table table-bordered table-striped">
                  <thead><tr><th>Estimate #</th><th>Date</th><th>Amount</th><th>Status</th><th>Notes</th><th>Actions</th></tr></thead>
                  <tbody>
                    <?php if (!empty($estimates)) { foreach ($estimates as $estimate) { ?>
                      <tr>
                        <td><?php echo html_escape($estimate['number']); ?></td>
                        <td><?php echo !empty($estimate['date']) ? date('d-m-Y', strtotime($estimate['date'])) : ''; ?></td>
                        <td class="text-right"><?php echo $formatCurrency($estimate['amount']); ?></td>
                        <td><?php echo html_escape($estimate['status']); ?></td>
                        <td><?php echo html_escape($estimate['notes']); ?></td>
                        <td>
                          <a href="<?php echo base_url('customer/delete_estimate/'.$customer->customer_id.'/'.$estimate['id']); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this estimate?');">Delete</a>
                        </td>
                      </tr>
                    <?php } } else { ?>
                      <tr><td colspan="6" class="text-center">No estimates.</td></tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

              <div class="tab-pane<?php echo ($active_group==='expenses'?' active in':''); ?>" id="expenses" <?php echo ($active_group==='expenses'?'':'style="display:none"'); ?> >
                <div class="panel panel-default">
                  <div class="panel-heading"><strong>Add Expense</strong></div>
                  <div class="panel-body">
                    <?php echo form_open('customer/add_expense/'.$customer->customer_id); ?>
                      <div class="row">
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label>Category <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="category" value="<?php echo set_value('category', isset($expense_form_data['category']) ? $expense_form_data['category'] : ''); ?>" required>
                            <?php echo form_error('category','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" value="<?php echo set_value('date', isset($expense_form_data['date']) ? $expense_form_data['date'] : date('Y-m-d')); ?>" required>
                            <?php echo form_error('date','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="amount" value="<?php echo set_value('amount', isset($expense_form_data['amount']) ? $expense_form_data['amount'] : ''); ?>" required>
                            <?php echo form_error('amount','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label>Vendor</label>
                            <input type="text" class="form-control" name="vendor" value="<?php echo set_value('vendor', isset($expense_form_data['vendor']) ? $expense_form_data['vendor'] : ''); ?>">
                            <?php echo form_error('vendor','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-group">
                            <label>Notes</label>
                            <input type="text" class="form-control" name="notes" value="<?php echo set_value('notes', isset($expense_form_data['notes']) ? $expense_form_data['notes'] : ''); ?>">
                            <?php echo form_error('notes','<div class="text-danger small">','</div>'); ?>
                          </div>
                        </div>
                      </div>
                      <button type="submit" class="btn btn-primary">Save Expense</button>
                    <?php echo form_close(); ?>
                  </div>
                </div>
                <table class="table table-bordered table-striped">
                  <thead><tr><th>Category</th><th>Date</th><th>Amount</th><th>Vendor</th><th>Notes</th><th>Actions</th></tr></thead>
                  <tbody>
                    <?php if (!empty($expenses)) { foreach ($expenses as $expense) { ?>
                      <tr>
                        <td><?php echo html_escape($expense['category']); ?></td>
                        <td><?php echo !empty($expense['date']) ? date('d-m-Y', strtotime($expense['date'])) : ''; ?></td>
                        <td class="text-right"><?php echo $formatCurrency($expense['amount']); ?></td>
                        <td><?php echo html_escape($expense['vendor']); ?></td>
                        <td><?php echo html_escape($expense['notes']); ?></td>
                        <td>
                          <a href="<?php echo base_url('customer/delete_expense/'.$customer->customer_id.'/'.$expense['id']); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete this expense?');">Delete</a>
                        </td>
                      </tr>
                    <?php } } else { ?>
                      <tr><td colspan="6" class="text-center">No expenses.</td></tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

              <div class="tab-pane<?php echo ($active_group==='reminders'?' active in':''); ?>" id="reminders" <?php echo ($active_group==='reminders'?'':'style="display:none"'); ?> >
                <form method="post" action="<?php echo base_url('customer/add_reminder/'.$customer->customer_id); ?>" class="form-inline">
                  <div class="form-group">
                    <input type="text" name="title" class="form-control" placeholder="Reminder title" required>
                  </div>
                  <div class="form-group" style="margin-left:10px;">
                    <input type="datetime-local" name="remind_on" class="form-control">
                  </div>
                  <button type="submit" class="btn btn-primary" style="margin-left:10px;">Add</button>
                </form>
                <hr>
                <table class="table table-striped">
                  <thead><tr><th>Remind On</th><th>Title</th><th>Status</th><th>Action</th></tr></thead>
                  <tbody>
                    <?php if (!empty($reminders)) { foreach ($reminders as $reminder) { ?>
                      <tr>
                        <td><?php echo !empty($reminder['remind_on']) ? date('d-m-Y H:i', strtotime($reminder['remind_on'])) : ''; ?></td>
                        <td><?php echo html_escape($reminder['title']); ?></td>
                        <td><?php echo html_escape($reminder['status']); ?></td>
                        <td><a href="<?php echo base_url('customer/delete_reminder/'.$customer->customer_id.'/'.$reminder['id']); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete reminder?')">Delete</a></td>
                      </tr>
                    <?php } } else { ?>
                      <tr><td colspan="4" class="text-center">No reminders.</td></tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

              <div class="tab-pane<?php echo ($active_group==='map'?' active in':''); ?>" id="map" <?php echo ($active_group==='map'?'':'style="display:none"'); ?> >
                <?php $addr = trim(($customer->customer_address ?? '').' '.($customer->city ?? '').' '.($customer->country ?? '')); ?>
                <p>Address: <?php echo html_escape($addr); ?></p>
                <?php if ($addr) { ?>
                  <iframe width="100%" height="300" frameborder="0" style="border:0"
                    src="https://www.google.com/maps?q=<?php echo urlencode($addr); ?>&output=embed" allowfullscreen>
                  </iframe>
                <?php } else { ?>
                  <p class="text-muted">No address on file.</p>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Record Payment Modal (global, outside tabs) -->
<div class="modal fade" id="modalRecordPayment" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Record Payment for <?php echo html_escape($customer->customer_name); ?></h4>
      </div>
      <div class="modal-body">
        <form id="inlinePaymentForm">
          <input type="hidden" id="CSRF_TOKEN" value="<?php echo $this->security->get_csrf_hash(); ?>">
          <input type="hidden" id="ip_edit_vno" name="edit_vno" value="">
          <input type="hidden" name="voucher_no" id="ip_voucher_no" value="">
          <input type="hidden" name="customer_id" id="ip_customer_id" value="<?php echo html_escape($customer->customer_id); ?>">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label>Date</label>
                <input type="date" class="form-control" name="dtpDate" id="ip_date" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" min="0" class="form-control" name="txtAmount" id="ip_amount" required>
                <small class="text-muted">Max due: <span id="ip_due_hint">0.00</span></small>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label>Payment Method</label>
                <select name="multipaytype[]" id="ip_method" class="form-control" required>
                  <option value="">Select method</option>
                  <?php if (!empty($pay_methods)) { foreach ($pay_methods as $mid => $mname) { ?>
                    <option value="<?php echo html_escape($mid); ?>"><?php echo html_escape($mname); ?></option>
                  <?php } } ?>
                </select>
                <input type="hidden" name="pamount_by_method[]" id="ip_method_amount" value="">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Remarks</label>
            <textarea class="form-control" name="txtRemarks" id="ip_remarks" rows="2" placeholder="Optional note..."></textarea>
          </div>
          <div class="text-right">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success">Save Payment</button>
          </div>
        </form>
      </div>
      <div class="modal-footer hidden"></div>
      </div>
    </div>
    </div>

<!-- Modals -->
<div class="modal fade" id="modalAddContact" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Contact</h4></div>
    <div class="modal-body">
      <div class="form-group"><label>Name</label><input type="text" class="form-control"></div>
      <div class="form-group"><label>Email</label><input type="email" class="form-control"></div>
      <div class="form-group"><label>Phone</label><input type="text" class="form-control"></div>
      <div class="form-group"><label>Position</label><input type="text" class="form-control"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
  </div></div>
</div>

<div class="modal fade" id="modalAddProject" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Project</h4></div>
    <div class="modal-body">
      <div class="form-group"><label>Name</label><input type="text" class="form-control"></div>
      <div class="form-group"><label>Start</label><input type="date" class="form-control"></div>
      <div class="form-group"><label>End</label><input type="date" class="form-control"></div>
      <div class="form-group"><label>Progress (%)</label><input type="number" class="form-control" min="0" max="100"></div>
    </div>
    <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
  </div></div>
</div>

<div class="modal fade" id="modalAddTask" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Task</h4></div>
    <div class="modal-body">
      <div class="form-group"><label>Task</label><input type="text" class="form-control"></div>
      <div class="form-group"><label>Due Date</label><input type="date" class="form-control"></div>
      <div class="form-group"><label>Assignee</label><input type="text" class="form-control"></div>
      <div class="form-group"><label>Status</label><select class="form-control"><option>Open</option><option>In Progress</option><option>Done</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
  </div></div>
</div>

<div class="modal fade" id="modalAddTicket" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Ticket</h4></div>
    <div class="modal-body">
      <div class="form-group"><label>Subject</label><input type="text" class="form-control"></div>
      <div class="form-group"><label>Date</label><input type="date" class="form-control"></div>
      <div class="form-group"><label>Priority</label><select class="form-control"><option>Low</option><option>Medium</option><option>High</option></select></div>
      <div class="form-group"><label>Status</label><select class="form-control"><option>Open</option><option>Pending</option><option>Closed</option></select></div>
    </div>
    <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
  </div></div>
</div>

<!-- Tabs are now server-driven via ?group=... links; JS not needed -->
<script>
  (function(){
    var baseUrl = '<?php echo base_url(); ?>';
    $('#modalRecordPayment').on('show.bs.modal', function (e) {
      var $trigger = $(e.relatedTarget);
      var invoiceId = $trigger && $trigger.data('invoice') ? String($trigger.data('invoice')) : '';
      var due = $trigger && $trigger.data('due') ? parseFloat($trigger.data('due')) : 0;
      var vno = $trigger && $trigger.data('vno') ? String($trigger.data('vno')) : '';
      var amt = $trigger && $trigger.data('amount') ? parseFloat($trigger.data('amount')) : 0;
      var mth = $trigger && $trigger.data('method') ? String($trigger.data('method')) : '';
      var dt  = $trigger && $trigger.data('date') ? String($trigger.data('date')) : '';
      $('#ip_voucher_no').val(invoiceId);
      // edit vs create
      if (vno) {
        $('#ip_edit_vno').val(vno);
        if (amt > 0) { $('#ip_amount').val(amt.toFixed(2)); $('#ip_method_amount').val(amt.toFixed(2)); }
        if (mth) { $('#ip_method').val(mth); }
        if (dt) { $('#ip_date').val(dt); }
        $('#ip_due_hint').text('â€”');
        $('#ip_invoice_select_wrap').hide();
      } else {
        $('#ip_edit_vno').val('');
        $('#ip_amount').val(due.toFixed(2));
        $('#ip_method_amount').val(due.toFixed(2));
        $('#ip_due_hint').text(due.toFixed(2));
        // If opening without invoice, let user select due invoice
        if (!invoiceId) {
          $('#ip_invoice_select_wrap').show();
          $.ajax({
            url: baseUrl + 'account/accounts/customer_headcode',
            type: 'POST',
            dataType: 'json',
            data: { customer_id: $('#ip_customer_id').val(), csrf_test_name: $('#CSRF_TOKEN').val() },
            success: function(res){
              if (res && res.vouchers) {
                $('#ip_invoice_select_holder').html(res.vouchers);
                var $sel = $('#ip_invoice_select_holder').find('select');
                $sel.on('change', function(){
                  var inv = $(this).val();
                  $('#ip_voucher_no').val(inv);
                  // fetch due for selected
                  $.ajax({
                    url: baseUrl + 'account/accounts/customer_voucher_due_amount',
                    type: 'POST',
                    dataType: 'text',
                    data: { invoice_id: inv, csrf_test_name: $('#CSRF_TOKEN').val() },
                    success: function(txt){
                      var d = parseFloat(txt||0);
                      $('#ip_due_hint').text(d.toFixed(2));
                      $('#ip_amount').val(d.toFixed(2));
                      $('#ip_method_amount').val(d.toFixed(2));
                    }
                  });
                });
                // trigger initial
                setTimeout(function(){ $sel.trigger('change'); }, 50);
              } else {
                $('#ip_invoice_select_holder').html('<div class="text-warning">No due invoices.</div>');
              }
            },
            error: function(){ $('#ip_invoice_select_holder').html('<div class="text-danger">Failed to load invoices</div>'); }
          });
        } else {
          $('#ip_invoice_select_wrap').hide();
        }
      }
    });

    // Listen for payment completion from iframe and refresh Payments tab
    window.addEventListener('message', function(evt){
      if (!evt || !evt.data) return;
      try {
        var data = evt.data;
        if (typeof data === 'string') {
          data = JSON.parse(data);
        }
        if (data && data.type === 'paymentSaved') {
          var destRaw = data.return_to && data.return_to.length ? data.return_to : '<?php echo base_url('customer/customer_detail/'.$customer->customer_id.'?group=payments'); ?>';
          var dest = destRaw;
          try { dest = decodeURIComponent(destRaw); } catch(err) {}
          $('#modalRecordPayment').modal('hide');
          // small delay to allow modal to close animation
          setTimeout(function(){ window.location.href = dest; }, 150);
        }
      } catch (e) {
        // ignore
      }
    });

    // Delete payment
    $(document).on('click', '.js-delete-payment', function(){
      var vno = $(this).data('vno');
      if (!vno) return;
      if (!confirm('Delete this payment voucher '+vno+'?')) return;
      $.ajax({
        url: baseUrl + 'account/accounts/delete_customer_payment',
        type: 'POST',
        dataType: 'json',
        data: { vno: vno, csrf_test_name: $('#CSRF_TOKEN').val() },
        success: function(res){
          if (res && res.status) {
            if (window.toastr) toastr.success(res.message || 'Deleted');
            window.location.href = baseUrl + 'customer/customer_detail/<?php echo (int)$customer->customer_id; ?>?group=payments';
          } else {
            alert((res && (res.exception||res.message)) ? (res.exception||res.message) : 'Unable to delete');
          }
        },
        error: function(xhr){
          var msg = 'Request failed';
          try { if (xhr && xhr.responseText) { msg = xhr.responseText; } } catch(e) {}
          alert(msg);
        }
      });
    });

    // Delete invoice handler
    $(document).on('click', '.js-delete-invoice', function(){
      var invoiceId = $(this).data('invoice');
      if (!invoiceId) return;
      var csrf = $('#CSRF_TOKEN').val();
      var endpoint = baseUrl + 'invoice/invoice/bdtask_delete_invoice';
      var doDelete = function(force){
        $.ajax({
          url: endpoint,
          type: 'POST',
          dataType: 'json',
          data: { invoice_id: invoiceId, force_delete: force ? 1 : 0, csrf_test_name: csrf },
          success: function(res){
            if (res && res.status === true) {
              if (window.toastr) { toastr.success(res.message || 'Deleted'); }
              window.location.href = baseUrl + 'customer/customer_detail/<?php echo (int)$customer->customer_id; ?>?group=invoices';
              return;
            }
            if (res && res.needs_force) {
              if (confirm(res.message + '\n\nDelete anyway? This may remove linked vouchers/adjustments.')) {
                doDelete(true);
              }
              return;
            }
            alert(res && res.message ? res.message : 'Unable to delete invoice.');
          },
          error: function(){ alert('Request failed.'); }
        });
      };
      if (confirm('Delete this invoice? This action cannot be undone.')) {
        doDelete(false);
      }
    });

    // Inline payment submit
    $('#inlinePaymentForm').on('submit', function(ev){
      ev.preventDefault();
      var amount = parseFloat($('#ip_amount').val() || 0);
      var isEdit = ($('#ip_edit_vno').val() || '').length > 0;
      var due = parseFloat($('#ip_due_hint').text() || 0);
      var invId = ($('#ip_voucher_no').val() || '').trim();
      
      if (amount <= 0) { alert('Enter payment amount'); return; }
      if (!isEdit && invId && (amount > due + 0.0001)) { alert('Amount cannot exceed due'); return; }
      $('#ip_method_amount').val(amount.toFixed(2));
      var editVno = $('#ip_edit_vno').val();
      var data = {
        voucher_no: invId,
        dtpDate: $('#ip_date').val(),
        customer_id: $('#ip_customer_id').val(),
        txtRemarks: $('#ip_remarks').val(),
        txtAmount: amount.toFixed(2),
        'multipaytype[]': $('#ip_method').val(),
        'pamount_by_method[]': amount.toFixed(2),
        csrf_test_name: $('#CSRF_TOKEN').val()
      };
      var postUrl = editVno ? (baseUrl + 'account/accounts/update_customer_payment') : (baseUrl + 'account/accounts/create_customer_receive');
      if (editVno) { data['edit_vno'] = editVno; }
      $.ajax({
        url: postUrl,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function(res){
          if (res && res.status === true) {
            if (window.toastr) { toastr.success(res.message || 'Payment recorded'); }
            $('#modalRecordPayment').modal('hide');
            setTimeout(function(){ window.location.href = baseUrl + 'customer/customer_detail/<?php echo (int)$customer->customer_id; ?>?group=payments'; }, 150);
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






