<?php
include('view/commonView.html');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <script type="text/javascript" src="../js/commonJS.js"></script>
    <script type="text/javascript" src="../js/SignupJS.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/css1.css">
    <link rel="stylesheet" type="text/css" href="../css/css2.css">
    <link rel="stylesheet" type="text/css" href="../css/css3.css">
    <link rel="stylesheet" type="text/css" href="../css/inputCSS.css">


</head>
<body>
    <a href="../index.html">Home</a>
    <div class="signup-container">
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
                            <form action='controller/SignupController.php' method='post' id="signupForm" onsubmit="return validatePasswordMatch()">
                                <h1>Signup</h1>
                                <div class='auth-sub-title'>Welcome. Signup to login.</div>
                                <div class='form'>
                                    <div class='row'>
                                        <div class='label'>Email</div>
                                        <div class='input'>
                                            <input type='text' name='email' required placeholder='Your email'>
                                        </div>
                                    </div>

                                    <div class='row'>
                                        <div class='label'>Username</div>
                                        <div class='input'>
                                            <input type='text' name='username' required placeholder='Your username'>
                                        </div>
                                    </div>

                                    <div class='row'>
                                        <div class='label'>
                                            Password
                                        </div>
                                        <div class='input'>
                                            <input type='password' id='password' name='password' required minlength="8" placeholder='Your password'>
                                            <span class="password-toggle" onclick="hideShowPasswords()"><i class="show-icon">🔓</i></span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class='label'>Confirm Password</div>
                                        <div class='input'>
                                            <input type='password' id="confirm_password" name='confirm_password' required minlength="8" placeholder='Confirm your password'>
                                            <span class="password-toggle" onclick="hideShowPasswords()"><i class="show-icon">🔓</i></span>
                                        </div>
                                    </div>
                                    <div id="passwordError" style="color: red; display: none;">Passwords do not match!</div>


                                    <div class='row'>
                                        <div class='label'>Full name</div>
                                        <div class='input'>
                                            <input type='text' name='name' required placeholder='Your full name'>
                                        </div>
                                    </div>



                                    <div class='row relative xo'>
                                        <div class='submit' onclick="submitForm()">Sign up</div>
                                        <button type="submit" name="signup" style="display: none;">Sign up</button>
                                    </div>
                                    <span class='a normal url' onclick="redirectToLogin()">Already had an account? Login here</span>

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
        document.getElementById('confirm_password').addEventListener('keyup', function() {
            validatePasswordMatch();
        });
    </script>

</body>
</html>