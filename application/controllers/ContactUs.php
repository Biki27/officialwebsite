<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class ContactUs extends CI_Controller {

    public function index() { 
        $this->load->view('headerView');
        
        $this->load->model('ContactusModel');
        $this->load->model('TestimonialsModel');
        $tsm_data = $this->TestimonialsModel->get_testimonials();

        $con_tsm_data = array(
            'tsm_d' => $tsm_data
        );

        $this->load->view('contactusView', $con_tsm_data);
        $this->load->view('footerView');
    }

    /**
     * AJAX Submission Handler
     * Improved for faster response and SweetAlert2 compatibility
     */
    public function submit() {
        // Ensure this is an AJAX request to prevent direct URL access
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // 1. Set Validation Rules
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('subject', 'Subject', 'required|trim');
        $this->form_validation->set_rules('message', 'Message', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            // 2. Return Validation Errors as JSON
            echo json_encode([
                'status' => 'error',
                'message' => validation_errors('<li>', '</li>')
            ]);
        } else {
            // 3. Process the data through the Model
            $this->load->model('ContactusModel');
            $data = $this->input->post();
            
            $issuccess = $this->ContactusModel->insertConactus($data);

            if ($issuccess['code'] == 0) {
                // 4. Return Success Response
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Thank you! Your message has been sent successfully. We will contact you shortly.'
                ]);
            } else {
                // 5. Return Database Error Response
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Something went wrong on our server. Please try again later.'
                ]);
            }
        }
    }

    // Keep your existing profile methods
    public function Ceo() { $this->load->view('headerView'); $this->load->view('ceoView'); $this->load->view('footerView'); }
    public function LeadDev() { $this->load->view('headerView'); $this->load->view('leadDevView'); $this->load->view('footerView'); }
    public function DevOps() { $this->load->view('headerView'); $this->load->view('devopsView'); $this->load->view('footerView'); }
}
?>