<?php
  session_start();
  require('../core/outlook2.php');
  require('../core/functions.php');
  
  if(isset($_SESSION['access_token']) && $_SESSION['user_email']) { 
    
      if (isset($_POST['type'])) {
      
            switch ($_POST['type']) {
                case "get-emails":
                    $emails = json_decode(file_get_contents("../include_files/calendars.json"));
                    array_unshift($emails, array('email' => $_SESSION['user_email'] , 'name' => 'Your Calendar'));
                    echo json_encode($emails);
                    break;
                case "validate-email":
                    $email = $_POST['email']['email'];
                    $name = $_POST['email']['name'];
                    $emails = array('email' => $email, 'name' => $name);
                    $access_calendars = OutlookService::validateEmailAccess($_SESSION['access_token'], $_SESSION['user_email'], $emails);
                    echo json_encode(array('items' => $access_calendars));
                    break;
                default:
        }
      } else {
            $emails = json_decode(file_get_contents("../include_files/calendars.json"));
            $access_calendars = OutlookService::validateEmailAccess($_SESSION['access_token'], $_SESSION['user_email'], $emails);
            echo json_encode(array('items' => $access_calendars));
      } 
  } 
  