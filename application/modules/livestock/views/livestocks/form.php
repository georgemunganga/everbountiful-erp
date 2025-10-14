<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open('livestocks' . (!empty($livestock->id) ? '/edit/' . $livestock->id : '/create'), array('class' => 'form-vertical')); ?>
                    <div class="form-group row">
                        <label for="name" class="col-sm-3 col-form-label"><?php echo html_escape(display('name') ?: 'Name'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input type="text" name="name" id="name" class="form-control" required maxlength="255" value="<?php echo html_escape($livestock->name); ?>">
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
                                        <option value="<?php echo $shed['id']; ?>" <?php echo ((string) $livestock->shed_id === (string) $shed['id']) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($shed['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('shed_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="livestock_group_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('livestock_group') ?: 'Livestock Group'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="livestock_group_id" id="livestock_group_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($livestock_groups)): ?>
                                    <?php foreach ($livestock_groups as $group): ?>
                                        <option value="<?php echo $group['id']; ?>" <?php echo ((string) $livestock->livestock_group_id === (string) $group['id']) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($group['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('livestock_group_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="unit_type_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('unit') ?: 'Unit'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="unit_type_id" id="unit_type_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($units)): ?>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?php echo $unit['unit_id']; ?>" <?php echo ((string) $livestock->unit_type_id === (string) $unit['unit_id']) ? 'selected' : ''; ?>>
                                            <?php echo html_escape($unit['unit_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('unit_type_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="livestock_total_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('livestock_total_qty') ?: 'Total Qty'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="livestock_total_qty" id="livestock_total_qty" class="form-control" step="0.01" value="<?php echo html_escape($livestock->livestock_total_qty); ?>">
                            <?php echo form_error('livestock_total_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="livestock_female_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('livestock_female_qty') ?: 'Female Qty'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="livestock_female_qty" id="livestock_female_qty" class="form-control" step="0.01" value="<?php echo html_escape($livestock->livestock_female_qty); ?>">
                            <?php echo form_error('livestock_female_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="livestock_male_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('livestock_male_qty') ?: 'Male Qty'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="livestock_male_qty" id="livestock_male_qty" class="form-control" step="0.01" value="<?php echo html_escape($livestock->livestock_male_qty); ?>">
                            <?php echo form_error('livestock_male_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="livestock_mortality_qty" class="col-sm-3 col-form-label"><?php echo html_escape(display('livestock_mortality_qty') ?: 'Mortality Qty'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" name="livestock_mortality_qty" id="livestock_mortality_qty" class="form-control" step="0.01" value="<?php echo html_escape($livestock->livestock_mortality_qty); ?>">
                            <?php echo form_error('livestock_mortality_qty', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="notes" class="col-sm-3 col-form-label"><?php echo html_escape(display('notes') ?: 'Notes'); ?></label>
                        <div class="col-sm-6">
                            <textarea name="notes" id="notes" class="form-control" rows="4" maxlength="1000"><?php echo html_escape($livestock->notes); ?></textarea>
                            <?php echo form_error('notes', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-6">
                            <a href="<?php echo base_url('livestocks'); ?>" class="btn btn-default"><?php echo html_escape(display('cancel') ?: 'Cancel'); ?></a>
                            <button type="submit" class="btn btn-success"><?php echo html_escape(display('save') ?: 'Save'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
