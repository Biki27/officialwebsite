<?php 
 

defined('BASEPATH') OR exit('No direct script access allowed');

class Careers extends CI_Controller{

    function index(){
       $this->load->view('headerView');
		$this->load->view('careerView');
		$this->load->view('footerView');
	}
    	public function Jobs()
	{
		$this->load->model('JobsModel');

		$search_query = $this->input->post();
		$ques = '';
		if (isset($search_query['search'])) {
			$ques = $search_query['search'];
		}
		;

		$query = $this->JobsModel->get_search_in_anyfield_query($ques);
		$result = $this->JobsModel->get_jobmodel_query_result($query);

		$viewData = array('res' => $result);

		$this->load->view('jobsView', $viewData);
		$this->load->view('footerView');
	}
    



}


?>