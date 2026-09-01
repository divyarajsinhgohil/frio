<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

$message = "";
$error = "";

// Ensure a valid ID is provided
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: list.php");
    exit;
}

// Fetch existing record
try {
    $stmt = $pdo->prepare("SELECT * FROM `category` WHERE `id` = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
    
    if (!$category) {
        header("Location: list.php");
        exit;
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catName = isset($_POST['catName']) ? trim($_POST['catName']) : '';
    $catDesc = isset($_POST['catDesc']) ? trim($_POST['catDesc']) : '';
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1; // 1 = Active, 0 = Inactive
    $displayOrder = isset($_POST['displayOrder']) ? intval($_POST['displayOrder']) : 0;

    $kept_images = isset($_POST['kept_images']) ? $_POST['kept_images'] : [];
    $catImgPaths = $kept_images;

    if (!empty($catName)) {
        try {
            // Handle file upload if new files are uploaded
            $uploaded_paths_by_index = [];
            if (isset($_FILES['catImgFiles']) && !empty($_FILES['catImgFiles']['name'][0])) {
                $totalFiles = count($_FILES['catImgFiles']['name']);
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $uploadFileDir = $base_path . 'assets/imag/category/';
                
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }

                for ($i = 0; $i < $totalFiles; $i++) {
                    if ($_FILES['catImgFiles']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['catImgFiles']['tmp_name'][$i];
                        $fileName = $_FILES['catImgFiles']['name'][$i];
                        
                        $fileNameCmps = explode(".", $fileName);
                        $fileExtension = strtolower(end($fileNameCmps));
                        
                        if (in_array($fileExtension, $allowedExtensions)) {
                            $newFileName = 'cat_' . time() . '_' . md5(uniqid()) . '_' . $i . '.' . $fileExtension;
                            $dest_path = $uploadFileDir . $newFileName;
                            
                            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                $web_path = 'assets/imag/category/' . $newFileName;
                                $uploaded_paths_by_index[$i] = $web_path;
                                $catImgPaths[] = $web_path;
                            }
                        }
                    }
                }
            }

            // Cap at 5 images total
            $catImgPaths = array_slice($catImgPaths, 0, 5);

            // Re-order based on selected primary image
            $primary_image = isset($_POST['primary_image']) ? $_POST['primary_image'] : '';
            if (!empty($primary_image)) {
                $primary_image_path = '';
                if (strpos($primary_image, 'newfile_') === 0) {
                    $idx = intval(substr($primary_image, 8)); // extract index
                    if (isset($uploaded_paths_by_index[$idx])) {
                        $primary_image_path = $uploaded_paths_by_index[$idx];
                    }
                } else {
                    $primary_image_path = $primary_image;
                }

                if (!empty($primary_image_path)) {
                    $key = array_search($primary_image_path, $catImgPaths);
                    if ($key !== false) {
                        unset($catImgPaths[$key]);
                        array_unshift($catImgPaths, $primary_image_path);
                    }
                }
            }
            $catImgPaths = array_values($catImgPaths);

            if (empty($catImgPaths)) {
                $error = "At least one category image is required.";
            } else {
                $catImg = json_encode($catImgPaths);
                
                // Optional: Delete local files of removed images to conserve space
                $old_val = $category['image'];
                $old_images = [];
                $decoded = json_decode($old_val, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $old_images = $decoded;
                } elseif (!empty($old_val)) {
                    $old_images = [$old_val];
                }
                
                foreach ($old_images as $old_img) {
                    if (!in_array($old_img, $kept_images)) {
                        // Deleted image!
                        if (!empty($old_img) && strpos($old_img, 'http://') !== 0 && strpos($old_img, 'https://') !== 0) {
                            $old_file = $base_path . $old_img;
                            if (file_exists($old_file)) {
                                unlink($old_file);
                            }
                        }
                    }
                }

                // Update category row
                $update = $pdo->prepare("UPDATE `category` SET `name` = ?, `description` = ?, `image` = ?, `active` = ?, `display_order` = ? WHERE `id` = ?");
                $update->execute([$catName, $catDesc, $catImg, $status, $displayOrder, $id]);
                
                // Redirect to list with success query param
                header("Location: list.php?msg=updated");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Category Name is required.";
    }
}

// Compute dynamic preview source path
$preview_img_src = htmlspecialchars($category['image']);
if (strpos($category['image'], 'http://') !== 0 && strpos($category['image'], 'https://') !== 0) {
    $preview_img_src = $base_path . htmlspecialchars($category['image']);
}
?>
<?php
$page_title = "FRIO Admin - Edit Category";
include $base_path . 'includes/head.php';
?>
<body class="bg-background text-on-background font-body-md min-h-screen">
<?php include $base_path . 'includes/sidebar.php'; ?>

<?php
$header_title = 'Category';
include $base_path . 'includes/header.php';
?>
<!-- Main Content -->
<main class="ml-64 pt-24 px-gutter pb-12">
    <!-- Breadcrumbs in Canvas -->
    <nav class="flex items-center text-label-sm text-on-surface-variant mb-8 max-w-7xl mx-auto">
        <a href="<?php echo $base_path; ?>dashbord.php" class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">dashboard</span>
            <span>Dashboard</span>
        </a>
        <span class="material-symbols-outlined text-[16px] mx-2">chevron_right</span>
        <a href="list.php" class="hover:text-primary transition-colors">Category</a>
        <span class="material-symbols-outlined text-[16px] mx-2">chevron_right</span>
        <span class="text-primary font-bold">Edit Category</span>
    </nav>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- LEFT COLUMN: Form -->
        <section class="lg:col-span-12 max-w-5xl mx-auto w-full">
            <div class="bg-white rounded-[2rem] p-8 md:p-12 shadow-[0px_4px_40px_rgba(12,75,134,0.05)] border border-surface-container">
                <div class="mb-10">
                    <h1 class="text-headline-lg font-headline-lg text-primary">EDIT CATEGORY</h1>
                    <p class="text-on-surface-variant mt-2 font-body-md">Modify category attributes and display details for <b><?php echo htmlspecialchars($category['code']); ?></b>.</p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="bg-error-container/50 border border-error/20 p-4 rounded-xl flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-error">error</span>
                        <p class="font-label-sm text-label-sm text-on-error-container"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <form class="space-y-8" id="categoryForm" method="POST" action="edit.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
                    <!-- Category Name -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="catNameInput">Category Name <span class="text-error font-bold">*</span></label>
                        <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-lg bg-surface-container-lowest placeholder:opacity-50" id="catNameInput" name="catName" placeholder="Enter category name" type="text" value="<?php echo htmlspecialchars($category['name']); ?>" required />
                    </div>

                    <!-- Description (Optional) -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="catDescInput">Description <span class="text-outline text-label-sm font-normal">(Optional)</span></label>
                        <textarea class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest resize-none placeholder:opacity-50" id="catDescInput" name="catDesc" placeholder="Write short category description..." rows="4"><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>

                    <!-- Image Upload (Optional for Edit) -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Category Images (Select up to 5 files total)</label>
                        <?php 
                        $current_images = [];
                        $img_val = $category['image'];
                        $decoded = json_decode($img_val, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $current_images = $decoded;
                        } elseif (!empty($img_val)) {
                            $current_images = [$img_val];
                        }
                        ?>
                        
                        <p class="text-[11px] text-outline font-extrabold uppercase tracking-wider mb-2">Category Images (Hover to remove, mark primary)</p>
                        
                        <!-- Unified previews grid -->
                        <div id="imagePreviewGrid" class="flex flex-wrap gap-4 mb-5">
                            <?php foreach ($current_images as $idx => $img_path): 
                                $display_src = (strpos($img_path, 'http') === 0) ? $img_path : $base_path . $img_path;
                            ?>
                                <div class="relative w-24 h-24 rounded-2xl overflow-hidden border border-outline-variant/60 shadow-sm group current-img-card" data-path="<?php echo htmlspecialchars($img_path); ?>">
                                    <img src="<?php echo htmlspecialchars($display_src); ?>" class="w-full h-full object-cover" />
                                    
                                    <!-- Bottom Delete overlay button -->
                                    <button type="button" onclick="removeCurrentImage(this)" class="absolute inset-x-0 bottom-0 bg-black/60 py-1.5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white" title="Remove image">
                                        <span class="material-symbols-outlined text-[18px] text-error font-bold">delete</span>
                                    </button>
                                    
                                    <!-- Top-Left Primary Selection Badge/Radio -->
                                    <label class="absolute top-1.5 left-1.5 bg-white/90 rounded-full p-1 cursor-pointer shadow-sm hover:bg-white transition-all flex items-center justify-center z-20" title="Set as primary image">
                                        <input type="radio" name="primary_image" value="<?php echo htmlspecialchars($img_path); ?>" <?php echo ($idx === 0) ? 'checked' : ''; ?> class="w-3.5 h-3.5 text-primary border-outline-variant/60 focus:ring-0 cursor-pointer" onchange="updateSidebarPreview(this.value)" />
                                    </label>
                                    
                                    <input type="hidden" name="kept_images[]" value="<?php echo htmlspecialchars($img_path); ?>" />
                                </div>
                            <?php endforeach; ?>
                            
                            <!-- Dynamic JS-rendered cards will append here -->
                        </div>

                        <div class="relative cursor-pointer bg-surface-container-lowest border border-outline-variant/60 hover:bg-primary/5 hover:border-primary/50 transition-all rounded-xl p-6 flex flex-col items-center justify-center min-h-[110px] group">
                            <span class="material-symbols-outlined text-primary text-3xl mb-1 group-hover:scale-110 transition-transform">cloud_upload</span>
                            <span class="text-label-sm text-primary font-bold">Upload New Image Files (Optional)</span>
                        <!-- Visible file picker with correct name -->
                        <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="catImgFileInput" type="file" name="catImgFiles[]" accept="image/*" multiple />
                            <span id="fileNameDisplay" class="text-[10px] text-on-surface-variant/70 mt-1 truncate max-w-xs font-medium">No files selected</span>
                        </div>
                        
                        <!-- Hidden file input removed - using catImgFileInput directly -->
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Display Order -->
                        <div class="space-y-2">
                            <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="displayOrderInput">Display Order</label>
                            <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest placeholder:opacity-50" id="displayOrderInput" name="displayOrder" placeholder="0" type="number" min="0" value="<?php echo htmlspecialchars($category['display_order']); ?>" required />
                        </div>

                        <!-- Status Toggle Switch -->
                        <div class="space-y-2">
                            <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Status</label>
                            <div class="flex items-center gap-3 py-1.5">
                                <label class="relative inline-flex items-center cursor-pointer select-none">
                                    <input type="hidden" name="status" value="0" />
                                    <input <?php echo $category['active'] ? 'checked' : ''; ?> id="statusToggle" class="peer sr-only" name="status" type="checkbox" value="1" />
                                    <div class="w-12 h-6 bg-outline/20 rounded-full peer peer-checked:after:translate-x-6 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-all shadow-inner"></div>
                                </label>
                                <span id="statusLabel" class="font-label-bold text-label-bold text-on-surface-variant/80 select-none"><?php echo $category['active'] ? 'Active' : 'Inactive'; ?></span>
                             </div>
                        </div>
                    </div>

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

        
    </div>
    <?php include $base_path . 'includes/footer.php'; ?>
</main>

<script>
    // Live preview syncing
    const nameInput = document.getElementById('catNameInput');
    const descInput = document.getElementById('catDescInput');
    const fileInput = document.getElementById('catImgFileInput');
    const catImgFileInput = fileInput; // alias for submit handler
    const previewGrid = document.getElementById('imagePreviewGrid');
    const form = document.getElementById('categoryForm');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const orderInput = document.getElementById('displayOrderInput');
    const statusToggle = document.getElementById('statusToggle');
    const statusLabel = document.getElementById('statusLabel');

    const previewTitle = document.getElementById('previewTitle');
    const previewDesc = document.getElementById('previewDesc');
    const previewImg = document.getElementById('previewImg');
    const previewOrder = document.getElementById('previewOrder');
    const statusBadge = document.getElementById('statusBadge');

    const originalImg = "<?php echo $preview_img_src; ?>";

    let newFiles = [];

    nameInput.addEventListener('input', (e) => {
        if (previewTitle) previewTitle.textContent = e.target.value || 'New Category Name';
    });

    descInput.addEventListener('input', (e) => {
        if (previewDesc) previewDesc.textContent = e.target.value || 'Enter a description in the form to see it reflected here.';
    });

    window.removeCurrentImage = function(btn) {
        const card = btn.closest('.current-img-card');
        if (card) {
            const wasChecked = card.querySelector('input[name="primary_image"]')?.checked;
            card.remove();
            if (wasChecked) {
                ensurePrimarySelected();
            }
        }
    };

    // Custom File Pool Appending
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        const currentCount = document.querySelectorAll('.current-img-card').length;
        
        if (currentCount + newFiles.length + files.length > 5) {
            alert("Maximum 5 category images are allowed.");
            fileInput.value = '';
            return;
        }
        
        newFiles = newFiles.concat(files);
        fileInput.value = ''; // Clear input so subsequent selections can be made
        
        renderNewPreviews();
    });

    function renderNewPreviews() {
        // Remove all previous new-img-card elements first
        document.querySelectorAll('.new-img-card').forEach(el => el.remove());
        
        if (newFiles.length === 0) {
            fileNameDisplay.textContent = 'No files selected';
            return;
        }
        
        fileNameDisplay.textContent = `${newFiles.length} new file(s) selected`;
        
        newFiles.forEach((file, index) => {
            const objectURL = URL.createObjectURL(file);
            const cardValue = `newfile_${index}`;
            
            const card = document.createElement('div');
            card.className = "relative w-24 h-24 rounded-2xl overflow-hidden border border-outline-variant/60 shadow-sm group new-img-card";
            
            card.innerHTML = `
                <img src="${objectURL}" class="w-full h-full object-cover" />
                
                <!-- Bottom Delete overlay button -->
                <button type="button" onclick="removeNewFile(${index})" class="absolute inset-x-0 bottom-0 bg-black/60 py-1.5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white" title="Remove image">
                    <span class="material-symbols-outlined text-[18px] text-error font-bold">delete</span>
                </button>
                
                <!-- Top-Left Primary Selection Badge/Radio -->
                <label class="absolute top-1.5 left-1.5 bg-white/90 rounded-full p-1 cursor-pointer shadow-sm hover:bg-white transition-all flex items-center justify-center z-20" title="Set as primary image">
                    <input type="radio" name="primary_image" value="${cardValue}" class="w-3.5 h-3.5 text-primary border-outline-variant/60 focus:ring-0 cursor-pointer" onchange="updateSidebarPreview(this.value)" />
                </label>
            `;
            
            previewGrid.appendChild(card);
        });
        
        ensurePrimarySelected();
    }

    window.removeNewFile = function(index) {
        // Track whether the one being removed was checked
        const cardValue = `newfile_${index}`;
        const radio = document.querySelector(`input[name="primary_image"][value="${cardValue}"]`);
        const wasChecked = radio ? radio.checked : false;
        
        newFiles.splice(index, 1);
        renderNewPreviews();
        
        if (wasChecked) {
            ensurePrimarySelected();
        }
    };

    window.updateSidebarPreview = function(val) {
        if (!previewImg) return;
        if (val.startsWith('newfile_')) {
            const idx = parseInt(val.substring(8));
            if (!isNaN(idx) && newFiles[idx]) {
                previewImg.src = URL.createObjectURL(newFiles[idx]);
            }
        } else {
            const base = "<?php echo $base_path; ?>";
            const src = (val.startsWith('http') === 0) ? val : base + val;
            previewImg.src = src;
        }
    };

    function ensurePrimarySelected() {
        const checkedRadio = document.querySelector('input[name="primary_image"]:checked');
        if (!checkedRadio) {
            const firstRadio = document.querySelector('input[name="primary_image"]');
            if (firstRadio) {
                firstRadio.checked = true;
                updateSidebarPreview(firstRadio.value);
            } else {
                if (previewImg) previewImg.src = originalImg;
            }
        }
    }

    orderInput.addEventListener('input', (e) => {
        if (previewOrder) previewOrder.textContent = e.target.value || '0';
    });

    if (statusToggle) {
        statusToggle.addEventListener('change', (e) => {
            if (e.target.checked) {
                statusLabel.textContent = 'Active';
                if (statusBadge) {
                    statusBadge.textContent = 'Active';
                    statusBadge.className = 'bg-secondary text-on-secondary px-4 py-1.5 rounded-full font-label-bold text-[10px] uppercase shadow-lg shadow-secondary/20';
                }
            } else {
                statusLabel.textContent = 'Inactive';
                if (statusBadge) {
                    statusBadge.textContent = 'Inactive';
                    statusBadge.className = 'bg-on-surface-variant text-white px-4 py-1.5 rounded-full font-label-bold text-[10px] uppercase shadow-lg';
                }
            }
        });
    }

    form.addEventListener('submit', (e) => {
        const currentCount = document.querySelectorAll('.current-img-card').length;
        if (currentCount + newFiles.length === 0) {
            e.preventDefault();
            alert("At least one category image is required.");
            return;
        }
        
        // Transfer the in-memory new-file pool to the named file input
        const dataTransfer = new DataTransfer();
        newFiles.forEach(file => dataTransfer.items.add(file));
        catImgFileInput.files = dataTransfer.files;
    });
</script>
</body></html>
