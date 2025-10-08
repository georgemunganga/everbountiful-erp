<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_model extends CI_Model {

     //insert beneficial type
   public function add_beneficial($data = array())
    {
        return $this->db->insert('salary_type',$data);
    }



//beneficial list
  public function salary_setupView()
    {
        return $this->db->select('*')   
            ->from('salary_type')
            ->order_by('salary_type_id', 'desc')
            ->get()
            ->result();
    }

     public function salarysetup_updateForm($id){
        $this->db->where('salary_type_id',$id);
        $query = $this->db->get('salary_type');
        return $query->result_array();
    }


    public function update_benefits($data = []){
        return $this->db->where('salary_type_id', $data["salary_type_id"])
            ->update("salary_type", $data);
            
    }

 
    public function benefits_delete($id = null)
    {
        $this->db->where('salary_type_id',$id)
            ->delete('salary_type');

        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    } 


       public function salary_typeName()
    {
        return $this->db->select('*')   
            ->from('salary_type')
             ->where('salary_type',1)
            ->get()
            ->result();
    }

        public function salary_typedName()
    {
        return $this->db->select('*')   
            ->from('salary_type')
             ->where('salary_type',0)
            ->get()
            ->result();
    }

        public function empdropdown(){
        $this->db->select('*');
        $this->db->from('employee_history');
        $query = $this->db->get();
        $data = $query->result();
       
        $list = array('' => 'Select One...');
        if (!empty($data) ) {
            foreach ($data as $value) {
                $list[$value->id] = $value->first_name." ".$value->last_name;
            } 
        }
        return $list;
    }


     public function check_exist($employee_id){
         return $this->db->select('*')   
            ->from('employee_salary_setup')
            ->where('employee_id',$employee_id)
            ->get()
            ->num_rows();

    }

     public function salary_setup_create($data = array())
    {
        return $this->db->insert('employee_salary_setup', $data);
    }

             public function salary_setupindex()
    {
             return $this->db->select('count(DISTINCT(sstp.e_s_s_id)) as e_s_s_id,sstp.*,p.id,p.first_name,p.last_name')   
            ->from('employee_salary_setup sstp')
            ->join('employee_history p', 'sstp.employee_id = p.id', 'left')
            ->group_by('sstp.employee_id')
            ->order_by('sstp.salary_type_id', 'desc')
            ->get()
            ->result();
    }


     public function salary_amountlft($id){
        return $result = $this->db->select('employee_salary_setup.*,salary_type.*') 
             ->from('employee_salary_setup')
             ->join('salary_type','salary_type.salary_type_id=employee_salary_setup.salary_type_id')
             ->where('employee_salary_setup.employee_id',$id)
             ->where('salary_type.salary_type',0)
             ->get()
             ->result();
    }

        public function salary_amount($id){
          return $result = $this->db->select('employee_salary_setup.*,salary_type.*') 
             ->from('employee_salary_setup')
             ->join('salary_type','salary_type.salary_type_id=employee_salary_setup.salary_type_id')
             ->where('employee_salary_setup.employee_id',$id)
             ->where('salary_type.salary_type',1)
             ->get()
             ->result();
    }


    public function employee_informationId($id)
    {
        return $result = $this->db->select('hrate as rate,rate_type')
                       ->from('employee_history')
                       ->where('id',$id)
                       ->get()
                       ->result_array();

    }

    public function update_sal_stup($data = array())
    {
        $term = array('employee_id' => $data['employee_id'], 'salary_type_id' => $data['salary_type_id']);

        return $this->db->where($term)
            ->update("employee_salary_setup", $data);
    }

    public function emp_salstup_delete($id = null){
        $this->db->where('employee_id',$id)
            ->delete('employee_salary_setup');
        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    } 

           public function salary_generateView($limit = null, $start = null)
    {
             return $this->db->select('*')   
            ->from('salary_sheet_generate')
            ->group_by('ssg_id')
            ->order_by('ssg_id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result();
    }

        public function sal_generate_delete($id = null) {
         $this->db->where('ssg_id',$id)
            ->delete('salary_sheet_generate');
            $this->db->where('generate_id',$id)
            ->delete('employee_salary_payment');
        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    } 


      public function emp_paymentView($limit = null, $start = null)
    {
            return $this->db->select('count(DISTINCT(pment.emp_sal_pay_id)) as emp_sal_pay_id,pment.*,p.id as employee_id,p.first_name,p.last_name')   
            ->from('employee_salary_payment pment')
            ->join('employee_history p', 'pment.employee_id = p.id', 'left')
            ->group_by('pment.emp_sal_pay_id')
            ->order_by('pment.emp_sal_pay_id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result();
    }

        public function update_payment($data = array())
    {
        return $this->db->where('emp_sal_pay_id', $data["emp_sal_pay_id"])
            ->update("employee_salary_payment", $data);
    }


        public function salary_paymentinfo($id = null){
            return $this->db->select('count(DISTINCT(pment.emp_sal_pay_id)) as emp_sal_pay_id,pment.*,p.id as employee_id,p.first_name,p.last_name,desig.designation as position_name,p.hrate as basic,p.rate_type as salarytype')   
            ->from('employee_salary_payment pment')
            ->join('employee_history p', 'pment.employee_id = p.id', 'left')
            ->join('designation desig', 'desig.id = p.designation', 'left')
            ->where('pment.emp_sal_pay_id',$id)
            ->group_by('pment.emp_sal_pay_id')
            ->get()
            ->result_array();

    }


    public function salary_addition_fields($id)
         {
        return $result = $this->db->select('employee_salary_setup.*,salary_type.*') 
             ->from('employee_salary_setup')
             ->join('salary_type','salary_type.salary_type_id=employee_salary_setup.salary_type_id')
             ->where('employee_salary_setup.employee_id',$id)
             ->where('salary_type.salary_type',1)
             ->get()
             ->result();
    }


    public function salary_deduction_fields($id){
        return $result = $this->db->select('employee_salary_setup.*,salary_type.*') 
             ->from('employee_salary_setup')
             ->join('salary_type','salary_type.salary_type_id=employee_salary_setup.salary_type_id')
             ->where('employee_salary_setup.employee_id',$id)
             ->where('salary_type.salary_type',0)
             ->get()
             ->result();
    }

        public function setting()
    {
        $settings = $this->db->get('web_setting')->result_array();
        
        // Add currency_symbol property for backward compatibility
        if (!empty($settings)) {
            foreach ($settings as &$setting) {
                if (isset($setting['currency'])) {
                    $setting['currency_symbol'] = $setting['currency'];
                }
            }
        }
        
        return $settings;
    }
    
        public function companyinfo()
    {
        return $this->db->get('company_information')->result_array();
    }

    // --------------------------------For new Payrole-----------------------------------

public function emp_salsetup_create($data = array())
	{
		return $this->db->insert('salary_type', $data);
	}

	public function delete_s_type($id = null)
	{
		$this->db->where('salary_type_id',$id)
			->delete('salary_type');

		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	} 

	public function update_em_salstup($data = array())
	{
		return $this->db->where('salary_type_id', $data["salary_type_id"])
			->update("salary_type", $data);
	}
	

	public function s_delete($id = null)
	{
		$this->db->where('employee_id',$id)
			->delete('employee_salary_setup');

		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	} 

	
	/* salary sheet generate  */
	public function salary_genrate_create($data = array())
	{
		return $this->db->insert('salary_sheet_generate', $data);
	}
	
	public function salary_gen_delete($id = null,$salname = null)
	{
		$this->db->where('ssg_id',$id)
			->delete('salary_sheet_generate');
		$this->db->where('salary_name',$salname)
			->delete('employee_salary_payment');
		$this->db->where('VNo',$salname)
			->delete('acc_transaction');	

		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	} 

	public function update_sal_gen($data = array())
	{
		return $this->db->where('ssg_id', $data["ssg_id"])
			->update("salary_sheet_generate", $data);
	}
	public function salargen_updateForm($id){
        $this->db->where('ssg_id',$id);
        $query = $this->db->get('salary_sheet_generate');
        return $query->row();
    }
    public function salary_head_create($data = array())
	{
		return $this->db->insert('salary_setup_header', $data);
	}

/* salary setup Update ********************************************/


	public function update_sal_head($data = array())
	{
		return $this->db->where('employee_id', $data["employee_id"])
			->update("salary_setup_header", $data);
	}

	public function salary_s_updateForm($id){
        $this->db->where('employee_id',$id);
        $query = $this->db->get('employee_salary_setup','salary_setup_header');
        return $query->row();
    }
/* salary setup Update ********************************************/


	public  function get_empid($id)
    {
        $query=$this->db->get_where('employee_salary_setup',array('employee_id'=>$id));
        return $query->row_array();
    } 
    public  function get_type($id)
    {
       
        return $result = $this->db->select('sal_type')
                       ->from('employee_salary_setup')
                       ->where('employee_id',$id)
                       ->get()
                       ->row_array();
    } 


    public function type()
	{
		$this->db->select('*');
        $this->db->from('employee_salary_setup');
        $query = $this->db->get();
        $data = $query->result();
       
        $list = array('' => 'Select One...');
       	if (!empty($data) ) {
       		foreach ($data as $value) {
       			$list[$value->sal_type] = $value->sal_type;
       		} 
       	}
       	return $list;
	}

	public function payable()
	{
		$this->db->select('*');
        $this->db->from('salary_setup_header');
        $query = $this->db->get();
        $data = $query->result();
       
         $list = array('' => 'Select One...');
       	if (!empty($data) ) {
       		foreach ($data as $value) {
       			$list[$value->salary_payable] = $value->salary_payable;
       		} 
       	}
       	return $list;
	}
	public  function get_payable($id)
    {
        
        return $result = $this->db->select('salary_payable')
                       ->from('salary_setup_header')
                       ->where('employee_id',$id)
                       ->get()
                       ->row_array();
    } 


public function create_employee_payment($data = array())
	{
		return $this->db->insert('employee_salary_payment', $data);

	}

	public function gmb_salary_generateView($limit = null, $start = null)
	{

        return  $this->db->select('ssg.*,u.first_name,u.last_name,uu.first_name as firstname_apv_by,uu.last_name as lastname_apv_by')   
            ->from('gmb_salary_sheet_generate ssg')
            ->join('users u', 'ssg.generate_by = u.id', 'left')
            ->join('users uu', 'ssg.approved_by = uu.id', 'left')
            ->order_by('ssg_id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result();

            
	}

	public function gmb_salary_generate_delete($id = null,$salname = null)
	{
		$this->db->where('ssg_id',$id)
			->delete('gmb_salary_sheet_generate');
		$this->db->where('sal_month_year',$salname)
			->delete('gmb_salary_generate');
		

		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	} 

	public function emp_salary_paymentView($limit = null, $start = null)
	{
			return $this->db->select('count(DISTINCT(pment.id)) as emp_sal_pay_id,pment.*,p.id as employee_id,p.first_name,p.last_name')   
            ->from('gmb_salary_generate pment')
            ->join('employee_history p', 'pment.employee_id = p.id', 'left')
            ->group_by('pment.id')
            ->order_by('pment.id', 'desc')
            ->limit($limit, $start)
            ->get()
            ->result();
	}

	public function salary_sheet_generate_info($ssg_id)
	{

		$salary_sheet_generate_info = $this->db->select('*')
                       ->from('gmb_salary_sheet_generate')
                       ->where('ssg_id',$ssg_id)
                       ->get()
                       ->row();
                       
        return $salary_sheet_generate_info;
	}


	public function employee_salary_charts($ssg_id)
	{
			$salary_sheet_generate_info = $this->db->select('*')
                       ->from('gmb_salary_sheet_generate')
                       ->where('ssg_id',$ssg_id)
                       ->get()
                       ->row();

			return $this->db->select('count(DISTINCT(pment.id)) as emp_sal_pay_id,pment.*,p.id as employee_id,p.first_name,p.last_name')   
            ->from('gmb_salary_generate pment')
            ->join('employee_history p', 'pment.employee_id = p.id', 'left')
            ->group_by('pment.id')
            ->order_by('pment.id', 'desc')
            ->where('pment.sal_month_year',$salary_sheet_generate_info->name)
            ->get()
            ->result();
	}

	/* Payroll related functionality starts from 16th april 2022*/

	public function salary_advance_deduction($emp_id,$salary_month)
	{

		$query = 'SELECT * FROM `gmb_salary_advance` WHERE `salary_month` = '."'".$salary_month."'".' AND `employee_id` = '.$emp_id.' AND (`amount` - `release_amount`) > 0';

		return $this->db->query($query)->row();
	}

	public function update_sal_advance($data = array())
	{
		

		return $this->db->where('id', $data['id'])
			->update("gmb_salary_advance", $data);
	}

	public function loan_installment_deduction($emp_id)
	{
		$loan_status = 1;

		$query = 'SELECT * FROM `grand_loan` WHERE `employee_id` = '.$emp_id.' AND `loan_status` = '.$loan_status.' AND (`installment_period` - `installment_cleared`) > 0';

		return $this->db->query($query)->row();
	}

	public function update_loan_installment($data = array())
	{
		

		return $this->db->where('loan_id', $data['loan_id'])
			->update("grand_loan", $data);
	}

	// Office loan installment deduction for payroll
	public function office_loan_installment_deduction($emp_id, $current_date = null)
	{
		if (!$current_date) {
			$current_date = date('Y-m-d');
		}

		$query = "SELECT 
			old.*, 
			pl.transaction_id,
			pi.person_id,
			pi.employee_id,
			(old.principal_amount - IFNULL(old.total_paid, 0)) AS remaining_balance,
			LEAST(old.monthly_installment, (old.principal_amount - IFNULL(old.total_paid, 0))) AS payable_amount,
			CASE 
				WHEN old.next_due_date IS NULL THEN old.repayment_start_date
				ELSE old.next_due_date
			END AS next_payment_date
		FROM office_loan_details old
		JOIN person_ledger pl ON pl.transaction_id = old.transaction_id
		JOIN person_information pi ON pi.person_id = pl.person_id
		WHERE pi.employee_id = ? 
		AND old.monthly_installment > 0
		AND (old.principal_amount - IFNULL(old.total_paid, 0)) > 0
		AND (old.repayment_start_date IS NULL OR old.repayment_start_date <= ?)
		AND (old.repayment_end_date IS NULL OR old.repayment_end_date >= ?)
		AND (
			old.next_due_date IS NULL 
			OR old.next_due_date <= ?
		)
		ORDER BY old.next_due_date IS NULL ASC, old.next_due_date ASC, old.disbursement_date ASC
		LIMIT 1";

		return $this->db->query($query, array($emp_id, $current_date, $current_date, $current_date))->row();
	}

	// Update office loan payment after deduction
	public function update_office_loan_payment($data = array())
	{
		$transaction_id = $data['transaction_id'];
		$payment_amount = $data['payment_amount'];
		$payment_date = $data['payment_date'];

		// Update office_loan_details
		$this->db->query("UPDATE office_loan_details 
			SET total_paid = IFNULL(total_paid, 0) + ?,
				last_deduction_date = ?,
				next_due_date = DATE_ADD(?, INTERVAL 1 MONTH)
			WHERE transaction_id = ?", 
			array($payment_amount, $payment_date, $payment_date, $transaction_id));

		// Add credit entry to person_ledger
		$ledger_data = array(
			'person_id' => $data['person_id'],
			'transaction_id' => $transaction_id,
			'date' => $payment_date,
			'debit' => 0,
			'credit' => $payment_amount,
			'details' => 'Payroll deduction - ' . date('M Y', strtotime($payment_date))
		);

		return $this->db->insert('person_ledger', $ledger_data);
	}

	// Get office loan summary for employee
	public function get_employee_office_loan_summary($emp_id)
	{
		$query = "SELECT 
			old.*,
			pl.transaction_id,
			pi.person_id,
			(old.principal_amount - IFNULL(old.total_paid, 0)) as remaining_balance,
			CASE 
				WHEN old.principal_amount > 0 AND old.monthly_installment > 0 
				THEN CEIL((old.principal_amount - IFNULL(old.total_paid, 0)) / old.monthly_installment)
				ELSE 0
			END as remaining_installments
		FROM office_loan_details old
		JOIN person_ledger pl ON pl.transaction_id = old.transaction_id
		JOIN person_information pi ON pi.person_id = pl.person_id
		WHERE pi.employee_id = ? 
		AND old.monthly_installment > 0
		AND (old.principal_amount - IFNULL(old.total_paid, 0)) > 0
		ORDER BY old.disbursement_date DESC";

		return $this->db->query($query, array($emp_id))->result();
	}

	public function employee_salary_generate_info($id)
	{

		return $this->db->select('pment.*,p.id as employee_id,p.first_name,p.last_name')   
        ->from('gmb_salary_generate pment')
        ->join('employee_history p', 'pment.employee_id = p.id', 'left')
        ->where('pment.id', $id)
        ->get()
        ->row();
	}

	// employee Information
	public function employee_info($id)
	{
		return $result = $this->db->select('emp.*,p.designation')
                       ->from('employee_history emp')
                       ->join('designation p', 'emp.designation = p.id', 'left')
                       ->where('emp.id',$id)
                       ->get()
                       ->row();

	}

	// employee Information
	public function payment_natures()
	{
		$results = $this->db->select('HeadCode,HeadName,PHeadName,IsActive,isCashNature,isBankNature')
                       ->from('acc_coa')
                       ->where('isCashNature',1)
                       ->or_where('isBankNature',1)
                       ->get()
                       ->result();

        $respo_arr = array();
        foreach ($results as $key => $value) {
        	if($value->IsActive == 1){
        		$respo_arr[$value->HeadCode] = $value->HeadName;
        	}
        }
        return $respo_arr;

	}

	// employee Information
	public function payment_natures_bank()
	{
		$results = $this->db->select('HeadCode,HeadName,PHeadName,IsActive,isCashNature,isBankNature')
                       ->from('acc_coa')
                       ->or_where('isBankNature',1)
                       ->get()
                       ->result();

        $respo_arr = array();
        foreach ($results as $key => $value) {
        	if($value->IsActive == 1){
        		$respo_arr[$value->HeadCode] = $value->HeadName;
        	}
        }
        return $respo_arr;

	}

	public function update_salary_as_approved($ssg_id,$data = array())
	{
		return $this->db->where('ssg_id', $ssg_id)
			->update("gmb_salary_sheet_generate", $data);
	}

	public function ensure_tax_slab_support()
	{
		if ($this->db->table_exists('hrm_salary_components')) {
			$column = $this->db->query("SHOW COLUMNS FROM `hrm_salary_components` LIKE 'amount_type'");
			if ($column && $column->num_rows() > 0) {
				$row = $column->row();
				if (strpos($row->Type, 'tax_slab') === false) {
					$this->db->query("ALTER TABLE `hrm_salary_components` MODIFY `amount_type` ENUM('fixed','percentage','tax_slab') NOT NULL DEFAULT 'fixed'");
				}
			}
		}

		if (!$this->db->table_exists('hrm_salary_component_tax_slabs')) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `hrm_salary_component_tax_slabs` (
				`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`component_id` int(10) UNSIGNED NOT NULL,
				`slab_id` int(10) UNSIGNED NOT NULL,
				`created_at` datetime DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `component_id` (`component_id`),
				KEY `slab_id` (`slab_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		}
	}

	public function get_salary_components($only_active = false)
	{
		$this->db->from('hrm_salary_components');
		if ($only_active) {
			$this->db->where('status', 1);
		}
		$components = $this->db->order_by('component_name', 'asc')->get()->result();

		if (!empty($components) && $this->db->table_exists('hrm_salary_component_tax_slabs')) {
			$ids = array();
			foreach ($components as $component) {
				if (isset($component->id)) {
					$ids[] = (int) $component->id;
				}
			}

			if (!empty($ids)) {
				$map = $this->get_component_tax_slabs_map($ids);
				foreach ($components as $component) {
					$component_id = isset($component->id) ? (int) $component->id : 0;
					$component->tax_slabs = isset($map[$component_id]) ? $map[$component_id] : array();
				}
			}
		}

		return $components;
	}

	public function get_salary_component($id)
	{
		return $this->db->get_where('hrm_salary_components', ['id' => $id])->row();
	}

	public function insert_salary_component($data = array())
	{
		return $this->db->insert('hrm_salary_components', $data);
	}

	public function update_salary_component($id, $data = array())
	{
		return $this->db->where('id', $id)->update('hrm_salary_components', $data);
	}

	public function delete_salary_component($id)
	{
		$deleted = $this->db->where('id', $id)->delete('hrm_salary_components');

		if ($deleted && $this->db->table_exists('hrm_salary_component_tax_slabs')) {
			$this->db->where('component_id', (int) $id)->delete('hrm_salary_component_tax_slabs');
		}

		return $deleted;
	}

		public function replace_component_tax_slabs($component_id, $slab_ids = array())
	{
		if (!$this->db->table_exists('hrm_salary_component_tax_slabs')) {
			return true;
		}

		$component_id = (int) $component_id;
		$this->db->where('component_id', $component_id)->delete('hrm_salary_component_tax_slabs');

		$unique_ids = array_unique(array_filter(array_map('intval', (array) $slab_ids)));
		if (empty($unique_ids)) {
			return true;
		}

		$rows = array();
		$now  = date('Y-m-d H:i:s');
		foreach ($unique_ids as $slab_id) {
			$rows[] = array(
				'component_id' => $component_id,
				'slab_id'      => (int) $slab_id,
				'created_at'   => $now,
			);
		}

		return $this->db->insert_batch('hrm_salary_component_tax_slabs', $rows);
	}

	public function get_component_tax_slabs_map($component_ids = array())
	{
		if (!$this->db->table_exists('hrm_salary_component_tax_slabs')) {
			return array();
		}

		if (!empty($component_ids)) {
			$component_ids = array_unique(array_filter(array_map('intval', (array) $component_ids)));
			if (empty($component_ids)) {
				return array();
			}
		}

		$this->db->select('cts.component_id, cts.slab_id, ts.min_amount, ts.max_amount, ts.rate_percent, ts.additional_amount');
		$this->db->from('hrm_salary_component_tax_slabs cts');
		$this->db->join('hrm_tax_slabs ts', 'ts.id = cts.slab_id', 'left');
		if (!empty($component_ids)) {
			$this->db->where_in('cts.component_id', $component_ids);
		}
		$result = $this->db->get()->result();

		$map = array();
		if (!empty($result)) {
			foreach ($result as $row) {
				$component_id = isset($row->component_id) ? (int) $row->component_id : 0;
				$slab_id      = isset($row->slab_id) ? (int) $row->slab_id : 0;
				$min_value   = isset($row->min_amount) ? (float) $row->min_amount : 0.0;
				$min_label   = number_format($min_value, 2);
				$max_raw      = isset($row->max_amount) ? $row->max_amount : null;
				$max_value   = ($max_raw !== null) ? (float) $max_raw : null;
				$max_label   = ($max_value !== null) ? number_format($max_value, 2) : 'No limit';
				$rate_value  = isset($row->rate_percent) ? (float) $row->rate_percent : 0.0;
				$rate_label  = number_format($rate_value, 2);
				$additional_value = isset($row->additional_amount) ? (float) $row->additional_amount : 0.0;
				$label        = $min_label . ' - ' . $max_label . ' @ ' . $rate_label . '%';

				$map[$component_id][] = array(
					'id'                => $slab_id,
					'label'             => $label,
					'min_amount'        => $min_value,
					'max_amount'        => $max_value,
					'rate_percent'      => $rate_value,
					'additional_amount' => $additional_value,
				);
			}
		}

		return $map;
	}


	public function calculate_component_breakdown($salary_info = null)
	{
		$breakdown = array(
			'earnings' => array(),
			'deductions' => array(),
			'earning_total' => 0.0,
			'deduction_total' => 0.0,
		);

		if (empty($salary_info) || !$this->db->table_exists('hrm_salary_components')) {
			return $breakdown;
		}

		$this->ensure_tax_slab_support();

		$components = $this->get_salary_components(true);
		if (empty($components)) {
			return $breakdown;
		}

		$gross_amount = isset($salary_info->gross_salary) ? (float) $salary_info->gross_salary : 0.0;
		$basic_amount = isset($salary_info->basic_salary_pro_rated) ? (float) $salary_info->basic_salary_pro_rated : (isset($salary_info->basic) ? (float) $salary_info->basic : 0.0);
		$net_amount   = isset($salary_info->net_salary) ? (float) $salary_info->net_salary : 0.0;

		foreach ($components as $component) {
			$component_status = isset($component->status) ? (string) $component->status : '1';
			if ($component_status !== '1') {
				continue;
			}

			$amount = $this->calculate_component_amount($component, $salary_info, $gross_amount, $basic_amount, $net_amount);
			if (abs($amount) < 0.0001) {
				continue;
			}

			$rounded = round($amount, 2);
			$entry = array(
				'name'   => isset($component->component_name) ? $component->component_name : '',
				'code'   => isset($component->component_code) ? $component->component_code : '',
				'amount' => $rounded,
			);

			$type = isset($component->component_type) ? strtolower((string) $component->component_type) : 'earning';
			if ($type === 'earning') {
				$breakdown['earnings'][] = $entry;
				$breakdown['earning_total'] += $rounded;
			} else {
				$breakdown['deductions'][] = $entry;
				$breakdown['deduction_total'] += $rounded;
			}
		}

		$breakdown['earning_total'] = round($breakdown['earning_total'], 2);
		$breakdown['deduction_total'] = round($breakdown['deduction_total'], 2);

		return $breakdown;
	}

	protected function calculate_component_amount($component, $salary_info, $gross_amount, $basic_amount, $net_amount)
	{
		$amount_type = isset($component->amount_type) ? strtolower((string) $component->amount_type) : 'fixed';

		switch ($amount_type) {
			case 'percentage':
				$percentage = isset($component->amount_value) ? (float) $component->amount_value : 0.0;
				if ($percentage === 0.0) {
					return 0.0;
				}

				$base_key = isset($component->percentage_base) ? strtolower((string) $component->percentage_base) : 'gross';
				$base_amount = $gross_amount;
				if ($base_key === 'basic') {
					$base_amount = $basic_amount;
				} elseif ($base_key === 'net') {
					$base_amount = $net_amount > 0 ? $net_amount : $gross_amount;
				}

				return ($base_amount * $percentage) / 100.0;

			case 'tax_slab':
				return $this->calculate_tax_slab_component_amount($component, $gross_amount);

			case 'fixed':
			default:
				return isset($component->amount_value) ? (float) $component->amount_value : 0.0;
		}
	}

	protected function calculate_tax_slab_component_amount($component, $base_amount)
	{
		if ($base_amount <= 0 || empty($component->tax_slabs) || !is_array($component->tax_slabs)) {
			return 0.0;
		}

		$slabs = $component->tax_slabs;
		usort($slabs, function ($a, $b) {
			$aMin = isset($a['min_amount']) ? (float) $a['min_amount'] : 0.0;
			$bMin = isset($b['min_amount']) ? (float) $b['min_amount'] : 0.0;
			if ($aMin === $bMin) {
				return 0;
			}

			return ($aMin < $bMin) ? -1 : 1;
		});

		$amount = 0.0;
		foreach ($slabs as $slab) {
			$min = isset($slab['min_amount']) ? (float) $slab['min_amount'] : 0.0;
			$max = (isset($slab['max_amount']) && $slab['max_amount'] !== null) ? (float) $slab['max_amount'] : null;
			$rate = isset($slab['rate_percent']) ? (float) $slab['rate_percent'] : 0.0;
			$additional = isset($slab['additional_amount']) ? (float) $slab['additional_amount'] : 0.0;

			if ($rate <= 0) {
				continue;
			}

			if ($max !== null && $max > $min) {
				if ($base_amount > $min && $base_amount <= $max) {
					$amount = (($base_amount - $min) * $rate / 100.0) + $additional;
					break;
				}
			} else {
				if ($base_amount > $min) {
					$amount = (($base_amount - $min) * $rate / 100.0) + $additional;
					break;
				}
			}
		}

		return $amount;
	}

public function get_tax_slabs($only_active = false)
	{
		$this->db->from('hrm_tax_slabs');
		if ($only_active) {
			$this->db->where('status', 1);
		}
		return $this->db->order_by('min_amount', 'asc')->get()->result();
	}

	public function get_tax_slab($id)
	{
		return $this->db->get_where('hrm_tax_slabs', ['id' => $id])->row();
	}

	public function insert_tax_slab($data = array())
	{
		return $this->db->insert('hrm_tax_slabs', $data);
	}

	public function update_tax_slab($id, $data = array())
	{
		return $this->db->where('id', $id)->update('hrm_tax_slabs', $data);
	}

	public function delete_tax_slab($id)
	{
		return $this->db->where('id', $id)->delete('hrm_tax_slabs');
	}

	// Update office loan deduction after payroll processing
	public function update_office_loan_deduction($data = array())
	{
		$transaction_id = $data['transaction_id'];
		$deduction_amount = $data['deduction_amount'];
		$deduction_date = $data['deduction_date'];

		// Update office_loan_details
		$this->db->query("UPDATE office_loan_details 
			SET total_paid = LEAST(principal_amount, IFNULL(total_paid, 0) + ?),
				last_deduction_date = ?,
				next_due_date = CASE 
					WHEN repayment_end_date IS NOT NULL AND DATE_ADD(?, INTERVAL 1 MONTH) > repayment_end_date 
						THEN repayment_end_date
					ELSE DATE_ADD(?, INTERVAL 1 MONTH)
				END
			WHERE transaction_id = ?", 
			array($deduction_amount, $deduction_date, $deduction_date, $deduction_date, $transaction_id));

		// Get person_id for this transaction
		$person_query = $this->db->select('pl.person_id')
			->from('person_ledger pl')
			->where('pl.transaction_id', $transaction_id)
			->limit(1)
			->get();

		if ($person_query->num_rows() > 0) {
			$person_id = $person_query->row()->person_id;

			// Add credit entry to person_ledger
			$ledger_data = array(
				'person_id' => $person_id,
				'transaction_id' => $transaction_id,
				'date' => $deduction_date,
				'debit' => 0,
				'credit' => $deduction_amount,
				'details' => 'Payroll deduction - ' . date('M Y', strtotime($deduction_date))
			);

			return $this->db->insert('person_ledger', $ledger_data);
		}

		return false;
	}

}
