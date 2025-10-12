<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
                <div class="panel-title text-right">
                    <a href="<?php echo base_url('livestocks/create'); ?>" class="btn btn-success btn-sm text-white">
                        <i class="ti-plus"></i> <?php echo html_escape(display('add_livestock') ?: 'Add Livestock'); ?>
                    </a>
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
                <?php if (!empty($active_name_filter)): ?>
                    <div class="alert alert-info" role="alert">
                        <?php echo html_escape(sprintf((display('filtered_by_name') ?: 'Filtered by name: %s'), $active_name_filter)); ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo html_escape(display('name') ?: 'Name'); ?></th>
                                <th><?php echo html_escape(display('shed') ?: 'Shed'); ?></th>
                                <th><?php echo html_escape(display('livestock_group') ?: 'Group'); ?></th>
                                <th><?php echo html_escape(display('livestock_total_qty') ?: 'Total Qty'); ?></th>
                                <th><?php echo html_escape(display('livestock_female_qty') ?: 'Female Qty'); ?></th>
                                <th><?php echo html_escape(display('livestock_male_qty') ?: 'Male Qty'); ?></th>
                                <th><?php echo html_escape(display('livestock_mortality_qty') ?: 'Mortality'); ?></th>
                                <th><?php echo html_escape(display('unit') ?: 'Unit'); ?></th>
                                <th><?php echo html_escape(display('created_at') ?: 'Created At'); ?></th>
                                <th class="text-center"><?php echo html_escape(display('action') ?: 'Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($livestocks)): ?>
                                <?php foreach ($livestocks as $index => $livestock): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo html_escape($livestock['name']); ?></td>
                                        <td><?php echo html_escape($livestock['shed_name']); ?></td>
                                        <td><?php echo html_escape($livestock['group_name']); ?></td>
                                        <td><?php echo html_escape($livestock['livestock_total_qty']); ?></td>
                                        <td><?php echo html_escape($livestock['livestock_female_qty']); ?></td>
                                        <td><?php echo html_escape($livestock['livestock_male_qty']); ?></td>
                                        <td><?php echo html_escape($livestock['livestock_mortality_qty']); ?></td>
                                        <td><?php echo html_escape($livestock['unit_name']); ?></td>
                                        <td><?php echo html_escape($livestock['created_at']); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo base_url('livestocks/edit/' . $livestock['id']); ?>" class="btn btn-info btn-sm">
                                                <i class="ti-pencil"></i>
                                            </a>
                                            <a href="<?php echo base_url('livestocks/delete/' . $livestock['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo html_escape(display('are_you_sure') ?: 'Are you sure?'); ?>');">
                                                <i class="ti-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted">
                                        <?php echo html_escape(display('no_data_found') ?: 'No data found'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
