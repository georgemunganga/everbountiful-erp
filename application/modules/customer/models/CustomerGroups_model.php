<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CustomerGroups_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
        $this->ensure_table();
    }

    private function ensure_table()
    {
        if ($this->db->table_exists('customer_groups')) {
            return true;
        }
        $fields = array(
            'id' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true),
            'group_name' => array('type' => 'VARCHAR', 'constraint' => 150, 'null' => false),
            'description' => array('type' => 'TEXT', 'null' => true),
            'is_active' => array('type' => 'TINYINT', 'constraint' => 1, 'default' => 1),
            'created_at' => array('type' => 'DATETIME', 'null' => true),
            'updated_at' => array('type' => 'DATETIME', 'null' => true),
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('customer_groups', true);
        return $this->db->table_exists('customer_groups');
    }

    public function list_all()
    {
        $this->ensure_table();
        return $this->db->select('*')
            ->from('customer_groups')
            ->order_by('group_name', 'asc')
            ->get()
            ->result();
    }

    public function get_dropdown()
    {
        $groups = $this->list_all();
        $list = ['' => display('select_option')];
        foreach ($groups as $g) {
            $list[$g->id] = $g->group_name;
        }
        return $list;
    }

    public function get($id)
    {
        $this->ensure_table();
        return $this->db->select('*')->from('customer_groups')->where('id', (int)$id)->get()->row();
    }

    public function create($data)
    {
        $this->ensure_table();
        $insert = [
            'group_name' => trim($data['group_name'] ?? ''),
            'description' => $data['description'] ?? null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->insert('customer_groups', $insert);
    }

    public function update($id, $data)
    {
        $this->ensure_table();
        $update = [
            'group_name' => trim($data['group_name'] ?? ''),
            'description' => $data['description'] ?? null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->where('id', (int)$id)->update('customer_groups', $update);
    }

    public function delete($id)
    {
        $this->ensure_table();
        return $this->db->where('id', (int)$id)->delete('customer_groups');
    }
}
