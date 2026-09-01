<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

$message = "";
$error = "";
$catalogue = null;

// Fetch existing record
if (isset($_GET['id'])) {
    $catId = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM `catalogue` WHERE `id` = ?");
        $stmt->execute([$catId]);
        $catalogue = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Error fetching catalogue: " . $e->getMessage();
    }
}

if (!$catalogue) {
    header("Location: list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catName = isset($_POST['catalogueName']) ? trim($_POST['catalogueName']) : '';
    $displayOrder = isset($_POST['displayOrder']) ? intval($_POST['displayOrder']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;

    $pdfPath = $catalogue['pdf_file'];
    $imgPath = $catalogue['preview_image'];

    try {
        // 1. Handle PDF replacement if uploaded
        if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
            $pdfTmpPath = $_FILES['pdfFile']['tmp_name'];
            $pdfName = $_FILES['pdfFile']['name'];
            
            $pdfNameCmps = explode(".", $pdfName);
            $pdfExtension = strtolower(end($pdfNameCmps));
            
            if ($pdfExtension === 'pdf') {
                $newPdfName = 'brochure_' . time() . '_' . md5(uniqid()) . '.pdf';
                $pdfUploadDir = $base_path . 'assets/pdf/catalogue/';
                
                if (!is_dir($pdfUploadDir)) {
                    mkdir($pdfUploadDir, 0777, true);
                }
                
                $dest_pdf_path = $pdfUploadDir . $newPdfName;
                if (move_uploaded_file($pdfTmpPath, $dest_pdf_path)) {
                    // Unlink old PDF file to preserve storage
                    if (!empty($catalogue['pdf_file']) && file_exists($base_path . $catalogue['pdf_file'])) {
                        unlink($base_path . $catalogue['pdf_file']);
                    }
                    $pdfPath = 'assets/pdf/catalogue/' . $newPdfName;
                } else {
                    $error = "Error saving replacement PDF file.";
                }
            } else {
                $error = "Invalid file type. Only PDF documents (.pdf) are allowed.";
            }
        }

        // 2. Handle Preview Image replacement if uploaded
        if (empty($error)) {
            if (isset($_FILES['coverImgFile']) && $_FILES['coverImgFile']['error'] === UPLOAD_ERR_OK) {
                $imgTmpPath = $_FILES['coverImgFile']['tmp_name'];
                $imgName = $_FILES['coverImgFile']['name'];
                
                $imgNameCmps = explode(".", $imgName);
                $imgExtension = strtolower(end($imgNameCmps));
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($imgExtension, $allowedExtensions)) {
                    $newImgName = 'cover_' . time() . '_' . md5(uniqid()) . '.' . $imgExtension;
                    $imgUploadDir = $base_path . 'assets/imag/catalogue/';
                    
                    if (!is_dir($imgUploadDir)) {
                        mkdir($imgUploadDir, 0777, true);
                    }
                    
                    $dest_img_path = $imgUploadDir . $newImgName;
                    if (move_uploaded_file($imgTmpPath, $dest_img_path)) {
                        // Unlink old cover image file to preserve storage
                        if (!empty($catalogue['preview_image']) && file_exists($base_path . $catalogue['preview_image'])) {
                            unlink($base_path . $catalogue['preview_image']);
                        }
                        $imgPath = 'assets/imag/catalogue/' . $newImgName;
                    } else {
                        $error = "Error saving replacement preview cover image.";
                    }
                } else {
                    $error = "Invalid cover image type. Allowed formats: " . implode(', ', $allowedExtensions);
                }
            }
        }

        if (empty($error) && !empty($catName)) {
            // Update catalogue
            $update = $pdo->prepare("UPDATE `catalogue` SET `name` = ?, `pdf_file` = ?, `preview_image` = ?, `display_order` = ?, `active` = ? WHERE `id` = ?");
            $update->execute([$catName, $pdfPath, $imgPath, $displayOrder, $status, $catId]);
            
            header("Location: list.php?msg=updated");
            exit;
        } elseif (empty($catName)) {
            $error = "Catalogue Name is required.";
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>
<?php
$page_title = "FRIO Admin - Edit Catalogue";
include $base_path . 'includes/head.php';
?>
<body class="bg-background text-on-background font-body-md min-h-screen">
<?php include $base_path . 'includes/sidebar.php'; ?>

<?php
$header_title = 'Catalogue';
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
        <a href="list.php" class="hover:text-primary transition-colors">Catalogue</a>
        <span class="material-symbols-outlined text-[16px] mx-2">chevron_right</span>
        <span class="text-primary font-bold">Edit Catalogue</span>
    </nav>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- LEFT COLUMN: Form -->
        <section class="lg:col-span-12 max-w-5xl mx-auto w-full">
            <div class="bg-white rounded-[2rem] p-8 md:p-12 shadow-[0px_4px_40px_rgba(12,75,134,0.05)] border border-surface-container">
                <div class="mb-10">
                    <h1 class="text-headline-lg font-headline-lg text-primary">EDIT CATALOGUE</h1>
                    <p class="text-on-surface-variant mt-2 font-body-md">Modify details for this technical brochure.</p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="bg-error-container/50 border border-error/20 p-4 rounded-xl flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-error">error</span>
                        <p class="font-label-sm text-label-sm text-on-error-container"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" id="catalogueForm" method="POST" action="edit.php?id=<?php echo $catalogue['id']; ?>" enctype="multipart/form-data">
                    <!-- Catalogue Name -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="catalogueNameInput">Catalogue Name <span class="text-error font-bold">*</span></label>
                        <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-lg bg-surface-container-lowest placeholder:opacity-50" id="catalogueNameInput" name="catalogueName" placeholder="Enter brochure name" type="text" value="<?php echo htmlspecialchars($catalogue['name']); ?>" required />
                    </div>

                    <!-- PDF Document File -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Upload PDF Catalogue <span class="text-outline text-label-sm font-normal">(Optional - Choose to replace)</span></label>
                        <div class="relative cursor-pointer bg-surface-container-lowest border border-outline-variant/60 hover:bg-primary/5 hover:border-primary/50 transition-all rounded-xl p-5 flex flex-col items-center justify-center min-h-[95px] group">
                            <span class="material-symbols-outlined text-error text-3xl mb-1 group-hover:scale-110 transition-transform">picture_as_pdf</span>
                            <span class="text-label-sm text-error font-bold">Choose Replacement PDF File</span>
                            <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="pdfFileInput" name="pdfFile" type="file" accept=".pdf" />
                            <span id="pdfNameDisplay" class="text-[10px] text-on-surface-variant/70 mt-1 truncate max-w-xs font-medium">Using existing PDF document</span>
                        </div>
                    </div>

                    <!-- Cover Preview Image -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Cover Preview Image <span class="text-outline text-label-sm font-normal">(Optional - Choose to replace)</span></label>
                        <?php if (!empty($catalogue['preview_image'])): ?>
                            <div class="mb-3 rounded-xl overflow-hidden border border-outline-variant max-w-sm">
                                <img src="<?php echo $base_path . htmlspecialchars($catalogue['preview_image']); ?>" alt="Current Catalogue Image" class="w-full h-auto object-cover max-h-48" />
                            </div>
                        <?php endif; ?>
                        <div class="relative cursor-pointer bg-surface-container-lowest border border-outline-variant/60 hover:bg-primary/5 hover:border-primary/50 transition-all rounded-xl p-5 flex flex-col items-center justify-center min-h-[95px] group">
                            <span class="material-symbols-outlined text-primary text-3xl mb-1 group-hover:scale-110 transition-transform">image</span>
                            <span class="text-label-sm text-primary font-bold">Choose Replacement Cover Image</span>
                            <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="coverImgFileInput" name="coverImgFile" type="file" accept="image/*" />
                            <span id="coverNameDisplay" class="text-[10px] text-on-surface-variant/70 mt-1 truncate max-w-xs font-medium">Using existing cover preview</span>
                        </div>
                        <p class="text-[11px] text-on-surface-variant/80 mt-2 leading-relaxed ml-1">
                            <span class="text-primary font-bold">Recommended size:</span> WebP or JPG cover preview files <strong class="font-bold">under 300 KB</strong> to ensure lightning-fast storefront browsing speeds.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Display Order -->
                        <div class="space-y-2">
                            <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="displayOrderInput">Display Order</label>
                            <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest placeholder:opacity-50" id="displayOrderInput" name="displayOrder" placeholder="0" type="number" min="0" value="<?php echo htmlspecialchars($catalogue['display_order']); ?>" required />
                        </div>

                        <!-- Status Toggle Switch -->
                        <div class="space-y-2">
                            <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Status</label>
                            <div class="flex items-center gap-3 py-1.5">
                                <label class="relative inline-flex items-center cursor-pointer select-none">
                                    <input type="hidden" name="status" value="0" />
                                    <input <?php echo $catalogue['active'] ? 'checked' : ''; ?> id="statusToggle" class="peer sr-only" name="status" type="checkbox" value="1" />
                                    <div class="w-12 h-6 bg-outline/20 rounded-full peer peer-checked:after:translate-x-6 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-all shadow-inner"></div>
                                </label>
                                <span id="statusLabel" class="<?php echo $catalogue['active'] ? 'font-label-bold text-label-bold text-primary' : 'font-label-bold text-label-bold text-on-surface-variant/50'; ?> select-none"><?php echo $catalogue['active'] ? 'Active' : 'Inactive'; ?></span>
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
    // Live preview syncing inputs
    const nameInput = document.getElementById('catalogueNameInput');
    const pdfFileInput = document.getElementById('pdfFileInput');
    const pdfNameDisplay = document.getElementById('pdfNameDisplay');
    const coverImgFileInput = document.getElementById('coverImgFileInput');
    const coverNameDisplay = document.getElementById('coverNameDisplay');
    const orderInput = document.getElementById('displayOrderInput');
    const statusToggle = document.getElementById('statusToggle');
    const statusLabel = document.getElementById('statusLabel');

    // Live preview mockup elements
    const previewTitle = document.getElementById('previewTitle');
    const previewImg = document.getElementById('previewImg');
    const previewOrder = document.getElementById('previewOrder');
    const statusBadge = document.getElementById('statusBadge');

    nameInput.addEventListener('input', (e) => {
        previewTitle.textContent = e.target.value || 'New Brochure Catalogue';
    });

    // Handle PDF upload event
    pdfFileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            pdfNameDisplay.textContent = file.name;
        } else {
            pdfNameDisplay.textContent = 'Using existing PDF document';
        }
    });

    // Handle Preview Image upload event
    coverImgFileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            coverNameDisplay.textContent = file.name;
            const reader = new FileReader();
            reader.onload = (event) => {
                previewImg.src = event.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            coverNameDisplay.textContent = 'Using existing cover preview';
        }
    });

    orderInput.addEventListener('input', (e) => {
        previewOrder.textContent = '#' + (e.target.value || '0');
    });

    if (statusToggle) {
        statusToggle.addEventListener('change', (e) => {
            if (e.target.checked) {
                statusLabel.textContent = 'Active';
                statusLabel.className = 'font-label-bold text-label-bold text-primary select-none';
                statusBadge.textContent = 'Active';
                statusBadge.className = 'bg-secondary text-on-secondary px-3 py-1 rounded-full font-label-bold text-[9px] uppercase shadow-md inline-block';
            } else {
                statusLabel.textContent = 'Inactive';
                statusLabel.className = 'font-label-bold text-label-bold text-on-surface-variant/50 select-none';
                statusBadge.textContent = 'Inactive';
                statusBadge.className = 'bg-slate-700 text-slate-300 px-3 py-1 rounded-full font-label-bold text-[9px] uppercase shadow-md inline-block';
            }
        });
    }
</script>
</body></html>
