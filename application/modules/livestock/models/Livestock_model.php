<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Livestock_model extends CI_Model
{
    /* ------------------------------- Helpers ----------------------------- */

    private function insert_record($table, array $data)
    {
        $timestamp            = date('Y-m-d H:i:s');
        $data['created_at']   = $timestamp;
        $data['updated_at']   = $timestamp;
        $this->db->insert($table, $data);
        if ($this->db->error()['code']) {
            log_message('error', sprintf('Livestock_model insert error on %s: %s', $table, json_encode($this->db->error())));
            return false;
        }
        return true;
    }

    private function update_record($table, $id_field, $id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where($id_field, $id)->update($table, $data);
        if ($this->db->error()['code']) {
            log_message('error', sprintf('Livestock_model update error on %s: %s', $table, json_encode($this->db->error())));
            return false;
        }
        return $this->db->affected_rows() >= 0;
    }

    private function delete_record($table, $id_field, $id)
    {
        $this->db->where($id_field, $id)->delete($table);
        $error = $this->db->error();
        if (!empty($error['code'])) {
            log_message('error', sprintf('Livestock_model delete error on %s: %s', $table, json_encode($error)));
            return false;
        }
        return $this->db->affected_rows() > 0;
    }

    /* -------------------------------- Sheds ------------------------------ */

    public function get_sheds()
    {
        return $this->db->select('*')
            ->from('sheds')
            ->order_by('name', 'asc')
            ->get()
            ->result_array();
    }

    public function get_shed($id)
    {
        return $this->db->select('*')
            ->from('sheds')
            ->where('id', (int) $id)
            ->get()
            ->row_array();
    }

    public function create_shed(array $data)
    {
        return $this->insert_record('sheds', $data);
    }

    public function update_shed($id, array $data)
    {
        return $this->update_record('sheds', 'id', $id, $data);
    }

    public function delete_shed($id)
    {
        return $this->delete_record('sheds', 'id', $id);
    }

    /* ------------------------- Livestock Groups ------------------------- */

    public function get_livestock_groups()
    {
        return $this->db->select('*')
            ->from('livestock_groups')
            ->order_by('name', 'asc')
            ->get()
            ->result_array();
    }

    public function get_livestock_group($id)
    {
        return $this->db->select('*')
            ->from('livestock_groups')
            ->where('id', (int) $id)
            ->get()
            ->row_array();
    }

    public function create_livestock_group(array $data)
    {
        return $this->insert_record('livestock_groups', $data);
    }

    public function update_livestock_group($id, array $data)
    {
        return $this->update_record('livestock_groups', 'id', $id, $data);
    }

    public function delete_livestock_group($id)
    {
        return $this->delete_record('livestock_groups', 'id', $id);
    }

    /* ------------------------------ Productions ------------------------- */

    public function get_productions()
    {
        return $this->db->select('p.*, s.name AS shed_name, u.unit_name')
            ->from('productions p')
            ->join('sheds s', 's.id = p.shed_id', 'left')
            ->join('units u', 'u.unit_id = p.unit_type_id', 'left')
            ->order_by('p.created_at', 'desc')
            ->get()
            ->result_array();
    }

    public function get_production($id)
    {
        return $this->db->select('*')
            ->from('productions')
            ->where('id', (int) $id)
            ->get()
            ->row_array();
    }

    public function create_production(array $data)
    {
        return $this->insert_record('productions', $data);
    }

    public function update_production($id, array $data)
    {
        return $this->update_record('productions', 'id', $id, $data);
    }

    public function delete_production($id)
    {
        return $this->delete_record('productions', 'id', $id);
    }

    /* ------------------------------ Livestocks -------------------------- */

    public function get_livestocks()
    {
        return $this->db->select('l.*, s.name AS shed_name, g.name AS group_name, u.unit_name')
            ->from('livestocks l')
            ->join('sheds s', 's.id = l.shed_id', 'left')
            ->join('livestock_groups g', 'g.id = l.livestock_group_id', 'left')
            ->join('units u', 'u.unit_id = l.unit_type_id', 'left')
            ->order_by('l.created_at', 'desc')
            ->get()
            ->result_array();
    }

    public function get_livestock($id)
    {
        return $this->db->select('*')
            ->from('livestocks')
            ->where('id', (int) $id)
            ->get()
            ->row_array();
    }

    public function create_livestock(array $data)
    {
        return $this->insert_record('livestocks', $data);
    }

    public function update_livestock($id, array $data)
    {
        return $this->update_record('livestocks', 'id', $id, $data);
    }

    public function delete_livestock($id)
    {
        return $this->delete_record('livestocks', 'id', $id);
    }

    /* -------------------------------- Feeds ----------------------------- */

    public function get_feeds()
    {
        return $this->db->select('f.*, u.unit_name')
            ->from('feeds f')
            ->join('units u', 'u.unit_id = f.purchase_unit_id', 'left')
            ->order_by('f.created_at', 'desc')
            ->get()
            ->result_array();
    }

    public function get_feeds_dropdown()
    {
        return $this->db->select('id, name')
            ->from('feeds')
            ->order_by('name', 'asc')
            ->get()
            ->result_array();
    }

    public function get_feed($id)
    {
        return $this->db->select('*')
            ->from('feeds')
            ->where('id', (int) $id)
            ->get()
            ->row_array();
    }

    public function create_feed(array $data)
    {
        return $this->insert_record('feeds', $data);
    }

    public function update_feed($id, array $data)
    {
        return $this->update_record('feeds', 'id', $id, $data);
    }

    public function delete_feed($id)
    {
        return $this->delete_record('feeds', 'id', $id);
    }

    /* --------------------------- Feed Usages ---------------------------- */

    public function get_feed_usages()
    {
        return $this->db->select('fu.*, f.name AS feed_name, s.name AS shed_name')
            ->from('feed_usages fu')
            ->join('feeds f', 'f.id = fu.feed_id', 'left')
            ->join('sheds s', 's.id = fu.shed_id', 'left')
            ->order_by('fu.created_at', 'desc')
            ->get()
            ->result_array();
    }

    public function get_feed_usage($id)
    {
        return $this->db->select('*')
            ->from('feed_usages')
            ->where('id', (int) $id)
            ->get()
            ->row_array();
    }

    public function create_feed_usage(array $data)
    {
        return $this->insert_record('feed_usages', $data);
    }

    public function update_feed_usage($id, array $data)
    {
        return $this->update_record('feed_usages', 'id', $id, $data);
    }

    public function delete_feed_usage($id)
    {
        return $this->delete_record('feed_usages', 'id', $id);
    }

    /* ------------------------------ Utilities --------------------------- */

    public function get_units()
    {
        return $this->db->select('*')
            ->from('units')
            ->order_by('unit_name', 'asc')
            ->get()
            ->result_array();
    }
}
