<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Waste extends MX_Controller
{
    /**
     * @var array<string,string>
     */
    private $reasonOptions = array(
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
        redirect('stock/waste');
    }

    public function form()
    {
        redirect('stock/waste');
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
            $this->render('waste/form', $data);
            return;
        }

        $quantity   = (float) $this->input->post('quantity', true);
        $reasonCode = strtoupper(trim($this->input->post('reason_code', true)));
        $wasteDate  = $this->input->post('waste_date', true);
        $notes      = $this->input->post('notes', true);
        $reference  = $this->input->post('reference_code', true);
        $allowOverride = (bool) $this->input->post('allow_override', true);

        if (!isset($this->reasonOptions[$reasonCode])) {
            $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
            redirect('inventory/waste');
        }

        $payload = array(
            'quantity'          => $quantity,
            'unit_id'           => 0,
            'reason_code'       => $reasonCode,
            'movement_date'     => $wasteDate,
            'reference_type'    => 'waste',
            'reference_id'      => $reference ?: null,
            'narration'         => $notes ?: $this->reasonOptions[$reasonCode],
            'allow_negative'    => $allowOverride,
            'created_by'        => $this->session->userdata('id'),
        );

        if ($autoAllocate) {
            $payload['product_id']  = trim($this->input->post('product_id', true));
            $payload['location_id'] = (int) $this->input->post('location_id', true);
        } else {
            $payload['lot_id'] = (int) $this->input->post('lot_id', true);
        }

        $result = $this->inventoryledger->recordWaste($payload);

        if ($result === false) {
            $message = $allowOverride
                ? (display('please_try_again') ?: 'Please try again')
                : (display('insufficient_stock') ?: 'Insufficient stock for waste. Enable override to proceed.');
            $this->session->set_flashdata('exception', $message);
            redirect('inventory/waste');
        }

        log_message('info', sprintf(
            'Inventory waste recorded (reason %s, qty %.2f) by user %s',
            $reasonCode,
            $quantity,
            $this->session->userdata('id')
        ));
        if ($allowOverride) {
            log_message('warning', sprintf('Waste override used for reason %s by user %s', $reasonCode, $this->session->userdata('id')));
        }

        $this->session->set_flashdata('message', display('save_successfully') ?: 'Saved successfully');
        redirect('inventory/waste');
    }

    private function render($page, array $data = array())
    {
        $data['module'] = 'inventory';
        $data['page'] = $page;
        echo modules::run('template/layout', $data);
    }
}
