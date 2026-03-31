<?php 
// This code is Written by :
// PAPPU BISWAS 
// Suropriyo Enterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeDetailsModel extends CI_Model{

    function get_this_employee_details(){
        $empid = $this->session->userdata('empid');
        $res = $this->db->from('seempdetails')->where('seempd_empid',$empid)->limit(1)->get()->result();
        return $res;
    }

    function get_employee_details_for_id($empid = ''){

    if(trim($empid) == ''){
        return ['code'=>1];
    }

    return $this->db
        ->from('seempdetails')
        ->where('seempd_empid',$empid)
        ->limit(1)
        ->get()
        ->row();
}

}

?>