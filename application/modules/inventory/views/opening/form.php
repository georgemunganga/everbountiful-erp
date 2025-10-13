<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php if ($this->session->flashdata('message')): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('exception')): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo $this->session->flashdata('exception'); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($validation_errors)): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo $validation_errors; ?>
                    </div>
                <?php endif; ?>

                <?php echo form_open($form_action, array('class' => 'form-vertical')); ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="product_id"><?php echo html_escape(display('product') ?: 'Product'); ?> <i class="text-danger">*</i></label>
                            <select name="product_id" id="product_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo html_escape($product['product_id']); ?>" <?php echo set_select('product_id', $product['product_id']); ?>>
                                            <?php echo html_escape($product['product_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="location_id"><?php echo html_escape(display('location') ?: 'Location'); ?> <i class="text-danger">*</i></label>
                            <select name="location_id" id="location_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($locations)): ?>
                                    <?php foreach ($locations as $location): ?>
                                        <option value="<?php echo (int) $location['id']; ?>" <?php echo set_select('location_id', $location['id']); ?>>
                                            <?php echo html_escape($location['location_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="opening_date"><?php echo html_escape(display('date') ?: 'Date'); ?> <i class="text-danger">*</i></label>
                            <input type="date" name="opening_date" id="opening_date" class="form-control" value="<?php echo html_escape(set_value('opening_date', date('Y-m-d'))); ?>" required>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="quantity"><?php echo html_escape(display('qty') ?: 'Qty'); ?> <i class="text-danger">*</i></label>
                            <input type="number" step="0.01" min="0" name="quantity" id="quantity" class="form-control" value="<?php echo html_escape(set_value('quantity')); ?>" required>
                            <small class="text-muted">
                                <?php echo html_escape(display('quantity_help_base_unit') ?: 'Enter the Qty in the product\'s default units.'); ?>
                            </small>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="reference_code"><?php echo html_escape(display('reference') ?: 'Reference'); ?></label>
                            <input type="text" name="reference_code" id="reference_code" class="form-control" value="<?php echo html_escape(set_value('reference_code')); ?>" placeholder="OB-PROD-LOCATION">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="notes"><?php echo html_escape(display('notes') ?: 'Notes'); ?></label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"><?php echo html_escape(set_value('notes')); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="replace_existing" value="1" <?php echo set_checkbox('replace_existing', '1'); ?>>
                                <?php echo html_escape(display('replace_existing_balance') ?: 'Replace existing opening balance for this product/location'); ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-success"><?php echo html_escape(display('save') ?: 'Save'); ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($recent_openings)): ?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape(display('recent_opening_balances') ?: 'Recent Opening Balances'); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?php echo html_escape(display('date') ?: 'Date'); ?></th>
                                <th><?php echo html_escape(display('product') ?: 'Product'); ?></th>
                                <th><?php echo html_escape(display('location') ?: 'Location'); ?></th>
                                <th><?php echo html_escape(display('quantity') ?: 'Quantity'); ?></th>
                                <th><?php echo html_escape(display('reference') ?: 'Reference'); ?></th>
                                <th><?php echo html_escape(display('notes') ?: 'Notes'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_openings as $opening): ?>
                                <tr>
                                    <td><?php echo html_escape($opening['movement_date']); ?></td>
                                    <td><?php echo html_escape($opening['product_name']); ?></td>
                                    <td><?php echo html_escape($opening['location_name']); ?></td>
                                    <td><?php echo html_escape(number_format((float) $opening['quantity_in'], 2)); ?> <?php echo html_escape($opening['unit_name']); ?></td>
                                    <td><?php echo html_escape($opening['reference_id']); ?></td>
                                    <td><?php echo html_escape($opening['narration']); ?></td>
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
