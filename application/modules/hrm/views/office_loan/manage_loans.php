        <div class="row">
            <div class="col-sm-12">
                <?php if($this->permission1->method('add_office_loan','create')->access()){ ?>
                    <a href="<?php echo base_url('add_office_loan')?>" class="btn btn-success m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_office_loan')?> </a>
                <?php }?>
                <?php if($this->permission1->method('add_office_loan_payment','create')->access()){ ?>
                    <a href="<?php echo base_url('add_office_loan_payment')?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_payment')?> </a>
                <?php }?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo display('manage_loan') ?> </h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table id="dataTableExample3" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo display('disbursement_date'); ?></th>
                                        <th><?php echo display('employee_name'); ?></th>
                                        <th><?php echo display('phone'); ?></th>
                                        <th class="text-right"><?php echo display('loan_amount'); ?></th>
                                        <th class="text-right">Paid</th>
                                        <th class="text-right">Outstanding</th>
                                        <th><?php echo display('details') ?></th>
                                        <th><?php echo display('action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($loan_list)) { ?>
                                        <?php foreach ($loan_list as $loan) { ?>
                                            <tr>
                                                <td><?php echo html_escape($loan['date']); ?></td>
                                                <td>
                                                    <a href="<?php echo base_url('office_loan_person_ledger/' . $loan['person_id']); ?>">
                                                        <?php echo html_escape($loan['employee_display_name']); ?>
                                                    </a>
                                                    <?php if (!empty($loan['person_name']) && $loan['person_name'] !== $loan['employee_display_name']) { ?>
                                                        <div><small class="text-muted"><?php echo html_escape($loan['person_name']); ?></small></div>
                                                    <?php } ?>
                                                    <?php if (!empty($loan['person_id'])) { ?>
                                                        <div><small class="text-muted"><?php echo html_escape($loan['person_id']); ?></small></div>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo html_escape($loan['person_phone']); ?></td>
                                                <td class="text-right">
                                                    <?php
                                                        $formatted_amount = number_format((float) $loan['debit'], 2, '.', ',');
                                                        if ($position == 0) {
                                                            echo $currency . ' ' . $formatted_amount;
                                                        } else {
                                                            echo $formatted_amount . ' ' . $currency;
                                                        }
                                                    ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php
                                                        $formatted_paid = number_format((float) $loan['total_credit'], 2, '.', ',');
                                                        if ($position == 0) {
                                                            echo $currency . ' ' . $formatted_paid;
                                                        } else {
                                                            echo $formatted_paid . ' ' . $currency;
                                                        }
                                                    ?>
                                                </td>
                                                <td class="text-right">
                                                    <?php
                                                        $formatted_outstanding = number_format((float) $loan['outstanding'], 2, '.', ',');
                                                        if ($position == 0) {
                                                            echo $currency . ' ' . $formatted_outstanding;
                                                        } else {
                                                            echo $formatted_outstanding . ' ' . $currency;
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo html_escape($loan['details']); ?>
                                                    <?php if (!empty($loan['disbursement_date']) || !empty($loan['repayment_start_date']) || !empty($loan['repayment_end_date']) || !empty($loan['repayment_period']) || !empty($loan['payment_channel_label'])) { ?>
                                                        <div class="small text-muted m-t-5">
                                                            <?php if (!empty($loan['disbursement_date'])) { ?>
                                                                <div><?php echo display('disbursement_date'); ?>: <?php echo html_escape($loan['disbursement_date']); ?></div>
                                                            <?php } ?>
                                                            <?php if (!empty($loan['repayment_start_date'])) { ?>
                                                                <div><?php echo display('repayment_start_date'); ?>: <?php echo html_escape($loan['repayment_start_date']); ?></div>
                                                            <?php } ?>
                                                            <?php if (!empty($loan['repayment_end_date'])) { ?>
                                                                <div><?php echo display('repayment_end_date'); ?>: <?php echo html_escape($loan['repayment_end_date']); ?></div>
                                                            <?php } ?>
                                                            <?php if (!empty($loan['repayment_period'])) { ?>
                                                                <div><?php echo display('repayment_period'); ?>: <?php echo html_escape($loan['repayment_period']); ?> <?php echo display('months'); ?></div>
                                                            <?php } ?>
                                                            <?php if (!empty($loan['payment_channel_label'])) { ?>
                                                                <div><?php echo display('payment_type') ?: 'Payment Channel'; ?>: <?php echo html_escape($loan['payment_channel_label']); ?></div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if($this->permission1->method('manage_office_loan_person','read')->access()){ ?>
                                                        <a href="<?php echo base_url('office_loan_overview/' . $loan['person_id']); ?>" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="<?php echo display('view'); ?>">
                                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if($this->permission1->method('manage_office_loan_person','update')->access()){ ?>
                                                        <a href="<?php echo base_url('bdtask_edit_office_loan/' . $loan['transaction_id']); ?>" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="<?php echo display('update'); ?>">
                                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if($this->permission1->method('manage_office_loan_person','delete')->access()){ ?>
                                                        <a href="<?php echo base_url('delete_office_loan_entry/' . $loan['transaction_id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo display('are_you_sure') ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="8" class="text-center"><?php echo display('no_data_found'); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right"><?php echo display('total'); ?></th>
                                        <th class="text-right">
                                            <?php
                                                $formatted_total = number_format((float) $total_amount, 2, '.', ',');
                                                if ($position == 0) {
                                                    echo $currency . ' ' . $formatted_total;
                                                } else {
                                                    echo $formatted_total . ' ' . $currency;
                                                }
                                            ?>
                                        </th>
                                        <th class="text-right"></th>
                                        <th class="text-right"></th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="text-right"><?php echo $links ?></div>
                    </div>
                </div>
            </div>
        </div>
