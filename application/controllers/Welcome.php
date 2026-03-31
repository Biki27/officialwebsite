<?php

// Suropriyo Eterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller
{

	 
	public function index()
	{
		$this->load->view('headerView');
		$this->load->view('homepageView');
		$this->load->view('footerView');
	}

	public function Home()
	{
		$this->load->view('headerView');
		$this->load->view('homepageView');
		$this->load->view('footerView');
	}

	 
	// public function Services()
	// {

	// 	$this->load->model('ServicesModel');
	// 	$all_srv = $this->ServicesModel->get_first_six_services();
	// 	$data = array(
	// 		'allserv' => $all_srv
	// 	);

	// 	$this->load->view('headerView');
	// 	$this->load->view('servicesView', $data);
	// 	$this->load->view('footerView');

	// }
/*
	public function Careers()
	{
		$this->load->view('headerView');
		$this->load->view('careerView');
		$this->load->view('footerView');
	}
	*/

	// public function ContactUs()
	// {
	// 	$this->load->view('headerView');

	// 	$this->load->model('TestimonialsModel');
	// 	$tsm_data = $this->TestimonialsModel->get_testimonials();

	// 	$con_tsm_data = array(
	// 		'tsm_d' => $tsm_data
	// 	);

	// 	$this->load->view('contactusView', $con_tsm_data);
	// 	$this->load->view('footerView');
	// }

 

}
