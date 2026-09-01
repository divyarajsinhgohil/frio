<?php
/**
 * FRIO Frontend - System Configuration
 * Defines core parameters and implements the high-resilience REST API data consumer.
 */

// Detect environment and configure local vs. production live links
$_protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_host      = $_SERVER['HTTP_HOST'] ?? 'localhost'; // e.g. localhost OR 192.168.1.5

$is_localhost = ($_host === 'localhost' || $_host === '127.0.0.1' || strpos($_host, '192.168.') === 0 || strpos($_host, '10.') === 0 || strpos($_host, '172.') === 0);

if ($is_localhost) {
    define('FRONTEND_LIVE_URL', $_protocol . '://' . $_host . '/FrioFront/');
    define('API_BASE_URL', $_protocol . '://' . $_host . '/FrioAdmin/');
} else {
    // Production settings
    define('FRONTEND_LIVE_URL', 'https://frioindia.com/'); // Production storefront URL
    define('API_BASE_URL', 'https://admin.frioindia.com/'); // Production admin api URL
}

/**
 * Fetch and decode JSON payload from specific FRIO REST API endpoints.
 * Implements a dual-fallback design (cURL first, then stream file streams) for server stability.
 */
function api_fetch($endpoint) {
    $url = API_BASE_URL . 'api/front_api/' . $endpoint;
    $response = false;

    // Phase A: cURL Request
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_USERAGENT, 'FrioFront/1.0');
        $response = curl_exec($ch);
        curl_close($ch);
    }

    // Phase B: File Stream Fallback (if cURL is missing/failed)
    if ($response === false) {
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: FrioFront/1.0\r\n",
                "timeout" => 6
            ]
        ];
        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);
    }

    if ($response !== false) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($data['status']) && $data['status'] === 'success') {
            return $data['data'];
        }
    }
    return null;
}

/**
 * Helper to build safe absolute path to admin assets
 */
function asset_url($path) {
    if (empty($path)) {
        return '';
    }
    // If the path is already an absolute HTTP URL, return as-is
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    return API_BASE_URL . ltrim($path, '/');
}
?>
