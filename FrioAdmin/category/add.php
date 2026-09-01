<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catName = isset($_POST['catName']) ? trim($_POST['catName']) : '';
    $catDesc = isset($_POST['catDesc']) ? trim($_POST['catDesc']) : '';
    $status = isset($_POST['status']) ? intval($_POST['status']) : 1; // 1 = Active, 0 = Inactive
    $displayOrder = isset($_POST['displayOrder']) ? intval($_POST['displayOrder']) : 0;
    
    $catImg = "";

    if (!empty($catName)) {
        try {
            $catImgPaths = [];
            
            // 1. Handle multiple files upload (Required)
            if (isset($_FILES['catImgFiles']) && !empty($_FILES['catImgFiles']['name'][0])) {
                $totalFiles = count($_FILES['catImgFiles']['name']);
                // Limit to max 5 files
                $uploadCount = min($totalFiles, 5);
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $uploadFileDir = $base_path . 'assets/imag/category/';
                
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }
                
                for ($i = 0; $i < $uploadCount; $i++) {
                    if ($_FILES['catImgFiles']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['catImgFiles']['tmp_name'][$i];
                        $fileName = $_FILES['catImgFiles']['name'][$i];
                        
                        $fileNameCmps = explode(".", $fileName);
                        $fileExtension = strtolower(end($fileNameCmps));
                        
                        if (in_array($fileExtension, $allowedExtensions)) {
                            $newFileName = 'cat_' . time() . '_' . md5(uniqid()) . '_' . $i . '.' . $fileExtension;
                            $dest_path = $uploadFileDir . $newFileName;
                            
                            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                $catImgPaths[] = 'assets/imag/category/' . $newFileName;
                            }
                        }
                    }
                }
                
                if (empty($catImgPaths)) {
                    $error = "Upload failed. Please upload valid image files (JPG, PNG, WEBP).";
                } else {
                    // Re-order based on selected primary index
                    $primaryIndex = isset($_POST['primary_index']) ? intval($_POST['primary_index']) : 0;
                    if ($primaryIndex >= 0 && $primaryIndex < count($catImgPaths)) {
                        $primaryPath = $catImgPaths[$primaryIndex];
                        unset($catImgPaths[$primaryIndex]);
                        array_unshift($catImgPaths, $primaryPath);
                        $catImgPaths = array_values($catImgPaths);
                    }
                    $catImg = json_encode($catImgPaths);
                }
            } else {
                $error = "At least one Category Image file is required.";
            }

            if (empty($error)) {
                // Generate a random SKU code (e.g. #FR-8823)
                $code = '#FR-' . rand(8823, 8999);
                
                // Insert category row
                $insert = $pdo->prepare("INSERT INTO `category` (`code`, `name`, `description`, `image`, `active`, `display_order`) VALUES (?, ?, ?, ?, ?, ?)");
                $insert->execute([$code, $catName, $catDesc, $catImg, $status, $displayOrder]);
                
                // Redirect to list with success query param
                header("Location: list.php?msg=created");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "Category Name is required.";
    }
}
?>
<?php
$page_title = "FRIO Admin - Add Category";
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
        <span class="text-primary font-bold">Add Category</span>
    </nav>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- LEFT COLUMN: Form -->
        <section class="lg:col-span-12 max-w-5xl mx-auto w-full">
            <div class="bg-white rounded-[2rem] p-8 md:p-12 shadow-[0px_4px_40px_rgba(12,75,134,0.05)] border border-surface-container">
                <div class="mb-10">
                    <h1 class="text-headline-lg font-headline-lg text-primary">ADD CATEGORY</h1>
                    <p class="text-on-surface-variant mt-2 font-body-md">Create a new high-precision product category for the industrial catalog.</p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="bg-error-container/50 border border-error/20 p-4 rounded-xl flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-error">error</span>
                        <p class="font-label-sm text-label-sm text-on-error-container"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <form class="space-y-8" id="categoryForm" method="POST" action="add.php" enctype="multipart/form-data">
                    <!-- Category Name -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="catNameInput">Category Name <span class="text-error font-bold">*</span></label>
                        <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-lg bg-surface-container-lowest placeholder:opacity-50" id="catNameInput" name="catName" placeholder="Enter category name" type="text" required />
                    </div>

                    <!-- Description (Optional) -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="catDescInput">Description <span class="text-outline text-label-sm font-normal">(Optional)</span></label>
                        <textarea class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest resize-none placeholder:opacity-50" id="catDescInput" name="catDesc" placeholder="Write short category description..." rows="4"></textarea>
                    </div>

                    <!-- Image Upload (Required) -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Category Images (Select 4-5 files) <span class="text-error font-bold">*</span></label>
                        <div class="relative cursor-pointer bg-surface-container-lowest border border-outline-variant/60 hover:bg-primary/5 hover:border-primary/50 transition-all rounded-xl p-6 flex flex-col items-center justify-center min-h-[110px] group">
                            <span class="material-symbols-outlined text-primary text-3xl mb-1 group-hover:scale-110 transition-transform">cloud_upload</span>
                            <span class="text-label-sm text-primary font-bold">Choose Image Files</span>
                        <!-- Visible file picker - name is set here directly -->
                        <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="catImgFileInput" type="file" name="catImgFiles[]" accept="image/*" multiple />
                            <span id="fileNameDisplay" class="text-[10px] text-on-surface-variant/70 mt-1 truncate max-w-xs font-medium">No files selected</span>
                        </div>
                        
                        <!-- Hidden inputs for custom pool submission -->
                        <input type="hidden" name="primary_index" id="primaryIndexInput" value="0" />
                        
                        <!-- Selected Files Previews -->
                        <p class="text-[11px] text-outline font-extrabold uppercase tracking-wider mt-4 mb-2" id="previewHeader" style="display: none;">Selected Images (Hover to remove, mark primary)</p>
                        <div id="imagePreviewGrid" class="flex flex-wrap gap-4"></div>
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
                                    <input checked id="statusToggle" class="peer sr-only" name="status" type="checkbox" value="1" />
                                    <div class="w-12 h-6 bg-outline/20 rounded-full peer peer-checked:after:translate-x-6 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-all shadow-inner"></div>
                                </label>
                                <span id="statusLabel" class="font-label-bold text-label-bold text-on-surface-variant/80 select-none">Active</span>
                             </div>
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
    const nameInput = document.getElementById('catNameInput');
    const descInput = document.getElementById('catDescInput');
    const fileInput = document.getElementById('catImgFileInput');
    const catImgFileInput = fileInput; // named alias used in submit handler
    const primaryIndexInput = document.getElementById('primaryIndexInput');
    const previewGrid = document.getElementById('imagePreviewGrid');
    const previewHeader = document.getElementById('previewHeader');
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

    const defaultImg = "https://lh3.googleusercontent.com/aida-public/AB6AXuB2ZQ8yeQoePJq5Gwlxo2DKly2CS8BFuRajp8W9-EB2GEkGaWnYdWVidnjcUNSQ_GueB6HE6B7tUaTt99qYx1VONkvRjBC1Mfc05PQM-IIb58hYyjtUVPBLPd_iDMPUxpH9-Sg8U-UmXwuIWCwVSZ2g_ge58tw8BXS08Vkh2W65JU5QxcAoHu39ApLe6TI0j9mn4ciHpZMFyvvUI41sSreckbQbqg49L9AvwiE98sRKKNXMTskI-_bTIF_qLllr1U_FicJ8MJj_5UEH";

    let selectedFiles = [];

    nameInput.addEventListener('input', (e) => {
        if (previewTitle) previewTitle.textContent = e.target.value || 'New Category Name';
    });

    descInput.addEventListener('input', (e) => {
        if (previewDesc) previewDesc.textContent = e.target.value || 'Enter a description in the form to see it reflected here.';
    });

    // Custom File Pool Appending
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        
        if (selectedFiles.length + files.length > 5) {
            alert("Maximum 5 category images are allowed.");
            fileInput.value = '';
            return;
        }
        
        selectedFiles = selectedFiles.concat(files);
        fileInput.value = ''; // Clear input so same file can be selected again from different location
        
        renderPreviews();
    });

    function renderPreviews() {
        previewGrid.innerHTML = '';
        
        if (selectedFiles.length === 0) {
            fileNameDisplay.textContent = 'No files selected';
            previewHeader.style.display = 'none';
            if (previewImg) previewImg.src = defaultImg;
            return;
        }
        
        previewHeader.style.display = 'block';
        fileNameDisplay.textContent = `${selectedFiles.length} file(s) selected`;
        
        // Ensure primary index is valid
        let primaryIdx = parseInt(primaryIndexInput.value);
        if (isNaN(primaryIdx) || primaryIdx < 0 || primaryIdx >= selectedFiles.length) {
            primaryIdx = 0;
            primaryIndexInput.value = 0;
        }
        
        selectedFiles.forEach((file, index) => {
            const isPrimary = (index === primaryIdx);
            const objectURL = URL.createObjectURL(file);
            
            // Render first image to sidebar preview if it is primary or if it is the first selected
            if (index === primaryIdx && previewImg) {
                previewImg.src = objectURL;
            }
            
            const card = document.createElement('div');
            card.className = "relative w-24 h-24 rounded-2xl overflow-hidden border border-outline-variant/60 shadow-sm group";
            
            card.innerHTML = `
                <img src="${objectURL}" class="w-full h-full object-cover" />
                
                <!-- Top-Left Primary Selection Badge/Radio -->
                <label class="absolute top-1.5 left-1.5 bg-white/90 rounded-full p-1 cursor-pointer shadow-sm hover:bg-white transition-all flex items-center justify-center z-20" title="Set as primary image">
                    <input type="radio" name="primary_select" value="${index}" ${isPrimary ? 'checked' : ''} class="w-3.5 h-3.5 text-primary border-outline-variant/60 focus:ring-0 cursor-pointer" onchange="setPrimaryIndex(${index})" />
                </label>
                
                <!-- Bottom Delete overlay button -->
                <button type="button" onclick="removeSelectedFile(${index})" class="absolute inset-x-0 bottom-0 bg-black/60 py-1.5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white" title="Remove image">
                    <span class="material-symbols-outlined text-[18px] text-error font-bold">delete</span>
                </button>
            `;
            
            previewGrid.appendChild(card);
        });
    }

    window.setPrimaryIndex = function(index) {
        primaryIndexInput.value = index;
        renderPreviews();
    };

    window.removeSelectedFile = function(index) {
        selectedFiles.splice(index, 1);
        
        let currentPrimary = parseInt(primaryIndexInput.value);
        if (currentPrimary === index) {
            primaryIndexInput.value = 0;
        } else if (currentPrimary > index) {
            primaryIndexInput.value = currentPrimary - 1;
        }
        
        renderPreviews();
    };

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
        if (selectedFiles.length === 0) {
            e.preventDefault();
            alert("Please select at least one category image.");
            return;
        }
        
        // Transfer the in-memory file pool to the actual file input
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        catImgFileInput.files = dataTransfer.files;
    });
</script>
</body></html>
