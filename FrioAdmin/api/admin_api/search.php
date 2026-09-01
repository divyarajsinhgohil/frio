<?php
$base_path = '../../';
require_once $base_path . 'db_connect.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 1) {
    echo json_encode([
        'categories' => [],
        'products' => [],
        'banners' => [],
        'catalogues' => []
    ]);
    exit;
}

$search = "%{$query}%";
$results = [
    'categories' => [],
    'products' => [],
    'banners' => [],
    'catalogues' => []
];

try {
    // 1. Search Categories
    $stmt = $pdo->prepare("SELECT id, code, name FROM category WHERE name LIKE ? OR code LIKE ? OR description LIKE ? LIMIT 5");
    $stmt->execute([$search, $search, $search]);
    $results['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Search Products
    $stmt = $pdo->prepare("SELECT id, code, name FROM product WHERE name LIKE ? OR code LIKE ? OR description LIKE ? LIMIT 5");
    $stmt->execute([$search, $search, $search]);
    $results['products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Search Banners
    $stmt = $pdo->prepare("SELECT id, name FROM banner_slider WHERE name LIKE ? OR description LIKE ? LIMIT 5");
    $stmt->execute([$search, $search]);
    $results['banners'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Search Catalogues / brochures
    $stmt = $pdo->prepare("SELECT id, name FROM catalogue WHERE name LIKE ? LIMIT 5");
    $stmt->execute([$search]);
    $results['catalogues'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}
