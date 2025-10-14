<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
                <div class="panel-title text-right">
                    <a href="<?php echo base_url('productions/create'); ?>" class="btn btn-success btn-sm text-white">
                        <i class="ti-plus"></i> <?php echo html_escape(display('add_production') ?: 'Add Production'); ?>
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
                                <th><?php echo html_escape(display('name') ?: 'Name'); ?></th>
                                <th><?php echo html_escape(display('shed') ?: 'Shed'); ?></th>
                                <th><?php echo html_escape(display('produced_total_qty') ?: 'Total Qty'); ?></th>
                                <th><?php echo html_escape(display('produced_mortality_qty') ?: 'Mortality Qty'); ?></th>
                                <th><?php echo html_escape(display('produced_damaged_qty') ?: 'Damaged Qty'); ?></th>
                                <th><?php echo html_escape(display('produced_extras_qty') ?: 'Extras Qty'); ?></th>
                                <th><?php echo html_escape(display('unit') ?: 'Unit'); ?></th>
                                <th><?php echo html_escape(display('created_at') ?: 'Created At'); ?></th>
                                <th class="text-center"><?php echo html_escape(display('action') ?: 'Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($productions)): ?>
                                <?php foreach ($productions as $index => $production): ?>
                                    <tr>
                                        <td><?php echo (isset($offset) ? $offset : 0) + $index + 1; ?></td>
                                        <td><?php echo html_escape($production['name']); ?></td>
                                        <td><?php echo html_escape($production['shed_name']); ?></td>
                                        <td><?php echo html_escape($production['produced_total_qty']); ?></td>
                                        <td><?php echo html_escape($production['produced_mortality_qty']); ?></td>
                                        <td><?php echo html_escape($production['produced_damaged_qty']); ?></td>
                                        <td><?php echo html_escape($production['produced_extras_qty']); ?></td>
                                        <td><?php echo html_escape($production['unit_name']); ?></td>
                                        <td><?php echo html_escape($production['created_at']); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo base_url('productions/edit/' . $production['id']); ?>" class="btn btn-info btn-sm">
                                                <i class="ti-pencil"></i>
                                            </a>
                                            <a href="<?php echo base_url('productions/delete/' . $production['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo html_escape(display('are_you_sure') ?: 'Are you sure?'); ?>');">
                                                <i class="ti-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="text-center text-muted">
                                        <?php echo html_escape(display('no_data_found') ?: 'No data found'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (!empty($links)): ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php echo $links; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
