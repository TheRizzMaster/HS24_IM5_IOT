<?php

require_once 'database.php';


try{

    $pdo = new PDO($dsn, $username, $password, $options);

    // Get card_id from HTTP request
    $card_id = $_GET['card_id'];

    if (empty($card_id)) {
        echo json_encode([
            "status" => "error",
            "message" => "card_id is required"
        ]);
        exit();
    }

    // Find the user_id associated with the card_id
    $query = "SELECT id FROM users WHERE card_id = :card_id LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':card_id' => $card_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user_id = $user['id'];

        // Check for active session for this user
        $checkSessionQuery = "SELECT id FROM work_sessions WHERE user_id = :user_id AND end_time IS NULL LIMIT 1";
        $checkSessionStmt = $pdo->prepare($checkSessionQuery);
        $checkSessionStmt->execute([':user_id' => $user_id]);
        $activeSession = $checkSessionStmt->fetch(PDO::FETCH_ASSOC);

        if ($activeSession) {
            // End the session if one is active
            $end_time = date("Y-m-d H:i:s");
            $sessionId = $activeSession['id'];
            $updateQuery = "UPDATE work_sessions SET end_time = :end_time WHERE id = :id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([':end_time' => $end_time, ':id' => $sessionId]);

            echo json_encode([
                "status" => "success",
                "message" => "Session ended",
                "user_id" => $user_id,
                "end_time" => $end_time
            ]);
        } else {
            // Start a new session if none is active
            $start_time = date("Y-m-d H:i:s");
            $description = "Started new session";
            $insertQuery = "INSERT INTO work_sessions (user_id, start_time) VALUES (:user_id, :start_time)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([':user_id' => $user_id, ':start_time' => $start_time]);

            echo json_encode([
                "status" => "success",
                "message" => "New session started",
                "user_id" => $user_id,
                "start_time" => $start_time
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Card ID not found"
        ]);
    }

}catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

