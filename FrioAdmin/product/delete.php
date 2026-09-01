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
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // 1. Fetch all main product images to unlink local files
        $sel_prod = $pdo->prepare("SELECT `image` FROM `product` WHERE `id` IN ($placeholders)");
        $sel_prod->execute($ids);
        $prod_images = $sel_prod->fetchAll(PDO::FETCH_COLUMN);

        // 2. Fetch all variation images to unlink local files
        $sel_vars = $pdo->prepare("SELECT `image` FROM `product_variation` WHERE `product_id` IN ($placeholders)");
        $sel_vars->execute($ids);
        $var_images = $sel_vars->fetchAll(PDO::FETCH_COLUMN);

        // 3. Fetch all gallery images to unlink local files
        $sel_gals = $pdo->prepare("SELECT `image` FROM `product_image` WHERE `product_id` IN ($placeholders)");
        $sel_gals->execute($ids);
        $gal_images = $sel_gals->fetchAll(PDO::FETCH_COLUMN);

        // A. Unlink main product images
        foreach ($prod_images as $img) {
            if (!empty($img) && strpos($img, 'http://') !== 0 && strpos($img, 'https://') !== 0) {
                $file_path = $base_path . $img;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }

        // B. Unlink size variation images
        foreach ($var_images as $v_img) {
            if (!empty($v_img) && strpos($v_img, 'http://') !== 0 && strpos($v_img, 'https://') !== 0) {
                $v_file_path = $base_path . $v_img;
                if (file_exists($v_file_path)) {
                    unlink($v_file_path);
                }
            }
        }

        // C. Unlink gallery images
        foreach ($gal_images as $g_img) {
            if (!empty($g_img) && strpos($g_img, 'http://') !== 0 && strpos($g_img, 'https://') !== 0) {
                $g_file_path = $base_path . $g_img;
                if (file_exists($g_file_path)) {
                    unlink($g_file_path);
                }
            }
        }

        // 3. Delete gallery image rows from the database
        $del_gals = $pdo->prepare("DELETE FROM `product_image` WHERE `product_id` IN ($placeholders)");
        $del_gals->execute($ids);

        // 4. Delete product variations from the database
        $del_vars = $pdo->prepare("DELETE FROM `product_variation` WHERE `product_id` IN ($placeholders)");
        $del_vars->execute($ids);

        // 5. Purge product rows from the database
        $del_stmt = $pdo->prepare("DELETE FROM `product` WHERE `id` IN ($placeholders)");
        $del_stmt->execute($ids);

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
