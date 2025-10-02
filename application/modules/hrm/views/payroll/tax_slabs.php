<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('tax_slabs'); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="text-right m-b-10">
                    <button type="button" id="addTaxSlabBtn" class="btn btn-success" data-toggle="modal" data-target="#taxSlabModal" data-mode="create">
                        <i class="fa fa-plus"></i> <?php echo display('add'); ?> <?php echo display('tax_slabs'); ?>
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="datatable table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><?php echo display('sl'); ?></th>
                                <th>Min Range</th>
                                <th>Max Range</th>
                                <th>Tax Rate (%)</th>
                                <th>Additional Amount</th>
                                <th><?php echo display('status'); ?></th>
                                <th><?php echo display('action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tax_slabs)) { ?>
                                <?php $sl = 1; ?>
                                <?php foreach ($tax_slabs as $slab) { ?>
                                    <?php
                                        $slab_id = isset($slab->id) ? $slab->id : null;
                                        $min_amount = isset($slab->min_amount) ? $slab->min_amount : 0;
                                        $max_amount = isset($slab->max_amount) ? $slab->max_amount : null;
                                        $rate_percent = isset($slab->rate_percent) ? $slab->rate_percent : 0;
                                        $additional_amount = isset($slab->additional_amount) ? $slab->additional_amount : 0;
                                        $status_raw = isset($slab->status) ? $slab->status : 1;
                                        $notes = isset($slab->notes) ? $slab->notes : '';

                                        $min_label = number_format((float)$min_amount, 2);
                                        $max_label = $max_amount !== null ? number_format((float)$max_amount, 2) : 'No limit';
                                        $rate_label = number_format((float)$rate_percent, 2);
                                        $additional_label = number_format((float)$additional_amount, 2);

                                        $status_text = ((string)$status_raw === '1') ? 'Active' : 'Inactive';
                                        $status_class = ((string)$status_raw === '1') ? 'label label-success' : 'label label-default';
                                    ?>
                                    <tr class="<?php echo ($sl & 1) ? 'odd gradeX' : 'even gradeC'; ?>">
                                        <td><?php echo $sl++; ?></td>
                                        <td><?php echo $min_label; ?></td>
                                        <td><?php echo html_escape($max_label); ?></td>
                                        <td><?php echo $rate_label; ?></td>
                                        <td>
                                            <?php echo $additional_label; ?>
                                            <?php if (!empty($notes)) { ?>
                                                <div class="text-muted small"><?php echo html_escape($notes); ?></div>
                                            <?php } ?>
                                        </td>
                                        <td><span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-xs btn-primary edit-tax-slab"
                                                    data-toggle="modal"
                                                    data-target="#taxSlabModal"
                                                    data-mode="edit"
                                                    data-id="<?php echo html_escape($slab_id); ?>"
                                                    data-min="<?php echo html_escape($min_amount); ?>"
                                                    data-max="<?php echo html_escape($max_amount); ?>"
                                                    data-rate="<?php echo html_escape($rate_percent); ?>"
                                                    data-additional="<?php echo html_escape($additional_amount); ?>"
                                                    data-notes="<?php echo html_escape($notes); ?>"
                                                    data-status="<?php echo html_escape($status_raw); ?>">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-xs btn-danger"
                                                    data-toggle="modal"
                                                    data-target="#taxSlabDeleteModal"
                                                    data-id="<?php echo html_escape($slab_id); ?>"
                                                    data-label="<?php echo html_escape($min_label . ' - ' . $max_label); ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <?php echo display('no_data_found') ? display('no_data_found') : 'No tax slabs available.'; ?>
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

<div id="taxSlabModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Tax Slab</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('hrm/payroll/save_tax_slab', 'id="taxSlabForm"'); ?>
                <input type="hidden" name="slab_id" value="">
                <div class="form-group">
                    <label for="taxMinAmount">Min Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" class="form-control" id="taxMinAmount" name="min_amount" required>
                </div>
                <div class="form-group">
                    <label for="taxMaxAmount">Max Amount</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="taxMaxAmount" name="max_amount">
                </div>
                <div class="form-group">
                    <label for="taxRate">Tax Rate (%) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" class="form-control" id="taxRate" name="rate_percent" required>
                </div>
                <div class="form-group">
                    <label for="taxAdditionalAmount">Additional Amount</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="taxAdditionalAmount" name="additional_amount">
                </div>
                <div class="form-group">
                    <label for="taxNotes">Notes</label>
                    <textarea class="form-control" id="taxNotes" name="notes" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="taxStatus">Status</label>
                    <select class="form-control" id="taxStatus" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo display('close'); ?></button>
                <button type="submit" form="taxSlabForm" class="btn btn-success"><?php echo display('save'); ?></button>
            </div>
        </div>
    </div>
</div>

<div id="taxSlabDeleteModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete Tax Slab</h4>
            </div>
            <div class="modal-body">
                <?php echo form_open('hrm/payroll/delete_tax_slab', 'id="taxSlabDeleteForm"'); ?>
                <input type="hidden" name="slab_id" value="">
                <p>Are you sure you want to delete the slab for <strong class="delete-range-label"></strong>?</p>
                <?php echo form_close(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo display('cancel'); ?></button>
                <button type="submit" form="taxSlabDeleteForm" class="btn btn-danger"><?php echo display('delete'); ?></button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    (function ($) {
        'use strict';

        $('#taxSlabModal').on('show.bs.modal', function (event) {
            var $button = $(event.relatedTarget);
            var mode = $button && $button.data('mode') ? $button.data('mode') : 'create';
            var $modal = $(this);
            var $form = $modal.find('form');

            if (mode === 'edit') {
                $modal.find('.modal-title').text('Edit Tax Slab');
                $form.find('[name="slab_id"]').val($button.data('id') || '');
                $form.find('[name="min_amount"]').val($button.data('min') || '');
                $form.find('[name="max_amount"]').val($button.data('max') || '');
                $form.find('[name="rate_percent"]').val($button.data('rate') || '');
                $form.find('[name="additional_amount"]').val($button.data('additional') || '');
                $form.find('[name="notes"]').val($button.data('notes') || '');
                var statusValue = $button.data('status');
                if (statusValue === undefined || statusValue === null || statusValue === '') {
                    statusValue = '1';
                }
                if (statusValue === true) {
                    statusValue = '1';
                }
                if (statusValue === false) {
                    statusValue = '0';
                }
                $form.find('[name="status"]').val(statusValue);
            } else {
                $modal.find('.modal-title').text('Add Tax Slab');
                $form[0].reset();
                $form.find('[name="slab_id"]').val('');
                $form.find('[name="status"]').val('1');
            }
        });

        $('#taxSlabModal').on('hidden.bs.modal', function () {
            var $form = $(this).find('form');
            $form[0].reset();
            $form.find('[name="slab_id"]').val('');
            $form.find('[name="status"]').val('1');
        });

        $('#taxSlabDeleteModal').on('show.bs.modal', function (event) {
            var $button = $(event.relatedTarget);
            var $modal = $(this);
            $modal.find('[name="slab_id"]').val($button.data('id') || '');
            $modal.find('.delete-range-label').text($button.data('label') || '');
        });
    }(jQuery));
</script>
