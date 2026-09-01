<?php
/**
 * Database Setup & Initialization Script
 * Creates the database 'frio' and the table 'admin_users' if they don't exist,
 * and seeds the default administrator.
 */

// Connection details
$host = "localhost";
$username = "root";
$password = "";

try {
    // 1. Connect without a database name to create the database if needed
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `frio` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database 'frio' created or already exists.<br>";
    
    // 2. Re-connect specifically to the 'frio' database
    $pdo = new PDO("mysql:host=$host;dbname=frio", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create admin_users table
    $table_sql = "
    CREATE TABLE IF NOT EXISTS `admin_users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password` VARCHAR(32) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($table_sql);
    echo "Table 'admin_users' created or already exists.<br>";
    
    // Create category table
    $category_sql = "
    CREATE TABLE IF NOT EXISTS `category` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `code` VARCHAR(20) NOT NULL UNIQUE,
        `name` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `image` TEXT,
        `active` TINYINT(1) DEFAULT 1,
        `display_order` INT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($category_sql);
    echo "Table 'category' created or already exists.<br>";

    // Create product table
    $product_sql = "
    CREATE TABLE IF NOT EXISTS `product` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `category_id` INT NOT NULL,
        `code` VARCHAR(100) NOT NULL UNIQUE,
        `name` VARCHAR(255) NOT NULL,
        `description` TEXT,
        `image` TEXT,
        `active` TINYINT(1) DEFAULT 1,
        `display_order` INT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($product_sql);
    echo "Table 'product' created or already exists.<br>";

    // Create product_variation table
    $variation_sql = "
    CREATE TABLE IF NOT EXISTS `product_variation` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `product_id` INT NOT NULL,
        `no` VARCHAR(50),
        `code` VARCHAR(100) NOT NULL,
        `name` VARCHAR(255) NULL,
        `size` VARCHAR(100) NOT NULL,
        `image` TEXT,
        `display_order` INT DEFAULT 0,
        `active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($variation_sql);
    echo "Table 'product_variation' created or already exists.<br>";

    // Create product_image gallery table
    $gallery_sql = "
    CREATE TABLE IF NOT EXISTS `product_image` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `product_id` INT NOT NULL,
        `image` TEXT NOT NULL,
        `display_order` INT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($gallery_sql);
    echo "Table 'product_image' created or already exists.<br>";

    // Create banner_slider table
    $banner_sql = "
    CREATE TABLE IF NOT EXISTS `banner_slider` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NULL,
        `description` TEXT,
        `button_link` VARCHAR(255) DEFAULT NULL,
        `image` TEXT NOT NULL,
        `display_order` INT DEFAULT 0,
        `active` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($banner_sql);
    echo "Table 'banner_slider' created or already exists.<br>";

    // Create catalogue table
    $catalogue_sql = "
    CREATE TABLE IF NOT EXISTS `catalogue` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `pdf_file` TEXT NOT NULL,
        `preview_image` TEXT NOT NULL,
        `display_order` INT DEFAULT 0,
        `active` TINYINT(1) DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($catalogue_sql);
    echo "Table 'catalogue' created or already exists.<br>";

    $settings_sql = "
    CREATE TABLE IF NOT EXISTS `settings` (
        `id` INT PRIMARY KEY DEFAULT 1,
        `logo` TEXT,
        `office_name_1` VARCHAR(255) DEFAULT 'Uniglobe Overseas',
        `office_name_2` VARCHAR(255) DEFAULT '',
        `address` TEXT,
        `address_2` TEXT,
        `email` VARCHAR(255),
        `email_2` VARCHAR(255),
        `phone` VARCHAR(255),
        `phone_2` VARCHAR(255),
        `facebook` VARCHAR(255),
        `instagram` VARCHAR(255),
        `linkedin` VARCHAR(255),
        `twitter` VARCHAR(255),
        `youtube` VARCHAR(255),
        `catalogue_banner` TEXT DEFAULT NULL,
        `contact_banner` TEXT DEFAULT NULL,
        `whatsapp` VARCHAR(255) DEFAULT NULL,
        `map_embed_url` TEXT DEFAULT NULL,
        `notification_email` VARCHAR(255) DEFAULT 'info@frio.co',
        `email_method` VARCHAR(20) DEFAULT 'mail',
        `smtp_host` VARCHAR(255) DEFAULT '',
        `smtp_port` INT DEFAULT 587,
        `smtp_user` VARCHAR(255) DEFAULT '',
        `smtp_pass` VARCHAR(255) DEFAULT '',
        `smtp_secure` VARCHAR(20) DEFAULT 'tls',
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($settings_sql);
    echo "Table 'settings' created or already exists.<br>";

    // Create inquiries table
    $inquiries_sql = "
    CREATE TABLE IF NOT EXISTS `inquiries` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `type` VARCHAR(20) NOT NULL,
        `first_name` VARCHAR(100) NOT NULL,
        `last_name` VARCHAR(100) NULL,
        `email` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(50) NOT NULL,
        `message` TEXT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    $pdo->exec($inquiries_sql);
    echo "Table 'inquiries' created or already exists.<br>";

    // Seed default global settings row if empty
    $chk_stmt = $pdo->query("SELECT COUNT(*) FROM `settings` WHERE `id` = 1");
    if ($chk_stmt->fetchColumn() == 0) {
        $seed_settings = $pdo->prepare("
            INSERT INTO `settings` (`id`, `logo`, `office_name_1`, `office_name_2`, `address`, `address_2`, `email`, `email_2`, `phone`, `phone_2`, `facebook`, `instagram`, `linkedin`, `twitter`, `youtube`, `notification_email`, `email_method`, `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass`, `smtp_secure`) 
            VALUES (1, 'assets/imag/frio-logo-white.png', 'Uniglobe Overseas', '', 'Frio India Industrial Park, Delhi, India', '', 'info@frio.co', '', '+91 98765 43210', '', 'https://facebook.com', 'https://instagram.com', 'https://linkedin.com', 'https://twitter.com', 'https://youtube.com', 'info@frio.co', 'mail', '', 587, '', '', 'tls')
        ");
        $seed_settings->execute();
        echo "Seeded default global settings row.<br>";
    }

    // Seed default categories
    $default_cats = [
        [
            'code' => '#FR-8820',
            'name' => 'Flare Nuts',
            'description' => 'Heavy-duty industrial fittings',
            'image' => 'https://lh3.googleusercontent.com/aida/ADBb0uhXDmvSLEQCWzdz7sClSoEIGWK1SAoPWPoVixEzG2ioA5Hpx8CbXBkHIrpSq36EYWNjhlyLZ2O7FYUwlQCe2MQ-IgLsW62xoBIQEg8ea6qxYlF0Vy0KRqdO9C9fK3EYF7rDMpOJP7QAE_d675OUYEfoNYb2f-1gE61oGt0txh6ZI3sFiLQUXNfaWFpdON2L-_rZTWRDf1XshMuLDkqTu65T63Tn7zpnHH-wFgEx0luncyXJ3npmRaWHO5U',
            'active' => 1,
            'display_order' => 1
        ],
        [
            'code' => '#FR-8821',
            'name' => 'Flare Union',
            'description' => 'Seamless connection solutions',
            'image' => 'https://lh3.googleusercontent.com/aida/ADBb0uilFh1xrqygZWkbnK-Cvw24wFrJ7wRba5o6qLBw2dIN7bawrEdaKMBZSExRFQPKsOPunFJ8yTYm0n8d2yEiDPs1wUto7KkMDkLDe1_EbUzaECuKNiDiHrV-89MGI-OF2HwLCtb9SD_NLuh4fTdoS0XMcY1271YB6rqLuQHsrGhk6j1A0ZpszyBFYlaRuISAYmxqSO860Rz8xzVvDoVFKz_Q4VO3QhRTbOGSX_YP38W6dd70zftNXc1Y_YY',
            'active' => 1,
            'display_order' => 2
        ],
        [
            'code' => '#FR-8822',
            'name' => 'Cap Nuts',
            'description' => 'Protective industrial capping',
            'image' => 'https://lh3.googleusercontent.com/aida/ADBb0uhlZz_Eo6JcqVa7ZIZSkVw0OPJcpnhy9WiQ7auNpyQPIdZcRIODaEoTjOQPTP45oPqY18Nfn6AY_v_wBytngrvxHFoJNzamN5y2faw_kIQvvOacwrSJkhtmMCrKrQXocmwB3CSnSIdkEn5bTqwoxA9ND4Vg1s_YfEIi-0RkjVh7DzFtYaEJJb7h3ZChH6k1jShS9xuPyVnYE7GwiGBseNhnGP4uXhe_IMcHwbhSjQx0vsyTGHaPEhZXg_xk',
            'active' => 0,
            'display_order' => 3
        ]
    ];

    foreach ($default_cats as $cat) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `category` WHERE `code` = ?");
        $stmt->execute([$cat['code']]);
        if ($stmt->fetchColumn() == 0) {
            $insert = $pdo->prepare("INSERT INTO `category` (`code`, `name`, `description`, `image`, `active`, `display_order`) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$cat['code'], $cat['name'], $cat['description'], $cat['image'], $cat['active'], $cat['display_order']]);
            echo "Seeded category: <b>" . htmlspecialchars($cat['name']) . "</b><br>";
        }
    }

    // Seed default products
    $cat_stmt = $pdo->prepare("SELECT `id` FROM `category` WHERE `name` = ? LIMIT 1");
    $cat_stmt->execute(['Flare Nuts']);
    $cat_id = $cat_stmt->fetchColumn();

    if ($cat_id) {
        $prod_code = '#PR-8823';
        $prod_stmt = $pdo->prepare("SELECT COUNT(*) FROM `product` WHERE `code` = ?");
        $prod_stmt->execute([$prod_code]);
        if ($prod_stmt->fetchColumn() == 0) {
            $insert_prod = $pdo->prepare("INSERT INTO `product` (`category_id`, `code`, `name`, `description`, `image`, `active`, `display_order`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $prod_image = 'https://lh3.googleusercontent.com/aida/ADBb0uhXDmvSLEQCWzdz7sClSoEIGWK1SAoPWPoVixEzG2ioA5Hpx8CbXBkHIrpSq36EYWNjhlyLZ2O7FYUwlQCe2MQ-IgLsW62xoBIQEg8ea6qxYlF0Vy0KRqdO9C9fK3EYF7rDMpOJP7QAE_d675OUYEfoNYb2f-1gE61oGt0txh6ZI3sFiLQUXNfaWFpdON2L-_rZTWRDf1XshMuLDkqTu65T63Tn7zpnHH-wFgEx0luncyXJ3npmRaWHO5U';
            $insert_prod->execute([$cat_id, $prod_code, 'Heavy Hex Flare Nuts', 'Premium high-pressure hex flare nuts for hydraulic seals', $prod_image, 1, 1]);
            $prod_id = $pdo->lastInsertId();
            echo "Seeded product: <b>Heavy Hex Flare Nuts</b><br>";

            // Seed variations
            $vars = [
                ['no' => '1', 'code' => '#PR-8823-01', 'size' => '1/4 inch', 'image' => '', 'display_order' => 1, 'active' => 1],
                ['no' => '2', 'code' => '#PR-8823-02', 'size' => '3/8 inch', 'image' => '', 'display_order' => 2, 'active' => 1],
                ['no' => '3', 'code' => '#PR-8823-03', 'size' => '1/2 inch', 'image' => '', 'display_order' => 3, 'active' => 0]
            ];

            $insert_var = $pdo->prepare("INSERT INTO `product_variation` (`product_id`, `no`, `code`, `size`, `image`, `display_order`, `active`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($vars as $var) {
                $insert_var->execute([$prod_id, $var['no'], $var['code'], $var['size'], $var['image'], $var['display_order'], $var['active']]);
                echo "Seeded variation: <b>Size " . htmlspecialchars($var['size']) . "</b> for Heavy Hex Flare Nuts<br>";
            }
        }
    }
    
    // 3. Seed default administrator
    $admin_user = "frio";
    $admin_pass = "frio@7750";
    $admin_hash = md5($admin_pass);
    
    // Clean up any old "firo" accounts to prevent confusion
    $pdo->exec("DELETE FROM `admin_users` WHERE `username` = 'firo'");
    
    // Check if the user already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `admin_users` WHERE `username` = ?");
    $stmt->execute([$admin_user]);
    $exists = $stmt->fetchColumn();
    
    if (!$exists) {
        $insert = $pdo->prepare("INSERT INTO `admin_users` (`username`, `password`) VALUES (?, ?)");
        $insert->execute([$admin_user, $admin_hash]);
        echo "Default administrator created:<br>";
        echo "- Username: <b>$admin_user</b><br>";
        echo "- Password: <b>$admin_pass</b> (MD5 Hash: $admin_hash)<br>";
    } else {
        // If it exists, update it to make sure the credentials are correct
        $update = $pdo->prepare("UPDATE `admin_users` SET `password` = ? WHERE `username` = ?");
        $update->execute([$admin_hash, $admin_user]);
        echo "Administrator user '$admin_user' credentials updated successfully.<br>";
    }
    
    echo "<br><b>Database configuration and setup completed successfully!</b>";
} catch (PDOException $e) {
    echo "<span style='color:red;'><b>Database Error:</b> " . $e->getMessage() . "</span>";
}
