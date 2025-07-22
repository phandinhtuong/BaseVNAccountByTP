<?php

session_start();

error_log("----------------------------------------------------------------");
error_log("begin logout");


error_log("SERVER VARIABLES:\n" . print_r($_SERVER, true));
error_log("SESSION VARIABLES:\n" . print_r($_SESSION, true));
error_log("POST VARIABLES:\n" . print_r($_POST, true));


// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

error_log("end logout");
error_log("----------------------------------------------------------------");

// Redirect to login page
//header("Location: login.php?error=logout");
header("Location: login.php");
exit();
