<?php
session_start();
require_once 'db_connect.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashbord.php");
    exit;
}

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        try {
            // Fetch user from DB
            $stmt = $pdo->prepare("SELECT * FROM `admin_users` WHERE `username` = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Perform MD5 verification
            if ($user && $user['password'] === md5($password)) {
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                
                // Redirect to dashboard
                header("Location: dashbord.php");
                exit;
            } else {
                $error_msg = "Invalid username or password";
            }
        } catch (PDOException $e) {
            $error_msg = "Database error occurred. Please try again.";
        }
    } else {
        $error_msg = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>FRIO Admin Login</title>
<!-- Fonts are self-hosted locally via assets/css/style.css - no CDN needed -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary": "#303436",
                        "surface-dim": "#cfdaf1",
                        "error": "#ba1a1a",
                        "secondary": "#735c00",
                        "secondary-fixed-dim": "#e9c349",
                        "on-primary-container": "#8dbcfe",
                        "on-error": "#ffffff",
                        "on-background": "#111c2c",
                        "inverse-primary": "#a4c9ff",
                        "on-secondary-container": "#745c00",
                        "tertiary-fixed": "#e0e3e6",
                        "secondary-fixed": "#ffe088",
                        "outline": "#727781",
                        "on-surface-variant": "#424750",
                        "on-secondary-fixed": "#241a00",
                        "surface-container": "#e7eeff",
                        "primary-container": "#0c4b86",
                        "background": "#f9f9ff",
                        "on-secondary-fixed-variant": "#574500",
                        "on-tertiary": "#ffffff",
                        "inverse-on-surface": "#ebf1ff",
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary-fixed": "#191c1e",
                        "inverse-surface": "#263142",
                        "error-container": "#ffdad6",
                        "surface-container-low": "#f0f3ff",
                        "on-primary-fixed-variant": "#044882",
                        "primary-fixed": "#d4e3ff",
                        "on-primary-fixed": "#001c39",
                        "tertiary-fixed-dim": "#c4c7ca",
                        "tertiary-container": "#474a4d",
                        "surface-container-highest": "#d8e3fa",
                        "surface": "#f9f9ff",
                        "on-tertiary-fixed-variant": "#44474a",
                        "on-tertiary-container": "#b7b9bc",
                        "on-error-container": "#93000a",
                        "surface-variant": "#d8e3fa",
                        "primary": "#003462",
                        "surface-tint": "#2c609c",
                        "secondary-container": "#fed65b",
                        "on-secondary": "#ffffff",
                        "surface-container-high": "#dee8ff",
                        "primary-fixed-dim": "#a4c9ff",
                        "on-primary": "#ffffff",
                        "outline-variant": "#c2c6d1",
                        "on-surface": "#111c2c",
                        "surface-bright": "#f9f9ff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "gutter": "24px",
                        "margin-mobile": "16px",
                        "unit": "8px",
                        "max-width": "1440px",
                        "margin-desktop": "64px"
                    },
                    "fontFamily": {
                        "headline-lg-mobile": ["Hanken Grotesk"],
                        "headline-xl": ["Hanken Grotesk"],
                        "headline-md": ["Hanken Grotesk"],
                        "headline-lg": ["Hanken Grotesk"],
                        "label-sm": ["Hanken Grotesk"],
                        "label-bold": ["Hanken Grotesk"],
                        "body-md": ["Hanken Grotesk"],
                        "body-lg": ["Hanken Grotesk"]
                    },
                    "fontSize": {
                        "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "700"}],
                        "headline-xl": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "800"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                        "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                        "label-bold": ["14px", {"lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "700"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}]
                    }
                },
            },
        }
    </script>
<style>
        /* Self-hosted fonts - no CDN needed */
        @font-face {
            font-family: 'Material Symbols Outlined';
            font-style: normal;
            font-weight: 100 700;
            font-display: block;
            src: url('assets/fonts/MaterialSymbolsOutlined.ttf') format('truetype');
        }
        @font-face {
            font-family: 'Hanken Grotesk';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url('assets/fonts/HankenGrotesk-Regular.ttf') format('truetype');
        }
        @font-face {
            font-family: 'Hanken Grotesk';
            font-style: normal;
            font-weight: 500 800;
            font-display: swap;
            src: url('assets/fonts/HankenGrotesk-SemiBold.ttf') format('truetype');
        }
        body {
            background-color: #f9f9ff;
            background-image: 
                linear-gradient(rgba(207, 218, 241, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(207, 218, 241, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
            font-family: 'Hanken Grotesk', sans-serif;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 30px rgba(12, 75, 134, 0.05);
        }

        .input-focus-brass:focus {
            border-color: #e9c349;
            box-shadow: 0 0 0 2px rgba(233, 195, 73, 0.2);
            outline: none;
        }

        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            vertical-align: middle;
            line-height: 1;
            font-style: normal;
            font-weight: normal;
            /* Always visible - font loaded from local assets/fonts/ */
            opacity: 1;
            -webkit-font-smoothing: antialiased;
        }

        .fonts-loaded .material-symbols-outlined {
            opacity: 1;
        }

        .btn-glow:hover {
            box-shadow: 0 0 15px rgba(12, 75, 134, 0.4);
        }
    </style>
    <!-- Icons always visible - fonts loaded locally -->
    <script>
        (function() {
            // Apply fonts-loaded immediately - fonts are self-hosted, always available
            document.documentElement.classList.add('fonts-loaded');
        })();
    </script>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4">
<!-- Content Canvas -->
<main class="w-full flex flex-col items-center justify-center space-y-8">
<!-- Login Card -->
<div class="glass-card w-full max-w-[500px] rounded-xl p-10 flex flex-col items-center">
<!-- Top Section -->
<div class="flex flex-col items-center mb-8 w-full text-center">
<div class="mb-4 flex items-center justify-center">
<img src="assets/imag/frio-logo.png" alt="Frio Logo" class="h-16 w-auto object-contain" />
</div>
<span class="font-label-bold text-label-bold text-secondary tracking-widest mb-4">ADMIN PANEL</span>
<h2 class="font-headline-md text-headline-md text-on-surface">WELCOME BACK</h2>
<p class="font-body-md text-body-md text-on-surface-variant">Sign in to continue</p>
</div>
<!-- Form -->
<form class="w-full space-y-6" id="loginForm" method="POST" action="login.php">
<!-- Error Area -->
<?php if (!empty($error_msg)): ?>
<div class="bg-error-container/50 border border-error/20 p-4 rounded-lg flex items-center gap-3 transition-all duration-300" id="errorArea">
<span class="material-symbols-outlined text-error">error</span>
<p class="font-label-sm text-label-sm text-on-error-container"><?php echo htmlspecialchars($error_msg); ?></p>
</div>
<?php endif; ?>

<!-- Username Input -->
<div class="space-y-2">
<label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="username">USERNAME</label>
<div class="relative group">
<input class="w-full bg-white/50 border border-outline-variant px-4 py-4 rounded-lg font-body-md text-body-md transition-all duration-200 input-focus-brass" id="username" name="username" placeholder="Enter username" type="text" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required />
<span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-secondary transition-colors">person</span>
</div>
</div>
<!-- Password Input -->
<div class="space-y-2">
<label class="font-label-bold text-label-bold text-on-surface-variant ml-1" for="password">PASSWORD</label>
<div class="relative group">
<input class="w-full bg-white/50 border border-outline-variant px-4 py-4 rounded-lg font-body-md text-body-md transition-all duration-200 input-focus-brass" id="password" name="password" placeholder="Enter password" type="password" required />
<button class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-secondary transition-colors" onclick="togglePassword()" type="button">
<span class="material-symbols-outlined" id="passwordIcon">visibility</span>
</button>
</div>
</div>
<!-- Action Button -->
<button class="w-full bg-primary-container text-white py-4 rounded-lg font-label-bold text-label-bold uppercase tracking-widest transition-all duration-300 transform hover:scale-[1.02] active:scale-95 btn-glow flex items-center justify-center gap-2" type="submit">
<span>Login</span>
<span class="material-symbols-outlined text-[20px]">login</span>
</button>

</form>
</div>
<!-- Footer -->
<footer class="text-center">
<p class="font-label-bold text-label-bold text-on-surface-variant opacity-60 tracking-widest uppercase">FRIO Admin Panel</p>
<p class="font-label-sm text-label-sm text-on-surface-variant opacity-40 mt-1">© 2024 Industrial Reliability Solutions</p>
</footer>
</main>
<!-- Visual Atmosphere -->
<div class="fixed top-0 left-0 w-full h-full pointer-events-none -z-10 overflow-hidden">
<div class="absolute top-[-10%] right-[-5%] w-[500px] h-[500px] bg-primary-container/5 rounded-full blur-[120px]"></div>
<div class="absolute bottom-[-10%] left-[-5%] w-[400px] h-[400px] bg-secondary-container/5 rounded-full blur-[100px]"></div>
</div>
<script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.innerText = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                passwordIcon.innerText = 'visibility';
            }
        }
    </script>
</body></html>
