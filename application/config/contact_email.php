<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Contact Form Email Settings
// $config['contact_from_email'] = 'satyajitmanna35@gmail.com';
// $config['contact_from_name'] = 'Suropriyo Enterprise';
// $config['admin_notification_email'] = 'goutam201shaw@gmail.com'; // Admin email
// $config['support_email'] = 'suropriyoenterprise@gmail.com'; // Support email

// Email Templates
$config['contact_templates'] = array(
    'user_confirmation' => 'emailContactConfirmationView',
    'admin_notification' => 'emailContactAdminView'
);

// Email Subjects
$config['contact_subjects'] = array(
    'user_confirmation' => 'Suropriyo Enterprise - Contact Form Received',
    'admin_notification' => 'Suropriyo Enterprise - New Contact Form Submission'
);

// Response Time Settings
$config['contact_response_time'] = '24-48 hours';
$config['contact_auto_reply'] = TRUE;

?>