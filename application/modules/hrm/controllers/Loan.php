<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author: Bdtask Ltd
    # Author link: https://www.bdtask.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    

class Loan extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
  
        $this->load->model(array(
            'loan_model')); 
        if (! $this->session->userdata('isLogIn'))
            redirect('login');
          
    }
   

    public function bdtask_add_office_loan_person(){
        $data['title']      = display('add_person');
        $data['module']     = "hrm";
        $data['page']       = "office_loan/add_person"; 
        $data['employees']  = $this->db->select('id, first_name, last_name, phone, address_line_1')->from('employee_history')->order_by('first_name', 'asc')->order_by('last_name', 'asc')->get()->result();
        echo modules::run('template/layout', $data);
    }
    public function submit_office_loan_person()
    {
        $employee_id    = $this->input->post('employee_id', TRUE);
        $person_id      = $this->occational->generator(10);
        $person_name    = $this->input->post('name', TRUE);
        $person_phone   = $this->input->post('phone', TRUE);
        $person_address = $this->input->post('address', TRUE);

        if (!empty($employee_id)) {
            $employee = $this->db->select('id, first_name, last_name, phone, address_line_1')
                ->from('employee_history')
                ->where('id', $employee_id)
                ->get()
                ->row();

            if ($employee) {
                $person_id      = sprintf('EMP%05d', $employee->id);
                $person_name    = trim($employee->first_name . ' ' . $employee->last_name);
                $person_phone   = !empty($employee->phone) ? $employee->phone : $person_phone;
                $person_address = !empty($employee->address_line_1) ? $employee->address_line_1 : $person_address;
            }
        }

        $data = array(
            'person_id'      => $person_id,
            'person_name'    => $person_name,
            'person_phone'   => $person_phone,
            'person_address' => $person_address,
            'status'         => 1
        );

        $existing_person = $this->db->select('person_id, person_name')
            ->from('person_information')
            ->where('person_id', $person_id)
            ->get()
            ->row();

        $loan_head_code = '10203020001';
        $coa            = $this->loanheadcode();
        if ($coa && $coa->HeadCode != null) {
            $loan_head_code = $coa->HeadCode + 1;
        }

        $result = false;

        if ($existing_person) {
            $update_data = $data;
            unset($update_data['person_id']);
            $result = $this->loan_model->update_person($update_data, $person_id);

            if ($result) {
                $existing_head_name = $existing_person->person_id . '-' . $existing_person->person_name;
                $new_head_name      = $person_id . '-' . $person_name;
                $this->db->where('HeadName', $existing_head_name)
                    ->update('acc_coa', array(
                        'HeadName'   => $new_head_name,
                        'UpdateBy'   => $this->session->userdata('id'),
                        'UpdateDate' => date('Y-m-d H:i:s'),
                    ));
            }
        } else {
            $result = $this->loan_model->submit_officeloan_person($data);

            if ($result) {
                $loan_coa = array(
                    'HeadCode'         => $loan_head_code,
                    'HeadName'         => $person_id . '-' . $person_name,
                    'PHeadName'        => 'Loan Receivable',
                    'HeadLevel'        => '4',
                    'IsActive'         => '1',
                    'IsTransaction'    => '1',
                    'IsGL'             => '0',
                    'HeadType'         => 'A',
                    'IsBudget'         => '0',
                    'IsDepreciation'   => '0',
                    'DepreciationRate' => '0',
                    'CreateBy'         => $this->session->userdata('id'),
                    'CreateDate'       => date('Y-m-d H:i:s'),
                );
                $this->db->insert('acc_coa', $loan_coa);
            }
        }

        if ($result) {
            $this->session->set_flashdata(array('message' => display('successfully_added')));
            redirect(base_url('manage_office_loans'));
        } else {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect(base_url('add_officeloan_person'));
        }
    }

    public function loanheadcode(){
        $query=$this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE HeadLevel='4' And HeadCode LIKE '1020302000%'");
        return $query->row();

    }

    private function ensure_person_for_employee($employee_id)
    {
        if (empty($employee_id)) {
            return null;
        }

        $employee = $this->db->select('id, first_name, last_name, phone, address_line_1')
            ->from('employee_history')
            ->where('id', $employee_id)
            ->get()
            ->row();

        if (!$employee) {
            return null;
        }

        $person_id      = sprintf('EMP%05d', $employee->id);
        $person_name    = trim($employee->first_name . ' ' . $employee->last_name);
        $person_phone   = !empty($employee->phone) ? $employee->phone : '';
        $person_address = !empty($employee->address_line_1) ? $employee->address_line_1 : '';

        $existing_person = $this->db->select('person_id, person_name, person_phone, person_address')
            ->from('person_information')
            ->where('person_id', $person_id)
            ->get()
            ->row();

        if ($existing_person) {
            $update_data = array(
                'person_name'    => $person_name,
                'person_phone'   => !empty($person_phone) ? $person_phone : $existing_person->person_phone,
                'person_address' => !empty($person_address) ? $person_address : $existing_person->person_address,
                'status'         => 1,
            );
            $this->loan_model->update_person($update_data, $person_id);

            $existing_head_name = $existing_person->person_id . '-' . $existing_person->person_name;
            $new_head_name      = $person_id . '-' . $person_name;
            if ($existing_head_name !== $new_head_name) {
                $this->db->where('HeadName', $existing_head_name)
                    ->update('acc_coa', array(
                        'HeadName'   => $new_head_name,
                        'UpdateBy'   => $this->session->userdata('id'),
                        'UpdateDate' => date('Y-m-d H:i:s'),
                    ));
            }
        } else {
            $data = array(
                'person_id'      => $person_id,
                'person_name'    => $person_name,
                'person_phone'   => $person_phone,
                'person_address' => $person_address,
                'status'         => 1,
            );

            $this->loan_model->submit_officeloan_person($data);

            $loan_head_code = '10203020001';
            $coa            = $this->loanheadcode();
            if ($coa && $coa->HeadCode != null) {
                $loan_head_code = $coa->HeadCode + 1;
            }

            $loan_coa = array(
                'HeadCode'         => $loan_head_code,
                'HeadName'         => $person_id . '-' . $person_name,
                'PHeadName'        => 'Loan Receivable',
                'HeadLevel'        => '4',
                'IsActive'         => '1',
                'IsTransaction'    => '1',
                'IsGL'             => '0',
                'HeadType'         => 'A',
                'IsBudget'         => '0',
                'IsDepreciation'   => '0',
                'DepreciationRate' => '0',
                'CreateBy'         => $this->session->userdata('id'),
                'CreateDate'       => date('Y-m-d H:i:s'),
            );
            $this->db->insert('acc_coa', $loan_coa);
        }

        $person = $this->db->select('*')
            ->from('person_information')
            ->where('person_id', $person_id)
            ->get()
            ->row();

        $head = $this->db->select('HeadCode')
            ->from('acc_coa')
            ->where('HeadName', $person_id . '-' . $person_name)
            ->get()
            ->row();

        return array(
            'employee' => $employee,
            'person'   => $person,
            'headcode' => $head ? $head->HeadCode : null,
        );
    }


        public function manage_office_loans() {
        #pagination starts
        $data['title']     = display('manage_loan');
        $config["base_url"] = base_url('manage_office_loans/');
        $config["total_rows"] = $this->loan_model->office_loan_entries_count();
        $config["per_page"]    = 10;
        $config["uri_segment"] = 2;
        $config["num_links"] = 5;
        /* This Application Must Be Used With BootStrap 3 * */
        $config['full_tag_open'] = "<ul class='pagination'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['last_tagl_close'] = "</li>";
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data["links"] = $this->pagination->create_links();
        $loan_list = $this->loan_model->office_loan_entries($config["per_page"], $page);

        $total_amount = 0;
        if (!empty($loan_list)) {
            foreach ($loan_list as $key => $loan) {
                $loan_list[$key]['date'] = $this->occational->dateConvert($loan['date']);
                $loan_list[$key]['debit'] = (float) $loan['debit'];
                $total_amount += $loan_list[$key]['debit'];
            }
        }

        $data['module']      = "hrm";
        $data['loan_list']   = $loan_list;
        $data['total_amount'] = $total_amount;
        $data['page']        = "office_loan/manage_loans";
        echo Modules::run('template/layout', $data);
    }


    public function office_loan_person_ledger($person_id){
        $person_details_all = $this->loan_model->office_loan_persons();
        $person_details     = $this->loan_model->select_person_by_id($person_id);
        $ledger             = $this->loan_model->personledger_tradational($person_id);
        $balance            = 0;
        $total_credit       = 0;
        $total_debit        = 0;
        $total_balance      = 0;

        if (!empty($ledger)) {
            foreach ($ledger as $k => $v) {
                $ledger[$k]['balance']         = ($ledger[$k]['debit'] - $ledger[$k]['credit']) + $balance;
                $balance                       = $ledger[$k]['balance'];
                $ledger[$k]['subtotalDebit']   = $total_debit + $ledger[$k]['debit'];
                $ledger[$k]['date']            = $this->occational->dateConvert($ledger[$k]['date']);
                $total_debit                   = $ledger[$k]['subtotalDebit'];
                $ledger[$k]['subtotalCredit']  = $total_credit + $ledger[$k]['credit'];
                $total_credit                  = $ledger[$k]['subtotalCredit'];
                $ledger[$k]['subtotalBalance'] = $total_balance + $ledger[$k]['balance'];
                $total_balance                 = $ledger[$k]['subtotalDebit'] - $ledger[$k]['subtotalCredit'];
            }
        }
        $data = array(
            'title'              => display('person_ledger'),
            'person_details'     => $person_details,
            'person_details_all' => $person_details_all,
            'person_id'          => $person_details[0]['person_id'],
            'person_name'        => $person_details[0]['person_name'],
            'person_phone'       => $person_details[0]['person_phone'],
            'person_address'     => $person_details[0]['person_address'],
            'ledger'             => $ledger,
            'subtotalDebit'      => number_format($total_debit, 2, '.', ','),
            'subtotalCredit'     => number_format($total_credit, 2, '.', ','),
            'subtotalBalance'    => number_format($total_balance, 2, '.', ','),
            'links'              => '',
        );
        $data['module']     = "hrm";
        $data['page']       = "office_loan/person_ledger"; 
        echo modules::run('template/layout', $data);
    }

      public function phone_search_by_name() {
        $person_id = $this->input->post('person_id',TRUE);
        $result = $this->db->select('person_phone')
                ->from('person_information')
                ->where('person_id', $person_id)
                ->get()
                ->row();
        if ($result) {
            echo $result->person_phone;
        } else {
            return false;
        }
    }

    public function office_loan_ledger_search(){
        $today = date('Y-m-d');

        $person_id          = $this->input->post('person_id',TRUE) ? $this->input->post('person_id',TRUE) : "";
        $from_date          = $this->input->post('from_date',TRUE);
        $to_date            = $this->input->post('to_date',TRUE) ? $this->input->post('to_date',TRUE) : $today;
        $person_details_all = $this->loan_model->office_loan_persons();
        $person_details     = $this->loan_model->select_person_by_id($person_id);
        $ledger             = $this->loan_model->ledger_search_by_date($person_id,$from_date, $to_date);
        $balance            = 0;
        $total_credit       = 0;
        $total_debit        = 0;
        $total_balance      = 0;

        if (!empty($ledger)) {
            foreach ($ledger as $k => $v) {
                $ledger[$k]['balance']     = ($ledger[$k]['debit'] - $ledger[$k]['credit']) + $balance;
            $balance                       = $ledger[$k]['balance'];
            $ledger[$k]['date']            = $this->occational->dateConvert($ledger[$k]['date']);
            $ledger[$k]['subtotalDebit']   = $total_debit + $ledger[$k]['debit'];
            $total_debit                   = $ledger[$k]['subtotalDebit'];
            $ledger[$k]['subtotalCredit']  = $total_credit + $ledger[$k]['credit'];
            $total_credit                  = $ledger[$k]['subtotalCredit'];
            $ledger[$k]['subtotalBalance'] = $total_balance + $ledger[$k]['balance'];
            $total_balance = $ledger[$k]['subtotalBalance'];
            }
        }

        $data = array(
            'title'              => display('person_ledger'),
            'person_details'     => $person_details,
            'person_details_all' => $person_details_all,
            'person_id'          => $person_details[0]['person_id'],
            'person_name'        => $person_details[0]['person_name'],
            'person_phone'       => $person_details[0]['person_phone'],
            'person_address'     => $person_details[0]['person_address'],
            'ledger'             => $ledger,
            'subtotalDebit'      => $total_debit,
            'subtotalCredit'     => $total_credit,
            'subtotalBalance'    => $total_balance,

        );
        $data['module']     = "hrm";
        $data['page']       = "office_loan/person_ledger"; 
        echo modules::run('template/layout', $data);
    }

       public function bdtask_add_office_loan() {
        $data['title']       = display('add_office_loan');
        $data['employees']   = $this->db->select('id, first_name, last_name, phone, address_line_1')
            ->from('employee_history')
            ->order_by('first_name', 'asc')
            ->order_by('last_name', 'asc')
            ->get()
            ->result();
        $data['module']      = "hrm";
        $data['page']        = "office_loan/add_office_loan";
        echo modules::run('template/layout', $data);
    }



     public function bdtask_insert_office_loan() {
        $employee_id = $this->input->post('employee_id', TRUE);
        $amount      = (float) $this->input->post('ammount', TRUE);
        $date        = $this->input->post('date', TRUE);
        $details     = $this->input->post('details', TRUE);
        $paytype     = (int) $this->input->post('paytype', TRUE);
        $bank_id     = $this->input->post('bank_id', TRUE);

        if (empty($date)) {
            $date = date('Y-m-d');
        }
        $details = !empty($details) ? $details : '';
        if ($paytype !== 2) {
            $paytype = 1;
            $bank_id = '';
        }

        if (empty($employee_id) || $amount <= 0) {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect(base_url('add_office_loan'));
        }

        $person_context = $this->ensure_person_for_employee($employee_id);

        if (empty($person_context) || empty($person_context['person'])) {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect(base_url('add_office_loan'));
        }

        $personinfo = $person_context['person'];
        $manual_phone = $this->input->post('phone', TRUE);
        if (!empty($manual_phone) && $manual_phone !== $personinfo->person_phone) {
            $this->loan_model->update_person(array('person_phone' => $manual_phone), $personinfo->person_id);
            $personinfo->person_phone = $manual_phone;
        }
        $headname   = $personinfo->person_id . '-' . $personinfo->person_name;

        $head = $this->db->select('HeadCode')
            ->from('acc_coa')
            ->where('HeadName', $headname)
            ->get()
            ->row();

        if ($head) {
            $headcode = $head->HeadCode;
        } else {
            $loan_head_code = '10203020001';
            $coa            = $this->loanheadcode();
            if ($coa && $coa->HeadCode != null) {
                $loan_head_code = $coa->HeadCode + 1;
            }
            $headcode = $loan_head_code;
            $loan_coa = array(
                'HeadCode'         => $headcode,
                'HeadName'         => $headname,
                'PHeadName'        => 'Loan Receivable',
                'HeadLevel'        => '4',
                'IsActive'         => '1',
                'IsTransaction'    => '1',
                'IsGL'             => '0',
                'HeadType'         => 'A',
                'IsBudget'         => '0',
                'IsDepreciation'   => '0',
                'DepreciationRate' => '0',
                'CreateBy'         => $this->session->userdata('id'),
                'CreateDate'       => date('Y-m-d H:i:s'),
            );
            $this->db->insert('acc_coa', $loan_coa);
        }

        $transaction_id = $this->occational->generator(10);

        $bankcoaid = '';
        if ($paytype === 2) {
            if (empty($bank_id)) {
                $this->session->set_flashdata(array('exception' => display('not_added')));
                redirect(base_url('add_office_loan'));
            }

            $bank = $this->db->select('bank_name')
                ->from('bank_add')
                ->where('bank_id', $bank_id)
                ->get()
                ->row();

            if (!$bank) {
                $this->session->set_flashdata(array('exception' => display('not_added')));
                redirect(base_url('add_office_loan'));
            }

            $bank_head = $this->db->select('HeadCode')
                ->from('acc_coa')
                ->where('HeadName', $bank->bank_name)
                ->get()
                ->row();

            if ($bank_head) {
                $bankcoaid = $bank_head->HeadCode;
            } else {
                $this->session->set_flashdata(array('exception' => display('not_added')));
                redirect(base_url('add_office_loan'));
            }
        }

        $ledger_data = array(
            'transaction_id' => $transaction_id,
            'person_id'      => $personinfo->person_id,
            'debit'          => $amount,
            'date'           => $date,
            'details'        => $details,
            'status'         => 1
        );

        $loan = array(
          'VNo'            =>  $transaction_id,
          'Vtype'          =>  'LNR',
          'VDate'          =>  $date,
          'COAID'          =>  $headcode,
          'Narration'      =>  'Loan for .'.$personinfo->person_name,
          'Debit'          =>  $amount,
          'Credit'         =>  0,
          'IsPosted'       =>  1,
          'CreateBy'       =>  $this->session->userdata('id'),
          'CreateDate'     =>  date('Y-m-d H:i:s'),
          'IsAppove'       =>  1
        );

        $credit_entry = array(
          'VNo'            =>  $transaction_id,
          'Vtype'          =>  'LNR',
          'VDate'          =>  $date,
          'COAID'          =>  $paytype === 2 ? $bankcoaid : 111000002,
          'Narration'      =>  ($paytype === 2 ? 'Loan for .' : 'Cash in Hand Credit For ').$personinfo->person_name,
          'Debit'          =>  0,
          'Credit'         =>  $amount,
          'IsPosted'       =>  1,
          'CreateBy'       =>  $this->session->userdata('id'),
          'CreateDate'     =>  date('Y-m-d H:i:s'),
          'IsAppove'       =>  1
        );

        $result = $this->loan_model->submit_payment($ledger_data);
        if ($result) {
            $this->db->insert('acc_transaction',$loan);
            $this->db->insert('acc_transaction',$credit_entry);
            $this->session->set_flashdata(array('message' => display('successfully_added')));
            redirect(base_url('add_office_loan'));
        } else {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect(base_url('add_office_loan'));
        }
    }

    public function bdtask_edit_office_loan($transaction_id = null)
    {
        if (empty($transaction_id)) {
            $this->session->set_flashdata(array('exception' => display('not_found')));
            redirect(base_url('manage_office_loans'));
        }

        $loan = $this->loan_model->get_office_loan_by_transaction($transaction_id);

        if (empty($loan)) {
            $this->session->set_flashdata(array('exception' => display('not_found')));
            redirect(base_url('manage_office_loans'));
        }

        $credit_entry = $this->db->select('a.COAID, b.HeadName')
            ->from('acc_transaction a')
            ->join('acc_coa b', 'b.HeadCode = a.COAID', 'left')
            ->where('a.VNo', $transaction_id)
            ->where('a.Credit >', 0)
            ->get()
            ->row();

        $paytype  = 1;
        $bank_id  = '';
        if ($credit_entry && (int) $credit_entry->COAID !== 111000002) {
            $paytype = 2;
            if (!empty($credit_entry->HeadName)) {
                $bank = $this->db->select('bank_id')
                    ->from('bank_add')
                    ->where('bank_name', $credit_entry->HeadName)
                    ->get()
                    ->row();
                if ($bank) {
                    $bank_id = $bank->bank_id;
                }
            }
        }

        $employee_id = '';
        if (!empty($loan['person_id']) && strpos($loan['person_id'], 'EMP') === 0) {
            $employee_id = (int) ltrim(substr($loan['person_id'], 3), '0');
            $employee_exists = $this->db->select('id')
                ->from('employee_history')
                ->where('id', $employee_id)
                ->get()
                ->row();
            if (!$employee_exists) {
                $employee_id = '';
            }
        }

        $employees = $this->db->select('id, first_name, last_name, phone, address_line_1')
            ->from('employee_history')
            ->order_by('first_name', 'asc')
            ->order_by('last_name', 'asc')
            ->get()
            ->result();

        $data['title']        = display('update');
        $data['module']       = "hrm";
        $data['page']         = "office_loan/edit_office_loan";
        $data['loan']         = $loan;
        $data['paytype']      = $paytype;
        $data['bank_id']      = $bank_id;
        $data['employees']    = $employees;
        $data['employee_id']  = $employee_id;
        $data['transaction_id'] = $transaction_id;

        echo modules::run('template/layout', $data);
    }

    public function bdtask_update_office_loan()
    {
        $transaction_id = $this->input->post('transaction_id', TRUE);
        $employee_id    = $this->input->post('employee_id', TRUE);
        $amount         = (float) $this->input->post('ammount', TRUE);
        $date           = $this->input->post('date', TRUE);
        $details        = $this->input->post('details', TRUE);
        $paytype        = (int) $this->input->post('paytype', TRUE);
        $bank_id        = $this->input->post('bank_id', TRUE);

        if (empty($date)) {
            $date = date('Y-m-d');
        }
        $details = !empty($details) ? $details : '';
        if ($paytype !== 2) {
            $paytype = 1;
            $bank_id = '';
        }

        if (empty($transaction_id) || empty($employee_id) || $amount <= 0) {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect(base_url('manage_office_loans'));
        }

        $person_context = $this->ensure_person_for_employee($employee_id);

        if (empty($person_context) || empty($person_context['person'])) {
            $this->session->set_flashdata(array('exception' => display('not_found')));
            redirect(base_url('manage_office_loans'));
        }

        $personinfo = $person_context['person'];
        $manual_phone = $this->input->post('phone', TRUE);
        if (!empty($manual_phone) && $manual_phone !== $personinfo->person_phone) {
            $this->loan_model->update_person(array('person_phone' => $manual_phone), $personinfo->person_id);
            $personinfo->person_phone = $manual_phone;
        }
        $headname   = $personinfo->person_id . '-' . $personinfo->person_name;
        $head       = $this->db->select('HeadCode')
            ->from('acc_coa')
            ->where('HeadName', $headname)
            ->get()
            ->row();

        if ($head) {
            $headcode = $head->HeadCode;
        } else {
            $loan_head_code = '10203020001';
            $coa            = $this->loanheadcode();
            if ($coa && $coa->HeadCode != null) {
                $loan_head_code = $coa->HeadCode + 1;
            }
            $headcode = $loan_head_code;
            $loan_coa = array(
                'HeadCode'         => $headcode,
                'HeadName'         => $headname,
                'PHeadName'        => 'Loan Receivable',
                'HeadLevel'        => '4',
                'IsActive'         => '1',
                'IsTransaction'    => '1',
                'IsGL'             => '0',
                'HeadType'         => 'A',
                'IsBudget'         => '0',
                'IsDepreciation'   => '0',
                'DepreciationRate' => '0',
                'CreateBy'         => $this->session->userdata('id'),
                'CreateDate'       => date('Y-m-d H:i:s'),
            );
            $this->db->insert('acc_coa', $loan_coa);
        }

        $bankcoaid = '';
        if ($paytype === 2) {
            if (empty($bank_id)) {
                $this->session->set_flashdata(array('exception' => display('not_added')));
                redirect(base_url('bdtask_edit_office_loan/' . $transaction_id));
            }

            $bank = $this->db->select('bank_name')
                ->from('bank_add')
                ->where('bank_id', $bank_id)
                ->get()
                ->row();

            if (!$bank) {
                $this->session->set_flashdata(array('exception' => display('not_added')));
                redirect(base_url('bdtask_edit_office_loan/' . $transaction_id));
            }

            $bank_head = $this->db->select('HeadCode')
                ->from('acc_coa')
                ->where('HeadName', $bank->bank_name)
                ->get()
                ->row();

            if ($bank_head) {
                $bankcoaid = $bank_head->HeadCode;
            } else {
                $this->session->set_flashdata(array('exception' => display('not_added')));
                redirect(base_url('bdtask_edit_office_loan/' . $transaction_id));
            }
        }

        $ledger_update = array(
            'person_id' => $personinfo->person_id,
            'debit'     => $amount,
            'date'      => $date,
            'details'   => $details,
        );

        $this->db->where('transaction_id', $transaction_id)
            ->update('person_ledger', $ledger_update);

        $loan = array(
          'VNo'            =>  $transaction_id,
          'Vtype'          =>  'LNR',
          'VDate'          =>  $date,
          'COAID'          =>  $headcode,
          'Narration'      =>  'Loan for .'.$personinfo->person_name,
          'Debit'          =>  $amount,
          'Credit'         =>  0,
          'IsPosted'       =>  1,
          'CreateBy'       =>  $this->session->userdata('id'),
          'CreateDate'     =>  date('Y-m-d H:i:s'),
          'IsAppove'       =>  1
        );

        $credit_entry = array(
          'VNo'            =>  $transaction_id,
          'Vtype'          =>  'LNR',
          'VDate'          =>  $date,
          'COAID'          =>  $paytype === 2 ? $bankcoaid : 111000002,
          'Narration'      =>  ($paytype === 2 ? 'Loan for .' : 'Cash in Hand Credit For ').$personinfo->person_name,
          'Debit'          =>  0,
          'Credit'         =>  $amount,
          'IsPosted'       =>  1,
          'CreateBy'       =>  $this->session->userdata('id'),
          'CreateDate'     =>  date('Y-m-d H:i:s'),
          'IsAppove'       =>  1
        );

        $this->db->where('VNo', $transaction_id)->delete('acc_transaction');
        $this->db->insert('acc_transaction', $loan);
        $this->db->insert('acc_transaction', $credit_entry);

        $this->session->set_flashdata(array('message' => display('successfully_updated')));
        redirect(base_url('manage_office_loans'));
    }

    public function delete_office_loan_entry($transaction_id = null)
    {
        if (empty($transaction_id)) {
            $this->session->set_flashdata(array('exception' => display('not_found')));
            redirect(base_url('manage_office_loans'));
        }

        $this->db->where('transaction_id', $transaction_id)
            ->delete('person_ledger');
        $this->db->where('VNo', $transaction_id)
            ->delete('acc_transaction');

        if ($this->db->affected_rows()) {
            $this->session->set_flashdata(array('message' => display('successfully_deleted')));
        } else {
            $this->session->set_flashdata(array('exception' => display('not_found')));
        }

        redirect(base_url('manage_office_loans'));
    }


        public function bdtask_add_office_loan_payment() {
        $data['title']       = display('add_payment');
        $data['person_list'] = $this->loan_model->office_loan_persons();
        $data['module']      = "hrm";
        $data['page']        = "office_loan/add_officeloan_payment"; 
        echo modules::run('template/layout', $data);
    }


        public function bdtask_submit_payment() {
        $personid       = $this->input->post('person_id',TRUE);
        $personinfo     = $this->db->select('person_name')->from('person_information')->where('person_id',$personid)->get()->row();
        $headname       = $personid.'-'.$personinfo->person_name;
        $headcid        = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName',$headname)->get()->row()->HeadCode;
        $transaction_id = $this->occational->generator(10);

   $bank_id = $this->input->post('bank_id',TRUE);
        if(!empty($bank_id)){
       $bankname = $this->db->select('bank_name')->from('bank_add')->where('bank_id',$bank_id)->get()->row()->bank_name;
    
       $bankcoaid = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName',$bankname)->get()->row()->HeadCode;
       }else{
        $bankcoaid='';
       }

        $data = array(
            'transaction_id' => $transaction_id,
            'person_id'      => $this->input->post('person_id',TRUE),
            'credit'         => $this->input->post('ammount',TRUE),
            'date'           => $this->input->post('date',TRUE),
            'details'        => $this->input->post('details',TRUE),
            'status'         => 2
        );
        $paidloan = array(
          'VNo'            =>  $transaction_id,
          'Vtype'          =>  'LNP',
          'VDate'          =>  $this->input->post('date',TRUE),
          'COAID'          =>  $headcid,
          'Narration'      =>  'Loan Payment from .'.$personinfo->person_name,
          'Debit'          =>  0,
          'Credit'         =>  $this->input->post('ammount',TRUE),
          'IsPosted'       =>  1,
          'CreateBy'       =>  $this->session->userdata('id'),
          'CreateDate'     =>  date('Y-m-d H:i:s'),
          'IsAppove'       =>  1
        ); 
         $cc = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'LNR',
      'VDate'          =>  $this->input->post('date',TRUE),
      'COAID'          =>  111000002,
      'Narration'      =>  'Cash in Hand Debit For '.$personinfo->person_name,
      'Debit'          =>  $this->input->post('ammount',TRUE),
      'Credit'         =>  0,
      'IsPosted'       =>  1,
      'CreateBy'       =>  $this->session->userdata('id'),
      'CreateDate'     =>  date('Y-m-d H:i:s'),
      'IsAppove'       =>  1
    ); 


   // bank ledger
   $bankc = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'LNR',
      'VDate'          =>  $this->input->post('date',TRUE),
      'COAID'          =>  $bankcoaid,
      'Narration'      =>  'Loan for .'.$personinfo->person_name,
      'Debit'          =>  $this->input->post('ammount',TRUE),
      'Credit'         =>  0,
      'IsPosted'       =>  1,
      'CreateBy'       =>  $this->session->userdata('id'),
      'CreateDate'     =>  date('Y-m-d H:i:s'),
      'IsAppove'       =>  1
    ); 



        $result = $this->loan_model->submit_payment($data);
        if ($result) {
            $this->db->insert('acc_transaction',$paidloan);
            if($this->input->post('paytype',TRUE) == 2){
        $this->db->insert('acc_transaction',$bankc);
       
        }
            if($this->input->post('paytype',TRUE) == 1){
        $this->db->insert('acc_transaction',$cc);
        }
            $this->session->set_flashdata(array('message' => display('successfully_added')));
            redirect(base_url('add_office_loan_payment'));
        } else {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect(base_url('add_office_loan_payment'));
        }
    }

            public function bdtask_edit_office_person($person_id) {
        $person_list = $this->loan_model->select_person_by_id($person_id);
        $data = array(
            'title'          => display('personal_edit'),
            'person_id'      => $person_list[0]['person_id'],
            'person_name'    => $person_list[0]['person_name'],
            'person_phone'   => $person_list[0]['person_phone'],
            'person_address' => $person_list[0]['person_address'],
        );

        $data['module']       = "hrm";
        $data['page']         = "office_loan/person_edit"; 
        echo modules::run('template/layout', $data);
    }


        public function update_person($person_id) {
        $data = array(
            'person_name'    => $this->input->post('name',TRUE),
            'person_phone'   => $this->input->post('phone',TRUE),
            'person_address' => $this->input->post('address',TRUE),
            'status'         => 1
        );
        $result = $this->loan_model->update_person($data, $person_id);
        if ($result) {
            $this->session->set_flashdata(array('message' => display('successfully_updated')));
            redirect('manage_office_loans');
        } else {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

      public function delete_office_loan($id = null) 
    { 
        if ($this->loan_model->delete_office_loan($id)) {
            #set success message
            $this->session->set_flashdata('message',display('delete_successfully'));
        } else {
            #set exception message
            $this->session->set_flashdata('exception',display('please_try_again'));
        }
       redirect($_SERVER['HTTP_REFERER']);
    }


    /*personal loan part start*/
    public function bdtask_add_person(){
        $data['title']      = display('add_person');
        $data['module']     = "hrm";
        $data['page']       = "personal_loan/add_person"; 
        echo modules::run('template/layout', $data);  
    }


        public function bdtask_submit_person() {
        $person_id = $this->occational->generator(10);
        $data = array(
            'person_id'      => $person_id,
            'person_name'    => $this->input->post('name',TRUE),
            'person_phone'   => $this->input->post('phone',TRUE),
            'person_address' => $this->input->post('address',TRUE),
            'status'         => 1
        );
       
        $result = $this->loan_model->submit_person_personal_loan($data);
        if ($result) {
           
            $this->session->set_flashdata(array('message' => display('successfully_added')));
            redirect(base_url('manage_person'));
        } else {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect(base_url('manage_person'));
        }
    }



    public function manage_person() {
        $data['title']   = display('manage_person');
        $config["base_url"] = base_url('manage_person/');
        $config["total_rows"] = $this->loan_model->person_list_count();
        $config["per_page"] = 10;
        $config["uri_segment"] = 2;
        $config["num_links"] = 5;
        /* This Application Must Be Used With BootStrap 3 * */
        $config['full_tag_open'] = "<ul class='pagination'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['last_tagl_close'] = "</li>";
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data["links"] = $this->pagination->create_links();
        $data['module']  = "hrm";
        $data['person_list']=$this->loan_model->person_list_limt_loan($config["per_page"], $page);
         $data['page']   = "personal_loan/pesonal_loan_manage";
        echo Modules::run('template/layout', $data);
    }

    public function bdtask_personal_ledger($person_id){

        $person_details_all = $this->loan_model->person_list_personal_loan();
        $person_details     = $this->loan_model->select_loan_person_by_id($person_id);
        $ledger             = $this->loan_model->personal_loan_tradational($person_id);
        $balance            = 0;
        $total_credit       = 0;
        $total_debit        = 0;
        $total_balance      = 0;

        if (!empty($ledger)) {
            foreach ($ledger as $k => $v) {
                $ledger[$k]['balance'] = ($ledger[$k]['debit'] - $ledger[$k]['credit']) + $balance;
                $balance = $ledger[$k]['balance'];
                $ledger[$k]['date'] = $this->occational->dateConvert($ledger[$k]['date']);
                $ledger[$k]['subtotalDebit']  = $total_debit + $ledger[$k]['debit'];
                $total_debit                  = $ledger[$k]['subtotalDebit'];
                $ledger[$k]['subtotalCredit'] = $total_credit + $ledger[$k]['credit'];
                $total_credit                 = $ledger[$k]['subtotalCredit'];
                $ledger[$k]['subtotalBalance']= $ledger[$k]['subtotalDebit'] - $ledger[$k]['subtotalCredit'];
                $total_balance                = $ledger[$k]['subtotalBalance'];
            }
        }

        $data = array(
            'title'              => display('person_ledger'),
            'person_details_all' => $person_details_all,
            'person_details'     => $person_details,
            'person_id'          => $person_details[0]['person_id'],
            'person_name'        => $person_details[0]['person_name'],
            'person_phone'       => $person_details[0]['person_phone'],
            'person_address'     => $person_details[0]['person_address'],
            'ledger'             => $ledger,
            'subtotalDebit'      => number_format($total_debit, 2, '.', ','),
            'subtotalCredit'     => number_format($total_credit, 2, '.', ','),
            'subtotalBalance'    => number_format($total_balance, 2, '.', ','),
            'links'              => '',
        );
        $data['module']     = "hrm";
        $data['page']       = "personal_loan/person_loan_summary"; 
        echo modules::run('template/layout', $data);
    }


        //Person loan search by phone number
    public function loan_phone_search_by_name() {
        $person_id = $this->input->post('person_id',TRUE);
        $result = $this->db->select('person_phone')
                ->from('pesonal_loan_information')
                ->where('person_id', $person_id)
                ->get()
                ->row();
        if ($result) {
            echo $result->person_phone;
        } else {
            return false;
        }
    }


        public function bdtask_personal_loan_summary() {

        $today              = date('Y-m-d');
        $person_id          = $this->input->post('person_id',TRUE) ? $this->input->post('person_id',TRUE) : "";
        $from_date          = $this->input->post('from_date',TRUE);
        $to_date            = $this->input->post('to_date',TRUE) ? $this->input->post('to_date',TRUE) : $today;
        $person_details_all = $this->loan_model->person_list_personal_loan();
        $person_details     = $this->loan_model->select_loan_person_by_id($person_id);
        $ledger             = $this->loan_model->person_loan_search_by_date($person_id, $from_date, $to_date);
        $balance            = 0;
        $total_credit       = 0;
        $total_debit        = 0;
        $total_balance      = 0;

        if (!empty($ledger)) {
            foreach ($ledger as $k => $v) {
                $ledger[$k]['balance'] = ($ledger[$k]['debit'] - $ledger[$k]['credit']) + $balance;
            $balance = $ledger[$k]['balance'];
            $ledger[$k]['date']            = $this->occational->dateConvert($ledger[$k]['date']);
            $ledger[$k]['subtotalDebit']   = $total_debit + $ledger[$k]['debit'];
            $total_debit                   = $ledger[$k]['subtotalDebit'];
            $ledger[$k]['subtotalCredit']  = $total_credit + $ledger[$k]['credit'];
            $total_credit                  = $ledger[$k]['subtotalCredit'];
            $ledger[$k]['subtotalBalance'] = $total_balance + $ledger[$k]['balance'];
            $total_balance                 = $ledger[$k]['subtotalBalance'];
            }
        }
        $data = array(
            'title'              => display('person_ledger'),
            'person_details'     => $person_details,
            'person_details_all' => $person_details_all,
            'person_id'          => $person_details[0]['person_id'],
            'person_name'        => $person_details[0]['person_name'],
            'person_phone'       => $person_details[0]['person_phone'],
            'person_address'     => $person_details[0]['person_address'],
            'ledger'             => $ledger,
            'subtotalDebit'      => $total_debit,
            'subtotalCredit'     => $total_credit,
            'subtotalBalance'    => $total_balance,
            'links'              => '',
        );
        $data['module']     = "hrm";
        $data['page']       = "personal_loan/person_loan_summary"; 
        echo modules::run('template/layout', $data);
    }


    public function bdtask_add_loan(){
        $data['title']       = display('add_loan');
        $data['person_list'] = $this->loan_model->person_list_personal_loan();
        $data['module']      = "hrm";
        $data['page']        = "personal_loan/add_loan"; 
        echo modules::run('template/layout', $data); 
    }


        public function bdtask_submit_loan() {
        $transaction_id = $this->occational->generator(10);
        $data = array(
            'transaction_id' => $transaction_id,
            'person_id'      => $this->input->post('person_id',TRUE),
            'debit'          => $this->input->post('ammount',TRUE),
            'date'           => $this->input->post('date',TRUE),
            'details'        => $this->input->post('details',TRUE),
            'status'         => 1
        );
       

        $result = $this->loan_model->submit_loan_personal($data);
        if ($result) {
           
            $this->session->set_flashdata(array('message' => display('successfully_added')));
            redirect(base_url('add_loan'));
        } else {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect(base_url('add_loan'));
        }
    }

        public function bdtask_add_payment(){
        $data['title']       = display('add_payment');
        $data['person_list'] = $this->loan_model->person_list_personal_loan();
        $data['module']      = "hrm";
        $data['page']        = "personal_loan/add_payment"; 
        echo modules::run('template/layout', $data); 
    }


    public function bdtask_submit_personal_payment() {
        $transaction_id = $this->occational->generator(10);
        $data = array(
            'transaction_id' => $transaction_id,
            'person_id'      => $this->input->post('person_id',TRUE),
            'credit'         => $this->input->post('ammount',TRUE),
            'date'           => $this->input->post('date',TRUE),
            'details'        => $this->input->post('details',TRUE),
            'status'         => 2
        );
       
        $result = $this->loan_model->submit_payment_per_loan($data);
        if ($result) {
            $this->session->set_flashdata(array('message' => display('successfully_added')));
            redirect(base_url('add_payment'));
        } else {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect(base_url('add_payment'));
        }
    }

      public function edit_person_loan($person_id) {
        $person_list = $this->loan_model->select_loan_person_by_id($person_id);
        $data = array(
            'title'          => display('personal_edit'),
            'person_id'      => $person_list[0]['person_id'],
            'person_name'    => $person_list[0]['person_name'],
            'person_phone'   => $person_list[0]['person_phone'],
            'person_address' => $person_list[0]['person_address'],
        );
        $data['module']      = "hrm";
        $data['page']        = "personal_loan/person_loan_edit"; 
        echo modules::run('template/layout', $data);
    }

    public function update_person_personalloan($person_id) {
        $data = array(
            'person_name'    => $this->input->post('name',TRUE),
            'person_phone'   => $this->input->post('phone',TRUE),
            'person_address' => $this->input->post('address',TRUE),
            'status'         => 1
        );
        $result = $this->loan_model->update_person_personal($data, $person_id);
        if ($result) {
            $this->session->set_flashdata(array('message' => display('successfully_updated')));
            redirect(base_url('manage_person'));
        } else {
            $this->session->set_flashdata(array('exception' => display('not_added')));
            redirect(base_url('manage_person'));
        }
    }


      public function delete_personal_loan($id = null) 
        { 
        if ($this->loan_model->delete_personal_loan($id)) {
            $this->session->set_flashdata('message',display('delete_successfully'));
        } else {
            $this->session->set_flashdata('exception',display('please_try_again'));
        }
       redirect($_SERVER['HTTP_REFERER']);
    }

}






