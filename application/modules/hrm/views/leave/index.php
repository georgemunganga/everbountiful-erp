<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
                <div class="panel-title text-right">
                    <a href="<?php echo base_url('leaves/create'); ?>" class="btn btn-success btn-sm text-white">
                        <i class="ti-plus"></i> <?php echo html_escape(display('add_leave') ?: 'Add Leave'); ?>
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

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo html_escape(display('employee_name') ?: 'Employee'); ?></th>
                                <th><?php echo html_escape(display('leave_type') ?: 'Leave Type'); ?></th>
                                <th><?php echo html_escape(display('start_date') ?: 'Start Date'); ?></th>
                                <th><?php echo html_escape(display('end_date') ?: 'End Date'); ?></th>
                                <th><?php echo html_escape(display('leave_days') ?: 'Leave Days'); ?></th>
                                <th><?php echo html_escape(display('status') ?: 'Status'); ?></th>
                                <th class="text-center"><?php echo html_escape(display('action') ?: 'Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($leaves)): ?>
                                <?php foreach ($leaves as $index => $leave): ?>
                                    <tr>
                                        <td><?php echo (isset($offset) ? $offset : 0) + $index + 1; ?></td>
                                        <td><?php echo html_escape(trim(($leave['first_name'] ?? '') . ' ' . ($leave['last_name'] ?? ''))); ?></td>
                                        <td><?php echo html_escape($leave['leave_type']); ?></td>
                                        <td><?php echo html_escape($leave['start_date']); ?></td>
                                        <td><?php echo html_escape($leave['end_date']); ?></td>
                                        <td><?php echo html_escape(number_format((float) $leave['leave_days'], 2)); ?></td>
                                        <td>
                                            <span class="label label-<?php echo ($leave['status'] === 'approved') ? 'success' : (($leave['status'] === 'pending') ? 'warning' : 'danger'); ?>">
                                                <?php echo html_escape(ucfirst($leave['status'])); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?php echo base_url('leaves/create/' . $leave['id']); ?>" class="btn btn-info btn-sm">
                                                <i class="ti-pencil"></i>
                                            </a>
                                            <a href="<?php echo base_url('leaves/delete/' . $leave['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo html_escape(display('are_you_sure') ?: 'Are you sure?'); ?>');">
                                                <i class="ti-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <?php echo html_escape(display('no_data_found') ?: 'No data found'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($links)): ?>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <?php echo $links; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
