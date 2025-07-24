<?php

require_once "../../logging/logByTP.php";


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
                            <form action='SendResetLink.php' method='post' id="resetForm">
                                <h1>Reset password</h1>
                                <div class='auth-sub-title'>Reset password by your email.</div>
                                <div class='form'>
                                    <div class='row'>
                                        <div class='label'>Email</div>

                                        <div class='input'>
                                            <input type='text' name='email' placeholder='Your email'>
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
                                        <script>
                                            function submitForm() {
                                                const form = document.getElementById("resetForm");
                                                let submitButton = form.querySelector("[type=submit]");
                                                submitButton.click();
                                            }
                                        </script>
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
        const urlParams = new URLSearchParams(window.location.search);
        const email = urlParams.get('email');
        window.history.replaceState({}, '', window.location.pathname);
        if (email) {
            document.querySelector('input[name="email"]').value  = decodeURIComponent(email);
        }
        function redirectToLogin() {
            // Get the email value from the form
            const email = document.querySelector('input[name="email"]').value;

            // Redirect to forgot password page with email as parameter
            window.location.href = `../Login.php?email=${encodeURIComponent(email)}`;
        }

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

        if (urlParams.has('error')) {
            console.error("error exists");
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error';

            // Map error codes to friendly messages
            const errorMessages = {
                'emailNotFound': 'This email is not registered',
                'default': 'Send reset mail failed. Please try again.'
            };

            const errorCode = urlParams.get('error');
            showErrorModal(errorMessages[errorCode] || errorMessages['default']);
        }


    </script>

</body>
</html>