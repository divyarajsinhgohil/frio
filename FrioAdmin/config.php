<?php
/**
 * FRIO Backend Admin - System Configuration
 * Dynamically switches database credentials and domain URLs based on hosting environment.
 */

// Dynamically detect server host
$_protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host      = $_SERVER['HTTP_HOST'] ?? 'localhost';

$is_localhost = ($_host === 'localhost' || $_host === '127.0.0.1' || strpos($_host, '192.168.') === 0 || strpos($_host, '10.') === 0 || strpos($_host, '172.') === 0);

if ($is_localhost) {
    // Local database settings
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u306157368_frio');
    define('DB_USER', 'u306157368_frio');
    define('DB_PASS', 'Frio@#7750');

    define('FRONTEND_LIVE_URL', $_protocol . '://' . $_host . '/Frio/');
    define('API_BASE_URL', $_protocol . '://' . $_host . '/FrioAdmin/');
} else {
    // Production database settings (change these to your live hosting DB credentials)
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'u306157368_frio');
    define('DB_USER', 'u306157368_frio');
    define('DB_PASS', 'Frio@#7750');

    define('FRONTEND_LIVE_URL', 'https://frioindia.com/');
    define('API_BASE_URL', 'https://frioindia.com/FrioAdmin/');
}
?>
