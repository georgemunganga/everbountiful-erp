<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php if (!empty($validation_errors)): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo $validation_errors; ?>
                    </div>
                <?php endif; ?>

                <?php
                    $action = isset($leave['id']) ? 'leaves/create/' . $leave['id'] : 'leaves/create';
                    echo form_open($action, array('class' => 'form-vertical'));
                ?>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="employee_id"><?php echo html_escape(display('employee_name') ?: 'Employee'); ?> <i class="text-danger">*</i></label>
                            <?php echo form_dropdown('employee_id', $employees, set_value('employee_id', $leave['employee_id'] ?? ''), 'class="form-control" id="employee_id" required'); ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="leave_type"><?php echo html_escape(display('leave_type') ?: 'Leave Type'); ?> <i class="text-danger">*</i></label>
                            <input type="text" name="leave_type" id="leave_type" class="form-control" value="<?php echo html_escape(set_value('leave_type', $leave['leave_type'] ?? '')); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="start_date"><?php echo html_escape(display('start_date') ?: 'Start Date'); ?> <i class="text-danger">*</i></label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo html_escape(set_value('start_date', $leave['start_date'] ?? '')); ?>" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="end_date"><?php echo html_escape(display('end_date') ?: 'End Date'); ?> <i class="text-danger">*</i></label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo html_escape(set_value('end_date', $leave['end_date'] ?? '')); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="status"><?php echo html_escape(display('status') ?: 'Status'); ?> <i class="text-danger">*</i></label>
                            <select name="status" id="status" class="form-control" required>
                                <?php
                                    $status = set_value('status', $leave['status'] ?? 'approved');
                                    $options = array(
                                        'approved' => display('approved') ?: 'Approved',
                                        'pending'  => display('pending') ?: 'Pending',
                                        'rejected' => display('rejected') ?: 'Rejected',
                                    );
                                    foreach ($options as $value => $label):
                                ?>
                                    <option value="<?php echo $value; ?>" <?php echo ($status === $value) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="notes"><?php echo html_escape(display('notes') ?: 'Notes'); ?></label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo html_escape(set_value('notes', $leave['notes'] ?? '')); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-success"><?php echo html_escape(display('save') ?: 'Save'); ?></button>
                        <a href="<?php echo base_url('leaves'); ?>" class="btn btn-default"><?php echo html_escape(display('cancel') ?: 'Cancel'); ?></a>
                    </div>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
