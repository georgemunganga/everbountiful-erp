<?php echo form_open('update_office_loan', array('class' => 'form-vertical', 'id' => 'validate')) ?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('edit_loan') ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="form-group row">
                    <label for="employee_id" class="col-sm-3 col-form-label"><?php echo display('employee') ?> <i class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <select name="employee_id" class="form-control" id="employee_id" required>
                            <option value=""><?php echo display('select_employee') ?></option>
                            <?php if (!empty($employees)) { ?>
                                <?php foreach ($employees as $employee) { ?>
                                    <option value="<?php echo $employee['employee_id']; ?>" 
                                        <?php echo ($loan_details['person_id'] == $employee['employee_id']) ? 'selected' : ''; ?>>
                                        <?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="loan_amount" class="col-sm-3 col-form-label"><?php echo display('loan_amount') ?> <i class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <input name="loan_amount" class="form-control" type="number" step="0.01" placeholder="<?php echo display('loan_amount') ?>" id="loan_amount" value="<?php echo $loan_details['loan_amount']; ?>" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="loan_date" class="col-sm-3 col-form-label"><?php echo display('date') ?> <i class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <input type="text" name="loan_date" class="form-control datepicker" placeholder="<?php echo display('date') ?>" value="<?php echo date('Y-m-d', strtotime($loan_details['loan_date'])); ?>" id="loan_date" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="loan_details" class="col-sm-3 col-form-label"><?php echo display('details') ?></label>
                    <div class="col-sm-6">
                        <textarea name="loan_details" class="form-control" placeholder="<?php echo display('details') ?>" id="loan_details"><?php echo $loan_details['loan_details']; ?></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-offset-3 col-sm-6">
                        <input type="hidden" name="loan_id" value="<?php echo $loan_details['loan_id']; ?>">
                        <div class="text-center">
                            <button type="submit" class="btn btn-success w-md m-b-5"><?php echo display('update') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo form_close() ?>