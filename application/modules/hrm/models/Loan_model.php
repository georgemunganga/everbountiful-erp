<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_model extends CI_Model {

 
       public function submit_officeloan_person($data = []) {
       return $result = $this->db->insert('person_information', $data);
    }

       //Person  List
    public function office_loan_list($per_page, $limit) {
        $this->db->select('*');
        $this->db->from('person_information');
        $this->db->where('status', 1);
        $this->db->limit($per_page, $limit);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    /**
     * Retrieve paginated office loan entries.
     */
    public function office_loan_entries($per_page, $limit)
    {
        return $this->db
            ->select('pl.*, pi.person_name, pi.person_phone, pi.person_address, pi.employee_id')
            ->select("CONCAT_WS(' ', eh.first_name, eh.last_name) AS employee_full_name", false)
            ->select('IFNULL(old.principal_amount, pl.debit) AS principal_amount', false)
            ->select('IFNULL(old.total_paid, 0) AS total_paid_amount', false)
            ->select('(SELECT IFNULL(SUM(debit), 0) FROM person_ledger WHERE person_id = pl.person_id) AS total_debit', false)
            ->select('(SELECT IFNULL(SUM(credit), 0) FROM person_ledger WHERE person_id = pl.person_id) AS total_credit', false)
            ->select('old.disbursement_date, old.repayment_period, old.repayment_start_date, old.repayment_end_date')
            ->from('person_ledger pl')
            ->join('person_information pi', 'pi.person_id = pl.person_id', 'left')
            ->join('employee_history eh', 'eh.id = pi.employee_id', 'left')
            ->join('office_loan_details old', 'old.transaction_id = pl.transaction_id', 'left')
            ->where('pl.debit >', 0)
            ->order_by('pl.date', 'desc')
            ->limit($per_page, $limit)
            ->get()
            ->result_array();
    }

    /**
     * Count total office loan entries.
     */
    public function office_loan_entries_count()
    {
        return $this->db->where('debit >', 0)
            ->from('person_ledger')
            ->count_all_results();
    }

    public function employees_without_open_office_loan()
    {
        return $this->db->select('eh.id, eh.first_name, eh.last_name, eh.phone, eh.address_line_1')
            ->from('employee_history eh')
            ->join('person_information pi', 'pi.employee_id = eh.id', 'left')
            ->join('office_loan_details old', '(old.person_id = pi.person_id AND (old.principal_amount - IFNULL(old.total_paid, 0)) > 0)', 'left', false)
            ->join('(SELECT person_id, SUM(debit) AS total_debit, SUM(credit) AS total_credit FROM person_ledger GROUP BY person_id) pls', 'pls.person_id = pi.person_id', 'left', false)
            ->where('old.id IS NULL')
            ->group_start()
                ->where('pls.person_id IS NULL')
                ->or_where('(IFNULL(pls.total_debit, 0) - IFNULL(pls.total_credit, 0)) <=', 0, false)
            ->group_end()
            ->order_by('eh.first_name', 'asc')
            ->order_by('eh.last_name', 'asc')
            ->group_by('eh.id')
            ->get()
            ->result();
    }

    public function employee_has_open_office_loan($employee_id)
    {
        if (empty($employee_id)) {
            return false;
        }

        $person = $this->db->select('person_id')
            ->from('person_information')
            ->where('employee_id', $employee_id)
            ->get()
            ->row();

        if (!$person) {
            return false;
        }

        $open_detail = $this->db->select('id')
            ->from('office_loan_details')
            ->where('person_id', $person->person_id)
            ->where('(principal_amount - IFNULL(total_paid, 0)) >', 0, false)
            ->limit(1)
            ->get()
            ->num_rows() > 0;

        if ($open_detail) {
            return true;
        }

        $ledger_totals = $this->db->select('IFNULL(SUM(debit), 0) AS total_debit, IFNULL(SUM(credit), 0) AS total_credit', false)
            ->from('person_ledger')
            ->where('person_id', $person->person_id)
            ->get()
            ->row();

        if ($ledger_totals) {
            $outstanding = (float) $ledger_totals->total_debit - (float) $ledger_totals->total_credit;
            if ($outstanding > 0) {
                return true;
            }
        }

        return false;
    }


    public function upsert_office_loan_detail($data)
    {
        if (empty($data['transaction_id'])) {
            return false;
        }

        $transaction_id = $data['transaction_id'];
        $existing = $this->db->select('*')
            ->from('office_loan_details')
            ->where('transaction_id', $transaction_id)
            ->get()
            ->row_array();

        $timestamp = date('Y-m-d H:i:s');
        $base_update = array(
            'person_id'             => isset($data['person_id']) ? $data['person_id'] : null,
            'disbursement_date'    => isset($data['disbursement_date']) ? $data['disbursement_date'] : null,
            'repayment_period'     => isset($data['repayment_period']) ? $data['repayment_period'] : null,
            'repayment_start_date' => isset($data['repayment_start_date']) ? $data['repayment_start_date'] : null,
            'repayment_end_date'   => isset($data['repayment_end_date']) ? $data['repayment_end_date'] : null,
            'updated_at'           => $timestamp,
        );

        $principal_amount    = isset($data['principal_amount']) ? (float) $data['principal_amount'] : null;
        $monthly_installment = isset($data['monthly_installment']) ? (float) $data['monthly_installment'] : null;
        $next_due_date       = isset($data['next_due_date']) ? $data['next_due_date'] : null;
        $total_paid          = isset($data['total_paid']) ? (float) $data['total_paid'] : null;

        if ($existing) {
            if ($principal_amount !== null) {
                $paid_so_far = isset($existing['total_paid']) ? (float) $existing['total_paid'] : 0.0;
                $base_update['principal_amount'] = max($principal_amount, $paid_so_far);
            }
            if ($monthly_installment !== null) {
                $base_update['monthly_installment'] = $monthly_installment;
            }
            if ($next_due_date !== null) {
                $base_update['next_due_date'] = $next_due_date;
            }

            $base_update = array_filter($base_update, function ($value) {
                return $value !== null;
            });

            if (!isset($base_update['monthly_installment']) && !empty($base_update['repayment_period'])) {
                $principal_source = isset($base_update['principal_amount']) ? $base_update['principal_amount'] : (isset($existing['principal_amount']) ? (float) $existing['principal_amount'] : 0.0);
                if ($principal_source > 0) {
                    $base_update['monthly_installment'] = round($principal_source / max(1, (int) $base_update['repayment_period']), 2);
                }
            }

            $updated = $this->db->where('transaction_id', $transaction_id)
                ->update('office_loan_details', $base_update);
            if (!$updated) {
                $error = json_encode($this->db->error());
                log_message('error', 'Failed to update office_loan_details: ' . $error . ' data=' . json_encode($base_update));
                error_log('[Loan_model::upsert_office_loan_detail][update] ' . $error . ' data=' . json_encode($base_update));
            }
            return $updated;
        }

        $insert = array_merge($base_update, array(
            'transaction_id'      => $transaction_id,
            'created_at'          => $timestamp,
            'principal_amount'    => $principal_amount !== null ? $principal_amount : (isset($data['amount']) ? (float) $data['amount'] : null),
            'monthly_installment' => $monthly_installment,
            'total_paid'          => $total_paid !== null ? $total_paid : 0.0,
            'next_due_date'       => $next_due_date !== null ? $next_due_date : (isset($data['repayment_start_date']) ? $data['repayment_start_date'] : null),
        ));

        if (empty($insert['monthly_installment']) && !empty($insert['principal_amount']) && !empty($insert['repayment_period'])) {
            $insert['monthly_installment'] = round($insert['principal_amount'] / max(1, (int) $insert['repayment_period']), 2);
        }

        $inserted = $this->db->insert('office_loan_details', $insert);
        if (!$inserted) {
            $error = json_encode($this->db->error());
            log_message('error', 'Failed to insert office_loan_details: ' . $error . ' data=' . json_encode($insert));
            error_log('[Loan_model::upsert_office_loan_detail][insert] ' . $error . ' data=' . json_encode($insert));
        }
        return $inserted;
    }

    public function get_office_loan_detail($transaction_id)
    {
        return $this->db->select('*')
            ->from('office_loan_details')
            ->where('transaction_id', $transaction_id)
            ->get()
            ->row_array();
    }

        public function office_person_list_count() {
        $this->db->select('*');
        $this->db->from('person_information');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }

        //Person  List Count
    public function person_list_count() {
        $this->db->select('*');
        $this->db->from('pesonal_loan_information');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }

    public function office_loan_persons(){
    	$this->db->select('*');
        $this->db->from('person_information');
        $this->db->where('status', 1);
        $this->db->where('employee_id IS NOT NULL', null, false);
        return $this->db->order_by('person_name', 'asc')->get()->result_array();
    }

    public function get_person_active_office_loans($person_id)
    {
        if (empty($person_id)) {
            return array();
        }

        return $this->db->select('old.*, (old.principal_amount - IFNULL(old.total_paid, 0)) AS remaining_balance')
            ->from('office_loan_details old')
            ->where('old.person_id', $person_id)
            ->where('(old.principal_amount - IFNULL(old.total_paid, 0)) >', 0)
            ->order_by('old.next_due_date IS NULL', 'asc', false)
            ->order_by('old.next_due_date', 'asc')
            ->order_by('old.disbursement_date', 'asc')
            ->get()
            ->result();
    }

    public function apply_office_loan_payment($transaction_id, $amount, $payment_date)
    {
        if (empty($transaction_id) || $amount <= 0) {
            return false;
        }

        $loan = $this->get_office_loan_detail($transaction_id);
        if (!$loan) {
            return false;
        }

        $remaining = max(0.0, floatval($loan->principal_amount) - floatval($loan->total_paid));
        if ($remaining <= 0.0) {
            return false;
        }

        $applied_amount = min($remaining, $amount);
        $new_total_paid = floatval($loan->total_paid) + $applied_amount;
        if ($new_total_paid > floatval($loan->principal_amount)) {
            $new_total_paid = floatval($loan->principal_amount);
        }

        $next_due_date = null;
        if ($new_total_paid < floatval($loan->principal_amount)) {
            $base_date = $loan->next_due_date ? $loan->next_due_date : $loan->repayment_start_date;
            if (empty($base_date) || strtotime($base_date) < strtotime($payment_date)) {
                $base_date = $payment_date;
            }
            $candidate = date('Y-m-d', strtotime('+1 month', strtotime($base_date)));
            if (!empty($loan->repayment_end_date) && strtotime($candidate) > strtotime($loan->repayment_end_date)) {
                $candidate = $loan->repayment_end_date;
            }
            $next_due_date = $candidate;
        }

        $update = array(
            'total_paid'          => number_format($new_total_paid, 2, '.', ''),
            'last_deduction_date' => $payment_date,
            'next_due_date'       => $next_due_date,
        );

        return $this->db->where('transaction_id', $transaction_id)->update('office_loan_details', $update);
    }

        public function select_person_by_id($person_id) {
        $this->db->select('*');
        $this->db->from('person_information');
        $this->db->where('person_id', $person_id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

      public function personledger_tradational($person_id) {
        $this->db->select('
            person_ledger.*,
            sum(person_ledger.debit) as debit,
            sum(person_ledger.credit) as credit
            ');
        $this->db->from('person_ledger');
        $this->db->where('person_id', $person_id);
        $this->db->order_by('date', 'desc');
        $this->db->group_by('transaction_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
   


       public function ledger_search_by_date($person_id, $from_date, $to_date) {
        $this->db->select('
            person_ledger.*,
            sum(person_ledger.debit) as debit,
            sum(person_ledger.credit) as credit
            ');
        $this->db->from('person_ledger');
        $this->db->where('person_id', $person_id);
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->db->group_by('transaction_id');
        $this->db->order_by('date','desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

        //Submit payment
     public function submit_payment($data) {
        $result = $this->db->insert('person_ledger', $data);
        if ($result) {
            return true;
        } else {
            $error = json_encode($this->db->error());
            log_message('error', 'Failed to insert person_ledger record: ' . $error);
            error_log('[Loan_model::submit_payment] ' . $error);
            return false;
        }
    }


        // submit person for personal loan
    public function submit_person_personal_loan($data) {
      return   $result = $this->db->insert('pesonal_loan_information', $data);
        
       
    }

        //personal loan limit 
    public function person_list_limt_loan($per_page, $limit) {
        $this->db->select('
                pesonal_loan_information.*,
                sum(personal_loan.debit) as debit,
                sum(personal_loan.credit) as credit
            ');
        $this->db->from('pesonal_loan_information');
        $this->db->join('personal_loan', 'pesonal_loan_information.person_id = personal_loan.person_id', 'left');
        $this->db->where('pesonal_loan_information.status', 1);
        $this->db->group_by('pesonal_loan_information.person_id');
        $this->db->limit($per_page, $limit);
        $this->db->order_by('pesonal_loan_information.id', 'DESC');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


        public function person_list_personal_loan() {
        $this->db->select('*');
        $this->db->from('pesonal_loan_information');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
       }

        public function update_person($data, $person_id) {
        $this->db->where('person_id', $person_id);
        $result = $this->db->update('person_information', $data);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_office_loan($id = null){
         $this->db->where('person_id',$id)
            ->delete('person_information');
             $this->db->select('*');
        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieve a single office loan transaction.
     */
    public function get_office_loan_by_transaction($transaction_id)
    {
        return $this->db->select('pl.*, pi.person_name, pi.person_phone, pi.person_address, old.disbursement_date, old.repayment_period, old.repayment_start_date, old.repayment_end_date')
            ->from('person_ledger pl')
            ->join('person_information pi', 'pi.person_id = pl.person_id', 'left')
            ->join('office_loan_details old', 'old.transaction_id = pl.transaction_id', 'left')
            ->where('pl.transaction_id', $transaction_id)
            ->get()
            ->row_array();
    }

         // personal loan person id selecetd
        public function select_loan_person_by_id($person_id) {
        $this->db->select('*');
        $this->db->from('pesonal_loan_information');
        $this->db->where('person_id', $person_id);
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

        //Personal loan detail ledger
    public function personal_loan_tradational($person_id) {

        $this->db->select('
            personal_loan.*,
            sum(personal_loan.debit) as debit,
            sum(personal_loan.credit) as credit
            ');
        $this->db->from('personal_loan');
        $this->db->where('person_id', $person_id);
        $this->db->group_by('transaction_id');
        $this->db->order_by('per_loan_id','asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


        public function person_loan_search_by_date($person_id, $from_date, $to_date) {
        $this->db->select('
            personal_loan.*,
            sum(personal_loan.debit) as debit,
            sum(personal_loan.credit) as credit
            ');
        $this->db->from('personal_loan');
        $this->db->where('person_id', $person_id);
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $this->db->group_by('transaction_id');
        $this->db->order_by('per_loan_id','asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

       public function submit_loan_personal($data) {
        return $result = $this->db->insert('personal_loan', $data);
    }


        public function submit_payment_per_loan($data) {
        $result = $this->db->insert('personal_loan', $data);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

     public function update_person_personal($data, $person_id) {
        $this->db->where('person_id', $person_id);
        $result = $this->db->update('pesonal_loan_information', $data);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

      public function delete_personal_loan($id = null)
    {
         $this->db->where('person_id',$id)
            ->delete('personal_loan');
         $this->db->where('person_id',$id)
            ->delete('pesonal_loan_information');
        
        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    } 
}

