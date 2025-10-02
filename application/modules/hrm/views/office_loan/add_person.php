

        <div class="row">
            <div class="col-sm-12">
               
                <?php if($this->permission1->method('manage_ofln_person','read')->access()){ ?>
                  <a href="<?php echo base_url('manage_office_loan_person')?>" class="btn btn-success m-b-5 m-r-2"><i class="ti-align-justify"> </i> <?php echo display('manage_person')?> </a>
              <?php }?>
              
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo display('add_person') ?> </h4>
                        </div>
                    </div>
                   <?php echo form_open_multipart('hrm/loan/submit_office_loan_person',array('class' => 'form-vertical','id' => 'inflow_entry' ))?>
                    <div class="panel-body">

                        <?php if (!empty($employees)) { ?>
                        <div class="form-group row">
                            <label for="officeLoanEmployee" class="col-sm-3 col-form-label">Existing Employee</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="officeLoanEmployee">
                                    <option value="">-- <?php echo display('select_one'); ?> --</option>
                                    <?php foreach ($employees as $employee) { ?>
                                        <option value="<?php echo html_escape($employee->id); ?>" data-name="<?php echo html_escape(trim($employee->first_name . ' ' . $employee->last_name)); ?>" data-phone="<?php echo html_escape($employee->phone); ?>" data-address="<?php echo html_escape($employee->address_line_1); ?>">
                                            <?php echo html_escape(trim($employee->first_name . ' ' . $employee->last_name)); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <small class="text-muted">Select an employee to auto-fill the person details.</small>
                            </div>
                        </div>
                        <?php } ?>

                        <input type="hidden" name="employee_id" id="officeLoanEmployeeId" value="">

                    	<div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label"><?php echo display('name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="name" id="name" required="" placeholder="<?php echo display('name') ?>" tabindex="1"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-sm-3 col-form-label"><?php echo display('phone') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="phone" id="phone" placeholder="<?php echo display('phone') ?>" tabindex="2" data-validation="number" data-validation-allowing="negative,number" input name="color" data-validation="number" datavalidation-ignore="$" required="required" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-sm-3 col-form-label"><?php echo display('address') ?> <i class="text-danger"></i></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="address" id="address" placeholder="<?php echo display('address') ?>" tabindex="3"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-6">
                                <input type="reset" class="btn btn-danger" value="<?php echo display('reset') ?>" tabindex="4"/>
                                <input type="submit" id="add-deposit" class="btn btn-success" name="add-deposit" value="<?php echo display('save') ?>" tabindex="5"/>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close()?>
                </div>
            </div>
        </div>


<script type="text/javascript">
    (function ($) {
        'use strict';

        $('#officeLoanEmployee').on('change', function () {
            var option = $(this).find('option:selected');
            var employeeId = option.val();

            if (employeeId) {
                $('#officeLoanEmployeeId').val(employeeId);
                $('#name').val(option.data('name') || '').prop('readonly', true);
                $('#phone').val(option.data('phone') || '');
                $('#address').val(option.data('address') || '');
            } else {
                $('#officeLoanEmployeeId').val('');
                $('#name').val('').prop('readonly', false);
                $('#phone').val('');
                $('#address').val('');
            }
        });
    }(jQuery));
</script>
