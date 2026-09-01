<?php
require_once 'config.php';
$settings   = api_fetch('settings.php') ?? [];
$categories = api_fetch('categories.php') ?? [];
$page_title  = 'Product Categories | FRIO Industrial';
$meta_desc   = 'Browse FRIO precision brass fitting product categories — Flare Nuts, Fittings, Valves, and more.';
$active_page = 'category';
include 'includes/header.php';
?>
</div> <!-- Close global wrapper for full-bleed hero banner -->

<!-- Hero Banner -->
<section class="relative overflow-hidden py-16 md:py-24 bg-gradient-to-b from-[#071322] to-[#0b192c] flex items-center justify-center border-b border-white/5">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(254,214,91,0.05),transparent_40%)] pointer-events-none"></div>
    <div class="text-center px-8 relative z-10">
        <nav class="flex items-center gap-2 text-sm text-white/80 mb-5 justify-center">
            <a href="index.php" class="hover:text-white transition-colors font-medium">Home</a>
            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            <span class="text-[#ffe088] font-bold">Category</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 font-headline-lg tracking-tight">Product Categories</h1>
        <p class="text-white/85 text-sm md:text-base max-w-2xl leading-relaxed font-medium">Explore our complete range of precision brass fittings organized by category</p>
    </div>
</section>

<!-- Reopen centered wrapper for page content -->
<div class="max-w-[1700px] mx-auto w-full flex-1 flex flex-col relative">

<!-- Category Grid -->
<main class="w-full px-4 md:px-8 py-14">
    <?php if (!empty($categories)): ?>
        <div class="flex flex-wrap justify-center gap-8 w-full">
            <?php foreach ($categories as $cat): ?>
            <a href="product.php?category_id=<?php echo $cat['id']; ?>"
               class="group relative overflow-hidden rounded-2xl h-[320px] industrial-shadow block card-lift w-full sm:w-[calc(50%_-_16px)] lg:w-[calc(33.333%_-_22px)]">
                <!-- Background Image -->
                <?php 
                $img_value = $cat['image'] ?? '';
                $images = json_decode($img_value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($images) && !empty($images)):
                    foreach ($images as $idx => $img): 
                        $hidden = ($idx > 0) ? 'opacity-0' : 'opacity-100';
                ?>
                    <img loading="lazy" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 <?php echo $hidden; ?> cat-slideshow-img" data-index="<?php echo $idx; ?>"
                         src="<?php echo asset_url($img); ?>"
                         alt="<?php echo htmlspecialchars($cat['name']); ?>" />
                <?php 
                    endforeach;
                elseif (!empty($img_value)): 
                ?>
                <img loading="lazy" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                     src="<?php echo asset_url($img_value); ?>"
                     alt="<?php echo htmlspecialchars($cat['name']); ?>" />
                <?php else: ?>
                <div class="absolute inset-0 bg-surface-container-highest flex items-center justify-center">
                    <span class="material-symbols-outlined text-7xl text-outline/20">inventory_2</span>
                </div>
                <?php endif; ?>

                <!-- Gradient overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-primary/95 via-primary/40 to-transparent"></div>

                <!-- Hover highlight ring -->
                <div class="absolute inset-0 border-2 border-secondary-fixed/0 group-hover:border-secondary-fixed/60 rounded-2xl transition-all duration-300"></div>

                <!-- Content -->
                <div class="absolute bottom-0 left-0 right-0 p-7">
                    <span class="text-[9px] text-secondary font-bold uppercase tracking-[0.2em] block mb-2">Category</span>
                    <h2 class="text-xl font-bold text-white mb-2"><?php echo htmlspecialchars($cat['name']); ?></h2>
                    <?php if (!empty($cat['description'])): ?>
                    <p class="text-white/70 text-sm line-clamp-2 mb-4"><?php echo htmlspecialchars($cat['description']); ?></p>
                    <?php endif; ?>
                    <span class="inline-flex items-center gap-2 text-secondary-fixed font-bold text-sm group-hover:gap-3 transition-all">
                        View Products
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <span class="material-symbols-outlined text-7xl text-outline/20 mb-5">category</span>
            <h2 class="text-2xl font-bold text-on-surface-variant mb-2">No Categories Yet</h2>
            <p class="text-sm text-outline mb-6">Categories will appear here once added from the admin panel.</p>
            <a href="contact.php" class="bg-primary text-white px-6 py-3 rounded-lg font-bold text-sm hover:bg-primary-container transition-all">Contact Us</a>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
