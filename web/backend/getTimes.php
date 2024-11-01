<?php

require_once 'database.php';


header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle adding a description to a work session
        $session_id = $_POST['session_id'] ?? null;
        $description = $_POST['description'] ?? null;

        if (empty($session_id) || empty($description)) {
            echo json_encode([
                "status" => "error",
                "message" => "session_id and description are required"
            ]);
            exit();
        }

        $updateQuery = "UPDATE work_sessions SET description = :description WHERE id = :session_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([':description' => $description, ':session_id' => $session_id]);

        echo json_encode([
            "status" => "success",
            "message" => "Description added to session",
            "session_id" => $session_id,
            "description" => $description
        ]);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Retrieve work sessions with user details
        $query = "
            SELECT 
                ws.id,
                ws.user_id,
                u.firstname,
                u.lastname,
                ws.start_time,
                ws.end_time,
                ws.description,
                DATE(ws.start_time) AS start_date,
                DATE(ws.end_time) AS end_date
            FROM work_sessions ws
            JOIN users u ON ws.user_id = u.id
            ORDER BY ws.start_time DESC
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        $sessions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sessions[] = [
                "session_id" => $row['id'],
                "user_id" => $row['user_id'],
                "firstname" => $row['firstname'],
                "lastname" => $row['lastname'],
                "start_time" => $row['start_time'],
                "end_time" => $row['end_time'],
                "description" => $row['description'],
                "start_date" => $row['start_date'],
                "end_date" => $row['end_date']
            ];
        }

        echo json_encode([
            "status" => "success",
            "work_sessions" => $sessions
        ]);

    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid request method"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}