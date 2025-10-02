
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

                        <div class="form-group row">
                            <label for="employee_id" class="col-sm-3 col-form-label"><?php echo display('name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" required name="employee_id" id="employee_id" tabindex="1">
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
                               <input type="number" class="form-control" name="ammount" id="ammount" required="" placeholder="<?php echo display('ammount') ?>" min="0" tabindex="3"/>
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

                        <div class="form-group row" id="disbursement_row">
                            <label for="disbursement_date" class="col-sm-3 col-form-label">Disbursement Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datepicker" name="date" id="disbursement_date" value="<?php echo html_escape(isset($schedule['disbursement_date']) ? $schedule['disbursement_date'] : date('Y-m-d'));?>" placeholder="YYYY-MM-DD" required tabindex="5"/>
                            </div>
                        </div>

                        <div class="form-group row" id="bank_div" style="<?php echo (isset($schedule['payment_channel']) && $schedule['payment_channel'] === 'bank') ? '' : 'display:none;';?>">
                            <label for="bank_id" class="col-sm-3 col-form-label"><?php echo display('bank'); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                               <select name="bank_id" class="form-control" id="bank_id" <?php echo (isset($schedule['payment_channel']) && $schedule['payment_channel'] === 'bank') ? 'required' : ''; ?> tabindex="6">
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
                                <input type="number" class="form-control" name="repayment_period" id="repayment_period" min="1" value="<?php echo isset($schedule['repayment_period']) ? (int) $schedule['repayment_period'] : 6;?>" required tabindex="7"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_start_date" class="col-sm-3 col-form-label">Repayment Start Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datepicker" name="repayment_start_date" id="repayment_start_date" value="<?php echo html_escape(isset($schedule['repayment_start_date']) ? $schedule['repayment_start_date'] : '');?>" placeholder="YYYY-MM-DD" required tabindex="8"/>
                                <small class="text-muted">Auto-calculated from the disbursement date; adjust if payroll timing differs.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repayment_end_date" class="col-sm-3 col-form-label">Repayment End Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control datepicker" name="repayment_end_date" id="repayment_end_date" value="<?php echo html_escape(isset($schedule['repayment_end_date']) ? $schedule['repayment_end_date'] : '');?>" placeholder="YYYY-MM-DD" required tabindex="9"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="details" class="col-sm-3 col-form-label"><?php echo display('details') ?> <i class="text-danger"></i></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="details" id="details" placeholder="<?php echo display('details') ?>" tabindex="10"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-6">
                                <input type="reset" class="btn btn-danger" value="<?php echo display('reset') ?>" tabindex="11"/>
                                <input type="submit" id="add-deposit" class="btn btn-success" name="add-deposit" value="<?php echo display('save') ?>" tabindex="12"/>
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
        </script>
   



