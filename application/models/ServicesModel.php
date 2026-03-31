<?php
// This code is Written by :
// PAPPU BISWAS 
// Suropriyo Eterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');

class ServicesModel extends CI_Model{
    function get_all_services(){
        $query = $this->db->query('SELECT * FROM seservices');
        return $query->result(); 
    }

    function get_first_six_services(){
        $query = $this->db->query('SELECT * FROM seservices LIMIT 6');
        return $query->result(); 
    }

    function get_filtered_service($querys = ''){
        if(trim($querys) == ''){
            return $this->get_all_services();
        }
        
        $this->db->from('seservices');
        $this->db->or_like('sesrv_id',$querys);
        $this->db->or_like('sesrv_name',$querys);
        $this->db->or_like('sesrv_type',$querys);
        $this->db->or_like('sesrv_img',$querys);
        $this->db->or_like('sesrv_lines',$querys);

        $query = $this->db->get();
        $res  = $query->result();
        return $res;
    }

    function get_service_by_id($servid = ''){
        if(empty($servid)){
            return [];
        }else{
            $query = $this->db->from('seservices')
                ->where('sesrv_id',$servid)
                ->limit(1)
                ->get();

            $res = $query->result();
            return $res;
        }
    }

}

?>

