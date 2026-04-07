<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Candidate extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Load all necessary models, libraries, and helpers FIRST
        $this->load->model('CandidateModel');
        $this->load->model('jobApplicationModel');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper(array('url', 'form'));

        // ==========================================
        // THE SECURITY GATE WITH EXCEPTIONS
        // ==========================================
        // We define the methods that DO NOT require a login
        $allowed_methods = array('login', 'register', 'google_login', 'google_callback');

        // Get the current method the user is trying to access
        $current_method = $this->router->fetch_method();

        // If the method they are trying to access is NOT in the allowed list...
        if (!in_array($current_method, $allowed_methods)) {
            // ...then we check if they are actually logged in.
            if (!$this->session->userdata('candidate_logged_in')) {
                $this->session->set_flashdata('error', 'Please log in to access your dashboard.');
                redirect('Candidate/login'); // Redirect safely to our own login method
            }
        }
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
    }

    // 1. CANDIDATE REGISTRATION
    public function register()
    {
        // Redirect if already logged in
        if ($this->session->userdata('candidate_logged_in')) {
            redirect('Candidate/dashboard');
        }
        $data['hide_navbar'] = true;
        // Set Strict Validation Rules
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[secandidates.email]', array(
            'is_unique' => 'This email is already registered. Please log in.'
        ));
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            // Validation failed or first load -> Show the form using the Master Layout
            $data['title'] = "Create an Account | Suropriyo Enterprise";
            $data['content'] = $this->load->view('candidate/candidateRegisterView', '', TRUE);

            $this->load->view('candidate/layout', $data);
        } else {
            // Validation passed -> Register the candidate in the database
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $candidate_id = $this->CandidateModel->register_candidate($email, $password);

            if ($candidate_id) {
                // Log them in immediately
                $this->set_candidate_session($candidate_id, $email);

                $this->session->set_flashdata('success', 'Account created successfully! You can now apply for jobs.');
                $this->handle_redirect();
            } else {
                $this->session->set_flashdata('error', 'Something went wrong. Please try again.');
                redirect('Candidate/register');
            }
        }
    }
    // In the login page, I don't want to show the navbar and footer.
    // So, I will set a variable in the controller and check it in the layout.
    public function login()
    {
        // Redirect if already logged in
        if ($this->session->userdata('candidate_logged_in')) {
            redirect('Candidate/dashboard');
        }

        $data['hide_navbar'] = true;

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Validation failed or first load -> Show the form using the Master Layout
            $data['title'] = "Candidate Login | Suropriyo Enterprise";
            $data['content'] = $this->load->view('candidate/candidateLoginView', '', TRUE);

            $this->load->view('candidate/layout', $data);
        } else {
            // Process the login attempt
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $user = $this->CandidateModel->login_candidate($email, $password);

            if ($user) {
                // Login successful
                $this->set_candidate_session($user->id, $user->email);
                $this->handle_redirect();
            } else {
                $this->session->set_flashdata('error', 'Invalid Email or Password.');
                // reload the form — POST data stays alive for set_value()
                $data['title'] = "Candidate Login | Suropriyo Enterprise";
                $data['content'] = $this->load->view('candidate/candidateLoginView', '', TRUE);
                $this->load->view('candidate/layout', $data);
            }
        }
    }
    // public function login()
    // {
    //     if ($this->session->userdata('candidate_logged_in')) {
    //         redirect('Candidate/dashboard');
    //     }

    //     $data['hide_navbar'] = true;
    //     $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    //     $this->form_validation->set_rules('password', 'Password', 'required');

    //     if ($this->form_validation->run() == FALSE) {
    //         $data['title'] = "Candidate Login | Suropriyo Enterprise";
    //         $data['content'] = $this->load->view('candidate/candidateLoginView', '', TRUE);
    //         $this->load->view('candidate/layout', $data);
    //     } else {
    //         $email = $this->input->post('email');
    //         $password = $this->input->post('password');
    //         $user = $this->CandidateModel->login_candidate($email, $password);

    //         if ($user) {
    //             $this->set_candidate_session($user->id, $user->email);
    //             $this->handle_redirect();
    //         } else {
    //             // FIX: Use redirect instead of loading view directly
    //             $this->session->set_flashdata('error', 'Invalid Email or Password.');
    //             // Optional: flash the email back to pre-fill the field
    //              $data['title'] = "Candidate Login | Suropriyo Enterprise";
    //             $data['content'] = $this->load->view('candidate/candidateLoginView', '', TRUE);
    //             $this->session->set_flashdata('old_email', $email);
    //             redirect('Candidate/login');
    //         }
    //     }
    // }

    // 3. CANDIDATE LOGOUT
    public function logout()
    {
        // Safely remove ONLY candidate data so HR admins testing the site aren't logged out
        $this->session->unset_userdata('candidate_id');
        $this->session->unset_userdata('candidate_email');
        $this->session->unset_userdata('candidate_logged_in');

        $this->session->set_flashdata('success', 'You have been safely logged out.');
        redirect('Careers');
    }

    // 4. THE MAIN DASHBOARD
    public function dashboard()
    {
        // Notice we don't need a session check here because the __construct() handles it!
        $candidate_id = $this->session->userdata('candidate_id');

        // Fetch Candidate Identity Profile
        $view_data['profile'] = $this->CandidateModel->get_candidate_by_id($candidate_id);

        // Fetch their Application History (Joined with Job Details)
        $view_data['applications'] = $this->jobApplicationModel->get_applications_by_candidate($candidate_id);

        // Check if we just logged in and need to auto-open an application form
        $auto_job_id = $this->session->flashdata('auto_open_job_id');
        if ($auto_job_id) {
            $this->load->model('JobsModel');
            $view_data['auto_apply_job'] = $this->JobsModel->get_job_by_id($auto_job_id);
        }

        // Prepare the Master Layout Data
        $data['title'] = "My Dashboard | Suropriyo Enterprise";

        // Load the view into a string variable
        $data['content'] = $this->load->view('candidate/candidateDashboardView', $view_data, TRUE);

        // Render the lightweight master layout
        $this->load->view('candidate/layout', $data);
    }

    // 5. HELPER FUNCTIONS
    // Sets the secure session array
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

            //  Only set the auto-open flag if they are eligible
            if ($this->jobApplicationModel->is_eligible_to_apply($candidate_id)) {
                $this->session->set_flashdata('auto_open_job_id', $job_id);
            } else {
                // If they have an active application, show an error instead of the pop-up
                $this->session->set_flashdata('error', 'You currently have an active application. You can only apply for a new role if your current application is rejected.');
            }
        }

        redirect('Candidate/dashboard');
    }

    // ==========================================
    // 7. APPLY FORM SERVER-SIDE VALIDATION
    // ==========================================
    // Called by Jobs/ApplyStatus BEFORE saving the application.
    // Returns TRUE if all rules pass; FALSE + sets flashdata if not.
    //
    // Usage in Jobs controller (ApplyStatus method):
    //
    //   $this->load->controller('Candidate');           // or use a shared model/library
    //   if (!$this->Candidate->validate_apply_form()) {
    //       redirect('Candidate/dashboard');
    //   }
    //
    // Because the form is a modal on the dashboard we redirect back
    // to the dashboard on failure; the view reads flashdata to
    // re-open the modal and highlight the bad fields.
    // ==========================================
    public function validate_apply_form()
    {
        // Rules
        $this->form_validation->set_rules(
            'full_name',
            'Full Name',
            'required|trim|min_length[2]|max_length[80]|regex_match[/^[A-Za-z\s.\'\-]+$/]',
            array(
                'required' => 'Full name is required.',
                'min_length' => 'Name must be at least 2 characters.',
                'max_length' => 'Name cannot exceed 80 characters.',
                'regex_match' => 'Name may only contain letters, spaces, dots, or hyphens.'
            )
        );

        $this->form_validation->set_rules(
            'phone',
            'Phone Number',
            'required|trim|regex_match[/^(\+91|91|0)?[6-9]\d{9}$/]',
            array(
                'required' => 'Phone number is required.',
                'regex_match' => 'Enter a valid 10-digit Indian mobile number.'
            )
        );

        $this->form_validation->set_rules(
            'experience',
            'Years of Experience',
            'required|decimal|greater_than_equal_to[0]|less_than_equal_to[50]',
            array(
                'required' => 'Experience is required.',
                'decimal' => 'Experience must be a number (e.g. 2 or 2.5).',
                'greater_than_equal_to' => 'Experience cannot be negative.',
                'less_than_equal_to' => 'Experience cannot exceed 50 years.'
            )
        );

        $this->form_validation->set_rules(
            'expected_salary',
            'Expected Salary',
            'required|integer|greater_than_equal_to[1000]',
            array(
                'required' => 'Expected salary is required.',
                'integer' => 'Salary must be a whole number.',
                'greater_than_equal_to' => 'Salary must be at least ₹1,000 per month.'
            )
        );

        $this->form_validation->set_rules(
            'coverletter',
            'Cover Letter',
            'required|trim|min_length[50]|max_length[2000]',
            array(
                'required' => 'A cover letter is required.',
                'min_length' => 'Cover letter must be at least 50 characters.',
                'max_length' => 'Cover letter cannot exceed 2000 characters.'
            )
        );

        if ($this->form_validation->run() == FALSE) {
            // Build a field-keyed error map for the view
            $fields = array('full_name', 'phone', 'experience', 'expected_salary', 'coverletter');
            $error_map = array();
            foreach ($fields as $f) {
                $err = $this->form_validation->error($f);
                if (!empty(strip_tags($err))) {
                    $error_map[$f] = strip_tags($err);
                }
            }

            // Server-side file check (CI's form_validation cannot validate files)
            if (empty($_FILES['resume']['name'])) {
                $error_map['resume'] = 'A PDF resume is required.';
            } else {
                $allowed_mime = array('application/pdf');
                $file_mime = mime_content_type($_FILES['resume']['tmp_name']);
                $file_ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
                $max_size = 5 * 1024 * 1024; // 5 MB

                if (!in_array($file_mime, $allowed_mime) || $file_ext !== 'pdf') {
                    $error_map['resume'] = 'Only PDF files are accepted for the resume.';
                } elseif ($_FILES['resume']['size'] > $max_size) {
                    $error_map['resume'] = 'Resume must be smaller than 5 MB.';
                }
            }

            if (!empty($error_map)) {
                // Preserve old POST values so the view can re-populate fields
                $old_values = array();
                foreach (array('full_name', 'phone', 'experience', 'expected_salary', 'coverletter') as $f) {
                    $old_values[$f] = $this->input->post($f, TRUE);
                }

                // Store errors + old values as flashdata, then signal the dashboard
                // to re-open the modal with the correct job ID
                $this->session->set_flashdata('apply_errors', json_encode($error_map));
                $this->session->set_flashdata('apply_old', json_encode($old_values));
                // Re-trigger modal on dashboard for the same job
                $job_id = $this->uri->segment(3); // Jobs/ApplyStatus/{job_id}
                $this->session->set_flashdata('auto_open_job_id', $job_id);

                return FALSE; // Caller must redirect('Candidate/dashboard')
            }
        }

        // Extra file validation when form_validation passed but file still unchecked
        if (empty($_FILES['resume']['name'])) {
            $this->session->set_flashdata('apply_errors', json_encode(
                array('resume' => 'A PDF resume is required.')
            ));
            $job_id = $this->uri->segment(3);
            $this->session->set_flashdata('auto_open_job_id', $job_id);
            return FALSE;
        }

        return TRUE; // All good — caller may proceed to save
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

            // Check if the token is valid before setting it
            if (isset($token['error'])) {
                $this->session->set_flashdata('error', 'Google Auth Error: ' . $token['error_description']);
                redirect('Candidate/login');
            }

            $client->setAccessToken($token['access_token']);


            // Get the user's Google profile info
            $google_oauth = new Google_Service_Oauth2($client);
            $google_account_info = $google_oauth->userinfo->get();

            $email = $google_account_info->email;
            $oauth_uid = $google_account_info->id;

            // WE ADDED THIS LINE to fetch the Name:
            $name = $google_account_info->name;

            // Send to our Model (Now passing 3 variables: ID, Email, and Name)
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
