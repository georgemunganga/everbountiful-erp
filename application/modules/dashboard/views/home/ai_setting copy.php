<link href="<?php echo base_url('assets/css/ai-setting.css'); ?>" rel="stylesheet" type="text/css" />


<div class="row m-t-20">
	<div class="col-sm-12">
		<div class="panel panel-bd lobidrag">
			<div class="panel-heading">
				<div class="panel-title">
					<h4><?php echo display('update_ai_setting');
                    ?> </h4>
				</div>
			</div>
			<?php echo form_open_multipart('dashboard/AI_Setting/bdtask_create_ai_settings', ['class' => 'form-vertical', 'id' => 'insert_setting']); ?>
			<div class="panel-body">


				<div class="form-group row">
					<label for="api_key" class="col-sm-3 col-form-label"><?php echo display('api_key'); ?> <i
							class="text-danger">*</i></label>
					<div class="col-sm-6">
						<input class="form-control" name="api_key" id="api_key" type="text" placeholder="API Key"
							value="<?php echo $sdata->api_key; ?>" tabindex="7">
						<input type="hidden" name="id" value="<?php echo $sdata->setting_id; ?>">
					</div>
				</div>

				<div class="form-group row">
					<label for="discount_type" class="col-sm-3 col-form-label"><?php echo display('model'); ?> <i
							class="text-danger">*</i></label>
					<div class="col-sm-6">
						<select class="form-control" name="model" id="model" tabindex="10">
							<option value=""><?php echo display('select_one'); ?></option>
							<option value="gpt4" <?php if ($sdata->model == 'gpt4') {
                                echo 'selected';
                            } ?>><?php echo display('gpt4'); ?></option>
							<option value="gpt35turbo" <?php if ($sdata->model == 'gpt35turbo') {
                                echo 'selected';
                            } ?>><?php echo display('gpt35turbo'); ?></option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="temperature" class="col-sm-3 col-form-label"><?php echo display('temperature'); ?> <i
							class="text-danger">*</i></label>
					<div class="col-sm-6">
						<input class="form-control" name="temperature" id="temperature" type="text" placeholder="Temperature"
							value="<?php echo $sdata->temperature; ?>" tabindex="7">
					</div>
				</div>

				<div class="form-group row">
					<label for="max_tokens" class="col-sm-3 col-form-label"><?php echo display('max_tokens'); ?> <i
							class="text-danger">*</i></label>
					<div class="col-sm-6">
						<input class="form-control" name="max_tokens" id="max_tokens" type="number" placeholder="Max_Tokens"
							value="<?php echo $sdata->max_tokens; ?>" tabindex="7">
					</div>
				</div>

				<div class="form-group row">
					<label for="prompt_template" class="col-sm-3 col-form-label"><?php echo display('prompt_template'); ?> <i
							class="text-danger">*</i></label>
					<div class="col-sm-6">
						<input class="form-control" name="prompt_template" id="prompt_template" type="text"
							placeholder="prompt_template" value="<?php echo $sdata->max_tokens; ?>" tabindex="7">
					</div>
				</div>


				<div class="form-group row">
					<label for="example-text-input" class="col-sm-4 col-form-label"></label>
					<div class="col-sm-6">
						<input type="submit" id="add-setting" class="btn btn-success btn-large" name="add-setting"
							value="<?php echo display('save_changes'); ?>" tabindex="13" />
					</div>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>





<script>
$('#api_key').bind("cut copy", function(e) {
	e.preventDefault();
});
</script>