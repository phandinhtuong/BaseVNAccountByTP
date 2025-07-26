function handleURLParams() {

    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get('email');
    window.history.replaceState({}, '', window.location.pathname);

    if (email) {
        document.querySelector('input[name="email"]').value = decodeURIComponent(email);
    }
}
function redirectToLogin() {
    // Get the email value from the form
    const email = document.querySelector('input[name="email"]').value;

    // Redirect to forgot password page with email as parameter
    window.location.href = `../Login.php?email=${encodeURIComponent(email)}`;
}

function displayError() {
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
}

function submitForm() {
    const form = document.getElementById("resetForm");
    let submitButton = form.querySelector("[type=submit]");
    submitButton.click();
}