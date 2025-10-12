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
          /* Desktop vertical tabs layout */
          @media (min-width: 768px) {
            .vertical-tabs-container {
              display: flex;
              min-height: 500px;
            }
            .vertical-nav-tabs {
              flex: 0 0 250px;
              background-color: #f8f9fa;
              border-right: 1px solid #ddd;
              padding: 0;
              margin: 0;
              list-style: none;
            }
            .vertical-nav-tabs li {
              display: block;
              width: 100%;
              border-bottom: 1px solid #ddd;
            }
            .vertical-nav-tabs li a {
              display: block;
              padding: 12px 20px;
              color: #333;
              text-decoration: none;
              border: none;
              border-radius: 0;
              background: none;
              transition: all 0.3s ease;
            }
            .vertical-nav-tabs li a:hover {
              background-color: #e9ecef;
              color: #37a000;
            }
            .vertical-nav-tabs li.active a {
              background-color: #37a000;
              color: white;
              border-left: 4px solid #2d8000;
            }
            .vertical-tab-content {
              flex: 1;
              padding: 20px;
              background-color: white;
            }
          }
          
          /* Mobile responsive - horizontal tabs */
          @media (max-width: 767px) {
            .vertical-tabs-container {
              display: block;
            }
            .vertical-nav-tabs {
              display: flex;
              flex-wrap: wrap;
              background-color: #f8f9fa;
              border-bottom: 1px solid #ddd;
              padding: 0;
              margin: 0 0 15px 0;
              list-style: none;
              overflow-x: auto;
            }
            .vertical-nav-tabs li {
              flex: 0 0 auto;
              border-right: 1px solid #ddd;
            }
            .vertical-nav-tabs li a {
              display: block;
              padding: 10px 15px;
              color: #333;
              text-decoration: none;
              border: none;
              border-radius: 0;
              background: none;
              white-space: nowrap;
              font-size: 14px;
            }
            .vertical-nav-tabs li a:hover {
              background-color: #e9ecef;
              color: #37a000;
            }
            .vertical-nav-tabs li.active a {
              background-color: #37a000;
              color: white;
              border-bottom: 3px solid #2d8000;
            }
            .vertical-tab-content {
              padding: 10px;
            }
          }
        </style>
        
        <div class="vertical-tabs-container">
          <ul class="vertical-nav-tabs" role="tablist">
            <li class="active"><a href="#profile" role="tab" data-toggle="tab">Profile</a></li>
            <li><a href="#contacts" role="tab" data-toggle="tab">Contacts</a></li>
            <li><a href="#notes" role="tab" data-toggle="tab">Notes</a></li>
            <li><a href="#statement" role="tab" data-toggle="tab">Statement</a></li>
            <li><a href="#invoices" role="tab" data-toggle="tab">Invoices</a></li>
            <li><a href="#payments" role="tab" data-toggle="tab">Payments</a></li>
            <li><a href="#proposals" role="tab" data-toggle="tab">Proposals</a></li>
            <li><a href="#credit_notes" role="tab" data-toggle="tab">Credit Notes</a></li>
            <li><a href="#estimates" role="tab" data-toggle="tab">Estimates</a></li>
            <li><a href="#subscriptions" role="tab" data-toggle="tab">Subscriptions</a></li>
            <li><a href="#expenses" role="tab" data-toggle="tab">Expenses</a></li>
            <li><a href="#contracts" role="tab" data-toggle="tab">Contracts</a></li>
            <li><a href="#projects" role="tab" data-toggle="tab">Projects</a></li>
            <li><a href="#tasks" role="tab" data-toggle="tab">Tasks</a></li>
            <li><a href="#tickets" role="tab" data-toggle="tab">Tickets</a></li>
            <li><a href="#files" role="tab" data-toggle="tab">Files</a></li>
            <li><a href="#vault" role="tab" data-toggle="tab">Vault</a></li>
            <li><a href="#reminders" role="tab" data-toggle="tab">Reminders</a></li>
            <li><a href="#map" role="tab" data-toggle="tab">Map</a></li>
          </ul>

          <div class="vertical-tab-content">
            <div class="tab-content">
          <div class="tab-pane active" id="profile">
            <div class="row">
              <div class="col-sm-6">
                <h5>Basic Info</h5>
                <p><strong>Name:</strong> <?php echo html_escape($customer->customer_name); ?></p>
                <p><strong>Mobile:</strong> <?php echo html_escape($customer->customer_mobile); ?></p>
                <p><strong>Email:</strong> <?php echo html_escape($customer->customer_email); ?></p>
                <p><strong>City:</strong> <?php echo html_escape($customer->city); ?></p>
                <p><strong>Country:</strong> <?php echo html_escape($customer->country); ?></p>
              </div>
              <div class="col-sm-6">
                <h5>Address</h5>
                <p><?php echo nl2br(html_escape($customer->customer_address)); ?></p>
                <p><?php echo nl2br(html_escape($customer->address2)); ?></p>
              </div>
            </div>
          </div>

          <div class="tab-pane" id="contacts">
            <p>Phone: <?php echo html_escape($customer->phone); ?> | Fax: <?php echo html_escape($customer->fax); ?></p>
            <p>Additional Email: <?php echo html_escape($customer->email_address); ?></p>
          </div>

          <div class="tab-pane" id="notes">
            <form method="post" action="<?php echo base_url('customer/add_note/'.$customer->customer_id); ?>" class="form-inline">
              <div class="form-group">
                <input type="text" name="note_text" class="form-control" placeholder="Add note..." required>
              </div>
              <button type="submit" class="btn btn-primary" style="margin-left:10px;">Add</button>
            </form>
            <hr>
            <table class="table table-striped">
              <thead><tr><th>Created</th><th>Note</th><th>Action</th></tr></thead>
              <tbody>
              <?php if (!empty($notes)) { foreach ($notes as $n) { ?>
                <tr>
                  <td><?php echo date('d-m-Y H:i', strtotime($n['created_at'])); ?></td>
                  <td><?php echo html_escape($n['note_text']); ?></td>
                  <td><a href="<?php echo base_url('customer/delete_note/'.$customer->customer_id.'/'.$n['id']); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete note?')">Delete</a></td>
                </tr>
              <?php } } else { ?>
                <tr><td colspan="3" class="text-center">No notes</td></tr>
              <?php } ?>
              </tbody>
            </table>
          </div>

          <div class="tab-pane" id="statement">
            <style>
              .statement-header {
                border-bottom: 2px solid #37a000;
                padding-bottom: 20px;
                margin-bottom: 30px;
              }
              .company-info {
                float: left;
                width: 50%;
              }
              .company-name {
                font-size: 24px;
                font-weight: bold;
                color: #37a000;
                margin-bottom: 5px;
              }
              .company-address {
                color: #666;
                line-height: 1.3;
              }
              .statement-info {
                float: right;
                width: 45%;
                text-align: right;
              }
              .statement-title {
                font-size: 28px;
                font-weight: bold;
                color: #37a000;
                margin-bottom: 10px;
              }
              .statement-meta {
                color: #666;
              }
              .clearfix::after {
                content: "";
                display: table;
                clear: both;
              }
              .customer-section {
                margin: 30px 0;
                padding: 20px;
                background-color: #f8f9fa;
                border-left: 4px solid #37a000;
              }
              .customer-title {
                font-size: 16px;
                font-weight: bold;
                margin-bottom: 10px;
                color: #333;
              }
              .summary-section {
                margin: 30px 0;
              }
              .summary-title {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 15px;
                color: #333;
                border-bottom: 1px solid #ddd;
                padding-bottom: 5px;
              }
              .summary-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
              }
              .summary-table td {
                padding: 8px 12px;
                border: 1px solid #ddd;
              }
              .summary-table .label {
                background-color: #f8f9fa;
                font-weight: bold;
                width: 60%;
              }
              .summary-table .amount {
                text-align: right;
                font-weight: bold;
              }
              .transactions-section {
                margin: 30px 0;
              }
              .transactions-title {
                font-size: 18px;
                font-weight: bold;
                margin-bottom: 15px;
                color: #333;
                border-bottom: 1px solid #ddd;
                padding-bottom: 5px;
              }
              .transactions-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
              }
              .transactions-table th {
                background-color: #37a000;
                color: #ffffff;
                padding: 12px 8px;
                text-align: left;
                font-weight: bold;
              }
              .transactions-table td {
                padding: 10px 8px;
                border-bottom: 1px solid #ddd;
              }
              .transactions-table tr:nth-child(even) {
                background-color: #f8f9fa;
              }
              .amount-cell {
                text-align: right;
                font-family: monospace;
              }
              .balance-due {
                margin-top: 20px;
                padding: 15px;
                background-color: #eaf7ea;
                border: 2px solid #37a000;
                text-align: center;
              }
              .balance-due-label {
                font-size: 16px;
                font-weight: bold;
                color: #333;
              }
              .balance-due-amount {
                font-size: 24px;
                font-weight: bold;
                color: #37a000;
                margin-top: 5px;
              }
              .period-info {
                margin: 20px 0;
                padding: 10px;
                background-color: #fff3cd;
                border: 1px solid #ffeaa7;
                border-radius: 4px;
                text-align: center;
                font-style: italic;
              }
              .controls-section {
                margin-bottom: 30px;
                padding: 15px;
                background-color: #f8f9fa;
                border-radius: 5px;
              }
            </style>
            
            <!-- Controls Section -->
            <div class="controls-section">
              <form class="form-inline" method="get">
                <div class="form-group">
                  <label>From</label>
                  <input type="date" name="from_date" class="form-control" value="<?php echo html_escape($from_date); ?>">
                </div>
                <div class="form-group" style="margin-left:10px;">
                  <label>To</label>
                  <input type="date" name="to_date" class="form-control" value="<?php echo html_escape($to_date); ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="margin-left:10px;">Update</button>
                <a class="btn btn-success" style="margin-left:10px;" href="<?php echo base_url('customer/customer_statement_pdf/'.$customer->customer_id.'?from_date='.$from_date.'&to_date='.$to_date); ?>">Download PDF</a>
              </form>
            </div>

            <!-- Header Section -->
            <div class="statement-header clearfix">
              <div class="company-info">
                <div class="company-name">Your Company Name</div>
                <div class="company-address">
                  123 Business Street<br>
                  Business City, State 12345<br>
                  Phone: (555) 123-4567<br>
                  Email: info@yourcompany.com
                </div>
              </div>
              <div class="statement-info">
                <div class="statement-title">STATEMENT</div>
                <div class="statement-meta">
                  Statement Date: <?php echo date('F j, Y'); ?><br>
                  Statement Period: <?php echo date('M j, Y', strtotime($from_date)); ?> - <?php echo date('M j, Y', strtotime($to_date)); ?>
                </div>
              </div>
            </div>

            <!-- Customer Information -->
            <div class="customer-section">
              <div class="customer-title">Bill To:</div>
              <div class="customer-address">
                <strong><?php echo html_escape($customer->customer_name); ?></strong><br>
                <?php if (!empty($customer->customer_address)) { ?>
                  <?php echo nl2br(html_escape($customer->customer_address)); ?><br>
                <?php } ?>
                <?php if (!empty($customer->address2)) { ?>
                  <?php echo nl2br(html_escape($customer->address2)); ?><br>
                <?php } ?>
                <?php if (!empty($customer->city) || !empty($customer->state) || !empty($customer->zip)) { ?>
                  <?php echo html_escape($customer->city); ?><?php echo !empty($customer->state) ? ', ' . html_escape($customer->state) : ''; ?> <?php echo html_escape($customer->zip); ?><br>
                <?php } ?>
                <?php if (!empty($customer->country)) { ?>
                  <?php echo html_escape($customer->country); ?><br>
                <?php } ?>
                <?php if (!empty($customer->customer_mobile)) { ?>
                  Phone: <?php echo html_escape($customer->customer_mobile); ?><br>
                <?php } ?>
                <?php if (!empty($customer->customer_email)) { ?>
                  Email: <?php echo html_escape($customer->customer_email); ?>
                <?php } ?>
              </div>
            </div>

            <!-- Period Information -->
            <div class="period-info">
              Showing all invoices and payments between <?php echo date('M j, Y', strtotime($from_date)); ?> and <?php echo date('M j, Y', strtotime($to_date)); ?>
            </div>

            <!-- Account Summary -->
            <div class="summary-section">
              <div class="summary-title">Account Summary</div>
              <table class="summary-table">
                <?php $s = $statement['summary']; ?>
                <tr>
                  <td class="label">Beginning Balance:</td>
                  <td class="amount">$<?php echo number_format($s['beginning'], 2); ?></td>
                </tr>
                <tr>
                  <td class="label">Invoiced Amount:</td>
                  <td class="amount">$<?php echo number_format($s['invoiced'], 2); ?></td>
                </tr>
                <tr>
                  <td class="label">Amount Paid:</td>
                  <td class="amount">$<?php echo number_format($s['paid'], 2); ?></td>
                </tr>
                <tr style="background-color: #eaf7ea;">
                  <td class="label" style="font-size: 14px;"><strong>Balance Due:</strong></td>
                  <td class="amount" style="font-size: 14px; color: #37a000;"><strong>$<?php echo number_format($s['balance_due'], 2); ?></strong></td>
                </tr>
              </table>
            </div>

            <!-- Transaction Details -->
            <div class="transactions-section">
              <div class="transactions-title">Transaction Details</div>
              <table class="transactions-table">
                <thead>
                  <tr>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 45%;">Details</th>
                    <th style="width: 15%;">Amount</th>
                    <th style="width: 15%;">Payments</th>
                    <th style="width: 15%;">Balance</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($statement['lines'] as $line) { ?>
                    <tr>
                      <td><?php echo html_escape($line['date']); ?></td>
                      <td><?php echo html_escape($line['details']); ?></td>
                      <td class="amount-cell"><?php echo $line['amount'] ? '$' . $line['amount'] : ''; ?></td>
                      <td class="amount-cell"><?php echo $line['payments'] ? '$' . $line['payments'] : ''; ?></td>
                      <td class="amount-cell"><strong>$<?php echo $line['balance']; ?></strong></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>

            <!-- Balance Due Highlight -->
            <div class="balance-due">
              <div class="balance-due-label">Total Balance Due</div>
              <div class="balance-due-amount">$<?php echo number_format($s['balance_due'], 2); ?></div>
            </div>
          </div>

          <div class="tab-pane" id="invoices">
            <table class="table table-bordered">
              <thead><tr><th>Date</th><th>Invoice</th><th>Total</th><th>Paid</th><th>Due</th></tr></thead>
              <tbody>
              <?php if (!empty($invoices)) { foreach ($invoices as $inv) { ?>
                <tr>
                  <td><?php echo date('d-m-Y', strtotime($inv['date'])); ?></td>
                  <td><?php echo html_escape($inv['invoice_no']); ?></td>
                  <td class="text-right">$<?php echo number_format($inv['total_amount'], 2); ?></td>
                  <td class="text-right">$<?php echo number_format($inv['paid_amount'], 2); ?></td>
                  <td class="text-right">$<?php echo number_format($inv['due_amount'], 2); ?></td>
                </tr>
              <?php } } else { ?>
                <tr><td colspan="5" class="text-center">No invoices</td></tr>
              <?php } ?>
              </tbody>
            </table>
          </div>

          <div class="tab-pane" id="payments">
            <table class="table table-bordered">
              <thead><tr><th>Date</th><th>Voucher</th><th>Narration</th><th>Amount</th></tr></thead>
              <tbody>
              <?php if (!empty($payments)) { foreach ($payments as $p) { ?>
                <tr>
                  <td><?php echo date('d-m-Y', strtotime($p['date'])); ?></td>
                  <td><?php echo html_escape($p['voucher_no']); ?></td>
                  <td><?php echo html_escape($p['Narration']); ?></td>
                  <td class="text-right">$<?php echo number_format($p['Credit'], 2); ?></td>
                </tr>
              <?php } } else { ?>
                <tr><td colspan="4" class="text-center">No payments</td></tr>
              <?php } ?>
              </tbody>
            </table>
          </div>

          <div class="tab-pane" id="files">
            <form method="post" action="<?php echo base_url('customer/upload_file/'.$customer->customer_id); ?>" enctype="multipart/form-data">
              <div class="form-group">
                <input type="file" name="file" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary">Upload</button>
            </form>
            <hr>
            <table class="table table-striped">
              <thead><tr><th>Uploaded</th><th>File</th><th>Action</th></tr></thead>
              <tbody>
              <?php if (!empty($files)) { foreach ($files as $f) { ?>
                <tr>
                  <td><?php echo date('d-m-Y H:i', strtotime($f['uploaded_at'])); ?></td>
                  <td><a href="<?php echo base_url($f['file_path']); ?>" target="_blank"><?php echo html_escape($f['file_name']); ?></a></td>
                  <td><a href="<?php echo base_url('customer/delete_file/'.$customer->customer_id.'/'.$f['id']); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete file?')">Delete</a></td>
                </tr>
              <?php } } else { ?>
                <tr><td colspan="3" class="text-center">No files</td></tr>
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
              <?php if (!empty($reminders)) { foreach ($reminders as $r) { ?>
                <tr>
                  <td><?php echo !empty($r['remind_on']) ? date('d-m-Y H:i', strtotime($r['remind_on'])) : ''; ?></td>
                  <td><?php echo html_escape($r['title']); ?></td>
                  <td><?php echo html_escape($r['status']); ?></td>
                  <td><a href="<?php echo base_url('customer/delete_reminder/'.$customer->customer_id.'/'.$r['id']); ?>" class="btn btn-xs btn-danger" onclick="return confirm('Delete reminder?')">Delete</a></td>
                </tr>
              <?php } } else { ?>
                <tr><td colspan="4" class="text-center">No reminders</td></tr>
              <?php } ?>
              </tbody>
            </table>
          </div>

          <div class="tab-pane" id="map">
            <?php $addr = trim($customer->customer_address.' '.$customer->city.' '.$customer->country); ?>
            <p>Address: <?php echo html_escape($addr); ?></p>
            <iframe width="100%" height="300" frameborder="0" style="border:0"
              src="https://www.google.com/maps?q=<?php echo urlencode($addr); ?>&output=embed" allowfullscreen>
            </iframe>
          </div>

          <!-- Proposals -->
          <div class="tab-pane" id="proposals">
            <div class="clearfix" style="margin-bottom:10px;">
              <input type="text" class="form-control" placeholder="Search proposals..." style="width:250px; display:inline-block;">
              <button class="btn btn-success pull-right" data-toggle="modal" data-target="#modalAddProposal">Add Proposal</button>
            </div>
            <table class="table table-bordered table-striped">
              <thead><tr><th>Title</th><th>Date</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
              <tbody>
                <tr><td colspan="5" class="text-center">No proposals</td></tr>
              </tbody>
            </table>
          </div>
          <!-- Credit Notes -->
          <div class="tab-pane" id="credit_notes">
            <div class="clearfix" style="margin-bottom:10px;">
              <input type="text" class="form-control" placeholder="Search credit notes..." style="width:250px; display:inline-block;">
              <button class="btn btn-success pull-right" data-toggle="modal" data-target="#modalAddCreditNote">Add Credit Note</button>
            </div>
            <table class="table table-bordered table-striped">
              <thead><tr><th>Number</th><th>Date</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
              <tbody>
                <tr><td colspan="5" class="text-center">No credit notes</td></tr>
              </tbody>
            </table>
          </div>
          <!-- Estimates -->
          <div class="tab-pane" id="estimates">
            <div class="clearfix" style="margin-bottom:10px;">
              <input type="text" class="form-control" placeholder="Search estimates..." style="width:250px; display:inline-block;">
              <button class="btn btn-success pull-right" data-toggle="modal" data-target="#modalAddEstimate">Add Estimate</button>
            </div>
            <table class="table table-bordered table-striped">
              <thead><tr><th>Estimate No</th><th>Date</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
              <tbody>
                <tr><td colspan="5" class="text-center">No estimates</td></tr>
              </tbody>
            </table>
          </div>
          <!-- Subscriptions -->
          <div class="tab-pane" id="subscriptions">
            <div class="clearfix" style="margin-bottom:10px;">
              <input type="text" class="form-control" placeholder="Search subscriptions..." style="width:250px; display:inline-block;">
              <button class="btn btn-success pull-right" data-toggle="modal" data-target="#modalAddSubscription">Add Subscription</button>
            </div>
            <table class="table table-bordered table-striped">
              <thead><tr><th>Plan</th><th>Start Date</th><th>Next Billing</th><th>Status</th><th>Action</th></tr></thead>
              <tbody>
                <tr><td colspan="5" class="text-center">No subscriptions</td></tr>
              </tbody>
            </table>
          </div>
          <!-- Expenses -->
          <div class="tab-pane" id="expenses">
            <div class="clearfix" style="margin-bottom:10px;">
              <input type="text" class="form-control" placeholder="Search expenses..." style="width:250px; display:inline-block;">
              <button class="btn btn-success pull-right" data-toggle="modal" data-target="#modalAddExpense">Add Expense</button>
            </div>
            <table class="table table-bordered table-striped">
              <thead><tr><th>Category</th><th>Date</th><th>Amount</th><th>Vendor</th><th>Action</th></tr></thead>
              <tbody>
                <tr><td colspan="5" class="text-center">No expenses</td></tr>
              </tbody>
            </table>
          </div>
          <!-- Contracts -->
          <div class="tab-pane" id="contracts">
            <div class="clearfix" style="margin-bottom:10px;">
              <input type="text" class="form-control" placeholder="Search contracts..." style="width:250px; display:inline-block;">
              <button class="btn btn-success pull-right" data-toggle="modal" data-target="#modalAddContract">Add Contract</button>
            </div>
            <table class="table table-bordered table-striped">
              <thead><tr><th>Contract No</th><th>Start</th><th>End</th><th>Status</th><th>Action</th></tr></thead>
              <tbody>
                <tr><td colspan="5" class="text-center">No contracts</td></tr>
              </tbody>
            </table>
          </div>
          <!-- Projects -->
          <div class="tab-pane" id="projects">
            <div class="clearfix" style="margin-bottom:10px;">
              <input type="text" class="form-control" placeholder="Search projects..." style="width:250px; display:inline-block;">
              <button class="btn btn-success pull-right" data-toggle="modal" data-target="#modalAddProject">Add Project</button>
            </div>
            <table class="table table-bordered table-striped">
              <thead><tr><th>Name</th><th>Start</th><th>End</th><th>Progress</th><th>Action</th></tr></thead>
              <tbody>
                <tr><td colspan="5" class="text-center">No projects</td></tr>
              </tbody>
            </table>
          </div>
          <!-- Tasks -->
          <div class="tab-pane" id="tasks">
            <div class="clearfix" style="margin-bottom:10px;">
              <input type="text" class="form-control" placeholder="Search tasks..." style="width:250px; display:inline-block;">
              <button class="btn btn-success pull-right" data-toggle="modal" data-target="#modalAddTask">Add Task</button>
            </div>
            <table class="table table-bordered table-striped">
              <thead><tr><th>Task</th><th>Due Date</th><th>Status</th><th>Assignee</th><th>Action</th></tr></thead>
              <tbody>
                <tr><td colspan="5" class="text-center">No tasks</td></tr>
              </tbody>
            </table>
          </div>
          <!-- Tickets -->
          <div class="tab-pane" id="tickets">
            <div class="clearfix" style="margin-bottom:10px;">
              <input type="text" class="form-control" placeholder="Search tickets..." style="width:250px; display:inline-block;">
              <button class="btn btn-success pull-right" data-toggle="modal" data-target="#modalAddTicket">Add Ticket</button>
            </div>
            <table class="table table-bordered table-striped">
              <thead><tr><th>Subject</th><th>Date</th><th>Status</th><th>Priority</th><th>Action</th></tr></thead>
              <tbody>
                <tr><td colspan="5" class="text-center">No tickets</td></tr>
              </tbody>
            </table>
          </div>
          <!-- Vault -->
          <div class="tab-pane" id="vault">
            <p class="text-muted">Store sensitive credentials securely. For demo purposes, entries are not persisted.</p>
            <div class="row">
              <div class="col-sm-6">
                <div class="form-group"><label>Name</label><input type="text" class="form-control" placeholder="Resource name"></div>
                <div class="form-group"><label>Username</label><input type="text" class="form-control" placeholder="Username"></div>
                <div class="form-group"><label>Password</label><input type="password" class="form-control" placeholder="Password"></div>
                <div class="form-group"><label>URL</label><input type="url" class="form-control" placeholder="https://..."></div>
                <button class="btn btn-primary" disabled>Save (demo)</button>
              </div>
              <div class="col-sm-6">
                <table class="table table-striped"><thead><tr><th>Name</th><th>Username</th><th>URL</th></tr></thead><tbody><tr><td colspan="3" class="text-center">No entries</td></tr></tbody></table>
              </div>
            </div>
          </div>

          <!-- Add-item modals (demo, non-persistent) -->
          <div class="modal fade" id="modalAddProposal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Proposal</h4></div>
            <div class="modal-body">
              <div class="form-group"><label>Title</label><input type="text" class="form-control"></div>
              <div class="form-group"><label>Date</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>Amount</label><input type="number" step="0.01" class="form-control"></div>
              <div class="form-group"><label>Status</label><select class="form-control"><option>Draft</option><option>Sent</option><option>Accepted</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
          </div></div></div>
          <div class="modal fade" id="modalAddCreditNote" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Credit Note</h4></div>
            <div class="modal-body">
              <div class="form-group"><label>Number</label><input type="text" class="form-control"></div>
              <div class="form-group"><label>Date</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>Amount</label><input type="number" step="0.01" class="form-control"></div>
              <div class="form-group"><label>Status</label><select class="form-control"><option>Open</option><option>Applied</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
          </div></div></div>
          <div class="modal fade" id="modalAddEstimate" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Estimate</h4></div>
            <div class="modal-body">
              <div class="form-group"><label>Estimate No</label><input type="text" class="form-control"></div>
              <div class="form-group"><label>Date</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>Amount</label><input type="number" step="0.01" class="form-control"></div>
              <div class="form-group"><label>Status</label><select class="form-control"><option>Draft</option><option>Sent</option><option>Accepted</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
          </div></div></div>
          <div class="modal fade" id="modalAddSubscription" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Subscription</h4></div>
            <div class="modal-body">
              <div class="form-group"><label>Plan</label><input type="text" class="form-control"></div>
              <div class="form-group"><label>Start Date</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>Next Billing</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>Status</label><select class="form-control"><option>Active</option><option>Paused</option><option>Cancelled</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
          </div></div></div>
          <div class="modal fade" id="modalAddExpense" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Expense</h4></div>
            <div class="modal-body">
              <div class="form-group"><label>Category</label><input type="text" class="form-control"></div>
              <div class="form-group"><label>Date</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>Amount</label><input type="number" step="0.01" class="form-control"></div>
              <div class="form-group"><label>Vendor</label><input type="text" class="form-control"></div>
            </div>
            <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
          </div></div></div>
          <div class="modal fade" id="modalAddContract" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Contract</h4></div>
            <div class="modal-body">
              <div class="form-group"><label>Contract No</label><input type="text" class="form-control"></div>
              <div class="form-group"><label>Start</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>End</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>Status</label><select class="form-control"><option>Active</option><option>Expired</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
          </div></div></div>
          <div class="modal fade" id="modalAddProject" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Project</h4></div>
            <div class="modal-body">
              <div class="form-group"><label>Name</label><input type="text" class="form-control"></div>
              <div class="form-group"><label>Start</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>End</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>Progress (%)</label><input type="number" class="form-control" min="0" max="100"></div>
            </div>
            <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
          </div></div></div>
          <div class="modal fade" id="modalAddTask" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Task</h4></div>
            <div class="modal-body">
              <div class="form-group"><label>Task</label><input type="text" class="form-control"></div>
              <div class="form-group"><label>Due Date</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>Assignee</label><input type="text" class="form-control"></div>
              <div class="form-group"><label>Status</label><select class="form-control"><option>Open</option><option>In Progress</option><option>Done</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
          </div></div></div>
          <div class="modal fade" id="modalAddTicket" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Add Ticket</h4></div>
            <div class="modal-body">
              <div class="form-group"><label>Subject</label><input type="text" class="form-control"></div>
              <div class="form-group"><label>Date</label><input type="date" class="form-control"></div>
              <div class="form-group"><label>Priority</label><select class="form-control"><option>Low</option><option>Medium</option><option>High</option></select></div>
              <div class="form-group"><label>Status</label><select class="form-control"><option>Open</option><option>Pending</option><option>Closed</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-default" data-dismiss="modal">Close</button><button class="btn btn-primary" disabled>Save (demo)</button></div>
          </div></div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>