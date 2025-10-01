<link href="<?php echo base_url('assets/css/ai-setting.css'); ?>" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


<style>
#pdfContent,
#aiResponseContent {
	overflow: visible !important;
	height: auto !important;
}

.selected_items .selected_item {
	font-size: 13px;
	border: 1px solid #e2e2e2;
	background-color: #f5f5f5;
	border-radius: 6px;
	color: #657085;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 6px;
	padding: 2px 8px
}

.selected_items .remove_item {
	cursor: pointer;
	font-weight: 400;
	font-size: 16px;
}

.selected_items .remove_item:hover {
	color: red;
}

/* select2-container */

.select2-container .select2-selection--multiple .select2-selection__rendered {
	display: inline-block;
	overflow: hidden;
	padding-left: 12px;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.select2-container--default .select2-selection--multiple {
	border: 1px solid #ebebeb;
	outline: 0;
}

.select2-container--default.select2-container--focus .select2-selection--multiple {
	border: 1px solid #ebebeb;
	outline: 0;
}

.select2-selection__clear,
.select2-selection__choice {
	display: none !important;
}
</style>




<div class="ai_setting_container">
	<!-- Section Heading -->
	<div class="section_head">
		<p class="heading">AI Business Analyst (ChatGPT)</p>
		<div class="" id="downloadBtnWrap">
			<button id="downloadPdfBtn" class="btn_download">
				<svg width="16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
					<path
						d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z" />
				</svg> Download PDF</button>
		</div>
	</div>

	<div class="ai_chat_container">
		<!-- Ask AI -->
		<div class="ai_chat_wrapper">
			<form id="aiChatForm" class="form_container">
				<!-- Select Report Type -->
				<div class="input_label">
					<label class="label_name">Select Report Type</label>
					<select multiple required class="form-control" name="report_type[]" id="report_type">
					</select>
					<!-- ********** START *********** -->
					<!-- Custom dropdown -->
					<div class="dropdown_report_type hidden">
						<button type="button" class="dropdown_toggle" id="dropdownMenuButton">
							Select Option <svg width="16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 448 512">
								<path
									d="M201.4 374.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 306.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z" />
							</svg>
						</button>
						<ul class="dropdown_menu" id="dropdownReportOptions">
							<li><a href="#" data-value="purchase">Purchase</a></li>
							<li><a href="#" data-value="sale">Sale</a></li>
							<li><a href="#" data-value="stock">Stock</a></li>
							<li><a href="#" data-value="profit">Profit</a></li>
						</ul>
					</div>

					<button type="button" class="btn_add_new_type"> <svg width="16" fill="currentColor"
							xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
							<path
								d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z" />
						</svg>Add New Type</button>
				</div>

				<!-- ********** Selected items (START) *********** -->

				<div class="selected_items" id="selectedItemsContainer"></div>

				<!-- ********** Selected items (END) *********** -->

				<!-- Date Section -->
				<div class="date_section">
					<label class="label_name">Select Date</label>
					<div class="date_label" id="reportrange">
						<div class="calender_icon">
							<svg width="14" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
								<path
									d="M128 0c17.7 0 32 14.3 32 32l0 32 128 0 0-32c0-17.7 14.3-32 32-32s32 14.3 32 32l0 32 48 0c26.5 0 48 21.5 48 48l0 48L0 160l0-48C0 85.5 21.5 64 48 64l48 0 0-32c0-17.7 14.3-32 32-32zM0 192l448 0 0 272c0 26.5-21.5 48-48 48L48 512c-26.5 0-48-21.5-48-48L0 192zm64 80l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm128 0l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM64 400l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zm112 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16z" />
							</svg>
							<span class="date"></span>
						</div>
						<svg width="14" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
							<path
								d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z" />
						</svg>
					</div>
				</div>

				<!-- Commands -->
				<div class="input_label">
					<label class="label_name" for="">Your Question / Command</label>
					<textarea name="user_input" class="input_section" rows="6" placeholder="Ask something about your report..."
						required></textarea>
				</div>


				<div class="text-right">
					<button type="submit" class="btn_submit">Generate</button>
				</div>

			</form>
		</div>
		<!-- AI Chat Report  -->
		<div class="ai_chat_report">
			<div id="pdfContent">
				<div id="aiResponseBox">
					<h5 class="section_titles">AI Response:</h5>
					<div id="aiResponseContent" style="white-space: pre-wrap;"></div>
					<canvas id="summaryChart"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>


<!-- Add New Type Modal -->
<div id="addReportTypeModal" class="modal_ai">
	<div class="modal_overlay"></div>
	<div class="modal_content_ai">
		<p class="modal_heading">Add New Report Type</p>

		<input class="input_section" type="text" id="new_report_type" placeholder="Enter new type name" />

		<div class="modal_actions">
			<button id="saveReportType" class="btn_save">Save</button>
			<button id="closeModalBtn" class="btn_cancel">Cancel</button>
		</div>
	</div>
</div>
<!-- <div id="addTypeModal" class="modal_ai">
	<div class="modal_overlay"></div>
	<div class="modal_content_ai">
		<p class="modal_heading">Add New Report Type</p>

		<input class="input_section" type="text" id="newTypeInput" placeholder="Enter new type name" />

		<div class="modal_actions">
			<button id="saveTypeBtn" class="btn_save">Save</button>
			<button id="closeModalBtn" class="btn_cancel">Cancel</button>
		</div>
	</div>
</div> -->







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

	// ✅ Show selected items as removable badges
	$('#report_type').on('change', function() {
		const selected = $(this).val(); // array of selected values
		const container = $('#selectedItemsContainer');
		container.empty();

		if (selected && selected.length > 0) {
			selected.forEach(function(item) {
				container.append(`
					<span class="selected_item" data-value="${item}">
						${item} 
						<span class="remove_item">&times;</span>
					</span>
				`);
			});
		}
	});

	// ✅ Click handler for close/delete
	$('#selectedItemsContainer').on('click', '.remove_item', function() {
		const badge = $(this).closest('.selected_item');
		const value = badge.data('value').toString();

		// Remove from <select>
		const select = $('#report_type');
		const selected = select.val() || [];
		const updated = selected.filter(v => v !== value);

		select.val(updated).trigger('change'); // updates both <select> and badges
	});

});
</script>



<!-- Multi data pic -->

<script type="text/javascript">
$(function() {

	var start = moment().subtract(29, 'days');
	var end = moment();

	function cb(start, end) {
		$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	}

	$('#reportrange').daterangepicker({
		startDate: start,
		endDate: end,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'Last 7 Days': [moment().subtract(6, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
				'month').endOf(
				'month')]
		}
	}, cb);

	cb(start, end);

});
</script>

<script>
const addTypeBtn = document.querySelector('.btn_add_new_type');
const modal = document.getElementById('addReportTypeModal');
const closeModalBtn = document.getElementById('closeModalBtn');
// const saveTypeBtn = document.getElementById('saveTypeBtn');
// const newTypeInput = document.getElementById('newTypeInput');
const reportTypeSelect = document.getElementById('report_type');

addTypeBtn.addEventListener('click', () => {
	modal.classList.add('show');
	// newTypeInput.value = '';
	// newTypeInput.focus();
});

closeModalBtn.addEventListener('click', () => {
	modal.classList.remove('show');
});

modal.addEventListener('click', (e) => {
	if (e.target.classList.contains('modal_overlay')) {
		modal.classList.remove('show');
	}
});

/* saveTypeBtn.addEventListener('click', () => {
	const newType = newTypeInput.value.trim();
	if (newType) {
		const option = document.createElement('option');
		option.value = newType.toLowerCase().replace(/\s+/g, '');
		option.textContent = newType;
		option.selected = true;
		reportTypeSelect.appendChild(option);
		modal.classList.remove('show');
	} else {
		alert('Please enter a type name.');
	}
}); */
</script>