<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->db->query('SET SESSION sql_mode = ""');
		$this->load->model(array(
			'Payroll_model',
			'account/Accounts_model',
		));	
		$this->load->library('numbertowords');

		if (! $this->session->userdata('isLogIn'))
			redirect('login');	 
	}

	public function emp_salary_setup_view(){   
		$this->permission1->method('payroll','read')->access();
		$data['title']    = display('view_salary_setup');  ;
		$data['emp_sl']   = $this->Payroll_model->salary_setupView();
		$data['module']   = "payroll";
		$data['page']     = "emp_sal_setupview";   
		echo Modules::run('template/layout', $data); 
	} 


	public function create_salary_setup(){ 
		$data['title'] = display('selectionlist');
		#-------------------------------#
		$this->form_validation->set_rules('sal_name',display('sal_name'),'required|max_length[50]');
		$this->form_validation->set_rules('emp_sal_type',display('emp_sal_type'));
		
		#-------------------------------#
		if ($this->form_validation->run() === true) {

			$postData = [
				'sal_name'        => $this->input->post('sal_name',true),
				'emp_sal_type' 	  => $this->input->post('emp_sal_type',true),
			];   

			if ($this->Payroll_model->emp_salsetup_create($postData)) { 
				$this->session->set_flashdata('message', display('successfully_saved'));
			} else {
				$this->session->set_flashdata('exception',  display('please_try_again'));
			}
			redirect("payroll/Payroll/create_salary_setup");
		} else {
			$data['title']  = display('salary_type');
			$data['module'] = "payroll";
			$data['page']   = "emp_salarysetup_form";
			$data['emp_sl'] = $this->Payroll_model->salary_setupView(); 
			echo Modules::run('template/layout', $data);   
			
		}   
	}
	public function delete_emp_salarysetup($id = null){ 
		$this->permission1->method('payroll','delete')->access();

		if ($this->Payroll_model->emp_salstup_delete($id)) {
			#set success message
			$this->session->set_flashdata('message',display('delete_successfully'));
		} else {
			#set exception message
			$this->session->set_flashdata('exception',display('please_try_again'));
		}
		redirect("payroll/Payroll/emp_salary_setup_view");
	}




	public function update_salsetup_form($id = null){
		$this->form_validation->set_rules('salary_type_id',null,'required|max_length[11]');
		$this->form_validation->set_rules('sal_name',display('sal_name'),'required|max_length[50]');
		$this->form_validation->set_rules('emp_sal_type',display('emp_sal_type')  ,'max_length[20]');
		#-------------------------------#
		if ($this->form_validation->run() === true) {

			$postData = [
				'salary_type_id' 	             => $this->input->post('salary_type_id',true),
				'sal_name' 	                     => $this->input->post('sal_name',true),
				'emp_sal_type' 		             => $this->input->post('emp_sal_type',true),
				
			]; 
			
			if ($this->Payroll_model->update_em_salstup($postData)) { 
				$this->session->set_flashdata('message', display('successfully_updated'));
			} else {
				$this->session->set_flashdata('exception',  display('please_try_again'));
			}
			redirect("payroll/Payroll/create_salary_setup/");

		} else {
			$data['title']  = display('update');
			$data['data']   =$this->Payroll_model->salarysetup_updateForm($id);
			$data['module'] = "payroll";
			$data['page']   = "update_salarysetup_form";   
			echo Modules::run('template/layout', $data); 
		}

	}


	public function salary_setup_view()
	{   
		$this->permission->module('payroll','read')->redirect();
		$data['title']         = display('view_salary_setup');  ;
		$data['emp_sl_setup']  = $this->Payroll_model->salary_setupindex();
		$data['module']        = "payroll";
		$data['page']          = "sal_setupview";   
		echo Modules::run('template/layout', $data); 
	} 


	public function create_s_setup(){ 
		$data['title'] = display('selectionlist');
		#-------------------------------#
		$this->form_validation->set_rules('employee_id',display('employee_id'),'required|max_length[50]');
		$this->form_validation->set_rules('sal_type',display('sal_type'));
		$this->form_validation->set_rules('amount[]',display('amount'));
		$this->form_validation->set_rules('salary_payable',display('salary_payable'));
		$this->form_validation->set_rules('absent_deduct',display('absent_deduct'));
		$this->form_validation->set_rules('tax_manager',display('tax_manager'));
		$amount=$this->input->post('amount',true);
		$is_percentage = $this->input->post('is_percentage',true);
		
		#-------------------------------#
		if ($this->form_validation->run() === true) {
			$date=date('Y-m-d');

			foreach($amount as $key=>$value)
			{	
				$postData = [
					'employee_id'           => $this->input->post('employee_id',true),
					'sal_type'              => $this->input->post('sal_type',true),
					'is_percentage'         => (!empty($is_percentage)?$is_percentage:0),
					'salary_type_id' 	    => $key,
					'amount' 	            => (!empty($value)?$value:0),
					'create_date'           => $date,
					'gross_salary'          => $this->input->post('gross_salary',true),
				]; 
					$this->Payroll_model->salary_setup_create($postData);
				
			}

			if($this->input->post('absent_deduct',true)==1)
			{
				$absent_deduct=1;	
			}
			else
			{
				$absent_deduct=0;
			}
			if($this->input->post('tax_manager',true)==1)
			{
				$tax_manager=1;	
			}
			else
			{
				$tax_manager=0;
			}
			$Data1 = [
				'employee_id'                => $this->input->post('employee_id',true),
				'salary_payable' 	         => $this->input->post('salary_payable',true),
				'absent_deduct' 	         => $absent_deduct,
				'tax_manager' 	             => $tax_manager,	
			];   
			$this->Payroll_model->salary_head_create($Data1);
			$this->session->set_flashdata('message', display('successfully_saved_saletup'));
			redirect("payroll/Payroll/create_s_setup");
		} else {
			$data['title']      = display('create');
			$data['module']     = "payroll";
			$data['slname']     = $this->Payroll_model->salary_typeName();
			$data['sldname']    = $this->Payroll_model->salary_typedName();
			$data['employee']   = $this->Payroll_model->empdropdown();
			$data['emp_sl_setup']   = $this->Payroll_model->salary_setupindex();
			$data['page']       = "salarysetup_form"; 
			
			echo Modules::run('template/layout', $data);   
			
		}   
	}
	public function delete_salsetup($id = null) 
	{ 
		$this->permission->module('payroll','delete')->redirect();

		if ($this->Payroll_model->emp_salstup_delete($id)) {
			#set success message
			$this->session->set_flashdata('message',display('delete_successfully'));
		} else {
			#set exception message
			$this->session->set_flashdata('exception',display('please_try_again'));
		}
		redirect("payroll/Payroll/salary_setup_view");
	}

		public function delete_s_type($id = null) 
	{ 
		$this->permission->module('payroll','delete')->redirect();

		if ($this->Payroll_model->delete_s_type($id)) {
			#set success message
			$this->session->set_flashdata('message',display('delete_successfully'));
		} else {
			#set exception message
			$this->session->set_flashdata('exception',display('please_try_again'));
		}
		redirect("payroll/Payroll/emp_salary_setup_view");
	}



	public function salary_generate_view($id = null)
	{   
		$data['title']    = display('view_salary_generate');  
		$config["base_url"] = base_url('payroll/Payroll/salary_generate_view');
        $config["total_rows"]  = $this->db->count_all('salary_sheet_generate');
        $config["per_page"]    = 10;
        $config["uri_segment"] = 4;
        $config["last_link"] = "Last"; 
        $config["first_link"] = "First"; 
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';  
        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
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
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data["links"] = $this->pagination->create_links();
		$data['salgen']   = $this->Payroll_model->salary_generateView($config["per_page"], $page);
		$data['module']   = "payroll";
		$data['page']     = "sal_genview";   
		echo Modules::run('template/layout', $data); 
	} 

	public function create_salary_generate()
	{ 
		$data['title'] = display('selectionlist'); 
		#-------------------------------# 
		$this->form_validation->set_rules('name',display('salar_month'),'required|max_length[50]');
		#-------------------------------#

		if ($this->form_validation->run() === true) {

		    list($month,$year) = explode(' ',$this->input->post('name',true));

	        $query =$this->db->select('*')->from('salary_sheet_generate')->where('name',$this->input->post('name',true))->get()->num_rows();
	                if ($query > 0) {
	                	
	            $this->session->set_userdata(array('exception' => display('the_salary_of').$month. display('already_generated')));
	            $this->session->set_flashdata('exception','Salary of '.$this->input->post('name',true).' Already Generated');
	            redirect(base_url('payroll/Payroll/create_salary_generate'));
	        }
	           
	        switch ($month)
	        {
	            case "January":
	                $month = '1';
	                break;
	            case "February":
	                $month = '2';
	                break;
	            case "March":
	                $month = '3';
	                break;
	            case "April":
	                $month = '4';
	                break;
	            case "May":
	                $month = '5';
	                break;
	            case "June":
	                $month = '6';
	                break;
	            case "July":
	                $month = '7';
	                break;
	            case "August":
	                $month = '8';
	                break;
	            case "September":
	                $month = '9';
	                break;
	            case "October":
	                $month = '10';
	                break;
	            case "November":
	                $month = '11';
	                break;
	            case "December":
	                $month = '12';
	                break;
	        }


	        $fdate = $year.'-'.$month.'-'.'1';
	        $lastday = date('t',strtotime($fdate));
	        $edate = $year.'-'.$month.'-'.$lastday;
	        $startd    = $fdate;

			$employee = $this->db->select('employee_id')->from('employee_salary_setup')->group_by('employee_id')->get()->result();
			$ab=date('Y-m-d');
				$postData = [
				'name'                =>  $this->input->post('name',true),
				'gdate'               =>  $ab,
				'start_date' 	      =>  $startd, 
				'end_date' 	          =>  $edate, 
				'generate_by' 	      =>  $this->session->userdata('fullname'), 
			]; 

			$this->db->insert('salary_sheet_generate', $postData);

			if (sizeof($employee) > 0)

				foreach($employee as $key=>$value)
				{ 
				
					$aAmount   = $this->db->select('a.gross_salary,a.sal_type,a.employee_id,b.first_name,b.last_name')->from('employee_salary_setup a')->join('employee_history b','b.employee_id=a.employee_id')->where('a.employee_id', $value->employee_id)->get()->row();
					$Amount    = $aAmount->gross_salary;
					$startd    = $startd;
					$end       = $edate;

					 $att_in = $this->db->select('a.time,MIN(a.time) as intime,MAX(a.time) as outtime,a.uid, DATE(time) as mydate')
					->from('attendance_history a')
					->where('a.uid',$value->employee_id)
					 ->where('DATE(a.time) >=',date('Y-m-d', strtotime($startd)))
					 ->where('DATE(a.time) <=',date('Y-m-d', strtotime($end)))
					->group_by('DATE(a.time)')
					->get()
					->result();

			  		$idx=1;
			        $totalhour=[];
			        $totalday = [];

					foreach ($att_in as $attendancedata) { 

						$date_a = new DateTime($attendancedata->outtime);
						$date_b = new DateTime($attendancedata->intime);
						$interval = date_diff($date_a,$date_b);

						$totalwhour = $interval->format('%h:%i:%s');
						$totalhour[$idx] = $totalwhour;
						$totalday[$idx] = $attendancedata->mydate;
						$idx++;
					}

			        $seconds = 0;

					foreach($totalhour as $t)
					{
						$timeArr = array_reverse(explode(":", $t));

						foreach ($timeArr as $key => $tv)
						{
						    if ($key > 2) break;
						    $seconds += pow(60, $key) * $tv;
						}

					}

					$hours = floor($seconds / 3600);
					$mins = floor(($seconds - ($hours*3600)) / 60);
					$secs = floor($seconds % 60);
					$times = $hours * 3600 + $mins * 60 + $secs;;

					// end new salary generate		
					$wormin = ($times/60);
					$worhour = number_format($wormin/60,2);

					if($aAmount->sal_type == 1){

					$dStart = new DateTime($startd);
					$dEnd  = new DateTime($end);
					$dDiff = $dStart->diff($dEnd);
					$numberofdays =  $dDiff->days+1;
					$totamount = $Amount*$worhour;
					$PYI = ($totamount/$numberofdays)*365;
					$PossibleYearlyIncome = round($PYI);

					$this->db->select('*');
					$this->db->from('payroll_tax_setup');
					$this->db->where("start_amount <",$PossibleYearlyIncome);
					$query = $this->db->get();
					$taxrate = $query->result_array();

					$TotalTax = 0;

				    foreach($taxrate as $row){

						// "Inside tax calculation";
						if($PossibleYearlyIncome > $row['start_amount'] && $PossibleYearlyIncome > $row['end_amount']){
							$diff=$row['end_amount']-$row['start_amount'];
						}
						if($PossibleYearlyIncome > $row['start_amount'] && $PossibleYearlyIncome < $row['end_amount']){
							$diff=$PossibleYearlyIncome-$row['start_amount'];
						}

						$tax=(($row['rate']/100)*$diff);
						$TotalTax += $tax;	
					} 

			        $TaxAmount = ($TotalTax/365)*$numberofdays;

			        $netAmount = number_format(($totamount-$TaxAmount),2);

					}else if($aAmount->sal_type == 2){

						$netAmount = $Amount;
					}

					$workingper   = count($totalday);
					$paymentData = array(
						'employee_id'           => $value->employee_id,
						'total_salary'          => $netAmount,
						'total_working_minutes' => $worhour,
						'salary_name'           => $this->input->post('name',true),
						'working_period'        => $workingper,
					);

					if(!empty($aAmount->employee_id)){

						$this->db->insert('employee_salary_payment', $paymentData);

						$c_code = $aAmount->employee_id;
						$c_name = $aAmount->first_name.$aAmount->last_name;
						$c_acc=$c_code.'-'.$c_name;
						$headcode = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName',$c_acc)->get()->row()->HeadCode;
						$createby = $this->session->userdata('fullname');
						$createdate = date('Y-m-d H:i:s');

						$accpayable = array(
							'VNo'            => $this->input->post('name',true),
							'Vtype'          => 'Generated Salary',
							'VDate'          => date('Y-m-d'),
							'COAID'          => $headcode,
							'Narration'      => 'Salary For Employee Id'.$aAmount->employee_id,
							'Debit'          => 0,
							'Credit'         => intval(str_replace(',', '', $netAmount)),
							'IsPosted'       => 1,
							'CreateBy'       => $this->session->userdata('id'),
							'CreateDate'     => date('Y-m-d H:i:s'),
							'IsAppove'       => 1
						); 

						$this->db->insert('acc_transaction', $accpayable);
					}

				}

				$this->session->set_flashdata('message', display('successfully_saved_saletup'));
				redirect("payroll/Payroll/create_salary_generate");
		} 
		else{
				$data['title']  = display('create');
				$config["base_url"] = base_url('payroll/Payroll/create_salary_generate');
		        $config["total_rows"]  = $this->db->count_all('salary_sheet_generate');
		        $config["per_page"]    = 3;
		        $config["uri_segment"] = 4;
		        $config["last_link"] = "Last"; 
		        $config["first_link"] = "First"; 
		        $config['next_link'] = 'Next';
		        $config['prev_link'] = 'Prev';  
		        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
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
		        /* ends of bootstrap */
		        $this->pagination->initialize($config);
		        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		        $data["links"] = $this->pagination->create_links();
				$data['module'] = "payroll";
				$data['page']   = "salary_generate_form"; 
				$data['salgen'] = $this->Payroll_model->salary_generateView($config["per_page"], $page);
				echo Modules::run('template/layout', $data);   

			}   
		}

		public function delete_sal_gen($id = null) 
		{ 
			$sal_name = $this->db->select('name')->from('salary_sheet_generate')->where('ssg_id',$id)->get()->row()->name;

			if ($this->Payroll_model->salary_gen_delete($id,$sal_name)) {
			#set success message
				$this->session->set_flashdata('message',display('delete_successfully'));
			} else {
			#set exception message
				$this->session->set_flashdata('exception',display('please_try_again'));
			}
			redirect("payroll/Payroll/create_salary_generate");
		}

		public function update_salgen_form($id = null){
			$this->form_validation->set_rules('ssg_id',null,'max_length[11]');
			$this->form_validation->set_rules('name',display('name'),'max_length[50]');

			$this->form_validation->set_rules('start_date',display('start_date'));
			$this->form_validation->set_rules('end_date',display('end_date'));
		#-------------------------------#
			if ($this->form_validation->run() === true) {
				$postData = [
					'ssg_id' 	             => $this->input->post('ssg_id',true),
					'name'                   => $this->input->post('name',true),
					'start_date' 	         => $this->input->post('start_date',true),
					'end_date' 	             => $this->input->post('end_date',true),
				]; 
				if ($this->Payroll_model->update_sal_gen($postData)) { 
					$this->session->set_flashdata('message', display('successfully_updated'));
				} else {
					$this->session->set_flashdata('exception',  display('please_try_again'));
				}
				redirect("payroll/Payroll/salary_generate_view");
			} else {
				$data['title']  = display('update');
				$data['data']   =$this->Payroll_model->salargen_updateForm($id);
				$data['module'] = "payroll";
				$data['page']   = "update_salarygenerate_form";   
				echo Modules::run('template/layout', $data); 
			}

		}
		/* salary setup update form  start*/
		public function updates_salstup_form($id = null){

 		#-------------------------------#
			$this->form_validation->set_rules('employee_id',display('employee_id'),'required|max_length[50]');
			$this->form_validation->set_rules('sal_type',display('sal_type'));
			$this->form_validation->set_rules('amount[]',display('amount'));
			$this->form_validation->set_rules('salary_payable',display('salary_payable'));
			$this->form_validation->set_rules('absent_deduct',display('absent_deduct'));
			$this->form_validation->set_rules('tax_manager',display('tax_manager'));
			$amount=$this->input->post('amount',true);

		#-------------------------------#
			if ($this->form_validation->run() === true) {


				foreach($amount as $key=>$value)
				{

					$postData = array(
						'employee_id'        => $this->input->post('employee_id',true),
						'sal_type'           => $this->input->post('sal_type',true),
						'salary_type_id' 	 => $key,
						'amount' 	         => $value,
						'gross_salary'          => $this->input->post('gross_salary',true),
					);

					$this->Payroll_model->update_sal_stup($postData);

				}


				if($this->input->post('absent_deduct',true)==1)
				{
					$absent_deduct=1;	
				}
				else
				{
					$absent_deduct=0;
				}


				if($this->input->post('tax_manager',true)==1)
				{
					$tax_manager=1;	
				}
				else
				{
					$tax_manager=0;
				}


				$Data = [
					'employee_id'                => $this->input->post('employee_id',true),
					'salary_payable' 	         => $this->input->post('salary_payable',true),
					'absent_deduct' 	         => $absent_deduct,
					'tax_manager' 	             => $tax_manager,


				];   


				$this->Payroll_model->update_sal_head($Data);



				$this->session->set_flashdata('message', display('successfully_saved_saletup'));
				redirect("payroll/Payroll/salary_setup_view/");

			} else {

			$data['title']       = display('update');
			$data['data']        = $this->Payroll_model->salary_s_updateForm($id);
			$data['samlft']      = $this->Payroll_model->salary_amountlft($id);
			$data['amo']         = $this->Payroll_model->salary_amount($id);
			$data['bb']          = $this->Payroll_model->get_empid($id);
			$data['gt']          = $this->Payroll_model->get_type($id);
			$data['employee']    = $this->Payroll_model->empdropdown();
			$data['type']        = $this->Payroll_model->type();
			$data['payable']     = $this->Payroll_model->payable();
			$data['gt_pay']      = $this->Payroll_model->get_payable($id);
			$data['EmpRate']     = $this->Payroll_model->employee_informationId($id);
			$data['module']      = "payroll";
			$data['page']        = "update_sal_setup_form";   
			echo Modules::run('template/layout', $data); 
		}

	}

	// salary with tax calculation
	public function salarywithtax(){
		$tamount =$this->input->post('amount',true);
		$amount = $tamount*12;
       $this->db->select('*');
		$this->db->from('payroll_tax_setup');
		$this->db->where("start_amount <",$amount);
		$query = $this->db->get();
		$taxrate = $query->result_array();
		$TotalTax = 0;
	    foreach($taxrate as $row){
                    // "Inside tax calculation";
	    	    if($amount > $row['start_amount'] && $amount > $row['end_amount']){
                   $diff=$row['end_amount']-$row['start_amount'];
                    }
                     if($amount > $row['start_amount'] && $amount < $row['end_amount']){
                    $diff=$amount-$row['start_amount'];
                    }
                    $tax=(($row['rate']/100)*$diff);
                    $TotalTax += $tax;	
                } 
		$salary = $TotalTax/12;
		echo json_encode(round($salary));
	}

	//employee Basic Salary get
	public function employeebasic(){
		$id = $this->input->post('employee_id',true);
		$data = $this->db->select('rate,rate_type')->from('employee_history')->where('employee_id',$id)->get()->row();
		$basic = $data->rate;
		if($data->rate_type ==1){
			$type = 'Hourly';
		}else{
			$type = 'Salary';	
		}
		$sent = array(
			'rate'      =>  $data->rate,
			'rate_type' =>$data->rate_type,
			'stype'     => $type
		);
		echo json_encode($sent);
	}
	
	public function emp_payment_view()
	{   
	        $data['title']         = display('view_employee_payment'); 
			$config["base_url"]    = base_url('payroll/Payroll/emp_payment_view');
	        $config["total_rows"]  = $this->db->count_all('employee_salary_payment');
	        $config["per_page"]    = 25;
	        $config["uri_segment"] = 4;
	        $config["last_link"] = "Last"; 
	        $config["first_link"] = "First"; 
	        $config['next_link'] = 'Next';
	        $config['prev_link'] = 'Prev';  
	        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
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
	        /* ends of bootstrap */
	        $this->pagination->initialize($config);
	        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
	        $data["links"]     = $this->pagination->create_links();
			$data['module']    = "payroll"; 
			$data['emp_pay']   = $this->Employees_model->emp_paymentView($config["per_page"], $page);
			$data['bank_list'] = $this->Employees_model->bank_list();
			$data['module']    = "payroll";
			$data['page']      = "paymentview";   
		echo Modules::run('template/layout', $data); 
	} 

	public function payslip($id = null){
			$data['title']         = display('salary_slip');
			$data['paymentdata']   = $this->Payroll_model->salary_paymentinfo($id);  
			$data['addition']      = $this->Payroll_model->salary_addition_fields($data['paymentdata'][0]['employee_id']);
			$data['deduction']     = $this->Payroll_model->salary_deduction_fields($data['paymentdata'][0]['employee_id']);
			$data['amountinword'] = $this->numbertowords->convert_number(intval(str_replace(',', '', $data['paymentdata'][0]['total_salary'])));
			$data['setting']     = $this->Payroll_model->setting();
			$data['module']      = "hrm";
			$data['page']          = "payslip";   
			echo Modules::run('template/layout', $data); 

	}

	public function employee_salary_generate2()
	{ 
		$postData = $this->input->post('salary_month',true);

		sleep(3);

		echo json_encode($postData);
		exit;
	}

	public function employee_salary_generate(){

		$this->permission1->method('employee_salary_generate','read')->access();
		
		$data['title'] = display('selectionlist'); 
		#-------------------------------# 
		$this->form_validation->set_rules('name',display('salar_month'),'required|max_length[50]');
		
		#-------------------------------#
		if ($this->form_validation->run() === true) {
			$employee = $this->db->select('*')->from('employee_history')->get()->result();
			list($month,$year) = explode(' ',$this->input->post('name',TRUE));
			
			$query =$this->db->select('*')->from('gmb_salary_sheet_generate')->where('name',$this->input->post('name',TRUE))->get()->num_rows();
		
			if ($query > 0) {
				
				$this->session->set_flashdata(array('exception' => 'The Salary of '.$month. ' Already Generated'));
				
				redirect(base_url('employee_salary_generate'));
			}
			
			
			switch ($month)
			{
				case "January":
					$month = '1';
					break;
				case "February":
					$month = '2';
					break;
				case "March":
					$month = '3';
					break;
				case "April":
					$month = '4';
					break;
				case "May":
					$month = '5';
					break;
				case "June":
					$month = '6';
					break;
				case "July":
					$month = '7';
					break;
				case "August":
					$month = '8';
					break;
				case "September":
					$month = '9';
					break;
				case "October":
					$month = '10';
					break;
				case "November":
					$month = '11';
					break;
				case "December":
					$month = '12';
					break;
			}
			$fdate = $year.'-'.$month.'-'.'1';
			$lastday = date('t',strtotime($fdate));
			$edate = $year.'-'.$month.'-'.$lastday;
			$startd    = $fdate;
			
			$ab=date('Y-m-d');
			$postData = [
				'name'          =>  $this->input->post('name',true),
				'gdate'         =>  $ab,
				'start_date'    =>  $startd, 
				'end_date' 	    =>  $edate, 
				'generate_by'   =>  $this->session->userdata('id'), 
			];
			
			$this->db->insert('gmb_salary_sheet_generate', $postData);
			$generate_id = $this->db->insert_id();
			// keepting activity logs for salary generate
			addActivityLog("salary generate", "create", $generate_id, "gmb_salary_sheet_generate", 2, $postData);

			
			if (sizeof($employee) > 0)
			foreach($employee as $key=>$value)
			{ 
				$aAmount   = $this->db->select('hrate as gross_salary,rate_type as sal_type,id as employee_id')->from('employee_history')->where('id', $value->id)->get()->row();
				$Amount    = $aAmount->gross_salary;
				$startd    = $fdate;
				$end       = $edate;
				$times     = $this->db->select('SUM(TIME_TO_SEC(staytime)) AS staytime')->from('attendance')->where('date BETWEEN "'. date('Y-m-d', strtotime($startd)). '" and "'. date('Y-m-d', strtotime($end)).'"')->where("employee_id" ,$value->id )->get()->row()->staytime;
				$wormin    = ($times/60);
				$worhour   = $wormin/60;
				if($aAmount->sal_type == 1){
				$dStart    = new DateTime($startd);
				$dEnd      = new DateTime($end);
				$dDiff     = $dStart->diff($dEnd);
				$numberofdays =  $dDiff->days+1;
				$totamount = $Amount*$worhour;
				$PYI       = ($totamount/$numberofdays)*365;
				$PossibleYearlyIncome = round($PYI);
				$this->db->select('*');
				$this->db->from('payroll_tax_setup');
				$this->db->where("start_amount <",$PossibleYearlyIncome);
				$query = $this->db->get();
				$taxrate = $query->result_array();
				$TotalTax = 0;
				foreach($taxrate as $row){
				
					if($PossibleYearlyIncome > $row['start_amount'] && $PossibleYearlyIncome > $row['end_amount']){
						$diff=$row['end_amount']-$row['start_amount'];
					}
					if($PossibleYearlyIncome > $row['start_amount'] && $PossibleYearlyIncome < $row['end_amount']){
						$diff=$PossibleYearlyIncome-$row['start_amount'];
					}
					$tax=(($row['rate']/100)*$diff);
					$TotalTax += $tax;  
				} 
				$TaxAmount = ($TotalTax/365)*$numberofdays;
		
				$netAmount = $totamount;
		
				}else if($aAmount->sal_type == 2){
					$netAmount = $Amount;
				}

				/* Starts of loan and salary advance deduction*/
				$salary_advance = 0.0;
				$salary_advance_id = null;
				$salary_advance_respo = $this->Payroll_model->salary_advance_deduction($value->id,$this->input->post('name',true));
				if($salary_advance_respo){

					$salary_advance = floatval($salary_advance_respo->amount);
					$salary_advance_id = $salary_advance_respo->id;
				}

				$net_salary = ($netAmount - $salary_advance);

				$workingper   = $this->db->select('COUNT(date) AS date')->from('attendance')->where('date BETWEEN "'. date('Y-m-d', strtotime($startd)). '" and "'. date('Y-m-d', strtotime($end)).'"')->where("employee_id" ,$value->id )->get()->row()->date;
				
				$paymentData = array(
					'sal_month_year'                  => $this->input->post('name',true),
					'employee_id'                     => $value->id,
					'tin_no'          		          => 0,
					'total_attendance'                => 0, //tagret_hours / days
					'total_count'          	          => $worhour?$worhour:0, //weorked_hours / days
					'atten_allowance'          	      => 0,
					'gross'                           => $value->hrate,
					'basic'                           => $value->hrate,
					'transport'                       => 0,
					'house_rent'                      => 0,
					'medical'                         => 0,
					'other_allowance'                 => 0,
					'gross_salary'                    => $netAmount,
					'income_tax'                      => 0,
					'soc_sec_npf_tax'                 => 0,
					'employer_contribution'           => 0,
					'icf_amount'    		          => 0,
					'loan_deduct'                     => 0,
					'loan_id'                         => 0,
					'salary_advance'                  => $salary_advance,
					'salary_adv_id'                   => $salary_advance_id,
					'lwp'                   		  => 0,
					'pf'                   			  => 0,
					'stamp'                   		  => 0,
					'net_salary'                      => $net_salary,
					'createDate'                      => date('Y-m-d'),
					'createBy'                        => $this->session->userdata('id'),
					'medical_benefit'        		  => 0,
					'family_benefit'        		  => 0,
					'transportation_benefit'          => 0,
					'other_benefit'        			  => 0,
					'normal_working_hrs_month'        => 0,
					'actual_working_hrs_month'        => 0,
					'hourly_rate_basic' 	          => 0,
					'hourly_rate_trasport_allowance'  => 0,
					'basic_salary_pro_rated'   		  => 0,
					'transport_allowance_pro_rated'   => 0,
					'basic_transport_allowance'		  => 0,
				);

				if($aAmount->employee_id){
					
					$this->db->insert('gmb_salary_generate', $paymentData);
				
					
				}

				
				if($salary_generate_respo){

					// Update salary advance afetr applying it to salary generate
					if($salary_advance_respo){

						$sala_adv_data = array(
							'id'              => $salary_advance_id,
							'release_amount'  => $salary_advance,
							'UpdateDate'      => date('Y-m-d'),
							'UpdateBy'        => $this->session->userdata('id'),
						);

						$salary_advanc_paid_respo = $this->Payroll_model->update_sal_advance($sala_adv_data);
					}
				}

				
			}
			$this->session->set_flashdata('message', display('successfully_saved_saletup'));
				redirect("employee_salary_generate");
			
		}else {
			
		

		$data['title']  = display('create');
		$config["base_url"] = base_url('hrm/Payroll/employee_salary_generate');
		$config["total_rows"]  = $this->db->count_all('gmb_salary_sheet_generate');
		$config["per_page"]    = 3;
		$config["uri_segment"] = 4;
		$config["last_link"] = "Last"; 
		$config["first_link"] = "First"; 
		$config['next_link'] = 'Next';
		$config['prev_link'] = 'Prev';  
		$config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
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
		/* ends of bootstrap */
		$this->pagination->initialize($config);
		$page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$data["links"] = $this->pagination->create_links();
		$data['module'] = "hrm";
		$data['page']   = "employee_salary/salary_gen_form"; 
		
		$data['salgen'] = $this->Payroll_model->gmb_salary_generateView($config["per_page"], $page);
		
		echo Modules::run('template/layout', $data);   
        
		}
    }
	public function employee_salary_generate_misor()
	{ 

		$this->permission1->method('employee_salary_generate','read')->access();
		
		$data['title'] = display('selectionlist'); 
		#-------------------------------# 
		$this->form_validation->set_rules('name',display('salar_month'),'required|max_length[50]');
		
		#-------------------------------#
		if ($this->form_validation->run() === true) {

			$this->permission1->method('employee_salary_generate','read')->access();

			$setting = $this->Payroll_model->setting();
			
			list($month,$year) = explode(' ',$this->input->post('name',true));
			
			// Check if, salary already generated for the selected month
	        $query =$this->db->select('*')->from('gmb_salary_sheet_generate')->where('name',$this->input->post('name',true))->get()->num_rows();
	        if ($query > 0) {
				
				$this->session->set_flashdata('exception','Salary of '.$this->input->post('name',true).' Already Generated');
	            redirect(base_url('hrm/Payroll/employee_salary_generate'));
	        }
	        // End of Check if, salary already generated for the selected month
			
	        // Get month number based on moth name..
	        $month = $this->month_number_check($month);
			
	        $fdate = $year.'-'.$month.'-'.'1';
	        $lastday = date('t',strtotime($fdate));
	        $edate = $year.'-'.$month.'-'.$lastday;
	        $startd    = $fdate;
			
	        // Getting all active employee list from employee_history table
	        $employee = $this->db->select('*')->from('employee_history')->get()->result();
			echo 'exit';
			exit;

			$ab=date('Y-m-d');
			$postData = [
				'name'          =>  $this->input->post('name',true),
				'gdate'         =>  $ab,
				'start_date'    =>  $startd, 
				'end_date' 	    =>  $edate, 
				'generate_by'   =>  $this->session->userdata('id'), 
			];

			// Saving data into gmb_salary_sheet_generate table

			$respo = $this->db->insert('gmb_salary_sheet_generate', $postData);
			$salary_sheet_generate_id = $this->db->insert_id();
			// keepting activity logs for salary generate
			addActivityLog("salary generate", "create", $salary_sheet_generate_id, "gmb_salary_sheet_generate", 2, $postData);

			/*Generate salary*/

			if (sizeof($employee) > 0){

				foreach($employee as $key=>$value)
				{ 
					$emp_id = $value->employee_id;

					$employee_file = $this->db->select('*')->from('gmb_employee_file')->where('employee_id',$emp_id)->get()->row();

					if($employee_file){

						/*Hourly rate compution along with transport allowance*/
						$worked_hours = $this->employee_worked_hours($emp_id,$startd,$edate);

						$actual_working_hrs_month = floatval($worked_hours); // this is for calculation 
						$month_actual_work_hrs = floatval($worked_hours);

						// Check if actual_working_hrs_month by employee is greater than monthly_work_hours, then set monthly_work_hours as his/her actual_working_hrs_month for now..
						if($actual_working_hrs_month > floatval($value->monthly_work_hours)){

							$actual_working_hrs_month = floatval($value->monthly_work_hours);
						}

						$hourly_rate_basic = floatval($employee_file->basic / $value->monthly_work_hours);
						$hourly_rate_trasport_allowance = floatval($employee_file->transport / $value->monthly_work_hours);

						$basic_salary_pro_rated = $basic_salary = floatval($hourly_rate_basic * $actual_working_hrs_month);
						$transport_allowance_pro_rated = floatval($hourly_rate_trasport_allowance * $actual_working_hrs_month);

						// Benefits amounts
						$total_benefits = 0.0;
						$total_benefits = floatval($employee_file->medical_benefit) + floatval($employee_file->family_benefit) + floatval($employee_file->transportation_benefit) + floatval($employee_file->other_benefit);

						$basic_transport_allowance = $gross_salary =  $basic_salary_pro_rated + $transport_allowance_pro_rated + $total_benefits;
						/*End of Hourly rate compution along with transport allowance*/

						/* Start of tax calculation*/
						$state_income_tax = 0.0;
						// Check if employee type is Full_time and Tin no is not null
						if($employee_file->tin_no != null && (int)$value->employee_type == 3){ 
							$state_income_tax = floatval($this->state_income_tax($gross_salary));
						}
						// Check if employee SOS.Sec.NPF is available
						$soc_sec_npf_tax = 0.0;
						$employer_contribution = 0.0;
						$icf_amount = 0.0;

						$soc_sec_npf_tax_percnt = floatval($setting->soc_sec_npf_tax);
						if($value->sos != "" && (int)$value->employee_type == 3){
							$soc_sec_npf_tax = floatval(($basic_salary * $soc_sec_npf_tax_percnt) / 100);
							// Employer contribution is 10% of basic salary..
							$employer_contribution = floatval(($basic_salary * 10) / 100);
							if($basic_salary > 0){
								$icf_amount = 15;
							}
						}
						/* End  of tax calculation*/

						/* Starts of loan and salary advance deduction*/
						$salary_advance = 0.0;
						$salary_advance_id = null;
						$salary_advance_respo = $this->Payroll_model->salary_advance_deduction($emp_id,$this->input->post('name',true));
						if($salary_advance_respo){

							$salary_advance = floatval($salary_advance_respo->amount);
							$salary_advance_id = $salary_advance_respo->id;
						}
						$loan_deduct = 0.0;
						$loan_id = null;
						$loan_installment_respo = $this->Payroll_model->loan_installment_deduction($emp_id);
						if($loan_installment_respo){

							$loan_deduct = floatval($loan_installment_respo->installment);
							$loan_id = $loan_installment_respo->loan_id;
						}

						// Office loan deduction
						$office_loan_deduct = 0.0;
						$office_loan_transaction_id = null;
						// Convert salary month to proper date format for loan deduction check
						$salary_month = $this->input->post('name',true);
						$current_date = date('Y-m-d', strtotime($salary_month . '-01'));
						$office_loan_respo = $this->Payroll_model->office_loan_installment_deduction($emp_id, $current_date);
						if($office_loan_respo){
							$office_loan_deduct = floatval($office_loan_respo->monthly_installment);
							$office_loan_transaction_id = $office_loan_respo->transaction_id;
						}

						/*Net salary calculation*/
						$net_salary = 0.0;
						$total_deductions =  0.0;
						$total_deductions = ($state_income_tax + $soc_sec_npf_tax + $loan_deduct + $salary_advance + $office_loan_deduct);
						$net_salary = ($gross_salary - $total_deductions);

						/*Ends*/

						$paymentData = array(
							'employee_id'                     => $emp_id,
							'tin_no'          		          => $employee_file->tin_no,
							'total_attendance'                => $value->monthly_work_hours, //tagret_hours / days
							'total_count'          	          => $month_actual_work_hrs, //weorked_hours / days
							'gross'                           => $employee_file->gross_salary,
							'basic'                           => $employee_file->basic,
							'transport'                       => $employee_file->transport,
							'gross_salary'                    => $gross_salary,
							'income_tax'                      => $state_income_tax,
							'soc_sec_npf_tax'                 => $soc_sec_npf_tax,
							'employer_contribution'           => $employer_contribution,
							'icf_amount'    		          => $icf_amount,
							'loan_deduct'                     => $loan_deduct,
							'loan_id'                         => $loan_id,
							'office_loan_deduct'              => $office_loan_deduct,
							'office_loan_transaction_id'      => $office_loan_transaction_id,
							'salary_advance'                  => $salary_advance,
							'salary_adv_id'                   => $salary_advance_id,
							'net_salary'                      => $net_salary,
							'sal_month_year'                  => $this->input->post('name',true),
							'createDate'                      => date('Y-m-d'),
							'createBy'                        => $this->session->userdata('id'),
							'medical_benefit'        		  => floatval($employee_file->medical_benefit),
							'family_benefit'        		  => floatval($employee_file->family_benefit),
							'transportation_benefit'          => floatval($employee_file->transportation_benefit),
							'other_benefit'        			  => floatval($employee_file->other_benefit),
							'normal_working_hrs_month'        => $value->monthly_work_hours,
							'actual_working_hrs_month'        => $month_actual_work_hrs,
							'hourly_rate_basic' 	          => $hourly_rate_basic,
							'hourly_rate_trasport_allowance'  => $hourly_rate_trasport_allowance,
							'basic_salary_pro_rated'   		  => $basic_salary_pro_rated,
							'transport_allowance_pro_rated'   => $transport_allowance_pro_rated,
							'basic_transport_allowance'		  => $basic_transport_allowance,
						);

						$salary_generate_respo = $this->db->insert('gmb_salary_generate', $paymentData);

						if($salary_generate_respo){

							// Update salary advance afetr applying it to salary generate
							if($salary_advance_respo){

								$sala_adv_data = array(
									'id'              => $salary_advance_id,
									'release_amount'  => $salary_advance,
									'UpdateDate'      => date('Y-m-d'),
									'UpdateBy'        => $this->session->userdata('id'),
								);

								$salary_advanc_paid_respo = $this->Payroll_model->update_sal_advance($sala_adv_data);
							}

							// Update loan afetr applying it to salary generate
							if($loan_installment_respo){

								$total_released_amount = 0.0;
								$total_released_amount = floatval($loan_installment_respo->released_amount) + $loan_deduct;

								$total_installment_cleared = 0;
								$total_installment_cleared = (int)$loan_installment_respo->installment_cleared + 1;

								$loan_installment_data = array(
									'loan_id'            => $loan_id,
									'installment_cleared'=> $total_installment_cleared,
									'released_amount'    => $total_released_amount,
									'updated_by'         => $this->session->userdata('id'),
								);

								$loan_installmnt_paid_respo = $this->Payroll_model->update_loan_installment($loan_installment_data);
							}

							// Update office loan after applying deduction
							if($office_loan_respo && $office_loan_deduct > 0){
								$office_loan_deduction_data = array(
									'transaction_id'   => $office_loan_transaction_id,
									'deduction_amount' => $office_loan_deduct,
									'deduction_date'   => date('Y-m-d')
								);

								$office_loan_deduction_respo = $this->Payroll_model->update_office_loan_deduction($office_loan_deduction_data);
							}
						}

					}
				}

			}
			/*End of Generate salary*/

			if($respo){
				$this->session->set_flashdata('message', display('successfully_saved_saletup'));
				redirect("hrm/Payroll/employee_salary_generate");
			}else{

				$this->session->set_flashdata('exception', display('please_try_again'));
				redirect("hrm/Payroll/employee_salary_generate");
			}

		}else{

			$data['title']  = display('create');
			$config["base_url"] = base_url('hrm/Payroll/employee_salary_generate');
	        $config["total_rows"]  = $this->db->count_all('gmb_salary_sheet_generate');
	        $config["per_page"]    = 3;
	        $config["uri_segment"] = 4;
	        $config["last_link"] = "Last"; 
	        $config["first_link"] = "First"; 
	        $config['next_link'] = 'Next';
	        $config['prev_link'] = 'Prev';  
	        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
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
	        /* ends of bootstrap */
	        $this->pagination->initialize($config);
	        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
	        $data["links"] = $this->pagination->create_links();
			$data['module'] = "hrm";
			$data['page']   = "employee_salary/salary_gen_form"; 
			$data['salgen'] = $this->Payroll_model->gmb_salary_generateView($config["per_page"], $page);
			
			echo Modules::run('template/layout', $data);   

		} 

	}

	public function delete_employee_salary_generate($id = null) 
	{ 
		$this->permission1->method('employee_salary_generate','delete')->access();

		$sal_info = $this->db->select('*')->from('gmb_salary_sheet_generate')->where('ssg_id',$id)->get()->row();
		$sal_name = $sal_info->name;

		if($sal_info->approved == 1){

			$this->session->set_flashdata('exception',"Can not be deleted as it's already approved !");
			redirect(base_url('employee_salary_generate'));
		}

		// Start of revrsing loan and salary advance amount if applied for any employee salary...for the month
		$salaries = $this->db->select('id,loan_deduct,loan_id,salary_advance,salary_adv_id')->from('gmb_salary_generate')->where('sal_month_year',$sal_name)->get()->result();

		foreach ($salaries as $key => $row) {
			
			// Salary advance data
			if(floatval($row->salary_advance) > 0){
				// Get Salary advance data
				$salary_adv_data = $this->db->select('*')->from('gmb_salary_advance')->where('id',$row->salary_adv_id)->get()->row();
				// Deduction Salary advance data
				$salary_advance = floatval($salary_adv_data->release_amount) - floatval($row->salary_advance);
				$salary_advance_post_data = array(
						'release_amount' => $salary_advance,
				);
				// Update Salary advance data
				$this->db->where('id', $row->salary_adv_id)->update("gmb_salary_advance", $salary_advance_post_data);
			}
		}

		// Finally delete all salaries from gmb_salary_generate table
		if ($this->Payroll_model->gmb_salary_generate_delete($id,$sal_name)) {

			// keepting activity logs for salary generate
			$postData = array();
			addActivityLog("salary generate", "delete", $id, "gmb_salary_sheet_generate", 3, $postData);

			#set success message
			$this->session->set_flashdata('message',display('delete_successfully'));
		} else {
		#set exception message
			$this->session->set_flashdata('exception',display('please_try_again'));
		}
		redirect(base_url('employee_salary_generate'));
	}


	public function employee_salary_payment_view()
	{   
		$this->permission1->method('employee_salary_payment_view','read')->access();

        $data['title']         = display('view_employee_payment'); 
		$config["base_url"]    = base_url('hrm/Payroll/employee_salary_payment_view');
        $config["total_rows"]  = $this->db->count_all('gmb_salary_generate');
        $config["per_page"]    = 25;
        $config["uri_segment"] = 4;
        $config["last_link"] = "Last"; 
        $config["first_link"] = "First"; 
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';  
        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
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
        /* ends of bootstrap */
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
        $data["links"]     = $this->pagination->create_links();
		$data['module']    = "hrm"; 
		$data['emp_pay']   = $this->Payroll_model->emp_salary_paymentView($config["per_page"], $page);
		$data['module']    = "hrm";
		$data['page']      = "employee_salary/paymentview";   

		echo Modules::run('template/layout', $data); 
	}


    public function tax_slabs()
    {
        $this->permission1->method('employee_salary_generate','read')->access();

        $data['title']      = 'Tax Slabs';
        $data['module']     = 'hrm';
        $data['page']       = 'payroll/tax_slabs';
        $data['tax_slabs']  = $this->db->table_exists('hrm_tax_slabs') ? $this->Payroll_model->get_tax_slabs() : array();

        echo Modules::run('template/layout', $data);
    }

    public function salary_components()
    {
        $this->permission1->method('manage_salary_setup','read')->access();

        $this->Payroll_model->ensure_tax_slab_support();

        $data['title']       = 'Salary Components';
        $data['module']      = 'hrm';
        $data['page']        = 'payroll/salary_components';
        $data['components']  = $this->db->table_exists('hrm_salary_components') ? $this->Payroll_model->get_salary_components() : array();
        $data['tax_slabs']   = $this->db->table_exists('hrm_tax_slabs') ? $this->Payroll_model->get_tax_slabs() : array();

        echo Modules::run('template/layout', $data);
    }

    public function save_salary_component()
    {
        $component_id = $this->input->post('component_id', true);
        $is_update = !empty($component_id);

        $this->permission1->method('manage_salary_setup', $is_update ? 'update' : 'create')->access();

        $this->Payroll_model->ensure_tax_slab_support();

        $this->form_validation->set_rules('component_name', 'Component Name', 'required|max_length[100]');
        $this->form_validation->set_rules('component_type', 'Component Type', 'required|in_list[earning,deduction]');
        $this->form_validation->set_rules('amount_type', 'Amount Type', 'required|in_list[fixed,percentage,tax_slab]');
        $amount_type_input = $this->input->post('amount_type', true);

        if ($amount_type_input === 'tax_slab') {
            $this->form_validation->set_rules('amount_value', 'Amount Value', 'numeric');
            $this->form_validation->set_rules('tax_slab_ids[]', 'Tax Slabs', 'required');
        } else {
            $this->form_validation->set_rules('amount_value', 'Amount Value', 'required|numeric');
        }

        $this->form_validation->set_rules('component_code', 'Component Code', 'max_length[50]');
        $this->form_validation->set_rules('description', 'Description', 'max_length[1000]');

        if ($amount_type_input === 'percentage') {
            $this->form_validation->set_rules('percentage_base', 'Percentage Base', 'required|in_list[gross,basic,net]');
        }

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('exception', strip_tags(validation_errors()));
            redirect('salary_components');
        }

        $amount_type  = $this->input->post('amount_type', true);
        $amount_value = floatval($this->input->post('amount_value', true));
        $raw_tax_slabs = $this->input->post('tax_slab_ids', true);
        $tax_slab_ids = array();

        if ($amount_type === 'tax_slab') {
            $amount_value = 0;
            $tax_slab_ids = array_filter(array_map('intval', (array) $raw_tax_slabs));
        }

        if ($amount_type === 'percentage' && ($amount_value < 0 || $amount_value > 100)) {
            $this->session->set_flashdata('exception', 'Percentage value must be between 0 and 100.');
            redirect('salary_components');
        }

        if ($amount_type === 'fixed' && $amount_value < 0) {
            $this->session->set_flashdata('exception', 'Amount value cannot be negative.');
            redirect('salary_components');
        }

        $code = trim($this->input->post('component_code', true));
        $data = array(
            'component_name'  => $this->input->post('component_name', true),
            'component_code'  => $code !== '' ? $code : null,
            'component_type'  => $this->input->post('component_type', true),
            'amount_type'     => $amount_type,
            'amount_value'    => $amount_value,
            'percentage_base' => $amount_type === 'percentage' ? $this->input->post('percentage_base', true) : null,
            'is_taxable'      => ($this->input->post('is_taxable', true) !== null ? (int) $this->input->post('is_taxable', true) : 0),
            'status'          => ($this->input->post('status', true) !== null ? (int) $this->input->post('status', true) : 0),
            'description'     => $this->input->post('description', true),
        );

        $timestamp = date('Y-m-d H:i:s');
        $user_id   = $this->session->userdata('id');
        $success   = false;

        if ($is_update) {
            $data['updated_by'] = $user_id;
            $data['updated_at'] = $timestamp;
            $success = $this->Payroll_model->update_salary_component($component_id, $data);
        } else {
            $data['created_by'] = $user_id;
            $data['created_at'] = $timestamp;
            $success = $this->Payroll_model->insert_salary_component($data);
        }

        if ($success) {
            $component_ref_id = $is_update ? (int) $component_id : (int) $this->db->insert_id();
            $mapping_saved = true;

            if ($component_ref_id > 0) {
                $mapping_saved = $this->Payroll_model->replace_component_tax_slabs(
                    $component_ref_id,
                    $amount_type === 'tax_slab' ? $tax_slab_ids : array()
                );
            }

            if ($mapping_saved) {
                $this->session->set_flashdata('message', $is_update ? display('successfully_updated') : display('successfully_saved'));
            } else {
                $this->session->set_flashdata('exception', 'Unable to update tax slab assignments for this component.');
            }
        } else {
            $db_error = $this->db->error();
            $message  = !empty($db_error['message']) ? $db_error['message'] : display('please_try_again');
            $this->session->set_flashdata('exception', $message);
        }

        redirect('salary_components');
    }

    public function delete_salary_component($id = null)
    {
        $this->permission1->method('manage_salary_setup','delete')->access();

        $component_id = $id ?: $this->input->post('component_id', true);

        if (empty($component_id) || !$this->Payroll_model->delete_salary_component($component_id)) {
            $this->session->set_flashdata('exception', display('please_try_again'));
        } else {
            $this->session->set_flashdata('message', display('delete_successfully'));
        }

        redirect('salary_components');
    }

    public function save_tax_slab()
    {
        $slab_id   = $this->input->post('slab_id', true);
        $is_update = !empty($slab_id);

        $this->permission1->method('employee_salary_generate', $is_update ? 'update' : 'create')->access();

        $this->form_validation->set_rules('min_amount', 'Minimum Amount', 'required|numeric');
        $this->form_validation->set_rules('max_amount', 'Maximum Amount', 'numeric');
        $this->form_validation->set_rules('rate_percent', 'Tax Rate (%)', 'required|numeric');
        $this->form_validation->set_rules('additional_amount', 'Additional Amount', 'numeric');
        $this->form_validation->set_rules('notes', 'Notes', 'max_length[1000]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('exception', strip_tags(validation_errors()));
            redirect('tax_slabs');
        }

        $min = floatval($this->input->post('min_amount', true));
        $max_input = $this->input->post('max_amount', true);
        $max = ($max_input === '' ? null : floatval($max_input));
        if (!is_null($max) && $max <= $min) {
            $this->session->set_flashdata('exception', 'Maximum amount must be greater than minimum amount.');
            redirect('tax_slabs');
        }

        $data = array(
            'min_amount'        => $min,
            'max_amount'        => $max,
            'rate_percent'      => floatval($this->input->post('rate_percent', true)),
            'additional_amount' => floatval($this->input->post('additional_amount', true)),
            'status'            => ($this->input->post('status', true) !== null ? (int) $this->input->post('status', true) : 0),
            'notes'             => $this->input->post('notes', true),
        );

        $timestamp = date('Y-m-d H:i:s');
        $user_id   = $this->session->userdata('id');
        $success   = false;

        if ($is_update) {
            $data['updated_by'] = $user_id;
            $data['updated_at'] = $timestamp;
            $success = $this->Payroll_model->update_tax_slab($slab_id, $data);
        } else {
            $data['created_by'] = $user_id;
            $data['created_at'] = $timestamp;
            $success = $this->Payroll_model->insert_tax_slab($data);
        }

        if ($success) {
            $this->session->set_flashdata('message', $is_update ? display('successfully_updated') : display('successfully_saved'));
        } else {
            $db_error = $this->db->error();
            $message  = !empty($db_error['message']) ? $db_error['message'] : display('please_try_again');
            $this->session->set_flashdata('exception', $message);
        }

        redirect('tax_slabs');
    }

    public function delete_tax_slab($id = null)
    {
        $this->permission1->method('employee_salary_generate','delete')->access();

        $slab_id = $id ?: $this->input->post('slab_id', true);

        if (empty($slab_id) || !$this->Payroll_model->delete_tax_slab($slab_id)) {
            $this->session->set_flashdata('exception', display('please_try_again'));
        } else {
            $this->session->set_flashdata('message', display('delete_successfully'));
        }

        redirect('tax_slabs');
    }

	public function salary_pay_slip($id)
	{ 
		$data['title']         = display('view_employee_payment'); 
		$data['setting'] = $setting = $this->Payroll_model->setting();
		$data['user_info'] = $this->session->userdata();

		$data['salary_info'] =  $salary_info  = $this->Payroll_model->employee_salary_generate_info($id);

		$component_breakdown = $this->Payroll_model->calculate_component_breakdown($salary_info);
		$component_add_total = isset($component_breakdown['earning_total']) ? (float) $component_breakdown['earning_total'] : 0.0;
		$component_ded_total = isset($component_breakdown['deduction_total']) ? (float) $component_breakdown['deduction_total'] : 0.0;
                $loan_total = floatval($salary_info->loan_deduct);
                $office_loan_total = floatval($salary_info->office_loan_deduct);
                $salary_advance_total = floatval($salary_info->salary_advance);

		$data['component_breakdown'] = $component_breakdown;
		$data['component_add_total'] = round($component_add_total, 2);
		$data['component_ded_total'] = round($component_ded_total, 2);
                $data['loan_total'] = round($loan_total, 2);
                $data['office_loan_total'] = round($office_loan_total, 2);
                $data['salary_advance_total'] = round($salary_advance_total, 2);

                $total_deductions = $component_ded_total + $loan_total + $office_loan_total + $salary_advance_total;
                $data['total_deductions'] = round($total_deductions, 2);

		$post_gross_total = isset($salary_info->gross_salary) ? ((float) $salary_info->gross_salary + $component_add_total) : $component_add_total;
		$data['post_gross_total'] = round($post_gross_total, 2);

		$net_salary_calc = $post_gross_total - $total_deductions;
		if ($net_salary_calc < 0) {
			$net_salary_calc = 0.0;
		}
		$data['net_salary_calculated'] = round($net_salary_calc, 2);

		$social_security_employee = $this->resolve_social_security_component_total($component_breakdown, $salary_info);
		$data['social_security_employee_total'] = $social_security_employee;
		$data['social_security_combined_total'] = round($social_security_employee + floatval($salary_info->employer_contribution), 2);

		list($month,$year) = explode(' ',$salary_info->sal_month_year);
		$data['month_name']  = $month;
		$data['year_name']   = $year;
		// Get month number based on moth name..
	    $month = $this->month_number_check($month);
	    $data['month_number']   = $month;
	    // Get last date of the month
	    $firstDate = $year.'-'.$month.'-'.'1';
	    $data['first_date']   = $firstDate;

	    $lastGetDate = date("Y-m-t", strtotime($firstDate));
	    $data['last_date']   = $lastGetDate;
	    $data['work_days']   = date("t", strtotime($firstDate));

	    $data['from_date'] = '1'.'-'.$data['month_name'].'-'.$year;
	    $data['to_date']   = date("t", strtotime($firstDate)).'-'.$data['month_name'].'-'.$year;

	    // Employee info 
	    $employee_info  = $this->Payroll_model->employee_info($salary_info->employee_id);
	    $data['employee_info'] = $employee_info;

		$data['module']    = "hrm";
		$data['page']      = "employee_salary/pay_slip"; 

		echo Modules::run('template/layout', $data); 
	}

	public function gmb_employee_salary_chart($ssg_id)
	{
		$this->permission1->method('employee_salary_generate','read')->access();

		$data['title']         = display('employee_salary_chart'); 
		
		$data['salary_sheet_generate_info'] = $this->Payroll_model->salary_sheet_generate_info($ssg_id);
		$data['employee_salary_charts']     = $this->Payroll_model->employee_salary_charts($ssg_id);
		
		$data['setting'] = $this->db->get('web_setting')->row();
		$data['user_info'] = $this->session->userdata();
		
		// PDF Generator 
		
		$data['salary_month'] = $data['salary_sheet_generate_info']->name;
		
		$data['module']    = "hrm";
		$data['page']      = "employee_salary/employee_salary_chart";

		echo Modules::run('template/layout', $data); 
	}

	public function gmb_employee_salary_approval($ssg_id)
	{

		$data['title']         = display('employee_salary_chart'); 

		$data['salary_sheet_generate_info'] = $this->Payroll_model->salary_sheet_generate_info($ssg_id);
		$employee_salary_charts  = $this->Payroll_model->employee_salary_charts($ssg_id);
		$data['module']    = "hrm";
		$data['page']      = "employee_salary/employee_salary_approval";

		// Find Total Gross salary for the month
		$gross_salary = 0.0;
		$net_salary = 0.0;
		$loans = 0.0;
		$salary_advance = 0.0;
		$state_income_tax = 0.0;
		$employee_npf_contribution = 0.0; // It's indicates the social security tax(soc.sec.tax)...
		$employer_npf_contribution = 0.0; // It's indicates the employeer contribution if social security tax(soc.sec.tax) available
		$icf_amount = 0.0; // It indicates icf_amount...
		$total_npf_contribution = 0.0;

		foreach ($employee_salary_charts as $key => $value) {

			$gross_salary 			   = $gross_salary + floatval($value->gross_salary);
			$net_salary   			   = $net_salary + floatval($value->net_salary);
			$loans   				   = $loans + floatval($value->loan_deduct);
			$salary_advance            = $salary_advance + floatval($value->salary_advance);
			$state_income_tax          = $state_income_tax + floatval($value->income_tax);
			$employee_npf_contribution = $employee_npf_contribution + floatval($value->soc_sec_npf_tax);
			$employer_npf_contribution = $employer_npf_contribution + floatval($value->employer_contribution);
			$icf_amount 			   = $icf_amount + floatval($value->icf_amount);
		}

		$total_npf_contribution = $employee_npf_contribution + $employer_npf_contribution;

		$data['gross_salary']  				= $gross_salary;
		$data['net_salary']  			    = $net_salary;
		$data['loans']  				    = $loans;
		$data['salary_advance']  			= $salary_advance;
		$data['state_income_tax']           = $state_income_tax;
		$data['employee_npf_contribution']  = $employee_npf_contribution;
		$data['employer_npf_contribution']  = $employer_npf_contribution;
		$data['icf_amount']  			    = $icf_amount;
		$data['total_npf_contribution']  	= $total_npf_contribution;

		$credit_amount = 0.0;
		$credit_amount = $net_salary+$loans+$salary_advance+$state_income_tax+$employee_npf_contribution;
		$data['credit_amount']  = $credit_amount;

		// Get all payment natures from account COA(acc_coa) table
		$data['payment_natures']      = $this->Payroll_model->payment_natures();
		$data['bank_payment_natures'] = $this->Payroll_model->payment_natures_bank();
		$data['ssg_id']  		      = $ssg_id;


		echo Modules::run('template/layout', $data); 
	}

	public function gmb_hourly_rate_computation($ssg_id)
	{
		
		$this->permission->check_label('salary_generate')->read()->redirect();

		$data['title']         = display('employee_hourly_rate_computation'); 
		$data['salary_sheet_generate_info'] = $this->Payroll_model->salary_sheet_generate_info($ssg_id);
		$data['hourly_rate_computations']   = $this->Payroll_model->employee_salary_charts($ssg_id);

		$data['setting'] = $this->db->get('setting')->row();
		$data['user_info'] = $this->session->userdata();

		// PDF Generator 

		$data['salary_month'] = $salary_sheet_generate_info->name;

		$data['module']    = "payroll";
		$data['page']      = "employee_salary/emp_hrly_rte_cmptation";

		echo Modules::run('template/layout', $data); 
	}

	public function save_employee_salary_approval()
	{
		$finyear = financial_year();
		$ssg_id  = $this->input->post('ssg_id');
		
        if ($finyear != '') {
	
			$salary_info = $this->db->select('*')->from('gmb_salary_sheet_generate')->where('ssg_id',$ssg_id)->get()->row();
			if($salary_info->approved == 1){
	
				$this->session->set_flashdata('message',"Salary already approved !");
				redirect(base_url('employee_salary_approval/'.$ssg_id));
			}
	
			$payment_nature = $this->input->post('payment_nature');
			$amount         = $this->input->post('amount');
	
			$tax_payment_nature = $this->input->post('tax_payment_nature');
			$tax_amount         = $this->input->post('tax_amount');
	
			$npf_payment_nature = $this->input->post('npf_payment_nature');
			$npf_amount         = $this->input->post('npf_amount');
	
			$iicf_payment_nature = $this->input->post('iicf_payment_nature');
			$iicf_amount         = $this->input->post('iicf_amount');
	
			$net_renumeration = $this->input->post('net_renumeration');
			$employee_npf_contribution = $this->input->post('employee_npf_contribution');
			$employer_npf_contribution = $this->input->post('employer_npf_contribution');
	
			$emp_npf_contr_amnt 	   = floatval($employee_npf_contribution) + floatval($employer_npf_contribution);
			$icf_amount 			   = floatval($this->input->post('icf_amount'));
			$state_income_tax_amnt	   = floatval($this->input->post('state_income_tax'));
			
			if($payment_nature && $amount && floatval($net_renumeration) > 0){
	
				// Check net_renumeration amount is equal to the sum of the amount of payment_nature
				$total_pay_nature_amount = 0.0;
				for ($i=0; $i < count($payment_nature); $i++) {
					$total_pay_nature_amount = $total_pay_nature_amount + floatval($amount[$i]);
				}
				if($total_pay_nature_amount != $net_renumeration){
	
					$this->session->set_flashdata('exception',"Net Renumeration must be equal to sum of payment nature amount.");
					redirect(base_url('employee_salary_approval/'.$ssg_id));
				}
				// End
	
				$predefine_account = $this->db->select('*')->from('acc_predefine_account')->get()->row();
				$Narration = "Salary for payment";
				$Comment = $this->input->post('month_year');
				$COAID = $predefine_account->salary_code;
				$amnt_type = 'Debit';
	
				// net_renumeration to insert as DV voucher
				for ($i=0; $i < count($payment_nature); $i++) {
	
					$reVID = $payment_nature[$i];
					$amount_pay = $amount[$i];
	
					$insrt_pay_amnt_vcher = $this->insert_payroll_debitvoucher($COAID,$amnt_type,$amount_pay,$Narration,$Comment,$reVID);
					
				}
				// State incomce tax insertion as DV
				if($tax_payment_nature && $tax_amount){
	
					$COAID = $predefine_account->state_tax; // Need clarification , if its ok or not...?
					for ($i=0; $i < count($tax_payment_nature); $i++) {
	
						$reVID = $tax_payment_nature[$i]; // Need clarification , if its ok or not...?
						$tax_amount_pay = $tax_amount[$i];
	
						$insrt_tax_pay_vcher = $this->insert_payroll_debitvoucher($COAID,$amnt_type,$tax_amount_pay,$Narration,$Comment,$reVID);
					}
				}
				// emp_npf_contribution data to insert as DV vouchers..
				if($emp_npf_contr_amnt > 0 && $npf_amount){
	
					for ($i=0; $i < count($npf_payment_nature); $i++) {
	
						$reVID = $npf_payment_nature[$i]; // Need clarification , if its ok or not...
						// Insert emp_npf_contribution data in ac_voucher table
						$COAID = $predefine_account->emp_npf_contribution;
						$insrt_emp_npf_pay_vcher = $this->insert_payroll_debitvoucher($COAID,$amnt_type,$employee_npf_contribution,$Narration,$Comment,$reVID);
	
						// Insert empr_npf_contribution data in ac_voucher table
						$COAID = $predefine_account->empr_npf_contribution;
						$insrt_empr_npf_pay_vcher = $this->insert_payroll_debitvoucher($COAID,$amnt_type,$employer_npf_contribution,$Narration,$Comment,$reVID);
					}
				}
	
				// emp_icf_contribution data to insert as DV vouchers..
				if($icf_amount > 0 && $iicf_amount){
	
					for ($i=0; $i < count($iicf_payment_nature); $i++) {
	
						$reVID = $iicf_payment_nature[$i]; // Need clarification , if its ok or not...
						// Insert emp_icf_contribution data in ac_voucher table
						$COAID = $predefine_account->emp_icf_contribution;
						$insrt_emp_icf_pay_vcher = $this->insert_payroll_debitvoucher($COAID,$amnt_type,$icf_amount,$Narration,$Comment,$reVID);
					}
				}
	
				// Now update employee salary generate table salary as approved..
				$salary_ss_approved = array(
					'approved'      => 1,
					'approved_by'   => $this->session->userdata('id'),
					'approved_date' => date('Y-m-d'),
				);
				$loan_installmnt_paid_respo = $this->Payroll_model->update_salary_as_approved($ssg_id,$salary_ss_approved);
	
				if($loan_installmnt_paid_respo){
	
					$this->session->set_flashdata('message',"Successfully approved !");
				}else{
	
					$this->session->set_flashdata('exception',"There is some issue! , please delete all the vouchers created against this salary.");
				}
	
	
			}else{
				$this->session->set_flashdata('exception',"Please full fill all requirements !");
			}
	
			redirect(base_url('employee_salary_approval/'.$ssg_id));
		}else {
			$this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
			redirect(base_url('employee_salary_approval/'.$ssg_id));
		}
		

	}

	// Insert Payroll Journal voucher 

	 public function isrt_pyrll_state_tax_jv($dbtid = null,$Damnt = null,$Narration = null,$Comment = null,$reVID = null){  

        $maxid = $this->Accounts_model->getMaxFieldNumber('id','acc_vaucher','Vtype','JV','VNo');             
        $vaucherNo = "JV-". ($maxid +1);
        $fyear = $this->session->userdata('fyear');
        $VDate = date('Y-m-d');
        $CreateBy=$this->session->userdata('id');
        $createdate=date('Y-m-d H:i:s');               
       
        $jurnalinsert = array(     
                  'fyear'          =>  $fyear,
                  'VNo'            =>  $vaucherNo,
                  'Vtype'          =>  'JV',
                  'VDate'          =>  $VDate,
                  'COAID'          =>  $dbtid,     
                  'Narration'      =>  $Narration,     
                  'ledgerComment'  =>  $Comment,     
                  'Debit'          =>  0.0, 
                  'Credit'         =>  $Damnt,    
                  'RevCodde'       =>  $reVID,    
                  'isApproved'     =>  0,                      
                  'CreateBy'       =>  $CreateBy,
                  'CreateDate'     =>  $createdate,      
                  'status'         =>  0,     
                );
	     
	    $this->db->insert('acc_vaucher',$jurnalinsert);

		return true;
	}

    public function insert_payroll_journalvoucher($dbtid = null,$Damnt = null,$Narration = null,$Comment = null,$reVID = null){  

        $maxid = $this->Accounts_model->getMaxFieldNumber('id','acc_vaucher','Vtype','JV','VNo');             
        $vaucherNo = "JV-". ($maxid +1);
        $fyear = $this->session->userdata('fyear');
        $VDate = date('Y-m-d');
        $CreateBy=$this->session->userdata('id');
        $createdate=date('Y-m-d H:i:s');               
       
        $jurnalinsert = array(     
                  'fyear'          =>  $fyear,
                  'VNo'            =>  $vaucherNo,
                  'Vtype'          =>  'JV',
                  'VDate'          =>  $VDate,
                  'COAID'          =>  $dbtid,     
                  'Narration'      =>  $Narration,     
                  'ledgerComment'  =>  $Comment,     
                  'Debit'          =>  $Damnt, 
                  'Credit'         =>  0.0,    
                  'RevCodde'       =>  $reVID,    
                  'isApproved'     =>  0,                      
                  'CreateBy'       => $CreateBy,
                  'CreateDate'     => $createdate,      
                  'status'         => 0,      
                );
	     
	     $this->db->insert('acc_vaucher',$jurnalinsert);

		return true;
	}

	// insert_payroll_debitvoucher
    public function insert_payroll_debitvoucher($dbtid = null,$amnt_type = null,$amnt = null,$Narration = null,$Comment = null,$reVID = null){  

        $maxid = $this->Accounts_model->getMaxFieldNumber('id','acc_vaucher','Vtype','DV','VNo');             
        $vaucherNo = "DV-". ($maxid +1);
        $fyear = financial_year();          
        $VDate = date('Y-m-d');
        $CreateBy=$this->session->userdata('id');
        $createdate=date('Y-m-d H:i:s');              
       
        $debitinsert = array(     
                  'fyear'          =>  $fyear,
                  'VNo'            =>  $vaucherNo,
                  'Vtype'          =>  'DV',
                  'VDate'          =>  $VDate,
                  'COAID'          =>  $dbtid,     
                  'Narration'      =>  $Narration,     
                  'ledgerComment'  =>  $Comment,   
                  'RevCodde'       =>  $reVID,    
                  'isApproved'     =>  0,                      
                  'CreateBy'       => $CreateBy,
                  'CreateDate'     => $createdate,      
                  'status'         => 0,      
        	);

        if($amnt_type == 'Debit'){
        	
        	$debitinsert['Debit']  = $amnt;
        	$debitinsert['Credit'] =  0.00;    
        }else{

        	$debitinsert['Debit']  = 0.00;
        	$debitinsert['Credit'] =  $amnt; 
        }
    	
    	// return $debitinsert;

      	$this->db->insert('acc_vaucher',$debitinsert);

	    return true;
	}

	// Get employee worked hours for the requested date range/ month
	public function employee_worked_hours($employee_id,$startd,$edate){

		$startd  = $startd;
		$end     = $edate;

		$att_in = $this->db->select('a.time,MIN(a.time) as intime,MAX(a.time) as outtime,a.uid, DATE(time) as mydate')
		->from('attendance_history a')
		->where('a.uid',$employee_id)
		 ->where('DATE(a.time) >=',date('Y-m-d', strtotime($startd)))
		 ->where('DATE(a.time) <=',date('Y-m-d', strtotime($end)))
		->group_by('DATE(a.time)')
		->get()
		->result();

  		$idx=1;
        $totalhour=[];
        $totalday = [];

		foreach ($att_in as $attendancedata) { 

			$date_a = new DateTime($attendancedata->outtime);
			$date_b = new DateTime($attendancedata->intime);
			$interval = date_diff($date_a,$date_b);

			$totalwhour = $interval->format('%h:%i:%s');
			$totalhour[$idx] = $totalwhour;
			$totalday[$idx] = $attendancedata->mydate;
			$idx++;
		}

        $seconds = 0;

		foreach($totalhour as $t)
		{
			$timeArr = array_reverse(explode(":", $t));

			foreach ($timeArr as $key => $tv)
			{
			    if ($key > 2) break;
			    $seconds += pow(60, $key) * $tv;
			}

		}

		$hours = floor($seconds / 3600);
		$mins = floor(($seconds - ($hours*3600)) / 60);
		$secs = floor($seconds % 60);
		$times = $hours * 3600 + $mins * 60 + $secs;;

		// end new salary generate		
		$wormin = ($times/60);
		$worhour = number_format($wormin/60,2);

		return $worhour;


	}

	/* Calculate state income tax*/
	protected function resolve_social_security_component_total($component_breakdown, $salary_info)
	{
		$total = 0.0;
		if (!empty($component_breakdown['deductions']) && is_array($component_breakdown['deductions'])) {
			foreach ($component_breakdown['deductions'] as $deduction) {
				$name = strtolower(isset($deduction['name']) ? $deduction['name'] : '');
				$code = strtolower(isset($deduction['code']) ? $deduction['code'] : '');
				if (strpos($name, 'social') !== false || strpos($name, 'napsa') !== false || strpos($code, 'napsa') !== false) {
					$total += isset($deduction['amount']) ? (float) $deduction['amount'] : 0.0;
				}
			}
		}

		if ($total <= 0 && isset($salary_info->soc_sec_npf_tax)) {
			$total = (float) $salary_info->soc_sec_npf_tax;
		}

		return round($total, 2);
	}

	public function state_income_tax($gross_salary){

		$tax_calculations = $this->db->select('*')
		->from('gmb_tax_calculation')
		->get()
		->result();

		$tax_amount = 0.0;

		foreach ($tax_calculations as $row) {

			if(floatval($gross_salary) > floatval($row->min) && floatval($gross_salary) <= floatval($row->max) && floatval($row->min) != floatval($row->max)){

				$amount = floatval($gross_salary) - floatval($row->min);
				$gross_salary_tax = ($amount * floatval($row->tax_percent)) / 100;
				$tax_amount =  floatval($row->add_smount) +  floatval($gross_salary_tax);
			}

			if(floatval($gross_salary) > floatval($row->min) && floatval($gross_salary) > floatval($row->max) && floatval($row->min) == floatval($row->max)){

				$amount = floatval($gross_salary) - floatval($row->min);
				$gross_salary_tax = ($amount * floatval($row->tax_percent)) / 100;
				$tax_amount =  floatval($row->add_smount) +  floatval($gross_salary_tax);
			}
		}

		return $tax_amount;
	}

	// Check month number based on month name
	public function month_number_check($month_name)
	{ 
		$month = '';

		switch($month_name)
        {
            case "January":
                $month = '1';
                break;
            case "February":
                $month = '2';
                break;
            case "March":
                $month = '3';
                break;
            case "April":
                $month = '4';
                break;
            case "May":
                $month = '5';
                break;
            case "June":
                $month = '6';
                break;
            case "July":
                $month = '7';
                break;
            case "August":
                $month = '8';
                break;
            case "September":
                $month = '9';
                break;
            case "October":
                $month = '10';
                break;
            case "November":
                $month = '11';
                break;
            case "December":
                $month = '12';
                break;
        }

        return $month;

	}

	public function months_diff_between_two_date($recruited_date){

		$date1 = $recruited_date;
		$date2 = date('Y-m-d');

		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$diff = (($year2 - $year1) * 12) + ($month2 - $month1);

		/////////////////
		$years = floor($diff / 12);
        if($years == 0) {
            $years = 0;
        }
	    $months = ($diff % 12);
	    if($months == 0 or $months > 1) {
	        $months = $months;
	    }

	    $display = $years.'.'.$months;

		return $display;
	}

}
