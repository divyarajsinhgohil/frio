<?php
/**
 * FrioFront - Full-Width Hero Banner (Bell-style)
 * Variables expected: $banners, $catalogues
 */
?>
<!-- ===== FULL-WIDTH HERO BANNER ===== -->
<div class="relative w-full overflow-hidden bg-gray-900" style="height:clamp(420px, 70vh, 720px);" id="banner-section">

    <!-- Slides Wrapper -->
    <div class="relative w-full h-full">
        <?php if (!empty($banners)): ?>
            <?php foreach ($banners as $idx => $banner): ?>
            <div class="banner-slide absolute inset-0 transition-opacity duration-1000 ease-in-out <?php echo $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>"
                 data-slide-index="<?php echo $idx; ?>">

                <!-- Background Image -->
                <?php if (!empty($banner['image'])): ?>
                <img loading="lazy" src="<?php echo asset_url($banner['image']); ?>"
                     alt="<?php echo htmlspecialchars($banner['name']); ?>"
                     class="banner-bg-img absolute inset-0 w-full h-full object-cover transition-transform duration-[8s] ease-linear scale-105"
                     style="transform-origin: center;" />
                <?php else: ?>
                <div class="absolute inset-0 bg-gradient-to-br from-primary to-primary-container"></div>
                <?php endif; ?>

                <!-- Dark overlay gradient — darker on left side for text legibility (Only show if there is text to display) -->
                <?php if (!empty(trim($banner['name'] ?? '')) || !empty(trim($banner['description'] ?? ''))): ?>
                    <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/30 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/10"></div>
                <?php endif; ?>

                <!-- Text Content (overlaid on image, left side - Only show if name or description exists) -->
                <?php if (!empty(trim($banner['name'] ?? '')) || !empty(trim($banner['description'] ?? ''))): ?>
                <div class="absolute inset-0 flex items-end pb-14 md:pb-20">
                    <div class="w-full px-6 md:px-16 w-full">
                        <div class="max-w-2xl">
                            <span class="inline-flex items-center gap-2 bg-secondary-container/90 text-on-secondary-container text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full mb-4">
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
                                Safety By Choice
                            </span>
                            <?php if (!empty(trim($banner['name'] ?? ''))): ?>
                            <h1 class="text-white font-extrabold leading-tight mb-4"
                                style="font-size: clamp(2rem, 5vw, 3.75rem); text-shadow: 0 2px 20px rgba(0,0,0,0.4);">
                                <?php echo htmlspecialchars($banner['name']); ?>
                            </h1>
                            <?php endif; ?>
                            <?php if (!empty(trim($banner['description'] ?? ''))): ?>
                            <p class="text-white/80 text-base md:text-lg leading-relaxed mb-8 max-w-xl"
                               style="text-shadow: 0 1px 8px rgba(0,0,0,0.5);">
                                <?php echo htmlspecialchars($banner['description']); ?>
                            </p>
                            <?php endif; ?>
                            <div class="flex flex-wrap gap-4">
                                <a href="<?php echo htmlspecialchars($banner['button_link'] ?: 'product.php'); ?>"
                                   class="bg-primary hover:bg-primary-container text-white px-7 py-3.5 rounded-lg font-bold text-sm transition-all hover:-translate-y-0.5 shadow-xl">
                                   Explore Products
                                </a>
                                <?php if (!empty($catalogues)): ?>
                                <a href="<?php echo asset_url($catalogues[0]['pdf_file']); ?>" target="_blank"
                                   class="bg-white/15 backdrop-blur-sm border border-white/30 text-white px-7 py-3.5 rounded-lg font-bold text-sm hover:bg-white/25 transition-all">
                                   Download Catalogue
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <!-- Dot Indicators -->
            <?php if (count($banners) > 1): ?>
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex items-center gap-2">
                <?php foreach ($banners as $idx => $b): ?>
                <button class="slide-dot transition-all duration-300 rounded-full <?php echo $idx === 0 ? 'w-7 h-2 bg-white' : 'w-2 h-2 bg-white/40 hover:bg-white/70'; ?>"
                        data-dot-index="<?php echo $idx; ?>"></button>
                <?php endforeach; ?>
            </div>

            <!-- Left / Right Arrows -->
            <button id="banner-prev" class="absolute left-4 md:left-6 top-1/2 -translate-y-1/2 z-30 w-10 h-10 md:w-12 md:h-12 rounded-full bg-black/40 hover:bg-secondary-container border border-white/20 hover:border-secondary-fixed text-white hover:text-on-secondary-container flex items-center justify-center transition-all backdrop-blur-md shadow-lg duration-300 hover:scale-110 active:scale-95">
                <span class="material-symbols-outlined text-inherit text-[22px] md:text-[26px] font-bold">chevron_left</span>
            </button>
            <button id="banner-next" class="absolute right-4 md:right-6 top-1/2 -translate-y-1/2 z-30 w-10 h-10 md:w-12 md:h-12 rounded-full bg-black/40 hover:bg-secondary-container border border-white/20 hover:border-secondary-fixed text-white hover:text-on-secondary-container flex items-center justify-center transition-all backdrop-blur-md shadow-lg duration-300 hover:scale-110 active:scale-95">
                <span class="material-symbols-outlined text-inherit text-[22px] md:text-[26px] font-bold">chevron_right</span>
            </button>
            <?php endif; ?>

        <?php else: ?>
            <!-- Fallback when no banners -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary via-primary-container to-[#003462]"></div>
            <div class="absolute inset-0" style="background:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2240%22 fill=%22none%22 stroke=%22white%22 stroke-opacity=%220.03%22 stroke-width=%2220%22/></svg>') center/cover;"></div>
            <div class="absolute inset-0 flex items-end pb-20">
                <div class="w-full px-6 md:px-16 w-full">
                    <div class="max-w-xl">
                        <span class="inline-flex items-center gap-2 bg-secondary-container/90 text-on-secondary-container text-[11px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full mb-4">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
                            Safety By Choice
                        </span>
                        <h1 class="text-white font-extrabold leading-tight mb-4" style="font-size: clamp(2rem, 5vw, 3.75rem);">
                            Precision Brass<br/>Fittings & Safety
                        </h1>
                        <p class="text-white/80 text-lg mb-8">Engineered for performance and reliability in high-stakes manufacturing environments.</p>
                        <div class="flex flex-wrap gap-4">
                            <a href="product.php" class="bg-secondary-container text-on-secondary-container px-7 py-3.5 rounded-lg font-bold text-sm hover:bg-secondary-fixed transition-all">Explore Products</a>
                            <a href="contact.php" class="border border-white/30 text-white px-7 py-3.5 rounded-lg font-bold text-sm hover:bg-white/10 transition-all">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    const slides  = document.querySelectorAll('.banner-slide');
    const dots    = document.querySelectorAll('.slide-dot');
    const prev    = document.getElementById('banner-prev');
    const next    = document.getElementById('banner-next');
    let cur       = 0, timer;

    function goTo(n) {
        slides[cur].style.opacity = '0';
        slides[cur].style.zIndex  = '0';
        dots[cur]?.classList.remove('w-7', 'bg-white');
        dots[cur]?.classList.add('w-2', 'bg-white/40');

        cur = (n + slides.length) % slides.length;

        slides[cur].style.opacity = '1';
        slides[cur].style.zIndex  = '10';
        dots[cur]?.classList.remove('w-2', 'bg-white/40');
        dots[cur]?.classList.add('w-7', 'bg-white');

        // Ken Burns effect on new slide
        const img = slides[cur].querySelector('.banner-bg-img');
        if (img) { img.style.transform = 'scale(1)'; setTimeout(() => { img.style.transform = 'scale(1.08)'; }, 50); }
    }

    function start() { timer = setInterval(() => goTo(cur + 1), 6000); }

    if (slides.length > 1) {
        start();
        next?.addEventListener('click', () => { clearInterval(timer); goTo(cur + 1); start(); });
        prev?.addEventListener('click', () => { clearInterval(timer); goTo(cur - 1); start(); });
        dots.forEach(d => d.addEventListener('click', () => { clearInterval(timer); goTo(parseInt(d.dataset.dotIndex)); start(); }));
    }

    // Ken Burns on first slide
    const firstImg = slides[0]?.querySelector('.banner-bg-img');
    if (firstImg) setTimeout(() => { firstImg.style.transform = 'scale(1.08)'; }, 100);
})();
</script>
