<style type="text/css">
    table {
        font-size: 13px;
    }

    table thead {
        background-color: #E7E0EE;
    }

    table.payrollDatatableReport {
        border-collapse: collapse;
        border: 0;
    }

    table.payrollDatatableReport td,
    table.payrollDatatableReport th {
        padding: 6px 15px;
    }

    table.payrollDatatableReport td,
    table.payrollDatatableReport th {
        border: 1px solid #ededed;
        border-collapse: collapse;
    }

    table.payrollDatatableReport td.noborder {
        border: none;
        padding-top: 40px;
    }

    table.payrollDatatableReport tbody tr td {
        font-size: 10px;
        padding-left: 5px;
        font-size: 13px;
    }

    table.payrollDatatableReport thead tr th {
        font-size: 13px;
        padding-left: 5px;
        text-align: left;
    }
</style>

<div class="container">

    <div class="panel panel-default thumbnail">

        <div class="panel-body">

            <div class="text-right" id="print">
                <a href="<?php echo base_url('salary_pay_slip_pdf/' . (isset($salary_info->id) ? (int) $salary_info->id : 0)); ?>" class="btn btn-info" id="btnDownloadPdf">
                    <i class="fa fa-download"></i>
                </a>
                <button type="button" class="btn btn-warning" id="btnPrint" onclick="printPageArea('printArea');">
                    <i class="fa fa-print"></i>
                </button>
            </div>

            <br>

            <div id="printArea">

                <div style="padding:20px;">

                    <table width="99%">
                        <tr>
                            <td width="30%" align="left">
                                <?php
                                // Generate a clean URL or file path
                                $path = rtrim(base_url(), '/') . '/' . (!empty($setting->logo) ? $setting->logo : 'assets/img/icons/mini-logo.png');

                                // Check if the file exists and read it
                                if (@file_get_contents($path)) {
                                    $type = pathinfo($path, PATHINFO_EXTENSION);
                                    $data = file_get_contents($path);
                                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                } else {
                                    $base64 = ''; // Fallback for missing file
                                }
                                ?>
                                <img src="<?php echo $base64; ?>" alt="Logo">
                            </td>
                            <td width="30%" align="right">
                                </date>
                            </td>
                        </tr>
                    </table>
                    <br>

                    <div class="row mb-10">
                        <table width="99%">
                            <thead>
                                <tr style="height: 40px;background-color: #E7E0EE;">
                                    <th class="text-center fs-20">PAYSLIP</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <br>
                    <div class="row">
                        <table width="99%"
                            class="payrollDatatableReport table table-striped table-bordered table-hover">
                            <tbody>
                                <tr style="text-align: left;background-color: #E7E0EE;">
                                    <th>Employee Name</th>
                                    <th><?php echo $salary_info->first_name . ' ' . $salary_info->last_name; ?></th>
                                    <th>Month</th>
                                    <th><?php echo $month_name; ?></th>
                                </tr>
                                <tr style="text-align: left;">
                                    <th>Position</th>
                                    <td><?php echo $employee_info->designation; ?></td>
                                    <td>From</td>
                                    <td><?php echo $from_date; ?></td>
                                </tr>
                                <tr style="text-align: left;">
                                    <th>Contact</th>
                                    <td><?php echo $employee_info->phone; ?></td>
                                    <td>To</td>
                                    <td><?php echo $to_date; ?></td>
                                </tr>
                                <tr style="text-align: left;">
                                    <th>Worked Days</th>
                                    <td><?php echo html_escape(isset($worked_days) && $worked_days !== '' ? $worked_days : ''); ?></td>
                                    <td>Leave Days</td>
                                    <td><?php echo isset($leave_days) && (float)$leave_days > 0 ? html_escape(number_format((float)$leave_days, 2)) : '0'; ?></td>
                                </tr>
                                <tr style="text-align: left;">
                                    <th>Address</th>
                                    <td><?php echo $employee_info->address_line_1; ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="text-align: left;">
                                    <th>Recruitment date</th>
                                    <td><?php echo isset($recruitment_date) ? $recruitment_date : 'Not Available'; ?>
                                    </td>
                                    <td>Worked Days</td>
                                    <td><?php echo $work_days; ?></td>
                                </tr>
                                <tr style="text-align: left;">
                                    <th>Staff ID No.</th>
                                    <td><?php echo $employee_info->id; ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr style="text-align: left;">
                                    <th>Seniority (yrs)</th>
                                    <td><?php echo isset($seniority_years) ? $seniority_years : 'Not Available'; ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php

                    $curncy_symbol = '';
                    $social_security_tax_percnt = '';
                    if (!empty($setting->currency_symbol)) {
                        $curncy_symbol = $setting->currency_symbol;
                        $social_security_tax_percnt = $setting->soc_sec_npf_tax;
                    }

                    $total_benefits = 0.0;
                    $total_benefits = floatval($salary_info->medical_benefit) + floatval($salary_info->family_benefit) + floatval($salary_info->transportation_benefit) + floatval($salary_info->other_benefit);

                    $component_breakdown = isset($component_breakdown) && is_array($component_breakdown) ? $component_breakdown : array('earnings' => array(), 'deductions' => array(), 'earning_total' => 0.0, 'deduction_total' => 0.0);
                    $component_earnings = isset($component_breakdown['earnings']) && is_array($component_breakdown['earnings']) ? $component_breakdown['earnings'] : array();
                    $component_deductions = isset($component_breakdown['deductions']) && is_array($component_breakdown['deductions']) ? $component_breakdown['deductions'] : array();
                    $component_add_total = isset($component_add_total) ? (float) $component_add_total : (float) $component_breakdown['earning_total'];
                    $component_ded_total = isset($component_ded_total) ? (float) $component_ded_total : (float) $component_breakdown['deduction_total'];
                    $component_add_total_display = round($component_add_total, 2);
                    $component_ded_total_display = round($component_ded_total, 2);
                    $office_loan_display = isset($office_loan_total) ? (float) $office_loan_total : (isset($salary_info->office_loan_deduct) ? (float) $salary_info->office_loan_deduct : 0.0);
                    $net_salary_display = isset($net_salary_calculated) ? (float) $net_salary_calculated : (isset($salary_info->net_salary) ? (float) $salary_info->net_salary : 0.0);
                    $post_gross_total_display = isset($post_gross_total) ? (float) $post_gross_total : ((isset($salary_info->gross_salary) ? (float) $salary_info->gross_salary : 0.0) + $component_add_total);
                    $social_security_combined_display = isset($social_security_combined_total) ? (float) $social_security_combined_total : (floatval($salary_info->employer_contribution) + floatval($salary_info->soc_sec_npf_tax));
                    $social_security_employee_display = isset($social_security_employee_total) ? (float) $social_security_employee_total : (float) $salary_info->soc_sec_npf_tax;

                    ?>

                    <div class="row">
                        <table width="99%"
                            class="payrollDatatableReport table table-striped table-bordered table-hover">
                            <thead>
                                <tr style="text-align: left;background-color: #E7E0EE;">
                                    <th>Description</th>
                                    <th>Gross Amount</th>
                                    <th>Rate</th>
                                    <th><?php echo display('additions') ? display('additions') : 'Additions'; ?></th>
                                    <th>Deduction</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="text-align: left;">
                                    <td>Basic Salary</td>
                                    <td><?php echo $curncy_symbol . ' ' . number_format($salary_info->basic, 2); ?></td>
                                    <td></td>
                                    <td><?php echo $curncy_symbol . ' ' . number_format($salary_info->basic_salary_pro_rated, 2); ?></td>
                                    <td></td>
                                </tr>
                                
                                <tr style="text-align: left;">
                                    <td>Total Benefit</td>
                                    <td></td>
                                    <td></td>
                                    <td><?php echo $curncy_symbol . ' ' . number_format($total_benefits, 2); ?></td>
                                    <td></td>
                                </tr>
                                <tr style="text-align: left;">
                                    <td>Overtime</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <?php if (!empty($component_earnings)) { ?>
                                <?php foreach ($component_earnings as $earning) { ?>
                                <tr style="text-align: left;">
                                    <td><?php echo html_escape($earning['name']); ?></td>
                                    <td></td>
                                    <td></td>
                                    <td><?php echo $curncy_symbol . ' ' . number_format($earning['amount'], 2); ?></td>
                                    <td></td>
                                </tr>
                                <?php } ?>
                                <tr style="text-align: left;">
                                    <th>Component Additions Total</th>
                                    <th></th>
                                    <th></th>
                                    <th><?php echo $curncy_symbol . ' ' . number_format($component_add_total_display, 2); ?></th>
                                    <th></th>
                                </tr>
                                <?php } ?>
                                <tr style="text-align: left;">
                                    <th>Gross Salary</th>
                                    <th></th>
                                    <th></th>
                                    <th><?php echo $curncy_symbol . ' ' . number_format($salary_info->gross_salary, 2); ?></th>
                                    <th></th>
                                </tr>
                                <?php if (!empty($component_earnings)) { ?>
                                <tr style="text-align: left;">
                                    <th>Subtotal Before Deductions</th>
                                    <th></th>
                                    <th></th>
                                    <th><?php echo $curncy_symbol . ' ' . number_format($post_gross_total_display, 2); ?></th>
                                    <th></th>
                                </tr>
                                <?php } ?>

                                <?php if (!empty($component_deductions)) { ?>
                                <?php foreach ($component_deductions as $deduction) { ?>
                                <tr style="text-align: left;">
                                    <td><?php echo html_escape($deduction['name']); ?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><?php echo $curncy_symbol . ' ' . number_format($deduction['amount'], 2); ?></td>
                                </tr>
                                <?php } ?>
                                <tr style="text-align: left;">
                                    <th>Component Deductions Total</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><?php echo $curncy_symbol . ' ' . number_format($component_ded_total_display, 2); ?></th>
                                </tr>
                                <?php } ?>

                                <?php 
                                if ($office_loan_display > 0) { ?>
                                <tr style="text-align: left !important;">
                                    <th style="text-align: left !important;">Office Loan Deduction</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style="text-align: left !important;"><?php echo $curncy_symbol.' '.number_format($office_loan_display,2);?></td>
                                </tr>
                                <?php } ?>
<tr style="text-align: left;">
                                    <th align="left">Total Deductions</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th align="left">
                                        <?php echo $curncy_symbol . ' ' . number_format($total_deductions, 2); ?>
                                    </th>
                                </tr>
                                <tr style="text-align: left;">
                                    <th colspan="3" align="left">NET SALARY</th>

                                    <th></th>
                                    <th align="left">
                                        <?php echo $curncy_symbol . ' ' . number_format($net_salary_display, 2); ?>
                                    </th>
                                </tr>
                            </tbody>

                            <br>

                            <tfoot>
                                <tr>
                                    <td colspan="5" class="noborder">
                                        <table border="0" width="100%"
                                            style="padding-top: 50px;border: none !important;">
                                            <tr border="0"
                                                style="height:50px;padding-top: 50px;border-left: none !important;">
                                                <td align="left" class="noborder" width="30%">
                                                    <div class="border-top"><?php echo display('prepared_by') ?>:
                                                        <b><?php echo $user_info['fullname']; ?><b>
                                                    </div>
                                                </td>
                                                <td align="left" class="noborder" width="30%">
                                                    <div class="border-top"><?php echo display('checked_by') ?></div>
                                                </td>
                                                <td align="left" class="noborder" width="20%">
                                                    <div class="border-top"><?php echo display('authorised_by') ?></div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                </div>

            </div>

        </div>
    </div>

</div>

<script type="text/javascript">
    function printPageArea(areaID) {
        var printContent = document.getElementById(areaID);
        var WinPrint = window.open('', '', 'width=900,height=650');

        var htmlToPrint = '' +
            '<style type="text/css">' +
            'table.payrollDatatableReport {' +
            'border-collapse: collapse;border: 0' +
            '}' +
            'table.payrollDatatableReport td, table.payrollDatatableReport th {' +
            'padding: 6px 15px;' +
            '}' +
            'table.payrollDatatableReport td, table.payrollDatatableReport th {' +
            'border: 1px solid #ededed;border-collapse: collapse;' +
            '}' +
            'table.payrollDatatableReport td.noborder {' +
            'border: none;padding-top: 40px;' +
            '}' +
            '</style>';

        htmlToPrint += printContent.innerHTML;

        WinPrint.document.write(htmlToPrint);
        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print();
    }
</script>
