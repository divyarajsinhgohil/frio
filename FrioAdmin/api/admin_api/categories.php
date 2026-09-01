<?php
/**
 * FRIO Admin API - Category Endpoint
 * Provides full CRUD operations for product categories.
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
            // Fetch category lists or single category
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt = $pdo->prepare("SELECT * FROM `category` WHERE `id` = ?");
                $stmt->execute([$id]);
                $category = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($category) {
                    echo json_encode(['status' => 'success', 'data' => $category]);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Category not found.']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM `category` ORDER BY `display_order` ASC, `id` DESC");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $categories]);
            }
            break;

        case 'POST':
            // Create a category
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                $input = $_POST;
            }

            $name = isset($input['name']) ? trim($input['name']) : '';
            $description = isset($input['description']) ? trim($input['description']) : '';
            $image = isset($input['image']) ? trim($input['image']) : '';
            $active = isset($input['active']) ? intval($input['active']) : 1;
            $display_order = isset($input['display_order']) ? intval($input['display_order']) : 0;

            if (empty($name)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Category name is required.']);
                exit(0);
            }

            // Generate unique SKU code #FR-XXXX
            $is_unique = false;
            $code = '';
            while (!$is_unique) {
                $code = '#FR-' . rand(8823, 8999);
                $chk = $pdo->prepare("SELECT COUNT(*) FROM `category` WHERE `code` = ?");
                $chk->execute([$code]);
                if ($chk->fetchColumn() == 0) {
                    $is_unique = true;
                }
            }

            $stmt = $pdo->prepare("INSERT INTO `category` (`code`, `name`, `description`, `image`, `active`, `display_order`) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$code, $name, $description, $image, $active, $display_order]);
            $newId = $pdo->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'message' => 'Category created successfully.',
                'data' => [
                    'id' => $newId,
                    'code' => $code,
                    'name' => $name
                ]
            ]);
            break;

        case 'PUT':
            // Update a category
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                parse_str(file_get_contents("php://input"), $input);
            }

            $id = isset($input['id']) ? intval($input['id']) : 0;
            $name = isset($input['name']) ? trim($input['name']) : '';
            $description = isset($input['description']) ? trim($input['description']) : '';
            $image = isset($input['image']) ? trim($input['image']) : '';
            $active = isset($input['active']) ? intval($input['active']) : null;
            $display_order = isset($input['display_order']) ? intval($input['display_order']) : null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Category ID is required for update.']);
                exit(0);
            }

            // Check existence
            $chk = $pdo->prepare("SELECT * FROM `category` WHERE `id` = ?");
            $chk->execute([$id]);
            $existing = $chk->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Category not found.']);
                exit(0);
            }

            // Update only fields that are provided
            $finalName = !empty($name) ? $name : $existing['name'];
            $finalDesc = isset($input['description']) ? $description : $existing['description'];
            $finalImg = !empty($image) ? $image : $existing['image'];
            $finalActive = ($active !== null) ? $active : $existing['active'];
            $finalOrder = ($display_order !== null) ? $display_order : $existing['display_order'];

            $stmt = $pdo->prepare("UPDATE `category` SET `name` = ?, `description` = ?, `image` = ?, `active` = ?, `display_order` = ? WHERE `id` = ?");
            $stmt->execute([$finalName, $finalDesc, $finalImg, $finalActive, $finalOrder, $id]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Category updated successfully.',
                'data' => [
                    'id' => $id,
                    'name' => $finalName
                ]
            ]);
            break;

        case 'DELETE':
            // Delete a category
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                parse_str(file_get_contents("php://input"), $input);
            }

            $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($input['id']) ? intval($input['id']) : 0);

            if (!$id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Category ID is required for deletion.']);
                exit(0);
            }

            // Check existence
            $chk = $pdo->prepare("SELECT COUNT(*) FROM `category` WHERE `id` = ?");
            $chk->execute([$id]);
            if ($chk->fetchColumn() == 0) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Category not found.']);
                exit(0);
            }

            $stmt = $pdo->prepare("DELETE FROM `category` WHERE `id` = ?");
            $stmt->execute([$id]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Category and all associated products deleted successfully.'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed.']);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
