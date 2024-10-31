<?php
// protected-endpoint.php

// Start the session
session_start();

// Check if user is authenticated
if (isset($_SESSION['user_id'])) {
    echo json_encode(['message' => 'Access granted']);
} else {
    http_response_code(401); // Send 401 if not authenticated
    echo json_encode(['message' => 'Unauthorized']);
}
?>
