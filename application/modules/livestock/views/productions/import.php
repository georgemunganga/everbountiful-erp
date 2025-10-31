<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title ?: 'Import Productions'); ?></h4>
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

                <?php echo form_open_multipart('productions/import', 'class="form-inline"'); ?>
                    <div class="form-group" style="margin-right:12px; margin-bottom:10px;">
                        <label style="display:block; font-weight:normal; color:#4b5563;">Excel File</label>
                        <input type="file" name="xlsx" accept=".xlsx,.xls,.csv" class="form-control" required>
                    </div>

                    <div class="form-group" style="margin-right:12px; margin-bottom:10px;">
                        <label style="display:block; font-weight:normal; color:#4b5563;">Shed <span style="color:#dc2626;">*</span></label>
                        <select name="shed_id" class="form-control" required>
                            <option value="">-- Select Shed --</option>
                            <?php if (!empty($sheds)) { foreach ($sheds as $s) { ?>
                                <option value="<?php echo (int) $s['id']; ?>"><?php echo html_escape($s['name']); ?></option>
                            <?php } } ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-right:12px; margin-bottom:10px;">
                        <label style="display:block; font-weight:normal; color:#4b5563;">Output Product (optional)</label>
                        <select name="output_product_id" class="form-control">
                            <option value="">-- Auto (tries Eggs) --</option>
                            <?php if (!empty($products)) { foreach ($products as $p) { ?>
                                <option value="<?php echo html_escape($p['product_id']); ?>" <?php echo (!empty($default_product_id) && $default_product_id === $p['product_id']) ? 'selected' : ''; ?>>
                                    <?php echo html_escape($p['product_name']); ?>
                                </option>
                            <?php } } ?>
                        </select>
                    </div>

                    <div class="form-group" style="margin-right:12px; margin-bottom:10px;">
                        <label style="display:block; font-weight:normal; color:#4b5563;">Eggs per tray</label>
                        <input type="number" min="1" step="1" class="form-control" name="eggs_per_tray" value="30">
                    </div>

                    <div class="form-group" style="margin-right:12px; margin-bottom:10px;">
                        <label style="display:block; font-weight:normal; color:#4b5563;">Name Prefix</label>
                        <input type="text" class="form-control" name="name_prefix" value="Daily Production">
                    </div>

                    <div class="form-group" style="margin-bottom:10px;">
                        <label style="display:block; visibility:hidden;">&nbsp;</label>
                        <button type="submit" class="btn btn-success">Import</button>
                        <a href="<?php echo base_url('productions'); ?>" class="btn btn-default" style="margin-left:6px;">Back</a>
                    </div>
                <?php echo form_close(); ?>

                <hr>
                <p class="text-muted" style="margin-top:10px;">
                    Expected headers include at least <strong>Date</strong> and either <strong>Total Eggs</strong> or <strong>Trays</strong>.
                    If you provide <strong>Trays</strong>, the importer uses “Eggs per tray” (default 30) and adds <strong>Extras</strong> (leftover eggs not forming a full tray).
                    Optional columns: <strong>Damaged/Broken</strong> (reduces egg stock), <strong>Mortality</strong> (bird deaths; tracked on production record only), and <strong>Notes</strong>.
                    The importer upserts by date.
                </p>
            </div>
        </div>
    </div>
</div>
