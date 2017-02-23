<?php
  session_start();
  require('../core/outlook2.php');
  require('../core/functions.php');
  
  if(isset($_SESSION['access_token']) && $_SESSION['user_email'] ) {
      $templates = file_get_contents("../include_files/templates.json");
      echo $templates;
  } else {
      echo json_encode(array("errorNumber" => 401));
  }