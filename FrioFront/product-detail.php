<?php
require_once 'config.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$product_id) { header('Location: product.php'); exit; }

$settings = api_fetch('settings.php') ?? [];
$product  = api_fetch("products.php?id={$product_id}");

if (empty($product)) { header('Location: product.php'); exit; }

$page_title = htmlspecialchars($product['name']) . ' | FRIO Industrial';
$meta_desc  = htmlspecialchars(substr($product['description'] ?? '', 0, 150));
$active_page = 'product';

// Specifications JSON
$specs = [];
if (!empty($product['specifications'])) {
    if (is_string($product['specifications'])) {
        $decoded = json_decode($product['specifications'], true);
        if (is_array($decoded)) $specs = $decoded;
    } elseif (is_array($product['specifications'])) {
        $specs = $product['specifications'];
    }
}

// Spec cards: prefer Material / Finish / Application, else first 3 entries
$specIcons = [
    'material'    => 'architecture',
    'finish'      => 'flare',
    'application' => 'precision_manufacturing',
];
$specCards = [];
$preferred = ['Material', 'Finish', 'Application'];
foreach ($preferred as $label) {
    foreach ($specs as $k => $v) {
        if (strcasecmp($k, $label) === 0) {
            $iconKey = strtolower($label);
            $specCards[] = ['label' => $label, 'value' => $v, 'icon' => $specIcons[$iconKey] ?? 'tune'];
            break;
        }
    }
}
if (count($specCards) < 3) {
    $used = array_column($specCards, 'label');
    foreach ($specs as $k => $v) {
        if (count($specCards) >= 3) break;
        if (in_array($k, $used, true)) continue;
        $specCards[] = ['label' => $k, 'value' => $v, 'icon' => 'tune'];
    }
}

// Gallery image paths
$gallery = [];
if (!empty($product['gallery']) && is_array($product['gallery'])) {
    foreach ($product['gallery'] as $row) {
        $path = is_array($row) ? ($row['image'] ?? '') : $row;
        if ($path) $gallery[] = $path;
    }
}
if (!empty($product['image'])) {
    $mainImg = $product['image'];
    if (!in_array($mainImg, $gallery, true)) {
        array_unshift($gallery, $mainImg);
    }
}
$gallery = array_values(array_unique($gallery));

// Size variations
$variations = [];
if (!empty($product['variations']) && is_array($product['variations'])) {
    $variations = $product['variations'];
}

$category_name = $product['category_name'] ?? '';
$category_id   = $product['category_id'] ?? 0;
$contact_url   = 'contact.php?product=' . urlencode($product['name']);

$catalogues = api_fetch('catalogues.php') ?? [];
$catalog_pdf  = !empty($catalogues[0]['pdf_file']) ? asset_url($catalogues[0]['pdf_file']) : '';

// Other products in the same category (exclude current)
$category_products = [];
if ($category_id) {
    $all_products = api_fetch('products.php') ?? [];
    $category_products = array_values(array_filter(
        $all_products,
        fn($p) => (int)($p['category_id'] ?? 0) === (int)$category_id && (int)($p['id'] ?? 0) !== $product_id
    ));
    $category_products = array_slice($category_products, 0, 8);
}

include 'includes/header.php';
?>
</div> <!-- Close global wrapper for full-bleed hero banner -->




<main class="w-full px-4 md:px-8 pt-8 pb-16 bg-gradient-to-b from-[#fdfdff] via-[#f4f7ff] to-[#fdfdff]">
    <div class="max-w-[1400px] mx-auto w-full flex-1 flex flex-col relative">
        
        <!-- Breadcrumb Inline (Premium Side/Top Placement) -->
        <nav class="flex items-center gap-2 text-sm text-outline/80 mb-6">
            <a href="index.php" class="hover:text-primary transition-colors font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-[18px] text-outline/60 font-medium">home</span>
                Home
            </a>
            <?php if (!empty($product['category_name'])): ?>
            <span class="material-symbols-outlined text-outline/40 text-[18px]">chevron_right</span>
            <a href="product.php?category_id=<?php echo $category_id; ?>" class="hover:text-primary transition-colors font-semibold flex items-center gap-1">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </a>
            <?php else: ?>
            <span class="material-symbols-outlined text-outline/40 text-[18px]">chevron_right</span>
            <a href="product.php" class="hover:text-primary transition-colors font-semibold flex items-center gap-1">
                Products
            </a>
            <?php endif; ?>
            <span class="material-symbols-outlined text-outline/40 text-[18px]">chevron_right</span>
            <span class="text-primary font-bold">
                <?php echo htmlspecialchars($product['name']); ?>
            </span>
        </nav>
        
        <!-- PRODUCT DETAIL SECTION (Item 1 & 2) -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-16 lg:gap-24 mb-14">
        
        <!-- LEFT: Product Image & Gallery (Item 3 & 4) -->
        <div class="flex flex-col items-center justify-start pt-2 md:sticky md:top-24 self-start">
            <div class="product-image-container overflow-hidden w-full flex justify-center items-center h-[500px] p-8 bg-white border border-outline-variant/30 rounded-2xl hover:shadow-[0_8px_30px_rgba(0,52,98,0.06)] transition-all duration-300 relative group cursor-zoom-in">
                <?php if (!empty($product['image'])): ?>
                    <img loading="lazy" id="main-product-image"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        src="<?php echo asset_url($product['image']); ?>"
                        class="w-full h-full object-contain mix-blend-multiply transition-transform duration-300 ease-out origin-center"
                        data-base-image="<?php echo asset_url($product['image']); ?>"
                    />
                <?php else: ?>
                    <img loading="lazy" id="main-product-image"
                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                        src=""
                        class="w-full h-full object-contain hidden"
                        data-base-image=""
                    />
                    <div id="image-fallback" class="flex items-center justify-center h-full text-gray-300">
                        <span class="material-symbols-outlined text-[96px]">inventory_2</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Gallery Thumbnails (Item 4) -->
            <?php if (count($gallery) > 1): ?>
            <div class="flex flex-wrap gap-3.5 mt-6 justify-center w-full">
                <?php foreach ($gallery as $gIdx => $gImg): ?>
                <button 
                    type="button"
                    onclick="changeGalleryImage(this, '<?php echo asset_url($gImg); ?>', <?php echo $gIdx; ?>)"
                    class="gallery-thumb w-[70px] h-[70px] rounded-xl border p-1 bg-white hover:border-primary transition-all overflow-hidden flex items-center justify-center <?php echo $gIdx === 0 ? 'border-primary shadow-sm scale-105' : 'border-outline-variant/40 hover:scale-105'; ?>"
                >
                    <img loading="lazy" src="<?php echo asset_url($gImg); ?>" 
                        alt="Product View <?php echo $gIdx + 1; ?>" 
                        class="w-full h-full object-contain"
                    />
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- RIGHT: Product Information (Item 1 & 5) -->
        <div class="flex flex-col justify-start relative" id="right-column">
            
            <!-- Zoom Result Container (Covers description on hover) -->
            <div id="zoom-result-container" class="hidden absolute inset-0 bg-white z-[60] rounded-2xl shadow-2xl pointer-events-none transition-opacity duration-200 opacity-0" style="background-repeat: no-repeat; border: 1px solid #c2c6d1;"></div>

            <!-- Product Category Subtitle -->
            <?php if (!empty($product['category_name'])): ?>
            <a href="product.php?category_id=<?php echo $category_id; ?>" class="inline-block text-xs font-extrabold uppercase tracking-widest text-secondary hover:text-primary transition-colors mb-2.5">
                <?php echo htmlspecialchars($product['category_name']); ?>
            </a>
            <?php endif; ?>

            <!-- Product Name (Item 5) -->
            <h1 id="product-title" class="text-[42px] font-[800] leading-tight text-primary mb-3 font-headline-xl" data-default-name="<?php echo htmlspecialchars($product['name']); ?>">
                <?php echo htmlspecialchars($product['name']); ?>
            </h1>
            
            <!-- Product Code Subtitle (SKU) -->
            <p id="product-sku" class="text-xs font-extrabold tracking-wider uppercase text-outline/80 mb-5">
                <?php echo !empty($product['code']) ? 'SKU: ' . htmlspecialchars($product['code']) : ''; ?>
            </p>

            <!-- Trust Badges (Item 13) -->
            <div class="flex flex-wrap gap-2.5 mb-8">
                <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-wider text-[#0B3A73] bg-[#0B3A73]/5 px-3 py-1.5 rounded-lg border border-[#0B3A73]/10">
                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                    Leak Proof
                </span>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-wider text-secondary bg-secondary-fixed/20 px-3 py-1.5 rounded-lg border border-secondary/10">
                    <span class="material-symbols-outlined text-[14px]">verified</span>
                    Corrosion Resistant
                </span>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-wider text-green-700 bg-green-50 px-3 py-1.5 rounded-lg border border-green-100">
                    <span class="material-symbols-outlined text-[14px]">architecture</span>
                    Precision Engineered
                </span>
                <span class="inline-flex items-center gap-1.5 text-[11px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-100">
                    <span class="material-symbols-outlined text-[14px]">shield</span>
                    Industrial Grade
                </span>
            </div>
            
            <!-- Available Sizes / Variants (Item 8) -->
            <?php if (!empty($variations)): ?>
            <div class="mb-8">
                <p class="font-extrabold text-sm text-primary mb-3.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">aspect_ratio</span>
                    Select Dimensions <span class="text-xs text-outline font-medium font-body-md normal-case tracking-normal">(<?php echo count($variations); ?> Variants Available)</span>
                </p>
                <div class="flex flex-wrap gap-2.5">
                    <?php 
                    $maxVisible = 10;
                    $totalVars = count($variations);
                    foreach ($variations as $vIdx => $var): 
                        $sizeLabel = !empty($var['size']) ? $var['size'] : ($var['code'] ?? 'Size ' . ($vIdx + 1)); 
                        $hiddenClass = ($vIdx >= $maxVisible) ? 'hidden extra-chip' : '';
                    ?>
                        <button 
                            type="button"
                            onclick="selectSize(this);"
                            class="px-5 py-3 rounded-xl border text-sm font-extrabold transition-all duration-300 <?php echo $hiddenClass; ?> <?php echo $vIdx === 0 ? 'border-primary bg-primary text-white size-chip-active shadow-md' : 'border-outline-variant/40 bg-white text-outline hover:border-primary hover:text-primary hover:bg-primary/5'; ?>"
                            data-size="<?php echo htmlspecialchars($sizeLabel); ?>"
                            data-code="<?php echo htmlspecialchars($var['code'] ?? $product['code'] ?? ''); ?>"
                            data-name="<?php echo htmlspecialchars(!empty($var['name']) ? $var['name'] : $product['name']); ?>"
                            data-image="<?php echo !empty($var['image']) ? asset_url($var['image']) : ''; ?>"
                            data-index="<?php echo $vIdx; ?>"
                        >
                            <?php echo htmlspecialchars($sizeLabel); ?>
                        </button>
                    <?php endforeach; ?>
                    
                    <?php if ($totalVars > $maxVisible): ?>
                        <button type="button" id="more-chips-btn" class="px-5 py-3 rounded-xl border border-outline-variant/40 bg-white text-primary font-extrabold text-sm hover:bg-primary/5 hover:border-primary transition-all duration-300">
                            +<?php echo $totalVars - $maxVisible; ?> More
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Primary CTA Button: Premium B2B Inquiry (Item 9) -->
            <a 
                href="<?php echo htmlspecialchars($contact_url); ?>"
                class="w-full flex flex-col items-center justify-center gap-0.5 bg-gradient-to-r from-primary to-primary-container hover:from-secondary-container hover:to-secondary-fixed hover:text-on-secondary-container text-white py-4.5 rounded-2xl font-extrabold text-base transition-all duration-300 shadow-[0_8px_30px_rgba(0,52,98,0.15)] hover:shadow-[0_12px_40px_rgba(115,92,0,0.2)] hover:-translate-y-1 hover:scale-[1.01] text-center border border-white/10 group/cta"
            >
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] group-hover/cta:rotate-12 transition-transform">send</span>
                    Send B2B Inquiry
                </span>
                <span class="text-[10px] uppercase tracking-wider font-extrabold opacity-75 font-label-bold">Get Custom Manufacturing Quote</span>
            </a>

            <!-- Tabbed Accordion Section (Item 6, 7, 11 & 12) -->
            <div class="mt-8 border border-outline-variant/20 rounded-2xl overflow-hidden bg-white shadow-sm">
                <!-- Tab 1: Description -->
                <div class="border-b border-outline-variant/20">
                    <button class="accordion-trigger w-full flex items-center justify-between px-6 py-4 bg-surface-container-low hover:bg-surface-container transition-colors text-left font-extrabold text-base text-primary outline-none" data-target="accordion-desc">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">description</span>
                            Description
                        </span>
                        <span class="material-symbols-outlined text-[20px] transition-transform duration-300">expand_more</span>
                    </button>
                    <div id="accordion-desc" class="accordion-content transition-all duration-300 overflow-hidden max-h-0">
                        <div class="p-6 rich-text-content text-outline/90 leading-relaxed font-medium text-[15px] space-y-3 font-body-md">
                            <?php echo !empty($product['description']) ? $product['description'] : 'Precision manufactured B2B industrial component engineered to strict specifications for leak-proof performance under high-pressure conditions.'; ?>
                        </div>
                    </div>
                </div>



                <!-- Tab 4: Downloads -->
                <div>
                    <button class="accordion-trigger w-full flex items-center justify-between px-6 py-4 bg-surface-container-low hover:bg-surface-container transition-colors text-left font-extrabold text-base text-primary outline-none" data-target="accordion-downloads">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">download</span>
                            Downloads
                        </span>
                        <span class="material-symbols-outlined text-[20px] transition-transform duration-300">expand_more</span>
                    </button>
                    <div id="accordion-downloads" class="accordion-content transition-all duration-300 overflow-hidden max-h-0">
                        <div class="p-6 flex flex-col gap-6">
                            <!-- 1. Product Datasheet (if available) -->
                            <?php if (!empty($product['pdf_file'])): ?>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 p-4 rounded-xl border border-outline-variant/30 bg-surface-container-lowest hover:shadow-md transition-shadow w-full">
                                <div class="w-14 h-14 rounded-lg bg-red-50 flex items-center justify-center border border-red-100 text-red-600 shrink-0">
                                    <span class="material-symbols-outlined text-[32px]">picture_as_pdf</span>
                                </div>
                                <div class="flex-grow min-w-0">
                                    <h4 class="font-extrabold text-sm text-primary leading-tight truncate">Product Technical Datasheet</h4>
                                    <p class="text-xs text-outline font-semibold mt-1">Detailed dimensions, tolerances & compliance specifications</p>
                                </div>
                                <a href="<?php echo asset_url($product['pdf_file']); ?>" target="_blank" class="shrink-0 flex items-center gap-1.5 bg-primary text-white hover:bg-primary-container px-4 py-2.5 rounded-lg font-bold text-xs shadow transition-colors w-full sm:w-auto text-center justify-center download-gate-trigger" data-pdf="<?php echo asset_url($product['pdf_file']); ?>" data-name="<?php echo htmlspecialchars($product['name'] ?? 'Product Technical Datasheet'); ?>">
                                    <span class="material-symbols-outlined text-[16px]">download</span>
                                    Download
                                </a>
                            </div>
                            <?php endif; ?>

                            <!-- 2. Main Corporate Technical Catalogue -->
                            <?php if (!empty($catalog_pdf)): ?>
                            <?php
                            $catalog_preview = !empty($catalogues[0]['preview_image']) ? asset_url($catalogues[0]['preview_image']) : 'assets/imag/catalogue_cover_placeholder.jpg';
                            $catalog_name = !empty($catalogues[0]['name']) ? htmlspecialchars($catalogues[0]['name']) : 'Technical Catalogue';
                            ?>
                            <div class="flex flex-col md:flex-row items-start md:items-center gap-6 p-5 rounded-2xl border border-outline-variant/30 bg-surface-container-lowest hover:shadow-lg transition-all duration-300 w-full">
                                <!-- Catalogue Preview Image (Left) -->
                                <div class="w-36 h-44 rounded-xl overflow-hidden border border-outline-variant/30 bg-white shadow-md hover:scale-105 transition-transform shrink-0 flex items-center justify-center relative group self-center md:self-start">
                                    <img src="<?php echo $catalog_preview; ?>" alt="<?php echo $catalog_name; ?>" class="w-full h-full object-cover" />
                                    <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition-colors"></div>
                                </div>
                                
                                <!-- Catalogue Details & Download Button (Right) -->
                                <div class="flex-grow min-w-0 flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">
                                    <div>
                                        <h4 class="font-extrabold text-lg text-primary leading-tight"><?php echo $catalog_name; ?></h4>
                                    </div>
                                    
                                    <a href="<?php echo $catalog_pdf; ?>" target="_blank" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#735c00] to-secondary hover:from-[#ffe088] hover:to-[#fed65b] hover:text-on-secondary-container text-white px-5 py-3 rounded-xl font-extrabold text-xs shadow-md hover:shadow-lg transition-all duration-300 download-gate-trigger" data-pdf="<?php echo $catalog_pdf; ?>" data-name="<?php echo $catalog_name; ?>">
                                        <span class="material-symbols-outlined text-[18px]">download</span>
                                        Download Catalogue
                                    </a>
                                </div>
                            </div>
                            <?php else: ?>
                            <!-- Fallback link to catalog list page -->
                            <div class="flex flex-col md:flex-row items-start md:items-center gap-6 p-5 rounded-2xl border border-outline-variant/30 bg-surface-container-lowest hover:shadow-lg transition-all duration-300 w-full">
                                <div class="w-36 h-44 rounded-xl bg-surface-container flex items-center justify-center border border-outline-variant/30 text-outline shrink-0 self-center md:self-start">
                                    <span class="material-symbols-outlined text-[48px]">menu_book</span>
                                </div>
                                <div class="flex-grow min-w-0 flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">
                                    <div>
                                        <h4 class="font-extrabold text-lg text-primary leading-tight">FRIO Corporate Technical Brochure</h4>
                                    </div>
                                    <a href="catalogue.php" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-primary text-white hover:bg-primary-container px-5 py-3 rounded-xl font-extrabold text-xs shadow-md transition-all">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        View Catalogues
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- RELATED PRODUCTS SECTION (Item 10) -->
    <?php if (!empty($category_products)): ?>
    <section class="mt-20 border-t border-outline-variant/20 pt-14">
        <div class="mb-10 text-center md:text-left">
            <span class="inline-block text-xs font-extrabold uppercase tracking-widest text-secondary bg-secondary-fixed/30 px-3 py-1.5 rounded-full mb-3">
                Related Industrial Catalog
            </span>
            <h2 class="text-3xl font-extrabold text-primary font-headline-lg">Related Components</h2>
            <p class="text-sm text-outline mt-1 font-semibold">Explore matching hardware in <a href="product.php?category_id=<?php echo $category_id; ?>" class="text-secondary hover:text-primary hover:underline transition-colors font-bold"><?php echo htmlspecialchars($category_name ?: 'this Category'); ?></a></p>
        </div>
        
        <div class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-6 scroll-smooth hide-scrollbar">
            <?php foreach ($category_products as $cp): ?>
            <div class="product-card card-lift bg-white border border-outline-variant/20 rounded-[18px] shadow-[0_8px_24px_rgba(0,0,0,0.03)] hover:shadow-[0_12px_32px_rgba(0,52,98,0.08)] transition-all overflow-hidden flex flex-col group relative shrink-0 w-[85%] sm:w-[calc(50%-12px)] md:w-[calc(33.333%-16px)] lg:w-[calc(25%-18px)] snap-center">
                
                <!-- Image aspect-square container (Item 10) -->
                <div class="block aspect-square bg-surface-container p-6 overflow-hidden relative group-hover:scale-105 transition-transform duration-500">
                    <?php if (!empty($cp['image'])): ?>
                        <img loading="lazy" src="<?php echo asset_url($cp['image']); ?>"
                            alt="<?php echo htmlspecialchars($cp['name']); ?>"
                            class="w-full h-full object-contain mix-blend-multiply transition-all duration-500" />
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-5xl text-outline/30">inventory_2</span>
                        </div>
                    <?php endif; ?>

                    <!-- Hover Actions Overlay -->
                    <div class="absolute inset-0 bg-primary/45 backdrop-blur-[2px] flex flex-col items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10">
                        <a href="product-detail.php?id=<?php echo $cp['id']; ?>" class="bg-white text-primary font-bold text-xs px-5 py-2.5 rounded-lg hover:bg-secondary-container hover:text-on-secondary-container transition-all shadow-md flex items-center gap-1.5 hover:scale-105">
                            <span class="material-symbols-outlined text-[15px]">visibility</span>
                            Quick View
                        </a>
                    </div>
                </div>

                <div class="p-5 flex flex-col flex-1 relative z-20 bg-white">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="font-extrabold text-primary text-[15px] leading-tight line-clamp-2">
                            <?php echo htmlspecialchars($cp['name']); ?>
                        </h3>
                    </div>
                    <?php if (!empty($cp['code'])): ?>
                    <span class="inline-block text-[10px] font-extrabold uppercase tracking-wider text-outline bg-surface-container px-2 py-1 rounded mb-4 w-fit">
                        <?php echo htmlspecialchars($cp['code']); ?>
                    </span>
                    <?php endif; ?>
                    <div class="border-t border-outline-variant/20 pt-4 mt-auto">
                        <a href="product-detail.php?id=<?php echo $cp['id']; ?>"
                            class="flex items-center gap-1.5 text-primary font-extrabold text-xs no-underline group/btn">
                            View Details
                            <span class="material-symbols-outlined text-[15px] group-hover/btn:translate-x-1 transition-transform">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    
</main>

<!-- Sticky Order/Inquiry Scroll Bar (Item 14) -->
<div id="sticky-inquiry-bar" class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-outline-variant/30 shadow-[0_-8px_30px_rgba(0,0,0,0.08)] transform translate-y-full transition-transform duration-300 flex items-center justify-center py-4 px-6">
    <div class="max-w-[1400px] w-full flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <?php if (!empty($product['image'])): ?>
                <img loading="lazy" src="<?php echo asset_url($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-10 h-10 object-contain mix-blend-multiply bg-surface-container p-1 rounded-lg" />
            <?php endif; ?>
            <div>
                <p class="font-extrabold text-sm text-primary leading-tight"><?php echo htmlspecialchars($product['name']); ?></p>
                <p class="text-xs text-outline font-semibold">Need Bulk Order? Get Custom Quote</p>
            </div>
        </div>
        <div>
            <a href="<?php echo htmlspecialchars($contact_url); ?>" class="inline-flex items-center gap-1.5 bg-secondary-container text-on-secondary-container hover:bg-secondary hover:text-white font-extrabold text-xs px-5 py-2.5 rounded-lg shadow-md transition-all">
                <span class="material-symbols-outlined text-[16px]">send</span>
                Send Inquiry
            </a>
        </div>
    </div>
</div>


<!-- Amazon-Style Premium Lightbox Modal -->
<div id="amazon-media-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/85 backdrop-blur-sm transition-all duration-300 opacity-0">
    <!-- Close Button Top-Right of Screen -->
    <button id="amazon-modal-close" class="absolute top-4 right-4 z-[110] text-white hover:text-secondary-container transition-colors flex items-center justify-center p-2 rounded-full hover:bg-white/10" aria-label="Close Gallery">
        <span class="material-symbols-outlined text-[36px] font-bold">close</span>
    </button>
    
    <!-- Modal Card -->
    <div id="amazon-modal-card" class="relative w-full h-full md:h-[90vh] md:max-h-[850px] md:w-[95vw] md:max-w-[1300px] bg-white md:rounded-3xl shadow-2xl flex flex-col md:flex-row overflow-hidden transform scale-95 transition-all duration-300">
        
        <!-- LEFT: Vertical thumbnail list (visible on desktop) -->
        <div class="hidden md:flex flex-col gap-3 p-6 border-r border-outline-variant/30 overflow-y-auto w-[110px] shrink-0 bg-surface-container-lowest">
            <p class="text-[10px] font-extrabold uppercase tracking-wider text-outline mb-1 text-center">Gallery</p>
            <div id="amazon-modal-thumbs" class="flex flex-col gap-3 items-center"></div>
        </div>
        
        <!-- CENTER: Large image view with zooming capabilities -->
        <div class="flex-grow flex flex-col items-center justify-center p-6 md:p-10 relative bg-white min-w-0">
            <!-- Navigation Arrows -->
            <button id="amazon-modal-prev" class="absolute left-4 z-20 bg-white/80 hover:bg-white text-primary rounded-full w-12 h-12 shadow-lg border border-outline-variant/20 flex items-center justify-center transition-all hover:scale-105" aria-label="Previous Image">
                <span class="material-symbols-outlined text-[28px] font-bold">chevron_left</span>
            </button>
            
            <div id="amazon-modal-img-container" class="relative overflow-hidden w-full h-[55vh] md:h-[70vh] max-h-[600px] flex justify-center items-center p-4 cursor-zoom-in group">
                <img id="amazon-modal-large-img" src="" alt="Zoomed view" class="max-w-full max-h-full object-contain select-none mix-blend-multiply transition-transform duration-200 ease-out origin-center" />
                
                <!-- Overlayed Zoom Hint -->
                <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 bg-black/60 text-white text-[11px] font-bold px-3 py-1.5 rounded-full flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[14px]">zoom_in</span>
                    Hover to Zoom
                </div>
            </div>
            
            <button id="amazon-modal-next" class="absolute right-4 z-20 bg-white/80 hover:bg-white text-primary rounded-full w-12 h-12 shadow-lg border border-outline-variant/20 flex items-center justify-center transition-all hover:scale-105" aria-label="Next Image">
                <span class="material-symbols-outlined text-[28px] font-bold">chevron_right</span>
            </button>
            
            <!-- Mobile Horizontal Thumbnails Strip -->
            <div class="flex md:hidden overflow-x-auto max-w-full gap-2.5 mt-4 pb-2 scroll-smooth hide-scrollbar w-full justify-center">
                <div id="amazon-modal-thumbs-mobile" class="flex gap-2.5 shrink-0"></div>
            </div>
        </div>
        
        <!-- RIGHT: Product Info Sidebar (Desktop visible) -->
        <div class="hidden lg:flex flex-col p-8 border-l border-outline-variant/30 w-[340px] shrink-0 bg-surface-container-low/40 justify-between">
            <div>
                <!-- Category -->
                <?php if (!empty($product['category_name'])): ?>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-secondary mb-1.5 block">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </span>
                <?php endif; ?>
                
                <!-- Title -->
                <h3 class="text-xl font-[800] text-primary leading-snug mb-2 font-headline-md">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h3>
                
                <!-- SKU -->
                <p id="amazon-modal-sku" class="text-[11px] font-extrabold tracking-wider uppercase text-outline/80 mb-6">
                    <?php echo !empty($product['code']) ? 'SKU: ' . htmlspecialchars($product['code']) : ''; ?>
                </p>
                
                <hr class="border-outline-variant/30 mb-6" />
                
                <!-- Specifications -->
                <p class="font-extrabold text-xs text-primary mb-3 uppercase tracking-wider">Specifications</p>
                <div class="flex flex-col gap-2.5 max-h-[220px] overflow-y-auto pr-1">
                    <?php if (!empty($specs)): ?>
                        <?php foreach (array_slice($specs, 0, 6) as $sName => $sVal): ?>
                        <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-outline-variant/20">
                            <span class="font-semibold text-outline"><?php echo htmlspecialchars($sName); ?></span>
                            <span class="font-bold text-primary text-right"><?php echo htmlspecialchars($sVal); ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-xs text-outline italic">No specifications listed.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Bottom Inquiry CTA -->
            <div class="mt-6 pt-4 border-t border-outline-variant/30">
                <a href="<?php echo htmlspecialchars($contact_url); ?>" class="w-full flex items-center justify-center gap-2 bg-primary hover:bg-secondary-container hover:text-on-secondary-container text-white py-3 rounded-xl font-extrabold text-xs transition-all duration-300 shadow-md">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    Send B2B Inquiry
                </a>
            </div>
        </div>
        
    </div>
</div>

<script>
// Global state for active product gallery image index
window.currentImageIndex = 0;

// All gallery image URLs resolved for JS / Fancybox
window.galleryImages = <?php 
    $resolved_gallery = array_map(function($img) {
        return asset_url($img);
    }, $gallery);
    echo json_encode($resolved_gallery);
?>.map(img => ({ src: img, type: 'image' }));
</script>
<script src="assets/js/product.js?v=<?php echo time(); ?>" defer></script>

<!-- Gated Catalogue Download Modal -->
<div id="download-gate-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm transition-all duration-300">
    <div class="relative w-full max-w-[500px] bg-white rounded-3xl p-8 shadow-2xl border border-outline-variant/20 mx-4 transform transition-all duration-300 animate-[fadeInScale_0.2s_ease-out]">
        <!-- Close Button -->
        <button id="download-gate-close" class="absolute top-4 right-4 text-outline hover:text-primary transition-colors flex items-center justify-center p-1.5 rounded-full hover:bg-surface-container-low" aria-label="Close form">
            <span class="material-symbols-outlined text-[24px]">close</span>
        </button>
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-secondary-container/10 text-secondary rounded-2xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[28px]">download_lock</span>
            </div>
            <h3 class="text-xl font-extrabold text-primary leading-tight font-headline-md">Technical Catalogue Access</h3>
            <p class="text-xs text-outline font-semibold mt-1.5">Please provide your details below to unlock immediate access to our high-resolution brochures.</p>
        </div>
        
        <!-- Error panel -->
        <div id="dg-error-msg" class="hidden bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-5 text-xs font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px] text-red-600">error</span>
            <span id="dg-error-text">Error message goes here</span>
        </div>
        
        <!-- Form -->
        <form id="download-gate-form" class="space-y-4" novalidate>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-extrabold text-on-surface-variant uppercase tracking-widest" for="dg-fname">First Name *</label>
                    <input id="dg-fname" type="text" required placeholder="John"
                           class="w-full h-12 border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-surface-container-low" />
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-extrabold text-on-surface-variant uppercase tracking-widest" for="dg-lname">Last Name</label>
                    <input id="dg-lname" type="text" placeholder="Smith"
                           class="w-full h-12 border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-surface-container-low" />
                </div>
            </div>
            
            <div class="space-y-1.5">
                <label class="block text-[10px] font-extrabold text-on-surface-variant uppercase tracking-widest" for="dg-email">Email Address *</label>
                <input id="dg-email" type="email" required placeholder="you@company.com"
                       class="w-full h-12 border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-surface-container-low" />
            </div>
            
            <div class="space-y-1.5">
                <label class="block text-[10px] font-extrabold text-on-surface-variant uppercase tracking-widest" for="dg-phone">Mobile Number *</label>
                <input id="dg-phone" type="tel" required placeholder="10-digit number" maxlength="10" pattern="[0-9]{10}"
                       class="w-full h-12 border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-surface-container-low" />
                <p class="text-[9px] text-outline font-semibold">Enter your 10-digit mobile number without country prefix.</p>
            </div>
            
            <button type="submit" id="dg-submit-btn"
                    class="w-full bg-gradient-to-r from-primary to-primary-container hover:from-secondary-container hover:to-secondary-fixed hover:text-on-secondary-container text-white py-3.5 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:-translate-y-0.5 mt-2">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span id="dg-submit-label">Submit &amp; Download</span>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let pendingPdfPath = '';
    let pendingPdfName = '';
    let pendingPdfAction = 'download'; // 'view' or 'download'
    
    const downloadModal = document.getElementById('download-gate-modal');
    const modalForm = document.getElementById('download-gate-form');
    const closeBtn = document.getElementById('download-gate-close');
    const errorBox = document.getElementById('dg-error-msg');
    const errorText = document.getElementById('dg-error-text');
    
    function openGateModal() {
        if (!downloadModal) return;
        downloadModal.classList.remove('hidden');
        downloadModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
    }
    
    function closeGateModal() {
        if (!downloadModal) return;
        downloadModal.classList.add('hidden');
        downloadModal.classList.remove('flex');
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
    }
    
    function showError(msg) {
        if (errorBox && errorText) {
            errorText.textContent = msg;
            errorBox.classList.remove('hidden');
        }
    }
    
    function updateModalLabels(action) {
        const modalSubtitle = document.querySelector('#download-gate-modal p.text-outline');
        const submitLabel = document.getElementById('dg-submit-label');
        if (action === 'view') {
            if (modalSubtitle) modalSubtitle.textContent = "Please provide your details below to view our high-resolution brochures.";
            if (submitLabel) submitLabel.textContent = "Submit & View";
        } else {
            if (modalSubtitle) modalSubtitle.textContent = "Please provide your details below to unlock immediate access to our high-resolution brochures.";
            if (submitLabel) submitLabel.textContent = "Submit & Download";
        }
    }
    
    // Intercept catalogue download clicks
    document.querySelectorAll('.download-gate-trigger').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            if (localStorage.getItem('catalogue_unlocked') === 'true') {
                return; // Let standard click execute natively
            }
            
            e.preventDefault();
            pendingPdfPath = trigger.getAttribute('data-pdf') || trigger.getAttribute('href');
            pendingPdfName = trigger.getAttribute('data-name') || 'Technical Catalogue';
            
            // Determine action (view vs download)
            const isDownload = trigger.hasAttribute('download') || 
                               trigger.innerText.toLowerCase().includes('download') || 
                               trigger.getAttribute('data-action') === 'download';
            pendingPdfAction = isDownload ? 'download' : 'view';
            
            updateModalLabels(pendingPdfAction);
            
            if (errorBox) errorBox.classList.add('hidden');
            if (modalForm) modalForm.reset();
            
            openGateModal();
        });
    });
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeGateModal);
    }
    
    if (downloadModal) {
        downloadModal.addEventListener('click', (e) => {
            if (e.target === downloadModal) {
                closeGateModal();
            }
        });
    }
    
    if (modalForm) {
        modalForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = document.getElementById('dg-submit-btn');
            const btnLabel = document.getElementById('dg-submit-label');
            
            const first_name = document.getElementById('dg-fname').value.trim();
            const last_name = document.getElementById('dg-lname').value.trim();
            const email = document.getElementById('dg-email').value.trim();
            const phone = document.getElementById('dg-phone').value.trim();
            
            // Validation
            if (!first_name) {
                showError("First name is required.");
                return;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                showError("Please enter a valid email address (e.g., name@email.com).");
                return;
            }
            const cleanPhone = phone.replace(/[^0-9]/g, '');
            if (cleanPhone.length !== 10) {
                showError("Mobile number must be a valid 10-digit number.");
                return;
            }
            
            btn.disabled = true;
            btnLabel.textContent = 'Verifying...';
            if (errorBox) errorBox.classList.add('hidden');
            
            try {
                const response = await fetch('<?php echo API_BASE_URL; ?>api/front_api/inquiries.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'catalogue',
                        first_name: first_name,
                        last_name: last_name,
                        email: email,
                        phone: cleanPhone,
                        message: (pendingPdfAction === 'view' ? 'Catalogue Viewed: ' : 'Catalogue Downloaded: ') + pendingPdfName
                    })
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    localStorage.setItem('catalogue_unlocked', 'true');
                    closeGateModal();
                    
                    if (pendingPdfPath) {
                        const dlLink = document.createElement('a');
                        dlLink.href = pendingPdfPath;
                        dlLink.target = '_blank';
                        if (pendingPdfAction === 'download') {
                            dlLink.download = '';
                        }
                        document.body.appendChild(dlLink);
                        dlLink.click();
                        document.body.removeChild(dlLink);
                    }
                } else {
                    showError(result.message || "Failed to submit. Please try again.");
                }
            } catch (err) {
                showError("Network connection failure. Please try again.");
            } finally {
                btn.disabled = false;
                btnLabel.textContent = pendingPdfAction === 'view' ? 'Submit & View' : 'Submit & Download';
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
