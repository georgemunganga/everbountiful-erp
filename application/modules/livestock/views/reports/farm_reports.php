<?php
$json_options   = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
$summary_json   = json_encode($summary, $json_options);
$timeseries_json = json_encode($timeseries, $json_options);
$filters_json   = json_encode($filters, $json_options);

$summary_json = ($summary_json !== false) ? $summary_json : '{}';
$timeseries_json = ($timeseries_json !== false) ? $timeseries_json : '[]';
$filters_json = ($filters_json !== false) ? $filters_json : '{}';
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo html_escape($title); ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <form id="farm-report-filters" class="form-inline mb-3">
                    <div class="form-group mr-3">
                        <label class="mr-2" for="start_date"><?php echo html_escape(display('from') ?: 'From'); ?></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo html_escape($filters['start_date']); ?>">
                    </div>
                    <div class="form-group mr-3">
                        <label class="mr-2" for="end_date"><?php echo html_escape(display('to') ?: 'To'); ?></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo html_escape($filters['end_date']); ?>">
                    </div>
                    <div class="form-group mr-3">
                        <label class="mr-2" for="interval"><?php echo html_escape(display('interval') ?: 'Interval'); ?></label>
                        <select name="interval" id="interval" class="form-control">
                            <option value="daily" <?php echo $filters['interval'] === 'daily' ? 'selected' : ''; ?>><?php echo html_escape(display('daily') ?: 'Daily'); ?></option>
                            <option value="weekly" <?php echo $filters['interval'] === 'weekly' ? 'selected' : ''; ?>><?php echo html_escape(display('weekly') ?: 'Weekly'); ?></option>
                            <option value="monthly" <?php echo $filters['interval'] === 'monthly' ? 'selected' : ''; ?>><?php echo html_escape(display('monthly') ?: 'Monthly'); ?></option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success"><?php echo html_escape(display('filter') ?: 'Filter'); ?></button>
                </form>

                <div class="row text-center" id="farm-report-summary">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="well">
                            <h5><?php echo html_escape(display('livestock') ?: 'Livestock'); ?> (<?php echo html_escape('Chicken'); ?>)</h5>
                            <strong id="summary-chicken"><?php echo number_format((float) $summary['chicken_total'], 2); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="well">
                            <h5><?php echo html_escape(display('production') ?: 'Production'); ?></h5>
                            <strong id="summary-production"><?php echo number_format((float) $summary['production_total'], 2); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="well">
                            <h5><?php echo html_escape(display('mortality') ?: 'Mortality'); ?></h5>
                            <strong id="summary-mortality"><?php echo number_format((float) $summary['production_mortality'], 2); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="well">
                            <h5><?php echo html_escape(display('total_wasted') ?: 'Wasted / Damaged'); ?></h5>
                            <strong id="summary-damaged"><?php echo number_format((float) $summary['production_damaged'], 2); ?></strong>
                        </div>
                    </div>
                </div>

                <div id="farmReportChart" style="min-height:340px;"></div>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th><?php echo html_escape(display('period') ?: 'Period'); ?></th>
                                <th><?php echo html_escape(display('livestock') ?: 'Livestock'); ?> (<?php echo html_escape('Chicken'); ?>)</th>
                                <th><?php echo html_escape(display('production') ?: 'Production'); ?></th>
                                <th><?php echo html_escape(display('mortality') ?: 'Mortality'); ?></th>
                                <th><?php echo html_escape(display('total_wasted') ?: 'Damaged'); ?></th>
                                <th><?php echo html_escape(display('extras') ?: 'Extras'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="farm-report-table-body">
                            <?php if (!empty($timeseries)): ?>
                                <?php foreach ($timeseries as $row): ?>
                                    <tr>
                                        <td><?php echo html_escape($row['period_label']); ?></td>
                                        <td><?php echo number_format((float) $row['chicken_total'], 2); ?></td>
                                        <td><?php echo number_format((float) $row['production_total'], 2); ?></td>
                                        <td><?php echo number_format((float) $row['production_mortality'], 2); ?></td>
                                        <td><?php echo number_format((float) $row['production_damaged'], 2); ?></td>
                                        <td><?php echo number_format((float) $row['production_extras'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted"><?php echo html_escape(display('no_data_found') ?: 'No data found'); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function() {
    const summaryEl = {
        chicken: document.getElementById('summary-chicken'),
        production: document.getElementById('summary-production'),
        mortality: document.getElementById('summary-mortality'),
        damaged: document.getElementById('summary-damaged')
    };
    const tableBody = document.getElementById('farm-report-table-body');
    const form = document.getElementById('farm-report-filters');
    const endpoint = "<?php echo base_url('farm-reports/data'); ?>";

    function formatNumber(value) {
        const number = parseFloat(value);
        if (isNaN(number)) {
            return '0.00';
        }
        return number.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function renderSummary(summary) {
        summaryEl.chicken.textContent = formatNumber(summary.chicken_total);
        summaryEl.production.textContent = formatNumber(summary.production_total);
        summaryEl.mortality.textContent = formatNumber(summary.production_mortality);
        summaryEl.damaged.textContent = formatNumber(summary.production_damaged);
    }

    function renderTable(rows) {
        tableBody.innerHTML = '';
        if (!rows || rows.length === 0) {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 6;
            td.className = 'text-center text-muted';
            td.textContent = "<?php echo html_escape(display('no_data_found') ?: 'No data found'); ?>";
            tr.appendChild(td);
            tableBody.appendChild(tr);
            return;
        }
        rows.forEach(function(row) {
            const tr = document.createElement('tr');
            tr.innerHTML = [
                '<td>' + row.period_label + '</td>',
                '<td>' + formatNumber(row.chicken_total) + '</td>',
                '<td>' + formatNumber(row.production_total) + '</td>',
                '<td>' + formatNumber(row.production_mortality) + '</td>',
                '<td>' + formatNumber(row.production_damaged) + '</td>',
                '<td>' + formatNumber(row.production_extras) + '</td>'
            ].join('');
            tableBody.appendChild(tr);
        });
    }

    function extractChartSeries(rows) {
        const categories = [];
        const chickenSeries = [];
        const productionSeries = [];

        rows.forEach(function(row) {
            categories.push(row.period_label);
            chickenSeries.push(parseFloat(row.chicken_total) || 0);
            productionSeries.push(parseFloat(row.production_total) || 0);
        });

        return {
            categories: categories,
            series: [
                { name: "<?php echo html_escape(display('livestock') ?: 'Livestock'); ?>", data: chickenSeries },
                { name: "<?php echo html_escape(display('production') ?: 'Production'); ?>", data: productionSeries }
            ]
        };
    }

    const initialData = <?php echo $timeseries_json; ?>;
    const chartConfig = {
        chart: {
            type: 'bar',
            height: 360,
            toolbar: { show: false }
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '45%'
            }
        },
        xaxis: { categories: [] },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return formatNumber(val);
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return formatNumber(val);
                }
            }
        },
        series: []
    };

    const chart = new ApexCharts(document.querySelector("#farmReportChart"), chartConfig);
    chart.render();

    function updateChart(rows) {
        const extracted = extractChartSeries(rows || []);
        chart.updateOptions({ xaxis: { categories: extracted.categories } });
        chart.updateSeries(extracted.series);
    }

    renderSummary(<?php echo $summary_json; ?>);
    renderTable(initialData);
    updateChart(initialData);

    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(form);
        fetch(endpoint, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(function(payload) {
                if (!payload.success) {
                    throw new Error('Unable to load report data');
                }
                renderSummary(payload.summary || {});
                renderTable(payload.timeseries || []);
                updateChart(payload.timeseries || []);
            })
            .catch(function(error) {
                console.error(error);
                alert('<?php echo html_escape(display('please_try_again') ?: 'Please try again.'); ?>');
            });
    });
})();
</script>
