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
              color: #37a000;
            }
            .vertical-nav-tabs li.active a {
              background: #37a000;
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
              background: #37a000;
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
        $return_query['tab'] = 'payments';
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
        <div class="vertical-tabs-container">
          <ul class="vertical-nav-tabs" role="tablist">
            <li class="active"><a href="#profile" data-toggle="tab">Profile</a></li>
            <li><a href="#contacts" data-toggle="tab">Contacts</a></li>
            <li><a href="#notes" data-toggle="tab">Notes</a></li>
            <li><a href="#statement" data-toggle="tab">Statement</a></li>
            <li><a href="#invoices" data-toggle="tab">Invoices</a></li>
            <li><a href="#payments" data-toggle="tab">Payments</a></li>
            <li><a href="#credit_notes" data-toggle="tab">Credit Notes</a></li>
            <li><a href="#estimates" data-toggle="tab">Estimates</a></li>
            <li><a href="#expenses" data-toggle="tab">Expenses</a></li>
            <li><a href="#reminders" data-toggle="tab">Reminders</a></li>
            <li><a href="#map" data-toggle="tab">Map</a></li>
          </ul>

          <div class="vertical-tab-content">
            <div class="tab-content">
              <div class="tab-pane active" id="profile">
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

              <div class="tab-pane" id="contacts">
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

              <div class="tab-pane" id="notes">
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

              <div class="tab-pane" id="statement">
                <div class="row">
                  <div class="col-sm-4">
                <form method="get">
                  <input type="hidden" name="tab" value="statement">
                      <div class="form-group">
                        <label>From</label>
                        <input type="date" name="from_date" class="form-control" value="<?php echo html_escape($from_date ?? ''); ?>">
                      </div>
                      <div class="form-group">
                        <label>To</label>
                        <input type="date" name="to_date" class="form-control" value="<?php echo html_escape($to_date ?? ''); ?>">
                      </div>
                      <button type="submit" class="btn btn-primary">Filter</button>
                      <a href="<?php echo base_url('customer/customer_statement_pdf/'.$customer->customer_id); ?>?from_date=<?php echo urlencode($from_date ?? ''); ?>&to_date=<?php echo urlencode($to_date ?? ''); ?>" target="_blank" class="btn btn-default">Download PDF</a>
                    </form>
                  </div>
                  <div class="col-sm-8">
                    <h4>Account Statement</h4>
                    <table class="table table-bordered table-striped">
                      <thead><tr><th>Date</th><th>Description</th><th>Debit</th><th>Credit</th><th>Balance</th></tr></thead>
                      <tbody>
                        <?php
                          $runningBalance = 0;
                          if (!empty($statement)) {
                            foreach ($statement as $entry) {
                              $runningBalance += $entry['debit'] - $entry['credit']; ?>
                              <tr>
                                <td><?php echo date('d-m-Y', strtotime($entry['date'])); ?></td>
                                <td><?php echo html_escape($entry['description']); ?></td>
                                <td class="text-right"><?php echo $entry['debit'] ? $formatCurrency($entry['debit'], false) : ''; ?></td>
                                <td class="text-right"><?php echo $entry['credit'] ? $formatCurrency($entry['credit'], false) : ''; ?></td>
                                <td class="text-right"><?php echo $formatCurrency($runningBalance); ?></td>
                              </tr>
                            <?php }
                          } else { ?>
                            <tr><td colspan="5" class="text-center">No activity for selected period.</td></tr>
                          <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="invoices">
                <div class="clearfix" style="margin-bottom:10px;">
                  <a href="<?php echo base_url('add_invoice?customer_id=' . urlencode($customer->customer_id)); ?>" class="btn btn-success">New Invoice</a>
                </div>
                <table class="table table-bordered table-striped">
                  <thead><tr><th>Invoice #</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Action</th></tr></thead>
                  <tbody>
                    <?php if (!empty($invoices)) { foreach ($invoices as $invoice) { ?>
                      <?php $invoice_number = isset($invoice['invoice_no']) ? $invoice['invoice_no'] : (isset($invoice['invoice']) ? $invoice['invoice'] : $invoice['invoice_id']); ?>
                      <tr>
                        <td><a href="<?php echo base_url('invoice/invoice_details/'.$invoice['invoice_id'].'?customer_id=' . urlencode($customer->customer_id)); ?>"><?php echo html_escape($invoice_number); ?></a></td>
                        <td><?php echo date('d-m-Y', strtotime($invoice['date'])); ?></td>
                        <td class="text-right"><?php echo $formatCurrency($invoice['total_amount']); ?></td>
                        <td class="text-right"><?php echo $formatCurrency($invoice['paid_amount'], false); ?></td>
                        <td class="text-right"><?php echo $formatCurrency($invoice['due_amount'], false); ?></td>
                        <td><span class="<?php echo html_escape($invoice['status_class']); ?>"><?php echo html_escape($invoice['status_label']); ?></span></td>
                        <td>
                          <a href="<?php echo base_url('invoice/invoice_details/'.$invoice['invoice_id'].'?customer_id=' . urlencode($customer->customer_id)); ?>" class="btn btn-xs btn-info">View</a>
                          <a href="<?php echo base_url('invoice/edit_invoice/'.$invoice['invoice_id'].'?customer_id=' . urlencode($customer->customer_id)); ?>" class="btn btn-xs btn-primary">Edit</a>
                        </td>
                      </tr>
                    <?php } } else { ?>
                      <tr><td colspan="7" class="text-center">No invoices found</td></tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

              <div class="tab-pane" id="payments">
                <div class="clearfix" style="margin-bottom:10px;">
                  <a class="btn btn-success" href="<?php echo base_url('customer_receive?customer_id=' . urlencode($customer->customer_id) . '&return_to=' . urlencode($return_payments_url)); ?>">Record Payment</a>
                </div>
                <table class="table table-bordered table-striped">
                  <thead><tr><th>Date</th><th>Voucher</th><th>Narration</th><th>Debit</th><th>Credit</th></tr></thead>
                  <tbody>
                    <?php if (!empty($payments)) { foreach ($payments as $payment) { ?>
                      <tr>
                        <td><?php echo date('d-m-Y', strtotime($payment['date'])); ?></td>
                        <td><?php echo html_escape($payment['voucher_no']); ?></td>
                        <td><?php echo html_escape($payment['Narration']); ?></td>
                        <td class="text-right"><?php echo $payment['Debit'] ? $formatCurrency($payment['Debit'], false) : ''; ?></td>
                        <td class="text-right"><?php echo $payment['Credit'] ? $formatCurrency($payment['Credit'], false) : ''; ?></td>
                      </tr>
                    <?php } } else { ?>
                      <tr><td colspan="5" class="text-center">No payments recorded.</td></tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

              <div class="tab-pane" id="credit_notes">
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

              <div class="tab-pane" id="estimates">
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

              <div class="tab-pane" id="expenses">
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

              <div class="tab-pane" id="reminders">
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

              <div class="tab-pane" id="map">
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

<script>
$(function () {
  var storageKey = 'customer_detail_tab_<?php echo (int) $customer->customer_id; ?>';
  var $tabs = $('.vertical-nav-tabs a[data-toggle="tab"]');
  var params = new URLSearchParams(window.location.search);
  var targetTab = params.get('tab') ? '#' + params.get('tab') : null;
  var stored = targetTab || localStorage.getItem(storageKey);
  if (stored && $tabs.filter('[href="' + stored + '"]').length) {
    $tabs.filter('[href="' + stored + '"]').tab('show');
  } else {
    $tabs.filter(':first').tab('show');
  }
  $tabs.on('shown.bs.tab', function (e) {
    localStorage.setItem(storageKey, $(e.target).attr('href'));
  });
});
</script>
