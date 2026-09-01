<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bannerIds = isset($_POST['bannerIds']) ? $_POST['bannerIds'] : [];
    
    if (is_array($bannerIds) && !empty($bannerIds)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE `banner_slider` SET `display_order` = ? WHERE `id` = ?");
            
            foreach ($bannerIds as $index => $id) {
                $id = intval($id);
                if ($id > 0) {
                    $stmt->execute([$index + 1, $id]);
                }
            }
            $pdo->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No IDs provided']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
