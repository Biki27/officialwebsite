<?php 
// Suropriyo Eterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');


// don't need this class 

class Ping extends CI_Controller{

    function index(){
        $postdata = $this->input->post();
        // if(isset($postdata['sessionid']) && isset($postdata['empid'])){
        //     if ($this->session->has_userdata('sessionid') && $this->session->has_userdata('empid')) {
                
        //         $uid = $this->session->userdata('sessionid');
        //         $empid = $this->session->userdata('empid');

        //         if(trim($postdata['sessionid']) == trim($uid) &&
        //            trim($postdata['empid']) == trim($uid) 
        //         ){}

        //     }
        // }else{

        // }
    }


}

?>