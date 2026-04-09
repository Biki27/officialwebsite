<?php

// This code is Written by :
// biki
// Suropriyo Eterprise
// Howrah

defined('BASEPATH') or exit('No direct script access allowed');

class Jobs extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('JobsModel');
        $this->load->model('jobApplicationModel'); // Added for portal logic
        $this->load->library('session');
        $this->load->helper(array('form', 'url'));
    }

    public function index()
    {
        // Capture any post data (though usually empty on first load)
        $search_query = $this->input->post();
        $ques = isset($search_query['search']) ? $search_query['search'] : '';

        // If there is no search query, use the filter_jobs_query to get the 
        // default view with the correct sorting logic you implemented.
        if (empty(trim($ques))) {
            $query = $this->JobsModel->filter_jobs_query();
        } else {
            $query = $this->JobsModel->get_search_in_anyfield_query($ques);
        }

        $result = $this->JobsModel->get_jobmodel_query_result($query);

        $viewData = array('res' => $result);
        $this->load->view('jobsView', $viewData);
        $this->load->view('footerView');
    }

    // Update SearchJob method
    /* public function SearchJob()
     {
         $search_query = $this->input->post();
         $ques = isset($search_query['search']) ? $search_query['search'] : '';

         $query = $this->JobsModel->get_search_in_anyfield_query($ques);
         $result = $this->JobsModel->get_jobmodel_query_result($query);

         // Add 'search_val' to the data array
         $viewData = array(
             'res' => $result,
             'search_val' => $ques
         );
         $this->load->view('jobsView', $viewData);
         $this->load->view('footerView');
     }*/

    // Update FilterJob method
    public function FilterJob()
    {

        $filter_query = $this->input->get();

        $title = $filter_query['jtitle'] ?? '';
        $location = $filter_query['jlocation'] ?? '';
        $skills = $filter_query['jskills'] ?? '';
        $experience = $filter_query['jexp'] ?? '';

        $query = $this->JobsModel->filter_jobs_query($title, $location, $skills, $experience);
        $result = $this->JobsModel->get_jobmodel_query_result($query);

        $viewData = array(
            'res' => $result,
            'filter_vals' => $filter_query,
            'scrollToResults' => true,
            // 'db_skills' => $this->JobsModel->get_all_skills() // <--- NEW: Fetching the skills
        );

        $this->load->view('jobsView', $viewData);
        $this->load->view('footerView');
    }

    public function Apply($job_id = null)
    {
        if ($job_id == null) {
            redirect('Careers/Jobs');
        }

        // 1. Existing check for active/inactive jobs
        $job = $this->JobsModel->get_job_by_id($job_id);
        if (!$job || strtolower($job->sejob_state) !== 'active') {
            $this->session->set_flashdata('error', 'This job position is no longer accepting applications.');
            redirect('Jobs');
            return;
        }

        // 2. If NOT logged in, save the Job ID and go to Login
        if (!$this->session->userdata('candidate_logged_in')) {
            $this->session->set_userdata('redirect_to_job', $job_id);
            $this->session->set_flashdata('error', 'Please log in to apply for this position.');
            redirect('Candidate/login');
            return;
        }

        // 3. ELIGIBILITY CHECK: If they are logged in, check their status
        $candidate_id = $this->session->userdata('candidate_id');
        if (!$this->jobApplicationModel->is_eligible_to_apply($candidate_id)) {
            $this->session->set_flashdata('error', 'You currently have an active application. You can only apply for a new role if your current application is rejected.');
            redirect('Candidate/dashboard');
            return;
        }

        // 4. Only set this if they pass all checks above
        $this->session->set_flashdata('auto_open_job_id', $job_id);
        redirect('Candidate/dashboard');
    }
    public function ApplyStatus($job_id = null)
    {
        if ($job_id == null || !$this->session->userdata('candidate_logged_in')) {
            redirect('Careers/Jobs');
        }

        $candidate_id = $this->session->userdata('candidate_id');

        // Verify eligibility again before processing
        if (!$this->jobApplicationModel->is_eligible_to_apply($candidate_id, $job_id)) {
            redirect('Candidate/dashboard');
        }

        if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {

            $config['upload_path'] = './resume/';
            $config['allowed_types'] = 'pdf';
            $config['file_name'] = 'resume_cand_' . $candidate_id . '_' . time();
            $config['max_size'] = 5120; // 5MB

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('resume')) {
                $fileinfo = $this->upload->data();
                $resume_path = './resume/' . $fileinfo['file_name'];

                // Capture all the form data
                $cover_letter = $this->input->post('coverletter');
                $phone = $this->input->post('phone');
                $experience = $this->input->post('experience');
                $expected_salary = $this->input->post('expected_salary');
                $gender = $this->input->post('gender', TRUE); 

                // If they provided a name (because they didn't have one), update their candidate profile
                $full_name = $this->input->post('full_name');
                if (!empty($full_name)) {
                    $this->db->where('id', $candidate_id);
                    $this->db->update('secandidates', array('full_name' => $full_name));
                }

                // Submit record linking the candidate_id to the job record with all new data
                $is_success = $this->jobApplicationModel->submit_application(
                    $candidate_id,
                    $job_id,
                    $resume_path,
                    $cover_letter,
                    $phone,
                    $experience,
                    $expected_salary
                    ,$gender
                );

                if ($is_success) {
                    $this->session->set_flashdata('success', 'Application submitted successfully!');
                    redirect('Candidate/dashboard');
                } else {
                    $this->session->set_flashdata('error', 'Database error. Please try again.');
                    redirect('Jobs/Apply/' . $job_id);
                }
            }
        }
    }
}