<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <?php echo form_open('sheds' . (!empty($shed->id) ? '/edit/' . $shed->id : '/create'), array('class' => 'form-vertical')); ?>
                    <div class="form-group row">
                        <label for="name" class="col-sm-3 col-form-label"><?php echo html_escape(display('name') ?: 'Name'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo html_escape($shed->name); ?>" required maxlength="255">
                            <?php echo form_error('name', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description" class="col-sm-3 col-form-label"><?php echo html_escape(display('description') ?: 'Description'); ?></label>
                        <div class="col-sm-6">
                            <textarea name="description" id="description" class="form-control" rows="4" maxlength="1000"><?php echo html_escape($shed->description); ?></textarea>
                            <?php echo form_error('description', '<div class="text-danger">', '</div>'); ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-6">
                            <a href="<?php echo base_url('sheds'); ?>" class="btn btn-default"><?php echo html_escape(display('cancel') ?: 'Cancel'); ?></a>
                            <button type="submit" class="btn btn-success"><?php echo html_escape(display('save') ?: 'Save'); ?></button>
                        </div>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
