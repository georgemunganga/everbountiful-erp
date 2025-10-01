<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DemoRestriction {

    public function check_request_method() {
        if (defined('DEMO_MODE') && DEMO_MODE === true) {
            $CI =& get_instance();
            $CI->load->library('session');
    
            $method = $_SERVER['REQUEST_METHOD'] ?? '';
            $current_url = $_SERVER['REQUEST_URI'] ?? '';
    
            // ✅ Allow GET requests and safe POSTs for data fetch
            $allowed_urls = [
                '/login',
                '/auth',
                'charts/getPercentage',
                'supplier/supplier/bdtask_ChecksupplierList',
                'customer/customer/bdtask_CheckCustomerList',
                'product/product/CheckProductList',
                'purchase/purchase/CheckPurchaseList',
                'report/report/bdtask_checkStocklist',
                'account/accounts/CheckdebitvoucherList',
                'account/accounts/CheckcreditvoucherList',
                'account/accounts/CheckjournalvoucherList',  // ✅ allow this request
                '/add_payment_method',  // ✅ allow this request
                '/trial_balance',  // ✅ allow this request
                '/day_book',  // ✅ allow this request
                '/general_ledger',  // ✅ allow this request
                '/sub_ledger',  // ✅ allow this request
                '/trial_balance',  // ✅ allow this request
                '/income_statement_form',  // ✅ allow this request
                '/invoice/invoice/getitemlist',  // ✅ allow this request
                '/expenditure_statement',  // ✅ allow this request
                '/profit_loss_report',  // ✅ allow this request
                '/balance_sheet',  // ✅ allow this request
                '/fixedasset_schedule',  // ✅ allow this request
                '/receipt_payment',  // ✅ allow this request
                '/coa_print',  // ✅ allow this request
                '/bank_reconciliation_report',  // ✅ allow this request
                '/closing_form',  // ✅ allow this request
                '/closing_report',  // ✅ allow this request
                '/report/report/CheckReportList',  // ✅ allow this request
                '/report/report/getSalesReportList',  // ✅ allow this request
                '/todays_customer_received',  // ✅ allow this request
                '/userwise_sales_report',  // ✅ allow this request
                '/report/report/get_retrieve_dateWise_DueReports',  // ✅ allow this request
                '/shipping_cost_report',  // ✅ allow this request
                '/purchase_report',  // ✅ allow this request
                '/purchase_report_categorywise',  // ✅ allow this request
                '/product_wise_sales_report',  // ✅ allow this request
                '/sales_return',  // ✅ allow this request
                '/supplier_returns',  // ✅ allow this request
                '/tax_report',  // ✅ allow this request
                '/profit_report',  // ✅ allow this request
                '/designation_form',  // ✅ allow this request
                '/employee_form',  // ✅ allow this request
                '/bank_form',  // ✅ allow this request
                '/add_service',  // ✅ allow this request
                '/add_service_invoice',  // ✅ allow this request
                '/add_quotation',  // ✅ allow this request
                '/vat_tax_setting',  // ✅ allow this request
                '/tax_setting',  // ✅ allow this request
                '/return_form',  // ✅ allow this request
                '/invoice_return_list',  // ✅ allow this request
                '/supplier_return_list',  // ✅ allow this request
                '/wastage_return_list',  // ✅ allow this request
                '/invoice/invoice/gui_pos_invoice',  // ✅ allow this request
                '/gui_pos',  // ✅ allow this request
                '/invoice/invoice/get_invoice_id',  // ✅ allow this request
                '/invoice/invoice/gui_pos_invoice',  // ✅ allow this request
                '/invoice/invoice/bdtask_manual_sales_insert',  // ✅ allow this request
                '/invoice/invoice/bdtask_autocomplete_product',  // ✅ allow this request
                '/terms_add',  // ✅ allow this request
                '/purchase/purchase/bdtask_product_search_by_supplier',  // ✅ allow this request
                '/add_purchase',  // ✅ allow this request
                '/purchase/purchase/bdtask_save_purchase',  // ✅ allow this request
                '/purchase/purchase/bdtask_retrieve_product_data',  // ✅ allow this request
                '/add_invoice',  // ✅ allow this request
                '/invoice/invoice/retrieve_product_data_inv',  // ✅ allow this request
                '/invoice/invoice/bdtask_batchwise_productprice',  // ✅ allow this request
                // Add other data-fetching routes here as needed
            ];
            

            $current_url_lc = strtolower($current_url);
            foreach ($allowed_urls as $allowed_url) {
                if (strpos($current_url_lc, strtolower($allowed_url)) !== false) {
                    return; // Allow this request
                }
            }
    
            // ❌ Block data modification attempts
            if (
                $method === 'POST' ||
                $method === 'PUT' ||
                $method === 'DELETE' ||
                strpos($current_url, '/delete') !== false ||
                strpos($current_url, '/update') !== false ||
                strpos($current_url, '/create') !== false
            ) {
                // If it's an AJAX request, return JSON error
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'error', 'message' => 'This is a demo project. Data modification is not allowed.']);
                    exit;
                }
    
                // Otherwise, flash message and redirect
                $CI->session->set_flashdata('exception', 'This is a demo project. Data modification is not allowed.');
                redirect($_SERVER['HTTP_REFERER'] ?? base_url());
                exit;
            }
        }
    }
    
    
}

