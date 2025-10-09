
                <div class="row">
            <div class="col-sm-12">
                <?php if($this->permission1->method('add_office_loan','create')->access()){ ?>
                    <a href="<?php echo base_url('add_office_loan')?>" class="btn btn-success m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_loan')?> </a>
                <?php }?>
                <?php if($this->permission1->method('manage_ofln_person','read')->access()){ ?>
                    <a href="<?php echo base_url('manage_office_loans')?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('manage_loan')?> </a>
                <?php }?>
            </div>
        </div>
    

<script type="text/javascript">
(function ($) {
    "use strict";

    var $personSelect = $('#nameofficeloanperson');
    var $loanSelect = $('#loan_transaction_id');
    var $loanHint = $('#loan_balance_hint');
    var baseUrl = $('#base_url').val() || '';

    if (!baseUrl) {
        baseUrl = window.location.origin + '/';
    }

    if (baseUrl && baseUrl.slice(-1) !== '/') {
        baseUrl += '/';
    }

    function formatCurrency(value) {
        var amount = parseFloat(value);
        if (!isFinite(amount) || amount <= 0) {
            return '0.00';
        }
        try {
            return amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } catch (e) {
            return amount.toFixed(2);
        }
    }

    function resetLoanSelect(message) {
        $loanSelect.empty()
            .append($('<option>', { value: '', text: '<?php echo display('select_one'); ?>' }))
            .prop('disabled', true);
        $loanHint.text(message || '');
    }

    $personSelect.on('change', function () {
        var $option = $(this).find('option:selected');
        var personId = $option.val() || '';
        var phone = $option.data('phone') || '';

        if (phone) {
            $('#phone').val(phone);
        }

        resetLoanSelect('');

        if (!personId) {
            return;
        }

        $.getJSON(baseUrl + 'office_loan/person_loans/' + encodeURIComponent(personId))
            .done(function (response) {
                if (!response || response.status !== 'success' || !$.isArray(response.loans) || !response.loans.length) {
                    resetLoanSelect('<?php echo html_escape(display('no_data_found')); ?>');
                    return;
                }

                $loanSelect.prop('disabled', false);
                $.each(response.loans, function (idx, loan) {
                    var label = formatCurrency(loan.remaining_balance) + ' <?php echo display('remaining') ? display('remaining') : 'remaining'; ?>';
                    if (loan.monthly_installment) {
                        label += ' | <?php echo display('monthly') ? display('monthly') : 'monthly'; ?> ' + formatCurrency(loan.monthly_installment);
                    }
                    if (loan.next_due_date) {
                        label += ' | <?php echo display('due_date') ? display('due_date') : 'due'; ?> ' + loan.next_due_date;
                    }
                    $loanSelect.append(
                        $('<option>', {
                            value: loan.transaction_id,
                            text: label
                        }).data({
                            remaining: loan.remaining_balance,
                            monthly: loan.monthly_installment,
                            nextDue: loan.next_due_date
                        })
                    );
                });
                $loanHint.text('<?php echo html_escape(display('select_one')); ?>');
            })
            .fail(function () {
                resetLoanSelect('<?php echo display('please_try_again'); ?>');
            });
    });

    $loanSelect.on('change', function () {
        var $option = $(this).find('option:selected');
        if (!$option.length || !$option.val()) {
            $loanHint.text('');
            return;
        }

        var remaining = formatCurrency($option.data('remaining'));
        var monthly = formatCurrency($option.data('monthly'));
        var nextDue = $option.data('nextDue') || '';
        var parts = [
            '<?php echo display('remaining') ? display('remaining') : 'Remaining'; ?>: ' + remaining
        ];
        if (monthly > 0) {
            parts.push('<?php echo display('monthly') ? display('monthly') : 'Monthly'; ?>: ' + monthly);
        }
        if (nextDue) {
            parts.push('<?php echo display('due_date') ? display('due_date') : 'Due'; ?>: ' + nextDue);
        }
        $loanHint.text(parts.join(' | '));
    });
})(jQuery);
</script>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo display('add_payment') ?> </h4>
                        </div>
                    </div>
                   <?php echo form_open_multipart('hrm/loan/bdtask_submit_payment',array('class' => 'form-vertical','id' => 'inflow_entry' ))?>
                    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
                    <div class="panel-body">

                    	<div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label"><?php echo display('name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="person_id" required id="nameofficeloanperson" tabindex="1">
                                    <option value='' ><?php echo display('select_one')?></option>
                                    <?php if (!empty($person_list)) { ?>
                                        <?php foreach($person_list as $persons){?>
                                            <option value="<?php echo $persons['person_id']?>" data-phone="<?php echo html_escape(isset($persons['person_phone']) ? $persons['person_phone'] : '');?>">
                                                <?php echo html_escape($persons['person_name']);?>
                                            </option>
                                       <?php }?>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-sm-3 col-form-label"><?php echo display('phone') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control phone" name="phone" id="phone" required="" placeholder="<?php echo display('phone') ?>" min="0" tabindex="2"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="loan_transaction_id" class="col-sm-3 col-form-label"><?php echo display('loan') ? display('loan') : 'Loan'; ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="loan_transaction_id" id="loan_transaction_id" required disabled tabindex="3">
                                    <option value=""><?php echo display('select_one'); ?></option>
                                </select>
                                <small class="text-muted d-block" id="loan_balance_hint"></small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="ammount" class="col-sm-3 col-form-label"><?php echo display('ammount') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                               <input type="number" class="form-control" name="ammount" id="ammount" required="" placeholder="<?php echo display('ammount') ?>" min="0" tabindex="4"/>
                            </div>
                        </div>
                           <div class="form-group row" id="payment_from">
                                
                                    <label for="payment_type" class="col-sm-3 col-form-label"><?php
                                        echo display('payment_type');
                                        ?> <i class="text-danger">*</i></label>
                                    <div class="col-sm-6">
                                        <select name="paytype" class="form-control" required="" onchange="bank_paymetExpense(this.value)" tabindex="5">
                                    <option value="1"><?php echo display('cash_payment')?></option>
                                    <option value="2"><?php echo display('bank_payment')?></option> 
                                        </select>
                                      

                                     
                                    </div>
                                
                            </div>
                              
                            <div class="form-group row" id="bank_div">
                                <label for="bank" class="col-sm-3 col-form-label"><?php
                                    echo display('bank');
                                    ?> <i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                   <select name="bank_id" class="form-control"  id="bank_id" tabindex="6">
                                        <option value="">Select Location</option>
                                        <?php foreach($bank_list as $bank){?>
                                            <option value="<?php echo $bank['bank_id']?>"><?php echo $bank['bank_name'];?></option>
                                        <?php }?>
                                    </select>
                                 
                                </div>
                             
                            </div>
                        <div class="form-group row">
                            <label for="date" class="col-sm-3 col-form-label"><?php echo display('date') ?> <i class="text-danger"></i></label>
                            <div class="col-sm-6">
                               <input type="text" class="form-control datepicker" name="date" id="date"  value="<?php echo date("Y-m-d");?>" placeholder="<?php echo display('date') ?>" tabindex="7"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="details" class="col-sm-3 col-form-label"><?php echo display('details') ?> <i class="text-danger"></i></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="details" id="details"  placeholder="<?php echo display('details') ?>" tabindex="8"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-6">
                                <input type="reset" class="btn btn-danger" value="<?php echo display('reset') ?>" tabindex="9"/>
                                <input type="submit" id="add-deposit" class="btn btn-success" name="add-deposit" value="<?php echo display('save') ?>" tabindex="10"/>
                            </div>
                        </div>
                    </div>
                    <?php echo form_close()?>
                </div>
            </div>
        </div>
    
