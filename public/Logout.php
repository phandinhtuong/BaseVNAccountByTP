<?php

require "../class/User.php";
require "UserController.php";

session_start();

error_log("----------------------------------------------------------------");
error_log("begin logout");


error_log("SERVER VARIABLES:\n" . print_r($_SERVER, true));
error_log("SESSION VARIABLES:\n" . print_r($_SESSION, true));
error_log("POST VARIABLES:\n" . print_r($_POST, true));
error_log("COOKIE VARIABLES:\n" . print_r($_COOKIE, true));

if (!empty($_SESSION['username'])) {

    $user = new User();
    $user->setUsername($_SESSION['username']);

    $userController = new UserController($user);

    if ($userController->clearToken()) {
        error_log("clear token user " . $_SESSION['username'] . " successfully");
    }

}

// Clear remember cookie
setcookie('remember', '', time() - 3600, '/');

// Clear session
session_unset();

// Destroy the session
session_destroy();

error_log("end logout");
error_log("----------------------------------------------------------------\n");

// Redirect to login page
//header("Location: login.php?error=logout");
header("Location: login.php");
exit();
