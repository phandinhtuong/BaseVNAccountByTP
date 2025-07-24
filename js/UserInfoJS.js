function showEditModal() {
    document.getElementById('editModal').style.display = 'block';
}

function hideEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) {
        hideEditModal();
    }
}

function displayError() {
    if (urlParams.has('error')) {

        // if (window.location.search) {
        //     // Remove all URL parameters without reloading the page
        //     const cleanUrl = window.location.origin + window.location.pathname;
        //     window.history.replaceState({}, document.title, cleanUrl);
        // }

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