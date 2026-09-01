<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

// Fetch banners from database table banner_slider
try {
    $banners = $pdo->query("SELECT * FROM `banner_slider` ORDER BY `display_order` ASC, `id` DESC")->fetchAll();
} catch (Exception $e) {
    $banners = [];
}
?>
<?php
$page_title = "FRIO | Banner Management";
include $base_path . 'includes/head.php';
?>
<body class="bg-background text-on-surface font-body-md selection:bg-primary/20">
<?php include $base_path . 'includes/sidebar.php'; ?>

<!-- Main Content Area -->
<main class="ml-64 min-h-screen flex flex-col">
<?php
$header_title = 'Banner Slider';
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
        <span class="text-primary font-bold">Banner Slider</span>
    </nav>

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h2 class="font-headline-xl text-headline-xl text-primary tracking-tight">BANNER SLIDER</h2>
            <p class="text-body-lg text-on-surface-variant mt-1">Manage home promo sliders, descriptions, buttons, and display orders.</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Dynamic Bulk Actions Button -->
            <button id="bulkDeleteBtn" onclick="submitBulkDelete()" class="bg-error text-white hover:bg-error/95 flex items-center gap-2 px-6 py-3 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-error/20 hover:-translate-y-0.5 transition-all active:scale-95 hidden">
                <span class="material-symbols-outlined text-[20px]">delete_sweep</span>
                DELETE SELECTED (<span id="selectedCount">0</span>)
            </button>
            <a href="add.php" class="bg-primary text-on-primary flex items-center gap-2 px-6 py-3 rounded-xl font-label-bold text-label-bold shadow-lg hover:shadow-primary/20 hover:-translate-y-0.5 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                ADD BANNER
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-card rounded-2xl p-4 mb-6 flex items-center justify-between shadow-sm border border-white/40">
        <div class="flex items-center gap-4 flex-grow">
            <div class="relative w-full max-w-md">
                <input class="w-full bg-white/50 border-outline-variant rounded-xl py-3 pl-12 focus:ring-primary focus:border-primary text-body-md transition-all" id="bannerSearch" autocomplete="off" placeholder="Search banner..." type="text"/>
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

    <!-- Banner Table -->
    <div class="overflow-x-auto overflow-y-hidden glass-card rounded-3xl border border-white/40 shadow-xl">
        <table class="w-full text-left border-collapse">
            <thead class="bg-primary text-on-primary">
                <tr>
                    <th class="py-5 px-4 font-label-bold text-label-bold uppercase tracking-wider w-12 text-center">
                        <input type="checkbox" id="selectAllCheckbox" class="rounded border-outline-variant/60 text-secondary focus:ring-secondary/20 cursor-pointer w-4 h-4 bg-white/50" />
                    </th>
                    <th class="py-5 px-4 font-label-bold text-label-bold uppercase tracking-wider w-12 text-center"><span class="material-symbols-outlined text-[20px] text-white/50">drag_indicator</span></th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider">Banner Image</th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn(3, 'string')">
                        <div class="flex items-center gap-1">Details <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                    </th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider cursor-pointer hover:bg-white/10 transition-colors" onclick="sortColumn(4, 'string')">
                        <div class="flex items-center gap-1">Status <span class="material-symbols-outlined text-[14px]">swap_vert</span></div>
                    </th>
                    <th class="py-5 px-8 font-label-bold text-label-bold uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-highest" id="bannerTableBody">
                <?php if (empty($banners)): ?>
                    <tr>
                        <td colspan="7" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl mb-2 block text-outline">view_carousel</span>
                            No banners found in the database. Click "Add Banner" to create one.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($banners as $b): ?>
                        <tr class="hover:bg-primary/5 transition-colors group banner-row cursor-grab active:cursor-grabbing" data-id="<?php echo $b['id']; ?>" data-status="<?php echo $b['active'] ? 'ACTIVE' : 'INACTIVE'; ?>">
                            <td class="py-6 px-4 text-center w-12">
                                <input type="checkbox" value="<?php echo $b['id']; ?>" class="banner-select-checkbox rounded border-outline-variant/60 text-secondary focus:ring-secondary/20 cursor-pointer w-4 h-4 bg-white/50" onclick="updateBulkDeleteState()" />
                            </td>
                            <td class="py-6 px-4 text-center text-outline/40 w-12">
                                <span class="material-symbols-outlined text-[20px] handle select-none cursor-grab active:cursor-grabbing">drag_indicator</span>
                            </td>
                                                        <td class="py-6 px-8">
                                <div class="w-28 h-14 rounded-xl overflow-hidden bg-white border border-outline-variant shadow-sm group-hover:scale-105 transition-transform flex items-center justify-center">
                                    <?php 
                                    $img_src = htmlspecialchars($b['image']);
                                    if (strpos($b['image'], 'http://') !== 0 && strpos($b['image'], 'https://') !== 0) {
                                        $img_src = $base_path . htmlspecialchars($b['image']);
                                    }
                                    ?>
                                    <img alt="<?php echo htmlspecialchars($b['name']); ?>" class="w-full h-full object-cover" src="<?php echo $img_src; ?>"/>
                                </div>
                            </td>
                            <td class="py-6 px-8">
                                <span class="font-headline-md text-primary block"><?php echo htmlspecialchars($b['name']); ?></span>
                                <?php if (!empty($b['button_link'])): ?>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-secondary bg-secondary/5 px-2 py-0.5 rounded border border-secondary/10">
                                        <span class="material-symbols-outlined text-[12px]">link</span>
                                        <?php echo htmlspecialchars($b['button_link']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                                                        <td class="py-6 px-8">
                                <?php if ($b['active']): ?>
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
                                    <a href="edit.php?id=<?php echo $b['id']; ?>" class="p-2 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-all" aria-label="Edit <?php echo htmlspecialchars($b['name']); ?>"><span class="material-symbols-outlined text-[20px]">edit</span></a>
                                    <button onclick="confirmDelete(<?php echo $b['id']; ?>, '<?php echo htmlspecialchars(addslashes($b['name'])); ?>')" class="p-2 border border-error text-error rounded-lg hover:bg-error hover:text-white transition-all">
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
            <p class="text-label-sm text-on-surface-variant">Total: <span class="font-bold text-primary" id="rowCount"><?php echo count($banners); ?></span> Banners</p>
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
    // Real-time local search and status filtering
    const searchInput = document.getElementById('bannerSearch');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.banner-row');
    const rowCountText = document.getElementById('rowCount');

    function filterBanners() {
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
        const tbody = document.getElementById('bannerTableBody');
        const rowsArray = Array.from(tbody.querySelectorAll('tr.banner-row'));
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

    if (searchInput) searchInput.addEventListener('input', filterBanners);
    if (statusFilter) statusFilter.addEventListener('change', filterBanners);

    function clearSearch() {
        searchInput.value = '';
        statusFilter.value = 'ALL';
        filterBanners();
    }

    // Secure Delete Confirmation
    function confirmDelete(id, name) {
        if (confirm(`Are you absolutely sure you want to delete the banner slider "${name}"?\nThis action cannot be undone.`)) {
            window.location.href = `delete.php?id=${id}`;
        }
    }

    // Checkbox toggling and state management for bulk delete
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.banner-select-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', (e) => {
            const isChecked = e.target.checked;
            rowCheckboxes.forEach(cb => {
                // Only select visible check boxes
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
            updateBulkDeleteState();
        });
    }

    function updateBulkDeleteState() {
        const checkedCount = document.querySelectorAll('.banner-select-checkbox:checked').length;
        selectedCount.textContent = checkedCount;

        if (checkedCount > 0) {
            bulkDeleteBtn.classList.remove('hidden');
        } else {
            bulkDeleteBtn.classList.add('hidden');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }
    }

    function submitBulkDelete() {
        const selectedCheckboxes = document.querySelectorAll('.banner-select-checkbox:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

        if (selectedIds.length > 0) {
            if (confirm(`Are you absolutely sure you want to permanently delete all ${selectedIds.length} selected banners?\nThis action will permanently remove them and delete their files.`)) {
                window.location.href = `delete.php?ids=${selectedIds.join(',')}`;
            }
        }
    }

    
    // Remember search state
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('bannerSearch');
        if (searchInput) {
            // Load from session storage
            const savedSearch = sessionStorage.getItem('bannerSearch_value');
            if (savedSearch !== null) {
                searchInput.value = savedSearch;
            }
            
            // Apply filter immediately on load
            if (typeof filterBanners === 'function') {
                filterBanners();
            }

            // Save on input
            searchInput.addEventListener('input', function() {
                sessionStorage.setItem('bannerSearch_value', this.value);
            });
        }
        
        // Hook into the clear button (restart_alt) to also clear session storage
        const clearBtn = document.querySelector('button[onclick="clearFilters()"]');
        if (clearBtn) {
            const oldOnclick = clearBtn.onclick;
            clearBtn.onclick = function(e) {
                sessionStorage.removeItem('bannerSearch_value');
                if (oldOnclick) oldOnclick.call(this, e);
            };
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tbody = document.getElementById('bannerTableBody');
        if (tbody) {
            new Sortable(tbody, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'bg-primary/10',
                onEnd: function (evt) {
                    const rows = tbody.querySelectorAll('tr[data-id]');
                    const formData = new FormData();
                    rows.forEach(row => {
                        formData.append('bannerIds[]', row.getAttribute('data-id'));
                    });
                    
                    fetch('update_order.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(!data.success) {
                            console.error('Failed to update order');
                        }
                    })
                    .catch(err => console.error(err));
                }
            });
        }
    });
</script>
</body></html>
