<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-bd lobidrag">
      <div class="panel-heading">
        <div class="panel-title">
          <h4><?php echo html_escape($title); ?>: <?php echo html_escape($customer->customer_name); ?></h4>
        </div>
      </div>
      <div class="panel-body">
        <ul class="nav nav-tabs" role="tablist">
          <li class="active"><a href="#profile" role="tab" data-toggle="tab">Profile</a></li>
          <li><a href="#contacts" role="tab" data-toggle="tab">Contacts</a></li>
          <li><a href="#notes" role="tab" data-toggle="tab">Notes</a></li>
          <li><a href="#statement" role="tab" data-toggle="tab">Statement</a></li>
          <li><a href="#invoices" role="tab" data-toggle="tab">Invoices</a></li>
          <li><a href="#payments" role="tab" data-toggle="tab">Payments</a></li>
          <li><a href="#proposals" role="tab" data-toggle="tab">Proposals</a></li>
          <li><a href="#credit_notes" role="tab" data-toggle="tab">Credit Notes</a></li>
          <li><a href="#estimates_contract_expenses" role="tab" data-toggle="tab">Estimates, contract and expenses</a></li>
        </ul>

        <div class="tab-content" style="margin-top:15px;">
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
            <p>Notes placeholder. Integrate your notes subsystem here.</p>
          </div>

          <div class="tab-pane" id="statement">
            <p>Statement placeholder. Embed ledger/statement widget.</p>
          </div>

          <div class="tab-pane" id="invoices">
            <p>Invoices placeholder. List customer invoices.</p>
          </div>

          <div class="tab-pane" id="payments">
            <p>Payments placeholder. List customer payments.</p>
          </div>

          <div class="tab-pane" id="proposals">
            <p>Proposals placeholder.</p>
          </div>

          <div class="tab-pane" id="credit_notes">
            <p>Credit Notes placeholder.</p>
          </div>

          <div class="tab-pane" id="estimates_contract_expenses">
            <p>Estimates, contract and expenses placeholder.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>