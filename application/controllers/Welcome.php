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
}
