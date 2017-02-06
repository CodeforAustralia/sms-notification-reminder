<?php
  session_start();
  require('../core/outlook2.php');
  require('../core/functions.php');
  
  if(isset($_SESSION['access_token']) && $_SESSION['user_email'] && $_POST['email'] && $_POST['date']) {
      $events = OutlookService::getEventsByEmailAndDate($_SESSION['access_token'], $_SESSION['user_email'], $_POST['email'], $_POST['date']);
      
      if (isset($events['errorNumber'])) {
          echo json_encode($events);
      } else {
            $events_output = array();
            foreach ($events as $calendar_key => $calendar_events) {
                if (is_array($calendar_events)) {
                    foreach($calendar_events as $event) {
                        if (!stristr($event['Subject'],'cancel') && !stristr($event['Subject'],'reschedule')) {
                            $pased_subject  = parse_subject($event);
                            $event_type     = $pased_subject['event_type'];
                            $event_template = $pased_subject['event_template'];
                            $phone          = trim($pased_subject['phone']);
                            $name           = trim($pased_subject['client_name']);
                            $sent           = trim($pased_subject['sent']);
                            $id             = $event['Id'];
                            if ($event_type != '' && $event_template != '' && $phone != '') {
                                $events_output['cards'][] = array(
                                        'id'        => $id,
                                        'sent'      => $sent,
                                        'name'      => $name,
                                        'mobile'    => $phone,
                                        'message'   => $event_template,
                                        'type'      => $event_type,
                                        'appt_date' => $pased_subject['appt_date']
                                    );   
                            }
                            $_SESSION['events'][$id] = $event['Subject'];
                        }
                    }
                }
            }
            $events_output['self_calendar'] = array('email' => $_SESSION['user_email'] , 'name' => 'Your Calendar');
            echo json_encode($events_output);
      }
  } else {
      echo json_encode(array("errorNumber" => 401));
  }