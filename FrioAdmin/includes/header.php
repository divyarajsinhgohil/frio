<?php
/**
 * FRIO Admin Console - Shared Header Component
 * Contains the search bar, current date, and dynamic profile info.
 */
if (!isset($header_title)) {
    $header_title = 'Dashboard';
}
?>
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-40 bg-white/70 dark:bg-surface-container/70 backdrop-blur-md flex justify-between items-center h-16 px-gutter shadow-sm border-b border-white/10">
    <div class="flex items-center gap-8">
        <?php if (isset($breadcrumbs_html)): ?>
            <?php echo $breadcrumbs_html; ?>
        <?php else: ?>
            <h2 class="text-headline-md font-headline-md text-primary"><?php echo htmlspecialchars($header_title); ?></h2>
        <?php endif; ?>
        
        <!-- Top Search Bar -->
        <div class="relative group hidden md:block" id="global-search-container">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input id="global-search-input" class="pl-10 pr-4 py-2 bg-surface-container-low rounded-full border-none focus:ring-2 focus:ring-primary/20 w-80 text-label-sm transition-all duration-300" placeholder="Search categories, products, banners..." type="text" autocomplete="off"/>
            
            <!-- Search Results Dropdown -->
            <div id="global-search-results" class="hidden absolute left-0 mt-2 w-96 bg-white dark:bg-surface-container rounded-2xl shadow-[0px_4px_40px_rgba(0,0,0,0.12)] border border-outline-variant/30 py-3 z-50 max-h-[450px] overflow-y-auto">
                <div class="px-4 py-1 text-xs font-bold text-outline uppercase tracking-wider mb-2" id="search-status">Type to search...</div>
                <div id="search-results-content" class="space-y-4"></div>
            </div>
        </div>
    </div>
    
    <div class="flex items-center gap-6">
        <!-- Date Display -->
        <div class="hidden lg:flex items-center gap-2 text-outline">
            <span class="material-symbols-outlined text-[18px]">calendar_today</span>
            <span class="text-label-sm"><?php echo date("F d, Y"); ?></span>
        </div>
        
        <div class="flex items-center gap-4">
            <button class="p-2 hover:bg-surface-container-low rounded-full transition-colors">
                <span class="material-symbols-outlined text-on-surface-variant">apps</span>
            </button>
            
            <div class="h-8 w-px bg-outline-variant/30 mx-1"></div>
            
            <!-- User Profile Avatar & Session Name -->
            <div class="flex items-center gap-3 pl-2">
                <div class="text-right hidden sm:block">
                    <p class="text-label-bold text-on-surface leading-none font-bold"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                    <p class="text-[10px] text-outline uppercase tracking-tighter">Super Admin</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-white text-[20px]">person</span>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Global search AJAX logic
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('global-search-input');
    const searchResults = document.getElementById('global-search-results');
    const resultsContent = document.getElementById('search-results-content');
    const searchStatus = document.getElementById('search-status');
    
    // Close search dropdown on document click
    document.addEventListener('click', (e) => {
        if (searchResults && !e.target.closest('#global-search-container')) {
            searchResults.classList.add('hidden');
        }
    });
    
    if (searchInput) {
        let debounceTimer;
        
        searchInput.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            const val = e.target.value.trim();
            
            if (val.length < 1) {
                searchResults.classList.add('hidden');
                return;
            }
            
            searchResults.classList.remove('hidden');
            searchStatus.textContent = 'Searching...';
            resultsContent.innerHTML = '';
            
            debounceTimer = setTimeout(() => {
                fetch(`<?php echo $base_path; ?>api/admin_api/search.php?q=${encodeURIComponent(val)}`)
                    .then(res => res.json())
                    .then(data => {
                        searchStatus.textContent = 'Search Results';
                        let hasResults = false;
                        let html = '';
                        
                        const basePath = '<?php echo $base_path; ?>';
                        
                        // 1. Categories
                        if (data.categories && data.categories.length > 0) {
                            hasResults = true;
                            html += `<div>
                                <div class="px-4 py-1.5 text-[10px] font-bold text-primary uppercase tracking-wider bg-primary/5">Categories</div>
                                <div class="divide-y divide-outline-variant/10">`;
                            data.categories.forEach(cat => {
                                html += `<a href="${basePath}category/edit.php?id=${cat.id}" class="block px-4 py-2 hover:bg-surface-container-low transition-colors">
                                    <div class="text-xs font-bold text-on-surface">${cat.name}</div>
                                    <div class="text-[9px] text-outline uppercase tracking-wider">${cat.code}</div>
                                </a>`;
                            });
                            html += `</div></div>`;
                        }
                        
                        // 2. Products
                        if (data.products && data.products.length > 0) {
                            hasResults = true;
                            html += `<div>
                                <div class="px-4 py-1.5 text-[10px] font-bold text-secondary uppercase tracking-wider bg-secondary/5 mt-2">Products</div>
                                <div class="divide-y divide-outline-variant/10">`;
                            data.products.forEach(prod => {
                                html += `<a href="${basePath}product/edit.php?id=${prod.id}" class="block px-4 py-2 hover:bg-surface-container-low transition-colors">
                                    <div class="text-xs font-bold text-on-surface">${prod.name}</div>
                                    <div class="text-[9px] text-outline uppercase tracking-wider">${prod.code}</div>
                                </a>`;
                            });
                            html += `</div></div>`;
                        }
                        
                        // 3. Banners
                        if (data.banners && data.banners.length > 0) {
                            hasResults = true;
                            html += `<div>
                                <div class="px-4 py-1.5 text-[10px] font-bold text-success uppercase tracking-wider bg-success/5 mt-2">Banners</div>
                                <div class="divide-y divide-outline-variant/10">`;
                            data.banners.forEach(ban => {
                                html += `<a href="${basePath}banner/edit.php?id=${ban.id}" class="block px-4 py-2 hover:bg-surface-container-low transition-colors">
                                    <div class="text-xs font-bold text-on-surface">${ban.name}</div>
                                    <div class="text-[9px] text-outline">Slider Banner Campaign</div>
                                </a>`;
                            });
                            html += `</div></div>`;
                        }
                        
                        // 4. Catalogues / Sub Categories
                        if (data.catalogues && data.catalogues.length > 0) {
                            hasResults = true;
                            html += `<div>
                                <div class="px-4 py-1.5 text-[10px] font-bold text-error uppercase tracking-wider bg-error/5 mt-2">Brochures & Catalogues</div>
                                <div class="divide-y divide-outline-variant/10">`;
                            data.catalogues.forEach(cat => {
                                html += `<a href="${basePath}catalogue/edit.php?id=${cat.id}" class="block px-4 py-2 hover:bg-surface-container-low transition-colors">
                                    <div class="text-xs font-bold text-on-surface">${cat.name}</div>
                                    <div class="text-[9px] text-outline">B2B Gate Brochure PDF</div>
                                </a>`;
                            });
                            html += `</div></div>`;
                        }
                        
                        if (!hasResults) {
                            resultsContent.innerHTML = `<div class="px-4 py-6 text-center text-outline text-xs">
                                <span class="material-symbols-outlined text-3xl mb-1 text-outline/30 block">search_off</span>
                                No matching records found.
                            </div>`;
                        } else {
                            resultsContent.innerHTML = html;
                        }
                    })
                    .catch(err => {
                        searchStatus.textContent = 'Error searching';
                        resultsContent.innerHTML = `<div class="px-4 py-4 text-xs text-error text-center">Failed to fetch search results.</div>`;
                    });
            }, 300);
        });
    }
});
</script>
