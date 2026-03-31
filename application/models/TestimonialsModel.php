<?php 
// This code is Written by :
// PAPPU BISWAS 
// Suropriyo Eterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');

Class TestimonialsModel extends CI_Model{

    function get_testimonials(){
        $query = $this->db->query("SELECT * FROM setestimonials");
        return $query->result();
    }


}


?>