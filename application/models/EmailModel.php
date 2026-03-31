<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmailModel extends CI_Model
{
    public function send_applicant_submission_email($email = '', $applicantname = '', $jobposition = '', $phone = '')
    {
        $this->load->helper('email');
        $this->load->library('email'); // This automatically loads config/email.php settings

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 1;
        }

        // Fetch values from .env
        $from_email = getenv('SYSTEM_EMAIL_FROM');
        $from_name = getenv('SYSTEM_EMAIL_NAME');
        $admin_email = getenv('ADMIN_NOTIFICATION_EMAIL');

        // 1. Send Email to Applicant
        $this->email->from($from_email, $from_name);
        $this->email->to($email);
        $this->email->subject($from_name . ' - Application Received.');

        $data = array('name' => $applicantname, 'position' => $jobposition);
        $this->email->message($this->load->view('email/emailApplicantView', $data, TRUE));
        $this->email->send();

        // 2. Send Notification Email to Admin
        $this->email->clear(); // CRITICAL: Clear settings before the second send
        $this->email->from($from_email, $from_name);
        $this->email->to($admin_email);
        $this->email->subject($from_name . ' - New Application Received.');

        $admindata = array(
            'email' => $email,
            'name' => $applicantname,
            'position' => $jobposition,
            'phone' => $phone
        );
        $this->email->message($this->load->view('email/emailAdminView', $admindata, TRUE));
        $this->email->send();

        return 0;
    }
}