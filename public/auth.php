<?php
require "UserController.php";
require_once "../logging/logByTP.php";
session_start();
beginLog("auth");

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

    endLog("Not authenticated", "auth");
    // Not authenticated
    header('Location: login.php');
    exit;
}

try {
    $currentUsername = authenticateUser();
} catch (Exception $e) {
    logException("auth", $e);
}

endLog("Success", "auth");


