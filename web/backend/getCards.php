<?php

require_once 'database.php';
header('Content-Type: application/json');

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    // Query to get cards with and without user details
    $query = "
        SELECT 
            card_id,
            firstname,
            lastname,
            CASE 
                WHEN firstname IS NOT NULL AND lastname IS NOT NULL THEN 'assigned'
                ELSE 'unassigned'
            END AS status
        FROM users
        ORDER BY status DESC, card_id ASC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $cards = [
        "assigned" => [],
        "unassigned" => []
    ];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cardData = [
            "card_id" => $row['card_id'],
            "firstname" => $row['firstname'],
            "lastname" => $row['lastname']
        ];

        if ($row['status'] === 'assigned') {
            $cards['assigned'][] = $cardData;
        } else {
            $cards['unassigned'][] = $cardData;
        }
    }

    echo json_encode([
        "status" => "success",
        "cards" => $cards
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
