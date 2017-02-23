<?php
  session_start();
  require('../core/outlook2.php');
  require('../core/functions.php');
  $calendar_path = "../include_files/calendars.json";
  $template_path = "../include_files/templates.json";
  
  if(isset($_SESSION['access_token']) && $_SESSION['user_email'] && 
      $_POST['info'] &&  $_POST['save_type'] && $_POST['info'] != '' &&  $_POST['save_type'] != '' ) {
      if($_POST['save_type'] == 'calendar') {
        $file_path = $calendar_path;
      } else if($_POST['save_type'] == 'template') {
        $file_path = $template_path;
      }
      $info = json_encode($_POST['info']);
      $output_file = fopen($file_path, "w") or die("Unable to open file!");
      fwrite($output_file, $info);
      fclose($output_file);
      echo "Done";
  } else {
      echo json_encode(array("errorNumber" => 401));
  }