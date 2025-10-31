<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author:Greenwebb Ltd
    # Author link: https://www.Greenwebb.tech/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    

class Customer extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
  
        $this->load->model(array(
            'customer_model',
            'customergroups_model' => 'CustomerGroups_model',
            'template/template_model' => 'template_model',
            'account/Accounts_model' => 'accounts_model'
        )); 
        if (! $this->session->userdata('isLogIn'))
            redirect('login');
          
    }
    
    function index() {
        $data['title']             = display('customer_list');
        $data['module']            = "customer";
        $data['page']              = "customer_list"; 
        $data["customer_dropdown"] = $this->customer_model->customer_dropdown();
        $data['all_customer']      = $this->customer_model->allcustomer(); 
        // Dashboard mini-card metrics now shown on customer list page
        $data['total_customer']          = (int) $this->db->count_all('customer_information');
        $data['customers_owing_count']   = (int) $this->customer_model->count_credit_customer();
        $data['customers_you_owe_count'] = (int) $this->count_customers_you_owe();
        echo modules::run('template/layout', $data);
    }


    public function bdtask_CheckCustomerList(){
        $postData = $this->input->post();
        $data     = $this->customer_model->getCustomerList($postData);
        
        echo json_encode($data);
       
    }

     public function bdtask_credit_customer() {
        $data['title']             = 'Owing Customers';
        $data['module']            = "customer";
        $data['page']              = "credit_customer"; 
        $data["customer_dropdown"] = $this->customer_model->bd_task_credit_customer_dropdown();
        $data['all_customer']      = $this->customer_model->bd_task_all_credit_customer(); 
        echo modules::run('template/layout', $data);
    }

     public function bdtask_CheckCreditCustomerList(){

        $postData = $this->input->post();
        $data = $this->customer_model->getCreditCustomerList($postData);
        echo json_encode($data);
    } 

    //Paid Customer list. The customer who will pay 100%.
    public function bdtask_paid_customer() {
        $data['title']             = display('paid_customer');
        $data['module']            = "customer";
        $data['page']              = "paid_customer"; 
        $data["customer_dropdown"] = $this->customer_model->bd_task_paid_customer_dropdown();
        $data['all_customer']      = $this->customer_model->bd_task_all_paid_customer(); 
        echo modules::run('template/layout', $data);
        
    }
    
    public function bdtask_CheckPaidCustomerList(){
        // GET data
        $postData = $this->input->post();
        $data = $this->customer_model->bd_task_getPaidCustomerList($postData);
        echo json_encode($data);
    } 

    private function count_customers_you_owe()
    {
        $q = $this->db->select("a.customer_id, ((SELECT IFNULL(SUM(Debit),0) FROM acc_transaction WHERE COAID = b.HeadCode AND IsAppove = 1) - (SELECT IFNULL(SUM(Credit),0) FROM acc_transaction WHERE COAID = b.HeadCode AND IsAppove = 1)) AS balance", false)
            ->from('customer_information a')
            ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
            ->group_by('a.customer_id')
            ->having('balance <', 0)
            ->get();
        return $q ? $q->num_rows() : 0;
    }


  public function bdtask_form($id = null)
    {
        // dd("Testing customer");
        $data['title'] = display('add_customer');
        #-------------------------------#
        $this->form_validation->set_rules('customer_name',display('customer_name'),'required|max_length[200]');
        $this->form_validation->set_rules('customer_mobile', display('customer_mobile') ,'max_length[20]');
        if(empty($id)){
        $this->form_validation->set_rules('customer_email',display('email'),'max_length[100]|valid_email|is_unique[customer_information.customer_email]');
    }else{
        $this->form_validation->set_rules('customer_email',display('email'),'max_length[100]|valid_email');
    }
        $this->form_validation->set_rules('contact',display('contact'),'max_length[200]');
        $this->form_validation->set_rules('phone',display('phone'),'max_length[20]');
        $this->form_validation->set_rules('city',display('city'),'max_length[100]'); 
        $this->form_validation->set_rules('state',display('state'),'max_length[100]');
        $this->form_validation->set_rules('zip',display('zip'),'max_length[30]');
        $this->form_validation->set_rules('country',display('country'),'max_length[100]');  
        $this->form_validation->set_rules('customer_address',display('customer_address'),'max_length[255]');
        $this->form_validation->set_rules('address2',display('address2'),'max_length[255]'); 
        #-------------------------------#
        
        $data['customer'] = (object)$postData = [
            'customer_id'      => $this->input->post('customer_id',true),
            'customer_name'    => $this->input->post('customer_name',true),
            'customer_mobile'  => $this->input->post('customer_mobile', true),
            'customer_email'   => $this->input->post('customer_email', true),
            'email_address'    => $this->input->post('email_address', true),
            'contact'          => $this->input->post('contact', true),
            'phone'            => $this->input->post('phone', true),
            'fax'              => $this->input->post('fax', true), 
            'city'             => $this->input->post('city', true) ,
            'state'            => $this->input->post('state', true) ,
            'zip'              => $this->input->post('zip', true) ,
            'country'          => $this->input->post('country', true) ,
            'customer_address' => $this->input->post('customer_address', true) ,
            'address2'         => $this->input->post('address2', true) ,
            'group_id'         => $this->input->post('group_id', true) ,
            'status'           => 1,
            'create_by'        => $this->session->userdata('id') ,
            
        ]; 
        #-------------------------------#
        if ($this->form_validation->run() === true) {
            #if empty $id then insert data
            if (empty($postData['customer_id'])) {
                if ($this->customer_model->create($postData)) {
                    #set success message
                        $info['msg']    = display('save_successfully');
                        $info['status'] = 1;
                } else {
                    #set exception message
                        $info['msg']    = display('please_try_again');
                        $info['status'] = 0;
                }
            } else {
                if ($this->customer_model->update($postData)) {
                    #set success message
                    $info['msg']    = display('update_successfully');
                    $info['status'] = 1;
                } else {
                    #set exception message
                    $info['msg']    = display('please_try_again');
                    $info['status'] = 0;
                } 
            }
 
            echo json_encode($info);

        } else { 
            if(empty($this->input->post('customer_name',true))){
            if(!empty($id)){
            $data['title']    = display('edit_customer');
            $data['customer'] = $this->customer_model->singledata($id);  
            }
            $data['groups']   = $this->CustomerGroups_model->get_dropdown();
            $data['module']   = "customer";  
            $data['page']     = "form";  
            echo Modules::run('template/layout', $data); 
        }else{

          $info['msg']    = validation_errors();
          $info['status'] = 0;
           echo json_encode($info);
        }
        } 
    }



    public function bdtask_delete($id) {
        if ($this->customer_model->delete($id)) {
            echo display('delete_successfully');
        } else {
            display('please_try_again');
        }
    }

    public function customer_search($id){
       $data["customers"] = $this->customer_model->individual_info($id);
        $this->load->view('customer_search', $data);
    }

    public function bdtask_customer_ledger() {
    $data['title']             = display('customer_ledger'); 
    $config["base_url"]        = base_url('customer_ledger');
    $config["total_rows"]      = $this->customer_model->count_customer_ledger();
    $config["per_page"]        = 10;
    $config["uri_segment"]     = 2;
    $config["last_link"]       = "Last"; 
    $config["first_link"]      = "First"; 
    $config['next_link']       = 'Next';
    $config['prev_link']       = 'Prev';  
    $config['full_tag_open']   = "<ul class='pagination col-xs pull-right'>";
    $config['full_tag_close']  = "</ul>";
    $config['num_tag_open']    = '<li>';
    $config['num_tag_close']   = '</li>';
    $config['cur_tag_open']    = "<li class='disabled'><li class='active'><a href='#'>";
    $config['cur_tag_close']   = "<span class='sr-only'></span></a></li>";
    $config['next_tag_open']   = "<li>";
    $config['next_tag_close']  = "</li>";
    $config['prev_tag_open']   = "<li>";
    $config['prev_tagl_close'] = "</li>";
    $config['first_tag_open']  = "<li>";
    $config['first_tagl_close']= "</li>";
    $config['last_tag_open']   = "<li>";
    $config['last_tagl_close'] = "</li>";
    $this->pagination->initialize($config);
    $page                      = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
    $data["ledgers"]           = $this->customer_model->customer_ledgerdata($config["per_page"], $page);
    $data["links"]             = $this->pagination->create_links();
    $data['customer']          = $this->customer_model->customer_list_ledger();
    $data['customer_name']     = '';
    $data['customer_id']       = '';
    $data['address']           ='';
    $data['module']            = "customer";
    $data['page']              = "customer_ledger";   
    echo Modules::run('template/layout', $data); 
    }

    public function bdtask_customer_ledgerData() {
    $start                 = $this->input->post('from_date',true);
    $end                   = $this->input->post('to_date',true);
    $customer_id           = $this->input->post('customer_id',true);
    $customer_detail       = $this->customer_model->customer_personal_data($customer_id);
    $data['title']         = display('customer_ledger');
    $data['customer']      = $this->customer_model->customer_list_ledger();
    $data["ledgers"]       = $this->customer_model->customerledger_searchdata($customer_id, $start, $end);
    $data['customer_name'] = $customer_detail[0]['customer_name'];
    $data['customer_id']   = $customer_id;
    $data['address']       = $customer_detail[0]['customer_address'];
    $data['module']        = "customer";
    $data["links"]         = '';
    $data['page']          = "customer_ledger";   
    echo Modules::run('template/layout', $data); 
    }


    public function bdtask_customer_advance() {
    $data['title']        = display('customer_advance');    
    $data['customer_list']= $this->customer_model->customer_list_advance();
    $data['module']       = "customer";
    $data['page']         = "customer_advance";   
    echo Modules::run('template/layout', $data); 
    }

    // Customer Groups: list
    public function customer_groups()
    {
        $data['title']  = 'Customer Groups';
        $data['groups'] = $this->CustomerGroups_model->list_all();
        $data['module'] = 'customer';
        $data['page']   = 'customer_group_list';
        echo Modules::run('template/layout', $data);
    }

    // Customer Groups: create/edit form
    public function customer_group_form($id = null)
    {
        $data['title']  = empty($id) ? 'Add Group' : 'Edit Group';
        $data['group']  = !empty($id) ? $this->CustomerGroups_model->get($id) : null;
        $data['module'] = 'customer';
        $data['page']   = 'customer_group_form';
        echo Modules::run('template/layout', $data);
    }

    // Customer Groups: save
    public function customer_group_save()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('group_name', 'Group Name', 'required|trim|max_length[150]');

        $id   = (int) $this->input->post('id', true);
        $data = [
            'group_name' => $this->input->post('group_name', true),
            'description'=> $this->input->post('description', true),
            'is_active'  => $this->input->post('is_active', true) ? 1 : 0,
        ];

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('exception', validation_errors());
            if ($id > 0) {
                redirect('customer/customer_group_form/' . $id);
            } else {
                redirect('customer/customer_group_form');
            }
            return;
        }

        $ok = false;
        if ($id > 0) {
            $ok = $this->CustomerGroups_model->update($id, $data);
        } else {
            $ok = $this->CustomerGroups_model->create($data);
        }

        if ($ok) {
            $this->session->set_flashdata('message', 'Group saved successfully');
        } else {
            $db_err = $this->db->error();
            $msg = !empty($db_err['message']) ? $db_err['message'] : 'Unable to save group';
            $this->session->set_flashdata('exception', $msg);
        }
        redirect('customer/customer_groups');
    }

    // Customer Groups: delete
    public function customer_group_delete($id)
    {
        $this->CustomerGroups_model->delete($id);
        redirect('customer/customer_groups');
    }

    // Customer detail view with tabs
    public function customer_detail($customer_id)
    {
        // Gracefully handle accidental extra slash paths like /customer_detail//10
        if (empty($customer_id) || !is_numeric($customer_id)) {
            $seg4 = $this->uri->segment(4);
            if (!empty($seg4) && is_numeric($seg4)) {
                $customer_id = (int)$seg4;
            }
        }
        if (empty($customer_id)) {
            $this->session->set_flashdata("exception", "Invalid or missing customer id.");
            redirect("customer_list");
            return;
        }
        $customer = $this->customer_model->singledata($customer_id);
        if (!$customer) {
            show_404();
            return;
        }
        // Date range for statement
        $from = $this->input->get('from_date', true);
        $to   = $this->input->get('to_date', true);
        $range = strtolower(trim($this->input->get('range', true)));
        $active_group = strtolower(trim($this->input->get('group', true)));
        if (empty($from) || empty($to)) {
            // derive from 'range' if provided
            if (!empty($range)) {
                $today = new DateTime('today');
                switch ($range) {
                    case 'today':
                        $start = clone $today; $end = clone $today; break;
                    case 'this_week':
                        $start = (clone $today)->modify('monday this week');
                        $end   = (clone $start)->modify('sunday this week');
                        break;
                    case 'last_week':
                        $start = (clone $today)->modify('monday last week');
                        $end   = (clone $start)->modify('sunday next week')->modify('-1 week');
                        break;
                    case 'this_month':
                        $start = new DateTime(date('Y-m-01'));
                        $end   = new DateTime(date('Y-m-t'));
                        break;
                    case 'last_month':
                        $start = new DateTime('first day of last month');
                        $end   = new DateTime('last day of last month');
                        break;
                    case 'this_year':
                        $start = new DateTime(date('Y-01-01'));
                        $end   = new DateTime(date('Y-12-31'));
                        break;
                    case 'last_year':
                        $y = (int) date('Y') - 1;
                        $start = new DateTime($y.'-01-01');
                        $end   = new DateTime($y.'-12-31');
                        break;
                    case 'all_time':
                        $start = new DateTime('1970-01-01');
                        $end   = clone $today;
                        break;
                    default:
                        $start = new DateTime(date('Y-m-01'));
                        $end   = new DateTime(date('Y-m-t'));
                }
                $from = $start->format('Y-m-d');
                $to   = $end->format('Y-m-d');
            } else {
                // default current month
                $from = date('Y-m-01');
                $to   = date('Y-m-t');
            }
        }

        $data['title']     = 'Customer Details';
        $data['customer']  = $customer;
        $data['contacts']  = $this->customer_model->get_customer_contacts($customer_id);
        $data['credit_notes'] = $this->customer_model->get_credit_notes($customer_id);
        $data['estimates'] = $this->customer_model->get_estimates($customer_id);
        $data['expenses']  = $this->customer_model->get_expenses($customer_id);
        $data['contact_form_data'] = $this->session->flashdata('contact_form_data') ?: array();
        $data['credit_note_form_data'] = $this->session->flashdata('credit_note_form_data') ?: array();
        $data['estimate_form_data'] = $this->session->flashdata('estimate_form_data') ?: array();
        $data['expense_form_data'] = $this->session->flashdata('expense_form_data') ?: array();
        // Payments list for Payments tab
        $receipts_from = ($active_group === 'payments') ? null : $from;
        $receipts_to   = ($active_group === 'payments') ? null : $to;
        $data['customer_receipts'] = $this->customer_model->get_customer_receipts($customer_id, $receipts_from, $receipts_to);
        // Build group summaries for Payments tab (lumpsum group payments)
        $payment_groups = array();
        if (!empty($data['customer_receipts']) && is_array($data['customer_receipts'])) {
            foreach ($data['customer_receipts'] as $rc) {
                $label = isset($rc['group_note']) ? trim((string)$rc['group_note']) : '';
                if ($label !== '' && stripos($label, 'Group Payment') === 0) {
                    if (!isset($payment_groups[$label])) {
                        $payment_groups[$label] = array(
                            'label' => $label,
                            'date' => isset($rc['date']) ? $rc['date'] : '',
                            'total' => 0.0,
                            'items' => array(),
                        );
                    }
                    // keep earliest date for the group
                    if (!empty($rc['date']) && strtotime($rc['date']) < strtotime($payment_groups[$label]['date'])) {
                        $payment_groups[$label]['date'] = $rc['date'];
                    }
                    $amt = isset($rc['amount']) ? (float)$rc['amount'] : 0.0;
                    $payment_groups[$label]['total'] += $amt;
                    $payment_groups[$label]['items'][] = array(
                        'invoice_id' => isset($rc['invoice_id']) ? $rc['invoice_id'] : null,
                        'amount' => $amt,
                        'date' => isset($rc['date']) ? $rc['date'] : '',
                        'voucher' => isset($rc['VNo']) ? $rc['VNo'] : '',
                    );
                }
            }
            // Fallback batch grouping for older vouchers without the group label
            $buckets = array();
            foreach ($data['customer_receipts'] as $rc) {
                $label = isset($rc['group_note']) ? trim((string)$rc['group_note']) : '';
                if ($label !== '' && stripos($label, 'Group Payment') === 0) { continue; }
                $cd = isset($rc['create_dt']) ? trim((string)$rc['create_dt']) : '';
                if ($cd === '') { continue; }
                $key = $cd;
                if (!isset($buckets[$key])) {
                    $buckets[$key] = array(
                        'date' => isset($rc['date']) ? $rc['date'] : '',
                        'total' => 0.0,
                        'items' => array(),
                    );
                }
                $amt = isset($rc['amount']) ? (float)$rc['amount'] : 0.0;
                $buckets[$key]['total'] += $amt;
                $buckets[$key]['items'][] = array(
                    'invoice_id' => isset($rc['invoice_id']) ? $rc['invoice_id'] : null,
                    'amount' => $amt,
                    'date' => isset($rc['date']) ? $rc['date'] : '',
                    'voucher' => isset($rc['VNo']) ? $rc['VNo'] : '',
                );
            }
            foreach ($buckets as $key => $bk) {
                if (count($bk['items']) < 2 || $bk['total'] <= 0.0001) { continue; }
                $label2 = 'Batch @ '.date('Y-m-d H:i', strtotime($key));
                if (!isset($payment_groups[$label2])) {
                    $payment_groups[$label2] = array(
                        'label' => $label2,
                        'date'  => $bk['date'],
                        'total' => $bk['total'],
                        'items' => $bk['items'],
                    );
                }
            }
        }
        $data['payment_groups'] = $payment_groups;
        $invoice_rows_all = $this->customer_model->get_customer_invoices($customer_id);
        $prepared_invoices = array();
        if (!empty($invoice_rows_all)) {
            foreach ($invoice_rows_all as $row) {
                $total = isset($row['total_amount']) ? (float) $row['total_amount'] : 0.0;
                $paid  = isset($row['paid_amount']) ? (float) $row['paid_amount'] : 0.0;
                $due   = isset($row['due_amount']) ? (float) $row['due_amount'] : max(0.0, $total - $paid);
                $status_flag = isset($row['invoice_status']) ? (int)$row['invoice_status'] : null;
                $status = 'Unpaid';
                $status_class = 'label label-danger';
                $epsilon = 0.01;
                if ($status_flag === 1 && $due <= $epsilon) {
                    $status = 'Paid';
                    $status_class = 'label label-success';
                    $due = 0.0;
                } elseif ($due <= $epsilon) {
                    $status = 'Paid';
                    $status_class = 'label label-success';
                    $due = 0.0;
                } elseif ($paid > $epsilon && $due > $epsilon) {
                    $status = 'Partially Paid';
                    $status_class = 'label label-warning';
                }
                $row['total_amount'] = $total;
                $row['paid_amount']  = $paid;
                $row['due_amount']   = $due;
                $row['status_label'] = $status;
                $row['status_class'] = $status_class;
                $prepared_invoices[] = $row;
            }
        }
        $data['invoices']  = $prepared_invoices;
        // For payments tab, default to all-time (no date restriction) so older payments are visible
        $payments_from = $from;
        $payments_to   = $to;
        if ($active_group === 'payments') {
            $payments_from = null; $payments_to = null;
        }
        $data['payments']  = $this->customer_model->get_customer_payments($customer_id, $payments_from, $payments_to);
        $data['notes']     = $this->customer_model->get_notes($customer_id);
        $data['reminders'] = $this->customer_model->get_reminders($customer_id);
        $data['files']     = $this->customer_model->get_files($customer_id);
        $data['from_date'] = $from;
        $data['to_date']   = $to;
        $data['range']     = $range;
        $data['statement'] = $this->customer_model->get_customer_statement($customer_id, $from, $to);
        $setting           = $this->template_model->setting();
        if ($setting) {
            $data['currency'] = !empty($setting->currency_symbol) ? $setting->currency_symbol : (!empty($setting->currency) ? $setting->currency : 'ZMW');
            $data['position'] = isset($setting->currency_position) ? (int) $setting->currency_position : 0;
        } else {
            $data['currency'] = 'ZMW';
            $data['position'] = 0;
        }

        $data['module']   = 'customer';
        $data['page']     = 'customer_detail_tabs';
        // Payment methods for inline payment modal
        $data['pay_methods'] = $this->accounts_model->pmethod_dropdown();

        echo Modules::run('template/layout', $data); 
    }

    public function add_contact($customer_id)
    {
        $this->form_validation->set_rules('name', 'Contact Name', 'required|max_length[255]');
        $this->form_validation->set_rules('email', 'Email', 'max_length[255]');
        $this->form_validation->set_rules('phone', 'Phone', 'max_length[100]');
        $this->form_validation->set_rules('position', 'Position', 'max_length[255]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('exception', validation_errors());
            $this->session->set_flashdata('contact_form_data', $this->input->post(null, true));
            redirect('customer/customer/customer_detail/' . $customer_id . '?tab=contacts');
            return;
        }

        $contact_data = array(
            'name'     => $this->input->post('name', true),
            'email'    => $this->input->post('email', true),
            'phone'    => $this->input->post('phone', true),
            'position' => $this->input->post('position', true),
        );

        $this->customer_model->add_customer_contact($customer_id, $contact_data);
        $this->session->set_flashdata('message', 'Contact saved successfully.');
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=contacts');
    }

    public function delete_contact($customer_id, $contact_id)
    {
        if ($this->customer_model->delete_customer_contact($customer_id, $contact_id)) {
            $this->session->set_flashdata('message', 'Contact deleted.');
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
        }
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=contacts');
    }

    public function add_credit_note($customer_id)
    {
        $this->form_validation->set_rules('number', 'Credit Note Number', 'required|max_length[100]');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('status', 'Status', 'max_length[50]');
        $this->form_validation->set_rules('remarks', 'Remarks', 'max_length[500]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('exception', validation_errors());
            $this->session->set_flashdata('credit_note_form_data', $this->input->post(null, true));
            redirect('customer/customer/customer_detail/' . $customer_id . '?tab=credit_notes');
            return;
        }

        $note_data = array(
            'number'  => $this->input->post('number', true),
            'date'    => $this->input->post('date', true),
            'amount'  => $this->input->post('amount', true),
            'status'  => $this->input->post('status', true),
            'remarks' => $this->input->post('remarks', true),
        );

        if ($this->customer_model->add_credit_note($customer_id, $note_data)) {
            $this->session->set_flashdata('message', 'Credit note saved.');
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
            $this->session->set_flashdata('credit_note_form_data', $note_data);
        }
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=credit_notes');
    }

    public function delete_credit_note($customer_id, $note_id)
    {
        if ($this->customer_model->delete_credit_note($customer_id, $note_id)) {
            $this->session->set_flashdata('message', 'Credit note deleted.');
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
        }
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=credit_notes');
    }

    public function add_estimate($customer_id)
    {
        $this->form_validation->set_rules('number', 'Estimate Number', 'required|max_length[100]');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('status', 'Status', 'max_length[50]');
        $this->form_validation->set_rules('notes', 'Notes', 'max_length[500]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('exception', validation_errors());
            $this->session->set_flashdata('estimate_form_data', $this->input->post(null, true));
            redirect('customer/customer/customer_detail/' . $customer_id . '?tab=estimates');
            return;
        }

        $estimate_data = array(
            'number' => $this->input->post('number', true),
            'date'   => $this->input->post('date', true),
            'amount' => $this->input->post('amount', true),
            'status' => $this->input->post('status', true),
            'notes'  => $this->input->post('notes', true),
        );

        if ($this->customer_model->add_estimate($customer_id, $estimate_data)) {
            $this->session->set_flashdata('message', 'Estimate saved.');
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
            $this->session->set_flashdata('estimate_form_data', $estimate_data);
        }
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=estimates');
    }

    public function delete_estimate($customer_id, $estimate_id)
    {
        if ($this->customer_model->delete_estimate($customer_id, $estimate_id)) {
            $this->session->set_flashdata('message', 'Estimate deleted.');
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
        }
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=estimates');
    }

    public function add_expense($customer_id)
    {
        $this->form_validation->set_rules('category', 'Category', 'required|max_length[100]');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required|numeric');
        $this->form_validation->set_rules('vendor', 'Vendor', 'max_length[150]');
        $this->form_validation->set_rules('notes', 'Notes', 'max_length[500]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('exception', validation_errors());
            $this->session->set_flashdata('expense_form_data', $this->input->post(null, true));
            redirect('customer/customer/customer_detail/' . $customer_id . '?tab=expenses');
            return;
        }

        $expense_data = array(
            'category' => $this->input->post('category', true),
            'date'     => $this->input->post('date', true),
            'amount'   => $this->input->post('amount', true),
            'vendor'   => $this->input->post('vendor', true),
            'notes'    => $this->input->post('notes', true),
        );

        if ($this->customer_model->add_expense($customer_id, $expense_data)) {
            $this->session->set_flashdata('message', 'Expense saved.');
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
            $this->session->set_flashdata('expense_form_data', $expense_data);
        }
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=expenses');
    }

    public function delete_expense($customer_id, $expense_id)
    {
        if ($this->customer_model->delete_expense($customer_id, $expense_id)) {
            $this->session->set_flashdata('message', 'Expense deleted.');
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
        }
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=expenses');
    }
    // Statement HTML view (optional separate route)
    public function customer_statement($customer_id = null)
    {
        $customerId = $customer_id !== null ? (int) $customer_id : null;
        $selectedId = (int) $this->input->get('customer_id', true);

        if (!$customerId && $selectedId > 0) {
            $query = array();
            $fromParam = $this->input->get('from_date', true);
            $toParam   = $this->input->get('to_date', true);
            if (!empty($fromParam)) {
                $query['from_date'] = $fromParam;
            }
            if (!empty($toParam)) {
                $query['to_date'] = $toParam;
            }
            $queryString = !empty($query) ? '?' . http_build_query($query) : '';
            redirect('customer/customer_statement/' . $selectedId . $queryString);
            return;
        }

        if (!$customerId) {
            $data['title']     = 'Customer Statement';
            $data['module']    = 'customer';
            $data['page']      = 'customer_statement_select';
            $data['customers'] = $this->customer_model->customer_dropdown();
            $data['from_date'] = $this->input->get('from_date', true) ?: date('Y-m-01');
            $data['to_date']   = $this->input->get('to_date', true) ?: date('Y-m-t');
            $data['selected_customer_id'] = $selectedId > 0 ? $selectedId : '';
            echo Modules::run('template/layout', $data);
            return;
        }

        $customer = $this->customer_model->singledata($customerId);
        if (!$customer) {
            show_404();
            return;
        }
        $from = $this->input->get('from_date', true) ?: date('Y-m-01');
        $to   = $this->input->get('to_date', true) ?: date('Y-m-t');
        $data['customer']  = $customer;
        $data['from_date'] = $from;
        $data['to_date']   = $to;
        $data['statement'] = $this->customer_model->get_customer_statement($customerId, $from, $to);
        $data['title']     = 'Customer Statement';
        $data['module']    = 'customer';
        $data['page']      = 'customer_statement';
        echo Modules::run('template/layout', $data);
    }

    // Statement PDF
    public function customer_statement_pdf($customer_id)
    {
        $customerId = (int) $customer_id;
        $customer = $this->customer_model->singledata($customerId);
        if (!$customer) {
            show_404();
            return;
        }
        $from = $this->input->get('from_date', true) ?: date('Y-m-01');
        $to   = $this->input->get('to_date', true) ?: date('Y-m-t');
        $data['customer']  = $customer;
        $data['from_date'] = $from;
        $data['to_date']   = $to;
        $data['statement'] = $this->customer_model->get_customer_statement($customerId, $from, $to);

        // Render HTML using a simple view and then stream via Dompdf
        $html = $this->load->view('customer_statement_pdf', $data, true);
        require_once FCPATH . 'vendor/autoload.php';
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('customer_statement_' . $customer_id . '.pdf', ['Attachment' => true]);
    }

    // Notes CRUD
    public function add_note($customer_id)
    {
        $text = $this->input->post('note_text', true);
        if (!empty($text)) {
            $this->customer_model->add_note($customer_id, $text);
        }
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=notes');
    }
    public function delete_note($customer_id, $note_id)
    {
        $this->customer_model->delete_note($note_id, $customer_id);
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=notes');
    }

    // Reminders CRUD
    public function add_reminder($customer_id)
    {
        $title = $this->input->post('title', true);
        $remind_on = $this->input->post('remind_on', true);
        if (!empty($title)) {
            $this->customer_model->add_reminder($customer_id, $title, $remind_on);
        }
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=reminders');
    }
    public function delete_reminder($customer_id, $reminder_id)
    {
        $this->customer_model->delete_reminder($reminder_id, $customer_id);
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=reminders');
    }

    // Files upload/delete
    public function upload_file($customer_id)
    {
        if (!empty($_FILES['file']['name'])) {
            $upload_dir = 'uploads/customer_files/';
            if (!is_dir(FCPATH . $upload_dir)) {
                @mkdir(FCPATH . $upload_dir, 0777, true);
            }
            $file_name = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $_FILES['file']['name']);
            $target = FCPATH . $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
                $this->customer_model->add_file_record($customer_id, $_FILES['file']['name'], $upload_dir . $file_name);
            }
        }
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=files');
    }
    public function delete_file($customer_id, $file_id)
    {
        // Optionally remove physical file; for now, delete DB record
        $this->customer_model->delete_file($file_id, $customer_id);
        redirect('customer/customer/customer_detail/' . $customer_id . '?tab=files');
    }

      public function insert_customer_advance(){
        $advance_type = $this->input->post('type',TRUE);
        if($advance_type ==1){
            $dr = $this->input->post('amount',TRUE);
            $tp = 'd';
        }else{
            $cr = $this->input->post('amount',TRUE);
            $tp = 'c';
        }
            $createby      = $this->session->userdata('id');
            $createdate    = date('Y-m-d H:i:s');
            $transaction_id= $this->customer_model->generator(10);
            $customer_id   = $this->input->post('customer_id',TRUE);
            $cusifo        = $this->db->select('*')->from('customer_information')->where('customer_id',$customer_id)->get()->row();
            $headn         = $customer_id.'-'.$cusifo->customer_name;
            $coainfo       = $this->db->select('*')->from('acc_coa')->where('customer_id',$customer_id)->get()->row();
    $customer_headcode = $coainfo->HeadCode;
              
                   $customer_accledger = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'Advance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  $customer_headcode,
      'Narration'      =>  'Customer Advance For  '.$cusifo->customer_name,
      'Debit'          =>  (!empty($dr)?$dr:0),
      'Credit'         =>  (!empty($cr)?$cr:0),
      'IsPosted'       => 1,
      'CreateBy'       => $this->session->userdata('id'),
      'CreateDate'     => date('Y-m-d H:i:s'),
      'IsAppove'       => 1
    );
                         $cc = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'Advance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  111000001,
      'Narration'      =>  'Cash in Hand  For '.$cusifo->customer_name.' Advance',
      'Debit'          =>  (!empty($dr)?$dr:0),
      'Credit'         =>  (!empty($cr)?$cr:0),
      'IsPosted'       =>  1,
      'CreateBy'       =>  $this->session->userdata('id'),
      'CreateDate'     =>  date('Y-m-d H:i:s'),
      'IsAppove'       =>  1
    ); 
                  
       $this->db->insert('acc_transaction',$customer_accledger);
       $this->db->insert('acc_transaction',$cc);
       redirect(base_url('advance_receipt/'.$transaction_id.'/'.$customer_id));

  }

  //customer_advance_receipt
   public function customer_advancercpt($receiptid=null,$customer_id=null) {
    $data['title']         = display('advance_receipt'); 
    $customer_id           = $this->uri->segment(3);
    $receiptdata           = $this->customer_model->advance_details($receiptid,$customer_id);
    $customer_details      = $this->customer_model->customer_personal_data($customer_id);
    $data['details']       = $receiptdata;
    $data['customer_name'] = $customer_details[0]['customer_name'];
    $data['receipt_no']    = $receiptdata[0]['VNo'];
    $data['address']       = $customer_details[0]['customer_address'];
    $data['mobile']        = $customer_details[0]['customer_mobile'];
    $data['module']        = "customer";
    $data['page']          = "customer_advance_receipt";   
    echo Modules::run('template/layout', $data); 
    }

}



