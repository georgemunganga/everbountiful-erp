<link href="<?php echo base_url('assets/css/ai-setting.css'); ?>" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


<style>
	.btn_toggle {
		background-color: #28a745;
		border: none;
		color: white;
		padding: 6px 12px;
		margin-right: 8px;
		border-radius: 4px;
		cursor: pointer;
		font-size: 14px;
	}

	.btn_toggle:hover {
		background-color: #218838;
	}

	.btn_copy {
		background-color: #f0ad4e;
		border: none;
		color: white;
		padding: 5px 10px;
		border-radius: 4px;
		cursor: pointer;
		font-size: 13px;
	}

	.btn_copy:hover {
		background-color: #ec971f;
	}

	.instructions-box {
		border: 2px solid #28a745;
		border-radius: 8px;
		padding: 10px 15px;
		background-color: #f6fff8;
		max-width: 530px;
		margin-top: 20px;
		font-family: Arial, sans-serif;
	}

	.instructions-box h4 {
		color: #28a745;
		font-size: 20px;
		font-weight: bold;
		border-bottom: 2px solid #28a745;
		padding-bottom: 5px;
		margin-bottom: 12px;
	}

	.instructions-box ul {
		list-style-type: none;
		padding-left: 0;
		margin: 0;
	}

	.instructions-box li {
		margin-bottom: 8px;
	}

	.instructions-box li::before {
		content: "✔️ ";
		color: #28a745;
	}

	@media print {
		#pdfContent {
			max-width: 700px;
			margin: auto;
			padding: 20px;
			background: white;
			overflow-wrap: break-word;
		}

		#summaryChart {
			width: 600px !important;
			height: 400px !important;
			display: block;
			margin: 20px auto;
		}
	}

	.print-pdf {
		max-width: 700px;
		margin: auto;
		padding: 20px;
		background: white;
	}

	.print-pdf #summaryChart {
		width: 600px !important;
		height: 400px !important;
	}


	#pdfContent,
	#aiResponseContent {
		overflow: visible !important;
		height: auto !important;
	}

	.insights-text {
		padding: 10px;
		background-color: #f9f9f9;
		border-left: 4px solid #28a745;
		font-family: Arial, sans-serif;
		white-space: normal;
		line-height: 1.6;
	}


	#reportTypeList .report_type_item:hover {
		background: #f9f9f9;
	}

	#reportTypeList .btn_delete_type:hover {
		color: darkred;
	}

	.table-bordered {
		border-collapse: collapse;
		width: 100%;
	}

	.table-bordered th,
	.table-bordered td {
		border: 1px solid #dee2e6;
		padding: 8px;
	}

	.table-bordered th {
		background-color: #f8f9fa;
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

	.btn_remove_type {
		background: none;
		border: 1px solid transparent;
		color: darkred;
		font-weight: bold;
		margin-top: 12px;

	}
</style>

<div class="ai_setting_container">
	<!-- Section Heading -->
	<div class="section_head">
		<p class="heading">AI Business Analyst</p>
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
					<select multiple class="form-control" name="report_type[]" id="report_type">
					</select>


					<div class="add_new_type_section">
						<!-- Add Button -->
						<button type="button" class="btn_add_new_type">
							<svg width="16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
								<path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32v144H48c-17.7 0-32 14.3-32 32s14.3 32 32 32h144v144c0 17.7 14.3 32 32 32s32-14.3 32-32V288h144c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z" />
							</svg> Add New Type
						</button>

						<!-- Remove Button with Cross Icon -->
						<button type="button" class="btn_remove_type">
							<svg width="16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
								<path d="M231 256l107-107c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L186 210.7 79 103.7C66.5 91.2 46.2 91.2 33.7 103.7s-12.5 32.8 0 45.3L140.7 256 33.7 363c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L186 301.3l107 107c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L231 256z" />
							</svg> Remove Type
						</button>
					</div>



				</div>
				<!-- <div id="reportTypeList"></div> -->


				<div class="selected_items" id="selectedItemsContainer"></div>

				<!-- ********** Selected items (END) *********** -->

				<!-- Date Section -->

				<div class="data_range">
					<label class="label_name" for="">Select Date</label>
					<div class="input_section_content">
						<input class="input_section" id="myDateRangeInput" type="text" name="daterange" placeholder="Select Date" />
						<svg class="calender_icon" width="14" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
							viewBox="0 0 448 512">
							<path
								d="M128 0c17.7 0 32 14.3 32 32l0 32 128 0 0-32c0-17.7 14.3-32 32-32s32 14.3 32 32l0 32 48 0c26.5 0 48 21.5 48 48l0 48L0 160l0-48C0 85.5 21.5 64 48 64l48 0 0-32c0-17.7 14.3-32 32-32zM0 192l448 0 0 272c0 26.5-21.5 48-48 48L48 512c-26.5 0-48-21.5-48-48L0 192zm64 80l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm128 0l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM64 400l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm144-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zm112 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16z" />
						</svg>
					</div>
					<input type="hidden" name="date_from" id="date_from">
					<input type="hidden" name="date_to" id="date_to">
				</div>
				<!-- Commands -->
				<div class="input_label">
					<label class="label_name" for="">Your Question / Command</label>
					<textarea name="user_input" class="input_section" rows="6" placeholder="Ask something about your report..."></textarea>
				</div>


				<div class="text-right">
					<button type="submit" class="btn_submit">Generate</button>
				</div>

			</form>
			<div style="margin-top: 15px;">
				<button id="toggleInstructions" class="btn_toggle">📌 Instructions</button>
				<button id="toggleExample" class="btn_toggle">💡 Example</button>
			</div>

			<div id="instructionsBox" class="instructions-box" style="display: none;">
				<h4>📌 User Instructions</h4>
				<ul>
					<li>Please first go to the AI Settings page and enter your credentials.</li>
					<li>If you don't have an API key, please purchase one from OpenAI.</li>
					<li>Include report types like: <em>sales, purchase, stock, chart, table_summary</em>.</li>
					<li>For chart display, ensure the report type is set to <em>chart</em>.</li>
					<li>For table report display, ensure the report type is set to <em>table_summary</em>.</li>
					<li>Provide clear and meaningful instructions or commands.</li>
				</ul>
			</div>

			<div class="example-box" id="exampleBox" style="display: none; margin-top: 15px; background: #1e88e5; color: #fff; padding: 10px; border-radius: 8px;">
				<strong>📌 Example Prompt:</strong>
				<p id="examplePromptText" style="margin: 6px 0;">Generate a sales and stock summary report with table format for the last 30 days.</p>
				<button type="button" id="copyExampleBtn" style="background: #fff; color: #1e88e5; border: none; padding: 6px 12px; border-radius: 5px; font-weight: bold; cursor: pointer;">
					Copy & Past
				</button>
			</div>


		</div>
		<!-- AI Chat Report  -->
		<div class="ai_chat_report">
			<div id="pdfContent">
				<div id="aiResponseBox">
					<h5 class="section_titles" style="text-align: center; margin-bottom:10px; border:2px solid green; padding:3px; border-radius:13px 0 13px 0; color:rgb(26, 75, 1); width:29%; margin:auto">AI Business Analysis Report</h5>
					<div id="aiResponseContent" style="white-space: pre-wrap; margin-top:18px;"></div>
					<div id="demoResponse" style="display:none; margin-top:18px;">
						<h4 style="text-align:center; color:gray;">📌 Demo AI Business Report</h4>

						<div class="insights-text">
							<p>Below is a sample AI-generated business insight combining sales, purchases, stock, and recommended actions:</p>

							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Product</th>
										<th>Sales</th>
										<th>Purchases</th>
										<th>Stock</th>
										<th>Remarks</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Product A</td>
										<td>120</td>
										<td>80</td>
										<td>40</td>
										<td>Stock running low. Consider reorder.</td>
									</tr>
									<tr>
										<td>Product B</td>
										<td>150</td>
										<td>100</td>
										<td>50</td>
										<td>Good balance. Monitor demand trends.</td>
									</tr>
									<tr>
										<td>Product C</td>
										<td>90</td>
										<td>110</td>
										<td>80</td>
										<td>Healthy stock. Sales promotion recommended.</td>
									</tr>
									<tr>
										<td>Product D</td>
										<td>200</td>
										<td>150</td>
										<td>50</td>
										<td>High sales. Urgent restock advised.</td>
									</tr>
								</tbody>
							</table>

							<h5>📈 AI Insights:</h5>
							<ul>
								<li>🔥 <b>Product D</b> is in high demand – consider priority restocking.</li>
								<li>✅ <b>Product C</b> has excess stock – launch discounts to boost sales.</li>
								<li>📊 Monitor overall purchase-to-sales ratio to optimize cash flow.</li>
								<li>⚠️ Review vendor pricing for better purchase margins.</li>
							</ul>

							<p style="font-style: italic; color: gray;">This is a demo response. Actual AI-generated insights will vary based on your real sales and stock data.</p>
						</div>
					</div>



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

<!-- Remove Type Modal -->
<div id="removeReportTypeModal" class="modal_ai">
	<div class="modal_overlay"></div>
	<div class="modal_content_ai">
		<p class="modal_heading">Remove Report Types</p>

		<div id="removeTypeList">
			<!-- এখানে লোড হবে টাইপগুলো -->
		</div>

		<div class="modal_actions">
			<button id="closeRemoveModalBtn" class="btn_cancel">Close</button>
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
	$(document).ready(function() {
		$('#aiResponseContent').hide();
		$('#demoResponse').show();
	});

	$('#downloadPdfBtn').off('click').on('click', function() {
		const original = document.getElementById('pdfContent');
		const clone = original.cloneNode(true);

		clone.style.maxWidth = '700px';
		clone.style.margin = 'auto';
		clone.style.padding = '20px';
		clone.style.background = 'white';

		// 👉 Get the original canvas
		const originalCanvas = document.getElementById('summaryChart');
		if (originalCanvas) {
			const imageData = originalCanvas.toDataURL('image/png');

			// 👉 Find the cloned canvas
			const clonedCanvas = clone.querySelector('#summaryChart');
			if (clonedCanvas) {
				// Remove the cloned (blank) canvas
				clonedCanvas.parentNode.removeChild(clonedCanvas);

				// Create <img> and add the data
				const img = document.createElement('img');
				img.src = imageData;
				img.style.width = '600px';
				img.style.height = '400px';
				img.style.display = 'block';
				img.style.margin = '20px auto';

				// Append the img instead of canvas
				const parent = clone.querySelector('#aiResponseBox') || clone;
				parent.appendChild(img);
			}
		}

		clone.classList.add('print-pdf');

		// Hide offscreen
		const container = document.createElement('div');
		container.style.position = 'fixed';
		container.style.left = '-9999px';
		container.appendChild(clone);
		document.body.appendChild(container);

		// PDF options
		const opt = {
			margin: 0.5,
			filename: 'AI_Business_Analysis_' + new Date().toISOString().slice(0, 10) + '.pdf',
			image: {
				type: 'jpeg',
				quality: 0.98
			},
			html2canvas: {
				scale: 1,
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
				mode: ['avoid-all', 'css', 'legacy'],
				before: '#summaryChart'
			}
		};

		html2pdf().set(opt).from(clone).save().then(() => {
			document.body.removeChild(container);
		});
	});
</script>



<script>
	$('#aiChatForm').off('submit').on('submit', function(e) {
		e.preventDefault();

		$.ajax({
			url: '<?= base_url("dashboard/AI_Controller/bdtask_get_ai_analysis") ?>',
			method: 'POST',
			data: $(this).serialize(),
			dataType: 'json',
			beforeSend: function() {
				$('#aiResponseContent').html('<i>Generating AI response...</i>').show();
				$('#demoResponse').hide();
				$('#downloadBtnWrap').hide();
			},
			success: function(res) {
				// ✅ সবসময় পুরনো chart clear করে দেই
				if (window.summaryChart instanceof Chart) {
					window.summaryChart.destroy();
				}
				$('#summaryChart').hide();
				$('#aiResponseContent').html('');

				if (res.status === 'success') {
					let aiText = res.response.trim();

					// 1️⃣ যদি AI response এ "Insights:" থাকে
					if (aiText.includes('Insights:')) {
						const [jsonPartRaw, ...insightParts] = aiText.split('Insights:');
						const insightsText = insightParts.join('Insights:').trim();

						try {
							const chartJson = JSON.parse(jsonPartRaw.trim());

							$('#aiResponseContent').html(`
          <div class="insights-text">${insightsText.replace(/\n/g, '<br>')}</div>
        `);

							renderChart(chartJson);
							$('#summaryChart').show();
							$('#downloadBtnWrap').show();
							return;
						} catch (err) {
							console.error('Chart JSON parse failed', err);

							if (insightsText) {
								$('#aiResponseContent').html(`
            <div class="insights-text">${insightsText.replace(/\n/g, '<br>')}</div>
          `);
								$('#downloadBtnWrap').show();
								return;
							}
						}
					}

					// 2️⃣ যদি pure JSON table data আসে
					try {
						const possibleJson = JSON.parse(aiText);
						if (possibleJson && possibleJson.headers && possibleJson.rows) {
							renderHtmlTable(possibleJson.headers, possibleJson.rows);
							$('#downloadBtnWrap').show();
							return;
						}
					} catch (e) {}

					// 3️⃣ যদি Markdown table থাকে
					if (aiText.includes('|') && aiText.includes('---')) {
						let htmlFromMd = markdownTableToHtml(aiText);
						$('#aiResponseContent').html(htmlFromMd);
						$('#downloadBtnWrap').show();
						return;
					}

					// 4️⃣ Fallback plain text
					$('#aiResponseContent').html(`
      <div class="insights-text">${aiText.replace(/\n/g, '<br>')}</div>
    `);
					$('#downloadBtnWrap').show();

					// 5️⃣ Separate chart_data object থাকলে
					if (res.chart_data && res.chart_data.labels && res.chart_data.labels.length > 0) {
						renderChart(res.chart_data);
						$('#summaryChart').show();
					}

				} else {
					$('#aiResponseContent').html('<span class="text-danger">Error: ' + res.message + '</span>');
					$('#demoResponse').show();
					$('#downloadBtnWrap').hide();
				}
			},

			error: function() {
				$('#aiResponseContent').html('<span class="text-danger">Something went wrong.</span>');
				$('#demoResponse').show();
				$('#downloadBtnWrap').hide();
			}
		});
	});
</script>



<!-- AI Report Type -->
<script>
	function loadReportTypes() {
		$.getJSON('<?= base_url('dashboard/AI_Controller/get_report_types') ?>', function(data) {
			const container = $('#report_type');
			container.empty();

			$.each(data, function(i, item) {
				container.append(`
				<option value="${item.name}">${item.name}</option>
			`);
			});

			// ✅ ডিলিট বাটনযুক্ত লিস্ট দেখাবো
			const listContainer = $('#reportTypeList');
			listContainer.empty();
			$.each(data, function(i, item) {
				listContainer.append(`
				<div class="report_type_item" style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;">
					<span>${item.name}</span>
					<button class="btn_delete_type" data-name="${item.name}" style="color:red;cursor:pointer;border:none;background:none;font-size:16px;">&#10005;</button>
				</div>
			`);
			});
		});
	}


	function loadRemoveTypeList() {
		$.getJSON('<?= base_url('dashboard/AI_Controller/get_report_types') ?>', function(data) {
			const container = $('#removeTypeList');
			container.empty();

			if (data.length === 0) {
				container.append('<p style="text-align:center;color:#888;">No report types found</p>');
				return;
			}

			$.each(data, function(i, item) {
				container.append(`
          <div class="report_type_item" style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #eee;">
            <span>${item.name}</span>
            <button class="btn_delete_type" data-name="${item.name}" style="color:red;cursor:pointer;border:none;background:none;font-size:16px;">&#10005;</button>
          </div>
        `);
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
						// alert("Save Successfully!");
						$('#new_report_type').val('');
						modal.classList.remove('show');
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

<script>
	const addTypeBtn = document.querySelector('.btn_add_new_type');
	const modal = document.getElementById('addReportTypeModal');

	const closeModalBtn = document.getElementById('closeModalBtn');

	const reportTypeSelect = document.getElementById('report_type');

	addTypeBtn.addEventListener('click', () => {
		modal.classList.add('show');
	});

	closeModalBtn.addEventListener('click', () => {
		modal.classList.remove('show');
	});

	modal.addEventListener('click', (e) => {
		if (e.target.classList.contains('modal_overlay')) {
			modal.classList.remove('show');
		}
	});
</script>

<!-- Date picker (Multi calender) -->
<script>
	$(function() {
		const $input = $('input[name="daterange"]');

		$input.daterangepicker({
			autoUpdateInput: false, // 🚫 Do not auto-fill on open
			locale: {
				cancelLabel: 'Clear'
			},
			opens: 'left'
		}, function(start, end) {
			$input.val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
			// Hidden input এ সেট করো
			$('#date_from').val(start.format('YYYY-MM-DD'));
			$('#date_to').val(end.format('YYYY-MM-DD'));
		});

		// ✅ Optional: If you want a "Clear" option to reset
		$input.on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
		});

		// ✅ Icon click triggers input focus
		$('.calender_icon').on('click', function() {
			$input.trigger('click');
		});
	});
</script>

<!--- Delete Report Type form Report type list  --->

<script>
	const removeTypeBtn = document.querySelector('.btn_remove_type');
	const removeModal = document.getElementById('removeReportTypeModal');
	const closeRemoveModalBtn = document.getElementById('closeRemoveModalBtn');

	removeTypeBtn.addEventListener('click', () => {
		loadRemoveTypeList();
		removeModal.classList.add('show');
	});

	closeRemoveModalBtn.addEventListener('click', () => {
		removeModal.classList.remove('show');
	});

	removeModal.addEventListener('click', (e) => {
		if (e.target.classList.contains('modal_overlay')) {
			removeModal.classList.remove('show');
		}
	});
</script>

<script>
	// Delete বাটনে ক্লিক করলে (Modal এর ভিতরে)
	$('#removeTypeList').on('click', '.btn_delete_type', function() {
		const name = $(this).data('name');
		$.post('<?= base_url('dashboard/AI_Controller/delete_report_type') ?>', {
			name: name
		}, function(response) {
			const res = JSON.parse(response);
			if (res.status === 'success') {
				// alert('Deleted successfully!');
				loadReportTypes(); // Select box কে রিফ্রেশ করবে
				loadRemoveTypeList(); // Modal এর লিস্ট রিফ্রেশ করবে
			} else {
				alert(res.message || 'Delete failed!');
			}
		});
	});



	function markdownTableToHtml(mdText) {
		const lines = mdText.trim().split('\n');
		let html = '<h5>📋 AI Table Analysis</h5><table class="table table-bordered">';

		// Headers
		const headerLine = lines.find(line => line.includes('|') && !line.includes('---'));
		if (!headerLine) return '<pre>' + mdText + '</pre>';

		const headers = headerLine.split('|').map(h => h.trim()).filter(Boolean);
		html += '<thead><tr>';
		headers.forEach(h => {
			html += '<th>' + h + '</th>';
		});
		html += '</tr></thead><tbody>';

		// Data Rows
		let dataStarted = false;
		for (let line of lines) {
			if (line.includes('---')) {
				dataStarted = true;
				continue;
			}
			if (!dataStarted) continue;
			if (!line.includes('|')) continue;

			const cells = line.split('|').map(c => c.trim()).filter(Boolean);
			if (cells.length === 0) continue;

			html += '<tr>';
			cells.forEach(c => {
				html += '<td>' + c + '</td>';
			});
			html += '</tr>';
		}

		html += '</tbody></table>';
		return html;
	}



	function renderChart(chartData) {
		const ctx = document.getElementById('summaryChart').getContext('2d');
		if (window.summaryChart instanceof Chart) {
			window.summaryChart.destroy();
		}

		window.summaryChartConfig = {
			type: 'line',
			data: {
				labels: chartData.labels,
				datasets: [{
						label: 'Sales',
						data: chartData.sales,
						borderColor: 'green',
						backgroundColor: 'rgba(0,128,0,0.1)',
						fill: true,
						tension: 0.3
					},
					{
						label: 'Purchases',
						data: chartData.purchases,
						borderColor: 'orange',
						backgroundColor: 'rgba(255,165,0,0.1)',
						fill: true,
						tension: 0.3
					},
					{
						label: 'Stock',
						data: chartData.stock,
						borderColor: 'blue',
						backgroundColor: 'rgba(2, 53, 223, 0.87)',
						fill: true,
						tension: 0.3
					},
					{
						label: 'Sales Return',
						data: chartData.sales_return,
						borderColor: 'orange',
						backgroundColor: 'rgba(255,165,0,0.4)'
					},
					{
						label: 'Supplier Return',
						data: chartData.supplier_return,
						borderColor: 'purple',
						backgroundColor: 'rgba(128,0,128,0.3)'
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
		};

		window.summaryChart = new Chart(ctx, window.summaryChartConfig);
	}
</script>

<script>
	$(document).ready(function() {
		$('#toggleInstructions').click(function() {
			$('#instructionsBox').slideToggle();
		});

		$('#toggleExample').click(function() {
			$('#exampleBox').slideToggle();
		});

		$('#copyExampleBtn').click(function() {
			const exampleText = `Generate a sales report summary for January to March with chart and table_summary.`;
			navigator.clipboard.writeText(exampleText).then(function() {
			}, function(err) {
				alert('Failed to copy text: ' + err);
			});
		});
	});
</script>

<script>
  document.getElementById('copyExampleBtn').addEventListener('click', function () {
    const exampleText = document.getElementById('examplePromptText').innerText.trim();
    document.querySelector('textarea[name="user_input"]').value = exampleText;
    document.querySelector('textarea[name="user_input"]').focus();
  });
</script>
