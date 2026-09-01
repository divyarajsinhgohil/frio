<?php
/**
 * FRIO Admin Panel - Authentication Middleware
 * Included at the top of secure admin pages to verify session state.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the session variable is not set or not true, redirect to login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: " . (isset($base_path) ? $base_path : "") . "login.php");
    exit;
}
