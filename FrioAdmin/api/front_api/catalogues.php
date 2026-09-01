<?php
/**
 * FRIO Admin API - Catalogues Endpoint
 * Returns all active PDF catalogues ordered by display order.
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
    $stmt = $pdo->prepare("SELECT * FROM `catalogue` WHERE `active` = 1 ORDER BY `display_order` ASC, `id` DESC");
    $stmt->execute();
    $catalogues = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'data' => $catalogues]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
