<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
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
})();
</script>
