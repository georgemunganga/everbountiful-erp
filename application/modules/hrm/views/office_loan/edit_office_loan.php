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
                               <input type="number" class="form-control" name="ammount" id="ammount" required="" placeholder="<?php echo display('ammount') ?>" min="0" tabindex="3" value="<?php echo html_escape($loan['debit']);?>"/>
                            </div>
                        </div>

                        <div class="form-group row" id="payment_from">
                            <label for="payment_type" class="col-sm-3 col-form-label"><?php echo display('payment_type'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select name="paytype" class="form-control" id="payment_type" required onchange="bank_paymetExpense(this.value)" tabindex="4">
                                    <?php if (!empty($payment_channels)) { foreach ($payment_channels as $key => $label) { ?>
                                        <option value="<?php echo html_escape($key);?>" <?php echo ($paytype === $key) ? 'selected' : '';?>><?php echo html_escape($label);?></option>
                                    <?php }} ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row" id="disbursement_row">
                            <label for="disbursement_date" class="col-sm-3 col-form-label">Disbursement Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                               <input type="text" class="form-control datepicker" name="date" id="disbursement_date" value="<?php echo html_escape($schedule['disbursement_date']);?>" placeholder="YYYY-MM-DD" required tabindex="5"/>
                            </div>
                        </div>

                        <div class="form-group row" id="bank_div" style="<?php echo ($paytype === 'bank') ? '' : 'display:none;';?>">
                            <label for="bank_id" class="col-sm-3 col-form-label"><?php echo display('bank'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                               <select name="bank_id" class="form-control" id="bank_id" <?php echo ($paytype === 'bank') ? 'required' : ''; ?> tabindex="6">
                                    <option value=""><?php echo display('select_one');?></option>
                                    <?php if(!empty($bank_list)){foreach($bank_list as $bank){
                                        $selected_bank = ($bank_id == $bank['bank_id'])?'selected':'';
                                    ?>
                                        <option value="<?php echo html_escape($bank['bank_id']);?>" <?php echo $selected_bank;?>><?php echo html_escape($bank['bank_name']);?></option>
                                    <?php }}?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_period" class="col-sm-3 col-form-label">Repayment Period (months) <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="repayment_period" id="repayment_period" min="1" value="<?php echo (int) $schedule['repayment_period'];?>" required tabindex="7"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_start_date" class="col-sm-3 col-form-label">Repayment Start Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datepicker" name="repayment_start_date" id="repayment_start_date" value="<?php echo html_escape($schedule['repayment_start_date']);?>" placeholder="YYYY-MM-DD" required tabindex="8"/>
                                <small class="text-muted">Auto-calculated from the disbursement date; adjust if payroll timing differs.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_end_date" class="col-sm-3 col-form-label">Repayment End Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datepicker" name="repayment_end_date" id="repayment_end_date" value="<?php echo html_escape($schedule['repayment_end_date']);?>" placeholder="YYYY-MM-DD" required tabindex="9"/>
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
                if (employeeSelect) {
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
                }

                var disbursementInput = document.getElementById('disbursement_date');
                var periodInput = document.getElementById('repayment_period');
                var startInput = document.getElementById('repayment_start_date');
                var endInput = document.getElementById('repayment_end_date');
                var paymentTypeSelect = document.getElementById('payment_type');
                var bankSelect = document.getElementById('bank_id');

                function parseDateInput(value) {
                    if (!value) {
                        return null;
                    }
                    var parts = value.split('-');
                    if (parts.length !== 3) {
                        return null;
                    }
                    var year = parseInt(parts[0], 10);
                    var month = parseInt(parts[1], 10) - 1;
                    var day = parseInt(parts[2], 10);
                    if (isNaN(year) || isNaN(month) || isNaN(day)) {
                        return null;
                    }
                    var date = new Date(year, month, day);
                    if (date.getFullYear() !== year || date.getMonth() !== month || date.getDate() !== day) {
                        return null;
                    }
                    return date;
                }

                function formatDateOutput(date) {
                    if (!(date instanceof Date)) {
                        return '';
                    }
                    var month = (date.getMonth() + 1).toString().padStart(2, '0');
                    var day = date.getDate().toString().padStart(2, '0');
                    return date.getFullYear() + '-' + month + '-' + day;
                }

                function addMonths(date, months) {
                    var working = new Date(date.getTime());
                    var originalDay = working.getDate();
                    working.setMonth(working.getMonth() + months);
                    if (working.getDate() < originalDay) {
                        working.setDate(0);
                    }
                    return working;
                }

                var startDirty = false;
                var endDirty = false;

                if (disbursementInput && startInput) {
                    var initialDisbursement = parseDateInput(disbursementInput.value);
                    var initialStart = parseDateInput(startInput.value);
                    if (initialDisbursement) {
                        var expectedStart = formatDateOutput(addMonths(initialDisbursement, 1));
                        if (startInput.value && startInput.value !== expectedStart) {
                            startDirty = true;
                        }
                    }
                    if (initialStart && endInput && periodInput) {
                        var months = parseInt(periodInput.value, 10);
                        if (!isNaN(months) && months > 0) {
                            var expectedEnd = formatDateOutput(addMonths(initialStart, months - 1));
                            if (endInput.value && endInput.value !== expectedEnd) {
                                endDirty = true;
                            }
                        }
                    }
                }

                function updateEndDate(force) {
                    if (!startInput || !endInput || !periodInput) {
                        return;
                    }
                    var startDate = parseDateInput(startInput.value);
                    var months = parseInt(periodInput.value, 10);
                    if (!startDate || isNaN(months) || months <= 0) {
                        return;
                    }
                    var calculated = formatDateOutput(addMonths(startDate, months - 1));
                    if (force || !endDirty || !endInput.value) {
                        endInput.value = calculated;
                        endDirty = false;
                    }
                }

                function updateStartFromDisbursement() {
                    if (!disbursementInput || !startInput) {
                        return;
                    }
                    var disbursementDate = parseDateInput(disbursementInput.value);
                    if (!disbursementDate) {
                        return;
                    }
                    var proposedStart = formatDateOutput(addMonths(disbursementDate, 1));
                    if (!startDirty || !startInput.value) {
                        startInput.value = proposedStart;
                        startDirty = false;
                    }
                    updateEndDate(true);
                }

                if (disbursementInput) {
                    disbursementInput.addEventListener('change', function() {
                        startDirty = false;
                        updateStartFromDisbursement();
                    });
                }

                if (periodInput) {
                    periodInput.addEventListener('input', function() {
                        updateEndDate(true);
                    });
                    periodInput.addEventListener('change', function() {
                        updateEndDate(true);
                    });
                }

                if (startInput) {
                    startInput.addEventListener('input', function() {
                        startDirty = true;
                        updateEndDate(true);
                    });
                    startInput.addEventListener('change', function() {
                        startDirty = true;
                        updateEndDate(true);
                    });
                }

                if (endInput) {
                    endInput.addEventListener('input', function() {
                        endDirty = true;
                    });
                    endInput.addEventListener('change', function() {
                        endDirty = true;
                    });
                }

                updateStartFromDisbursement();
                updateEndDate(false);

                if (paymentTypeSelect && typeof bank_paymetExpense === 'function') {
                    bank_paymetExpense(paymentTypeSelect.value);
                }

                if (bankSelect) {
                    bankSelect.addEventListener('change', function() {
                        if (this.value) {
                            this.classList.remove('is-invalid');
                        }
                    });
                }
            })();
        </script>
