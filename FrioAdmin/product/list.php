<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

// Fetch products from database along with category names and variation counts
try {
    $stmt = $pdo->query("
        SELECT p.*, c.name AS category_name,
               (SELECT COUNT(*) FROM `product_variation` WHERE `product_id` = p.id) AS variation_count
        FROM `product` p
        INNER JOIN `category` c ON p.category_id = c.id
        ORDER BY p.display_order ASC, p.id DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
}
?>
<?php
$page_title = "FRIO | Product Management";
include $base_path . 'includes/head.php';
?>
<body class="bg-background text-on-surface font-body-md selection:bg-primary/20">
<?php include $base_path . 'includes/sidebar.php'; ?>

<!-- Main Content Area -->
<main class="ml-64 min-h-screen flex flex-col">
<?php
$header_title = 'Product'; // Label this cleanly
include $base_path . 'includes/header.php';
?>
<!-- Canvas Area -->
<div class="pt-24 pb-12 px-gutter flex-grow max-w-screen-2xl mx-auto w-full">
    <!-- Breadcrumbs in Canvas -->
    <nav class="flex items-center text-label-sm text-on-surface-variant mb-6">
        <a href="<?php echo $base_path; ?>dashbord.php" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">dashboard</span>
            <span>Dashboard</span>
        </a>
        <span class="material-symbols-outlined text-[16px] mx-2">chevron_right</span>
        <span class="text-primary font-bold">Product</span>
    </nav>

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-primary tracking-tight">PRODUCT LIST</h2>
            <p class="text-body-lg text-on-surface-variant mt-1">Manage industrial hardware products, specifications, and dynamic size variations.</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Dynamic Bulk Actions Button -->
            <button id="bulkDeleteBtn" onclick="submitBulkDelete()" class="bg-error text-white hover:bg-error/95 flex items-center gap-2 px-6 py-3 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-error/20 hover:-translate-y-0.5 transition-all active:scale-95 hidden">
                <span class="material-symbols-outlined text-[20px]">delete_sweep</span>
                DELETE SELECTED (<span id="selectedCount">0</span>)
            </button>
            <a href="add.php" class="bg-primary text-on-primary flex items-center gap-2 px-6 py-3 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-primary/20 hover:-translate-y-0.5 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                ADD PRODUCT
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-card rounded-2xl p-4 mb-6 flex items-center justify-between shadow-sm border border-white/40">
        <div class="flex items-center gap-4 flex-grow">
            <div class="relative w-full max-w-md">
                <input class="w-full bg-white/50 border-outline-variant rounded-xl py-3 pl-12 focus:ring-primary focus:border-primary text-body-md transition-all" id="productSearch" autocomplete="off" placeholder="Search product name, code..." type="text"/>
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            </div>
            <button class="p-3 text-on-surface-variant hover:bg-white/50 rounded-xl transition-all border border-transparent hover:border-outline-variant group" onclick="clearFilters()">
                <span class="material-symbols-outlined group-active:rotate-180 transition-transform">restart_alt</span>
            </button>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-label-sm text-on-surface-variant">Category:</span>
                <select class="bg-white/50 border-outline-variant rounded-lg text-label-bold py-2 focus:ring-primary" id="categoryFilter">
                    <option value="ALL">All Categories</option>
                    <?php
                    // Get all unique categories that have products in list
                    $cats = array_unique(array_column($products, 'category_name'));
                    foreach ($cats as $c_name):
                    ?>
                        <option value="<?php echo htmlspecialchars($c_name); ?>"><?php echo htmlspecialchars($c_name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-label-sm text-on-surface-variant">Status:</span>
                <select class="bg-white/50 border-outline-variant rounded-lg text-label-bold py-2 focus:ring-primary" id="statusFilter">
                    <option value="ALL">All Status</option>
                    <option value="ACTIVE">Active</option>
                    <option value="INACTIVE">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Product Table -->
    <div class="overflow-x-auto overflow-y-hidden glass-card rounded-3xl border border-white/40 shadow-xl">
        <table class="w-full text-left border-collapse">
            <thead class="bg-primary text-on-primary">
                <tr>
                    <th class="py-5 px-4 font-label-bold text-label-bold uppercase tracking-wider w-12 text-center">
                        <input type="checkbox" id="selectAllCheckbox" class="rounded border-outline-variant/60 text-secondary focus:ring-secondary/20 cursor-pointer w-4 h-4 bg-white/50" />
                    </th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn(1, 'number')">
                        <div class="flex items-center gap-1">ID <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                    </th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider">Image</th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn(3, 'string')">
                        <div class="flex items-center gap-1">Product Info <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                    </th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider">Variations</th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn(5, 'number')">
                        <div class="flex items-center gap-1">Display Order <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                    </th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn(6, 'string')">
                        <div class="flex items-center gap-1">Status <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                    </th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-highest" id="productTableBody">
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl mb-2 block text-outline">inventory</span>
                            No products found in the database. Click "Add Product" to create one.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $prod): ?>
                        <tr class="hover:bg-primary/5 transition-colors group product-row" data-category="<?php echo htmlspecialchars($prod['category_name']); ?>" data-status="<?php echo $prod['active'] ? 'ACTIVE' : 'INACTIVE'; ?>">
                            <td class="py-6 px-4 text-center w-12">
                                <input type="checkbox" value="<?php echo $prod['id']; ?>" class="product-select-checkbox rounded border-outline-variant/60 text-secondary focus:ring-secondary/20 cursor-pointer w-4 h-4 bg-white/50" onclick="updateBulkDeleteState()" />
                            </td>
                            <td class="py-6 px-8 text-label-bold text-on-surface-variant">#<?php echo htmlspecialchars($prod['id']); ?></td>
                            <td class="py-6 px-8">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-white border border-outline-variant shadow-sm group-hover:scale-110 transition-transform flex items-center justify-center">
                                    <?php 
                                    $img_src = htmlspecialchars($prod['image']);
                                    if (strpos($prod['image'], 'http://') !== 0 && strpos($prod['image'], 'https://') !== 0) {
                                        $img_src = $base_path . htmlspecialchars($prod['image']);
                                    }
                                    ?>
                                    <img alt="<?php echo htmlspecialchars($prod['name']); ?>" class="w-full h-full object-cover" src="<?php echo $img_src; ?>"/>
                                </div>
                            </td>
                            <td class="py-6 px-8">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-headline-md text-primary block text-lg font-bold leading-tight"><?php echo htmlspecialchars($prod['name']); ?></span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider bg-surface-container-high text-primary border border-outline-variant/20 uppercase"><?php echo htmlspecialchars($prod['category_name']); ?></span>
                                </div>
                                <div class="text-label-sm font-semibold text-secondary uppercase tracking-wider mb-1">CODE: <?php echo htmlspecialchars($prod['code']); ?></div>
                                </td>
                            <td class="py-6 px-8">
                                <div class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-primary/5 text-primary border border-primary/10 font-label-bold text-label-sm font-bold">
                                    <span class="material-symbols-outlined text-[16px]">layers</span>
                                    <span><?php echo $prod['variation_count']; ?> Variations</span>
                                </div>
                            </td>
                            <td class="py-6 px-8 text-label-bold text-primary font-bold"><?php echo htmlspecialchars($prod['display_order']); ?></td>
                            <td class="py-6 px-8">
                                <?php if ($prod['active']): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container font-label-bold text-label-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-on-secondary-container"></span>
                                        ACTIVE
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-highest text-on-surface-variant font-label-bold text-label-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-on-surface-variant"></span>
                                        INACTIVE
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-6 px-8 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="edit.php?id=<?php echo $prod['id']; ?>" class="p-2 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-all" aria-label="Edit <?php echo htmlspecialchars($prod['name']); ?>"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                                    <button onclick="confirmDelete(<?php echo $prod['id']; ?>, '<?php echo htmlspecialchars(addslashes($prod['name'])); ?>')" class="p-2 border border-error text-error rounded-lg hover:bg-error hover:text-white transition-all">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- Pagination Footer -->
        <div class="p-6 bg-white/30 flex items-center justify-between border-t border-white/20">
            <p class="text-label-sm text-on-surface-variant">Total: <span class="font-bold text-primary" id="rowCount"><?php echo count($products); ?></span> Products</p>
            <div class="flex items-center gap-2" id="paginationControls">
            </div>
        </div>
    </div>
</div>
<?php include $base_path . 'includes/footer.php'; ?>
</main>

<script>
    // Real-time search, filter and pagination logic
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const rowCountText = document.getElementById('rowCount');

    let currentPage = 1;
    const itemsPerPage = 10;

    function filterProducts(resetPage = true) {
        if (resetPage) currentPage = 1;

        const term = searchInput.value.toLowerCase();
        const selectedCat = categoryFilter.value;
        const selectedStatus = statusFilter.value;

        // Fetch dynamically to maintain order of rows after sorting
        const currentRows = Array.from(document.querySelectorAll('.product-row'));
        let visibleRows = [];

        currentRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const category = row.getAttribute('data-category');
            const status = row.getAttribute('data-status');

            const matchesSearch = text.includes(term);
            const matchesCat = (selectedCat === 'ALL' || category === selectedCat);
            const matchesStatus = (selectedStatus === 'ALL' || status === selectedStatus);

            if (matchesSearch && matchesCat && matchesStatus) {
                visibleRows.push(row);
            } else {
                row.style.display = 'none';
            }
        });

        rowCountText.textContent = visibleRows.length;
        updatePagination(visibleRows);
    }

    function updatePagination(visibleRows) {
        const totalItems = visibleRows.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // Hide all rows in this list first, then display only current page items
        visibleRows.forEach((row, index) => {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            if (index >= start && index < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        const paginationContainer = document.getElementById('paginationControls');
        if (!paginationContainer) return;

        paginationContainer.innerHTML = '';

        // Previous Button
        const prevBtn = document.createElement('button');
        prevBtn.textContent = 'Previous';
        prevBtn.className = 'px-4 py-2 rounded-lg text-label-bold transition-all ' +
            (currentPage === 1
                ? 'text-on-surface-variant/40 bg-transparent cursor-not-allowed opacity-50'
                : 'text-primary hover:bg-white border border-outline-variant/30');
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => {
            if (currentPage > 1) {
                currentPage--;
                filterProducts(false);
            }
        };
        paginationContainer.appendChild(prevBtn);

        // Page buttons
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i;
            pageBtn.className = 'w-10 h-10 rounded-lg font-bold transition-all ' +
                (currentPage === i
                    ? 'bg-primary text-on-primary shadow-md'
                    : 'text-primary hover:bg-white border border-outline-variant/30');
            pageBtn.onclick = () => {
                currentPage = i;
                filterProducts(false);
            };
            paginationContainer.appendChild(pageBtn);
        }

        // Next Button
        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Next';
        nextBtn.className = 'px-4 py-2 rounded-lg text-label-bold transition-all ' +
            (currentPage === totalPages
                ? 'text-on-surface-variant/40 bg-transparent cursor-not-allowed opacity-50'
                : 'text-primary hover:bg-white border border-outline-variant/30');
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => {
            if (currentPage < totalPages) {
                currentPage++;
                filterProducts(false);
            }
        };
        paginationContainer.appendChild(nextBtn);
    }

    // Table Sorting Logic
    let currentSortCol = -1;
    let currentSortAsc = true;

    function sortColumn(colIndex, type) {
        const tbody = document.getElementById('productTableBody');
        const rowsArray = Array.from(tbody.querySelectorAll('tr.product-row'));
        if (rowsArray.length <= 1) return;

        if (currentSortCol === colIndex) {
            currentSortAsc = !currentSortAsc;
        } else {
            currentSortCol = colIndex;
            currentSortAsc = true;
        }

        rowsArray.sort((a, b) => {
            let aText = a.cells[colIndex].textContent.trim();
            let bText = b.cells[colIndex].textContent.trim();

            if (type === 'number') {
                aText = parseFloat(aText.replace(/[^0-9.-]+/g, '')) || 0;
                bText = parseFloat(bText.replace(/[^0-9.-]+/g, '')) || 0;
                return currentSortAsc ? aText - bText : bText - aText;
            } else {
                aText = aText.toLowerCase();
                bText = bText.toLowerCase();
                if (aText < bText) return currentSortAsc ? -1 : 1;
                if (aText > bText) return currentSortAsc ? 1 : -1;
                return 0;
            }
        });

        rowsArray.forEach(row => tbody.appendChild(row));
        
        // Refresh pagination using sorted rows and keep page index
        filterProducts(false);
    }

    if (searchInput) searchInput.addEventListener('input', () => filterProducts(true));
    if (categoryFilter) categoryFilter.addEventListener('change', () => filterProducts(true));
    if (statusFilter) statusFilter.addEventListener('change', () => filterProducts(true));

    function clearFilters() {
        searchInput.value = '';
        categoryFilter.value = 'ALL';
        statusFilter.value = 'ALL';
        filterProducts(true);
    }

    // Individual Delete Confirmation
    function confirmDelete(id, name) {
        if (confirm(`Are you absolutely sure you want to permanently delete the product "${name}" and all of its size variations?\nThis action cannot be undone.`)) {
            window.location.href = `delete.php?id=${id}`;
        }
    }

    // Checkbox and bulk delete logic
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.product-select-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', (e) => {
            const isChecked = e.target.checked;
            rowCheckboxes.forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
            updateBulkDeleteState();
        });
    }

    function updateBulkDeleteState() {
        const checkedCount = document.querySelectorAll('.product-select-checkbox:checked').length;
        selectedCount.textContent = checkedCount;

        if (checkedCount > 0) {
            bulkDeleteBtn.classList.remove('hidden');
        } else {
            bulkDeleteBtn.classList.add('hidden');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }
    }

    function submitBulkDelete() {
        const selectedCheckboxes = document.querySelectorAll('.product-select-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        if (selectedIds.length > 0) {
            if (confirm(`Are you absolutely sure you want to permanently delete all ${selectedIds.length} selected products along with all their size variations and image assets?\nThis action will completely erase them from the database.`)) {
                window.location.href = `delete.php?ids=${selectedIds.join(',')}`;
            }
        }
    }

    
    // Remember search state
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('productSearch');
        if (searchInput) {
            // Load from session storage
            const savedSearch = sessionStorage.getItem('productSearch_value');
            if (savedSearch !== null) {
                searchInput.value = savedSearch;
            }
            
            // Apply filter immediately on load
            if (typeof filterProducts === 'function') {
                filterProducts();
            }

            // Save on input
            searchInput.addEventListener('input', function() {
                sessionStorage.setItem('productSearch_value', this.value);
            });
        }
        
        // Hook into the clear button (restart_alt) to also clear session storage
        const clearBtn = document.querySelector('button[onclick="clearFilters()"]');
        if (clearBtn) {
            const oldOnclick = clearBtn.onclick;
            clearBtn.onclick = function(e) {
                sessionStorage.removeItem('productSearch_value');
                if (oldOnclick) oldOnclick.call(this, e);
            };
        }
    });
</script>
</body></html>
