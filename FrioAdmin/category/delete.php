<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

// Support both single ID (delete.php?id=3) and bulk IDs (delete.php?ids=2,5,9)
$ids = [];
if (isset($_GET['ids']) && !empty(trim($_GET['ids']))) {
    $raw_ids = explode(',', $_GET['ids']);
    foreach ($raw_ids as $r_id) {
        $clean_id = intval(trim($r_id));
        if ($clean_id > 0) {
            $ids[] = $clean_id;
        }
    }
} elseif (isset($_GET['id'])) {
    $clean_id = intval($_GET['id']);
    if ($clean_id > 0) {
        $ids[] = $clean_id;
    }
}

if (!empty($ids)) {
    try {
        // Create placeholders for prepared SQL
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        // 1. Fetch images to delete their local files and conserve disk space
        $select_stmt = $pdo->prepare("SELECT `image` FROM `category` WHERE `id` IN ($placeholders)");
        $select_stmt->execute($ids);
        $categories = $select_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($categories as $cat) {
            $img_val = $cat['image'];
            $images = json_decode($img_val, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($images)) {
                foreach ($images as $img) {
                    if (!empty($img) && strpos($img, 'http://') !== 0 && strpos($img, 'https://') !== 0) {
                        $file_path = $base_path . $img;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
            } else {
                if (!empty($img_val) && strpos($img_val, 'http://') !== 0 && strpos($img_val, 'https://') !== 0) {
                    $file_path = $base_path . $img_val;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
        }
        
        // 2. Permanently delete categories from the database
        $delete_stmt = $pdo->prepare("DELETE FROM `category` WHERE `id` IN ($placeholders)");
        $delete_stmt->execute($ids);
        
        // Redirect back to list page with deletion success code
        header("Location: list.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        die("Database Error while deleting: " . $e->getMessage());
    }
} else {
    header("Location: list.php");
    exit;
}
?>
