<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php if (!empty($validation_errors)): ?>
                    <div class="alert alert-danger"><?php echo $validation_errors; ?></div>
                <?php endif; ?>

                <?php echo form_open(isset($form_action) ? $form_action : 'inventory/consumption'); ?>
                    <div class="form-group row">
                        <label for="product_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('product') ?: 'Product'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="product_id" id="product_id" class="form-control">
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['product_id']; ?>" <?php echo set_select('product_id', $product['product_id']); ?>>
                                            <?php echo html_escape($product['product_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('location') ?: 'Location'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="location_id" id="location_id" class="form-control">
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($locations)): ?>
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?php echo $location['id']; ?>" <?php echo set_select('location_id', $location['id']); ?>>
                                            <?php echo html_escape($location['location_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="quantity" class="col-sm-3 col-form-label"><?php echo html_escape(display('quantity') ?: 'Quantity'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input type="number" name="quantity" id="quantity" class="form-control" step="0.01" min="0" required value="<?php echo html_escape(set_value('quantity')); ?>">
                            <small class="form-text text-muted"><?php echo html_escape(display('quantity_in_base_unit') ?: 'Enter quantity in the lot base unit.'); ?></small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="reason_code" class="col-sm-3 col-form-label"><?php echo html_escape(display('reason') ?: 'Reason'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="reason_code" id="reason_code" class="form-control" required>
                                <?php foreach ($reasons as $code => $label): ?>
                                    <option value="<?php echo html_escape($code); ?>" <?php echo set_select('reason_code', $code); ?>>
                                        <?php echo html_escape($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="consumption_date" class="col-sm-3 col-form-label"><?php echo html_escape(display('date') ?: 'Date'); ?></label>
                        <div class="col-sm-6">
                            <input type="date" name="consumption_date" id="consumption_date" class="form-control" value="<?php echo html_escape(set_value('consumption_date', date('Y-m-d'))); ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="reference_code" class="col-sm-3 col-form-label"><?php echo html_escape(display('reference') ?: 'Reference'); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="reference_code" id="reference_code" class="form-control" maxlength="60" value="<?php echo html_escape(set_value('reference_code')); ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="notes" class="col-sm-3 col-form-label"><?php echo html_escape(display('notes') ?: 'Notes'); ?></label>
                        <div class="col-sm-6">
                            <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo html_escape(set_value('notes')); ?></textarea>
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="allow_override" name="allow_override" value="1" <?php echo set_checkbox('allow_override', '1'); ?>>
                                <label class="form-check-label" for="allow_override"><?php echo html_escape(display('allow_negative_override') ?: 'Allow negative stock override'); ?></label>
                            </div>
                            <small class="form-text text-muted"><?php echo html_escape(display('override_warning') ?: 'Enable only with supervisor approval.'); ?></small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success"><?php echo html_escape(display('save') ?: 'Save'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($recent_consumption)): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd lobidrag">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4><?php echo html_escape(display('recent_consumption') ?: 'Recent Consumption'); ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo html_escape(display('date') ?: 'Date'); ?></th>
                                    <th><?php echo html_escape(display('product') ?: 'Product'); ?></th>
                                    <th><?php echo html_escape(display('reference') ?: 'Reference'); ?></th>
                                    <th><?php echo html_escape(display('location') ?: 'Location'); ?></th>
                                    <th><?php echo html_escape(display('reason') ?: 'Reason'); ?></th>
                                    <th class="text-right"><?php echo html_escape(display('quantity') ?: 'Quantity'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_consumption as $row): ?>
                                    <tr>
                                        <td><?php echo html_escape(date('Y-m-d', strtotime($row['movement_date']))); ?></td>
                                        <td><?php echo html_escape($row['product_name']); ?></td>
                                        <td><?php echo html_escape($row['reference_id'] ?: $row['id']); ?></td>
                                        <td><?php echo html_escape($row['location_name']); ?></td>
                                        <td><?php echo html_escape($row['reason_code']); ?></td>
                                        <td class="text-right"><?php echo number_format($row['quantity_out'], 4) . ' ' . html_escape($row['unit_name']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($recent_notifications)): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd lobidrag">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4><?php echo html_escape(display('inventory_alerts') ?: 'Inventory Alerts'); ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo html_escape(display('date') ?: 'Date'); ?></th>
                                    <th><?php echo html_escape(display('type') ?: 'Type'); ?></th>
                                    <th><?php echo html_escape(display('severity') ?: 'Severity'); ?></th>
                                    <th><?php echo html_escape(display('message') ?: 'Message'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_notifications as $notification): ?>
                                    <tr>
                                        <td><?php echo html_escape(date('Y-m-d H:i', strtotime($notification['created_at']))); ?></td>
                                        <td><?php echo html_escape($notification['notification_type']); ?></td>
                                        <td>
                                            <span class="label label-<?php echo ($notification['severity'] === 'warning') ? 'warning' : 'info'; ?>">
                                                <?php echo html_escape(ucfirst($notification['severity'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo html_escape($notification['message']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
