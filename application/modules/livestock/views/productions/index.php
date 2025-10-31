<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
                <div class="panel-title text-right">
                    <a href="<?php echo base_url('productions/import'); ?>" class="btn btn-primary btn-sm text-white" style="margin-right:6px;">
                        <i class="ti-upload"></i> <?php echo html_escape(display('import') ?: 'Import'); ?>
                    </a>
                    <a href="<?php echo base_url('productions/create'); ?>" class="btn btn-success btn-sm text-white">
                        <i class="ti-plus"></i> <?php echo html_escape(display('add_production') ?: 'Add Production'); ?>
                    </a>
                </div>
            </div>
            <div class="panel-body">
                <form method="get" action="<?php echo base_url('productions'); ?>" class="form-inline" style="margin-bottom:10px;">
                    <div class="form-group" style="margin-right:10px;">
                        <label style="display:block; font-weight:normal; color:#4b5563;">Search</label>
                        <input type="text" name="q" value="<?php echo html_escape(isset($active_q) ? $active_q : ''); ?>" class="form-control" placeholder="Name, Shed, Product">
                    </div>
                    <div class="form-group" style="margin-right:10px;">
                        <label style="display:block; font-weight:normal; color:#4b5563;">From</label>
                        <input type="date" name="from" value="<?php echo html_escape(isset($active_from) ? $active_from : ''); ?>" class="form-control">
                    </div>
                    <div class="form-group" style="margin-right:10px;">
                        <label style="display:block; font-weight:normal; color:#4b5563;">To</label>
                        <input type="date" name="to" value="<?php echo html_escape(isset($active_to) ? $active_to : ''); ?>" class="form-control">
                    </div>
                    <div class="form-group" style="margin-top:22px;">
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        <a class="btn btn-default btn-sm" href="<?php echo base_url('productions'); ?>" style="margin-left:6px;">Reset</a>
                    </div>
                </form>
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
                                <th><?php echo 'Total Eggs'; ?></th>
                                <th><?php echo html_escape(display('produced_mortality_qty') ?: 'Mortality Qty'); ?></th>
                                <th><?php echo 'Damaged Eggs'; ?></th>
                                <th><?php echo 'Extras Eggs'; ?></th>
                                <th><?php echo html_escape(display('unit') ?: 'Unit'); ?></th>
                                <th><?php echo html_escape(display('date') ?: 'Date'); ?></th>
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
                                        <?php
                                            // Convert tray-based quantities to eggs for display
                                            $EGGS_PER_TRAY = 30; // display convention
                                            $trays_total   = (float) ($production['produced_total_qty'] ?? 0);
                                            $trays_damaged = (float) ($production['produced_damaged_qty'] ?? 0);
                                            $trays_extras  = (float) ($production['produced_extras_qty'] ?? 0);
                                            $eggs_total    = ($trays_total * $EGGS_PER_TRAY) + ($trays_extras * $EGGS_PER_TRAY);
                                            $eggs_damaged  = ($trays_damaged * $EGGS_PER_TRAY);
                                            $eggs_extras   = ($trays_extras * $EGGS_PER_TRAY);
                                        ?>
                                        <td><?php echo html_escape(number_format($eggs_total, 0)); ?></td>
                                        <td><?php echo html_escape($production['produced_mortality_qty']); ?></td>
                                        <td><?php echo html_escape(number_format($eggs_damaged, 0)); ?></td>
                                        <td><?php echo html_escape(number_format($eggs_extras, 0)); ?></td>
                                        <td><?php echo 'Eggs'; ?></td>
                                        <td>
                                            <?php
                                                $pdate = isset($production['production_date']) ? trim((string)$production['production_date']) : '';
                                                if ($pdate === '' || $pdate === '0000-00-00') {
                                                    $pdate = isset($production['created_at']) ? substr((string)$production['created_at'], 0, 10) : '';
                                                }
                                                echo html_escape($pdate);
                                            ?>
                                        </td>
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
