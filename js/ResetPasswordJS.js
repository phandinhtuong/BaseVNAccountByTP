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
document.getElementById('confirm_password').addEventListener('keyup', function() {
    validatePasswordMatch();
});


function hideShowPasswords(){
    togglePasswordVisibility('password');
    togglePasswordVisibility('confirm_password')
}

function submitForm() {
    const form = document.getElementById("updatePasswordForm");
    if (validatePasswordMatch()) {
        let submitButton = form.querySelector("[type=submit]");
        submitButton.click();
    }
}