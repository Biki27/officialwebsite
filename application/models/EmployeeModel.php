<?php

defined('BASEPATH') or exit('No direct script access allowed');

class EmployeeModel extends CI_Model
{

    // function getallemployee_with_joins()
    // {
    //     $res = $this->db
    //         ->from('seemployee')
    //         ->join('seempdetails', 'seemployee.seemp_id = seempdetails.seempd_empid', 'left')
    //         ->join('sejobapplicant', 'seempdetails.seempd_jobaid = sejobapplicant.sejoba_id', 'left')
    //         ->get()
    //         ->result();

    //     return $res;
    // }
    function getallemployee_with_joins()
    {
        $res = $this->db
            ->from('seemployee')
            ->join('seempdetails', 'seemployee.seemp_id = seempdetails.seempd_empid', 'left')
            ->join('sejobapplicant', 'seempdetails.seempd_jobaid = sejobapplicant.sejoba_id', 'left')
            ->join('seempbankdetails', 'seemployee.seemp_id = seempbankdetails.sebank_empid', 'left') // Fetch Bank Details
            ->get()
            ->result();

        return $res;
    }

    function get_employee_with_id($empid = '')
    {
        if (trim($empid) == '') {
            return ['code' => 1];
        }

        $res = $this->db
            ->from('seemployee')
            ->join('seempdetails', 'seemployee.seemp_id = seempdetails.seempd_empid', 'left')
            ->join('sejobapplicant', 'seempdetails.seempd_jobaid = sejobapplicant.sejoba_id', 'left')
            ->where('seemployee.seemp_id', $empid)
            ->limit(1)
            ->get()
            ->result();

        return $res;
    }

    function get_employee_with_search($query = '', $status = '')
    {
        // 1. Explicitly select all from main table and joined tables
        $this->db->select('seemployee.*, seempdetails.*, sejobapplicant.sejoba_phone');
        $this->db->from('seemployee');

        // 2. Apply Joins
        $this->db->join('seempdetails', 'seemployee.seemp_id = seempdetails.seempd_empid', 'left');
        $this->db->join('sejobapplicant', 'seempdetails.seempd_jobaid = sejobapplicant.sejoba_id', 'left');

        // 3. Handle Text Query (If not empty)
        if (!empty(trim($query))) {
            $this->db->group_start();
            $this->db->like('seemployee.seemp_id', $query);
            $this->db->or_like('seemployee.seemp_email', $query);
            $this->db->or_like('seempdetails.seempd_name', $query);
            $this->db->or_like('seempdetails.seempd_designation', $query);
            $this->db->group_end();
        }

        // 4. Handle Status Filter (IMPORTANT: Use the table prefix)
        if (!empty(trim($status))) {
            $this->db->where('seemployee.seemp_status', strtolower($status));
        }

        // 5. Execute
        $result = $this->db->get()->result();

        return $result;
    }
    function check_if_employee_exist($username = '', $pass = '')
    {
        if (trim($username) == '' || trim($pass) == '') {
            return array('code' => 1);
        }

        $query = $this->db->from('seemployee')
            ->where('seemp_email', $username)
            ->limit('1')
            ->get();

        $res = $query->result();
        if (empty($res)) {
            $res += array(
                'code' => 1
            );
            return $res;
        }

        $res += array(
            'code' => 0
        );
        return $res;
    }


    function change_employee_password($oldpass = '', $newpass = '')
    {

        $empid = $this->session->userdata('empid');
        $info = $this->db->from('seemployee')->where('seemp_id =', $empid)->limit(1)->get()->result();

        if (password_verify($oldpass, $info[0]->seemp_pass)) {
            $this->db->trans_start();
            $condition = array(
                'seemp_id' => $empid,
                'seemp_email' => $info[0]->seemp_email,
                'seemp_status' => $info[0]->seemp_status
            );
            $data = array(
                'seemp_pass' => password_hash($newpass, PASSWORD_DEFAULT)
            );
            $issuccess = $this->db->where($condition)->update('seemployee', $data);
            if ($issuccess == TRUE && $this->db->affected_rows() == 1) {
                $this->db->trans_complete();
                return ['code' => 0];
            } else {
                $this->db->trans_rollback();
                return ['code' => 1];
            }
        } else {
            return ['code' => 1];
        }
    }

    function update_employee_table_with_today($empid = '', $email = '', $seqid = '', $status = '')
    {
        if (
            empty(trim($empid)) || empty(trim($email)) || empty(trim($seqid)) ||
            empty(trim($status)) || trim($status) == 'inactive'
        ) {
            return ['code' => 1];
        } else {

            $this->db->trans_begin();

            $condition = array(
                'seemp_id =' => $empid,
                'seemp_email =' => $email,
                'seseq_id =' => $seqid,
                'seemp_status =' => 'active'
            );
            $isupdated = $this->db->where($condition)->update('seemployee', ['seemp_lastlogin' => date('Y-m-d')]);

            if ($isupdated == TRUE && $this->db->affected_rows() == 1) {
                $this->db->trans_complete();
                return ['code' => 0];
            } else {
                $this->db->trans_rollback();
                return ['code' => 1, 'message' => 'Something is wrong Check Credentials or Multiple rows getting affected.'];
            }
        }
    }
    // Update Login/Logout Log with Device and Geolocation
    function update_log_current_state($empid = '', $action = 'login', $device = null, $ipAddress = null)
    {
        if (empty(trim($empid)) || empty(trim($action))) {
            return ['code' => 1];
        }

        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        // Check if an entry already exists for this employee for TODAY
        $this->db->where([
            'seemp_logempid' => $empid,
            'seemp_logdate' => $today
        ]);
        $existing_log = $this->db->get('seemployeeloginlog')->row();

        if ($action == 'login') {
            // If NO record exists for today, insert it.
            if (!$existing_log) {
                $data = [
                    'seemp_logempid' => $empid,
                    'seemp_logdate' => $today,
                    'seemp_logintime' => $now,
                    'seemp_device_info' => $device,
                    'seemp_ip_address' => $ipAddress // Saves Login IP
                ];
                $this->db->insert('seemployeeloginlog', $data);
                return ['code' => 0];
            } else {
                // Error: Already clocked in
                return ['code' => 1];
            }
        } else if ($action == 'logout') {
            // Find today's record and update the logout time AND logout IP
            if ($existing_log) {
                $this->db->where([
                    'seemp_logempid' => $empid,
                    'seemp_logdate' => $today
                ]);
                $this->db->update('seemployeeloginlog', [
                    'seemp_logouttime' => $now,
                    'seemp_logout_ip_address' => $ipAddress // <-- Saves Logout IP
                ]);
                return ['code' => 0];
            } else {
                // Error: Trying to clock out before clocking in
                return ['code' => 1];
            }
        }
        return ['code' => 1];
    }
    function get_all_loginlog_for_thisempid()
    {
        $empid = $this->session->userdata('empid');
        $res = $this->db->from('seemployeeloginlog')->where('seemp_logempid =', $empid)->get()->result();
        return $res;
    }

    // for add employee from admin panel
    public function register_employee($employee, $details)
    {

        $this->db->trans_start();

        // Insert into main employee table
        $this->db->insert('seemployee', $employee);

        // Insert into employee details table
        $this->db->insert('seempdetails', $details);

        // Update applicant if exists
        if (!empty($details['seempd_jobaid'])) {

            $this->db->where('sejoba_id', $details['seempd_jobaid']);

            $this->db->update(
                'sejobapplicant',
                ['sejoba_state' => 'selected']
            );
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return ['code' => 1];
        } else {

            return ['code' => 0];
        }
    }

    //  for update employee from admin panel
    public function update_employee($empid = '', $data = array())
    {
        if (empty($empid) || empty($data)) {
            return ['code' => 1, 'message' => 'Invalid parameters'];
        }

        $this->db->trans_start();

        // 1. HANDLE ID MIGRATION (If HR changed the Intern ID to a Permanent ID)
        $new_empid = $data['new_empid'];

        if ($empid !== $new_empid) {
            // Temporarily disable strict foreign key checks to safely move the data
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

            // Migrate the ID across all 4 connected tables instantly
            $this->db->where('seemp_id', $empid)->update('seemployee', ['seemp_id' => $new_empid]);
            $this->db->where('seempd_empid', $empid)->update('seempdetails', ['seempd_empid' => $new_empid]);
            $this->db->where('seemp_logempid', $empid)->update('seemployeeloginlog', ['seemp_logempid' => $new_empid]);
            $this->db->where('seemrq_empid', $empid)->update('seemprequests', ['seemrq_empid' => $new_empid]);

            // Re-enable strict rules
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        // 2. Update main table (seemployee) USING THE NEW ID
        $employee_data = [
            'seemp_email' => $data['email'],
            'seemp_branch' => strtoupper($data['branch']),
            'seemp_status' => strtolower($data['status']),
            'seemp_acesslevel' => strtoupper($data['accessLevel'])
        ];

        // Password logic: only update if user typed a new one
        $new_pass = $this->input->post('password');
        if (!empty($new_pass)) {
            $employee_data['seemp_pass'] = password_hash($new_pass, PASSWORD_DEFAULT);
        }

        $this->db->where('seemp_id', $new_empid); // Use new ID
        $this->db->update('seemployee', $employee_data);

        // 3. Update details table (seempdetails) USING THE NEW ID
        $details_data = [
            'seempd_name' => $data['empName'],
            'seempd_phone' => $data['phone'],
            'seempd_designation' => $data['designation'],
            'seempd_salary' => $data['salary'],
            'seempd_project' => $data['project'],
            'seempd_experience' => $data['experience'],
            'seempd_dob' => $data['dob'],
            'seempd_joiningdate' => $data['joiningDate'],
            'seempd_increment' => $data['increment'],
            'seempd_address_permanent' => $data['permAddress'],
            'seempd_address_current' => $data['currentAddress'],
            'seempd_aadhar' => $data['aadhar'],
            'seempd_pan' => $data['pan'],
            'seempd_img' => $data['photo'],
            'seempd_cv' => $data['cv']
        ];

        $this->db->where('seempd_empid', $new_empid); // Use new ID
        $this->db->update('seempdetails', $details_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['code' => 1, 'message' => 'Database Transaction Failed'];
        }
        return ['code' => 0, 'message' => 'Success'];
    }
    /**
     * Reset Employee Password
     */
    public function reset_employee_password($empid = '', $hashed_password = '')
    {
        if (empty($empid) || empty($hashed_password)) {
            return ['code' => 1, 'message' => 'Invalid parameters'];
        }

        $this->db->trans_start();

        $this->db->where('seemp_id', $empid);
        $this->db->update('seemployee', [
            'seemp_pass' => $hashed_password
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['code' => 1, 'message' => 'Password reset failed'];
        } else {
            return ['code' => 0, 'message' => 'Password reset successfully'];
        }
    }

    /**
     * Get Employee by ID with Full Details
     */
    public function get_employee_full_details($empid = '')
    {
        if (empty($empid)) {
            return [];
        }

        $this->db->select('*');
        $this->db->from('seemployee');
        $this->db->join('seempdetails', 'seemployee.seemp_id = seempdetails.seempd_empid', 'left');
        $this->db->where('seemployee.seemp_id', $empid);
        $query = $this->db->get();

        return $query->result();
    }

    // hr dashboard functions
    public function get_total_staff_count()
    {
        return $this->db->count_all('seemployee');
    }

    // bank details functions
    // Fetch an employee's bank details
    public function get_bank_details($empid)
    {
        return $this->db->where('sebank_empid', $empid)->get('seempbankdetails')->row();
    }

    // Insert or Update the bank details
    public function save_bank_details($data)
    {
        // Check if the record already exists
        $this->db->where('sebank_empid', $data['sebank_empid']);
        $query = $this->db->get('seempbankdetails');

        if ($query->num_rows() > 0) {
            // Update existing
            $this->db->where('sebank_empid', $data['sebank_empid']);
            return $this->db->update('seempbankdetails', $data);
        } else {
            // Insert new
            return $this->db->insert('seempbankdetails', $data);
        }
    }
    // --- Employee Salary Slip Functions ---

    // Get all salary slips for a specific employee
    public function get_employee_salary_slips($empid)
    {
        return $this->db->where('seemp_id', $empid)
            ->order_by('slip_id', 'DESC') // Newest first
            ->get('sesalaryslips')
            ->result();
    }

    // Get a specific slip (Ensure it belongs to the logged-in employee for security)
    public function get_slip_by_id($slip_id, $empid)
    {
        return $this->db->where('slip_id', $slip_id)
            ->where('seemp_id', $empid)
            ->get('sesalaryslips')
            ->row_array(); // Return as array to easily pass to the print view
    }
     
  // Check if a salary slip already exists for an employee for a specific month
    public function slip_already_exists($empid, $month)
    {
        $this->db->where('seemp_id', $empid);
        $this->db->where('slip_month', $month);
        $query = $this->db->get('sesalaryslips');
        
        // Returns true if a slip is found, false if it is safe to generate
        return $query->num_rows() > 0;
    }
    // Fetch all slips generated in a specific month, indexed by Employee ID
    public function get_slips_by_month($month_year)
    {
        $query = $this->db->where('slip_month', $month_year)->get('sesalaryslips')->result();
        $slips = [];
        foreach ($query as $row) {
            $slips[$row->seemp_id] = $row; // Key the array by Employee ID for instant lookup
        }
        return $slips;
    }
    // Fetch only active, regular employees for Payroll (Excludes HR & ADMIN)
    public function get_payroll_employees()
    {
        return $this->db
            ->from('seemployee')
            ->join('seempdetails', 'seemployee.seemp_id = seempdetails.seempd_empid', 'left')
            ->join('sejobapplicant', 'seempdetails.seempd_jobaid = sejobapplicant.sejoba_id', 'left')
            ->join('seempbankdetails', 'seemployee.seemp_id = seempbankdetails.sebank_empid', 'left')
            ->where('seemployee.seemp_acesslevel', 'EMPL') // Only regular employees
            ->where('seemployee.seemp_status', 'active')   // Only active employees
            ->get()
            ->result();
    }
}
