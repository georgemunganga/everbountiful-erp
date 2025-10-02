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
                                        <th><?php echo display('date') ?></th>
                                        <th><?php echo display('name') ?></th>
                                        <th><?php echo display('phone') ?></th>
                                        <th class="text-right"><?php echo display('ammount') ?></th>
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
                                                        <?php echo html_escape($loan['person_name']); ?>
                                                    </a>
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
                                                <td><?php echo html_escape($loan['details']); ?></td>
                                                <td>
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
                                            <td colspan="6" class="text-center"><?php echo display('no_data_found'); ?></td>
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
