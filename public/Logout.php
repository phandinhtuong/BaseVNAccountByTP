<?php

require "../class/User.php";
require "service/UserService.php";
require_once "../logging/logByTP.php";

session_start();
beginLog("logout");

if (!empty($_SESSION['username'])) {

    $user = new User();
    $user->setUsername($_SESSION['username']);

    $userService = new UserService($user);

    if ($userService->clearToken()) {
        error_log("clear token user " . $_SESSION['username'] . " successfully");
    }

}

// Clear remember cookie
setcookie('remember', '', time() - 3600, '/');

// Clear session
session_unset();

// Destroy the session
session_destroy();

endLog("success","logout");

// Redirect to login page
//header("Location: login.php?error=logout");
header("Location: Login.php");
exit();
