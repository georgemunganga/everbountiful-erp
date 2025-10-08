<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('loan_ledger'); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php
                $person_name_label     = display('person_name') ? display('person_name') : 'Person Name';
                $address_label         = display('address') ? display('address') : 'Address';
                $designation_label     = display('designation') ? display('designation') : 'Designation';
                $monthly_label         = display('monthly_installment') ? display('monthly_installment') : 'Monthly Installment';
                $repayment_schedule    = display('repayment_schedule') ? display('repayment_schedule') : 'Repayment Schedule';
                $next_due_label        = display('next_due_date') ? display('next_due_date') : 'Next Due Date';
                $deduction_history     = display('deduction_history') ? display('deduction_history') : 'Deduction History';
                $payroll_deductions_lb = display('payroll_deductions') ? display('payroll_deductions') : 'Payroll Deductions';

                $person_name    = isset($person['person_name']) ? $person['person_name'] : '';
                $person_phone   = isset($person['person_phone']) ? $person['person_phone'] : '';
                $person_email   = isset($employee['email']) ? $employee['email'] : (isset($person['person_email']) ? $person['person_email'] : '');
                $person_address = isset($person['person_address']) ? $person['person_address'] : '';

                $employee_name = '';
                if (!empty($employee)) {
                    $first = isset($employee['first_name']) ? $employee['first_name'] : '';
                    $last  = isset($employee['last_name']) ? $employee['last_name'] : '';
                    $employee_name = trim($first . ' ' . $last);
                }

                $employee_phone = isset($employee['phone']) ? $employee['phone'] : '';
                $designation    = isset($employee['designation']) ? $employee['designation'] : '';

                $loan_amount          = isset($loan_details['principal_amount']) ? (float) $loan_details['principal_amount'] : (isset($total_debit) ? (float) $total_debit : 0);
                $disbursement_date    = isset($loan_details['disbursement_date']) ? $loan_details['disbursement_date'] : '';
                $repayment_period     = isset($loan_details['repayment_period']) ? (int) $loan_details['repayment_period'] : null;
                $monthly_installment  = isset($loan_details['monthly_installment']) ? (float) $loan_details['monthly_installment'] : null;
                $repayment_start_date = isset($loan_details['repayment_start_date']) ? $loan_details['repayment_start_date'] : '';
                $repayment_end_date   = isset($loan_details['repayment_end_date']) ? $loan_details['repayment_end_date'] : '';
                $next_due_date        = isset($loan_details['next_due_date']) ? $loan_details['next_due_date'] : '';
                $total_paid           = isset($loan_details['total_paid']) ? (float) $loan_details['total_paid'] : (isset($total_credit) ? (float) $total_credit : 0);
                $outstanding_balance  = isset($outstanding) ? (float) $outstanding : ($loan_amount - $total_paid);
                ?>
                <div class="row">
                    <div class="col-sm-6">
                        <h5><strong><?php echo $person_name_label; ?>:</strong> <?php echo html_escape($person_name); ?></h5>
                        <?php if (!empty($employee_name)) { ?>
                            <h5><strong><?php echo display('employee_name'); ?>:</strong> <?php echo html_escape($employee_name); ?></h5>
                        <?php } ?>
                        <?php if (!empty($designation)) { ?>
                            <h5><strong><?php echo $designation_label; ?>:</strong> <?php echo html_escape($designation); ?></h5>
                        <?php } ?>
                        <?php if (!empty($person_phone)) { ?>
                            <h5><strong><?php echo display('phone'); ?>:</strong> <?php echo html_escape($person_phone); ?></h5>
                        <?php } elseif (!empty($employee_phone)) { ?>
                            <h5><strong><?php echo display('phone'); ?>:</strong> <?php echo html_escape($employee_phone); ?></h5>
                        <?php } ?>
                        <?php if (!empty($person_email)) { ?>
                            <h5><strong><?php echo display('email'); ?>:</strong> <?php echo html_escape($person_email); ?></h5>
                        <?php } ?>
                        <?php if (!empty($person_address)) { ?>
                            <h5><strong><?php echo $address_label; ?>:</strong> <?php echo html_escape($person_address); ?></h5>
                        <?php } ?>
                    </div>
                    <div class="col-sm-6">
                        <h5><strong><?php echo display('loan_amount'); ?>:</strong> <?php echo number_format($loan_amount, 2); ?></h5>
                        <?php if (!empty($disbursement_date)) { ?>
                            <h5><strong>Loan Date:</strong> <?php echo html_escape($disbursement_date); ?></h5>
                        <?php } ?>
                        <?php if ($repayment_period !== null) { ?>
                            <h5><strong><?php echo display('repayment_period'); ?>:</strong> <?php echo html_escape($repayment_period); ?> <?php echo display('months'); ?></h5>
                        <?php } ?>
                        <?php if ($monthly_installment !== null) { ?>
                            <h5><strong><?php echo $monthly_label; ?>:</strong> <?php echo number_format($monthly_installment, 2); ?></h5>
                        <?php } ?>
                        <?php if (!empty($repayment_start_date) || !empty($repayment_end_date)) { ?>
                            <h5>
                                <strong><?php echo $repayment_schedule; ?>:</strong>
                                <?php echo html_escape(trim($repayment_start_date . ' - ' . $repayment_end_date, ' -')); ?>
                            </h5>
                        <?php } ?>
                        <?php if (!empty($next_due_date)) { ?>
                            <h5><strong><?php echo $next_due_label; ?>:</strong> <?php echo html_escape($next_due_date); ?></h5>
                        <?php } ?>
                        <h5><strong>Total Paid:</strong> <?php echo number_format($total_paid, 2); ?></h5>
                        <h5><strong><?php echo display('balance'); ?>:</strong> <?php echo number_format($outstanding_balance, 2); ?></h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="dataTableExample2" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?php echo display('date'); ?></th>
                                <th><?php echo display('description'); ?></th>
                                <th class="text-right"><?php echo display('debit'); ?></th>
                                <th class="text-right"><?php echo display('credit'); ?></th>
                                <th class="text-right"><?php echo display('balance'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ledger)) { ?>
                                <?php foreach ($ledger as $row) { ?>
                                    <?php
                                    $entry_date    = isset($row['date']) ? $row['date'] : (isset($row['VDate']) ? date('Y-m-d', strtotime($row['VDate'])) : '');
                                    $description   = isset($row['details']) ? $row['details'] : (isset($row['Narration']) ? $row['Narration'] : '');
                                    $debit_amount  = isset($row['debit']) ? (float) $row['debit'] : (isset($row['Debit']) ? (float) $row['Debit'] : 0);
                                    $credit_amount = isset($row['credit']) ? (float) $row['credit'] : (isset($row['Credit']) ? (float) $row['Credit'] : 0);
                                    $row_balance   = isset($row['balance']) ? (float) $row['balance'] : ($debit_amount - $credit_amount);
                                    ?>
                                    <tr>
                                        <td><?php echo html_escape($entry_date); ?></td>
                                        <td><?php echo nl2br(html_escape($description)); ?></td>
                                        <td class="text-right"><?php echo $debit_amount > 0 ? number_format($debit_amount, 2) : '-'; ?></td>
                                        <td class="text-right"><?php echo $credit_amount > 0 ? number_format($credit_amount, 2) : '-'; ?></td>
                                        <td class="text-right"><?php echo number_format($row_balance, 2); ?></td>
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
                                <th colspan="2"><?php echo display('total'); ?></th>
                                <th class="text-right"><?php echo number_format(isset($total_debit) ? $total_debit : 0, 2); ?></th>
                                <th class="text-right"><?php echo number_format(isset($total_credit) ? $total_credit : 0, 2); ?></th>
                                <th class="text-right"><?php echo number_format($outstanding_balance, 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php if (!empty($deductions)) { ?>
                    <div class="table-responsive m-t-30">
                        <h5><?php echo $deduction_history; ?></h5>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo display('date'); ?></th>
                                    <th class="text-right"><?php echo display('credit'); ?></th>
                                    <th><?php echo display('description'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deductions as $deduction) { ?>
                                    <tr>
                                        <td><?php echo html_escape(isset($deduction['date']) ? $deduction['date'] : ''); ?></td>
                                        <td class="text-right"><?php echo number_format(isset($deduction['credit']) ? $deduction['credit'] : 0, 2); ?></td>
                                        <td><?php echo nl2br(html_escape(isset($deduction['details']) ? $deduction['details'] : '')); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
                <?php if (!empty($payroll_deductions)) { ?>
                    <div class="table-responsive m-t-30">
                        <h5><?php echo $payroll_deductions_lb; ?></h5>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo display('month'); ?></th>
                                    <th class="text-right"><?php echo display('amount'); ?></th>
                                    <th><?php echo display('date'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payroll_deductions as $entry) { ?>
                                    <tr>
                                        <td><?php echo html_escape(isset($entry['sal_month_year']) ? $entry['sal_month_year'] : ''); ?></td>
                                        <td class="text-right"><?php echo number_format(isset($entry['office_loan_deduct']) ? $entry['office_loan_deduct'] : 0, 2); ?></td>
                                        <td><?php echo html_escape(isset($entry['createDate']) ? $entry['createDate'] : ''); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
