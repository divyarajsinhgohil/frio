<?php
/**
 * FRIO Admin API - Live Search Endpoint
 * Returns a combined list of matching categories and products for a given keyword.
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../../db_connect.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($q)) {
    echo json_encode(["status" => "success", "data" => ["categories" => [], "products" => []]]);
    exit;
}

try {
    $search_term = '%' . $q . '%';

    // 1. Fetch Categories (max 3)
    $cat_stmt = $pdo->prepare("SELECT `id`, `name`, `image` FROM `category` WHERE `active` = 1 AND `name` LIKE ? ORDER BY `display_order` ASC LIMIT 3");
    $cat_stmt->execute([$search_term]);
    $categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch Products (max 5)
    // We match on product name or code
    $prod_stmt = $pdo->prepare("
        SELECT p.id, p.name, p.code, p.image, c.name AS category_name 
        FROM `product` p 
        JOIN `category` c ON p.category_id = c.id 
        WHERE p.active = 1 AND (p.name LIKE ? OR p.code LIKE ?) 
        ORDER BY p.display_order ASC LIMIT 5
    ");
    $prod_stmt->execute([$search_term, $search_term]);
    $products = $prod_stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => [
            "categories" => $categories,
            "products" => $products
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database error occurred."]);
}
?>
