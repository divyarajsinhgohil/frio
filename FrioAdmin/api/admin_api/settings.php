<?php
/**
 * FRIO Admin API - Settings Endpoint
 * Provides CRUD/update operations for dynamic site-wide variables (logo, contact info, social).
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
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
            // Fetch global settings (always row with ID = 1)
            $stmt = $pdo->prepare("SELECT * FROM `settings` WHERE `id` = 1");
            $stmt->execute();
            $settings = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($settings) {
                echo json_encode(['status' => 'success', 'data' => $settings]);
            } else {
                http_response_code(444); // Missing seed row
                echo json_encode(['status' => 'error', 'message' => 'Global settings row not seeded in database.']);
            }
            break;

        case 'POST':
        case 'PUT':
            // Update global settings row
            $input = json_decode(file_get_contents("php://input"), true);
            if (!$input) {
                $input = $_POST;
            }

            // Fetch current settings to compare / fallback
            $currStmt = $pdo->prepare("SELECT * FROM `settings` WHERE `id` = 1");
            $currStmt->execute();
            $existing = $currStmt->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                // If somehow missing, seed initial empty row first
                $pdo->exec("INSERT INTO `settings` (`id`) VALUES (1)");
                $currStmt->execute();
                $existing = $currStmt->fetch(PDO::FETCH_ASSOC);
            }

            $logo = isset($input['logo']) ? trim($input['logo']) : $existing['logo'];
            $office_name_1 = isset($input['office_name_1']) ? trim($input['office_name_1']) : $existing['office_name_1'];
            $office_name_2 = isset($input['office_name_2']) ? trim($input['office_name_2']) : $existing['office_name_2'];
            $address = isset($input['address']) ? trim($input['address']) : $existing['address'];
            $address_2 = isset($input['address_2']) ? trim($input['address_2']) : $existing['address_2'];
            $email = isset($input['email']) ? trim($input['email']) : $existing['email'];
            $email_2 = isset($input['email_2']) ? trim($input['email_2']) : $existing['email_2'];
            $phone = isset($input['phone']) ? trim($input['phone']) : $existing['phone'];
            $phone_2 = isset($input['phone_2']) ? trim($input['phone_2']) : $existing['phone_2'];
            $facebook = isset($input['facebook']) ? trim($input['facebook']) : $existing['facebook'];
            $instagram = isset($input['instagram']) ? trim($input['instagram']) : $existing['instagram'];
            $linkedin = isset($input['linkedin']) ? trim($input['linkedin']) : $existing['linkedin'];
            $twitter = isset($input['twitter']) ? trim($input['twitter']) : $existing['twitter'];
            $youtube = isset($input['youtube']) ? trim($input['youtube']) : $existing['youtube'];
            $catalogue_banner = isset($input['catalogue_banner']) ? trim($input['catalogue_banner']) : $existing['catalogue_banner'];
            $contact_banner = isset($input['contact_banner']) ? trim($input['contact_banner']) : $existing['contact_banner'];

            $stmt = $pdo->prepare("
                UPDATE `settings` 
                SET `logo` = ?, `office_name_1` = ?, `office_name_2` = ?, `address` = ?, `address_2` = ?, `email` = ?, `email_2` = ?, `phone` = ?, `phone_2` = ?, 
                    `facebook` = ?, `instagram` = ?, `linkedin` = ?, `twitter` = ?, `youtube` = ?,
                    `catalogue_banner` = ?, `contact_banner` = ?
                WHERE `id` = 1
            ");
            $stmt->execute([
                $logo, $office_name_1, $office_name_2, $address, $address_2, $email, $email_2, $phone, $phone_2, 
                $facebook, $instagram, $linkedin, $twitter, $youtube,
                $catalogue_banner, $contact_banner
            ]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Global settings updated successfully.',
                'data' => [
                    'logo' => $logo,
                    'email' => $email,
                    'phone' => $phone
                ]
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
