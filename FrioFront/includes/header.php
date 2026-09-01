<?php
/**
 * FrioFront - Shared Header & Navbar Component
 * Variables expected from parent page:
 *   $page_title   - Browser tab title
 *   $meta_desc    - Meta description
 *   $active_page  - Active nav key: home | category | product | catalogue | about | contact
 *   $settings     - Settings array from API
 */
$active_page = $active_page ?? 'home';
$page_title  = $page_title  ?? 'FRIO | Precision Brass Fittings & Industrial Safety';
$meta_desc   = $meta_desc   ?? 'FRIO — Precision Brass Fittings & Industrial Safety. Safety By Choice.';

function nav_class($page, $active) {
    return $page === $active
        ? 'text-white font-bold border-b-2 border-secondary-fixed pb-0.5 transition-colors'
        : 'text-white/80 hover:text-white font-semibold transition-colors';
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($meta_desc); ?>" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- TomSelect CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    
    <!-- Fancybox 5.0 CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
              "secondary-fixed": "#ffe088", "outline": "#727781", "inverse-primary": "#a4c9ff",
              "tertiary-fixed": "#e0e3e6", "surface-container": "#e7eeff", "surface": "#f9f9ff",
              "on-secondary-fixed": "#241a00", "secondary-container": "#fed65b",
              "surface-container-low": "#f0f3ff", "surface-container-high": "#dee8ff",
              "primary-container": "#0c4b86", "surface-dim": "#cfdaf1",
              "on-surface-variant": "#424750", "surface-tint": "#2c609c",
              "outline-variant": "#c2c6d1", "error": "#ba1a1a", "on-primary-fixed": "#001c39",
              "inverse-on-surface": "#ebf1ff", "on-background": "#111c2c",
              "on-primary-container": "#8dbcfe", "primary-fixed-dim": "#a4c9ff",
              "secondary-fixed-dim": "#e9c349", "tertiary": "#303436",
              "surface-variant": "#d8e3fa", "primary": "#003462", "on-error-container": "#93000a",
              "on-error": "#ffffff", "inverse-surface": "#263142", "background": "#f9f9ff",
              "on-secondary-container": "#745c00", "surface-bright": "#f9f9ff",
              "tertiary-fixed-dim": "#c4c7ca", "error-container": "#ffdad6",
              "on-surface": "#111c2c", "on-tertiary": "#ffffff", "tertiary-container": "#474a4d",
              "surface-container-lowest": "#ffffff", "on-primary-fixed-variant": "#044882",
              "on-tertiary-container": "#b7b9bc", "secondary": "#735c00",
              "surface-container-highest": "#d8e3fa", "primary-fixed": "#d4e3ff",
              "on-secondary-fixed-variant": "#574500", "on-secondary": "#ffffff",
              "on-primary": "#ffffff", "on-tertiary-fixed": "#191c1e"
            },
            "spacing": {
              "unit": "8px", "gutter": "24px", "margin-desktop": "64px",
              "max-width": "1440px", "margin-mobile": "16px"
            },
            "fontFamily": {
              "headline-xl": ["Hanken Grotesk"], "headline-md": ["Hanken Grotesk"],
              "body-md": ["Hanken Grotesk"], "headline-lg": ["Hanken Grotesk"],
              "label-bold": ["Hanken Grotesk"], "label-sm": ["Hanken Grotesk"],
              "headline-lg-mobile": ["Hanken Grotesk"], "body-lg": ["Hanken Grotesk"]
            },
            "fontSize": {
              "headline-xl": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "800"}],
              "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
              "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
              "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
              "label-bold": ["14px", {"lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "700"}],
              "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
              "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "700"}],
              "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}]
            }
          },
        },
      }
    </script>
    <link href="assets/css/style.css" rel="stylesheet" />
    <script> window.API_BASE_URL = "<?php echo API_BASE_URL; ?>"; </script>
    <script src="assets/js/main.js?v=<?php echo time(); ?>" defer></script>
</head>
<body class="bg-background text-on-background selection:bg-primary-container selection:text-on-primary-container overflow-x-hidden min-h-screen flex flex-col">

<!-- ===== STICKY NAVBAR ===== -->
<nav class="fixed top-0 left-0 right-0 w-full z-50 bg-primary/95 backdrop-blur-md border-b border-white/10 shadow-lg" id="main-nav">
    <div class="max-w-[1700px] mx-auto px-4 md:px-8 h-16 md:h-20 flex items-center justify-between w-full">

        <!-- Logo -->
        <a href="index.php" aria-label="Homepage" class="flex items-center gap-3 shrink-0">
            <?php if (!empty($settings['logo'])): ?>
                <img loading="lazy" src="<?php echo asset_url($settings['logo']); ?>" alt="Frio Logo" class="h-11 w-auto object-contain brightness-0 invert" />
            <?php else: ?>
                <span class="text-white font-extrabold text-2xl tracking-tight">FRIO</span>
            <?php endif; ?>
        </a>

        <!-- Desktop Nav Links -->
        <div class="hidden lg:flex items-center gap-8 md:gap-9">
            <a href="index.php"     class="<?php echo nav_class('home',      $active_page); ?> text-[15px]">Home</a>
            <a href="category.php"  class="<?php echo nav_class('category',  $active_page); ?> text-[15px]">Category</a>
            <a href="product.php"   class="<?php echo nav_class('product',   $active_page); ?> text-[15px]">Product</a>
            <a href="catalogue.php" class="<?php echo nav_class('catalogue', $active_page); ?> text-[15px]">Catalogue</a>
            <a href="about.php"     class="<?php echo nav_class('about',     $active_page); ?> text-[15px]">About Us</a>
            <a href="contact.php"   class="<?php echo nav_class('contact',   $active_page); ?> text-[15px]">Contact Us</a>
        </div>

        <!-- Right CTA & TomSelect Search -->
        <div class="hidden lg:flex items-center gap-6">
            <select id="global-search" aria-label="Global Search" class="global-search-ts" placeholder="Search product or category..."></select>
            
            <a href="contact.php" id="inquire-btn"
               class="bg-secondary-container text-on-secondary-container px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-secondary-fixed transition-all whitespace-nowrap">
               Inquire Now
            </a>
        </div>

        <!-- Mobile Hamburger & Search -->
        <div class="lg:hidden flex items-center gap-2">
            <select id="mobile-global-search" aria-label="Mobile Global Search" class="global-search-ts" style="width:150px" placeholder="Search..."></select>
            <button id="hamburger-btn" class="flex flex-col gap-1.5 p-2 rounded-lg hover:bg-white/10 transition-all" aria-label="Toggle menu">
                <span class="w-6 h-0.5 bg-white rounded-full transition-all" id="ham-line1"></span>
                <span class="w-6 h-0.5 bg-white rounded-full transition-all" id="ham-line2"></span>
                <span class="w-4 h-0.5 bg-white rounded-full transition-all" id="ham-line3"></span>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div id="mobile-menu" class="lg:hidden bg-primary border-t border-white/10">
        <div class="px-6 py-4 flex flex-col gap-1">
            <a href="index.php"     class="<?php echo nav_class('home',      $active_page); ?> py-3 border-b border-white/10 text-sm">Home</a>
            <a href="category.php"  class="<?php echo nav_class('category',  $active_page); ?> py-3 border-b border-white/10 text-sm">Category</a>
            <a href="product.php"   class="<?php echo nav_class('product',   $active_page); ?> py-3 border-b border-white/10 text-sm">Product</a>
            <a href="catalogue.php" class="<?php echo nav_class('catalogue', $active_page); ?> py-3 border-b border-white/10 text-sm">Catalogue</a>
            <a href="about.php"     class="<?php echo nav_class('about',     $active_page); ?> py-3 border-b border-white/10 text-sm">About Us</a>
            <a href="contact.php"   class="<?php echo nav_class('contact',   $active_page); ?> py-3 border-b border-white/10 text-sm">Contact Us</a>
            <a href="contact.php" class="mt-3 bg-secondary-container text-on-secondary-container px-5 py-3 rounded-lg font-bold text-sm text-center hover:bg-secondary-fixed transition-all">
                Inquire Now
            </a>
        </div>
    </div>
</nav>

<!-- Spacer for fixed navbar — only on non-home pages -->
<?php if (($active_page ?? '') !== 'home'): ?>
<div class="h-16 md:h-20"></div>
<?php endif; ?>

<!-- Main Centered Content Container -->
<div class="max-w-[1700px] mx-auto w-full flex-1 flex flex-col relative">


