<?php

// fixed By SM

defined('BASEPATH') OR exit('No direct script access allowed');

class InterviewModel extends CI_Model
{

    function send_interview_email($email = '', $name = '', $position = '', $date = '', $time = '', $location = '', $phone = '', $hr_name = 'Eshita Sen', $round = 'Interview')
    {

        $this->load->helper('email');
        $this->load->library('email');

        if (!filter_var($email)) {
            return 1;
        }

        $this->email->set_newline("\r\n");

        // Applicant Interview Email     
        $data = array(
            'name' => $name,
            'position' => $position,
            'date' => $date,
            'time' => $time,
            'location' => $location,
            'hr_name' => $hr_name,
            'round' => $round // Added this
        );

        $this->email->from('hr@suropriyo.in', 'Suropriyo Enterprise');
        $this->email->to($email);
        $this->email->subject('Interview Invitation - Suropriyo Enterprise');
        $this->email->message($this->load->view('email/interview_invitation', $data, TRUE));

        // Send applicant email
        if (!$this->email->send()) {
            log_message('error', 'Applicant interview email failed: ' . $this->email->print_debugger());
            return 1;
        }

        // HR || Admin Notification Email
        $admindata = array(
            'email' => $email,
            'name' => $name,
            'position' => $position,
            'date' => $date,
            'time' => $time,
            'location' => $location,
            'phone' => $phone,
            'hr_name' => $hr_name,
            'round' => $round // Added this
        );

        $this->email->from('hr@suropriyo.in', 'Suropriyo Enterprise');
        $this->email->to('eshitasen07@gmail.com'); // HR email
        $this->email->subject('Interview Scheduled - Suropriyo Enterprise');
        $this->email->message($this->load->view('email/interview_hr_notification', $admindata, TRUE));

        // Send HR notification email
        if (!$this->email->send()) {
            log_message('error', 'HR interview notification email failed: ' . $this->email->print_debugger());
            return 1;
        }
        return 0;
    }
    // function send_interview_email($email = '', $name = '', $position = '', $date = '', $time = '', $location = '', $phone = '', $hr_name = 'Eshita Sen', $round = 'Interview')
    // {
    //     $this->load->helper('email');
    //     $this->load->library('email');

    //     if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //         return 1;
    //     }

    //     $this->email->set_newline("\r\n");

    //     // Pass the $round variable to the view
    //     $data = array(
    //         'name' => $name,
    //         'position' => $position,
    //         'date' => $date,
    //         'time' => $time,
    //         'location' => $location,
    //         'hr_name' => $hr_name,
    //         'round' => $round // Added this
    //     );

    //     $this->email->from('hr@suropriyo.in', 'Suropriyo Enterprise');
    //     $this->email->to($email);
    //     $this->email->subject($round . ' Invitation - Suropriyo Enterprise');
    //     $this->email->message($this->load->view('email/interview_invitation', $data, TRUE));

    //     return $this->email->send() ? 0 : 1;
    // }

}

?>