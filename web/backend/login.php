<?php

require_once 'config.php';

// Start or resume the session
session_start();

// Function to authenticate users
function authenticateUser($username, $password, $users) {
    // Check if the username exists in the map and if the password matches
    return isset($users[$username]) && $users[$username] === $password;
}

// Retrieve credentials from POST request
$username = $_POST['username'] ?? null;
$password = $_POST['password'] ?? null;

// Authenticate the user
if ($username && $password && authenticateUser($username, $password, $users)) {
    // Store user information in session variables
    $_SESSION['user_id'] = $username;

    // Set the session cookie parameters
    session_set_cookie_params([
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']),
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
