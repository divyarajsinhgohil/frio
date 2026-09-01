<?php
/**
 * FRIO Admin API - Banner Endpoint
 * Provides full CRUD operations for promo homepage slider banners.
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
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt = $pdo->prepare("SELECT * FROM `banner_slider` WHERE `id` = ?");
                $stmt->execute([$id]);
                $banner = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($banner) {
                    echo json_encode(['status' => 'success', 'data' => $banner]);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Banner not found.']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM `banner_slider` ORDER BY `display_order` ASC, `id` DESC");
                $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $banners]);
            }
            break;

        case 'POST':
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                $input = $_POST;
            }

            $name = isset($input['name']) ? trim($input['name']) : '';
            $description = isset($input['description']) ? trim($input['description']) : '';
            $button_link = isset($input['button_link']) ? trim($input['button_link']) : null;
            $text_align = isset($input['text_align']) ? trim($input['text_align']) : 'center';
            $image = isset($input['image']) ? trim($input['image']) : '';
            $active = isset($input['active']) ? intval($input['active']) : 0;
            $display_order = isset($input['display_order']) ? intval($input['display_order']) : 0;

            if (empty($image)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Banner image path is required.']);
                exit(0);
            }

            // Get next order if not provided
            if (!$display_order) {
                $orderStmt = $pdo->query("SELECT MAX(display_order) FROM `banner_slider`");
                $maxOrder = $orderStmt->fetchColumn();
                $display_order = $maxOrder ? $maxOrder + 1 : 1;
            }

            $stmt = $pdo->prepare("
                INSERT INTO `banner_slider` (`name`, `description`, `button_link`, `text_align`, `image`, `display_order`, `active`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $description, $button_link, $text_align, $image, $display_order, $active]);
            $newId = $pdo->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'message' => 'Banner created successfully.',
                'data' => [
                    'id' => $newId,
                    'name' => $name
                ]
            ]);
            break;

        case 'PUT':
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                parse_str(file_get_contents("php://input"), $input);
            }

            $id = isset($input['id']) ? intval($input['id']) : 0;
            $name = isset($input['name']) ? trim($input['name']) : '';
            $description = isset($input['description']) ? trim($input['description']) : '';
            $button_link = isset($input['button_link']) ? trim($input['button_link']) : null;
            $text_align = isset($input['text_align']) ? trim($input['text_align']) : '';
            $image = isset($input['image']) ? trim($input['image']) : '';
            $active = isset($input['active']) ? intval($input['active']) : null;
            $display_order = isset($input['display_order']) ? intval($input['display_order']) : null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Banner ID is required for update.']);
                exit(0);
            }

            $chk = $pdo->prepare("SELECT * FROM `banner_slider` WHERE `id` = ?");
            $chk->execute([$id]);
            $existing = $chk->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Banner not found.']);
                exit(0);
            }

            $finalName = isset($input['name']) ? $name : $existing['name'];
            $finalDesc = isset($input['description']) ? $description : $existing['description'];
            $finalLink = isset($input['button_link']) ? $button_link : $existing['button_link'];
            $finalAlign = !empty($text_align) ? $text_align : $existing['text_align'];
            $finalImg = !empty($image) ? $image : $existing['image'];
            $finalActive = ($active !== null) ? $active : $existing['active'];
            $finalOrder = ($display_order !== null) ? $display_order : $existing['display_order'];

            $stmt = $pdo->prepare("
                UPDATE `banner_slider` 
                SET `name` = ?, `description` = ?, `button_link` = ?, `text_align` = ?, `image` = ?, `display_order` = ?, `active` = ? 
                WHERE `id` = ?
            ");
            $stmt->execute([$finalName, $finalDesc, $finalLink, $finalAlign, $finalImg, $finalOrder, $finalActive, $id]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Banner updated successfully.',
                'data' => [
                    'id' => $id,
                    'name' => $finalName
                ]
            ]);
            break;

        case 'DELETE':
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                parse_str(file_get_contents("php://input"), $input);
            }

            $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($input['id']) ? intval($input['id']) : 0);

            if (!$id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Banner ID is required for deletion.']);
                exit(0);
            }

            $chk = $pdo->prepare("SELECT COUNT(*) FROM `banner_slider` WHERE `id` = ?");
            $chk->execute([$id]);
            if ($chk->fetchColumn() == 0) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Banner not found.']);
                exit(0);
            }

            $stmt = $pdo->prepare("DELETE FROM `banner_slider` WHERE `id` = ?");
            $stmt->execute([$id]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Banner deleted successfully.'
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
