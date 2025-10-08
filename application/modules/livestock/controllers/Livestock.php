<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Livestock extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'livestock_model'
        ));

        if (!$this->session->userdata('isLogIn')) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->ensure_language_phrases();
    }

    private function render($page, array $data = array())
    {
        $data['module'] = 'livestock';
        $data['page']   = $page;
        echo modules::run('template/layout', $data);
    }

    private function phrase($phrase, $fallback)
    {
        $translated = display($phrase);
        return $translated ? $translated : $fallback;
    }

    private function ensure_language_phrases()
    {
        $phrases = array(
            'livestock_management'    => 'Livestock Management',
            'sheds'                   => 'Sheds',
            'shed'                    => 'Shed',
            'add_shed'                => 'Add Shed',
            'edit_shed'               => 'Edit Shed',
            'productions'             => 'Productions',
            'production'              => 'Production',
            'add_production'          => 'Add Production',
            'edit_production'         => 'Edit Production',
            'livestock_groups'        => 'Livestock Groups',
            'livestock_group'         => 'Livestock Group',
            'add_livestock_group'     => 'Add Livestock Group',
            'edit_livestock_group'    => 'Edit Livestock Group',
            'livestocks'              => 'Livestocks',
            'livestock'               => 'Livestock',
            'add_livestock'           => 'Add Livestock',
            'edit_livestock'          => 'Edit Livestock',
            'feeds'                   => 'Feeds',
            'feed'                    => 'Feed',
            'add_feed'                => 'Add Feed',
            'edit_feed'               => 'Edit Feed',
            'feed_usages'             => 'Feed Usages',
            'feed_usage'              => 'Feed Usage',
            'add_feed_usage'          => 'Add Feed Usage',
            'edit_feed_usage'         => 'Edit Feed Usage',
            'assigned_batch'          => 'Assigned Batch',
            'purchase_unit'           => 'Purchase Unit',
            'default_qty'             => 'Default Qty',
            'total_purchased'         => 'Total Purchased',
            'used_total'              => 'Used Total',
            'total_wasted'            => 'Total Wasted',
            'total_instock'           => 'Total In Stock',
            'mortality'               => 'Mortality',
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

    private function flash_and_redirect($message_key, $redirect_to, $type = 'message')
    {
        $this->session->set_flashdata(array($type => display($message_key) ?: $message_key));
        redirect(base_url($redirect_to));
    }

    /* ------------------------------ Sheds -------------------------------- */

    public function sheds()
    {
        try {
            $data['title'] = $this->phrase('sheds', 'Sheds');
            $data['sheds'] = $this->livestock_model->get_sheds();
            $this->render('sheds/index', $data);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function shed_form($id = null)
    {
        $shed = null;
        if (!empty($id)) {
            $shed = $this->livestock_model->get_shed($id);
            if (!$shed) {
                $this->flash_and_redirect('not_found', 'sheds', 'exception');
            }
        }

        $this->form_validation->set_rules('name', $this->phrase('shed', 'Shed') . ' ' . strtolower($this->phrase('name', 'Name')), 'required|max_length[255]');
        $this->form_validation->set_rules('description', $this->phrase('description', 'Description'), 'max_length[1000]');

        if ($this->form_validation->run() === true) {
            $payload = array(
                'name'        => $this->input->post('name', true),
                'description' => $this->input->post('description', true),
            );

            if ($shed) {
                $success = $this->livestock_model->update_shed($shed['id'], $payload);
                $message = $success ? 'successfully_updated' : 'please_try_again';
            } else {
                $success = $this->livestock_model->create_shed($payload);
                $message = $success ? 'save_successfully' : 'please_try_again';
            }

            $this->flash_and_redirect($message, 'sheds', $success ? 'message' : 'exception');
        }

        $data['title'] = $shed ? $this->phrase('edit_shed', 'Edit Shed') : $this->phrase('add_shed', 'Add Shed');
        $data['shed']  = (object) array(
            'id'          => $shed ? $shed['id'] : null,
            'name'        => set_value('name', $shed ? $shed['name'] : ''),
            'description' => set_value('description', $shed ? $shed['description'] : ''),
        );

        $this->render('sheds/form', $data);
    }

    public function shed_delete($id = null)
    {
        if (empty($id)) {
            $this->flash_and_redirect('not_found', 'sheds', 'exception');
        }

        $success = $this->livestock_model->delete_shed($id);
        $message = $success ? 'successfully_deleted' : 'please_try_again';
        $this->flash_and_redirect($message, 'sheds', $success ? 'message' : 'exception');
    }

    /* --------------------------- Livestock Groups ------------------------ */

    public function livestock_groups()
    {
        $data['title']            = $this->phrase('livestock_groups', 'Livestock Groups');
        $data['livestock_groups'] = $this->livestock_model->get_livestock_groups();
        $this->render('livestock_groups/index', $data);
    }

    public function livestock_group_form($id = null)
    {
        $group = null;
        if (!empty($id)) {
            $group = $this->livestock_model->get_livestock_group($id);
            if (!$group) {
                $this->flash_and_redirect('not_found', 'livestock-groups', 'exception');
            }
        }

        $this->form_validation->set_rules('name', $this->phrase('livestock_group', 'Livestock Group') . ' ' . strtolower($this->phrase('name', 'Name')), 'required|max_length[255]');
        $this->form_validation->set_rules('description', $this->phrase('description', 'Description'), 'max_length[1000]');
        $this->form_validation->set_rules('status', $this->phrase('status', 'Status'), 'required|in_list[0,1]');

        if ($this->form_validation->run() === true) {
            $payload = array(
                'name'        => $this->input->post('name', true),
                'description' => $this->input->post('description', true),
                'status'      => (int) $this->input->post('status', true),
            );

            if ($group) {
                $success = $this->livestock_model->update_livestock_group($group['id'], $payload);
                $message = $success ? 'successfully_updated' : 'please_try_again';
            } else {
                $success = $this->livestock_model->create_livestock_group($payload);
                $message = $success ? 'save_successfully' : 'please_try_again';
            }

            $this->flash_and_redirect($message, 'livestock-groups', $success ? 'message' : 'exception');
        }

        $data['title'] = $group ? $this->phrase('edit_livestock_group', 'Edit Livestock Group') : $this->phrase('add_livestock_group', 'Add Livestock Group');
        $data['group'] = (object) array(
            'id'          => $group ? $group['id'] : null,
            'name'        => set_value('name', $group ? $group['name'] : ''),
            'description' => set_value('description', $group ? $group['description'] : ''),
            'status'      => set_value('status', $group ? $group['status'] : 1),
        );

        $this->render('livestock_groups/form', $data);
    }

    public function livestock_group_delete($id = null)
    {
        if (empty($id)) {
            $this->flash_and_redirect('not_found', 'livestock-groups', 'exception');
        }

        $success = $this->livestock_model->delete_livestock_group($id);
        $message = $success ? 'successfully_deleted' : 'please_try_again';
        $this->flash_and_redirect($message, 'livestock-groups', $success ? 'message' : 'exception');
    }

    /* ----------------------------- Productions --------------------------- */

    public function productions()
    {
        $data['title']        = $this->phrase('productions', 'Productions');
        $data['productions']  = $this->livestock_model->get_productions();
        $this->render('productions/index', $data);
    }

    public function production_form($id = null)
    {
        $production = null;
        if (!empty($id)) {
            $production = $this->livestock_model->get_production($id);
            if (!$production) {
                $this->flash_and_redirect('not_found', 'productions', 'exception');
            }
        }

        $this->form_validation->set_rules('name', $this->phrase('production', 'Production') . ' ' . strtolower($this->phrase('name', 'Name')), 'required|max_length[255]');
        $this->form_validation->set_rules('shed_id', $this->phrase('shed', 'Shed'), 'required|integer');
        $this->form_validation->set_rules('unit_type_id', $this->phrase('unit', 'Unit'), 'required|integer');
        $this->form_validation->set_rules('produced_total_qty', $this->phrase('produced_total_qty', 'Produced Total Qty'), 'numeric');
        $this->form_validation->set_rules('produced_mortality_qty', $this->phrase('mortality', 'Mortality'), 'numeric');
        $this->form_validation->set_rules('produced_damaged_qty', $this->phrase('produced_damaged_qty', 'Produced Damaged Qty'), 'numeric');
        $this->form_validation->set_rules('produced_extras_qty', $this->phrase('produced_extras_qty', 'Produced Extras Qty'), 'numeric');
        $this->form_validation->set_rules('description', $this->phrase('description', 'Description'), 'max_length[1000]');

        if ($this->form_validation->run() === true) {
            $payload = array(
                'name'                   => $this->input->post('name', true),
                'shed_id'                => (int) $this->input->post('shed_id', true),
                'unit_type_id'           => (int) $this->input->post('unit_type_id', true),
                'description'            => $this->input->post('description', true),
                'produced_total_qty'     => $this->decimal_input('produced_total_qty'),
                'produced_mortality_qty' => $this->decimal_input('produced_mortality_qty'),
                'produced_damaged_qty'   => $this->decimal_input('produced_damaged_qty'),
                'produced_extras_qty'    => $this->decimal_input('produced_extras_qty'),
            );

            if ($production) {
                $success = $this->livestock_model->update_production($production['id'], $payload);
                $message = $success ? 'successfully_updated' : 'please_try_again';
            } else {
                $success = $this->livestock_model->create_production($payload);
                $message = $success ? 'save_successfully' : 'please_try_again';
            }

            $this->flash_and_redirect($message, 'productions', $success ? 'message' : 'exception');
        }

        $data['title']       = $production ? $this->phrase('edit_production', 'Edit Production') : $this->phrase('add_production', 'Add Production');
        $data['production']  = (object) array(
            'id'                     => $production ? $production['id'] : null,
            'name'                   => set_value('name', $production ? $production['name'] : ''),
            'shed_id'                => set_value('shed_id', $production ? $production['shed_id'] : ''),
            'unit_type_id'           => set_value('unit_type_id', $production ? $production['unit_type_id'] : ''),
            'description'            => set_value('description', $production ? $production['description'] : ''),
            'produced_total_qty'     => set_value('produced_total_qty', $production ? $production['produced_total_qty'] : '0.00'),
            'produced_mortality_qty' => set_value('produced_mortality_qty', $production ? $production['produced_mortality_qty'] : '0.00'),
            'produced_damaged_qty'   => set_value('produced_damaged_qty', $production ? $production['produced_damaged_qty'] : '0.00'),
            'produced_extras_qty'    => set_value('produced_extras_qty', $production ? $production['produced_extras_qty'] : '0.00'),
        );
        $data['sheds'] = $this->livestock_model->get_sheds();
        $data['units'] = $this->livestock_model->get_units();

        $this->render('productions/form', $data);
    }

    public function production_delete($id = null)
    {
        if (empty($id)) {
            $this->flash_and_redirect('not_found', 'productions', 'exception');
        }

        $success = $this->livestock_model->delete_production($id);
        $message = $success ? 'successfully_deleted' : 'please_try_again';
        $this->flash_and_redirect($message, 'productions', $success ? 'message' : 'exception');
    }

    /* ----------------------------- Livestocks ---------------------------- */

    public function livestocks()
    {
        $data['title']      = $this->phrase('livestocks', 'Livestocks');
        $data['livestocks'] = $this->livestock_model->get_livestocks();
        $this->render('livestocks/index', $data);
    }

    public function livestock_form($id = null)
    {
        $livestock = null;
        if (!empty($id)) {
            $livestock = $this->livestock_model->get_livestock($id);
            if (!$livestock) {
                $this->flash_and_redirect('not_found', 'livestocks', 'exception');
            }
        }

        $this->form_validation->set_rules('shed_id', $this->phrase('shed', 'Shed'), 'required|integer');
        $this->form_validation->set_rules('livestock_group_id', $this->phrase('livestock_group', 'Livestock Group'), 'required|integer');
        $this->form_validation->set_rules('unit_type_id', $this->phrase('unit', 'Unit'), 'required|integer');
        $this->form_validation->set_rules('livestock_total_qty', $this->phrase('livestock_total_qty', 'Livestock Total Qty'), 'numeric');
        $this->form_validation->set_rules('livestock_female_qty', $this->phrase('livestock_female_qty', 'Livestock Female Qty'), 'numeric');
        $this->form_validation->set_rules('livestock_male_qty', $this->phrase('livestock_male_qty', 'Livestock Male Qty'), 'numeric');
        $this->form_validation->set_rules('livestock_mortality_qty', $this->phrase('livestock_mortality_qty', 'Livestock Mortality Qty'), 'numeric');
        $this->form_validation->set_rules('notes', $this->phrase('notes', 'Notes'), 'max_length[1000]');

        if ($this->form_validation->run() === true) {
            $payload = array(
                'shed_id'               => (int) $this->input->post('shed_id', true),
                'livestock_group_id'    => (int) $this->input->post('livestock_group_id', true),
                'unit_type_id'          => (int) $this->input->post('unit_type_id', true),
                'livestock_total_qty'   => $this->decimal_input('livestock_total_qty'),
                'livestock_female_qty'  => $this->decimal_input('livestock_female_qty'),
                'livestock_male_qty'    => $this->decimal_input('livestock_male_qty'),
                'livestock_mortality_qty' => $this->decimal_input('livestock_mortality_qty'),
                'notes'                 => $this->input->post('notes', true),
            );

            if ($livestock) {
                $success = $this->livestock_model->update_livestock($livestock['id'], $payload);
                $message = $success ? 'successfully_updated' : 'please_try_again';
            } else {
                $success = $this->livestock_model->create_livestock($payload);
                $message = $success ? 'save_successfully' : 'please_try_again';
            }

            $this->flash_and_redirect($message, 'livestocks', $success ? 'message' : 'exception');
        }

        $data['title']     = $livestock ? $this->phrase('edit_livestock', 'Edit Livestock') : $this->phrase('add_livestock', 'Add Livestock');
        $data['livestock'] = (object) array(
            'id'                     => $livestock ? $livestock['id'] : null,
            'shed_id'                => set_value('shed_id', $livestock ? $livestock['shed_id'] : ''),
            'livestock_group_id'     => set_value('livestock_group_id', $livestock ? $livestock['livestock_group_id'] : ''),
            'unit_type_id'           => set_value('unit_type_id', $livestock ? $livestock['unit_type_id'] : ''),
            'livestock_total_qty'    => set_value('livestock_total_qty', $livestock ? $livestock['livestock_total_qty'] : '0.00'),
            'livestock_female_qty'   => set_value('livestock_female_qty', $livestock ? $livestock['livestock_female_qty'] : '0.00'),
            'livestock_male_qty'     => set_value('livestock_male_qty', $livestock ? $livestock['livestock_male_qty'] : '0.00'),
            'livestock_mortality_qty'=> set_value('livestock_mortality_qty', $livestock ? $livestock['livestock_mortality_qty'] : '0.00'),
            'notes'                  => set_value('notes', $livestock ? $livestock['notes'] : ''),
        );
        $data['sheds']            = $this->livestock_model->get_sheds();
        $data['livestock_groups'] = $this->livestock_model->get_livestock_groups();
        $data['units']            = $this->livestock_model->get_units();

        $this->render('livestocks/form', $data);
    }

    public function livestock_delete($id = null)
    {
        if (empty($id)) {
            $this->flash_and_redirect('not_found', 'livestocks', 'exception');
        }

        $success = $this->livestock_model->delete_livestock($id);
        $message = $success ? 'successfully_deleted' : 'please_try_again';
        $this->flash_and_redirect($message, 'livestocks', $success ? 'message' : 'exception');
    }

    /* -------------------------------- Feeds ------------------------------ */

    public function feeds()
    {
        $data['title'] = $this->phrase('feeds', 'Feeds');
        $data['feeds'] = $this->livestock_model->get_feeds();
        $this->render('feeds/index', $data);
    }

    public function feed_form($id = null)
    {
        $feed = null;
        if (!empty($id)) {
            $feed = $this->livestock_model->get_feed($id);
            if (!$feed) {
                $this->flash_and_redirect('not_found', 'feeds', 'exception');
            }
        }

        $this->form_validation->set_rules('name', $this->phrase('feed', 'Feed') . ' ' . strtolower($this->phrase('name', 'Name')), 'required|max_length[255]');
        $this->form_validation->set_rules('purchase_unit_id', $this->phrase('purchase_unit', 'Purchase Unit'), 'required|integer');
        $this->form_validation->set_rules('assigned_batch', $this->phrase('assigned_batch', 'Assigned Batch'), 'max_length[100]');
        $this->form_validation->set_rules('description', $this->phrase('description', 'Description'), 'max_length[1000]');

        if ($this->form_validation->run() === true) {
            $payload = array(
                'name'              => $this->input->post('name', true),
                'purchase_unit_id'  => (int) $this->input->post('purchase_unit_id', true),
                'assigned_batch'    => $this->input->post('assigned_batch', true),
                'description'       => $this->input->post('description', true),
            );

            if ($feed) {
                $success = $this->livestock_model->update_feed($feed['id'], $payload);
                $message = $success ? 'successfully_updated' : 'please_try_again';
            } else {
                $success = $this->livestock_model->create_feed($payload);
                $message = $success ? 'save_successfully' : 'please_try_again';
            }

            $this->flash_and_redirect($message, 'feeds', $success ? 'message' : 'exception');
        }

        $data['title'] = $feed ? $this->phrase('edit_feed', 'Edit Feed') : $this->phrase('add_feed', 'Add Feed');
        $data['feed']  = (object) array(
            'id'               => $feed ? $feed['id'] : null,
            'name'             => set_value('name', $feed ? $feed['name'] : ''),
            'purchase_unit_id' => set_value('purchase_unit_id', $feed ? $feed['purchase_unit_id'] : ''),
            'assigned_batch'   => set_value('assigned_batch', $feed ? $feed['assigned_batch'] : ''),
            'description'      => set_value('description', $feed ? $feed['description'] : ''),
        );
        $data['units'] = $this->livestock_model->get_units();

        $this->render('feeds/form', $data);
    }

    public function feed_delete($id = null)
    {
        if (empty($id)) {
            $this->flash_and_redirect('not_found', 'feeds', 'exception');
        }

        $success = $this->livestock_model->delete_feed($id);
        $message = $success ? 'successfully_deleted' : 'please_try_again';
        $this->flash_and_redirect($message, 'feeds', $success ? 'message' : 'exception');
    }

    /* ----------------------------- Feed Usages --------------------------- */

    public function feed_usages()
    {
        $data['title']       = $this->phrase('feed_usages', 'Feed Usages');
        $data['feed_usages'] = $this->livestock_model->get_feed_usages();
        $this->render('feed_usages/index', $data);
    }

    public function feed_usage_form($id = null)
    {
        $feed_usage = null;
        if (!empty($id)) {
            $feed_usage = $this->livestock_model->get_feed_usage($id);
            if (!$feed_usage) {
                $this->flash_and_redirect('not_found', 'feed-usages', 'exception');
            }
        }

        $this->form_validation->set_rules('feed_id', $this->phrase('feed', 'Feed'), 'required|integer');
        $this->form_validation->set_rules('shed_id', $this->phrase('shed', 'Shed'), 'required|integer');
        $this->form_validation->set_rules('total_purchased_qty', $this->phrase('total_purchased', 'Total Purchased'), 'numeric');
        $this->form_validation->set_rules('used_total_qty', $this->phrase('used_total', 'Used Total'), 'numeric');
        $this->form_validation->set_rules('total_wasted_qty', $this->phrase('total_wasted', 'Total Wasted'), 'numeric');

        if ($this->form_validation->run() === true) {
            $total_purchased = $this->decimal_input('total_purchased_qty');
            $used_total      = $this->decimal_input('used_total_qty');
            $total_wasted    = $this->decimal_input('total_wasted_qty');
            $total_instock   = max(0, $total_purchased - $used_total - $total_wasted);

            $payload = array(
                'feed_id'             => (int) $this->input->post('feed_id', true),
                'shed_id'             => (int) $this->input->post('shed_id', true),
                'total_purchased_qty' => $this->format_decimal($total_purchased),
                'used_total_qty'      => $this->format_decimal($used_total),
                'total_wasted_qty'    => $this->format_decimal($total_wasted),
                'total_instock_qty'   => $this->format_decimal($total_instock),
            );

            if ($feed_usage) {
                $success = $this->livestock_model->update_feed_usage($feed_usage['id'], $payload);
                $message = $success ? 'successfully_updated' : 'please_try_again';
            } else {
                $success = $this->livestock_model->create_feed_usage($payload);
                $message = $success ? 'save_successfully' : 'please_try_again';
            }

            $this->flash_and_redirect($message, 'feed-usages', $success ? 'message' : 'exception');
        }

        $data['title'] = $feed_usage ? $this->phrase('edit_feed_usage', 'Edit Feed Usage') : $this->phrase('add_feed_usage', 'Add Feed Usage');
        $data['feed_usage'] = (object) array(
            'id'                   => $feed_usage ? $feed_usage['id'] : null,
            'feed_id'              => set_value('feed_id', $feed_usage ? $feed_usage['feed_id'] : ''),
            'shed_id'              => set_value('shed_id', $feed_usage ? $feed_usage['shed_id'] : ''),
            'total_purchased_qty'  => set_value('total_purchased_qty', $feed_usage ? $feed_usage['total_purchased_qty'] : '0.00'),
            'used_total_qty'       => set_value('used_total_qty', $feed_usage ? $feed_usage['used_total_qty'] : '0.00'),
            'total_wasted_qty'     => set_value('total_wasted_qty', $feed_usage ? $feed_usage['total_wasted_qty'] : '0.00'),
            'total_instock_qty'    => set_value('total_instock_qty', $feed_usage ? $feed_usage['total_instock_qty'] : '0.00'),
        );
        $data['feeds'] = $this->livestock_model->get_feeds_dropdown();
        $data['sheds'] = $this->livestock_model->get_sheds();

        $this->render('feed_usages/form', $data);
    }

    public function feed_usage_delete($id = null)
    {
        if (empty($id)) {
            $this->flash_and_redirect('not_found', 'feed-usages', 'exception');
        }

        $success = $this->livestock_model->delete_feed_usage($id);
        $message = $success ? 'successfully_deleted' : 'please_try_again';
        $this->flash_and_redirect($message, 'feed-usages', $success ? 'message' : 'exception');
    }

    /* ----------------------------- Helpers ------------------------------- */

    private function decimal_input($field)
    {
        $value = $this->input->post($field, true);
        if ($value === '' || $value === null) {
            return $this->format_decimal(0);
        }
        return $this->format_decimal((float) $value);
    }

    private function format_decimal($value)
    {
        return number_format((float) $value, 2, '.', '');
    }
}
