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
    <title>PHP Calendar API Tutorial</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- <link href="css/font-awesome.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/send_mails.js"></script>
  </head>
  <body>
    <?php 
      if (!$loggedIn) {
    ?>
      <!-- User not logged in, prompt for login -->
      <div class="container container-table">
        <div class="row vertical-center-row">
          <div class="text-center col-md-4 col-md-offset-4">
            <h2>SMS Notification Service</h2>
            <p>Please <a href="<?php echo oAuthService::getLoginUrl($redirectUri)?>">sign in</a> with your VLA account.</p>
          </div>
        </div>
      </div>
    <?php
      }
      else {
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

    ?>
      <!-- User is logged in, do something here -->
      <div class="header">
        <div class="header-left col-xs-9">
          <div class="col-xs-2"><img src="logos/c4a-logo.png" /></div>
          <div class="col-xs-2"><img src="logos/vla-logo.png" /></div>          
        </div>
        <div class="header-right col-xs-2 pull-right">
          <a href="logout.php" id="logout">Sign out</a>
        </div>
      </div>      

      <div class="container"> 
          <div class="col-xs-12"><h2>Appointments by calendar</h2></div>
          <ul  class="nav nav-pills">
            <?php
              $calendar_number = 1;
              foreach ($events as $calendar_key => $value) {
                echo '<li ' . ($calendar_number == 1 ? 'class="active"': '') . '><a href="#calendar-' . $calendar_number . '" data-toggle="tab">' . $calendar_key . '</a></li>';
                $calendar_number++;
              }
            ?>
          </ul>

          <div class="tab-content clearfix">
            <?php
              $calendar_number = 1;
              $calendar_obj = array();
              foreach ($events as $calendar_key => $calendar_events) {
                ?>
                <div class="tab-pane <?= ($calendar_number == 1 ? 'active': '')?>" id="calendar-<?= $calendar_number ?>">
                  <button type="button" class="btn btn-success pull-right send_sms">Send SMS</button>
                  <button type="button" class="btn btn-info pull-right refresh_page">Refresh</button>
                  <div class="table-responsive col-xs-12">

                    <table class="table" id="reminders">
                      <thead>
                        <tr>
                          <th class="col-xs-2">Event Type</th>
                          <th class="col-xs-6">SMS template</th>
                          <th class="col-xs-2"># of chars</th>
                          <th class="col-xs-2">Name</th>
                          <th class="col-xs-2">P. Number</th>
                          <!-- <th class="col-xs-1"></th> -->
                        </tr>
                      </thead>                         
                      <tbody>            
                        <?php foreach($calendar_events as $event) { 
                            $pased_subject  = parse_subject($event);
                            $event_type     = $pased_subject['event_type'];
                            $event_template = $pased_subject['event_template'];
                            $phone          = trim($pased_subject['phone']);
                            $name           = trim($pased_subject['client_name']);
                            $number_chars   = strlen($event_template);
                            $calendar_obj["calendar-" . $calendar_number][] = 
                              array('event_type' => $event_type, 'template' => $event_template, 'phone' => $phone);
                            if($event_type != ''):
                          ?>
                          <tr>
                            <td><?= $event_type ?></td>
                            <td><?= $event_template ?></td>
                            <td><span class="badge"><?= $number_chars ?></span></td>
                            <td><span><?= $name ?></span></td>
                            <td><span><?= $phone ?></span></td>
                            <!-- <td><input type="checkbox" /></td> -->
                          </tr>
                        <?php 
                            endif;
                        } ?>
                      </tbody>
                  </table>
                  </div>
                </div>
                <?php
                $calendar_number++;
              }
            ?>
          </div>
      </div>
      <footer class="footer">
        <div class="container">
          <p class="text-muted">Check our new interface <a href="/v2">here</a> <i class="fa fa-github" aria-hidden="true"></i> If you find an issue or just want to give us your feedback, please use this link. (<a href="https://github.com/CodeforAustralia/sms-notification-reminder/issues/" target="_blank">Link</a>)</p>
        </div>
      </footer>
      <div class="hidden loading-box">
        <i class="fa fa-refresh fa-spin" style="font-size:200px;color:#980747"></i>  
      </div>
    <?php    
      }
    ?>
    <script type="text/javascript">
      var calendar_obj = '<?= json_encode($calendar_obj); ?>'; //Used in send_mails.js
    </script>
  </body>
</html>