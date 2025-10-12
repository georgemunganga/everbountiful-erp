<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfers extends MX_Controller
{
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
        redirect('stock/transfers');
    }

    public function form()
    {
        redirect('stock/transfers');
    }

    private function process_form(array $data)
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
            redirect('inventory/transfers');
        }

        if ($fromLocationId <= 0) {
            $fromLocationId = (int) $lot['location_id'];
        }

        if ($fromLocationId === $toLocationId) {
            $this->session->set_flashdata('exception', display('please_try_again') ?: 'Please try again');
            redirect('inventory/transfers');
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
            redirect('inventory/transfers');
        }

        log_message('info', sprintf(
            'Inventory transfer %s processed (lot %d -> location %d, quantity %.2f) by user %s',
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
        redirect('inventory/transfers');
    }

    private function render($page, array $data = array())
    {
        $data['module'] = 'inventory';
        $data['page'] = $page;
        echo modules::run('template/layout', $data);
    }
}
