<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manager extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // 1. Strict Security Guard: Only MANAGER access level allowed
        if (
            !$this->session->has_userdata('empid') ||
            $this->session->userdata('status') != 'active' ||
            $this->session->userdata('accesslevel') != 'MANAGER'
        ) {
            $this->session->sess_destroy();
            redirect('Employee/Login');
        }

        // Prevent caching
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
    }

    public function Dashboard()
    {
        $this->load->model('EmployeeModel');
        $this->load->model('AttendanceModel');
        $this->load->model('ProjectsModel');

        $manager_branch = $this->session->userdata('branch'); // e.g., 'KOLKATA'

        // 1. Get Total Branch Staff
        $branch_employees = $this->EmployeeModel->get_employees_by_branch($manager_branch);
        $data['total_staff'] = count($branch_employees);

        // 2. Get Branch Attendance for Today
        $today = date('Y-m-d');
        $this->db->select('e.seemp_id');
        $this->db->from('seemployeeloginlog l');
        $this->db->join('seemployee e', 'l.seemp_logempid = e.seemp_id', 'left');
        $this->db->where('l.seemp_logdate', $today);
        $this->db->where('e.seemp_branch', $manager_branch);
        $this->db->group_by('e.seemp_id');
        $data['present_today'] = $this->db->get()->num_rows();

        // 3. Get Project Stats (Managers have full access to projects as requested)
        $data['running_projects'] = $this->ProjectsModel->count_running_projects();
        $data['pending_projects'] = $this->ProjectsModel->count_pending_projects();

        $data['branch_name'] = ucfirst(strtolower($manager_branch));

        // Load the manager views
        $this->load->view('manager/managerHeaderView');
        $this->load->view('manager/managerDashboardView', $data);
    }

    // View Employees (Filtered strictly by the Manager's Branch)
    public function viewEmployee()
    {
        $this->load->model('EmployeeModel');
        $manager_branch = $this->session->userdata('branch'); // e.g., 'KOLKATA'

        // Fetch only employees matching the manager's branch
        // Note: You will need to add this function to your EmployeeModel if it doesn't exist
        $data['employees'] = $this->EmployeeModel->get_employees_by_branch($manager_branch);

        $this->load->view('manager/managerHeaderView');
        $this->load->view('manager/managerEmployeesView', $data);
    }

    public function RegisterEmployee()
    {
        $this->load->view('manager/managerHeaderView');
        $this->load->view('manager/managerEmployeeRegistrationView');
    }

    // Process New Employee Registration
    public function addEmployee()
    {
        $this->load->library('upload');
        $this->load->model('EmployeeModel');
        $this->load->library('form_validation');

        // 1. Set Validation Rules
        $this->form_validation->set_rules('empName', 'Employee Name', 'required|trim');
        $this->form_validation->set_rules('empid', 'Employee ID', 'required|trim|is_unique[seemployee.seemp_id]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[seemployee.seemp_email]');
        $this->form_validation->set_rules('phone', 'Phone', 'required|numeric|exact_length[10]');
        $this->form_validation->set_rules('aadhar', 'Aadhar', 'required|numeric|exact_length[12]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules(
            'salary',
            'Salary',
            'required|numeric|greater_than[0]|less_than_equal_to[9999999.99]',
            array('less_than_equal_to' => 'Salary cannot exceed ₹9,999,999.99.')
        );

        // 2. Check Validation Result
        if ($this->form_validation->run() == FALSE) {
            $error_msg = strip_tags(validation_errors('', "\n"));
            $this->session->set_flashdata('msg', "Validation Failed:\n" . $error_msg);
            redirect('Manager/RegisterEmployee');
            return;
        }

        // 3. Prepare Upload Configuration
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|doc|docx';
        $config['max_size'] = 5120; // 5MB limit
        $config['encrypt_name'] = TRUE;
        $this->upload->initialize($config);

        // 4. Handle Photo Upload
        $photo_name = '';
        if (!empty($_FILES['photo']['name'])) {
            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                $this->session->set_flashdata('msg', '❌ Please upload an image smaller than 5MB.');
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

        // 5. Handle CV Upload (REQUIRED for new employees)
        $cv_name = '';
        if (empty($_FILES['cv']['name'])) {
            $this->session->set_flashdata('msg', 'Error: CV Document is required for new employees.');
            redirect('Manager/RegisterEmployee');
            return;
        } else if ($this->upload->do_upload('cv')) {
            $cv_name = $this->upload->data('file_name');
        } else {
            $this->session->set_flashdata('msg', 'CV Upload Error: ' . strip_tags($this->upload->display_errors('', '')));
            redirect('Manager/RegisterEmployee');
            return;
        }

        $formData = $this->input->post();
        $manager_branch = $this->session->userdata('branch'); // Get manager's assigned branch

        // 6. FORCE SECURITY CONSTRAINTS
        // Ignore branch/access level from the frontend form to prevent hacking
        $employee = [
            'seemp_id' => $formData['empid'],
            'seemp_branch' => $manager_branch, // FORCED: Locked to manager's branch
            'seemp_email' => $formData['email'],
            'seemp_pass' => password_hash($formData['password'], PASSWORD_DEFAULT),
            'seemp_acesslevel' => 'EMPL'           // FORCED: Standard employee only
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
            $this->session->set_flashdata('msg', 'Employee Added Successfully to ' . ucfirst(strtolower($manager_branch)) . ' branch!');
            redirect('Manager/viewEmployee');
        } else {
            $this->session->set_flashdata('msg', 'Database Error adding employee. ID or Email may already exist.');
            redirect('Manager/RegisterEmployee');
        }
    }
    // 1. View Employee Details (Manager Level)
    public function viewEmployeeDetails()
    {
        $empid = $this->input->post('empid');
        if (!$empid) {
            redirect('Manager/viewEmployee');
        }

        $this->load->model('EmployeeModel');
        // Assuming you have this standard function in your model
        $data['info'] = $this->EmployeeModel->get_employee_by_id($empid);

        // SECURITY: Kick them out if they try to view an employee from another branch
        if (!$data['info'] || $data['info']->seemp_branch !== $this->session->userdata('branch')) {
            $this->session->set_flashdata('msg', 'Unauthorized access.');
            redirect('Manager/viewEmployee');
        }

        $this->load->view('manager/managerHeaderView');
        $this->load->view('manager/managerEmployeeDetailsView', $data);
    }

    // 2. Load the Edit Form
    public function editEmployee($empid = null)
    {
        // Accept ID from URL or POST form
        if (!$empid) {
            $empid = $this->input->post('empid');
        }

        $this->load->model('EmployeeModel');
        $emp = $this->EmployeeModel->get_employee_by_id($empid);

        // SECURITY: Prevent editing employees outside their branch
        if (!$emp || $emp->seemp_branch !== $this->session->userdata('branch')) {
            $this->session->set_flashdata('msg', 'Unauthorized to edit this employee.');
            redirect('Manager/viewEmployee');
        }

        $data['emp'] = $emp;
        $this->load->view('manager/managerHeaderView');
        // Reuses the manager registration view we already locked down
        $this->load->view('manager/managerEmployeeRegistrationView', $data);
    }

    // 3. Process the Update
    public function updateEmployee($empid)
    {
        $this->load->library('upload');
        $this->load->model('EmployeeModel');
        $this->load->library('form_validation');

        // SECURITY 1: Verify ownership before doing anything else
        $current_emp = $this->EmployeeModel->get_employee_by_id($empid);
        $manager_branch = $this->session->userdata('branch');

        if (!$current_emp || $current_emp->seemp_branch !== $manager_branch) {
            $this->session->set_flashdata('msg', 'Unauthorized update attempt.');
            redirect('Manager/viewEmployee');
            return;
        }

        // 1. Set Validation Rules
        $this->form_validation->set_rules('empName', 'Employee Name', 'required|trim');
        $this->form_validation->set_rules('phone', 'Phone', 'required|numeric|exact_length[10]');
        $this->form_validation->set_rules('aadhar', 'Aadhar', 'required|numeric|exact_length[12]');
        $this->form_validation->set_rules('salary', 'Salary', 'required|numeric|greater_than[0]');

        // Only validate unique email if the email was actually changed
        if ($this->input->post('email') != $current_emp->seemp_email) {
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[seemployee.seemp_email]');
        } else {
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        }

        if ($this->form_validation->run() == FALSE) {
            $error_msg = strip_tags(validation_errors('', "\n"));
            $this->session->set_flashdata('msg', "Validation Failed:\n" . $error_msg);
            redirect('Manager/editEmployee/' . $empid);
            return;
        }

        // 2. Prepare Upload Configuration
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|doc|docx';
        $config['max_size'] = 5120; // 5MB
        $config['encrypt_name'] = TRUE;
        $this->upload->initialize($config);

        // Retain existing files by default
        $photo_name = $current_emp->seempd_img;
        $cv_name = $current_emp->seempd_cv;

        // 3. Handle Photo Upload (Only if a new one is selected)
        if (!empty($_FILES['photo']['name'])) {
            if ($this->upload->do_upload('photo')) {
                $photo_name = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('msg', 'Photo Upload Error: ' . strip_tags($this->upload->display_errors('', '')));
                redirect('Manager/editEmployee/' . $empid);
                return;
            }
        }

        // 4. Handle CV Upload (Only if a new one is selected)
        if (!empty($_FILES['cv']['name'])) {
            if ($this->upload->do_upload('cv')) {
                $cv_name = $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('msg', 'CV Upload Error: ' . strip_tags($this->upload->display_errors('', '')));
                redirect('Manager/editEmployee/' . $empid);
                return;
            }
        }

        $formData = $this->input->post();

        // 5. FORCE SECURITY CONSTRAINTS
        $employee = [
            'seemp_email' => $formData['email'],
            'seemp_branch' => $manager_branch, // FORCED
            'seemp_acesslevel' => 'EMPL'           // FORCED
        ];

        // Only update password if they typed a new one
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

        // Call the Manager-specific update function we added to the model
        $result = $this->EmployeeModel->manager_update_employee($empid, $employee, $details);

        if ($result) {
            $this->session->set_flashdata('msg', 'Employee Updated Successfully!');
        } else {
            $this->session->set_flashdata('msg', 'Failed to update employee.');
        }
        redirect('Manager/viewEmployee');
    }
    // =========================================================
    // Attendance Section (Branch Restricted)
    // =========================================================
    public function viewAttendance()
    {
        $this->load->view('manager/managerHeaderView');
        $this->load->view('employee/adminAttendanceView');
    }

   public function fetchAttendanceAjax()
    {
        $start_date = $this->input->post('startdate');
        $end_date   = $this->input->post('enddate');
        $empid      = $this->input->post('empid');
        
        // SECURITY: Automatically use the Manager's branch from session
        $manager_branch = $this->session->userdata('branch'); 

        $this->load->model('AttendanceModel');
        
        // Pass the branch to the model to restrict data
        $records = $this->AttendanceModel->find_empid_with_daterange($empid, $start_date, $end_date, $manager_branch);

        echo json_encode([
            'status' => 'success',
            'data'   => $records
        ]);
    }

    // =========================================================
    // View Projects (Manager Level)
    // =========================================================
    public function viewProjects()
    {
        $this->load->model('ProjectsModel');

        // 1. Get the actual project records
        $data['projects'] = $this->ProjectsModel->getAllProjects();

        // 2. FIX: Add the missing 'total' variable that the view is asking for
            $data['total'] = $this->ProjectsModel->count_all_projects();
            $data['running'] = $this->ProjectsModel->count_running_projects();
            $data['pending'] = $this->ProjectsModel->count_pending_projects();
            $data['completed'] = $this->ProjectsModel->count_completed_projects();
        $this->load->view('manager/managerHeaderView');

        // 3. Load the view from the employee subfolder
        $this->load->view('employee/adminProjectsView', $data);
    }
}