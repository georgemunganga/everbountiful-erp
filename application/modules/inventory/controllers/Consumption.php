<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Consumption extends MX_Controller
{
    /**
     * @var array<string,string>
     */
    private $reasonOptions = array(
        'FEED_USAGE'      => 'Feed usage',
        'VACCINE_USAGE'   => 'Vaccine usage',
        'PACKAGING'       => 'Packaging consumption',
        'MAINTENANCE'     => 'Maintenance / repairs',
        'GENERAL_CONSUME' => 'General consumption',
    );

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('isLogIn')) {
            redirect('login');
        }

        $this->load->model('inventory/Inventory_model');
        $this->load->library('InventoryLedger');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->form();
    }

    public function form()
    {
        // Permission: stock_consumption create
        if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
            if (!$this->permission1->method('stock_consumption', 'create')->access()) {
                $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                redirect('home');
            }
        }
        $data = array();
        $data['title'] = display('inventory_consumption') ?: 'Inventory Consumption';
        $data['lots'] = $this->Inventory_model->get_available_lots();
        $data['locations'] = $this->Inventory_model->get_active_locations();
        $data['products'] = $this->Inventory_model->get_consumable_products();
        $data['reasons'] = $this->reasonOptions;
        $data['recent_consumption'] = $this->Inventory_model->get_recent_consumption(10);
        $data['recent_notifications'] = $this->Inventory_model->get_recent_notifications(10);

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            // Permission: stock_override for allowing negative
            if ($this->input->post('allow_override') && isset($this->permission1) && method_exists($this->permission1, 'method')) {
                if (!$this->permission1->method('stock_override', 'create')->access()) {
                    $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                    redirect('inventory/consumption');
                }
            }
            $this->process_form($data);
            return;
        }

        $this->render('consumption/form', $data);
    }

    private function process_form(array $data)
    {
        $autoAllocate = (bool) $this->input->post('auto_allocate', true);

        $this->form_validation->set_rules('quantity', display('quantity') ?: 'Quantity', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('reason_code', display('reason') ?: 'Reason', 'required');

        if ($autoAllocate) {
            $this->form_validation->set_rules('product_id', display('product') ?: 'Product', 'required');
            $this->form_validation->set_rules('location_id', display('location') ?: 'Location', 'required|integer');
        } else {
            $this->form_validation->set_rules('lot_id', display('lot') ?: 'Lot', 'required|integer');
        }

        if ($this->form_validation->run() === false) {
            $data['validation_errors'] = validation_errors();
            $this->render('consumption/form', $data);
            return;
        }

        $quantity   = (float) $this->input->post('quantity', true);
        $reasonCode = strtoupper(trim($this->input->post('reason_code', true)));
        $consumedOn = $this->input->post('consumption_date', true);
        $notes      = $this->input->post('notes', true);
        $reference  = $this->input->post('reference_code', true);

        if (!isset($this->reasonOptions[$reasonCode])) {
            $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
            redirect('inventory/consumption');
        }

        $payload = array(
            'quantity'          => $quantity,
            'unit_id'           => 0,
            'reason_code'       => $reasonCode,
            'movement_date'     => $consumedOn,
            'reference_type'    => 'consumption',
            'reference_id'      => $reference ?: null,
            'narration'         => $notes ?: $this->reasonOptions[$reasonCode],
            'created_by'        => $this->session->userdata('id'),
        );

        if ($autoAllocate) {
            $payload['product_id']  = trim($this->input->post('product_id', true));
            $payload['location_id'] = (int) $this->input->post('location_id', true);
        } else {
            $payload['lot_id'] = (int) $this->input->post('lot_id', true);
        }

        if ($this->input->post('allow_override')) {
            $payload['allow_negative'] = true;
        }

        $result = $this->inventoryledger->recordConsumption($payload);

        if ($result === false) {
            $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
            redirect('inventory/consumption');
        }

        $movementsMade = is_array($result) && isset($result['movements']) ? count($result['movements']) : 1;
        log_message('info', sprintf(
            'Inventory consumption recorded (reason %s, qty %.2f, movements %d) by user %s',
            $reasonCode,
            $quantity,
            $movementsMade,
            $this->session->userdata('id')
        ));
        if (!empty($payload['allow_negative'])) {
            log_message('warning', sprintf('Consumption override used for reason %s by user %s', $reasonCode, $this->session->userdata('id')));
        }

        $message = display('save_successfully') ?: 'Saved successfully';
        $this->session->set_flashdata('message', $message);
        redirect('inventory/consumption');
    }

    private function render($page, array $data = array())
    {
        $data['module'] = 'inventory';
        $data['page'] = $page;
        echo modules::run('template/layout', $data);
    }
}
