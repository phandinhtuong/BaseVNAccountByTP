

function handleURLParams(){
    const email = urlParams.get('email');
    window.history.replaceState({}, '', window.location.pathname);
    if (email) {
        document.querySelector('input[name="email"]').value  = decodeURIComponent(email);
    }
}


function redirectToLogin() {
    // Get the email value from the form
    const email = document.querySelector('input[name="email"]').value;

    // Redirect to forgot password page with email as parameter
    window.location.href = `Login.php?email=${encodeURIComponent(email)}`;
}

function displayError() {
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
        showErrorModal(errorMessages[errorCode] || errorMessages['default']);

    }
}

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


function hideShowPasswords(){
    togglePasswordVisibility('password');
    togglePasswordVisibility('confirm_password')
}

function submitForm() {
    const form = document.getElementById("signupForm");
    if (validatePasswordMatch()) {
        let submitButton = form.querySelector("[type=submit]");
        submitButton.click();
    }
}