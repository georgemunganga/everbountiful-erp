<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
                <div class="panel-title text-right">
                    <a href="<?php echo base_url('vaccine-usages/create'); ?>" class="btn btn-success btn-sm text-white">
                        <i class="ti-plus"></i> <?php echo html_escape(display('add_vaccine_usage') ?: 'Add Vaccine Usage'); ?>
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
                                <th><?php echo html_escape(display('vaccine') ?: 'Vaccine'); ?></th>
                                <th><?php echo html_escape(display('shed') ?: 'Shed'); ?></th>
                                <th><?php echo html_escape(display('usage_date') ?: 'Usage Date'); ?></th>
                                <th><?php echo html_escape(display('total_purchased') ?: 'Total Purchased'); ?></th>
                                <th><?php echo html_escape(display('used_total') ?: 'Used Total'); ?></th>
                                <th><?php echo html_escape(display('total_wasted') ?: 'Total Wasted'); ?></th>
                                <th><?php echo html_escape(display('total_instock') ?: 'Total In Stock'); ?></th>
                                <th class="text-center"><?php echo html_escape(display('action') ?: 'Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($vaccine_usages)): ?>
                                <?php foreach ($vaccine_usages as $index => $usage): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo html_escape($usage['vaccine_name']); ?></td>
                                        <td><?php echo html_escape($usage['shed_name']); ?></td>
                                        <td><?php echo html_escape($usage['usage_date']); ?></td>
                                        <td><?php echo html_escape($usage['total_purchased_qty']); ?></td>
                                        <td><?php echo html_escape($usage['used_total_qty']); ?></td>
                                        <td><?php echo html_escape($usage['total_wasted_qty']); ?></td>
                                        <td><?php echo html_escape($usage['total_instock_qty']); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo base_url('vaccine-usages/edit/' . $usage['id']); ?>" class="btn btn-info btn-sm">
                                                <i class="ti-pencil"></i>
                                            </a>
                                            <a href="<?php echo base_url('vaccine-usages/delete/' . $usage['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo html_escape(display('are_you_sure') ?: 'Are you sure?'); ?>');">
                                                <i class="ti-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
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
