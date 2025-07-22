<?php

session_start();

require "../class/User.php";
require "UserController.php";

try {
    if (isset($_POST['signup'])) {

        $user = new User();

        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $user->setUsername($_POST['username']);
        $user->setName($_POST['name']);

        $userController = new UserController($user);


        //$errors = $userController->checkValidUserSignup();
        $result = $userController->checkValidUserSignup();

        //if (empty($errors)) {
        if ($result['success']) {

            if ($userController->signup()){
                header("Location: login.php?registration=success"."&email=".urlencode($_POST['email']));
                exit();
            };

        } else {
//            foreach ($errors as $error) {
//                echo "<p style='color:red;'>$error</p>";
//            }


            header("Location: signup.html?error=".$result['error']
                        ."&name=".urlencode($_POST['name'])
                        ."&username=".urlencode($_POST['username'])
                        ."&email=".urlencode($_POST['email']));
            exit();

        }

    } else {
        error_log("signup:  not found");
        echo 'Error, please try again later';
    }

} catch (PDOException $e) {
    error_log("Signup error: ".$e->getMessage() . ", at:" . $e->getTraceAsString());
    echo 'Error, please try again later';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <script type="text/javascript" src="../js/js1.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/css1.css">
    <link rel="stylesheet" type="text/css" href="../css/css2.css">
    <link rel="stylesheet" type="text/css" href="../css/css3.css">


</head>
<body>
<a href="../index.html">Home</a>
<div class="signup-container">
    <!--        <h1>let's signup</h1>-->
    <!--        <form action="Signup.php" method="post">-->
    <!--            <label>name</label>-->
    <!--            <label>-->
    <!--                <input type="text" name="name">-->
    <!--            </label>-->
    <!--            <br>-->

    <!--            <label>username</label>-->
    <!--            <label>-->
    <!--                <input type="text" name="username">-->
    <!--            </label>-->
    <!--            <br>-->

    <!--            <label>email</label>-->
    <!--            <label>-->
    <!--                <input type="email" name="email">-->
    <!--            </label>-->
    <!--            <br>-->

    <!--            <label>pass</label>-->
    <!--            <label>-->
    <!--                <input type="password" name="password">-->
    <!--            </label>-->
    <!--            <br>-->
    <!--            <button type="submit" name="signup">Register</button>-->
    <!--        </form>-->
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
                        <form action='Signup.php' method='post' id="signupForm">
                            <h1>Signup</h1>
                            <div class='auth-sub-title'>Welcome. Signup to login.</div>
                            <div class='form'>
                                <div class='row'>
                                    <div class='label'>Email</div>
                                    <div class='input'>
                                        <input type='text' name='email' placeholder='Your email'>
                                    </div>
                                </div>

                                <div class='row'>
                                    <div class='label'>
                                        Password
                                    </div>
                                    <div class='input'>
                                        <input type='password' id='login-password' name='password' placeholder='Your password'>
                                    </div>
                                </div>

                                <div class='row'>
                                    <div class='label'>Full name</div>
                                    <div class='input'>
                                        <input type='text' name='name' placeholder='Your full name'>
                                    </div>
                                </div>

                                <div class='row'>
                                    <div class='label'>Username</div>
                                    <div class='input'>
                                        <input type='text' name='username' placeholder='Your username'>
                                    </div>
                                </div>

                                <div class='row relative xo'>

                                    <!--                                        <div class='submit' onclick='Account.login(this);'>Login</div>-->
                                    <div class='submit' onclick="submitForm()">Sign up</div>
                                    <button type="submit" name="signup" style="display: none;">Sign up</button>
                                    <script>
                                        function submitForm() {
                                            const form = document.getElementById("signupForm");
                                            let submitButton = form.querySelector("[type=submit]");
                                            submitButton.click();
                                        }
                                    </script>
                                </div>
                                <a href="login.php">Already had an account? Login here</a>
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
                                        <span class="-ap icon-help-with-circle" style="font-size:40px; color:#666"/>
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

    //window.addEventListener('load', positionDialog);
    // if (document.readyState === 'loading') {
    //     window.addEventListener('load', positionDialog);
    //
    //     console.log("loading");
    // } else {
    //     console.log("loaded");
    //     positionDialog(); // DOM already ready
    // }
    // window.addEventListener('resize', positionDialog);


    // Show error modal with custom message


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
    const container = document.querySelector('.signup-container');


    if (urlParams.has('error')) {
        const savedValues = {
            name: urlParams.get('name'),
            username: urlParams.get('username'),
            email: urlParams.get('email')
        };

        if (window.location.search) {
            // Remove all URL parameters without reloading the page
            const cleanUrl = window.location.origin + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }

        if (savedValues.name) {
            document.querySelector('input[name="name"]').value = decodeURIComponent(savedValues.name);
        }
        if (savedValues.username) {
            document.querySelector('input[name="username"]').value = decodeURIComponent(savedValues.username);
        }
        if (savedValues.email) {
            document.querySelector('input[name="email"]').value = decodeURIComponent(savedValues.email);
        }

        console.error("error exists");
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error';

        // Map error codes to friendly messages
        const errorMessages = {
            'nullName': 'Name is required',
            'nullUsername': 'Username is required',
            'nullEmail': 'Email is required',
            'invalidEmail': 'Invalid email format',
            'nullPassword': 'Password is required',
            'weakPassword': 'Password must be at least 8 characters',
            'usernameExists': 'Username or email already exists',
            'default': 'Signup failed. Please try again.'
        };

        const errorCode = urlParams.get('error');
        //errorDiv.textContent = errorMessages[errorCode] || errorMessages['default'];
        //container.prepend(errorDiv);
        showErrorModal(errorMessages[errorCode] || errorMessages['default']);





    }

</script>

</body>
</html>