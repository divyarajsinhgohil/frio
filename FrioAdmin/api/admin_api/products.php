<?php
/**
 * FRIO Admin API - Product & Variations Endpoint
 * Provides full CRUD operations for products, dimensions, and image galleries.
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 1. Authenticate Request
require_once 'auth.php';

// 2. Connect to Database
require_once '../../db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Fetch product lists or single product details
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                
                // Get product details
                $stmt = $pdo->prepare("
                    SELECT p.*, c.name AS category_name 
                    FROM `product` p 
                    LEFT JOIN `category` c ON p.category_id = c.id 
                    WHERE p.id = ?
                ");
                $stmt->execute([$id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($product) {
                    // Fetch Gallery Images
                    $galStmt = $pdo->prepare("SELECT * FROM `product_image` WHERE `product_id` = ? ORDER BY `display_order` ASC, `id` ASC");
                    $galStmt->execute([$id]);
                    $product['gallery'] = $galStmt->fetchAll(PDO::FETCH_ASSOC);

                    // Fetch Variations
                    $varStmt = $pdo->prepare("SELECT * FROM `product_variation` WHERE `product_id` = ? ORDER BY `display_order` ASC, `id` ASC");
                    $varStmt->execute([$id]);
                    $product['variations'] = $varStmt->fetchAll(PDO::FETCH_ASSOC);

                    echo json_encode(['status' => 'success', 'data' => $product]);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
                }
            } else {
                // Return all products
                $stmt = $pdo->query("
                    SELECT p.*, c.name AS category_name 
                    FROM `product` p 
                    LEFT JOIN `category` c ON p.category_id = c.id 
                    ORDER BY p.display_order ASC, p.id DESC
                ");
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $products]);
            }
            break;

        case 'POST':
            // Create a new product + seed variations and gallery
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                $input = $_POST;
            }

            $category_id = isset($input['category_id']) ? intval($input['category_id']) : 0;
            $name = isset($input['name']) ? trim($input['name']) : '';
            $description = isset($input['description']) ? trim($input['description']) : '';
            $image = isset($input['image']) ? trim($input['image']) : '';
            $active = isset($input['active']) ? intval($input['active']) : 1;
            $display_order = isset($input['display_order']) ? intval($input['display_order']) : 0;

            // Optional variations array & gallery array
            $gallery = isset($input['gallery']) && is_array($input['gallery']) ? $input['gallery'] : [];
            $variations = isset($input['variations']) && is_array($input['variations']) ? $input['variations'] : [];

            // Validation
            if (!$category_id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Category ID is required.']);
                exit(0);
            }
            if (empty($name)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Product name is required.']);
                exit(0);
            }

            // Generate unique SKU code #PR-XXXX-XX
            $is_unique = false;
            $code = '';
            while (!$is_unique) {
                $code = '#PR-' . rand(8000, 8999) . '-' . rand(10, 99);
                $chk = $pdo->prepare("SELECT COUNT(*) FROM `product` WHERE `code` = ?");
                $chk->execute([$code]);
                if ($chk->fetchColumn() == 0) {
                    $is_unique = true;
                }
            }

            // Start Transaction to guarantee data integrity
            $pdo->beginTransaction();

            // 1. Insert product
            $stmt = $pdo->prepare("
                INSERT INTO `product` (`category_id`, `code`, `name`, `description`, `image`, `active`, `display_order`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$category_id, $code, $name, $description, $image, $active, $display_order]);
            $productId = $pdo->lastInsertId();

            // 2. Insert Gallery Images
            if (!empty($gallery)) {
                $galIns = $pdo->prepare("INSERT INTO `product_image` (`product_id`, `image`, `display_order`) VALUES (?, ?, ?)");
                foreach ($gallery as $gIdx => $gPath) {
                    $galPath = is_array($gPath) ? ($gPath['image'] ?? '') : $gPath;
                    if (!empty($galPath)) {
                        $galIns->execute([$productId, $galPath, $gIdx]);
                    }
                }
            }

            // 3. Insert Variations
            if (!empty($variations)) {
                $varIns = $pdo->prepare("
                    INSERT INTO `product_variation` (`product_id`, `no`, `code`, `name`, `size`, `image`, `display_order`, `active`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                foreach ($variations as $vIdx => $vRow) {
                    $vNo = isset($vRow['no']) ? trim($vRow['no']) : ($vIdx + 1);
                    $vSize = isset($vRow['size']) ? trim($vRow['size']) : '';
                    $vName = isset($vRow['name']) ? trim($vRow['name']) : '';
                    $vImage = isset($vRow['image']) ? trim($vRow['image']) : '';
                    $vActive = isset($vRow['active']) ? intval($vRow['active']) : 1;
                    $vOrder = isset($vRow['display_order']) ? intval($vRow['display_order']) : $vIdx;
                    
                    // Generate unique variation SKU code
                    $vCode = $code . '-' . str_pad($vNo, 2, '0', STR_PAD_LEFT);

                    if (!empty($vSize)) {
                        $varIns->execute([$productId, $vNo, $vCode, $vName, $vSize, $vImage, $vOrder, $vActive]);
                    }
                }
            }

            // Commit Transaction
            $pdo->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'Product and all variations created successfully.',
                'data' => [
                    'id' => $productId,
                    'code' => $code,
                    'name' => $name
                ]
            ]);
            break;

        case 'PUT':
            // Update an existing product
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                parse_str(file_get_contents("php://input"), $input);
            }

            $id = isset($input['id']) ? intval($input['id']) : 0;
            $category_id = isset($input['category_id']) ? intval($input['category_id']) : null;
            $name = isset($input['name']) ? trim($input['name']) : '';
            $description = isset($input['description']) ? trim($input['description']) : '';
            $image = isset($input['image']) ? trim($input['image']) : '';
            $active = isset($input['active']) ? intval($input['active']) : null;
            $display_order = isset($input['display_order']) ? intval($input['display_order']) : null;

            // Optional variations array & gallery array
            $gallery = isset($input['gallery']) && is_array($input['gallery']) ? $input['gallery'] : null;
            $variations = isset($input['variations']) && is_array($input['variations']) ? $input['variations'] : null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Product ID is required for update.']);
                exit(0);
            }

            // Check existence
            $chk = $pdo->prepare("SELECT * FROM `product` WHERE `id` = ?");
            $chk->execute([$id]);
            $existing = $chk->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
                exit(0);
            }

            // Update only fields that are provided
            $finalCatId = ($category_id !== null) ? $category_id : $existing['category_id'];
            $finalName = !empty($name) ? $name : $existing['name'];
            $finalDesc = isset($input['description']) ? $description : $existing['description'];
            $finalImg = !empty($image) ? $image : $existing['image'];
            $finalActive = ($active !== null) ? $active : $existing['active'];
            $finalOrder = ($display_order !== null) ? $display_order : $existing['display_order'];

            $pdo->beginTransaction();

            // 1. Update product base info
            $stmt = $pdo->prepare("
                UPDATE `product` 
                SET `category_id` = ?, `name` = ?, `description` = ?, `image` = ?, `active` = ?, `display_order` = ? 
                WHERE `id` = ?
            ");
            $stmt->execute([$finalCatId, $finalName, $finalDesc, $finalImg, $finalActive, $finalOrder, $id]);

            // 2. Synchronize Gallery (if submitted)
            if ($gallery !== null) {
                // Delete old records
                $delGal = $pdo->prepare("DELETE FROM `product_image` WHERE `product_id` = ?");
                $delGal->execute([$id]);

                // Insert new ones
                $galIns = $pdo->prepare("INSERT INTO `product_image` (`product_id`, `image`, `display_order`) VALUES (?, ?, ?)");
                foreach ($gallery as $gIdx => $gPath) {
                    $galPath = is_array($gPath) ? ($gPath['image'] ?? '') : $gPath;
                    if (!empty($galPath)) {
                        $galIns->execute([$id, $galPath, $gIdx]);
                    }
                }
            }

            // 3. Synchronize Variations (if submitted)
            if ($variations !== null) {
                // Delete old variations
                $delVars = $pdo->prepare("DELETE FROM `product_variation` WHERE `product_id` = ?");
                $delVars->execute([$id]);

                // Insert new ones
                $varIns = $pdo->prepare("
                    INSERT INTO `product_variation` (`product_id`, `no`, `code`, `name`, `size`, `image`, `display_order`, `active`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                foreach ($variations as $vIdx => $vRow) {
                    $vNo = isset($vRow['no']) ? trim($vRow['no']) : ($vIdx + 1);
                    $vSize = isset($vRow['size']) ? trim($vRow['size']) : '';
                    $vName = isset($vRow['name']) ? trim($vRow['name']) : '';
                    $vImage = isset($vRow['image']) ? trim($vRow['image']) : '';
                    $vActive = isset($vRow['active']) ? intval($vRow['active']) : 1;
                    $vOrder = isset($vRow['display_order']) ? intval($vRow['display_order']) : $vIdx;
                    
                    // Generate variation SKU code
                    $vCode = $existing['code'] . '-' . str_pad($vNo, 2, '0', STR_PAD_LEFT);

                    if (!empty($vSize)) {
                        $varIns->execute([$id, $vNo, $vCode, $vName, $vSize, $vImage, $vOrder, $vActive]);
                    }
                }
            }

            $pdo->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'Product updated successfully.',
                'data' => [
                    'id' => $id,
                    'name' => $finalName
                ]
            ]);
            break;

        case 'DELETE':
            // Delete product (cascade deletes associated variations & images)
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                parse_str(file_get_contents("php://input"), $input);
            }

            $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($input['id']) ? intval($input['id']) : 0);

            if (!$id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Product ID is required for deletion.']);
                exit(0);
            }

            // Check existence
            $chk = $pdo->prepare("SELECT COUNT(*) FROM `product` WHERE `id` = ?");
            $chk->execute([$id]);
            if ($chk->fetchColumn() == 0) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
                exit(0);
            }

            $stmt = $pdo->prepare("DELETE FROM `product` WHERE `id` = ?");
            $stmt->execute([$id]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Product and associated variations/gallery deleted successfully.'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed.']);
            break;
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
