<?php
  class OutlookService {
    private static $outlookApiUrl = "https://outlook.office.com/api/v2.0";

    public static function makeApiCall($access_token, $user_email, $method, $url, $payload = NULL) {
      // Generate the list of headers to always send.
      $headers = array(
        "User-Agent: php-tutorial/1.0",         // Sending a User-Agent header is a best practice.
        "Authorization: Bearer ".$access_token, // Always need our auth token!
        "Accept: application/json",             // Always accept JSON response.
        "client-request-id: ".self::makeGuid(), // Stamp each new request with a new GUID.
        "return-client-request-id: true",       // Tell the server to include our request-id GUID in the response.
        "X-AnchorMailbox: ".$user_email,         // Provider user's email to optimize routing of API call
        'Prefer: outlook.timezone="Australia/Sydney"' // Use VIC time instead of UTC Outlook time
      );

      $curl = curl_init($url);

      switch(strtoupper($method)) {
        case "GET":
          // Nothing to do, GET is the default and needs no
          // extra headers.
          error_log("Doing GET");
          break;
        case "POST":
          error_log("Doing POST");
          // Add a Content-Type header (IMPORTANT!)
          $headers[] = "Content-Type: application/json";
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
          break;
        case "PATCH":
          error_log("Doing PATCH");
          // Add a Content-Type header (IMPORTANT!)
          $headers[] = "Content-Type: application/json";
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
          curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
          break;
        case "DELETE":
          error_log("Doing DELETE");
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
          break;
        default:
          error_log("INVALID METHOD: ".$method);
          exit;
      }

      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //CURL doesn't like microsoft's cert
      $response = curl_exec($curl);
      error_log("curl_exec done.");

      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      error_log("Request returned status ".$httpCode);

      if ($httpCode >= 400) {
        return array('errorNumber' => $httpCode,
                     'error' => 'Request returned HTTP error '.$httpCode);
      }

      $curl_errno = curl_errno($curl);
      $curl_err = curl_error($curl);

      if ($curl_errno) {
        $msg = $curl_errno.": ".$curl_err;
        error_log("CURL returned an error: ".$msg);
        curl_close($curl);
        return array('errorNumber' => $curl_errno,
                     'error' => $msg);
      }
      else {
        error_log("Response: ".$response);
        curl_close($curl);
        return json_decode($response, true);
      }
    }

    // This function generates a random GUID.
    public static function makeGuid(){
        if (function_exists('com_create_guid')) {
          error_log("Using 'com_create_guid'.");
          return strtolower(trim(com_create_guid(), '{}'));
        }
        else {
          error_log("Using custom GUID code.");
          $charid = strtolower(md5(uniqid(rand(), true)));
          $hyphen = chr(45);
          $uuid = substr($charid, 0, 8).$hyphen
                  .substr($charid, 8, 4).$hyphen
                  .substr($charid, 12, 4).$hyphen
                  .substr($charid, 16, 4).$hyphen
                  .substr($charid, 20, 12);

          return $uuid;
        }
    }
    public static function getUser($access_token) {
      $getUserParameters = array (
        // Only return the user's display name and email address
        "\$select" => "DisplayName,EmailAddress"
      );

      $getUserUrl = self::$outlookApiUrl."/Me?".http_build_query($getUserParameters);

      return self::makeApiCall($access_token, "", "GET", $getUserUrl);
    }

    public static function getMessages($access_token, $user_email) {
      $getMessagesParameters = array (
        // Only return Subject, ReceivedDateTime, and From fields
        "\$select" => "Subject,ReceivedDateTime,From,Organizer",
        // Sort by ReceivedDateTime, newest first
        "\$orderby" => "ReceivedDateTime DESC",
        // Return at most 20 results
        "\$top" => "20"
      );

      $getMessagesUrl = self::$outlookApiUrl."/Me/MailFolders/Inbox/Messages?".http_build_query($getMessagesParameters);

      return self::makeApiCall($access_token, $user_email, "GET", $getMessagesUrl);
    }

    public static function getEvents($access_token, $user_email) {
      $getEventsParameters = array (
        // Only return Subject, Start, and End fields
        "\$select" => "Subject,Start,End, BodyPreview,Organizer",
        // Sort by Start, oldest first
        "\$orderby" => "Start/DateTime",
        // Return at most 20 results
        "\$top" => "20"
      );

      $getEventsUrl = self::$outlookApiUrl."/Me/Events?".http_build_query($getEventsParameters);

      return self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl);
    }

    public static function getCalendars($access_token, $user_email) {
      $getEventsParameters = array ();

      $getEventsUrl = self::$outlookApiUrl."/Me/Calendars";

      return self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl);      
    }

    public static function getEventsByCalendarId($access_token, $user_email, $id) {
      date_default_timezone_set('Australia/Melbourne');
      //Check if is Friday to support appointments on Monday
      if (date("l") == 'Friday'){
        $day_after = date('Y-m-d', strtotime(' +3 day'));
        $today = date('Y-m-d', strtotime(' +2 day'));
      } else {
        $day_after = date('Y-m-d', strtotime(' +1 day'));
        $today = date('Y-m-d');
      }
      
      $getEventsParameters = array (
        "\$select" => "Subject,Start,Location, BodyPreview,Organizer",
        // Only return Subject, Start, and End fields
        "startdatetime" => $today . "T13:00:00Z",
        // Sort by Start, oldest first
        "enddatetime" => $day_after . "T12:59:00Z",
        // Return at most 20 results
        "\$top" => "20"
      );

      $getEventsUrl = self::$outlookApiUrl."/Me/Calendars/" . $id . "/calendarview?".http_build_query($getEventsParameters); 
      return self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl);
    }
    
    public static function getEventsByEmail($access_token, $user_email, $calendar_email) {
      date_default_timezone_set('Australia/Melbourne');
      //Check if is Friday to support appointments on Monday
      if (date("l") == 'Friday'){
        $day_after = date('Y-m-d', strtotime(' +3 day'));
        $today = date('Y-m-d', strtotime(' +2 day'));
      } else {
        $day_after = date('Y-m-d', strtotime(' +1 day'));
        $today = date('Y-m-d');
      }
      
      $getEventsParameters = array (
        "\$select" => "Subject,Start,Location, BodyPreview, Body,Organizer",
        // Only return Subject, Start, and End fields
        "startdatetime" => $today . "T13:00:00Z",
        // Sort by Start, oldest first
        "enddatetime" => $day_after . "T12:59:00Z" ,
        // Return at most 20 results
        "\$top" => "20"
      );

      $getEventsUrl = self::$outlookApiUrl."/Users/" . $calendar_email . "/calendarview?".http_build_query($getEventsParameters); 
      return self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl);
    }
    
    public static function getEventsByEmailAndDate($access_token, $user_email, $calendar_email, $date) {
      date_default_timezone_set('Australia/Melbourne');
     
      $day_before = date('Y-m-d',strtotime($date . ' -1 day'));
      
      $getEventsParameters = array (
        "\$select" => "Subject,Start,Location, BodyPreview, Body,Organizer",
        // Only return Subject, Start, and End fields
        "startdatetime" => $day_before . "T13:00:00Z",
        // Sort by Start, oldest first
        "enddatetime" => $date . "T12:59:00Z" ,
        // Return at most 20 results
        "\$top" => "20"
      );

      $getEventsUrl = self::$outlookApiUrl."/Users/" . $calendar_email . "/calendarview?".http_build_query($getEventsParameters); 
      return self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl);
    }
    
    public static function sendEmail($access_token, $user_email, $subject, $content, $recipient = '') {
      
      if ($recipient != '') {
        if($content == '') { //if empty content then get subject and put it into the body to avoid limitation of chars
          $content = $subject;
          $subject = '';
        }
        $getEventsUrl = self::$outlookApiUrl."/Me/sendmail/";      //https://outlook.office.com/api/v2.0/me/sendmail
        $payload = '{
                      "Message": {
                        "Subject": "' . $subject . '",
                        "Body": {
                          "ContentType": "HTML",
                          "Content": "' .  $content . '"
                        },
                        "ToRecipients": [
                          {
                            "EmailAddress": {
                              "Address": "' . $recipient . '"
                            }
                          }
                        ]
                      },
                      "SaveToSentItems": "true"
                    }';
        return self::makeApiCall($access_token, $user_email, "POST", $getEventsUrl, $payload);
      }

    }

    public static function getEventsByCalendars($access_token, $user_email, $calendars) {
      $events = array();
      $result = array();
      foreach ($calendars as $calendar) {
        $event = self::getEventsByCalendarId($access_token, $user_email, $calendar["Id"]);
        //$events[] = $event['value'];
        $events = array_merge($events, $event['value']);
        $result[$calendar['Name']] = $event['value']; //name of the calendar     
      }
      return $result;
    }
    
    public static function getEventsByEmails() {
      require('email.php');
      $email_obj = new Email();
      $events = array();
      $result = array();
      foreach($email_obj->getEmailsFromFile() as $calendar_email) {
        $calendars_email = self::getEventsByEmail($_SESSION['access_token'], $_SESSION['user_email'], $calendar_email->email);
        if ($calendars_email['errorNumber']){
          //var_dump("Dont have enough permisions");
        } else {
          //var_dump($calendars_email);
            $events = array_merge($events, $calendars_email['value']);
            $result[$calendar_email->name] = $calendars_email['value']; //name of the calendar     
        }
      }
      return $result;
    }
    
    public static function updateEventSubject($events){
      date_default_timezone_set('Australia/Melbourne');
      foreach($events as $event_args) {
        $id     = $event_args['id'];
        $email  = $event_args['email'];
        $user_email  = $event_args['user_email'];
        $access_token  = $event_args['access_token'];
        
        $getEventsUrl = self::$outlookApiUrl."/Users/" . $email . "/events/" . $id;      // {{API_URL}}/Users/{{email}}/events/{{event_id}}
        //$event = self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl);
        $event_subject = $_SESSION['events'][$id];
        $regex = '#Sent:(.*?)\.#';
        preg_match($regex, $event_subject, $sent_dates);
        if(!empty($sent_dates)) {
          $event_subject = str_replace($sent_dates[0],"Sent:" . $sent_dates[1] . date(", Y-m-d") . ".", $event_subject);
        } else {
          $event_subject .= " # Sent:" . date(" Y-m-d") . ".";
        }
        $payload = '{
                      "Subject": "' . $event_subject . '"
                    }';
       self::makeApiCall($access_token, $user_email, "PATCH", $getEventsUrl, $payload);
      }
    }
    
    public static function getEventById($event){
        $id     = $event_args['id'];
        $email  = $event_args['email'];
        $user_email  = $event_args['user_email'];
        $access_token  = $event_args['access_token'];
        
        $getEventsUrl = self::$outlookApiUrl."/Users/" . $email . "/events/" . $id;      // {{API_URL}}/Users/{{email}}/events/{{event_id}}
        $event = self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl);
        return $event;
    }
    
    public static function validateEmailAccess($access_token, $user_email, $email){
      date_default_timezone_set('Australia/Melbourne');
      
      $today = date('Y-m-d');
      $getEventsParameters = array (
        "\$select" => "Subject",
        // Only return Subject, Start, and End fields
        "startdatetime" => $today . "T00:00:00Z",
        // Sort by Start, oldest first
        "enddatetime" => $today . "T23:59:00Z"
      );
      
      $emails_access = array();
      
      $getEventsUrl = self::$outlookApiUrl."/Users/" . $email['email'] . "/calendarview?".http_build_query($getEventsParameters); 
      $api_call = self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl);
      if(!isset($api_call["errorNumber"]) || isset($api_call["value"])) {
        error_log("Accesssing calendars: " . $email['email'] . " - ". json_encode($api_call));
        $emails_access[] = $email;
      } 
      
      //array_unshift($emails_access, array('email' => $_SESSION['user_email'] , 'name' => 'Your Calendar'));
      return $emails_access;
    }
  }
?>