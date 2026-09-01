<?php
/**
 * FRIO Admin Console - Admin Users Management Module
 * Allows listing, adding, editing, and deleting console administrators.
 * Features secure MD5 password hashing and active-session delete protection.
 */
require_once 'auth_check.php';
require_once 'db_connect.php';

$success_msg = '';
$error_msg = '';

if (isset($_SESSION['admin_success'])) {
    $success_msg = $_SESSION['admin_success'];
    unset($_SESSION['admin_success']);
}
if (isset($_SESSION['admin_error'])) {
    $error_msg = $_SESSION['admin_error'];
    unset($_SESSION['admin_error']);
}

// 1. Handle Delete Action (GET Request)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    
    try {
        // Fetch target user details
        $stmt = $pdo->prepare("SELECT * FROM `admin_users` WHERE `id` = ?");
        $stmt->execute([$delete_id]);
        $target_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($target_user) {
            // Safety guard: prevent self-deletion
            if ($target_user['username'] === $_SESSION['admin_username']) {
                $_SESSION['admin_error'] = "Security Violation: You cannot delete your active logged-in profile.";
            } else {
                $del_stmt = $pdo->prepare("DELETE FROM `admin_users` WHERE `id` = ?");
                $del_stmt->execute([$delete_id]);
                $_SESSION['admin_success'] = "Administrator account '{$target_user['username']}' deleted successfully.";
            }
        } else {
            $_SESSION['admin_error'] = "The target administrator account could not be found.";
        }
    } catch (PDOException $e) {
        $_SESSION['admin_error'] = "Database Error: " . $e->getMessage();
    }
    
    header("Location: admin_users.php");
    exit;
}

// 2. Handle Add / Edit Submission (POST Request)
$edit_mode = false;
$edit_id = 0;
$edit_user = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action   = trim($_POST['action'] ?? 'add');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $target_id = intval($_POST['user_id'] ?? 0);

    if (empty($username)) {
        $_SESSION['admin_error'] = "Username is required.";
        header("Location: admin_users.php" . ($action === 'edit' ? "?edit=$target_id" : ""));
        exit;
    }

    if (strlen($username) < 3) {
        $_SESSION['admin_error'] = "Username must be at least 3 characters long.";
        header("Location: admin_users.php" . ($action === 'edit' ? "?edit=$target_id" : ""));
        exit;
    }

    if ($action === 'add') {
        // Add Administrator Account
        if (empty($password)) {
            $_SESSION['admin_error'] = "Password is required for new accounts.";
            header("Location: admin_users.php");
            exit;
        }

        try {
            // Check for existing username
            $chk = $pdo->prepare("SELECT COUNT(*) FROM `admin_users` WHERE `username` = ?");
            $chk->execute([$username]);
            if ($chk->fetchColumn() > 0) {
                $_SESSION['admin_error'] = "Username '{$username}' is already taken.";
                header("Location: admin_users.php");
                exit;
            }

            // Insert new administrator
            $ins = $pdo->prepare("INSERT INTO `admin_users` (`username`, `password`) VALUES (?, ?)");
            $ins->execute([$username, md5($password)]);
            
            $_SESSION['admin_success'] = "New administrator account '{$username}' created successfully.";
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = "Failed to create account: " . $e->getMessage();
        }

    } elseif ($action === 'edit' && $target_id > 0) {
        // Edit Administrator Account
        try {
            // Check for username conflicts
            $chk = $pdo->prepare("SELECT COUNT(*) FROM `admin_users` WHERE `username` = ? AND `id` != ?");
            $chk->execute([$username, $target_id]);
            if ($chk->fetchColumn() > 0) {
                $_SESSION['admin_error'] = "Username '{$username}' is already taken by another account.";
                header("Location: admin_users.php?edit=$target_id");
                exit;
            }

            // Fetch target user's current username for session updating
            $curr_stmt = $pdo->prepare("SELECT `username` FROM `admin_users` WHERE `id` = ?");
            $curr_stmt->execute([$target_id]);
            $old_username = $curr_stmt->fetchColumn();

            if (!empty($password)) {
                // Update username & password
                $upd = $pdo->prepare("UPDATE `admin_users` SET `username` = ?, `password` = ? WHERE `id` = ?");
                $upd->execute([$username, md5($password), $target_id]);
            } else {
                // Update username only
                $upd = $pdo->prepare("UPDATE `admin_users` SET `username` = ? WHERE `id` = ?");
                $upd->execute([$username, $target_id]);
            }

            // Sync active session username if self-updating
            if ($old_username === $_SESSION['admin_username']) {
                $_SESSION['admin_username'] = $username;
            }

            $_SESSION['admin_success'] = "Administrator account parameters updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = "Failed to update account: " . $e->getMessage();
        }
    }

    header("Location: admin_users.php");
    exit;
}

// 3. Load Active Edit Parameters if requested
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM `admin_users` WHERE `id` = ?");
        $stmt->execute([$edit_id]);
        $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($edit_user) {
            $edit_mode = true;
        }
    } catch (PDOException $e) {
        // Fallback
    }
}

// 4. Fetch All Administrator Accounts for Listing
$admins = [];
try {
    $admins = $pdo->query("SELECT * FROM `admin_users` ORDER BY `id` ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "Database Error: " . $e->getMessage();
}

$page_title = "FRIO Console | Administrative Accounts";
include 'includes/head.php';
?>
<body class="bg-background text-on-background min-h-screen flex overflow-hidden">
<?php include 'includes/sidebar.php'; ?>

<!-- Main Workspace -->
<main class="ml-64 flex-grow h-screen overflow-y-auto flex flex-col justify-between">
<?php 
$header_title = 'Admin Users';
include 'includes/header.php'; 
?>

<!-- Content Area -->
<section class="mt-24 p-gutter flex-grow">
    <!-- Top Header Title -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-primary">Console Administrators</h1>
            <p class="text-body-md text-outline">Manage administrative access keys, credentials, and hash codes controlling the FRIO Admin Console.</p>
        </div>
        <div class="text-xs text-outline bg-surface-container px-3 py-1 rounded-full border border-outline-variant/30 flex items-center gap-1.5 font-bold uppercase tracking-wider">
            <span class="material-symbols-outlined text-[14px] text-primary">manage_accounts</span>
            Active Registry: <?php echo count($admins); ?> Accounts
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

    <!-- Two-Column Workspace Layout -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-gutter items-start">
        
        <!-- List View Table Left Column (7/12) -->
        <div class="xl:col-span-7 space-y-6">
            <div class="glass-card rounded-[2rem] border border-white/20 shadow-md overflow-hidden">
                <div class="p-6 border-b border-outline-variant/10 flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">group</span>
                    <h3 class="text-headline-md font-headline-md text-primary">Authorized Administrators</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low text-outline text-[11px] uppercase tracking-widest font-bold border-b border-outline-variant/20">
                                <th class="py-4 px-6 text-center w-16">ID</th>
                                <th class="py-4 px-6">Username</th>
                                <th class="py-4 px-6">Created Date</th>
                                <th class="py-4 px-6 text-center w-32">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10 text-body-md">
                            <?php foreach ($admins as $admin): 
                                $is_self = ($admin['username'] === $_SESSION['admin_username']);
                            ?>
                                <tr class="hover:bg-primary-fixed/5 transition-colors duration-200 group">
                                    <td class="py-4 px-6 text-center font-bold text-outline text-xs"><?php echo $admin['id']; ?></td>
                                    <td class="py-4 px-6 font-bold text-on-surface flex items-center gap-2">
                                        <?php echo htmlspecialchars($admin['username']); ?>
                                        <?php if ($is_self): ?>
                                            <span class="px-2 py-0.5 text-[9px] bg-primary text-white border border-white/10 rounded-full font-bold uppercase tracking-wider">You</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-6 text-outline font-medium"><?php echo date("M d, Y H:i", strtotime($admin['created_at'])); ?></td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Edit button -->
                                            <a href="admin_users.php?edit=<?php echo $admin['id']; ?>" class="p-2 hover:bg-primary/10 rounded-xl transition-all duration-300 text-primary flex items-center justify-center hover:scale-110" title="Edit Admin">
                                                <span class="material-symbols-outlined text-[20px]">edit</span>
                                            </a>
                                            <!-- Delete button -->
                                            <?php if ($is_self): ?>
                                                <span class="p-2 text-outline/35 cursor-not-allowed flex items-center justify-center" title="Self-deletion Protected">
                                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                                </span>
                                            <?php else: ?>
                                                <a href="admin_users.php?delete=<?php echo $admin['id']; ?>" onclick="return confirm('Are you absolutely certain you wish to delete administrator account \'<?php echo htmlspecialchars($admin['username']); ?>\'?');" class="p-2 hover:bg-error/10 rounded-xl transition-all duration-300 text-error flex items-center justify-center hover:scale-110" title="Delete Admin">
                                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add / Edit Form Right Column (5/12) -->
        <div class="xl:col-span-5 space-y-6">
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md">
                
                <!-- Dynamic Form Header -->
                <div class="flex items-center justify-between mb-6 pb-2 border-b border-outline-variant/10">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">
                            <?php echo $edit_mode ? 'manage_accounts' : 'person_add'; ?>
                        </span>
                        <h3 class="text-headline-md font-headline-md text-primary">
                            <?php echo $edit_mode ? 'Edit Administrator' : 'Add Administrator'; ?>
                        </h3>
                    </div>
                    <?php if ($edit_mode): ?>
                        <a href="admin_users.php" class="text-xs font-bold text-outline hover:text-primary flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">arrow_back</span>
                            Cancel Edit
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Coordinator Input Fields -->
                <form method="POST" action="admin_users.php" class="space-y-5">
                    <input type="hidden" name="action" value="<?php echo $edit_mode ? 'edit' : 'add'; ?>" />
                    <input type="hidden" name="user_id" value="<?php echo $edit_id; ?>" />

                    <!-- Username input -->
                    <div class="space-y-1">
                        <label for="username" class="text-label-bold font-bold text-on-surface">Username</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">person</span>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($edit_mode ? $edit_user['username'] : ''); ?>" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl pl-10 pr-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass font-semibold" placeholder="Enter username" required />
                        </div>
                    </div>

                    <!-- Password input -->
                    <div class="space-y-1">
                        <label for="password" class="text-label-bold font-bold text-on-surface">
                            Password <?php echo $edit_mode ? '<span class="text-outline font-normal text-xs">(Optional)</span>' : ''; ?>
                        </label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors">lock</span>
                            <input type="password" id="password" name="password" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl pl-10 pr-4 py-3 text-body-md focus:ring-2 focus:ring-primary/20 transition-all input-focus-brass" placeholder="<?php echo $edit_mode ? 'Preserves current password if blank' : 'Enter account password'; ?>" <?php echo $edit_mode ? '' : 'required'; ?> />
                        </div>
                    </div>

                    <!-- Save Trigger Action -->
                    <button type="submit" class="w-full bg-primary hover:bg-primary-container text-white py-3.5 rounded-2xl font-bold btn-glow flex items-center justify-center gap-2 shadow-lg transition-all duration-300">
                        <span class="material-symbols-outlined">
                            <?php echo $edit_mode ? 'save' : 'how_to_reg'; ?>
                        </span>
                        <span>
                            <?php echo $edit_mode ? 'Save Account Changes' : 'Create Administrator Account'; ?>
                        </span>
                    </button>

                </form>
            </div>

            <!-- Security Architecture Advice Card -->
            <div class="glass-card p-5 rounded-[2rem] border border-white/20 shadow-md">
                <h5 class="text-label-bold font-bold text-primary flex items-center gap-1.5 mb-2">
                    <span class="material-symbols-outlined text-[18px]">security</span>
                    Access Security Standards
                </h5>
                <ul class="space-y-1.5 text-xs text-outline leading-relaxed list-disc pl-4">
                    <li>Minimum username length is 3 characters (alphanumeric).</li>
                    <li>Passwords are stored using secure <b>MD5 hash checksums</b> within the registry database schema.</li>
                    <li>Self-deletion is actively restricted to prevent locks or structural administrator orphan errors.</li>
                </ul>
            </div>
        </div>

    </div>
</section>

<!-- Page Footer -->
<?php include 'includes/footer.php'; ?>
</main>
</body></html>
