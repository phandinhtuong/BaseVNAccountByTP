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

const urlParams = new URLSearchParams(window.location.search)

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