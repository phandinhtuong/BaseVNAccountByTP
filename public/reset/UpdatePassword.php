<?php
require "../service/PasswordResetService.php";
require "../../class/User.php";
require "../../class/PasswordReset.php";
require "../service/UserService.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Validate
    if ($password !== $confirm) {
        die("Passwords don't match");
    }

    $passwordReset = new PasswordReset();
    $passwordReset->setToken($token);
    $passwordReset->setEmail($email);

    $passwordResetService = new PasswordResetService($passwordReset);

    $result = $passwordResetService->authPasswordToken();

    if ($result == null) {
        $error = "invalidResetToken";
        header('Location: ../login.php?error='.$error."&email=".$email);
        exit;

    } elseif ($result['expires_at'] < date('Y-m-d H:i:s')){
        $error = "expiredResetToken";
        header('Location: ../login.php?error='.$error."&email=".$email);
        exit;
    }

    // Update password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $user = new User();
    $user->setEmail($email);
    $user->setPassword($hashedPassword);

    $userService = new UserService($user);

    // Update user password
    if ($userService->updatePassword()){
        error_log("update password for email " . $email . " successfully");
    } else {
        error_log("update password for email " . $email . " failed");
    }

    // Delete reset token
    if ($passwordResetService->deletePasswordReset()) {
        error_log("delete token " . $token . " successfully");
    } else {
        error_log("delete token " . $token . " failed");
    }

    $error = "updatePasswordSuccessfully";
    header('Location: ../login.php?error='.$error."&email=".$email);
    exit;
}
?>