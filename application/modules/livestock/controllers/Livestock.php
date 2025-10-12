<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Livestock extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'livestock_model',
            'inventory/Inventory_model' => 'inventory_model'
        ));
        $this->load->library('InventoryLedger');

        if (!$this->input->is_cli_request() && !$this->session->userdata('isLogIn')) {
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
            'production_date'         => 'Production Date',
            'add_production'          => 'Add Production',
            'edit_production'         => 'Edit Production',
            'farm_collections'        => 'Farms Collections',
            'production_percentage_report' => 'Production Percentage',
            'summary'                 => 'Summary',
            'daily_breakdown'         => 'Daily Breakdown',
            'usable_output'           => 'Usable Output',
            'yield_percentage'        => 'Yield Percentage',
            'batches'                 => 'Batches',
            'select_product_instruction' => 'Select a product to view production percentage details.',
            'biological_assets'       => 'Biological Assets',
            'livestock_asset'         => 'Livestock Asset',
            'select_livestock_asset'  => 'Select Livestock Asset',
            'use_livestock_asset_help'=> 'Choosing a livestock record will auto-fill the biological asset count.',
            'no_livestock_available'  => 'No livestock records available.',
            'asset_count_help'        => 'Optional: total number of productive units (animals or plants).',
            'asset_count_override_help' => 'Override the auto-filled value from the selected livestock if needed.',
            'interval'                => 'Interval',
            'daily'                   => 'Daily',
            'weekly'                  => 'Weekly',
            'monthly'                 => 'Monthly',
            'yearly'                  => 'Yearly',
            'enter_asset_count'       => 'Enter number of producing units',
            'component_meaning'       => 'Meaning of Each Component',
            'term'                    => 'Term',
            'description'             => 'Description',
            'example_poultry'         => 'Example (Poultry)',
            'example_crops'           => 'Example (Crops)',
            'products_produced_desc'  => 'The measurable output (e.g. eggs, milk, fruit, harvested crops).',
            'products_produced'       => 'Products Produced',
            'biological_assets_present' => 'Biological Assets Present',
            'biological_assets_present_desc' => 'The number of living organisms or producing units responsible for the output.',
            'production_percentage_desc' => 'Yield relative to the number of productive units.',
            'production_percentage_formula' => '(Units produced / Number of assets) x 100',
            'examples'                => 'Examples',
            'example_dairy'           => 'Dairy Example',
            'production_rate'         => 'Production %',
            'period'                  => 'Period',
            'dynamic_production_template' => 'Dynamic Production Report Template',
            'biological_asset_type'   => 'Biological Asset Type',
            'number_of_assets'        => 'No. of Assets',
            'units_produced'          => 'Units Produced',
            'feed_inputs_used'        => 'Feed/Inputs Used',
            'interpretation'          => 'Interpretation',
            'interpretation_above_100'=> 'Above 100% -> Each biological unit produced more than expected (excellent efficiency).',
            'interpretation_mid'      => 'Around 80-100% -> Normal, healthy production.',
            'interpretation_low'      => 'Below 70% -> Potential stress, disease, or input constraints.',
            'optional_metrics'        => 'Optional Metrics',
            'yield_per_area'          => 'Yield per unit area (crops): Total yield / Area planted.',
            'conversion_ratios'       => 'Conversion ratios (livestock): Feed input / Weight gain.',
            'economic_efficiency'     => 'Economic efficiency: (Value of output / Cost of inputs) x 100.',
            'invalid_date'            => 'Please provide a valid date.',
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
            'vaccines'                => 'Vaccines',
            'vaccine'                 => 'Vaccine',
            'add_vaccine'             => 'Add Vaccine',
            'edit_vaccine'            => 'Edit Vaccine',
            'manufacturer'            => 'Manufacturer',
            'disease_target'          => 'Disease Target',
            'vaccine_usages'          => 'Vaccine Usages',
            'vaccine_usage'           => 'Vaccine Usage',
            'add_vaccine_usage'       => 'Add Vaccine Usage',
            'edit_vaccine_usage'      => 'Edit Vaccine Usage',
            'usage_date'              => 'Usage Date',
            'notes'                   => 'Notes',
            'farm_reports'            => 'Farm Reports',
            'daily'                   => 'Daily',
            'weekly'                  => 'Weekly',
            'monthly'                 => 'Monthly',
            'extras'                  => 'Extras',
            'interval'                => 'Interval',
            'filter'                  => 'Filter',
            'filtered_by_name'        => 'Filtered by name: %s',
            'period'                  => 'Period',
            'from'                    => 'From',
            'to'                      => 'To',
            'mortality'               => 'Mortality',
            'outputs'                 => 'Outputs',
            'add_output'              => 'Add Output',
            'please_add_output'       => 'Please add at least one output product',
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

        $isPost = strtoupper($this->input->server('REQUEST_METHOD')) === 'POST';

        $outputItems = array();
        $postedProducts = (array) $this->input->post('output_product_id', true);
        $postedUnits = (array) $this->input->post('output_unit_id', true);
        $postedQuantities = (array) $this->input->post('output_quantity', true);
        $postedLosses = (array) $this->input->post('output_loss_qty', true);
        $postedExtras = (array) $this->input->post('output_extras_qty', true);

        if ($isPost && !empty($postedProducts)) {
            $shedIdInput = (int) $this->input->post('shed_id', true);
            $locationDefault = $shedIdInput > 0 ? $this->inventoryledger->resolveShedLocationId($shedIdInput) : $this->inventoryledger->getDefaultLocationId();

            foreach ($postedProducts as $idx => $productId) {
                $outputItems[] = array(
                    'product_id'  => trim((string) $productId),
                    'unit_id'     => isset($postedUnits[$idx]) ? $postedUnits[$idx] : '',
                    'quantity'    => isset($postedQuantities[$idx]) ? $postedQuantities[$idx] : '',
                    'loss_qty'    => isset($postedLosses[$idx]) ? $postedLosses[$idx] : '',
                    'extras_qty'  => isset($postedExtras[$idx]) ? $postedExtras[$idx] : '',
                    'location_id' => $locationDefault,
                );
            }
        } elseif ($production) {
            $existingOutputs = $this->livestock_model->get_production_outputs($production['id']);
            foreach ($existingOutputs as $existing) {
                $unitId = (int) $existing['unit_id'];
                $outputItems[] = array(
                    'product_id'  => $existing['product_id'],
                    'unit_id'     => $unitId,
                    'quantity'    => $this->inventoryledger->convertFromBaseQuantity($existing['product_id'], $unitId, $existing['quantity']),
                    'loss_qty'    => $this->inventoryledger->convertFromBaseQuantity($existing['product_id'], $unitId, $existing['loss_quantity']),
                    'extras_qty'  => 0,
                    'location_id' => (int) $existing['location_id'],
                );
            }
        }

        if (empty($outputItems)) {
            $outputItems[] = array(
                'product_id'  => '',
                'unit_id'     => '',
                'quantity'    => '',
                'loss_qty'    => '',
                'extras_qty'  => '',
                'location_id' => $production ? (int) $production['stock_location_id'] : $this->inventoryledger->getDefaultLocationId(),
            );
        }

        $defaultProductionDate = date('Y-m-d');
        if ($production) {
            if (!empty($production['production_date']) && $production['production_date'] !== '0000-00-00') {
                $timestamp = strtotime($production['production_date']);
                if ($timestamp !== false) {
                    $defaultProductionDate = date('Y-m-d', $timestamp);
                }
            } elseif (!empty($production['created_at'])) {
                $timestamp = strtotime($production['created_at']);
                if ($timestamp !== false) {
                    $defaultProductionDate = date('Y-m-d', $timestamp);
                }
            }
        }

        $shedId = $isPost ? (int) $this->input->post('shed_id', true) : ($production ? (int) $production['shed_id'] : 0);
        $defaultLocationId = $shedId > 0 ? $this->inventoryledger->resolveShedLocationId($shedId) : $this->inventoryledger->getDefaultLocationId();

        $processedOutputs = array();
        $totalQty = 0.0;
        $totalLoss = 0.0;
        $totalExtras = 0.0;

        foreach ($outputItems as $item) {
            $productId = isset($item['product_id']) ? trim((string) $item['product_id']) : '';
            $quantity = isset($item['quantity']) && $item['quantity'] !== '' ? (float) $item['quantity'] : 0.0;
            $lossQty = isset($item['loss_qty']) && $item['loss_qty'] !== '' ? (float) $item['loss_qty'] : 0.0;
            $extrasQty = isset($item['extras_qty']) && $item['extras_qty'] !== '' ? (float) $item['extras_qty'] : 0.0;
            $unitId = isset($item['unit_id']) && $item['unit_id'] !== '' ? (int) $item['unit_id'] : 0;
            $locationId = isset($item['location_id']) ? (int) $item['location_id'] : 0;

            if ($productId === '' || $quantity <= 0) {
                continue;
            }

            if ($unitId <= 0) {
                $unitId = $this->inventoryledger->getBaseUnitId($productId);
            }
            if ($locationId <= 0) {
                $locationId = $defaultLocationId;
            }

            $processedOutputs[] = array(
                'product_id'    => $productId,
                'unit_id'       => $unitId,
                'quantity'      => $quantity,
                'mortality_qty' => $lossQty,
                'damaged_qty'   => 0,
                'extras_qty'    => $extrasQty,
                'location_id'   => $locationId,
            );

            $totalQty += $quantity;
            $totalLoss += $lossQty;
            $totalExtras += $extrasQty;
        }

        if ($isPost) {
            $_POST['produced_total_qty'] = $this->format_decimal($totalQty);
            $_POST['produced_mortality_qty'] = $this->format_decimal($totalLoss);
            $_POST['produced_extras_qty'] = $this->format_decimal($totalExtras);
        }

        $this->form_validation->set_rules('name', $this->phrase('production', 'Production') . ' ' . strtolower($this->phrase('name', 'Name')), 'required|max_length[255]');
        $this->form_validation->set_rules('shed_id', $this->phrase('shed', 'Shed'), 'required|integer');
        $this->form_validation->set_rules('production_date', $this->phrase('production_date', 'Production Date'), 'required|callback_valid_date');
        $this->form_validation->set_rules('produced_total_qty', $this->phrase('produced_total_qty', 'Produced Total Qty'), 'numeric');
        $this->form_validation->set_rules('produced_mortality_qty', $this->phrase('mortality', 'Mortality'), 'numeric');
        $this->form_validation->set_rules('produced_damaged_qty', $this->phrase('produced_damaged_qty', 'Produced Damaged Qty'), 'numeric');
        $this->form_validation->set_rules('produced_extras_qty', $this->phrase('produced_extras_qty', 'Produced Extras Qty'), 'numeric');
        $this->form_validation->set_rules('description', $this->phrase('description', 'Description'), 'max_length[1000]');

        if ($this->form_validation->run() === true) {
            $productionDate = $this->normalize_date_input($this->input->post('production_date', true), date('Y-m-d'));

            if (empty($processedOutputs)) {
                $this->session->set_flashdata('exception', $this->phrase('please_add_output', 'Please add at least one output product.'));
                redirect($production ? "productions/edit/{$production['id']}" : 'productions/add');
            }

            $primaryOutput = $processedOutputs[0];

            $payload = array(
                'name'                   => $this->input->post('name', true),
                'shed_id'                => $shedId,
                'unit_type_id'           => (int) $primaryOutput['unit_id'],
                'description'            => $this->input->post('description', true),
                'produced_total_qty'     => $this->format_decimal($totalQty),
                'produced_mortality_qty' => $this->format_decimal($totalLoss),
                'produced_damaged_qty'   => $this->decimal_input('produced_damaged_qty'),
                'produced_extras_qty'    => $this->format_decimal($totalExtras),
                'output_product_id'      => $primaryOutput['product_id'],
                'stock_location_id'      => $primaryOutput['location_id'],
                'output_unit_id'         => $primaryOutput['unit_id'],
                'production_date'        => $productionDate,
            );

            if ($production) {
                $success = $this->livestock_model->update_production($production['id'], $payload);
                if ($success) {
                    $syncSuccess = $this->sync_production_inventory($production['id'], $processedOutputs, $productionDate);
                    if (!$syncSuccess) {
                        $this->session->set_flashdata('exception', $this->phrase('please_try_again', 'Please try again.'));
                        redirect("productions/edit/{$production['id']}");
                    }
                }
                $message = $success ? 'successfully_updated' : 'please_try_again';
            } else {
                $newId = $this->livestock_model->create_production($payload);
                $success = (bool) $newId;
                if ($success) {
                    $syncSuccess = $this->sync_production_inventory($newId, $processedOutputs, $productionDate);
                    if (!$syncSuccess) {
                        $this->session->set_flashdata('exception', $this->phrase('please_try_again', 'Please try again.'));
                        redirect('productions/add');
                    }
                }
                $message = $success ? 'save_successfully' : 'please_try_again';
            }

            $this->flash_and_redirect($message, 'productions', $success ? 'message' : 'exception');
        }

        $data['title'] = $production ? $this->phrase('edit_production', 'Edit Production') : $this->phrase('add_production', 'Add Production');
        $data['production'] = (object) array(
            'id'                     => $production ? $production['id'] : null,
            'name'                   => set_value('name', $production ? $production['name'] : ''),
            'shed_id'                => set_value('shed_id', $production ? $production['shed_id'] : ''),
            'production_date'        => set_value('production_date', $defaultProductionDate),
            'output_product_id'      => set_value('output_product_id', $production ? $production['output_product_id'] : ''),
            'description'            => set_value('description', $production ? $production['description'] : ''),
            'produced_total_qty'     => set_value('produced_total_qty', $production ? $production['produced_total_qty'] : '0.00'),
            'produced_mortality_qty' => set_value('produced_mortality_qty', $production ? $production['produced_mortality_qty'] : '0.00'),
            'produced_damaged_qty'   => set_value('produced_damaged_qty', $production ? $production['produced_damaged_qty'] : '0.00'),
            'produced_extras_qty'    => set_value('produced_extras_qty', $production ? $production['produced_extras_qty'] : '0.00'),
        );
        $data['sheds'] = $this->livestock_model->get_sheds();
        $data['units'] = $this->livestock_model->get_units();
        $data['products'] = $this->livestock_model->get_products();
        $data['production_outputs'] = $outputItems;

        $this->render('productions/form', $data);
    }

    public function valid_date($date)
    {
        $normalized = $this->normalize_date_input($date, null);
        if ($normalized === null) {
            $this->form_validation->set_message('valid_date', $this->phrase('invalid_date', 'Please provide a valid date.'));
            return false;
        }
        $_POST['production_date'] = $normalized;
        return true;
    }

    private function normalize_date_input($input, $fallback = null)
    {
        if ($input === null) {
            return $fallback;
        }

        $input = trim((string) $input);
        if ($input === '') {
            return $fallback;
        }

        $timestamp = strtotime($input);
        if ($timestamp === false) {
            return $fallback;
        }

        return date('Y-m-d', $timestamp);
    }

    private function sync_production_inventory($productionId, array $outputs, $productionDate)
    {
        $productionId = (int) $productionId;
        if ($productionId <= 0) {
            return false;
        }

        $productionDate = $this->normalize_date_input($productionDate, date('Y-m-d'));

        if (empty($outputs)) {
            $this->inventoryledger->clearProductionOutput($productionId);
            return true;
        }

        $clearExisting = true;
        $recordedAny = false;

        foreach ($outputs as $output) {
            $productId = isset($output['product_id']) ? trim($output['product_id']) : '';
            $quantity = isset($output['quantity']) ? (float) $output['quantity'] : 0;
            $locationId = isset($output['location_id']) ? (int) $output['location_id'] : 0;
            $unitId = isset($output['unit_id']) ? (int) $output['unit_id'] : 0;

            if ($productId === '' || $locationId <= 0 || $quantity <= 0) {
                log_message('error', sprintf('Skipping invalid production output payload for production %s', $productionId));
                continue;
            }

            if ($unitId <= 0) {
                $unitId = $this->inventoryledger->getBaseUnitId($productId);
            }
            if ($locationId <= 0) {
                $locationId = $this->inventoryledger->getDefaultLocationId();
            }

            $payload = array(
                'production_id'   => $productionId,
                'product_id'      => $productId,
                'unit_id'         => $unitId,
                'quantity'        => $quantity,
                'mortality_qty'   => isset($output['mortality_qty']) ? (float) $output['mortality_qty'] : 0,
                'damaged_qty'     => isset($output['damaged_qty']) ? (float) $output['damaged_qty'] : 0,
                'extras_qty'      => isset($output['extras_qty']) ? (float) $output['extras_qty'] : 0,
                'location_id'     => $locationId,
                'production_date' => $productionDate,
                'created_by'      => $this->session->userdata('id'),
            );

            $result = $this->inventoryledger->recordProductionOutput($payload, $clearExisting);
            if ($result === false) {
                log_message('error', sprintf('Failed to record production output for production %s, product %s', $productionId, $productId));
                return false;
            }

            $recordedAny = true;
            $clearExisting = false;
        }

        return $recordedAny;
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

    public function livestocks($name = null)
    {
        $queryName = $this->input->get('name', true);
        $activeName = null;

        if ($queryName !== null && $queryName !== '') {
            $activeName = trim((string) $queryName);
        } elseif ($name !== null && $name !== '') {
            $activeName = trim((string) urldecode($name));
        }

        $filters = array();
        if (!empty($activeName)) {
            $filters['name'] = $activeName;
        }

        $data['title']      = $this->phrase('livestocks', 'Livestocks');
        $data['livestocks'] = $this->livestock_model->get_livestocks($filters);
        $data['active_name_filter'] = $activeName;
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

        $this->form_validation->set_rules('name', $this->phrase('livestock', 'Livestock') . ' ' . strtolower($this->phrase('name', 'Name')), 'trim|required|max_length[255]');
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
                'name'                  => trim((string) $this->input->post('name', true)),
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
            'name'                   => set_value('name', $livestock ? $livestock['name'] : ''),
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
        $this->form_validation->set_rules('name', $this->phrase('name', 'Name'), 'required|max_length[255]');
        $this->form_validation->set_rules('shed_id', $this->phrase('shed', 'Shed'), 'required|integer');
        $this->form_validation->set_rules('total_purchased_qty', $this->phrase('total_purchased', 'Total Purchased'), 'numeric');
        $this->form_validation->set_rules('used_total_qty', $this->phrase('used_total', 'Used Total'), 'numeric');
        $this->form_validation->set_rules('total_wasted_qty', $this->phrase('total_wasted', 'Total Wasted'), 'numeric');
        $this->form_validation->set_rules('inventory_product_id', $this->phrase('product', 'Product'), 'required');
        $this->form_validation->set_rules('location_id', $this->phrase('location', 'Location'), 'required|integer');

        $existingLocationId = null;
        if ($feed_usage) {
            if (array_key_exists('inventory_location_id', $feed_usage) && $feed_usage['inventory_location_id'] !== null) {
                $existingLocationId = (int) $feed_usage['inventory_location_id'];
            } elseif (array_key_exists('location_id', $feed_usage) && $feed_usage['location_id'] !== null) {
                $existingLocationId = (int) $feed_usage['location_id'];
            }
        }

        if ($this->form_validation->run() === true) {
            // Permissions: enforce consumption and override
            if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
                if (!$this->permission1->method('stock_consumption', 'create')->access()) {
                    $this->flash_and_redirect('unauthorized', 'feed-usages', 'exception');
                }
            }
            $total_purchased = $this->decimal_input('total_purchased_qty');
            $used_total      = $this->decimal_input('used_total_qty');
            $total_wasted    = $this->decimal_input('total_wasted_qty');
            $total_instock   = max(0, $total_purchased - $used_total - $total_wasted);
            $selectedLocationId = (int) $this->input->post('location_id', true);
            if ($selectedLocationId <= 0) {
                $selectedLocationId = null;
            }

            $payload = array(
                'feed_id'             => (int) $this->input->post('feed_id', true),
                'shed_id'             => (int) $this->input->post('shed_id', true),
                'inventory_product_id'=> trim($this->input->post('inventory_product_id', true)),
                'total_purchased_qty' => $this->format_decimal($total_purchased),
                'used_total_qty'      => $this->format_decimal($used_total),
                'total_wasted_qty'    => $this->format_decimal($total_wasted),
                'total_instock_qty'   => $this->format_decimal($total_instock),
            );

            if ($selectedLocationId) {
                if ($this->db->field_exists('inventory_location_id', 'feed_usages')) {
                    $payload['inventory_location_id'] = $selectedLocationId;
                } elseif ($this->db->field_exists('location_id', 'feed_usages')) {
                    $payload['location_id'] = $selectedLocationId;
                }
            }

            $allowOverride = (bool) $this->input->post('allow_override', true);

            if ($feed_usage) {
                $usageId = (int) $feed_usage['id'];
                $success = $this->livestock_model->update_feed_usage($usageId, $payload);
                if ($success) {
                    $this->sync_feed_usage_ledger($usageId, $allowOverride, null, $selectedLocationId);
                    $message = 'successfully_updated';
                    log_message('info', sprintf('Feed usage #%d updated and synced by user %s', $usageId, $this->session->userdata('id')));
                } else {
                    $message = 'please_try_again';
                }
            } else {
                $usageId = $this->livestock_model->create_feed_usage($payload);
                if ($usageId) {
                    $this->sync_feed_usage_ledger($usageId, $allowOverride, null, $selectedLocationId);
                    $message = 'save_successfully';
                    log_message('info', sprintf('Feed usage #%d created and synced by user %s', $usageId, $this->session->userdata('id')));
                } else {
                    $message = 'please_try_again';
                }
                $success = (bool) $usageId;
            }

            if ($success && $allowOverride) {
                if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
                    if (!$this->permission1->method('stock_override', 'create')->access()) {
                        $this->flash_and_redirect('unauthorized', 'feed-usages', 'exception');
                    }
                }
                log_message('warning', sprintf('Feed usage #%d applied with override by user %s', $usageId, $this->session->userdata('id')));
            }

            $this->flash_and_redirect($message, 'feed-usages', $success ? 'message' : 'exception');
        }

        $data['title'] = $feed_usage ? $this->phrase('edit_feed_usage', 'Edit Feed Usage') : $this->phrase('add_feed_usage', 'Add Feed Usage');
        if (!$existingLocationId && $feed_usage) {
            $existingLocationId = $this->inventoryledger->resolveShedLocationId((int) $feed_usage['shed_id']);
        }
        $data['feed_usage'] = (object) array(
            'id'                   => $feed_usage ? $feed_usage['id'] : null,
            'feed_id'              => set_value('feed_id', $feed_usage ? $feed_usage['feed_id'] : ''),
            'shed_id'              => set_value('shed_id', $feed_usage ? $feed_usage['shed_id'] : ''),
            'inventory_product_id' => set_value('inventory_product_id', $feed_usage ? $feed_usage['inventory_product_id'] : ''),
            'total_purchased_qty'  => set_value('total_purchased_qty', $feed_usage ? $feed_usage['total_purchased_qty'] : '0.00'),
            'used_total_qty'       => set_value('used_total_qty', $feed_usage ? $feed_usage['used_total_qty'] : '0.00'),
            'total_wasted_qty'     => set_value('total_wasted_qty', $feed_usage ? $feed_usage['total_wasted_qty'] : '0.00'),
            'total_instock_qty'    => set_value('total_instock_qty', $feed_usage ? $feed_usage['total_instock_qty'] : '0.00'),
            'location_id'          => set_value('location_id', $existingLocationId ?: ''),
        );
        $data['feeds'] = $this->livestock_model->get_feeds_dropdown();
        $data['sheds'] = $this->livestock_model->get_sheds();
        $data['inventory_products'] = $this->inventory_model->get_consumable_products();
        $data['locations'] = $this->inventory_model->get_active_locations();
        $data['recent_notifications'] = $this->inventory_model->get_recent_notifications(10);

        $this->render('feed_usages/form', $data);
    }

    public function feed_usage_delete($id = null)
    {
        if (empty($id)) {
            $this->flash_and_redirect('not_found', 'feed-usages', 'exception');
        }

        $this->inventoryledger->clearMovementsByReference('feed_usage', $id);
        $success = $this->livestock_model->delete_feed_usage($id);
        $message = $success ? 'successfully_deleted' : 'please_try_again';
        if ($success) {
            log_message('info', sprintf('Feed usage #%d deleted and ledger movements cleared by user %s', $id, $this->session->userdata('id')));
        }
        $this->flash_and_redirect($message, 'feed-usages', $success ? 'message' : 'exception');
    }

    /* ------------------------------ Vaccines --------------------------- */

    public function vaccines()
    {
        $data['title']    = $this->phrase('vaccines', 'Vaccines');
        $data['vaccines'] = $this->livestock_model->get_vaccines();
        $this->render('vaccines/index', $data);
    }

    public function vaccine_form($id = null)
    {
        $vaccine = null;
        if (!empty($id)) {
            $vaccine = $this->livestock_model->get_vaccine($id);
            if (!$vaccine) {
                $this->flash_and_redirect('not_found', 'vaccines', 'exception');
            }
        }

        $this->form_validation->set_rules('name', $this->phrase('vaccine', 'Vaccine') . ' ' . strtolower($this->phrase('name', 'Name')), 'required|max_length[255]');
        $this->form_validation->set_rules('manufacturer', $this->phrase('manufacturer', 'Manufacturer'), 'max_length[255]');
        $this->form_validation->set_rules('disease_target', $this->phrase('disease_target', 'Disease Target'), 'max_length[255]');
        $this->form_validation->set_rules('unit_type_id', $this->phrase('purchase_unit', 'Unit'), 'integer');
        $this->form_validation->set_rules('description', $this->phrase('description', 'Description'), 'max_length[1000]');

        if ($this->form_validation->run() === true) {
            $unitTypeId = $this->input->post('unit_type_id', true);
            $payload = array(
                'name'           => $this->input->post('name', true),
                'manufacturer'   => $this->input->post('manufacturer', true),
                'disease_target' => $this->input->post('disease_target', true),
                'unit_type_id'   => $unitTypeId !== '' ? (int) $unitTypeId : null,
                'description'    => $this->input->post('description', true),
            );

            if ($vaccine) {
                $success = $this->livestock_model->update_vaccine($vaccine['id'], $payload);
                $message = $success ? 'successfully_updated' : 'please_try_again';
            } else {
                $success = $this->livestock_model->create_vaccine($payload);
                $message = $success ? 'save_successfully' : 'please_try_again';
            }

            $this->flash_and_redirect($message, 'vaccines', $success ? 'message' : 'exception');
        }

        $data['title'] = $vaccine ? $this->phrase('edit_vaccine', 'Edit Vaccine') : $this->phrase('add_vaccine', 'Add Vaccine');
        $data['vaccine'] = (object) array(
            'id'             => $vaccine ? $vaccine['id'] : null,
            'name'           => set_value('name', $vaccine ? $vaccine['name'] : ''),
            'manufacturer'   => set_value('manufacturer', $vaccine ? $vaccine['manufacturer'] : ''),
            'disease_target' => set_value('disease_target', $vaccine ? $vaccine['disease_target'] : ''),
            'unit_type_id'   => set_value('unit_type_id', $vaccine ? $vaccine['unit_type_id'] : ''),
            'description'    => set_value('description', $vaccine ? $vaccine['description'] : ''),
        );
        $data['units'] = $this->livestock_model->get_units();

        $this->render('vaccines/form', $data);
    }

    public function vaccine_delete($id = null)
    {
        if (empty($id)) {
            $this->flash_and_redirect('not_found', 'vaccines', 'exception');
        }

        $success = $this->livestock_model->delete_vaccine($id);
        $message = $success ? 'successfully_deleted' : 'please_try_again';
        $this->flash_and_redirect($message, 'vaccines', $success ? 'message' : 'exception');
    }

    /* ---------------------------- Vaccine Usages ----------------------- */

    public function vaccine_usages()
    {
        $data['title']          = $this->phrase('vaccine_usages', 'Vaccine Usages');
        $data['vaccine_usages'] = $this->livestock_model->get_vaccine_usages();
        $this->render('vaccine_usages/index', $data);
    }

    public function vaccine_usage_form($id = null)
    {
        $vaccine_usage = null;
        if (!empty($id)) {
            $vaccine_usage = $this->livestock_model->get_vaccine_usage($id);
            if (!$vaccine_usage) {
                $this->flash_and_redirect('not_found', 'vaccine-usages', 'exception');
            }
        }

        $existingLocationId = null;
        if ($vaccine_usage) {
            if (array_key_exists('inventory_location_id', $vaccine_usage) && $vaccine_usage['inventory_location_id'] !== null) {
                $existingLocationId = (int) $vaccine_usage['inventory_location_id'];
            } elseif (array_key_exists('location_id', $vaccine_usage) && $vaccine_usage['location_id'] !== null) {
                $existingLocationId = (int) $vaccine_usage['location_id'];
            }
        }

        $this->form_validation->set_rules('vaccine_id', $this->phrase('vaccine', 'Vaccine'), 'required|integer');
        $this->form_validation->set_rules('shed_id', $this->phrase('shed', 'Shed'), 'integer');
        $this->form_validation->set_rules('usage_date', $this->phrase('usage_date', 'Usage Date'), 'required');
        $this->form_validation->set_rules('total_purchased_qty', $this->phrase('total_purchased', 'Total Purchased'), 'numeric');
        $this->form_validation->set_rules('used_total_qty', $this->phrase('used_total', 'Used Total'), 'numeric');
        $this->form_validation->set_rules('total_wasted_qty', $this->phrase('total_wasted', 'Total Wasted'), 'numeric');
        $this->form_validation->set_rules('notes', $this->phrase('notes', 'Notes'), 'max_length[2000]');
        $this->form_validation->set_rules('inventory_product_id', $this->phrase('product', 'Product'), 'required');
        $this->form_validation->set_rules('location_id', $this->phrase('location', 'Location'), 'required|integer');

        if ($this->form_validation->run() === true) {
            // Permissions: enforce consumption and override
            if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
                if (!$this->permission1->method('stock_consumption', 'create')->access()) {
                    $this->flash_and_redirect('unauthorized', 'vaccine-usages', 'exception');
                }
            }
            $total_purchased = $this->decimal_input('total_purchased_qty');
            $total_used      = $this->decimal_input('used_total_qty');
            $total_wasted    = $this->decimal_input('total_wasted_qty');
            $total_instock   = max(0, $total_purchased - $total_used - $total_wasted);
            $usage_date      = $this->input->post('usage_date', true);
            $usage_date      = $usage_date ? date('Y-m-d', strtotime($usage_date)) : date('Y-m-d');
            $shed_id         = $this->input->post('shed_id', true);
            $selectedLocationId = (int) $this->input->post('location_id', true);
            if ($selectedLocationId <= 0) {
                $selectedLocationId = null;
            }

            $payload = array(
                'vaccine_id'           => (int) $this->input->post('vaccine_id', true),
                'shed_id'              => ($shed_id !== '') ? (int) $shed_id : null,
                'usage_date'           => $usage_date,
                'inventory_product_id' => trim($this->input->post('inventory_product_id', true)),
                'total_purchased_qty'  => $this->format_decimal($total_purchased),
                'used_total_qty'       => $this->format_decimal($total_used),
                'total_wasted_qty'     => $this->format_decimal($total_wasted),
                'total_instock_qty'    => $this->format_decimal($total_instock),
                'notes'                => $this->input->post('notes', true),
            );

            if ($selectedLocationId) {
                if ($this->db->field_exists('inventory_location_id', 'vaccine_usages')) {
                    $payload['inventory_location_id'] = $selectedLocationId;
                } elseif ($this->db->field_exists('location_id', 'vaccine_usages')) {
                    $payload['location_id'] = $selectedLocationId;
                }
            }

            $allowOverride = (bool) $this->input->post('allow_override', true);

            if ($vaccine_usage) {
                $usageId = (int) $vaccine_usage['id'];
                $success = $this->livestock_model->update_vaccine_usage($usageId, $payload);
                if ($success) {
                    $this->sync_vaccine_usage_ledger($usageId, $allowOverride, null, $selectedLocationId);
                    $message = 'successfully_updated';
                    log_message('info', sprintf('Vaccine usage #%d updated and synced by user %s', $usageId, $this->session->userdata('id')));
                } else {
                    $message = 'please_try_again';
                }
            } else {
                $usageId = $this->livestock_model->create_vaccine_usage($payload);
                if ($usageId) {
                    $this->sync_vaccine_usage_ledger($usageId, $allowOverride, null, $selectedLocationId);
                    $message = 'save_successfully';
                    log_message('info', sprintf('Vaccine usage #%d created and synced by user %s', $usageId, $this->session->userdata('id')));
                } else {
                    $message = 'please_try_again';
                }
                $success = (bool) $usageId;
            }

            if ($success && $allowOverride) {
                if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
                    if (!$this->permission1->method('stock_override', 'create')->access()) {
                        $this->flash_and_redirect('unauthorized', 'vaccine-usages', 'exception');
                    }
                }
                log_message('warning', sprintf('Vaccine usage #%d applied with override by user %s', $usageId, $this->session->userdata('id')));
            }

            $this->flash_and_redirect($message, 'vaccine-usages', $success ? 'message' : 'exception');
        }

        $data['title'] = $vaccine_usage ? $this->phrase('edit_vaccine_usage', 'Edit Vaccine Usage') : $this->phrase('add_vaccine_usage', 'Add Vaccine Usage');
        if (!$existingLocationId && $vaccine_usage) {
            $existingLocationId = $this->inventoryledger->resolveShedLocationId((int) $vaccine_usage['shed_id']);
        }
        $data['vaccine_usage'] = (object) array(
            'id'                   => $vaccine_usage ? $vaccine_usage['id'] : null,
            'vaccine_id'           => set_value('vaccine_id', $vaccine_usage ? $vaccine_usage['vaccine_id'] : ''),
            'shed_id'              => set_value('shed_id', $vaccine_usage ? $vaccine_usage['shed_id'] : ''),
            'usage_date'           => set_value('usage_date', $vaccine_usage ? $vaccine_usage['usage_date'] : date('Y-m-d')),
            'inventory_product_id' => set_value('inventory_product_id', $vaccine_usage ? $vaccine_usage['inventory_product_id'] : ''),
            'total_purchased_qty'  => set_value('total_purchased_qty', $vaccine_usage ? $vaccine_usage['total_purchased_qty'] : '0.00'),
            'used_total_qty'       => set_value('used_total_qty', $vaccine_usage ? $vaccine_usage['used_total_qty'] : '0.00'),
            'total_wasted_qty'     => set_value('total_wasted_qty', $vaccine_usage ? $vaccine_usage['total_wasted_qty'] : '0.00'),
            'total_instock_qty'    => set_value('total_instock_qty', $vaccine_usage ? $vaccine_usage['total_instock_qty'] : '0.00'),
            'notes'                => set_value('notes', $vaccine_usage ? $vaccine_usage['notes'] : ''),
            'location_id'          => set_value('location_id', $existingLocationId ?: ''),
        );
        $data['vaccines'] = $this->livestock_model->get_vaccines_dropdown();
        $data['sheds']    = $this->livestock_model->get_sheds();
        $data['inventory_products'] = $this->inventory_model->get_consumable_products();
        $data['locations'] = $this->inventory_model->get_active_locations();
        $data['recent_notifications'] = $this->inventory_model->get_recent_notifications(10);

        $this->render('vaccine_usages/form', $data);
    }

    public function vaccine_usage_delete($id = null)
    {
        if (empty($id)) {
            $this->flash_and_redirect('not_found', 'vaccine-usages', 'exception');
        }

        $this->inventoryledger->clearMovementsByReference('vaccine_usage', $id);
        $success = $this->livestock_model->delete_vaccine_usage($id);
        $message = $success ? 'successfully_deleted' : 'please_try_again';
        if ($success) {
            log_message('info', sprintf('Vaccine usage #%d deleted and ledger movements cleared by user %s', $id, $this->session->userdata('id')));
        }
        $this->flash_and_redirect($message, 'vaccine-usages', $success ? 'message' : 'exception');
    }

    /* ------------------------------ Farm Reports ----------------------- */

    public function farm_reports()
    {
        $filters = $this->normalize_report_filters(
            $this->input->get('start_date', true),
            $this->input->get('end_date', true),
            $this->input->get('interval', true)
        );

        $data['title']     = $this->phrase('farm_reports', 'Farm Reports');
        $data['filters']   = $filters;
        $data['summary']   = $this->livestock_model->get_farm_report_summary($filters['start_date'], $filters['end_date']);
        $data['timeseries'] = $this->livestock_model->get_farm_report_timeseries($filters['start_date'], $filters['end_date'], $filters['interval']);

        $this->render('reports/farm_reports', $data);
    }

    public function farm_reports_data()
    {
        $filters = $this->normalize_report_filters(
            $this->input->post('start_date', true),
            $this->input->post('end_date', true),
            $this->input->post('interval', true)
        );

        $summary   = $this->livestock_model->get_farm_report_summary($filters['start_date'], $filters['end_date']);
        $timeseries = $this->livestock_model->get_farm_report_timeseries($filters['start_date'], $filters['end_date'], $filters['interval']);

        $response = array(
            'success'    => true,
            'filters'    => $filters,
            'summary'    => $summary,
            'timeseries' => $timeseries,
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function production_percentage()
    {
        $products = $this->livestock_model->get_products();
        $livestockAssets = $this->livestock_model->get_livestock_assets();

        $productIdInput = $this->input->get('product_id', true);
        $selectedProductId = ($productIdInput !== null) ? trim((string) $productIdInput) : '';

        $livestockIdInput = $this->input->get('livestock_id', true);
        $selectedLivestockId = ($livestockIdInput !== null) ? trim((string) $livestockIdInput) : '';
        $selectedLivestock = null;
        foreach ($livestockAssets as $asset) {
            if ((string) $asset['id'] === $selectedLivestockId) {
                $selectedLivestock = $asset;
                break;
            }
        }
        if ($selectedLivestockId !== '' && !$selectedLivestock) {
            $selectedLivestockId = '';
        }

        $intervalInput = strtolower((string) $this->input->get('interval', true));
        $allowedIntervals = array('daily', 'weekly', 'monthly', 'yearly');
        $selectedInterval = in_array($intervalInput, $allowedIntervals, true) ? $intervalInput : 'daily';

        $assetCountInput = $this->input->get('asset_count', true);
        $assetCountInputValue = ($assetCountInput !== null) ? trim((string) $assetCountInput) : '';
        $assetCount = null;
        $assetCountProvided = false;
        if ($assetCountInputValue !== '') {
            $candidate = (float) $assetCountInputValue;
            if ($candidate > 0) {
                $assetCount = $candidate;
                $assetCountProvided = true;
            }
        }
        if (!$assetCountProvided && $selectedLivestock && array_key_exists('livestock_total_qty', $selectedLivestock)) {
            $assetCountInputValue = (string) $selectedLivestock['livestock_total_qty'];
            $candidate = (float) ($selectedLivestock['livestock_total_qty'] ?? 0);
            if ($candidate > 0) {
                $assetCount = $candidate;
            }
        }

        $endDateInput = $this->input->get('end_date', true);
        $endDate = (!empty($endDateInput) && strtotime($endDateInput)) ? date('Y-m-d', strtotime($endDateInput)) : date('Y-m-d');

        $startDateInput = $this->input->get('start_date', true);
        $startDate = (!empty($startDateInput) && strtotime($startDateInput)) ? date('Y-m-d', strtotime($startDateInput)) : date('Y-m-d', strtotime('-29 days', strtotime($endDate)));

        if ($startDate > $endDate) {
            $tmp = $startDate;
            $startDate = $endDate;
            $endDate = $tmp;
        }

        $productName = '';
        foreach ($products as $product) {
            if ((string) $product['product_id'] === $selectedProductId) {
                $productName = $product['product_name'];
                break;
            }
        }

        $summary = null;
        $series  = array();
        if ($selectedProductId !== '' && $productName !== '') {
            $summary = $this->livestock_model->get_production_percentage_summary($selectedProductId, $startDate, $endDate);
            $series  = $this->livestock_model->get_production_percentage_series($selectedProductId, $startDate, $endDate, $selectedInterval);
        } else {
            $selectedProductId = '';
        }

        $productionPercent = null;
        $outputPerAsset = null;
        if ($summary && $assetCount && $assetCount > 0) {
            $totalOutput = (float) ($summary['total_output'] ?? 0.0);
            if ($totalOutput > 0) {
                $outputPerAsset = $totalOutput / $assetCount;
                $productionPercent = $outputPerAsset * 100.0;
            }
        }

        $enhancedSeries = array();
        foreach ($series as $row) {
            $row['production_percent'] = null;
            if ($assetCount && $assetCount > 0) {
                $row['production_percent'] = ($row['total_output'] ?? 0) > 0
                    ? (($row['total_output'] / $assetCount) * 100.0)
                    : 0.0;
            }
            $enhancedSeries[] = $row;
        }

        $data = array(
            'title'                   => $this->phrase('production_percentage_report', 'Production Percentage'),
            'products'                => $products,
            'selected_product_id'     => $selectedProductId,
            'selected_product_name'   => $productName,
            'livestock_assets'        => $livestockAssets,
            'selected_livestock_id'   => $selectedLivestockId,
            'selected_livestock'      => $selectedLivestock,
            'filters'                 => array(
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ),
            'selected_interval'       => $selectedInterval,
            'asset_count'             => $assetCount,
            'asset_count_input'       => $assetCountInputValue,
            'summary'                 => $summary,
            'series'                  => $enhancedSeries,
            'production_percent'      => $productionPercent,
            'output_per_asset'        => $outputPerAsset,
        );

        $this->render('reports/production_percentage', $data);
    }

    private function sync_feed_usage_ledger($usageId, $allowOverride, $manualLotId = null, $locationOverride = null)
    {
        $usage = $this->livestock_model->get_feed_usage($usageId);
        if (!$usage) {
            return;
        }

        $this->inventoryledger->clearMovementsByReference('feed_usage', $usageId);

        $productId = isset($usage['inventory_product_id']) ? trim($usage['inventory_product_id']) : '';
        if ($productId === '') {
            log_message('debug', sprintf('Feed usage #%d skipped ledger sync: no inventory product mapping', $usageId));
            return;
        }

        $createdBy = (isset($this->session) && method_exists($this->session, 'userdata')) ? $this->session->userdata('id') : null;

        if (!$locationOverride) {
            if (isset($usage['inventory_location_id']) && $usage['inventory_location_id']) {
                $locationOverride = (int) $usage['inventory_location_id'];
            } elseif (isset($usage['location_id']) && $usage['location_id']) {
                $locationOverride = (int) $usage['location_id'];
            }
        }

        if (!$locationOverride) {
            if (isset($usage['inventory_location_id']) && $usage['inventory_location_id']) {
                $locationOverride = (int) $usage['inventory_location_id'];
            } elseif (isset($usage['location_id']) && $usage['location_id']) {
                $locationOverride = (int) $usage['location_id'];
            }
        }

        $locationId = null;
        if ($locationOverride) {
            $locationId = $this->inventoryledger->resolveLocationId($locationOverride);
        }
        if (!$locationId) {
            $locationId = $this->inventoryledger->resolveShedLocationId((int) ($usage['shed_id'] ?? 0));
        }
        if (!$locationId) {
            $locationId = $this->inventoryledger->getDefaultLocationId();
        }

        if (!$locationId) {
            log_message('warning', sprintf('Feed usage #%d skipped ledger sync: no location resolved', $usageId));
            return;
        }

        $movementDate    = $usage['updated_at'] ?? date('Y-m-d');
        $referenceLineId = isset($usage['feed_id']) ? (int) $usage['feed_id'] : null;

        if ((float) $usage['used_total_qty'] > 0) {
            $payload = array(
                'product_id'        => $productId,
                'location_id'       => $locationId,
                'quantity'          => (float) $usage['used_total_qty'],
                'reason_code'       => 'FEED_USAGE',
                'movement_date'     => $movementDate,
                'reference_type'    => 'feed_usage',
                'reference_id'      => $usageId,
                'reference_line_id' => $referenceLineId,
                'narration'         => sprintf('Feed usage #%d (feed %s)', $usageId, $referenceLineId ?: '?'),
                'allow_negative'    => $allowOverride,
                'created_by'        => $createdBy,
            );
            if (!empty($manualLotId)) {
                $payload['lot_id'] = (int) $manualLotId;
            }
            $result = $this->inventoryledger->recordConsumption($payload);
            if ($result === false) {
                log_message('error', sprintf('Failed to sync feed usage consumption for usage #%d', $usageId));
            }
        }

        if ((float) $usage['total_wasted_qty'] > 0) {
            $payload = array(
                'product_id'        => $productId,
                'location_id'       => $locationId,
                'quantity'          => (float) $usage['total_wasted_qty'],
                'reason_code'       => 'FEED_WASTE',
                'movement_date'     => $movementDate,
                'reference_type'    => 'feed_usage',
                'reference_id'      => $usageId,
                'reference_line_id' => $referenceLineId,
                'narration'         => sprintf('Feed waste #%d', $usageId),
                'allow_negative'    => $allowOverride,
                'created_by'        => $createdBy,
            );
            if (!empty($manualLotId)) {
                $payload['lot_id'] = (int) $manualLotId;
            }
            $result = $this->inventoryledger->recordWaste($payload);
            if ($result === false) {
                log_message('error', sprintf('Failed to sync feed waste for usage #%d', $usageId));
            }
        }
    }

    private function sync_vaccine_usage_ledger($usageId, $allowOverride, $manualLotId = null, $locationOverride = null)
    {
        $usage = $this->livestock_model->get_vaccine_usage($usageId);
        if (!$usage) {
            return;
        }

        $this->inventoryledger->clearMovementsByReference('vaccine_usage', $usageId);

        $productId = isset($usage['inventory_product_id']) ? trim($usage['inventory_product_id']) : '';
        if ($productId === '') {
            log_message('debug', sprintf('Vaccine usage #%d skipped ledger sync: no inventory product mapping', $usageId));
            return;
        }

        $createdBy = (isset($this->session) && method_exists($this->session, 'userdata')) ? $this->session->userdata('id') : null;

        $locationId = null;
        if ($locationOverride) {
            $locationId = $this->inventoryledger->resolveLocationId($locationOverride);
        }
        if (!$locationId) {
            $locationId = $this->inventoryledger->resolveShedLocationId((int) ($usage['shed_id'] ?? 0));
        }
        if (!$locationId) {
            $locationId = $this->inventoryledger->getDefaultLocationId();
        }

        if (!$locationId) {
            log_message('warning', sprintf('Vaccine usage #%d skipped ledger sync: no location resolved', $usageId));
            return;
        }

        $movementDate    = $usage['usage_date'] ?? ($usage['updated_at'] ?? date('Y-m-d'));
        $referenceLineId = isset($usage['vaccine_id']) ? (int) $usage['vaccine_id'] : null;

        if ((float) $usage['used_total_qty'] > 0) {
            $payload = array(
                'product_id'        => $productId,
                'location_id'       => $locationId,
                'quantity'          => (float) $usage['used_total_qty'],
                'reason_code'       => 'VACCINE_USAGE',
                'movement_date'     => $movementDate,
                'reference_type'    => 'vaccine_usage',
                'reference_id'      => $usageId,
                'reference_line_id' => $referenceLineId,
                'narration'         => sprintf('Vaccine usage #%d (vaccine %s)', $usageId, $referenceLineId ?: '?'),
                'allow_negative'    => $allowOverride,
                'created_by'        => $createdBy,
            );
            if (!empty($manualLotId)) {
                $payload['lot_id'] = (int) $manualLotId;
            }
            $result = $this->inventoryledger->recordConsumption($payload);
            if ($result === false) {
                log_message('error', sprintf('Failed to sync vaccine usage consumption for usage #%d', $usageId));
            }
        }

        if ((float) $usage['total_wasted_qty'] > 0) {
            $payload = array(
                'product_id'        => $productId,
                'location_id'       => $locationId,
                'quantity'          => (float) $usage['total_wasted_qty'],
                'reason_code'       => 'VACCINE_WASTE',
                'movement_date'     => $movementDate,
                'reference_type'    => 'vaccine_usage',
                'reference_id'      => $usageId,
                'reference_line_id' => $referenceLineId,
                'narration'         => sprintf('Vaccine waste #%d', $usageId),
                'allow_negative'    => $allowOverride,
                'created_by'        => $createdBy,
            );
            if (!empty($manualLotId)) {
                $payload['lot_id'] = (int) $manualLotId;
            }
            $result = $this->inventoryledger->recordWaste($payload);
            if ($result === false) {
                log_message('error', sprintf('Failed to sync vaccine waste for usage #%d', $usageId));
            }
        }
    }

    public function resync_inventory_usage($type = 'all', $allowOverride = '0')
    {
        if (!$this->input->is_cli_request()) {
            show_error('Inventory usage resync must be run from the CLI.', 403);
        }

        $type = strtolower((string) $type);
        $validTypes = array('feed', 'vaccine', 'all');
        if (!in_array($type, $validTypes, true)) {
            $type = 'all';
        }

        $allowOverrideFlag = in_array(strtolower((string) $allowOverride), array('1', 'true', 'yes', 'allow', 'override'), true);

        $summary = array('feed' => 0, 'vaccine' => 0);

        if ($type === 'all' || $type === 'feed') {
            $ids = $this->db->select('id')->from('feed_usages')->order_by('id', 'asc')->get()->result_array();
            foreach ($ids as $row) {
                $usageId = (int) $row['id'];
                $this->sync_feed_usage_ledger($usageId, $allowOverrideFlag, null, null);
                $summary['feed']++;
            }
        }

        if ($type === 'all' || $type === 'vaccine') {
            $ids = $this->db->select('id')->from('vaccine_usages')->order_by('id', 'asc')->get()->result_array();
            foreach ($ids as $row) {
                $usageId = (int) $row['id'];
                $this->sync_vaccine_usage_ledger($usageId, $allowOverrideFlag, null, null);
                $summary['vaccine']++;
            }
        }

        echo sprintf("Feed usages synced: %d%s", $summary['feed'], PHP_EOL);
        echo sprintf("Vaccine usages synced: %d%s", $summary['vaccine'], PHP_EOL);
        echo sprintf("Override mode: %s%s", $allowOverrideFlag ? 'enabled' : 'disabled', PHP_EOL);
    }

    /* ----------------------------- Helpers ------------------------------- */

    private function normalize_report_filters($start_date, $end_date, $interval)
    {
        $default_end = date('Y-m-d');
        $sanitized_end = (!empty($end_date) && strtotime($end_date)) ? date('Y-m-d', strtotime($end_date)) : $default_end;
        $default_start = date('Y-m-d', strtotime('-29 days', strtotime($sanitized_end)));
        $sanitized_start = (!empty($start_date) && strtotime($start_date)) ? date('Y-m-d', strtotime($start_date)) : $default_start;

        if ($sanitized_start > $sanitized_end) {
            $temp = $sanitized_start;
            $sanitized_start = $sanitized_end;
            $sanitized_end = $temp;
        }

        $allowed_intervals = array('daily', 'weekly', 'monthly');
        $sanitized_interval = in_array($interval, $allowed_intervals, true) ? $interval : 'daily';

        return array(
            'start_date' => $sanitized_start,
            'end_date'   => $sanitized_end,
            'interval'   => $sanitized_interval,
        );
    }

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
