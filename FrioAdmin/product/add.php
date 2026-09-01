<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

$error = "";
$message = "";

// Fetch active categories for selection dropdown
try {
    $categories = $pdo->query("SELECT * FROM `category` WHERE `active` = 1 ORDER BY `display_order` ASC, `id` DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = isset($_POST['categoryId']) ? intval($_POST['categoryId']) : 0;
    $prodName = isset($_POST['prodName']) ? trim($_POST['prodName']) : '';
    $prodDesc = isset($_POST['prodDesc']) ? trim($_POST['prodDesc']) : '';
    // Auto-generate product code securely in PHP instead of user input
    $prodCode = '#PR-' . rand(8000, 8999) . '-' . rand(10, 99);
    $displayOrder = isset($_POST['displayOrder']) ? intval($_POST['displayOrder']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1; // 1 = Active, 0 = Inactive

    $prodImg = "";

    // 1. Validate required fields
    if ($categoryId > 0 && !empty($prodName) && !empty($prodCode)) {
        try {
            // Check if product code is already in use (loop to ensure uniqueness)
            $is_unique = false;
            while (!$is_unique) {
                $chk_stmt = $pdo->prepare("SELECT COUNT(*) FROM `product` WHERE `code` = ?");
                $chk_stmt->execute([$prodCode]);
                if ($chk_stmt->fetchColumn() == 0) {
                    $is_unique = true;
                } else {
                    $prodCode = '#PR-' . rand(8000, 8999) . '-' . rand(10, 99);
                }
            }

            // 2. Handle gallery image uploads & primary image assignment
            $uploadedGalleryPaths = [];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $galleryUploadDir = $base_path . 'assets/imag/product/gallery/';

            if (empty($error)) {
                if (!is_dir($galleryUploadDir)) {
                    mkdir($galleryUploadDir, 0777, true);
                }

                // Process up to 5 slots
                for ($gi = 1; $gi <= 5; $gi++) {
                    if (isset($_FILES['galleryImg_' . $gi]) && $_FILES['galleryImg_' . $gi]['error'] === UPLOAD_ERR_OK) {
                        $gTmpPath = $_FILES['galleryImg_' . $gi]['tmp_name'];
                        $gName    = $_FILES['galleryImg_' . $gi]['name'];
                        $gExt     = strtolower(pathinfo($gName, PATHINFO_EXTENSION));
                        
                        if (in_array($gExt, $allowedExtensions)) {
                            $gNewName = 'gal_' . $gi . '_' . time() . '_' . md5(uniqid()) . '.' . $gExt;
                            $gDest    = $galleryUploadDir . $gNewName;
                            
                            if (move_uploaded_file($gTmpPath, $gDest)) {
                                $uploadedGalleryPaths[$gi] = 'assets/imag/product/gallery/' . $gNewName;
                            } else {
                                $error = "Error moving uploaded gallery image #$gi.";
                                break;
                            }
                        } else {
                            $error = "Allowed product image formats: " . implode(', ', $allowedExtensions);
                            break;
                        }
                    }
                }

                if (empty($error)) {
                    if (empty($uploadedGalleryPaths)) {
                        $error = "Please upload at least one product image in the gallery.";
                    } else {
                        // Find the primary slot
                        $primarySlot = isset($_POST['primarySlot']) ? intval($_POST['primarySlot']) : 0;
                        if ($primarySlot <= 0 || !isset($uploadedGalleryPaths[$primarySlot])) {
                            // Fallback to the first uploaded slot
                            reset($uploadedGalleryPaths);
                            $primarySlot = key($uploadedGalleryPaths);
                        }
                        $prodImg = $uploadedGalleryPaths[$primarySlot];
                    }
                }
            }

            // 3. Process Product Insertion & Variations
            if (empty($error)) {
                $pdo->beginTransaction();

                // Insert dynamic product record
                $stmt = $pdo->prepare("INSERT INTO `product` (`category_id`, `code`, `name`, `description`, `image`, `active`, `display_order`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$categoryId, $prodCode, $prodName, $prodDesc, $prodImg, $status, $displayOrder]);
                $productId = $pdo->lastInsertId();

                // Process dynamically added Size Variations
                $varIndices = isset($_POST['varIndices']) ? $_POST['varIndices'] : [];
                foreach ($varIndices as $index) {
                    $index = intval($index);
                    $varNo = isset($_POST['varNo_' . $index]) ? trim($_POST['varNo_' . $index]) : '';
                    $varCode = isset($_POST['varCode_' . $index]) ? trim($_POST['varCode_' . $index]) : '';
                    $varName = isset($_POST['varName_' . $index]) ? trim($_POST['varName_' . $index]) : '';
                    $varSize = isset($_POST['varSize_' . $index]) ? trim($_POST['varSize_' . $index]) : '';
                    $finalOrder = isset($_POST['finalOrder']) ? $_POST['finalOrder'] : [];
                    $varDisplayOrder = array_search('new_' . $index, $finalOrder);
                    if ($varDisplayOrder === false) $varDisplayOrder = isset($varNo) ? intval($varNo) : 0;
                    $varActive = 1;

                    $varImg = "";

                    // Process specific variation file upload if provided
                    if (isset($_FILES['varImgFile_' . $index]) && $_FILES['varImgFile_' . $index]['error'] === UPLOAD_ERR_OK) {
                        $vTmpPath = $_FILES['varImgFile_' . $index]['tmp_name'];
                        $vName = $_FILES['varImgFile_' . $index]['name'];
                        $vNameCmps = explode(".", $vName);
                        $vExt = strtolower(end($vNameCmps));
                        
                        if (in_array($vExt, $allowedExtensions)) {
                            $vNewName = 'var_' . time() . '_' . md5(uniqid()) . '.' . $vExt;
                            $vUploadDir = $base_path . 'assets/imag/product/variations/';
                            
                            if (!is_dir($vUploadDir)) {
                                mkdir($vUploadDir, 0777, true);
                            }
                            
                            $vDest = $vUploadDir . $vNewName;
                            if (move_uploaded_file($vTmpPath, $vDest)) {
                                $varImg = 'assets/imag/product/variations/' . $vNewName;
                            }
                        }
                    }

                    // Insert size variation row
                    $var_stmt = $pdo->prepare("INSERT INTO `product_variation` (`product_id`, `no`, `code`, `name`, `size`, `image`, `display_order`, `active`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $var_stmt->execute([$productId, $varNo, $varCode, $varName, $varSize, $varImg, $varDisplayOrder, $varActive]);
                }

                // Update product master code to match the first variation's SKU code (ordered by display_order, then id)
                $first_v_stmt = $pdo->prepare("SELECT `code` FROM `product_variation` WHERE `product_id` = ? ORDER BY `display_order` ASC, `id` ASC LIMIT 1");
                $first_v_stmt->execute([$productId]);
                $firstVarCode = $first_v_stmt->fetchColumn();
                
                if (!empty($firstVarCode)) {
                    // Check uniqueness excluding current product
                    $chk_code_stmt = $pdo->prepare("SELECT COUNT(*) FROM `product` WHERE `code` = ? AND `id` <> ?");
                    $chk_code_stmt->execute([$firstVarCode, $productId]);
                    if ($chk_code_stmt->fetchColumn() > 0) {
                        throw new Exception("SKU Code '$firstVarCode' is already used by another product. The first variation's SKU Code must be unique.");
                    }
                    
                    $upd_p_code_stmt = $pdo->prepare("UPDATE `product` SET `code` = ? WHERE `id` = ?");
                    $upd_p_code_stmt->execute([$firstVarCode, $productId]);
                }

                 // Process gallery images (up to 5 slots: galleryImg_1 .. galleryImg_5)
                 $galleryInsert = $pdo->prepare("INSERT INTO `product_image` (`product_id`, `image`, `display_order`) VALUES (?, ?, ?)");
                 foreach ($uploadedGalleryPaths as $gi => $gPath) {
                     $galleryInsert->execute([$productId, $gPath, $gi]);
                 }

                $pdo->commit();
                header("Location: list.php?msg=created");
                exit;
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = "Transaction Failed: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields marked with *.";
    }
}
?>
<?php
$page_title = "FRIO Admin - Add Product";
include $base_path . 'includes/head.php';
?>
<body class="bg-background text-on-background font-body-md min-h-screen">
<?php include $base_path . 'includes/sidebar.php'; ?>

<?php
$header_title = 'Product';
include $base_path . 'includes/header.php';
?>

<!-- TomSelect CSS for searchable dropdown -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Customize TomSelect to match FrioAdmin Tailwind theme */
    .ts-control {
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        border-color: rgba(var(--color-outline-variant), 1);
        background-color: var(--color-surface-container-lowest);
        font-size: 0.875rem;
        font-family: inherit;
        border-width: 1px;
        box-shadow: none;
    }
    .ts-control.focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 4px rgba(var(--color-primary), 0.1);
    }
    .ts-dropdown {
        border-radius: 0.75rem;
        border-color: rgba(var(--color-outline-variant), 1);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        margin-top: 0.5rem;
    }
    .ts-dropdown .option {
        padding: 0.75rem 1.5rem;
    }
    .ts-dropdown .option.active {
        background-color: rgba(var(--color-primary), 0.1);
        color: var(--color-primary);
    }
    .ts-control > input {
        display: inline-block !important;
        font-size: 0.875rem !important;
    }
</style>

<!-- Main Content -->
<main class="ml-64 pt-24 px-gutter pb-12">
    <!-- Breadcrumbs in Canvas -->
    <nav class="flex items-center text-label-sm text-on-surface-variant mb-8 max-w-7xl mx-auto">
        <a href="<?php echo $base_path; ?>dashbord.php" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">dashboard</span>
            <span>Dashboard</span>
        </a>
        <span class="material-symbols-outlined text-[16px] mx-2">chevron_right</span>
        <a href="list.php" class="hover:text-primary transition-colors">Product</a>
        <span class="material-symbols-outlined text-[16px] mx-2">chevron_right</span>
        <span class="text-primary font-bold">Add Product</span>
    </nav>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- LEFT COLUMN: Form -->
        <section class="lg:col-span-12 w-full max-w-screen-xl mx-auto">
            <div class="bg-white rounded-[2rem] p-8 md:p-12 shadow-[0px_4px_40px_rgba(12,75,134,0.05)] border border-surface-container">
                <div class="mb-10">
                    <h1 class="text-headline-lg font-headline-lg text-primary">ADD PRODUCT</h1>
                    <p class="text-on-surface-variant mt-2 font-body-md">Register a new product with custom technical attributes and sizes.</p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="bg-error-container/50 border border-error/20 p-4 rounded-xl flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-error">error</span>
                        <p class="font-label-sm text-label-sm text-on-error-container"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <form class="space-y-8" id="productForm" method="POST" action="add.php" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Category Dynamic Dropdown -->
                        <div class="space-y-2">
                            <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="categoryIdSelect">Category <span class="text-error font-bold">*</span></label>
                            <select class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest" id="categoryIdSelect" name="categoryId" required>
                                <option value="" disabled selected>Select category...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Product Name -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="prodNameInput">Product Name <span class="text-error font-bold">*</span></label>
                        <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-lg bg-surface-container-lowest placeholder:opacity-50" id="prodNameInput" name="prodName" placeholder="Enter product name" type="text" required />
                    </div>

                    <!-- Description (Optional) -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="prodDescInput">Description <span class="text-outline text-label-sm font-normal">(Optional)</span></label>
                        <textarea class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest resize-none placeholder:opacity-50" id="prodDescInput" name="prodDesc" placeholder="Write short product description..." rows="3"></textarea>
                    </div>

                    <!-- Hidden field to track primary slot number (1-5) -->
                    <input type="hidden" name="primarySlot" id="primarySlot" value="" />

                    <!-- Unified Product Images Gallery & Showcase Card -->
                    <div class="space-y-3">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Product Images <span class="text-error font-bold">*</span> <span class="text-outline text-label-sm font-normal">(Up to 5 images. Click a thumbnail's 'Primary' button to set the cover)</span></label>
                        
                        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-4 md:p-6 shadow-inner min-h-[140px]">
                            <!-- Unified 6-Column Grid Layout -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 w-full">
                                
                                <!-- Big Multi-Upload Button (Acts as the first box) -->
                                <div class="relative w-full aspect-square md:aspect-[4/5] bg-primary/5 border-2 border-primary/20 rounded-xl hover:bg-primary/10 hover:border-primary/50 transition-all flex flex-col items-center justify-center cursor-pointer group" title="Select up to 5 images at once">
                                    <span class="material-symbols-outlined text-primary text-4xl mb-2 group-hover:scale-110 transition-transform">cloud_upload</span>
                                    <span class="text-xs text-primary font-bold text-center px-2 leading-tight">Bulk Upload<br/>(Up to 5)</span>
                                    <input type="file" id="multiFileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-30" multiple accept="image/*" onchange="handleMultiSelect(this)" />
                                </div>

                                <!-- 5 Individual Image Upload Slots -->
                                <?php for ($gi = 1; $gi <= 5; $gi++): ?>
                                <div class="gallery-slot relative group" id="gallerySlot_<?php echo $gi; ?>">
                                        <div class="aspect-square md:aspect-[4/5] rounded-xl border-2 border-dashed border-outline-variant/60 bg-surface-container-lowest hover:border-primary/50 hover:bg-primary/5 transition-all flex flex-col items-center justify-center cursor-pointer overflow-hidden relative" id="gallerySlotBox_<?php echo $gi; ?>">
                                            <!-- Preview Image -->
                                            <img src="" alt="" id="galleryPreview_<?php echo $gi; ?>" class="w-full h-full object-cover hidden absolute inset-0 rounded-xl" />
                                            
                                            <!-- Overlay for uploading when empty -->
                                            <div class="flex flex-col items-center justify-center pointer-events-none z-10" id="galleryUploadUi_<?php echo $gi; ?>">
                                                <span class="material-symbols-outlined text-outline/40 text-4xl group-hover:text-primary transition-colors">add_photo_alternate</span>
                                                <span class="text-xs text-outline/50 mt-1 font-bold group-hover:text-primary transition-colors">#<?php echo $gi; ?></span>
                                            </div>

                                            <!-- Overlay details when has image -->
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-30 hidden" id="galleryOverlay_<?php echo $gi; ?>">
                                                <button type="button" onclick="setPrimarySlot(<?php echo $gi; ?>)" class="bg-white/95 hover:bg-white text-primary text-[8px] font-bold uppercase py-1 px-2 rounded shadow hover:scale-105 transition-transform" id="gallerySetPrimaryBtn_<?php echo $gi; ?>">
                                                    Primary
                                                </button>
                                            </div>

                                            <!-- Static Primary Badge -->
                                            <div class="absolute top-1 left-1 bg-primary text-white text-[7px] font-bold uppercase tracking-wider px-1 py-0.5 rounded shadow z-10 hidden" id="galleryPrimaryBadge_<?php echo $gi; ?>">
                                                ★
                                            </div>

                                            <!-- Hidden File Input -->
                                            <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" name="galleryImg_<?php echo $gi; ?>" id="galleryFileInput_<?php echo $gi; ?>" type="file" accept="image/*" onchange="previewGallerySlot(this, <?php echo $gi; ?>)" />
                                        </div>
                                        <button type="button" onclick="clearGallerySlot(<?php echo $gi; ?>)" class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-error text-white rounded-full hidden items-center justify-center z-30 shadow hover:scale-110 transition-transform" id="galleryClearBtn_<?php echo $gi; ?>">
                                            <span class="material-symbols-outlined text-[10px]">close</span>
                                        </button>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Display Order -->
                        <div class="space-y-2">
                            <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="displayOrderInput">Display Order</label>
                            <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest placeholder:opacity-50" id="displayOrderInput" name="displayOrder" placeholder="0" type="number" min="0" value="0" required />
                        </div>

                         <!-- Status Toggle Switch -->
                         <div class="space-y-2">
                             <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Status</label>
                             <div class="flex items-center gap-3 py-1.5">
                                 <label class="relative inline-flex items-center cursor-pointer select-none">
                                     <input type="hidden" name="status" value="0" />
                                     <input id="statusToggle" class="peer sr-only" name="status" type="checkbox" value="1" checked />
                                     <div class="w-12 h-6 bg-outline/20 rounded-full peer peer-checked:after:translate-x-6 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-all shadow-inner"></div>
                                 </label>
                                 <span id="statusLabel" class="font-label-bold text-label-bold text-on-surface-variant/80 select-none">Active</span>
                             </div>
                         </div>
                    </div>

                    <!-- DYNAMIC SIZE VARIATIONS SECTION -->
                    <div class="pt-6 border-t border-surface-container space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-headline-md text-headline-md text-primary leading-tight">SIZE VARIATIONS</h3>
                                <p class="text-label-sm text-on-surface-variant">Add distinct size mappings, serial codes, and optional visual overrides.</p>
                            </div>
                            <button type="button" onclick="addVariationRow()" class="bg-secondary text-on-secondary hover:bg-secondary/95 flex items-center gap-1 px-4 py-2 rounded-lg font-label-bold text-label-sm shadow-md transition-all active:scale-95">
                                <span class="material-symbols-outlined text-[18px]">add</span>
                                Add Size Variation
                            </button>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-outline-variant bg-surface-container-lowest">
                            <table class="w-full text-left border-collapse text-body-md" id="variationsTable">
                                <thead class="bg-surface-container-low text-on-surface-variant font-bold border-b border-outline-variant">
                                    <tr>
                                        <th class="py-3 px-2 w-16 text-center text-outline/50"><span class="material-symbols-outlined text-[20px]">drag_indicator</span></th>
                                        <th class="py-3 px-2 w-52">
                                            <div class="flex items-center gap-1 cursor-pointer select-none group w-max" onclick="toggleSortVariations('varCode_', 'sortAscIconSku', 'sortDescIconSku')" title="Click to sort SKUs">
                                                <span>SKU Code</span> <span class="text-error font-bold">*</span>
                                                <div class="flex flex-col -space-y-2.5 opacity-50 group-hover:opacity-100 transition-opacity">
                                                    <span class="material-symbols-outlined text-[20px] sort-icon" id="sortAscIconSku">arrow_drop_up</span>
                                                    <span class="material-symbols-outlined text-[20px] sort-icon" id="sortDescIconSku">arrow_drop_down</span>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="py-3 px-2 w-64">Name <span class="text-outline font-normal text-[10px]">(Optional)</span></th>
                                        <th class="py-3 px-2 w-72">
                                            <div class="flex items-center gap-1 cursor-pointer select-none group w-max" onclick="toggleSortVariations('varSize_', 'sortAscIconSize', 'sortDescIconSize')" title="Click to sort sizes">
                                                <span>Size Label</span> <span class="text-error font-bold">*</span>
                                                <div class="flex flex-col -space-y-2.5 opacity-50 group-hover:opacity-100 transition-opacity">
                                                    <span class="material-symbols-outlined text-[20px] sort-icon" id="sortAscIconSize">arrow_drop_up</span>
                                                    <span class="material-symbols-outlined text-[20px] sort-icon" id="sortDescIconSize">arrow_drop_down</span>
                                                </div>
                                            </div>
                                        </th>
                                        <th class="py-3 px-2 w-32 text-center">Image (Optional)</th>
                                        <th class="py-3 px-2 w-14 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-container" id="variationsTableBody">
                                    <!-- Dynamic rows injected here -->
                                    <tr id="emptyVarRow">
                                        <td colspan="7" class="py-8 text-center text-on-surface-variant font-medium">
                                            <span class="material-symbols-outlined text-3xl mb-1 block text-outline/60">layers_clear</span>
                                            No size variations added. Click "Add Size Variation" above.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center gap-4 pt-6">
                        <a href="list.php" class="flex-1 py-4 border border-outline text-on-surface-variant font-label-bold rounded-xl hover:bg-surface-container-low text-center flex items-center justify-center transition-all">
                            Cancel
                        </a>
                        <button class="flex-1 py-4 bg-primary text-white font-label-bold rounded-xl shadow-lg shadow-primary/20 hover:translate-y-[-2px] hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2" type="submit">
                            <span>Submit</span>
                            <span class="material-symbols-outlined text-[20px]">save</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>

        
    </div>
    <?php include $base_path . 'includes/footer.php'; ?>
</main>

<script>
    // Live preview syncing
    const nameInput = document.getElementById('prodNameInput');
    const descInput = document.getElementById('prodDescInput');
    const orderInput = document.getElementById('displayOrderInput');
    const catSelect = document.getElementById('categoryIdSelect');
    const statusToggle = document.getElementById('statusToggle');
    const statusLabel = document.getElementById('statusLabel');
 
    const previewTitle = document.getElementById('previewTitle');
    const previewDesc = document.getElementById('previewDesc');
    const previewImg = document.getElementById('previewImg');
    const previewOrder = document.getElementById('previewOrder');
    const previewCode = document.getElementById('previewCode');
    const previewCategory = document.getElementById('previewCategory');
    const statusBadge = document.getElementById('statusBadge');

    const defaultImg = "https://lh3.googleusercontent.com/aida-public/AB6AXuB2ZQ8yeQoePJq5Gwlxo2DKly2CS8BFuRajp8W9-EB2GEkGaWnYdWVidnjcUNSQ_GueB6HE6B7tUaTt99qYx1VONkvRjBC1Mfc05PQM-IIb58hYyjtUVPBLPd_iDMPUxpH9-Sg8U-UmXwuIWCwVSZ2g_ge58tw8BXS08Vkh2W65JU5QxcAoHu39ApLe6TI0j9mn4ciHpZMFyvvUI41sSreckbQbqg49L9AvwiE98sRKKNXMTskI-_bTIF_qLllr1U_FicJ8MJj_5UEH";

    if (nameInput) {
        nameInput.addEventListener('input', (e) => {
            if (previewTitle) previewTitle.textContent = e.target.value || 'New Product Name';
        });
    }

    if (descInput) {
        descInput.addEventListener('input', (e) => {
            if (previewDesc) previewDesc.textContent = e.target.value || 'Enter a description in the form to see it reflected here.';
        });
    }

    function syncSkuPreview() {
        const previewCode = document.getElementById('previewCode');
        if (!previewCode) return;

        const skuInputs = Array.from(document.querySelectorAll('#variationsTableBody input[name^="varCode_"]'));

        if (skuInputs.length === 0) {
            previewCode.style.display = 'none';
            previewCode.textContent = '';
        } else {
            previewCode.style.display = '';
            const firstSku = skuInputs[0].value.trim();
            if (firstSku) {
                previewCode.textContent = "SKU: " + firstSku.toUpperCase();
            } else {
                previewCode.textContent = "SKU: PENDING";
            }
        }
    }

    // Set up event delegation for variation SKU updates
    const variationsTableBody = document.getElementById('variationsTableBody');
    if (variationsTableBody) {
        variationsTableBody.addEventListener('input', function(e) {
            if (e.target && e.target.name && e.target.name.startsWith('varCode_')) {
                syncSkuPreview();
            }
        });
    }

    // Initialize preview code visibility based on size variations
    syncSkuPreview();

    if (catSelect) {
        catSelect.addEventListener('change', (e) => {
            const selectedText = e.target.options[e.target.selectedIndex].text;
            if (previewCategory) previewCategory.textContent = selectedText;
        });
    }

    if (orderInput) {
        orderInput.addEventListener('input', (e) => {
            if (previewOrder) previewOrder.textContent = e.target.value || '0';
        });
    }

    if (statusToggle) {
        statusToggle.addEventListener('change', (e) => {
            if (e.target.checked) {
                if (statusLabel) statusLabel.textContent = 'Active';
                if (statusBadge) {
                    statusBadge.textContent = 'Active';
                    statusBadge.className = 'bg-secondary text-on-secondary px-4 py-1.5 rounded-full font-label-bold text-[10px] uppercase shadow-lg shadow-secondary/20';
                }
            } else {
                if (statusLabel) statusLabel.textContent = 'Inactive';
                if (statusBadge) {
                    statusBadge.textContent = 'Inactive';
                    statusBadge.className = 'bg-on-surface-variant text-white px-4 py-1.5 rounded-full font-label-bold text-[10px] uppercase shadow-lg';
                }
            }
        });
    }

    // Gallery & Showcase Logic
    const primarySlotInput = document.getElementById('primarySlot');
    
    let activePreviewSlot = null;

    function updateGalleryState() {
        let activeSlots = [];
        for (let gi = 1; gi <= 5; gi++) {
            const preview = document.getElementById(`galleryPreview_${gi}`);
            if (preview && preview.src && !preview.classList.contains('hidden') && preview.src !== window.location.href) {
                activeSlots.push(gi);
            }
        }

        const totalImages = activeSlots.length;
        let currentPrimary = parseInt(primarySlotInput.value);

        // Auto-select primary if not set, or if set slot is empty
        if (totalImages > 0) {
            if (!currentPrimary || !activeSlots.includes(currentPrimary)) {
                currentPrimary = activeSlots[0];
                primarySlotInput.value = currentPrimary;
            }
        } else {
            currentPrimary = null;
            primarySlotInput.value = "";
        }

        // Loop through slots to update badges, delete buttons, overlays
        for (let gi = 1; gi <= 5; gi++) {
            const preview = document.getElementById(`galleryPreview_${gi}`);
            const uploadUi = document.getElementById(`galleryUploadUi_${gi}`);
            const overlay = document.getElementById(`galleryOverlay_${gi}`);
            const primaryBadge = document.getElementById(`galleryPrimaryBadge_${gi}`);
            const clearBtn = document.getElementById(`galleryClearBtn_${gi}`);
            const box = document.getElementById(`gallerySlotBox_${gi}`);

            const hasImage = preview && preview.src && !preview.classList.contains('hidden') && preview.src !== window.location.href;

            if (hasImage) {
                uploadUi.classList.add('hidden');
                clearBtn.classList.remove('hidden');
                clearBtn.classList.add('flex');

                if (gi === currentPrimary) {
                    primaryBadge.classList.remove('hidden');
                    overlay.classList.add('hidden'); // Hide Make Primary overlay since already primary
                    box.classList.remove('border-outline-variant/60');
                    box.classList.add('border-primary', 'ring-2', 'ring-primary/10');

                    // Style primary delete button
                    if (totalImages > 1) {
                        clearBtn.classList.remove('bg-error');
                        clearBtn.classList.add('bg-outline', 'opacity-60', 'cursor-not-allowed');
                        clearBtn.title = "Make another image primary to delete this image.";
                    } else {
                        clearBtn.classList.remove('bg-outline', 'opacity-60', 'cursor-not-allowed');
                        clearBtn.classList.add('bg-error');
                        clearBtn.removeAttribute('title');
                    }
                } else {
                    primaryBadge.classList.add('hidden');
                    overlay.classList.remove('hidden'); // Show Make Primary overlay on hover
                    box.classList.remove('border-primary', 'ring-2', 'ring-primary/10');
                    box.classList.add('border-outline-variant/60');

                    clearBtn.classList.remove('bg-outline', 'opacity-60', 'cursor-not-allowed');
                    clearBtn.classList.add('bg-error');
                    clearBtn.removeAttribute('title');
                }
            } else {
                uploadUi.classList.remove('hidden');
                overlay.classList.add('hidden');
                primaryBadge.classList.add('hidden');
                clearBtn.classList.add('hidden');
                clearBtn.classList.remove('flex');
                box.classList.remove('border-primary', 'ring-2', 'ring-primary/10');
                box.classList.add('border-outline-variant/60');
            }
        }

        // Update live preview sync
        if (currentPrimary) {
            const primaryPreview = document.getElementById(`galleryPreview_${currentPrimary}`);
            previewImg.src = primaryPreview.src;
        } else {
            previewImg.src = defaultImg;
        }
    }

     function handleMultiSelect(input) {
         if (!input.files || input.files.length === 0) return;

         const files = Array.from(input.files);
         
         let activeSlots = [];
         for (let gi = 1; gi <= 5; gi++) {
             const preview = document.getElementById(`galleryPreview_${gi}`);
             if (preview && preview.src && !preview.classList.contains('hidden') && preview.src !== window.location.href) {
                 activeSlots.push(gi);
             }
         }

         const availableSlots = [];
         for (let gi = 1; gi <= 5; gi++) {
             if (!activeSlots.includes(gi)) {
                 availableSlots.push(gi);
             }
         }

         if (files.length > availableSlots.length) {
             alert(`You can only upload up to ${availableSlots.length} more image(s). (Max 5 images total in the gallery)`);
             input.value = '';
             return;
         }

         files.forEach((file, index) => {
             if (index >= availableSlots.length) return;
             const slotNo = availableSlots[index];

             // Assign to slot input using DataTransfer
             const slotInput = document.getElementById(`galleryFileInput_${slotNo}`);
             const dt = new DataTransfer();
             dt.items.add(file);
             slotInput.files = dt.files;

             // Preview the image
             const preview = document.getElementById(`galleryPreview_${slotNo}`);
             const reader = new FileReader();
             reader.onload = (e) => {
                 preview.src = e.target.result;
                 preview.classList.remove('hidden');
                 updateGalleryState();
             };
             reader.readAsDataURL(file);
         });

         input.value = ''; // Reset input
     }

    function previewGallerySlot(input, slotNo) {
        const preview = document.getElementById(`galleryPreview_${slotNo}`);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                updateGalleryState();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearGallerySlot(slotNo) {
        let activeSlots = [];
        for (let gi = 1; gi <= 5; gi++) {
            const preview = document.getElementById(`galleryPreview_${gi}`);
            if (preview && preview.src && !preview.classList.contains('hidden') && preview.src !== window.location.href) {
                activeSlots.push(gi);
            }
        }

        const totalImages = activeSlots.length;
        const currentPrimary = parseInt(primarySlotInput.value);

        // Enforce deletion rule
        if (slotNo === currentPrimary && totalImages > 1) {
            alert("This image is marked as Primary. To delete it, please make another image primary first.");
            return;
        }

        const preview = document.getElementById(`galleryPreview_${slotNo}`);
        const box = document.getElementById(`gallerySlotBox_${slotNo}`);
        const fileInput = document.getElementById(`galleryFileInput_${slotNo}`);
        
        if (fileInput) {
            fileInput.value = '';
        }
        if (preview) {
            preview.src = '';
            preview.classList.add('hidden');
        }
        updateGalleryState();
    }

    // Set primary slot function
    function setPrimarySlot(slotNo) {
        primarySlotInput.value = slotNo;
        updateGalleryState();
    }

    // Client-side form submit validation
    document.getElementById('productForm').addEventListener('submit', function(e) {
        let activeSlots = [];
        for (let gi = 1; gi <= 5; gi++) {
            const preview = document.getElementById(`galleryPreview_${gi}`);
            if (preview && preview.src && !preview.classList.contains('hidden') && preview.src !== window.location.href) {
                activeSlots.push(gi);
            }
        }

        if (activeSlots.length === 0) {
            e.preventDefault();
            alert("Please upload at least one image in the Product Gallery.");
            return false;
        }
    });

    // Interactive Dynamic Variation rows script
    let varIndex = 0;
    const tableBody = document.getElementById('variationsTableBody');
    const emptyRow = document.getElementById('emptyVarRow');

    function addVariationRow() {
        // Remove empty state placeholder row if it exists
        if (emptyRow) {
            emptyRow.style.display = 'none';
        }

        varIndex++;

        // Construct dynamic table row element
        const row = document.createElement('tr');
        row.id = `varRow_${varIndex}`;
        row.className = 'hover:bg-primary/5 transition-colors cursor-grab active:cursor-grabbing';

        row.innerHTML = `
            <td class="py-3 px-2 text-center text-outline/40">
                <span class="material-symbols-outlined text-[20px] handle select-none cursor-grab active:cursor-grabbing">drag_indicator</span>
                <input type="hidden" name="varIndices[]" value="${varIndex}" />
                <input type="hidden" name="varNo_${varIndex}" value="${varIndex}" />
            </td>
            <td class="py-3 px-2">
                <input class="w-full px-3 py-2 rounded-lg border-outline-variant focus:border-primary text-body-md" name="varCode_${varIndex}" placeholder="e.g. SKU-${varIndex}" type="text" required />
            </td>
            <td class="py-3 px-2">
                <input class="w-full px-3 py-2 rounded-lg border-outline-variant focus:border-primary text-body-md" name="varName_${varIndex}" placeholder="Optional Name" type="text" />
            </td>
            <td class="py-3 px-2">
                <input class="w-full px-3 py-2 rounded-lg border-outline-variant focus:border-primary text-body-md" name="varSize_${varIndex}" placeholder="e.g. 122x400x500" type="text" required />
            </td>
            <td class="py-3 px-2">
                <div class="relative cursor-pointer bg-surface-container-low border border-outline-variant rounded-lg py-1.5 px-2 text-center group hover:bg-primary/5 hover:border-primary/30 transition-all flex items-center justify-center gap-1 min-h-[32px]">
                    <span class="material-symbols-outlined text-primary text-[16px]">cloud_upload</span>
                    <span class="text-[9px] text-primary font-bold truncate select-none var-file-label" id="varFileLabel_${varIndex}">Upload</span>
                    <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" name="varImgFile_${varIndex}" type="file" accept="image/*" onchange="updateVarFileLabel(this, ${varIndex})" />
                </div>
            </td>
            <td class="py-3 px-2 text-center">
                <button type="button" onclick="removeVariationRow(${varIndex})" class="p-1.5 text-error hover:bg-error/15 rounded-lg transition-all active:scale-90">
                    <span class="material-symbols-outlined text-[18px] block">delete</span>
                </button>
            </td>
        `;

        tableBody.prepend(row);
        syncSkuPreview();
    }

    function removeVariationRow(index) {
        const row = document.getElementById(`varRow_${index}`);
        if (row) {
            row.remove();
        }

        // Show empty placeholder row if no variations exist
        const rows = tableBody.querySelectorAll('tr:not(#emptyVarRow)');
        if (rows.length === 0 && emptyRow) {
            emptyRow.style.display = '';
        }
        syncSkuPreview();
    }

    function updateVarFileLabel(input, index) {
        const label = document.getElementById(`varFileLabel_${index}`);
        if (input.files && input.files[0]) {
            label.textContent = input.files[0].name;
            label.className = "text-[9px] text-on-surface-variant font-medium truncate select-none var-file-label";
        } else {
            label.textContent = "Upload Image";
            label.className = "text-[10px] text-primary font-bold truncate select-none var-file-label";
        }
    }

    // Sort size variations based on Size input
    let varSortStates = {};
    
    function toggleSortVariations(inputNamePrefix, ascIconId, descIconId) {
        const rows = Array.from(tableBody.querySelectorAll('tr:not(#emptyVarRow)'));
        if (rows.length <= 1) return;

        if (varSortStates[inputNamePrefix] === undefined) {
            varSortStates[inputNamePrefix] = true;
        } else {
            varSortStates[inputNamePrefix] = !varSortStates[inputNamePrefix];
        }
        
        const isAsc = varSortStates[inputNamePrefix];

        // Reset all sort icons styling
        document.querySelectorAll('.sort-icon').forEach(icon => icon.classList.remove('text-primary'));

        const ascIcon = document.getElementById(ascIconId);
        const descIcon = document.getElementById(descIconId);

        if (isAsc) {
            ascIcon.classList.add('text-primary');
        } else {
            descIcon.classList.add('text-primary');
        }

        rows.sort((a, b) => {
            const aInput = a.querySelector(`input[name^="${inputNamePrefix}"]`);
            const bInput = b.querySelector(`input[name^="${inputNamePrefix}"]`);
            
            const aVal = aInput ? aInput.value.trim().toLowerCase() : '';
            const bVal = bInput ? bInput.value.trim().toLowerCase() : '';

            const aNum = parseFloat(aVal);
            const bNum = parseFloat(bVal);
            
            if (!isNaN(aNum) && !isNaN(bNum)) {
                if (aNum !== bNum) {
                    return isAsc ? aNum - bNum : bNum - aNum;
                }
            }

            if (aVal < bVal) return isAsc ? -1 : 1;
            if (aVal > bVal) return isAsc ? 1 : -1;
            return 0;
        });

        rows.forEach(row => tableBody.appendChild(row));
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tbody = document.querySelector('#variationsTable tbody');
        if (tbody) {
            new Sortable(tbody, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'bg-primary/10'
            });
        }
    });

    // Capture final dragged order on submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const tbody = document.querySelector('#variationsTable tbody');
            if (tbody) {
                // Remove old finalOrder inputs if any (in case of double submit)
                document.querySelectorAll('.final-order-input').forEach(el => el.remove());
                
                const rows = tbody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    let val = '';
                    if (row.id.startsWith('existingVarRow_')) {
                        val = 'existing_' + row.id.split('_')[1];
                    } else if (row.id.startsWith('varRow_')) {
                        val = 'new_' + row.id.split('_')[1];
                    }
                    if (val) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'finalOrder[]';
                        input.value = val;
                        input.className = 'final-order-input';
                        form.appendChild(input);
                    }
                });
            }
        });
    }
</script>
<!-- TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  if (typeof tinymce !== 'undefined') {
      tinymce.init({
        selector: '#prodDescInput',
        plugins: 'lists link code',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link code',
        menubar: false,
        height: 300,
        promotion: false,
        setup: function(editor) {
            editor.on('change keyup paste', function(e) {
                editor.save();
                const textarea = document.getElementById('prodDescInput');
                if (textarea) {
                    // Manually dispatch input event for live preview if needed
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }
      });
  }
</script>

<!-- TomSelect JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('categoryIdSelect')) {
            new TomSelect('#categoryIdSelect', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: 'Search and select a category...',
                maxOptions: 50
            });
        }
    });
</script>
</body></html>
