<?php
/**
 * FRIO Admin API - Inquiries Endpoint
 * Provides read and delete operations for customer inquiries (brochure downloads & contact logs).
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, DELETE, OPTIONS");
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
            // Fetch inquiries
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $stmt = $pdo->prepare("SELECT * FROM `inquiries` WHERE `id` = ?");
                $stmt->execute([$id]);
                $inquiry = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($inquiry) {
                    echo json_encode(['status' => 'success', 'data' => $inquiry]);
                } else {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Inquiry record not found.']);
                }
            } else {
                $type = isset($_GET['type']) ? trim($_GET['type']) : '';
                if ($type === 'catalogue' || $type === 'contact') {
                    $stmt = $pdo->prepare("SELECT * FROM `inquiries` WHERE `type` = ? ORDER BY `id` DESC");
                    $stmt->execute([$type]);
                } else {
                    $stmt = $pdo->query("SELECT * FROM `inquiries` ORDER BY `id` DESC");
                }
                $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['status' => 'success', 'data' => $inquiries]);
            }
            break;

        case 'DELETE':
            // Delete inquiry (single or bulk)
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                parse_str(file_get_contents("php://input"), $input);
            }

            // Check if deleting single via query param or bulk/single via JSON
            $id = isset($_GET['id']) ? intval($_GET['id']) : (isset($input['id']) ? intval($input['id']) : 0);
            $ids = isset($input['ids']) && is_array($input['ids']) ? $input['ids'] : [];

            if ($id) {
                // Single Deletion
                $chk = $pdo->prepare("SELECT COUNT(*) FROM `inquiries` WHERE `id` = ?");
                $chk->execute([$id]);
                if ($chk->fetchColumn() == 0) {
                    http_response_code(404);
                    echo json_encode(['status' => 'error', 'message' => 'Inquiry record not found.']);
                    exit(0);
                }

                $stmt = $pdo->prepare("DELETE FROM `inquiries` WHERE `id` = ?");
                $stmt->execute([$id]);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Inquiry record deleted successfully.'
                ]);
            } elseif (!empty($ids)) {
                // Bulk Deletion
                $ids = array_map('intval', $ids);
                $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM `inquiries` WHERE `id` IN ($placeholders)");
                $stmt->execute($ids);

                echo json_encode([
                    'status' => 'success',
                    'message' => count($ids) . ' inquiry records deleted successfully.'
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Inquiry ID or a list of IDs is required for deletion.']);
            }
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
