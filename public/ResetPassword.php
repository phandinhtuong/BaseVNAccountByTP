<?php

require "PasswordController.php";
require "../class/PasswordReset.php";

if (empty($_GET['token'])) {
    die("Invalid reset token");
}
$token = $_GET['token'];

// Verify token
//$stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
//$stmt->execute([$token]);
//$reset = $stmt->fetch();

$passwordReset = new PasswordReset();
$passwordReset->setToken($token);

$passwordController = new PasswordController($passwordReset);

$result = $passwordController->authPasswordToken();

if ($result == null) {
    die("Invalid or expired reset token");
}
//
//if (!$reset) {
//    die("Invalid or expired reset token");
//}

// Show reset form
?>
<form method="post" action="UpdatePassword.php">
    <input type="text" name="token" value="<?= htmlspecialchars($token) ?>">
    <input type="text" name="email" value="<?= htmlspecialchars($result['email']) ?>">
    <input type="text" name="expires_at" value="<?= htmlspecialchars($result['expires_at']) ?>">
    <input type="text" name="now" value="<?= date('Y-m-d H:i:s') ?>">

    <h2>Reset Password</h2>
    <div class="form-group">
        <label>New Password</label>
        <input type="password" name="password" required minlength="8">
    </div>
    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required minlength="8">
    </div>
    <button type="submit">Update Password</button>
</form>