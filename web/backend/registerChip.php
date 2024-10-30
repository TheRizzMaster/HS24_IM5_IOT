<?php

require_once 'database.php';
header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    // Retrieve POST data
    $action = $_POST['action'] ?? null;
    $card_id = $_POST['card_id'] ?? null;
    $firstname = $_POST['firstname'] ?? null;
    $lastname = $_POST['lastname'] ?? null;

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
        // Handle new card registration or adding user details based on action
        if ($action === "add_card") {
            // Register new card
            $insertQuery = "INSERT INTO users (card_id) VALUES (:card_id)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([':card_id' => $card_id]);

            // Fetch the generated UUID to include in the response
            $stmt->execute([':card_id' => $card_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "status" => "success",
                "message" => "Card registered successfully",
                "user_id" => $user['id'],
                "card_id" => $card_id
            ]);

        } elseif ($action === "add_user_details") {
            if (empty($firstname) || empty($lastname)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "firstname and lastname are required for adding user details"
                ]);
                exit();
            }

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
                "message" => "Invalid action"
            ]);
        }
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
