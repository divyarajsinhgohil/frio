<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

// Fetch catalogues from database
try {
    $catalogues = $pdo->query("SELECT * FROM `catalogue` ORDER BY `display_order` ASC, `id` DESC")->fetchAll();
} catch (Exception $e) {
    $catalogues = [];
}
?>
<?php
$page_title = "FRIO | Catalogue Management";
include $base_path . 'includes/head.php';
?>
<body class="bg-background text-on-surface font-body-md selection:bg-primary/20">
<?php include $base_path . 'includes/sidebar.php'; ?>

<!-- Main Content Area -->
<main class="ml-64 min-h-screen flex flex-col">
<?php
$header_title = 'Catalogue';
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
        <span class="text-primary font-bold">Catalogue</span>
    </nav>

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-primary tracking-tight">CATALOGUE</h2>
            <p class="text-body-lg text-on-surface-variant mt-1">Manage corporate technical brochures, PDFs, and cover preview images.</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Dynamic Bulk Actions Button -->
            <button id="bulkDeleteBtn" onclick="submitBulkDelete()" class="bg-error text-white hover:bg-error/95 flex items-center gap-2 px-6 py-3 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-error/20 hover:-translate-y-0.5 transition-all active:scale-95 hidden">
                <span class="material-symbols-outlined text-[20px]">delete_sweep</span>
                DELETE SELECTED (<span id="selectedCount">0</span>)
            </button>
            <a href="add.php" class="bg-primary text-on-primary flex items-center gap-2 px-6 py-3 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-primary/20 hover:-translate-y-0.5 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                ADD CATALOGUE
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-card rounded-2xl p-4 mb-6 flex items-center justify-between shadow-sm border border-white/40">
        <div class="flex items-center gap-4 flex-grow">
            <div class="relative w-full max-w-md">
                <input class="w-full bg-white/50 border-outline-variant rounded-xl py-3 pl-12 focus:ring-primary focus:border-primary text-body-md transition-all" id="catalogueSearch" autocomplete="off" placeholder="Search catalogue..." type="text"/>
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            </div>
            <button class="p-3 text-on-surface-variant hover:bg-white/50 rounded-xl transition-all border border-transparent hover:border-outline-variant group" onclick="clearSearch()">
                <span class="material-symbols-outlined group-active:rotate-180 transition-transform">restart_alt</span>
            </button>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-label-sm text-on-surface-variant mr-2">Filter by:</span>
            <select class="bg-white/50 border-outline-variant rounded-lg text-label-bold py-2 focus:ring-primary" id="statusFilter">
                <option value="ALL">All Status</option>
                <option value="ACTIVE">Active</option>
                <option value="INACTIVE">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Catalogue Table -->
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
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider">Cover Preview</th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn(3, 'string')">
                        <div class="flex items-center gap-1">Catalogue Details <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                    </th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn(4, 'number')">
                        <div class="flex items-center gap-1">Display Order <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                    </th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn(5, 'string')">
                        <div class="flex items-center gap-1">Status <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                    </th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-highest" id="catalogueTableBody">
                <?php if (empty($catalogues)): ?>
                    <tr>
                        <td colspan="7" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl mb-2 block text-outline">menu_book</span>
                            No catalogues found in the database. Click "Add Catalogue" to create one.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($catalogues as $c): ?>
                        <tr class="hover:bg-primary/5 transition-colors group catalogue-row" data-status="<?php echo $c['active'] ? 'ACTIVE' : 'INACTIVE'; ?>">
                            <td class="py-6 px-4 text-center w-12">
                                <input type="checkbox" value="<?php echo $c['id']; ?>" class="catalogue-select-checkbox rounded border-outline-variant/60 text-secondary focus:ring-secondary/20 cursor-pointer w-4 h-4 bg-white/50" onclick="updateBulkDeleteState()" />
                            </td>
                            <td class="py-6 px-8 text-label-bold text-on-surface-variant">#<?php echo htmlspecialchars($c['id']); ?></td>
                            <td class="py-6 px-8">
                                <div class="w-16 h-20 rounded-xl overflow-hidden bg-white border border-outline-variant shadow-sm group-hover:scale-105 transition-transform flex items-center justify-center">
                                    <?php 
                                    $img_src = htmlspecialchars($c['preview_image']);
                                    if (strpos($c['preview_image'], 'http://') !== 0 && strpos($c['preview_image'], 'https://') !== 0) {
                                        $img_src = $base_path . htmlspecialchars($c['preview_image']);
                                    }
                                    ?>
                                    <img alt="<?php echo htmlspecialchars($c['name']); ?>" class="w-full h-full object-cover" src="<?php echo $img_src; ?>"/>
                                </div>
                            </td>
                            <td class="py-6 px-8">
                                <span class="font-headline-md text-primary block mb-1"><?php echo htmlspecialchars($c['name']); ?></span>
                                <a href="<?php echo $base_path . htmlspecialchars($c['pdf_file']); ?>" download target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-error/10 hover:bg-error/20 text-error text-[12px] font-bold transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                                    DOWNLOAD PDF
                                </a>
                            </td>
                            <td class="py-6 px-8 text-label-bold text-primary font-bold"><?php echo htmlspecialchars($c['display_order']); ?></td>
                            <td class="py-6 px-8">
                                <?php if ($c['active']): ?>
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
                                    <a href="edit.php?id=<?php echo $c['id']; ?>" class="p-2 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-all" aria-label="Edit <?php echo htmlspecialchars($c['name']); ?>"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                                    <button onclick="confirmDelete(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars(addslashes($c['name'])); ?>')" class="p-2 border border-error text-error rounded-lg hover:bg-error hover:text-white transition-all">
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
            <p class="text-label-sm text-on-surface-variant">Total: <span class="font-bold text-primary" id="rowCount"><?php echo count($catalogues); ?></span> Catalogues</p>
            <div class="flex items-center gap-2">
                <button class="px-4 py-2 rounded-lg text-label-bold text-on-surface-variant hover:bg-white transition-all disabled:opacity-50" disabled="">Previous</button>
                <button class="w-10 h-10 rounded-lg bg-primary text-on-primary font-bold shadow-md">1</button>
                <button class="px-4 py-2 rounded-lg text-label-bold text-primary hover:bg-white transition-all disabled:opacity-50" disabled="">Next</button>
            </div>
        </div>
    </div>
</div>
<?php include $base_path . 'includes/footer.php'; ?>
</main>

<script>
    // Real-time search and status filtering
    const searchInput = document.getElementById('catalogueSearch');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.catalogue-row');
    const rowCountText = document.getElementById('rowCount');

    function filterCatalogue() {
        const term = searchInput.value.toLowerCase();
        const selectedStatus = statusFilter.value;
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const status = row.getAttribute('data-status');

            const matchesSearch = text.includes(term);
            const matchesStatus = (selectedStatus === 'ALL' || status === selectedStatus);

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        rowCountText.textContent = visibleCount;
    }

    // Table Sorting Logic
    let currentSortCol = -1;
    let currentSortAsc = true;

    function sortColumn(colIndex, type) {
        const tbody = document.getElementById('catalogueTableBody');
        const rowsArray = Array.from(tbody.querySelectorAll('tr.catalogue-row'));
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
    }

    if (searchInput) searchInput.addEventListener('input', filterCatalogue);
    if (statusFilter) statusFilter.addEventListener('change', filterCatalogue);

    function clearSearch() {
        searchInput.value = '';
        statusFilter.value = 'ALL';
        filterCatalogue();
    }

    // Secure Delete Confirmation
    function confirmDelete(id, name) {
        if (confirm(`Are you absolutely sure you want to delete the brochure catalogue "${name}"?\nThis action will permanently delete it and its files.`)) {
            window.location.href = `delete.php?id=${id}`;
        }
    }

    // Checkbox toggling and state management for bulk delete
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.catalogue-select-checkbox');
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
        const checkedCount = document.querySelectorAll('.catalogue-select-checkbox:checked').length;
        selectedCount.textContent = checkedCount;

        if (checkedCount > 0) {
            bulkDeleteBtn.classList.remove('hidden');
        } else {
            bulkDeleteBtn.classList.add('hidden');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }
    }

    function submitBulkDelete() {
        const selectedCheckboxes = document.querySelectorAll('.catalogue-select-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        if (selectedIds.length > 0) {
            if (confirm(`Are you absolutely sure you want to permanently delete all ${selectedIds.length} selected brochures?\nThis action will delete them permanently from the database and unlink their files.`)) {
                window.location.href = `delete.php?ids=${selectedIds.join(',')}`;
            }
        }
    }

    
    // Remember search state
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('catalogueSearch');
        if (searchInput) {
            // Load from session storage
            const savedSearch = sessionStorage.getItem('catalogueSearch_value');
            if (savedSearch !== null) {
                searchInput.value = savedSearch;
            }
            
            // Apply filter immediately on load
            if (typeof filterCatalogues === 'function') {
                filterCatalogues();
            }

            // Save on input
            searchInput.addEventListener('input', function() {
                sessionStorage.setItem('catalogueSearch_value', this.value);
            });
        }
        
        // Hook into the clear button (restart_alt) to also clear session storage
        const clearBtn = document.querySelector('button[onclick="clearFilters()"]');
        if (clearBtn) {
            const oldOnclick = clearBtn.onclick;
            clearBtn.onclick = function(e) {
                sessionStorage.removeItem('catalogueSearch_value');
                if (oldOnclick) oldOnclick.call(this, e);
            };
        }
    });
</script>
</body></html>
