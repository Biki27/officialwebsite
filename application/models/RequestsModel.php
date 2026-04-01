<?php
// Suropriyo Enterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');

class RequestsModel extends CI_Model
{

    function get_requestes_for_thisempid()
    {
        $empid = $this->session->userdata('empid');
        $res = $this->db->from('seemprequests')
            ->where('seemrq_empid = ', $empid)
            ->get()
            ->result();
        return $res;

    }

    function addRequest($formdata = [])
    {
        $start = new DateTime($formdata['startdate']);
        $end = new DateTime($formdata['enddate']);

        // .diff() gives the absolute difference. 
        // Adding +1 makes the range inclusive (e.g., 23rd to 24th = 2 days)
        $interval = $start->diff($end);
        $days = $interval->days + 1;

        $info = array(
            'seemrq_empid' => $this->session->userdata('empid'),
            'seemrq_reason' => $formdata['reason'],
            'seemrq_summary' => $formdata['summary'],
            'seemrq_reqdate' => date('Y-m-d'),
            'seemrq_fromdate' => $formdata['startdate'],
            'seemrq_todate' => $formdata['enddate'],
            'seemrq_days' => $days,
            'seemrq_status' => 'pending' // Good practice to explicitly set default
        );

        $this->db->trans_start();
        $this->db->insert('seemprequests', $info);
        $this->db->trans_complete();

        return $this->db->error();
    }

    function updateRequest($requestid = '', $empid = '', $status = 'pending')
    {

        $this->db->trans_start();
        $this->db->where('seemrq_id', $requestid)->where('seemrq_empid =', trim($empid))->update('seemprequests', ['seemrq_status' => $status]);
        if ($this->db->affected_rows() == 1) {
            $this->db->trans_complete();
        } else {
            $this->db->trans_rollback();
        }

        $issuccess = $this->db->error();
        return $issuccess;
    }

    function get_holidays_count()
    {
        $thisyear = date('Y');
        $empid = $this->session->userdata('empid');
        // from('seemprequests')

        $res = $this->db->select_sum('seemrq_days')
            ->where('seemrq_empid =', $empid)
            ->where('YEAR(seemrq_reqdate) = ' . $thisyear, null, false)
            ->where('seemrq_status =', 'approved')
            ->get('seemprequests')
            ->row();


        if (trim($res->seemrq_days) == '') {
            return 0;
        } else {
            return trim($res->seemrq_days);
        }

    }
    // for HR dashboard
    public function get_pending_requests_count()
    {
        return $this->db->where('seemrq_status', 'pending')->count_all_results('seemprequests');
    }

    public function get_pending_leaves_with_balance()
    {
        $current_year = date('Y');

        $this->db->select('r.*, d.seempd_name, 
        (SELECT SUM(seemrq_days) FROM seemprequests 
         WHERE seemrq_empid = r.seemrq_empid 
         AND seemrq_status = "approved" 
         AND YEAR(seemrq_reqdate) = ' . $current_year . ') as total_taken');

        $this->db->from('seemprequests r');
        $this->db->join('seemployee e', 'r.seemrq_empid = e.seemp_id');
        $this->db->join('seempdetails d', 'e.seemp_id = d.seempd_empid');
        $this->db->where('r.seemrq_status', 'pending');
        $this->db->order_by('r.seemrq_reqdate', 'DESC');

        return $this->db->get()->result_array();
    }

    // add code for Leave request Management for HR dashboard
    public function get_all_requests()
    {
        $this->db->select('seemprequests.*, seempdetails.seempd_name');
        $this->db->from('seemprequests');
        $this->db->join('seempdetails', 'seempdetails.seempd_empid = seemprequests.seemrq_empid', 'left');
        $this->db->order_by('seemprequests.seemrq_id', 'DESC');

        return $this->db->get()->result();
    }

    public function update_request_status($id, $status)
    {
        $this->db->where('seemrq_id', $id);
        return $this->db->update('seemprequests', [
            'seemrq_status' => $status
        ]);
    }
    public function get_requestes_for_thisempid_custom($empid)
    {
        return $this->db->from('seemprequests')
            ->where('seemrq_empid', $empid)
            ->order_by('seemrq_reqdate', 'DESC')
            ->get()
            ->result();
    }
    public function get_requestes_for_any_empid($empid)
    {
        $this->db->from('seemprequests');
        $this->db->where('seemrq_empid', $empid);
        $this->db->order_by('seemrq_reqdate', 'DESC'); // FOR latest requests first
        return $this->db->get()->result();
    }

}

?>