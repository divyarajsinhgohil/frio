<?php
require_once 'config.php';
$settings   = api_fetch('settings.php') ?? [];
$catalogues = api_fetch('catalogues.php') ?? [];
$page_title  = 'Technical Catalogues | FRIO Industrial';
$meta_desc   = 'Download FRIO technical catalogues, brochures, and specification guides for precision brass fittings.';
$active_page = 'catalogue';
include 'includes/header.php';
?>
</div> <!-- Close global wrapper for full-bleed hero banner -->


<!-- Hero Banner -->
<section class="relative overflow-hidden py-16 md:py-24 bg-gradient-to-b from-[#071322] to-[#0b192c] flex items-center justify-center border-b border-white/5">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(254,214,91,0.05),transparent_40%)] pointer-events-none"></div>
    <div class="text-center px-8 relative z-10">
        <nav class="flex items-center gap-2 text-sm text-white/80 mb-5 justify-center">
            <a href="index.php" class="hover:text-white transition-colors font-medium">Home</a>
            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            <span class="text-[#ffe088] font-bold">Catalogues</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 font-headline-lg tracking-tight">Technical Catalogues</h1>
        <p class="text-white/85 text-sm md:text-base max-w-2xl leading-relaxed font-medium">Download our comprehensive technical brochures, CAD specifications, and product catalog guides</p>
    </div>
</section>

<!-- Reopen centered wrapper for page content -->
<div class="max-w-[1700px] mx-auto w-full flex-1 flex flex-col relative">

<!-- Background Gradient Body (Item 14) -->
<main class="w-full px-4 md:px-8 py-16 bg-gradient-to-b from-[#f8f9fc] via-[#f0f4fc] to-[#f8f9fc]">
    
    <?php
    $display_catalogues = [];

    // 1. Fetch catalogues from database
    if (!empty($catalogues)) {
        foreach ($catalogues as $c) {
            $display_catalogues[] = [
                'id'            => $c['id'] ?? 0,
                'name'          => $c['name'] ?? 'Technical Catalogue',
                'preview_image' => !empty($c['preview_image']) ? asset_url($c['preview_image']) : '',
                'pdf_file'      => !empty($c['pdf_file']) ? asset_url($c['pdf_file']) : '',
                'type'          => 'Brochure',
                'pages'         => '48 Pages',
                'category'      => 'HVAC Brass Fittings',
                'downloads'     => '1,840 Downloads',
                'badge'         => 'NEW',
                'badge_sub'     => 'UPDATED 2026'
            ];
        }
    }

    // 2. Fallback if database catalogue is empty
    if (empty($display_catalogues)) {
        $display_catalogues[] = [
            'id'            => 'fb-1',
            'name'          => 'FRIO Product Catalogue',
            'preview_image' => 'assets/imag/banners/product_banner_preset.png',
            'pdf_file'      => '',
            'type'          => 'Brochure',
            'pages'         => '64 Pages',
            'category'      => 'Precision Valves & Fittings',
            'downloads'     => '2,150 Downloads',
            'badge'         => 'NEW',
            'badge_sub'     => '2026 EDITION'
        ];
    }
    ?>

    <!-- Catalogue Card Responsive Centered Grid Layout (Item 1 & 2) -->
    <div class="max-w-[1400px] mx-auto w-full">
        <div class="flex flex-wrap justify-center gap-8 w-full">
            <?php foreach ($display_catalogues as $cat): 
                $cat_pdf  = !empty($cat['pdf_file']) ? $cat['pdf_file'] : '';
                $cat_img  = !empty($cat['preview_image']) ? $cat['preview_image'] : '';
                $cat_name = htmlspecialchars($cat['name'] ?? '');
            ?>
             <div class="catalogue-card-container group flex flex-col bg-white rounded-3xl border border-outline-variant/20 overflow-hidden shadow-[0_8px_24px_rgba(0,0,0,0.02)] hover:shadow-[0_24px_50px_rgba(0,52,98,0.1)] hover:-translate-y-2 transition-all duration-300 relative w-full sm:w-[320px]">
                
                <?php if ($cat_pdf): ?>
                <!-- Absolute overlay to make the entire card clickable (Item 8) -->
                <a href="<?php echo $cat_pdf; ?>" target="_blank" rel="noopener" class="absolute inset-0 z-10 cursor-pointer download-gate-trigger" data-pdf="<?php echo $cat_pdf; ?>" data-name="<?php echo $cat_name; ?>" aria-label="View <?php echo $cat_name; ?> PDF"></a>
                <?php endif; ?>
 
                <!-- 3D Book Cover Box with spine shadow (Item 2 & 3) -->
                <div class="relative w-full overflow-hidden bg-surface-container aspect-[3/4]">
                    
                    <div class="absolute top-4 right-4 z-20">
                        <span class="inline-flex bg-primary/85 backdrop-blur-sm text-white font-extrabold text-[8px] uppercase tracking-widest px-2.5 py-1 rounded-md border border-white/10">
                            <?php echo $cat['badge_sub']; ?>
                        </span>
                    </div>
 
                    <?php if ($cat_img): ?>
                        <img loading="lazy" src="<?php echo $cat_img; ?>"
                             alt="<?php echo $cat_name; ?>"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                    <?php else: ?>
                        <!-- Premium 3D Corporate manual cover mockup -->
                        <div class="w-full h-full bg-gradient-to-br from-primary via-primary-container to-[#001e39] flex flex-col justify-between p-6 text-white border-l-4 border-secondary-container transition-transform duration-700 group-hover:scale-103">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold tracking-[0.2em] text-secondary-fixed">FRIO INDUSTRIAL</span>
                                <span class="material-symbols-outlined text-secondary-fixed text-[20px]">verified</span>
                            </div>
                            <div class="my-auto flex flex-col gap-2">
                                <span class="material-symbols-outlined text-secondary-fixed text-[48px] opacity-90">menu_book</span>
                                <span class="font-extrabold text-lg leading-tight uppercase tracking-wide text-white"><?php echo $cat_name; ?></span>
                                <span class="text-[10px] text-white/60 tracking-wider font-semibold">TECHNICAL SPECIFICATION BOOK</span>
                            </div>
                            <div class="border-t border-white/10 pt-4 flex items-center justify-between text-[10px] opacity-75">
                                <span>EDITION 2026</span>
                                <span>PDF FORMAT</span>
                            </div>
                        </div>
                    <?php endif; ?>
 
                    <!-- Book spine shadow overlay -->
                    <div class="absolute top-0 bottom-0 left-0 w-2.5 bg-gradient-to-r from-black/35 to-transparent pointer-events-none z-10"></div>
                </div>
 
                <!-- Catalogue Details and Metadata (Item 7 & 9) -->
                <div class="p-6 flex flex-col justify-between flex-grow relative z-10">
                    <div class="flex items-center justify-between gap-3 mb-6">
                        <h3 class="font-extrabold text-primary text-lg leading-tight line-clamp-2"><?php echo $cat_name; ?></h3>
                        <?php if ($cat_pdf): ?>
                        <span class="shrink-0 inline-flex items-center gap-1 bg-red-600 text-white font-extrabold text-[9px] uppercase tracking-wider px-2.5 py-1 rounded-md shadow-sm border border-red-500/10 relative z-20">
                            <span class="material-symbols-outlined text-[12px] font-bold">picture_as_pdf</span>
                            PDF
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Action Buttons (Item 5 & 8) -->
                    <div class="flex items-center gap-3 border-t border-outline-variant/20 pt-4">
                        <?php if ($cat_pdf): ?>
                        <a href="<?php echo $cat_pdf; ?>" target="_blank" rel="noopener"
                           class="flex-1 flex items-center justify-center gap-2 border border-primary text-primary hover:bg-primary/5 font-bold text-xs py-3.5 rounded-xl transition-all shadow-sm relative z-20 download-gate-trigger" data-pdf="<?php echo $cat_pdf; ?>" data-name="<?php echo $cat_name; ?>">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            View
                        </a>
                        <a href="<?php echo $cat_pdf; ?>" download
                           class="flex-1 flex items-center justify-center gap-2 bg-gradient-to-r from-primary to-primary-container hover:from-secondary-container hover:to-secondary-fixed hover:text-on-secondary-container text-white font-bold text-xs py-3.5 rounded-xl transition-all shadow-md hover:-translate-y-0.5 relative z-20 download-gate-trigger" data-pdf="<?php echo $cat_pdf; ?>" data-name="<?php echo $cat_name; ?>">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            Download
                        </a>
                        <?php else: ?>
                        <a href="contact.php"
                           class="w-full flex items-center justify-center gap-2 border border-outline-variant text-on-surface-variant font-bold text-xs py-3.5 rounded-xl hover:border-primary hover:text-primary transition-all relative z-20">
                            <span class="material-symbols-outlined text-[16px]">mail</span>
                            Request Hard Copy
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Bottom Premium CTA (Item 6) -->
    <div class="mt-20 max-w-[1400px] mx-auto w-full relative overflow-hidden bg-gradient-to-r from-primary to-primary-container rounded-3xl p-10 md:p-14 flex flex-col md:flex-row items-center gap-8 justify-between border border-white/10 shadow-2xl">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_120%,rgba(254,214,91,0.1),transparent_40%)] pointer-events-none"></div>
        <div class="relative z-10">
            <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest text-secondary-fixed bg-secondary-fixed/10 px-3 py-1.5 rounded-lg border border-secondary-fixed/20 mb-3">Custom Technical Support</span>
            <h3 class="text-2xl md:text-3xl font-extrabold text-white mb-3 font-headline-md tracking-tight">Need a Custom Specification?</h3>
            <p class="text-white/80 text-sm max-w-2xl leading-relaxed font-medium">
                Our in-house engineering team can develop custom components and provide tailored technical brochures, 3D CAD modeling tolerances, and customized compliance datasheets for your specific B2B projects.
            </p>
        </div>
        <a href="contact.php" class="relative z-10 shrink-0 bg-gradient-to-r from-[#ffe088] to-secondary-container hover:from-secondary-container hover:to-secondary-fixed text-on-secondary-container font-extrabold text-sm px-8 py-4.5 rounded-xl hover:-translate-y-0.5 transition-all shadow-lg inline-flex items-center gap-2 border border-white/15">
            <span class="material-symbols-outlined text-[18px]">send</span>
            Contact Engineering Team
        </a>
    </div>
</main>
<!-- Gated Catalogue Download Modal -->
<div id="download-gate-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/60 backdrop-blur-sm transition-all duration-300">
    <div class="relative w-full max-w-[500px] bg-white rounded-3xl p-8 shadow-2xl border border-outline-variant/20 mx-4 transform transition-all duration-300 animate-[fadeInScale_0.2s_ease-out]">
        <!-- Close Button -->
        <button id="download-gate-close" class="absolute top-4 right-4 text-outline hover:text-primary transition-colors flex items-center justify-center p-1.5 rounded-full hover:bg-surface-container-low" aria-label="Close form">
            <span class="material-symbols-outlined text-[24px]">close</span>
        </button>
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-secondary-container/10 text-secondary rounded-2xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-[28px]">download_lock</span>
            </div>
            <h3 class="text-xl font-extrabold text-primary leading-tight font-headline-md">Technical Catalogue Access</h3>
            <p class="text-xs text-outline font-semibold mt-1.5">Please provide your details below to unlock immediate access to our high-resolution brochures.</p>
        </div>
        
        <!-- Error panel -->
        <div id="dg-error-msg" class="hidden bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-5 text-xs font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-[16px] text-red-600">error</span>
            <span id="dg-error-text">Error message goes here</span>
        </div>
        
        <!-- Form -->
        <form id="download-gate-form" class="space-y-4" novalidate>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-extrabold text-on-surface-variant uppercase tracking-widest" for="dg-fname">First Name *</label>
                    <input id="dg-fname" type="text" required placeholder="John"
                           class="w-full h-12 border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-surface-container-low" />
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-extrabold text-on-surface-variant uppercase tracking-widest" for="dg-lname">Last Name</label>
                    <input id="dg-lname" type="text" placeholder="Smith"
                           class="w-full h-12 border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-surface-container-low" />
                </div>
            </div>
            
            <div class="space-y-1.5">
                <label class="block text-[10px] font-extrabold text-on-surface-variant uppercase tracking-widest" for="dg-email">Email Address *</label>
                <input id="dg-email" type="email" required placeholder="you@company.com"
                       class="w-full h-12 border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-surface-container-low" />
            </div>
            
            <div class="space-y-1.5">
                <label class="block text-[10px] font-extrabold text-on-surface-variant uppercase tracking-widest" for="dg-phone">Mobile Number *</label>
                <input id="dg-phone" type="tel" required placeholder="10-digit number" maxlength="10" pattern="[0-9]{10}"
                       class="w-full h-12 border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all bg-surface-container-low" />
                <p class="text-[9px] text-outline font-semibold">Enter your 10-digit mobile number without country prefix.</p>
            </div>
            
            <button type="submit" id="dg-submit-btn"
                    class="w-full bg-gradient-to-r from-primary to-primary-container hover:from-secondary-container hover:to-secondary-fixed hover:text-on-secondary-container text-white py-3.5 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:-translate-y-0.5 mt-2">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span id="dg-submit-label">Submit &amp; Download</span>
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let pendingPdfPath = '';
    let pendingPdfName = '';
    let pendingPdfAction = 'download'; // 'view' or 'download'
    
    const downloadModal = document.getElementById('download-gate-modal');
    const modalForm = document.getElementById('download-gate-form');
    const closeBtn = document.getElementById('download-gate-close');
    const errorBox = document.getElementById('dg-error-msg');
    const errorText = document.getElementById('dg-error-text');
    
    function openGateModal() {
        if (!downloadModal) return;
        downloadModal.classList.remove('hidden');
        downloadModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
    }
    
    function closeGateModal() {
        if (!downloadModal) return;
        downloadModal.classList.add('hidden');
        downloadModal.classList.remove('flex');
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
    }
    
    function showError(msg) {
        if (errorBox && errorText) {
            errorText.textContent = msg;
            errorBox.classList.remove('hidden');
        }
    }
    
    function updateModalLabels(action) {
        const modalSubtitle = document.querySelector('#download-gate-modal p.text-outline');
        const submitLabel = document.getElementById('dg-submit-label');
        if (action === 'view') {
            if (modalSubtitle) modalSubtitle.textContent = "Please provide your details below to view our high-resolution brochures.";
            if (submitLabel) submitLabel.textContent = "Submit & View";
        } else {
            if (modalSubtitle) modalSubtitle.textContent = "Please provide your details below to unlock immediate access to our high-resolution brochures.";
            if (submitLabel) submitLabel.textContent = "Submit & Download";
        }
    }
    
    // Intercept catalogue download and view clicks
    document.querySelectorAll('.download-gate-trigger').forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            // Check if already unlocked in browser
            if (localStorage.getItem('catalogue_unlocked') === 'true') {
                return; // Let standard click/download execute natively
            }
            
            e.preventDefault();
            pendingPdfPath = trigger.getAttribute('data-pdf') || trigger.getAttribute('href');
            pendingPdfName = trigger.getAttribute('data-name') || 'Technical Catalogue';
            
            // Determine action (view vs download)
            const isDownload = trigger.hasAttribute('download') || 
                               trigger.innerText.toLowerCase().includes('download') || 
                               trigger.getAttribute('data-action') === 'download';
            pendingPdfAction = isDownload ? 'download' : 'view';
            
            updateModalLabels(pendingPdfAction);
            
            if (errorBox) errorBox.classList.add('hidden');
            if (modalForm) modalForm.reset();
            
            openGateModal();
        });
    });
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeGateModal);
    }
    
    if (downloadModal) {
        downloadModal.addEventListener('click', (e) => {
            if (e.target === downloadModal) {
                closeGateModal();
            }
        });
    }
    
    if (modalForm) {
        modalForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = document.getElementById('dg-submit-btn');
            const btnLabel = document.getElementById('dg-submit-label');
            
            const first_name = document.getElementById('dg-fname').value.trim();
            const last_name = document.getElementById('dg-lname').value.trim();
            const email = document.getElementById('dg-email').value.trim();
            const phone = document.getElementById('dg-phone').value.trim();
            
            // Validation
            if (!first_name) {
                showError("First name is required.");
                return;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                showError("Please enter a valid email address (e.g., name@email.com).");
                return;
            }
            const cleanPhone = phone.replace(/[^0-9]/g, '');
            if (cleanPhone.length !== 10) {
                showError("Mobile number must be a valid 10-digit number.");
                return;
            }
            
            btn.disabled = true;
            btnLabel.textContent = 'Verifying...';
            if (errorBox) errorBox.classList.add('hidden');
            
            try {
                const response = await fetch('<?php echo API_BASE_URL; ?>api/front_api/inquiries.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        type: 'catalogue',
                        first_name: first_name,
                        last_name: last_name,
                        email: email,
                        phone: cleanPhone,
                        message: (pendingPdfAction === 'view' ? 'Catalogue Viewed: ' : 'Catalogue Downloaded: ') + pendingPdfName
                    })
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    localStorage.setItem('catalogue_unlocked', 'true');
                    closeGateModal();
                    
                    if (pendingPdfPath) {
                        const dlLink = document.createElement('a');
                        dlLink.href = pendingPdfPath;
                        dlLink.target = '_blank';
                        if (pendingPdfAction === 'download') {
                            dlLink.download = '';
                        }
                        document.body.appendChild(dlLink);
                        dlLink.click();
                        document.body.removeChild(dlLink);
                    }
                } else {
                    showError(result.message || "Failed to submit. Please try again.");
                }
            } catch (err) {
                showError("Network connection failure. Please try again.");
            } finally {
                btn.disabled = false;
                btnLabel.textContent = pendingPdfAction === 'view' ? 'Submit & View' : 'Submit & Download';
            }
        });
    }
    
    // Support sitewide query param autostart funnel
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autostart') === '1') {
        const urlPdf = urlParams.get('pdf');
        const urlName = urlParams.get('name') || 'Catalogue';
        const urlAction = urlParams.get('action') || 'download';
        
        if (localStorage.getItem('catalogue_unlocked') === 'true') {
            if (urlPdf) {
                const dlLink = document.createElement('a');
                dlLink.href = urlPdf;
                dlLink.target = '_blank';
                if (urlAction === 'download') {
                    dlLink.download = '';
                }
                document.body.appendChild(dlLink);
                dlLink.click();
                document.body.removeChild(dlLink);
            }
        } else {
            pendingPdfPath = urlPdf;
            pendingPdfName = urlName;
            pendingPdfAction = urlAction;
            
            updateModalLabels(urlAction);
            
            if (errorBox) errorBox.classList.add('hidden');
            if (modalForm) modalForm.reset();
            
            openGateModal();
        }
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
