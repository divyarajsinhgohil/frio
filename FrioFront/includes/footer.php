<?php
/**
 * FrioFront - Shared Footer Component
 * Variables expected: $settings (array from API)
 */
$social_platforms = [
    'facebook'  => ['title' => 'Facebook',  'icon' => '<svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M9 8H7v3h2v9h3v-9h3l.5-3H12V6c0-.88.39-1 1-1h2V2h-3c-2.5 0-4 1.5-4 4v2z"/></svg>'],
    'instagram' => ['title' => 'Instagram', 'icon' => '<svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>'],
    'linkedin'  => ['title' => 'LinkedIn',  'icon' => '<svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>'],
    'twitter'   => ['title' => 'Twitter/X', 'icon' => '<svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>'],
    'youtube'   => ['title' => 'YouTube',   'icon' => '<svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.108C19.516 3.53 12 3.53 12 3.53s-7.516 0-9.388.525a3.003 3.003 0 0 0-2.11 2.108C0 8.055 0 12 0 12s0 3.945.502 5.837a3.003 3.003 0 0 0 2.11 2.108c1.872.525 9.388.525 9.388.525s7.516 0 9.388-.525a3.003 3.003 0 0 0 2.11-2.108C24 15.945 24 12 24 12s0-3.945-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>'],
];
?>

</div> <!-- End of max-w-[1700px] content wrapper -->

<!-- ===== FOOTER ===== -->
<footer class="bg-primary text-on-primary w-full mt-auto">
    <div class="max-w-[1700px] mx-auto px-4 md:px-8 pt-[70px] pb-[70px] grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

        <!-- Brand Column -->
        <div class="lg:col-span-1 space-y-5">
            <?php if (!empty($settings['logo'])): ?>
                <img loading="lazy" src="<?php echo asset_url($settings['logo']); ?>" alt="Frio Logo" class="h-12 w-auto object-contain brightness-0 invert" />
            <?php else: ?>
                <span class="text-white font-extrabold text-2xl">FRIO</span>
            <?php endif; ?>
            <p class="text-white/75 text-sm leading-[1.7] max-w-[240px]">
                Leading the industry in precision brass solutions since 2008. Quality you can trust, safety by choice.
            </p>
            <!-- Social Icons -->
            <div class="flex flex-wrap gap-3.5 pt-1">
                <?php foreach ($social_platforms as $key => $props): ?>
                    <?php if (!empty($settings[$key])): ?>
                        <a href="<?php echo htmlspecialchars($settings[$key]); ?>" target="_blank" rel="noopener"
                           title="<?php echo $props['title']; ?>"
                           class="w-9 h-9 rounded-full border border-white/20 hover:border-white bg-white/5 hover:bg-white/20 flex items-center justify-center transition-all hover:translate-y-[-3px]">
                           <?php echo $props['icon']; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Links -->
        <div>
            <h4 class="text-white font-bold text-xs uppercase tracking-widest mb-5">Quick Links</h4>
            <ul class="space-y-3 text-sm text-white/80 leading-[1.7]">
                <li><a href="index.php"     class="hover:text-secondary-fixed transition-colors">Home</a></li>
                <li><a href="category.php"  class="hover:text-secondary-fixed transition-colors">Category</a></li>
                <li><a href="product.php"   class="hover:text-secondary-fixed transition-colors">Product Catalog</a></li>
                <li><a href="catalogue.php" class="hover:text-secondary-fixed transition-colors">Catalogue</a></li>
                <li><a href="about.php"     class="hover:text-secondary-fixed transition-colors">About Us</a></li>
                <li><a href="contact.php"   class="hover:text-secondary-fixed transition-colors">Contact Us</a></li>
            </ul>
        </div>

        <!-- Address -->
        <div class="space-y-6">
            <div>
                <h4 class="text-white font-bold text-xs uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="material-symbols-outlined text-secondary text-[16px]">factory</span> <?php echo htmlspecialchars($settings['office_name_1'] ?? 'Jamnagar Unit'); ?></h4>
                <div class="flex gap-3 text-xs text-white/75 leading-[1.6]">
                    <span class="material-symbols-outlined text-secondary text-[16px] mt-0.5 shrink-0">location_on</span>
                    <span><?php echo htmlspecialchars($settings['address'] ?? ''); ?></span>
                </div>
            </div>
            <?php if (!empty($settings['address_2'])): ?>
            <div>
                <h4 class="text-white font-bold text-xs uppercase tracking-widest mb-3 flex items-center gap-1.5"><span class="material-symbols-outlined text-secondary text-[16px]">corporate_fare</span> <?php echo htmlspecialchars($settings['office_name_2'] ?? 'Bangalore Office'); ?></h4>
                <div class="flex gap-3 text-xs text-white/75 leading-[1.6]">
                    <span class="material-symbols-outlined text-secondary text-[16px] mt-0.5 shrink-0">location_on</span>
                    <span><?php echo htmlspecialchars($settings['address_2'] ?? ''); ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Contact -->
        <div>
            <h4 class="text-white font-bold text-xs uppercase tracking-widest mb-5">Get In Touch</h4>
            <ul class="space-y-4 text-sm text-white/80 leading-[1.7]">
                <?php if (!empty($settings['phone'])): ?>
                <li class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary text-[18px]">call</span>
                    <a href="tel:<?php echo htmlspecialchars(trim(explode(',', $settings['phone'])[0])); ?>" class="hover:text-secondary-fixed transition-colors font-medium">
                        <?php echo htmlspecialchars(trim(explode(',', $settings['phone'])[0])); ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if (!empty($settings['email'])): ?>
                <li class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary text-[18px]">mail</span>
                    <a href="mailto:<?php echo htmlspecialchars(trim(explode('|', $settings['email'])[0])); ?>" class="hover:text-secondary-fixed transition-colors">
                        <?php echo htmlspecialchars(trim(explode('|', $settings['email'])[0])); ?>
                    </a>
                </li>
                <?php endif; ?>
                <li class="mt-6">
                    <a href="contact.php" class="inline-block bg-secondary-container text-on-secondary-container px-5 py-2.5 rounded-lg font-bold text-xs hover:bg-secondary-fixed transition-all">
                        Send Enquiry →
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="border-t border-white/10">
        <div class="max-w-[1700px] mx-auto px-4 md:px-8 py-5 flex flex-col md:flex-row items-center justify-between gap-3">
            <p class="text-white/40 text-sm leading-[1.7]">
                © <?php echo date("Y"); ?> FRIO Industrial. Safety By Choice. All Rights Reserved.
            </p>
            <p class="text-white/30 text-sm leading-[1.7]">Precision Engineering for Global Infrastructure.</p>
        </div>
    </div>
</footer>

</body>
</html>
