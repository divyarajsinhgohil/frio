<?php
/**
 * FrioFront - Dynamic Product Catalog
 */
require_once 'config.php';

$settings   = api_fetch('settings.php') ?? [];
$categories = api_fetch('categories.php') ?? [];
$products   = api_fetch('products.php')   ?? [];
$catalogues = api_fetch('catalogues.php') ?? [];

$active_category = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? intval($_GET['category_id']) : 0;

$page_title  = 'Product Catalog | FRIO Industrial';
$meta_desc   = 'Browse the complete FRIO precision brass fittings product catalog. Filter by category, search by product code.';
$active_page = 'product';

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
            <span class="text-[#ffe088] font-bold">Products</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 font-headline-lg tracking-tight">Our Products</h1>
        <p class="text-white/85 text-sm md:text-base max-w-2xl leading-relaxed font-medium">Explore our premium range of precision engineered brass fittings and industrial safety products</p>
    </div>
</section>

<!-- Main Content with dynamic background pattern (Item 7) -->
<main class="w-full px-4 md:px-8 py-14 bg-gradient-to-b from-[#fdfdff] via-[#f4f7ff] to-[#fdfdff]">
<div class="max-w-[1700px] mx-auto w-full flex-1 flex flex-col relative">

    <!-- Search & Sort Bar -->
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between mb-8 bg-white p-4 rounded-2xl border border-outline-variant/20 shadow-sm">
        <div class="relative flex-1 w-full">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
            <input id="product-search" type="text"
                placeholder="Search products, codes, categories..."
                class="w-full pl-12 pr-6 py-3 bg-surface-container-low border border-outline-variant/50 rounded-xl focus:ring-4 focus:ring-secondary-fixed/50 focus:border-secondary-fixed text-sm outline-none transition-all shadow-sm" />
        </div>
        <div class="flex items-center gap-3">
            <label for="sort-select" class="font-bold text-xs text-outline whitespace-nowrap">Sort by:</label>
            <select id="sort-select" class="bg-surface-container-low border border-outline-variant/50 rounded-xl px-4 py-3 font-bold text-sm focus:ring-primary focus:border-primary outline-none cursor-pointer">
                <option value="default">Default</option>
                <option value="az">Name A-Z</option>
                <option value="za">Name Z-A</option>
                <option value="code">Product Code</option>
            </select>
        </div>
        <div id="product-count" class="font-bold text-sm text-outline whitespace-nowrap">
            Showing 1–<?php echo min(9, count($products)); ?> of <?php echo count($products); ?> Products
        </div>
    </div>

    <!-- Product Grid + Sidebar -->
    <div class="flex flex-col lg:flex-row gap-8">

        <!-- Sidebar with Advanced Filters (Item 1) -->
        <aside class="w-full lg:w-64 shrink-0 space-y-6">
            
            <!-- Categories Filter -->
            <?php if (!empty($categories)): ?>
            <div class="bg-white border border-outline-variant/20 rounded-2xl p-5 shadow-sm">
                <h3 class="font-extrabold text-sm text-primary tracking-wider uppercase mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">category</span>
                    Categories
                </h3>
                <div class="space-y-1.5">
                    <button data-cat-id="0" id="sidebar-cat-0"
                        class="sidebar-cat w-full text-left px-3.5 py-2 rounded-xl font-bold text-sm transition-all border <?php echo $active_category === 0 ? 'bg-primary text-white border-primary' : 'text-on-surface-variant border-transparent hover:bg-surface-container hover:text-primary'; ?>">
                        All Products
                    </button>
                    <?php foreach ($categories as $cat): ?>
                    <button data-cat-id="<?php echo $cat['id']; ?>" id="sidebar-cat-<?php echo $cat['id']; ?>"
                        class="sidebar-cat w-full text-left px-3.5 py-2 rounded-xl font-bold text-sm transition-all border <?php echo $active_category === intval($cat['id']) ? 'bg-primary text-white border-primary' : 'text-on-surface-variant border-transparent hover:bg-surface-container hover:text-primary'; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>



        </aside>

        <!-- Product Grid -->
        <div class="flex-1">
            <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php if (!empty($products)):
                    foreach ($products as $prod): 
                        // Derive rich mock metadata for interactive filters (Item 1)
                        $p_id = intval($prod['id']);
                        $material = ($p_id % 3 === 0) ? 'brass' : (($p_id % 3 === 1) ? 'steel' : 'polymer');
                        $availability = ($p_id % 2 === 0) ? 'instock' : 'order';
                        
                        $app_arr = ['refrigeration', 'ac', 'hvac'];
                        $application = $app_arr[$p_id % count($app_arr)];

                        $size_arr = ['1/4', '3/8', '1/2', '3/4'];
                        $size = $size_arr[$p_id % count($size_arr)];

                    ?>
                    <a href="product-detail.php?id=<?php echo $prod['id']; ?>"
                       class="product-card card-lift bg-white border border-outline-variant/20 rounded-[18px] shadow-[0_8px_24px_rgba(0,0,0,0.03)] hover:shadow-[0_12px_32px_rgba(0,52,98,0.08)] transition-all overflow-hidden flex flex-col group relative cursor-pointer no-underline"
                        data-name="<?php echo strtolower(htmlspecialchars($prod['name'])); ?>"
                        data-code="<?php echo strtolower(htmlspecialchars($prod['code'] ?? '')); ?>"
                        data-category="<?php echo $prod['category_id']; ?>"
                        data-cat-name="<?php echo strtolower(htmlspecialchars($prod['category_name'] ?? '')); ?>"
                        data-material="<?php echo $material; ?>"
                        data-availability="<?php echo $availability; ?>"
                        data-application="<?php echo $application; ?>"
                        data-size="<?php echo $size; ?>">

                        <!-- Image Square Container -->
                        <div class="block aspect-square bg-surface-container p-6 overflow-hidden relative">
                            <?php if (!empty($prod['image'])): ?>
                                <img loading="lazy" src="<?php echo asset_url($prod['image']); ?>"
                                    alt="<?php echo htmlspecialchars($prod['name']); ?>"
                                    class="w-full h-full object-contain mix-blend-multiply" />
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-6xl text-outline/30">inventory_2</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="p-6 flex flex-col flex-1 relative z-20 bg-white">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h3 class="font-extrabold text-primary text-base leading-tight line-clamp-2">
                                    <?php echo htmlspecialchars($prod['name']); ?>
                                </h3>
                                <?php if (!empty($prod['code'])): ?>
                                <span class="shrink-0 text-[10px] font-bold text-secondary bg-secondary-fixed/20 px-2 py-1 rounded uppercase">
                                    <?php echo htmlspecialchars($prod['code']); ?>
                                </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($prod['category_name'])): ?>
                            <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-outline bg-surface-container px-2 py-1 rounded-full mb-4 w-fit">
                                <?php echo htmlspecialchars($prod['category_name']); ?>
                            </span>
                            <?php endif; ?>
                            <div class="border-t border-outline-variant/20 pt-4 mt-auto">
                                <span class="flex items-center gap-1.5 text-primary font-bold text-xs">
                                    View Details
                                    <span class="material-symbols-outlined text-[15px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                </span>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full flex flex-col items-center justify-center py-20 px-4 text-center bg-gradient-to-br from-surface-container-low to-white border border-outline-variant/30 rounded-3xl shadow-sm">
                        <div class="w-20 h-20 bg-primary/5 rounded-full flex items-center justify-center mb-6">
                            <span class="material-symbols-outlined text-5xl text-primary/40">inventory_2</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-primary mb-2">No Products Available</h3>
                        <p class="text-sm text-outline max-w-sm mx-auto">Products will appear here once they have been added from the secure admin console.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- No results -->
            <div id="no-results" style="display:none;" class="flex-col items-center justify-center py-20 px-4 text-center mt-8 bg-gradient-to-br from-surface-container-low to-white border border-outline-variant/30 rounded-3xl shadow-sm">
                <div class="w-20 h-20 bg-error/5 rounded-full flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-5xl text-error/40">search_off</span>
                </div>
                <h3 class="text-2xl font-extrabold text-primary mb-2">No Results Found</h3>
                <p class="text-sm text-outline mb-8 max-w-sm mx-auto">We couldn't find any products matching your current search or category filter. Try adjusting your query.</p>
                <button onclick="resetFilters()"
                    class="bg-primary text-white shadow-md shadow-primary/20 px-8 py-3 rounded-xl font-bold text-sm hover:bg-primary-container hover:scale-105 transition-all">
                    Reset All Filters
                </button>
            </div>
            
            <!-- Pagination Controls -->
            <div id="pagination-controls" class="mt-12 flex flex-wrap items-center justify-center gap-2"></div>
            
        </div>
    </div>

    <!-- Section: Why Choose Frio Section Below Products (Item 12) -->
    <section class="mt-24 border-t border-outline-variant/20 pt-16">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="inline-block text-xs font-extrabold uppercase tracking-widest text-secondary bg-secondary-fixed/30 px-3 py-1.5 rounded-full mb-3">
                Frio Quality Guarantee
            </span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-primary leading-tight">Engineered for High-Pressure Industrial Excellence</h2>
            <p class="text-sm text-outline mt-3 font-medium">Why global B2B procurement partners choose FRIO precision brass components</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Precision Engineered -->
            <div class="bg-white border border-outline-variant/15 p-8 rounded-[20px] shadow-[0_8px_24px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_32px_rgba(0,52,98,0.06)] hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center text-center group">
                <div class="w-14 h-14 bg-surface-container rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
                    <span class="material-symbols-outlined text-primary text-2xl group-hover:text-white transition-colors duration-300">architecture</span>
                </div>
                <h3 class="font-extrabold text-primary text-lg mb-2">Precision Engineered</h3>
                <p class="text-sm text-outline/80 leading-relaxed font-medium">Manufactured to strict micron-level tolerances under rigid ISO certified procedures.</p>
            </div>

            <!-- Leak Proof -->
            <div class="bg-white border border-outline-variant/15 p-8 rounded-[20px] shadow-[0_8px_24px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_32px_rgba(0,52,98,0.06)] hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center text-center group">
                <div class="w-14 h-14 bg-surface-container rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
                    <span class="material-symbols-outlined text-primary text-2xl group-hover:text-white transition-colors duration-300">verified</span>
                </div>
                <h3 class="font-extrabold text-primary text-lg mb-2">Leak Proof</h3>
                <p class="text-sm text-outline/80 leading-relaxed font-medium">100% helium and pressure-tested fittings to guarantee seamless hermetic operation.</p>
            </div>

            <!-- High Durability -->
            <div class="bg-white border border-outline-variant/15 p-8 rounded-[20px] shadow-[0_8px_24px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_32px_rgba(0,52,98,0.06)] hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center text-center group">
                <div class="w-14 h-14 bg-surface-container rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
                    <span class="material-symbols-outlined text-primary text-2xl group-hover:text-white transition-colors duration-300">shield_with_heart</span>
                </div>
                <h3 class="font-extrabold text-primary text-lg mb-2">High Durability</h3>
                <p class="text-sm text-outline/80 leading-relaxed font-medium">Forged from heavy-duty brass alloy composition for superior corrosion resistance.</p>
            </div>

            <!-- Fast Delivery -->
            <div class="bg-white border border-outline-variant/15 p-8 rounded-[20px] shadow-[0_8px_24px_rgba(0,0,0,0.02)] hover:shadow-[0_12px_32px_rgba(0,52,98,0.06)] hover:-translate-y-1.5 transition-all duration-300 flex flex-col items-center text-center group">
                <div class="w-14 h-14 bg-surface-container rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors duration-300">
                    <span class="material-symbols-outlined text-primary text-2xl group-hover:text-white transition-colors duration-300">local_shipping</span>
                </div>
                <h3 class="font-extrabold text-primary text-lg mb-2">Fast Delivery</h3>
                <p class="text-sm text-outline/80 leading-relaxed font-medium">Vast stock capacities and streamlined global distribution systems ensure quick turnaround times.</p>
            </div>
        </div>
    </section>

    <!-- Section: Bulk Inquiry CTA Block (Item 13) -->
    <section class="mt-20">
        <div class="relative overflow-hidden bg-primary rounded-3xl p-10 md:p-14 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-8">
            <!-- Abstract brass glowing lines overlay -->
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_120%,rgba(254,224,136,0.12),transparent_40%)] pointer-events-none"></div>
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-secondary-fixed/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-3 text-center md:text-left max-w-xl">
                <h2 class="text-3xl md:text-4xl font-extrabold text-white leading-tight">Need Bulk Brass Fittings?</h2>
                <p class="text-white/85 text-base md:text-lg font-medium">Get custom OEM/ODM manufacturing solutions tailored perfectly to your industrial requirements.</p>
            </div>
            
            <div class="relative z-10 shrink-0">
                <a href="contact.php" class="inline-flex items-center gap-2 bg-secondary-container text-on-secondary-container hover:bg-secondary hover:text-white font-extrabold text-base px-8 py-4 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    <span class="material-symbols-outlined text-[20px]">send</span>
                    Send Bulk Inquiry
                </a>
            </div>
        </div>
    </section>

</main>

<!-- Sticky Rotated B2B Floating Download Catalogue Button (Item 10) -->
<?php if (!empty($catalogues)): ?>
<a href="catalogue.php?autostart=1&pdf=<?php echo urlencode(asset_url($catalogues[0]['pdf_file'])); ?>&name=<?php echo urlencode($catalogues[0]['name'] ?? 'Technical Catalogue'); ?>&action=download"
   class="fixed right-0 top-1/2 -translate-y-1/2 z-40 bg-secondary text-white font-extrabold text-[11px] uppercase tracking-wider px-3.5 py-4 rounded-l-2xl shadow-[0_10px_30px_rgba(115,92,0,0.2)] hover:bg-primary hover:text-white hover:-translate-x-1 transition-all duration-300 flex items-center gap-2 [writing-mode:vertical-lr] border-l border-y border-white/20 select-none">
   <span class="material-symbols-outlined text-[16px] -rotate-90">menu_book</span>
   Download Catalogue
</a>
<?php endif; ?>

<script>
let currentCategory = <?php echo $active_category; ?>;
let currentSearch   = '';
let currentSort     = 'default';
let currentPage     = 1;
const itemsPerPage  = 9;

function applyFilters(resetPage = true) {
    if (resetPage) currentPage = 1;
    const cards      = Array.from(document.querySelectorAll('.product-card'));
    const searchTerm = currentSearch.toLowerCase().trim();
    let visibleCards = [];

    cards.forEach(card => {
        const catMatch    = currentCategory === 0 || parseInt(card.dataset.category) === currentCategory;
        const searchMatch = !searchTerm || card.dataset.name.includes(searchTerm) || card.dataset.code.includes(searchTerm) || (card.dataset.catName||'').includes(searchTerm);

        if (catMatch && searchMatch) {
            visibleCards.push(card);
        }
        card.style.display = 'none'; // hide everything initially
    });

    if (currentSort === 'az')        visibleCards.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name));
    else if (currentSort === 'za')   visibleCards.sort((a, b) => b.dataset.name.localeCompare(a.dataset.name));
    else if (currentSort === 'code') visibleCards.sort((a, b) => a.dataset.code.localeCompare(b.dataset.code));
    else                             visibleCards.sort((a, b) => parseInt(a.dataset.index) - parseInt(b.dataset.index));

    const grid = document.getElementById('product-grid');
    visibleCards.forEach(card => grid.appendChild(card));

    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex   = startIndex + itemsPerPage;
    const pageCards  = visibleCards.slice(startIndex, endIndex);

    pageCards.forEach(card => card.style.display = '');

    // Mathematical count format (Item 5)
    const visibleCount = visibleCards.length;
    if (visibleCount === 0) {
        document.getElementById('product-count').textContent = 'Showing 0–0 of 0 Products';
    } else {
        const startNum = startIndex + 1;
        const endNum = Math.min(endIndex, visibleCount);
        document.getElementById('product-count').textContent = `Showing ${startNum}–${endNum} of ${visibleCount} Products`;
    }

    const noRes = document.getElementById('no-results');
    noRes.style.display = visibleCount === 0 ? 'flex' : 'none';

    renderPagination(visibleCount);
}

function renderPagination(totalItems) {
    const totalPages = Math.max(1, Math.ceil(totalItems / itemsPerPage));
    const pagContainer = document.getElementById('pagination-controls');
    pagContainer.innerHTML = '';

    const prevBtn = document.createElement('button');
    prevBtn.innerHTML = '<span class="material-symbols-outlined text-[18px]">chevron_left</span> <span class="text-xs font-extrabold ml-1">Previous</span>';
    prevBtn.className = `px-4 h-10 flex items-center justify-center rounded-xl font-bold transition-all ${currentPage === 1 ? 'text-outline-variant bg-surface-container/50 cursor-not-allowed opacity-50' : 'text-primary bg-white border border-outline-variant/30 hover:bg-primary hover:text-white'}`;
    prevBtn.disabled = currentPage === 1;
    prevBtn.onclick = () => { if(currentPage > 1) { currentPage--; applyFilters(false); window.scrollTo({top: 0, behavior: 'smooth'}); } };
    pagContainer.appendChild(prevBtn);

    for (let i = 1; i <= totalPages; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.innerText = i;
        pageBtn.className = `w-10 h-10 flex items-center justify-center rounded-xl font-bold transition-all ${currentPage === i ? 'bg-primary text-white shadow-md' : 'text-primary bg-white border border-[#e2e8f0] hover:bg-primary/10'}`;
        pageBtn.onclick = () => { currentPage = i; applyFilters(false); window.scrollTo({top: 0, behavior: 'smooth'}); };
        pagContainer.appendChild(pageBtn);
    }

    const nextBtn = document.createElement('button');
    nextBtn.innerHTML = '<span class="text-xs font-extrabold mr-1">Next</span> <span class="material-symbols-outlined text-[18px]">chevron_right</span>';
    nextBtn.className = `px-4 h-10 flex items-center justify-center rounded-xl font-bold transition-all ${currentPage === totalPages ? 'text-outline-variant bg-surface-container/50 cursor-not-allowed opacity-50' : 'text-primary bg-white border border-outline-variant/30 hover:bg-primary hover:text-white'}`;
    nextBtn.disabled = currentPage === totalPages;
    nextBtn.onclick = () => { if(currentPage < totalPages) { currentPage++; applyFilters(false); window.scrollTo({top: 0, behavior: 'smooth'}); } };
    pagContainer.appendChild(nextBtn);
}

function setActiveCategory(catId) {
    currentCategory = catId;
    document.querySelectorAll('.sidebar-cat').forEach(btn => {
        if (parseInt(btn.dataset.catId) === catId) {
            btn.classList.add('bg-primary','text-white','border-primary');
            btn.classList.remove('text-on-surface-variant','border-transparent');
        } else {
            btn.classList.remove('bg-primary','text-white','border-primary');
            btn.classList.add('text-on-surface-variant','border-transparent');
        }
    });
    applyFilters();
}

function resetFilters() {
    currentCategory = 0; currentSearch = ''; currentSort = 'default';
    document.getElementById('product-search').value = '';
    document.getElementById('sort-select').value    = 'default';
    setActiveCategory(0);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.product-card').forEach((card, i) => card.dataset.index = i);
    document.querySelectorAll('.sidebar-cat').forEach(btn => btn.addEventListener('click', () => setActiveCategory(parseInt(btn.dataset.catId))));
    let t;
    document.getElementById('product-search').addEventListener('input', e => { clearTimeout(t); t = setTimeout(() => { currentSearch = e.target.value; applyFilters(); }, 200); });
    document.getElementById('sort-select').addEventListener('change', e => { currentSort = e.target.value; applyFilters(); });
    applyFilters();
});
</script>

<?php include 'includes/footer.php'; ?>
