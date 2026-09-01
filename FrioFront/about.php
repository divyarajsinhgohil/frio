<?php
require_once 'config.php';
$settings   = api_fetch('settings.php') ?? [];
$page_title  = 'About Us | FRIO Industrial';
$meta_desc   = 'Learn about FRIO — our history, mission, certifications, and commitment to precision brass manufacturing since 2008.';
$active_page = 'about';
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
            <span class="text-[#ffe088] font-bold">About Us</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 font-headline-lg tracking-tight">About FRIO</h1>
        <p class="text-white/85 text-sm md:text-base max-w-2xl leading-relaxed font-medium">Learn about our legacy of precision manufacturing, engineering excellence, and safety standards</p>
    </div>
</section>

<!-- Reopen centered wrapper for page content -->
<div class="max-w-[1700px] mx-auto w-full flex-1 flex flex-col relative">

<!-- Mission & Vision -->
<section class="py-20 w-full px-4 md:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
            <div>
                <p class="text-secondary font-bold text-xs uppercase tracking-widest mb-2">Our Purpose</p>
                <h2 class="text-3xl font-extrabold text-primary mb-4">Mission & Vision</h2>
                <div class="h-1 w-14 bg-secondary-container rounded-full mb-6"></div>
            </div>
            <div class="space-y-6">
                <div>
                    <h3 class="font-bold text-primary mb-2 uppercase">Ensure best quality for family safety</h3>
                    <p class="text-on-surface-variant text-sm leading-relaxed mb-3">
                        Your family is the one which you are most protective about. No compromises are sought when it comes to ensuring the health and safety of your family. Frio is a revolutionary brand for Brass Air Conditioning and Compression Fittings and for all types of HVAC requirements.
                    </p>
                    <p class="text-on-surface-variant text-sm leading-relaxed mb-6">
                        Frio products provide a highly effective and low cost solution which annuls the need of peaceful environment around your family. All our products employ our unique, innovative and advance technology, Also with our products you are rendered peace of mind and sense of freedom in terms of safety. Frio products are unique and compatible and are hassle free and easy to apply.
                    </p>
                </div>
                <div>
                    <h3 class="font-bold text-primary mb-2 uppercase">Superior chilling through precision engineering</h3>
                    <p class="text-on-surface-variant text-sm leading-relaxed mb-3">
                        Our goal is to win the trust of customers across the world and provide them a carefree solution for all refrigeration, Air-conditioning and HVAC needs. We strive to provide best in class and timely delivery at highly affordable and competitive rates.
                    </p>
                    <p class="text-on-surface-variant text-sm leading-relaxed">
                        Our strength lies in innovation and diversity. We believe in upgrading our products and service with newer innovative technologies and we provide all required international standards.
                    </p>
                </div>
            </div>
        </div>
        <!-- Stats Grid -->
        <div class="grid grid-cols-2 gap-5">
            <?php
            $stats = [
                ['value'=>'30+',  'label'=>'Years of Excellence', 'icon'=>'calendar_today'],
                ['value'=>'50K+', 'label'=>'Products Delivered',  'icon'=>'inventory_2'],
                ['value'=>'ISO',  'label'=>'9001:2015 Certified', 'icon'=>'verified'],
                ['value'=>'99.9%','label'=>'Material Purity',     'icon'=>'science'],
            ];
            foreach ($stats as $s): ?>
            <div class="bg-surface-container-low border border-outline-variant/20 rounded-2xl p-7 text-center hover:-translate-y-1 transition-all">
                <span class="material-symbols-outlined text-primary text-3xl mb-3 block" style="font-variation-settings:'FILL' 1;"><?php echo $s['icon']; ?></span>
                <div class="text-2xl font-extrabold text-primary mb-1"><?php echo $s['value']; ?></div>
                <div class="text-xs text-outline font-bold uppercase tracking-wider"><?php echo $s['label']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Manufacturing Excellence -->
<section class="py-20 bg-surface-container">
    <div class="w-full px-4 md:px-8">
        <div class="text-center mb-14">
            <p class="text-secondary font-bold text-xs uppercase tracking-widest mb-3">What Sets Us Apart</p>
            <h2 class="text-3xl font-extrabold text-primary mb-6">Manufacturing Excellence</h2>
            <p class="text-on-surface-variant text-base leading-relaxed max-w-4xl mx-auto mb-10">
                We are the manufacturer and exporter of wide range of products considering Global market requirement. We also manufacture products based on customer drawings and specification. We can manufacture any precision machined parts as per customer's requirements. Our wide range capabilities execute economical and high quality precision machined metal components in any volume. We also Provide surface treatment facility/Plating like Copper plating, Nickel plating, Chrome Plating, Black nickel, Buffing etc...
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php
            $features = [
                ['icon'=>'precision_manufacturing', 'title'=>'Advanced CNC Machining',   'desc'=>'State-of-the-art CNC turning centers achieving tolerances to ±0.005mm for critical components.'],
                ['icon'=>'science',                  'title'=>'Material Testing Lab',     'desc'=>'In-house metallurgical laboratory testing every batch for composition, tensile strength, and hardness.'],
                ['icon'=>'shield',                   'title'=>'Zero-Defect Policy',       'desc'=>'100% inspection of finished components using CMM and vision systems before any shipment leaves our facility.'],
            ];
            foreach ($features as $f): ?>
            <div class="bg-white rounded-2xl p-8 border border-outline-variant/20 industrial-shadow hover:-translate-y-1 transition-all">
                <div class="w-14 h-14 bg-primary/8 rounded-2xl flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings:'FILL' 1;"><?php echo $f['icon']; ?></span>
                </div>
                <h3 class="font-bold text-primary text-lg mb-3"><?php echo $f['title']; ?></h3>
                <p class="text-on-surface-variant text-sm leading-relaxed"><?php echo $f['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 w-full px-4 md:px-8">
    <div class="bg-primary rounded-3xl p-10 md:p-14 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-secondary-fixed/5 blur-3xl rounded-full"></div>
        <div class="relative z-10">
            <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-4">Ready to Work Together?</h2>
            <p class="text-white/70 text-base mb-8 max-w-xl mx-auto">Let's discuss your industrial requirements. Our engineering team is ready to help you find the perfect solution.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="contact.php" class="bg-secondary-container text-on-secondary-container px-8 py-4 rounded-xl font-bold text-sm hover:bg-secondary-fixed transition-all inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">send</span> Contact Us
                </a>
                <a href="product.php" class="border-2 border-white/30 text-white px-8 py-4 rounded-xl font-bold text-sm hover:bg-white/10 transition-all inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">inventory_2</span> View Products
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
