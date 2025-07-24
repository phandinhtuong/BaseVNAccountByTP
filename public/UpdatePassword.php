<?php
require "PasswordController.php";
require "../class/User.php";
require "../class/PasswordReset.php";
require "UserController.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Validate
    if ($password !== $confirm) {
        die("Passwords don't match");
    }

    // Verify token again
    //$stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND email = ? AND expires_at > NOW()");
    //$stmt->execute([$token, $email]);
    //$reset = $stmt->fetch();

    $passwordReset = new PasswordReset();
    $passwordReset->setToken($token);
    $passwordReset->setEmail($email);


    $passwordController = new PasswordController($passwordReset);


    $result = $passwordController->authPasswordToken();

//    if (!$reset) {
//        die("Invalid or expired reset token");
//    }

    // Update password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $user = new User();
    $user->setEmail($email);
    $user->setPassword($hashedPassword);

    $userController = new UserController($user);

    // Update user password
    if ($userController->updatePassword()){
        error_log("update password for email " . $email . " successfully");
    } else {
        error_log("update password for email " . $email . " failed");
    }

    // Delete reset token
    if ($passwordController->deletePasswordReset()) {
        error_log("delete token " . $token . " successfully");
    } else {
        error_log("delete token " . $token . " failed");
    }

    $error = "updatePasswordSuccessfully";
    header('Location: login.php?error='.$error."&email=".$email);
    exit;
}
?>