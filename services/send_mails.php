<?php

  session_start();
  require('../core/oauth2.php');
  require('../core/outlook2.php');
  require('../core/functions.php');
  require('../core/email.php');
    $data = array();
    $rows = array();
    
    $pcsms = "@e2s.pcsms.com.au";
    $subject = 'VLA SMS Notification Report ' . date("Y-m-d");
    
if (isset($_POST['reminders'])) {
    
    foreach($_POST['reminders'] as $key => $value) {
        $data[] = OutlookService::sendEmail($_SESSION['access_token'], $_SESSION['user_email'], '', $value['template'], $value['phone'] . $pcsms);
        $rows[] = array($value['event_type'], $value['template']);
    }
        
    $email_data = array('subject' => $subject, 'rows' => $rows);
    $email_obj = new Email();
    
    //error_log(preg_replace('/\t+/', '',$email_obj->mergeTemplateData($email_data)));
    OutlookService::sendEmail($_SESSION['access_token'], $_SESSION['user_email'], $subject, $email_obj->mergeTemplateData($email_data), $_SESSION['user_email']);
    
    echo "Thanks";
} elseif (isset($_POST['messages'])) {
    $update_event = array();
    foreach($_POST['messages'] as $message){
        $data[] = OutlookService::sendEmail($_SESSION['access_token'], $_SESSION['user_email'], $message['message'], '', $message['mobile'] . $pcsms);
        $rows[] = array($message['name'], $message['mobile'], $message['type'], $message['appt_date']);
        $update_event[] = array(
                                'id'=> $message['id'],
                                'email'=> $_POST['email'],
                                'user_email'=> $_SESSION['user_email'],
                                'access_token'=> $_SESSION['access_token'],
                            );
    }
    $email_data = array('subject' => $subject, 'rows' => $rows);
    $email_obj = new Email();
    
    //error_log(preg_replace('/\t+/', '',$email_obj->mergeTemplateData($email_data)));
    OutlookService::sendEmail($_SESSION['access_token'], $_SESSION['user_email'], $subject, $email_obj->mergeTemplateData($email_data), $_SESSION['user_email']);
    
    OutlookService::updateEventSubject($update_event);
    
    echo "Thanks";
} else {
    echo "error";
}
