<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_model extends CI_Model
{
    public function get_leaves($limit = 25, $offset = 0)
    {
        return $this->db->select('l.*, e.first_name, e.last_name, d.designation')
            ->from('hrm_employee_leaves l')
            ->join('employee_history e', 'e.id = l.employee_id', 'left')
            ->join('designation d', 'd.id = e.designation', 'left')
            ->order_by('l.start_date', 'desc')
            ->order_by('l.id', 'desc')
            ->limit((int) $limit, (int) $offset)
            ->get()
            ->result_array();
    }

    public function count_leaves()
    {
        return $this->db->from('hrm_employee_leaves')->count_all_results();
    }

    public function get_leave($id)
    {
        return $this->db->select('*')
            ->from('hrm_employee_leaves')
            ->where('id', (int) $id)
            ->get()
            ->row_array();
    }

    public function create_leave(array $data)
    {
        $data['leave_days'] = $this->calculate_days($data['start_date'], $data['end_date']);
        return $this->db->insert('hrm_employee_leaves', $data);
    }

    public function update_leave($id, array $data)
    {
        $data['leave_days'] = $this->calculate_days($data['start_date'], $data['end_date']);
        return $this->db->where('id', (int) $id)
            ->update('hrm_employee_leaves', $data);
    }

    public function delete_leave($id)
    {
        return $this->db->where('id', (int) $id)
            ->delete('hrm_employee_leaves');
    }

    public function employee_dropdown()
    {
        $employees = $this->db->select('id, first_name, last_name')
            ->from('employee_history')
            ->order_by('first_name', 'asc')
            ->order_by('last_name', 'asc')
            ->get()
            ->result();

        $list = array('' => display('select_one') ?: 'Select one...');
        foreach ($employees as $employee) {
            $list[$employee->id] = trim($employee->first_name . ' ' . $employee->last_name);
        }

        return $list;
    }

    public function calculate_days($startDate, $endDate)
    {
        try {
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
        } catch (Exception $exception) {
            return 0;
        }

        if ($end < $start) {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }

        return (float) ($start->diff($end)->days + 1);
    }

    public function get_leave_days_for_period($employeeId, $monthString = '', $fallbackDate = '')
    {
        $employeeId = (int) $employeeId;
        if ($employeeId <= 0) {
            return 0.0;
        }

        $period = $this->resolve_period($monthString, $fallbackDate);
        if (!$period) {
            return 0.0;
        }

        list($periodStart, $periodEnd) = $period;

        $rows = $this->db->select('*')
            ->from('hrm_employee_leaves')
            ->where('employee_id', $employeeId)
            ->where('status', 'approved')
            ->where('start_date <=', $periodEnd->format('Y-m-d'))
            ->where('end_date >=', $periodStart->format('Y-m-d'))
            ->get()
            ->result();

        $total = 0.0;
        foreach ($rows as $leave) {
            try {
                $leaveStart = new DateTime($leave->start_date);
                $leaveEnd = new DateTime($leave->end_date);
            } catch (Exception $exception) {
                continue;
            }

            if ($leaveEnd < $leaveStart) {
                $tmp = $leaveStart;
                $leaveStart = $leaveEnd;
                $leaveEnd = $tmp;
            }

            if ($leaveEnd < $periodStart || $leaveStart > $periodEnd) {
                continue;
            }

            $overlapStart = $leaveStart > $periodStart ? clone $leaveStart : clone $periodStart;
            $overlapEnd = $leaveEnd < $periodEnd ? clone $leaveEnd : clone $periodEnd;
            $overlapDays = $overlapStart->diff($overlapEnd)->days + 1;

            $recorded = isset($leave->leave_days) ? (float) $leave->leave_days : (float) $overlapDays;
            if ($recorded <= 0) {
                $recorded = (float) $overlapDays;
            }

            if ($leaveStart < $periodStart || $leaveEnd > $periodEnd) {
                $total += min($recorded, (float) $overlapDays);
            } else {
                $total += (float) $recorded;
            }
        }

        return round($total, 2);
    }

    private function resolve_period($monthString, $fallbackDate = '')
    {
        $candidates = array_filter(array(
            $monthString,
            $fallbackDate,
            date('Y-m-01'),
        ));

        $formats = array('F Y', 'F-Y', 'Y-m', 'Y-m-d');

        foreach ($candidates as $value) {
            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            foreach ($formats as $format) {
                $dt = DateTime::createFromFormat($format, $value);
                if ($dt !== false) {
                    $start = DateTime::createFromFormat('Y-m-d', $dt->format('Y-m-01'));
                    $end = DateTime::createFromFormat('Y-m-d', $dt->format('Y-m-t'));
                    if ($start && $end) {
                        return array($start, $end);
                    }
                }
            }
        }

        return null;
    }
}
