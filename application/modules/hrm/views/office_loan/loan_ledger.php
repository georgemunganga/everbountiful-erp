<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('loan_ledger') ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <h5><strong><?php echo display('employee_name') ?>:</strong> <?php echo $loan_details['first_name'] . ' ' . $loan_details['last_name']; ?></h5>
                        <h5><strong><?php echo display('phone') ?>:</strong> <?php echo $loan_details['phone']; ?></h5>
                        <h5><strong><?php echo display('email') ?>:</strong> <?php echo $loan_details['email']; ?></h5>
                    </div>
                    <div class="col-sm-6">
                        <h5><strong><?php echo display('loan_amount') ?>:</strong> <?php echo number_format($loan_details['loan_amount'], 2); ?></h5>
                        <h5><strong><?php echo display('loan_date') ?>:</strong> <?php echo date('Y-m-d', strtotime($loan_details['loan_date'])); ?></h5>
                        <h5><strong><?php echo display('balance') ?>:</strong> <?php echo number_format($balance, 2); ?></h5>
                    </div>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="dataTableExample2" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?php echo display('date') ?></th>
                                <th><?php echo display('description') ?></th>
                                <th><?php echo display('debit') ?></th>
                                <th><?php echo display('credit') ?></th>
                                <th><?php echo display('balance') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ledger)) { ?>
                                <?php $running_balance = 0; ?>
                                <?php foreach ($ledger as $row) { ?>
                                    <?php $running_balance += ($row['Debit'] - $row['Credit']); ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d', strtotime($row['VDate'])); ?></td>
                                        <td><?php echo $row['Narration']; ?></td>
                                        <td><?php echo ($row['Debit'] > 0) ? number_format($row['Debit'], 2) : ''; ?></td>
                                        <td><?php echo ($row['Credit'] > 0) ? number_format($row['Credit'], 2) : ''; ?></td>
                                        <td><?php echo number_format($running_balance, 2); ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center"><?php echo display('no_data_found'); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2"><?php echo display('total') ?></th>
                                <th><?php echo number_format($total_debit, 2); ?></th>
                                <th><?php echo number_format($total_credit, 2); ?></th>
                                <th><?php echo number_format($balance, 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>