<?php
/**
 * FrioFront - Homepage
 * Fetches banners, categories, featured products, and catalogues from FrioAdmin API.
 */
require_once 'config.php';

$settings   = api_fetch('settings.php') ?? ['logo'=>'','address'=>'','email'=>'','phone'=>''];
$banners    = api_fetch('banners.php')    ?? [];
$categories = api_fetch('categories.php') ?? [];
$catalogues = api_fetch('catalogues.php') ?? [];
$products   = api_fetch('products.php')   ?? [];

$page_title  = 'FRIO | Precision Brass Fittings & Industrial Safety';
$meta_desc   = 'FRIO — Leading manufacturer of precision brass fittings and industrial safety products. Safety By Choice.';
$active_page = 'home';

include 'includes/header.php';
?>
</div> <!-- Close global wrapper for full-bleed banner slider -->

<!-- ===== HERO BANNER ===== -->
<?php if (!empty($banners)): ?>
<div class="w-full relative mt-16 md:mt-20" id="banner-slider">

    <!-- Slides -->
    <?php foreach ($banners as $i => $banner): ?>
    <div class="banner-slide w-full relative <?php echo $i > 0 ? 'hidden' : ''; ?>" data-index="<?php echo $i; ?>">
        <?php
            $has_link  = !empty($banner['button_link']);
            $has_text  = !empty($banner['name']) || !empty($banner['description']);
            
            // Text overlay content
            ob_start();
            if ($has_text || $has_link) {
                $align = $banner['text_align'] ?? 'center';
                if ($align === 'left') {
                    $align_class = 'items-start text-left pl-6 sm:pl-8 md:pl-16 lg:pl-24';
                } elseif ($align === 'right') {
                    $align_class = 'items-end text-right pr-6 sm:pr-8 md:pr-16 lg:pr-24';
                } else {
                    $align_class = 'items-center text-center px-6 sm:px-8';
                }
                
                echo '<div class="absolute inset-0 flex flex-col justify-center ' . $align_class . ' py-8 bg-black/25 sm:bg-black/20 pointer-events-none z-10">';
                if (!empty($banner['name'])) {
                    echo '<h2 class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-white mb-3 md:mb-5 drop-shadow-md max-w-4xl leading-[1.1] tracking-[-1.5px]">' . htmlspecialchars($banner['name']) . '</h2>';
                }
                if (!empty($banner['description'])) {
                    echo '<div class="text-white/90 text-xs sm:text-sm md:text-lg mb-6 max-w-2xl drop-shadow-sm [&_b]:font-bold [&_strong]:font-bold [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-1 [&_p]:mb-2">' . $banner['description'] . '</div>';
                }
                if ($has_link) {
                    echo '<a href="' . htmlspecialchars($banner['button_link']) . '" class="bg-primary text-on-primary px-6 py-2.5 md:px-8 md:py-3.5 rounded-xl font-bold text-xs md:text-sm shadow-lg pointer-events-auto hover:bg-primary/90 transition-all inline-block mt-2">Explore Now</a>';
                }
                echo '</div>';
            }
            $overlay = ob_get_clean();
        ?>
        <!-- Premium fully-responsive B2B banner container with exact aspect ratio mapping -->
        <div class="relative w-full overflow-hidden bg-gradient-to-b from-[#071322] to-[#0b192c]">
            <img
                loading="lazy"
                src="<?php echo asset_url($banner['image']); ?>"
                alt="<?php echo htmlspecialchars($banner['name'] ?? 'FRIO Banner'); ?>"
                class="w-full h-auto block"
            />
            <?php echo $overlay; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Dots — only show if more than 1 banner -->
    <?php if (count($banners) > 1): ?>
    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2 z-10">
        <?php foreach ($banners as $i => $banner): ?>
        <button
            onclick="goBannerSlide(<?php echo $i; ?>)"
            class="banner-dot w-2.5 h-2.5 rounded-full border-2 border-white transition-all <?php echo $i === 0 ? 'bg-white' : 'bg-white/30'; ?>"
            data-dot="<?php echo $i; ?>">
        </button>
        <?php endforeach; ?>
    </div>

    <!-- Prev / Next arrows -->
    <button onclick="moveBanner(-1)"
        class="absolute left-3 md:left-6 top-1/2 -translate-y-1/2 z-10 w-10 h-10 md:w-12 md:h-12 rounded-full bg-black/40 hover:bg-secondary-container border border-white/20 hover:border-secondary-fixed text-white hover:text-on-secondary-container flex items-center justify-center transition-all backdrop-blur-md shadow-lg duration-300 hover:scale-110 active:scale-95">
        <span class="material-symbols-outlined text-inherit text-[22px] md:text-[26px] font-bold">chevron_left</span>
    </button>
    <button onclick="moveBanner(1)"
        class="absolute right-3 md:right-6 top-1/2 -translate-y-1/2 z-10 w-10 h-10 md:w-12 md:h-12 rounded-full bg-black/40 hover:bg-secondary-container border border-white/20 hover:border-secondary-fixed text-white hover:text-on-secondary-container flex items-center justify-center transition-all backdrop-blur-md shadow-lg duration-300 hover:scale-110 active:scale-95">
        <span class="material-symbols-outlined text-inherit text-[22px] md:text-[26px] font-bold">chevron_right</span>
    </button>
    <?php endif; ?>

</div>


<?php else: ?>

<!-- Fallback when no banners uploaded yet -->
<div class="w-full bg-primary text-center relative overflow-hidden aspect-[1900/600] min-h-[350px] sm:min-h-[400px] md:max-h-[600px]">
    <div class="absolute inset-0 bg-secondary-fixed/5 blur-[80px] pointer-events-none"></div>
    <div class="relative z-10 max-w-3xl mx-auto flex flex-col justify-center h-full px-4 md:px-8">
        <p class="text-secondary font-bold text-[10px] md:text-xs uppercase tracking-widest mb-2 md:mb-4">Safety By Choice</p>
        <h1 class="text-xl sm:text-2xl md:text-5xl font-extrabold text-white mb-3 md:mb-5 leading-tight">
            Precision Brass Fittings &amp; Industrial Safety
        </h1>
        <p class="text-white/70 text-xs sm:text-sm md:text-lg mb-4 md:mb-8 max-w-xl mx-auto">
            Engineered for performance. Trusted for reliability. Built for global infrastructure.
        </p>
        <div class="flex flex-row gap-3 justify-center">
            <a href="product.php"
               class="bg-secondary-container text-on-secondary-container px-4 py-2 md:px-8 md:py-3.5 rounded-lg md:rounded-xl font-bold text-xs md:text-sm hover:bg-secondary-fixed transition-all inline-flex items-center gap-1.5 md:gap-2">
                <span class="material-symbols-outlined text-[15px] md:text-[18px]">inventory_2</span> View Products
            </a>
            <a href="contact.php"
               class="border-2 border-white/30 text-white px-4 py-2 md:px-8 md:py-3.5 rounded-lg md:rounded-xl font-bold text-xs md:text-sm hover:bg-white/10 transition-all inline-flex items-center gap-1.5 md:gap-2">
                <span class="material-symbols-outlined text-[15px] md:text-[18px]">send</span> Contact Us
            </a>
        </div>
    </div>
</div>

<?php endif; ?>

<!-- Reopen centered wrapper for homepage content -->
<div class="max-w-[1700px] mx-auto w-full flex-1 flex flex-col relative">


<!-- ===== FEATURED CATEGORIES ===== -->
<section class="py-20 w-full px-4 md:px-8">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h2 class="text-3xl font-bold text-primary mb-2">Core Product Categories</h2>
            <div class="h-1 w-16 bg-secondary-container rounded-full"></div>
        </div>
        <a href="category.php" class="text-primary font-bold text-sm flex items-center gap-2 group">
            View All <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform text-[18px]">arrow_forward</span>
        </a>
    </div>

    <?php if (!empty($categories)): ?>
    <div class="flex flex-wrap justify-center gap-6 w-full">
        <?php foreach ($categories as $cat): ?>
        <a href="product.php?category_id=<?php echo $cat['id']; ?>"
           class="group relative overflow-hidden rounded-2xl industrial-shadow block card-lift w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)]"
           style="aspect-ratio: 4/3;">

            <!-- Background image -->
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

            <!-- Gradient overlay — stronger at bottom like screenshot -->
            <div class="absolute inset-0 bg-gradient-to-t from-primary/90 via-primary/20 to-transparent"></div>

            <!-- Hover border ring -->
            <div class="absolute inset-0 rounded-2xl border-2 border-secondary/0 group-hover:border-secondary/60 transition-all duration-300"></div>

            <!-- Text content at bottom -->
            <div class="absolute bottom-0 left-0 right-0 p-6">
                <span class="text-[9px] text-secondary-fixed font-bold uppercase tracking-[0.2em] block mb-2">Category</span>
                <h3 class="text-xl font-bold text-white mb-3">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </h3>
                <span class="inline-flex items-center gap-2 text-secondary-fixed font-bold text-sm group-hover:gap-3 transition-all">
                    View Products
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </span>
            </div>

        </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center text-outline italic py-10">No categories added yet.</p>
    <?php endif; ?>

    <div class="mt-8 text-left border-t border-outline-variant/20 pt-6">
        <p class="text-primary font-bold text-sm md:text-base">Special Products Can Be Made Upon Customer Requirement</p>
    </div>
</section>

<!-- ===== WHY FRIO ===== -->
<section class="py-14 md:py-16 bg-primary text-white overflow-hidden relative">
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-secondary-fixed/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="w-full px-4 md:px-8">
        <div class="text-center mb-14">
            <p class="text-secondary font-bold tracking-widest uppercase text-xs mb-3">Why Choose FRIO</p>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white">Built on Precision & Trust</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php
            $features = [
                ['icon'=>'workspace_premium',        'title'=>'Premium Brass',         'desc'=>'Manufactured with the highest grade brass material for maximum durability and strength.'],
                ['icon'=>'precision_manufacturing',  'title'=>'Precision Engineering', 'desc'=>'CAD-driven manufacturing processes achieving exacting tolerances for critical components.'],
                ['icon'=>'water_drop',               'title'=>'Leak Proof Quality',    'desc'=>'Rigorous multi-stage pressure testing guarantees 100% leak-proof performance in the field.'],
                ['icon'=>'support_agent',            'title'=>'Fast Support',          'desc'=>'Dedicated rapid-response customer service team ready to assist with any technical inquiries.'],
            ];
            foreach ($features as $f): ?>
            <div class="bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl p-10 transition-all hover:-translate-y-1">
                <div class="w-16 h-16 bg-secondary-fixed/20 flex items-center justify-center rounded-xl mb-6">
                    <span class="material-symbols-outlined text-secondary-fixed text-3xl" style="font-variation-settings:'FILL' 1;"><?php echo $f['icon']; ?></span>
                </div>
                <h3 class="text-xl font-extrabold text-white mb-3"><?php echo $f['title']; ?></h3>
                <p class="text-white/80 text-[15px] leading-[1.6]"><?php echo $f['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== TRUST & CERTIFICATIONS METRICS STRIP ===== -->
<section class="py-16 bg-[#f8f9fc] border-b border-outline-variant/15 w-full px-4 md:px-8">
    <div class="max-w-[1400px] mx-auto w-full">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-12">
            <!-- Metric 1: Experience -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/5 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[28px]">calendar_today</span>
                </div>
                <div>
                    <div class="text-4xl font-black text-primary leading-none tracking-tight">30+</div>
                    <div class="text-xs font-bold text-outline uppercase tracking-wider mt-1.5">Years of Excellence</div>
                    <p class="text-on-surface-variant text-[13px] leading-relaxed mt-1 font-medium">Established manufacturing heritage since 1996 serving critical global industries.</p>
                </div>
            </div>

            <!-- Metric 2: Export Countries -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/5 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[28px]">public</span>
                </div>
                <div>
                    <div class="text-4xl font-black text-primary leading-none tracking-tight">50+</div>
                    <div class="text-xs font-bold text-outline uppercase tracking-wider mt-1.5">Export Countries</div>
                    <p class="text-on-surface-variant text-[13px] leading-relaxed mt-1 font-medium">Trusted supplier of precision brass fittings to infrastructure projects worldwide.</p>
                </div>
            </div>

            <!-- Metric 3: Quality Standard -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/5 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[28px]">verified</span>
                </div>
                <div>
                    <div class="text-4xl font-black text-primary leading-none tracking-tight">ISO 9001</div>
                    <div class="text-xs font-bold text-outline uppercase tracking-wider mt-1.5">Certified Standards</div>
                    <p class="text-on-surface-variant text-[13px] leading-relaxed mt-1 font-medium">100% compliant with ISO 9001:2015 quality control systems and batch traceability.</p>
                </div>
            </div>

            <!-- Metric 4: Production Capability -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/5 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[28px]">precision_manufacturing</span>
                </div>
                <div>
                    <div class="text-4xl font-black text-primary leading-none tracking-tight">100%</div>
                    <div class="text-xs font-bold text-outline uppercase tracking-wider mt-1.5">In-House Testing</div>
                    <p class="text-on-surface-variant text-[13px] leading-relaxed mt-1 font-medium">Rigorous metallurgical lab verification, material purity testing, and dimensional inspection.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FEATURED PRODUCTS ===== -->
<section class="py-20 w-full px-4 md:px-8">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h2 class="text-3xl font-bold text-primary mb-2 uppercase">Our Other Products</h2>
            <div class="h-1 w-16 bg-secondary-container rounded-full"></div>
        </div>
        <a href="product.php" class="text-primary font-bold text-sm flex items-center gap-2 group">
            View All <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform text-[18px]">arrow_forward</span>
        </a>
    </div>
    <div class="flex overflow-x-auto snap-x snap-mandatory gap-4 pb-6 sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:gap-6 sm:pb-0 sm:overflow-visible sm:snap-none hide-scrollbar">
        <?php if (!empty($products)):
            foreach (array_slice(array_reverse($products), 0, 4) as $prod): ?>
            <div class="bg-white border border-outline-variant/30 rounded-2xl overflow-hidden card-lift group flex flex-col shrink-0 w-[85%] sm:w-auto snap-center sm:snap-align-none">
                <a href="product-detail.php?id=<?php echo $prod['id']; ?>"
                   class="aspect-square bg-surface-container flex items-center justify-center p-6 overflow-hidden block">
                    <?php if (!empty($prod['image'])): ?>
                        <img loading="lazy" src="<?php echo asset_url($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>"
                             class="w-full h-full object-contain mix-blend-multiply group-hover:scale-110 transition-transform duration-500" />
                    <?php else: ?>
                        <span class="material-symbols-outlined text-5xl text-outline/30">inventory_2</span>
                    <?php endif; ?>
                </a>
                <div class="p-5 flex flex-col flex-1">
                <?php if (!empty($prod['code'])): ?>
                <span class="text-[10px] font-bold text-secondary bg-secondary-fixed/20 px-2 py-1 rounded uppercase w-fit mb-2"><?php echo htmlspecialchars($prod['code']); ?></span>
                <?php endif; ?>
                <h4 class="font-bold text-primary text-base mb-4 flex-1 line-clamp-2"><?php echo htmlspecialchars($prod['name']); ?></h4>
                    <div class="border-t border-outline-variant/20 pt-3 mt-auto">
                        <a href="product-detail.php?id=<?php echo $prod['id']; ?>"
                           class="flex items-center gap-1.5 text-primary font-bold text-xs no-underline">
                           View Details <span class="material-symbols-outlined text-[15px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <p class="col-span-4 text-center text-outline italic py-10">No products in catalog yet.</p>
        <?php endif; ?>
    </div>

    <div class="mt-4 text-left">
        <p class="text-primary font-bold text-sm md:text-base italic flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">info</span>
            Special Products Can Be Made Upon Customer Requirement
        </p>
    </div>
</section>

<!-- ===== CATALOGUE CTA BANNER (screenshot style) ===== -->
<?php if (!empty($catalogues)): ?>
<section class="w-full my-16">
    <div class="bg-primary overflow-hidden px-6 sm:px-8 md:px-16 lg:px-24 py-12 md:py-16 flex flex-col md:flex-row items-center justify-between gap-10 relative w-full">

            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-white/3 rounded-full blur-3xl pointer-events-none -translate-y-1/2 translate-x-1/4"></div>

            <!-- LEFT: Text + CTA -->
            <div class="z-10 flex-1">
                <p class="text-secondary-fixed font-bold text-xs uppercase tracking-widest mb-3">Technical Documentation</p>
                <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-3 leading-snug">
                    <?php echo htmlspecialchars($catalogues[0]['name'] ?? '2026 Technical Catalogue'); ?>
                </h2>
                <p class="text-white/65 text-sm md:text-base leading-relaxed max-w-lg mb-8">
                    Get the complete product dataset, installation manuals, and technical specification guide for our entire brass fittings range.
                </p>
                <div class="flex flex-wrap gap-4">
                    <?php if (!empty($catalogues[0]['pdf_file'])): ?>
                    <a href="catalogue.php?autostart=1&pdf=<?php echo urlencode(asset_url($catalogues[0]['pdf_file'])); ?>&name=<?php echo urlencode($catalogues[0]['name'] ?? 'Technical Catalogue'); ?>&action=download"
                       class="inline-flex items-center gap-2.5 bg-secondary-container text-on-secondary-container
                              font-bold text-sm px-7 py-3.5 rounded-xl hover:bg-secondary-fixed transition-all shadow-lg">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Download Catalogue
                    </a>
                    <?php endif; ?>
                    <a href="catalogue.php"
                       class="inline-flex items-center gap-2 border border-white/25 text-white
                              font-bold text-sm px-7 py-3.5 rounded-xl hover:bg-white/10 transition-all">
                        View All
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>
            </div>

            <!-- RIGHT: Clean catalogue book thumbnails (max 2) -->
            <div class="z-10 flex items-end gap-4 shrink-0">
                <?php foreach (array_slice($catalogues, 0, 2) as $i => $cat):
                    $thumb_img = !empty($cat['preview_image']) ? asset_url($cat['preview_image']) : '';
                    $thumb_pdf = !empty($cat['pdf_file'])      ? 'catalogue.php?autostart=1&pdf=' . urlencode(asset_url($cat['pdf_file'])) . '&name=' . urlencode($cat['name'] ?? 'Catalogue') . '&action=view' : 'contact.php';
                ?>
                <a href="<?php echo $thumb_pdf; ?>"
                   class="relative group shrink-0 block rounded-xl overflow-hidden shadow-2xl
                          transition-all duration-400 hover:-translate-y-2
                          <?php echo $i === 0 ? 'w-[165px] md:w-[190px]' : 'w-[130px] md:w-[150px] opacity-70 hover:opacity-100'; ?>"
                   style="aspect-ratio:3/4;">

                    <?php if ($thumb_img): ?>
                    <img loading="lazy" src="<?php echo $thumb_img; ?>"
                         alt="<?php echo htmlspecialchars($cat['name'] ?? ''); ?>"
                         class="w-full h-full object-cover" />
                    <?php else: ?>
                    <div class="w-full h-full bg-white/10 flex flex-col justify-between p-4">
                        <div class="text-white/30 font-extrabold text-xs uppercase tracking-widest">FRIO Industrial</div>
                        <div class="text-white font-bold text-sm"><?php echo htmlspecialchars($cat['name'] ?? ''); ?></div>
                    </div>
                    <?php endif; ?>

                    <!-- Book-spine left shadow -->
                    <div class="absolute top-0 bottom-0 left-0 w-2.5 bg-black/30 pointer-events-none"></div>

                    <!-- Hover open icon -->
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center
                                opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <span class="material-symbols-outlined text-white text-3xl">open_in_new</span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
</section>
<?php endif; ?>

<!-- ===== INQUIRY STRIP ===== -->
<section class="w-full bg-secondary-fixed py-10 px-4 md:px-8 border-t border-secondary-fixed/20">
    <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-left">
        <div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-on-secondary-fixed">Need Custom Brass Fittings?</h2>
            <p class="text-on-secondary-fixed/80 mt-1 font-medium">We manufacture bespoke solutions tailored to your exact specifications.</p>
        </div>
        <a href="contact.php" class="bg-primary text-white px-8 py-4 rounded-xl font-bold text-sm hover:bg-primary/90 transition-all shadow-lg inline-flex items-center gap-2 shrink-0">
            Send Inquiry <span class="material-symbols-outlined text-[18px]">send</span>
        </a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>