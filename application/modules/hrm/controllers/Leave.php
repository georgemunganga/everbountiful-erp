<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('isLogIn')) {
            redirect('login');
        }

        $this->load->model(array(
            'hrm/Leave_model' => 'leave_model',
        ));
        $this->load->library('pagination');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
            if (!$this->permission1->method('leave', 'read')->access()) {
                $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                redirect('home');
            }
        }

        $config['base_url'] = base_url('leaves');
        $config['per_page'] = 20;
        $config['uri_segment'] = 2;
        $config['total_rows'] = $this->leave_model->count_leaves();
        $config['last_link'] = 'Last';
        $config['first_link'] = 'First';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $config['full_tag_open'] = "<ul class='pagination pull-right'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tag_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tag_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['last_tag_close'] = "</li>";

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(2)) ? (int) $this->uri->segment(2) : 0;

        $data['title'] = display('leave_management') ?: 'Leave Management';
        $data['leaves'] = $this->leave_model->get_leaves($config['per_page'], $page);
        $data['links'] = $this->pagination->create_links();
        $data['offset'] = $page;
        $data['module'] = 'hrm';
        $data['page'] = 'leave/index';

        echo Modules::run('template/layout', $data);
    }

    public function create($id = null)
    {
        if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
            if (!$this->permission1->method('leave', $id ? 'update' : 'create')->access()) {
                $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                redirect('leaves');
            }
        }

        $leave = null;
        if ($id !== null) {
            $leave = $this->leave_model->get_leave($id);
            if (empty($leave)) {
                $this->session->set_flashdata('exception', display('not_found') ?: 'Not found');
                redirect('leaves');
            }
        }

        $this->form_validation->set_rules('employee_id', display('employee_name') ?: 'Employee', 'required|integer');
        $this->form_validation->set_rules('leave_type', display('leave_type') ?: 'Leave Type', 'required|max_length[100]');
        $this->form_validation->set_rules('start_date', display('start_date') ?: 'Start Date', 'required');
        $this->form_validation->set_rules('end_date', display('end_date') ?: 'End Date', 'required');
        $this->form_validation->set_rules('status', display('status') ?: 'Status', 'required|max_length[20]');

        if ($this->form_validation->run() === true) {
            $payload = array(
                'employee_id' => (int) $this->input->post('employee_id', true),
                'leave_type'  => strip_tags($this->input->post('leave_type', true)),
                'start_date'  => $this->input->post('start_date', true),
                'end_date'    => $this->input->post('end_date', true),
                'status'      => strip_tags($this->input->post('status', true)),
                'notes'       => $this->input->post('notes', true),
            );

            if ($payload['employee_id'] <= 0) {
                $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
                redirect('leaves');
            }

            if ($id === null) {
                $result = $this->leave_model->create_leave($payload);
                $message = display('successfully_saved') ?: 'Saved successfully';
            } else {
                $result = $this->leave_model->update_leave($id, $payload);
                $message = display('successfully_updated') ?: 'Updated successfully';
            }

            if ($result) {
                $this->session->set_flashdata('message', $message);
            } else {
                $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
            }

            redirect('leaves');
        }

        $data['title'] = ($id === null)
            ? (display('add_leave') ?: 'Add Leave')
            : (display('edit_leave') ?: 'Edit Leave');
        $data['employees'] = $this->leave_model->employee_dropdown();
        $data['leave'] = $leave;
        $data['validation_errors'] = validation_errors();
        $data['module'] = 'hrm';
        $data['page'] = 'leave/form';

        echo Modules::run('template/layout', $data);
    }

    public function delete($id = null)
    {
        if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
            if (!$this->permission1->method('leave', 'delete')->access()) {
                $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                redirect('leaves');
            }
        }

        if ($id === null) {
            $this->session->set_flashdata('exception', display('not_found') ?: 'Not found');
            redirect('leaves');
        }

        if ($this->leave_model->delete_leave($id)) {
            $this->session->set_flashdata('message', display('delete_successfully') ?: 'Deleted successfully');
        } else {
            $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
        }

        redirect('leaves');
    }
}
