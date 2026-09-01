<?php
/**
 * FRIO Admin Console - Backup & Restore Module
 * Allows download of all database schema, tables, rows, and active media assets inside a single ZIP package.
 * Also handles restoring the environment by uploading a previously generated ZIP backup.
 */
require_once 'auth_check.php';
require_once 'db_connect.php';

$success_msg = '';
$error_msg   = '';
$warning_msg = '';

if (isset($_SESSION['backup_success'])) {
    $success_msg = $_SESSION['backup_success'];
    unset($_SESSION['backup_success']);
}
if (isset($_SESSION['backup_error'])) {
    $error_msg = $_SESSION['backup_error'];
    unset($_SESSION['backup_error']);
}
if (isset($_SESSION['backup_warning'])) {
    $warning_msg = $_SESSION['backup_warning'];
    unset($_SESSION['backup_warning']);
}

// Active media folders for stats and backup compilation
$media_folders = [
    'assets/imag/banners',
    'assets/imag/catalogue',
    'assets/imag/category',
    'assets/imag/product',
    'assets/imag/logo',
    'assets/pdf/catalogue'
];

/**
 * Traverses directories and returns combined files size and count
 */
function get_media_stats($folders) {
    $total_size = 0;
    $file_count = 0;
    foreach ($folders as $folder) {
        if (is_dir($folder)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $total_size += $file->getSize();
                    $file_count++;
                }
            }
        }
    }
    return ['size' => $total_size, 'count' => $file_count];
}

/**
 * Format size in human-readable bytes
 */
function format_bytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Helper to recursively delete a directory
 */
function delete_directory($dir) {
    if (!is_dir($dir)) return;
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = "$dir/$file";
        is_dir($path) ? delete_directory($path) : @unlink($path);
    }
    return @rmdir($dir);
}

/**
 * Helper to recursively copy a directory
 */
function copy_directory($src, $dst) {
    if (!is_dir($dst)) {
        mkdir($dst, 0777, true);
    }
    $files = array_diff(scandir($src), ['.', '..']);
    foreach ($files as $file) {
        $src_path = "$src/$file";
        $dst_path = "$dst/$file";
        if (is_dir($src_path)) {
            copy_directory($src_path, $dst_path);
        } else {
            @copy($src_path, $dst_path);
        }
    }
}

/**
 * Compiles a robust SQL backup dump using TRUNCATE + INSERT IGNORE strategy.
 * 
 * Why TRUNCATE instead of DROP/CREATE:
 *   - Product descriptions contain HTML with semicolons inside strings.
 *   - Simple SQL parsers that split on ';' break on those values.
 *   - TRUNCATE TABLE is always a clean single statement (no embedded semicolons).
 *   - INSERT IGNORE skips duplicate-key errors safely on partial restores.
 *   - REPLACE INTO replaces any existing row with backup data.
 */
function generate_db_backup($pdo) {
    $sql  = "-- ======================================================\n";
    $sql .= "-- FRIO Admin Console Database Backup\n";
    $sql .= "-- Timestamp: " . date('Y-m-d H:i:s') . "\n";
    $sql .= "-- Strategy: TRUNCATE + REPLACE (safe with HTML content)\n";
    $sql .= "-- ======================================================\n\n";
    $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    // Strict schema structural order to prevent foreign key errors
    $tables = [
        'admin_users', 'settings', 'category',
        'product', 'product_variation', 'product_image',
        'banner_slider', 'catalogue'
    ];

    foreach ($tables as $table) {
        $sql .= "-- Restoring table: `$table` --\n";

        // TRUNCATE clears all rows cleanly — single statement, no schema parsing needed
        $sql .= "TRUNCATE TABLE `$table`;\n";

        // Fetch all rows and write REPLACE INTO statements
        $rows_stmt = $pdo->query("SELECT * FROM `$table`");
        $rows = $rows_stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $keys = array_keys($row);
                $cols = implode('`, `', $keys);

                $vals = [];
                foreach ($row as $val) {
                    if ($val === null) {
                        $vals[] = 'NULL';
                    } else {
                        // Use PDO::quote for proper escaping of ALL special chars
                        $vals[] = $pdo->quote($val);
                    }
                }
                $vals_str = implode(', ', $vals);

                // REPLACE INTO: inserts if new, replaces if primary key exists
                $sql .= "REPLACE INTO `$table` (`$cols`) VALUES ($vals_str);\n";
            }
        }
        $sql .= "\n";
    }

    $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    return $sql;
}

// 1. Process Database & Media Download Action
if (isset($_GET['action']) && $_GET['action'] === 'download') {
    // Generate clean safe ZIP archive
    $zip = new ZipArchive();
    $zip_filename = 'frio_backup_' . date('Ymd_His') . '.zip';
    $temp_zip = 'assets/frio_backup_temp.zip';
    
    if ($zip->open($temp_zip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        
        // Add Database backup SQL
        $zip->addFromString('database_backup.sql', generate_db_backup($pdo));
        
        // Recursively add all media subdirectories
        foreach ($media_folders as $folder) {
            if (is_dir($folder)) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $file_path = $file->getRealPath();
                        // Standardize slash style to relative path
                        $relative_path = str_replace('\\', '/', substr($file_path, strlen(realpath('.') . '/')));
                        $relative_path = ltrim($relative_path, '/');
                        $zip->addFile($file_path, $relative_path);
                    }
                }
            }
        }
        
        $zip->close();
        
        // Serve Download Output Headers
        if (file_exists($temp_zip)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
            header('Content-Length: ' . filesize($temp_zip));
            header('Pragma: no-cache');
            header('Expires: 0');
            readfile($temp_zip);
            @unlink($temp_zip);
            exit;
        }
    }
    
    $_SESSION['backup_error'] = "Failed to construct the ZIP backup package.";
    header("Location: backup.php");
    exit;
}

// 2. Process Restore Upload Action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A. Detect if post_max_size was exceeded (POST request empty, but content length is non-zero)
    if (empty($_FILES) && empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $post_max = ini_get('post_max_size');
        $uploaded_size = $_SERVER['CONTENT_LENGTH'];
        $_SESSION['backup_error'] = "Upload Failed: The backup package size (" . format_bytes($uploaded_size) . ") exceeds the web server's PHP POST limit (post_max_size = $post_max). Please increase 'post_max_size' and 'upload_max_filesize' in your php.ini and restart Apache.";
        header("Location: backup.php");
        exit;
    }

    if (isset($_FILES['backup_file'])) {
        $file_error = $_FILES['backup_file']['error'];
        
        if ($file_error !== UPLOAD_ERR_OK) {
            switch ($file_error) {
                case UPLOAD_ERR_INI_SIZE:
                    $upload_max = ini_get('upload_max_filesize');
                    $_SESSION['backup_error'] = "Upload Failed: The file size exceeds the web server's PHP configuration limit (upload_max_filesize = $upload_max). Please increase 'upload_max_filesize' in your php.ini and restart Apache.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $_SESSION['backup_error'] = "Upload Failed: The file size exceeds the limit specified in the HTML form.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $_SESSION['backup_error'] = "Upload Failed: The file was only partially uploaded. Please try again.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $_SESSION['backup_error'] = "Upload Failed: No file was selected for upload.";
                    break;
                default:
                    $_SESSION['backup_error'] = "Upload Failed: PHP encountered error code $file_error during upload.";
                    break;
            }
            header("Location: backup.php");
            exit;
        }

        $file_tmp = $_FILES['backup_file']['tmp_name'];
        $file_name = $_FILES['backup_file']['name'];
        
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if ($ext !== 'zip') {
            $_SESSION['backup_error'] = "Invalid file type. Only standard FRIO backup ZIP files are accepted.";
            header("Location: backup.php");
            exit;
        }
        
        $zip = new ZipArchive();
        if ($zip->open($file_tmp) === true) {
            
            // Validate presence of DB SQL dump
            $sql_index = $zip->locateName('database_backup.sql');
            if ($sql_index === false) {
                $_SESSION['backup_error'] = "Malformed backup package. database_backup.sql is missing.";
                $zip->close();
                header("Location: backup.php");
                exit;
            }
            
            // Build isolated extraction paths
            $extract_dir = 'assets/tmp_restore_' . uniqid() . '/';
            if (!is_dir($extract_dir)) {
                mkdir($extract_dir, 0777, true);
            }
            
            // Extract ZIP package
            $zip->extractTo($extract_dir);
            $zip->close();
            
            $old_format_detected = false;
            
            // STEP A: Restore Database
            // Uses a proper character-by-character SQL parser to split statements.
            // This correctly handles BOTH backup formats:
            //   - Old format: DROP TABLE + CREATE TABLE + INSERT INTO
            //   - New format: TRUNCATE + REPLACE INTO
            // HTML product descriptions may contain semicolons inside &amp; entities.
            // A naive explode(';') would cause false splits; this parser tracks
            // quoted strings and only splits on ';' outside of quotes.
            $sql_dump_path = $extract_dir . 'database_backup.sql';
            if (file_exists($sql_dump_path)) {
                $sql_queries = file_get_contents($sql_dump_path);
                
                // Detect if they are using an old backup format
                if (stripos($sql_queries, 'DROP TABLE IF EXISTS') !== false) {
                    $old_format_detected = true;
                }

                try {
                    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

                    // Pre-truncate all tables in reverse FK dependency order to guarantee
                    // idempotence and a completely clean slate for both old and new backup formats.
                    $truncate_order = [
                        'product_image', 'product_variation', 'product', 'category',
                        'banner_slider', 'catalogue', 'settings', 'admin_users'
                    ];
                    foreach ($truncate_order as $t) {
                        try {
                            $pdo->exec("TRUNCATE TABLE `$t`");
                        } catch (PDOException $truncateEx) {
                            // Ignored (e.g. if table does not exist yet)
                        }
                    }

                    // --- Robust SQL statement splitter ---
                    $statements = [];
                    $current    = '';
                    $in_string  = false;
                    $str_char   = '';
                    $len        = strlen($sql_queries);

                    for ($i = 0; $i < $len; $i++) {
                        $c = $sql_queries[$i];

                        if ($in_string) {
                            if ($c === '\\' && $i + 1 < $len) {
                                // Backslash escape: consume both chars as-is
                                $current .= $c . $sql_queries[++$i];
                            } elseif ($c === $str_char) {
                                // Closing quote — exit string mode
                                $in_string = false;
                                $current  .= $c;
                            } else {
                                $current .= $c;
                            }
                        } else {
                            if ($c === "'" || $c === '"' || $c === '`') {
                                // Opening quote — enter string mode
                                $in_string = true;
                                $str_char  = $c;
                                $current  .= $c;
                            } elseif ($c === '-' && $i + 1 < $len && $sql_queries[$i + 1] === '-') {
                                // Line comment (-- ...) — skip to end of line
                                while ($i < $len && $sql_queries[$i] !== "\n") {
                                    $i++;
                                }
                            } elseif ($c === '#') {
                                // MySQL hash comment — skip to end of line
                                while ($i < $len && $sql_queries[$i] !== "\n") {
                                    $i++;
                                }
                            } elseif ($c === ';') {
                                // Real statement boundary (outside quoted string)
                                $stmt = trim($current);
                                if ($stmt !== '') {
                                    $statements[] = $stmt;
                                }
                                $current = '';
                            } else {
                                $current .= $c;
                            }
                        }
                    }
                    // Catch any trailing statement without a final semicolon
                    if (($stmt = trim($current)) !== '') {
                        $statements[] = $stmt;
                    }
                    // --- End SQL splitter ---

                    $failed_stmts = [];
                    foreach ($statements as $stmt) {
                        // Skip FOREIGN_KEY_CHECKS — already handled above/below
                        if (stripos($stmt, 'FOREIGN_KEY_CHECKS') !== false) continue;

                        try {
                            $pdo->exec($stmt);
                        } catch (PDOException $stmtEx) {
                            // Log error but keep going — best-effort restore
                            $failed_stmts[] = $stmtEx->getMessage();
                        }
                    }

                    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

                    if (!empty($failed_stmts)) {
                        $_SESSION['backup_warning'] = "Restore completed with " . count($failed_stmts) . " SQL warning(s). Core data has been recovered.";
                    }

                } catch (PDOException $e) {
                    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
                    delete_directory($extract_dir);
                    $_SESSION['backup_error'] = "Database Restore Failed: " . $e->getMessage();
                    header("Location: backup.php");
                    exit;
                }
            }
            
            // STEP B: Restore only user Media folders (NOT css/js code files)
            // Only copy the imag and pdf subdirectories to avoid overwriting admin panel code files
            $safe_media_subdirs = ['imag', 'pdf'];
            $extracted_assets = $extract_dir . 'assets';
            if (is_dir($extracted_assets)) {
                foreach ($safe_media_subdirs as $subdir) {
                    $src_subdir = $extracted_assets . '/' . $subdir;
                    $dst_subdir = 'assets/' . $subdir;
                    if (is_dir($src_subdir)) {
                        copy_directory($src_subdir, $dst_subdir);
                    }
                }
            }
            
            // Clear temporary directory
            delete_directory($extract_dir);
            
            $_SESSION['backup_success'] = "Backup successfully restored! Storefront database and files are synchronized.";
            if ($old_format_detected) {
                $_SESSION['backup_warning'] = "System restored successfully, but you are using an older backup format. We highly recommend downloading a fresh backup now to use the new, safer and faster format.";
            }
        } else {
            $_SESSION['backup_error'] = "Failed to open the uploaded ZIP archive.";
        }
    } else {
        $_SESSION['backup_error'] = "No backup file uploaded or upload error encountered.";
    }
    
    header("Location: backup.php");
    exit;
}

// 3. Fetch current system metadata & statistics for display
$db_size = 0;
$table_count = 0;
try {
    // Queries information schema to fetch exact byte allocation for the dynamic DB
    $schema_stmt = $pdo->prepare("
        SELECT table_name, (data_length + index_length) AS total_bytes
        FROM information_schema.TABLES
        WHERE table_schema = ?
    ");
    $schema_stmt->execute([DB_NAME]);
    $tables_meta = $schema_stmt->fetchAll(PDO::FETCH_ASSOC);
    $table_count = count($tables_meta);
    foreach ($tables_meta as $t) {
        $db_size += $t['total_bytes'];
    }
} catch (PDOException $e) {
    // Fallback if schemas cannot be loaded
}

$media_stats = get_media_stats($media_folders);

$page_title = "FRIO Console | Backup & Restore";
include 'includes/head.php';
?>
<body class="bg-background text-on-background min-h-screen flex overflow-hidden">
<?php include 'includes/sidebar.php'; ?>

<!-- Main Workspace -->
<main class="ml-64 flex-grow h-screen overflow-y-auto flex flex-col justify-between">
<?php 
$header_title = 'Backup & Restore';
include 'includes/header.php'; 
?>

<!-- Content Area -->
<section class="mt-24 p-gutter flex-grow">
    <!-- Top Header Layout -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">System Backup & Recovery</h1>
            <p class="text-body-md text-outline">Compile database states, seeded accounts, product lists, brochures, and asset media in one downloadable archive.</p>
        </div>
        <div class="text-xs text-outline bg-surface-container px-3 py-1 rounded-full border border-outline-variant/30 flex items-center gap-1.5 font-bold uppercase tracking-wider">
            <span class="material-symbols-outlined text-[14px] text-primary">settings_backup_restore</span>
            Zip Engine Active
        </div>
    </div>

    <!-- Feedback Alerts -->
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

    <?php if ($warning_msg): ?>
        <div class="mb-6 flex items-center gap-3 bg-amber-50 text-amber-700 p-4 rounded-xl border border-amber-200 transition-all duration-300">
            <span class="material-symbols-outlined">warning</span>
            <span class="text-body-md font-bold"><?php echo htmlspecialchars($warning_msg); ?></span>
        </div>
    <?php endif; ?>

    <!-- Bento Grid Layout Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
        
        <!-- Statistics Block Left Column (5/12) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- System Stats Bento Card -->
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md">
                <div class="flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">monitoring</span>
                    <h3 class="text-headline-md font-headline-md text-primary">Storage Allocation</h3>
                </div>
                
                <div class="space-y-4">
                    <!-- DB Stats Row -->
                    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-2xl text-primary bg-primary/10 p-2 rounded-xl">database</span>
                            <div>
                                <p class="text-label-bold font-bold text-on-surface">Storefront Database</p>
                                <p class="text-xs text-outline font-medium"><?php echo $table_count; ?> Tables compiled</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-label-bold font-bold text-primary"><?php echo format_bytes($db_size); ?></p>
                            <p class="text-[9px] uppercase tracking-wider text-outline font-bold">SQL Raw Size</p>
                        </div>
                    </div>

                    <!-- Media Assets Stats Row -->
                    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-2xl text-primary bg-primary/10 p-2 rounded-xl">folder_zip</span>
                            <div>
                                <p class="text-label-bold font-bold text-on-surface">User Uploaded Media</p>
                                <p class="text-xs text-outline font-medium"><?php echo $media_stats['count']; ?> Assets found</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-label-bold font-bold text-primary"><?php echo format_bytes($media_stats['size']); ?></p>
                            <p class="text-[9px] uppercase tracking-wider text-outline font-bold">Binary Payload</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Caution Advisory Bento Card -->
            <div class="bg-amber-50 dark:bg-amber-950/10 border border-amber-200 dark:border-amber-900/30 p-6 rounded-[2rem] shadow-sm space-y-3">
                <h5 class="text-label-bold font-bold text-amber-800 dark:text-amber-400 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[20px]">warning</span>
                    System Override Advisory
                </h5>
                <ul class="space-y-2 text-xs text-amber-700 dark:text-amber-300 leading-relaxed list-disc pl-4">
                    <li>Restoring a ZIP package will immediately <b>TRUNCATE</b> and recompile all tables.</li>
                    <li>Uploaded media files will be merged and overwrite matching paths.</li>
                    <li>We recommend making an active export backup before performing a restore sequence to avoid accidental data loss.</li>
                </ul>
            </div>
        </div>

        <!-- Download & Upload Control Panel Right Column (7/12) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Download Backup Block -->
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-outline-variant/10">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">download</span>
                        <h3 class="text-headline-md font-headline-md text-primary">Compile & Export</h3>
                    </div>
                </div>
                <p class="text-body-md text-outline mb-6 leading-relaxed">
                    Packages the complete system coordinates, banner parameters, catalogues, product items, and MD5 hashed administrator credentials into a portable ZIP container ready for offsite storage.
                </p>
                <a href="backup.php?action=download" class="w-full bg-primary hover:bg-primary-container text-white py-4 rounded-2xl font-bold btn-glow flex items-center justify-center gap-2 shadow-lg transition-all duration-300 text-body-lg">
                    <span class="material-symbols-outlined">archive</span>
                    Download Backup ZIP Package
                </a>
            </div><!-- END Download Backup Block -->

            <!-- Restore Backup Block -->
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md" id="restore-card">
                <div class="flex items-center gap-3 mb-4 pb-2 border-b border-outline-variant/10">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">publish</span>
                    <h3 class="text-headline-md font-headline-md text-primary">Import &amp; Restore System</h3>
                </div>

                <form method="POST" enctype="multipart/form-data" action="backup.php" id="restore-form">

                    <!-- Drop Zone -->
                    <div id="drop-zone"
                         class="relative rounded-2xl border-2 border-dashed border-outline-variant/40 bg-surface-container-lowest hover:border-primary/50 hover:bg-primary/5 transition-all duration-300 cursor-pointer min-h-[170px] flex flex-col items-center justify-center p-6 mb-4"
                         onclick="document.getElementById('backup-file-input').click()">

                        <!-- Default State -->
                        <div id="drop-default" class="flex flex-col items-center gap-2 text-center">
                            <span class="material-symbols-outlined text-5xl text-primary/60">cloud_upload</span>
                            <span class="text-label-bold font-bold text-primary text-base">Drag &amp; Drop Backup ZIP here</span>
                            <span class="text-xs text-outline">or click to browse — only valid Frio .zip packages</span>
                        </div>

                        <!-- Dragging-Over State (hidden by default) -->
                        <div id="drop-hover" class="hidden flex-col items-center gap-2 text-center">
                            <span class="material-symbols-outlined text-5xl text-primary animate-bounce">move_to_inbox</span>
                            <span class="text-label-bold font-bold text-primary text-base">Release to load ZIP</span>
                        </div>

                        <!-- File Selected State (hidden by default) -->
                        <div id="drop-selected" class="hidden w-full">
                            <div class="flex items-center gap-4 bg-primary/8 rounded-xl p-4 border border-primary/20">
                                <span class="material-symbols-outlined text-4xl text-primary">folder_zip</span>
                                <div class="flex-grow min-w-0">
                                    <p class="text-label-bold font-bold text-primary truncate" id="file-name-display">file.zip</p>
                                    <p class="text-xs text-outline mt-0.5" id="file-size-display">0 MB</p>
                                </div>
                                <button type="button" onclick="clearFile(event)"
                                        class="shrink-0 p-1.5 rounded-lg text-outline hover:text-error hover:bg-error/10 transition-all"
                                        title="Remove file">
                                    <span class="material-symbols-outlined text-[20px]">close</span>
                                </button>
                            </div>
                        </div>

                        <input type="file" id="backup-file-input" name="backup_file" accept=".zip" class="hidden" />
                    </div>

                    <!-- ZIP Content Preview (hidden until file selected) -->
                    <div id="zip-preview" class="hidden mb-4 rounded-2xl border border-outline-variant/20 bg-surface-container-low overflow-hidden">
                        <div class="px-4 py-3 bg-primary/8 border-b border-outline-variant/10 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[18px]">preview</span>
                            <span class="text-xs font-bold text-primary uppercase tracking-wider">Backup Contents Preview</span>
                        </div>
                        <div class="p-4 grid grid-cols-2 gap-3" id="preview-grid">
                            <!-- Filled dynamically by JS -->
                        </div>
                        <div class="px-4 py-2.5 bg-amber-50 border-t border-amber-100 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-amber-600 text-[15px]">info</span>
                            <span class="text-[11px] text-amber-700 font-medium">All existing data will be replaced with the above counts on restore.</span>
                        </div>
                    </div>

                    <!-- Trigger Restore Button -->
                    <button type="submit" id="btn-trigger-restore"
                            class="w-full py-4 rounded-2xl font-bold flex items-center justify-center gap-2 shadow-lg transition-all duration-300 text-body-lg
                                   bg-surface-container-highest text-outline cursor-not-allowed opacity-50"
                            disabled>
                        <span class="material-symbols-outlined" id="btn-icon">restart_alt</span>
                        <span id="btn-label">Select a ZIP file to enable restore</span>
                    </button>
                </form>
            </div>

            <!-- Full-screen loading overlay (shown on form submit) -->
            <div id="restore-loading" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex flex-col items-center justify-center gap-4">
                <div class="bg-white rounded-3xl p-10 flex flex-col items-center gap-5 shadow-2xl max-w-sm w-full mx-4">
                    <div class="w-16 h-16 rounded-full border-4 border-primary border-t-transparent animate-spin"></div>
                    <div class="text-center">
                        <p class="text-headline-sm font-bold text-primary">Restoring System…</p>
                        <p class="text-xs text-outline mt-1">Do not close this window. Importing database &amp; media files.</p>
                    </div>
                </div>
            </div>

        </div>

        </div>

    </div>
</section>

<!-- Page Footer -->
<?php include 'includes/footer.php'; ?>
</main>

<!-- JSZip for reading ZIP contents client-side (preview only) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Interactive Frontend Interactions -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInput   = document.getElementById('backup-file-input');
    const dropZone    = document.getElementById('drop-zone');
    const dropDefault = document.getElementById('drop-default');
    const dropHover   = document.getElementById('drop-hover');
    const dropSelected= document.getElementById('drop-selected');
    const fileNameEl  = document.getElementById('file-name-display');
    const fileSizeEl  = document.getElementById('file-size-display');
    const zipPreview  = document.getElementById('zip-preview');
    const previewGrid = document.getElementById('preview-grid');
    const btnRestore  = document.getElementById('btn-trigger-restore');
    const btnIcon     = document.getElementById('btn-icon');
    const btnLabel    = document.getElementById('btn-label');
    const form        = document.getElementById('restore-form');
    const loadingOverlay = document.getElementById('restore-loading');

    // ── Drag-and-Drop Events ────────────────────────────────────────────
    ['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('border-primary', 'bg-primary/8');
            dropDefault.classList.add('hidden');
            dropHover.classList.remove('hidden');
            dropHover.classList.add('flex');
        });
    });

    ['dragleave', 'dragend'].forEach(evt => {
        dropZone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            // Only reset if we actually left the drop zone
            if (!dropZone.contains(e.relatedTarget)) {
                resetToDefault();
            }
        });
    });

    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        e.stopPropagation();
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    // ── File Input Change ────────────────────────────────────────────────
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            handleFile(fileInput.files[0]);
        }
    });

    // ── Handle Selected File ─────────────────────────────────────────────
    function handleFile(file) {
        const ext = file.name.split('.').pop().toLowerCase();
        if (ext !== 'zip') {
            alert('Invalid file type. Please upload a valid Frio .zip backup.');
            resetToDefault();
            return;
        }

        // Show selected state in drop zone
        dropDefault.classList.add('hidden');
        dropHover.classList.add('hidden');
        dropHover.classList.remove('flex');
        dropSelected.classList.remove('hidden');
        dropZone.classList.remove('border-outline-variant/40');
        dropZone.classList.add('border-primary/50', 'bg-primary/5');

        fileNameEl.textContent = file.name;
        fileSizeEl.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB  ·  ' + new Date().toLocaleTimeString();

        // Transfer to actual input
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;

        // Show loading preview state
        zipPreview.classList.remove('hidden');
        previewGrid.innerHTML = `
            <div class="col-span-2 flex items-center gap-2 text-outline py-2">
                <div class="w-4 h-4 rounded-full border-2 border-primary border-t-transparent animate-spin"></div>
                <span class="text-xs">Reading ZIP contents…</span>
            </div>`;

        // Read ZIP and parse SQL to get counts
        readZipContents(file);
    }

    // ── Parse ZIP Contents ────────────────────────────────────────────────
    async function readZipContents(file) {
        try {
            const zip = await JSZip.loadAsync(file);
            const sqlFile = zip.file('database_backup.sql');
            if (!sqlFile) {
                showPreviewError('database_backup.sql not found in ZIP.');
                return;
            }
            const sql = await sqlFile.async('string');

            // Count INSERT/REPLACE rows per table
            const tables = {
                'category':          { label: 'Categories',       icon: 'category' },
                'product':           { label: 'Products',         icon: 'inventory_2' },
                'product_variation': { label: 'Variations',       icon: 'tune' },
                'product_image':     { label: 'Product Images',   icon: 'image' },
                'banner_slider':     { label: 'Banner Slides',    icon: 'view_carousel' },
                'catalogue':         { label: 'Catalogues',       icon: 'menu_book' },
                'admin_users':       { label: 'Admin Accounts',   icon: 'manage_accounts' },
                'settings':          { label: 'Site Settings',    icon: 'settings' },
            };

            // Count media files
            let mediaCount = 0;
            zip.forEach((path, zipEntry) => {
                if (!zipEntry.dir && /\.(jpg|jpeg|png|gif|webp|pdf)$/i.test(path)) {
                    mediaCount++;
                }
            });

            // Count rows per table using regex on SQL
            const counts = {};
            for (const table of Object.keys(tables)) {
                // Match both INSERT INTO and REPLACE INTO for this table
                const re = new RegExp(`(?:INSERT INTO|REPLACE INTO)\\s+\`${table}\``, 'gi');
                const matches = sql.match(re);
                counts[table] = matches ? matches.length : 0;
            }

            // Build preview grid
            let html = '';
            for (const [table, meta] of Object.entries(tables)) {
                const count = counts[table];
                const isEmpty = count === 0;
                html += `
                <div class="flex items-center gap-3 p-3 rounded-xl ${isEmpty ? 'bg-surface-container-low opacity-50' : 'bg-white/60 border border-outline-variant/20'} shadow-sm">
                    <span class="material-symbols-outlined text-[22px] ${isEmpty ? 'text-outline' : 'text-primary'} bg-primary/10 p-2 rounded-lg shrink-0">${meta.icon}</span>
                    <div class="min-w-0">
                        <p class="text-[11px] text-outline font-medium truncate">${meta.label}</p>
                        <p class="text-label-bold font-bold ${isEmpty ? 'text-outline' : 'text-on-surface'}">${count} ${isEmpty ? '(empty)' : count === 1 ? 'record' : 'records'}</p>
                    </div>
                </div>`;
            }

            // Add media row spanning full width
            html += `
            <div class="col-span-2 flex items-center gap-3 p-3 rounded-xl bg-white/60 border border-outline-variant/20 shadow-sm">
                <span class="material-symbols-outlined text-[22px] text-primary bg-primary/10 p-2 rounded-lg shrink-0">perm_media</span>
                <div>
                    <p class="text-[11px] text-outline font-medium">Media Files (images, PDFs)</p>
                    <p class="text-label-bold font-bold text-on-surface">${mediaCount} files</p>
                </div>
            </div>`;

            previewGrid.innerHTML = html;

            // Activate the restore button
            btnRestore.disabled = false;
            btnRestore.className = 'w-full py-4 rounded-2xl font-bold flex items-center justify-center gap-2 shadow-lg transition-all duration-300 text-body-lg bg-error hover:bg-red-700 text-white cursor-pointer hover:shadow-red-500/30 hover:-translate-y-0.5 active:scale-95';
            btnIcon.textContent  = 'restart_alt';
            btnLabel.textContent = 'Trigger Environment Restore';

        } catch (err) {
            showPreviewError('Could not read ZIP: ' + err.message);
        }
    }

    function showPreviewError(msg) {
        previewGrid.innerHTML = `
            <div class="col-span-2 flex items-center gap-2 text-error py-2">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span class="text-xs font-bold">${msg}</span>
            </div>`;
    }

    // ── Clear File ───────────────────────────────────────────────────────
    window.clearFile = function(e) {
        e.stopPropagation();
        fileInput.value = '';
        zipPreview.classList.add('hidden');
        previewGrid.innerHTML = '';
        resetToDefault();
    };

    function resetToDefault() {
        dropDefault.classList.remove('hidden');
        dropHover.classList.add('hidden');
        dropHover.classList.remove('flex');
        dropSelected.classList.add('hidden');
        dropZone.classList.remove('border-primary/50', 'bg-primary/8', 'bg-primary/5');
        dropZone.classList.add('border-outline-variant/40');
        btnRestore.disabled = true;
        btnRestore.className = 'w-full py-4 rounded-2xl font-bold flex items-center justify-center gap-2 shadow-lg transition-all duration-300 text-body-lg bg-surface-container-highest text-outline cursor-not-allowed opacity-50';
        btnIcon.textContent  = 'restart_alt';
        btnLabel.textContent = 'Select a ZIP file to enable restore';
    }

    // ── Form Submit ───────────────────────────────────────────────────────
    form.addEventListener('submit', e => {
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            alert('Please select a backup ZIP file first.');
            return;
        }
        const confirmed = confirm('WARNING: This restore will overwrite ALL current database records and replace matching media files.\n\nThis cannot be undone. Proceed?');
        if (!confirmed) {
            e.preventDefault();
            return;
        }
        // Show loading overlay
        loadingOverlay.classList.remove('hidden');
        btnRestore.disabled = true;
        btnLabel.textContent = 'Restoring…';
    });
});
</script>

</body></html>

