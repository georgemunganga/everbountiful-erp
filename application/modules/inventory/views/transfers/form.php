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
                <?php echo form_open(isset($form_action) ? $form_action : 'inventory/transfers'); ?>
                    <div class="form-group row">
                        <label for="from_location_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('from') ?: 'From Location'); ?></label>
                        <div class="col-sm-6">
                            <select name="from_location_id" id="from_location_id" class="form-control">
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($locations)): ?>
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?php echo $location['id']; ?>" <?php echo set_select('from_location_id', $location['id']); ?>>
                                            <?php echo html_escape($location['location_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="source_lot_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('lot') ?: 'Lot'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="source_lot_id" id="source_lot_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($lots)): ?>
                                    <?php foreach ($lots as $lot): ?>
                                        <?php
                                            $label = sprintf(
                                                '%s - %s (%s) [%s %s]',
                                                $lot['lot_code'],
                                                $lot['product_name'],
                                                $lot['location_name'],
                                                number_format($lot['available_qty'], 4),
                                                $lot['unit_name']
                                            );
                                        ?>
                                        <option value="<?php echo $lot['lot_id']; ?>" <?php echo set_select('source_lot_id', $lot['lot_id']); ?>>
                                            <?php echo html_escape($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="to_location_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('to') ?: 'To Location'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="to_location_id" id="to_location_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($locations)): ?>
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?php echo $location['id']; ?>" <?php echo set_select('to_location_id', $location['id']); ?>>
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
                            <input type="number" step="0.01" min="0" name="quantity" id="quantity" class="form-control" required value="<?php echo html_escape(set_value('quantity')); ?>">
                            <small class="form-text text-muted"><?php echo html_escape(display('quantity_in_base_unit') ?: 'Enter quantity in the lot base unit.'); ?></small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="transfer_date" class="col-sm-3 col-form-label"><?php echo html_escape(display('date') ?: 'Date'); ?></label>
                        <div class="col-sm-6">
                            <input type="date" name="transfer_date" id="transfer_date" class="form-control" value="<?php echo html_escape(set_value('transfer_date', date('Y-m-d'))); ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="transfer_code" class="col-sm-3 col-form-label"><?php echo html_escape(display('reference') ?: 'Reference'); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="transfer_code" id="transfer_code" class="form-control" maxlength="60" value="<?php echo html_escape(set_value('transfer_code')); ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="notes" class="col-sm-3 col-form-label"><?php echo html_escape(display('notes') ?: 'Notes'); ?></label>
                        <div class="col-sm-6">
                            <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo html_escape(set_value('notes')); ?></textarea>
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

<?php if (!empty($recent_transfers)): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd lobidrag">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4><?php echo html_escape(display('recent_transfers') ?: 'Recent Transfers'); ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo html_escape(display('reference') ?: 'Reference'); ?></th>
                                    <th><?php echo html_escape(display('date') ?: 'Date'); ?></th>
                                    <th><?php echo html_escape(display('product') ?: 'Product'); ?></th>
                                    <th><?php echo html_escape(display('from') ?: 'From'); ?></th>
                                    <th><?php echo html_escape(display('to') ?: 'To'); ?></th>
                                    <th class="text-right"><?php echo html_escape(display('quantity') ?: 'Quantity'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_transfers as $row): ?>
                                    <tr>
                                        <td><?php echo html_escape($row['reference_id']); ?></td>
                                        <td><?php echo html_escape(date('Y-m-d', strtotime($row['movement_date']))); ?></td>
                                        <td><?php echo html_escape($row['product_name']); ?></td>
                                        <td><?php echo html_escape($row['source_location']); ?></td>
                                        <td><?php echo html_escape($row['destination_location'] ?? '-'); ?></td>
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
