<link href="<?php echo base_url('assets/css/ai-setting.css'); ?>" rel="stylesheet" type="text/css" />


<div class="ai_setting_container">
	<div class="update_ai_setting_section">
		<p class="section_titles"><?php echo display('update_ai_setting'); ?></p>

		<?php echo form_open_multipart('dashboard/AI_Setting/bdtask_create_ai_settings', ['class' => 'form-vertical', 'id' => 'insert_setting']); ?>

		<div class="form_container">
			<!-- API Key -->
			<div class="input_label">
				<label class="label_name" for="api_key">API Key</label>
				<input type="text" name="api_key" id="api_key" value="<?php echo $sdata->api_key; ?>" placeholder="API Key"
					class="input_section">
			</div>

			<!-- Model -->
			<div class="input_label">
				<label class="label_name" for="discount_type"><?php echo display('model'); ?></label>

				<select class="input_section" name="model" id="model">
					<option value=""><?php echo display('select_one'); ?></option>
					<option value="gpt4" <?php if ($sdata->model == 'gpt4') {
                        echo 'selected';
                    } ?>><?php echo display('gpt4'); ?></option>
					<option value="gpt-3.5-turbo" <?php if ($sdata->model == 'gpt-3.5-turbo') {
                        echo 'selected';
                    } ?>><?php echo display('gpt35turbo'); ?></option>
				</select>
			</div>

			<!-- Temperature -->
			<div class="input_label">
				<label class="label_name" for="temperature"><?php echo display('temperature'); ?></label>
				<input type="text" name="temperature" id="temperature" value="<?php echo $sdata->temperature; ?>"
					placeholder="Temperature" class="input_section">
			</div>

			<!-- Max Tokens -->
			<div class="input_label">
				<label class="label_name" for="max_tokens"><?php echo display('max_tokens'); ?></label>
				<input type="text" name="max_tokens" id="max_tokens" type="number" placeholder="Max_Tokens"
					value="<?php echo $sdata->max_tokens; ?>" class="input_section">
			</div>

			<!-- Prompt Template -->
			<div class="input_label">
				<label class="label_name" for="prompt_template"><?php echo display('prompt_template'); ?></label>
				<input type="text" name="prompt_template" id="prompt_template" placeholder="Prompt template"
					value="<?php echo $sdata->max_tokens; ?>" class="input_section">
			</div>

			<div class="submit_button_section">
				<input type="submit" id="add-setting" class="btn_save_change" name="add-setting"
					value="<?php echo display('save_changes'); ?>" />

				<button type="button" class="btn_cancel">Cancel</button>
			</div>
			<div style="margin-top:15px">
				<span style="color: green; font-size:17px; font-weight: bold;">If you don't have API, Please purchase by this link:- <a href="https://platform.openai.com/docs/overview" target="_blank">Open AI Platform</a> </span>
			</div>
		</div>
	</div>
</div>


<script>
$('#api_key').bind("cut copy", function(e) {
	e.preventDefault();
});
</script>