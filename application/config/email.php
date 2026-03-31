<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol']    = getenv('SMTP_PROTOCOL') ? getenv('SMTP_PROTOCOL') : 'smtp';
$config['smtp_host']    = getenv('SMTP_HOST');
$config['smtp_port']    = getenv('SMTP_PORT');
$config['smtp_user']    = getenv('SMTP_USER');
$config['smtp_pass']    = getenv('SMTP_PASS');
$config['smtp_crypto']  = getenv('SMTP_CRYPTO');

$config['smtp_auth']    = TRUE;
$config['mailtype']     = 'html';
$config['charset']      = 'utf-8';
$config['newline']      = "\r\n";
$config['crlf']         = "\r\n";
$config['wordwrap']     = TRUE;