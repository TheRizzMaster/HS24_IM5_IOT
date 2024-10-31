<?php
// logout.php

// Start the session
session_start();

// Destroy the session
session_unset();      // Clear session variables
session_destroy();    // Destroy the session data on the server

// Invalidate the session cookie
setcookie(session_name(), '', time() - 3600, '/');

// Send a JSON response to confirm logout
echo json_encode(['message' => 'Logout successful']);
?>
