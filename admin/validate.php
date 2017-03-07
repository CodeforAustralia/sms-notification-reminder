<?php
  session_start();
  $password = "UXVpZXRLbmlnaHQ=";
  
  if(isset($_POST['password']) && $_POST['password'] == base64_decode($password)) {
      $_SESSION['admin'] = $_SESSION['access_token'];
  }
  
  if( isset($_SESSION['access_token']) && 
        isset($_SESSION['user_email']) && 
        isset($_SESSION['admin']) ) {  //All set
            
      echo file_get_contents("index.php");
      $_SESSION['redirect'] = '';
      
  } elseif( isset($_SESSION['access_token']) && 
        isset($_SESSION['user_email']) && 
        !isset($_SESSION['admin']) ) {  //Needs password
            
      echo file_get_contents("form.php");
      
  } else {    //Needs Oauth Auth
      $_SESSION['redirect'] = "admin";
      header("Location: /calendar.php");
  } 

?>