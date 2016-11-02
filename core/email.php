<?php
  class Email {
    /**
     * [Get email template for mailing summary report]
     * @return [string] [html with template of summary report]
     */
      public static function getTemplate() {
          $template = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/include_files/email_template.php');
          return $template;
      }
      
      /**
       * [Set information into html rows for mailing summary report]
       * @param [string] $rows [html with rows for mailing summary report]
       */
      public static function setRowsInTemplate($rows) {
          $output = '';
          
          foreach($rows as $row){
              $output .= "<tr style='border-bottom:1px solid #EAEAEA'>
    						<td style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>" . $row[0] . "</td>
    						<td style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>" . $row[1] . "</td>
    					  </tr>";
          }
          return $output;
      }
      
      /**
       * [replace tags within template with information provided]
       * @param  [array] $data [information to be set into mailing template, subject and rows]
       * @return [string]       [final html to be send as a report]
       */
      public static function mergeTemplateData($data){
          $template = self::getTemplate();
          $rows     = self::setRowsInTemplate($data['rows']);
          $subject  = $data['subject'];
          
          $template = str_replace("*|SUBJECT|*", $subject, $template);
          $template = str_replace("*|ROWS|*", $rows, $template);
          return $template;
      }
      
      /**
       * [Get emails from file with a JSON object]
       * @return [array]       [email and name addresses]
       */
      public static function getEmailsFromFile() {
        $email_path = $_SERVER['DOCUMENT_ROOT'] . "/include_files/emails.txt";
        $file       = file_get_contents($email_path);
        $emails     = json_decode($file);
        return $emails;
      }
  }