<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CustomerGroups_model extends CI_Model
{
    public function list_all()
    {
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
        return $this->db->select('*')->from('customer_groups')->where('id', (int)$id)->get()->row();
    }

    public function create($data)
    {
        return $this->db->insert('customer_groups', [
            'group_name' => trim($data['group_name'] ?? ''),
            'description' => $data['description'] ?? null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
        ]);
    }

    public function update($id, $data)
    {
        return $this->db->where('id', (int)$id)->update('customer_groups', [
            'group_name' => trim($data['group_name'] ?? ''),
            'description' => $data['description'] ?? null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
        ]);
    }

    public function delete($id)
    {
        return $this->db->where('id', (int)$id)->delete('customer_groups');
    }
}