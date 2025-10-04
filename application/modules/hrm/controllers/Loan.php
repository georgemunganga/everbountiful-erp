<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author: Bdtask Ltd
    # Author link: https://www.bdtask.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    

class Loan extends MX_Controller {

    const LOAN_SCHEDULE_META_START = '[[SCHEDULE_META]]';
    const LOAN_SCHEDULE_META_END   = '[[/SCHEDULE_META]]';

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'loan_model'));
        if (! $this->session->userdata('isLogIn'))
            redirect('login');
          
    }


    private function get_payment_channel_options()
    {
        return array(
            'payroll' => 'Payroll',
            'cash'    => 'Cash',
            'bank'    => 'Bank Transfer',
        );
    }

    private function normalize_payment_channel($channel)
    {
        $channel = strtolower((string) $channel);
        $options = array_keys($this->get_payment_channel_options());
        if (!in_array($channel, $options, true)) {
            return 'payroll';
        }
        return $channel;
    }

    private function payment_channel_requires_bank($channel)
    {
        return $this->normalize_payment_channel($channel) === 'bank';
    }

    private function payment_channel_label($channel)
    {
        $options = $this->get_payment_channel_options();
        $normalized = $this->normalize_payment_channel($channel);
        return isset($options[$normalized]) ? $options[$normalized] : ucfirst($normalized);
    }

    private function normalize_date_input($date, $fallback = null)
    {
        if (empty($date)) {
            return $fallback;
        }
        try {
            $dt = new DateTime($date);
            return $dt->format('Y-m-d');
        } catch (Exception $exception) {
            return $fallback;
        }
    }

    private function add_months_to_date($date, $months)
    {
        try {
            $dt = new DateTime($date);
        } catch (Exception $exception) {
            return $date;
        }

        $months = (int) $months;
        if ($months === 0) {
            return $dt->format('Y-m-d');
        }

        $day = (int) $dt->format('d');
        $dt->modify('+' . $months . ' month');
        while ((int) $dt->format('d') < $day) {
            $dt->modify('-1 day');
        }

        return $dt->format('Y-m-d');
    }

    private function calculate_default_start_date($disbursement_date)
    {
        $date = $this->normalize_date_input($disbursement_date, date('Y-m-d'));
        return $this->add_months_to_date($date, 1);
    }

    private function calculate_end_date_from_schedule($start_date, $period_months)
    {
        $start_date = $this->normalize_date_input($start_date, null);
        if (empty($start_date)) {
            return null;
        }

        $period_months = (int) $period_months;
        if ($period_months <= 1) {
            return $start_date;
        }

        return $this->add_months_to_date($start_date, $period_months - 1);
    }

    private function build_schedule_payload(array $overrides = array(), $fallback_disbursement = null)
    {
        $today = date('Y-m-d');
        $disbursement = $this->normalize_date_input(
            isset($overrides['disbursement_date']) ? $overrides['disbursement_date'] : $fallback_disbursement,
            $today
        );

        $period = isset($overrides['repayment_period']) ? (int) $overrides['repayment_period'] : 6;
        if ($period <= 0) {
            $period = 1;
        }

        $start = $this->normalize_date_input(
            isset($overrides['repayment_start_date']) ? $overrides['repayment_start_date'] : null,
            null
        );
        if (empty($start)) {
            $start = $this->calculate_default_start_date($disbursement);
        }

        $end = $this->normalize_date_input(
            isset($overrides['repayment_end_date']) ? $overrides['repayment_end_date'] : null,
            null
        );
        if (empty($end)) {
            $end = $this->calculate_end_date_from_schedule($start, $period);
        }

        $channel = isset($overrides['payment_channel']) ? $overrides['payment_channel'] : null;
        $channel = $this->normalize_payment_channel($channel);

        return array(
            'disbursement_date'    => $disbursement,
            'repayment_period'     => $period,
            'repayment_start_date' => $start,
            'repayment_end_date'   => $end,
            'payment_channel'      => $channel,
        );
    }

    private function summarize_schedule(array $schedule, $channel_label)
    {
        $lines = array();

        if (!empty($schedule['disbursement_date'])) {
            $lines[] = 'Disbursement Date: ' . $schedule['disbursement_date'];
        }
        if (!empty($schedule['repayment_start_date'])) {
            $lines[] = 'Repayment Start Date: ' . $schedule['repayment_start_date'];
        }
        if (!empty($schedule['repayment_end_date'])) {
            $lines[] = 'Repayment End Date: ' . $schedule['repayment_end_date'];
        }
        if (!empty($schedule['repayment_period'])) {
            $lines[] = 'Repayment Period (months): ' . (int) $schedule['repayment_period'];
        }
        if (!empty($channel_label)) {
            $lines[] = 'Payment Channel: ' . $channel_label;
        }

        return $lines;
    }

    private function decorate_details_with_schedule($details, array $schedule)
    {
        $base_details = trim((string) $details);
        $channel_label = $this->payment_channel_label(isset($schedule['payment_channel']) ? $schedule['payment_channel'] : null);
        $summary_lines = $this->summarize_schedule($schedule, $channel_label);

        $display_parts = array();
        if ($base_details !== '') {
            $display_parts[] = $base_details;
        }
        if (!empty($summary_lines)) {
            $display_parts[] = implode("\n", $summary_lines);
        }

        $display_text = trim(implode("\n\n", $display_parts));

        $meta = $schedule;
        $meta['payment_channel_label'] = $channel_label;
        $meta['base_details'] = $base_details;

        $meta_json = json_encode($meta);
        if ($meta_json !== false) {
            if ($display_text !== '') {
                $display_text .= "\n\n";
            }
            $display_text .= self::LOAN_SCHEDULE_META_START . $meta_json . self::LOAN_SCHEDULE_META_END;
        }

        return $display_text;
    }

    private function parse_schedule_from_details($details)
    {
        $details = (string) $details;
        $start_pos = strpos($details, self::LOAN_SCHEDULE_META_START);
        $end_pos = strpos($details, self::LOAN_SCHEDULE_META_END);

        $meta = array();
        $clean_details = trim($details);

        if ($start_pos !== false && $end_pos !== false && $end_pos > $start_pos) {
            $json_start = $start_pos + strlen(self::LOAN_SCHEDULE_META_START);
            $json = substr($details, $json_start, $end_pos - $json_start);
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $meta = $decoded;
                if (isset($decoded['base_details'])) {
                    $clean_details = (string) $decoded['base_details'];
                } else {
                    $clean_details = trim(substr($details, 0, $start_pos));
                }
            } else {
                $clean_details = trim(substr($details, 0, $start_pos));
            }
        }

        return array(
            'details'  => $clean_details,
            'schedule' => $meta,
        );
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
        $employee_link_id = null;

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
                $employee_link_id = (int) $employee->id;
            }
        }

        $data = array(
            'person_id'      => $person_id,
            'person_name'    => $person_name,
            'person_phone'   => $person_phone,
            'person_address' => $person_address,
            'status'         => 1
        );

        if ($employee_link_id !== null) {
            $data['employee_id'] = $employee_link_id;
        }

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
            redirect(base_url('manage_office_loans'));
        }
    }

    public function loanheadcode(){
        $query=$this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE HeadLevel='4' And HeadCode LIKE '1020302000%'");
        return $query->row();

    }

    private function ensure_office_loan_details_table()
    {
        if (! $this->db->table_exists('office_loan_details')) {
            $this->db->query("CREATE TABLE IF NOT EXISTS `office_loan_details` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `transaction_id` varchar(50) NOT NULL,
                `person_id` varchar(50) NOT NULL,
                `disbursement_date` date NOT NULL,
                `repayment_period` int(11) NOT NULL,
                `repayment_start_date` date NOT NULL,
                `repayment_end_date` date NOT NULL,
                `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `transaction_id_unique` (`transaction_id`),
                KEY `person_id_idx` (`person_id`),
                CONSTRAINT `fk_office_loan_details_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `person_ledger` (`transaction_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }

        $this->ensure_office_loan_detail_columns();
    }

    private function ensure_office_loan_detail_columns()
    {
        $table = 'office_loan_details';

        if (! $this->db->field_exists('principal_amount', $table)) {
            $this->db->query("ALTER TABLE `office_loan_details` ADD COLUMN `principal_amount` decimal(14,2) DEFAULT NULL AFTER `repayment_end_date`");
        }

        if (! $this->db->field_exists('monthly_installment', $table)) {
            $this->db->query("ALTER TABLE `office_loan_details` ADD COLUMN `monthly_installment` decimal(14,2) DEFAULT NULL AFTER `principal_amount`");
        }

        if (! $this->db->field_exists('total_paid', $table)) {
            $this->db->query("ALTER TABLE `office_loan_details` ADD COLUMN `total_paid` decimal(14,2) NOT NULL DEFAULT 0 AFTER `monthly_installment`");
        }

        if (! $this->db->field_exists('last_deduction_date', $table)) {
            $this->db->query("ALTER TABLE `office_loan_details` ADD COLUMN `last_deduction_date` date DEFAULT NULL AFTER `total_paid`");
        }

        if (! $this->db->field_exists('next_due_date', $table)) {
            $this->db->query("ALTER TABLE `office_loan_details` ADD COLUMN `next_due_date` date DEFAULT NULL AFTER `last_deduction_date`");
        }

        // Backfill principal amount from ledger where missing
        $this->db->query("UPDATE `office_loan_details` old
            JOIN person_ledger pl ON pl.transaction_id = old.transaction_id AND pl.debit > 0
            SET old.principal_amount = pl.debit
            WHERE (old.principal_amount IS NULL OR old.principal_amount = 0)");

        // Ensure monthly installment is available when possible
        $this->db->query("UPDATE `office_loan_details`
            SET monthly_installment = ROUND(principal_amount / NULLIF(repayment_period, 0), 2)
            WHERE (monthly_installment IS NULL OR monthly_installment = 0)
              AND principal_amount IS NOT NULL
              AND repayment_period > 0");

        // Normalise totals and next due dates
        $this->db->query("UPDATE `office_loan_details`
            SET total_paid = 0
            WHERE total_paid IS NULL");

        $this->db->query("UPDATE `office_loan_details`
            SET next_due_date = repayment_start_date
            WHERE (next_due_date IS NULL OR next_due_date = '' OR next_due_date = '0000-00-00')
              AND repayment_start_date IS NOT NULL");
    }

    private function sanitize_date($value, $fallback = null)
    {
        if (empty($value)) {
            return $fallback;
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return $fallback;
        }

        return date('Y-m-d', $timestamp);
    }

    private function generate_office_headcode($employee_id = null)
    {
        $base = 1020302000;
        $max  = 2147483647;

        if (!empty($employee_id) && ctype_digit((string) $employee_id)) {
            $candidate = $base + (int) $employee_id;
            if ($candidate <= $max && !$this->headcode_exists($candidate)) {
                return $candidate;
            }
        }

        $candidate = $base + 1;
        while ($candidate <= $max) {
            if (!$this->headcode_exists($candidate)) {
                return $candidate;
            }
            $candidate++;
        }

        throw new Exception('No available headcode slot for office loans under INT limit.');
    }

    private function headcode_exists($headcode)
    {
        return $this->db->select('HeadCode')
            ->from('acc_coa')
            ->where('HeadCode', $headcode)
            ->get()
            ->num_rows() > 0;
    }

    private function ensure_office_loan_language()
    {
        $phrases = array(
            'disbursement_date'     => 'Disbursement Date',
            'repayment_period'      => 'Repayment Period',
            'repayment_start_date'  => 'Repayment Start Date',
            'repayment_end_date'    => 'Repayment End Date',
            'months'                => 'months',
        );

        foreach ($phrases as $phrase => $english) {
            $exists = $this->db->select('phrase')
                ->from('language')
                ->where('phrase', $phrase)
                ->get()
                ->row();

            if (!$exists) {
                $this->db->insert('language', array(
                    'phrase'  => $phrase,
                    'english' => $english,
                ));
            }
        }
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

        $existing_person = $this->db->select('person_id, person_name, person_phone, person_address, employee_id')
            ->from('person_information')
            ->where('person_id', $person_id)
            ->get()
            ->row();

        if ($existing_person) {
            $update_data = array(
                'person_name'    => $person_name,
                'person_phone'   => !empty($person_phone) ? $person_phone : $existing_person->person_phone,
                'person_address' => !empty($person_address) ? $person_address : $existing_person->person_address,
                'employee_id'    => (int) $employee->id,
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
                'employee_id'    => (int) $employee->id,
                'status'         => 1,
            );

            $this->loan_model->submit_officeloan_person($data);

            $loan_head_code = $this->generate_office_headcode($employee->id);

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


    public function office_loan_overview($person_id) {
        $person_details = $this->loan_model->select_person_by_id($person_id);
        if (empty($person_details)) {
            $this->session->set_flashdata(array('exception' => display('not_found')));
            redirect(base_url('manage_office_loans'));
        }

        $person = $person_details[0];
        $employee = null;
        if (!empty($person['employee_id'])) {
            $employee = $this->db->select('e.id, e.first_name, e.last_name, e.phone, e.address_line_1, d.designation')
                ->from('employee_history e')
                ->join('designation d', 'd.id = e.designation', 'left')
                ->where('e.id', $person['employee_id'])
                ->get()
                ->row_array();
        }

        $ledger = $this->loan_model->personledger_tradational($person_id);
        $balance = 0.0;
        $total_credit = 0.0;
        $total_debit = 0.0;
        $primary_detail = array();

        if (!empty($ledger)) {
            foreach ($ledger as $key => $entry) {
                if (empty($primary_detail) && (float) $entry['debit'] > 0) {
                    $detail_candidate = $this->loan_model->get_office_loan_detail($entry['transaction_id']);
                    if (!empty($detail_candidate)) {
                        $primary_detail = $detail_candidate;
                    }
                }
                $ledger[$key]['date'] = $this->occational->dateConvert($entry['date']);
                $ledger[$key]['balance'] = ($entry['debit'] - $entry['credit']) + $balance;
                $balance = $ledger[$key]['balance'];
                $total_debit += (float) $entry['debit'];
                $total_credit += (float) $entry['credit'];
            }
        }

        $outstanding = $total_debit - $total_credit;

        $deductions = array();
        if (!empty($ledger)) {
            foreach ($ledger as $entry) {
                if ((float) $entry['credit'] > 0) {
                    $deductions[] = $entry;
                }
            }
        }

        // Get payroll deduction history for this employee
        $payroll_deductions = array();
        if (!empty($person['employee_id'])) {
            $payroll_deductions = $this->db->select('sal_month_year, office_loan_deduct, createDate')
                ->from('gmb_salary_generate')
                ->where('employee_id', $person['employee_id'])
                ->where('office_loan_deduct >', 0)
                ->order_by('createDate', 'desc')
                ->get()
                ->result_array();
        }

        $data = array(
            'title'              => display('loan_overview'),
            'person'             => $person,
            'employee'           => $employee,
            'ledger'             => $ledger,
            'deductions'         => $deductions,
            'payroll_deductions' => $payroll_deductions,
            'loan_details'       => $primary_detail,
            'total_debit'        => $total_debit,
            'total_credit'       => $total_credit,
            'outstanding'        => $outstanding,
        );
        $data['module'] = "hrm";
        $data['page']   = "office_loan/loan_ledger";
        echo modules::run('template/layout', $data);
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
                $parsed_entry = $this->parse_schedule_from_details(isset($ledger[$k]['details']) ? $ledger[$k]['details'] : '');
                $details_block = array();
                $base_detail = trim($parsed_entry['details']);
                if ($base_detail !== '') {
                    $details_block[] = $base_detail;
                }
                if (!empty($parsed_entry['schedule'])) {
                    $schedule_entry = $this->build_schedule_payload($parsed_entry['schedule'], $v['date']);
                    $summary_lines_entry = $this->summarize_schedule($schedule_entry, $this->payment_channel_label($schedule_entry['payment_channel']));
                    if (!empty($summary_lines_entry)) {
                        $details_block[] = implode("\n", $summary_lines_entry);
                    }
                }
                $ledger[$k]['details'] = trim(implode("\n\n", $details_block));
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
                $balance                   = $ledger[$k]['balance'];
                $ledger[$k]['date']        = $this->occational->dateConvert($ledger[$k]['date']);
                $ledger[$k]['subtotalDebit']   = $total_debit + $ledger[$k]['debit'];
                $total_debit                   = $ledger[$k]['subtotalDebit'];
                $ledger[$k]['subtotalCredit']  = $total_credit + $ledger[$k]['credit'];
                $total_credit                  = $ledger[$k]['subtotalCredit'];
                $ledger[$k]['subtotalBalance'] = $total_balance + $ledger[$k]['balance'];
                $total_balance                 = $ledger[$k]['subtotalBalance'];

                $parsed_entry = $this->parse_schedule_from_details(isset($ledger[$k]['details']) ? $ledger[$k]['details'] : '');
                $details_block = array();
                $base_detail = trim($parsed_entry['details']);
                if ($base_detail !== '') {
                    $details_block[] = $base_detail;
                }
                if (!empty($parsed_entry['schedule'])) {
                    $schedule_entry = $this->build_schedule_payload($parsed_entry['schedule'], $v['date']);
                    $summary_lines_entry = $this->summarize_schedule($schedule_entry, $this->payment_channel_label($schedule_entry['payment_channel']));
                    if (!empty($summary_lines_entry)) {
                        $details_block[] = implode("\n", $summary_lines_entry);
                    }
                }
                $ledger[$k]['details'] = trim(implode("\n\n", $details_block));
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
<<<<<<< HEAD
       $data['title']       = display('add_office_loan');
       $data['employees']   = $this->db->select('id, first_name, last_name, phone, address_line_1')
=======
        $data['title']       = display('add_office_loan');
        $data['employees']   = $this->db->select('id, first_name, last_name, phone, address_line_1')
>>>>>>> final
            ->from('employee_history')
            ->order_by('first_name', 'asc')
            ->order_by('last_name', 'asc')
            ->get()
            ->result();
<<<<<<< HEAD
        $data['bank_list']   = $this->db->select('bank_id, bank_name')
            ->from('bank_add')
            ->order_by('bank_name', 'asc')
            ->get()
            ->result_array();
        $data['payment_channels'] = $this->get_payment_channel_options();
        $data['schedule'] = $this->build_schedule_payload();
=======
>>>>>>> final
        $data['module']      = "hrm";
        $data['page']        = "office_loan/add_office_loan";
        echo modules::run('template/layout', $data);
    }



     public function bdtask_insert_office_loan() {
        log_message('error', 'bdtask_insert_office_loan payload: ' . json_encode($this->input->post(NULL, TRUE)));
        $employee_id = $this->input->post('employee_id', TRUE);
        $amount      = (float) $this->input->post('ammount', TRUE);
        $date        = $this->input->post('date', TRUE);
        $details     = $this->input->post('details', TRUE);
        $paytype     = (int) $this->input->post('paytype', TRUE);
        $bank_id     = $this->input->post('bank_id', TRUE);

        $disbursement_date = $this->sanitize_date($this->input->post('disbursement_date', TRUE), $entry_date);
        if (empty($disbursement_date)) {
            $disbursement_date = $entry_date;
        }

        $repayment_period = (int) $this->input->post('repayment_period', TRUE);
        if ($repayment_period < 1) {
            $repayment_period = 1;
        }

        $monthly_installment = round($amount / max(1, $repayment_period), 2);
        $default_start_date = date('Y-m-d', strtotime('+1 month', strtotime($disbursement_date)));
        $repayment_start_date = $this->sanitize_date($this->input->post('repayment_start_date', TRUE), $default_start_date);
        if (strtotime($repayment_start_date) === false) {
            $repayment_start_date = $default_start_date;
        }
        if (strtotime($repayment_start_date) < strtotime($disbursement_date)) {
            $repayment_start_date = $default_start_date;
        }

        $calculated_end_date = date('Y-m-d', strtotime('+' . ($repayment_period - 1) . ' month', strtotime($repayment_start_date)));
        $repayment_end_date_input = $this->sanitize_date($this->input->post('repayment_end_date', TRUE), $calculated_end_date);
        $repayment_end_date = ($repayment_end_date_input === $calculated_end_date) ? $repayment_end_date_input : $calculated_end_date;

        if (empty($entry_date)) {
            $entry_date = date('Y-m-d');
        }
        $details = !empty($details) ? $details : '';
        if ($paytype !== 2) {
            $paytype = 1;
>>>>>>> final
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
            $headcode = $this->generate_office_headcode($employee_id);
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
<<<<<<< HEAD
        if ($this->payment_channel_requires_bank($channel)) {
            $bank = $this->db->select('bank_name')
                ->from('bank_add')
                ->where('bank_id', $bank_id)
                ->get()
                ->row();

=======
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

>>>>>>> final
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

<<<<<<< HEAD
        $details_with_schedule = $this->decorate_details_with_schedule($details, $schedule);

=======
>>>>>>> final
        $ledger_data = array(
            'transaction_id' => $transaction_id,
            'person_id'      => $personinfo->person_id,
            'debit'          => $amount,
            'date'           => $date,
            'details'        => $details,
>>>>>>> final
            'status'         => 1
        );

        $loan = array(
          'VNo'            =>  $transaction_id,
          'Vtype'          =>  'LNR',
          'VDate'          =>  $entry_date,
          'COAID'          =>  $headcode,
<<<<<<< HEAD
          'Narration'      =>  'Loan for '.$personinfo->person_name,
=======
          'Narration'      =>  'Loan for .'.$personinfo->person_name,
>>>>>>> final
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
>>>>>>> final
          'Debit'          =>  0,
          'Credit'         =>  $amount,
          'IsPosted'       =>  1,
          'CreateBy'       =>  $this->session->userdata('id'),
          'CreateDate'     =>  date('Y-m-d H:i:s'),
          'IsAppove'       =>  1
        );

        $result = $this->loan_model->submit_payment($ledger_data);
        log_message('error', 'bdtask_insert_office_loan submit_payment result: ' . ($result ? 'success' : 'fail'));
        if ($result) {
            $this->loan_model->upsert_office_loan_detail(array(
                'transaction_id'       => $transaction_id,
                'person_id'            => $personinfo->person_id,
                'disbursement_date'    => $disbursement_date,
                'repayment_period'     => $repayment_period,
                'repayment_start_date' => $repayment_start_date,
                'repayment_end_date'   => $repayment_end_date,
                'principal_amount'     => $amount,
                'monthly_installment'  => $monthly_installment,
                'total_paid'           => 0.0,
                'next_due_date'        => $repayment_start_date,
            ));

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
        $loan_details = $this->loan_model->get_office_loan_detail($transaction_id);

        if (empty($loan)) {
            $this->session->set_flashdata(array('exception' => display('not_found')));
            redirect(base_url('manage_office_loans'));
        }

<<<<<<< HEAD
        $parsed_details = $this->parse_schedule_from_details($loan['details']);
        $loan['details'] = $parsed_details['details'];
        $schedule = $this->build_schedule_payload($parsed_details['schedule'], $loan['date']);
        $loan['date'] = $schedule['disbursement_date'];

=======
>>>>>>> final
        $credit_entry = $this->db->select('a.COAID, b.HeadName')
            ->from('acc_transaction a')
            ->join('acc_coa b', 'b.HeadCode = a.COAID', 'left')
            ->where('a.VNo', $transaction_id)
            ->where('a.Credit >', 0)
            ->get()
            ->row();

<<<<<<< HEAD
        $bank_id  = '';
        if ($credit_entry && (int) $credit_entry->COAID !== 111000002) {
=======
        $paytype  = 1;
        $bank_id  = '';
        if ($credit_entry && (int) $credit_entry->COAID !== 111000002) {
            $paytype = 2;
>>>>>>> final
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

<<<<<<< HEAD
        if (empty($parsed_details['schedule'])) {
            $schedule['payment_channel'] = ($credit_entry && (int) $credit_entry->COAID !== 111000002) ? 'bank' : 'cash';
        }

        if ($schedule['payment_channel'] === 'bank' && empty($bank_id) && $credit_entry && !empty($credit_entry->HeadName)) {
            $bank = $this->db->select('bank_id')
                ->from('bank_add')
                ->where('bank_name', $credit_entry->HeadName)
                ->get()
                ->row();
            if ($bank) {
                $bank_id = $bank->bank_id;
            }
        }

=======
>>>>>>> final
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
>>>>>>> final
        $data['bank_id']      = $bank_id;
        $data['employees']    = $employees;
        $data['employee_id']  = $employee_id;
        $data['transaction_id'] = $transaction_id;
<<<<<<< HEAD
        $data['payment_channels'] = $this->get_payment_channel_options();
        $data['schedule']     = $schedule;
        $data['paytype']      = $schedule['payment_channel'];
        $data['bank_list']    = $this->db->select('bank_id, bank_name')
            ->from('bank_add')
            ->order_by('bank_name', 'asc')
            ->get()
            ->result_array();
=======
>>>>>>> final

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

        $disbursement_date = $this->sanitize_date($this->input->post('disbursement_date', TRUE), $entry_date);
        if (empty($disbursement_date)) {
            $disbursement_date = $entry_date;
        }

        $repayment_period = (int) $this->input->post('repayment_period', TRUE);
        if ($repayment_period < 1) {
            $repayment_period = 1;
        }

        $monthly_installment = round($amount / max(1, $repayment_period), 2);
        $default_start_date = date('Y-m-d', strtotime('+1 month', strtotime($disbursement_date)));
        $repayment_start_date = $this->sanitize_date($this->input->post('repayment_start_date', TRUE), $default_start_date);
        if (strtotime($repayment_start_date) === false) {
            $repayment_start_date = $default_start_date;
        }
        if (strtotime($repayment_start_date) < strtotime($disbursement_date)) {
            $repayment_start_date = $default_start_date;
        }

        $calculated_end_date = date('Y-m-d', strtotime('+' . ($repayment_period - 1) . ' month', strtotime($repayment_start_date)));
        $repayment_end_date_input = $this->sanitize_date($this->input->post('repayment_end_date', TRUE), $calculated_end_date);
        $repayment_end_date = ($repayment_end_date_input === $calculated_end_date) ? $repayment_end_date_input : $calculated_end_date;

        if (empty($entry_date)) {
            $entry_date = date('Y-m-d');
        }
        $details = !empty($details) ? $details : '';
        if ($paytype !== 2) {
            $paytype = 1;
>>>>>>> final
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
            $headcode = $this->generate_office_headcode($employee_id);
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
<<<<<<< HEAD
        if ($this->payment_channel_requires_bank($channel)) {
=======
        if ($paytype === 2) {
            if (empty($bank_id)) {
                $this->session->set_flashdata(array('exception' => display('not_added')));
                redirect(base_url('bdtask_edit_office_loan/' . $transaction_id));
            }

>>>>>>> final
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

<<<<<<< HEAD
        $details_with_schedule = $this->decorate_details_with_schedule($details, $schedule);

=======
>>>>>>> final
        $ledger_update = array(
            'person_id' => $personinfo->person_id,
            'debit'     => $amount,
            'date'      => $date,
            'details'   => $details,
>>>>>>> final
        );

        $this->db->where('transaction_id', $transaction_id)
            ->update('person_ledger', $ledger_update);

        $this->loan_model->upsert_office_loan_detail(array(
            'transaction_id'       => $transaction_id,
            'person_id'            => $personinfo->person_id,
            'disbursement_date'    => $disbursement_date,
            'repayment_period'     => $repayment_period,
            'repayment_start_date' => $repayment_start_date,
            'repayment_end_date'   => $repayment_end_date,
        'principal_amount'     => $amount,
        'monthly_installment'  => $monthly_installment,
        ));

        $loan = array(
          'VNo'            =>  $transaction_id,
          'Vtype'          =>  'LNR',
          'VDate'          =>  $entry_date,
          'COAID'          =>  $headcode,
<<<<<<< HEAD
          'Narration'      =>  'Loan for '.$personinfo->person_name,
=======
          'Narration'      =>  'Loan for .'.$personinfo->person_name,
>>>>>>> final
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
>>>>>>> final
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
        $ledger_deleted = $this->db->affected_rows() > 0;

        $this->db->where('VNo', $transaction_id)
            ->delete('acc_transaction');

        $this->db->where('transaction_id', $transaction_id)
            ->delete('office_loan_details');
        $detail_deleted = $this->db->affected_rows() > 0;

        if ($ledger_deleted || $detail_deleted) {
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






