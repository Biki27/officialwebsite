<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AttendanceModel extends CI_Model
{
    // Common helper to add the JOIN to all queries
    private function _get_attendance_with_names()
    {
        $this->db->select('log.*, emp.seempd_name');
        $this->db->from('seemployeeloginlog as log');
        $this->db->join('seempdetails as emp', 'log.seemp_logempid = emp.seempd_empid', 'left');
    }
    function get_attendance_of_all_employee()
    {
        $this->_get_attendance_with_names();
        $this->db->order_by('log.seemp_logdate', 'DESC'); // Latest date first
        $this->db->order_by('log.seemp_logintime', 'DESC'); // Latest login time first
        return $this->db->get()->result();
    }

    function find_empid_with_daterange($empid, $start, $end)
    {
        $this->_get_attendance_with_names();

        if (!empty($empid)) {
            $this->db->group_start();
            $this->db->like('log.seemp_logempid', $empid);
            $this->db->or_like('emp.seempd_name', $empid);
            $this->db->group_end();
        }
        if (!empty($start)) {
            $this->db->where('log.seemp_logdate >=', $start);
        }
        if (!empty($end)) {
            $this->db->where('log.seemp_logdate <=', $end);
        }

        $this->db->order_by('log.seemp_logdate', 'DESC');
        $this->db->order_by('log.seemp_logintime', 'DESC');

        return $this->db->get()->result();
    }

    function find_attendance_for_employee_id($empid = '')
    {
        if (trim($empid) == '')
            return ['code' => 1];
        $this->_get_attendance_with_names();
        $this->db->where('log.seemp_logempid', $empid);
        return $this->db->get()->result();
    }

    function find_from_startdate($start)
    {
        $this->_get_attendance_with_names();
        $this->db->where('log.seemp_logdate >=', $start);
        return $this->db->get()->result();
    }

    function find_until_enddate($end)
    {
        $this->_get_attendance_with_names();
        $this->db->where('log.seemp_logdate <=', $end);
        return $this->db->get()->result();
    }

    function find_by_daterange($start, $end)
    {
        $this->_get_attendance_with_names();
        $this->db->where('log.seemp_logdate >=', $start);
        $this->db->where('log.seemp_logdate <=', $end);
        return $this->db->get()->result();
    }

    function get_today_login_logout($empid)
    {
        $today = date('Y-m-d');
        return $this->db
            ->where('seemp_logempid', $empid)
            ->where('seemp_logdate', $today)
            ->order_by('seemp_logid', 'DESC') // Added: Always get the latest session for the top card
            ->get('seemployeeloginlog')
            ->row();
    }
    // for HR dashboard
    public function get_present_today_count()
    {
        return $this->db->where('seemp_logdate', date('Y-m-d'))->count_all_results('seemployeeloginlog');
    }
    // for HR dashboard
    public function get_today_attendance_list()
    {
        $today = date('Y-m-d');
        $this->db->select('l.*, d.seempd_name, d.seempd_designation');
        $this->db->from('seemployeeloginlog l');
        $this->db->join('seempdetails d', 'l.seemp_logempid = d.seempd_empid', 'left');
        $this->db->where('l.seemp_logdate', $today);
        $this->db->order_by('l.seemp_logintime', 'ASC');

        return $this->db->get()->result();
    }
}
