<?php

  session_start();
  require('../oauth2.php');
  require('../outlook2.php');
  require('../functions.php');

if (isset($_POST['reminders'])) {
    $data = array();
    foreach($_POST['reminders'] as $key => $value) {
        $data[] =OutlookService::sendEmail($_SESSION['access_token'], $_SESSION['user_email'], '', $value['template'], $value['phone']);
    }
    //$reminders = $_POST['reminders'];
    //var_dump($reminders);
    echo "Thanks";
} else {
    echo "error";
}