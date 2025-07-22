<?php

require "../class/User.php";
require "UserController.php";

session_start();

error_log("----------------------------------------------------------------");
error_log("begin login form");


error_log("SERVER VARIABLES:\n" . print_r($_SERVER, true));
error_log("SESSION VARIABLES:\n" . print_r($_SESSION, true));
error_log("POST VARIABLES:\n" . print_r($_POST, true));

// Initialize failed attempts counter
if (!isset($_SESSION['failed_attempts'])) {
    error_log("session failed_attempts = 0");
    $_SESSION['failed_attempts'] = 0;
} else {
    error_log("session failed_attempts = " .$_SESSION['failed_attempts']);
}

try {
    if (isset($_POST['login-submit'])) {

        //$db = connectToDatabase();

        // Get form data
        //$userInput = trim($_POST['email']);
        //$password = $_POST['password'];

        $user = new User();

        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $show_captcha = $_SESSION['failed_attempts'] >= 3;

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


            // Validate inputs
            //if (empty($userInput) || empty($password)) {
            //    header("Location: login.php?error=emptyfields");
            //    exit();
            //}
            $result = $userController->login();

            if ($result['success']) {
                $_SESSION['user_id'] = $result['user']->getId();
                $_SESSION['username'] = $result['user']->getUsername();
                unset($_SESSION['failed_attempts']);
                unset($_SESSION['captcha']);

                error_log("login success");
                error_log("end login form");
                error_log("----------------------------------------------------------------");
                header("Location: UserInfo.php");
                exit();
            } else {
    //            foreach ($result['errors'] as $error) {
    //                echo "<p style='color:red;'>$error</p>";
    //            }
                $_SESSION['failed_attempts']++;
                error_log("Login error: email: ".$_POST['email'] . ", error:" . $result['error']);
                error_log("end login form");
                error_log("----------------------------------------------------------------");
                header("Location: login.php?error=".$result['error']
                    ."&email=".urlencode($_POST['email']));
                exit();
            }
        } else {
            error_log("Login error: email: ".$_POST['email'] . ", error:" . $error);
            error_log("end login form");
            error_log("----------------------------------------------------------------");
            header("Location: login.php?error=".$error
                ."&email=".urlencode($_POST['email']));
            exit();
        }
    }
} catch (PDOException $e) {
    error_log("Login error: ".$e->getMessage() . ", at:" . $e->getTraceAsString());
    echo 'Error, please try again later';
    error_log("end login form");
    error_log("----------------------------------------------------------------");
}
error_log("end login form");
error_log("----------------------------------------------------------------");

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
<!--        <h1>let's login</h1>-->
<!--        <form action="Login.php" method="post">-->

<!--            <label>email</label>-->
<!--            <label>-->
<!--                <input type="email" name="email">-->
<!--            </label>-->
<!--            <br>-->

<!--            <label>password</label>-->
<!--            <label>-->
<!--                <input type="password" name="password">-->
<!--            </label>-->
<!--            <br>-->
<!--            <button type="submit" name="login-submit">Login</button>-->
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
                                            <span class='a right normal url' data-url='a/recover' onclick='AP.toURL(Lang.getUrlWithSelectedLang("a/recover"));'>Forget your password?</span>
                                            Password
                                        </div>
                                        <div class='input'>
                                            <input type='password' id='login-password' name='password' placeholder='Your password'>
                                        </div>
                                    </div>

                                    <?php if ($_SESSION['failed_attempts'] >= 3): ?>
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
                                            <input type='checkbox' checked name='saved'>&nbsp;Keep me logged in

                                        </div>
<!--                                        <div class='submit' onclick='Account.login(this);'>Login</div>-->
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
                                    <a href="signup.php">Don't have an account? Sign up here</a>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

<!--    <div style="margin-top: 15px; text-align: center;">-->
<!--        <a href="signup.html">Don't have an account? Register here</a>-->
<!--    </div>-->

    <!-- Error Popup (similar style to editModal) -->
<!--    <div id="errorModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); z-index: 1000;">-->
<!--        <div style="background-color:white; margin:100px auto; padding:20px; width:80%; max-width:500px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1);">-->
<!--            <span style="float:right; cursor:pointer; font-size:24px;" onclick="hideErrorModal()">×</span>-->
<!--            <h2 style="color:#d33; margin-top:0;">Error</h2>-->
<!--            <div id="errorMessage" style="margin:20px 0; color:#555;">-->
<!--                &lt;!&ndash; Error message will be inserted here &ndash;&gt;-->
<!--            </div>-->
<!--            <div style="text-align:center;">-->
<!--                <button onclick="hideErrorModal()" style="background:#d33; color:white; border:none; padding:8px 16px; border-radius:4px; cursor:pointer;">OK</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

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
            email: urlParams.get('email')
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
                'default': 'Login failed. Please try again.'
            };

            const errorCode = urlParams.get('error');
            //errorDiv.textContent = errorMessages[errorCode] || errorMessages['default'];
            //container.prepend(errorDiv);
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

    </script>

</body>
</html>