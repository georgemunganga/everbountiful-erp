
    <?php
        $today = date('Y-m-d');
        $disbursement_default = isset($schedule['disbursement_date']) && !empty($schedule['disbursement_date'])
            ? $schedule['disbursement_date']
            : $today;
        $period_default = isset($schedule['repayment_period']) && (int) $schedule['repayment_period'] > 0
            ? (int) $schedule['repayment_period']
            : 6;
        if ($period_default < 1) {
            $period_default = 1;
        }
        $default_start_date = isset($schedule['repayment_start_date']) && !empty($schedule['repayment_start_date'])
            ? $schedule['repayment_start_date']
            : date('Y-m-d', strtotime('+1 month', strtotime($disbursement_default)));
        $default_end_date = isset($schedule['repayment_end_date']) && !empty($schedule['repayment_end_date'])
            ? $schedule['repayment_end_date']
            : date('Y-m-d', strtotime('+' . max($period_default - 1, 0) . ' month', strtotime($default_start_date)));
        $default_next_due = $default_start_date;
    ?>
        <div class="row">
            <div class="col-sm-12">
        <?php if($this->permission1->method('add_loan_payment','create')->access()){ ?>
                  <a href="<?php echo base_url('add_office_loan_payment')?>" class="btn btn-success m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_payment')?> </a>
               <?php }?>
               <?php if($this->permission1->method('manage_ofln_person','read')->access()){ ?>
                  <a href="<?php echo base_url('manage_office_loans')?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('manage_loan')?> </a>
                  <?php }?>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo display('add_office_loan') ?> </h4>
                        </div>
                    </div>
                   <?php echo form_open_multipart('hrm/loan/bdtask_insert_office_loan',array('class' => 'form-vertical','id' => 'inflow_entry' ))?>
                    <div class="panel-body">
                        <?php if (empty($employees)) { ?>
                            <div class="alert alert-warning">
                                <?php echo html_escape('All employees already have an active office loan or none are eligible for a new loan.'); ?>
                            </div>
                        <?php } ?>
                        <?php
                            $selected_channel = isset($schedule['payment_channel']) ? $schedule['payment_channel'] : 'cash';
                            $is_bank_channel  = (strpos($selected_channel, 'bank:') === 0);
                        ?>

                        <div class="form-group row">
                            <label for="employee_id" class="col-sm-3 col-form-label"><?php echo display('name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="employee_id" id="employee_id" tabindex="1" <?php echo empty($employees) ? 'disabled' : 'required'; ?>>
                                    <option value=''><?php echo display('select_one')?></option>
                                  <?php if(!empty($employees)){foreach($employees as $employee){?>
                                    <option value="<?php echo $employee->id?>" data-phone="<?php echo html_escape($employee->phone);?>" data-address="<?php echo html_escape($employee->address_line_1);?>">
                                        <?php echo html_escape(trim($employee->first_name.' '.$employee->last_name));?>
                                    </option>
                                   <?php }}?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-sm-3 col-form-label"><?php echo display('phone') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control phone" name="phone" id="phone" placeholder="<?php echo display('phone') ?>" tabindex="2"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="ammount" class="col-sm-3 col-form-label"><?php echo display('ammount') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                               <input type="number" class="form-control" name="ammount" id="ammount" required placeholder="<?php echo display('ammount') ?>" min="0" tabindex="3"/>
                            </div>
                        </div>
                        <div class="form-group row" id="payment_from">
                            <label for="payment_type" class="col-sm-3 col-form-label"><?php echo display('payment_type'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select name="paytype" class="form-control" id="payment_type" required onchange="bank_paymetExpense(this.value)" tabindex="4">
                                    <?php if (!empty($payment_channels)) { foreach ($payment_channels as $key => $label) { ?>
                                        <option value="<?php echo html_escape($key);?>" <?php echo (isset($schedule['payment_channel']) && $schedule['payment_channel'] === $key) ? 'selected' : '';?>><?php echo html_escape($label);?></option>
                                    <?php }} ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row" id="bank_div" style="<?php echo $is_bank_channel ? '' : 'display:none;';?>">
                            <label for="bank_id" class="col-sm-3 col-form-label"><?php echo display('bank'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                               <select name="bank_id" class="form-control" id="bank_id" <?php echo $is_bank_channel ? 'required' : ''; ?> tabindex="5">
                                    <option value=""><?php echo display('select_one');?></option>
                                    <?php if (!empty($bank_list)) { foreach ($bank_list as $bank) { ?>
                                        <option value="<?php echo html_escape($bank['bank_id']);?>"><?php echo html_escape($bank['bank_name']);?></option>
                                    <?php }} ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_period" class="col-sm-3 col-form-label">Repayment Period (months) <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="repayment_period" id="repayment_period" min="1" value="<?php echo (int) $period_default; ?>" required tabindex="6"/>
                            </div>
                        </div>

                        <div class="form-group row" id="disbursement_row">
                            <label for="disbursement_date" class="col-sm-3 col-form-label">Disbursement Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datepicker" name="disbursement_date" id="disbursement_date" value="<?php echo html_escape($disbursement_default); ?>" placeholder="YYYY-MM-DD" required tabindex="7"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_start_date" class="col-sm-3 col-form-label">Repayment Start Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datepicker" name="repayment_start_date" id="repayment_start_date" value="<?php echo html_escape($default_start_date); ?>" placeholder="YYYY-MM-DD" required tabindex="8"/>
                                <small class="text-muted">Auto-calculated from the disbursement date; adjust if payroll timing differs.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_end_date" class="col-sm-3 col-form-label">Repayment Due Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datepicker" name="repayment_end_date" id="repayment_end_date" value="<?php echo html_escape($default_end_date); ?>" placeholder="YYYY-MM-DD" required tabindex="9"/>
                                <small class="text-muted">Updates automatically from the period and start date; manual changes will adjust the repayment period.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="next_due_date" class="col-sm-3 col-form-label">Next Repayment Date</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="next_due_date" id="next_due_date" value="<?php echo html_escape($default_next_due); ?>" readonly />
                                <small class="text-muted">Calculated from the repayment start date.</small>
                            </div>
                        </div>

                        <div class="row m-t-20">
                            <div class="col-sm-10 col-sm-offset-1">
                                <div class="well repayment-preview">
                                    <h5 class="text-semibold"><?php echo display('repayment_schedule'); ?> Preview</h5>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p class="m-b-5"><strong>Monthly Repayment:</strong> <span id="loan-helper-amount">0.00</span></p>
                                            <p class="m-b-5"><strong><?php echo display('total'); ?> payback:</strong> <span id="loan-helper-total-payback">0.00</span></p>
                                            <p class="m-b-5"><strong><?php echo display('repayment_period'); ?>:</strong> <span id="loan-helper-total-deductions">0</span></p>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="m-b-5"><strong>Schedule:</strong> <span id="loan-helper-schedule">Enter amount and period to preview deductions.</span></p>
                                            <div id="loan-helper-dates" style="display:none;">
                                                <p class="m-b-5"><strong>First deduction:</strong> <span id="loan-helper-first-date"></span></p>
                                                <p class="m-b-0"><strong>Final deduction:</strong> <span id="loan-helper-final-date"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="date" class="col-sm-3 col-form-label"><?php echo display('date') ?> <i class="text-danger"></i></label>
                            <div class="col-sm-6">
                               <input type="text" class="form-control datepicker" name="date" id="date" value="<?php echo date("Y-m-d");?>" placeholder="<?php echo display('date') ?>" tabindex="10"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="details" class="col-sm-3 col-form-label"><?php echo display('details') ?> <i class="text-danger"></i></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="details" id="details" placeholder="<?php echo display('details') ?>" tabindex="11"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-6">
                                <input type="reset" class="btn btn-danger" value="<?php echo display('reset') ?>" tabindex="12"/>
                                <input type="submit" id="add-deposit" class="btn btn-success" name="add-deposit" value="<?php echo display('save') ?>" tabindex="13"/>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close()?>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function bank_paymetExpense(value) {
                var isBank = value && value.indexOf('bank:') === 0;
                var bankDiv = document.getElementById('bank_div');
                var bankSelect = document.getElementById('bank_id');
                if (bankDiv) {
                    bankDiv.style.display = isBank ? '' : 'none';
                }
                if (bankSelect) {
                    bankSelect.required = !!isBank;
                    bankSelect.disabled = !isBank;
                    if (!isBank) {
                        bankSelect.value = '';
                    }
                }
            }

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
                var dueInput = document.getElementById('repayment_end_date');
                var nextInput = document.getElementById('next_due_date');
                var amountInput = document.getElementById('ammount');
                var amountHelper = document.getElementById('loan-helper-amount');
                var scheduleHelper = document.getElementById('loan-helper-schedule');
                var totalPaybackElement = document.getElementById('loan-helper-total-payback');
                var totalDeductionsElement = document.getElementById('loan-helper-total-deductions');
                var datesDiv = document.getElementById('loan-helper-dates');
                var firstDateSpan = document.getElementById('loan-helper-first-date');
                var finalDateSpan = document.getElementById('loan-helper-final-date');
                var paymentTypeSelect = document.getElementById('payment_type');

                if (!periodInput || !startInput || !dueInput) {
                    if (paymentTypeSelect) {
                        bank_paymetExpense(paymentTypeSelect.value);
                    }
                    return;
                }

                var manualStart = false;
                var suppressStart = false;
                var suppressDue = false;
                var suppressPeriod = false;

                function parseDate(value) {
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
                    if (isNaN(date.getTime())) {
                        return null;
                    }
                    return date;
                }

                function formatDate(date) {
                    if (!(date instanceof Date) || isNaN(date.getTime())) {
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
                    suppressStart = true;
                    startInput.value = value || '';
                    suppressStart = false;
                }

                function setDueValue(value) {
                    suppressDue = true;
                    dueInput.value = value || '';
                    suppressDue = false;
                }

                function setPeriodValue(value) {
                    suppressPeriod = true;
                    periodInput.value = value;
                    suppressPeriod = false;
                }

                function setNextValue(value) {
                    if (nextInput) {
                        nextInput.value = value || '';
                    }
                }

                function monthsBetweenInclusive(startDate, endDate) {
                    if (!(startDate instanceof Date) || !(endDate instanceof Date)) {
                        return 1;
                    }
                    if (endDate.getTime() <= startDate.getTime()) {
                        return 1;
                    }

                    var months = 1;
                    var limit = 600;
                    var endTime = endDate.getTime();

                    while (months <= limit) {
                        var candidate = addMonths(startDate, months - 1);
                        var candidateTime = candidate.getTime();
                        if (candidateTime === endTime) {
                            return months;
                        }
                        if (candidateTime > endTime) {
                            return Math.max(1, months - 1);
                        }
                        months++;
                    }

                    return months;
                }

                function normalizedDue(startDate, period) {
                    if (!(startDate instanceof Date) || !period || period < 1) {
                        return null;
                    }
                    return addMonths(startDate, Math.max(period - 1, 0));
                }

                function formatCurrency(value) {
                    if (!isFinite(value) || value <= 0) {
                        return '0.00';
                    }
                    try {
                        return value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    } catch (err) {
                        return value.toFixed(2);
                    }
                }

                function formatDisplayDate(date) {
                    if (!(date instanceof Date) || isNaN(date.getTime())) {
                        return '';
                    }
                    var month = date.toLocaleString(undefined, { month: 'short' });
                    var day = String(date.getDate()).padStart(2, '0');
                    var year = date.getFullYear();
                    return month + ' ' + day + ', ' + year;
                }

                function pluralize(count, singular) {
                    if (!count || count === 1) {
                        return singular;
                    }
                    return singular + 's';
                }

                function buildScheduleText(period, startDate, endDate) {
                    var readablePeriod = period ? period + ' ' + pluralize(period, 'deduction') : '';
                    var startLabel = formatDisplayDate(startDate);
                    var endLabel = formatDisplayDate(endDate);
                    if (startLabel && endLabel && readablePeriod) {
                        return readablePeriod + ' scheduled from ' + startLabel + ' to ' + endLabel + '.';
                    }
                    if (readablePeriod) {
                        return readablePeriod + ' scheduled.';
                    }
                    return 'Enter amount, period, and dates to preview payroll deductions.';
                }

                function updateHelper() {
                    var amount = amountInput ? parseFloat(amountInput.value) : NaN;
                    var period = parseInt(periodInput.value, 10);
                    var startDate = parseDate(startInput.value);
                    var dueDate = parseDate(dueInput.value);

                    var monthly = (!isNaN(amount) && amount > 0 && !isNaN(period) && period > 0) ? amount / period : 0;
                    if (amountHelper) {
                        amountHelper.textContent = formatCurrency(monthly);
                    }
                    if (totalPaybackElement) {
                        totalPaybackElement.textContent = formatCurrency(!isNaN(amount) && amount > 0 ? amount : 0);
                    }
                    if (totalDeductionsElement) {
                        totalDeductionsElement.textContent = (!isNaN(period) && period > 0) ? period : '0';
                    }
                    if (scheduleHelper) {
                        scheduleHelper.textContent = buildScheduleText(isNaN(period) ? null : period, startDate, dueDate);
                    }

                    if (datesDiv && firstDateSpan && finalDateSpan && startDate && dueDate) {
                        datesDiv.style.display = 'block';
                        firstDateSpan.textContent = formatDisplayDate(startDate);
                        finalDateSpan.textContent = formatDisplayDate(dueDate);
                    } else if (datesDiv) {
                        datesDiv.style.display = 'none';
                    }
                }

                function refreshDueFromPeriod() {
                    var startDate = parseDate(startInput.value);
                    var period = parseInt(periodInput.value, 10);
                    if (!startDate || isNaN(period) || period < 1) {
                        setDueValue('');
                        setNextValue(startInput.value || '');
                        updateHelper();
                        return;
                    }
                    var dueDate = normalizedDue(startDate, period);
                    setDueValue(formatDate(dueDate));
                    setNextValue(startInput.value);
                    updateHelper();
                }

                function refreshPeriodFromDue() {
                    var startDate = parseDate(startInput.value);
                    var dueDate = parseDate(dueInput.value);
                    if (!startDate || !dueDate) {
                        updateHelper();
                        return;
                    }
                    if (dueDate.getTime() < startDate.getTime()) {
                        setStartValue(formatDate(dueDate));
                        startDate = dueDate;
                    }
                    var period = monthsBetweenInclusive(startDate, dueDate);
                    setPeriodValue(period);
                    var normalized = normalizedDue(startDate, period);
                    setDueValue(formatDate(normalized));
                    setNextValue(startInput.value);
                    updateHelper();
                }

                function handleDisbursementChange() {
                    if (!disbursementInput) {
                        refreshDueFromPeriod();
                        return;
                    }
                    var disbursementDate = parseDate(disbursementInput.value);
                    if (!disbursementDate) {
                        refreshDueFromPeriod();
                        return;
                    }
                    if (!manualStart) {
                        var defaultStart = addMonths(disbursementDate, 1);
                        setStartValue(formatDate(defaultStart));
                    }
                    refreshDueFromPeriod();
                }

                if (disbursementInput) {
                    ['change', 'keyup', 'input'].forEach(function(eventName) {
                        disbursementInput.addEventListener(eventName, handleDisbursementChange);
                    });
                }

                ['change', 'keyup', 'input'].forEach(function(eventName) {
                    periodInput.addEventListener(eventName, function() {
                        if (!suppressPeriod) {
                            refreshDueFromPeriod();
                        }
                    });
                });

                ['change', 'keyup', 'input'].forEach(function(eventName) {
                    startInput.addEventListener(eventName, function() {
                        if (!suppressStart) {
                            manualStart = true;
                        }
                        refreshDueFromPeriod();
                    });
                });

                ['change', 'keyup', 'input'].forEach(function(eventName) {
                    dueInput.addEventListener(eventName, function() {
                        if (!suppressDue) {
                            refreshPeriodFromDue();
                        }
                    });
                });

                if (amountInput) {
                    ['change', 'keyup', 'input'].forEach(function(eventName) {
                        amountInput.addEventListener(eventName, updateHelper);
                    });
                }

                refreshDueFromPeriod();

                if (paymentTypeSelect) {
                    bank_paymetExpense(paymentTypeSelect.value);
                }
            })();
        </script>
   



