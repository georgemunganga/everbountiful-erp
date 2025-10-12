<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <form action="<?php echo base_url('production-percentage'); ?>" method="get" class="form-horizontal">
                    <div class="form-group row">
                        <label for="product_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('product') ?: 'Product'); ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="product_id" id="product_id" class="form-control" required>
                                <option value=""><?php echo html_escape(display('select_one') ?: 'Select one'); ?></option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['product_id']; ?>" <?php echo ((string) $selected_product_id === (string) $product['product_id']) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($product['product_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="livestock_id" class="col-sm-3 col-form-label"><?php echo html_escape(display('livestock_asset') ?: 'Livestock Asset'); ?></label>
                        <div class="col-sm-6">
                            <select name="livestock_id" id="livestock_id" class="form-control">
                                <option value=""><?php echo html_escape(display('select_livestock_asset') ?: 'Select Livestock Asset'); ?></option>
                                <?php if (!empty($livestock_assets)): ?>
                                    <?php foreach ($livestock_assets as $asset): ?>
                                        <?php
                                            $assetId = (int) ($asset['id'] ?? 0);
                                            $assetQty = number_format((float) ($asset['livestock_total_qty'] ?? 0), 2);
                                            $assetName = trim((string) ($asset['name'] ?? ''));
                                            if ($assetName === '') {
                                                $labelBase = display('livestock') ?: 'Livestock';
                                                $assetName = sprintf('%s #%d', $labelBase, $assetId);
                                            }
                                        ?>
                                        <option value="<?php echo $assetId; ?>" data-total="<?php echo html_escape((string) ($asset['livestock_total_qty'] ?? '')); ?>" <?php echo ((string) $selected_livestock_id === (string) $assetId) ? 'selected' : ''; ?>>
                                            <?php echo html_escape(sprintf('%s (%s)', $assetName, $assetQty)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value=""><?php echo html_escape(display('no_livestock_available') ?: 'No livestock records available.'); ?></option>
                                <?php endif; ?>
                            </select>
                            <span class="help-block small text-muted"><?php echo html_escape(display('use_livestock_asset_help') ?: 'Choosing a livestock record will auto-fill the biological asset count.'); ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="asset_count" class="col-sm-3 col-form-label"><?php echo html_escape(display('number_of_assets') ?: 'No. of Assets'); ?></label>
                        <div class="col-sm-6">
                            <input type="number" step="0.01" min="0" name="asset_count" id="asset_count" class="form-control" value="<?php echo html_escape($asset_count_input); ?>" placeholder="<?php echo html_escape(display('enter_asset_count') ?: 'Enter number of producing units'); ?>">
                            <span class="help-block small text-muted"><?php echo html_escape(display('asset_count_help') ?: 'Optional: number of animals or plants responsible for the output.'); ?></span>
                            <span class="help-block small text-muted"><?php echo html_escape(display('asset_count_override_help') ?: 'Override the auto-filled value from the selected livestock if needed.'); ?></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="interval" class="col-sm-3 col-form-label"><?php echo html_escape(display('interval') ?: 'Interval'); ?></label>
                        <div class="col-sm-6">
                            <select name="interval" id="interval" class="form-control">
                                <option value="daily" <?php echo ($selected_interval === 'daily') ? 'selected' : ''; ?>><?php echo html_escape(display('daily') ?: 'Daily'); ?></option>
                                <option value="weekly" <?php echo ($selected_interval === 'weekly') ? 'selected' : ''; ?>><?php echo html_escape(display('weekly') ?: 'Weekly'); ?></option>
                                <option value="monthly" <?php echo ($selected_interval === 'monthly') ? 'selected' : ''; ?>><?php echo html_escape(display('monthly') ?: 'Monthly'); ?></option>
                                <option value="yearly" <?php echo ($selected_interval === 'yearly') ? 'selected' : ''; ?>><?php echo html_escape(display('yearly') ?: 'Yearly'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="start_date" class="col-sm-3 col-form-label"><?php echo html_escape(display('from') ?: 'From'); ?></label>
                        <div class="col-sm-3">
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo html_escape($filters['start_date']); ?>">
                        </div>
                        <label for="end_date" class="col-sm-1 col-form-label text-right"><?php echo html_escape(display('to') ?: 'To'); ?></label>
                        <div class="col-sm-3">
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo html_escape($filters['end_date']); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-primary"><?php echo html_escape(display('filter') ?: 'Filter'); ?></button>
                            <?php if (!empty($selected_product_id)): ?>
                                <a href="<?php echo base_url('production-percentage'); ?>" class="btn btn-default"><?php echo html_escape(display('reset') ?: 'Reset'); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($selected_product_id) && !empty($summary)): ?>
    <?php
        $totalOutput    = (float) ($summary['total_output'] ?? 0);
        $totalMortality = (float) ($summary['total_mortality'] ?? 0);
        $totalDamaged   = (float) ($summary['total_damaged'] ?? 0);
        $totalExtras    = (float) ($summary['total_extras'] ?? 0);
        $batches        = (int) ($summary['batch_count'] ?? 0);
        $usableOutput   = max(0.0, $totalOutput - $totalMortality - $totalDamaged);

        $percent = function ($portion, $total) {
            return ($total > 0) ? ($portion / $total) * 100.0 : 0.0;
        };

        $mortalityPct = $percent($totalMortality, $totalOutput);
        $damagedPct   = $percent($totalDamaged, $totalOutput);
        $extrasPct    = $percent($totalExtras, $totalOutput);
        $yieldPct     = $percent($usableOutput, $totalOutput);
        $productionPct = $production_percent ?? null;
        $assetsEntered = $asset_count ?? null;
        $selectedLivestockName = '';
        if (!empty($selected_livestock)) {
            $selectedLivestockName = trim((string) ($selected_livestock['name'] ?? ''));
            if ($selectedLivestockName === '') {
                $labelBase = display('livestock') ?: 'Livestock';
                $selectedLivestockName = sprintf('%s #%d', $labelBase, (int) ($selected_livestock['id'] ?? 0));
            }
        }
    ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4><?php echo html_escape(display('component_meaning') ?: 'Meaning of Each Component'); ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <p class="text-muted">
                        <?php echo html_escape(ucfirst($selected_interval)); ?> |
                        <?php echo html_escape($filters['start_date']); ?> &rarr; <?php echo html_escape($filters['end_date']); ?>
                        <?php if (!empty($selected_product_name)): ?>
                            &middot; <?php echo html_escape($selected_product_name); ?>
                        <?php endif; ?>
                        <?php if ($selectedLivestockName !== ''): ?>
                            &middot; <?php echo html_escape(display('livestock_asset') ?: 'Livestock Asset'); ?>: <?php echo html_escape($selectedLivestockName); ?>
                        <?php endif; ?>
                        <?php if ($assetsEntered): ?>
                            &middot; <?php echo html_escape(display('biological_assets_present') ?: 'Biological Assets'); ?>: <?php echo number_format($assetsEntered, 2); ?>
                        <?php endif; ?>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th><?php echo html_escape(display('term') ?: 'Term'); ?></th>
                                    <th><?php echo html_escape(display('description') ?: 'Description'); ?></th>
                                    <th><?php echo html_escape(display('example_poultry') ?: 'Example (Poultry)'); ?></th>
                                    <th><?php echo html_escape(display('example_crops') ?: 'Example (Crops)'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo html_escape(display('products_produced') ?: 'Products Produced'); ?></td>
                                    <td><?php echo html_escape(display('products_produced_desc') ?: 'The measurable output (e.g. eggs, milk, fruit, harvested crops).'); ?></td>
                                    <td>855 eggs laid</td>
                                    <td>1,200 maize cobs harvested</td>
                                </tr>
                                <tr>
                                    <td><?php echo html_escape(display('biological_assets_present') ?: 'Biological Assets Present'); ?></td>
                                    <td><?php echo html_escape(display('biological_assets_present_desc') ?: 'The number of living organisms or producing units responsible for the output.'); ?></td>
                                    <td>950 hens</td>
                                    <td>1,000 maize plants</td>
                                </tr>
                                <tr>
                                    <td><?php echo html_escape(display('production_percentage') ?: 'Production %'); ?></td>
                                    <td><?php echo html_escape(display('production_percentage_desc') ?: 'Yield relative to the number of productive units.'); ?></td>
                                    <td>90% laying rate</td>
                                    <td>120% crop yield rate</td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="help-block"><?php echo html_escape(display('production_percentage_formula') ?: '(Units produced ÷ Number of assets) × 100'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4><?php echo html_escape(display('examples') ?: 'Examples'); ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <ul>
                        <li><strong><?php echo html_escape(display('example_poultry') ?: 'Poultry Example'); ?>:</strong> (855 eggs &divide; 950 hens) &times; 100 = 90%</li>
                        <li><strong><?php echo html_escape(display('example_crops') ?: 'Crop Example'); ?>:</strong> (1,200 maize cobs &divide; 1,000 plants) &times; 100 = 120%</li>
                        <li><strong><?php echo html_escape(display('example_dairy') ?: 'Dairy Example'); ?>:</strong> 50 cows producing 1,450 liters can be benchmarked per cow/day or per lactation cycle.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>
                            <?php echo html_escape(display('summary') ?: 'Summary'); ?>
                            <?php if (!empty($selected_product_name)): ?>
                                <small class="text-muted"><?php echo html_escape($selected_product_name); ?></small>
                            <?php endif; ?>
                        </h4>
                    </div>
                </div>
                <div class="panel-body">
                    <p class="text-muted">
                        <?php echo html_escape(ucfirst($selected_interval)); ?> |
                        <?php echo html_escape($filters['start_date']); ?> &rarr; <?php echo html_escape($filters['end_date']); ?>
                        <?php if (!empty($selected_product_name)): ?>
                            &middot; <?php echo html_escape($selected_product_name); ?>
                        <?php endif; ?>
                        <?php if (!empty($selectedLivestockName)): ?>
                            &middot; <?php echo html_escape(display('livestock_asset') ?: 'Livestock Asset'); ?>: <?php echo html_escape($selectedLivestockName); ?>
                        <?php endif; ?>
                        <?php if ($assetsEntered): ?>
                            &middot; <?php echo html_escape(display('biological_assets_present') ?: 'Biological Assets'); ?>: <?php echo number_format($assetsEntered, 2); ?>
                        <?php endif; ?>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th><?php echo html_escape(display('metric') ?: 'Metric'); ?></th>
                                    <th class="text-right"><?php echo html_escape(display('quantity') ?: 'Quantity'); ?></th>
                                    <th class="text-right"><?php echo html_escape(display('percentage') ?: 'Percentage'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo html_escape(display('produced_total_qty') ?: 'Produced Total'); ?></td>
                                    <td class="text-right"><?php echo number_format($totalOutput, 2); ?></td>
                                    <td class="text-right">100%</td>
                                </tr>
                                <?php if ($assetsEntered): ?>
                                    <tr>
                                        <td><?php echo html_escape(display('production_rate') ?: 'Production %'); ?></td>
                                        <td class="text-right"><?php echo ($production_percent !== null) ? number_format($production_percent, 2) . '%' : '&mdash;'; ?></td>
                                        <td class="text-right"><?php echo ($output_per_asset !== null) ? number_format($output_per_asset, 2) : '&mdash;'; ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td><?php echo html_escape(display('mortality') ?: 'Mortality'); ?></td>
                                    <td class="text-right"><?php echo number_format($totalMortality, 2); ?></td>
                                    <td class="text-right"><?php echo number_format($mortalityPct, 2); ?>%</td>
                                </tr>
                                <tr>
                                    <td><?php echo html_escape(display('produced_damaged_qty') ?: 'Damaged'); ?></td>
                                    <td class="text-right"><?php echo number_format($totalDamaged, 2); ?></td>
                                    <td class="text-right"><?php echo number_format($damagedPct, 2); ?>%</td>
                                </tr>
                                <tr>
                                    <td><?php echo html_escape(display('extras') ?: 'Extras'); ?></td>
                                    <td class="text-right"><?php echo number_format($totalExtras, 2); ?></td>
                                    <td class="text-right"><?php echo number_format($extrasPct, 2); ?>%</td>
                                </tr>
                                <tr>
                                    <td><?php echo html_escape(display('usable_output') ?: 'Usable Output'); ?></td>
                                    <td class="text-right"><?php echo number_format($usableOutput, 2); ?></td>
                                    <td class="text-right"><?php echo number_format($yieldPct, 2); ?>%</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><?php echo html_escape(display('batches') ?: 'Batches'); ?></td>
                                    <td class="text-right" colspan="2"><?php echo number_format($batches); ?></td>
                                </tr>
                                <?php if ($assetsEntered): ?>
                                    <tr>
                                        <td><?php echo html_escape(display('biological_assets_present') ?: 'Biological Assets'); ?></td>
                                        <td class="text-right" colspan="2"><?php echo number_format($assetsEntered, 2); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4><?php echo html_escape(display('daily_breakdown') ?: 'Daily Breakdown'); ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <?php if (!empty($series)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th><?php echo html_escape(display('period') ?: 'Period'); ?></th>
                                        <th class="text-right"><?php echo html_escape(display('produced_total_qty') ?: 'Produced Total'); ?></th>
                                        <th class="text-right"><?php echo html_escape(display('mortality') ?: 'Mortality'); ?></th>
                                        <th class="text-right"><?php echo html_escape(display('produced_damaged_qty') ?: 'Damaged'); ?></th>
                                        <th class="text-right"><?php echo html_escape(display('extras') ?: 'Extras'); ?></th>
                                        <th class="text-right"><?php echo html_escape(display('usable_output') ?: 'Usable Output'); ?></th>
                                        <th class="text-right"><?php echo html_escape(display('yield_percentage') ?: 'Yield %'); ?></th>
                                        <?php if ($assetsEntered): ?>
                                            <th class="text-right"><?php echo html_escape(display('production_percentage') ?: 'Production %'); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($series as $row): ?>
                                        <?php
                                            $periodOutput   = (float) ($row['total_output'] ?? 0);
                                            $periodMort     = (float) ($row['total_mortality'] ?? 0);
                                            $periodDamaged  = (float) ($row['total_damaged'] ?? 0);
                                            $periodExtras   = (float) ($row['total_extras'] ?? 0);
                                            $periodUsable   = max(0.0, $periodOutput - $periodMort - $periodDamaged);
                                            $periodYieldPct = ($periodOutput > 0) ? ($periodUsable / $periodOutput) * 100.0 : 0.0;
                                            $periodProductionPct = $row['production_percent'] ?? null;
                                        ?>
                                        <tr>
                                            <td><?php echo html_escape($row['period_label']); ?></td>
                                            <td class="text-right"><?php echo number_format($periodOutput, 2); ?></td>
                                            <td class="text-right"><?php echo number_format($periodMort, 2); ?></td>
                                            <td class="text-right"><?php echo number_format($periodDamaged, 2); ?></td>
                                            <td class="text-right"><?php echo number_format($periodExtras, 2); ?></td>
                                            <td class="text-right"><?php echo number_format($periodUsable, 2); ?></td>
                                            <td class="text-right"><?php echo number_format($periodYieldPct, 2); ?>%</td>
                                            <?php if ($assetsEntered): ?>
                                                <td class="text-right">
                                                    <?php echo ($periodProductionPct !== null) ? number_format($periodProductionPct, 2) . '%' : '—'; ?>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            <?php echo html_escape(display('no_data_found') ?: 'No data found for the selected filters.'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4><?php echo html_escape(display('dynamic_production_template') ?: 'Dynamic Production Report Template'); ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th><?php echo html_escape(display('date') ?: 'Date'); ?></th>
                                    <th><?php echo html_escape(display('biological_asset_type') ?: 'Biological Asset Type'); ?></th>
                                    <th class="text-right"><?php echo html_escape(display('number_of_assets') ?: 'No. of Assets'); ?></th>
                                    <th class="text-right"><?php echo html_escape(display('units_produced') ?: 'Units Produced'); ?></th>
                                    <th class="text-right"><?php echo html_escape(display('production_percentage') ?: 'Production %'); ?></th>
                                    <th class="text-right"><?php echo html_escape(display('feed_inputs_used') ?: 'Feed/Inputs Used'); ?></th>
                                    <th><?php echo html_escape(display('notes') ?: 'Notes'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($series)): ?>
                                    <?php foreach ($series as $row): ?>
                                        <tr>
                                            <td><?php echo html_escape($row['period_label']); ?></td>
                                            <td><?php echo html_escape($selected_product_name !== '' ? $selected_product_name : (display('product') ?: 'Product')); ?></td>
                                            <td class="text-right"><?php echo $assetsEntered ? number_format($assetsEntered, 2) : '&mdash;'; ?></td>
                                            <td class="text-right"><?php echo number_format($row['total_output'], 2); ?></td>
                                            <td class="text-right">
                                                <?php echo ($row['production_percent'] !== null) ? number_format($row['production_percent'], 2) . '%' : '&mdash;'; ?>
                                            </td>
                                            <td class="text-right">&mdash;</td>
                                            <td>&mdash;</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted"><?php echo html_escape(display('no_data_found') ?: 'No data found for the selected filters.'); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4><?php echo html_escape(display('interpretation') ?: 'Interpretation'); ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <ul>
                        <li><?php echo html_escape(display('interpretation_above_100') ?: 'Above 100% → Each biological unit produced more than expected (excellent efficiency).'); ?></li>
                        <li><?php echo html_escape(display('interpretation_mid') ?: 'Around 80–100% → Normal, healthy production.'); ?></li>
                        <li><?php echo html_escape(display('interpretation_low') ?: 'Below 70% → Potential stress, disease, or input constraints. Investigate immediately.'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4><?php echo html_escape(display('optional_metrics') ?: 'Optional Metrics'); ?></h4>
                    </div>
                </div>
                <div class="panel-body">
                    <ul>
                        <li><?php echo html_escape(display('yield_per_area') ?: 'Yield per unit area (crops): Total yield ÷ Area planted.'); ?></li>
                        <li><?php echo html_escape(display('conversion_ratios') ?: 'Conversion ratios (livestock): Feed input ÷ Weight gain.'); ?></li>
                        <li><?php echo html_escape(display('economic_efficiency') ?: 'Economic efficiency: (Value of output ÷ Cost of inputs) × 100.'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php elseif (!empty($selected_product_id)): ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-warning">
                <?php echo html_escape(display('no_data_found') ?: 'No data found for the selected filters.'); ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-info">
                <?php echo html_escape(display('select_product_instruction') ?: 'Select a product to view production percentage details.'); ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
(function () {
    var livestockSelect = document.getElementById('livestock_id');
    var assetCountInput = document.getElementById('asset_count');
    if (!livestockSelect || !assetCountInput) {
        return;
    }

    function applyLivestockTotal() {
        var option = livestockSelect.options[livestockSelect.selectedIndex];
        if (!option) {
            return;
        }
        var totalRaw = option.getAttribute('data-total');
        if (totalRaw === null || totalRaw === '') {
            return;
        }
        var parsed = parseFloat(totalRaw);
        if (isNaN(parsed)) {
            return;
        }
        assetCountInput.value = totalRaw;
    }

    livestockSelect.addEventListener('change', applyLivestockTotal);

    if (!assetCountInput.value) {
        applyLivestockTotal();
    }
})();
</script>
