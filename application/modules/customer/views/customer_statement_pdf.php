<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Statement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #37a000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #37a000;
            margin-bottom: 5px;
        }
        .company-address {
            color: #666;
            line-height: 1.3;
        }
        .statement-info {
            float: right;
            width: 45%;
            text-align: right;
        }
        .statement-title {
            font-size: 28px;
            font-weight: bold;
            color: #37a000;
            margin-bottom: 10px;
        }
        .statement-meta {
            color: #666;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .customer-section {
            margin: 30px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-left: 4px solid #37a000;
        }
        .customer-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .customer-address {
            line-height: 1.4;
        }
        .summary-section {
            margin: 30px 0;
        }
        .summary-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }
        .summary-table .label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 60%;
        }
        .summary-table .amount {
            text-align: right;
            font-weight: bold;
        }
        .transactions-section {
            margin: 30px 0;
        }
        .transactions-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .transactions-table th {
            background-color: #37a000;
            color: #ffffff;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
        }
        .transactions-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
        }
        .transactions-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .amount-cell {
            text-align: right;
            font-family: monospace;
        }
        .balance-due {
            margin-top: 20px;
            padding: 15px;
            background-color: #eaf7ea;
            border: 2px solid #37a000;
            text-align: center;
        }
        .balance-due-label {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .balance-due-amount {
            font-size: 24px;
            font-weight: bold;
            color: #37a000;
            margin-top: 5px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
        .period-info {
            margin: 20px 0;
            padding: 10px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            text-align: center;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header clearfix">
        <div class="company-info">
            <div class="company-name">Everbountiful Farms</div>
            <div class="company-address">
                Kafue<br>                
                Phone: +260978821001<br>
                Email: +260978821001
            </div>
        </div>
        <div class="statement-info">
            <div class="statement-title">STATEMENT</div>
            <div class="statement-meta">
                Statement Date: <?php echo date('F j, Y'); ?><br>
                Statement Period: <?php echo date('M j, Y', strtotime($from_date)); ?> - <?php echo date('M j, Y', strtotime($to_date)); ?>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="customer-section">
        <div class="customer-title">Bill To:</div>
        <div class="customer-address">
            <strong><?php echo html_escape($customer->customer_name); ?></strong><br>
            <?php if (!empty($customer->customer_address)) { ?>
                <?php echo nl2br(html_escape($customer->customer_address)); ?><br>
            <?php } ?>
            <?php if (!empty($customer->address2)) { ?>
                <?php echo nl2br(html_escape($customer->address2)); ?><br>
            <?php } ?>
            <?php if (!empty($customer->city) || !empty($customer->state) || !empty($customer->zip)) { ?>
                <?php echo html_escape($customer->city); ?><?php echo !empty($customer->state) ? ', ' . html_escape($customer->state) : ''; ?> <?php echo html_escape($customer->zip); ?><br>
            <?php } ?>
            <?php if (!empty($customer->country)) { ?>
                <?php echo html_escape($customer->country); ?><br>
            <?php } ?>
            <?php if (!empty($customer->customer_mobile)) { ?>
                Phone: <?php echo html_escape($customer->customer_mobile); ?><br>
            <?php } ?>
            <?php if (!empty($customer->customer_email)) { ?>
                Email: <?php echo html_escape($customer->customer_email); ?>
            <?php } ?>
        </div>
    </div>

    <!-- Period Information -->
    <div class="period-info">
        Showing all invoices and payments between <?php echo date('M j, Y', strtotime($from_date)); ?> and <?php echo date('M j, Y', strtotime($to_date)); ?>
    </div>

    <!-- Account Summary -->
    <div class="summary-section">
        <div class="summary-title">Account Summary</div>
        <?php
            $summary = isset($statement['summary']) && is_array($statement['summary']) ? $statement['summary'] : array('beginning'=>0,'invoiced'=>0,'paid'=>0,'balance_due'=>0);
            $lines = isset($statement['lines']) && is_array($statement['lines']) ? $statement['lines'] : array();
        ?>
        <table class="summary-table">
            <tr>
                <td class="label">Beginning Balance:</td>
                <td class="amount">K<?php echo number_format($summary['beginning'], 2); ?></td>
            </tr>
            <tr>
                <td class="label">Invoiced Amount:</td>
                <td class="amount">K<?php echo number_format($summary['invoiced'], 2); ?></td>
            </tr>
            <tr>
                <td class="label">Amount Paid:</td>
                <td class="amount">K<?php echo number_format($summary['paid'], 2); ?></td>
            </tr>
            <tr style="background-color: #e3f2fd;">
                <td class="label" style="font-size: 14px;"><strong>Balance Due:</strong></td>
                <td class="amount" style="font-size: 14px; color: #007bff;"><strong>K<?php echo number_format($summary['balance_due'], 2); ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- Transaction Details -->
    <div class="transactions-section">
        <div class="transactions-title">Transaction Details</div>
        <table class="transactions-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 45%;">Details</th>
                    <th style="width: 15%;">Debit</th>
                    <th style="width: 15%;">Credit</th>
                    <th style="width: 15%;">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($lines)) { foreach ($lines as $line) { ?>
                    <tr>
                        <td><?php echo html_escape(date('d-m-Y', strtotime($line['date']))); ?></td>
                        <td><?php echo html_escape($line['description']); ?></td>
                        <td class="amount-cell"><?php echo $line['debit'] > 0 ? 'K' . number_format($line['debit'], 2) : ''; ?></td>
                        <td class="amount-cell"><?php echo $line['credit'] > 0 ? 'K' . number_format($line['credit'], 2) : ''; ?></td>
                        <td class="amount-cell"><strong>K<?php echo number_format($line['balance'], 2); ?></strong></td>
                    </tr>
                <?php } } else { ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">No activity for selected period.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Balance Due Highlight -->
    <div class="balance-due">
        <div class="balance-due-label">Total Balance Due</div>
        <div class="balance-due-amount">K<?php echo number_format($summary['balance_due'], 2); ?></div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business. Please remit payment by the due date to avoid any late fees.</p>
        <p>For questions about this statement, please contact us at +260978821001 or info@everbountiful.com</p>
        <p style="margin-top: 15px;">Generated on <?php echo date('F j, Y \a\t g:i A'); ?></p>
    </div>
</body>
</html>
