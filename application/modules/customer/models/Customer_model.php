<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author:Greenwebb Ltd
# Author link: https://www.greenwebb.tech/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Customer_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    $this->ensure_group_id_column();
  }

  private $customer_tables_checked = array();

  private function ensure_customer_table($table)
  {
    if (isset($this->customer_tables_checked[$table])) {
      return $this->customer_tables_checked[$table];
    }
    if ($this->db->table_exists($table)) {
      $this->customer_tables_checked[$table] = true;
      return true;
    }
    $this->load->dbforge();
    switch ($table) {
      case 'customer_credit_notes':
        $this->dbforge->add_field(array(
          'id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true,
          ),
          'customer_id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
          ),
          'note_number' => array(
            'type' => 'VARCHAR',
            'constraint' => 100,
            'null' => true,
          ),
          'note_date' => array(
            'type' => 'DATE',
            'null' => true,
          ),
          'amount' => array(
            'type' => 'DECIMAL',
            'constraint' => '18,2',
            'default' => '0.00',
          ),
          'status' => array(
            'type' => 'VARCHAR',
            'constraint' => 50,
            'null' => true,
          ),
          'remarks' => array(
            'type' => 'TEXT',
            'null' => true,
          ),
          'created_at' => array(
            'type' => 'DATETIME',
            'null' => true,
          ),
          'updated_at' => array(
            'type' => 'DATETIME',
            'null' => true,
          ),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('customer_id');
        break;
      case 'customer_estimates':
        $this->dbforge->add_field(array(
          'id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true,
          ),
          'customer_id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
          ),
          'estimate_number' => array(
            'type' => 'VARCHAR',
            'constraint' => 100,
            'null' => true,
          ),
          'estimate_date' => array(
            'type' => 'DATE',
            'null' => true,
          ),
          'amount' => array(
            'type' => 'DECIMAL',
            'constraint' => '18,2',
            'default' => '0.00',
          ),
          'status' => array(
            'type' => 'VARCHAR',
            'constraint' => 50,
            'null' => true,
          ),
          'notes' => array(
            'type' => 'TEXT',
            'null' => true,
          ),
          'created_at' => array(
            'type' => 'DATETIME',
            'null' => true,
          ),
          'updated_at' => array(
            'type' => 'DATETIME',
            'null' => true,
          ),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('customer_id');
        break;
      case 'customer_expenses':
        $this->dbforge->add_field(array(
          'id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true,
          ),
          'customer_id' => array(
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
          ),
          'category' => array(
            'type' => 'VARCHAR',
            'constraint' => 100,
            'null' => true,
          ),
          'expense_date' => array(
            'type' => 'DATE',
            'null' => true,
          ),
          'amount' => array(
            'type' => 'DECIMAL',
            'constraint' => '18,2',
            'default' => '0.00',
          ),
          'vendor' => array(
            'type' => 'VARCHAR',
            'constraint' => 150,
            'null' => true,
          ),
          'notes' => array(
            'type' => 'TEXT',
            'null' => true,
          ),
          'created_at' => array(
            'type' => 'DATETIME',
            'null' => true,
          ),
          'updated_at' => array(
            'type' => 'DATETIME',
            'null' => true,
          ),
        ));
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('customer_id');
        break;
      default:
        $this->customer_tables_checked[$table] = false;
        return false;
    }
    $result = $this->dbforge->create_table($table, true);
    $this->customer_tables_checked[$table] = $result;
    return $result;
  }

  private function ensure_group_id_column()
  {
    // Add group_id column to customer_information if missing
    if (!$this->db->table_exists('customer_information')) {
      return;
    }
    if (!$this->db->field_exists('group_id', 'customer_information')) {
      $this->load->dbforge();
      // Add nullable INT column for customer group assignment
      $fields = array(
        'group_id' => array('type' => 'INT', 'constraint' => 11, 'null' => true),
      );
      $this->dbforge->add_column('customer_information', $fields);
    }
  }


  public function create($data = array())
  {
    $add_customer =  $this->db->insert('customer_information', $data);

    $customer_id = $this->db->insert_id();
    $coa = $this->headcode();
    if ($coa->HeadCode != NULL) {
      $headcode = $coa->HeadCode + 1;
    } else {
      $headcode = "113100000001";
    }
    $c_acc = $customer_id . '-' . $data['customer_name'];
    $createby = $this->session->userdata('id');
    $createdate = date('Y-m-d H:i:s');

    $customer_coa = [
      'HeadCode'         => $headcode,
      'HeadName'         => $c_acc,
      'PHeadName'        => 'Customers',
      'HeadLevel'        => '4',
      'IsActive'         => '1',
      'IsTransaction'    => '1',
      'IsGL'             => '0',
      'HeadType'         => 'A',
      'IsBudget'         => '0',
      'IsDepreciation'   => '0',
      'DepreciationRate' => '0',
      'customer_id'      => $customer_id,
      'CreateBy'         => $createby,
      'CreateDate'       => $createdate,
    ];

    $sub_acc = [
      'subTypeId'   => 3,
      'name'        => $data['customer_name'],
      'referenceNo' => $customer_id,
      'status'      => 1,
      'created_date' => date("Y-m-d"),

    ];

    if ($add_customer) {
      $this->db->insert('acc_subcode', $sub_acc);
    }
    if (!empty($this->input->post('previous_balance'))) {
    }
    return true;
  }

  public function customer_dropdown()
  {
    $data =  $this->db->select("*")
      ->from('customer_information')
      ->order_by('customer_name', 'asc')
      ->get()
      ->result();

    $list[''] = display('select_option');
    if (!empty($data)) {
      foreach ($data as $value)
        $list[$value->customer_id] = $value->customer_name;
      return $list;
    } else {
      return false;
    }
  }

  // Invoices for a customer within date range
  public function get_customer_invoices($customer_id, $from_date = null, $to_date = null)
  {
    // Sales invoices
    $this->db->select('i.id, i.invoice_id, i.invoice as invoice_no, i.date, i.total_amount, i.paid_amount, i.due_amount, i.is_credit, i.status AS invoice_status');
    $this->db->from('invoice i');
    $this->db->where('i.customer_id', $customer_id);
    if (!empty($from_date)) { $this->db->where('DATE(i.date) >=', $from_date); }
    if (!empty($to_date))   { $this->db->where('DATE(i.date) <=', $to_date); }
    $this->db->order_by('i.date', 'desc');
    $this->db->order_by('i.id', 'desc');
    $sales = $this->db->get()->result_array();
    foreach ($sales as &$row) { $row['source'] = 'sales'; }

    // Service invoices (normalize columns to match list)
    $this->db->select('s.id as id, s.voucher_no as invoice_id, s.voucher_no as invoice_no, s.date, s.total_amount, s.paid_amount, s.due_amount, s.is_credit');
    $this->db->from('service_invoice s');
    $this->db->where('s.customer_id', $customer_id);
    if (!empty($from_date)) { $this->db->where('DATE(s.date) >=', $from_date); }
    if (!empty($to_date))   { $this->db->where('DATE(s.date) <=', $to_date); }
    $this->db->order_by('s.date', 'desc');
    $this->db->order_by('s.id', 'desc');
    $service = $this->db->get()->result_array();
    foreach ($service as &$row) { $row['source'] = 'service'; $row['invoice_status'] = null; }

    // Merge and sort by date desc, then id desc
    $all = array_merge($sales, $service);
    usort($all, function($a, $b){
      $ad = strtotime(isset($a['date']) ? $a['date'] : '1970-01-01');
      $bd = strtotime(isset($b['date']) ? $b['date'] : '1970-01-01');
      if ($ad === $bd) { return (int)($b['id'] ?? 0) <=> (int)($a['id'] ?? 0); }
      return $bd <=> $ad; // desc
    });
    return $all;
  }

  public function get_customer_contacts($customer_id)
  {
    return $this->db->select('id, customer_id, name, email, phone, position, created_at')
      ->from('customer_contacts')
      ->where('customer_id', $customer_id)
      ->order_by('name', 'asc')
      ->get()
      ->result_array();
  }

  public function add_customer_contact($customer_id, array $data)
  {
    $allowed = ['name', 'email', 'phone', 'position', 'created_at'];
    $data = array_intersect_key($data, array_flip($allowed));
    $data['customer_id'] = $customer_id;
    if (empty($data['created_at'])) {
      $data['created_at'] = date('Y-m-d H:i:s');
    }
    $this->db->insert('customer_contacts', $data);
    return $this->db->insert_id();
  }

  public function delete_customer_contact($customer_id, $contact_id)
  {
    $this->db->where('customer_id', $customer_id)
      ->where('id', $contact_id)
      ->delete('customer_contacts');
    return $this->db->affected_rows() > 0;
  }
  // Payments for a customer within date range (from acc_transaction via COA)
  public function get_customer_payments($customer_id, $from_date = null, $to_date = null)
  {
    $results = array();
    $head = $this->db->select('HeadCode')->from('acc_coa')->where('customer_id', $customer_id)->get()->row();
    if ($head) {
      // Approved ledger entries
      $this->db->select('VDate as date, VNo as voucher_no, Debit, Credit, Narration');
      $this->db->from('acc_transaction');
      $this->db->where('COAID', $head->HeadCode);
      $this->db->where('IsAppove', 1);
      if (!empty($from_date)) {
        $this->db->where('DATE(VDate) >=', $from_date);
      }
      if (!empty($to_date)) {
        $this->db->where('DATE(VDate) <=', $to_date);
      }
      $ledger = $this->db->get()->result_array();
      $results = array_merge($results, $ledger);
    }

    // Include voucher entries tied to this customer sub-code (covers pending/unposted)
    $sub = $this->db->select('id')
      ->from('acc_subcode')
      ->where('referenceNo', $customer_id)
      ->where('subTypeId', 3)
      ->get()->row();
    if ($sub) {
      $this->db->select('VDate as date, VNo as voucher_no, Debit, Credit, Narration');
      $this->db->from('acc_vaucher');
      $this->db->where('subType', 3);
      $this->db->where('subCode', $sub->id);
      if (!empty($from_date)) {
        $this->db->where('DATE(VDate) >=', $from_date);
      }
      if (!empty($to_date)) {
        $this->db->where('DATE(VDate) <=', $to_date);
      }
      $vchs = $this->db->get()->result_array();
      $results = array_merge($results, $vchs);
    }

    // Sort by date ASC, then voucher
    usort($results, function($a, $b){
      $ad = strtotime(isset($a['date']) ? $a['date'] : '1970-01-01');
      $bd = strtotime(isset($b['date']) ? $b['date'] : '1970-01-01');
      if ($ad === $bd) { return strcmp(isset($a['voucher_no']) ? $a['voucher_no'] : '', isset($b['voucher_no']) ? $b['voucher_no'] : ''); }
      return $ad <=> $bd;
    });
    return $results;
  }

  // Customer cash receipts (voucher rows) for CRUD on Payments tab
  public function get_customer_receipts($customer_id, $from_date = null, $to_date = null)
  {
    // Resolve subcode id for this customer (subTypeId 3)
    $sub = $this->db->select('id')->from('acc_subcode')
      ->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row();
    if (!$sub) { return array(); }
    $this->db->select('v.VNo, v.VDate as date, v.referenceNo as invoice_id, v.Credit as amount, v.RevCodde as method_coa, v.ledgerComment as group_note, v.CreateDate as create_dt, pm.HeadName')
      ->from('acc_vaucher v')
      ->join('acc_coa pm', 'pm.HeadCode = v.RevCodde', 'left')
      ->where('v.subType', 3)
      ->where('v.subCode', $sub->id)
      ->where('v.Vtype', 'CV');
    if (!empty($from_date)) { $this->db->where('DATE(v.VDate) >=', $from_date); }
    if (!empty($to_date))   { $this->db->where('DATE(v.VDate) <=', $to_date); }
    $this->db->order_by('v.VDate','desc')->order_by('v.VNo','desc');
    return $this->db->get()->result_array();
  }

  // Statement summary and lines
  public function get_customer_statement($customer_id, $from_date, $to_date)
  {
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    $head = $this->db->select('HeadCode')->from('acc_coa')->where('customer_id', $customer_id)->get()->row();
    $headCode = $head ? $head->HeadCode : null;

    // Beginning balance: sum(debit-credit) before from_date
    if ($headCode) {
      $begin_q = $this->db->select('IFNULL(SUM(Debit),0) AS deb, IFNULL(SUM(Credit),0) AS cred')
        ->from('acc_transaction')
        ->where('COAID', $headCode)
        ->where('IsAppove', 1)
        ->where('DATE(VDate) <', $from_date)
        ->get()
        ->row();
      $beginning = (float)$begin_q->deb - (float)$begin_q->cred;
    } else {
      $beginning = 0.0;
    }

    // Invoiced amount within range (sum invoice totals)
    $inv_q = $this->db->select('IFNULL(SUM(total_amount),0) AS total')
      ->from('invoice')
      ->where('customer_id', $customer_id)
      ->where('DATE(date) >=', $from_date)
      ->where('DATE(date) <=', $to_date)
      ->get()
      ->row();
    $invoiced = (float)$inv_q->total;

    // Entries: invoices first
    $entries = [];
    $invoices = $this->get_customer_invoices($customer_id, $from_date, $to_date);
    foreach ($invoices as $inv) {
      $tag = '';
      if (isset($inv['is_credit'])) {
        $tag = ((int)$inv['is_credit'] === 1) ? ' (Pay Later)' : ' (Pay Now)';
      }
      $entries[] = [
        'date'       => $inv['date'],
        'description'=> sprintf('Invoice %s%s', $inv['invoice_no'], $tag),
        'debit'      => (float)$inv['total_amount'],
        'credit'     => 0.0,
        'type_sort'  => 1,
        'sort_ts'    => strtotime($inv['date']),
      ];
      // Do not add a separate payment line here to avoid double counting.
      // Payments will be listed from ledger/voucher entries below.
    }

    // Payments: derive from vouchers; group FIFO batches as one credit + breakdown lines
    $entries_pay = [];
    $sub = $this->db->select('id')->from('acc_subcode')->where('referenceNo', $customer_id)->where('subTypeId', 3)->get()->row();
    if ($sub) {
      $vouchers = $this->db->select('VNo, VDate as date, referenceNo as invoice_id, Credit, ledgerComment')
        ->from('acc_vaucher')
        ->where('Vtype','CV')
        ->where('subType', 3)
        ->where('subCode', $sub->id)
        ->where('DATE(VDate) >=', date('Y-m-d', strtotime($from_date)))
        ->where('DATE(VDate) <=', date('Y-m-d', strtotime($to_date)))
        ->order_by('VDate','asc')->order_by('id','asc')
        ->get()->result_array();

      // Map invoice ids to numbers for friendlier breakdowns
      $invIds = [];
      foreach ($vouchers as $v) { if (!empty($v['invoice_id'])) { $invIds[(int)$v['invoice_id']] = true; } }
      $invMap = [];
      if (!empty($invIds)) {
        $rows = $this->db->select('invoice_id, invoice')->from('invoice')->where_in('invoice_id', array_keys($invIds))->get()->result_array();
        foreach ($rows as $r) { $invMap[(int)$r['invoice_id']] = $r['invoice']; }
      }

      // Group by ledgerComment when it starts with 'Group Payment'
      $groups = [];
      $singles = [];
      foreach ($vouchers as $v) {
        $lc = trim((string)($v['ledgerComment'] ?? ''));
        if (stripos($lc, 'Group Payment') === 0) {
          if (!isset($groups[$lc])) { $groups[$lc] = ['date' => $v['date'], 'total' => 0.0, 'items' => []]; }
          // keep earliest date
          if (strtotime($v['date']) < strtotime($groups[$lc]['date'])) { $groups[$lc]['date'] = $v['date']; }
          $groups[$lc]['total'] += (float)$v['Credit'];
          $groups[$lc]['items'][] = $v;
        } else {
          $singles[] = $v;
        }
      }

      foreach ($groups as $label => $g) {
        $entries_pay[] = [
          'date'       => $g['date'],
          'description'=> sprintf('Payment (Group) %s', $label),
          'debit'      => 0.0,
          'credit'     => (float)$g['total'],
          'type_sort'  => 2,
          'sort_ts'    => strtotime($g['date']),
        ];
        // Breakdown lines (no amounts, just explanation)
        foreach ($g['items'] as $it) {
          $invNo = isset($invMap[(int)$it['invoice_id']]) ? $invMap[(int)$it['invoice_id']] : ('#'.(int)$it['invoice_id']);
          $entries_pay[] = [
            'date'       => $it['date'],
            'description'=> sprintf('  -> Applied to Invoice %s: %0.2f', $invNo, (float)$it['Credit']),
            'debit'      => 0.0,
            'credit'     => 0.0,
            'type_sort'  => 3,
            'sort_ts'    => strtotime($it['date']) + 0.001, // ensure after group header same day
          ];
        }
      }
      // Non-group vouchers as simple payments
      foreach ($singles as $v) {
        $entries_pay[] = [
          'date'       => $v['date'],
          'description'=> sprintf('Payment %s', $v['VNo'] ?? ''),
          'debit'      => 0.0,
          'credit'     => (float)$v['Credit'],
          'type_sort'  => 2,
          'sort_ts'    => strtotime($v['date']),
        ];
      }
    }

    // Merge payments into entries
    foreach ($entries_pay as $e) { $entries[] = $e; }


    usort($entries, function ($a, $b) {
      if ($a['sort_ts'] === $b['sort_ts']) {
        return $a['type_sort'] <=> $b['type_sort'];
      }
      return $a['sort_ts'] <=> $b['sort_ts'];
    });

    $lines = [];
    $running = $beginning;
    $lines[] = [
      'date'        => $from_date,
      'description' => 'Beginning Balance',
      'debit'       => 0.0,
      'credit'      => 0.0,
      'balance'     => $running,
    ];

    foreach ($entries as $entry) {
      $running += $entry['debit'];
      $running -= $entry['credit'];
      $lines[] = [
        'date'        => $entry['date'],
        'description' => $entry['description'],
        'debit'       => $entry['debit'],
        'credit'      => $entry['credit'],
        'balance'     => $running,
      ];
    }

    $paid_total = 0.0;
    foreach ($entries as $entry) {
      $paid_total += $entry['credit'];
    }

    $balance_due = $running;

    return [
      'summary' => [
        'beginning'   => $beginning,
        'invoiced'    => $invoiced,
        'paid'        => $paid_total,
        'balance_due' => $balance_due,
      ],
      'lines' => $lines,
    ];
  }

  // Notes CRUD
  public function get_notes($customer_id)
  {
    if (!$this->db->table_exists('customer_notes')) {
      return [];
    }
    return $this->db->select('*')->from('customer_notes')->where('customer_id', $customer_id)->order_by('created_at', 'desc')->get()->result_array();
  }
  public function add_note($customer_id, $text)
  {
    if (!$this->db->table_exists('customer_notes')) {
      return false;
    }
    return $this->db->insert('customer_notes', ['customer_id' => $customer_id, 'note_text' => $text]);
  }
  public function delete_note($id, $customer_id)
  {
    if (!$this->db->table_exists('customer_notes')) {
      return false;
    }
    return $this->db->where('id', $id)->where('customer_id', $customer_id)->delete('customer_notes');
  }

  public function get_credit_notes($customer_id)
  {
    if (!$this->ensure_customer_table('customer_credit_notes')) {
      return [];
    }
    return $this->db->select('id, note_number AS number, note_date AS date, amount, status, remarks')
      ->from('customer_credit_notes')
      ->where('customer_id', $customer_id)
      ->order_by('note_date', 'desc')
      ->order_by('id', 'desc')
      ->get()
      ->result_array();
  }

  public function add_credit_note($customer_id, array $data)
  {
    if (!$this->ensure_customer_table('customer_credit_notes')) {
      return false;
    }
    $insert = array(
      'customer_id' => $customer_id,
      'note_number' => isset($data['number']) ? trim($data['number']) : null,
      'note_date'   => !empty($data['date']) ? $data['date'] : null,
      'amount'      => isset($data['amount']) ? (float) $data['amount'] : 0,
      'status'      => isset($data['status']) ? trim($data['status']) : null,
      'remarks'     => isset($data['remarks']) ? trim($data['remarks']) : null,
      'created_at'  => date('Y-m-d H:i:s'),
      'updated_at'  => date('Y-m-d H:i:s'),
    );
    return $this->db->insert('customer_credit_notes', $insert);
  }

  public function delete_credit_note($customer_id, $note_id)
  {
    if (!$this->ensure_customer_table('customer_credit_notes')) {
      return false;
    }
    return $this->db->where('id', $note_id)->where('customer_id', $customer_id)->delete('customer_credit_notes');
  }

  public function get_estimates($customer_id)
  {
    if (!$this->ensure_customer_table('customer_estimates')) {
      return [];
    }
    return $this->db->select('id, estimate_number AS number, estimate_date AS date, amount, status, notes')
      ->from('customer_estimates')
      ->where('customer_id', $customer_id)
      ->order_by('estimate_date', 'desc')
      ->order_by('id', 'desc')
      ->get()
      ->result_array();
  }

  public function add_estimate($customer_id, array $data)
  {
    if (!$this->ensure_customer_table('customer_estimates')) {
      return false;
    }
    $insert = array(
      'customer_id'     => $customer_id,
      'estimate_number' => isset($data['number']) ? trim($data['number']) : null,
      'estimate_date'   => !empty($data['date']) ? $data['date'] : null,
      'amount'          => isset($data['amount']) ? (float) $data['amount'] : 0,
      'status'          => isset($data['status']) ? trim($data['status']) : null,
      'notes'           => isset($data['notes']) ? trim($data['notes']) : null,
      'created_at'      => date('Y-m-d H:i:s'),
      'updated_at'      => date('Y-m-d H:i:s'),
    );
    return $this->db->insert('customer_estimates', $insert);
  }

  public function delete_estimate($customer_id, $estimate_id)
  {
    if (!$this->ensure_customer_table('customer_estimates')) {
      return false;
    }
    return $this->db->where('id', $estimate_id)->where('customer_id', $customer_id)->delete('customer_estimates');
  }

  public function get_expenses($customer_id)
  {
    if (!$this->ensure_customer_table('customer_expenses')) {
      return [];
    }
    return $this->db->select('id, category, expense_date AS date, amount, vendor, notes')
      ->from('customer_expenses')
      ->where('customer_id', $customer_id)
      ->order_by('expense_date', 'desc')
      ->order_by('id', 'desc')
      ->get()
      ->result_array();
  }

  public function add_expense($customer_id, array $data)
  {
    if (!$this->ensure_customer_table('customer_expenses')) {
      return false;
    }
    $insert = array(
      'customer_id'  => $customer_id,
      'category'     => isset($data['category']) ? trim($data['category']) : null,
      'expense_date' => !empty($data['date']) ? $data['date'] : null,
      'amount'       => isset($data['amount']) ? (float) $data['amount'] : 0,
      'vendor'       => isset($data['vendor']) ? trim($data['vendor']) : null,
      'notes'        => isset($data['notes']) ? trim($data['notes']) : null,
      'created_at'   => date('Y-m-d H:i:s'),
      'updated_at'   => date('Y-m-d H:i:s'),
    );
    return $this->db->insert('customer_expenses', $insert);
  }

  public function delete_expense($customer_id, $expense_id)
  {
    if (!$this->ensure_customer_table('customer_expenses')) {
      return false;
    }
    return $this->db->where('id', $expense_id)->where('customer_id', $customer_id)->delete('customer_expenses');
  }

  // Reminders CRUD
  public function get_reminders($customer_id)
  {
    if (!$this->db->table_exists('customer_reminders')) {
      return [];
    }
    return $this->db->select('*')->from('customer_reminders')->where('customer_id', $customer_id)->order_by('remind_on', 'asc')->get()->result_array();
  }
  public function add_reminder($customer_id, $title, $remind_on)
  {
    if (!$this->db->table_exists('customer_reminders')) {
      return false;
    }
    return $this->db->insert('customer_reminders', ['customer_id' => $customer_id, 'title' => $title, 'remind_on' => $remind_on, 'status' => 'pending']);
  }
  public function delete_reminder($id, $customer_id)
  {
    if (!$this->db->table_exists('customer_reminders')) {
      return false;
    }
    return $this->db->where('id', $id)->where('customer_id', $customer_id)->delete('customer_reminders');
  }

  // Files list
  public function get_files($customer_id)
  {
    if (!$this->db->table_exists('customer_files')) {
      return [];
    }
    return $this->db->select('*')->from('customer_files')->where('customer_id', $customer_id)->order_by('uploaded_at', 'desc')->get()->result_array();
  }
  public function add_file_record($customer_id, $file_name, $file_path)
  {
    if (!$this->db->table_exists('customer_files')) {
      return false;
    }
    return $this->db->insert('customer_files', ['customer_id' => $customer_id, 'file_name' => $file_name, 'file_path' => $file_path]);
  }
  public function delete_file($id, $customer_id)
  {
    if (!$this->db->table_exists('customer_files')) {
      return false;
    }
    return $this->db->where('id', $id)->where('customer_id', $customer_id)->delete('customer_files');
  }

  //credit customer dropdown
  public function bdtask_credit_customer_dropdown()
  {
    $data =  $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('customer_information a')
      ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
      ->having('balance > 0')
      ->group_by('a.customer_id')
      ->order_by('a.customer_name', 'asc')
      ->get()
      ->result();

    $list[''] = display('select_option');
    if (!empty($data)) {
      foreach ($data as $value)
        $list[$value->customer_id] = $value->customer_name;
      return $list;
    } else {
      return false;
    }
  }


  // paid customer dropdown
  public function bdtask_paid_customer_dropdown()
  {
    $data =  $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('customer_information a')
      ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
      ->having('balance <= 0')
      ->group_by('a.customer_id')
      ->order_by('a.customer_name', 'asc')
      ->get()
      ->result();

    $list[''] = display('select_option');
    if (!empty($data)) {
      foreach ($data as $value)
        $list[$value->customer_id] = $value->customer_name;
      return $list;
    } else {
      return false;
    }
  }

  public function customer_list($offset = null, $limit = null)
  {


    return $result = $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('customer_information a')
      ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
      ->group_by('a.customer_id')
      ->order_by('a.customer_name', 'asc')
      ->limit($offset, $limit)
      ->get()
      ->result();
  }


  public function getCustomerList($postData = null)
  {

    $response = array();
    $customer_id =  $this->input->post('customer_id');
    $custom_data = $this->input->post('customfiled');
    if (!empty($custom_data)) {
      $cus_data = [''];
      foreach ($custom_data as $cusd) {
        $cus_data[] = $cusd;
      }
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
      $searchQuery = " (a.customer_name like '%" . $searchValue . "%' or a.customer_mobile like '%" . $searchValue . "%' or a.customer_email like '%" . $searchValue . "%'or a.phone like '%" . $searchValue . "%' or a.customer_address like '%" . $searchValue . "%' or a.country like '%" . $searchValue . "%' or a.state like '%" . $searchValue . "%' or a.zip like '%" . $searchValue . "%' or a.city like '%" . $searchValue . "%') ";
    }

    ## Total number of records without filtering
    $this->db->select('count(*) as allcount');
    $this->db->from('customer_information a');
    $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');

    if (!empty($customer_id)) {
      $this->db->where('a.customer_id', $customer_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.customer_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->group_by('a.customer_id');
    $totalRecords = $this->db->get()->num_rows();

    ## Total number of record with filtering
    $this->db->select('count(*) as allcount');
    $this->db->from('customer_information a');
    $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');
    if (!empty($customer_id)) {
      $this->db->where('a.customer_id', $customer_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.customer_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->group_by('a.customer_id');
    $totalRecordwithFilter = $this->db->get()->num_rows();

    ## Fetch records
    $this->db->select("a.*,b.HeadCode,cg.group_name,((select ifnull(sum(Debit),0) from acc_transaction  where subCode= `s`.`id` AND subType = 3)-(select ifnull(sum(Credit),0) from acc_transaction where subCode= `s`.`id` AND subType = 3)) as balance");
    $this->db->from('customer_information a');
    $this->db->join('customer_groups cg', 'cg.id = a.group_id', 'left');

    $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');
    $this->db->join('acc_subcode s', 'a.customer_id = s.referenceNo', 'left');
    $this->db->where('s.subTypeId', 3);

    $this->db->group_by('a.customer_id');
    if (!empty($customer_id)) {
      $this->db->where('a.customer_id', $customer_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.customer_id', $cus_data);
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

      // Details view link
      $button .= ' <a href="' . $base_url . 'customer/customer_detail/' . $record->customer_id . '" class="btn btn-info btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="left" title="Details"><i class="fa fa-user" aria-hidden="true"></i></a>';
      if ($this->permission1->method('manage_customer', 'update')->access()) {
        $button .= ' <a href="' . $base_url . 'edit_customer/' . $record->customer_id . '" class="btn btn-success btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="left" title="Update"><i class="pe-7s-note" aria-hidden="true"></i></a>';
      }
      if ($this->permission1->method('manage_customer', 'delete')->access()) {
        $button .= ' <a onclick="customerdelete(' . $record->customer_id . ')" href="javascript:void(0)"  class="btn btn-danger btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="right" title="Delete "><i class="pe-7s-trash" aria-hidden="true"></i></a>';
      }




      $data[] = array(
        'sl'               => $sl,
        'customer_name'    => '<a href="' . $base_url . 'customer/customer_detail//' . $record->customer_id . '" title="View Details">' . html_escape($record->customer_name) . '</a>',
        'address'          => $record->customer_address,
        'address2'         => $record->address2,
        'mobile'           => $record->customer_mobile,
        'phone'            => $record->phone,
        'email'            => $record->customer_email,
        'email_address'    => $record->email_address,
        'contact'          => $record->contact,
        'fax'              => $record->fax,
        'city'             => $record->email_address,
        'state'            => $record->contact,
        'customer_group'   => isset($record->group_name) ? $record->group_name : '',
        'zip'              => $record->zip,
        'country'          => $record->country,
        'balance'          => (!empty($record->balance) ? $record->balance : 0),
        'button'           => $button,

      );
      $sl++;
    }

    ## Response
    $response = array(
      "draw" => intval($draw),
      "iTotalRecords" => $totalRecords, "recordsTotal" => $totalRecords,
      "iTotalDisplayRecords" => $totalRecordwithFilter, "recordsFiltered" => $totalRecordwithFilter,
      "aaData" => $data, "data" => $data
    );

    return $response;
  }



  public function getCreditCustomerList($postData = null)
  {

    $response = array();
    $customer_id =  $this->input->post('customer_id');
    $custom_data = $this->input->post('customfiled');
    if (!empty($custom_data)) {
      $cus_data = [''];
      foreach ($custom_data as $cusd) {
        $cus_data[] = $cusd;
      }
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
      $searchQuery = " (a.customer_name like '%" . $searchValue . "%' or a.customer_mobile like '%" . $searchValue . "%' or a.customer_email like '%" . $searchValue . "%'or a.phone like '%" . $searchValue . "%' or a.customer_address like '%" . $searchValue . "%' or a.country like '%" . $searchValue . "%' or a.state like '%" . $searchValue . "%' or a.zip like '%" . $searchValue . "%' or a.city like '%" . $searchValue . "%') ";
    }

    ## Total number of records without filtering
    $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
    $this->db->from('customer_information a');
    $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');

    if (!empty($customer_id)) {
      $this->db->where('a.customer_id', $customer_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.customer_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->having('balance > 0');
    $this->db->group_by('a.customer_id');
    $totalRecords = $this->db->get()->num_rows();

    ## Total number of record with filtering
    $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
    $this->db->from('customer_information a');
    $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');
    if (!empty($customer_id)) {
      $this->db->where('a.customer_id', $customer_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.customer_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->having('balance > 0');
    $this->db->group_by('a.customer_id');
    $totalRecordwithFilter = $this->db->get()->num_rows();

    ## Fetch records
    $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
    $this->db->from('customer_information a');
    $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');
    $this->db->group_by('a.customer_id');
    if (!empty($customer_id)) {
      $this->db->where('a.customer_id', $customer_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.customer_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->having('balance > 0');
    $this->db->order_by($columnName, $columnSortOrder);
    $this->db->limit($rowperpage, $start);
    $records = $this->db->get()->result();
    $data = array();
    $sl = 1;

    foreach ($records as $record) {
      $button = '';
      $base_url = base_url();

      // Details view link
      $button .= ' <a href="' . $base_url . 'customer/customer_detail/' . $record->customer_id . '" class="btn btn-info btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="left" title="Details"><i class="fa fa-user" aria-hidden="true"></i></a>';
      if ($this->permission1->method('credit_customer', 'update')->access()) {
        $button .= ' <a href="' . $base_url . 'edit_customer/' . $record->customer_id . '" class="btn btn-success btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="left" title="Update"><i class="pe-7s-note" aria-hidden="true"></i></a>';
      }
      if ($this->permission1->method('credit_customer', 'dalete')->access()) {
        $button .= ' <a onclick="customerdelete(' . $record->customer_id . ')" href="javascript:void(0)"  class="btn btn-danger btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="right" title="Delete "><i class="pe-7s-trash" aria-hidden="true"></i></a>';
      }




      $data[] = array(
        'sl'               => $sl,
        'customer_name'    => $record->customer_name,
        'address'          => $record->customer_address,
        'address2'         => $record->address2,
        'mobile'           => $record->customer_mobile,
        'phone'            => $record->phone,
        'email'            => $record->customer_email,
        'email_address'    => $record->email_address,
        'contact'          => $record->contact,
        'fax'              => $record->fax,
        'city'             => $record->city,
        'state'            => $record->state,
        'zip'              => $record->zip,
        'country'          => $record->country,
        'balance'          => (!empty($record->balance) ? $record->balance : 0),
        'button'           => $button,

      );
      $sl++;
    }

    ## Response
    $response = array(
      "draw" => intval($draw),
      "iTotalRecords" => $totalRecords, "recordsTotal" => $totalRecords,
      "iTotalDisplayRecords" => $totalRecordwithFilter, "recordsFiltered" => $totalRecordwithFilter,
      "aaData" => $data, "data" => $data
    );

    return $response;
  }

  //paid customer list
  public function bdtask_getPaidCustomerList($postData = null)
  {

    $response = array();
    $customer_id =  $this->input->post('customer_id');
    $custom_data = $this->input->post('customfiled');
    if (!empty($custom_data)) {
      $cus_data = [''];
      foreach ($custom_data as $cusd) {
        $cus_data[] = $cusd;
      }
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
      $searchQuery = " (a.customer_name like '%" . $searchValue . "%' or a.customer_mobile like '%" . $searchValue . "%' or a.customer_email like '%" . $searchValue . "%'or a.phone like '%" . $searchValue . "%' or a.customer_address like '%" . $searchValue . "%' or a.country like '%" . $searchValue . "%' or a.state like '%" . $searchValue . "%' or a.zip like '%" . $searchValue . "%' or a.city like '%" . $searchValue . "%') ";
    }

    ## Total number of records without filtering
    $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
    $this->db->from('customer_information a');
    $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');

    if (!empty($customer_id)) {
      $this->db->where('a.customer_id', $customer_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.customer_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->having('balance <= 0');
    $this->db->group_by('a.customer_id');
    $totalRecords = $this->db->get()->num_rows();

    ## Total number of record with filtering
    $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
    $this->db->from('customer_information a');
    $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');
    if (!empty($customer_id)) {
      $this->db->where('a.customer_id', $customer_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.customer_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->having('balance <= 0');
    $this->db->group_by('a.customer_id');
    $totalRecordwithFilter = $this->db->get()->num_rows();

    ## Fetch records
    $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
    $this->db->from('customer_information a');
    $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');
    $this->db->group_by('a.customer_id');
    if (!empty($customer_id)) {
      $this->db->where('a.customer_id', $customer_id);
    }
    if (!empty($custom_data)) {
      $this->db->where_in('a.customer_id', $cus_data);
    }
    if ($searchValue != '')
      $this->db->where($searchQuery);
    $this->db->having('balance <= 0');
    $this->db->order_by($columnName, $columnSortOrder);
    $this->db->limit($rowperpage, $start);
    $this->db->group_by('a.customer_id');
    $records = $this->db->get()->result();
    $data = array();
    $sl = 1;

    foreach ($records as $record) {
      $button = '';
      $base_url = base_url();

      // Details view link
      $button .= ' <a href="' . $base_url . 'customer/customer_detail/' . $record->customer_id . '" class="btn btn-info btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="left" title="Details"><i class="fa fa-user" aria-hidden="true"></i></a>';
      if ($this->permission1->method('paid_customer', 'update')->access()) {
        $button .= ' <a href="' . $base_url . 'edit_customer/' . $record->customer_id . '" class="btn btn-success btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="left" title="Update"><i class="pe-7s-note" aria-hidden="true"></i></a>';
      }
      if ($this->permission1->method('paid_customer', 'delete')->access()) {
        $button .= ' <a onclick="customerdelete(' . $record->customer_id . ')" href="javascript:void(0)"  class="btn btn-danger btn-xs m-b-5 custom_btn" data-toggle="tooltip" data-placement="right" title="Delete "><i class="pe-7s-trash" aria-hidden="true"></i></a>';
      }




      $data[] = array(
        'sl'               => $sl,
        'customer_name'    => $record->customer_name,
        'address'          => $record->customer_address,
        'address2'         => $record->address2,
        'mobile'           => $record->customer_mobile,
        'phone'            => $record->phone,
        'email'            => $record->customer_email,
        'email_address'    => $record->email_address,
        'contact'          => $record->contact,
        'fax'              => $record->fax,
        'city'             => $record->city,
        'state'            => $record->state,
        'zip'              => $record->zip,
        'country'          => $record->country,
        'balance'          => (!empty($record->balance) ? $record->balance : 0),
        'button'           => $button,

      );
      $sl++;
    }

    ## Response
    $response = array(
      "draw" => intval($draw),
      "iTotalRecords" => $totalRecords, "recordsTotal" => $totalRecords,
      "iTotalDisplayRecords" => $totalRecordwithFilter, "recordsFiltered" => $totalRecordwithFilter,
      "aaData" => $data, "data" => $data
    );

    return $response;
  }

  public function individual_info($id)
  {
    return $result = $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('customer_information a')
      ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
      ->where('a.customer_id', $id)
      ->group_by('a.customer_id')
      ->order_by('a.customer_name', 'asc')
      ->get()
      ->result();
  }

  public function credit_customer($offset = null, $limit = null)
  {


    return $result = $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('customer_information a')
      ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
      ->having('balance > 0')
      ->group_by('a.customer_id')
      ->order_by('a.customer_name', 'asc')
      ->limit($offset, $limit)
      ->get()
      ->result();
  }


  public function count_credit_customer()
  {
    return $result = $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('customer_information a')
      ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
      ->having('balance > 0')
      ->group_by('a.customer_id')
      ->order_by('a.customer_name', 'asc')
      ->get()
      ->num_rows();
  }

  public function singledata($id = null)
  {
    return $this->db->select('*')
      ->from('customer_information')
      ->where('customer_id', $id)
      ->get()
      ->row();
  }

  public function allcustomer()
  {
    return $this->db->select('*')
      ->from('customer_information')
      ->get()
      ->result();
  }

  public function bdtask_all_credit_customer()
  {

    return $data =  $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('customer_information a')
      ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
      ->having('balance > 0')
      ->group_by('a.customer_id')
      ->order_by('a.customer_name', 'asc')
      ->get()
      ->result();
  }

  public function bdtask_all_paid_customer()
  {

    return $data =  $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode`)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode`)) as balance")
      ->from('customer_information a')
      ->join('acc_coa b', 'a.customer_id = b.customer_id', 'left')
      ->having('balance <= 0')
      ->group_by('a.customer_id')
      ->order_by('a.customer_name', 'asc')
      ->get()
      ->result();
  }

  public function update($data = array())
  {
    $updatecustomer =  $this->db->where('customer_id', $data["customer_id"])
      ->update("customer_information", $data);

    $customer_id = $data["customer_id"];
    $old_headnam = $customer_id . '-' . $this->input->post("old_name");
    $c_acc = $customer_id . '-' . $data["customer_name"];
    $customer_coa = [
      'HeadName'         => $c_acc
    ];

    $sub_acc = [
      'name'        => $data['customer_name'],
    ];

    $this->db->where('referenceNo', $customer_id)
      ->where('subTypeId', 3)
      ->update('acc_subcode', $sub_acc);

    return true;
  }

  public function delete($id = null)
  {
    $this->db->where('referenceNo', $id)
      ->where('subTypeId', 3)
      ->delete('acc_subcode');

    return $this->db->where('customer_id', $id)
      ->delete("customer_information");
  }


  public function headcode()
  {
    $query = $this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE HeadLevel='4' And HeadCode LIKE '113100%'");
    return $query->row();
  }


  public function previous_balance_add($balance, $customer_id)
  {
    $cusifo = $this->db->select('*')->from('customer_information')->where('customer_id', $customer_id)->get()->row();
    $headn = $customer_id . '-' . $cusifo->customer_name;
    $coainfo = $this->db->select('*')->from('acc_coa')->where('HeadName', $headn)->get()->row();
    $customer_headcode = $coainfo->HeadCode;
    $transaction_id = $this->generator(10);


    // Customer debit for previous balance
    $cosdr = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'PR Balance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  $customer_headcode,
      'Narration'      =>  'Customer debit For ' . $cusifo->customer_name,
      'Debit'          =>  $balance,
      'Credit'         =>  0,
      'IsPosted'       => 1,
      'CreateBy'       => $this->session->userdata('id'),
      'CreateDate'     => date('Y-m-d H:i:s'),
      'IsAppove'       => 1
    );
    $inventory = array(
      'VNo'            =>  $transaction_id,
      'Vtype'          =>  'PR Balance',
      'VDate'          =>  date("Y-m-d"),
      'COAID'          =>  1141,
      'Narration'      =>  'Inventory credit For Old sale For' . $cusifo->customer_name,
      'Debit'          =>  0,
      'Credit'         =>  $balance, //purchase price asbe
      'IsPosted'       => 1,
      'CreateBy'       => $this->session->userdata('id'),
      'CreateDate'     => date('Y-m-d H:i:s'),
      'IsAppove'       => 1
    );


    if (!empty($balance)) {
      $this->db->insert('acc_transaction', $cosdr);
      $this->db->insert('acc_transaction', $inventory);
    }
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


  public function customer_ledgerdata($per_page, $page)
  {
    $this->db->select('a.*,b.HeadName');
    $this->db->from('acc_transaction a');
    $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
    $this->db->where('b.PHeadName', 'Customer Receivable');
    $this->db->where('a.IsAppove', 1);
    $this->db->order_by('a.VDate', 'desc');
    $this->db->limit($per_page, $page);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }


  public function count_customer_ledger()
  {
    $this->db->select('a.*,b.HeadName');
    $this->db->from('acc_transaction a');
    $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
    $this->db->where('b.PHeadName', 'Customer Receivable');
    $this->db->where('a.IsAppove', 1);
    $this->db->order_by('a.VDate', 'desc');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->num_rows();
    }
    return false;
  }


  public function customer_list_ledger()
  {
    $this->db->select('*');
    $this->db->from('customer_information');
    $this->db->order_by('customer_name', 'asc');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }

  public function customer_personal_data($customer_id)
  {
    $this->db->select('*');
    $this->db->from('customer_information');
    $this->db->where('customer_id', $customer_id);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }

  public function customerledger_searchdata($customer_id, $start, $end)
  {
    $this->db->select('a.*,b.HeadName');
    $this->db->from('acc_transaction a');
    $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
    $this->db->where(array('b.customer_id' => $customer_id, 'a.VDate >=' => $start, 'a.VDate <=' => $end));
    $this->db->where('a.IsAppove', 1);
    $this->db->order_by('a.VDate', 'desc');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }

  public function customer_list_advance()
  {
    $this->db->select('*');
    $this->db->from('customer_information');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }

  public function advance_details($transaction_id, $customer_id)
  {

    $headcode = $this->db->select('HeadCode')->from('acc_coa')->where('customer_id', $customer_id)->get()->row();
    return $data  = $this->db->select('*')
      ->from('acc_transaction')
      ->where('VNo', $transaction_id)
      ->where('COAID', $headcode->HeadCode)
      ->get()
      ->result_array();
  }
}

