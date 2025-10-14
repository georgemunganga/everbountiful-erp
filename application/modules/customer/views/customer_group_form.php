<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-bd lobidrag">
      <div class="panel-heading">
        <div class="panel-title">
          <h4><?php echo html_escape($title); ?></h4>
        </div>
      </div>
      <div class="panel-body">
        <?php echo form_open('customer/customer_group_save', ['class' => '', 'id' => 'customer_group_form']); ?>
          <input type="hidden" name="id" value="<?php echo !empty($group) ? (int)$group->id : ''; ?>">
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Group Name</label>
            <div class="col-sm-6">
              <input type="text" name="group_name" class="form-control" value="<?php echo !empty($group) ? html_escape($group->group_name) : ''; ?>" required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Description</label>
            <div class="col-sm-6">
              <textarea name="description" class="form-control" rows="3"><?php echo !empty($group) ? html_escape($group->description) : ''; ?></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Active</label>
            <div class="col-sm-6">
              <input type="checkbox" name="is_active" value="1" <?php echo (!empty($group) && (int)$group->is_active === 1) ? 'checked' : 'checked'; ?>>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-sm-8 text-right">
              <button type="submit" class="btn btn-success">Save</button>
              <a href="<?php echo base_url('customer/customer_groups'); ?>" class="btn btn-default">Cancel</a>
            </div>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>