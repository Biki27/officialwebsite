<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContactusModel extends CI_Model
{
    /**
     * Inserts contact data and triggers notifications
     * Optimized for AJAX response speed
     */
    public function insertConactus($data)
    {
        $contact_info = array(
            'secon_name'    => $data['name'],
            'secon_email'   => $data['email'],
            'secon_subject' => $data['subject'],
            'secon_message' => $data['message'],
            'secon_action'  => 'none',
        );

        // Start Transaction to ensure data integrity
        $this->db->trans_start();
        $this->db->insert('secontactus', $contact_info);
        $this->db->trans_complete();

        $db_error = $this->db->error();

        // If DB insertion was successful, handle emails
        if ($this->db->trans_status() === TRUE && $db_error['code'] == 0) {
            // Trigger background email process
            $this->send_contact_notification_email($data);
            return ['code' => 0, 'message' => 'Success'];
        }

        return $db_error;
    }

    // Send contact notification emails using your existing views
    function send_contact_notification_email($contact_data = array())
    {
        $this->load->library('email');

        // Basic validation
        if (!isset($contact_data['email']) || !filter_var($contact_data['email'], FILTER_VALIDATE_EMAIL)) {
            return 1;
        }

        // Fetch values from .env for a clean configuration
        $from_email  = getenv('SYSTEM_EMAIL_FROM');
        $from_name   = getenv('SYSTEM_EMAIL_NAME');
        $admin_email = getenv('ADMIN_CONTACT_EMAIL');

        $this->email->set_newline("\r\n");

        // --- 1. Send confirmation to the person who contacted ---
        // Uses your existing 'emailContactConfirmationView'
        $this->email->from($from_email, $from_name);
        $this->email->to($contact_data['email']);

        $view_data = array(
            'name'    => $contact_data['name'],
            'subject' => $contact_data['subject'],
            'message' => $contact_data['message']
        );

        $this->email->subject($from_name . ' - Contact Form Received');
        $this->email->message($this->load->view('email/emailContactConfirmationView', $view_data, TRUE));
        $this->email->send();
        // log_message('error', $this->email->print_debugger());
        // --- 2. Send notification to the Admin ---
        // Uses your existing 'emailContactAdminView'
        $admindata = array(
            'name'         => $contact_data['name'],
            'email'        => $contact_data['email'],
            'subject'      => $contact_data['subject'],
            'message'      => $contact_data['message'],
            'contact_date' => date('Y-m-d H:i:s')
        );

        $this->email->from($from_email, $from_name);
        $this->email->to($admin_email);
        $this->email->subject($from_name . ' - New Contact Form Submission');
        $this->email->message($this->load->view('email/emailContactAdminView', $admindata, TRUE));

        $this->email->send();
        // log_message('error', $this->email->print_debugger());
    }
}