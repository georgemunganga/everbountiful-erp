<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Customergroups_model extends CI_Model
{
    private $table = 'customer_groups';

    public function __construct()
    {
        parent::__construct();
    }

    // Return list of all groups (as array of objects)
    public function list_all()
    {
        return $this->db->order_by('group_name', 'asc')->get($this->table)->result();
    }

    // Get single group by ID (as object)
    public function get($id)
    {
        return $this->db->get_where($this->table, ['id' => (int)$id])->row();
    }

    // Create a new group
    public function create(array $data)
    {
        $payload = $this->filter_fields($data);
        return $this->db->insert($this->table, $payload);
    }

    // Update a group by ID
    public function update($id, array $data)
    {
        $payload = $this->filter_fields($data);
        $this->db->where('id', (int)$id);
        return $this->db->update($this->table, $payload);
    }

    // Delete a group by ID
    public function delete($id)
    {
        return $this->db->where('id', (int)$id)->delete($this->table);
    }

    // Return dropdown array id => name (optionally only active)
    public function get_dropdown($active_only = true)
    {
        if ($active_only) {
            $this->db->where('is_active', 1);
        }
        $rows = $this->db->order_by('group_name', 'asc')->get($this->table)->result();
        $out = [];
        foreach ($rows as $r) {
            $out[(int)$r->id] = (string)$r->group_name;
        }
        return $out;
    }

    // Whitelist allowed columns for create/update
    private function filter_fields(array $in)
    {
        $allowed = ['group_name', 'description', 'is_active'];
        $out = [];
        foreach ($allowed as $k) {
            if (array_key_exists($k, $in)) {
                $out[$k] = $in[$k];
            }
        }
        return $out;
    }
}

// Provide a case-variation alias in the same file (Windows FS is case-insensitive)
if (!class_exists('CustomerGroups_model', false)) {
    class CustomerGroups_model extends Customergroups_model {}
}
