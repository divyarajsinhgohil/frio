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
        
        // 1. Fetch both PDF files and cover images to delete from local disk storage
        $select_stmt = $pdo->prepare("SELECT `pdf_file`, `preview_image` FROM `catalogue` WHERE `id` IN ($placeholders)");
        $select_stmt->execute($ids);
        $catalogues = $select_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($catalogues as $cat) {
            // Unlink PDF File
            $pdf = $cat['pdf_file'];
            if (!empty($pdf) && strpos($pdf, 'http://') !== 0 && strpos($pdf, 'https://') !== 0) {
                $pdf_file_path = $base_path . $pdf;
                if (file_exists($pdf_file_path)) {
                    unlink($pdf_file_path);
                }
            }

            // Unlink Cover Preview Image
            $img = $cat['preview_image'];
            if (!empty($img) && strpos($img, 'http://') !== 0 && strpos($img, 'https://') !== 0) {
                $img_file_path = $base_path . $img;
                if (file_exists($img_file_path)) {
                    unlink($img_file_path);
                }
            }
        }
        
        // 2. Permanently delete records from the database
        $delete_stmt = $pdo->prepare("DELETE FROM `catalogue` WHERE `id` IN ($placeholders)");
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
