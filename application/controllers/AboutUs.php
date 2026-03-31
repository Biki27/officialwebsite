<?php 
 

defined('BASEPATH') OR exit('No direct script access allowed');

class AboutUs extends CI_Controller{

    function index(){
        $this->load->model('ProductsModel');

		$products = $this->ProductsModel->get_all_products();
		$data = array(
			'products' => $products
		);

		$this->load->view('headerView');
		$this->load->view('aboutusView', $data);
		$this->load->view('footerView');
    }
    



}


?>