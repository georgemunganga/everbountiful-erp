<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Livestock_model extends CI_Model
{
    /* ------------------------------- Helpers ----------------------------- */

    private function period_selects($alias, $interval)
    {
        switch ($interval) {
            case 'weekly':
                $label = "CONCAT(DATE_FORMAT({$alias}.created_at, '%x'), '-W', LPAD(DATE_FORMAT({$alias}.created_at, '%v'), 2, '0'))";
                $start = "DATE_SUB(DATE({$alias}.created_at), INTERVAL ((DAYOFWEEK({$alias}.created_at) + 5) % 7) DAY)";
                break;
            case 'monthly':
                $label = "DATE_FORMAT({$alias}.created_at, '%Y-%m')";
                $start = "DATE_FORMAT({$alias}.created_at, '%Y-%m-01')";
                break;
            default:
                $label = "DATE_FORMAT({$alias}.created_at, '%Y-%m-%d')";
                $start = "DATE({$alias}.created_at)";
                break;
        }

        return array($label, $start);
    }

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

    /* ------------------------------ Vaccines --------------------------- */

    public function get_vaccines()
    {
        return $this->db->select('v.*, u.unit_name')
            ->from('vaccines v')
            ->join('units u', 'u.unit_id = v.unit_type_id', 'left')
            ->order_by('v.created_at', 'desc')
            ->get()
            ->result_array();
    }

    public function get_vaccines_dropdown()
    {
        return $this->db->select('id, name')
            ->from('vaccines')
            ->order_by('name', 'asc')
            ->get()
            ->result_array();
    }

    public function get_vaccine($id)
    {
        return $this->db->select('*')
            ->from('vaccines')
            ->where('id', (int) $id)
            ->get()
            ->row_array();
    }

    public function create_vaccine(array $data)
    {
        return $this->insert_record('vaccines', $data);
    }

    public function update_vaccine($id, array $data)
    {
        return $this->update_record('vaccines', 'id', $id, $data);
    }

    public function delete_vaccine($id)
    {
        return $this->delete_record('vaccines', 'id', $id);
    }

    /* --------------------------- Vaccine Usages ------------------------ */

    public function get_vaccine_usages()
    {
        return $this->db->select('vu.*, v.name AS vaccine_name, s.name AS shed_name')
            ->from('vaccine_usages vu')
            ->join('vaccines v', 'v.id = vu.vaccine_id', 'left')
            ->join('sheds s', 's.id = vu.shed_id', 'left')
            ->order_by('vu.usage_date', 'desc')
            ->order_by('vu.created_at', 'desc')
            ->get()
            ->result_array();
    }

    public function get_vaccine_usage($id)
    {
        return $this->db->select('*')
            ->from('vaccine_usages')
            ->where('id', (int) $id)
            ->get()
            ->row_array();
    }

    public function create_vaccine_usage(array $data)
    {
        return $this->insert_record('vaccine_usages', $data);
    }

    public function update_vaccine_usage($id, array $data)
    {
        return $this->update_record('vaccine_usages', 'id', $id, $data);
    }

    public function delete_vaccine_usage($id)
    {
        return $this->delete_record('vaccine_usages', 'id', $id);
    }

    /* --------------------------- Farm Reporting ------------------------ */

    public function get_farm_report_summary($start_date, $end_date)
    {
        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date   = date('Y-m-d', strtotime($end_date));

        $chicken_total = $this->db->select('IFNULL(SUM(l.livestock_total_qty), 0) AS total_qty', false)
            ->from('livestocks l')
            ->join('livestock_groups g', 'g.id = l.livestock_group_id', 'left')
            ->where('DATE(l.created_at) >=', $start_date)
            ->where('DATE(l.created_at) <=', $end_date)
            ->where('LOWER(g.name) =', 'chicken')
            ->get()
            ->row();

        $production_totals = $this->db->select('
                IFNULL(SUM(p.produced_total_qty), 0) AS produced_total,
                IFNULL(SUM(p.produced_mortality_qty), 0) AS mortality_total,
                IFNULL(SUM(p.produced_damaged_qty), 0) AS damaged_total,
                IFNULL(SUM(p.produced_extras_qty), 0) AS extras_total
            ', false)
            ->from('productions p')
            ->where('DATE(p.created_at) >=', $start_date)
            ->where('DATE(p.created_at) <=', $end_date)
            ->get()
            ->row();

        return array(
            'chicken_total'        => $chicken_total ? (float) $chicken_total->total_qty : 0.0,
            'production_total'     => $production_totals ? (float) $production_totals->produced_total : 0.0,
            'production_mortality' => $production_totals ? (float) $production_totals->mortality_total : 0.0,
            'production_damaged'   => $production_totals ? (float) $production_totals->damaged_total : 0.0,
            'production_extras'    => $production_totals ? (float) $production_totals->extras_total : 0.0,
        );
    }

    public function get_farm_report_timeseries($start_date, $end_date, $interval = 'daily')
    {
        $allowed = array('daily', 'weekly', 'monthly');
        if (!in_array($interval, $allowed, true)) {
            $interval = 'daily';
        }

        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date   = date('Y-m-d', strtotime($end_date));

        list($livestockLabel, $livestockStart) = $this->period_selects('l', $interval);
        $livestockRows = $this->db->select("
                {$livestockLabel} AS period_label,
                {$livestockStart} AS period_start,
                IFNULL(SUM(l.livestock_total_qty), 0) AS total_qty
            ", false)
            ->from('livestocks l')
            ->join('livestock_groups g', 'g.id = l.livestock_group_id', 'left')
            ->where('DATE(l.created_at) >=', $start_date)
            ->where('DATE(l.created_at) <=', $end_date)
            ->where('LOWER(g.name) =', 'chicken')
            ->group_by(array('period_label', 'period_start'))
            ->order_by('period_start', 'asc')
            ->get()
            ->result_array();

        list($productionLabel, $productionStart) = $this->period_selects('p', $interval);
        $productionRows = $this->db->select("
                {$productionLabel} AS period_label,
                {$productionStart} AS period_start,
                IFNULL(SUM(p.produced_total_qty), 0) AS produced_total,
                IFNULL(SUM(p.produced_mortality_qty), 0) AS mortality_total,
                IFNULL(SUM(p.produced_damaged_qty), 0) AS damaged_total,
                IFNULL(SUM(p.produced_extras_qty), 0) AS extras_total
            ", false)
            ->from('productions p')
            ->where('DATE(p.created_at) >=', $start_date)
            ->where('DATE(p.created_at) <=', $end_date)
            ->group_by(array('period_label', 'period_start'))
            ->order_by('period_start', 'asc')
            ->get()
            ->result_array();

        $periods = array();

        foreach ($livestockRows as $row) {
            $key = $row['period_label'];
            if (!isset($periods[$key])) {
                $periods[$key] = array(
                    'period_label'        => $row['period_label'],
                    'period_start'        => $row['period_start'],
                    'chicken_total'       => 0.0,
                    'production_total'    => 0.0,
                    'production_mortality'=> 0.0,
                    'production_damaged'  => 0.0,
                    'production_extras'   => 0.0,
                );
            }
            $periods[$key]['chicken_total'] = (float) $row['total_qty'];
        }

        foreach ($productionRows as $row) {
            $key = $row['period_label'];
            if (!isset($periods[$key])) {
                $periods[$key] = array(
                    'period_label'        => $row['period_label'],
                    'period_start'        => $row['period_start'],
                    'chicken_total'       => 0.0,
                    'production_total'    => 0.0,
                    'production_mortality'=> 0.0,
                    'production_damaged'  => 0.0,
                    'production_extras'   => 0.0,
                );
            }
            $periods[$key]['production_total']     = (float) $row['produced_total'];
            $periods[$key]['production_mortality'] = (float) $row['mortality_total'];
            $periods[$key]['production_damaged']   = (float) $row['damaged_total'];
            $periods[$key]['production_extras']    = (float) $row['extras_total'];
        }

        if (empty($periods)) {
            return array();
        }

        usort($periods, function ($a, $b) {
            return strcmp($a['period_start'], $b['period_start']);
        });

        return $periods;
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
