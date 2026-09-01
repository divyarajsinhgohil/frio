<?php
/**
 * FRIO Admin Console - Dashboard Overhaul
 * Transforms the welcome page into a premium, interactive Bento Command Center.
 * Pulls real-time statistics from tables, renders time-aware greetings, and showcases live active configurations.
 */
require_once 'auth_check.php';
require_once 'db_connect.php';

// 1. Retrieve Dynamic Telemetry Counts
$cat_count = 0;
$prod_count = 0;
$banner_count = 0;
$catalogue_count = 0;

try {
    $cat_count       = $pdo->query("SELECT COUNT(*) FROM `category`")->fetchColumn();
    $prod_count      = $pdo->query("SELECT COUNT(*) FROM `product`")->fetchColumn();
    $banner_count    = $pdo->query("SELECT COUNT(*) FROM `banner_slider`")->fetchColumn();
    $catalogue_count = $pdo->query("SELECT COUNT(*) FROM `catalogue`")->fetchColumn();
} catch (PDOException $e) {
    // Graceful fallback to zero if database is adjusting
}

// 2. Fetch Active Storefront Settings (id = 1)
$settings = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM `settings` WHERE `id` = 1");
    $stmt->execute();
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback if settings table hasn't migrated
}

// Default values if settings row hasn't initialized
if (!$settings) {
    $settings = [
        'logo' => 'assets/imag/frio-logo-white.png',
        'address' => 'Frio India Industrial Park, Delhi, India',
        'email' => 'info@frio.co',
        'phone' => '+91 98765 43210',
        'facebook' => '',
        'instagram' => '',
        'linkedin' => '',
        'twitter' => '',
        'youtube' => ''
    ];
}

// 4. Fetch 5 Recent Inquiries (Catalogue Downloads & Messages)
$recent_inquiries = [];
try {
    $stmt_rec = $pdo->query("SELECT * FROM `inquiries` ORDER BY `id` DESC LIMIT 5");
    $recent_inquiries = $stmt_rec->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback if inquiries table hasn't migrated
}

// 3. Calculate Server-Time Aware Greeting
$hour = date('H');
$greeting = "Welcome back";
$greeting_icon = "waving_hand";
if ($hour < 12) {
    $greeting = "Good Morning";
    $greeting_icon = "light_mode";
} elseif ($hour < 18) {
    $greeting = "Good Afternoon";
    $greeting_icon = "wb_sunny";
} else {
    $greeting = "Good Evening";
    $greeting_icon = "dark_mode";
}

$page_title = "FRIO Admin Console | Safety By Choice";
include 'includes/head.php';
?>
<body class="bg-background text-on-background min-h-screen flex overflow-hidden">
<?php include 'includes/sidebar.php'; ?>

<!-- Main Content Area -->
<main class="ml-64 flex-grow h-screen overflow-y-auto flex flex-col justify-between">
<?php 
$header_title = 'Dashboard';
include 'includes/header.php'; 
?>

<!-- Content Area Section -->
<section class="mt-24 p-gutter flex-grow">
    
    <!-- Time-Aware Greeting Bento Canvas -->
    <div class="mb-6 bg-gradient-to-r from-primary via-[#0c4b86] to-[#001c39] text-white p-8 rounded-[2.5rem] border border-white/10 shadow-lg relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <!-- Decoration background glow rings -->
        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute left-1/3 -top-20 w-48 h-48 bg-secondary-container/5 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="flex items-center gap-4 relative z-10">
            <span class="material-symbols-outlined text-[48px] text-secondary animate-pulse"><?php echo $greeting_icon; ?></span>
            <div>
                <h2 class="text-headline-lg font-headline-lg leading-tight"><?php echo $greeting; ?>, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
                <p class="text-xs text-white/70 font-semibold uppercase tracking-wider mt-1">Super Administrative Authority Active</p>
            </div>
        </div>

        <div class="flex items-center gap-3 relative z-10">
            <div class="text-right hidden lg:block">
                <p class="text-[11px] text-white/55 font-bold uppercase tracking-widest leading-none">Console Integrity</p>
                <p class="text-xs text-secondary font-bold leading-normal">Precision Safety By Choice</p>
            </div>
            <div class="h-10 w-px bg-white/10 hidden lg:block"></div>
            <a href="settings.php" class="bg-secondary-container text-on-secondary-fixed hover:bg-secondary hover:text-white px-5 py-2.5 rounded-full font-bold transition-all duration-300 shadow-md text-xs flex items-center gap-1.5 hover:scale-105 active-glow">
                <span class="material-symbols-outlined text-[16px]">tune</span>
                Configure System
            </a>
        </div>
    </div>

    <!-- Live Telemetry Counts (4-Column Bento Grid Row) -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-gutter mb-6">
        
        <!-- Category Stats Card -->
        <a href="category/list.php" class="glass-card p-5 rounded-[2rem] border border-white/20 shadow-md hover:scale-[1.03] transition-all duration-300 flex items-center justify-between group">
            <div class="flex items-center gap-4">
                <span class="material-symbols-outlined text-3xl text-primary bg-primary-fixed/30 p-3 rounded-2xl group-hover:rotate-6 transition-transform">inventory_2</span>
                <div>
                    <h4 class="text-headline-md font-extrabold text-primary leading-none"><?php echo $cat_count; ?></h4>
                    <p class="text-[11px] text-outline font-bold uppercase tracking-wider mt-1">Categories</p>
                </div>
            </div>
            <span class="material-symbols-outlined text-outline group-hover:text-primary group-hover:translate-x-1 transition-all">chevron_right</span>
        </a>

        <!-- Product Stats Card -->
        <a href="product/list.php" class="glass-card p-5 rounded-[2rem] border border-white/20 shadow-md hover:scale-[1.03] transition-all duration-300 flex items-center justify-between group">
            <div class="flex items-center gap-4">
                <span class="material-symbols-outlined text-3xl text-[#735c00] bg-secondary-fixed/50 p-3 rounded-2xl group-hover:rotate-6 transition-transform">widgets</span>
                <div>
                    <h4 class="text-headline-md font-extrabold text-primary leading-none"><?php echo $prod_count; ?></h4>
                    <p class="text-[11px] text-outline font-bold uppercase tracking-wider mt-1">Products</p>
                </div>
            </div>
            <span class="material-symbols-outlined text-outline group-hover:text-primary group-hover:translate-x-1 transition-all">chevron_right</span>
        </a>

        <!-- Banners Stats Card -->
        <a href="banner/list.php" class="glass-card p-5 rounded-[2rem] border border-white/20 shadow-md hover:scale-[1.03] transition-all duration-300 flex items-center justify-between group">
            <div class="flex items-center gap-4">
                <span class="material-symbols-outlined text-3xl text-purple-700 bg-purple-100 dark:bg-purple-950/30 p-3 rounded-2xl group-hover:rotate-6 transition-transform">view_carousel</span>
                <div>
                    <h4 class="text-headline-md font-extrabold text-primary leading-none"><?php echo $banner_count; ?></h4>
                    <p class="text-[11px] text-outline font-bold uppercase tracking-wider mt-1">Active Banners</p>
                </div>
            </div>
            <span class="material-symbols-outlined text-outline group-hover:text-primary group-hover:translate-x-1 transition-all">chevron_right</span>
        </a>

        <!-- Catalogue Stats Card -->
        <a href="catalogue/list.php" class="glass-card p-5 rounded-[2rem] border border-white/20 shadow-md hover:scale-[1.03] transition-all duration-300 flex items-center justify-between group">
            <div class="flex items-center gap-4">
                <span class="material-symbols-outlined text-3xl text-emerald-700 bg-emerald-100 dark:bg-emerald-950/30 p-3 rounded-2xl group-hover:rotate-6 transition-transform">menu_book</span>
                <div>
                    <h4 class="text-headline-md font-extrabold text-primary leading-none"><?php echo $catalogue_count; ?></h4>
                    <p class="text-[11px] text-outline font-bold uppercase tracking-wider mt-1">Catalogues</p>
                </div>
            </div>
            <span class="material-symbols-outlined text-outline group-hover:text-primary group-hover:translate-x-1 transition-all">chevron_right</span>
        </a>

    </div>

    <!-- Multi-Column Bento Workspace -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-gutter items-start">
        
        <!-- Launchpad Quick Shortcuts (xl:col-span-7) -->
        <div class="xl:col-span-7 space-y-6">
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md">
                <div class="flex items-center gap-3 mb-6 pb-2 border-b border-outline-variant/10">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">rocket_launch</span>
                    <h3 class="text-headline-md font-headline-md text-primary">Console Launchpad</h3>
                </div>

                <!-- 6-Shortcuts Bento Hub Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <!-- Add Category -->
                    <a href="category/add.php" class="flex items-center gap-4 p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 shadow-sm hover:bg-primary-fixed/5 transition-all duration-300 hover:scale-[1.02] group">
                        <span class="material-symbols-outlined text-2xl text-primary bg-primary/10 p-2.5 rounded-xl group-hover:scale-110 transition-transform">create_new_folder</span>
                        <div>
                            <p class="text-label-bold font-bold text-on-surface">Add Category</p>
                            <p class="text-[10px] text-outline">Publish storefront catalog sections</p>
                        </div>
                    </a>

                    <!-- Add Product -->
                    <a href="product/add.php" class="flex items-center gap-4 p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 shadow-sm hover:bg-primary-fixed/5 transition-all duration-300 hover:scale-[1.02] group">
                        <span class="material-symbols-outlined text-2xl text-primary bg-primary/10 p-2.5 rounded-xl group-hover:scale-110 transition-transform">add_box</span>
                        <div>
                            <p class="text-label-bold font-bold text-on-surface">Add Product</p>
                            <p class="text-[10px] text-outline">List new heavy engineering components</p>
                        </div>
                    </a>

                    <!-- Manage Banners -->
                    <a href="banner/add.php" class="flex items-center gap-4 p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 shadow-sm hover:bg-primary-fixed/5 transition-all duration-300 hover:scale-[1.02] group">
                        <span class="material-symbols-outlined text-2xl text-primary bg-primary/10 p-2.5 rounded-xl group-hover:scale-110 transition-transform">add_to_photos</span>
                        <div>
                            <p class="text-label-bold font-bold text-on-surface">Manage Banners</p>
                            <p class="text-[10px] text-outline">Update slider slideshow banners</p>
                        </div>
                    </a>

                    <!-- Upload Catalogue -->
                    <a href="catalogue/add.php" class="flex items-center gap-4 p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 shadow-sm hover:bg-primary-fixed/5 transition-all duration-300 hover:scale-[1.02] group">
                        <span class="material-symbols-outlined text-2xl text-primary bg-primary/10 p-2.5 rounded-xl group-hover:scale-110 transition-transform">picture_as_pdf</span>
                        <div>
                            <p class="text-label-bold font-bold text-on-surface">Upload Brochure</p>
                            <p class="text-[10px] text-outline">Publish PDF product brochures</p>
                        </div>
                    </a>

                    <!-- Edit Site Settings -->
                    <a href="settings.php" class="flex items-center gap-4 p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 shadow-sm hover:bg-primary-fixed/5 transition-all duration-300 hover:scale-[1.02] group">
                        <span class="material-symbols-outlined text-2xl text-primary bg-primary/10 p-2.5 rounded-xl group-hover:scale-110 transition-transform">display_settings</span>
                        <div>
                            <p class="text-label-bold font-bold text-on-surface">Site Parameters</p>
                            <p class="text-[10px] text-outline">Configure hotline, logos, and links</p>
                        </div>
                    </a>

                    <!-- System Backup ZIP -->
                    <a href="backup.php" class="flex items-center gap-4 p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 shadow-sm hover:bg-primary-fixed/5 transition-all duration-300 hover:scale-[1.02] group">
                        <span class="material-symbols-outlined text-2xl text-primary bg-primary/10 p-2.5 rounded-xl group-hover:scale-110 transition-transform">cloud_download</span>
                        <div>
                            <p class="text-label-bold font-bold text-on-surface">System Backups</p>
                            <p class="text-[10px] text-outline">Download database & files backup ZIP</p>
                        </div>
                    </a>

                </div>
            </div>
        </div>

        <!-- Storefront Active Parameters Bento (xl:col-span-5) -->
        <div class="xl:col-span-5 space-y-6">
            <!-- Recent Inquiries Bento Block -->
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md">
                <div class="flex items-center justify-between mb-6 pb-2 border-b border-outline-variant/10">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">inbox</span>
                        <h3 class="text-headline-md font-headline-md text-primary">Recent Inquiries</h3>
                    </div>
                    <a href="inquiries.php" class="text-xs text-primary font-bold hover:underline">View All</a>
                </div>

                <div class="space-y-3.5">
                    <?php if (empty($recent_inquiries)): ?>
                        <div class="text-center py-8 text-outline text-xs italic">
                            No recent inquiries received yet.
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_inquiries as $inq): 
                            $is_cat = ($inq['type'] === 'catalogue');
                            $type_icon = $is_cat ? 'menu_book' : 'chat_bubble';
                            $icon_color = $is_cat ? 'text-blue-600 bg-blue-50 border-blue-100' : 'text-emerald-600 bg-emerald-50 border-emerald-100';
                            $title_text = htmlspecialchars($inq['first_name'] . ' ' . ($inq['last_name'] ?? ''));
                            
                            // Truncate message snippet
                            $raw_msg = $inq['message'] ?? '';
                            if ($is_cat) {
                                $raw_msg = basename($raw_msg);
                            }
                            $msg_snippet = strlen($raw_msg) > 38 ? substr($raw_msg, 0, 35) . '...' : $raw_msg;
                            $msg_snippet = htmlspecialchars($msg_snippet);
                            
                            $formatted_date = date('d M, h:i A', strtotime($inq['created_at']));
                        ?>
                            <div class="flex items-center justify-between gap-3 p-3 bg-surface-container-low rounded-2xl border border-outline-variant/10 hover:bg-primary-fixed/5 transition-all cursor-pointer recent-inquiry-row hover:shadow-sm" data-inquiry="<?php echo htmlspecialchars(json_encode($inq), ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="material-symbols-outlined text-[16px] p-2.5 rounded-xl border shrink-0 <?php echo $icon_color; ?>"><?php echo $type_icon; ?></span>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-on-surface leading-tight truncate"><?php echo $title_text; ?></h4>
                                        <p class="text-[10px] text-outline font-medium mt-0.5 truncate" title="<?php echo htmlspecialchars($raw_msg); ?>"><?php echo $msg_snippet; ?></p>
                                    </div>
                                </div>
                                <span class="text-[9px] text-outline font-extrabold uppercase shrink-0"><?php echo $formatted_date; ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Storefront State Bento Block -->
            <div class="glass-card p-6 rounded-[2rem] border border-white/20 shadow-md">
                <div class="flex items-center gap-3 mb-6 pb-2 border-b border-outline-variant/10">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed/30 p-2 rounded-xl">storefront</span>
                    <h3 class="text-headline-md font-headline-md text-primary">Storefront State</h3>
                </div>

                <div class="space-y-4">
                    <!-- Brand Logo Thumbnail -->
                    <div class="flex items-center justify-between p-4 bg-primary rounded-2xl border border-white/10 shadow-sm overflow-hidden h-20">
                        <span class="text-[10px] text-white/55 font-bold uppercase tracking-wider leading-none">Active Logo</span>
                        <img src="<?php echo htmlspecialchars($settings['logo']); ?>" alt="Storefront Logo" class="max-h-12 max-w-[160px] object-contain filter drop-shadow-md" />
                    </div>

                    <!-- Contact Details list -->
                    <div class="space-y-3 bg-surface-container-low p-4 rounded-2xl border border-outline-variant/10 shadow-sm text-xs text-on-surface-variant">
                        <div class="flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-[16px] text-primary mt-0.5">location_on</span>
                            <span class="leading-normal font-medium"><?php echo htmlspecialchars($settings['address']); ?></span>
                        </div>
                        <div class="h-px bg-outline-variant/20"></div>
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[16px] text-primary">call</span>
                            <span class="font-bold text-primary"><?php echo htmlspecialchars($settings['phone']); ?></span>
                        </div>
                        <div class="h-px bg-outline-variant/20"></div>
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[16px] text-primary">mail</span>
                            <span class="font-medium"><?php echo htmlspecialchars($settings['email']); ?></span>
                        </div>
                    </div>

                    <!-- Active Social Media Links pillbox -->
                    <div class="p-4 bg-surface-container-low rounded-2xl border border-outline-variant/10 shadow-sm">
                        <p class="text-[10px] uppercase font-bold tracking-widest text-outline mb-2.5">Configured Networks</p>
                        <div class="flex flex-wrap gap-1.5">
                            <?php 
                            $socials = [
                                'facebook'  => ['name' => 'Facebook', 'color' => 'bg-blue-500/10 text-blue-600 border-blue-500/20'],
                                'instagram' => ['name' => 'Instagram', 'color' => 'bg-pink-500/10 text-pink-600 border-pink-500/20'],
                                'linkedin'  => ['name' => 'LinkedIn', 'color' => 'bg-sky-600/10 text-sky-700 border-sky-600/20'],
                                'twitter'   => ['name' => 'Twitter/X', 'color' => 'bg-black/10 text-black border-black/20'],
                                'youtube'   => ['name' => 'YouTube', 'color' => 'bg-red-500/10 text-red-600 border-red-500/20']
                            ];
                            $active_socials_count = 0;
                            foreach ($socials as $key => $style) {
                                if (!empty($settings[$key])) {
                                    $active_socials_count++;
                                    echo '<span class="px-2.5 py-1 text-[10px] font-bold border rounded-full ' . $style['color'] . '">' . $style['name'] . '</span>';
                                }
                            }
                            if ($active_socials_count === 0) {
                                echo '<span class="text-xs text-outline font-medium italic">No social media links active</span>';
                            }
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</section>

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
            <a href="#" id="detail-download-btn" class="bg-secondary text-white hover:bg-secondary/95 flex items-center gap-1.5 px-5 py-2.5 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-secondary/10 transition-all hidden" download>
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>DOWNLOAD PDF</span>
            </a>
            <a href="#" id="detail-action-btn" class="bg-primary text-on-primary hover:bg-primary/95 flex items-center gap-1.5 px-5 py-2.5 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-primary/10 transition-all">
                <span class="material-symbols-outlined text-[18px]">mail</span>
                <span>REPLY EMAIL</span>
            </a>
        </div>
    </div>
</div>

<script>
    const FRONTEND_LIVE_URL = '<?php echo FRONTEND_LIVE_URL; ?>';
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
        const downloadBtn = document.getElementById('detail-download-btn');
        
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
            actionBtn.innerHTML = '<span class="material-symbols-outlined text-[18px]">visibility</span><span>VIEW PDF</span>';
            actionBtn.className = 'bg-primary text-on-primary hover:bg-primary/95 flex items-center gap-1.5 px-5 py-2.5 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-primary/10 transition-all';
            
            // Setup download button
            downloadBtn.href = `${FRONTEND_LIVE_URL}${data.message}`;
            downloadBtn.classList.remove('hidden');
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
            
            // Hide download button
            downloadBtn.classList.add('hidden');
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

    // Attach row click listeners
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.recent-inquiry-row').forEach(row => {
            row.addEventListener('click', () => {
                const data = JSON.parse(row.getAttribute('data-inquiry'));
                viewInquiry(data);
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
</main>
</body></html>
