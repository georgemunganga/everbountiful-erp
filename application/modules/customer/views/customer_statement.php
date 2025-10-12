<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-bd lobidrag">
      <div class="panel-heading">
        <div class="panel-title">
          <h4>Customer Statement For <?php echo html_escape($customer->customer_name); ?></h4>
        </div>
      </div>
      <div class="panel-body">
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
        <hr>

        <h5>Account Summary</h5>
        <?php $s = $statement['summary']; ?>
        <p>Period: <?php echo date('d-m-Y', strtotime($from_date)); ?> To <?php echo date('d-m-Y', strtotime($to_date)); ?></p>
        <table class="table table-bordered">
          <tr><th>Beginning Balance</th><td>$<?php echo number_format($s['beginning'], 2); ?></td></tr>
          <tr><th>Invoiced Amount</th><td>$<?php echo number_format($s['invoiced'], 2); ?></td></tr>
          <tr><th>Amount Paid</th><td>$<?php echo number_format($s['paid'], 2); ?></td></tr>
          <tr><th>Balance Due</th><td>$<?php echo number_format($s['balance_due'], 2); ?></td></tr>
        </table>

        <h5>Transactions</h5>
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Date</th>
              <th>Details</th>
              <th>Amount</th>
              <th>Payments</th>
              <th>Balance</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($statement['lines'] as $line) { ?>
              <tr>
                <td><?php echo html_escape($line['date']); ?></td>
                <td><?php echo html_escape($line['details']); ?></td>
                <td class="text-right"><?php echo $line['amount']; ?></td>
                <td class="text-right"><?php echo $line['payments']; ?></td>
                <td class="text-right"><?php echo $line['balance']; ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>