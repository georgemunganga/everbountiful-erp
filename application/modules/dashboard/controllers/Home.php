<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.greenwebb.tech/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Home extends MX_Controller
{
    private $allowed_ranges = array('today','last_week','this_month','last_month','this_year','last_year');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard/Home_model', 'home_model');
        $this->load->model('livestock/Livestock_model', 'livestock_model');
        $this->load->model('inventory/Inventory_model', 'inventory_model');
        $this->load->model('customer/customer_model', 'customer_model');
        $this->db->query('SET SESSION sql_mode = ""');

        if (!$this->session->userdata('isLogIn')) {
            redirect('login');
        }
    }

    public function index()
    {
        $range = trim($this->input->get('range', true));
        if (!in_array($range, $this->allowed_ranges, true)) {
            $range = 'this_month';
        }

        list($range_start, $range_end, $range_label) = $this->resolve_date_range($range);

        // Optional custom date range override via GET ?from=YYYY-MM-DD&to=YYYY-MM-DD
        $from = trim($this->input->get('from', true));
        $to   = trim($this->input->get('to', true));
        if (!empty($from) && !empty($to)) {
            $from_dt = DateTime::createFromFormat('Y-m-d', $from);
            $to_dt   = DateTime::createFromFormat('Y-m-d', $to);
            if ($from_dt && $from_dt->format('Y-m-d') === $from && $to_dt && $to_dt->format('Y-m-d') === $to) {
                if ($from_dt <= $to_dt) {
                    $range_start = $from_dt->format('Y-m-d');
                    $range_end   = $to_dt->format('Y-m-d');
                    $range_label = 'Custom';
                }
            }
        }

        // Access the model through the models array
        $best_sales_product = $this->home_model->best_sales_products();

        $sales_report        = $this->home_model->todays_total_sales_report();
        $salesamount         = $this->home_model->todays_total_sales_amount();
        $todays_sale_product = $this->home_model->todays_sale_product();
        $purchase_report     = $this->home_model->todays_total_purchase_report();
        $tlvmonth            = '';
        $month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        for ($i = 0; $i <= 11; $i++) {
            $tlvmonth .=  $month[$i] . ',';
        }
        $currentyearsale = '';
        for ($i = 1; $i <= 12; $i++) {
            $sold = $this->home_model->yearly_invoice_report($i);
            if (!empty($sold)) {
                $currentyearsale .= $sold->total_sale . ",";
            } else {
                $currentyearsale .= ",";
            }
        }

        $currentyearpurchase = '';
        for ($i = 1; $i <= 12; $i++) {
            $purchase = $this->home_model->yearly_purchase_report($i);
            if (!empty($purchase)) {
                $currentyearpurchase .= $purchase->total_purchase . ",";
            } else {
                $currentyearpurchase .= ",";
            }
        }

        $chart_label = $chart_data = '';
        if (!empty($best_sales_product))
            for ($i = 0; $i < 12; $i++) {
                $chart_label .= (!empty($best_sales_product[$i]) ?  $best_sales_product[$i]->product_name . ', ' : null);
                $chart_data .= (!empty($best_sales_product[$i]) ? $best_sales_product[$i]->quantity . ', ' : null);
            }

        // Ranged metrics for dashboard cards
        $total_sales_amount_range = $this->sum_sales_amount_between($range_start, $range_end);
        $total_sales_count_range  = $this->count_sales_between($range_start, $range_end);
        $production_summary       = $this->livestock_model->get_production_percentage_summary(null, $range_start, $range_end);
        $total_production_range   = (float) ($production_summary['total_output'] ?? 0);
        $total_mortality_range    = (float) ($production_summary['total_mortality'] ?? 0);
        $available_lots           = $this->inventory_model->get_available_lots();
        $available_products_count = 0;
        $total_available_qty      = 0.0;
        if (!empty($available_lots)) {
            $by_product = array();
            foreach ($available_lots as $lot) {
                $pid = (string) ($lot['product_id'] ?? '');
                $by_product[$pid] = true;
                $total_available_qty += (float) ($lot['available_qty'] ?? 0);
            }
            $available_products_count = count($by_product);
        }

        // Total feed stock from feed_usages (instock qty). Considered as current snapshot
        $total_feed_instock = $this->sum_feed_instock_between(null, null); // current overall

        // Last two purchased products within range (fallback to global if none)
        $last_two_purchased = $this->get_last_two_purchased_products($range_start, $range_end);
        if (empty($last_two_purchased)) {
            $last_two_purchased = $this->get_last_two_purchased_products(null, null);
        }

        // Latest consumption entry (stock consumption)
        $latest_consumption = $this->get_latest_consumption_within($range_start, $range_end);

        $customers_owing_count   = (int) $this->customer_model->count_credit_customer();
        $customers_you_owe_count = (int) $this->count_customers_you_owe();

        $data['title']         = display('home');
        $data = array(
            'title'                => display('dashboard'),
            'total_customer'       => $this->db->count_all('customer_information'),
            'total_product'        => $this->db->count_all('product_information'),
            'total_suppliers'      => $this->db->count_all('supplier_information'),
            'tlvmonthsale'         => $currentyearsale,
            'tlvmonthpurchase'     => $currentyearpurchase,
            'month'                => $tlvmonth,
            'total_sales'          => $this->db->count_all('invoice'),
            'todays_sales_report'  => $this->home_model->todays_sales_report(),
            'todays_sales_due'     => $this->home_model->todays_sales_due(),
            'todays_purchase_due'  => $this->home_model->todays_purchase_due(),
            'chart_label'          => $chart_label,
            'chart_data'           => $chart_data,
            'sales_amount' => number_format(
                is_numeric($salesamount[0]['total_amount'] ?? null) ? $salesamount[0]['total_amount'] : 0,
                2,
                '.',
                ','
            ),
            'todays_sale_product'  =>  $todays_sale_product,
            'todays_total_purchase' => number_format(
                is_numeric($purchase_report[0]['ttl_purchase_amount']) ? $purchase_report[0]['ttl_purchase_amount'] : 0,
                2,
                '.',
                ','
            ),
            // Filters
            'selected_range'        => $range,
            'selected_range_label'  => $range_label,
            'range_start'           => $range_start,
            'range_end'             => $range_end,
            // Extra cards
            'total_sales_count_range'  => (int) $total_sales_count_range,
            'total_sales_amount_range' => number_format((float) $total_sales_amount_range, 2, '.', ','),
            'total_production_range'   => (float) $total_production_range,
            'total_mortality_range'    => (float) $total_mortality_range,
            'total_feed_instock'       => (float) $total_feed_instock,
            'available_products_count' => (int) $available_products_count,
            'total_available_qty'      => (float) $total_available_qty,
            'last_two_purchased'       => $last_two_purchased,
            'latest_consumption'       => $latest_consumption,
            // Customer mini-cards
            'customers_owing_count'    => $customers_owing_count,
            'customers_you_owe_count'  => $customers_you_owe_count,
        );
        $data['module']      = "dashboard";
        $data['page']        = "home/home";

        echo Modules::run('template/layout', $data);
    }



    public function see_all_best_sales()
    {
        $data['title']                   = display('dashboard');
        $data['best_saler_product_list'] = $this->home_model->best_saler_product_list();
        $data['module']                  = "dashboard";
        $data['page']                    = "home/best_saler_product_list";

        echo Modules::run('template/layout', $data);
    }

    private function resolve_date_range($range)
    {
        $today = new DateTime('today');
        $start = null; $end = null; $label = '';
        switch ($range) {
            case 'today':
                $start = clone $today; $end = clone $today; $label = 'Today';
                break;
            case 'last_week':
                $end = clone $today;
                $start = (clone $today)->modify('monday last week');
                $end->modify('sunday last week');
                $label = 'Last Week';
                break;
            case 'this_month':
                $start = new DateTime(date('Y-m-01'));
                $end   = new DateTime(date('Y-m-t'));
                $label = 'This Month';
                break;
            case 'last_month':
                $start = (new DateTime('first day of last month'))->setTime(0,0,0);
                $end   = (new DateTime('last day of last month'))->setTime(0,0,0);
                $label = 'Last Month';
                break;
            case 'this_year':
                $start = new DateTime(date('Y-01-01'));
                $end   = new DateTime(date('Y-12-31'));
                $label = 'This Year';
                break;
            case 'last_year':
                $y = (int) date('Y') - 1;
                $start = new DateTime($y . '-01-01');
                $end   = new DateTime($y . '-12-31');
                $label = 'Last Year';
                break;
            default:
                $start = new DateTime(date('Y-m-01'));
                $end   = new DateTime(date('Y-m-t'));
                $label = 'This Month';
        }
        return array($start->format('Y-m-d'), $end->format('Y-m-d'), $label);
    }

    private function sum_sales_amount_between($start, $end)
    {
        if (empty($start) || empty($end)) return 0.0;
        $row = $this->db->select('COALESCE(SUM(total_amount),0) AS total')
            ->from('invoice')
            ->where('date >=', $start)
            ->where('date <=', $end)
            ->get()->row_array();
        return (float) ($row['total'] ?? 0.0);
    }

    private function count_sales_between($start, $end)
    {
        if (empty($start) || empty($end)) return 0;
        return (int) $this->db->from('invoice')
            ->where('date >=', $start)
            ->where('date <=', $end)
            ->count_all_results();
    }

    private function sum_feed_instock_between($start = null, $end = null)
    {
        $this->db->select('COALESCE(SUM(total_instock_qty),0) AS total')
            ->from('feed_usages');
        if (!empty($start)) {
            $this->db->where('DATE(created_at) >=', $start);
        }
        if (!empty($end)) {
            $this->db->where('DATE(created_at) <=', $end);
        }
        $row = $this->db->get()->row_array();
        return (float) ($row['total'] ?? 0.0);
    }

    private function get_last_two_purchased_products($start = null, $end = null)
    {
        $this->db->select('pi.product_name, pp.purchase_date')
            ->from('product_purchase_details ppd')
            ->join('product_purchase pp', 'pp.purchase_id = ppd.purchase_id', 'left')
            ->join('product_information pi', 'pi.product_id = ppd.product_id', 'left');
        if (!empty($start)) {
            $this->db->where('pp.purchase_date >=', $start);
        }
        if (!empty($end)) {
            $this->db->where('pp.purchase_date <=', $end);
        }
        $rows = $this->db->order_by('pp.purchase_date', 'desc')
            ->limit(2)
            ->get()->result_array();
        return $rows ?: array();
    }

    private function get_latest_consumption_within($start = null, $end = null)
    {
        $this->db->select('sm.movement_date, sm.product_id, sm.quantity_out, pi.product_name, u.unit_name')
            ->from('stock_movements sm')
            ->join('product_information pi', 'pi.product_id = sm.product_id', 'left')
            ->join('units u', 'u.unit_id = sm.unit_id', 'left')
            ->where('sm.reference_type', 'consumption')
            ->where('sm.quantity_out >', 0);
        if (!empty($start)) {
            $this->db->where('DATE(sm.movement_date) >=', $start);
        }
        if (!empty($end)) {
            $this->db->where('DATE(sm.movement_date) <=', $end);
        }
        return $this->db->order_by('sm.movement_date', 'desc')
            ->limit(1)
            ->get()->row_array();
    }

    private function count_customers_you_owe()
    {
        // Customers with negative balance (we owe them)
        $q = $this->db->select("a.customer_id, ((SELECT IFNULL(SUM(Debit),0) FROM acc_transaction WHERE COAID = b.HeadCode AND IsAppove = 1) - (SELECT IFNULL(SUM(Credit),0) FROM acc_transaction WHERE COAID = b.HeadCode AND IsAppove = 1)) AS balance", false)
            ->from('customer_information a')
            ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
            ->group_by('a.customer_id')
            ->having('balance <', 0)
            ->get();
        return $q ? $q->num_rows() : 0;
    }


    public function out_of_stock()
    {
        $data['title']        = display('out_of_stock');
        $data['out_of_stock'] = $this->home_model->out_of_stock();
        $data['module']       = "dashboard";
        $data['page']         = "home/out_of_stock";
        echo Modules::run('template/layout', $data);
    }

    public function setting()
    {
        $data['title']    = "Profile Setting";
        $id = $this->session->userdata('id');
        /*-----------------------------------*/
        $this->form_validation->set_rules('firstname', 'First Name', 'required|max_length[50]');
        $this->form_validation->set_rules('lastname', 'Last Name', 'required|max_length[50]');
        #------------------------#
        $this->form_validation->set_rules('email', 'Email Address', "required|valid_email|max_length[100]");
        $this->form_validation->set_rules('password', 'Password', 'max_length[32]|md5');
        $this->form_validation->set_rules('about', 'About', 'max_length[1000]');
        /*-----------------------------------*/
        $config['upload_path']          = './assets/img/user/';
        $config['allowed_types']        = 'gif|jpg|png';

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image')) {
            $data = $this->upload->data();
            $image = $config['upload_path'] . $data['file_name'];

            $config['image_library']  = 'gd2';
            $config['source_image']   = $image;
            $config['create_thumb']   = false;
            $config['maintain_ratio'] = TRUE;
            $config['width']          = 115;
            $config['height']         = 90;
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();
            $this->session->set_flashdata('message', "Image Upload Successfully!");
        }



        /*-----------------------------------*/
        $data['user'] = (object)$userData = array(
            'id'               => $this->input->post('id'),
            'firstname'   => $this->input->post('firstname'),
            'lastname'       => $this->input->post('lastname'),
            'email'         => $this->input->post('email'),
            'password'       => (!empty($this->input->post('password')) ? md5($this->input->post('password')) : $this->input->post('oldpassword')),
            'about'         => $this->input->post('about', true),
            'image'         => (!empty($image) ? $image : $this->input->post('old_image'))
        );

        /*-----------------------------------*/
        if ($this->form_validation->run()) {

            if ($image === false) {
                $this->session->set_flashdata('exception', display('invalid_image'));
            }


            if ($this->home_model->setting($userData)) {

                $this->session->set_userdata(array(
                    'fullname'   => $this->input->post('firstname') . ' ' . $this->input->post('lastname'),
                    'email'        => $this->input->post('email'),
                    'image'        => (!empty($image) ? $image : $this->input->post('old_image'))
                ));


                $this->session->set_flashdata('message', display('update_successfully'));
            } else {
                $this->session->set_flashdata('exception',  display('please_try_again'));
            }
            redirect("dashboard/home/setting");
        } else {
            $data['module'] = "dashboard";
            $data['page']   = "home/profile_setting";
            if (!empty($id))
                $data['user']   = $this->home_model->profile($id);
            echo Modules::run('template/layout', $data);
        }
    }


    public function profile()
    {

        $edit_data = $this->home_model->profile_edit_data();
        $data = array(
            'title'      => display('update_profile'),
            'first_name' => $edit_data[0]['first_name'],
            'last_name'  => $edit_data[0]['last_name'],
            'user_name'  => $edit_data[0]['username'],
            'logo'       => $edit_data[0]['logo']
        );
        $data['title']  = "Profile";
        $data['module'] = "dashboard";
        $data['page']   = "home/edit_profile";
        echo Modules::run('template/layout', $data);
    }


    public function update_profile()
    {
        $this->form_validation->set_rules('first_name', display('first_name'), 'required|max_length[50]');
        $this->form_validation->set_rules('last_name', display('last_name'), 'required|max_length[50]');
        $this->form_validation->set_rules('user_name', display('email'), 'required|max_length[50]');
        if ($this->form_validation->run()) {
            $this->home_model->profile_update();
            $this->session->set_flashdata(array('message' => display('successfully_updated')));
        } else {
            $this->session->set_flashdata(array('exception' => validation_errors()));
        }
        redirect(base_url('edit_profile'));
    }

    #=============Change Password=========# 

    public function change_password_form()
    {
        $data['title']  = "Change Password";
        $data['module'] = "dashboard";
        $data['page']   = "home/change_password";
        echo Modules::run('template/layout', $data);
    }


    public function change_password()
    {
        $error        = '';
        $email        = $this->input->post('email', TRUE);
        $old_password = $this->input->post('old_password', TRUE);
        $new_password = $this->input->post('password', TRUE);
        $repassword   = $this->input->post('repassword', TRUE);

        if ($email == '' || $old_password == '' || $new_password == '') {
            $error = display('blank_field_does_not_accept');
        } else if ($email != $this->session->userdata('email')) {
            $error = display('you_put_wrong_email_address');
        } else if (strlen($new_password) < 6) {
            $error = display('new_password_at_least_six_character');
        } else if ($new_password != $repassword) {
            $error = display('password_and_repassword_does_not_match');
        } else if ($this->home_model->change_password($email, $old_password, $new_password) === FALSE) {
            $error = display('you_are_not_authorised_person');
        }

        if ($error != '') {
            $this->session->set_flashdata(array('exception' => $error));
            redirect(base_url('change_password'));
        } else {
            $this->session->set_flashdata(array('message' => display('successfully_changed_password')));
            redirect(base_url('change_password'));
        }
    }
}
