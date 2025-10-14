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
        <?php
          $summary = isset($statement['summary']) && is_array($statement['summary']) ? $statement['summary'] : array('beginning'=>0,'invoiced'=>0,'paid'=>0,'balance_due'=>0);
          $lines = isset($statement['lines']) && is_array($statement['lines']) ? $statement['lines'] : array();
        ?>
        <p>Period: <?php echo date('d-m-Y', strtotime($from_date)); ?> To <?php echo date('d-m-Y', strtotime($to_date)); ?></p>
        <table class="table table-bordered">
          <tr><th>Beginning Balance</th><td>K<?php echo number_format($summary['beginning'], 2); ?></td></tr>
          <tr><th>Invoiced Amount</th><td>K<?php echo number_format($summary['invoiced'], 2); ?></td></tr>
          <tr><th>Amount Paid</th><td>K<?php echo number_format($summary['paid'], 2); ?></td></tr>
          <tr><th>Balance Due</th><td>K<?php echo number_format($summary['balance_due'], 2); ?></td></tr>
        </table>

        <h5>Transactions</h5>
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Date</th>
              <th>Details</th>
              <th>Debit</th>
              <th>Credit</th>
              <th>Balance</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($lines)) { foreach ($lines as $line) { ?>
              <tr>
                <td><?php echo html_escape(date('d-m-Y', strtotime($line['date']))); ?></td>
                <td><?php echo html_escape($line['description']); ?></td>
                <td class="text-right"><?php echo $line['debit'] > 0 ? number_format($line['debit'], 2) : ''; ?></td>
                <td class="text-right"><?php echo $line['credit'] > 0 ? number_format($line['credit'], 2) : ''; ?></td>
                <td class="text-right"><?php echo number_format($line['balance'], 2); ?></td>
              </tr>
            <?php } } else { ?>
              <tr><td colspan="5" class="text-center">No activity for selected period.</td></tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
