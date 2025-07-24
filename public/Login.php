<?php

require_once "../logging/logByTP.php";
require "../class/User.php";
require "controller/UserController.php";
require_once '../schema/Config.php';

session_start();
beginLog("login");



// Initialize failed attempts counter
if (!isset($_SESSION['failed_attempts'])) {
    error_log("session failed_attempts = 0");
    $_SESSION['failed_attempts'] = 0;
} else {
    error_log("session failed_attempts = " .$_SESSION['failed_attempts']);
}

function generateToken(): string
{
    try {
        return bin2hex(random_bytes(32));
    } catch (Exception $e) {
        logException("generateToken", $e);
        throw $e;
    }
}

try {
    if (isset($_POST['login-submit'])) {

        $user = new User();

        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $show_captcha = $_SESSION['failed_attempts'] >= numberOfFailedLoginsToShowCaptcha;
        $remember = isset($_POST['remember']);

        // CAPTCHA verification if needed
        if ($show_captcha) {
            if (empty($_POST['captcha']) || empty($_SESSION['captcha']) ||
                strtolower($_POST['captcha']) !== strtolower($_SESSION['captcha'])) {
                $error = "invalidCAPTCHA";
                $_SESSION['failed_attempts']++;
            }
        }

        if (!isset($error)) {
            $userController = new UserController($user);

            $result = $userController->login();

            if ($result['success']) {
                $userController->setUser($result['user']);
                $_SESSION['user_id'] = $result['user']->getId();
                $_SESSION['username'] = $result['user']->getUsername();
                unset($_SESSION['failed_attempts']);
                unset($_SESSION['captcha']);

                if ($remember) {
                    $token = generateToken();
                    $expires = date('Y-m-d H:i:s', strtotime('+'.numberOfDaysRemainingLogins.' days'));


                    if ($userController->saveToken()) {
                        error_log("user ". $result['user']->getUsername() . " save token successfully: ".$token . ", expires " . $expires);
                        // Set cookie (secure, HttpOnly, SameSite)
                        setcookie(
                            'remember',
                            $_SESSION['username'] . ':' . $token,
                            [
                                'expires' => strtotime('+'.numberOfDaysRemainingLogins.' days'),
                                'path' => '/',
                                'secure' => true, // HTTPS only
                                'httponly' => true,
                                'samesite' => 'Strict'
                            ]
                        );

                    } else {
                        endLog("user ". $result['user']->getUsername() . "save token error", "login");
                        header("Location: login.php?error=".$result['error']
                            ."&email=".urlencode($_POST['email'])."&remember=".urlencode($_POST['remember']));
                        exit();
                    }
                }
                endLog("login success", "login");
                header("Location: UserInfo.php");
                exit();
            } else {
                $_SESSION['failed_attempts']++;
                endLog("Login error: email: ".$_POST['email'] . ", error:" . $result['error'], "login");
                header("Location: login.php?error=".$result['error']
                    ."&email=".urlencode($_POST['email'])."&remember=".urlencode($_POST['remember']));
                exit();
            }
        } else {
            endLog("Login error: email: ".$_POST['email'] . ", error:" . $error, "login");
            header("Location: login.php?error=".$error
                ."&email=".urlencode($_POST['email'])."&remember=".urlencode($_POST['remember']));
            exit();
        }
    }
} catch (PDOException $e) {
    logException("login", $e);
    echo 'Error, please try again later';
    endLog("Error", "login");
}
endLog("success","login");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../css/css1.css">
    <link rel="stylesheet" type="text/css" href="../css/css2.css">
    <link rel="stylesheet" type="text/css" href="../css/css3.css">
</head>
<body>
    <a href="../index.html">Home</a>
    <div class="login-container">
        <div id='master' class='wf'>
            <div id='page'>
                <div id='auth' class='scrollable' data-autoscroll='1' data-autohide='1'>
                    <div class='box-wrap'>
                        <div class='auth-logo'>
                            <a href='../index.html'>
                                <img src='../images/logo.full.png'/>
                            </a>
                        </div>
                        <div class='box'>
                            <form action='Login.php' method='post' id="loginForm">
                                <h1>Login</h1>
                                <div class='auth-sub-title'>Welcome back. Login to start working.</div>
                                <div class='form'>
                                    <div class='row'>
                                        <div class='label'>Email</div>
                                        <div class='input'>
                                            <input type='text' name='email' placeholder='Your email'>
                                        </div>
                                    </div>

                                    <div class='row'>
                                        <div class='label'>
                                            <span class='a right normal url' onclick="redirectToForgotPassword()">Forget your password?</span>
                                            Password
                                        </div>
                                        <div class='input'>
                                            <input type='password' id='login-password' name='password' placeholder='Your password'>
                                        </div>
                                    </div>

                                    <?php if ($_SESSION['failed_attempts'] >= numberOfFailedLoginsToShowCaptcha): ?>
                                        <div class="captcha-group">
                                            <label>Enter the text from the image:</label>
                                            <div class="captcha-image">
                                                <img src="captcha.php?<?php echo time(); ?>" alt="CAPTCHA">
                                                <a href="#" onclick="document.querySelector('.captcha-image img').src='captcha.php?'+Date.now(); return false;">
                                                    Refresh
                                                </a>
                                            </div>
                                            <input type="text" name="captcha" required>
                                        </div>
                                    <?php endif; ?>

                                    <div class='row relative xo'>
                                        <div class='checkbox'>
                                            <input type='checkbox' id="remember" name='remember'>&nbsp;Keep me logged in
                                        </div>
                                        <div class='submit' onclick="submitForm()">Login</div>
                                        <button type="submit" name="login-submit" style="display: none;">Login</button>
                                        <script>
                                            function submitForm() {
                                                const form = document.getElementById("loginForm");
                                                let submitButton = form.querySelector("[type=submit]");
                                                submitButton.click();
                                            }
                                        </script>
                                    </div>
                                    <span class='a normal url' onclick="redirectToSignup()">Don't have an account? Sign up here</span>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="errorModal" style="width: 480px; display: none;">
        <div class="__wtdialog __apalert __dialog __dialog_ontop" id="__apdialog_alert" style="">
            <div class="__dialogwrapper" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: auto;">
                <div class="__dialogwrapper-inner">
                    <div class="__dialogmain">
                        <div class="__dialogclose" onclick="hideErrorModal()">
                            <span class="-ap icon-close"/>
                        </div>
                        <div class="__dialogcontent">
                            <div id="alert" style="" class="__apdialog" title="">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td class="icon">
                                            <span class="-ap icon-help-with-circle" style="font-size:40px; color:#666"></span>
                                        </td>
                                        <td class="text" id="errorMessage"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="__dialogbuttons unselectable" onclick="hideErrorModal()">
                            <div class="button er alert-button" >OK</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        // Show error modal with custom message
        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').style.display = 'block';
        }

        // Hide error modal
        function hideErrorModal() {
            document.getElementById('errorModal').style.display = 'none';
        }

        // Close when clicking outside modal
        window.onclick = function(event) {
            const errorModal = document.getElementById('errorModal');
            if (event.target === errorModal) {
                hideErrorModal();
            }

            // Keep your existing editModal close logic
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                hideEditModal();
            }
        }

        // Handle success message
        const urlParams = new URLSearchParams(window.location.search);
        const container = document.querySelector('.login-container');

        const savedValues = {
            email: urlParams.get('email'),
            remember: urlParams.get('remember')
        };

        if (urlParams.get('registration') === 'success') {


            const msgDiv = document.createElement('div');
            msgDiv.className = 'success';
            msgDiv.textContent = 'Registration successful! Please login.';
            document.querySelector('.login-container').prepend(msgDiv);

            // Clean URL
            window.history.replaceState({}, '', window.location.pathname);
            if (savedValues.email) {
                document.querySelector('input[name="email"]').value = decodeURIComponent(savedValues.email);
            }
            if (savedValues.remember) {
                document.querySelector('input[name="remember"]').checked = true;
            }

        }

        if (urlParams.has('error')) {
            console.error("error exists");
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error';

            // Map error codes to friendly messages
            const errorMessages = {
                'nullEmailOrPassword': 'Please input email and password',
                'noUser': 'User not found',
                'wrongPassword': 'Incorrect password',
                'invalidCAPTCHA': 'Invalid CAPTCHA, please try again',
                'resetLinkSent': 'Reset password email sent, please check your inbox and follow the instruction.',
                'updatePasswordSuccessfully': 'Update password successfully.',
                'invalidResetToken': 'Invalid reset token.',
                'expiredResetToken': 'Expired reset token.',
                'default': 'Login failed. Please try again.'
            };

            const errorCode = urlParams.get('error');
            showErrorModal(errorMessages[errorCode] || errorMessages['default']);
        }

        if (window.location.search) {
            // Remove all URL parameters without reloading the page
            const cleanUrl = window.location.origin + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }

        if (savedValues.email) {
            document.querySelector('input[name="email"]').value = decodeURIComponent(savedValues.email);
        }

        if (savedValues.remember) {
            document.querySelector('input[name="remember"]').checked = true;
        }

        function redirectToForgotPassword() {
            // Get the email value from the form
            const email = document.querySelector('input[name="email"]').value;

            // Redirect to forgot password page with email as parameter
            window.location.href = `reset/ForgotPassword.php?email=${encodeURIComponent(email)}`;
        }

        function redirectToSignup() {
            // Get the email value from the form
            const email = document.querySelector('input[name="email"]').value;

            // Redirect to forgot password page with email as parameter
            window.location.href = `signup.php?email=${encodeURIComponent(email)}`;
        }


    </script>

</body>
</html>