<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
                <div class="panel-title text-right">
                    <a href="<?php echo base_url('sheds/create'); ?>" class="btn btn-success btn-sm text-white">
                        <i class="ti-plus"></i> <?php echo html_escape(display('add_shed') ?: 'Add Shed'); ?>
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
                                <th><?php echo html_escape(display('description') ?: 'Description'); ?></th>
                                <th><?php echo html_escape(display('created_at') ?: 'Created At'); ?></th>
                                <th class="text-center"><?php echo html_escape(display('action') ?: 'Action'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sheds)): ?>
                                <?php foreach ($sheds as $index => $shed): ?>
                                    <tr>
                                        <td><?php echo (isset($offset) ? $offset : 0) + $index + 1; ?></td>
                                        <td><?php echo html_escape($shed['name']); ?></td>
                                        <td><?php echo html_escape($shed['description']); ?></td>
                                        <td><?php echo html_escape($shed['created_at']); ?></td>
                                        <td class="text-center">
                                            <a href="<?php echo base_url('sheds/edit/' . $shed['id']); ?>" class="btn btn-info btn-sm">
                                                <i class="ti-pencil"></i>
                                            </a>
                                            <a href="<?php echo base_url('sheds/delete/' . $shed['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo html_escape(display('are_you_sure') ?: 'Are you sure?'); ?>');">
                                                <i class="ti-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
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
