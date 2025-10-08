<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open('vaccines' . (!empty($vaccine->id) ? '/edit/' . $vaccine->id : '/create'), array('class' => 'form-vertical')); ?>
                    <div class="form-group row">
                        <label for="name" class="col-sm-3 col-form-label"><?php echo html_escape(display('name') ?: 'Name'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input type="text" name="name" id="name" class="form-control" maxlength="255" required value="<?php echo html_escape($vaccine->name); ?>">
                            <?php echo form_error('name', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="manufacturer" class="col-sm-3 col-form-label"><?php echo html_escape(display('manufacturer') ?: 'Manufacturer'); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="manufacturer" id="manufacturer" class="form-control" maxlength="255" value="<?php echo html_escape($vaccine->manufacturer); ?>">
                            <?php echo form_error('manufacturer', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="disease_target" class="col-sm-3 col-form-label"><?php echo html_escape(display('disease_target') ?: 'Disease Target'); ?></label>
                        <div class="col-sm-6">
                            <input type="text" name="disease_target" id="disease_target" class="form-control" maxlength="255" value="<?php echo html_escape($vaccine->disease_target); ?>">
                            <?php echo form_error('disease_target', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="unit_type_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('purchase_unit') ?: 'Unit'); ?></label>
                        <div class="col-sm-6">
                            <select name="unit_type_id" id="unit_type_id" class="form-control">
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php if (!empty($units)): ?>
                                    <?php foreach ($units as $unit): ?>
                                        <option value="<?php echo $unit['unit_id']; ?>" <?php echo ((string) $vaccine->unit_type_id === (string) $unit['unit_id']) ? 'selected' : ''; ?>><?php echo html_escape($unit['unit_name']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php echo form_error('unit_type_id', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description" class="col-sm-3 col-form-label"><?php echo html_escape(display('description') ?: 'Description'); ?></label>
                        <div class="col-sm-6">
                            <textarea name="description" id="description" class="form-control" rows="4" maxlength="1000"><?php echo html_escape($vaccine->description); ?></textarea>
                            <?php echo form_error('description', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-6">
                            <a href="<?php echo base_url('vaccines'); ?>" class="btn btn-default"><?php echo html_escape(display('cancel') ?: 'Cancel'); ?></a>
                            <button type="submit" class="btn btn-success"><?php echo html_escape(display('save') ?: 'Save'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
