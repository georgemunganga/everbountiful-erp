<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DemoRestriction {

    public function check_request_method() {
        if (defined('DEMO_MODE') && DEMO_MODE === true) {
            $CI =& get_instance();
            $CI->load->library('session');
    
            $method = $_SERVER['REQUEST_METHOD'] ?? '';
            $current_url = $_SERVER['REQUEST_URI'] ?? '';
    
            // âœ… Allow GET requests and safe POSTs for data fetch
            $allowed_urls = array();$current_url_lc = strtolower($current_url);
            foreach ($allowed_urls = array();as $allowed_url) {
                if (strpos($current_url_lc, strtolower($allowed_url)) !== false) {
                    return; // Allow this request
                }
            }
    
            // âŒ Block data modification attempts
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





