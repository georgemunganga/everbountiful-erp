<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    


class AI_Setting extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SESSION sql_mode = ""');
        $this->load->model(array(
            'AI_Setting_model'
        ));

        $this->load->library('ciqrcode');

        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }


    public function index()
    {
        echo "Testing_success";
        $this->check_setting();
        #-------------------------------#
        $data['languageList']  = $this->languageList();
        $data['sdata']         = $this->AI_Setting_model->read();

        $data['module'] = "dashboard";
        $data['page']   = "home/ai_setting";
        echo Modules::run('template/layout', $data);
    }

    public function bdtask_create_ai_settings()
    {
        $data['title'] = display('application_setting');
        #-------------------------------#
        $this->form_validation->set_rules('api_key', display('api_key'), 'required|max_length[250]');
        $this->form_validation->set_rules('model', display('model'), 'required');
        $this->form_validation->set_rules('temperature', display('temperature'), 'required|max_length[50]');
        $this->form_validation->set_rules('max_tokens', display('max_tokens'), 'required|max_length[100]');;

        #-------------------------------#
        $data['ai_setting'] = (object)$postData = [
            'setting_id'        => $this->input->post('id', true),
            'api_key'           => $this->input->post('api_key', true),
            'model'             => $this->input->post('model', true),
            'temperature'       => $this->input->post('temperature', true),
            'max_tokens'        => $this->input->post('max_tokens', true),
            'prompt_template'   => $this->input->post('prompt_template', true)
        ];
        #-------------------------------# 
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($postData['setting_id'])) {
                if ($this->AI_Setting_model->create($postData)) {
                    #set success message
                    $this->session->set_flashdata('message', display('save_successfully'));
                } else {
                    #set exception message
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
            } else {
                if ($this->AI_Setting_model->update($postData)) {
                    #set success message
                    $this->session->set_flashdata('message', display('update_successfully'));
                } else {
                    #set exception message
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
            }
            redirect('ai_settings');
        } else {
            $data['module']       = "dashboard";
            $data['page']         = "home/ai_setting";
            echo Modules::run('template/layout', $data);
        }
    }


    public function ai_chat_prompt()
    {

        $this->check_setting();
        #-------------------------------#
        $data['languageList']  = $this->languageList();
        $data['sdata']         = $this->AI_Setting_model->read();

        $data['module'] = "dashboard";
        $data['page']   = "home/ai_chat_prompt";
        echo Modules::run('template/layout', $data);
    }


    public function check_setting()
    {
        if ($this->db->count_all('web_setting') == 0) {
            $this->db->insert('web_setting', [
                'footer_text' => '2020&copy;Copyright',
            ]);
        }
    }

    public function languageList()
    {
        if ($this->db->table_exists("language")) {

            $fields = $this->db->field_data("language");

            $i = 1;
            foreach ($fields as $field) {
                if ($i++ > 2)
                    $result[$field->name] = ucfirst($field->name);
            }

            if (!empty($result)) return $result;
        } else {
            return false;
        }
    }
}
