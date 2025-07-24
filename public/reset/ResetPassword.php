<?php

require "../controller/PasswordController.php";
require "../../class/PasswordReset.php";

if (empty($_GET['token'])) {
    die("Invalid reset token");
}
$token = $_GET['token'];

$passwordReset = new PasswordReset();
$passwordReset->setToken($token);

$passwordController = new PasswordController($passwordReset);

$result = $passwordController->authPasswordToken();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" type="text/css" href="../../css/css1.css">
    <link rel="stylesheet" type="text/css" href="../../css/css2.css">
    <link rel="stylesheet" type="text/css" href="../../css/css3.css">
    <link rel="stylesheet" type="text/css" href="../../css/inputCSS.css">
</head>
<body>
    <a href="../../index.html">Home</a>
    <div class="login-container">
        <div id='master' class='wf'>
            <div id='page'>
                <div id='auth' class='scrollable' data-autoscroll='1' data-autohide='1'>
                    <div class='box-wrap'>
                        <div class='auth-logo'>
                            <a href='../../index.html'>
                                <img src='../../images/logo.full.png'/>
                            </a>
                        </div>
                        <div class='box'>

                            <?php if ($result == null): ?>
                                <div class="form-group">
                                    <h1>Invalid or expired reset token</h1>
                                    <a href="../Login.php" class="button">Click here to login</a>
                                </div>

                            <?php elseif ($result['expires_at'] < date('Y-m-d H:i:s')): ?>
                                <input type="hidden" name="expires_at" value="<?= htmlspecialchars($result['expires_at']) ?>">
                                <input type="hidden" name="now" value="<?= date('Y-m-d H:i:s') ?>">
                                <div class="form-group">
                                    <h1>Expired reset token</h1>
                                    <a href="../Login.php" class="button">Click here to login</a>
                                </div>

                            <?php else: ?>

                                <form action='UpdatePassword.php' method='post' id="updatePasswordForm" onsubmit="return validatePasswordMatch()">
                                    <h1>Reset password</h1>
                                    <div class='auth-sub-title'>Update your new password.</div>

                                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                                    <input type="hidden" name="email" value="<?= htmlspecialchars($result['email']) ?>">

                                    <div class='form'>
                                        <div class="row">
                                            <div class='label'>New Password</div>
                                            <div class='input'>
                                                <input type='password' id="password" name='password' required minlength="8" placeholder='Your new password'>
                                                <span class="password-toggle" onclick="hideShowPasswords()"><i class="show-icon">🔓</i></span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class='label'>Confirm Password</div>
                                            <div class='input'>
                                                <input type='password' id="confirm_password" name='confirm_password' required minlength="8" placeholder='Confirm your new password'>
                                                <span class="password-toggle" onclick="hideShowPasswords()"><i class="show-icon">🔓</i></span>
                                            </div>
                                        </div>

                                        <div id="passwordError" style="color: red; display: none;">Passwords do not match!</div>

                                        <div class='row relative xo'>
                                            <div class='submit' onclick="submitForm()" style="margin-top: 8px">Update Password</div>
                                            <button type="submit" name="update-submit" style="display: none;">update</button>
                                            <script>
                                                function submitForm() {
                                                    const form = document.getElementById("updatePasswordForm");
                                                    if (validatePasswordMatch()) {
                                                        let submitButton = form.querySelector("[type=submit]");
                                                        submitButton.click();
                                                    }
                                                }
                                            </script>
                                        </div>

                                    </div>
                                </form>


                                <script>
                                    function validatePasswordMatch() {
                                        const password = document.getElementById('password').value;
                                        const confirmPassword = document.getElementById('confirm_password').value;
                                        const errorElement = document.getElementById('passwordError');

                                        if (password !== confirmPassword) {
                                            errorElement.style.display = 'block';
                                            return false; // Prevent form submission
                                        }

                                        errorElement.style.display = 'none';
                                        return true; // Allow form submission
                                    }
                                    document.getElementById('confirm_password').addEventListener('keyup', function() {
                                        validatePasswordMatch();
                                    });

                                    function togglePasswordVisibility(inputId) {
                                        const input = document.getElementById(inputId);
                                        const toggle = input.nextElementSibling.querySelector('i');

                                        if (input.type === 'password') {
                                            input.type = 'text';
                                            toggle.textContent = '🔒'; // Or change icon class for Font Awesome
                                            toggle.title = 'Hide password';
                                        } else {
                                            input.type = 'password';
                                            toggle.textContent = '🔓'; // Or change icon class for Font Awesome
                                            toggle.title = 'Show password';
                                        }
                                    }

                                    function hideShowPasswords(){
                                        togglePasswordVisibility('password');
                                        togglePasswordVisibility('confirm_password')
                                    }

                                </script>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


</body>
</html>