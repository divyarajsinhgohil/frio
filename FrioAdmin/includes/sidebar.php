<?php
/**
 * FRIO Admin Console - Shared Sidebar Component
 * Dynamically highlights active navigation links.
 */
$current_page = basename($_SERVER['PHP_SELF']);

// Dashboard active logic
$is_dashboard = ($current_page === 'dashbord.php');
$dashboard_class = $is_dashboard
    ? 'bg-secondary-container text-on-secondary-container rounded-xl mx-2 px-4 py-3 font-bold transition-all duration-300 flex items-center gap-3 shadow-md'
    : 'text-white/70 hover:bg-white/10 hover:text-white px-4 py-3 mx-2 transition-all duration-300 flex items-center gap-3 rounded-xl';

// Active navigation logic based on directory subfolders
$uri = $_SERVER['PHP_SELF'];
$is_category = (strpos($uri, '/category/') !== false);
$is_product = (strpos($uri, '/product/') !== false);
$is_banner = (strpos($uri, '/banner/') !== false);
$is_catalogue = (strpos($uri, '/catalogue/') !== false);
$is_inquiries = ($current_page === 'inquiries.php');
$is_settings = ($current_page === 'settings.php');
$is_backup = ($current_page === 'backup.php');
$is_admin_users = ($current_page === 'admin_users.php');

$is_config_active = ($is_settings || $is_admin_users || $is_backup);

$category_class = $is_category
    ? 'bg-white/10 text-secondary-fixed rounded-xl mx-2 px-4 py-3 font-bold border-r-4 border-secondary-fixed transition-all duration-300 flex items-center gap-3 shadow-lg scale-102'
    : 'text-white/70 hover:bg-white/10 hover:text-white px-4 py-3 mx-2 transition-all duration-300 flex items-center gap-3 rounded-xl';

$product_class = $is_product
    ? 'bg-white/10 text-secondary-fixed rounded-xl mx-2 px-4 py-3 font-bold border-r-4 border-secondary-fixed transition-all duration-300 flex items-center gap-3 shadow-lg scale-102'
    : 'text-white/70 hover:bg-white/10 hover:text-white px-4 py-3 mx-2 transition-all duration-300 flex items-center gap-3 rounded-xl';

$banner_class = $is_banner
    ? 'bg-white/10 text-secondary-fixed rounded-xl mx-2 px-4 py-3 font-bold border-r-4 border-secondary-fixed transition-all duration-300 flex items-center gap-3 shadow-lg scale-102'
    : 'text-white/70 hover:bg-white/10 hover:text-white px-4 py-3 mx-2 transition-all duration-300 flex items-center gap-3 rounded-xl';

$catalogue_class = $is_catalogue
    ? 'bg-white/10 text-secondary-fixed rounded-xl mx-2 px-4 py-3 font-bold border-r-4 border-secondary-fixed transition-all duration-300 flex items-center gap-3 shadow-lg scale-102'
    : 'text-white/70 hover:bg-white/10 hover:text-white px-4 py-3 mx-2 transition-all duration-300 flex items-center gap-3 rounded-xl';

$inquiries_class = $is_inquiries
    ? 'bg-white/10 text-secondary-fixed rounded-xl mx-2 px-4 py-3 font-bold border-r-4 border-secondary-fixed transition-all duration-300 flex items-center gap-3 shadow-lg scale-102'
    : 'text-white/70 hover:bg-white/10 hover:text-white px-4 py-3 mx-2 transition-all duration-300 flex items-center gap-3 rounded-xl';

$settings_class = $is_settings
    ? 'bg-white/10 text-secondary-fixed rounded-xl mx-2 px-4 py-3 font-bold border-r-4 border-secondary-fixed transition-all duration-300 flex items-center gap-3 shadow-lg scale-102'
    : 'text-white/70 hover:bg-white/10 hover:text-white px-4 py-3 mx-2 transition-all duration-300 flex items-center gap-3 rounded-xl';

$backup_class = $is_backup
    ? 'bg-white/10 text-secondary-fixed rounded-xl mx-2 px-4 py-3 font-bold border-r-4 border-secondary-fixed transition-all duration-300 flex items-center gap-3 shadow-lg scale-102'
    : 'text-white/70 hover:bg-white/10 hover:text-white px-4 py-3 mx-2 transition-all duration-300 flex items-center gap-3 rounded-xl';

$admin_users_class = $is_admin_users
    ? 'bg-white/10 text-secondary-fixed rounded-xl mx-2 px-4 py-3 font-bold border-r-4 border-secondary-fixed transition-all duration-300 flex items-center gap-3 shadow-lg scale-102'
    : 'text-white/70 hover:bg-white/10 hover:text-white px-4 py-3 mx-2 transition-all duration-300 flex items-center gap-3 rounded-xl';
?>
<aside class="h-screen w-64 fixed left-0 top-0 bg-primary dark:bg-primary-container flex flex-col py-unit shadow-xl z-50">
    <div class="px-6 py-6 flex flex-col items-start mb-4">
        <img src="<?php echo isset($base_path) ? $base_path : ''; ?>assets/imag/frio-logo-white.png" alt="Frio Logo" class="h-10 w-auto object-contain" />
    </div>
    
    <nav class="flex-grow space-y-1">
        <!-- Dashboard Link -->
        <a class="<?php echo $dashboard_class; ?>" href="<?php echo isset($base_path) ? $base_path : ''; ?>dashbord.php">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo $is_dashboard ? '1' : '0'; ?>;">dashboard</span>
            <span class="text-label-bold font-label-bold">Dashboard</span>
        </a>
        
        <!-- Category Link -->
        <a class="<?php echo $category_class; ?>" href="<?php echo isset($base_path) ? $base_path : ''; ?>category/list.php">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo $is_category ? '1' : '0'; ?>;">inventory_2</span>
            <span class="text-label-bold font-label-bold">Category</span>
        </a>

        <!-- Product Link -->
        <a class="<?php echo $product_class; ?>" href="<?php echo isset($base_path) ? $base_path : ''; ?>product/list.php">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo $is_product ? '1' : '0'; ?>;">widgets</span>
            <span class="text-label-bold font-label-bold">Product</span>
        </a>

        <!-- Banner Slider Link -->
        <a class="<?php echo $banner_class; ?>" href="<?php echo isset($base_path) ? $base_path : ''; ?>banner/list.php">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo $is_banner ? '1' : '0'; ?>;">view_carousel</span>
            <span class="text-label-bold font-label-bold">Banner Slider</span>
        </a>

        <!-- Catalogue Link -->
        <a class="<?php echo $catalogue_class; ?>" href="<?php echo isset($base_path) ? $base_path : ''; ?>catalogue/list.php">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo $is_catalogue ? '1' : '0'; ?>;">menu_book</span>
            <span class="text-label-bold font-label-bold">Catalogue</span>
        </a>

        <!-- Inquiries Link -->
        <a class="<?php echo $inquiries_class; ?>" href="<?php echo isset($base_path) ? $base_path : ''; ?>inquiries.php">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' <?php echo $is_inquiries ? '1' : '0'; ?>;">mail</span>
            <span class="text-label-bold font-label-bold">Customer Inquiries</span>
        </a>

        <!-- Collapsible Configuration Parent -->
        <div class="px-2 mt-4">
            <button type="button" id="config-dropdown-btn" class="w-full text-white/70 hover:bg-white/10 hover:text-white px-4 py-3 transition-all duration-300 flex items-center gap-3 rounded-xl select-none text-left">
                <span class="material-symbols-outlined">settings</span>
                <span class="text-label-bold font-label-bold flex-grow">Configuration</span>
                <span class="material-symbols-outlined transition-transform duration-300 <?php echo $is_config_active ? 'rotate-180' : ''; ?>" id="config-chevron">expand_more</span>
            </button>
            
            <!-- Collapsible Sub-menu -->
            <div id="config-submenu" class="transition-all duration-300 overflow-hidden <?php echo $is_config_active ? 'max-h-60 mt-1 opacity-100' : 'max-h-0 opacity-0'; ?> pl-2 space-y-1">
                <!-- Site Settings -->
                <a class="<?php echo $settings_class; ?> pl-6 py-2 flex items-center gap-2" href="<?php echo isset($base_path) ? $base_path : ''; ?>settings.php">
                    <span class="material-symbols-outlined text-[18px]">display_settings</span>
                    <span class="text-xs font-semibold">Site Settings</span>
                </a>
                
                <!-- Admin Users -->
                <a class="<?php echo $admin_users_class; ?> pl-6 py-2 flex items-center gap-2" href="<?php echo isset($base_path) ? $base_path : ''; ?>admin_users.php">
                    <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                    <span class="text-xs font-semibold">Admin Users</span>
                </a>

                <!-- Backup & Restore -->
                <a class="<?php echo $backup_class; ?> pl-6 py-2 flex items-center gap-2" href="<?php echo isset($base_path) ? $base_path : ''; ?>backup.php">
                    <span class="material-symbols-outlined text-[18px]">settings_backup_restore</span>
                    <span class="text-xs font-semibold">Backup & Restore</span>
                </a>
            </div>
        </div>
    </nav>
    
    <div class="px-2 mt-auto">
        <div class="border-t border-white/10 pt-4 pb-2 space-y-1">
            <!-- Logout Link -->
            <a class="text-white/70 hover:bg-white/10 hover:text-white px-4 py-2 flex items-center gap-3 transition-all rounded-xl" href="<?php echo isset($base_path) ? $base_path : ''; ?>logout.php">
                <span class="material-symbols-outlined">logout</span>
                <span class="text-label-bold font-label-bold">Logout</span>
            </a>
        </div>
    </div>
</aside>

<!-- Sub-menu click micro-interaction toggle script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('config-dropdown-btn');
    const submenu = document.getElementById('config-submenu');
    const chevron = document.getElementById('config-chevron');
    if (btn && submenu && chevron) {
        btn.addEventListener('click', () => {
            const isCollapsed = submenu.classList.contains('max-h-0');
            if (isCollapsed) {
                submenu.classList.remove('max-h-0', 'opacity-0');
                submenu.classList.add('max-h-60', 'opacity-100', 'mt-1');
                chevron.classList.add('rotate-180');
            } else {
                submenu.classList.remove('max-h-60', 'opacity-100', 'mt-1');
                submenu.classList.add('max-h-0', 'opacity-0');
                chevron.classList.remove('rotate-180');
            }
        });
    }
});
</script>
