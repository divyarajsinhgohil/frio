<?php
/**
 * FRIO Admin Console - Settings Module
 * Allows direct single-page management of the storefront's global parameters:
 * brand logo, contact coordinates, emails, telephone hotline, and social media URLs.
 * Features a real-time responsive storefront mockup to sync configurations live.
 */
require_once 'auth_check.php';
require_once 'db_connect.php';

$success_msg = '';
$error_msg = '';

if (isset($_SESSION['settings_success'])) {
    $success_msg = $_SESSION['settings_success'];
    unset($_SESSION['settings_success']);
}
if (isset($_SESSION['settings_error'])) {
    $error_msg = $_SESSION['settings_error'];
    unset($_SESSION['settings_error']);
}

// 1. Fetch current settings row (id = 1)
try {
    $stmt = $pdo->prepare("SELECT * FROM `settings` WHERE `id` = 1");
    $stmt->execute();
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fallback if settings table hasn't been seeded
    if (!$settings) {
        $pdo->exec("
            INSERT IGNORE INTO `settings` (`id`, `logo`, `address`, `email`, `phone`, `facebook`, `instagram`, `linkedin`, `twitter`, `youtube`) 
            VALUES (1, 'assets/imag/frio-logo-white.png', 'Frio India Industrial Park, Delhi, India', 'info@frio.co', '+91 98765 43210', 'https://facebook.com', 'https://instagram.com', 'https://linkedin.com', 'https://twitter.com', 'https://youtube.com')
        ");
        $stmt->execute();
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error_msg = "Database Error: " . $e->getMessage();
}

// 2. Handle configuration update request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $office_name_1 = trim($_POST['office_name_1'] ?? 'Uniglobe Overseas');
    $office_name_2 = ''; // Hide Location 2
    $address       = trim($_POST['address'] ?? '');
    $address_2     = ''; // Hide Location 2
    $email         = trim($_POST['email'] ?? '');
    $email_2       = trim($_POST['email_2'] ?? '');
    
    $phone_1       = trim($_POST['phone_1'] ?? '');
    $phone_2_in    = trim($_POST['phone_2_in'] ?? '');
    $phone_3_in    = trim($_POST['phone_3_in'] ?? '');
    $phone         = implode(', ', array_filter([$phone_1, $phone_2_in, $phone_3_in]));
    $phone_2       = ''; // Hide Location 2
    $facebook      = trim($_POST['facebook'] ?? '');
    $instagram     = trim($_POST['instagram'] ?? '');
    $linkedin      = trim($_POST['linkedin'] ?? '');
    $twitter       = trim($_POST['twitter'] ?? '');
    $youtube       = trim($_POST['youtube'] ?? '');
    $whatsapp      = trim($_POST['whatsapp'] ?? '');
    $raw_map       = trim($_POST['map_embed_url'] ?? '');

    $notification_email = trim($_POST['notification_email'] ?? 'divyarajgohil6299@gmail.com');
    $email_method       = trim($_POST['email_method'] ?? 'mail');
    $smtp_host          = trim($_POST['smtp_host'] ?? '');
    $smtp_port          = intval($_POST['smtp_port'] ?? 587);
    $smtp_user          = trim($_POST['smtp_user'] ?? '');
    $smtp_pass          = trim($_POST['smtp_pass'] ?? '');
    $smtp_secure        = trim($_POST['smtp_secure'] ?? 'tls');

    // ── Step 1: If user pasted full <iframe> HTML, extract just the src URL ───
    $map_embed_url = '';
    if (!empty($raw_map)) {
        // Extract src="..." from iframe HTML (handles single or double quotes)
        if (preg_match('/src=["\']([^"\']+)["\']/i', $raw_map, $src_match)) {
            $map_embed_url = $src_match[1];
        } else {
            // Not iframe HTML — treat as raw URL
            $map_embed_url = $raw_map;
        }

        // ── Step 2: Decode any HTML entities (&amp; → &) ─────────────────────
        $map_embed_url = html_entity_decode($map_embed_url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $map_embed_url = trim($map_embed_url);
    }
    
    // Secure input validation
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['settings_error'] = "Invalid corporate email address format.";
        header("Location: settings.php");
        exit;
    }

    $logo_path             = $settings['logo'] ?? 'assets/imag/frio-logo-white.png';
    $catalogue_banner_path = $settings['catalogue_banner'] ?? '';

    // ── Handle Brand Logo Upload ──────────────────────────────────────────────
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['logo']['tmp_name'];
        $file_name = $_FILES['logo']['name'];
        $file_size = $_FILES['logo']['size'];

        $allowed_exts  = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
        $allowed_mimes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'];
        $max_size      = 2 * 1024 * 1024; // 2 MB
        $ext           = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $finfo     = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        if (!in_array($ext, $allowed_exts) || !in_array($mime_type, $allowed_mimes)) {
            $_SESSION['settings_error'] = "Unsupported image format for logo. Allowed: PNG, JPG, GIF, WebP.";
            header("Location: settings.php"); exit;
        }
        if ($file_size > $max_size) {
            $_SESSION['settings_error'] = "Logo image size exceeds 2 MB limit.";
            header("Location: settings.php"); exit;
        }

        $upload_dir = 'assets/imag/logo/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $new_filename = 'logo_' . time() . '_' . md5(uniqid()) . '.' . $ext;
        $target_path  = $upload_dir . $new_filename;

        if (move_uploaded_file($file_tmp, $target_path)) {
            $old_logo = $settings['logo'] ?? '';
            if (!empty($old_logo) && file_exists($old_logo)
                && strpos($old_logo, 'assets/imag/frio-logo') === false
                && is_file($old_logo)) {
                @unlink($old_logo);
            }
            $logo_path = $target_path;
        } else {
            $_SESSION['settings_error'] = "Failed to upload the logo file.";
            header("Location: settings.php"); exit;
        }
    }

    // ── Handle Catalogue Page Banner Upload ───────────────────────────────────
    if (isset($_FILES['catalogue_banner']) && $_FILES['catalogue_banner']['error'] === UPLOAD_ERR_OK) {
        $cb_tmp  = $_FILES['catalogue_banner']['tmp_name'];
        $cb_name = $_FILES['catalogue_banner']['name'];
        $cb_size = $_FILES['catalogue_banner']['size'];

        $allowed_exts  = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
        $allowed_mimes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'];
        $max_size      = 5 * 1024 * 1024; // 5 MB for banners
        $cb_ext        = strtolower(pathinfo($cb_name, PATHINFO_EXTENSION));

        $finfo     = finfo_open(FILEINFO_MIME_TYPE);
        $cb_mime   = finfo_file($finfo, $cb_tmp);
        finfo_close($finfo);

        if (!in_array($cb_ext, $allowed_exts) || !in_array($cb_mime, $allowed_mimes)) {
            $_SESSION['settings_error'] = "Unsupported image format for banner. Allowed: PNG, JPG, GIF, WebP.";
            header("Location: settings.php"); exit;
        }
        if ($cb_size > $max_size) {
            $_SESSION['settings_error'] = "Catalogue banner image size exceeds 5 MB limit.";
            header("Location: settings.php"); exit;
        }

        $banner_dir = 'assets/imag/banners/';
        if (!is_dir($banner_dir)) mkdir($banner_dir, 0777, true);

        $cb_filename = 'catalogue_banner_' . time() . '_' . md5(uniqid()) . '.' . $cb_ext;
        $cb_path     = $banner_dir . $cb_filename;

        if (move_uploaded_file($cb_tmp, $cb_path)) {
            // Delete old custom banner (but not the preset)
            $old_cb = $settings['catalogue_banner'] ?? '';
            if (!empty($old_cb) && file_exists($old_cb)
                && strpos($old_cb, 'catalogue_banner_preset') === false
                && is_file($old_cb)) {
                @unlink($old_cb);
            }
            $catalogue_banner_path = $cb_path;
        } else {
            $_SESSION['settings_error'] = "Failed to upload the catalogue banner image.";
            header("Location: settings.php"); exit;
        }
    }

    // ── Persist all changes to DB ─────────────────────────────────────────────
    try {
        $update_sql = "
            UPDATE `settings`
            SET
                `logo`              = :logo,
                `office_name_1`     = :office_name_1,
                `office_name_2`     = :office_name_2,
                `catalogue_banner`  = :catalogue_banner,
                `address`           = :address,
                `address_2`         = :address_2,
                `email`             = :email,
                `email_2`           = :email_2,
                `phone`             = :phone,
                `phone_2`           = :phone_2,
                `facebook`          = :facebook,
                `instagram`         = :instagram,
                `linkedin`          = :linkedin,
                `twitter`           = :twitter,
                `youtube`           = :youtube,
                `whatsapp`          = :whatsapp,
                `map_embed_url`     = :map_embed_url,
                `notification_email` = :notification_email,
                `email_method`       = :email_method,
                `smtp_host`          = :smtp_host,
                `smtp_port`          = :smtp_port,
                `smtp_user`          = :smtp_user,
                `smtp_pass`          = :smtp_pass,
                `smtp_secure`        = :smtp_secure
            WHERE `id` = 1
        ";

        $upd_stmt = $pdo->prepare($update_sql);
        $upd_stmt->execute([
            ':logo'             => $logo_path,
            ':office_name_1'    => $office_name_1,
            ':office_name_2'    => $office_name_2,
            ':catalogue_banner' => $catalogue_banner_path,
            ':address'          => $address,
            ':address_2'        => $address_2,
            ':email'            => $email,
            ':email_2'          => $email_2,
            ':phone'            => $phone,
            ':phone_2'          => $phone_2,
            ':facebook'         => $facebook,
            ':instagram'        => $instagram,
            ':linkedin'         => $linkedin,
            ':twitter'          => $twitter,
            ':youtube'          => $youtube,
            ':whatsapp'         => $whatsapp,
            ':map_embed_url'    => $map_embed_url,
            ':notification_email' => $notification_email,
            ':email_method'       => $email_method,
            ':smtp_host'          => $smtp_host,
            ':smtp_port'          => $smtp_port,
            ':smtp_user'          => $smtp_user,
            ':smtp_pass'          => $smtp_pass,
            ':smtp_secure'        => $smtp_secure,
        ]);

        $_SESSION['settings_success'] = "Global storefront configurations updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['settings_error'] = "Failed to update configurations: " . $e->getMessage();
    }

    header("Location: settings.php");
    exit;
}

// Parse phone numbers from DB
$phone_list = explode(',', $settings['phone'] ?? '');
$phone_1_val = isset($phone_list[0]) ? trim($phone_list[0]) : '';
$phone_2_val = isset($phone_list[1]) ? trim($phone_list[1]) : '';
$phone_3_val = isset($phone_list[2]) ? trim($phone_list[2]) : '';

$page_title = "FRIO Console | Storefront Settings";
include 'includes/head.php';
?>
<body class="bg-background text-on-background min-h-screen flex overflow-hidden">
<?php include 'includes/sidebar.php'; ?>

<!-- Main Workspace -->
<main class="ml-64 flex-grow h-screen overflow-y-auto flex flex-col justify-between">
<?php 
$header_title = 'Storefront Settings';
include 'includes/header.php'; 
?>

<!-- Content Area -->
<section class="mt-24 p-gutter flex-grow">
    <!-- Breadcrumbs / Top Indicator -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">Global Storefront Parameters</h1>
            <p class="text-body-md text-outline">Manage brand coordinates, logo assets, and social profiles linking live to the storefront footer.</p>
        </div>
        <div class="text-xs text-outline bg-surface-container px-3 py-1 rounded-full border border-outline-variant/30 flex items-center gap-1.5">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            Single-page Direct Controller (id = 1)
        </div>
    </div>

    <!-- Alert Banners -->
    <?php if ($success_msg): ?>
        <div class="mb-6 flex items-center gap-3 bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 p-4 rounded-xl border border-green-200 dark:border-green-900/30 transition-all duration-300">
            <span class="material-symbols-outlined">check_circle</span>
            <span class="text-body-md font-bold"><?php echo htmlspecialchars($success_msg); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div class="mb-6 flex items-center gap-3 bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 p-4 rounded-xl border border-red-200 dark:border-red-900/30 transition-all duration-300">
            <span class="material-symbols-outlined">error</span>
            <span class="text-body-md font-bold"><?php echo htmlspecialchars($error_msg); ?></span>
        </div>
    <?php endif; ?>

    <!-- Workspace Layout -->
    <div class="max-w-4xl mx-auto w-full">
        
        <!-- Form Fields -->
        <form method="POST" enctype="multipart/form-data" action="settings.php" class="space-y-6">
            
            <!-- Brand Logo Upload Section -->
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md">
                <div class="flex items-center gap-3 mb-4">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">image</span>
                    <h3 class="text-headline-md font-headline-md text-primary">Company Identity</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                    <!-- Current Logo View -->
                    <div class="md:col-span-4 flex flex-col items-center justify-center p-4 bg-primary rounded-2xl border border-white/10 shadow-sm relative group overflow-hidden h-36">
                        <span class="absolute top-2 left-2 text-[10px] text-white/55 font-bold uppercase tracking-wider">Current Logo</span>
                        <img id="form-logo-preview" src="<?php echo htmlspecialchars($settings['logo'] ?? 'assets/imag/frio-logo-white.png'); ?>" alt="Frio Brand Logo" class="max-h-20 max-w-full object-contain filter drop-shadow-md transition-transform duration-300 group-hover:scale-105" />
                    </div>
                    <!-- File Input Dash Area -->
                    <div class="md:col-span-8">
                        <label for="logo-upload" class="upload-dashed rounded-2xl flex flex-col items-center justify-center p-6 cursor-pointer hover:bg-primary-fixed/5 transition-all duration-300 border-2 border-transparent h-36">
                            <span class="material-symbols-outlined text-3xl text-primary mb-2">cloud_upload</span>
                            <span class="text-label-bold font-label-bold text-primary">Upload New Brand Logo</span>
                            <span class="text-[11px] text-outline mt-1 text-center">Supports transparent PNG, JPG, WebP. Recommended: 300x100px. Max 2MB.</span>
                            <input type="file" id="logo-upload" name="logo" accept="image/*" class="hidden" />
                        </label>
                    </div>
                </div>
            </div>


            <!-- Contacts and Location Section -->
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">contact_support</span>
                    <h3 class="text-headline-md font-headline-md text-primary">Company Contact Details</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="space-y-1">
                        <label for="office_name_1" class="text-label-sm font-semibold text-on-surface-variant flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-primary">domain</span> Company Name
                        </label>
                        <input type="text" id="office_name_1" name="office_name_1" value="<?php echo htmlspecialchars($settings['office_name_1'] ?? 'Uniglobe Overseas'); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all font-bold text-primary input-focus-brass" placeholder="Uniglobe Overseas" />
                    </div>
                    
                    <div class="space-y-1">
                        <label for="address" class="text-label-sm font-semibold text-on-surface-variant">Company Physical Address</label>
                        <textarea id="address" name="address" rows="2" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="Plot No. 4654 Phase lll, Dared GIDC, Jamnagar - 361004, Gujarat (INDIA)."><?php echo htmlspecialchars($settings['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label for="email" class="text-label-sm font-semibold text-on-surface-variant">General Inquiry Email</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">mail</span>
                                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl pl-10 pr-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="info@uniglobeoverseas.com" />
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label for="email_2" class="text-label-sm font-semibold text-on-surface-variant">Sales Inquiry Email</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">mail</span>
                                <input type="text" id="email_2" name="email_2" value="<?php echo htmlspecialchars($settings['email_2'] ?? ''); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl pl-10 pr-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="sales@uniglobeoverseas.com" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label for="phone_1" class="text-label-sm font-semibold text-on-surface-variant">Phone Number 1</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">call</span>
                                <input type="text" id="phone_1" name="phone_1" value="<?php echo htmlspecialchars($phone_1_val); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl pl-10 pr-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="+91 9723588952" />
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label for="phone_2_in" class="text-label-sm font-semibold text-on-surface-variant">Phone Number 2</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">call</span>
                                <input type="text" id="phone_2_in" name="phone_2_in" value="<?php echo htmlspecialchars($phone_2_val); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl pl-10 pr-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="+91 9328046282" />
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label for="phone_3_in" class="text-label-sm font-semibold text-on-surface-variant">Phone Number 3</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">call</span>
                                <input type="text" id="phone_3_in" name="phone_3_in" value="<?php echo htmlspecialchars($phone_3_val); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl pl-10 pr-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="+91 9265398945" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Notifications & SMTP Settings Section -->
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md space-y-6">
                <div class="flex items-center gap-3 mb-2">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">mail_lock</span>
                    <h3 class="text-headline-md font-headline-md text-primary">Inquiry Email Notifications</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Recipient Email -->
                        <div class="space-y-1">
                            <label for="notification_email" class="text-label-sm font-semibold text-on-surface-variant">Notification Recipient Email</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">contact_mail</span>
                                <input type="email" id="notification_email" name="notification_email" value="<?php echo htmlspecialchars($settings['notification_email'] ?? 'divyarajgohil6299@gmail.com'); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl pl-10 pr-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="divyarajgohil6299@gmail.com" required />
                            </div>
                            <span class="text-[10px] text-outline mt-1 block">Recipient email address where all new inquiries and requests are instantly routed.</span>
                        </div>

                        <!-- Sending Method -->
                        <div class="space-y-1">
                            <label class="text-label-sm font-semibold text-on-surface-variant">Email Transmission Method</label>
                            <div class="w-full bg-surface-container-low/50 border border-outline-variant/20 rounded-xl px-4 py-3 text-body-md text-outline font-bold flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-green-600">check_circle</span>
                                PHP Native mail() [Production / Live Server]
                            </div>
                            <input type="hidden" id="email_method" name="email_method" value="mail" />
                            <span class="text-[10px] text-outline mt-1 block">Native mail transmission is active and optimized for live hosting environments.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media Section -->
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md space-y-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">share</span>
                    <h3 class="text-headline-md font-headline-md text-primary">Social Connections</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="facebook" class="text-label-bold font-bold text-on-surface flex items-center gap-1.5">
                            <svg class="w-4 h-4 fill-primary" viewBox="0 0 24 24"><path d="M9 8H7v3h2v9h3v-9h3l.5-3H12V6c0-.88.39-1 1-1h2V2h-3c-2.5 0-4 1.5-4 4v2z"/></svg>
                            Facebook URL
                        </label>
                        <input type="url" id="facebook" name="facebook" value="<?php echo htmlspecialchars($settings['facebook'] ?? ''); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="https://facebook.com/username" />
                    </div>

                    <div class="space-y-1">
                        <label for="instagram" class="text-label-bold font-bold text-on-surface flex items-center gap-1.5">
                            <svg class="w-4 h-4 fill-primary" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                            Instagram URL
                        </label>
                        <input type="url" id="instagram" name="instagram" value="<?php echo htmlspecialchars($settings['instagram'] ?? ''); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="https://instagram.com/username" />
                    </div>

                    <div class="space-y-1">
                        <label for="linkedin" class="text-label-bold font-bold text-on-surface flex items-center gap-1.5">
                            <svg class="w-4 h-4 fill-primary" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            LinkedIn URL
                        </label>
                        <input type="url" id="linkedin" name="linkedin" value="<?php echo htmlspecialchars($settings['linkedin'] ?? ''); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="https://linkedin.com/company/username" />
                    </div>

                    <div class="space-y-1">
                        <label for="twitter" class="text-label-bold font-bold text-on-surface flex items-center gap-1.5">
                            <svg class="w-4 h-4 fill-primary" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            Twitter / X URL
                        </label>
                        <input type="url" id="twitter" name="twitter" value="<?php echo htmlspecialchars($settings['twitter'] ?? ''); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="https://twitter.com/username" />
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label for="youtube" class="text-label-bold font-bold text-on-surface flex items-center gap-1.5">
                            <svg class="w-4 h-4 fill-primary" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.108C19.516 3.53 12 3.53 12 3.53s-7.516 0-9.388.525a3.003 3.003 0 0 0-2.11 2.108C0 8.055 0 12 0 12s0 3.945.502 5.837a3.003 3.003 0 0 0 2.11 2.108c1.872.525 9.388.525 9.388.525s7.516 0 9.388-.525a3.003 3.003 0 0 0 2.11-2.108C24 15.945 24 12 24 12s0-3.945-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            YouTube URL
                        </label>
                        <input type="url" id="youtube" name="youtube" value="<?php echo htmlspecialchars($settings['youtube'] ?? ''); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="https://youtube.com/c/channelname" />
                    </div>
                </div>
            </div>

            <!-- WhatsApp & Map Location Section -->
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md space-y-4">
                <div class="flex items-center gap-3 mb-2">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">location_on</span>
                    <h3 class="text-headline-md font-headline-md text-primary">Map Location</h3>
                </div>

                <!-- Google Maps Embed URL -->
                <div class="space-y-2">
                    <label for="map_embed_url" class="text-label-bold font-bold text-on-surface">Google Maps Embed URL</label>

                    <!-- Step-by-step guide -->
                    <div class="bg-primary/5 border border-primary/15 rounded-xl px-4 py-3 text-[11px] text-on-surface space-y-1.5 leading-relaxed">
                        <p class="font-extrabold text-primary text-xs uppercase tracking-wider mb-1">📍 How to get the Embed URL:</p>
                        <p><span class="font-bold">1.</span> Open <a href="https://maps.google.com" target="_blank" class="text-primary underline font-bold">Google Maps</a> and search your location.</p>
                        <p><span class="font-bold">2.</span> Click <strong>Share</strong> button → choose <strong>"Embed a map"</strong> tab.</p>
                        <p><span class="font-bold">3.</span> Click <strong>"COPY HTML"</strong> — then paste the <em>entire</em> <code class="bg-white px-1 rounded">&lt;iframe ...&gt;</code> code OR just the <code class="bg-white px-1 rounded">src="..."</code> URL below.</p>
                        <p class="text-outline">✅ Both the full iframe HTML and just the src URL are accepted.</p>
                    </div>

                    <textarea id="map_embed_url" name="map_embed_url" rows="3"
                        class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass font-mono text-xs"
                        placeholder='Paste full <iframe ...> HTML or just the src URL here...'><?php echo htmlspecialchars($settings['map_embed_url'] ?? ''); ?></textarea>

                    <button type="button" id="map-preview-btn"
                        class="flex items-center gap-2 text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 px-4 py-2 rounded-xl transition-all">
                        <span class="material-symbols-outlined text-[16px]">map</span>
                        Test Map Preview
                    </button>
                </div>

                <!-- Live Map Preview Panel -->
                <div id="map-preview-panel" class="mt-3 <?php echo empty($settings['map_embed_url']) ? 'hidden' : ''; ?>">
                    <p class="text-[11px] text-outline uppercase tracking-wider font-bold mb-2">Map Preview</p>
                    <div class="w-full h-52 rounded-2xl overflow-hidden border border-outline-variant/20 shadow-sm">
                        <iframe id="map-preview-frame"
                            src="<?php echo htmlspecialchars($settings['map_embed_url'] ?? ''); ?>"
                            class="w-full h-full border-0"
                            loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <p id="map-invalid-msg" class="hidden mt-2 text-xs text-red-600 font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">error</span>
                        This URL could not be shown. Make sure you copied the embed <code>src</code> URL (starts with <code>https://www.google.com/maps/embed...</code>).
                    </p>
                </div>
            </div>

            <!-- Form Submission Commands -->
            <div class="flex items-center justify-end gap-4 bg-white/40 dark:bg-surface-container/20 p-4 rounded-[2rem] border border-white/10 backdrop-blur-md">
                <button type="reset" id="btn-reset" class="text-label-bold font-bold text-outline hover:text-primary hover:bg-primary-fixed/20 px-6 py-3 rounded-full transition-all duration-300">
                    Reset Form
                </button>
                <button type="submit" class="bg-primary hover:bg-primary-container text-white px-8 py-3 rounded-full font-bold btn-glow flex items-center gap-2 shadow-lg transition-all duration-300">
                    <span class="material-symbols-outlined">save</span>
                    Save Configurations
                </button>
            </div>

        </form>
    </div>
</section>

<!-- Page Footer -->
<?php include 'includes/footer.php'; ?>
</main>

<!-- Frontend Javascript syncing event handlers -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ── Real-time Logo Preview ────────────────────────────────────────────────
    const logoUpload     = document.getElementById('logo-upload');
    const formLogoPreview = document.getElementById('form-logo-preview');

    if (logoUpload && formLogoPreview) {
        logoUpload.addEventListener('change', () => {
            const file = logoUpload.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => { formLogoPreview.src = e.target.result; };
                reader.readAsDataURL(file);
            }
        });
    }

    // ── Form Reset ───────────────────────────────────────────────────────────
    const btnReset   = document.getElementById('btn-reset');
    const origLogo   = formLogoPreview ? formLogoPreview.src : '';

    if (btnReset) {
        btnReset.addEventListener('click', () => {
            setTimeout(() => {
                if (formLogoPreview) formLogoPreview.src = origLogo;
            }, 50);
        });
    }

    // ── Map Embed URL: auto-extract src from pasted <iframe> HTML ─────────────
    const mapTextarea    = document.getElementById('map_embed_url');
    const mapPreviewBtn  = document.getElementById('map-preview-btn');
    const mapPanel       = document.getElementById('map-preview-panel');
    const mapFrame       = document.getElementById('map-preview-frame');
    const mapInvalidMsg  = document.getElementById('map-invalid-msg');

    function extractMapSrc(raw) {
        raw = raw.trim();
        // If it looks like an iframe HTML block, extract src="..."
        const srcMatch = raw.match(/src=["']([^"']+)["']/i);
        if (srcMatch) return srcMatch[1];
        return raw;
    }

    // Auto-clean on paste: strip full iframe HTML → keep just the src URL
    if (mapTextarea) {
        mapTextarea.addEventListener('paste', () => {
            setTimeout(() => {
                const cleaned = extractMapSrc(mapTextarea.value);
                if (cleaned !== mapTextarea.value) {
                    mapTextarea.value = cleaned;
                }
            }, 50);
        });
    }

    // Test Map Preview button
    if (mapPreviewBtn && mapFrame && mapPanel) {
        mapPreviewBtn.addEventListener('click', () => {
            const src = extractMapSrc(mapTextarea.value.trim());
            if (!src) return;
            mapTextarea.value = src; // normalize
            mapFrame.src = src;
            mapPanel.classList.remove('hidden');
            if (mapInvalidMsg) mapInvalidMsg.classList.add('hidden');
            // Detect load error after a delay
            setTimeout(() => {
                try {
                    const loaded = mapFrame.contentDocument || mapFrame.contentWindow?.document;
                    if (!loaded) {
                        // cross-origin = map loaded fine (expected)
                        if (mapInvalidMsg) mapInvalidMsg.classList.add('hidden');
                    }
                } catch(e) {
                    // cross-origin access blocked = map loaded OK
                    if (mapInvalidMsg) mapInvalidMsg.classList.add('hidden');
                }
            }, 3000);
        });
    }

    // SMTP panel removed
});
</script>
</body></html>
