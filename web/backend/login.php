<?php

require_once 'config.php';

// Start or resume the session
session_start();

// Assuming you have a function to authenticate users
function authenticateUser($username, $password) {
    // Replace this with your actual user authentication logic
    global $login_user, $login_password;
    return ($username === $login_user && $password === $login_password);
}

// Retrieve credentials from POST request
$username = $_POST['username'];
$password = $_POST['password'];

// Authenticate the user
if (authenticateUser($username, $password)) {
    // Store user information in session variables
    $_SESSION['user_id'] = $username; // Store more data as needed

    // Set the session cookie parameters
    session_set_cookie_params([
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']), // Only secure if using HTTPS
        'samesite' => 'Strict'
    ]);

    // Respond with a success message
    echo json_encode(['message' => 'Login successful']);
} else {
    // Respond with an error message
    http_response_code(401);
    echo json_encode(['message' => 'Invalid credentials']);
}
?>
