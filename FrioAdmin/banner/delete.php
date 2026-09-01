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
        $select_stmt = $pdo->prepare("SELECT `image` FROM `banner_slider` WHERE `id` IN ($placeholders)");
        $select_stmt->execute($ids);
        $banners = $select_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($banners as $b) {
            $img = $b['image'];
            if (!empty($img) && strpos($img, 'http://') !== 0 && strpos($img, 'https://') !== 0) {
                $file_path = $base_path . $img;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        
        // 2. Permanently delete banners from the database
        $delete_stmt = $pdo->prepare("DELETE FROM `banner_slider` WHERE `id` IN ($placeholders)");
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
