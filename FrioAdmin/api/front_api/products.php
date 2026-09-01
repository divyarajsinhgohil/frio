<?php
/**
 * FRIO Admin API - Products Endpoint
 * Renders all active products, filters by category, or provides detailed product profiles
 * (including nested size variations and image gallery attachments) when passed a specific product ID.
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
    // A. Single Product Detail Request (e.g. ?id=Y)
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $product_id = intval($_GET['id']);
        
        // Fetch core product properties
        $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM `product` p JOIN `category` c ON p.category_id = c.id WHERE p.id = ? AND p.active = 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            // Fetch associated size variations
            $var_stmt = $pdo->prepare("SELECT * FROM `product_variation` WHERE `product_id` = ? AND `active` = 1 ORDER BY `display_order` ASC, `id` ASC");
            $var_stmt->execute([$product_id]);
            $product['variations'] = $var_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fetch associated gallery images
            $img_stmt = $pdo->prepare("SELECT * FROM `product_image` WHERE `product_id` = ? ORDER BY `display_order` ASC, `id` ASC");
            $img_stmt->execute([$product_id]);
            $product['gallery'] = $img_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['status' => 'success', 'data' => $product]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Product not found or currently inactive.']);
        }
        exit;
    }

    // B. Category-Filtered Product List (e.g. ?category_id=X)
    if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
        $category_id = intval($_GET['category_id']);
        $stmt = $pdo->prepare("SELECT * FROM `product` WHERE `category_id` = ? AND `active` = 1 ORDER BY `display_order` ASC, `id` ASC");
        $stmt->execute([$category_id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $products]);
        exit;
    }

    // C. All Active Products Listing
    $stmt = $pdo->prepare("SELECT * FROM `product` WHERE `active` = 1 ORDER BY `display_order` ASC, `id` ASC");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['status' => 'success', 'data' => $products]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
