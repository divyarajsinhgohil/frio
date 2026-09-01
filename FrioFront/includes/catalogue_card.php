<?php
/**
 * Reusable Catalogue Book Card - Clean Image, click opens PDF
 * Expected keys in $cat: preview_image, pdf_file, name
 */
$cat_pdf  = !empty($cat['pdf_file'])      ? asset_url($cat['pdf_file'])      : '';
$cat_img  = !empty($cat['preview_image']) ? asset_url($cat['preview_image']) : '';
$cat_name = htmlspecialchars($cat['name'] ?? '');
?>
<div class="flex flex-col">

    <!-- ===== CARD — click anywhere opens PDF ===== -->
    <a href="<?php echo $cat_pdf ?: 'contact.php'; ?>"
       <?php echo $cat_pdf ? 'target="_blank" rel="noopener"' : ''; ?>
       class="block relative rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-shadow duration-300"
       style="aspect-ratio:3/4;">

        <?php if ($cat_img): ?>
        <img loading="lazy" src="<?php echo $cat_img; ?>"
             alt="<?php echo $cat_name; ?>"
             class="absolute inset-0 w-full h-full object-cover" />
        <?php else: ?>
        <div class="absolute inset-0 bg-gradient-to-br from-primary via-primary-container to-[#001e3c] flex items-center justify-center">
            <span class="material-symbols-outlined text-white/20" style="font-size:80px;">menu_book</span>
        </div>
        <?php endif; ?>

        <!-- Book spine shadow -->
        <div class="absolute top-0 bottom-0 left-0 w-2 bg-black/25 pointer-events-none"></div>
    </a>

    <!-- ===== BELOW CARD: Name + View & Download buttons ===== -->
    <div class="mt-3 px-0.5">
        <div class="flex items-center justify-between gap-2 mb-2">
            <p class="font-bold text-primary text-sm line-clamp-1"><?php echo $cat_name; ?></p>
            <?php if ($cat_pdf): ?>
            <span class="shrink-0 inline-flex items-center gap-1 bg-red-600 text-white font-extrabold text-[9px] uppercase tracking-wider px-2.5 py-1 rounded-md shadow-sm border border-red-500/10">
                <span class="material-symbols-outlined text-[12px] font-bold">picture_as_pdf</span>
                PDF
            </span>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-2">
            <?php if ($cat_pdf): ?>
            <a href="<?php echo $cat_pdf; ?>" target="_blank" rel="noopener"
               class="flex-1 flex items-center justify-center gap-1.5
                      border border-primary text-primary font-bold text-[11px] px-3 py-1.5 rounded-lg
                      hover:bg-primary hover:text-white transition-all">
                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                View
            </a>
            <a href="<?php echo $cat_pdf; ?>" download
               class="flex-1 flex items-center justify-center gap-1.5
                      bg-primary text-white font-bold text-[11px] px-3 py-1.5 rounded-lg
                      hover:bg-primary-container transition-all">
                <span class="material-symbols-outlined text-[14px]">download</span>
                Download
            </a>
            <?php else: ?>
            <a href="contact.php"
               class="flex-1 flex items-center justify-center gap-1.5
                      border border-outline-variant text-on-surface-variant font-bold text-[11px] px-3 py-1.5 rounded-lg
                      hover:border-primary hover:text-primary transition-all">
                <span class="material-symbols-outlined text-[14px]">mail</span>
                Request
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
