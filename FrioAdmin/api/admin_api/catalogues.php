<?php
/**
 * FRIO Admin API - Catalogue Endpoint
 * Provides full CRUD operations for brochure catalogs.
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
                $stmt = $pdo->prepare("SELECT * FROM `catalogue` WHERE `id` = ?");
                $stmt->execute([$id]);
                $catalogue = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($catalogue) {
                    echo json_encode(['status' => 'success', 'data' => $catalogue]);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Catalogue not found.']);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM `catalogue` ORDER BY `display_order` ASC, `id` DESC");
                $catalogues = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $catalogues]);
            }
            break;

        case 'POST':
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                $input = $_POST;
            }

            $name = isset($input['name']) ? trim($input['name']) : '';
            $pdf_file = isset($input['pdf_file']) ? trim($input['pdf_file']) : '';
            $preview_image = isset($input['preview_image']) ? trim($input['preview_image']) : '';
            $active = isset($input['active']) ? intval($input['active']) : 0;
            $display_order = isset($input['display_order']) ? intval($input['display_order']) : 0;

            if (empty($name)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Catalogue name is required.']);
                exit(0);
            }
            if (empty($pdf_file)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'PDF document path is required.']);
                exit(0);
            }
            if (empty($preview_image)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Preview cover image path is required.']);
                exit(0);
            }

            $stmt = $pdo->prepare("
                INSERT INTO `catalogue` (`name`, `pdf_file`, `preview_image`, `display_order`, `active`) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $pdf_file, $preview_image, $display_order, $active]);
            $newId = $pdo->lastInsertId();

            echo json_encode([
                'status' => 'success',
                'message' => 'Catalogue created successfully.',
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
            $pdf_file = isset($input['pdf_file']) ? trim($input['pdf_file']) : '';
            $preview_image = isset($input['preview_image']) ? trim($input['preview_image']) : '';
            $active = isset($input['active']) ? intval($input['active']) : null;
            $display_order = isset($input['display_order']) ? intval($input['display_order']) : null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Catalogue ID is required for update.']);
                exit(0);
            }

            $chk = $pdo->prepare("SELECT * FROM `catalogue` WHERE `id` = ?");
            $chk->execute([$id]);
            $existing = $chk->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Catalogue not found.']);
                exit(0);
            }

            $finalName = !empty($name) ? $name : $existing['name'];
            $finalPdf = !empty($pdf_file) ? $pdf_file : $existing['pdf_file'];
            $finalPreview = !empty($preview_image) ? $preview_image : $existing['preview_image'];
            $finalActive = ($active !== null) ? $active : $existing['active'];
            $finalOrder = ($display_order !== null) ? $display_order : $existing['display_order'];

            $stmt = $pdo->prepare("
                UPDATE `catalogue` 
                SET `name` = ?, `pdf_file` = ?, `preview_image` = ?, `display_order` = ?, `active` = ? 
                WHERE `id` = ?
            ");
            $stmt->execute([$finalName, $finalPdf, $finalPreview, $finalOrder, $finalActive, $id]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Catalogue updated successfully.',
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
                echo json_encode(['status' => 'error', 'message' => 'Catalogue ID is required for deletion.']);
                exit(0);
            }

            $chk = $pdo->prepare("SELECT COUNT(*) FROM `catalogue` WHERE `id` = ?");
            $chk->execute([$id]);
            if ($chk->fetchColumn() == 0) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Catalogue not found.']);
                exit(0);
            }

            $stmt = $pdo->prepare("DELETE FROM `catalogue` WHERE `id` = ?");
            $stmt->execute([$id]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Catalogue deleted successfully.'
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
