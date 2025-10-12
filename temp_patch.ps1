<?php if (!function_exists('format_output_number')) {
    function format_output_number($value) {
        if ($value === '' || $value === null) {
            return '';
        }
        if (!is_numeric($value)) {
            return $value;
        }
        $formatted = number_format((float) $value, 4, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }
} ?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open('productions' . (!empty($production->id) ? '/edit/' . $production->id : '/create'), array('class' => 'form-vertical')); ?>
                    <div class="form-group row">
                        <label for="name" class="col-sm-3 col-form-label"><?php echo html_escape(display('name') ?: 'Name'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input type="text" name="name" id="name" class="form-control" required maxlength="255" value="<?php echo html_escape($production->name); ?>">
                            <?php echo form_error('name', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="shed_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('shed') ?: 'Shed'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="shed_id" id="shed_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($sheds)): ?>
                                    <?php foreach ($sheds as $shed): ?>
                                        <option value="<?php echo $shed['id']; ?>" <?php echo ((string) $production->shed_id === (string) $shed['id']) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($shed['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('shed_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="production_date" class="col-sm-3 col-form-label"><?php echo html_escape(display('production_date') ?: 'Production Date'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input type="date" name="production_date" id="production_date" class="form-control" required value="<?php echo html_escape($production->production_date); ?>">
                            <?php echo form_error('production_date', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><?php echo html_escape(display('farm_collections') ?: 'Farms Collections'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-9">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="production-output-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><?php echo html_escape(display('product') ?: 'Product'); ?></th>
                                            <th><?php echo html_escape(display('unit') ?: 'Unit'); ?></th>
                                            <th><?php echo html_escape(display('quantity') ?: 'Quantity'); ?></th>
                                            <th><?php echo html_escape(display('mortality') ?: 'Mortality'); ?></th>
                                            <th><?php echo html_escape(display('extras') ?: 'Extras'); ?></th>
                                            <th style="width: 60px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="production-output-rows">
                                        <?php foreach ($production_outputs as $index => $output): ?>
                                            <?php
                                                $productValue = isset($output['product_id']) ? $output['product_id'] : '';
                                                $unitValue = isset($output['unit_id']) ? $output['unit_id'] : '';
                                                $quantityValue = isset($output['quantity']) ? format_output_number($output['quantity']) : '';
                                                $lossValue = isset($output['loss_qty']) ? format_output_number($output['loss_qty']) : '';
                                                $extrasValue = isset($output['extras_qty']) ? format_output_number($output['extras_qty']) : '';
                                            ?>
                                            <tr class="production-output-row">
                                                <td>
                                                    <select name="output_product_id[]" class="form-control output-product">
                                                        <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                                        <?php if (!empty($products)): ?>
                                                            <?php foreach ($products as $product): ?>
                                                                <option value="<?php echo $product['product_id']; ?>" <?php echo ((string) $productValue === (string) $product['product_id']) ? 'selected' : ''; ?>>
                                                                    <?php echo html_escape($product['product_name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="output_unit_id[]" class="form-control output-unit">
                                                        <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                                        <?php if (!empty($units)): ?>
                                                            <?php foreach ($units as $unit): ?>
                                                                <option value="<?php echo $unit['unit_id']; ?>" <?php echo ((string) $unitValue === (string) $unit['unit_id']) ? 'selected' : ''; ?>>
                                                                    <?php echo html_escape($unit['unit_name']); ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="output_quantity[]" class="form-control output-quantity output-input" step="0.0001" min="0" value="<?php echo html_escape($quantityValue); ?>">
                                                </td>
                                                <td>
                                                    <input type="number" name="output_loss_qty[]" class="form-control output-loss output-input" step="0.0001" min="0" value="<?php echo html_escape($lossValue); ?>">
                                                </td>
                                                <td>
                                                    <input type="number" name="output_extras_qty[]" class="form-control output-extras output-input" step="0.0001" min="0" value="<?php echo html_escape($extrasValue); ?>">
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button type="button" class="btn btn-sm btn-danger remove-output-row" <?php echo $index === 0 ? 'disabled style="visibility:hidden;"' : ''; ?>>&times;</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" id="addOutputRow"><?php echo html_escape(display('add_output') ?: 'Add Output'); ?></button>
                            <template id="production-output-template">
                                <tr class="production-output-row">
                                    <td>
                                        <select name="output_product_id[]" class="form-control output-product">
                                            <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                            <?php if (!empty($products)): ?>
                                                <?php foreach ($products as $product): ?>
                                                    <option value="<?php echo $product['product_id']; ?>"><?php echo html_escape($product['product_name']); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="output_unit_id[]" class="form-control output-unit">
                                            <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                            <?php if (!empty($units)): ?>
                                                <?php foreach ($units as $unit): ?>
                                                    <option value="<?php echo $unit['unit_id']; ?>"><?php echo html_escape($unit['unit_name']); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="output_quantity[]" class="form-control output-quantity output-input" step="0.0001" min="0" value="">
                                    </td>
                                    <td>
                                        <input type="number" name="output_loss_qty[]" class="form-control output-loss output-input" step="0.0001" min="0" value="">
                                    </td>
                                    <td>
                                        <input type="number" name="output_extras_qty[]" class="form-control output-extras output-input" step="0.0001" min="0" value="">
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-sm btn-danger remove-output-row">&times;</button>
                                    </td>
                                </tr>
                            </template>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="produced_total_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('produced_total_qty') ?: 'Produced Total Qty'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="produced_total_qty" id="produced_total_qty" class="form-control" step="0.01" value="<?php echo html_escape($production->produced_total_qty); ?>" readonly>
                            <?php echo form_error('produced_total_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="produced_mortality_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('produced_mortality_qty') ?: 'Mortality Qty'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="produced_mortality_qty" id="produced_mortality_qty" class="form-control" step="0.01" value="<?php echo html_escape($production->produced_mortality_qty); ?>" readonly>
                            <?php echo form_error('produced_mortality_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="produced_damaged_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('produced_damaged_qty') ?: 'Damaged Qty'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="produced_damaged_qty" id="produced_damaged_qty" class="form-control" step="0.01" value="<?php echo html_escape($production->produced_damaged_qty); ?>">
                            <?php echo form_error('produced_damaged_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="produced_extras_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('produced_extras_qty') ?: 'Extras Qty'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="produced_extras_qty" id="produced_extras_qty" class="form-control" step="0.01" value="<?php echo html_escape($production->produced_extras_qty); ?>" readonly>
                            <?php echo form_error('produced_extras_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description" class="col-sm-3 col-form-label"><?php echo html_escape(display('description') ?: 'Description'); ?></label>
                        <div class="col-sm-6">
                            <textarea name="description" id="description" class="form-control" rows="4" maxlength="1000"><?php echo html_escape($production->description); ?></textarea>
                            <?php echo form_error('description', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-6">
                            <a href="<?php echo base_url('productions'); ?>" class="btn btn-default"><?php echo html_escape(display('cancel') ?: 'Cancel'); ?></a>
                            <button type="submit" class="btn btn-success"><?php echo html_escape(display('save') ?: 'Save'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
                <script>
                (function () {
                    var tableBody = document.getElementById('production-output-rows');
                    var addButton = document.getElementById('addOutputRow');
                    var template = document.getElementById('production-output-template');
                    if (!tableBody || !addButton || !template) {
                        return;
                    }

                    function forEachNodeList(list, callback) {
                        Array.prototype.forEach.call(list, callback);
                    }

                    function recalcTotals() {
                        var total = 0;
                        var loss = 0;
                        var extras = 0;
                        forEachNodeList(tableBody.querySelectorAll('tr'), function (row) {
                            var qty = parseFloat(row.querySelector('.output-quantity').value) || 0;
                            var lossVal = parseFloat(row.querySelector('.output-loss').value) || 0;
                            var extrasVal = parseFloat(row.querySelector('.output-extras').value) || 0;
                            total += qty;
                            loss += lossVal;
                            extras += extrasVal;
                        });
                        var totalField = document.getElementById('produced_total_qty');
                        var lossField = document.getElementById('produced_mortality_qty');
                        var extrasField = document.getElementById('produced_extras_qty');
                        if (totalField) {
                            totalField.value = (Math.round(total * 100) / 100).toFixed(2);
                        }
                        if (lossField) {
                            lossField.value = (Math.round(loss * 100) / 100).toFixed(2);
                        }
                        if (extrasField) {
                            extrasField.value = (Math.round(extras * 100) / 100).toFixed(2);
                        }
                    }

                    function bindInputs(row) {
                        forEachNodeList(row.querySelectorAll('.output-input'), function (input) {
                            input.addEventListener('input', recalcTotals);
                        });
                    }

                    addButton.addEventListener('click', function () {
                        var fragment = document.importNode(template.content, true);
                        var newRow = fragment.querySelector('tr');
                        if (!newRow) {
                            return;
                        }
                        tableBody.appendChild(newRow);
                        bindInputs(newRow);
                        recalcTotals();
                    });

                    tableBody.addEventListener('click', function (event) {
                        if (!event.target.classList.contains('remove-output-row')) {
                            return;
                        }
                        var row = event.target.closest('tr');
                        if (!row) {
                            return;
                        }
                        if (tableBody.querySelectorAll('tr').length === 1) {
                            forEachNodeList(row.querySelectorAll('input'), function (input) {
                                input.value = '';
                            });
                            forEachNodeList(row.querySelectorAll('select'), function (select) {
                                select.selectedIndex = 0;
                            });
                        } else {
                            row.parentNode.removeChild(row);
                        }
                        recalcTotals();
                    });

                    forEachNodeList(tableBody.querySelectorAll('tr'), function (row) {
                        bindInputs(row);
                    });
                    recalcTotals();
                })();
                </script>
            </div>
        </div>
    </div>
</div>
