<h1>VLA - SMS Notification reminder</h1>
<h2>Status: Alpha</h2>
<p>This SMS notification service was built for Victoria Legal Aid by the 2016, Code for Australia VLA Fellowship team. The tool was built specifically for admins across the organization tasked with sending SMS  to Legal Aid clients for appointment, court, and other reminders specific to the services clients access at VLA.</p>
<p>The purpose of creating this app was to increase the efficieny in admins workflows, reducing the time they spend individually messaging clients, and show the organization the benefits of builidng technology internally for user needs. The tool is currently live in 5 offices and initial metrics show an 80% decrease in time spent on this task and increased client participation.</p>
<h2>Demo</h2>
<iframe src="https://player.vimeo.com/video/198614737" width="640" height="363" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
<p><a href="https://vimeo.com/198614737">SMS_DEMO</a> from <a href="https://vimeo.com/user61290380">VLA Fellows</a> on <a href="https://vimeo.com">Vimeo</a>.</p>
<h2> Technical Documentation</h2>


The intention of this document is to give a guide of how to create a PHP + JS app that retrieves calendar events in Office 365 or Outlook.com. 

You should have a web server with PHP installed and working, either on your development machine or on a remote server. The tutorial was created using PHP 5.6.0 running on Ubuntu 14.04.

>This app use cURL to send oAuth2 and REST API calls.
>This app use Mustache.js to handle html templates between webservices and front-end.


### The app

This application provides a link to the user as soon as they arrive to the home page by first time. The link will take users to a login page provided by their Office 365 or Outlook.com and will grant access to the application and users will be redirectec to our application.

##### calendar.php
In this file we check if an user is logged in and redirect them the app.

##### Create your app in Outlook
Go to https://apps.dev.microsoft.com/ and register your new application, you should have a Microsoft account (Outlook.com, Hotmail.com, Live.com, MSN.com, and Passport.com) to access the registration centre and get a client Id and secret Id.

Add web under plataforms and register the url http://{{your-domain}}/core/authorize.php under redirect URIs, then sabe and copy the client Id and client Secret that you get from this page.

##### Set up your credentials in oauth2.php
 
```sh
<?php
  class oAuthService {
    private static $clientId = "YOUR CLIENT ID";
    private static $clientSecret = "YOUR CLIENT SECRET";
    private static $authority = "https://login.microsoftonline.com";
    private static $authorizeUrl = '/common/oauth2/v2.0/authorize?client_id=%1$s&redirect_uri=%2$s&response_type=code&scope=%3$s';
```
