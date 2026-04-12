<?php

defined('BASEPATH') or exit('No direct script access allowed');

class EmployeeModel extends CI_Model
{


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
            return array('code' => 1); // Username/Pass empty
        }

        $query = $this->db->from('seemployee')
            ->where('seemp_email', $username)
            ->limit('1')
            ->get();

        $res = $query->result();

        if (empty($res)) {
            return array('code' => 1); // USERNAME NOT FOUND
        }

        // USER FOUND: Return the user object
        return array('code' => 0, 'user' => $res[0]);
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
    // Get All Attendance Records for an Employee DESCENDING (Newest First)
    function get_all_loginlog_for_thisempid()
    {
        $empid = $this->session->userdata('empid');
        $res = $this->db->from('seemployeeloginlog')
            ->where('seemp_logempid =', $empid)
            ->order_by('seemp_logdate', 'DESC') // Sort by date (Newest first)
            ->order_by('seemp_logintime', 'DESC') // Sort by time (Latest login first)
            ->get()
            ->result();
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
            'seemp_status' => !empty($data['status']) ? strtolower($data['status']) : 'active',
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
            'seempd_permanent_date' => $data['permanentDate'],
            'seempd_termination_date' => $data['terminationDate'],
            'seempd_termination_reason' => $data['terminationReason'],
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
            ->order_by('slip_id', 'ASC') // Newest first
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
    // Add this to EmployeeModel.php
    public function delete_salary_slip($slip_id)
    {
        return $this->db->where('slip_id', $slip_id)->delete('sesalaryslips');
    }
    // --- Increment History Functions ---

    // 1. Fetch increment history for a specific employee
    public function get_increment_history(string $empid): array
    {
        return $this->db
            ->where('inc_empid', $empid)
            ->order_by('inc_effective_date', 'DESC')
            ->get('seemp_increments')
            ->result();
    }

    // 2. Add a new increment and update current salary (Using DB Transactions for safety)
    public function add_salary_increment(array $data): array
    {
        $today = date('Y-m-d');
        $effective_date = $data['inc_effective_date'];
        $is_immediate = ($effective_date <= $today);

        $data['inc_status'] = $is_immediate ? 'applied' : 'pending';

        $this->db->trans_start();

        // A. Always insert the history record
        $this->db->insert('seemp_increments', $data);

        // B. Only update the live salary when the effective date has arrived
        if ($is_immediate) {
            $this->db->where('seempd_empid', $data['inc_empid']);
            $this->db->update('seempdetails', [
                'seempd_salary' => $data['new_salary'],
                'seempd_increment' => $data['inc_percentage'],
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return ['code' => 1, 'message' => 'Database transaction failed.', 'status' => $data['inc_status']];
        }

        $human_date = date('d M Y', strtotime($effective_date));
        $msg = $is_immediate
            ? 'Increment applied and salary updated immediately.'
            : "Increment scheduled. Salary will update automatically on {$human_date}.";

        return ['code' => 0, 'message' => $msg, 'status' => $data['inc_status']];
    }

    /**
     * Check if an increment already exists for a given employee
     * in the same calendar month as the supplied effective date.
     */
    public function increment_exists_this_month(string $empid, string $effective_date): bool
    {
        $ts = strtotime($effective_date);
        $first_day = date('Y-m-01', $ts);
        $last_day = date('Y-m-t', $ts);   // last day of that month

        $this->db->where('inc_empid', $empid);
        $this->db->where('inc_effective_date >=', $first_day);
        $this->db->where('inc_effective_date <=', $last_day);

        return $this->db->count_all_results('seemp_increments') > 0;
    }

    // 3. Apply all pending increments for a specific employee
    public function apply_pending_increments(string $empid): int
    {
        $today = date('Y-m-d');

        $pending = $this->db
            ->where('inc_empid', $empid)
            ->where('inc_status', 'pending')
            ->where('inc_effective_date <=', $today)
            ->order_by('inc_effective_date', 'ASC')   // oldest first → correct compounding
            ->get('seemp_increments')
            ->result();

        if (empty($pending)) {
            return 0;
        }

        $applied = 0;

        foreach ($pending as $inc) {
            $this->db->trans_start();

            // Update live salary to this increment's new_salary
            $this->db->where('seempd_empid', $empid);
            $this->db->update('seempdetails', [
                'seempd_salary' => $inc->new_salary,
                'seempd_increment' => $inc->inc_percentage,
            ]);

            // Mark this row as applied
            $this->db->where('inc_id', $inc->inc_id);
            $this->db->update('seemp_increments', ['inc_status' => 'applied']);

            $this->db->trans_complete();

            if ($this->db->trans_status() !== FALSE) {
                $applied++;
            }
        }

        return $applied;
    }
    public function get_yearly_increment_report($year)
    {
        $sql = "
            SELECT 
                e.seemp_id, 
                d.seempd_name, 
                d.seempd_designation, 
                d.seempd_salary, 
                d.seempd_joiningdate,
                
                -- Get the date of their latest increment this year
                (SELECT MAX(inc_effective_date) FROM seemp_increments 
                 WHERE inc_empid = e.seemp_id AND YEAR(inc_effective_date) = ?) as last_inc_date,
                 
                -- NEW: Count exactly HOW MANY increments they got this year
                (SELECT COUNT(inc_id) FROM seemp_increments 
                 WHERE inc_empid = e.seemp_id AND YEAR(inc_effective_date) = ?) as inc_count,
                 
                -- Sum of all money added this year
                (SELECT SUM(inc_amount) FROM seemp_increments 
                 WHERE inc_empid = e.seemp_id AND YEAR(inc_effective_date) = ?) as total_inc_amount,
                 
                -- The percentage of their most recent increment
                (SELECT inc_percentage FROM seemp_increments 
                 WHERE inc_empid = e.seemp_id AND YEAR(inc_effective_date) = ? 
                 ORDER BY inc_effective_date DESC LIMIT 1) as latest_percentage
                 
            FROM seemployee e
            JOIN seempdetails d ON e.seemp_id = d.seempd_empid
            WHERE e.seemp_status = 'active' 
            AND e.seemp_acesslevel = 'EMPL'
            ORDER BY d.seempd_name ASC
        ";

        return $this->db->query($sql, array($year, $year, $year, $year))->result();
    }

    // --- Bonus Management Functions ---
    // In EmployeeModel.php
    public function get_yearly_bonus_report($year)
    {
        $sql = "
    SELECT 
        e.seemp_id, 
        d.seempd_name, 
        d.seempd_salary,
        d.seempd_permanent_date, -- ADD THIS LINE
        b.bonus_amount, 
        b.bonus_date, 
        b.next_eligible_date, 
        b.bonus_reason, 
        b.bonus_status
    FROM seemployee e
    JOIN seempdetails d ON e.seemp_id = d.seempd_empid
    LEFT JOIN seemp_bonuses b ON e.seemp_id = b.bonus_empid 
        AND b.bonus_id = (
            SELECT MAX(bonus_id) 
            FROM seemp_bonuses 
            WHERE bonus_empid = e.seemp_id 
            AND YEAR(bonus_date) = ?
        )
    WHERE e.seemp_status = 'active' 
    AND e.seemp_acesslevel = 'EMPL'
    ORDER BY d.seempd_name ASC";

        return $this->db->query($sql, array($year))->result();
    }

    public function get_bonus_history($empid)
    {
        return $this->db->where('bonus_empid', $empid)->order_by('bonus_date', 'DESC')->get('seemp_bonuses')->result();
    }

    public function check_bonus_eligibility($empid)
    {
        // 1. Fetch Permanent Date
        $emp = $this->db->select('seempd_permanent_date')
            ->where('seempd_empid', $empid)
            ->get('seempdetails')
            ->row();

        // 2. Fetch the absolute latest bonus recorded
        $last_bonus = $this->db->where('bonus_empid', $empid)
            ->order_by('bonus_date', 'DESC')
            ->limit(1)
            ->get('seemp_bonuses')
            ->row();

        $perm_date = ($emp && !empty($emp->seempd_permanent_date)) ? $emp->seempd_permanent_date : null;
        $today = date('Y-m-d');

        if (!$perm_date) {
            return ['eligible' => false, 'message' => 'Employee is not yet Permanent.'];
        }

        // 3. Determine the Milestone (Floor) Date
        // If no bonus exists: Floor = Permanent Date + 365 days
        // If bonus exists: Floor = Last Bonus Next Eligible Date
        $milestone_date = (!$last_bonus)
            ? date('Y-m-d', strtotime($perm_date . ' + 365 days'))
            : $last_bonus->next_eligible_date;

        // Server-side blocking if today is before the milestone
        if ($today < $milestone_date) {
            return [
                'eligible' => false,
                'next_date' => $milestone_date,
                'message' => 'Annual cycle not complete. Eligible again on ' . date('d M Y', strtotime($milestone_date))
            ];
        }

        return [
            'eligible' => true,
            'eligibility_threshold' => $milestone_date // This becomes the 'min' for the calendar
        ];
    }
    public function add_bonus($data)
    {
        // Force Next Eligible Date to be +365 days
        $data['next_eligible_date'] = date('Y-m-d', strtotime($data['bonus_date'] . ' + 365 days'));
        return $this->db->insert('seemp_bonuses', $data);
    }

    // Fetch current month's approved bonus for the Payroll screen
    public function get_pending_bonus_for_payroll($empid, $month_year)
    {
        $start = $month_year . "-01";
        $end = date("Y-m-t", strtotime($start));

        $res = $this->db->where('bonus_empid', $empid)
            ->where('bonus_date >=', $start)
            ->where('bonus_date <=', $end)
            ->get('seemp_bonuses')->row();
        return $res ? $res->bonus_amount : 0;
    }

    /**
     * Fetches employees and their nearest upcoming bonus dates.
     */
    public function get_upcoming_bonuses()
    {
        $sql = "
    SELECT 
        d.seempd_name, 
        d.seempd_empid,
        -- Identify the next eligibility date:
        -- If they had a bonus, it's the next_eligible_date from that bonus.
        -- If no bonus, it's 365 days after their permanent date.
        COALESCE(
            (SELECT next_eligible_date FROM seemp_bonuses 
             WHERE bonus_empid = d.seempd_empid 
             ORDER BY bonus_date DESC LIMIT 1),
            DATE_ADD(d.seempd_permanent_date, INTERVAL 365 DAY)
        ) as nearest_bonus_date
    FROM seempdetails d
    JOIN seemployee e ON d.seempd_empid = e.seemp_id
    WHERE e.seemp_status = 'active'
    -- Only show dates that haven't passed or are very recent
    HAVING nearest_bonus_date >= CURDATE()
    ORDER BY nearest_bonus_date ASC
    LIMIT 5";

        return $this->db->query($sql)->result();
    }
    /**
     * Fetches the bonus amount for a specific employee if it matches the payroll month.
     */
    public function get_bonus_for_payroll($empid, $month_year)
    {
        // month_year comes in as "YYYY-MM"
        $target_month = date('m', strtotime($month_year));
        $target_year = date('Y', strtotime($month_year));

        $this->db->select('bonus_amount');
        $this->db->from('seemp_bonuses');
        $this->db->where('bonus_empid', $empid);
        $this->db->where('MONTH(bonus_date)', $target_month);
        $this->db->where('YEAR(bonus_date)', $target_year);
        $this->db->where('bonus_status', 'completed');
        $this->db->limit(1);

        $query = $this->db->get();
        $result = $query->row();

        return $result ? (float) $result->bonus_amount : 0.00;
    }
}
