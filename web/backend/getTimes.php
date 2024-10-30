<?php

require_once 'database.php';

// Set CORS policy
$allowed_domains = [
    "https://www.fhgr-informatik.ch",
    "https://fhgr-informatik.ch",
    "https://www.taim.ing",
    "https://taim.ing"
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_domains)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
} else {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode([
        "status" => "error",
        "message" => "Access denied"
    ]);
    exit();
}


header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    // Query to fetch work sessions with user details and extract date from start and end times
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

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
