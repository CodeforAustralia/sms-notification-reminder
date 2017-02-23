<?php
/**
 * split calendar object into event type, template and client number 
 * @param  String $event_in_calendar Information of each event in calendar
 * @return Array                     Event type, template and client number 
 */
function parse_subject($event_in_calendar, $templates) {
    $event          = explode("#", $event_in_calendar['Subject']);
    $body           = $event_in_calendar['Body']['Content'];
    $body_preview   = $event_in_calendar['BodyPreview'];
    $location       = $event_in_calendar['Location']['DisplayName'];
    $date_time      = pase_outlook_date($event_in_calendar['Start']['DateTime']);
    $output         = array();
    $output['sent'] = get_sent_information($event_in_calendar['Subject']);
    if (isset($event[0])) {
        $event_info = explode(",", $event[0]);
        $phone      = end($event_info);
        $output['client_name']  = (sizeof($event_info) > 1 ? $event_info[0]: "-");
        $output['phone']        = sanitize_phone($event_info[1]);
        $output['appt_date']    = $date_time['date'] . " " . $date_time['time'];
        $args = array(
                        'templates' => $templates,
                        'body'      => $body,
                        'date'      => $date_time['date'],
                        'time'      => $date_time['time'],
                        'location'  => $location
                    ); 
        $event_obj = set_template($args);
        if($event_obj->name == "") {
            $args['body'] = $body_preview;
            $event_obj = set_template($args);
        }
        
        $output['name']     = $event_obj->name;
        $output['content']  = $event_obj->content;
        
    }
    return $output;
}

/**
 * Get all abbreviations from template object 
 * @param  Object $templates All templates and attributes
 * @return Array 
 */
function get_abbreviations($templates){
    $abbreviations = array();
    foreach($templates as $template){
        $abbreviations[] = $template->abbreviation;
    }
    return $abbreviations;
}

/**
 * Get Information form a specific template in the template Object 
 * @param  Object $templates All templates and attributes
 * @param  String $abbreviation Abbreviation to find a specific template
 * @return Object 
 */
function get_template($templates, $abbreviation) {
    foreach($templates as $template){
        if($template->abbreviation == $abbreviation) {
            return $template;
        }
    }
    return false;
}

/**
 * Find type of template in the body of an event
 * @param  Object $templates All templates and attributes
 * @param  String $body Body of an event
 * @return Object 
 */
function find_template($templates, $body) {
    $abbreviations = get_abbreviations($templates);
    $template = array();
    
    foreach($abbreviations as $abbr){
        if(stristr($body,$abbr)){
            $template = get_template($templates, $abbr);
        }
    }
    return $template;
}


/**
 * Replace template variables with provided values (Outlook, Templates)
 * @param  Object $templates All templates and attributes
 * @param  String $body Body of an event
 * @return Object 
 */
function set_template($args) {
    
    $templates = $args['templates'];
    $template  = find_template($templates, $args['body']);
    $date      = $args['date'];
    $time      = $args['time'];
    $location  = $args['location'];
    if(!empty($template)) {
        $template->content = str_replace("(phone)", $template->office_number, $template->content);
        $template->content = str_replace("(date)", $date, $template->content);
        $template->content = str_replace("(time)", $time, $template->content);
        $template->content = str_replace("(location)", $location, $template->content);
    }

    return $template;
}

/**
 * Extract date information from outlook event object
 * @param  String $outlook_date date in outlook format
 * @return Array                Date in simple format
 */
function pase_outlook_date($outlook_date) {
    $split_date = explode("T", $outlook_date);
    $date = $split_date[0];
    $date = date("D, d/m/Y", strtotime($split_date[0])); // date("M jS, Y", strtotime("2016-09-16")); Sep 16th, 2016
    $time = explode(".", $split_date[1]);
    $time = date("g:i a", strtotime($time[0]));
    return array('date' => $date, 'time' => $time);
}

/**
 * Sanitize phone numbers   
 * @param  String $phone    [String with phone number]
 * @return String           [String without special characters or white spaces]
 */
function sanitize_phone($phone){
    return preg_replace('/[^0-9.]+/', '', $phone);
}

/**
 * Get Sent information   
 * @param  String $subject  [String with subject of an event]
 * @return String           [String with html of sent or not sent text and color]
 */
function get_sent_information($subject) {
    $regex = '#Sent:(.*?)\.#';
    preg_match($regex, $subject, $sent_dates);
    if(!empty($sent_dates)) {
        return "<span style='color:green'>Status: sent" . $sent_dates[1] . "</span>";
    } else {
        return "<span style='color:red'>Status: Not sent</span>";
    }
}