<?php
require "UserController.php";

session_start();
error_log("----------------------------------------------------------------");
error_log("begin auth");


error_log("SERVER VARIABLES:\n" . print_r($_SERVER, true));
error_log("SESSION VARIABLES:\n" . print_r($_SESSION, true));
error_log("POST VARIABLES:\n" . print_r($_POST, true));
error_log("COOKIE VARIABLES:\n" . print_r($_COOKIE, true));

function authenticateUser()
{
    // Check session first
    if (!empty($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }

    // Check "remember me" cookie
    if (!empty($_COOKIE['remember'])) {
        list($userName, $token) = explode(':', $_COOKIE['remember']);

        $user = new User();
        $user->setUsername($userName);

        $userController = new UserController($user);

        if ($userController->authToken($token)) {
            // Refresh session
            $_SESSION['username'] = $userName;
            return $userName;
        } else {
            // Invalid token - clear cookie
            error_log("Invalid token - clear cookie");
            setcookie('remember', '', time() - 3600, '/');
        }
    }

    error_log("Not authenticated");
    error_log("end auth");
    error_log("----------------------------------------------------------------\n");
    // Not authenticated
    header('Location: login.php');
    exit;
}

try {
    $currentUsername = authenticateUser();
} catch (Exception $e) {
    error_log("auth error: ".$e->getMessage() . ", at:" . $e->getTraceAsString());
}

error_log("end auth");
error_log("----------------------------------------------------------------\n");

