<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Candidate extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Prevent page caching so the "Back" button doesn't serve stale data or create loops
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");

        $this->load->model('CandidateModel');
        $this->load->model('jobApplicationModel');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(array('url', 'form'));

        $allowed_methods = array('login', 'register', 'google_login', 'google_callback');
        $current_method = $this->router->fetch_method();

        if (!in_array($current_method, $allowed_methods)) {
            if (!$this->session->userdata('candidate_logged_in')) {
                $this->session->set_flashdata('error', 'Please log in to access your dashboard.');
                redirect('Candidate/login'); 
            }
        }
    }

    public function register()
    {
        if ($this->session->userdata('candidate_logged_in')) {
            redirect('Candidate/dashboard');
        }
        $data['hide_navbar'] = true;
        
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[secandidates.email]', array(
            'is_unique' => 'This email is already registered. Please log in.'
        ));
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = "Create an Account | Suropriyo Enterprise";
            $data['content'] = $this->load->view('candidate/candidateRegisterView', '', TRUE);
            $this->load->view('candidate/layout', $data);
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $candidate_id = $this->CandidateModel->register_candidate($email, $password);

            if ($candidate_id) {
                $this->set_candidate_session($candidate_id, $email);
                $this->session->set_flashdata('success', 'Account created successfully! You can now apply for jobs.');
                $this->handle_redirect();
            } else {
                $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
                redirect('Candidate/register');
            }
        }
    }

    public function login()
    {
        if ($this->session->userdata('candidate_logged_in')) {
            redirect('Candidate/dashboard');
        }

        $data['hide_navbar'] = true;

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = "Candidate Login | Suropriyo Enterprise";
            $data['content'] = $this->load->view('candidate/candidateLoginView', '', TRUE);
            $this->load->view('candidate/layout', $data);
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $user = $this->CandidateModel->login_candidate($email, $password);

            if ($user) {
                $this->set_candidate_session($user->id, $user->email);
                $this->handle_redirect();
            } else {
                // BUG FIX: Post-Redirect-Get pattern. Never load a view directly on failed POST.
                $this->session->set_flashdata('error', 'Invalid Email or Password.');
                $this->session->set_flashdata('old_email', $email); // Save email so user doesn't retype it
                redirect('Candidate/login');
            }
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('candidate_id');
        $this->session->unset_userdata('candidate_email');
        $this->session->unset_userdata('candidate_logged_in');

        $this->session->set_flashdata('success', 'You have been safely logged out.');
        redirect('Careers');
    }

    public function dashboard()
    {
        $candidate_id = $this->session->userdata('candidate_id');

        $view_data['profile'] = $this->CandidateModel->get_candidate_by_id($candidate_id);
        $view_data['applications'] = $this->jobApplicationModel->get_applications_by_candidate($candidate_id);

        $auto_job_id = $this->session->flashdata('auto_open_job_id');
        if ($auto_job_id) {
            $this->load->model('JobsModel');
            $view_data['auto_apply_job'] = $this->JobsModel->get_job_by_id($auto_job_id);
        }

        $data['title'] = "My Dashboard | Suropriyo Enterprise";
        $data['content'] = $this->load->view('candidate/candidateDashboardView', $view_data, TRUE);
        $this->load->view('candidate/layout', $data);
    }

    private function set_candidate_session($id, $email)
    {
        $session_data = array(
            'candidate_id' => $id,
            'candidate_email' => $email,
            'candidate_logged_in' => TRUE
        );
        $this->session->set_userdata($session_data);
    }

    private function handle_redirect()
    {
        $job_id = $this->session->userdata('redirect_to_job');
        $candidate_id = $this->session->userdata('candidate_id');

        if ($job_id) {
            $this->session->unset_userdata('redirect_to_job');

            if ($this->jobApplicationModel->is_eligible_to_apply($candidate_id)) {
                $this->session->set_flashdata('auto_open_job_id', $job_id);
            } else {
                $this->session->set_flashdata('error', 'You currently have an active application. You can only apply for a new role if your current application is rejected.');
            }
        }
        redirect('Candidate/dashboard');
    }

    // BUG FIX: Added $job_id parameter to safeguard against URI routing changes
    public function validate_apply_form($job_id = NULL)
    {
        $this->form_validation->set_rules(
            'full_name', 'Full Name', 'required|trim|min_length[2]|max_length[80]|regex_match[/^[A-Za-z\s.\'\-]+$/]'
        );
        $this->form_validation->set_rules(
            'phone', 'Phone Number', 'required|trim|regex_match[/^(\+91|91|0)?[6-9]\d{9}$/]'
        );
        $this->form_validation->set_rules(
            'experience', 'Years of Experience', 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[50]'
        );
        $this->form_validation->set_rules(
            'expected_salary', 'Expected Salary', 'required|integer|greater_than_equal_to[1000]'
        );
        $this->form_validation->set_rules('gender', 'Gender', 'required|in_list[Male,Female,Other]');

        $this->form_validation->set_rules(
            'coverletter', 'Cover Letter', 'required|trim|min_length[50]|max_length[2000]'
        );

        // Fallback to URI segment if parameter wasn't passed by the calling controller
        $active_job_id = $job_id ? $job_id : $this->uri->segment(3);

        if ($this->form_validation->run() == FALSE) {
            $fields = array('full_name', 'phone', 'experience', 'expected_salary', 'gender', 'coverletter');
            $error_map = array();
            foreach ($fields as $f) {
                $err = $this->form_validation->error($f);
                if (!empty(strip_tags($err))) {
                    $error_map[$f] = strip_tags($err);
                }
            }

            if (empty($_FILES['resume']['name'])) {
                $error_map['resume'] = 'A PDF resume is required.';
            } else {
                $allowed_mime = array('application/pdf');
                $file_mime = mime_content_type($_FILES['resume']['tmp_name']);
                $file_ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
                $max_size = 5 * 1024 * 1024; 

                if (!in_array($file_mime, $allowed_mime) || $file_ext !== 'pdf') {
                    $error_map['resume'] = 'Only PDF files are accepted for the resume.';
                } elseif ($_FILES['resume']['size'] > $max_size) {
                    $error_map['resume'] = 'Resume must be smaller than 5 MB.';
                }
            }

            if (!empty($error_map)) {
                $old_values = array();
                foreach (array('full_name', 'phone', 'experience', 'expected_salary', 'coverletter') as $f) {
                    $old_values[$f] = $this->input->post($f, TRUE);
                }

                $this->session->set_flashdata('apply_errors', json_encode($error_map));
                $this->session->set_flashdata('apply_old', json_encode($old_values));
                $this->session->set_flashdata('auto_open_job_id', $active_job_id);

                return FALSE; 
            }
        }

        if (empty($_FILES['resume']['name'])) {
            $this->session->set_flashdata('apply_errors', json_encode(array('resume' => 'A PDF resume is required.')));
            $this->session->set_flashdata('auto_open_job_id', $active_job_id);
            return FALSE;
        }

        return TRUE; 
    }

    public function google_login()
    {
        require_once FCPATH . 'vendor/autoload.php';
        $client = new Google_Client();
        $client->setClientId(getenv('CLIENT_ID'));
        $client->setClientSecret(getenv('CLIENT_SECRET'));
        $client->setRedirectUri(base_url('Candidate/google_callback'));
        $client->addScope("email");
        $client->addScope("profile");

        $login_url = $client->createAuthUrl();
        redirect($login_url);
    }

    public function google_callback()
    {
        require_once FCPATH . 'vendor/autoload.php';
        $client = new Google_Client();
        $client->setClientId(getenv('CLIENT_ID'));
        $client->setClientSecret(getenv('CLIENT_SECRET'));
        $client->setRedirectUri(base_url('Candidate/google_callback'));

        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

            if (isset($token['error'])) {
                $this->session->set_flashdata('error', 'Google Auth Error: ' . $token['error_description']);
                redirect('Candidate/login');
            }

            $client->setAccessToken($token['access_token']);
            $google_oauth = new Google_Service_Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();

            $email = $google_account_info->email;
            $oauth_uid = $google_account_info->id;
            $name = $google_account_info->name;

            $user = $this->CandidateModel->authenticate_google_user($oauth_uid, $email, $name);

            if ($user) {
                $this->set_candidate_session($user->id, $user->email);
                $this->handle_redirect();
            } else {
                $this->session->set_flashdata('error', 'Google Authentication Failed. Please try manually registering.');
                redirect('Candidate/login');
            }
        } else {
            redirect('Candidate/login');
        }
    }
}