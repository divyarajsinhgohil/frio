<?php
/**
 * FRIO Admin API - Security Guard
 * Included at the top of secure CRUD APIs to verify active session state.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access. Active administrator session required.'
    ]);
    exit(0);
}
?>
