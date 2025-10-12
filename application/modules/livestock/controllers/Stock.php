<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends MX_Controller
{
    /**
     * @var array<string,string>
     */
    private $consumptionReasons = array(
        'FEED_USAGE'      => 'Feed usage',
        'VACCINE_USAGE'   => 'Vaccine usage',
        'PACKAGING'       => 'Packaging consumption',
        'MAINTENANCE'     => 'Maintenance / repairs',
        'GENERAL_CONSUME' => 'General consumption',
    );

    /**
     * @var array<string,string>
     */
    private $wasteReasons = array(
        'WASTE'        => 'General waste',
        'BREAKAGE'     => 'Breakage / damage',
        'EXPIRY'       => 'Expired product',
        'MORTALITY'    => 'Livestock mortality',
        'QUALITY_DROP' => 'Quality downgrade',
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
        redirect('stock/consumption');
    }

    public function consumption()
    {
        if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
            if (!$this->permission1->method('stock_consumption', 'create')->access()) {
                $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                redirect('home');
            }
        }

        $data = array();
        $data['title'] = display('inventory_consumption') ?: 'Inventory Consumption';
        $data['locations'] = $this->Inventory_model->get_active_locations();
        $data['products'] = $this->Inventory_model->get_consumable_products();
        $data['reasons'] = $this->consumptionReasons;
        $data['recent_consumption'] = $this->Inventory_model->get_recent_consumption(10);
        $data['recent_notifications'] = $this->Inventory_model->get_recent_notifications(10);
        $data['form_action'] = 'stock/consumption';

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            if ($this->input->post('allow_override') && isset($this->permission1) && method_exists($this->permission1, 'method')) {
                if (!$this->permission1->method('stock_override', 'create')->access()) {
                    $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                    redirect('stock/consumption');
                }
            }
            $this->processConsumption($data);
            return;
        }

        $this->render('consumption/form', $data);
    }

    private function processConsumption(array $data)
    {
        $this->form_validation->set_rules('quantity', display('quantity') ?: 'Quantity', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('reason_code', display('reason') ?: 'Reason', 'required');
        $this->form_validation->set_rules('product_id', display('product') ?: 'Product', 'required');
        $this->form_validation->set_rules('location_id', display('location') ?: 'Location', 'required|integer');

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

        if (!isset($this->consumptionReasons[$reasonCode])) {
            $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
            redirect('stock/consumption');
        }

        $payload = array(
            'quantity'          => $quantity,
            'unit_id'           => 0,
            'reason_code'       => $reasonCode,
            'movement_date'     => $consumedOn,
            'reference_type'    => 'consumption',
            'reference_id'      => $reference ?: null,
            'narration'         => $notes ?: $this->consumptionReasons[$reasonCode],
            'created_by'        => $this->session->userdata('id'),
        );

        $payload['product_id']  = trim($this->input->post('product_id', true));
        $payload['location_id'] = (int) $this->input->post('location_id', true);

        if ($this->input->post('allow_override')) {
            $payload['allow_negative'] = true;
        }

        $result = $this->inventoryledger->recordConsumption($payload);

        if ($result === false) {
            $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
            redirect('stock/consumption');
        }

        $movementsMade = is_array($result) && isset($result['movements']) ? count($result['movements']) : 1;
        log_message('info', sprintf(
            'Stock consumption recorded (reason %s, qty %.2f, movements %d) by user %s',
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
        redirect('stock/consumption');
    }

    public function waste()
    {
        if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
            if (!$this->permission1->method('stock_waste', 'create')->access()) {
                $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                redirect('home');
            }
        }

        $data = array();
        $data['title'] = display('inventory_waste') ?: 'Inventory Waste';
        $data['locations'] = $this->Inventory_model->get_active_locations();
        $data['products'] = $this->Inventory_model->get_consumable_products();
        $data['reasons'] = $this->wasteReasons;
        $data['recent_waste'] = $this->Inventory_model->get_recent_waste(10);
        $data['recent_notifications'] = $this->Inventory_model->get_recent_notifications(10);
        $data['form_action'] = 'stock/waste';

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            if ($this->input->post('allow_override') && isset($this->permission1) && method_exists($this->permission1, 'method')) {
                if (!$this->permission1->method('stock_override', 'create')->access()) {
                    $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                    redirect('stock/waste');
                }
            }
            $this->processWaste($data);
            return;
        }

        $this->render('waste/form', $data);
    }

    private function processWaste(array $data)
    {
        $this->form_validation->set_rules('quantity', display('quantity') ?: 'Quantity', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('reason_code', display('reason') ?: 'Reason', 'required');
        $this->form_validation->set_rules('product_id', display('product') ?: 'Product', 'required');
        $this->form_validation->set_rules('location_id', display('location') ?: 'Location', 'required|integer');

        if ($this->form_validation->run() === false) {
            $data['validation_errors'] = validation_errors();
            $this->render('waste/form', $data);
            return;
        }

        $quantity   = (float) $this->input->post('quantity', true);
        $reasonCode = strtoupper(trim($this->input->post('reason_code', true)));
        $wasteDate  = $this->input->post('waste_date', true);
        $notes      = $this->input->post('notes', true);
        $reference  = $this->input->post('reference_code', true);
        $allowOverride = (bool) $this->input->post('allow_override', true);

        if (!isset($this->wasteReasons[$reasonCode])) {
            $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
            redirect('stock/waste');
        }

        $payload = array(
            'quantity'          => $quantity,
            'unit_id'           => 0,
            'reason_code'       => $reasonCode,
            'movement_date'     => $wasteDate,
            'reference_type'    => 'waste',
            'reference_id'      => $reference ?: null,
            'narration'         => $notes ?: $this->wasteReasons[$reasonCode],
            'allow_negative'    => $allowOverride,
            'created_by'        => $this->session->userdata('id'),
        );

        $payload['product_id']  = trim($this->input->post('product_id', true));
        $payload['location_id'] = (int) $this->input->post('location_id', true);

        $result = $this->inventoryledger->recordWaste($payload);

        if ($result === false) {
            $message = $allowOverride
                ? (display('please_try_again') ?: 'Please try again')
                : (display('insufficient_stock') ?: 'Insufficient stock for waste. Enable override to proceed.');
            $this->session->set_flashdata('exception', $message);
            redirect('stock/waste');
        }

        log_message('info', sprintf(
            'Stock waste recorded (reason %s, qty %.2f) by user %s',
            $reasonCode,
            $quantity,
            $this->session->userdata('id')
        ));
        if ($allowOverride) {
            log_message('warning', sprintf('Waste override used for reason %s by user %s', $reasonCode, $this->session->userdata('id')));
        }

        $this->session->set_flashdata('message', display('save_successfully') ?: 'Saved successfully');
        redirect('stock/waste');
    }

    public function transfers()
    {
        if (isset($this->permission1) && method_exists($this->permission1, 'method')) {
            if (!$this->permission1->method('stock_transfer', 'create')->access()) {
                $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                redirect('home');
            }
        }

        $data = array();
        $data['title'] = display('inventory_transfer') ?: 'Inventory Transfer';
        $data['locations'] = $this->Inventory_model->get_active_locations();
        $data['lots'] = $this->Inventory_model->get_available_lots();
        $data['recent_transfers'] = $this->Inventory_model->get_recent_transfers(10);
        $data['recent_notifications'] = $this->Inventory_model->get_recent_notifications(10);
        $data['form_action'] = 'stock/transfers';

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            if ($this->input->post('allow_override') && isset($this->permission1) && method_exists($this->permission1, 'method')) {
                if (!$this->permission1->method('stock_override', 'create')->access()) {
                    $this->session->set_flashdata('exception', display('unauthorized') ?: 'Unauthorized');
                    redirect('stock/transfers');
                }
            }
            $this->processTransfers($data);
            return;
        }

        $this->render('transfers/form', $data);
    }

    private function processTransfers(array $data)
    {
        $this->form_validation->set_rules('source_lot_id', display('lot') ?: 'Lot', 'required|integer');
        $this->form_validation->set_rules('to_location_id', display('to') ?: 'To', 'required|integer');
        $this->form_validation->set_rules('quantity', display('quantity') ?: 'Quantity', 'required|numeric|greater_than[0]');

        if ($this->form_validation->run() === false) {
            $data['validation_errors'] = validation_errors();
            $this->render('transfers/form', $data);
            return;
        }

        $lotId          = (int) $this->input->post('source_lot_id', true);
        $toLocationId   = (int) $this->input->post('to_location_id', true);
        $fromLocationId = (int) $this->input->post('from_location_id', true);
        $quantity       = (float) $this->input->post('quantity', true);
        $transferDate   = $this->input->post('transfer_date', true);
        $notes          = $this->input->post('notes', true);
        $reference      = $this->input->post('transfer_code', true);

        $lot = $this->Inventory_model->get_lot($lotId);
        if (!$lot) {
            $this->session->set_flashdata('exception', display('not_found') ?: 'Not found');
            redirect('stock/transfers');
        }

        if ($fromLocationId <= 0) {
            $fromLocationId = (int) $lot['location_id'];
        }

        if ($fromLocationId === $toLocationId) {
            $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
            redirect('stock/transfers');
        }

        $result = $this->inventoryledger->recordTransfer(array(
            'source_lot_id'     => $lotId,
            'quantity'          => $quantity,
            'unit_id'           => 0,
            'to_location_id'    => $toLocationId,
            'from_location_id'  => $fromLocationId,
            'transfer_date'     => $transferDate,
            'transfer_code'     => $reference ?: null,
            'notes'             => $notes,
            'created_by'        => $this->session->userdata('id'),
        ));

        if ($result === false) {
            $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
            redirect('stock/transfers');
        }

        log_message('info', sprintf(
            'Stock transfer %s processed (lot %d -> location %d, quantity %.2f) by user %s',
            $result['transfer_code'] ?? 'N/A',
            $lotId,
            $toLocationId,
            $quantity,
            $this->session->userdata('id')
        ));

        $message = display('save_successfully') ?: 'Saved successfully';
        if (!empty($result['transfer_code'])) {
            $message .= sprintf(' (%s)', $result['transfer_code']);
        }
        $this->session->set_flashdata('message', $message);
        redirect('stock/transfers');
    }

    private function render($page, array $data = array())
    {
        $data['module'] = 'inventory';
        $data['page'] = $page;
        $data['module_label'] = display('livestock') ?: 'Livestock';
        echo modules::run('template/layout', $data);
    }
}
