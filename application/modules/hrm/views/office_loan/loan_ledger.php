<?php
$currency = isset($currency) ? $currency : '';
$position = isset($position) ? $position : 0;
$format_currency = function ($value) use ($currency, $position) {
    $formatted = number_format((float) $value, 2, '.', ',');
    return ($position == 0)
        ? $currency . ' ' . $formatted
        : $formatted . ' ' . $currency;
};

$employee_name = '';
if (!empty($employee)) {
    $employee_name = trim($employee['first_name'] . ' ' . $employee['last_name']);
} elseif (!empty($person['person_name'])) {
    $employee_name = $person['person_name'];
}
$designation = !empty($employee['designation']) ? $employee['designation'] : '';
$primary_phone = !empty($employee['phone']) ? $employee['phone'] : (!empty($person['person_phone']) ? $person['person_phone'] : '');
$primary_address = !empty($employee['address_line_1']) ? $employee['address_line_1'] : (!empty($person['person_address']) ? $person['person_address'] : '');
$loan_details = isset($loan_details) && is_array($loan_details) ? $loan_details : array();

$summary_disbursement = '';
if (!empty($loan_details['disbursement_date'])) {
    $timestamp = strtotime($loan_details['disbursement_date']);
    $summary_disbursement = $timestamp ? date('M d, Y', $timestamp) : $loan_details['disbursement_date'];
}

$summary_start = '';
if (!empty($loan_details['repayment_start_date'])) {
    $timestamp = strtotime($loan_details['repayment_start_date']);
    $summary_start = $timestamp ? date('M d, Y', $timestamp) : $loan_details['repayment_start_date'];
}

$summary_end = '';
if (!empty($loan_details['repayment_end_date'])) {
    $timestamp = strtotime($loan_details['repayment_end_date']);
    $summary_end = $timestamp ? date('M d, Y', $timestamp) : $loan_details['repayment_end_date'];
}

$summary_period = isset($loan_details['repayment_period']) ? (int) $loan_details['repayment_period'] : null;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="btn-group">
            <a href="<?php echo base_url('manage_office_loans'); ?>" class="btn btn-primary m-b-5 m-r-2">
                <i class="ti-angle-left"></i> <?php echo display('manage_loan'); ?>
            </a>
            <a href="<?php echo base_url('office_loan_person_ledger/' . $person['person_id']); ?>" class="btn btn-info m-b-5 m-r-2">
                <i class="ti-list"></i> <?php echo display('person_ledger'); ?>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('loan_overview'); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="m-b-15"><?php echo display('name'); ?></h5>
                        <p class="m-b-5"><strong><?php echo html_escape($employee_name); ?></strong></p>
                        <?php if (!empty($designation)) { ?>
                            <p class="text-muted m-b-5"><?php echo html_escape($designation); ?></p>
                        <?php } ?>
                        <?php if (!empty($person['person_id'])) { ?>
                            <p class="m-b-5"><span class="text-muted"><?php echo display('employee_id'); ?>:</span> <?php echo html_escape($person['person_id']); ?></p>
                        <?php } ?>
                        <?php if (!empty($primary_phone)) { ?>
                            <p class="m-b-5"><span class="text-muted"><?php echo display('phone'); ?>:</span> <?php echo html_escape($primary_phone); ?></p>
                        <?php } ?>
                        <?php if (!empty($primary_address)) { ?>
                            <p class="m-b-0"><span class="text-muted"><?php echo display('address'); ?>:</span> <?php echo html_escape($primary_address); ?></p>
                        <?php } ?>
                    </div>
                    <div class="col-md-6">
                        <h5 class="m-b-15"><?php echo display('loan'); ?> <?php echo display('summary'); ?></h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo display('ammount'); ?></th>
                                        <th><?php echo display('paid'); ?></th>
                                        <th><?php echo display('outstanding'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-right"><?php echo $format_currency($total_debit); ?></td>
                                        <td class="text-right"><?php echo $format_currency($total_credit); ?></td>
                                        <td class="text-right"><?php echo $format_currency($outstanding); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!empty($loan_details['monthly_installment']) && $loan_details['monthly_installment'] > 0) { ?>
                        <div class="m-t-15">
                            <div class="alert alert-info">
                                <h6 class="m-b-10"><i class="fa fa-calculator"></i> <?php echo display('payroll_deduction_info'); ?></h6>
                                <p class="m-b-5"><strong><?php echo display('monthly_deduction'); ?>:</strong> <?php echo $format_currency($loan_details['monthly_installment']); ?></p>
                                <?php 
                                $remaining_amount = $outstanding;
                                $monthly_installment = (float) $loan_details['monthly_installment'];
                                $remaining_payments = $monthly_installment > 0 ? ceil($remaining_amount / $monthly_installment) : 0;
                                ?>
                                <p class="m-b-5"><strong><?php echo display('remaining_payments'); ?>:</strong> <?php echo $remaining_payments; ?> <?php echo display('months'); ?></p>
                                <?php if (!empty($loan_details['next_due_date'])) { ?>
                                <p class="m-b-0"><strong><?php echo display('next_deduction'); ?>:</strong> <?php echo date('M d, Y', strtotime($loan_details['next_due_date'])); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <div class="m-t-15">
                            <p class="m-b-5"><span class="text-muted"><?php echo display('disbursement_date'); ?>:</span> <?php echo !empty($summary_disbursement) ? html_escape($summary_disbursement) : '-'; ?></p>
                            <p class="m-b-5"><span class="text-muted"><?php echo display('repayment_start_date'); ?>:</span> <?php echo !empty($summary_start) ? html_escape($summary_start) : '-'; ?></p>
                            <p class="m-b-5"><span class="text-muted"><?php echo display('repayment_end_date'); ?>:</span> <?php echo !empty($summary_end) ? html_escape($summary_end) : '-'; ?></p>
                            <p class="m-b-0"><span class="text-muted"><?php echo display('repayment_period'); ?>:</span> <?php echo $summary_period ? html_escape($summary_period . ' ' . display('months')) : '-'; ?></p>
                        </div>
                    </div>
                </div>

                <div class="m-t-30">
                    <h5 class="m-b-15"><?php echo display('person_ledger'); ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo display('date'); ?></th>
                                    <th><?php echo display('details'); ?></th>
                                    <th class="text-right"><?php echo display('debit'); ?></th>
                                    <th class="text-right"><?php echo display('credit'); ?></th>
                                    <th class="text-right"><?php echo display('balance'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($ledger)) { ?>
                                    <?php foreach ($ledger as $entry) { ?>
                                        <tr>
                                            <td><?php echo html_escape($entry['date']); ?></td>
                                            <td><?php echo html_escape($entry['details']); ?></td>
                                            <td class="text-right"><?php echo $format_currency($entry['debit']); ?></td>
                                            <td class="text-right"><?php echo $format_currency($entry['credit']); ?></td>
                                            <td class="text-right"><?php echo $format_currency($entry['balance']); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted"><?php echo display('no_data_found'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="m-t-30">
                    <h5 class="m-b-15"><?php echo display('deduction'); ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo display('date'); ?></th>
                                    <th><?php echo display('details'); ?></th>
                                    <th class="text-right"><?php echo display('paid'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($deductions)) { ?>
                                    <?php foreach ($deductions as $deduction) { ?>
                                        <tr>
                                            <td><?php echo html_escape($deduction['date']); ?></td>
                                            <td><?php echo html_escape($deduction['details']); ?></td>
                                            <td class="text-right"><?php echo $format_currency($deduction['credit']); ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted"><?php echo display('no_data_found'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (!empty($payroll_deductions)) { ?>
                <div class="m-t-30">
                    <h5 class="m-b-15"><?php echo display('payroll_deductions'); ?></h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo display('salary_month'); ?></th>
                                    <th><?php echo display('deduction_date'); ?></th>
                                    <th class="text-right"><?php echo display('amount'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payroll_deductions as $payroll_deduction) { ?>
                                    <tr>
                                        <td><?php echo html_escape($payroll_deduction['sal_month_year']); ?></td>
                                        <td><?php echo html_escape(date('M d, Y', strtotime($payroll_deduction['createDate']))); ?></td>
                                        <td class="text-right"><?php echo $format_currency($payroll_deduction['office_loan_deduct']); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>