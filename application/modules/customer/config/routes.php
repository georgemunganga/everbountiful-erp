<?php
defined('BASEPATH') OR exit('No direct script access allowed');



$route['add_customer']         = "customer/customer/bdtask_form";
$route['customer_list']        = "customer/customer/index";
$route['edit_customer/(:num)'] = 'customer/customer/bdtask_form/$1';
$route['credit_customer']      = "customer/customer/bdtask_credit_customer";
$route['paid_customer']        = "customer/customer/bdtask_paid_customer";
$route['customer_ledger']      = "customer/customer/bdtask_customer_ledger";
$route['customer_ledger/(:num)']      = "customer/customer/bdtask_customer_ledger/$1";
$route['customer_ledgerdata']  = "customer/customer/bdtask_customer_ledgerData";
$route['customer_advance']     = "customer/customer/bdtask_customer_advance";
$route['customer/edit/(:num)']  = 'customer/customer/bdtask_form/$1';
$route['advance_receipt/(:any)/(:num)'] = 'customer/customer/customer_advancercpt/$1/$2';
$route['customer/add_contact/(:num)']   = 'customer/customer/add_contact/$1';
$route['customer/delete_contact/(:num)/(:num)'] = 'customer/customer/delete_contact/$1/$2';
$route['customer/add_note/(:num)'] = 'customer/customer/add_note/$1';
$route['customer/delete_note/(:num)/(:num)'] = 'customer/customer/delete_note/$1/$2';
$route['customer/add_reminder/(:num)'] = 'customer/customer/add_reminder/$1';
$route['customer/delete_reminder/(:num)/(:num)'] = 'customer/customer/delete_reminder/$1/$2';
$route['customer/add_credit_note/(:num)'] = 'customer/customer/add_credit_note/$1';
$route['customer/delete_credit_note/(:num)/(:num)'] = 'customer/customer/delete_credit_note/$1/$2';
$route['customer/add_estimate/(:num)'] = 'customer/customer/add_estimate/$1';
$route['customer/delete_estimate/(:num)/(:num)'] = 'customer/customer/delete_estimate/$1/$2';
$route['customer/add_expense/(:num)'] = 'customer/customer/add_expense/$1';
$route['customer/delete_expense/(:num)/(:num)'] = 'customer/customer/delete_expense/$1/$2';
$route['customer/customer_statement'] = 'customer/customer/customer_statement';
$route['customer/customer_statement/(:num)'] = 'customer/customer/customer_statement/$1';
$route['customer/customer_statement_pdf/(:num)'] = 'customer/customer/customer_statement_pdf/$1';
$route['customer/statement'] = 'customer/customer/customer_statement';
$route['customer/statement/(:num)'] = 'customer/customer/customer_statement/$1';
$route['customer/statement/(:num)/pdf'] = 'customer/customer/customer_statement_pdf/$1';
