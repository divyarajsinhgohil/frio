<?php
/**
 * FRIO Admin API - Settings Endpoint
 * Returns global storefront configurations (address, email, hotline, logo, and social URLs).
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../db_connect.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM `settings` WHERE `id` = 1");
    $stmt->execute();
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($settings) {
        echo json_encode(['status' => 'success', 'data' => $settings]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Global storefront settings row not found.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
