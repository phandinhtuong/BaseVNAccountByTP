<?php

require_once dirname(__DIR__, 1) . '/schema/Config.php';

session_start();

// Initialize failed attempts counter to show CAPTCHA
if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = 0;
}

include('view/commonView.html');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../css/css1.css">
    <link rel="stylesheet" type="text/css" href="../css/css2.css">
    <link rel="stylesheet" type="text/css" href="../css/css3.css">
    <link rel="stylesheet" type="text/css" href="../css/inputCSS.css">
    <link rel="stylesheet" type="text/css" href="../css/captcha.css">

    <script type="text/javascript" src="../js/commonJS.js"></script>
    <script type="text/javascript" src="../js/LoginJS.js"></script>

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
                            <form action='controller/LoginController.php' method='post' id="loginForm">
                                <h1>Login</h1>
                                <div class='auth-sub-title'>Welcome back. Login to start working.</div>
                                <div class='form'>
                                    <div class='row'>
                                        <div class='label'>Email</div>
                                        <div class='input'>
                                            <input type='text' name='email' required placeholder='Your email'>
                                        </div>
                                    </div>

                                    <div class='row'>
                                        <div class='label'>
                                            <span class='a right normal url' onclick="redirectToForgotPassword()">Forget your password?</span>
                                            Password
                                        </div>
                                        <div class='input'>
                                            <input type='password' id='password' required name='password' placeholder='Your password'>
                                            <span class="password-toggle" onclick="hideShowPasswords()"><i class="show-icon">🔓</i></span>
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



    <script>
        displayError();
        displaySignupSuccess();
        handleURLParams();
    </script>
</body>
</html>