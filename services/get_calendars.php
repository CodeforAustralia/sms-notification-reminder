<?php
  session_start();
  require('../core/outlook2.php');
  require('../core/functions.php');
  
  if(isset($_SESSION['access_token']) && $_SESSION['user_email'] ) {
      $calendars = file_get_contents("../include_files/calendars.json");
      echo $calendars;
  } else {
      echo json_encode(array("errorNumber" => 401));
  }