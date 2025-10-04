<?php
$loan_today = date('Y-m-d');
$loan_details = isset($loan_details) && is_array($loan_details) ? $loan_details : array();
$base_disbursement = !empty($loan_details['disbursement_date']) ? $loan_details['disbursement_date'] : (!empty($loan['date']) ? $loan['date'] : $loan_today);
$disbursement_date = $base_disbursement;
$repayment_period = !empty($loan_details['repayment_period']) ? (int) $loan_details['repayment_period'] : 12;
if ($repayment_period < 1) {
    $repayment_period = 12;
}
$default_start_date = date('Y-m-d', strtotime('+1 month', strtotime($disbursement_date)));
$repayment_start_date = !empty($loan_details['repayment_start_date']) ? $loan_details['repayment_start_date'] : $default_start_date;
$repayment_end_date = !empty($loan_details['repayment_end_date']) ? $loan_details['repayment_end_date'] : date('Y-m-d', strtotime('+' . ($repayment_period - 1) . ' month', strtotime($repayment_start_date)));
?>
        <div class="row">
            <div class="col-sm-12">
                <?php if($this->permission1->method('manage_ofln_person','read')->access()){ ?>
                    <a href="<?php echo base_url('manage_office_loans')?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-align-justify"> </i> <?php echo display('manage_loan')?> </a>
                <?php }?>
                <?php if($this->permission1->method('add_loan_payment','create')->access()){ ?>
                    <a href="<?php echo base_url('add_office_loan_payment')?>" class="btn btn-success m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_payment')?> </a>
                <?php }?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo display('update')?> </h4>
                        </div>
                    </div>
                   <?php echo form_open_multipart('hrm/loan/bdtask_update_office_loan',array('class' => 'form-vertical','id' => 'update_office_loan' ))?>
                    <input type="hidden" name="transaction_id" value="<?php echo html_escape($transaction_id);?>" />
                    <div class="panel-body">
                        <div class="form-group row">
                            <label for="employee_id" class="col-sm-3 col-form-label"><?php echo display('name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" required name="employee_id" id="employee_id" tabindex="1">
                                    <option value=''><?php echo display('select_one')?></option>
                                    <?php if(!empty($employees)){foreach($employees as $employee){
                                        $selected = ($employee_id == $employee->id) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $employee->id?>" data-phone="<?php echo html_escape($employee->phone);?>" <?php echo $selected;?>>
                                            <?php echo html_escape(trim($employee->first_name.' '.$employee->last_name));?>
                                        </option>
                                    <?php }}?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-sm-3 col-form-label"><?php echo display('phone') ?></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control phone" name="phone" id="phone" value="<?php echo html_escape($loan['person_phone']);?>" placeholder="<?php echo display('phone') ?>" tabindex="2"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="ammount" class="col-sm-3 col-form-label"><?php echo display('ammount') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                               <input type="number" class="form-control" name="ammount" id="ammount" required placeholder="<?php echo display('ammount') ?>" min="0" tabindex="3" value="<?php echo html_escape($loan['debit']);?>"/>
                            </div>
                        </div>

                        <div class="form-group row" id="payment_from">
                            <label for="payment_type" class="col-sm-3 col-form-label"><?php echo display('payment_type'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select name="paytype" class="form-control" required onchange="bank_paymetExpense(this.value)" tabindex="4">
                                    <option value="1" <?php echo ($paytype == 1)?'selected':'';?>><?php echo display('cash_payment')?></option>
                                    <option value="2" <?php echo ($paytype == 2)?'selected':'';?>><?php echo display('bank_payment')?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="disbursement_date" class="col-sm-3 col-form-label"><?php echo display('disbursement_date'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datepicker" name="disbursement_date" id="disbursement_date" value="<?php echo html_escape($disbursement_date); ?>" required tabindex="5"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_period" class="col-sm-3 col-form-label"><?php echo display('repayment_period'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="repayment_period" id="repayment_period" value="<?php echo (int) $repayment_period; ?>" min="1" required tabindex="6"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><?php echo display('months'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_start_date" class="col-sm-3 col-form-label"><?php echo display('repayment_start_date'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datepicker" name="repayment_start_date" id="repayment_start_date" value="<?php echo html_escape($repayment_start_date); ?>" required tabindex="7"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_end_date" class="col-sm-3 col-form-label"><?php echo display('repayment_end_date'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="repayment_end_date" id="repayment_end_date" value="<?php echo html_escape($repayment_end_date); ?>" required readonly tabindex="8"/>
                            </div>
                        </div>

                        <div class="form-group row" id="bank_div" style="<?php echo ($paytype == 2)?'':'display:none;';?>">
                            <label for="bank" class="col-sm-3 col-form-label"><?php echo display('bank'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                               <select name="bank_id" class="form-control" id="bank_id">
                                    <option value=""><?php echo display('select_one'); ?></option>
                                    <?php if(!empty($bank_list)){foreach($bank_list as $bank){
                                        $selected_bank = ($bank_id == $bank['bank_id'])?'selected':'';
                                    ?>
                                        <option value="<?php echo $bank['bank_id'];?>" <?php echo $selected_bank;?>><?php echo html_escape($bank['bank_name']);?></option>
                                    <?php }}?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="date" class="col-sm-3 col-form-label"><?php echo display('date') ?></label>
                            <div class="col-sm-6">
                               <input type="text" class="form-control datepicker" name="date" id="date" value="<?php echo html_escape($loan['date']);?>" placeholder="<?php echo display('date') ?>" tabindex="9"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="details" class="col-sm-3 col-form-label"><?php echo display('details') ?></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="details" id="details" placeholder="<?php echo display('details') ?>" tabindex="10"><?php echo html_escape($loan['details']);?></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-6">
                                <input type="reset" class="btn btn-danger" value="<?php echo display('reset') ?>" tabindex="11"/>
                                <input type="submit" class="btn btn-success" value="<?php echo display('save') ?>" tabindex="12"/>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close()?>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            (function() {
                var employeeSelect = document.getElementById('employee_id');
                var phoneInput = document.getElementById('phone');
                if (!employeeSelect) {
                    return;
                }

                employeeSelect.addEventListener('change', function() {
                    var option = this.options[this.selectedIndex];
                    if (!option) {
                        return;
                    }
                    var phone = option.getAttribute('data-phone') || '';
                    if (phoneInput) {
                        phoneInput.value = phone;
                    }
                });
            })();

            (function() {
                var disbursementInput = document.getElementById('disbursement_date');
                var periodInput = document.getElementById('repayment_period');
                var startInput = document.getElementById('repayment_start_date');
                var endInput = document.getElementById('repayment_end_date');
                if (!disbursementInput || !periodInput || !startInput || !endInput) {
                    return;
                }

                var startManual = false;
                var suppressStartListener = false;

                function parseDate(value) {
                    if (!value) return null;
                    var parts = value.split('-');
                    if (parts.length !== 3) return null;
                    var year = parseInt(parts[0], 10);
                    var month = parseInt(parts[1], 10) - 1;
                    var day = parseInt(parts[2], 10);
                    if (isNaN(year) || isNaN(month) || isNaN(day)) {
                        return null;
                    }
                    return new Date(year, month, day);
                }

                function formatDate(date) {
                    if (!(date instanceof Date)) {
                        return '';
                    }
                    var month = String(date.getMonth() + 1).padStart(2, '0');
                    var day = String(date.getDate()).padStart(2, '0');
                    return date.getFullYear() + '-' + month + '-' + day;
                }

                function addMonths(date, months) {
                    var result = new Date(date.getTime());
                    var targetDay = result.getDate();
                    result.setMonth(result.getMonth() + months);
                    if (result.getDate() !== targetDay) {
                        result.setDate(0);
                    }
                    return result;
                }

                function setStartValue(value) {
                    suppressStartListener = true;
                    startInput.value = value;
                    suppressStartListener = false;
                }

                function updateEndDate() {
                    var period = parseInt(periodInput.value, 10);
                    var startDate = parseDate(startInput.value);
                    if (!period || period < 1 || !startDate) {
                        endInput.value = '';
                        return;
                    }
                    var endDate = addMonths(startDate, Math.max(period - 1, 0));
                    endInput.value = formatDate(endDate);
                }

                function handleDisbursementChange() {
                    var disbursementDate = parseDate(disbursementInput.value);
                    if (!disbursementDate) {
                        return;
                    }
                    if (!startManual) {
                        var defaultStart = addMonths(disbursementDate, 1);
                        setStartValue(formatDate(defaultStart));
                    }
                    updateEndDate();
                }

                function handlePeriodChange() {
                    var period = parseInt(periodInput.value, 10);
                    if (isNaN(period) || period < 1) {
                        periodInput.value = 1;
                        period = 1;
                    }
                    updateEndDate();
                }

                disbursementInput.addEventListener('change', handleDisbursementChange);
                periodInput.addEventListener('change', handlePeriodChange);
                periodInput.addEventListener('keyup', handlePeriodChange);

                startInput.addEventListener('change', function() {
                    if (!suppressStartListener) {
                        startManual = true;
                    }
                    updateEndDate();
                });

                startInput.addEventListener('keyup', function() {
                    updateEndDate();
                });

                handleDisbursementChange();
                handlePeriodChange();
            })();
        </script>
