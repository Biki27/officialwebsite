<?php
// This code is Written by :
// PAPPU BISWAS 
// Suropriyo Eterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');


class Services extends CI_Controller
{
// service page
    function index()
    {
      	$this->load->model('ServicesModel');
		$all_srv = $this->ServicesModel->get_first_six_services();
		$data = array(
			'allserv' => $all_srv
		);

		$this->load->view('headerView');
		$this->load->view('servicesView', $data);
		$this->load->view('footerView');
    }

    function Searchservice()
    {
        $this->load->model('ServicesModel');
        $query = $this->input->post();

        if (isset($query['ques'])) {
            $all_srv = $this->ServicesModel->get_filtered_service($query['ques']);
            $data = array(
                'allserv' => $all_srv
            );

            $this->load->view('headerView');
            $this->load->view('searchServiceView', $data);
            $this->load->view('footerView');
        } else {

            $all_srv = $this->ServicesModel->get_all_services();
            $data = array(
                'allserv' => $all_srv
            );

            $this->load->view('headerView');
            $this->load->view('searchServiceView', $data);
            $this->load->view('footerView');
        }

    }

    // function ServiceDescription()
    // {
    //     $query = $this->input->post();

    //     if (isset($query['serv_id'])) {
    //         $this->load->model('ServicesModel');

    //         $list  = $this -> ServicesModel -> get_service_by_id($query['serv_id']);

    //         $data = array (
    //             'serv' => $list
    //         );

    //         $this->load->view('headerView');
    //         $this->load->view('serviceDescriptionView', $data);
    //         $this->load->view('footerView');
    //     }else{
    //         redirect('Services');
    //     }

    // }

    // function Technologies(){
    //     $this->load->view('headerView');
    //     $this->load->view('technologiesView');
    //     $this->load->view('footerView');
    // }
    // Services.php

public function ServiceDescription($serv_id = NULL)
{
    // If ID isn't in the URL, check if it was sent via POST (for backward compatibility)
    if ($serv_id === NULL) {
        $serv_id = $this->input->post('serv_id');
    }
 
    if ($serv_id) {
        $this->load->model('ServicesModel');
        $list = $this->ServicesModel->get_service_by_id($serv_id);
 
        if ($list) {
            $data = array('serv' => $list);
            $this->load->view('headerView');
            $this->load->view('serviceDescriptionView', $data);
            $this->load->view('footerView');
        } else {
            redirect('Services');
        }
    } else {
        redirect('Services');
    }
}
}

?>