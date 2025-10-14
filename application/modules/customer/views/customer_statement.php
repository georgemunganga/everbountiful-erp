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
          <a class="btn btn-success" style="margin-left:10px;" href="<?php echo base_url('customer/statement/'.$customer->customer_id.'/pdf?from_date='.$from_date.'&to_date='.$to_date); ?>">Download PDF</a>
        </form>
        <hr>

        <h5>Account Summary</h5>
        <?php
          $summaryRaw = (isset($statement['summary']) && is_array($statement['summary'])) ? $statement['summary'] : array();
          $summaryDefaults = array(
            'beginning'   => 0.0,
            'invoiced'    => 0.0,
            'paid'        => 0.0,
            'balance_due' => 0.0,
          );
          $summary = array_merge($summaryDefaults, array_intersect_key($summaryRaw, $summaryDefaults));
          foreach ($summary as $key => $value) {
            $summary[$key] = is_numeric($value) ? (float) $value : 0.0;
          }

          $lines = array();
          if (isset($statement['lines']) && is_array($statement['lines'])) {
            foreach ($statement['lines'] as $rawLine) {
              if (!is_array($rawLine)) {
                continue;
              }
              $lines[] = array(
                'date'        => isset($rawLine['date']) ? $rawLine['date'] : '',
                'description' => isset($rawLine['description']) ? $rawLine['description'] : '',
                'debit'       => isset($rawLine['debit']) && is_numeric($rawLine['debit']) ? (float) $rawLine['debit'] : 0.0,
                'credit'      => isset($rawLine['credit']) && is_numeric($rawLine['credit']) ? (float) $rawLine['credit'] : 0.0,
                'balance'     => isset($rawLine['balance']) && is_numeric($rawLine['balance']) ? (float) $rawLine['balance'] : 0.0,
              );
            }
          }
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
            <?php if (!empty($lines)) { foreach ($lines as $line) { 
              $lineDate = '';
              if (!empty($line['date'])) {
                $lineTs = strtotime($line['date']);
                $lineDate = $lineTs ? date('d-m-Y', $lineTs) : '';
              }
              $lineDescription = isset($line['description']) ? $line['description'] : '';
              $debitAmount = isset($line['debit']) ? (float)$line['debit'] : 0.0;
              $creditAmount = isset($line['credit']) ? (float)$line['credit'] : 0.0;
              $balanceAmount = isset($line['balance']) ? (float)$line['balance'] : ($debitAmount - $creditAmount);
            ?>
              <tr>
                <td><?php echo html_escape($lineDate); ?></td>
                <td><?php echo html_escape($lineDescription); ?></td>
                <td class="text-right"><?php echo $debitAmount > 0 ? number_format($debitAmount, 2) : ''; ?></td>
                <td class="text-right"><?php echo $creditAmount > 0 ? number_format($creditAmount, 2) : ''; ?></td>
                <td class="text-right"><?php echo number_format($balanceAmount, 2); ?></td>
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
