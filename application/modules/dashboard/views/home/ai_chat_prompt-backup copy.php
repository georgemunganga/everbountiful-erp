<style>
#pdfContent,
#aiResponseContent {
	overflow: visible !important;
	height: auto !important;
}
</style>


<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-bd lobidrag">
			<div class="panel-heading">
				<div class="panel-title">
					<h4>AI Business Analyst (ChatGPT)</h4>
				</div>
			</div>

			<!-- HTML Modal -->
			<div class="modal fade" id="addReportTypeModal" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title">Add New Report Type</h5>
							<button type="button" class="close" data-dismiss="modal">&times;</button>
						</div>
						<div class="modal-body">
							<input type="text" id="new_report_type" class="form-control" placeholder="Enter new report type">
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-primary" id="saveReportType">Save</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>

			<div class="panel-body">
				<form id="aiChatForm">

					<div class="form-group row">
						<label class="col-sm-2 col-form-label">Select Report Type</label>
						<div class="col-sm-6">
							<!-- Dropdown with Add New -->
							<select class="form-control" name="report_type[]" id="report_type" multiple required>
							</select>
							<small><i>Use Ctrl (Windows) or ⌘ (Mac) to select multiple</i></small>
							<button type="button" class="btn btn-link" data-toggle="modal" data-target="#addReportTypeModal">+ Add New
								Type</button>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label">Date From</label>
						<div class="col-sm-4">
							<input type="date" class="form-control" name="date_from" value="<?= date('Y-m-01') ?>">
						</div>
						<label class="col-sm-2 col-form-label">Date To</label>
						<div class="col-sm-4">
							<input type="date" class="form-control" name="date_to" value="<?= date('Y-m-d') ?>">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label">Your Question / Command</label>
						<div class="col-sm-10">
							<textarea name="user_input" class="form-control" rows="4" placeholder="Ask something about your report..."
								required></textarea>
						</div>
					</div>

					<div class="text-right">
						<button type="submit" class="btn btn-success">Ask AI</button>
					</div>
				</form>
				
				<hr>
				<div class="text-right mt-3" id="downloadBtnWrap" style="display:none;">
					<button id="downloadPdfBtn" class="btn btn-primary">Download PDF</button>
				</div>
				<div id="pdfContent">
					<div id="aiResponseBox" style="background: #f3f3f3; padding: 15px; border-radius: 8px; display:none">
						<h5>AI Response:</h5>
						<div id="aiResponseContent" style="white-space: pre-wrap;"></div>
						<canvas id="summaryChart"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
const labels = <?php echo json_encode($chart_data['labels']); ?>;
const salesData = <?php echo json_encode($chart_data['sales']); ?>;
const purchaseData = <?php echo json_encode($chart_data['purchases']); ?>;
</script>


<script>
$('#downloadPdfBtn').on('click', function() {
	var element = document.getElementById('pdfContent');

	var opt = {
		margin: 0.5,
		filename: 'AI_Business_Analysis_' + new Date().toISOString().slice(0, 10) + '.pdf',
		image: {
			type: 'jpeg',
			quality: 0.98
		},
		html2canvas: {
			scale: 2,
			useCORS: true,
			scrollX: 0,
			scrollY: 0
		},
		jsPDF: {
			unit: 'in',
			format: 'a4',
			orientation: 'portrait'
		},
		pagebreak: {
			mode: ['css', 'legacy']
		} // 👈 এই লাইনটি গুরুত্বপূর্ণ
	};

	html2pdf().set(opt).from(element).save();
});

html2pdf().set({
	pagebreak: {
		mode: 'avoid-all',
		before: '#summaryChart'
	}, // Chart এর আগে break নেবে
});
</script>


<script>
$('#aiChatForm').on('submit', function(e) {
	e.preventDefault();

	$.ajax({
		url: '<?= base_url('dashboard/AI_Controller/bdtask_get_ai_analysis') ?>',
		method: 'POST',
		data: $(this).serialize(),
		dataType: 'json',
		beforeSend: function() {
			$('#aiResponseBox').show();
			$('#aiResponseContent').html('<i>Loading AI response...</i>');
		},
		success: function(res) {
			if (res.status === 'success') {
				$('#aiResponseContent').html(res.response);
				$('#downloadBtnWrap').show();
				// Draw chart dynamically
				if (res.chart_data.labels.length > 0) {
					const ctx = document.getElementById('summaryChart').getContext('2d');
					if (window.summaryChart instanceof Chart) {
						window.summaryChart.destroy();
					}
					window.summaryChart = new Chart(ctx, {
						type: 'line',
						data: {
							labels: res.chart_data.labels,
							datasets: [{
									label: 'Sales',
									data: res.chart_data.sales,
									borderColor: 'green',
									backgroundColor: 'rgba(0,128,0,0.1)',
									fill: true,
									tension: 0.3
								},
								{
									label: 'Purchases',
									data: res.chart_data.purchases,
									borderColor: 'orange',
									backgroundColor: 'rgba(255,165,0,0.1)',
									fill: true,
									tension: 0.3
								},
								{
									label: 'Stock',
									data: res.chart_data.stock,
									borderColor: 'blue',
									backgroundColor: 'rgba(2, 53, 223, 0.87)',
									fill: true,
									tension: 0.3
								},
								{
									label: 'Sales Return',
									data: chart_data.sales_return,
									borderColor: 'orange',
									backgroundColor: 'rgba(255,165,0,0.4)',
								},
								{
									label: 'Supplier Return',
									data: chart_data.supplier_return,
									borderColor: 'purple',
									backgroundColor: 'rgba(128,0,128,0.3)',
								}
							]
						},
						options: {
							responsive: true,
							plugins: {
								title: {
									display: true,
									text: 'Sales vs Purchase vs Stock Summary'
								}
							}
						}
					});
				}
			} else {
				$('#aiResponseContent').html('<span class="text-danger">Error: ' + res.message +
					'</span>');
				$('#downloadBtnWrap').hide();
			}
		},
		error: function() {
			$('#aiResponseContent').html(
				'<span class="text-danger">Something went wrong.</span>');
		}
	});
});
</script>



<script>
$('#aiChatForm').on('submit', function(e) {
	e.preventDefault();

	$.ajax({
		url: '<?= base_url('dashboard/AI_Controller/bdtask_get_ai_analysis') ?>',
		method: 'POST',
		data: $(this).serialize(),
		dataType: 'json',
		beforeSend: function() {
			$('#aiResponseBox').show();
			$('#aiResponseContent').html('<i>Loading AI response...</i>');
		},
		success: function(res) {
			if (res.status === 'success') {
				$('#aiResponseContent').html(res.response);
			} else {
				$('#aiResponseContent').html('<span class="text-danger">Error: ' + res.message +
					'</span>');
			}
		},
		error: function() {
			$('#aiResponseContent').html(
				'<span class="text-danger">Something went wrong.</span>');
		}
	});
});
</script>

<!-- AI Report Type -->

<script>
function loadReportTypes() {
	$.getJSON('<?= base_url('dashboard/AI_Controller/get_report_types') ?>', function(data) {
		$('#report_type').empty();
		$.each(data, function(i, item) {
			$('#report_type').append(`<option value="${item.name}">${item.name}</option>`);
		});
	});
}

$(document).ready(function() {
	loadReportTypes();

	$('#saveReportType').click(function() {
		const name = $('#new_report_type').val().trim();
		if (name) {
			$.post('<?= base_url('dashboard/AI_Controller/add_report_type') ?>', {
				name: name
			}, function(response) {
				const res = JSON.parse(response);
				if (res.status === 'success') {
					alert("Save Successfully!");
					$('#new_report_type').val('');
					$('#addReportTypeModal').modal('hide');
					loadReportTypes();
				} else {
					alert(res.message || 'Failed to add type');
				}
			});
		}
	});
});
</script>