<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents('php://input'), true);
$house_title = $data['house_title'] ?? '';

if (empty($house_title)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing house title']);
    exit;
}

// Database connection parameters
$host = 'localhost';
$dbname = 'realease_db';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the property exists by title
    $stmt = $pdo->prepare("SELECT id FROM properties WHERE title = :title LIMIT 1");
    $stmt->execute(['title' => $house_title]);
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$property) {
        http_response_code(404);
        echo json_encode(['error' => 'Property not found']);
        exit;
    }

    $property_id = $property['id'];

    // Check if favorite exists
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = :user_id AND property_id = :property_id");
    $stmt->execute(['user_id' => $user_id, 'property_id' => $property_id]);
    $favorite = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($favorite) {
        // Remove favorite
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE id = :id");
        $stmt->execute(['id' => $favorite['id']]);
        echo json_encode(['status' => 'removed']);
    } else {
        // Add favorite
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, property_id) VALUES (:user_id, :property_id)");
        $stmt->execute(['user_id' => $user_id, 'property_id' => $property_id]);
        echo json_encode(['status' => 'added']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
