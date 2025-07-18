// function positionDialog() {
//     const dialog = document.querySelector('.__dialogwrapper');
//     const dialogWidth = dialog.offsetWidth;
//     const dialogHeight = dialog.offsetHeight;
//
//     // Calculate center position
//     const left = (window.innerWidth - dialogWidth) / 2;
//     const top = (window.innerHeight - dialogHeight) / 2;
//
//     // Apply styles
//     dialog.style.left = `${left}px`;
//     dialog.style.top = `${top}px`;
//
// }

function showErrorModal(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorModal').style.display = 'block';
}