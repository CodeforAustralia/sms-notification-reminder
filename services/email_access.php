<?php
  session_start();
  require('../core/outlook2.php');
  require('../core/functions.php');
  
  if(isset($_SESSION['access_token']) && $_SESSION['user_email']) {  
    
    $emails = json_decode(file_get_contents("../include_files/emails.txt"));
  
    $access_calendars = OutlookService::validateEmailAccess($_SESSION['access_token'], $_SESSION['user_email'], $emails);
    echo json_encode(array('items' => $access_calendars));
  }