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
                        <label for="unit_type_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('unit') ?: 'Unit'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="unit_type_id" id="unit_type_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($units)): ?>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?php echo $unit['unit_id']; ?>" <?php echo ((string) $production->unit_type_id === (string) $unit['unit_id']) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($unit['unit_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('unit_type_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="produced_total_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('produced_total_qty') ?: 'Produced Total Qty'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="produced_total_qty" id="produced_total_qty" class="form-control" step="0.01" value="<?php echo html_escape($production->produced_total_qty); ?>">
                            <?php echo form_error('produced_total_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="produced_mortality_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('produced_mortality_qty') ?: 'Mortality Qty'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="produced_mortality_qty" id="produced_mortality_qty" class="form-control" step="0.01" value="<?php echo html_escape($production->produced_mortality_qty); ?>">
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
                            <input type="number" name="produced_extras_qty" id="produced_extras_qty" class="form-control" step="0.01" value="<?php echo html_escape($production->produced_extras_qty); ?>">
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
            </div>
        </div>
    </div>
</div>
