<?php

  session_start();
  require('../core/oauth2.php');
  require('../core/outlook2.php');
  require('../core/functions.php');
  require('../core/email.php');

if (isset($_POST['reminders'])) {
    $data = array();
    $rows = array();
    
    $pcsms = "@pcsms.com.au";
    $subject = 'VLA SMS Notification Report';
    
    foreach($_POST['reminders'] as $key => $value) {
        $data[] = OutlookService::sendEmail($_SESSION['access_token'], $_SESSION['user_email'], '', $value['template'], $value['phone'] . $pcsms);
        $rows[] = array($value['event_type'], $value['template']);
    }
        
    $email_data = array('subject' => $subject, 'rows' => $rows);
    $email_obj = new Email();
    
    //error_log(preg_replace('/\t+/', '',$email_obj->mergeTemplateData($email_data)));
    OutlookService::sendEmail($_SESSION['access_token'], $_SESSION['user_email'], $subject, $email_obj->mergeTemplateData($email_data), $_SESSION['user_email']);
    
    echo "Thanks";
} else {
    echo "error";
}