<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Gmail example: enable 2-Step Verification, then create an "App Password".
$config['protocol']     = 'smtp';
$config['smtp_host']    = 'ssl://smtp.gmail.com';
$config['smtp_port']    = 465;
$config['smtp_user']    = 'your_email@gmail.com';
$config['smtp_pass']    = 'your_app_password';
$config['smtp_timeout'] = 10;
$config['mailtype']     = 'html';
$config['charset']      = 'utf-8';
$config['newline']      = "\r\n";
$config['crlf']         = "\r\n";

// Used as the "From" address in app emails
$config['from_email']   = 'your_email@gmail.com';
$config['from_name']    = 'CI3 CRUD App';
