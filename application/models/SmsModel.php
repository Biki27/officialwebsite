<?php
// This code is Written by :
// PAPPU BISWAS 
// Suropriyo Eterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');

class SmsModel extends CI_Model
{

    function send_sms($number, $message)
    {
        $api_url = 'http://example.com/sms-api'; 
        $username = 'your_username';
        $password = 'your_password';
        $sender_id = 'your_sender_id';

        // Prepare POST fields
        $fields = array(
            'username' => urlencode($username),
            'password' => urlencode($password),
            'sender' => urlencode($sender_id),
            'phone' => urlencode($number),
            'message' => urlencode($message)
        );

        // Convert fields to URL-encoded string
        $fields_string = http_build_query($fields);

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute and close cURL
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}


?>