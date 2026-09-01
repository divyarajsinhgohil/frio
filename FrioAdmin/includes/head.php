<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'FRIO Admin Console | Safety By Choice'; ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Fonts are self-hosted locally via assets/css/style.css @font-face -->
    <!-- No Google Fonts CDN needed - works fully offline on local XAMPP -->
    
    <!-- Centralized Stylesheet Asset -->
    <link href="<?php echo isset($base_path) ? $base_path : ''; ?>assets/css/style.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo isset($base_path) ? $base_path : ''; ?>assets/js/main.js" defer></script>
    
    <!-- Tailwind Custom Config -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "outline-variant": "#c2c6d1",
                        "on-error-container": "#93000a",
                        "on-secondary-container": "#745c00",
                        "surface-container-highest": "#d8e3fa",
                        "surface-tint": "#2c609c",
                        "on-tertiary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "surface-bright": "#f9f9ff",
                        "on-primary-fixed-variant": "#044882",
                        "surface-dim": "#cfdaf1",
                        "tertiary-container": "#474a4d",
                        "surface": "#f9f9ff",
                        "on-surface": "#111c2c",
                        "surface-container-high": "#dee8ff",
                        "tertiary-fixed": "#e0e3e6",
                        "error": "#ba1a1a",
                        "surface-container": "#e7eeff",
                        "on-error": "#ffffff",
                        "tertiary": "#303436",
                        "on-background": "#111c2c",
                        "on-primary": "#ffffff",
                        "secondary-fixed-dim": "#e9c349",
                        "tertiary-fixed-dim": "#c4c7ca",
                        "inverse-on-surface": "#ebf1ff",
                        "primary-fixed-dim": "#a4c9ff",
                        "on-tertiary-fixed-variant": "#44474a",
                        "on-primary-fixed": "#001c39",
                        "surface-variant": "#d8e3fa",
                        "surface-container-low": "#f0f3ff",
                        "on-secondary": "#ffffff",
                        "on-secondary-fixed": "#241a00",
                        "on-surface-variant": "#424750",
                        "error-container": "#ffdad6",
                        "on-secondary-fixed-variant": "#574500",
                        "inverse-surface": "#263142",
                        "secondary-container": "#fed65b",
                        "on-tertiary-fixed": "#191c1e",
                        "inverse-primary": "#a4c9ff",
                        "primary-container": "#0c4b86",
                        "outline": "#727781",
                        "on-tertiary-container": "#b7b9bc",
                        "secondary": "#735c00",
                        "primary-fixed": "#d4e3ff",
                        "background": "#f9f9ff",
                        "secondary-fixed": "#ffe088",
                        "on-primary-container": "#8dbcfe",
                        "primary": "#003462"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "body-lg": ["Hanken Grotesk"],
                        "headline-lg-mobile": ["Hanken Grotesk"],
                        "headline-xl": ["Hanken Grotesk"],
                        "body-md": ["Hanken Grotesk"],
                        "label-sm": ["Hanken Grotesk"],
                        "headline-md": ["Hanken Grotesk"],
                        "label-bold": ["Hanken Grotesk"]
                    },
                    "spacing": {
                        "margin-desktop": "64px",
                        "max-width": "1440px",
                        "unit": "8px",
                        "gutter": "24px",
                        "margin-mobile": "16px"
                    },
                    "fontSize": {
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "700"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                        "label-bold": ["14px", {"lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "700"}],
                        "headline-xl": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "800"}]
                    }
                }
            }
        }
    </script>
    
    <!-- Icons always visible - fonts-loaded class applied immediately -->
    <script>
        (function() {
            // Apply fonts-loaded immediately so icons are always visible
            document.documentElement.classList.add('fonts-loaded');
            // Also try to preload font for smoother render
            if (document.fonts) {
                document.fonts.ready.then(function() {
                    document.documentElement.classList.add('fonts-ready');
                });
            }
        })();
    </script>
    <noscript>
        <style>
            .material-symbols-outlined { opacity: 1 !important; }
        </style>
    </noscript>
</head>
