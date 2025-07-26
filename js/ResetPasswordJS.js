
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