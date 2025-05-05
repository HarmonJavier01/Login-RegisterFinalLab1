<?php
// Validation functions

// Validate email format
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate password strength (minimum 6 characters)
function validate_password($password) {
    return strlen($password) >= 6;
}

// Validate name (non-empty, max length 100)
function validate_name($name) {
    return !empty($name) && strlen($name) <= 100;
}

// Validate contact fields (name required, email optional but valid if provided)
function validate_contact($data) {
    $errors = [];

    if (empty($data['name']) || strlen($data['name']) > 100) {
        $errors[] = "Contact name is required and must be less than 100 characters.";
    }

    if (!empty($data['email']) && !validate_email($data['email'])) {
        $errors[] = "Contact email is invalid.";
    }

    // Additional validations can be added here

    return $errors;
}
?>
