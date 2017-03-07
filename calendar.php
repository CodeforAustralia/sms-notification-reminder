<?php
  session_start();
  require('core/oauth2.php');
  require('core/outlook2.php');
  require('core/functions.php');
  
  $loggedIn = isset($_SESSION['access_token']) && !is_null($_SESSION['access_token']);
  $redirectUri = "https://" . $_SERVER['SERVER_NAME'] . '/core/authorize.php';
  $ownCalendars = false; //Access own calendars or access calendars by emails in source
?>
<html>
  <head>
    <title>SMS Reminders</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- <link href="css/font-awesome.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </head>
  <body>
    <?php 
      if (!$loggedIn) {
    ?>
      <!-- User not logged in, prompt for login -->
      <div class="container container-table">
        <div class="row vertical-center-row">
          <a class="login-box" href="<?php echo oAuthService::getLoginUrl($redirectUri)?>">
            <div class="text-center col-md-4 col-md-offset-4">
              <h2>SMS Reminders</h2>
              <p>Please <span class="fake-link">sign in</span> with your VLA account.</p>
            </div>
          </a>
        </div>
      </div>
    <?php
      } else {
        if(isset($_SESSION['redirect']) && $_SESSION['redirect'] == 'admin') {
          $_SESSION['redirect'] = '';
          header("Location: /admin/validate.php");
        } else {
          header("Location: /v2");
          die();
        }
        $calendars = OutlookService::getCalendars($_SESSION['access_token'], $_SESSION['user_email']);
        if (isset($calendars['errorNumber']) && $calendars['errorNumber'] > 400) {
          header("Location: logout.php");
          die();
        }
	
      	if($ownCalendars) {
      		/** Own calendars **/
      		$events = OutlookService::getEventsByCalendars($_SESSION['access_token'], $_SESSION['user_email'], $calendars['value']);
      	} else {	
      		/** Calendars by emails in source[file] **/
      		$events = OutlookService::getEventsByEmails();
      	}
      }
    ?>
  </body>
</html>
