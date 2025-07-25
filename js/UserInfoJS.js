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

        if (window.location.search) {
            // Remove all URL parameters without reloading the page
            const cleanUrl = window.location.origin + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }

        const errorDiv = document.createElement('div');
        errorDiv.className = 'error';

        // Map error codes to friendly messages
        const errorMessages = {
            'success': 'Edit successful',
            'default': 'Edit failed. Please try again.'
        };

        const errorCode = urlParams.get('error');
        showErrorModal(errorMessages[errorCode] || errorMessages['default']);

    }
}