<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Employee extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Prevent browser caching for security (Solves the Back Button issue)
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }
    function index()
    {
        $this->Login();
    }
    function Login()
    {

        if ($this->session->has_userdata('empid') && $this->session->userdata('status') != 'inactive') {
            redirect('Employee/Dashboard');
            return; // Stop executing the rest of the function
        }


        $credentials = $this->input->post();
        $data = $this->security->xss_clean($credentials);

        if (!isset($data['username']) || !isset($data['password'])) {
            $this->load->view('employee/employeeLoginView');
            return;
        }

        $this->load->model('EmployeeModel');
        $result = $this->EmployeeModel->check_if_employee_exist($data['username'], $data['password']);

        if ($result['code'] != 0) {
            // CASE 1: Username does not exist
            $this->load->view('employee/employeeLoginView', array(
                'error' => 'Incorrect Username. This email is not registered.',
                'old_username' => $data['username']
            ));
        } else {
            $user = $result['user'];

            // CASE 2: Check Password
            if (password_verify($data['password'], $user->seemp_pass)) {

                // CASE 3: Check if Active
                if ($user->seemp_status == 'active') {
                    $sdata = array(
                        'email' => $user->seemp_email,
                        'status' => $user->seemp_status,
                        'empid' => $user->seemp_id,
                        'accesslevel' => $user->seemp_acesslevel,
                        'branch' => $user->seemp_branch,
                        'lastlogin' => $user->seemp_lastlogin,
                    );
                    $this->session->set_userdata($sdata);
                    redirect('Employee/Dashboard');
                } else {
                    $this->load->view('employee/employeeLoginView', array(
                        'error' => 'Your account is inactive. Please contact HR.',
                        'old_username' => $data['username']
                    ));
                }
            } else {
                // CASE 4: Username correct, but Password wrong
                $this->load->view('employee/employeeLoginView', array(
                    'error' => 'Incorrect Password. Please try again.',
                    'old_username' => $data['username']
                ));
            }
        }
    }
    function Dashboard()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') != 'inactive'
        ) {
            if ($this->session->userdata('accesslevel') == 'ADMIN') {
                $this->AdminDashboard();
            }

            if ($this->session->userdata('accesslevel') == 'HR') {
                $this->HRDashboard();
            }

            if ($this->session->userdata('accesslevel') == 'EMPL') {
                $this->EmployeeOverview();
            }
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }
    function AdminDashboard()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'ADMIN'
        ) {
            $this->load->model('EmployeeModel');
            $this->load->model('jobApplicationModel');
            $this->load->model('ProjectsModel');
            $this->load->model('AttendanceModel');
            $this->load->model('EmployeeDetailsModel');
            $this->load->model('RequestsModel');

            // Fetch employee details from seempdetails table
            $empdetails = $this->EmployeeDetailsModel->get_this_employee_details();

            if (!empty($empdetails) && !empty($empdetails[0]->seempd_name)) {
                $this->session->set_userdata('empname', $empdetails[0]->seempd_name);
            } else {
                $this->session->set_userdata('empname', 'Administrator');
            }

            if (!empty($empdetails) && !empty($empdetails[0]->seempd_jobaid)) {
                $emp_appliction_details = $this->jobApplicationModel->get_applicant_info($empdetails[0]->seempd_jobaid);
                if (!empty($emp_appliction_details)) {
                    $this->session->set_userdata('empapid', $emp_appliction_details[0]->sejoba_id);
                }
            }

            // ── Payroll counts for current month ──
            // Uses the same model methods as salaryManagement() so counts always match.
            $payroll_employees = $this->EmployeeModel->get_payroll_employees();
            $monthly_slips_now = $this->EmployeeModel->get_slips_by_month(date('Y-m'));
            $total_staff_count = $this->EmployeeModel->get_total_staff_count();
            $processed_count_now = count($monthly_slips_now);   // Slips Generated  (PAID)
            $pending_count_now = count($payroll_employees) - $processed_count_now; // Pending (UNPAID)

            // Fetch dashboard statistics
            $data = array(
                'projpending' => count($this->ProjectsModel->getPendingProjects()),
                'projrunning' => count($this->ProjectsModel->getRunningProjects()),
                'projcompleted' => count($this->ProjectsModel->getCompletedProjects()),
                'total_staff' => $total_staff_count,
                'present_today' => $this->AttendanceModel->get_present_today_count(),
                'new_apps' => $this->jobApplicationModel->get_new_applicants_count(),

                'recent_projs' => $this->ProjectsModel->getRecentProjects(5),
                'deadlines' => $this->ProjectsModel->getUpcomingDeadlines(3),

                // Payroll summary
                'processed_count' => $processed_count_now,
                'pending_count' => $pending_count_now,

                // Leave requests
                'leave_pending' => $this->RequestsModel->get_pending_requests_count(),
            );

            $this->load->view('employee/adminHeaderView');
            $this->load->view('employee/adminDashboardView', $data);
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }


    // AdminEmployee
    public function viewEmployee()
    {
        $access = $this->session->userdata('accesslevel');
        if ($this->session->userdata('status') == 'active' && ($access == 'ADMIN' || $access == 'HR')) {

            $this->load->model('EmployeeModel');
            // Get values
            $query = $this->input->post('query');
            $status = $this->input->post('status');
            // Clean input  
            $query = $this->security->xss_clean($query);
            $status = $this->security->xss_clean($status);

            if (!empty($query) || !empty($status)) {
                $data['employees'] = $this->EmployeeModel->get_employee_with_search($query, $status);
            } else {
                $data['employees'] = $this->EmployeeModel->getallemployee_with_joins();
            }

            if ($access == 'HR') {
                $this->load->view('hr/hrHeaderView');
                $this->load->view('employee/adminEmployeesView', $data);
            } else {
                $this->load->view('employee/adminHeaderView');
                $this->load->view('employee/adminEmployeesView', $data);
            }
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }

    function viewJobApplicants()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'ADMIN' || $this->session->userdata('accesslevel') == 'HR'
        ) {
            $this->load->model('jobApplicationModel');

            $postd = $this->input->post();

            if ($postd) {
                $postdata = $this->security->xss_clean($postd);

                $id = trim($postdata['applicant_id'] ?? '');
                $status = trim($postdata['status'] ?? '');
                $comment = trim($postdata['comment'] ?? '');

                if ($id != '' && $status != '') {
                    $this->jobApplicationModel->update_applicant_review(
                        $id,
                        $status,
                        $comment
                    );

                    $this->session->set_flashdata('msg', 'Review saved successfully.');
                    redirect('Employee/viewJobApplicants');
                }
            }

            // Fetch all applicants
            $all_applicants = $this->jobApplicationModel->get_all_applicants();

            // ---> NEW: Filter out 'hired' candidates so they disappear from the table
            $data['applicants'] = array_filter($all_applicants, function ($app) {
                return strtolower($app->sejoba_state) !== 'hired';
            });

            if ($this->session->userdata('accesslevel') == 'HR') {
                $this->load->view('hr/hrHeaderView');
                $this->load->view('hr/hrJobApplicantsView', $data);
            } else {
                $this->load->view('employee/adminHeaderView');
                $this->load->view('employee/adminJobApplicationsView', $data);
            }
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }

    // Admin Section forward to view details
    function viewEmployeeDetails()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'ADMIN' || $this->session->userdata('accesslevel') == 'HR'
        ) {
            $postd = $this->input->post();
            $postdata = $this->security->xss_clean($postd);

            if (isset($postdata['empid'])) {
                $this->load->model('EmployeeModel');
                $data = array();

                $res1 = $this->EmployeeModel->get_employee_with_id($postdata['empid'])[0];
                $data += ['info' => $res1];

                $_POST = [];
                if ($this->session->userdata('accesslevel') == 'HR') {
                    $this->load->view('hr/hrHeaderView');
                    $this->load->view('employee/adminEmployeeDetailsView', $data);
                    return;
                }
                $this->load->view('employee/adminHeaderView');
                $this->load->view('employee/adminEmployeeDetailsView', $data);
            }
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }

    // add coded for view details of employee total leave and history details
    public function getEmployeeLeaveSummary($empid)
    {
        if ($this->session->userdata('status') != 'active') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $this->load->model('RequestsModel');
        // We modify the existing RequestsModel logic to fetch by empid
        $history = $this->RequestsModel->get_requestes_for_any_empid($empid);

        $approved_days = 0;
        foreach ($history as $h) {
            if ($h->seemrq_status == 'approved') {
                $approved_days += (int) $h->seemrq_days;
            }
        }

        echo json_encode([
            'history' => $history,
            'approved_days' => $approved_days
        ]);
    }

    function viewAttendance()
    {
        $access = $this->session->userdata('accesslevel');
        if ($this->session->userdata('status') == 'active' && ($access == 'ADMIN' || $access == 'HR')) {

            $this->load->model('AttendanceModel');
            $empid_session = $this->session->userdata('empid');

            // 1. Fetch the HR/Admin's OWN today's log for the top card
            $todayAttendance = $this->AttendanceModel->get_today_login_logout($empid_session);

            $postd = $this->input->post();
            if ($postd) {
                $postdata = $this->security->xss_clean($postd);
                $s_id = trim($postdata['searchempid'] ?? '');
                $start = $postdata['startdate'] ?? '';
                $end = $postdata['enddate'] ?? '';
                // 2. Search results for the table below
                $list = $this->AttendanceModel->find_empid_with_daterange($s_id, $start, $end);
            } else {
                // 3. Default list for the table below
                $list = $this->AttendanceModel->get_attendance_of_all_employee();
            }

            // 4. Combine both for the View
            $data = array(
                'atten' => $list,
                'todayAttendance' => $todayAttendance
            );

            $header = ($access == 'HR') ? 'hr/hrHeaderView' : 'employee/adminHeaderView';
            $this->load->view($header);
            $this->load->view('employee/adminAttendanceView', $data);
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }
    public function viewAttendanceAjax()
    {
        // 1. Authorization check similar to your existing viewAttendance method
        $access = $this->session->userdata('accesslevel');
        if ($this->session->userdata('status') != 'active' || !($access == 'ADMIN' || $access == 'HR')) {
            echo json_encode([]);
            return;
        }

        $this->load->model('AttendanceModel');

        // 2. Get and Clean Input
        $s_id = trim($this->input->post('searchempid', TRUE));
        $start = $this->input->post('startdate', TRUE);
        $end = $this->input->post('enddate', TRUE);

        // 3. Fetch data using your existing model logic
        $list = $this->AttendanceModel->find_empid_with_daterange($s_id, $start, $end);

        // 4. Format the dates/times for the JSON output
        foreach ($list as &$att) {
            $att->formatted_date = date("d-M-Y", strtotime($att->seemp_logdate));
            $att->formatted_login = date("h:i A", strtotime($att->seemp_logintime));
            $att->formatted_logout = ($att->seemp_logouttime && $att->seemp_logouttime != '0000-00-00 00:00:00')
                ? date("h:i A", strtotime($att->seemp_logouttime))
                : '<span class="text-muted">Not Logged Out</span>';
        }

        // 5. Send JSON response back to the browser
        echo json_encode($list);
    }
    // Admin view fetch job applicants details
    public function getApplicantDetails($id)
    {
        // Authorization Check
        if (!$this->session->has_userdata('empid')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        // UPDATED: Added JOIN to get personal details from secandidates
        $this->db->select('sejobapplicant.*, secandidates.full_name, secandidates.email');
        $this->db->from('sejobapplicant');
        $this->db->join('secandidates', 'sejobapplicant.candidate_id = secandidates.id', 'left');
        $this->db->where('sejoba_id', $id);
        $this->db->where('sejoba_state', 'selected');

        $query = $this->db->get();
        $applicant = $query->row();

        if ($applicant) {
            echo json_encode(['success' => true, 'data' => $applicant]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Applicant not found or not in "Selected" state.']);
        }
    }


    // AdminviewProjects
    function viewProjects()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'ADMIN'
        ) {
            $this->load->model('ProjectsModel');

            // CHANGE: Always fetch all projects so JavaScript can filter them
            // This prevents "losing" projects when you switch filters
            $projects = $this->ProjectsModel->getAllProjects();

            $data['projects'] = $projects;
            $data['total'] = $this->ProjectsModel->count_all_projects();
            $data['running'] = $this->ProjectsModel->count_running_projects();
            $data['pending'] = $this->ProjectsModel->count_pending_projects();
            $data['completed'] = $this->ProjectsModel->count_completed_projects();

            $this->load->view('employee/adminHeaderView');
            $this->load->view('employee/adminProjectsView', $data);
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }
    // Add Project
    function addProject()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'ADMIN'
        ) {
            $this->load->model('ProjectsModel');

            $post = $this->input->post();

            if ($post) {
                $postdata = $this->security->xss_clean($post);

                $data = array(
                    'seproj_name' => $postdata['projectName'],
                    'seproj_desc' => $postdata['description'],
                    'seproj_date' => $postdata['startDate'],
                    'seproj_deadline' => $postdata['deadlineDate'],
                    'seproj_clientid' => $postdata['clientName'],
                    'seproj_headid' => $postdata['projectHead'],
                    'seproj_price' => $postdata['price'],
                    'seproj_status' => strtolower($postdata['status'])
                );
                $this->ProjectsModel->insert_project($data);

                $this->session->set_flashdata('msg', 'Project Added Successfully');
                // $this->load->view('employee/adminHeaderView');
                redirect('Employee/viewProjects');
            }
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }

    function addProjectPage()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'ADMIN'
        ) {
            $this->load->view('employee/adminHeaderView');
            $this->load->view('employee/addNewProjectView');
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }

    function fetchProject()
    {
        $id = $this->input->post('id');

        $this->load->model('ProjectsModel');

        $project = $this->ProjectsModel->getProjectById($id);

        echo json_encode($project);
    }

    function updateProject()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'ADMIN'
        ) {
            $this->load->model('ProjectsModel');

            $post = $this->input->post();

            if ($post) {
                $postdata = $this->security->xss_clean($post);

                $projectId = preg_replace('/[^0-9]/', '', $post['projectId']);

                $data = [
                    'seproj_name' => $post['projectName'],
                    'seproj_desc' => $post['description'],
                    'seproj_date' => $post['startDate'],
                    'seproj_deadline' => $post['deadlineDate'],
                    'seproj_clientid' => $post['clientName'],
                    'seproj_headid' => $post['projectHead'],
                    'seproj_price' => $post['price'],
                    'seproj_status' => $post['status']
                ];

                $this->ProjectsModel->update_project($projectId, $data);

                $this->session->set_flashdata('msg', 'Project Updated Successfully');

                redirect('Employee/viewProjects');
            } else {
                $this->session->sess_destroy();
                $this->load->view('errors/invalidAccessView');
            }
        }
    }

    /**
     * AJAX — Delete a project by ID.
     * POST params: id (integer)
     * Returns JSON: { success, message, csrf_hash }
     */
    public function deleteProject()
    {
        // Auth guard
        if (
            !$this->session->has_userdata('empid') ||
            $this->session->userdata('status')      !== 'active' ||
            $this->session->userdata('accesslevel') !== 'ADMIN'
        ) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $id = $this->input->post('id');
        $id = (int) preg_replace('/[^0-9]/', '', $id);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid project ID.']);
            return;
        }

        $this->load->model('ProjectsModel');
        $deleted = $this->ProjectsModel->delete_project($id);

        if ($deleted) {
            echo json_encode([
                'success'   => true,
                'message'   => 'Project deleted successfully.',
                'csrf_hash' => $this->security->get_csrf_hash(),   // Refresh token for next request
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Could not delete the project. It may no longer exist.',
            ]);
        }
    }

    /**
     * AJAX — Check whether a project with the given name already exists.
     * POST params: name (string)
     * Returns JSON: full project row object, or null if no match.
     */
    public function checkDuplicateProject()
    {
        // Auth guard
        if (
            !$this->session->has_userdata('empid') ||
            $this->session->userdata('status') !== 'active'
        ) {
            echo json_encode(null);
            return;
        }

        $name = $this->input->post('name');
        $name = trim($this->security->xss_clean($name));

        if (empty($name)) {
            echo json_encode(null);
            return;
        }

        $this->load->model('ProjectsModel');
        $project = $this->ProjectsModel->getProjectByName($name);

        echo json_encode($project);   // null if not found, object if found
    }

    function RegisterEmployee()
    {
        if (
            $this->session->userdata('status') == 'active' &&
            ($this->session->userdata('accesslevel') == 'ADMIN' || $this->session->userdata('accesslevel') == 'HR')
        ) {
            $this->load->model('EmployeeModel');
            $post = $this->input->post();
            $data = [];

            // 1. Check if Editing an Employee
            if (!empty($post['empid'])) {
                $empid = $post['empid'];
                $emp = $this->EmployeeModel->get_employee_full_details($empid);
                if (!empty($emp)) {
                    $data['emp'] = $emp[0];
                }
            }

            $applicant_id = $this->input->get('applicant_id', TRUE);
            if (!empty($applicant_id)) {
                // UPDATED: Now joins with secandidates to get the name/email for pre-filling
                $this->db->select('sejobapplicant.*, secandidates.full_name as sejoba_name, secandidates.email as sejoba_email');
                $this->db->from('sejobapplicant');
                $this->db->join('secandidates', 'sejobapplicant.candidate_id = secandidates.id', 'left');
                $this->db->where('sejoba_id', $applicant_id);

                $app_query = $this->db->get();
                if ($app_query->num_rows() > 0) {
                    $data['prefill_applicant'] = $app_query->row();
                }
            }

            $header = ($this->session->userdata('accesslevel') == 'HR') ? 'hr/hrHeaderView' : 'employee/adminHeaderView';
            $this->load->view($header);
            $this->load->view('employee/adminEmployeeRegistrationView', $data);
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }
    public function addEmployee()
    {
        $this->load->library('upload');
        $this->load->model('EmployeeModel');
        $this->load->library('form_validation');

        //  Set Validation Rules
        $this->form_validation->set_rules('empName', 'Employee Name', 'required|trim');
        $this->form_validation->set_rules('empid', 'Employee ID', 'required|trim|is_unique[seemployee.seemp_id]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[seemployee.seemp_email]');
        $this->form_validation->set_rules('phone', 'Phone', 'required|numeric|exact_length[10]');
        $this->form_validation->set_rules('aadhar', 'Aadhar', 'required|numeric|exact_length[12]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('branch', 'Branch', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');
        $this->form_validation->set_rules('accessLevel', 'Access Level', 'required');

        // 2. Check Validation Result
        if ($this->form_validation->run() == FALSE) {
            // Strip HTML tags and separate errors with a standard newline
            $error_msg = strip_tags(validation_errors('', "\n"));

            $this->session->set_flashdata('msg', "Validation Failed:\n" . $error_msg);
            redirect('Employee/RegisterEmployee');
            return;
        }

        //  Prepare Upload Configuration
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|doc|docx';
        $config['max_size'] = 5120; // 5MB
        $config['encrypt_name'] = TRUE;
        $this->upload->initialize($config);

        // Handle Photo & CV Uploads
        $photo_name = '';

        if (!empty($_FILES['photo']['name'])) {
            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {

                $this->session->set_flashdata('msg', '❌ Please upload an image smaller than 5MB.');
                redirect('Employee/RegisterEmployee');
                return;
            }

            // Now upload
            if ($this->upload->do_upload('photo')) {

                $photo_name = $this->upload->data('file_name');
            } else {

                $this->session->set_flashdata('msg', 'Photo Upload Error: ' . strip_tags($this->upload->display_errors('', '')));
                redirect('Employee/RegisterEmployee');
                return;
            }
        }

        $cv_name = '';
        if (empty($_FILES['cv']['name'])) {
            $this->session->set_flashdata('msg', 'Error: CV Document is required for new employees.');
            redirect('Employee/RegisterEmployee');
            return;
        } else if ($this->upload->do_upload('cv')) {
            $cv_name = $this->upload->data('file_name');
        } else {
            $this->session->set_flashdata('msg', 'CV Upload Error: ' . strip_tags($this->upload->display_errors('', '')));
            redirect('Employee/RegisterEmployee');
            return;
        }

        $formData = $this->input->post();

        $employee = [
            'seemp_id' => $formData['empid'],
            'seemp_branch' => $formData['branch'],
            'seemp_email' => $formData['email'],
            'seemp_pass' => password_hash($formData['password'], PASSWORD_DEFAULT),
            'seemp_status' => strtolower($formData['status']),
            'seemp_acesslevel' => $formData['accessLevel']
        ];

        $details = [
            'seempd_empid' => $formData['empid'],
            'seempd_name' => $formData['empName'],
            'seempd_phone' => $formData['phone'],
            'seempd_designation' => $formData['designation'],
            'seempd_salary' => $formData['salary'],
            'seempd_project' => $formData['project'],
            'seempd_experience' => $formData['experience'],
            'seempd_dob' => $formData['dob'],
            'seempd_joiningdate' => $formData['joiningDate'],
            'seempd_increment' => $formData['increment'],
            'seempd_address_permanent' => $formData['permAddress'],
            'seempd_address_current' => $formData['currentAddress'],
            'seempd_aadhar' => $formData['aadhar'],
            'seempd_pan' => $formData['pan'],
            'seempd_img' => $photo_name,
            'seempd_cv' => $cv_name,
            'seempd_jobaid' => !empty($formData['linked_applicant_id']) ? $formData['linked_applicant_id'] : NULL
        ];

        $result = $this->EmployeeModel->register_employee($employee, $details);

        if ($result['code'] == 0) {

            // Auto-Remove from Applicant Section by marking as 'hired'
            $linked_app_id = $this->input->post('linked_applicant_id', TRUE);
            if (!empty($linked_app_id)) {
                $this->db->where('sejoba_id', $linked_app_id);
                $this->db->update('sejobapplicant', ['sejoba_state' => 'hired']);
            }

            $this->session->set_flashdata('msg', 'Employee Added & Removed from Applicant List!');
            redirect('Employee/viewEmployee');
        } else {
            $this->session->set_flashdata('msg', 'Database Error adding employee. ID or Email may already exist.');
            redirect('Employee/RegisterEmployee');
        }
    }
    // updateEmployee function with improved validation, file handling, and error management
    public function updateEmployee($empid)
    {
        $this->load->library('upload');
        $this->load->model('EmployeeModel');
        $this->load->library('form_validation');

        // Fetch current employee details FIRST (Before validation)
        $current = $this->EmployeeModel->get_employee_full_details($empid);
        if (empty($current)) {
            show_error('Employee not found');
        }

        // Set Standard Validation Rules
        $this->form_validation->set_rules('empName', 'Employee Name', 'required|trim');
        $this->form_validation->set_rules('phone', 'Phone', 'required|numeric|exact_length[10]');
        $this->form_validation->set_rules('aadhar', 'Aadhar', 'required|numeric|exact_length[12]');

        // Email Uniqueness Check
        $posted_email = $this->input->post('email');
        if (trim($posted_email) !== $current[0]->seemp_email) {
            // They changed the email, so check if the NEW email already belongs to someone else
            $this->form_validation->set_rules(
                'email',
                'Email',
                'required|trim|valid_email|is_unique[seemployee.seemp_email]',
                array('is_unique' => 'This Email is already registered to another employee.')
            );
        } else {
            // They kept their current email, just validate the format
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        }

        //  Employee ID Uniqueness Check (In case they bypassed the readonly HTML)
        $posted_empid = $this->input->post('empid');
        if (trim($posted_empid) !== $current[0]->seemp_id) {
            $this->form_validation->set_rules(
                'empid',
                'Employee ID',
                'required|trim|is_unique[seemployee.seemp_id]',
                array('is_unique' => 'This Employee ID is already taken.')
            );
        }

        // Password Validation (Only if they typed something)
        if (!empty($this->input->post('password'))) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
        }

        // Run Validation
        if ($this->form_validation->run() == FALSE) {
            $error_msg = strip_tags(validation_errors('', "\n"));

            $this->session->set_flashdata('msg', "Validation Failed:\n" . $error_msg);
            redirect('Employee/RegisterEmployee');
            return;
        }

        // File Upload Config
        $photo_name = $current[0]->seempd_img;
        $cv_name = $current[0]->seempd_cv;

        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|doc|docx';
        $config['max_size'] = 5120;
        $config['encrypt_name'] = TRUE;
        $this->upload->initialize($config);

        // Process Uploads (Only overwrite if new file exists)
        if (!empty($_FILES['photo']['name'])) {

            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {

                $this->session->set_flashdata('msg', '❌ Please upload an image smaller than 5MB.');
                redirect('Employee/RegisterEmployee');
                return;
            }

            if ($this->upload->do_upload('photo')) {
                $photo_name = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('msg', 'Photo Upload Error: ' . strip_tags($this->upload->display_errors('', '')));
                redirect('Employee/RegisterEmployee');
                return;
            }
        }
        if (!empty($_FILES['cv']['name']) && $this->upload->do_upload('cv')) {
            $cv_name = $this->upload->data('file_name');
        }

        // Update Data
        $updateData = [
            'new_empid' => $this->input->post('empid'), // New Employee ID
            'empName' => $this->input->post('empName'),
            'email' => $this->input->post('email'),
            'branch' => $this->input->post('branch'),
            'status' => $this->input->post('status'),
            'designation' => $this->input->post('designation'),
            'phone' => $this->input->post('phone'),
            'salary' => $this->input->post('salary'),
            'experience' => $this->input->post('experience'),
            'dob' => $this->input->post('dob'),
            'joiningDate' => $this->input->post('joiningDate'),
            'permAddress' => $this->input->post('permAddress'),
            'currentAddress' => $this->input->post('currentAddress'),
            'aadhar' => $this->input->post('aadhar'),
            'pan' => $this->input->post('pan'),
            'accessLevel' => $this->input->post('accessLevel'),
            'project' => $this->input->post('project'),
            'increment' => $this->input->post('increment'),
            'photo' => $photo_name,
            'cv' => $cv_name
        ];

        $result = $this->EmployeeModel->update_employee($empid, $updateData);

        if ($result['code'] == 0) {
            $this->session->set_flashdata('msg', 'Employee Updated Successfully');
        } else {
            $this->session->set_flashdata('msg', 'Update failed: ' . $result['message']);
        }
        redirect('Employee/viewEmployee');
    }
    // Employee Overview
    function EmployeeOverview()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'EMPL'
        ) {
            $this->load->model('RequestsModel');
            $holidaycount = $this->RequestsModel->get_holidays_count();

            $this->load->model('EmployeeDetailsModel');

            $empdetails = $this->EmployeeDetailsModel->get_this_employee_details();

            if (empty($empdetails)) {
                echo "Employee details not found";
                return;
            }

            $emp = $empdetails[0];

            $this->session->set_userdata('empname', $emp->seempd_name);

            $this->load->model('EmployeeModel');
            $bank_details = $this->EmployeeModel->get_bank_details($this->session->userdata('empid'));

            // ---> ADD THESE TWO LINES <---
            $this->load->model('AttendanceModel');
            $todayAttendance = $this->AttendanceModel->get_today_login_logout($this->session->userdata('empid'));

            $data = array(
                'holidays_taken' => $holidaycount,
                'holidays_used' => 20 - $holidaycount,
                'holidays_percent' => 100 * (20 - $holidaycount) / 20,
                'empdetails' => $emp,
                'bank_details' => $bank_details,
                'todayAttendance' => $todayAttendance // ---> ADD THIS LINE <---
            );

            $this->load->view('employee/employeeHeaderView');
            $this->load->view('employee/employeeOverView', $data);
        } else {

            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }
    //new
    // Handle Employee Self-Service Bank Details
    public function updateMyBankDetails()
    {
        // Ensure only logged-in employees can do this
        if (!$this->session->userdata('empid') || $this->session->userdata('accesslevel') != 'EMPL') {
            redirect('Employee/Login');
        }

        $post = $this->input->post();
        if ($post) {
            $data = $this->security->xss_clean($post);

            $bank_data = array(
                'sebank_empid' => $this->session->userdata('empid'),
                'sebank_ac_no' => $data['bank_ac'],
                'sebank_ifsc' => strtoupper($data['bank_ifsc']), // Force uppercase for IFSC
                'sebank_esi' => $data['bank_esi']
            );

            $this->load->model('EmployeeModel');
            $this->EmployeeModel->save_bank_details($bank_data);

            // Send a success message back to the dashboard
            $this->session->set_flashdata('msg', 'Bank details securely updated!');
            redirect('Employee/EmployeeOverview');
        }
    }
    // --- Employee Portal: View Salary Slips Page ---
    public function mySalarySlips()
    {
        if (
            !$this->session->userdata('empid') ||
            $this->session->userdata('status') != 'active' ||
            $this->session->userdata('accesslevel') != 'EMPL'
        ) {
            $this->session->sess_destroy();
            redirect('Employee/Login');
        }
        // ... rest of function


        $empid = $this->session->userdata('empid');
        $this->load->model('EmployeeModel');

        $data['slips'] = $this->EmployeeModel->get_employee_salary_slips($empid);

        // Load the views
        $this->load->view('employee/employeeHeaderView');
        $this->load->view('employee/employeeSalarySlipsView', $data);
    }


    // --- Download/View Specific Slip (Handles both Employee and HR/Admin) ---
    public function viewMySlip($slip_id)
    {
        // 1. Basic Security Check (Must be logged in and active)
        if (!$this->session->userdata('empid') || $this->session->userdata('status') != 'active') {
            redirect('Employee/Login');
        }

        $logged_in_empid = $this->session->userdata('empid');
        $accesslevel = $this->session->userdata('accesslevel');
        $this->load->model('EmployeeModel');

        // 2. Fetch Slip Data Based on Access Level
        if ($accesslevel == 'HR' || $accesslevel == 'ADMIN') {
            // HR/Admin can view ANY slip. (Bypass the ownership check)
            $slip_data = $this->db->where('slip_id', $slip_id)->get('sesalaryslips')->row_array();
        } else {
            // Normal Employees can ONLY view their own slips. (Enforce ownership check)
            $slip_data = $this->EmployeeModel->get_slip_by_id($slip_id, $logged_in_empid);
        }

        // 3. If the slip exists and they have permission to view it
        if ($slip_data) {

            // IMPORTANT: Fetch the details of the person who OWNS the slip, not the person viewing it!
            $target_empid = $slip_data['seemp_id'];
            $emp_details = $this->EmployeeModel->get_employee_with_id($target_empid)[0];

            // Re-attach the employee's personal details to the array for the Print View
            $slip_data['emp_name'] = $emp_details->seempd_name;
            $slip_data['designation'] = $emp_details->seempd_designation;
            $slip_data['branch'] = $emp_details->seemp_branch;

            // Security Headers to prevent browser caching of sensitive financial data
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
            $this->output->set_header('Pragma: no-cache');

            // Load the exact same Print View
            $this->load->view('hr/salarySlipPrintView', $slip_data);
        } else {
            show_404(); // Slip doesn't exist or employee is trying to view someone else's slip
        }
    }
    function EmployeeAttendence()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'EMPL'
        ) {
            $this->load->model('EmployeeModel');
            $attendeces = $this->EmployeeModel->get_all_loginlog_for_thisempid();

            $data = array(
                'attendence' => $attendeces
            );

            $this->load->view('employee/employeeHeaderView');
            $this->load->view('employee/employeeAttendenceView.php', $data);
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }

    function EmployeeRequest()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'EMPL'
        ) {
            $this->load->model('RequestsModel');
            $data = $this->input->post();
            $postdata = $this->security->xss_clean($data);

            if (isset($postdata['action']) && trim($postdata['action']) == 'requestsubmit') {
                $config = array(
                    array(
                        'field' => 'startdate',
                        'label' => 'StartDate',
                        'rules' => 'required'
                    ),
                    array(
                        'field' => 'enddate',
                        'label' => 'End Date',
                        'rules' => 'required|callback_check_end_date'
                    ),
                    array(
                        'field' => 'reason',
                        'label' => 'Reason',
                        'rules' => 'required|in_list[Medical,Leave,Personal,Business]',
                        'errors' => array(
                            'required' => 'You must provide a valid %s.',
                        ),
                    ),
                    array(
                        'field' => 'summary',
                        'label' => 'Summary',
                        'rules' => 'required',
                        'errors' => array(
                            'required' => 'You must provide a %s.',
                        ),
                    ),
                );

                $this->form_validation->set_rules($config);

                // Properly checks if validation passed
                if ($this->form_validation->run() == TRUE) {
                    // Save to database
                    $this->RequestsModel->addRequest($postdata);


                    $this->session->set_flashdata('success', 'Request submitted successfully.');

                    // Redirect back to kill the POST request
                    redirect('Employee/EmployeeRequest');
                }
            }

            $all_requests = $this->RequestsModel->get_requestes_for_thisempid();

            $data = array(
                'requests' => $all_requests
            );

            $this->load->view('employee/employeeHeaderView');
            $this->load->view('employee/employeeRequestView.php', $data);
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }
    // CUSTOM VALIDATION CALLBACK
    public function check_end_date($enddate)
    {

        $startdate = $this->input->post('startdate');
        if (!empty($startdate) && !empty($enddate)) {


            if (strtotime($enddate) < strtotime($startdate)) {
                $this->form_validation->set_message('check_end_date', 'The End Date cannot be earlier than the Start Date.');
                return FALSE;
            }
        }

        return TRUE;
    }

    // Added code for sending Request leave reminders to HR.
    public function sendLeaveReminder($reqId)
    {
        if (!$this->session->has_userdata('empid')) {
            echo json_encode(['status' => 'error']);
            return;
        }
        $this->load->model('RequestsModel');
        $success = $this->RequestsModel->set_reminder($reqId);
        echo json_encode(['status' => $success ? 'success' : 'error']);
    }
    function ChangePassword()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') == 'active' &&
            $this->session->userdata('accesslevel') == 'EMPL'
        ) {
            $post = $this->input->post();
            $postdata = $this->security->xss_clean($post);

            $this->load->model('EmployeeModel');

            if (
                isset($postdata['oldpass']) &&
                isset($postdata['newpass']) &&
                isset($postdata['confirmpass']) &&
                trim($postdata['newpass']) == trim($postdata['confirmpass'])
            ) {
                $config = array(
                    array(
                        'field' => 'oldpass',
                        'label' => 'OldPassword',
                        'rules' => 'required'
                    ),
                    array(
                        'field' => 'newpass',
                        'label' => 'NewPassword',
                        'rules' => 'required'
                    ),
                    array(
                        'field' => 'confirmpass',
                        'label' => 'ConfirmPassword',
                        'rules' => 'required',
                        'errors' => array(
                            'required' => 'You must provide a valid %s.',
                        ),
                    ),
                );

                $this->form_validation->set_rules($config);
                $this->form_validation->run();

                $errors = validation_errors();
                if ($errors == FALSE) {
                    $issuccess = $this->EmployeeModel->change_employee_password($postdata['oldpass'], $postdata['newpass']);
                    $_POST = [];

                    if ($issuccess['code'] == 0) {
                        $this->Logout();
                    } else {
                        $this->load->view('alertView', ['heading' => 'Error Changing Password']);
                        $this->Dashboard();
                    }
                }
            } else {
                $this->load->view('alertView', ['heading' => 'Password Match Error.']);
                $this->Dashboard();
            }
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }

    // HR Dashboard
    function HRDashboard()
    {
        if ($this->session->userdata('accesslevel') == 'HR' && $this->session->userdata('status') == 'active') {

            // 1. Load Required Models
            $this->load->model('EmployeeModel');
            $this->load->model('AttendanceModel');
            $this->load->model('RequestsModel');
            $this->load->model('jobApplicationModel');

            $empid = $this->session->userdata('empid');

            // Fetch Logged-in HR Details
            $emp_details = $this->EmployeeModel->get_employee_with_id($empid);
            if (!empty($emp_details) && !empty($emp_details[0]->seempd_name)) {
                $this->session->set_userdata('empname', $emp_details[0]->seempd_name);
            } else {
                $this->session->set_userdata('empname', 'HR Manager');
            }

            $data['todayAttendance'] = $this->AttendanceModel->get_today_login_logout($empid);

            // Fetch Dashboard Statistics
            $data['total_staff'] = $this->EmployeeModel->get_total_staff_count();
            $data['pending_count'] = $this->RequestsModel->get_pending_requests_count();
            $data['new_applicants'] = $this->jobApplicationModel->get_new_applicants_count();
            $data['present_today'] = $this->AttendanceModel->get_present_today_count();

            // Fetch Lists for Dashboard Tables
            $data['today_attendance'] = $this->AttendanceModel->get_today_attendance_list();
            $data['recent_leaves'] = $this->RequestsModel->get_pending_leaves_with_balance();
            $data['recent_applicants'] = $this->jobApplicationModel->get_recent_applicants(5);

            // Load Views
            $this->load->view('hr/hrHeaderView');
            $this->load->view('hr/hrDashboardView', $data);
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }
    /**
     * Process Leave Decisions from Dashboard
     */
    public function updateLeaveStatus($id, $status)
    {
        // Authorization Check
        if ($this->session->userdata('accesslevel') != 'HR') {
            show_error('Unauthorized access', 403);
        }

        $valid_statuses = ['approved', 'rejected'];
        if (in_array($status, $valid_statuses)) {
            // Update the seemprequests table status
            $this->db->where('seemrq_id', $id);
            $this->db->update('seemprequests', ['seemrq_status' => $status]);

            $this->session->set_flashdata('msg', 'Leave request has been ' . $status);
        }

        // Redirect back to the dashboard to refresh the table
        redirect('Employee/Dashboard');
    }

    public function viewEmployeeLeaveRequests()
    {
        if (
            $this->session->has_userdata('empid') &&
            $this->session->has_userdata('email') &&
            $this->session->has_userdata('accesslevel') &&
            $this->session->has_userdata('branch') &&
            $this->session->has_userdata('status') &&
            $this->session->userdata('status') == 'active' &&
            ($this->session->userdata('accesslevel') == 'ADMIN' || $this->session->userdata('accesslevel') == 'HR')
        ) {

            $this->load->model('RequestsModel');

            $postd = $this->input->post();

            //  HANDLE STATUS UPDATE (Approve / Reject)
            if ($postd) {
                $postdata = $this->security->xss_clean($postd);

                $id = trim($postdata['request_id'] ?? '');
                $status = trim($postdata['status'] ?? '');

                if ($id != '' && $status != '') {

                    $this->RequestsModel->update_request_status($id, $status);

                    $this->session->set_flashdata('msg', 'Request updated successfully.');
                    redirect('Employee/viewEmployeeLeaveRequests');
                }
            }

            //  FETCH ALL REQUESTS
            $data['requests'] = $this->RequestsModel->get_all_requests();

            //  LOAD VIEW (HR / ADMIN)
            if ($this->session->userdata('accesslevel') == 'HR') {
                $this->load->view('hr/hrHeaderView');
                $this->load->view('hr/hrLeaveRequestView', $data);
            } else {
                $this->load->view('employee/adminHeaderView');
                $this->load->view('employee/adminEmployeeRequestView', $data);
            }
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }

    // Process Hiring Decisions from Dashboard
    public function updateHiringStatus($id, $state)
    {
        if ($this->session->userdata('accesslevel') != 'HR') {
            show_error('Unauthorized', 403);
        }

        // Added 'hired' to valid states
        $valid_states = ['applied', 'pending', 'selected', 'rejected', 'hired'];
        if (in_array($state, $valid_states)) {
            $this->db->where('sejoba_id', $id);
            $this->db->update('sejobapplicant', ['sejoba_state' => $state]);
        }

        redirect('Employee/Dashboard');
    }
    // Logout Function
    function Logout()
    {
        $this->load->model('EmployeeModel');
        // $sion = $this->session->userdata('empid');
        // $this->EmployeeModel->update_log_current_state($sion, 'logout');
        $this->session->unset_userdata(['empid', 'email', 'accesslevel', 'branch', 'status']);
        $this->session->sess_destroy();
        $this
            ->output
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate')
            ->set_header('Pragma: no-cache');
        redirect('Employee/Login');
    }

    /**
     * Get Employee for Edit via AJAX
     */
    public function getEmployeeForEdit($empid = '')
    {
        if (!$this->session->has_userdata('empid') || $this->session->userdata('accesslevel') != 'ADMIN') {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $employee = $this->EmployeeModel->get_employee_full_details($empid);

        if (!empty($employee)) {
            echo json_encode(['success' => true, 'data' => $employee[0]]);
        } else {
            echo json_encode(['error' => 'Employee not found']);
        }
    }

    public function sendInterviewInvite()
    {
        $this->load->model('InterviewModel');
        $this->load->model('jobApplicationModel');
        // Capture the new dropdown value
        $round = $this->input->post('interview_round', TRUE);
        $applicant_id = $this->input->post('applicant_id', TRUE);

        $email = $this->input->post('email', TRUE);
        $name = $this->input->post('name', TRUE);
        $position = $this->input->post('position', TRUE);
        $phone = $this->input->post('phone', TRUE);
        $applicant_id = $this->input->post('applicant_id', TRUE);
        $date = $this->input->post('interview_date', TRUE);
        $time = $this->input->post('interview_time', TRUE);
        $location = $this->input->post('location', TRUE);

        if (empty($applicant_id) || empty($email) || empty($date)) {
            $this->session->set_flashdata('error', 'Missing required interview details. Please try again.');
            redirect('Employee/viewJobApplicants');
            return;
        }

        $hr_name = $this->session->userdata('empname') ?? 'HR Team';

        $interview_data = array(
            'date' => $this->input->post('interview_date', TRUE),
            'time' => $this->input->post('interview_time', TRUE),
            'location' => $this->input->post('location', TRUE),
            'scheduled_by' => $this->session->userdata('empname') ?? 'HR Team',
            'round_status' => $round // Pass chosen round to model
        );

        $db_result = $this->jobApplicationModel->schedule_interview($applicant_id, $interview_data);

        if ($db_result['code'] == 0) {
            $this->InterviewModel->send_interview_email(
                $email,
                $name,
                $position,
                $interview_data['date'],
                $interview_data['time'],
                $interview_data['location'],
                $phone,
                $interview_data['scheduled_by'],
                $round
            );
            $this->session->set_flashdata('msg', $round . ' scheduled successfully!');
        }
        redirect('Employee/viewJobApplicants');
    }

    public function viewScheduledInterviews()
    {
        if (!$this->session->has_userdata('empid') || $this->session->userdata('accesslevel') != 'HR') {
            redirect('login');
            return;
        }

        $this->load->model('jobApplicationModel');
        $data['interviews'] = $this->jobApplicationModel->get_interview_scheduled_applicants();

        $this->load->view('hr/hrHeaderView');
        $this->load->view('hr/scheduledInterviewsView', $data);
    }

    //Admin manage product section
    public function products()
    {
        $this->load->model('ProductsModel');

        $data['products'] = $this->ProductsModel->get_all_products();
        $this->load->view('employee/adminHeaderView');
        $this->load->view('employee/adminProductListView', $data);
    }

    // admin add new product for his website.
    public function addProduct()
    {
        $name = $this->input->post('productName');
        $info = $this->input->post('productInfo');
        $link = $this->input->post('productLink');

        $config['upload_path'] = './uploads/products/';
        $config['allowed_types'] = 'jpg|png|jpeg';
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('productImg')) {

            $img = $this->upload->data('file_name');

            $data = [
                'seprod_name' => $name,
                'seprod_img' => $img,
                'seprod_inf' => $info,
                'seprod_link' => $link
            ];

            $this->db->insert('seproducts', $data);

            $this->session->set_flashdata('msg', 'Product Added Successfully');
        }

        $this->load->view('employee/adminHeaderView');
        $this->load->view('employee/adminManageProductView');
    }

    // DELETE PRODUCT
    public function deleteProduct($id)
    {
        $this->load->model('ProductsModel');

        $this->ProductsModel->delete_product($id);

        $this->session->set_flashdata('msg', 'Product Deleted');

        redirect('Employee/products');
    }

    // EDIT PRODUCT
    public function editProduct($id)
    {
        $this->load->model('ProductsModel');

        $data['product'] = $this->ProductsModel->get_product($id);

        $this->load->view('employee/adminHeaderView');
        $this->load->view('employee/adminManageProductView', $data);
    }

    // UPDATE PRODUCT
    public function updateProduct($id)
    {
        $this->load->model('ProductsModel');

        $name = $this->input->post('productName');
        $info = $this->input->post('productInfo');
        $link = $this->input->post('productLink');

        $config['upload_path'] = './uploads/products/';
        $config['allowed_types'] = 'jpg|jpeg|png';

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('productImg')) {
            $uploadData = $this->upload->data();
            $img = $uploadData['file_name'];

            $data = [
                'seprod_name' => $name,
                'seprod_inf' => $info,
                'seprod_link' => $link,
                'seprod_img' => $img
            ];
        } else {
            $data = [
                'seprod_name' => $name,
                'seprod_inf' => $info,
                'seprod_link' => $link
            ];
        }

        $this->ProductsModel->update_product($id, $data);

        $this->session->set_flashdata('msg', 'Product Updated Successfully');

        redirect('Employee/products');
    }
    public function viewJobs()
    {
        $access = $this->session->userdata('accesslevel');
        if ($this->session->userdata('status') == 'active' && ($access == 'ADMIN' || $access == 'HR')) {

            $this->load->model('JobsModel');
            $data['jobs'] = $this->JobsModel->get_all_jobs();

            $header = ($access == 'HR') ? 'hr/hrHeaderView' : 'employee/adminHeaderView';
            $this->load->view($header);
            $this->load->view('hr/hrManageJobsView', $data);
        } else {
            $this->session->sess_destroy();
            $this->load->view('errors/invalidAccessView');
        }
    }

    public function saveJob()
    {
        $this->load->model('JobsModel');

        $jobData = [
            'sejob_jobtitle' => $this->input->post('jobTitle', TRUE),
            'sejob_experience' => $this->input->post('experience', TRUE),
            'sejob_address' => $this->input->post('address', TRUE),
            'sejob_workinghours' => $this->input->post('workingHours', TRUE),
            'sejob_skills' => $this->input->post('skills', TRUE),
            'sejob_salary' => $this->input->post('salary', TRUE),
            'sejob_desc' => $this->input->post('description', TRUE),
            'sejob_urgency' => $this->input->post('urgency', TRUE),
            'sejob_state' => $this->input->post('status', TRUE),
            'sejob_dateofpost' => date('Y-m-d')
        ];

        $job_id = $this->input->post('job_id', TRUE);

        if (!empty($job_id)) {
            $this->db->where('sejob_id', $job_id);
            $this->db->update('sejobs', $jobData);
            $this->session->set_flashdata('msg', 'Job updated successfully');
        } else {
            $this->db->insert('sejobs', $jobData);
            $this->session->set_flashdata('msg', 'New job posted successfully');
        }
        redirect('Employee/viewJobs');
    }

    public function deleteJob($id)
    {
        $this->db->where('sejob_id', $id);
        $this->db->delete('sejobs');
        $this->session->set_flashdata('msg', 'Job posting removed');
        redirect('Employee/viewJobs');
    }

    // --- AJAX ATTENDANCE & GEOFENCING ROUTE ---
    public function SubmitAttendanceAjax()
    {
        // 1. Check Session
        if (!$this->session->has_userdata('empid')) {
            echo json_encode(['status' => 'error', 'message' => 'Your session has expired. Please login again.']);
            return;
        }

        // 2. Get POST data
        $action = $this->input->post('action');
        $empid = $this->session->userdata('empid');

        // 3. Detect the Device
        $this->load->library('user_agent');
        if ($this->agent->is_mobile()) {
            $device = 'Mobile (' . $this->agent->mobile() . ')';
        } elseif ($this->agent->is_browser()) {
            $device = 'Desktop/Laptop (' . $this->agent->browser() . ')';
        } else {
            $device = 'Unknown Device';
        }

        // 4. Get the IP Address (With aggressive fallback)
        $ipAddress = $this->input->ip_address();

        if (empty($ipAddress) || $ipAddress === '0.0.0.0') {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                // If multiple IPs are passed, grab the first one
                $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $ipAddress = trim($ipList[0]);
            } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
                $ipAddress = $_SERVER['REMOTE_ADDR'];
            } else {
                $ipAddress = 'Unknown IP';
            }
        }

        // Ensure the IP isn't too long for the database (IPv6 safety)
        $ipAddress = substr($ipAddress, 0, 45);

        // 5. Save to Database (THIS IS WHAT WAS MISSING)
        $this->load->model('EmployeeModel');

        $log_result = $this->EmployeeModel->update_log_current_state($empid, $action, $device, $ipAddress);

        if ($log_result['code'] == 0) {
            $msg = ($action == 'login') ? "Clocked IN successfully!" : "Clocked OUT successfully!";
            echo json_encode([
                'status' => 'success',
                'message' => $msg,
                'device' => $device,
                'ip' => $ipAddress
            ]);
        } else {
            $error_msg = ($action == 'login') ? 'You have already clocked in today.' : 'Could not clock out. Have you clocked in yet?';
            echo json_encode(['status' => 'error', 'message' => $error_msg]);
        }
    }
    // Load Salary Management View (HR/ADMIN Portal)
    public function salaryManagement()
    {
        if ($this->session->userdata('accesslevel') == 'HR' || $this->session->userdata('accesslevel') == 'ADMIN') {
            $this->load->model('EmployeeModel');

            // 1. Determine which month HR is looking at (Defaults to current month)
            $selected_month = $this->input->get('month') ? $this->input->get('month') : date('Y-m');

            // 2. Fetch all active regular employees (No Admin/HR)
            $data['employees'] = $this->EmployeeModel->get_payroll_employees();

            // 3. Fetch all slips already generated for this specific month
            $data['monthly_slips'] = $this->EmployeeModel->get_slips_by_month($selected_month);
            $data['selected_month'] = $selected_month;

            // 4. Calculate Stats for the Dashboard
            $data['total_emps'] = count($data['employees']);
            $data['processed_count'] = count($data['monthly_slips']);
            $data['pending_count'] = $data['total_emps'] - $data['processed_count'];

            // 5. Load appropriate header based on role
            $header = ($this->session->userdata('accesslevel') == 'HR') ? 'hr/hrHeaderView' : 'employee/adminHeaderView';
            $this->load->view($header);
            $this->load->view('hr/hrSalaryManagementView', $data);
        } else {
            redirect('Employee/Login');
        }
    }

    public function generatePayslip()
    {
        if ($this->session->userdata('accesslevel') == 'HR' || $this->session->userdata('accesslevel') == 'ADMIN') {
            $post = $this->input->post();

            if ($post) {
                $this->load->model('EmployeeModel');
                $data = $this->security->xss_clean($post);

                // Calculate Totals Securely on Server
                $data['gross_earnings'] = $data['basic'] + $data['transport'] + $data['incentive'] + $data['overtime'] + $data['round_off'];
                $data['total_deductions'] = $data['pf'] + $data['esi_deduction'] + $data['prof_tax'] + $data['late_fees'] + $data['loss_of_pay'] + $data['loan'];
                $data['net_salary'] = $data['gross_earnings'] - $data['total_deductions'];
                // MAJOR ERROR FIX: Stop execution if Net Salary is negative
                if ($data['net_salary'] < 0) {
                    $this->session->set_flashdata('error', 'Error: Deductions exceed Gross Earnings. Net Salary cannot be negative.');
                    redirect('Employee/salaryManagement');
                    return;
                }
                $db_data = $data;
                unset($db_data['emp_name'], $db_data['designation'], $db_data['branch']);

                if ($this->EmployeeModel->slip_already_exists($db_data['seemp_id'], $db_data['slip_month'])) {
                    $this->session->set_flashdata('error', 'Slip for this month already exists.');
                    redirect('Employee/salaryManagement');
                }

                // --- THE FIX STARTS HERE ---
                $this->db->insert('sesalaryslips', $db_data);

                // Capture the ID of the row just inserted
                $data['slip_id'] = $this->db->insert_id();
                // --- THE FIX ENDS HERE ---

                $this->load->view('hr/salarySlipPrintView', $data);
            }
        } else {
            redirect('Employee/Login');
        }
    }
    

    public function google_login()
    {
        // 1. Initialize Google Client (Requires composer require google/apiclient)
        $client = new Google\Client();
        $client->setClientId(getenv('LOGIN_CLIENT_ID')); // Use environment variable for security
        $client->setClientSecret(getenv('LOGIN_CLIENT_SECRET')); // Use environment variable for security
        $client->setRedirectUri(base_url('Employee/google_login'));
        $client->addScope("email");
        $client->addScope("profile");

        if (!isset($_GET['code'])) {
            redirect($client->createAuthUrl());
        } else {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token);
            $google_oauth = new Google\Service\Oauth2($client);
            $google_info = $google_oauth->userinfo->get();

            $email = $google_info->email;

            // 2. Check if email exists in your seemployee table
            $this->load->model('EmployeeModel');
            $this->db->where('seemp_email', $email);
            $query = $this->db->get('seemployee');
            $user = $query->row();

            if ($user) {
                if ($user->seemp_status == 'active') {
                    $sdata = array(
                        'email' => $user->seemp_email,
                        'status' => $user->seemp_status,
                        'empid' => $user->seemp_id,
                        'accesslevel' => $user->seemp_acesslevel,
                        'branch' => $user->seemp_branch,
                        'lastlogin' => $user->seemp_lastlogin,
                    );
                    $this->session->set_userdata($sdata);
                    redirect('Employee/Dashboard');
                } else {
                    // Account exists but is inactive
                    $this->session->set_flashdata('login_error', 'Your account is currently inactive. Contact Admin.');
                    redirect('Employee/Login');
                }
            } else {
                // THE FIX: Account does not exist in DB
                $this->session->set_flashdata('login_error', 'This email is not registered in our system. Please contact HR.');
                redirect('Employee/Login');
            }
        }
    }
}
