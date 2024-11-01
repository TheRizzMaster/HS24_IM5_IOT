<?php

require_once 'database.php';
header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    // Ensure the request has a JSON Content-Type header
    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Content-Type must be application/json"
        ]);
        exit();
    }

    // Get JSON input and decode it
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate JSON structure for allowed actions
    if (!isset($input['action']) || !isset($input['card_id'])) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Invalid JSON structure"
        ]);
        exit();
    }

    $action = $input['action'];
    $card_id = $input['card_id'];
    $firstname = $input['firstname'] ?? null;
    $lastname = $input['lastname'] ?? null;

    // Process each action based on its type
    if ($action === "add_card") {
        // Check if card already exists in users table
        $query = "SELECT id FROM users WHERE card_id = :card_id LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':card_id' => $card_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // If card_id already exists, delete all work_sessions for this user_id
            $user_id = $user['id'];
            $deleteSessionsQuery = "DELETE FROM work_sessions WHERE user_id = :user_id";
            $deleteStmt = $pdo->prepare($deleteSessionsQuery);
            $deleteStmt->execute([':user_id' => $user_id]);

            echo json_encode([
                "status" => "success",
                "message" => "Existing time entries deleted for card_id",
                "user_id" => $user_id,
                "card_id" => $card_id
            ]);
        } else {
            // Register new card
            $insertQuery = "INSERT INTO users (id, card_id) VALUES (NULL, :card_id)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([':card_id' => $card_id]);

            // Fetch the generated user record
            $query = "SELECT id FROM users WHERE card_id = :card_id LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':card_id' => $card_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "message" => "Card registered successfully",
                "user_id" => $user['id'],
                "card_id" => $card_id
            ]);
        }

    } elseif ($action === "add_user_details") {
        // Ensure both firstname and lastname are provided
        if (empty($firstname) || empty($lastname)) {
            echo json_encode([
                "status" => "error",
                "message" => "firstname and lastname are required for adding user details"
            ]);
            exit();
        }

        // Check if card_id exists
        $query = "SELECT id FROM users WHERE card_id = :card_id LIMIT 1";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':card_id' => $card_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Update user details
            $user_id = $user['id'];
            $updateQuery = "UPDATE users SET firstname = :firstname, lastname = :lastname WHERE id = :id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([':firstname' => $firstname, ':lastname' => $lastname, ':id' => $user_id]);

            echo json_encode([
                "status" => "success",
                "message" => "User details updated",
                "user_id" => $user_id,
                "firstname" => $firstname,
                "lastname" => $lastname
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Card ID not found"
            ]);
        }

    } else {
        // Invalid action
        echo json_encode([
            "status" => "error",
            "message" => "Invalid action"
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
