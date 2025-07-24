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

const urlParams = new URLSearchParams(window.location.search);
//const container = document.querySelector('.login-container');

function displayError(){
    if (urlParams.has('error')) {
        console.error("error exists");
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error';

        // Map error codes to friendly messages
        const errorMessages = {
            'notAuthenticated': 'Not Authenticated, please log in again.',
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
}

function handleURLParams(){
    // Handle success message

    const savedValues = {
        email: urlParams.get('email'),
        remember: urlParams.get('remember')
    };

    if (savedValues.email) {
        document.querySelector('input[name="email"]').value = decodeURIComponent(savedValues.email);
    }
    if (savedValues.remember) {
        document.querySelector('input[name="remember"]').checked = true;
    }

    if (window.location.search) {
        // Remove all URL parameters without reloading the page
        const cleanUrl = window.location.origin + window.location.pathname;
        window.history.replaceState({}, document.title, cleanUrl);
    }

}

function displaySignupSuccess(){
    if (urlParams.get('registration') === 'success') {

        const msgDiv = document.createElement('div');
        msgDiv.className = 'success';

        //msgDiv.textContent = 'Registration successful! Please login.';
        //document.querySelector('.login-container').prepend(msgDiv);

        const msgMessages = {
            'success': 'Registration successful! Please login.',
            'default': 'Error. Please try again.'
        };

        const errorCode = urlParams.get('registration');
        showErrorModal(msgMessages[errorCode] || msgMessages['default']);

    }
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
}