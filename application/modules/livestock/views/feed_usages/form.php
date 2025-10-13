<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php if (!empty($recent_notifications)) : ?>
                    <div class="alert alert-info">
                        <strong><?php echo html_escape(display('notifications') ?: 'Notifications'); ?>:</strong>
                        <ul class="mb-0">
                            <?php foreach ($recent_notifications as $n) : ?>
                                <li>
                                    <span class="label label-<?php echo html_escape($n['severity'] === 'warning' ? 'warning' : ($n['severity'] === 'error' ? 'danger' : 'info')); ?>">
                                        <?php echo html_escape(ucfirst($n['severity'])); ?>
                                    </span>
                                    <?php echo html_escape($n['message']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <?php echo form_open('feed-usages' . (!empty($feed_usage->id) ? '/edit/' . $feed_usage->id : '/create'), array('class' => 'form-vertical', 'id' => 'feed-usage-form')); ?>
                    <div class="form-group row">
                        <label for="feed_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('feed') ?: 'Feed'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="feed_id" id="feed_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($feeds)): ?>
                                    <?php foreach ($feeds as $feed): ?>
                                        <option value="<?php echo $feed['id']; ?>" <?php echo ((string) $feed_usage->feed_id === (string) $feed['id']) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($feed['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('feed_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="shed_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('shed') ?: 'Shed'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="shed_id" id="shed_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($sheds)): ?>
                                    <?php foreach ($sheds as $shed): ?>
                                        <option value="<?php echo $shed['id']; ?>" <?php echo ((string) $feed_usage->shed_id === (string) $shed['id']) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($shed['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('shed_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><?php echo html_escape(display('auto_allocate') ?: 'Auto allocate'); ?></label>
                        <div class="col-sm-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="auto_allocate" name="auto_allocate" value="1" <?php echo set_checkbox('auto_allocate', '1', true); ?>>
                                <label class="form-check-label" for="auto_allocate"><?php echo html_escape(display('auto_allocate_hint') ?: 'Auto select lots by product and location'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inventory_product_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('product') ?: 'Product'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="inventory_product_id" id="inventory_product_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($inventory_products)): ?>
                                    <?php foreach ($inventory_products as $product): ?>
                                        <option value="<?php echo $product['product_id']; ?>" <?php echo ((string) $feed_usage->inventory_product_id === (string) $product['product_id']) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($product['product_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('inventory_product_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="location_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('location') ?: 'Location'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="location_id" id="location_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($locations)): ?>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo $loc['id']; ?>">
                                            <?php echo html_escape($loc['location_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('location_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div id="manual-lot-fields">
                        <div class="form-group row">
                            <label for="lot_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('lot') ?: 'Lot'); ?></label>
                            <div class="col-sm-6">
                                <select name="lot_id" id="lot_id" class="form-control">
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
                                            <option value="<?php echo $lot['lot_id']; ?>" <?php echo set_select('lot_id', $lot['lot_id']); ?>>
                                                <?php echo html_escape($label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="total_purchased_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('total_purchased') ?: 'Total Purchased'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="total_purchased_qty" id="total_purchased_qty" class="form-control feed-qty" step="0.01" value="<?php echo html_escape($feed_usage->total_purchased_qty); ?>">
                            <?php echo form_error('total_purchased_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="used_total_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('used_total') ?: 'Used Total'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="used_total_qty" id="used_total_qty" class="form-control feed-qty" step="0.01" value="<?php echo html_escape($feed_usage->used_total_qty); ?>">
                            <?php echo form_error('used_total_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="total_wasted_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('total_wasted') ?: 'Total Wasted'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="total_wasted_qty" id="total_wasted_qty" class="form-control feed-qty" step="0.01" value="<?php echo html_escape($feed_usage->total_wasted_qty); ?>">
                            <?php echo form_error('total_wasted_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="total_instock_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('total_instock') ?: 'Total In Stock'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="total_instock_qty" id="total_instock_qty" class="form-control" step="0.01" value="<?php echo html_escape($feed_usage->total_instock_qty); ?>" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><?php echo html_escape(display('allow_negative_override') ?: 'Allow override'); ?></label>
                        <div class="col-sm-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="allow_override" name="allow_override" value="1" <?php echo set_checkbox('allow_override', '1'); ?>>
                                <label class="form-check-label" for="allow_override"><?php echo html_escape(display('override_warning') ?: 'Enable only with supervisor approval.'); ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-6">
                            <a href="<?php echo base_url('feed-usages'); ?>" class="btn btn-default"><?php echo html_escape(display('cancel') ?: 'Cancel'); ?></a>
                            <button type="submit" class="btn btn-success"><?php echo html_escape(display('save') ?: 'Save'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function recalcInstock() {
        var totalPurchased = parseFloat(document.getElementById('total_purchased_qty').value) || 0;
        var usedTotal      = parseFloat(document.getElementById('used_total_qty').value) || 0;
        var totalWasted    = parseFloat(document.getElementById('total_wasted_qty').value) || 0;
        var instock        = totalPurchased - usedTotal - totalWasted;
        if (instock < 0) {
            instock = 0;
        }
        document.getElementById('total_instock_qty').value = instock.toFixed(2);
    }

    var qtyInputs = document.querySelectorAll('#feed-usage-form .feed-qty');
    qtyInputs.forEach(function(input) {
        input.addEventListener('input', recalcInstock);
        input.addEventListener('change', recalcInstock);
    });

    recalcInstock();

    var autoToggle = document.getElementById('auto_allocate');
    var manualFields = document.getElementById('manual-lot-fields');
    function refreshFields() {
        if (!autoToggle) {
            return;
        }
        var checked = autoToggle.checked;
        if (manualFields) {
            manualFields.style.display = checked ? 'none' : '';
            var lotSelect = manualFields.querySelector('#lot_id');
            if (lotSelect) {
                lotSelect.required = !checked;
            }
        }
    }
    if (autoToggle) {
        autoToggle.addEventListener('change', refreshFields);
        refreshFields();
    }
})();
</script>
