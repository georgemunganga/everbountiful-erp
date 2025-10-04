<div class="row">
    <div class="col-md-4">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4 id="componentFormTitle">Add Salary Component</h4>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open('hrm/payroll/save_salary_component', 'id="componentForm" data-mode="create"'); ?>
                <input type="hidden" name="component_id" value="">
                <div class="form-group">
                    <label for="componentName">Component Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="componentName" name="component_name" required>
                </div>
                <div class="form-group">
                    <label for="componentCode">Component Code</label>
                    <input type="text" class="form-control" id="componentCode" name="component_code">
                </div>
                <div class="form-group">
                    <label for="componentType">Component Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="componentType" name="component_type" required>
                        <option value="earning"><?php echo display('addition'); ?></option>
                        <option value="deduction"><?php echo display('deduction'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="componentAmountType">Amount Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="componentAmountType" name="amount_type" required>
                        <option value="fixed">Fixed Amount</option>
                        <option value="percentage">Percentage</option>
                        <option value="tax_slab">Tax Slab</option>
                    </select>
                </div>
                <div class="form-group" id="amountValueWrapper">
                    <label for="componentAmount">Amount Value <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" class="form-control" id="componentAmount" name="amount_value" required>
                </div>
                <div class="form-group" id="percentageBaseWrapper" style="display:none;">
                    <label for="componentPercentageBase">Percentage Base <span class="text-danger">*</span></label>
                    <select class="form-control" id="componentPercentageBase" name="percentage_base">
                        <option value="">Select Base</option>
                        <option value="gross">Gross Salary</option>
                        <option value="basic">Basic Pay</option>
                        <option value="net">Net Pay</option>
                    </select>
                </div>
                <div class="form-group" id="taxSlabWrapper" style="display:none;">
                    <label for="componentTaxSlabs">Tax Slabs <span class="text-danger">*</span></label>
                    <select class="form-control" id="componentTaxSlabs" name="tax_slab_ids[]" multiple="multiple" size="5">
                        <?php if (!empty($tax_slabs)) { ?>
                            <?php foreach ($tax_slabs as $slab) { ?>
                                <?php
                                    $slab_id = isset($slab->id) ? $slab->id : null;
                                    $min_amount = isset($slab->min_amount) ? $slab->min_amount : 0;
                                    $max_amount = isset($slab->max_amount) ? $slab->max_amount : null;
                                    $rate_percent = isset($slab->rate_percent) ? $slab->rate_percent : 0;
                                    $status_raw = isset($slab->status) ? $slab->status : 1;
                                    $min_label = number_format((float)$min_amount, 2);
                                    $max_label = ($max_amount !== null) ? number_format((float)$max_amount, 2) : 'No limit';
                                    $rate_label = number_format((float)$rate_percent, 2);
                                    $option_label = $min_label . ' - ' . $max_label . ' @ ' . $rate_label . '%';
                                    if ((string)$status_raw !== '1') {
                                        $option_label .= ' (Inactive)';
                                    }
                                ?>
                                <option value="<?php echo html_escape($slab_id); ?>"><?php echo html_escape($option_label); ?></option>
                            <?php } ?>
                        <?php } else { ?>
                            <option value=""><?php echo display('no_data_found') ? display('no_data_found') : 'No tax slabs available.'; ?></option>
                        <?php } ?>
                    </select>
                    <small class="text-muted">Hold Ctrl (Windows) or Command (macOS) to select multiple slabs.</small>
                </div>

                <div class="form-group">
                    <label for="componentTaxable">Is Taxable?</label>
                    <select class="form-control" id="componentTaxable" name="is_taxable">
                        <option value="1"><?php echo display('yes'); ?></option>
                        <option value="0"><?php echo display('no'); ?></option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="componentStatus"><?php echo display('status'); ?></label>
                    <select class="form-control" id="componentStatus" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="componentDescription">Description</label>
                    <textarea class="form-control" id="componentDescription" name="description" rows="3"></textarea>
                </div>
                <div class="form-group text-right">
                    <button type="button" class="btn btn-default" id="componentFormCancel"><?php echo display('reset'); ?></button>
                    <button type="submit" class="btn btn-success" id="componentFormSubmit"><?php echo display('save'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('salary_components'); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="datatable table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><?php echo display('sl'); ?></th>
                                <th>Component</th>
                                <th>Code</th>
                                <th><?php echo display('type'); ?></th>
                                <th>Amount / Basis</th>
                                <th>Taxable</th>
                                <th><?php echo display('status'); ?></th>
                                <th><?php echo display('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($components)) { ?>
                            <?php $sl = 1; ?>
                            <?php foreach ($components as $component) { ?>
                                <?php
                                    $component_id = isset($component->id) ? $component->id : (isset($component->component_id) ? $component->component_id : null);
                                    $name = isset($component->component_name) ? $component->component_name : (isset($component->name) ? $component->name : '');
                                    $code = isset($component->component_code) ? $component->component_code : (isset($component->code) ? $component->code : '');
                                    $type_raw = isset($component->component_type) ? $component->component_type : (isset($component->type) ? $component->type : 'earning');
                                    $amount_value = isset($component->amount_value) ? $component->amount_value : (isset($component->default_amount) ? $component->default_amount : 0);
                                    $amount_type = isset($component->amount_type) ? $component->amount_type : (isset($component->calculation_type) ? $component->calculation_type : 'fixed');
                                    $percentage_base = isset($component->percentage_base) ? $component->percentage_base : (isset($component->base_on) ? $component->base_on : null);
                                    $is_taxable = isset($component->is_taxable) ? $component->is_taxable : (isset($component->taxable) ? $component->taxable : 0);
                                    $status_raw = isset($component->status) ? $component->status : (isset($component->is_active) ? $component->is_active : 1);
                                    $description = isset($component->description) ? $component->description : (isset($component->note) ? $component->note : '');

                                    $selected_tax_slabs = array();
                                    if (!empty($component->tax_slabs) && is_array($component->tax_slabs)) {
                                        foreach ($component->tax_slabs as $slab_info) {
                                            if (is_array($slab_info) && isset($slab_info['id'])) {
                                                $selected_tax_slabs[] = (int) $slab_info['id'];
                                            }
                                        }
                                    }

                                    $type_label = 'Addition';
                                    $type_class = 'label label-info';
                                    if (strtolower((string)$type_raw) === 'deduction' || (string)$type_raw === '0') {
                                        $type_label = 'Deduction';
                                        $type_class = 'label label-warning';
                                    }

                                    $amount_display = '';
                                    if ($amount_type === 'tax_slab') {
                                        $amount_display = 'Tax Slab';
                                        if (!empty($component->tax_slabs) && is_array($component->tax_slabs)) {
                                            $slab_labels = array();
                                            foreach ($component->tax_slabs as $slab_row) {
                                                if (is_array($slab_row) && isset($slab_row['label'])) {
                                                    $slab_labels[] = html_escape($slab_row['label']);
                                                }
                                            }
                                            if (!empty($slab_labels)) {
                                                $amount_display .= '<div class="text-muted small">' . implode('<br>', $slab_labels) . '</div>';
                                            }
                                        }
                                    } else {
                                        $amount_display = is_numeric($amount_value) ? number_format((float)$amount_value, 2) : html_escape($amount_value);
                                        if ($amount_type === 'percentage') {
                                            $base_label = $percentage_base ? ucwords($percentage_base) : '';
                                            $amount_display = number_format((float)$amount_value, 2) . '%';
                                            if ($base_label !== '') {
                                                $amount_display .= ' of ' . $base_label;
                                            }
                                        }
                                    }

                                    $taxable_text = ((string)$is_taxable === '1') ? display('yes') : display('no');
                                    $status_text = ((string)$status_raw === '1') ? 'Active' : 'Inactive';
                                    $status_class = ((string)$status_raw === '1') ? 'label label-success' : 'label label-default';
                                    $tax_slab_ids_json = html_escape(json_encode($selected_tax_slabs));
                                ?>
                                <tr class="<?php echo ($sl & 1) ? 'odd gradeX' : 'even gradeC'; ?>">
                                    <td><?php echo $sl++; ?></td>
                                    <td>
                                        <strong><?php echo html_escape($name); ?></strong>
                                        <?php if (!empty($description)) { ?>
                                            <div class="text-muted small"><?php echo html_escape($description); ?></div>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo html_escape($code); ?></td>
                                    <td><span class="<?php echo $type_class; ?>"><?php echo $type_label; ?></span></td>
                                    <td><?php echo $amount_display; ?></td>
                                    <td><?php echo html_escape($taxable_text); ?></td>
                                    <td><span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                    <td class="text-center">
                                        <button type="button"
                                            class="btn btn-xs btn-primary edit-component"
                                            data-id="<?php echo html_escape($component_id); ?>"
                                            data-name="<?php echo html_escape($name); ?>"
                                            data-code="<?php echo html_escape($code); ?>"
                                            data-type="<?php echo html_escape($type_raw); ?>"
                                            data-amount="<?php echo html_escape($amount_value); ?>"
                                            data-amount-type="<?php echo html_escape($amount_type); ?>"
                                            data-base="<?php echo html_escape($percentage_base); ?>"
                                            data-tax-slabs='<?php echo $tax_slab_ids_json; ?>'
                                            data-taxable="<?php echo html_escape($is_taxable); ?>"
                                            data-status="<?php echo html_escape($status_raw); ?>"
                                            data-description="<?php echo html_escape($description); ?>">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button type="button"
                                            class="btn btn-xs btn-danger"
                                            data-toggle="modal"
                                            data-target="#componentDeleteModal"
                                            data-id="<?php echo html_escape($component_id); ?>"
                                            data-label="<?php echo html_escape($name); ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    <?php echo display('no_data_found') ? display('no_data_found') : 'No components available.'; ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="componentDeleteModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Component</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('hrm/payroll/delete_salary_component', 'id="componentDeleteForm"'); ?>
                <input type="hidden" name="component_id" value="">
                <p>Are you sure you want to delete <strong class="component-name-label"></strong>?</p>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo display('cancel'); ?></button>
                <button type="submit" form="componentDeleteForm" class="btn btn-danger"><?php echo display('delete'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function ($) {
        'use strict';

        var $componentForm = $('#componentForm');
        var $componentTitle = $('#componentFormTitle');
        var $componentSubmit = $('#componentFormSubmit');
        var $amountType = $('#componentAmountType');
        var $percentageWrapper = $('#percentageBaseWrapper');
        var $percentageBase = $('#componentPercentageBase');
        var $amountWrapper = $('#amountValueWrapper');
        var $amountInput = $('#componentAmount');
        var $taxSlabWrapper = $('#taxSlabWrapper');
        var $taxSlabSelect = $('#componentTaxSlabs');

        function handleAmountTypeChange(type) {
            type = (type || '').toString().toLowerCase();

            if (type === 'percentage') {
                $amountWrapper.show();
                $amountInput.prop('required', true);
                $percentageWrapper.show();
                $percentageBase.prop('required', true);
                $amountInput.attr('max', 100);
                $taxSlabWrapper.hide();
                $taxSlabSelect.prop('required', false);
                $taxSlabSelect.val([]);
            } else if (type === 'tax_slab') {
                $amountWrapper.hide();
                $amountInput.prop('required', false).val('');
                $percentageWrapper.hide();
                $percentageBase.prop('required', false).val('');
                $amountInput.removeAttr('max');
                $taxSlabWrapper.show();
                $taxSlabSelect.prop('required', true);
            } else {
                $amountWrapper.show();
                $amountInput.prop('required', true);
                $percentageWrapper.hide();
                $percentageBase.prop('required', false).val('');
                $amountInput.removeAttr('max');
                $taxSlabWrapper.hide();
                $taxSlabSelect.prop('required', false);
                $taxSlabSelect.val([]);
            }
        }

        function resetTaxSlabSelect() {
            $taxSlabSelect.find('option').prop('selected', false);
        }

        function resetComponentForm() {
            $componentForm[0].reset();
            $componentForm.attr('data-mode', 'create');
            $componentForm.find('[name="component_id"]').val('');
            $componentTitle.text('Add Salary Component');
            $componentSubmit.text('<?php echo display('save'); ?>');
            $('#componentType').val('earning');
            $amountType.val('fixed');
            resetTaxSlabSelect();
            handleAmountTypeChange('fixed');
            $('#componentTaxable').val('1');
            $('#componentStatus').val('1');
            $('#componentDescription').val('');
        }

        resetComponentForm();

        $amountType.on('change', function () {
            handleAmountTypeChange($(this).val());
        });

        $('#componentFormCancel').on('click', function () {
            resetComponentForm();
        });

        $('.edit-component').on('click', function () {
            var $btn = $(this);
            var typeValue = ($btn.data('type') || 'earning').toString().toLowerCase();
            if (typeValue === '1') {
                typeValue = 'earning';
            }
            if (typeValue === '0' || typeValue === '2') {
                typeValue = 'deduction';
            }

            var amountType = ($btn.data('amount-type') || 'fixed').toString().toLowerCase();

            var slabData = $btn.data('tax-slabs');
            var slabIds = [];
            if ($.isArray(slabData)) {
                slabIds = slabData;
            } else if (typeof slabData === 'string') {
                try {
                    slabIds = JSON.parse(slabData);
                } catch (err) {
                    slabIds = [];
                }
            }

            var taxableValue = $btn.data('taxable');
            if (taxableValue === true) {
                taxableValue = '1';
            } else if (taxableValue === false || taxableValue === undefined) {
                taxableValue = '0';
            }

            var statusValue = $btn.data('status');
            if (statusValue === true) {
                statusValue = '1';
            } else if (statusValue === false || statusValue === undefined) {
                statusValue = '0';
            }

            resetTaxSlabSelect();

            $componentForm.attr('data-mode', 'edit');
            $componentTitle.text('Update Salary Component');
            $componentSubmit.text('Update');

            $componentForm.find('[name="component_id"]').val($btn.data('id') || '');
            $componentForm.find('[name="component_name"]').val($btn.data('name') || '');
            $componentForm.find('[name="component_code"]').val($btn.data('code') || '');
            $('#componentType').val(typeValue);
            $amountType.val(amountType);
            handleAmountTypeChange(amountType);
            $amountInput.val($btn.data('amount') || '');
            $percentageBase.val($btn.data('base') || '');
            $('#componentTaxable').val(taxableValue);
            $('#componentStatus').val(statusValue);
            $('#componentDescription').val($btn.data('description') || '');

            if (amountType === 'tax_slab' && slabIds.length) {
                var normalizedIds = $.map(slabIds, function (value) {
                    return value !== null && value !== undefined ? value.toString() : null;
                });
                $taxSlabSelect.val(normalizedIds);
            }

            $('html, body').animate({ scrollTop: $componentForm.offset().top - 60 }, 400);
        });

        $('#componentDeleteModal').on('show.bs.modal', function (event) {
            var $btn = $(event.relatedTarget);
            var $modal = $(this);
            $modal.find('[name="component_id"]').val($btn.data('id') || '');
            $modal.find('.component-name-label').text($btn.data('label') || '');
        });
    }(jQuery));
</script>
