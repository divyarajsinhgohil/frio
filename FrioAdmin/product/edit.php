<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

$error = "";
$message = "";

// Ensure a valid product ID is provided
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: list.php");
    exit;
}

// 1. Fetch current product record
try {
    $stmt = $pdo->prepare("SELECT * FROM `product` WHERE `id` = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header("Location: list.php");
        exit;
    }

    // Fetch parent categories for select dropdown
    $categories = $pdo->query("SELECT * FROM `category` WHERE `active` = 1 ORDER BY `display_order` ASC, `id` DESC")->fetchAll(PDO::FETCH_ASSOC);

    // Fetch existing size variations
    $v_stmt = $pdo->prepare("SELECT * FROM `product_variation` WHERE `product_id` = ? ORDER BY `display_order` ASC, `id` ASC");
    $v_stmt->execute([$id]);
    $variations = $v_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch existing gallery images
    $g_stmt = $pdo->prepare("SELECT * FROM `product_image` WHERE `product_id` = ? ORDER BY `display_order` ASC, `id` ASC");
    $g_stmt->execute([$id]);
    $existing_gallery = $g_stmt->fetchAll(PDO::FETCH_ASSOC);

    $gallery_slots = array_fill(1, 5, null);
    foreach ($existing_gallery as $g_img) {
        $do = intval($g_img['display_order']);
        if ($do >= 1 && $do <= 5) {
            $gallery_slots[$do] = $g_img;
        } else {
            // Find first empty slot
            for ($k = 1; $k <= 5; $k++) {
                if ($gallery_slots[$k] === null) {
                    $gallery_slots[$k] = $g_img;
                    break;
                }
            }
        }
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = isset($_POST['categoryId']) ? intval($_POST['categoryId']) : 0;
    $prodName = isset($_POST['prodName']) ? trim($_POST['prodName']) : '';
    $prodDesc = isset($_POST['prodDesc']) ? trim($_POST['prodDesc']) : '';
    $prodCode = $product['code']; // Keep existing product code securely in PHP instead of user input
    $displayOrder = isset($_POST['displayOrder']) ? intval($_POST['displayOrder']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1;

    $prodImg = $product['image']; // Default to existing image
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if ($categoryId > 0 && !empty($prodName) && !empty($prodCode)) {
        try {
            // Check if product code is already in use by another product
            $chk_stmt = $pdo->prepare("SELECT COUNT(*) FROM `product` WHERE `code` = ? AND `id` <> ?");
            $chk_stmt->execute([$prodCode, $id]);
            if ($chk_stmt->fetchColumn() > 0) {
                $error = "Product Code already exists. Please choose a unique SKU.";
            }

            // 2. Handle gallery image updates & primary image selection
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $galleryUploadDir = $base_path . 'assets/imag/product/gallery/';

            // We will track gallery image updates to execute within the database transaction
            $galleryOps = []; // list of operations: ['type' => 'insert'|'update'|'delete', 'id' => ..., 'path' => ..., 'display_order' => ...]
            $unlinks = [];    // files to delete from server after commit
            $galleryPathsAfter = []; // Maps slot index to active path

            if (empty($error)) {
                for ($gi = 1; $gi <= 5; $gi++) {
                    $gStatus = isset($_POST['gallery_status_' . $gi]) ? trim($_POST['gallery_status_' . $gi]) : 'empty';
                    $existId = isset($_POST['gallery_id_' . $gi]) ? intval($_POST['gallery_id_' . $gi]) : 0;
                    $existPath = isset($_POST['gallery_path_' . $gi]) ? trim($_POST['gallery_path_' . $gi]) : '';

                    if ($gStatus === 'empty') {
                        if ($existId > 0) {
                            $galleryOps[] = ['type' => 'delete', 'id' => $existId];
                            $unlinks[] = $existPath;
                        }
                    } elseif ($gStatus === 'new') {
                        if (isset($_FILES['galleryImg_' . $gi]) && $_FILES['galleryImg_' . $gi]['error'] === UPLOAD_ERR_OK) {
                            $gTmpPath = $_FILES['galleryImg_' . $gi]['tmp_name'];
                            $gName    = $_FILES['galleryImg_' . $gi]['name'];
                            $gExt     = strtolower(pathinfo($gName, PATHINFO_EXTENSION));
                            
                            if (in_array($gExt, $allowedExtensions)) {
                                $gNewName = 'gal_' . $gi . '_' . time() . '_' . md5(uniqid()) . '.' . $gExt;
                                if (!is_dir($galleryUploadDir)) {
                                    mkdir($galleryUploadDir, 0777, true);
                                }
                                $gDest = $galleryUploadDir . $gNewName;
                                if (move_uploaded_file($gTmpPath, $gDest)) {
                                    $newPath = 'assets/imag/product/gallery/' . $gNewName;
                                    $galleryPathsAfter[$gi] = $newPath;

                                    if ($existId > 0) {
                                        $galleryOps[] = ['type' => 'update', 'id' => $existId, 'path' => $newPath];
                                        $unlinks[] = $existPath;
                                    } else {
                                        $galleryOps[] = ['type' => 'insert', 'path' => $newPath, 'display_order' => $gi];
                                    }
                                } else {
                                    $error = "Error moving uploaded gallery image #$gi.";
                                    break;
                                }
                            } else {
                                $error = "Allowed product image formats: " . implode(', ', $allowedExtensions);
                                break;
                            }
                        } else {
                            $error = "Error uploading gallery image #$gi.";
                            break;
                        }
                    } elseif ($gStatus === 'existing') {
                        $galleryPathsAfter[$gi] = $existPath;
                    }
                }

                if (empty($error)) {
                    // Find primary image path
                    $primarySlot = isset($_POST['primarySlot']) ? intval($_POST['primarySlot']) : 0;
                    if ($primarySlot <= 0 || !isset($galleryPathsAfter[$primarySlot])) {
                        if (!empty($galleryPathsAfter)) {
                            reset($galleryPathsAfter);
                            $primarySlot = key($galleryPathsAfter);
                            $prodImg = $galleryPathsAfter[$primarySlot];
                        } else {
                            $prodImg = "";
                        }
                    } else {
                        $prodImg = $galleryPathsAfter[$primarySlot];
                    }
                }
            }

            // 3. Process database transaction for update
            if (empty($error)) {
                $pdo->beginTransaction();

                // Update product table attributes
                $stmt = $pdo->prepare("UPDATE `product` SET `category_id` = ?, `code` = ?, `name` = ?, `description` = ?, `image` = ?, `active` = ?, `display_order` = ? WHERE `id` = ?");
                $stmt->execute([$categoryId, $prodCode, $prodName, $prodDesc, $prodImg, $status, $displayOrder, $id]);

                // Process gallery changes inside transaction
                foreach ($galleryOps as $op) {
                    if ($op['type'] === 'delete') {
                        $del_g_stmt = $pdo->prepare("DELETE FROM `product_image` WHERE `id` = ?");
                        $del_g_stmt->execute([$op['id']]);
                    } elseif ($op['type'] === 'update') {
                        $upd_g_stmt = $pdo->prepare("UPDATE `product_image` SET `image` = ? WHERE `id` = ?");
                        $upd_g_stmt->execute([$op['path'], $op['id']]);
                    } elseif ($op['type'] === 'insert') {
                        $ins_g_stmt = $pdo->prepare("INSERT INTO `product_image` (`product_id`, `image`, `display_order`) VALUES (?, ?, ?)");
                        $ins_g_stmt->execute([$id, $op['path'], $op['display_order']]);
                    }
                }

                // A. Handle DELETED variations
                $deletedVarIds = isset($_POST['deletedVarIds']) ? $_POST['deletedVarIds'] : [];
                foreach ($deletedVarIds as $d_id) {
                    $d_id = intval($d_id);
                    if ($d_id > 0) {
                        // Fetch old variation image to delete it from server
                        $v_img_stmt = $pdo->prepare("SELECT `image` FROM `product_variation` WHERE `id` = ?");
                        $v_img_stmt->execute([$d_id]);
                        $old_var_img = $v_img_stmt->fetchColumn();

                        if (!empty($old_var_img) && strpos($old_var_img, 'http://') !== 0 && strpos($old_var_img, 'https://') !== 0) {
                            $old_v_file = $base_path . $old_var_img;
                            if (file_exists($old_v_file)) {
                                unlink($old_v_file);
                            }
                        }

                        // Delete record
                        $del_v_stmt = $pdo->prepare("DELETE FROM `product_variation` WHERE `id` = ?");
                        $del_v_stmt->execute([$d_id]);
                    }
                }

                // B. Handle UPDATED existing variations
                $existingVarIds = isset($_POST['existingVarIds']) ? $_POST['existingVarIds'] : [];
                foreach ($existingVarIds as $e_id) {
                    $e_id = intval($e_id);
                    if ($e_id > 0) {
                        $varNo = isset($_POST['varNo_existing_' . $e_id]) ? trim($_POST['varNo_existing_' . $e_id]) : '';
                        $varCode = isset($_POST['varCode_existing_' . $e_id]) ? trim($_POST['varCode_existing_' . $e_id]) : '';
                        $varName = isset($_POST['varName_existing_' . $e_id]) ? trim($_POST['varName_existing_' . $e_id]) : '';
                        $varSize = isset($_POST['varSize_existing_' . $e_id]) ? trim($_POST['varSize_existing_' . $e_id]) : '';
                        $finalOrder = isset($_POST['finalOrder']) ? $_POST['finalOrder'] : [];
                        $varDisplayOrder = array_search('existing_' . $e_id, $finalOrder);
                        if ($varDisplayOrder === false) $varDisplayOrder = 0;
                        $varActive = 1;

                        // Retrieve original variation image
                        $old_var_stmt = $pdo->prepare("SELECT `image` FROM `product_variation` WHERE `id` = ?");
                        $old_var_stmt->execute([$e_id]);
                        $varImg = $old_var_stmt->fetchColumn();

                        // Handle image replacement if a new file is uploaded
                        if (isset($_FILES['varImgFile_existing_' . $e_id]) && $_FILES['varImgFile_existing_' . $e_id]['error'] === UPLOAD_ERR_OK) {
                            $vTmpPath = $_FILES['varImgFile_existing_' . $e_id]['tmp_name'];
                            $vName = $_FILES['varImgFile_existing_' . $e_id]['name'];
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
                                    // Delete old image file
                                    if (!empty($varImg) && strpos($varImg, 'http://') !== 0 && strpos($varImg, 'https://') !== 0) {
                                        $old_v_file = $base_path . $varImg;
                                        if (file_exists($old_v_file)) {
                                            unlink($old_v_file);
                                        }
                                    }
                                    $varImg = 'assets/imag/product/variations/' . $vNewName;
                                }
                            }
                        }

                        // Update variation table attributes
                        $upd_v_stmt = $pdo->prepare("UPDATE `product_variation` SET `no` = ?, `code` = ?, `name` = ?, `size` = ?, `image` = ?, `display_order` = ?, `active` = ? WHERE `id` = ?");
                        $upd_v_stmt->execute([$varNo, $varCode, $varName, $varSize, $varImg, $varDisplayOrder, $varActive, $e_id]);
                    }
                }

                // C. Handle NEWLY added variations
                $varIndices = isset($_POST['varIndices']) ? $_POST['varIndices'] : [];
                foreach ($varIndices as $index) {
                    $index = intval($index);
                    $varNo = isset($_POST['varNo_' . $index]) ? trim($_POST['varNo_' . $index]) : '';
                    $varCode = isset($_POST['varCode_' . $index]) ? trim($_POST['varCode_' . $index]) : '';
                    $varName = isset($_POST['varName_' . $index]) ? trim($_POST['varName_' . $index]) : '';
                    $varSize = isset($_POST['varSize_' . $index]) ? trim($_POST['varSize_' . $index]) : '';
                    $finalOrder = isset($_POST['finalOrder']) ? $_POST['finalOrder'] : [];
                    $varDisplayOrder = array_search('new_' . $index, $finalOrder);
                    if ($varDisplayOrder === false) $varDisplayOrder = 0;
                    $varActive = 1;

                    $varImg = "";

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

                    $var_stmt = $pdo->prepare("INSERT INTO `product_variation` (`product_id`, `no`, `code`, `name`, `size`, `image`, `display_order`, `active`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $var_stmt->execute([$id, $varNo, $varCode, $varName, $varSize, $varImg, $varDisplayOrder, $varActive]);
                }

                // Update product master code to match the first variation's SKU code (ordered by display_order, then id)
                $first_v_stmt = $pdo->prepare("SELECT `code` FROM `product_variation` WHERE `product_id` = ? ORDER BY `display_order` ASC, `id` ASC LIMIT 1");
                $first_v_stmt->execute([$id]);
                $firstVarCode = $first_v_stmt->fetchColumn();
                
                if (!empty($firstVarCode)) {
                    // Check uniqueness excluding current product
                    $chk_code_stmt = $pdo->prepare("SELECT COUNT(*) FROM `product` WHERE `code` = ? AND `id` <> ?");
                    $chk_code_stmt->execute([$firstVarCode, $id]);
                    if ($chk_code_stmt->fetchColumn() > 0) {
                        throw new Exception("SKU Code '$firstVarCode' is already used by another product. The first variation's SKU Code must be unique.");
                    }
                    
                    $upd_p_code_stmt = $pdo->prepare("UPDATE `product` SET `code` = ? WHERE `id` = ?");
                    $upd_p_code_stmt->execute([$firstVarCode, $id]);
                }

                 $pdo->commit();

                 // Unlink old gallery files from disk safely
                 foreach ($unlinks as $old_path) {
                     if (!empty($old_path) && strpos($old_path, 'http://') !== 0 && strpos($old_path, 'https://') !== 0) {
                         $old_file = $base_path . $old_path;
                         if (file_exists($old_file)) {
                             unlink($old_file);
                         }
                     }
                 }

                 header("Location: list.php?msg=updated");
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

// Compute dynamic preview source path for main product
$preview_img_src = htmlspecialchars($product['image']);
if (strpos($product['image'], 'http://') !== 0 && strpos($product['image'], 'https://') !== 0) {
    $preview_img_src = $base_path . htmlspecialchars($product['image']);
}

// Get category name for live preview
$active_cat_name = "";
foreach ($categories as $cat) {
    if ($cat['id'] == $product['category_id']) {
        $active_cat_name = $cat['name'];
        break;
    }
}
?>
<?php
$page_title = "FRIO Admin - Edit Product";
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
        <span class="text-primary font-bold">Edit Product</span>
    </nav>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- LEFT COLUMN: Form -->
        <section class="lg:col-span-12 w-full max-w-screen-xl mx-auto">
            <div class="bg-white rounded-[2rem] p-8 md:p-12 shadow-[0px_4px_40px_rgba(12,75,134,0.05)] border border-surface-container">
                <div class="mb-10">
                    <h1 class="text-headline-lg font-headline-lg text-primary">EDIT PRODUCT</h1>
                    <p class="text-on-surface-variant mt-2 font-body-md">Modify product attributes and manage technical size variation profiles for <b><?php echo htmlspecialchars($product['code']); ?></b>.</p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="bg-error-container/50 border border-error/20 p-4 rounded-xl flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-error">error</span>
                        <p class="font-label-sm text-label-sm text-on-error-container"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <form class="space-y-8" id="productForm" method="POST" action="edit.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
                    <!-- Hidden Container for Tracking Deleted Variation IDs -->
                    <div id="deletedVarContainer"></div>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Category select dropdown -->
                        <div class="space-y-2">
                            <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="categoryIdSelect">Category <span class="text-error font-bold">*</span></label>
                            <select class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest" id="categoryIdSelect" name="categoryId" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Product Name -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="prodNameInput">Product Name <span class="text-error font-bold">*</span></label>
                        <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-lg bg-surface-container-lowest placeholder:opacity-50" id="prodNameInput" name="prodName" placeholder="Enter product name" type="text" value="<?php echo htmlspecialchars($product['name']); ?>" required />
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="prodDescInput">Description <span class="text-outline text-label-sm font-normal">(Optional)</span></label>
                        <textarea class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest resize-none placeholder:opacity-50" id="prodDescInput" name="prodDesc" placeholder="Write short product description..." rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <?php
                    $primary_slot_number = "";
                    for ($k = 1; $k <= 5; $k++) {
                        if ($gallery_slots[$k] !== null && $gallery_slots[$k]['image'] === $product['image']) {
                            $primary_slot_number = $k;
                            break;
                        }
                    }
                    if (empty($primary_slot_number) && !empty($product['image'])) {
                        $primary_slot_number = 1;
                    }
                    ?>

                    <!-- Hidden field to track primary slot number (1-5) -->
                    <input type="hidden" name="primarySlot" id="primarySlot" value="<?php echo htmlspecialchars($primary_slot_number); ?>" />

                    <!-- Unified Product Images Gallery & Showcase Card -->
                    <div class="space-y-3">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Product Images <span class="text-error font-bold">*</span> <span class="text-outline text-label-sm font-normal">(Up to 5 images. Click a thumbnail's 'Primary' button to set the cover)</span></label>
                        
                        <div class="bg-surface-container-lowest border border-outline-variant/60 rounded-2xl p-4 md:p-6 shadow-inner min-h-[140px]">
                             <!-- Unified 6-Column Grid Layout -->
                             <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 w-full" id="gallerySlots">
                                 
                                 <!-- Big Multi-Upload Button (Acts as the first box) -->
                                 <div class="relative w-full aspect-square md:aspect-[4/5] bg-primary/5 border-2 border-primary/20 rounded-xl hover:bg-primary/10 hover:border-primary/50 transition-all flex flex-col items-center justify-center cursor-pointer group" title="Select up to 5 images at once">
                                     <span class="material-symbols-outlined text-primary text-4xl mb-2 group-hover:scale-110 transition-transform">cloud_upload</span>
                                     <span class="text-xs text-primary font-bold text-center px-2 leading-tight">Bulk Upload<br/>(Up to 5)</span>
                                     <input type="file" id="multiFileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-30" multiple accept="image/*" onchange="handleMultiSelect(this)" />
                                 </div>

                                 <!-- 5 Individual Image Upload Slots -->
                                 <?php for ($gi = 1; $gi <= 5; $gi++): ?>
                                    <?php
                                    $slot_data = $gallery_slots[$gi];
                                    $has_img = ($slot_data !== null);
                                    $img_url = "";
                                    if ($has_img) {
                                        $img_url = htmlspecialchars($slot_data['image']);
                                        if (strpos($slot_data['image'], 'http://') !== 0 && strpos($slot_data['image'], 'https://') !== 0) {
                                            $img_url = $base_path . htmlspecialchars($slot_data['image']);
                                        }
                                    }
                                    ?>
                                    <!-- Hidden state trackers for this slot -->
                                    <input type="hidden" name="gallery_status_<?php echo $gi; ?>" id="galleryStatus_<?php echo $gi; ?>" value="<?php echo $has_img ? 'existing' : 'empty'; ?>" />
                                    <input type="hidden" name="gallery_id_<?php echo $gi; ?>" id="galleryId_<?php echo $gi; ?>" value="<?php echo $has_img ? $slot_data['id'] : ''; ?>" />
                                    <input type="hidden" name="gallery_path_<?php echo $gi; ?>" id="galleryPath_<?php echo $gi; ?>" value="<?php echo $has_img ? htmlspecialchars($slot_data['image']) : ''; ?>" />

                                    <div class="gallery-slot relative group" id="gallerySlot_<?php echo $gi; ?>">
                                        <div class="aspect-square md:aspect-[4/5] rounded-xl border-2 border-dashed border-outline-variant/60 bg-surface-container-lowest hover:border-primary/50 hover:bg-primary/5 transition-all flex flex-col items-center justify-center cursor-pointer overflow-hidden relative" id="gallerySlotBox_<?php echo $gi; ?>">
                                            <!-- Preview Image -->
                                            <img src="<?php echo $img_url; ?>" alt="" id="galleryPreview_<?php echo $gi; ?>" class="w-full h-full object-cover <?php echo $has_img ? '' : 'hidden'; ?> absolute inset-0 rounded-xl" />
                                            
                                            <!-- Overlay for uploading when empty -->
                                            <div class="flex flex-col items-center justify-center pointer-events-none z-10 <?php echo $has_img ? 'hidden' : ''; ?>" id="galleryUploadUi_<?php echo $gi; ?>">
                                                <span class="material-symbols-outlined text-outline/40 text-4xl group-hover:text-primary transition-colors">add_photo_alternate</span>
                                                <span class="text-xs text-outline/50 mt-1 font-bold group-hover:text-primary transition-colors">#<?php echo $gi; ?></span>
                                            </div>

                                            <!-- Overlay details when has image -->
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-30 <?php echo $has_img ? '' : 'hidden'; ?>" id="galleryOverlay_<?php echo $gi; ?>">
                                                <button type="button" onclick="setPrimarySlot(<?php echo $gi; ?>)" class="bg-white/95 hover:bg-white text-primary text-[8px] font-bold uppercase py-1 px-2 rounded shadow hover:scale-105 transition-transform" id="gallerySetPrimaryBtn_<?php echo $gi; ?>">
                                                    Primary
                                                </button>
                                            </div>

                                            <!-- Static Primary Badge -->
                                            <div class="absolute top-1 left-1 bg-primary text-white text-[7px] font-bold uppercase tracking-wider px-1 py-0.5 rounded shadow z-10 <?php echo ($has_img && $gi == $primary_slot_number) ? '' : 'hidden'; ?>" id="galleryPrimaryBadge_<?php echo $gi; ?>">
                                                ★
                                            </div>

                                            <!-- Hidden File Input -->
                                            <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" name="galleryImg_<?php echo $gi; ?>" id="galleryFileInput_<?php echo $gi; ?>" type="file" accept="image/*" onchange="previewGallerySlot(this, <?php echo $gi; ?>)" />
                                        </div>
                                        <button type="button" onclick="clearGallerySlot(<?php echo $gi; ?>)" class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-error text-white <?php echo $has_img ? 'flex' : 'hidden'; ?> items-center justify-center z-30 shadow hover:scale-110 transition-transform" id="galleryClearBtn_<?php echo $gi; ?>">
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
                            <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest placeholder:opacity-50" id="displayOrderInput" name="displayOrder" placeholder="0" type="number" min="0" value="<?php echo htmlspecialchars($product['display_order']); ?>" required />
                        </div>
                         <!-- Status Toggle Switch -->
                         <div class="space-y-2">
                             <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Status</label>
                             <div class="flex items-center gap-3 py-1.5">
                                 <label class="relative inline-flex items-center cursor-pointer select-none">
                                     <input type="hidden" name="status" value="0" />
                                     <input <?php echo $product['active'] ? 'checked' : ''; ?> id="statusToggle" class="peer sr-only" name="status" type="checkbox" value="1" />
                                     <div class="w-12 h-6 bg-outline/20 rounded-full peer peer-checked:after:translate-x-6 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-all shadow-inner"></div>
                                 </label>
                                 <span id="statusLabel" class="font-label-bold text-label-bold text-on-surface-variant/80 select-none"><?php echo $product['active'] ? 'Active' : 'Inactive'; ?></span>
                             </div>
                         </div>
                    </div>

                    <!-- SIZE VARIATIONS BUILDER -->
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
                                    <!-- Render existing variations -->
                                    <?php foreach ($variations as $var): ?>
                                        <tr id="existingVarRow_<?php echo $var['id']; ?>" class="hover:bg-primary/5 transition-colors cursor-grab active:cursor-grabbing">
                                            <td class="py-3 px-2 text-center text-outline/40">
                                                <span class="material-symbols-outlined text-[20px] handle select-none cursor-grab active:cursor-grabbing">drag_indicator</span>
                                                <input type="hidden" name="existingVarIds[]" value="<?php echo $var['id']; ?>" />
                                                <input type="hidden" name="varNo_existing_<?php echo $var['id']; ?>" value="<?php echo htmlspecialchars($var['no']); ?>" />
                                            </td>
                                            <td class="py-3 px-2">
                                                <input class="w-full px-3 py-2 rounded-lg border-outline-variant focus:border-primary text-body-md" name="varCode_existing_<?php echo $var['id']; ?>" value="<?php echo htmlspecialchars($var['code']); ?>" type="text" required />
                                            </td>
<td class="py-3 px-2">
                                                <input class="w-full px-3 py-2 rounded-lg border-outline-variant focus:border-primary text-body-md" name="varName_existing_<?php echo $var['id']; ?>" value="<?php echo htmlspecialchars($var['name'] ?? ''); ?>" placeholder="Optional Name" type="text" />
                                            </td>
                                            <td class="py-3 px-2">
                                                <input class="w-full px-3 py-2 rounded-lg border-outline-variant focus:border-primary text-body-md" name="varSize_existing_<?php echo $var['id']; ?>" value="<?php echo htmlspecialchars($var['size']); ?>" placeholder="e.g. 122x400x500" type="text" required />
                                            </td>
                                            <td class="py-3 px-2">
                                                <div class="flex items-center gap-2">
                                                    <?php if (!empty($var['image'])): ?>
                                                        <?php 
                                                        $v_img = htmlspecialchars($var['image']);
                                                        if (strpos($var['image'], 'http://') !== 0 && strpos($var['image'], 'https://') !== 0) {
                                                            $v_img = $base_path . htmlspecialchars($var['image']);
                                                        }
                                                        ?>
                                                        <img src="<?php echo $v_img; ?>" alt="Var" class="w-8 h-8 rounded object-cover border border-outline-variant" />
                                                    <?php endif; ?>
                                                    <div class="relative cursor-pointer bg-surface-container-low border border-outline-variant rounded-lg py-1.5 px-2 text-center group hover:bg-primary/5 hover:border-primary/30 transition-all flex items-center justify-center gap-1 min-h-[32px] flex-1">
                                                        <span class="material-symbols-outlined text-primary text-[16px]">cloud_upload</span>
                                                        <span class="text-[9px] text-on-surface-variant/80 font-medium truncate select-none var-file-label" id="varFileLabel_existing_<?php echo $var['id']; ?>">
                                                            <?php echo !empty($var['image']) ? 'Change' : 'Upload'; ?>
                                                        </span>
                                                        <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" name="varImgFile_existing_<?php echo $var['id']; ?>" type="file" accept="image/*" onchange="updateExistingVarFileLabel(this, <?php echo $var['id']; ?>)" />
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-3 px-2 text-center" style="display: none;">
                                                <input checked class="rounded border-outline-variant/60 text-secondary focus:ring-secondary/20 cursor-pointer w-4 h-4" name="varActive_existing_<?php echo $var['id']; ?>" type="checkbox" value="1" />
                                            </td>
                                            <td class="py-3 px-2 text-center">
                                                <button type="button" onclick="removeExistingVariationRow(<?php echo $var['id']; ?>)" class="p-1.5 text-error hover:bg-error/15 rounded-lg transition-all active:scale-90">
                                                    <span class="material-symbols-outlined text-[18px] block">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <!-- Empty row placeholder if all variations deleted -->
                                    <tr id="emptyVarRow" style="<?php echo !empty($variations) ? 'display: none;' : ''; ?>">
                                        <td colspan="7" class="py-8 text-center text-on-surface-variant font-medium">
                                            <span class="material-symbols-outlined text-3xl mb-1 block text-outline/60">layers_clear</span>
                                            No size variations added. Click "Add Size Variation" above.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div id="deletedVarContainer"></div>

                    <!-- Submit Actions -->
                    <div class="flex items-center gap-4 pt-6">
                        <a href="list.php" class="flex-1 py-4 border border-outline text-on-surface-variant font-label-bold rounded-xl hover:bg-surface-container-low text-center flex items-center justify-center transition-all">
                            Cancel
                        </a>
                        <button class="flex-1 py-4 bg-primary text-white font-label-bold rounded-xl shadow-lg shadow-primary/20 hover:translate-y-[-2px] hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2" type="submit">
                            <span>Update</span>
                            <span class="material-symbols-outlined text-[20px]">save</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>


<script>
    // Live preview syncing
    const nameInput = document.getElementById('prodNameInput');
    const descInput = document.getElementById('prodDescInput');
    const orderInput = document.getElementById('displayOrderInput');
    const catSelect = document.getElementById('categoryIdSelect');
    const statusToggle = document.getElementById('statusToggle');
    const statusLabel = document.getElementById('statusLabel');

    
 
    
 
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

    catSelect.addEventListener('change', (e) => {
        const selectedText = e.target.options[e.target.selectedIndex].text;
        previewCategory.textContent = selectedText;
    });

    orderInput.addEventListener('input', (e) => {
        previewOrder.textContent = e.target.value || '0';
    });

    if (statusToggle) {
        statusToggle.addEventListener('change', (e) => {
            if (e.target.checked) {
                statusLabel.textContent = 'Active';
                statusBadge.textContent = 'Active';
                statusBadge.className = 'bg-secondary text-on-secondary px-4 py-1.5 rounded-full font-label-bold text-[10px] uppercase shadow-lg shadow-secondary/20';
            } else {
                statusLabel.textContent = 'Inactive';
                statusBadge.textContent = 'Inactive';
                statusBadge.className = 'bg-on-surface-variant text-white px-4 py-1.5 rounded-full font-label-bold text-[10px] uppercase shadow-lg';
            }
        });
    }

    // Gallery & Showcase Logic in Edit View
    const primarySlotInput = document.getElementById('primarySlot');
    
    let activePreviewSlot = null;

    function handleMultiSelect(input) {
        if (!input.files || input.files.length === 0) return;

        const files = Array.from(input.files);
        
        let activeSlots = [];
        for (let gi = 1; gi <= 5; gi++) {
            const status = document.getElementById(`galleryStatus_${gi}`).value;
            if (status !== 'empty') {
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
            const statusInput = document.getElementById(`galleryStatus_${slotNo}`);
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                statusInput.value = 'new';
                updateGalleryState();
            };
            reader.readAsDataURL(file);
        });

        input.value = ''; // Reset input
    }

    function updateGalleryState() {
        let activeSlots = [];
        for (let gi = 1; gi <= 5; gi++) {
            const status = document.getElementById(`galleryStatus_${gi}`).value;
            if (status !== 'empty') {
                activeSlots.push(gi);
            }
        }

        const totalImages = activeSlots.length;
        let currentPrimary = parseInt(primarySlotInput.value);

        // Auto-select primary if not set, or if set slot is empty/deleted
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
            const status = document.getElementById(`galleryStatus_${gi}`).value;
            const box = document.getElementById(`gallerySlotBox_${gi}`);

            const hasImage = (status !== 'empty');

            if (hasImage) {
                uploadUi.classList.add('hidden');
                clearBtn.classList.remove('hidden');
                clearBtn.classList.add('flex');

                if (gi === currentPrimary) {
                    primaryBadge.classList.remove('hidden');
                    overlay.classList.add('hidden'); // Hide Make Primary button since already primary
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
                    overlay.classList.remove('hidden'); // Show Make Primary on hover
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
        if (typeof previewImg !== 'undefined' && previewImg) {
            if (currentPrimary) {
                const primaryPreview = document.getElementById(`galleryPreview_${currentPrimary}`);
                if (primaryPreview) previewImg.src = primaryPreview.src;
            } else if (typeof defaultImg !== 'undefined') {
                previewImg.src = defaultImg;
            }
        }
    }

    function previewGallerySlot(input, slotNo) {
        const preview = document.getElementById(`galleryPreview_${slotNo}`);
        const statusInput = document.getElementById(`galleryStatus_${slotNo}`);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                statusInput.value = 'new';
                updateGalleryState();
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearGallerySlot(slotNo) {
        let activeSlots = [];
        for (let gi = 1; gi <= 5; gi++) {
            const status = document.getElementById(`galleryStatus_${gi}`).value;
            if (status !== 'empty') {
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
        const statusInput = document.getElementById(`galleryStatus_${slotNo}`);
        const fileInput = document.getElementById(`galleryFileInput_${slotNo}`);
        
        if (fileInput) {
            fileInput.value = '';
        }
        if (preview) {
            preview.src = '';
            preview.classList.add('hidden');
        }
        statusInput.value = 'empty';
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
            const status = document.getElementById(`galleryStatus_${gi}`).value;
            if (status !== 'empty') {
                activeSlots.push(gi);
            }
        }

        if (activeSlots.length === 0) {
            e.preventDefault();
            alert("Please keep or upload at least one image in the Product Gallery.");
            return false;
        }
    });

    // Run initial synchronization on edit page load
    updateGalleryState();

    // Interactive Dynamic Size Variations script
    let varIndex = 0;
    const tableBody = document.getElementById('variationsTableBody');
    const emptyRow = document.getElementById('emptyVarRow');
    const deletedVarContainer = document.getElementById('deletedVarContainer');

    function addVariationRow() {
        if (emptyRow) {
            emptyRow.style.display = 'none';
        }

        varIndex++;

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
    }

    function removeVariationRow(index) {
        const row = document.getElementById(`varRow_${index}`);
        if (row) {
            row.remove();
        }

        checkEmptyState();
    }

    function removeExistingVariationRow(id) {
        const row = document.getElementById(`existingVarRow_${id}`);
        if (row) {
            row.remove();
            
            // Append deleted ID to hidden inputs so PHP knows to purge it
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'deletedVarIds[]';
            input.value = id;
            deletedVarContainer.appendChild(input);
        }

        checkEmptyState();
    }

    function checkEmptyState() {
        const rows = tableBody.querySelectorAll('tr:not(#emptyVarRow)');
        if (rows.length === 0 && emptyRow) {
            emptyRow.style.display = '';
        }
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

    function updateExistingVarFileLabel(input, id) {
        const label = document.getElementById(`varFileLabel_existing_${id}`);
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
