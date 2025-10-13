<?php
$group_label = display('group_name');
if (empty($group_label) || $group_label === 'group_name') {
    $group_label = trim((display('group') ?: 'Group') . ' ' . (display('name') ?: 'Name'));
}
if ($group_label === '') {
    $group_label = 'Group Name';
}
$component_label = display('salary_components');
if (empty($component_label) || $component_label === 'salary_components') {
    $component_label = 'Salary Components';
}
?>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 id="salaryGroupFormTitle"><?php echo display('add') ? display('add') . ' ' . display('group') : 'Add Salary Group'; ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open('save_salary_group', 'id="salaryGroupForm"'); ?>
                    <input type="hidden" name="group_id" value="">

                    <div class="form-group">
                        <label for="group_name"><?php echo display('group') ? display('group') : 'Group'; ?> <?php echo display('name') ?: 'Name'; ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="group_name" id="group_name" required>
                    </div>

                    <div class="form-group">
                        <label for="group_description"><?php echo display('description'); ?></label>
                        <textarea class="form-control" name="description" id="group_description" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="group_status"><?php echo display('status'); ?></label>
                        <select class="form-control" name="status" id="group_status">
                            <option value="1"><?php echo display('active'); ?></option>
                            <option value="0"><?php echo display('inactive'); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="group_employees"><?php echo display('employee_name'); ?> <span class="text-danger">*</span></label>
                        <select class="form-control" name="employee_ids[]" id="group_employees" multiple="multiple" size="8" required>
                            <?php if (!empty($employees)) { ?>
                                <?php foreach ($employees as $employee) { ?>
                                    <?php
                                        $employee_id = isset($employee->id) ? (int) $employee->id : (isset($employee->employee_id) ? (int) $employee->employee_id : 0);
                                        $first = isset($employee->first_name) ? $employee->first_name : '';
                                        $last  = isset($employee->last_name) ? $employee->last_name : '';
                                        $name  = trim($first . ' ' . $last);
                                        $designation = isset($employee->designation_name) ? trim($employee->designation_name) : '';
                                        $option_label = $name !== '' ? $name : ('#' . $employee_id);
                                        if ($designation !== '') {
                                            $option_label .= ' (' . $designation . ')';
                                        }
                                    ?>
                                    <option value="<?php echo $employee_id; ?>"><?php echo html_escape($option_label); ?></option>
                                <?php } ?>
                            <?php } else { ?>
                                <option value=""><?php echo display('no_data_found'); ?></option>
                            <?php } ?>
                        </select>
                        <?php $multi_hint = display('select_multiple'); ?>
                        <?php $multi_hint = ($multi_hint === 'select_multiple') ? 'Hold Ctrl (Cmd on Mac) to select multiple employees.' : $multi_hint; ?>
                        <small class="text-muted"><?php echo html_escape($multi_hint); ?></small>
                    </div>

                    <div class="form-group">
                        <label for="group_components"><?php echo html_escape($component_label); ?></label>
                        <select class="form-control" name="component_ids[]" id="group_components" multiple="multiple" size="6">
                            <?php if (!empty($components)) { ?>
                                <?php foreach ($components as $component) { ?>
                                    <?php
                                        $component_id = isset($component->id) ? (int) $component->id : 0;
                                        $component_name = isset($component->component_name) ? $component->component_name : ('#' . $component_id);
                                        $component_type = isset($component->component_type) ? strtolower($component->component_type) : 'earning';
                                        $type_label = ($component_type === 'deduction') ? display('deduction') : display('addition');
                                        if (empty($type_label)) {
                                            $type_label = ($component_type === 'deduction') ? 'Deduction' : 'Addition';
                                        }
                                        $inactive_flag = (isset($component->status) && (string) $component->status !== '1') ? ' (' . (display('inactive') ?: 'Inactive') . ')' : '';
                                        $component_label = $component_name . ' - ' . $type_label . $inactive_flag;
                                    ?>
                                    <option value="<?php echo $component_id; ?>"><?php echo html_escape($component_label); ?></option>
                                <?php } ?>
                            <?php } else { ?>
                                <option value=""><?php echo display('no_data_found'); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-default" id="salaryGroupReset"><?php echo display('cancel'); ?></button>
                        <button type="submit" class="btn btn-success" id="salaryGroupSubmit"><?php echo display('save'); ?></button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('salary_groups') ? display('salary_groups') : 'Salary Groups'; ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th><?php echo display('sl'); ?></th>
                            <th><?php echo html_escape($group_label); ?></th>
                            <th><?php echo display('status'); ?></th>
                            <th><?php echo display('employee_name'); ?></th>
                            <th><?php echo html_escape($component_label); ?></th>
                            <th><?php echo display('created_date') ? display('created_date') : 'Created'; ?></th>
                            <th><?php echo display('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($groups)) { ?>
                            <?php $sl = 1; ?>
                            <?php foreach ($groups as $group) { ?>
                                <?php
                                    $group_id = isset($group->id) ? (int) $group->id : 0;
                                    $assignment = isset($group_assignments[$group_id]) ? $group_assignments[$group_id] : array('employees' => array(), 'components' => array());
                                    $status_active = isset($group->status) && (string) $group->status === '1';
                                    $status_label = $status_active ? (display('active') ?: 'Active') : (display('inactive') ?: 'Inactive');
                                    $status_class = $status_active ? 'label label-success' : 'label label-warning';
                                    $employees_json = html_escape(json_encode($assignment['employees']));
                                    $components_json = html_escape(json_encode($assignment['components']));
                                    $created_at = isset($group->created_at) ? $group->created_at : '';
                                ?>
                                <tr>
                                    <td><?php echo $sl++; ?></td>
                                    <td><?php echo html_escape(isset($group->group_name) ? $group->group_name : ('#' . $group_id)); ?></td>
                                    <td><span class="<?php echo $status_class; ?>"><?php echo html_escape($status_label); ?></span></td>
                                    <td><?php echo isset($group->employee_count) ? (int) $group->employee_count : 0; ?></td>
                                    <td><?php echo isset($group->component_count) ? (int) $group->component_count : 0; ?></td>
                                    <td><?php echo html_escape($created_at); ?></td>
                                    <td>
                                        <?php if ($this->permission1->method('manage_salary_setup','update')->access()) { ?>
                                            <button type="button"
                                                class="btn btn-info btn-xs edit-group"
                                                data-id="<?php echo $group_id; ?>"
                                                data-name="<?php echo html_escape(isset($group->group_name) ? $group->group_name : ''); ?>"
                                                data-description="<?php echo html_escape(isset($group->description) ? $group->description : ''); ?>"
                                                data-status="<?php echo $status_active ? 1 : 0; ?>"
                                                data-employees="<?php echo $employees_json; ?>"
                                                data-components="<?php echo $components_json; ?>">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        <?php } ?>

                                        <?php if ($this->permission1->method('manage_salary_setup','delete')->access()) { ?>
                                            <a href="<?php echo base_url('hrm/Payroll/delete_salary_group/' . $group_id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo display('are_you_sure'); ?>');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="text-center"><?php echo display('no_data_found'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function ($) {
        "use strict";

        var $form = $('#salaryGroupForm');
        var $formTitle = $('#salaryGroupFormTitle');
        var $submitBtn = $('#salaryGroupSubmit');
        var $resetBtn = $('#salaryGroupReset');
        var defaultTitle = $formTitle.text();
        var defaultSubmit = $submitBtn.text();

        var $groupId = $form.find('[name="group_id"]');
        var $groupName = $form.find('[name="group_name"]');
        var $groupDescription = $form.find('[name="description"]');
        var $groupStatus = $form.find('[name="status"]');
        var $employeeSelect = $form.find('[name="employee_ids[]"]');
        var $componentSelect = $form.find('[name="component_ids[]"]');

        function parseAssignmentValues(raw) {
            if (raw === undefined || raw === null || raw === '') {
                return [];
            }

            if (Array.isArray(raw)) {
                return raw;
            }

            if (typeof raw === 'string') {
                var trimmed = $.trim(raw);
                if (trimmed === '') {
                    return [];
                }
                try {
                    var parsed = JSON.parse(trimmed);
                    if (Array.isArray(parsed)) {
                        return parsed;
                    }
                } catch (err) {
                    var parts = trimmed.split(',');
                    if (parts.length > 1) {
                        return parts;
                    }
                }
            }

            return [];
        }

        function setMultiSelectValues($select, values) {
            if (!$select.length) {
                return;
            }

            var normalized = $.map(values, function (value) {
                return value !== null && value !== undefined ? value.toString() : null;
            });

            $select.val(normalized).trigger('change');
        }

        function resetSalaryGroupForm() {
            $form[0].reset();
            $groupId.val('');
            setMultiSelectValues($employeeSelect, []);
            setMultiSelectValues($componentSelect, []);
            $formTitle.text(defaultTitle);
            $submitBtn.text(defaultSubmit);
        }

        $resetBtn.on('click', function () {
            resetSalaryGroupForm();
        });

        $('.edit-group').on('click', function () {
            var $btn = $(this);
            var data = $btn.data();

            resetSalaryGroupForm();

            if (data.id !== undefined) {
                $groupId.val(data.id);
            }
            if (data.name !== undefined) {
                $groupName.val(data.name);
            }
            if (data.description !== undefined) {
                $groupDescription.val(data.description);
            }
            if (data.status !== undefined) {
                $groupStatus.val(data.status.toString());
            }

            var employees = parseAssignmentValues(data.employees);
            var components = parseAssignmentValues(data.components);

            setMultiSelectValues($employeeSelect, employees);
            setMultiSelectValues($componentSelect, components);

            $formTitle.text('<?php echo display('update') ? display('update') . ' ' . (display('group') ?: 'Group') : 'Update Salary Group'; ?>');
            $submitBtn.text('<?php echo display('update'); ?>');

            $('html, body').animate({
                scrollTop: ($form.offset().top - 60)
            }, 400);
        });
    })(jQuery);
</script>
