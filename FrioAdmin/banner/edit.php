<?php
$base_path = '../';
require_once $base_path . 'auth_check.php';
require_once $base_path . 'db_connect.php';

$message = "";
$error = "";
$banner = null;

// Fetch existing record
if (isset($_GET['id'])) {
    $bannerId = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM `banner_slider` WHERE `id` = ?");
        $stmt->execute([$bannerId]);
        $banner = $stmt->fetch();
    } catch (PDOException $e) {
        $error = "Error fetching banner: " . $e->getMessage();
    }
}

if (!$banner) {
    header("Location: list.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bannerName = trim($_POST['bannerName'] ?? '');
    $bannerDesc = trim($_POST['bannerDesc'] ?? '');
    $buttonLink = trim($_POST['buttonLink'] ?? '');
    $textAlign  = trim($_POST['textAlign'] ?? 'center');
    $status     = isset($_POST['status']) && $_POST['status'] == '1' ? 1 : 0;

    $bannerImg = $banner['image']; // Default to current image path

    try {
        // Handle file upload if new one was uploaded
        if (isset($_FILES['bannerImgFile']) && $_FILES['bannerImgFile']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['bannerImgFile']['tmp_name'];
            $fileName = $_FILES['bannerImgFile']['name'];
            $fileSize = $_FILES['bannerImgFile']['size'];
            $fileType = $_FILES['bannerImgFile']['type'];
            
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = 'banner_' . time() . '_' . md5(uniqid()) . '.' . $fileExtension;
                $uploadFileDir = $base_path . 'assets/imag/banners/';
                
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }
                
                $dest_path = $uploadFileDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Delete old banner file to conserve disk space
                    if (!empty($banner['image']) && strpos($banner['image'], 'http://') !== 0 && strpos($banner['image'], 'https://') !== 0) {
                        $old_file = $base_path . $banner['image'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                    $bannerImg = 'assets/imag/banners/' . $newFileName;
                } else {
                    $error = "There was an error moving the uploaded image file on the server.";
                }
            } else {
                $error = "Upload failed. Supported formats: " . implode(', ', $allowedExtensions);
            }
        }

        if (empty($error)) {
            // Update table
            $update = $pdo->prepare("UPDATE `banner_slider` SET `name` = ?, `description` = ?, `button_link` = ?, `text_align` = ?, `image` = ?, `active` = ? WHERE `id` = ?");
            $update->execute([$bannerName, $bannerDesc, $buttonLink, $textAlign, $bannerImg, $status, $bannerId]);
            
            header("Location: list.php?msg=updated");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>
<?php
$page_title = "FRIO Admin - Edit Banner Slider";
include $base_path . 'includes/head.php';
?>
<body class="bg-background text-on-background font-body-md min-h-screen">
<?php include $base_path . 'includes/sidebar.php'; ?>

<?php
$header_title = 'Banner Slider';
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
        <a href="list.php" class="hover:text-primary transition-colors">Banner Slider</a>
        <span class="material-symbols-outlined text-[16px] mx-2">chevron_right</span>
        <span class="text-primary font-bold">Edit Banner</span>
    </nav>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- LEFT COLUMN: Form -->
        <section class="lg:col-span-12 w-full max-w-5xl mx-auto space-y-6">
            <!-- Form Card -->
            <div class="bg-white rounded-[2rem] p-8 md:p-12 shadow-[0px_4px_40px_rgba(12,75,134,0.05)] border border-surface-container">
                <div class="mb-8">
                    <h1 class="text-headline-lg font-headline-lg text-primary">EDIT BANNER SLIDER</h1>
                    <p class="text-on-surface-variant mt-2 font-body-md">Modify details for this homepage promo slider.</p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="bg-error-container/50 border border-error/20 p-4 rounded-xl flex items-center gap-3 mb-6">
                        <span class="material-symbols-outlined text-error">error</span>
                        <p class="font-label-sm text-label-sm text-on-error-container"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" id="bannerForm" method="POST" action="edit.php?id=<?php echo $banner['id']; ?>" enctype="multipart/form-data">
                    <!-- Banner Title Name -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="bannerNameInput">Banner Title Name <span class="text-outline text-label-sm font-normal">(Optional)</span></label>
                        <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-lg bg-surface-container-lowest placeholder:opacity-50" id="bannerNameInput" name="bannerName" placeholder="Enter banner heading text" type="text" value="<?php echo htmlspecialchars($banner['name']); ?>" />
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="bannerDescInput">Description / Subtitle <span class="text-outline text-label-sm font-normal">(Optional)</span></label>
                        <textarea class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest resize-none placeholder:opacity-50" id="bannerDescInput" name="bannerDesc" placeholder="Write promo caption or technical highlight text..." rows="3"><?php echo htmlspecialchars($banner['description']); ?></textarea>
                    </div>

                    <!-- Button Action Link -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="buttonLinkInput">Action Button URL Link <span class="text-outline text-label-sm font-normal">(Optional)</span></label>
                        <input class="w-full px-6 py-4 rounded-xl border-outline-variant focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 text-body-md bg-surface-container-lowest placeholder:opacity-50" id="buttonLinkInput" name="buttonLink" placeholder="e.g. /product/list.php or https://frio.co/shop" type="text" value="<?php echo htmlspecialchars($banner['button_link'] ?? ''); ?>" />
                    </div>

                    <!-- Text Alignment -->
                    <?php $align = $banner['text_align'] ?? 'center'; ?>
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Text Alignment Overlay</label>
                        <div class="flex gap-6 bg-surface-container-lowest p-4 rounded-xl border border-outline-variant/60">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="textAlign" value="left" class="text-primary focus:ring-primary w-4 h-4 border-outline-variant" <?php echo $align === 'left' ? 'checked' : ''; ?> />
                                <span class="text-body-md text-on-surface group-hover:text-primary transition-colors"><span class="material-symbols-outlined align-middle text-[18px] mr-1">format_align_left</span> Left</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="textAlign" value="center" class="text-primary focus:ring-primary w-4 h-4 border-outline-variant" <?php echo $align === 'center' ? 'checked' : ''; ?> />
                                <span class="text-body-md text-on-surface group-hover:text-primary transition-colors"><span class="material-symbols-outlined align-middle text-[18px] mr-1">format_align_center</span> Center</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="textAlign" value="right" class="text-primary focus:ring-primary w-4 h-4 border-outline-variant" <?php echo $align === 'right' ? 'checked' : ''; ?> />
                                <span class="text-body-md text-on-surface group-hover:text-primary transition-colors"><span class="material-symbols-outlined align-middle text-[18px] mr-1">format_align_right</span> Right</span>
                            </label>
                        </div>
                    </div>

                    <!-- Image Upload Section -->
                    <div class="space-y-2">
                        <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Banner Graphic Image <span class="text-outline text-label-sm font-normal">(Optional - Choose to replace)</span></label>
                        <?php if (!empty($banner['image'])): ?>
                            <div class="mb-3 rounded-xl overflow-hidden border border-outline-variant max-w-sm">
                                <img src="<?php echo $base_path . htmlspecialchars($banner['image']); ?>" alt="Current Banner" class="w-full h-auto object-cover max-h-48" />
                            </div>
                        <?php endif; ?>
                        <div class="relative cursor-pointer bg-surface-container-lowest border border-outline-variant/60 hover:bg-primary/5 hover:border-primary/50 transition-all rounded-xl p-6 flex flex-col items-center justify-center min-h-[110px] group">
                            <span class="material-symbols-outlined text-primary text-3xl mb-1 group-hover:scale-110 transition-transform">cloud_upload</span>
                            <span class="text-label-sm text-primary font-bold">Choose Replacement Banner Image File</span>
                            <input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="bannerImgFileInput" name="bannerImgFile" type="file" accept="image/*" />
                            <span id="fileNameDisplay" class="text-[10px] text-on-surface-variant/70 mt-1 truncate max-w-xs font-medium">Using existing banner file</span>
                        </div>
                        <p class="text-[11px] text-on-surface-variant/80 mt-2 leading-relaxed ml-1">
                            <span class="text-primary font-bold">Recommended size:</span> 1900 &times; 600 px (Supports Desktop & Laptop perfectly). Keep critical details/text within the central 1200 px zone to prevent mobile portrait cropping. <span class="text-secondary font-bold">File size should be under 500 KB</span> (preferably WebP or JPG) for optimized page speed.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Status Toggle Switch -->
                        <div class="space-y-2">
                            <label class="font-label-bold text-label-bold text-on-surface-variant ml-1">Status</label>
                            <div class="flex items-center gap-3 py-1.5">
                                <label class="relative inline-flex items-center cursor-pointer select-none">
                                    <input type="hidden" name="status" value="0" />
                                    <input <?php echo $banner['active'] ? 'checked' : ''; ?> id="statusToggle" class="peer sr-only" name="status" type="checkbox" value="1" />
                                    <div class="w-12 h-6 bg-outline/20 rounded-full peer peer-checked:after:translate-x-6 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-all shadow-inner"></div>
                                </label>
                                <span id="statusLabel" class="<?php echo $banner['active'] ? 'font-label-bold text-label-bold text-primary' : 'font-label-bold text-label-bold text-on-surface-variant/50'; ?> select-none"><?php echo $banner['active'] ? 'Active' : 'Inactive'; ?></span>
                             </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center gap-4 pt-4">
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
    </script>
<!-- TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  if (typeof tinymce !== 'undefined') {
      tinymce.init({
        selector: '#bannerDescInput',
        plugins: 'lists link code',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link code',
        menubar: false,
        height: 300,
        promotion: false,
        setup: function(editor) {
            editor.on('change keyup paste', function(e) {
                editor.save();
                const textarea = document.getElementById('bannerDescInput');
                if (textarea) {
                    // Manually dispatch input event for live preview if needed
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }
      });
  }
</script>
</body></html>
