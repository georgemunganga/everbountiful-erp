<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Purchase_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array('account/Accounts_model'));
        $this->load->library('InventoryLedger');
    }

    public function supplier_list()
    {
        $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $query = $this->db->select('*')
            ->from('supplier_information')
            ->where('status', '1')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function get_purchase_summary($date_from, $date_to)
    {
        return $this->db->select('
            pp.purchase_date,
            COUNT(DISTINCT pp.purchase_id) as total_purchase,
            SUM(ppd.quantity) as total_qty,
            SUM(ppd.total_amount) as total_amount,
            pi.product_name,
        ')
            ->from('product_purchase pp')
            ->join('product_purchase_details ppd', 'pp.purchase_id = ppd.purchase_id', 'left')
            ->join('product_information pi', 'pi.product_id = ppd.product_id', 'left')
            ->where('STR_TO_DATE(pp.purchase_date, "%Y-%m-%d") >=', $date_from)
            ->where('STR_TO_DATE(pp.purchase_date, "%Y-%m-%d") <=', $date_to)
            ->where('pp.status', 1)
            ->group_by('pp.purchase_date')
            ->order_by('pp.purchase_date', 'asc')
            ->get()
            ->result();
    }


    public function pmethod_dropdown()
    {
        $data = $this->db->select('*')
            ->from('acc_coa')
            ->where('PHeadName', 'Cash')
            ->or_where('PHeadName', 'Cash at Bank')
            ->get()
            ->result();

        $list[''] = 'Select Method';
        if (!empty($data)) {
            $list[0] = 'Credit Purchase';
            foreach ($data as $value)
                $list[$value->HeadCode] = $value->HeadName;
            return $list;
        } else {
            return false;
        }
    }
    public function pmethod_dropdown_new()
    {
        $data = $this->db->select('*')
            ->from('acc_coa')
            ->where('PHeadName', 'Cash')
            ->or_where('PHeadName', 'Cash at Bank')
            ->get()
            ->result();

        $list[''] = 'Select Method';
        if (!empty($data)) {

            foreach ($data as $value)
                $list[$value->HeadCode] = $value->HeadName;
            return $list;
        } else {
            return false;
        }
    }

    public function product_search_item($supplier_id, $product_name)
    {
        $query = $this->db->select('*')
            ->from('supplier_product a')
            ->join('product_information b', 'a.product_id = b.product_id')
            ->where('a.supplier_id', $supplier_id)
            ->like('b.product_model', $product_name, 'both')
            ->or_where('a.supplier_id', $supplier_id)
            ->like('b.product_name', $product_name, 'both')
            ->group_by('a.product_id')
            ->order_by('b.product_name', 'asc')
            ->limit(15)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function retrieve_purchase_editdata($purchase_id)
    {
        $this->db->select(
            'a.*,
                        b.*,
                        a.id as dbpurs_id,
                        c.product_id,
                        c.product_name,
                        c.product_model,
                        d.supplier_id,
                        d.supplier_name'
        );
        $this->db->from('product_purchase a');
        $this->db->join('product_purchase_details b', 'b.purchase_id =a.id');
        $this->db->join('product_information c', 'c.product_id =b.product_id');
        $this->db->join('supplier_information d', 'd.supplier_id = a.supplier_id');
        $this->db->where('a.purchase_id', $purchase_id);
        $this->db->order_by('a.purchase_details', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function get_total_product($product_id, $supplier_id)
    {
        $this->db->select('SUM(a.quantity) as total_purchase,b.*');
        $this->db->from('product_purchase_details a');
        $this->db->join('supplier_product b', 'a.product_id=b.product_id');
        $this->db->where('a.product_id', $product_id);
        $this->db->where('b.supplier_id', $supplier_id);
        $total_purchase = $this->db->get()->row();

        $this->db->select('SUM(b.quantity) as total_sale');
        $this->db->from('invoice_details b');
        $this->db->where('b.product_id', $product_id);
        $total_sale = $this->db->get()->row();

        $this->db->select('a.*,b.*');
        $this->db->from('product_information a');
        $this->db->join('supplier_product b', 'a.product_id=b.product_id');
        $this->db->where(array('a.product_id' => $product_id, 'a.status' => 1));
        $this->db->where('b.supplier_id', $supplier_id);
        $product_information = $this->db->get()->row();

        $available_quantity = ($total_purchase->total_purchase - $total_sale->total_sale);

        $data2 = array(
            'total_product'  => $available_quantity,
            'supplier_price' => $product_information->supplier_price,
            'price'          => $product_information->price,
            'supplier_id'    => $product_information->supplier_id,
            'unit'           => $product_information->unit,
            'product_vat'    => $product_information->product_vat,
        );

        return $data2;
    }

    public function count_purchase()
    {
        $this->db->select('a.*,b.supplier_name');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->order_by('a.purchase_date', 'desc');
        $query = $this->db->get();

        $last_query = $this->db->last_query();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }

    public function getPurchaseList($postData = null)
    {
        $response = array();
        $fromdate = $this->input->post('fromdate');
        $todate   = $this->input->post('todate');
        if (!empty($fromdate)) {
            $datbetween = "(a.purchase_date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (b.supplier_name like '%" . $searchValue . "%' or a.chalan_no like '%" . $searchValue . "%' or a.purchase_id like'%" . $searchValue . "%' or a.purchase_id like'%" . $searchValue . "%')";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);

        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);

        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('a.*,b.supplier_name');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);

        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;
        foreach ($records as $record) {
            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";

            $button .= '  <a href="' . $base_url . 'purchase_details/' . $record->purchase_id . '" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('purchase_details') . '"><i class="fa fa-window-restore" aria-hidden="true"></i></a>';
            if ($this->permission1->method('manage_purchase', 'update')->access()) {
                $approve = $this->db->select('status,referenceNo')->from('acc_vaucher')->where('referenceNo', $record->purchase_id)->where('status', 1)->get()->num_rows();
                if ($approve == 0) {

                    $button .= ' <a href="' . $base_url . 'purchase_edit/' . $record->purchase_id . '" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
                }
            }



            $purchase_ids = '<a href="' . $base_url . 'purchase_details/' . $record->purchase_id . '">' . $record->purchase_id . '</a>';

            $data[] = array(
                'sl'               => $sl,
                'chalan_no'        => $record->chalan_no,
                'purchase_id'      => $purchase_ids,
                'supplier_name'    => $record->supplier_name,
                'purchase_date'    => $record->purchase_date,
                'total_amount'     => $record->grand_total_amount,
                'button'           => $button,

            );
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    public function purchase_details_data($purchase_id)
    {
        $this->db->select('a.*,b.*,c.*,e.purchase_details,d.product_id,d.product_name,d.product_model');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->join('product_purchase_details c', 'c.purchase_id = a.id');
        $this->db->join('product_information d', 'd.product_id = c.product_id');
        $this->db->join('product_purchase e', 'e.purchase_id = c.purchase_id');
        $this->db->where('a.purchase_id', $purchase_id);
        // $this->db->group_by('d.product_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    /*invoice no generator*/
    public function number_generator()
    {
        $this->db->select_max('purchase_id', 'invoice_no');
        $query      = $this->db->get('product_purchase');
        $result     = $query->result_array();
        $invoice_no = $result[0]['invoice_no'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            $invoice_no = 1;
        }
        return $invoice_no;
    }

    public function insert_purchase()
    {
        $purchase_id = $this->number_generator();
        $product_ids = (array)$this->input->post('product_id', true);
        if (empty($product_ids)) {
            return 3;
        }

        $supplier_id = $this->input->post('supplier_id', true);
        $supinfo = $this->db->select('*')->from('supplier_information')->where('supplier_id', $supplier_id)->get()->row();
        if (!$supinfo) {
            return 3;
        }
        $receive_by = $this->session->userdata('id');
        $purchase_date_input = $this->input->post('purchase_date', true);
        $purchase_date = !empty($purchase_date_input) ? $purchase_date_input : date('Y-m-d');
        $stock_location_id = $this->inventoryledger->resolveLocationId($this->input->post('stock_location_id', true));
        if (!$stock_location_id) {
            return 3;
        }

        $paid_amount = (float)$this->input->post('paid_amount', true);
        $due_amount = (float)$this->input->post('due_amount', true);
        $multipayamount = (array)$this->input->post('pamount_by_method', true);
        $multipaytype = (array)$this->input->post('multipaytype', true);
        $multiamnt = array_sum(array_map('floatval', $multipayamount));

        if ($multiamnt != $paid_amount) {
            return 2;
        }

        foreach ($product_ids as $product_id_check) {
            if (!$this->product_supplier_check($product_id_check, $supplier_id)) {
                $this->session->set_flashdata('error_message', display('product_and_supplier_did_not_match'));
                redirect(base_url('add_purchase'));
                exit();
            }
        }

        $is_credit = '';
        if (!empty($multipaytype) && isset($multipaytype[0]) && (int)$multipaytype[0] === 0) {
            $is_credit = 1;
        }

        $data = array(
            'purchase_id'        => $purchase_id,
            'chalan_no'          => $this->input->post('chalan_no', true),
            'supplier_id'        => $supplier_id,
            'stock_location_id'  => $stock_location_id,
            'grand_total_amount' => $this->input->post('grand_total_price', true),
            'total_discount'     => $this->input->post('discount', true),
            'invoice_discount'   => $this->input->post('total_discount', true),
            'total_vat_amnt'     => $this->input->post('total_vat_amnt', true),
            'purchase_date'      => $purchase_date,
            'purchase_details'   => $this->input->post('purchase_details', true),
            'paid_amount'        => $paid_amount,
            'due_amount'         => $due_amount,
            'status'             => 1,
            'bank_id'            => $this->input->post('bank_id', true),
            'payment_type'       => 1,
            'is_credit'          => $is_credit,
        );

        $this->db->trans_begin();

        $this->db->insert('product_purchase', $data);
        $purs_insert_id = $this->db->insert_id();

        $predefine_account = $this->db->select('*')->from('acc_predefine_account')->get()->row();
        $Narration = 'Purchase Voucher';
        $Comment = 'Purchase Voucher for supplier';
        $COAID = $predefine_account ? $predefine_account->purchaseCode : null;

        if ($multipaytype && $multipayamount) {
            if ((int)$multipaytype[0] === 0) {
                $amount_pay = $data['grand_total_amount'];
                $amnt_type = 'Credit';
                $reVID = $predefine_account->supplierCode;
                $subcode_row = $this->db->select('id')->from('acc_subcode')->where('referenceNo', $supplier_id)->where('subTypeId', 4)->get()->row();
                $subcode = $subcode_row ? $subcode_row->id : null;
                $this->insert_purchase_debitvoucher($is_credit, $purchase_id, $COAID, $amnt_type, $amount_pay, $Narration, $Comment, $reVID, $subcode);
            } else {
                $amnt_type = 'Debit';
                foreach ($multipaytype as $index => $pay_type) {
                    $reVID = $pay_type;
                    $amount_pay = isset($multipayamount[$index]) ? $multipayamount[$index] : 0;
                    $this->insert_purchase_debitvoucher($is_credit, $purchase_id, $COAID, $amnt_type, $amount_pay, $Narration, $Comment, $reVID);
                }

                if ($data['due_amount'] > 0) {
                    $amount_pay2 = $data['due_amount'];
                    $amnt_type2 = 'Credit';
                    $reVID2 = $predefine_account->supplierCode;
                    $subcode2_row = $this->db->select('id')->from('acc_subcode')->where('referenceNo', $supplier_id)->where('subTypeId', 4)->get()->row();
                    $subcode2 = $subcode2_row ? $subcode2_row->id : null;
                    $this->insert_purchase_debitvoucher(1, $purchase_id, $COAID, $amnt_type2, $amount_pay2, $Narration, $Comment, $reVID2, $subcode2);
                }
            }
        }

        $rates = (array)$this->input->post('product_rate', true);
        $quantities = (array)$this->input->post('product_quantity', true);
        $expiry_dates = (array)$this->input->post('expiry_date', true);
        $batch_numbers = (array)$this->input->post('batch_no', true);
        $total_prices = (array)$this->input->post('total_price', true);
        $discount_values = (array)$this->input->post('discountvalue', true);
        $vat_percents = (array)$this->input->post('vatpercent', true);
        $vat_values = (array)$this->input->post('vatvalue', true);
        $discount_percents = (array)$this->input->post('discount_per', true);

        foreach ($product_ids as $index => $product_id) {
            $product_quantity = isset($quantities[$index]) ? (float)$quantities[$index] : 0;
            $product_rate = isset($rates[$index]) ? (float)$rates[$index] : 0;
            $total_price = isset($total_prices[$index]) ? (float)$total_prices[$index] : 0;
            $batch_no = isset($batch_numbers[$index]) ? trim($batch_numbers[$index]) : null;
            $expiry_date = isset($expiry_dates[$index]) ? $expiry_dates[$index] : null;
            $discount_percent = isset($discount_percents[$index]) ? (float)$discount_percents[$index] : 0;
            $discount_value = isset($discount_values[$index]) ? (float)$discount_values[$index] : 0;
            $vat_percent = isset($vat_percents[$index]) ? (float)$vat_percents[$index] : 0;
            $vat_value = isset($vat_values[$index]) ? (float)$vat_values[$index] : 0;

            if ($product_quantity <= 0) {
                continue;
            }

            $purchaseUnitId = $this->inventoryledger->getPreferredPurchaseUnitId($product_id);
            if (!$purchaseUnitId) {
                $purchaseUnitId = $this->inventoryledger->getBaseUnitId($product_id);
            }

            $detailData = array(
                'purchase_detail_id' => $this->generator(15),
                'purchase_id'        => $purs_insert_id,
                'product_id'         => $product_id,
                'unit_id'            => $purchaseUnitId,
                'quantity'           => $product_quantity,
                'rate'               => $product_rate,
                'batch_id'           => $batch_no,
                'expiry_date'        => $expiry_date,
                'total_amount'       => $total_price,
                'discount'           => $discount_percent,
                'discount_amnt'      => $discount_value,
                'vat_amnt_per'       => $vat_percent,
                'vat_amnt'           => $vat_value,
                'status'             => 1,
            );

            $this->db->insert('product_purchase_details', $detailData);
            $detailRowId = $this->db->insert_id();

            $lotResult = $this->inventoryledger->recordPurchaseReceipt(array(
                'product_id'         => $product_id,
                'location_id'        => $stock_location_id,
                'unit_id'            => $purchaseUnitId,
                'quantity'           => $product_quantity,
                'batch_no'           => $batch_no,
                'expiry_date'        => $expiry_date,
                'purchase_id'        => $purchase_id,
                'purchase_detail_id' => $detailData['purchase_detail_id'],
                'purchase_date'      => $purchase_date,
                'total_amount'       => $total_price,
                'discount_amount'    => $discount_value,
                'vat_amount'         => $vat_value,
                'created_by'         => $receive_by,
            ));

            if ($lotResult === false) {
                $this->db->trans_rollback();
                return 3;
            }

            $this->db->where('id', $detailRowId)->update('product_purchase_details', array('stock_lot_id' => $lotResult['lot_id']));

            $product_price = array('supplier_price' => $product_rate);
            $this->db->where('product_id', $product_id)->update('supplier_product', $product_price);
        }

        $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
        if ($setting_data && (int)$setting_data[0]['is_autoapprove_v'] === 1) {
            $this->autoapprove($purchase_id);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return 3;
        }

        $this->db->trans_commit();
        return 1;
    }

    public function get_stock_locations()
    {
        return $this->inventoryledger->getActiveLocations();
    }

    public function product_supplier_check($product_id, $supplier_id)
    {
        $this->db->select('*');
        $this->db->from('supplier_product');
        $this->db->where('product_id', $product_id);
        $this->db->where('supplier_id', $supplier_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        }
        return 0;
    }

    // insert purchase debitvoucher
    public function insert_purchase_debitvoucher($is_credit = null, $purchase_id = null, $dbtid = null, $amnt_type = null, $amnt = null, $Narration = null, $Comment = null, $reVID = null, $subcode = null)
    {


        $fyear = financial_year();
        $VDate = date('Y-m-d');
        $CreateBy = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');
        if ($is_credit == 1) {
            $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'JV', 'VNo');
            $vaucherNo = "JV-" . ($maxid + 1);

            $debitinsert = array(
                'fyear'          =>  $fyear,
                'VNo'            =>  $vaucherNo,
                'Vtype'          =>  'JV',
                'referenceNo'    =>  $purchase_id,
                'VDate'          =>  $VDate,
                'COAID'          =>  $reVID,
                'Narration'      =>  $Narration,
                'ledgerComment'  =>  $Comment,
                'RevCodde'       =>  $dbtid,
                'subType'        =>  4,
                'subCode'        =>  $subcode,
                'isApproved'     =>  0,
                'CreateBy'       =>  $CreateBy,
                'CreateDate'     =>  $createdate,
                'status'         =>  0,
            );
        } else {

            $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
            $vaucherNo = "DV-" . ($maxid + 1);

            $debitinsert = array(
                'fyear'          =>  $fyear,
                'VNo'            =>  $vaucherNo,
                'Vtype'          =>  'DV',
                'referenceNo'    =>  $purchase_id,
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
        }
        if ($amnt_type == 'Debit') {

            $debitinsert['Debit']  = $amnt;
            $debitinsert['Credit'] =  0.00;
        } else {

            $debitinsert['Debit']  = 0.00;
            $debitinsert['Credit'] =  $amnt;
        }


        $this->db->insert('acc_vaucher', $debitinsert);

        return true;
    }

    public function autoapprove($purchase_id)
    {

        $vouchers = $this->db->select('referenceNo, VNo')->from('acc_vaucher')->where('referenceNo', $purchase_id)->where('status', 0)->get()->result();
        foreach ($vouchers as $value) {
            # code...
            $this->Accounts_model->approved_vaucher($value->VNo, 'active');
        }
        return true;
    }

    public function generator($lenth)
    {
        $number = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "N", "M", "O", "P", "Q", "R", "S", "U", "V", "T", "W", "X", "Y", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 34);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }
        return $con;
    }
}
