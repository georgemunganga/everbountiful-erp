<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    


class AI_Controller extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->db->query('SET SESSION sql_mode = ""');
        $this->load->model(array(
            'AI_Setting_model'
        ));

        $this->load->library('ciqrcode');
        if (!$this->session->userdata('isLogIn'))
            redirect('login');

        $this->load->model('invoice/Invoice_model', 'invoice_model');
        $this->load->model('purchase/Purchase_model', 'purchase_model');
        $this->load->model('report/Report_model', 'report_model');
    }

    // Controller method to fetch types from database
    public function get_report_types()
    {
        $types = $this->db->get('ai_report_types')->result();
        echo json_encode($types);
    }

    // Controller method to insert new type
    public function add_report_type()
    {
        $name = $this->input->post('name');
        if (!empty($name)) {
            $this->db->insert('ai_report_types', ['name' => $name]);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Name required']);
        }
    }

    


//  public function bdtask_get_ai_analysis()
// {
//     $report_types = $this->input->post('report_type') ?? [];
//     if (!is_array($report_types)) $report_types = [];

//     $date_from = $this->input->post('date_from') ?: date('Y-m-01');
//     $date_to = $this->input->post('date_to') ?: date('Y-m-d');
//     $user_input = $this->input->post('user_input');

//     $summary_text = "";
//     $all_dates = [];
//     $chart_raw = [
//         'sales' => [],
//         'purchases' => [],
//         'stock' => [],
//         'sales_return' => [],
//         'supplier_return' => []
//     ];

//     $include_chart = in_array('chart', array_map('strtolower', $report_types));
//     $include_table = in_array('table_summary', array_map('strtolower', $report_types));
//     $table_data = [];

//     foreach ($report_types as $report_type) {
//         $report_type = strtolower($report_type);

//         if ($report_type === 'sales') {
//             $sales_data = $this->invoice_model->get_sales_summary($date_from, $date_to);
//             $summary_text .= "📊 **Sales Summary** ($date_from to $date_to):\n";
//             foreach ($sales_data as $row) {
//                 $summary_text .= "Date: {$row->sale_date}, Sales: {$row->total_sales}, Customer Name: {$row->customer_name}, Product Name: {$row->product_name}, Qty: {$row->total_qty}, Amount: {$row->total_sales_amount} BDT\n";
//                 $all_dates[] = $row->sale_date;
//                 $chart_raw['sales'][$row->sale_date] = (float)$row->total_sales_amount;
//             }
//             $summary_text .= "\n";
//         }

//         if ($report_type === 'sales_return') {
//             $sales_return_data = $this->report_model->sales_return_list($date_from, $date_to);
//             $summary_text .= "↩️ **Sales Return Summary** ($date_from to $date_to):\n";
//             if (!empty($sales_return_data)) {
//                 foreach ($sales_return_data as $row) {
//                     $summary_text .= "Date: {$row['date_return']}, Customer: {$row['customer_name']}, Return Amount: {$row['net_total_amount']} BDT\n";
//                     $all_dates[] = $row['date_return'];
//                     $chart_raw['sales_return'][$row['date_return']] = (float)$row['net_total_amount'];
//                 }
//             } else {
//                 $summary_text .= "No sales return data found.\n";
//             }
//             $summary_text .= "\n";
//         }

//         if ($report_type === 'purchase') {
//             $purchase_data = $this->purchase_model->get_purchase_summary($date_from, $date_to);
//             $summary_text .= "📦 **Purchase Summary** ($date_from to $date_to):\n";
//             foreach ($purchase_data as $row) {
//                 $summary_text .= "Date: {$row->purchase_date}, Product Name: {$row->product_name}, Qty: {$row->total_qty}, Cost: {$row->total_amount} BDT\n";
//                 $all_dates[] = $row->purchase_date;
//                 $chart_raw['purchases'][$row->purchase_date] = (float)$row->total_amount;
//             }
//             $summary_text .= "\n";
//         }

//         if ($report_type === 'supplier_return') {
//             $supplier_return_data = $this->report_model->supplier_return($date_from, $date_to);
//             $summary_text .= "↩️ **Supplier Return Summary** ($date_from to $date_to):\n";
//             if (!empty($supplier_return_data)) {
//                 foreach ($supplier_return_data as $row) {
//                     $summary_text .= "Date: {$row['date_return']}, Supplier: {$row['supplier_name']}, Return Amount: {$row['net_total_amount']} BDT\n";
//                     $all_dates[] = $row['date_return'];
//                     $chart_raw['supplier_return'][$row['date_return']] = (float)$row['net_total_amount'];
//                 }
//             } else {
//                 $summary_text .= "No supplier return data found.\n";
//             }
//             $summary_text .= "\n";
//         }

//         if ($report_type === 'stock') {
//             $stock_data = $this->report_model->bdtask_getStock([
//                 'draw' => 1,
//                 'start' => 0,
//                 'length' => 1000,
//                 'order' => [['column' => 0, 'dir' => 'asc']],
//                 'columns' => [['data' => 'product_name']],
//                 'search' => ['value' => '']
//             ]);
//             $summary_text .= "📦 **Stock Summary** (as of $date_to):\n";
//             $total_stock = 0;
//             if (!empty($stock_data['aaData'])) {
//                 foreach ($stock_data['aaData'] as $row) {
//                     $summary_text .= "Product: {$row['product_name']} ({$row['product_model']}), Stock: {$row['stok_quantity']}, Sales Qty: {$row['totalSalesQnty']}, Purchase Qty: {$row['totalPurchaseQnty']}, Purchase Price: {$row['purchase_p']} BDT, Sales Price: {$row['sales_price']} BDT\n";
//                     $total_stock += (float)$row['stok_quantity'];
//                 }
//                 $chart_raw['stock'][$date_to] = $total_stock;
//                 $all_dates[] = $date_to;
//             } else {
//                 $summary_text .= "No stock data found.\n";
//             }
//             $summary_text .= "\n";
//         }

//         if ($report_type === 'table_summary') {
//             $summary_text .= "✅ Table summary requested. Please analyze the above sales, purchase, stock, return summaries and provide your response in a clean table format (Markdown or JSON with headers and rows).\n";
//         }
//     }

//     $labels = array_values(array_unique($all_dates));
//     sort($labels);

//     $label_string = implode('", "', $labels);
//     $instruction = $include_chart
//         ? "Please analyze the following business data and return JSON with aligned arrays:\n{\"labels\": [\"$label_string\"], \"sales\": [...], \"purchases\": [...], \"stock\": [...], \"sales_return\": [...], \"supplier_return\": [...]} AND also give insights."
//         : "Please analyze the following business data and only give insights (no chart JSON needed).";

//     $final_prompt = $instruction . "\n\n" . $user_input . "\n\n" . $summary_text;
//     $ai_response = $this->call_openai($final_prompt);

//     // Try to extract JSON
//     $chart_data = [
//         'labels' => [],
//         'sales' => [],
//         'purchases' => [],
//         'stock' => [],
//         'sales_return' => [],
//         'supplier_return' => []
//     ];

//     if ($include_chart) {
//         if (preg_match('/\{.*\}/s', $ai_response, $matches)) {
//             $decoded = json_decode($matches[0], true);
//             if ($decoded && isset($decoded['labels'])) {
//                 $chart_data = $decoded;
//             }
//         }
//     }

//     echo json_encode([
//         'status' => 'success',
//         'response' => $ai_response,
//         'chart_data' => $include_chart ? $chart_data : null,
//         'table_data' => $include_table ? $table_data : null
//     ]);
//     exit;
// }



    public function bdtask_get_ai_analysis()
    {
        $report_types = $this->input->post('report_type');
        $date_from = $this->input->post('date_from') ?: date('Y-m-01');
        $date_to = $this->input->post('date_to') ?: date('Y-m-d');
        $user_input = $this->input->post('user_input');

        $summary_text = "";
        $include_chart = in_array('chart', array_map('strtolower', $report_types));
        $include_table = in_array('table_summary', array_map('strtolower', $report_types));

        $all_dates = [];
        $chart_raw = [
            'sales' => [],
            'purchases' => [],
            'stock' => [],
            'sales_return' => [],
            'supplier_return' => []
        ];

        $table_data = [];

        foreach ($report_types as $report_type) {
            $report_type = strtolower($report_type);

            if ($report_type === 'sales') {
                $sales_data = $this->invoice_model->get_sales_summary($date_from, $date_to);
                $summary_text .= "📊 **Sales Summary** ($date_from to $date_to):\n";
                foreach ($sales_data as $row) {
                    $summary_text .= "Date: {$row->sale_date}, Sales: {$row->total_sales}, Customer Name: {$row->customer_name}, Product Name: {$row->product_name}, Qty: {$row->total_qty}, Amount: {$row->total_sales_amount} BDT\n";
                    $all_dates[] = $row->sale_date;
                    $chart_raw['sales'][$row->sale_date] = (float) $row->total_sales_amount;
                }
                $summary_text .= "\n";
            }

            if ($report_type === 'sales_return') {
                $sales_return_data = $this->report_model->sales_return_list($date_from, $date_to);
                $summary_text .= "↩️ **Sales Return Summary** ($date_from to $date_to):\n";
                if (!empty($sales_return_data)) {
                    foreach ($sales_return_data as $row) {
                        $summary_text .= "Date: {$row['date_return']}, Customer: {$row['customer_name']}, Return Amount: {$row['net_total_amount']} BDT\n";
                        $all_dates[] = $row['date_return'];
                        $chart_raw['sales_return'][$row['date_return']] = (float) $row['net_total_amount'];
                    }
                } else {
                    $summary_text .= "No sales return data found.\n";
                }
                $summary_text .= "\n";
            }

            if ($report_type === 'purchase') {
                $purchase_data = $this->purchase_model->get_purchase_summary($date_from, $date_to);
                $summary_text .= "📦 **Purchase Summary** ($date_from to $date_to):\n";
                foreach ($purchase_data as $row) {
                    $summary_text .= "Date: {$row->purchase_date}, Product Name: {$row->product_name}, Qty: {$row->total_qty}, Cost: {$row->total_amount} BDT\n";
                    $all_dates[] = $row->purchase_date;
                    $chart_raw['purchases'][$row->purchase_date] = (float) $row->total_amount;
                }
                $summary_text .= "\n";
            }

            if ($report_type === 'supplier_return') {
                $supplier_return_data = $this->report_model->supplier_return($date_from, $date_to);
                $summary_text .= "↩️ **Supplier Return Summary** ($date_from to $date_to):\n";
                if (!empty($supplier_return_data)) {
                    foreach ($supplier_return_data as $row) {
                        $summary_text .= "Date: {$row['date_return']}, Supplier: {$row['supplier_name']}, Return Amount: {$row['net_total_amount']} BDT\n";
                        $all_dates[] = $row['date_return'];
                        $chart_raw['supplier_return'][$row['date_return']] = (float) $row['net_total_amount'];
                    }
                } else {
                    $summary_text .= "No supplier return data found.\n";
                }
                $summary_text .= "\n";
            }

            if ($report_type === 'stock') {
                $stock_data = $this->report_model->bdtask_getStock([
                    'draw' => 1,
                    'start' => 0,
                    'length' => 1000,
                    'order' => [['column' => 0, 'dir' => 'asc']],
                    'columns' => [['data' => 'product_name']],
                    'search' => ['value' => '']
                ]);

                $summary_text .= "📦 **Stock Summary** (as of $date_to):\n";
                $total_stock = 0;

                if (!empty($stock_data['aaData'])) {
                    foreach ($stock_data['aaData'] as $row) {
                        $summary_text .= "Product: {$row['product_name']} ({$row['product_model']}), Stock: {$row['stok_quantity']}, Sales Qty: {$row['totalSalesQnty']}, Purchase Qty: {$row['totalPurchaseQnty']}, Purchase Price: {$row['purchase_p']} BDT, Sales Price: {$row['sales_price']} BDT\n";
                        $total_stock += (float) $row['stok_quantity'];
                    }
                    $chart_raw['stock'][$date_to] = $total_stock;
                    $all_dates[] = $date_to;
                } else {
                    $summary_text .= "No stock data found.\n";
                }

                $summary_text .= "\n";
            }

            if ($report_type === 'table_summary') {
                $summary_text .= "✅ Table summary requested. Please analyze the above sales, purchase, stock, return summaries and provide your response in a clean table format with clear headings and data. Respond only with the table (either in markdown or JSON with headers and rows).\n";
            }
        }

        // Unique date labels sorted
        $labels = array_values(array_unique($all_dates));
        sort($labels);

        // Chart Instruction
        $label_string = implode('", "', $labels);
        $instruction = $include_chart
            ? "Please analyze the following business data and return a valid JSON object with date labels like:\n\n{\"labels\": [\"$label_string\"], \"sales\": [...], ...}\n\nAnd also give insights."
            : "Please analyze the following business data and give me only insights (no chart JSON needed).";

        $final_prompt = $instruction . "\n\n" . $user_input . "\n\n" . $summary_text;

        $ai_response = $this->call_openai($final_prompt);

        // Parse AI chart JSON if present
        $chart_data = [
            'labels' => [],
            'sales' => [],
            'purchases' => [],
            'stock' => [],
            'sales_return' => [],
            'supplier_return' => []
        ];

        if ($include_chart) {
            $json_start = strpos($ai_response, '{');
            $json_end = strrpos($ai_response, '}');
            if ($json_start !== false && $json_end !== false) {
                $json_string = substr($ai_response, $json_start, $json_end - $json_start + 1);
                $decoded = json_decode($json_string, true);
                if ($decoded && isset($decoded['labels'])) {
                    $chart_data = $decoded;
                }
            }
        }

        echo json_encode([
            'status' => 'success',
            'response' => $ai_response,
            'chart_data' => $include_chart ? $chart_data : null,
            'table_data' => $include_table ? $table_data : null
        ]);
        exit;
    }



    function call_openai($prompt)
    {
        $ci = &get_instance();
        $settings = $this->db->query("select * from ai_setting")->row();

        $api_key = $settings->api_key;
        $model = $settings->model;

        $temperature = floatval($settings->temperature);
        if ($temperature > 2 || $temperature < 0) {
            $temperature = 0.7;
        }

        $max_tokens = intval($settings->max_tokens);

        $postData = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a business analyst.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => $temperature,
            'max_tokens' => $max_tokens
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // DEBUG LOGGING
        file_put_contents(FCPATH . 'openai_log.txt', "HTTP Code: $http_code\nPrompt: $prompt\nResponse:\n$result");

        $response = json_decode($result, true);
        return $response['choices'][0]['message']['content'] ?? "AI response failed.";
    }

    // Delete Report Type

    public function delete_report_type()
    {
        $name = $this->input->post('name');
        if ($name) {
            $this->db->where('name', $name);
            $deleted = $this->db->delete('ai_report_types'); // টেবিলের নাম এখানে বসাবে

            if ($deleted) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No report type specified.']);
        }
    }
}
