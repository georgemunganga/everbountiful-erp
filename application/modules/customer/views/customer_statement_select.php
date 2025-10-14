<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-bd lobidrag">
      <div class="panel-heading">
        <div class="panel-title">
          <h4><?php echo display('customer_statement') ?: 'Customer Statement'; ?></h4>
        </div>
      </div>
      <div class="panel-body">
        <?php if (!empty($customers) && is_array($customers)) { ?>
          <form class="form-inline" method="get" action="<?php echo base_url('customer/customer_statement'); ?>">
            <div class="form-group">
              <label for="customer_id" class="sr-only">Customer</label>
              <select name="customer_id" id="customer_id" class="form-control" required>
                <?php foreach ($customers as $value => $label) {
                  $isSelected = isset($selected_customer_id) && (string)$selected_customer_id === (string)$value;
                ?>
                  <option value="<?php echo html_escape($value); ?>" <?php echo $isSelected ? 'selected' : ''; ?>><?php echo html_escape($label); ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group" style="margin-left: 10px;">
              <label for="from_date">From</label>
              <input type="date" id="from_date" name="from_date" class="form-control" value="<?php echo html_escape($from_date); ?>">
            </div>
            <div class="form-group" style="margin-left: 10px;">
              <label for="to_date">To</label>
              <input type="date" id="to_date" name="to_date" class="form-control" value="<?php echo html_escape($to_date); ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-left: 10px;">View Statement</button>
          </form>
          <p style="margin-top: 15px;">Select a customer and date range to view the detailed statement.</p>
        <?php } else { ?>
          <div class="alert alert-info">
            No customers found. Please add a customer before generating statements.
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
