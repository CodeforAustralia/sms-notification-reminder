<?php

session_start();
session_unset();
//unset($_SESSION);
header("Location: /calendar.php");
die();
?>