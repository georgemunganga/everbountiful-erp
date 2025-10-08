<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open('vaccine-usages' . (!empty($vaccine_usage->id) ? '/edit/' . $vaccine_usage->id : '/create'), array('class' => 'form-vertical')); ?>
                    <div class="form-group row">
                        <label for="vaccine_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('vaccine') ?: 'Vaccine'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="vaccine_id" id="vaccine_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($vaccines)): ?>
                                    <?php foreach ($vaccines as $vaccine): ?>
                                        <option value="<?php echo $vaccine['id']; ?>" <?php echo ((string) $vaccine_usage->vaccine_id === (string) $vaccine['id']) ? 'selected' : ''; ?>><?php echo html_escape($vaccine['name']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('vaccine_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="shed_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('shed') ?: 'Shed'); ?></label>
                        <div class="col-sm-6">
                            <select name="shed_id" id="shed_id" class="form-control">
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($sheds)): ?>
                                    <?php foreach ($sheds as $shed): ?>
                                        <option value="<?php echo $shed['id']; ?>" <?php echo ((string) $vaccine_usage->shed_id === (string) $shed['id']) ? 'selected' : ''; ?>><?php echo html_escape($shed['name']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('shed_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="usage_date" class="col-sm-3 col-form-label"><?php echo html_escape(display('usage_date') ?: 'Usage Date'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input type="date" name="usage_date" id="usage_date" class="form-control" required value="<?php echo html_escape($vaccine_usage->usage_date); ?>">
                            <?php echo form_error('usage_date', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="total_purchased_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('total_purchased') ?: 'Total Purchased'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" step="0.01" min="0" name="total_purchased_qty" id="total_purchased_qty" class="form-control" value="<?php echo html_escape($vaccine_usage->total_purchased_qty); ?>">
                            <?php echo form_error('total_purchased_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="used_total_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('used_total') ?: 'Used Total'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" step="0.01" min="0" name="used_total_qty" id="used_total_qty" class="form-control" value="<?php echo html_escape($vaccine_usage->used_total_qty); ?>">
                            <?php echo form_error('used_total_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="total_wasted_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('total_wasted') ?: 'Total Wasted'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" step="0.01" min="0" name="total_wasted_qty" id="total_wasted_qty" class="form-control" value="<?php echo html_escape($vaccine_usage->total_wasted_qty); ?>">
                            <?php echo form_error('total_wasted_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="total_instock_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('total_instock') ?: 'Total In Stock'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" step="0.01" min="0" name="total_instock_qty" id="total_instock_qty" class="form-control" value="<?php echo html_escape($vaccine_usage->total_instock_qty); ?>" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="notes" class="col-sm-3 col-form-label"><?php echo html_escape(display('notes') ?: 'Notes'); ?></label>
                        <div class="col-sm-6">
                            <textarea name="notes" id="notes" class="form-control" rows="4" maxlength="2000"><?php echo html_escape($vaccine_usage->notes); ?></textarea>
                            <?php echo form_error('notes', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-6">
                            <a href="<?php echo base_url('vaccine-usages'); ?>" class="btn btn-default"><?php echo html_escape(display('cancel') ?: 'Cancel'); ?></a>
                            <button type="submit" class="btn btn-success"><?php echo html_escape(display('save') ?: 'Save'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        const purchasedInput = document.getElementById('total_purchased_qty');
        const usedInput = document.getElementById('used_total_qty');
        const wastedInput = document.getElementById('total_wasted_qty');
        const instockInput = document.getElementById('total_instock_qty');

        function toNumber(input) {
            const value = parseFloat(input.value);
            return isNaN(value) ? 0 : value;
        }

        function updateInstock() {
            const purchased = toNumber(purchasedInput);
            const used = toNumber(usedInput);
            const wasted = toNumber(wastedInput);
            const instock = Math.max(0, purchased - used - wasted);
            instockInput.value = instock.toFixed(2);
        }

        [purchasedInput, usedInput, wastedInput].forEach(function (input) {
            if (input) {
                input.addEventListener('input', updateInstock);
            }
        });

        updateInstock();
    })();
</script>
