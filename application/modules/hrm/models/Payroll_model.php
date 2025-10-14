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
		return  $this->db->select('ssg.*, sg.group_name, u.first_name, u.last_name, uu.first_name as firstname_apv_by, uu.last_name as lastname_apv_by')
			->from('gmb_salary_sheet_generate ssg')
			->join('hrm_salary_groups sg', 'sg.id = ssg.salary_group_id', 'left')
			->join('users u', 'ssg.generate_by = u.id', 'left')
			->join('users uu', 'ssg.approved_by = uu.id', 'left')
			->order_by('ssg.ssg_id', 'desc')
			->limit($limit, $start)
			->get()
			->result();
	}

		public function gmb_salary_generate_delete($id = null,$salname = null)
		{
			$sheet = $this->db->select('salary_group_id, name')
				->from('gmb_salary_sheet_generate')
				->where('ssg_id', $id)
				->get()
				->row();

			if (!$sheet) {
				return false;
			}

			$this->db->trans_start();

			$salary_rows = $this->db->select('g.id, g.employee_id, g.office_loan_deduct, g.office_loan_transaction_id, g.createDate')
				->from('gmb_salary_generate g')
				->where('g.sal_month_year', $sheet->name);
			if (isset($sheet->salary_group_id) && $sheet->salary_group_id !== null) {
				$salary_rows = $salary_rows->where('g.salary_group_id', (int) $sheet->salary_group_id);
			}
			$salary_rows = $salary_rows->get()->result();

			foreach ($salary_rows as $salary_row) {
				if (!empty($salary_row->office_loan_transaction_id) && floatval($salary_row->office_loan_deduct) > 0) {
					$this->revert_office_loan_deduction(
						$salary_row->office_loan_transaction_id,
						$salary_row->createDate,
						floatval($salary_row->office_loan_deduct)
					);
				}
			}

			$this->db->where('ssg_id', $id)->delete('gmb_salary_sheet_generate');

			$this->db->where('sal_month_year', $sheet->name);
			if (isset($sheet->salary_group_id) && $sheet->salary_group_id !== null) {
				$this->db->where('salary_group_id', (int) $sheet->salary_group_id);
			}
			$this->db->delete('gmb_salary_generate');

			$this->db->trans_complete();

			return $this->db->trans_status();
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

		return $this->db->select('ssg.*, sg.group_name')
			->from('gmb_salary_sheet_generate ssg')
			->join('hrm_salary_groups sg', 'sg.id = ssg.salary_group_id', 'left')
			->where('ssg.ssg_id', $ssg_id)
			->get()
			->row();
	}


	public function employee_salary_charts($ssg_id)
	{
		$salary_sheet_generate_info = $this->salary_sheet_generate_info($ssg_id);
		if (!$salary_sheet_generate_info) {
			return array();
		}

		$this->db->select('count(DISTINCT(pment.id)) as emp_sal_pay_id, pment.*, p.id as employee_id, p.first_name, p.last_name, sg.group_name');
		$this->db->from('gmb_salary_generate pment');
		$this->db->join('employee_history p', 'pment.employee_id = p.id', 'left');
		$this->db->join('hrm_salary_groups sg', 'sg.id = pment.salary_group_id', 'left');
		$this->db->group_by('pment.id');
		$this->db->order_by('pment.id', 'desc');
		$this->db->where('pment.sal_month_year', $salary_sheet_generate_info->name);
		if (!empty($salary_sheet_generate_info->salary_group_id)) {
			$this->db->where('pment.salary_group_id', $salary_sheet_generate_info->salary_group_id);
		}

		return $this->db->get()->result();
	}

	/* Payroll related functionality starts from 16th april 2022*/

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
	public function office_loan_installment_deduction($emp_id, $current_date = null, $period_end = null)
	{
		if (!$current_date) {
			$current_date = date('Y-m-d');
		}

		$anchor_date = $current_date;
		$window_end = $period_end ?: $anchor_date;
		if (strtotime($window_end) === false || strtotime($window_end) < strtotime($anchor_date)) {
			$window_end = $anchor_date;
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

		return $this->db->query($query, array($emp_id, $window_end, $anchor_date, $window_end))->row();
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

		return $this->db->select('pment.*, p.id as employee_id, p.first_name, p.last_name, sg.group_name')
			->from('gmb_salary_generate pment')
			->join('employee_history p', 'pment.employee_id = p.id', 'left')
			->join('hrm_salary_groups sg', 'sg.id = pment.salary_group_id', 'left')
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

	public function ensure_salary_group_support()
	{
		if (!$this->db->table_exists('hrm_salary_groups')) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `hrm_salary_groups` (
				`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`group_name` varchar(100) NOT NULL,
				`description` text DEFAULT NULL,
				`status` tinyint(1) NOT NULL DEFAULT 1,
				`created_by` int(11) DEFAULT NULL,
				`created_at` datetime NOT NULL DEFAULT current_timestamp(),
				`updated_by` int(11) DEFAULT NULL,
				`updated_at` datetime DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `group_name` (`group_name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
		}

		if (!$this->db->table_exists('hrm_salary_group_employees')) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `hrm_salary_group_employees` (
				`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`group_id` int(10) UNSIGNED NOT NULL,
				`employee_id` int(11) NOT NULL,
				`assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
				PRIMARY KEY (`id`),
				KEY `group_id` (`group_id`),
				KEY `employee_id` (`employee_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
		}

		if (!$this->db->table_exists('hrm_salary_group_components')) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `hrm_salary_group_components` (
				`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`group_id` int(10) UNSIGNED NOT NULL,
				`component_id` int(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
				KEY `group_id` (`group_id`),
				KEY `component_id` (`component_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
		}

		if (!$this->db->table_exists('hrm_salary_group_tax_slabs')) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `hrm_salary_group_tax_slabs` (
				`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`group_id` int(10) UNSIGNED NOT NULL,
				`slab_id` int(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`),
				KEY `group_id` (`group_id`),
				KEY `slab_id` (`slab_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
		}

		if ($this->db->table_exists('gmb_salary_sheet_generate')) {
			if (!$this->column_exists('gmb_salary_sheet_generate', 'salary_group_id')) {
				$this->db->query("ALTER TABLE `gmb_salary_sheet_generate` ADD `salary_group_id` INT(10) UNSIGNED DEFAULT NULL AFTER `ssg_id`;");
			}
			if (!$this->column_exists('gmb_salary_sheet_generate', 'pay_date')) {
				$this->db->query("ALTER TABLE `gmb_salary_sheet_generate` ADD `pay_date` DATE DEFAULT NULL AFTER `gdate`;");
			}
		}

		if ($this->db->table_exists('gmb_salary_generate')) {
			if (!$this->column_exists('gmb_salary_generate', 'salary_group_id')) {
				$this->db->query("ALTER TABLE `gmb_salary_generate` ADD `salary_group_id` INT(10) UNSIGNED DEFAULT NULL AFTER `id`;");
			}
		}
	}

	public function get_salary_components($only_active = false, $group_id = null)
	{
		if ($group_id !== null) {
			$this->ensure_salary_group_support();
		}

		$this->db->select('c.*');
		$this->db->from('hrm_salary_components c');
		if ($group_id !== null) {
			$this->db->join('hrm_salary_group_components gc', 'gc.component_id = c.id', 'inner');
			$this->db->where('gc.group_id', (int) $group_id);
		}
		if ($only_active) {
			$this->db->where('c.status', 1);
		}
		$components = $this->db->order_by('c.component_name', 'asc')->get()->result();

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

		if ($group_id !== null) {
			$allowed_slab_ids = $this->get_salary_group_tax_slabs($group_id, true);
			if (!empty($allowed_slab_ids)) {
				$allowed_lookup = array_flip($allowed_slab_ids);
				foreach ($components as $component) {
					if (!empty($component->tax_slabs) && is_array($component->tax_slabs)) {
						$component->tax_slabs = array_values(array_filter($component->tax_slabs, function ($slab) use ($allowed_lookup) {
							$slab_id = isset($slab['slab_id']) ? (int) $slab['slab_id'] : 0;
							return $slab_id > 0 && isset($allowed_lookup[$slab_id]);
						}));
					}
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

	public function get_salary_group_tax_slabs($group_id, $return_ids = false)
	{
		$this->ensure_salary_group_support();

		$group_id = (int) $group_id;
		if ($group_id <= 0) {
			return $return_ids ? array() : array();
		}

		if ($return_ids) {
			$result = $this->db->select('slab_id')
				->from('hrm_salary_group_tax_slabs')
				->where('group_id', $group_id)
				->get()
				->result();

			$ids = array();
			if (!empty($result)) {
				foreach ($result as $row) {
					if (isset($row->slab_id)) {
						$ids[] = (int) $row->slab_id;
					}
				}
			}

			return $ids;
		}

		return $this->db->select('gt.*, ts.min_amount, ts.max_amount, ts.rate_percent, ts.additional_amount, ts.status')
			->from('hrm_salary_group_tax_slabs gt')
			->join('hrm_tax_slabs ts', 'ts.id = gt.slab_id', 'left')
			->where('gt.group_id', $group_id)
			->order_by('ts.min_amount', 'asc')
			->get()
			->result();
	}

	public function get_salary_groups($only_active = false)
	{
		$this->ensure_salary_group_support();

		$this->db->select('g.*,
			(SELECT COUNT(*) FROM hrm_salary_group_employees ge WHERE ge.group_id = g.id) AS employee_count,
			(SELECT COUNT(*) FROM hrm_salary_group_components gc WHERE gc.group_id = g.id) AS component_count,
			(SELECT COUNT(*) FROM hrm_salary_group_tax_slabs gt WHERE gt.group_id = g.id) AS tax_slab_count');
		$this->db->from('hrm_salary_groups g');
		if ($only_active) {
			$this->db->where('g.status', 1);
		}
		return $this->db->order_by('g.group_name', 'asc')->get()->result();
	}

	public function get_salary_group($group_id)
	{
		$this->ensure_salary_group_support();

		$group_id = (int) $group_id;
		if ($group_id <= 0) {
			return null;
		}

		return $this->db->get_where('hrm_salary_groups', array('id' => $group_id))->row();
	}

	public function get_salary_group_assignments($group_id)
	{
		$this->ensure_salary_group_support();

		$group_id = (int) $group_id;
		$result = array(
			'employees' => array(),
			'components' => array(),
			'tax_slabs' => array(),
		);

		if ($group_id <= 0) {
			return $result;
		}

		$employees = $this->db->select('employee_id')
			->from('hrm_salary_group_employees')
			->where('group_id', $group_id)
			->get()
			->result();
		foreach ($employees as $row) {
			if (isset($row->employee_id)) {
				$result['employees'][] = (int) $row->employee_id;
			}
		}

		$components = $this->db->select('component_id')
			->from('hrm_salary_group_components')
			->where('group_id', $group_id)
			->get()
			->result();
		foreach ($components as $row) {
			if (isset($row->component_id)) {
				$result['components'][] = (int) $row->component_id;
			}
		}

		$result['tax_slabs'] = $this->get_salary_group_tax_slabs($group_id, true);

		$result['employees'] = array_values(array_unique($result['employees']));
		$result['components'] = array_values(array_unique($result['components']));
		$result['tax_slabs'] = array_values(array_unique($result['tax_slabs']));

		return $result;
	}

	protected function replace_salary_group_assignments($group_id, $employee_ids = array(), $component_ids = array(), $tax_slab_ids = array())
	{
		$group_id = (int) $group_id;
		if ($group_id <= 0) {
			return false;
		}

		$employee_ids = $this->normalize_id_array($employee_ids);
		$component_ids = $this->normalize_id_array($component_ids);
		$tax_slab_ids = $this->normalize_id_array($tax_slab_ids);

		// Employees
		$this->db->where('group_id', $group_id)->delete('hrm_salary_group_employees');
		if (!empty($employee_ids)) {
			$now = date('Y-m-d H:i:s');
			$rows = array();
			foreach ($employee_ids as $employee_id) {
				$rows[] = array(
					'group_id'    => $group_id,
					'employee_id' => $employee_id,
					'assigned_at' => $now,
				);
			}
			$this->db->insert_batch('hrm_salary_group_employees', $rows);
		}

		// Components
		$this->db->where('group_id', $group_id)->delete('hrm_salary_group_components');
		if (!empty($component_ids)) {
			$rows = array();
			foreach ($component_ids as $component_id) {
				$rows[] = array(
					'group_id'     => $group_id,
					'component_id' => $component_id,
				);
			}
			$this->db->insert_batch('hrm_salary_group_components', $rows);
		}

		// Tax slabs
		$this->db->where('group_id', $group_id)->delete('hrm_salary_group_tax_slabs');
		if (!empty($tax_slab_ids)) {
			$rows = array();
			foreach ($tax_slab_ids as $slab_id) {
				$rows[] = array(
					'group_id' => $group_id,
					'slab_id'  => $slab_id,
				);
			}
			$this->db->insert_batch('hrm_salary_group_tax_slabs', $rows);
		}

		return true;
	}

	public function create_salary_group($data = array(), $employee_ids = array(), $component_ids = array(), $tax_slab_ids = array())
	{
		$this->ensure_salary_group_support();

		$this->db->trans_start();
		$this->db->insert('hrm_salary_groups', $data);
		$group_id = (int) $this->db->insert_id();
		if ($group_id > 0) {
			$this->replace_salary_group_assignments($group_id, $employee_ids, $component_ids, $tax_slab_ids);
		}
		$this->db->trans_complete();

		if (!$this->db->trans_status()) {
			return false;
		}

		return $group_id;
	}

	public function update_salary_group($group_id, $data = array(), $employee_ids = array(), $component_ids = array(), $tax_slab_ids = array())
	{
		$this->ensure_salary_group_support();

		$group_id = (int) $group_id;
		if ($group_id <= 0) {
			return false;
		}

		$current = $this->get_salary_group($group_id);
		if (!$current) {
			return false;
		}

		$this->db->trans_start();
		$this->db->where('id', $group_id)->update('hrm_salary_groups', $data);
		$this->replace_salary_group_assignments($group_id, $employee_ids, $component_ids, $tax_slab_ids);
		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	public function delete_salary_group($group_id)
	{
		$this->ensure_salary_group_support();

		$group_id = (int) $group_id;
		if ($group_id <= 0) {
			return false;
		}

		$current = $this->get_salary_group($group_id);
		if (!$current) {
			return false;
		}

		$this->db->trans_start();
		$this->db->where('group_id', $group_id)->delete('hrm_salary_group_employees');
		$this->db->where('group_id', $group_id)->delete('hrm_salary_group_components');
		$this->db->where('group_id', $group_id)->delete('hrm_salary_group_tax_slabs');
		$this->db->where('id', $group_id)->delete('hrm_salary_groups');
		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	public function get_all_employees_for_groups()
	{
		return $this->db->select('e.id, e.first_name, e.last_name, e.designation, d.designation as designation_name, e.phone')
			->from('employee_history e')
			->join('designation d', 'd.id = e.designation', 'left')
			->order_by('e.first_name', 'asc')
			->order_by('e.last_name', 'asc')
			->get()
			->result();
	}

	public function get_salary_group_employees($group_id)
	{
		$this->ensure_salary_group_support();

		$group_id = (int) $group_id;
		if ($group_id <= 0) {
			return array();
		}

		return $this->db->select('e.*')
			->from('hrm_salary_group_employees ge')
			->join('employee_history e', 'e.id = ge.employee_id', 'inner')
			->where('ge.group_id', $group_id)
			->order_by('e.first_name', 'asc')
			->order_by('e.last_name', 'asc')
			->get()
			->result();
	}

	public function calculate_component_breakdown($salary_info = null, $group_id = null)
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

		if ($group_id === null && is_object($salary_info) && isset($salary_info->salary_group_id)) {
			$group_id = (int) $salary_info->salary_group_id;
		}

		$this->ensure_tax_slab_support();

		$components = $this->get_salary_components(true, $group_id);
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

	protected function column_exists($table, $column)
	{
		$table = trim((string) $table);
		$column = trim((string) $column);
		if ($table === '' || $column === '') {
			return false;
		}

		$query = $this->db->query("SHOW COLUMNS FROM `{$table}` LIKE " . $this->db->escape($column));
		return $query && $query->num_rows() > 0;
	}

	protected function normalize_id_array($values = array())
	{
		$values = array_map('intval', (array) $values);
		$values = array_filter($values, function ($value) {
			return $value > 0;
		});

		return array_values(array_unique($values));
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

	private function generate_person_ledger_transaction_id()
	{
		do {
			$candidate = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
			$exists = $this->db->select('transaction_id')
				->from('person_ledger')
				->where('transaction_id', $candidate)
				->limit(1)
				->get()
				->num_rows() > 0;
		} while ($exists);

		return $candidate;
	}

		// Update office loan deduction after payroll processing
		public function update_office_loan_deduction($data = array())
		{
			$transaction_id = $data['transaction_id'];
			$deduction_amount = $data['deduction_amount'];
			$deduction_date = $data['deduction_date'];

			$this->db->trans_start();

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

			$success = false;
			if ($person_query->num_rows() > 0) {
				$person_id = $person_query->row()->person_id;

				// Add credit entry to person_ledger
				$ledger_transaction_id = $this->generate_person_ledger_transaction_id();
				$ledger_data = array(
					'person_id' => $person_id,
					'transaction_id' => $ledger_transaction_id,
					'date' => $deduction_date,
					'debit' => 0,
					'credit' => $deduction_amount,
					'details' => 'Payroll deduction - ' . date('M Y', strtotime($deduction_date)) . ' (Loan ref: ' . $transaction_id . ')'
				);

				$success = $this->db->insert('person_ledger', $ledger_data);
			}

			$this->db->trans_complete();

			return $success && $this->db->trans_status();
		}

		private function recalculate_office_loan_progress($loan_transaction_id)
		{
			$loan = $this->db->select('*')
				->from('office_loan_details')
				->where('transaction_id', $loan_transaction_id)
				->limit(1)
				->get()
				->row();

			if (!$loan) {
				return;
			}

			$person_row = $this->db->select('person_id')
				->from('person_ledger')
				->where('transaction_id', $loan_transaction_id)
				->where('debit >', 0)
				->limit(1)
				->get()
				->row();

			$person_id = $person_row ? $person_row->person_id : null;
			if (!$person_id) {
				return;
			}

			$credits = $this->db->select('date, credit')
				->from('person_ledger')
				->where('person_id', $person_id)
				->where('credit >', 0)
				->like('details', 'Loan ref: ' . $loan_transaction_id)
				->order_by('date', 'asc')
				->get()
				->result();

			$total_paid = 0.0;
			$last_date = null;
			foreach ($credits as $row) {
				$total_paid += floatval($row->credit);
				$last_date = $row->date;
			}

			$total_paid = min(floatval($loan->principal_amount), $total_paid);

			$payments_made = count($credits);
			$next_due_date = null;
			$repayment_start = !empty($loan->repayment_start_date) ? $loan->repayment_start_date : $loan->disbursement_date;
			if (!empty($repayment_start) && $total_paid < floatval($loan->principal_amount)) {
				try {
					$next_due = new DateTime($repayment_start);
					if ($payments_made > 0) {
						$next_due->modify('+' . $payments_made . ' month');
					}
					$candidate = $next_due->format('Y-m-d');
					if (!empty($loan->repayment_end_date) && strtotime($candidate) > strtotime($loan->repayment_end_date)) {
						$candidate = $loan->repayment_end_date;
					}
					$next_due_date = $candidate;
				} catch (Exception $exception) {
					$next_due_date = null;
				}
			}

			if ($total_paid >= floatval($loan->principal_amount)) {
				$next_due_date = null;
			}

			$this->db->where('transaction_id', $loan_transaction_id)->update('office_loan_details', array(
				'total_paid' => $total_paid,
				'last_deduction_date' => $last_date ? $last_date : null,
				'next_due_date' => $next_due_date,
			));
		}

		public function revert_office_loan_deduction($loan_transaction_id, $deduction_date, $amount)
		{
			if (empty($loan_transaction_id) || empty($deduction_date) || floatval($amount) <= 0) {
				return true;
			}

			$this->db->where('date', $deduction_date)
				->where('credit', floatval($amount))
				->like('details', 'Loan ref: ' . $loan_transaction_id)
				->delete('person_ledger');

			$this->recalculate_office_loan_progress($loan_transaction_id);

			return true;
		}

	}
