<?php

require_once "../../logging/logByTP.php";

include('../view/commonView.html');

beginLog("ForgotPassword");
endLog("Success", "ForgotPassword");


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" type="text/css" href="../../css/css1.css">
    <link rel="stylesheet" type="text/css" href="../../css/css2.css">
    <link rel="stylesheet" type="text/css" href="../../css/css3.css">
    <script type="text/javascript" src="../../js/commonJS.js"></script>
    <script type="text/javascript" src="../../js/ForgotPasswordJS.js"></script>

</head>
<body>
    <a href="../../index.html">Home</a>
    <div class="forgot-container">
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
                            <form action='SendResetLink.php' method='post' id="resetForm">
                                <h1>Reset password</h1>
                                <div class='auth-sub-title'>Reset password by your email.</div>
                                <div class='form'>
                                    <div class='row'>
                                        <div class='label'>Email</div>

                                        <div class='input'>
                                            <input type='email' required name='email' placeholder='Your email'>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class='label'>
                                            <span class='a right normal url' onclick="redirectToLogin()">Remembered password? Login here</span>
                                        </div>
                                    </div>

                                    <div class='row relative xo'>
                                        <div class='submit' onclick="submitForm()" style="margin-top: 8px">Send reset password email</div>
                                        <button type="submit" name="reset-submit" style="display: none;">reset</button>
                                    </div>
                                    <a href="../Signup.php" class='a normal url'>Don't have an account? Sign up here</a>
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
        handleURLParams();
    </script>

</body>
</html>