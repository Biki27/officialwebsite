<?php 
 
defined('BASEPATH') OR exit('No direct script access allowed');

class ContactUs extends CI_Controller{

        // for mysql
        // code 0  = inserted success 
        // code 1  = something wrong
    //contact us page
    function index(){ 
       $this->load->view('headerView');
       
       $this->load->model('ContactusModel');
       $this->load->model('TestimonialsModel');
       $tsm_data = $this->TestimonialsModel->get_testimonials();

       $con_tsm_data = array(
        'tsm_d' => $tsm_data
       );

       $this->load->view('contactusView', $con_tsm_data);
       $this->load->view('footerView');

       $config = array(
                array(
                        'field' => 'name',
                        'label' => 'Name',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'email',
                        'label' => 'Email',
                        'rules' => 'required|valid_email'
                ),
                array(
                        'field' => 'subject',
                        'label' => 'Subject',
                        'rules' => 'required',
                        'errors' => array(
                                'required' => 'You must provide a %s.',
                        ),
                ),
                array(
                        'field' => 'message',
                        'label' => 'Message',
                        'rules' => 'required',
                        'errors' => array(
                                'required' => 'You must provide a %s.',
                        ),
                ),
                
        );

       $this->form_validation->set_rules($config);
       $this->form_validation->run();

       $errors =  validation_errors();
       if($errors == FALSE){
            $data = $this->input->post();
            
            if($data !=  null){
                $issuccess = $this->ContactusModel->insertConactus($data);

                if($issuccess['code'] != 0 ){
                   $this->load->view('alertView', array("alertclass" => '',"heading"=>"Something went wrong. Email not sent." ));
                }

                if($issuccess['code'] == 0 ){
                   $this->load->view('alertView', array("alertclass" => '',"heading"=>"Thank you! Your message has been sent successfully. We will contact you within 24-48 hours." ));
                }

            }

       }else{
            $this->load->view('alertView', array("alertclass" => '',"heading"=>"Please fill the form Correctly." ));
       }
    }

    function Ceo(){
        $this->load->view('headerView');
        $this->load->view('ceoView');
        $this->load->view('footerView');
    }

    function LeadDev(){
        $this->load->view('headerView');
        $this->load->view('leadDevView');
        $this->load->view('footerView');
    }
    function DevOps(){
        $this->load->view('headerView');
        $this->load->view('devopsView');
        $this->load->view('footerView');
    }
    function Technologies(){
        $this->load->view('headerView');
        $this->load->view('technologiesView');
        $this->load->view('footerView');
    }



}


?>