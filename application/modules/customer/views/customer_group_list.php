<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-bd lobidrag">
      <div class="panel-heading">
        <div class="panel-title">
          <h4><?php echo html_escape($title); ?></h4>
        </div>
        <div class="text-right">
          <a href="<?php echo base_url('customer/customer_group_form'); ?>" class="btn btn-success btn-sm">Add Group</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>#</th>
              <th>Group Name</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($groups)) { $i=1; foreach ($groups as $g) { ?>
              <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo html_escape($g->group_name); ?></td>
                <td><?php echo $g->is_active ? 'Active' : 'Inactive'; ?></td>
                <td>
                  <a href="<?php echo base_url('customer/customer_group_form/'.$g->id); ?>" class="btn btn-info btn-xs">Edit</a>
                  <a href="<?php echo base_url('customer/customer_group_delete/'.$g->id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Delete this group?')">Delete</a>
                </td>
              </tr>
            <?php } } else { ?>
              <tr><td colspan="4" class="text-center">No groups</td></tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>