// Function to toggle editing mode for personal details
document.getElementById("edit-personal-btn").addEventListener("click", function() {
    toggleEditPersonal(true);
});

// Function to toggle editing mode for security details
document.getElementById("edit-security-btn").addEventListener("click", function() {
    toggleEditSecurity(true);
});

// Function to toggle edit mode for personal details
function toggleEditPersonal(isEditing) {
    const fields = document.querySelectorAll("#detail-name, #detail-username, #detail-dob");
    const inputs = document.querySelectorAll("#input-name, #input-username, #input-dob");
    const button = document.getElementById("save-cancel-buttons");

    fields.forEach(field => field.style.display = isEditing ? 'none' : 'block');
    inputs.forEach(input => input.style.display = isEditing ? 'block' : 'none');
    button.style.display = isEditing ? 'block' : 'none';
}

// Function to toggle edit mode for security details
function toggleEditSecurity(isEditing) {
    const emailField = document.getElementById("detail-email");
    const emailInput = document.getElementById("input-email");
    const passwordGroup = document.querySelector(".password-input-group");

    emailField.style.display = isEditing ? 'none' : 'block';
    emailInput.style.display = isEditing ? 'block' : 'none';
    passwordGroup.style.display = isEditing ? 'block' : 'none';
    document.getElementById("save-cancel-buttons").style.display = isEditing ? 'block' : 'none';
}

// Save changes and update profile details
document.getElementById("save-changes-btn").addEventListener("click", function() {
    const name = document.getElementById("input-name").value;
    const username = document.getElementById("input-username").value;
    const dob = document.getElementById("input-dob").value;
    const email = document.getElementById("input-email").value;
    const currentPassword = document.getElementById("input-current-password").value;
    const newPassword = document.getElementById("input-new-password").value;
    const confirmPassword = document.getElementById("input-confirm-password").value;

    // Perform validation and AJAX request to save data (you would need to write the PHP script to handle this)
    // Here we send the data via AJAX to the backend
    fetch("update_user_details.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, username, dob, email, currentPassword, newPassword, confirmPassword })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Profile updated successfully");
            location.reload(); // Reload the page after successful update
        } else {
            alert("Error: " + data.error);
        }
    })
    .catch(error => console.error("Error:", error));
});

// Cancel button to revert changes
document.getElementById("cancel-changes-btn").addEventListener("click", function() {
    location.reload(); // Reload to cancel changes and revert to the original state
});
