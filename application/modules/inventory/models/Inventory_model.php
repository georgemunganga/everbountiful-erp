<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model
{
    public function get_active_locations()
    {
        return $this->db->select('id, location_name, location_code')
            ->from('stock_locations')
            ->where('is_active', 1)
            ->order_by('location_name', 'asc')
            ->get()
            ->result_array();
    }

    public function get_available_lots()
    {
        $sub = $this->db->select('lot_id, SUM(quantity_in - quantity_out) AS qty')
            ->from('stock_movements')
            ->group_by('lot_id')
            ->get_compiled_select();

        return $this->db->select([
                'l.id AS lot_id',
                'l.lot_code',
                'l.product_id',
                'l.location_id',
                'l.base_unit_id',
                'pi.product_name',
                'sl.location_name',
                'u.unit_name',
                'COALESCE(m.qty, 0) AS available_qty',
            ])
            ->from('stock_lots l')
            ->join('(' . $sub . ') m', 'm.lot_id = l.id', 'left')
            ->join('product_information pi', 'pi.product_id = l.product_id', 'left')
            ->join('stock_locations sl', 'sl.id = l.location_id', 'left')
            ->join('units u', 'u.unit_id = l.base_unit_id', 'left')
            ->where('COALESCE(m.qty, 0) >', 0)
            ->order_by('pi.product_name', 'asc')
            ->order_by('l.lot_code', 'asc')
            ->get()
            ->result_array();
    }

    public function get_consumable_products()
    {
        return $this->db->select('product_id, product_name')
            ->from('product_information')
            ->where('status', 1)
            ->order_by('product_name', 'asc')
            ->get()
            ->result_array();
    }

    public function get_recent_transfers($limit = 10)
    {
        $rows = $this->db->select([
                'sm.reference_id',
                'sm.movement_date',
                'sm.product_id',
                'sm.lot_id',
                'sm.location_id',
                'sm.unit_id',
                'sm.quantity_out',
                'pi.product_name',
                'sl.location_name AS source_location',
                'u.unit_name',
            ])
            ->from('stock_movements sm')
            ->join('product_information pi', 'pi.product_id = sm.product_id', 'left')
            ->join('stock_locations sl', 'sl.id = sm.location_id', 'left')
            ->join('units u', 'u.unit_id = sm.unit_id', 'left')
            ->where('sm.reference_type', 'transfer')
            ->where('sm.quantity_out >', 0)
            ->order_by('sm.movement_date', 'desc')
            ->limit($limit)
            ->get()
            ->result_array();

        if (empty($rows)) {
            return array();
        }

        $references = array_column($rows, 'reference_id');
        $destinations = $this->db->select([
                'reference_id',
                'location_id',
                'quantity_in',
            ])
            ->from('stock_movements')
            ->where('reference_type', 'transfer')
            ->where_in('reference_id', $references)
            ->where('quantity_in >', 0)
            ->get()
            ->result_array();

        $destinationMap = array();
        foreach ($destinations as $dest) {
            $destinationMap[$dest['reference_id']] = $dest;
        }

        $locationIds = array();
        foreach ($destinationMap as $dest) {
            $locationIds[] = (int) $dest['location_id'];
        }
        $locationNames = array();
        if (!empty($locationIds)) {
            $locationRows = $this->db->select('id, location_name')
                ->from('stock_locations')
                ->where_in('id', $locationIds)
                ->get()
                ->result_array();
            foreach ($locationRows as $loc) {
                $locationNames[$loc['id']] = $loc['location_name'];
            }
        }

        foreach ($rows as &$row) {
            $dest = $destinationMap[$row['reference_id']] ?? null;
            $row['destination_location'] = $dest ? ($locationNames[$dest['location_id']] ?? $dest['location_id']) : null;
            $row['quantity_in'] = $dest ? (float) $dest['quantity_in'] : 0.0;
        }
        unset($row);

        return $rows;
    }

    public function get_lot($lotId)
    {
        return $this->db->select('*')
            ->from('stock_lots')
            ->where('id', (int) $lotId)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_recent_consumption($limit = 10)
    {
        return $this->db->select([
                'sm.id',
                'sm.movement_date',
                'sm.product_id',
                'sm.quantity_out',
                'sm.reason_id',
                'sm.reference_id',
                'sm.narration',
                'pi.product_name',
                'u.unit_name',
                'sl.location_name',
                'r.reason_code',
            ])
            ->from('stock_movements sm')
            ->join('product_information pi', 'pi.product_id = sm.product_id', 'left')
            ->join('units u', 'u.unit_id = sm.unit_id', 'left')
            ->join('stock_locations sl', 'sl.id = sm.location_id', 'left')
            ->join('stock_movement_reasons r', 'r.id = sm.reason_id', 'left')
            ->where('sm.reference_type', 'consumption')
            ->order_by('sm.movement_date', 'desc')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    public function get_recent_opening_balances($limit = 10)
    {
        return $this->db->select([
                'sm.id',
                'sm.movement_date',
                'sm.product_id',
                'sm.quantity_in',
                'sm.reference_id',
                'sm.narration',
                'pi.product_name',
                'u.unit_name',
                'sl.location_name',
            ])
            ->from('stock_movements sm')
            ->join('product_information pi', 'pi.product_id = sm.product_id', 'left')
            ->join('units u', 'u.unit_id = sm.unit_id', 'left')
            ->join('stock_locations sl', 'sl.id = sm.location_id', 'left')
            ->where('sm.reference_type', 'opening_balance')
            ->order_by('sm.movement_date', 'desc')
            ->order_by('sm.id', 'desc')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    public function get_recent_waste($limit = 10)
    {
        return $this->db->select([
                'sm.id',
                'sm.movement_date',
                'sm.product_id',
                'sm.quantity_out',
                'sm.reason_id',
                'sm.reference_id',
                'sm.narration',
                'pi.product_name',
                'u.unit_name',
                'sl.location_name',
                'r.reason_code',
            ])
            ->from('stock_movements sm')
            ->join('product_information pi', 'pi.product_id = sm.product_id', 'left')
            ->join('units u', 'u.unit_id = sm.unit_id', 'left')
            ->join('stock_locations sl', 'sl.id = sm.location_id', 'left')
            ->join('stock_movement_reasons r', 'r.id = sm.reason_id', 'left')
            ->where('sm.reference_type', 'waste')
            ->order_by('sm.movement_date', 'desc')
            ->limit($limit)
            ->get()
            ->result_array();
    }

    public function get_recent_notifications($limit = 10)
    {
        return $this->db->select([
                'n.id',
                'n.created_at',
                'n.notification_type',
                'n.message',
                'n.severity',
                'n.reference_type',
                'n.reference_id',
                'e.product_id',
                'e.location_id',
                'e.reason_code',
            ])
            ->from('inventory_notifications n')
            ->join('inventory_audit_events e', 'e.id = n.audit_event_id', 'left')
            ->order_by('n.created_at', 'desc')
            ->limit($limit)
            ->get()
            ->result_array();
    }
}
