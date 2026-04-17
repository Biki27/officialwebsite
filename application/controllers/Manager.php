<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
  Manager Controller:
  All methods are strictly branch-locked. The manager can only view/manage
  employees and attendance for their own branch. Branch value is always read
  from the session — never from user input — to prevent privilege escalation.
 */
class Manager extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        //  Strict Security Guard: MANAGER only ──
        if (
            !$this->session->has_userdata('empid') ||
            $this->session->userdata('status') != 'active' ||
            $this->session->userdata('accesslevel') != 'MANAGER'
        ) {
            $this->session->sess_destroy();
            redirect('Employee/Login');
            exit;
        }

        // Prevent browser caching ──
        $this->output
            ->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT')
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
            ->set_header('Pragma: no-cache')
            ->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    }

    // Dashboard
    public function Dashboard()
    {
        $this->load->model('EmployeeModel');
        $this->load->model('AttendanceModel');
        $this->load->model('ProjectsModel');
        $this->load->model('EmployeeDetailsModel');

        $manager_branch = $this->session->userdata('branch');

        // Set manager's real name in session (used by attendance view header)
        if (!$this->session->userdata('empname')) {
            $empdetails = $this->EmployeeDetailsModel->get_this_employee_details();
            if (!empty($empdetails) && !empty($empdetails[0]->seempd_name)) {
                $this->session->set_userdata('empname', $empdetails[0]->seempd_name);
            } else {
                $this->session->set_userdata('empname', 'Manager');
            }
        }

        $branch_employees = $this->EmployeeModel->get_employees_by_branch($manager_branch);
        $data['total_staff'] = count($branch_employees);

        $today = date('Y-m-d');
        $this->db->select('e.seemp_id');
        $this->db->from('seemployeeloginlog l');
        $this->db->join('seemployee e', 'l.seemp_logempid = e.seemp_id', 'left');
        $this->db->where('l.seemp_logdate', $today);
        $this->db->where('e.seemp_branch', $manager_branch);
        $this->db->group_by('e.seemp_id');
        $data['present_today'] = $this->db->get()->num_rows();

        $data['running_projects'] = $this->ProjectsModel->count_running_projects();
        $data['pending_projects'] = $this->ProjectsModel->count_pending_projects();
        $data['branch_name'] = ucfirst(strtolower($manager_branch));

        $this->load->view('manager/managerHeaderView');
        $this->load->view('manager/managerDashboardView', $data);
    }

    // View Employees (branch-restricted)
    public function viewEmployee()
    {
        $this->load->model('EmployeeModel');
        $manager_branch = $this->session->userdata('branch');

        $data['employees'] = $this->EmployeeModel->get_employees_by_branch($manager_branch);

        $this->load->view('manager/managerHeaderView');
        $this->load->view('manager/managerEmployeesView', $data);
    }

    // Register / Add Employee (automatically assigned to manager's branch)
    public function RegisterEmployee()
    {
        $this->load->view('manager/managerHeaderView');
        $this->load->view('manager/managerEmployeeRegistrationView');
    }

    public function addEmployee()
    {
        $this->load->library(['upload', 'form_validation']);
        $this->load->model('EmployeeModel');

        $this->form_validation->set_rules('empName', 'Employee Name', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('empid', 'Employee ID', 'required|trim|alpha_dash|max_length[20]|is_unique[seemployee.seemp_id]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[seemployee.seemp_email]');
        $this->form_validation->set_rules('phone', 'Phone', 'required|numeric|exact_length[10]');
        $this->form_validation->set_rules('aadhar', 'Aadhar', 'required|numeric|exact_length[12]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules(
            'salary',
            'Salary',
            'required|numeric|greater_than[0]|less_than_equal_to[9999999.99]',
            ['less_than_equal_to' => 'Salary cannot exceed ₹9,999,999.99.']
        );

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('msg', 'Validation Failed: ' . strip_tags(validation_errors('', "\n")));
            redirect('Manager/RegisterEmployee');
            return;
        }

        $config = [
            'upload_path' => './uploads/',
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|doc|docx',
            'max_size' => 5120,
            'encrypt_name' => TRUE,
        ];
        $this->upload->initialize($config);

        $photo_name = '';
        if (!empty($_FILES['photo']['name'])) {
            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                $this->session->set_flashdata('msg', 'Photo must be smaller than 5 MB.');
                redirect('Manager/RegisterEmployee');
                return;
            }
            if ($this->upload->do_upload('photo')) {
                $photo_name = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('msg', 'Photo Upload Error: ' . strip_tags($this->upload->display_errors('', '')));
                redirect('Manager/RegisterEmployee');
                return;
            }
        }

        $cv_name = '';
        if (empty($_FILES['cv']['name'])) {
            $this->session->set_flashdata('msg', 'CV Document is required.');
            redirect('Manager/RegisterEmployee');
            return;
        }
        if ($this->upload->do_upload('cv')) {
            $cv_name = $this->upload->data('file_name');
        } else {
            $this->session->set_flashdata('msg', 'CV Upload Error: ' . strip_tags($this->upload->display_errors('', '')));
            redirect('Manager/RegisterEmployee');
            return;
        }

        $formData = $this->input->post();
        $manager_branch = $this->session->userdata('branch');   // FORCED — never trust form

        $employee = [
            'seemp_id' => $formData['empid'],
            'seemp_branch' => $manager_branch,   // locked to manager's branch
            'seemp_email' => $formData['email'],
            'seemp_pass' => password_hash($formData['password'], PASSWORD_DEFAULT),
            'seemp_acesslevel' => 'EMPL',            // locked to standard employee
            'seemp_status'     => 'active'
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
            'seempd_permanent_date' => !empty($formData['permanentDate']) ? $formData['permanentDate'] : NULL,
            'seempd_address_permanent' => $formData['permAddress'],
            'seempd_address_current' => $formData['currentAddress'],
            'seempd_aadhar' => $formData['aadhar'],
            'seempd_pan' => $formData['pan'],
            'seempd_img' => $photo_name,
            'seempd_cv' => $cv_name,
        ];

        $result = $this->EmployeeModel->register_employee($employee, $details);

        if ($result['code'] == 0) {
            $this->session->set_flashdata('msg', 'Employee added to ' . ucfirst(strtolower($manager_branch)) . ' branch successfully!');
            redirect('Manager/viewEmployee');
        } else {
            $this->session->set_flashdata('msg', 'Database error. Employee ID or Email may already exist.');
            redirect('Manager/RegisterEmployee');
        }
    }

    // View Employee Details (branch-restricted)
    public function viewEmployeeDetails()
    {
        $empid = $this->input->post('empid', TRUE);
        $empid = trim($this->security->xss_clean($empid ?? ''));

        if (empty($empid)) {
            redirect('Manager/viewEmployee');
            return;
        }

        $this->load->model('EmployeeModel');
        $data['info'] = $this->EmployeeModel->get_employee_by_id($empid);

        // SECURITY: block access to employees from another branch
        if (!$data['info'] || $data['info']->seemp_branch !== $this->session->userdata('branch')) {
            $this->session->set_flashdata('msg', 'Unauthorized: employee not in your branch.');
            redirect('Manager/viewEmployee');
            return;
        }

        $this->load->view('manager/managerHeaderView');
        $this->load->view('manager/managerEmployeeDetailsView', $data);
    }

    // Edit Employee (branch-restricted)
    public function editEmployee($empid = null)
    {
        if (!$empid)
            $empid = $this->input->post('empid', TRUE);
        $empid = trim($this->security->xss_clean($empid ?? ''));

        if (empty($empid)) {
            redirect('Manager/viewEmployee');
            return;
        }

        $this->load->model('EmployeeModel');
        $emp = $this->EmployeeModel->get_employee_by_id($empid);

        if (!$emp || $emp->seemp_branch !== $this->session->userdata('branch')) {
            $this->session->set_flashdata('msg', 'Unauthorized: employee not in your branch.');
            redirect('Manager/viewEmployee');
            return;
        }

        $data['emp'] = $emp;
        $this->load->view('manager/managerHeaderView');
        $this->load->view('manager/managerEmployeeRegistrationView', $data);
    }

    // =========================================================
    // Update Employee (branch-restricted)
    // =========================================================
    public function updateEmployee($empid)
    {
        $this->load->library(['upload', 'form_validation']);
        $this->load->model('EmployeeModel');

        $empid = trim($this->security->xss_clean($empid ?? ''));
        $manager_branch = $this->session->userdata('branch');
        $current_emp = $this->EmployeeModel->get_employee_by_id($empid);

        // SECURITY: ownership check
        if (!$current_emp || $current_emp->seemp_branch !== $manager_branch) {
            $this->session->set_flashdata('msg', 'Unauthorized update attempt.');
            redirect('Manager/viewEmployee');
            return;
        }

        $this->form_validation->set_rules('empName', 'Employee Name', 'required|trim|max_length[100]');
        $this->form_validation->set_rules('phone', 'Phone', 'required|numeric|exact_length[10]');
        $this->form_validation->set_rules('aadhar', 'Aadhar', 'required|numeric|exact_length[12]');
        $this->form_validation->set_rules('salary', 'Salary', 'required|numeric|greater_than[0]');

        $posted_email = $this->input->post('email', TRUE);
        if (trim($posted_email) !== $current_emp->seemp_email) {
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[seemployee.seemp_email]');
        } else {
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        }

        if (!empty($this->input->post('password'))) {
            $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
        }

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('msg', 'Validation Failed: ' . strip_tags(validation_errors('', "\n")));
            redirect('Manager/editEmployee/' . $empid);
            return;
        }

        $config = [
            'upload_path' => './uploads/',
            'allowed_types' => 'gif|jpg|png|jpeg|pdf|doc|docx',
            'max_size' => 5120,
            'encrypt_name' => TRUE,
        ];
        $this->upload->initialize($config);

        $photo_name = $current_emp->seempd_img;
        $cv_name = $current_emp->seempd_cv;

        if (!empty($_FILES['photo']['name'])) {
            if ($this->upload->do_upload('photo')) {
                $photo_name = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('msg', 'Photo Error: ' . strip_tags($this->upload->display_errors('', '')));
                redirect('Manager/editEmployee/' . $empid);
                return;
            }
        }

        if (!empty($_FILES['cv']['name']) && $this->upload->do_upload('cv')) {
            $cv_name = $this->upload->data('file_name');
        }

        $formData = $this->input->post();

        $employee = [
            'seemp_email' => $formData['email'],
            'seemp_branch' => $manager_branch,   // FORCED
            'seemp_acesslevel' => 'EMPL',            // FORCED
        ];

        if (!empty($formData['password'])) {
            $employee['seemp_pass'] = password_hash($formData['password'], PASSWORD_DEFAULT);
        }

        $details = [
            'seempd_name' => $formData['empName'],
            'seempd_phone' => $formData['phone'],
            'seempd_designation' => $formData['designation'],
            'seempd_salary' => $formData['salary'],
            'seempd_project' => $formData['project'],
            'seempd_experience' => $formData['experience'],
            'seempd_dob' => $formData['dob'],
            'seempd_joiningdate' => $formData['joiningDate'],
            'seempd_permanent_date' => !empty($formData['permanentDate']) ? $formData['permanentDate'] : NULL,
            'seempd_address_permanent' => $formData['permAddress'],
            'seempd_address_current' => $formData['currentAddress'],
            'seempd_aadhar' => $formData['aadhar'],
            'seempd_pan' => $formData['pan'],
            'seempd_img' => $photo_name,
            'seempd_cv' => $cv_name,
        ];

        $result = $this->EmployeeModel->manager_update_employee($empid, $employee, $details);

        if ($result) {
            $this->session->set_flashdata('msg', 'Employee Updated Successfully!');
        } else {
            $this->session->set_flashdata('msg', 'Failed to update employee. Please try again.');
        }
        redirect('Manager/viewEmployee');
    }

    // =========================================================
    // Attendance — View Page (branch-locked)
    // =========================================================
    public function viewAttendance()
    {
        $this->load->model('AttendanceModel');

        $manager_branch = $this->session->userdata('branch');

        // Fetch the last 30 days by default for initial page load
        $start = date('Y-m-d', strtotime('-30 days'));
        $end = date('Y-m-d');

        // Pass branch to restrict records to manager's branch only
        $data['atten'] = $this->AttendanceModel->find_empid_with_daterange('', $start, $end, $manager_branch);

        $this->load->view('manager/managerHeaderView');
        $this->load->view('manager/managerAttendanceView', $data);
    }

    // =========================================================
    // Attendance — AJAX Endpoint (branch-locked, SECURITY CRITICAL)
    // =========================================================
    public function fetchAttendanceAjax()
    {
        // ── 1. JSON header ──
        $this->output->set_content_type('application/json');

        // ── 2. Auth guard ──
        if (
            !$this->session->has_userdata('empid') ||
            $this->session->userdata('status') !== 'active' ||
            $this->session->userdata('accesslevel') !== 'MANAGER'
        ) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized', 'data' => []]);
            return;
        }

        // ── 3. Sanitise inputs ──
        $empid = trim($this->security->xss_clean($this->input->post('empid') ?? ''));
        $start_date = trim($this->input->post('startdate', TRUE) ?? '');
        $end_date = trim($this->input->post('enddate', TRUE) ?? '');

        // ── 4. SECURITY: branch is ALWAYS from session — never from POST ──
        $manager_branch = $this->session->userdata('branch');

        // ── 5. Basic date sanity checks ──
        $today = date('Y-m-d');
        if (!empty($start_date) && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || $start_date > $today)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid start date.', 'data' => []]);
            return;
        }
        if (!empty($end_date) && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date) || $end_date > $today)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid end date.', 'data' => []]);
            return;
        }
        if (!empty($start_date) && !empty($end_date) && $start_date > $end_date) {
            echo json_encode(['status' => 'error', 'message' => 'Start date cannot be after end date.', 'data' => []]);
            return;
        }

        // ── 6. Validate Employee ID format (alphanumeric, hyphens, underscores, spaces) ──
        if (!empty($empid) && !preg_match('/^[a-zA-Z0-9 _\-]+$/', $empid)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid employee search term.', 'data' => []]);
            return;
        }

        // ── 7. Fetch from model — branch is enforced server-side ──
        $this->load->model('AttendanceModel');
        $records = $this->AttendanceModel->find_empid_with_daterange(
            $empid,
            $start_date,
            $end_date,
            $manager_branch   // ← always session branch, cannot be spoofed
        );

        // ── 8. Format dates/times for display ──
        foreach ($records as &$att) {
            $att->formatted_date = date('d-M-Y', strtotime($att->seemp_logdate));
            $att->formatted_login = date('h:i A', strtotime($att->seemp_logintime));
            $att->formatted_logout = (
                !empty($att->seemp_logouttime) &&
                $att->seemp_logouttime !== '0000-00-00 00:00:00'
            )
                ? date('h:i A', strtotime($att->seemp_logouttime))
                : '<span class="text-muted">Not Logged Out</span>';
        }
        unset($att);

        echo json_encode([
            'status' => 'success',
            'branch' => $manager_branch,
            'data' => $records,
        ]);
    }

    // =========================================================
    // Projects — View Page (read-only for managers)
    // =========================================================
    public function viewProjects()
    {
        $this->load->model('ProjectsModel');

        $data['projects'] = $this->ProjectsModel->getAllProjects();
        $data['total'] = $this->ProjectsModel->count_all_projects();
        $data['running'] = $this->ProjectsModel->count_running_projects();
        $data['pending'] = $this->ProjectsModel->count_pending_projects();
        $data['completed'] = $this->ProjectsModel->count_completed_projects();
        $data['controller'] = 'Manager'; // tells the shared view to use Manager/ routes

        $this->load->view('manager/managerHeaderView');
        $this->load->view('employee/adminProjectsView', $data);
    }
    // =========================================================
    // Project Management (Full Access for Manager)
    // ========================================================

    public function addProject()
    {
        $this->load->model('ProjectsModel');
        $data = $this->input->post();
        // Basic validation
        if (empty($data['projectName']) || empty($data['startDate']) || empty($data['deadlineDate']) || empty($data['clientName']) || empty($data['projectHead'])) {
            $this->session->set_flashdata('msg', 'All fields are required.');
            redirect('Manager/viewProjects');
            return;
        }
        $insert = [
            'seproj_name'     => $data['projectName'],
            'seproj_desc'     => $data['description'],
            'seproj_date'     => $data['startDate'],
            'seproj_deadline' => $data['deadlineDate'],
            'seproj_clientid' => $data['clientName'],
            'seproj_headid'   => $data['projectHead'],
            'seproj_price'    => $data['price'],
            'seproj_status'   => 'pending',
        ];
        $this->ProjectsModel->insert_project($insert);
        redirect('Manager/viewProjects');
    }

    public function updateProject()
    {
        $this->load->model('ProjectsModel');
        $data = $this->input->post();

        // ID comes from the hidden projectId field in the modal form
        $id = (int) ($data['projectId'] ?? 0);

        if (!$id) {
            $this->session->set_flashdata('msg', 'Error: Invalid project ID.');
            redirect('Manager/viewProjects');
            return;
        }

        $updateData = [
            'seproj_name'     => $data['projectName'],
            'seproj_desc'     => $data['description'],
            'seproj_date'     => $data['startDate'],
            'seproj_deadline' => $data['deadlineDate'],
            'seproj_clientid' => $data['clientName'],
            'seproj_headid'   => $data['projectHead'],
            'seproj_price'    => $data['price'],
            'seproj_status'   => $data['status'],
        ];

        if ($this->ProjectsModel->update_project($id, $updateData)) {
            $this->session->set_flashdata('msg', 'Success: Project details updated.');
        } else {
            $this->session->set_flashdata('msg', 'Error: Update failed.');
        }
        redirect('Manager/viewProjects');
    }

    public function deleteProject($id)
    {
        $this->load->model('ProjectsModel');
        $this->ProjectsModel->delete_project($id);
        redirect('Manager/viewProjects');
    }

    public function checkDuplicateProject()
    {
        $name = $this->input->post('name');
        $this->load->model('ProjectsModel');
        $existing = $this->ProjectsModel->getProjectByName($name);
        echo json_encode($existing);
    }
}