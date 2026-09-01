<?php
/**
 * FRIO Admin Console - Customer Inquiries Hub
 * Handles the viewing, management, deletion, and high-fidelity CSV export of
 * both Gated Brochure Catalogue Downloads and Contact Us inquiries.
 */
$base_path = '';
require_once 'auth_check.php';
require_once 'db_connect.php';

// Initialize error or success messages
$success_msg = '';
$error_msg = '';

if (isset($_GET['deleted']) && $_GET['deleted'] === 'success') {
    $success_msg = 'Inquiry record successfully deleted.';
}

// ── Handle Single Deletion Action ───────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM `inquiries` WHERE `id` = ?");
        $stmt->execute([$delete_id]);
        header("Location: inquiries.php?deleted=success");
        exit;
    } catch (PDOException $e) {
        $error_msg = "Error deleting record: " . htmlspecialchars($e->getMessage());
    }
}

// ── Handle Bulk Deletion Action ─────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'bulk_delete' && isset($_GET['ids'])) {
    $ids = explode(',', $_GET['ids']);
    $ids = array_map('intval', $ids);
    if (!empty($ids)) {
        try {
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $pdo->prepare("DELETE FROM `inquiries` WHERE `id` IN ($placeholders)");
            $stmt->execute($ids);
            header("Location: inquiries.php?deleted=success");
            exit;
        } catch (PDOException $e) {
            $error_msg = "Error bulk deleting records: " . htmlspecialchars($e->getMessage());
        }
    }
}

// ── Handle High-Fidelity CSV Export Action ───────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $export_type = isset($_GET['type']) ? trim($_GET['type']) : 'catalogue';
    if ($export_type !== 'catalogue' && $export_type !== 'contact') {
        $export_type = 'catalogue';
    }
    
    // Clear buffer to prevent corrupted CSV downloads
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=frio_' . $export_type . '_inquiries_' . date('Ymd_His') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Output UTF-8 BOM for full Excel/UTF-8 compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    if ($export_type === 'catalogue') {
        fputcsv($output, ['ID', 'First Name', 'Last Name', 'Email Address', 'Mobile Number', 'Brochure / File Target', 'Submitted Date']);
        $stmt = $pdo->prepare("
            SELECT `id`, `first_name`, `last_name`, `email`, `phone`, `message`, `created_at`
            FROM `inquiries`
            WHERE `type` = 'catalogue'
            ORDER BY `id` ASC
        ");
    } else {
        fputcsv($output, ['ID', 'First Name', 'Last Name', 'Email Address', 'Mobile Number', 'Contact Message', 'Submitted Date']);
        $stmt = $pdo->prepare("
            SELECT `id`, `first_name`, `last_name`, `email`, `phone`, `message`, `created_at`
            FROM `inquiries`
            WHERE `type` = 'contact'
            ORDER BY `id` ASC
        ");
    }
    
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

// ── Retrieve Customer Inquiries from Database ────────────────────────────────
try {
    // Catalogue Brochure Download leads
    $stmt_cat = $pdo->prepare("SELECT * FROM `inquiries` WHERE `type` = 'catalogue' ORDER BY `id` ASC");
    $stmt_cat->execute();
    $catalogue_inquiries = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

    // Contact Us Message inquiries
    $stmt_con = $pdo->prepare("SELECT * FROM `inquiries` WHERE `type` = 'contact' ORDER BY `id` ASC");
    $stmt_con->execute();
    $contact_inquiries = $stmt_con->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $catalogue_inquiries = [];
    $contact_inquiries = [];
    $error_msg = "Database Error: " . $e->getMessage();
}

$page_title = "FRIO Admin Console | Customer Inquiries";
include 'includes/head.php';
?>
<body class="bg-background text-on-surface font-body-md selection:bg-primary/20">
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content Area -->
<main class="ml-64 min-h-screen flex flex-col">
<?php
$header_title = 'Customer Inquiries';
include 'includes/header.php';
?>

<!-- Canvas Area -->
<div class="pt-24 pb-12 px-gutter flex-grow max-w-screen-2xl mx-auto w-full">
    <!-- Breadcrumbs in Canvas -->
    <nav class="flex items-center text-label-sm text-on-surface-variant mb-6">
        <a href="dashbord.php" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">dashboard</span>
            <span>Dashboard</span>
        </a>
        <span class="material-symbols-outlined text-[16px] mx-2">chevron_right</span>
        <span class="text-primary font-bold">Customer Inquiries</span>
    </nav>

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-primary tracking-tight">CUSTOMER INQUIRIES</h2>
            <p class="text-body-lg text-on-surface-variant mt-1">Review B2B catalogue downloads and user contact requests from a single database console.</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Dynamic Bulk Delete Button -->
            <button id="bulkDeleteBtn" onclick="submitBulkDelete()" class="bg-error text-white hover:bg-error/95 flex items-center gap-2 px-6 py-3 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-error/20 hover:-translate-y-0.5 transition-all active:scale-95 hidden animate-fadeIn">
                <span class="material-symbols-outlined text-[20px]">delete_sweep</span>
                DELETE SELECTED (<span id="selectedCount">0</span>)
            </button>
            
            <!-- High-Fidelity CSV Export Button -->
            <button onclick="exportCSV()" class="bg-primary text-on-primary hover:bg-primary/95 flex items-center gap-2 px-6 py-3 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-primary/20 hover:-translate-y-0.5 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]">ios_share</span>
                EXPORT ACTIVE LIST
            </button>
        </div>
    </div>

    <!-- Alert Messaging -->
    <?php if (!empty($success_msg)): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-sm animate-fadeIn">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <p class="text-sm font-semibold"><?php echo $success_msg; ?></p>
        </div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="mb-6 p-4 bg-error-container border border-error/20 text-on-error-container rounded-2xl flex items-center gap-3 shadow-sm animate-fadeIn">
            <span class="material-symbols-outlined text-error">error</span>
            <p class="text-sm font-semibold"><?php echo $error_msg; ?></p>
        </div>
    <?php endif; ?>

    <!-- Gorgeous Tab Switcher -->
    <div class="flex border-b border-outline-variant/30 mb-6 gap-2">
        <button onclick="switchTab('catalogue')" id="tab-btn-catalogue" class="py-4 px-6 font-label-bold text-label-bold text-sm tracking-wide border-b-2 border-primary text-primary transition-all flex items-center gap-2 relative">
            <span class="material-symbols-outlined text-[20px]">menu_book</span>
            <span>CATALOGUE DOWNLOADS</span>
            <span class="px-2 py-0.5 bg-primary/10 text-primary rounded-full text-xs font-extrabold"><?php echo count($catalogue_inquiries); ?></span>
        </button>
        <button onclick="switchTab('contact')" id="tab-btn-contact" class="py-4 px-6 font-label-bold text-label-bold text-sm tracking-wide border-b-2 border-transparent text-on-surface-variant hover:text-primary transition-all flex items-center gap-2 relative">
            <span class="material-symbols-outlined text-[20px]">chat_bubble</span>
            <span>CONTACT US MESSAGES</span>
            <span class="px-2 py-0.5 bg-on-surface-variant/10 text-on-surface-variant rounded-full text-xs font-extrabold"><?php echo count($contact_inquiries); ?></span>
        </button>
    </div>

    <!-- Filter & Real-Time Search Bar -->
    <div class="glass-card rounded-2xl p-4 mb-6 flex items-center justify-between shadow-sm border border-white/40">
        <div class="relative w-full max-w-md">
            <input class="w-full bg-white/50 border-outline-variant rounded-xl py-3 pl-12 focus:ring-primary focus:border-primary text-body-md transition-all" id="inquirySearch" autocomplete="off" placeholder="Search inquiries..." type="text"/>
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
        </div>
        <button class="p-3 text-on-surface-variant hover:bg-white/50 rounded-xl transition-all border border-transparent hover:border-outline-variant group" onclick="clearSearch()">
            <span class="material-symbols-outlined group-active:rotate-180 transition-transform">restart_alt</span>
        </button>
    </div>

    <!-- ── TAB 1: CATALOGUE DOWNLOADS WORKSPACE ───────────────────────────────── -->
    <div id="tab-content-catalogue" class="tab-content block">
        <div class="overflow-x-auto overflow-y-hidden glass-card rounded-3xl border border-white/40 shadow-xl">
            <table class="w-full text-left border-collapse">
                <thead class="bg-primary text-on-primary">
                    <tr>
                        <th class="py-5 px-4 font-label-bold text-label-bold uppercase tracking-wider w-12 text-center">
                            <input type="checkbox" id="selectAllCatalogue" class="rounded border-outline-variant/60 text-secondary focus:ring-secondary/20 cursor-pointer w-4 h-4 bg-white/50" onclick="toggleSelectAll('catalogue')" />
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors w-24" onclick="sortColumn('catalogue', 1, 'number')">
                            <div class="flex items-center gap-1">NO. <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn('catalogue', 2, 'string')">
                            <div class="flex items-center gap-1">Customer Name <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn('catalogue', 3, 'string')">
                            <div class="flex items-center gap-1">Email <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider">Mobile Number</th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn('catalogue', 5, 'string')">
                            <div class="flex items-center gap-1">Target Document <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors w-48" onclick="sortColumn('catalogue', 6, 'date')">
                            <div class="flex items-center gap-1">Downloaded Date <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-highest" id="catalogueTableBody">
                    <?php if (empty($catalogue_inquiries)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl mb-2 block text-outline">menu_book</span>
                                No catalogue downloads registered yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $idx_cat = 1;
                        foreach ($catalogue_inquiries as $inq): 
                        ?>
                            <tr class="hover:bg-primary/5 transition-colors group inquiry-row cursor-pointer" data-tab-type="catalogue" data-inquiry="<?php echo htmlspecialchars(json_encode($inq), ENT_QUOTES, 'UTF-8'); ?>">
                                <td class="py-5 px-4 text-center w-12 checkbox-cell">
                                    <input type="checkbox" value="<?php echo $inq['id']; ?>" class="catalogue-select-checkbox rounded border-outline-variant/60 text-secondary focus:ring-secondary/20 cursor-pointer w-4 h-4 bg-white/50" onclick="updateBulkDeleteState('catalogue')" />
                                </td>
                                <td class="py-5 px-6 text-label-bold text-on-surface-variant">#<?php echo $idx_cat++; ?></td>
                                <td class="py-5 px-6 font-bold text-primary">
                                    <?php echo htmlspecialchars($inq['first_name'] . ' ' . $inq['last_name']); ?>
                                </td>
                                <td class="py-5 px-6">
                                    <a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>" class="hover:text-primary transition-colors underline decoration-outline-variant">
                                        <?php echo htmlspecialchars($inq['email']); ?>
                                    </a>
                                </td>
                                <td class="py-5 px-6 text-label-bold tracking-tight font-bold text-on-surface">
                                    <?php 
                                    $formatted_phone = preg_replace('/(\d{5})(\d{5})/', '$1 $2', $inq['phone']);
                                    echo htmlspecialchars($formatted_phone); 
                                    ?>
                                </td>
                                <td class="py-5 px-6 max-w-[200px]">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-error text-[18px] shrink-0">picture_as_pdf</span>
                                        <span class="text-xs font-semibold bg-surface-container px-2.5 py-1 rounded-md text-on-surface-variant border border-outline-variant/20 truncate" title="<?php echo htmlspecialchars($inq['message']); ?>">
                                            <?php echo htmlspecialchars(basename($inq['message'])); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="py-5 px-6 text-label-sm text-outline font-semibold">
                                    <?php echo date('d M Y, h:i A', strtotime($inq['created_at'])); ?>
                                </td>
                                <td class="py-5 px-6 text-right action-cell">
                                    <div class="flex justify-end gap-2">
                                        <!-- Eye Detail View Icon -->
                                        <button onclick="viewInquiry(<?php echo htmlspecialchars(json_encode($inq), ENT_QUOTES, 'UTF-8'); ?>)" class="p-2 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-all shadow-sm" title="View inquiry detail">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $inq['id']; ?>, '<?php echo htmlspecialchars(addslashes($inq['first_name'] . ' ' . $inq['last_name'])); ?>')" class="p-2 border border-error text-error rounded-lg hover:bg-error hover:text-white transition-all shadow-sm" aria-label="Delete Entry">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Workspace Table Footer -->
            <div class="p-6 bg-white/30 flex items-center justify-between border-t border-white/20">
                <p class="text-label-sm text-on-surface-variant">Total: <span class="font-bold text-primary" id="catalogueCountText"><?php echo count($catalogue_inquiries); ?></span> Entries</p>
                <div class="flex items-center gap-2" id="cataloguePagination">
                    <!-- Dynamic client-side pagination buttons -->
                </div>
            </div>
        </div>
    </div>

    <!-- ── TAB 2: CONTACT US MESSAGES WORKSPACE ───────────────────────────────── -->
    <div id="tab-content-contact" class="tab-content hidden">
        <div class="overflow-x-auto overflow-y-hidden glass-card rounded-3xl border border-white/40 shadow-xl">
            <table class="w-full text-left border-collapse">
                <thead class="bg-primary text-on-primary">
                    <tr>
                        <th class="py-5 px-4 font-label-bold text-label-bold uppercase tracking-wider w-12 text-center">
                            <input type="checkbox" id="selectAllContact" class="rounded border-outline-variant/60 text-secondary focus:ring-secondary/20 cursor-pointer w-4 h-4 bg-white/50" onclick="toggleSelectAll('contact')" />
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors w-24" onclick="sortColumn('contact', 1, 'number')">
                            <div class="flex items-center gap-1">NO. <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn('contact', 2, 'string')">
                            <div class="flex items-center gap-1">Sender Name <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn('contact', 3, 'string')">
                            <div class="flex items-center gap-1">Email <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider">Mobile Number</th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider w-72">Customer Message</th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors w-48" onclick="sortColumn('contact', 6, 'date')">
                            <div class="flex items-center gap-1">Received Date <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                        </th>
                        <th class="py-5 px-6 font-label-bold text-label-bold uppercase tracking-wider text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-highest" id="contactTableBody">
                    <?php if (empty($contact_inquiries)): ?>
                        <tr>
                            <td colspan="8" class="py-12 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl mb-2 block text-outline">chat_bubble</span>
                                No contact messages received yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $idx_con = 1;
                        foreach ($contact_inquiries as $inq): 
                        ?>
                            <tr class="hover:bg-primary/5 transition-colors group inquiry-row cursor-pointer" data-tab-type="contact" data-inquiry="<?php echo htmlspecialchars(json_encode($inq), ENT_QUOTES, 'UTF-8'); ?>">
                                <td class="py-5 px-4 text-center w-12 checkbox-cell">
                                    <input type="checkbox" value="<?php echo $inq['id']; ?>" class="contact-select-checkbox rounded border-outline-variant/60 text-secondary focus:ring-secondary/20 cursor-pointer w-4 h-4 bg-white/50" onclick="updateBulkDeleteState('contact')" />
                                </td>
                                <td class="py-5 px-6 text-label-bold text-on-surface-variant">#<?php echo $idx_con++; ?></td>
                                <td class="py-5 px-6 font-bold text-primary">
                                    <?php echo htmlspecialchars($inq['first_name'] . ' ' . $inq['last_name']); ?>
                                </td>
                                <td class="py-5 px-6">
                                    <a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>" class="hover:text-primary transition-colors underline decoration-outline-variant">
                                        <?php echo htmlspecialchars($inq['email']); ?>
                                    </a>
                                </td>
                                <td class="py-5 px-6 text-label-bold tracking-tight font-bold text-on-surface">
                                    <?php 
                                    $formatted_phone = preg_replace('/(\d{5})(\d{5})/', '$1 $2', $inq['phone']);
                                    echo htmlspecialchars($formatted_phone); 
                                    ?>
                                </td>
                                <td class="py-5 px-6 max-w-xs">
                                    <!-- Neatly Truncated Message cell showing "..." as ellipsis -->
                                    <div class="text-xs font-semibold text-on-surface-variant truncate w-56 hover:text-primary transition-colors" title="Click to view full message">
                                        <?php echo htmlspecialchars($inq['message']); ?>
                                    </div>
                                </td>
                                <td class="py-5 px-6 text-label-sm text-outline font-semibold">
                                    <?php echo date('d M Y, h:i A', strtotime($inq['created_at'])); ?>
                                </td>
                                <td class="py-5 px-6 text-right action-cell">
                                    <div class="flex justify-end gap-2">
                                        <!-- Eye Detail View Icon -->
                                        <button onclick="viewInquiry(<?php echo htmlspecialchars(json_encode($inq), ENT_QUOTES, 'UTF-8'); ?>)" class="p-2 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-all shadow-sm" title="View message detail">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $inq['id']; ?>, '<?php echo htmlspecialchars(addslashes($inq['first_name'] . ' ' . $inq['last_name'])); ?>')" class="p-2 border border-error text-error rounded-lg hover:bg-error hover:text-white transition-all shadow-sm" aria-label="Delete Entry">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Workspace Table Footer -->
            <div class="p-6 bg-white/30 flex items-center justify-between border-t border-white/20">
                <p class="text-label-sm text-on-surface-variant">Total: <span class="font-bold text-primary" id="contactCountText"><?php echo count($contact_inquiries); ?></span> Entries</p>
                <div class="flex items-center gap-2" id="contactPagination">
                    <!-- Dynamic client-side pagination buttons -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── HIGH-FIDELITY CUSTOMER INQUIRY DETAILS MODAL ───────────────────────── -->
<div id="inquiry-details-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4">
    <!-- Backdrop Blur Overlay -->
    <div class="absolute inset-0 bg-[#001c39]/40 backdrop-blur-sm" onclick="closeInquiryDetails()"></div>
    
    <!-- Modal Canvas Body -->
    <div class="bg-white dark:bg-surface-container rounded-[2rem] border border-white/20 shadow-2xl w-full max-w-xl overflow-hidden relative z-10 scale-95 opacity-0 transition-all duration-300 transform" id="modal-container-body">
        <!-- Banner Header -->
        <div class="bg-gradient-to-r from-primary to-[#0c4b86] text-white px-8 py-6 flex items-center justify-between border-b border-white/10 relative overflow-hidden">
            <!-- Glow effect decoration -->
            <div class="absolute -right-16 -top-16 w-36 h-36 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex items-center gap-3 relative z-10">
                <span class="material-symbols-outlined text-secondary text-[32px] bg-white/10 p-2.5 rounded-2xl" id="detail-type-icon">mail</span>
                <div>
                    <h3 class="text-[18px] font-bold tracking-tight uppercase" id="detail-title">INQUIRY DETAILS</h3>
                    <p class="text-[10px] text-white/70 font-extrabold tracking-wider mt-0.5" id="detail-id-label">Inquiry #0</p>
                </div>
            </div>
            <button onclick="closeInquiryDetails()" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors relative z-10 group" aria-label="Close Details">
                <span class="material-symbols-outlined text-white text-[20px] group-hover:rotate-90 transition-transform">close</span>
            </button>
        </div>
        
        <!-- Parameter Details Body -->
        <div class="p-8 space-y-5 text-sm text-on-surface">
            <!-- Split Name & Phone Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/15 shadow-sm">
                    <span class="text-[10px] text-outline uppercase font-bold tracking-widest block mb-1">Customer Name</span>
                    <span class="font-bold text-primary text-[15px]" id="detail-name">-</span>
                </div>
                <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/15 shadow-sm">
                    <span class="text-[10px] text-outline uppercase font-bold tracking-widest block mb-1">Mobile Number</span>
                    <span class="font-bold text-on-surface text-[15px] tracking-tight" id="detail-phone">-</span>
                </div>
            </div>
            
            <!-- Email -->
            <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/15 shadow-sm">
                <span class="text-[10px] text-outline uppercase font-bold tracking-widest block mb-1">Email Address</span>
                <a href="#" class="font-bold text-primary text-[15px] hover:text-secondary hover:underline transition-colors block truncate" id="detail-email">-</a>
            </div>
            
            <!-- Message/brochure content -->
            <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/15 shadow-sm">
                <span class="text-[10px] text-outline uppercase font-bold tracking-widest block mb-1.5" id="detail-message-label">Customer Message</span>
                <div class="text-xs text-on-surface-variant font-medium bg-white/60 p-4 rounded-xl border border-outline-variant/10 shadow-inner max-h-48 overflow-y-auto whitespace-pre-wrap leading-relaxed" id="detail-message">-</div>
            </div>
            
            <!-- Metadata Line -->
            <div class="flex items-center justify-between text-xs text-outline font-semibold pt-3 border-t border-outline-variant/10">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-secondary">calendar_today</span>
                    <span id="detail-date">-</span>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1 bg-surface-container-high rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full" id="detail-badge-dot"></span>
                    <span class="text-[10px] font-extrabold uppercase tracking-wide text-on-surface-variant" id="detail-badge-text">-</span>
                </div>
            </div>
        </div>
        
        <!-- Action Drawer Footer -->
        <div class="bg-surface-container-lowest px-8 py-4 border-t border-outline-variant/10 flex justify-end gap-3">
            <button onclick="closeInquiryDetails()" class="px-5 py-2.5 bg-surface-container-high hover:bg-surface-container-highest text-on-surface-variant rounded-xl font-label-bold text-label-bold transition-all shadow-sm">
                CLOSE
            </button>
            <a href="#" id="detail-action-btn" class="bg-primary text-on-primary hover:bg-primary/95 flex items-center gap-1.5 px-5 py-2.5 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-primary/10 transition-all">
                <span class="material-symbols-outlined text-[18px]">mail</span>
                <span>REPLY EMAIL</span>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</main>

<script>
    const FRONTEND_LIVE_URL = '<?php echo FRONTEND_LIVE_URL; ?>';
    let activeTab = 'catalogue';
    
    // Pagination state
    let currentPage = {
        catalogue: 1,
        contact: 1
    };
    const PAGE_SIZE = 10;

    // Switch between table workspaces
    function switchTab(tab) {
        activeTab = tab;
        
        // Update tab buttons styles
        const btnCat = document.getElementById('tab-btn-catalogue');
        const btnCon = document.getElementById('tab-btn-contact');
        const contentCat = document.getElementById('tab-content-catalogue');
        const contentCon = document.getElementById('tab-content-contact');

        if (tab === 'catalogue') {
            btnCat.className = "py-4 px-6 font-label-bold text-label-bold text-sm tracking-wide border-b-2 border-primary text-primary transition-all flex items-center gap-2 relative";
            btnCon.className = "py-4 px-6 font-label-bold text-label-bold text-sm tracking-wide border-b-2 border-transparent text-on-surface-variant hover:text-primary transition-all flex items-center gap-2 relative";
            contentCat.classList.remove('hidden');
            contentCat.classList.add('block');
            contentCon.classList.remove('block');
            contentCon.classList.add('hidden');
        } else {
            btnCat.className = "py-4 px-6 font-label-bold text-label-bold text-sm tracking-wide border-b-2 border-transparent text-on-surface-variant hover:text-primary transition-all flex items-center gap-2 relative";
            btnCon.className = "py-4 px-6 font-label-bold text-label-bold text-sm tracking-wide border-b-2 border-primary text-primary transition-all flex items-center gap-2 relative";
            contentCat.classList.remove('block');
            contentCat.classList.add('hidden');
            contentCon.classList.remove('hidden');
            contentCon.classList.add('block');
        }

        // Search filtering state reload
        filterInquiries();
        updateBulkDeleteState(activeTab);
    }

    // Dynamic Pagination and Indexing calculations
    function updatePagination(tab) {
        const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const allRows = document.querySelectorAll(`.inquiry-row[data-tab-type="${tab}"]`);
        
        // 1. Get visible (filtered) rows
        const visibleRows = Array.from(allRows).filter(row => {
            const text = row.innerText.toLowerCase();
            return text.includes(term);
        });

        const totalEntries = visibleRows.length;
        const totalPages = Math.ceil(totalEntries / PAGE_SIZE) || 1;

        // Ensure current page is in valid range
        if (currentPage[tab] > totalPages) {
            currentPage[tab] = totalPages;
        }
        if (currentPage[tab] < 1) {
            currentPage[tab] = 1;
        }

        const startIdx = (currentPage[tab] - 1) * PAGE_SIZE;
        const endIdx = startIdx + PAGE_SIZE;

        // 2. Hide/show rows and update their sequential numbers dynamically
        visibleRows.forEach((row, i) => {
            const noCell = row.cells[1];
            if (noCell) {
                noCell.textContent = `#${i + 1}`;
            }

            if (i >= startIdx && i < endIdx) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Hide rows that don't match the search term
        allRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (!text.includes(term)) {
                row.style.display = 'none';
            }
        });

        // 3. Update count indicator
        const countText = document.getElementById(`${tab}CountText`);
        if (countText) {
            countText.textContent = totalEntries;
        }

        // 4. Render pagination controls
        renderControls(tab, totalPages);
    }

    function renderControls(tab, totalPages) {
        const container = document.getElementById(`${tab}Pagination`);
        if (!container) return;

        container.innerHTML = '';

        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.className = "px-4 py-2 rounded-lg text-label-bold text-primary hover:bg-white transition-all disabled:opacity-50";
        prevBtn.textContent = 'Previous';
        prevBtn.disabled = currentPage[tab] === 1;
        prevBtn.onclick = () => {
            currentPage[tab]--;
            updatePagination(tab);
        };
        container.appendChild(prevBtn);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            if (i === currentPage[tab]) {
                pageBtn.className = "w-10 h-10 rounded-lg bg-primary text-on-primary font-bold shadow-md";
            } else {
                pageBtn.className = "w-10 h-10 rounded-lg text-primary hover:bg-white transition-all font-bold";
            }
            pageBtn.textContent = i;
            pageBtn.onclick = () => {
                currentPage[tab] = i;
                updatePagination(tab);
            };
            container.appendChild(pageBtn);
        }

        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.className = "px-4 py-2 rounded-lg text-label-bold text-primary hover:bg-white transition-all disabled:opacity-50";
        nextBtn.textContent = 'Next';
        nextBtn.disabled = currentPage[tab] === totalPages;
        nextBtn.onclick = () => {
            currentPage[tab]++;
            updatePagination(tab);
        };
        container.appendChild(nextBtn);
    }

    // Live Real-Time Search Handler
    const searchInput = document.getElementById('inquirySearch');
    
    function filterInquiries() {
        updatePagination(activeTab);
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterInquiries);
    }

    function clearSearch() {
        if (searchInput) {
            searchInput.value = '';
            filterInquiries();
        }
    }

    // Column Table Sorting
    let sortState = {
        catalogue: { col: -1, asc: true },
        contact: { col: -1, asc: true }
    };

    function sortColumn(tab, colIndex, type) {
        const tbody = document.getElementById(`${tab}TableBody`);
        const rowsArray = Array.from(tbody.querySelectorAll(`tr.inquiry-row[data-tab-type="${tab}"]`));
        if (rowsArray.length <= 1) return;

        let state = sortState[tab];
        if (state.col === colIndex) {
            state.asc = !state.asc;
        } else {
            state.col = colIndex;
            state.asc = true;
        }

        rowsArray.sort((a, b) => {
            let aText = a.cells[colIndex].textContent.trim();
            let bText = b.cells[colIndex].textContent.trim();

            if (type === 'number') {
                aText = parseFloat(aText.replace(/[^0-9.-]+/g, '')) || 0;
                bText = parseFloat(bText.replace(/[^0-9.-]+/g, '')) || 0;
                return state.asc ? aText - bText : bText - aText;
            } else if (type === 'date') {
                let aDate = new Date(aText);
                let bDate = new Date(bText);
                return state.asc ? aDate - bDate : bDate - aDate;
            } else {
                aText = aText.toLowerCase();
                bText = bText.toLowerCase();
                if (aText < bText) return state.asc ? -1 : 1;
                if (aText > bText) return state.asc ? 1 : -1;
                return 0;
            }
        });

        rowsArray.forEach(row => tbody.appendChild(row));
        
        // Refresh page view and sequential numbering post-sort
        updatePagination(tab);
    }

    // CSV Export Redirect Handler
    function exportCSV() {
        window.location.href = `inquiries.php?action=export&type=${activeTab}`;
    }

    // Row Delete Alert Handler
    function confirmDelete(id, name) {
        if (confirm(`Are you absolutely sure you want to permanently delete the inquiry record for "${name}"?\nThis action cannot be undone.`)) {
            window.location.href = `inquiries.php?action=delete&id=${id}`;
        }
    }

    // Select All Checkbox Handler
    function toggleSelectAll(tab) {
        const selectAllCb = document.getElementById(tab === 'catalogue' ? 'selectAllCatalogue' : 'selectAllContact');
        const checkboxes = document.querySelectorAll(`.${tab}-select-checkbox`);
        
        checkboxes.forEach(cb => {
            if (cb.closest('tr').style.display !== 'none') {
                cb.checked = selectAllCb.checked;
            }
        });

        updateBulkDeleteState(tab);
    }

    // Bulk Delete State Manager
    function updateBulkDeleteState(tab) {
        const checkboxes = document.querySelectorAll(`.${activeTab}-select-checkbox:checked`);
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCount = document.getElementById('selectedCount');

        selectedCount.textContent = checkboxes.length;

        if (checkboxes.length > 0) {
            bulkDeleteBtn.classList.remove('hidden');
        } else {
            bulkDeleteBtn.classList.add('hidden');
            const selectAllCb = document.getElementById(activeTab === 'catalogue' ? 'selectAllCatalogue' : 'selectAllContact');
            if (selectAllCb) selectAllCb.checked = false;
        }
    }

    // Submit Bulk Delete Action
    function submitBulkDelete() {
        const checkboxes = document.querySelectorAll(`.${activeTab}-select-checkbox:checked`);
        const ids = Array.from(checkboxes).map(cb => cb.value);

        if (ids.length > 0) {
            if (confirm(`Are you absolutely sure you want to permanently delete all ${ids.length} selected inquiry records?\nThis action will remove them permanently from the database.`)) {
                window.location.href = `inquiries.php?action=bulk_delete&ids=${ids.join(',')}`;
            }
        }
    }

    // ── HIGH-FIDELITY DETAILS POPUP CONTROLLER ───────────────────────────────
    function viewInquiry(data) {
        const modal = document.getElementById('inquiry-details-modal');
        const container = document.getElementById('modal-container-body');
        
        // Populate modal data elements
        document.getElementById('detail-id-label').textContent = `Inquiry Reference ID: #${data.id}`;
        document.getElementById('detail-name').textContent = `${data.first_name} ${data.last_name || ''}`;
        document.getElementById('detail-email').textContent = data.email;
        document.getElementById('detail-email').href = `mailto:${data.email}`;
        
        // Format phone digits logically (e.g. 98765 43210)
        const formattedPhone = data.phone.replace(/(\d{5})(\d{5})/, '$1 $2');
        document.getElementById('detail-phone').textContent = formattedPhone;
        
        // Format submission timestamp
        const dateObj = new Date(data.created_at);
        const options = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
        document.getElementById('detail-date').textContent = dateObj.toLocaleString('en-US', options);
        
        // Target dynamic HTML elements based on inquiry type ('catalogue' vs 'contact')
        const typeIcon = document.getElementById('detail-type-icon');
        const title = document.getElementById('detail-title');
        const msgLabel = document.getElementById('detail-message-label');
        const msgBox = document.getElementById('detail-message');
        const badgeDot = document.getElementById('detail-badge-dot');
        const badgeText = document.getElementById('detail-badge-text');
        const actionBtn = document.getElementById('detail-action-btn');
        
        if (data.type === 'catalogue') {
            typeIcon.textContent = 'menu_book';
            title.textContent = 'CATALOGUE DOWNLOAD REQUEST';
            msgLabel.textContent = 'Catalogue Document Requested';
            
            const filename = data.message.split('/').pop();
            
            // Remove border, background, and scrolling constraints for a clean one-line layout
            msgBox.className = "text-xs font-bold text-primary flex items-center gap-2 py-1 px-1";
            msgBox.innerHTML = `
                <span class="material-symbols-outlined text-error text-[20px] shrink-0">picture_as_pdf</span>
                <span class="truncate" title="${filename}">${filename}</span>
            `;
            
            badgeDot.className = 'w-1.5 h-1.5 rounded-full bg-blue-500 animate-ping';
            badgeText.textContent = 'DOWNLOADED';
            
            // Allow admin to view or download the flyer brochure instantly
            actionBtn.href = `${FRONTEND_LIVE_URL}${data.message}`;
            actionBtn.target = '_blank';
            actionBtn.innerHTML = '<span class="material-symbols-outlined text-[18px]">cloud_download</span><span>VIEW PDF</span>';
            actionBtn.className = 'bg-primary text-on-primary hover:bg-primary/95 flex items-center gap-1.5 px-5 py-2.5 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-primary/10 transition-all';
        } else {
            typeIcon.textContent = 'chat_bubble';
            title.textContent = 'CUSTOMER CONTACT INQUIRY';
            msgLabel.textContent = 'Submitted Message';
            
            // Restore full message text styling for contact inquiries
            msgBox.className = "text-xs text-on-surface-variant font-medium bg-white/60 p-4 rounded-xl border border-outline-variant/10 shadow-inner max-h-48 overflow-y-auto whitespace-pre-wrap leading-relaxed";
            msgBox.textContent = data.message;
            
            badgeDot.className = 'w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse';
            badgeText.textContent = 'MESSAGE RECEIVED';
            
            // Enable quick response mail composition
            actionBtn.href = `mailto:${data.email}?subject=FRIO Inquiry Reply Regarding Your Message`;
            actionBtn.removeAttribute('target');
            actionBtn.innerHTML = '<span class="material-symbols-outlined text-[18px]">mail</span><span>REPLY EMAIL</span>';
            actionBtn.className = 'bg-secondary text-white hover:bg-secondary/95 flex items-center gap-1.5 px-5 py-2.5 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-secondary/10 transition-all';
        }
        
        // Open modal frame with smooth scale and opacity transition
        modal.classList.remove('hidden');
        setTimeout(() => {
            container.classList.remove('scale-95', 'opacity-0');
            container.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
    
    function closeInquiryDetails() {
        const modal = document.getElementById('inquiry-details-modal');
        const container = document.getElementById('modal-container-body');
        
        container.classList.remove('scale-100', 'opacity-100');
        container.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Attach row click listeners for premium interactive navigation
    document.addEventListener('DOMContentLoaded', () => {
        const savedSearch = sessionStorage.getItem('inquiries_search_val');
        if (savedSearch && searchInput) {
            searchInput.value = savedSearch;
        }
        
        // Initial pagination draw
        updatePagination('catalogue');
        updatePagination('contact');

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                sessionStorage.setItem('inquiries_search_val', searchInput.value);
            });
        }

        // Row clicks handler
        document.querySelectorAll('.inquiry-row').forEach(row => {
            row.addEventListener('click', (e) => {
                // Ignore clicks on checkboxes, links, or action buttons
                if (
                    e.target.closest('.checkbox-cell') || 
                    e.target.closest('.action-cell') || 
                    e.target.closest('a') || 
                    e.target.closest('input') || 
                    e.target.closest('button')
                ) {
                    return;
                }
                const data = JSON.parse(row.getAttribute('data-inquiry'));
                viewInquiry(data);
            });
        });

        // Handle deep-linking via ref_id query parameter
        const urlParams = new URLSearchParams(window.location.search);
        const refId = urlParams.get('ref_id');
        if (refId) {
            document.querySelectorAll('.inquiry-row').forEach(row => {
                const data = JSON.parse(row.getAttribute('data-inquiry'));
                if (data && String(data.id) === String(refId)) {
                    // Switch tab if needed
                    if (data.type === 'contact') {
                        switchTab('contact');
                    } else {
                        switchTab('catalogue');
                    }
                    // Wait a tiny bit for tab switch and rendering
                    setTimeout(() => {
                        viewInquiry(data);
                    }, 50);
                }
            });
        }
    });
</script>
</body>
</html>
