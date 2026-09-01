<?php
/**
 * FRIO Admin API - File Upload Endpoint
 * Securely uploads images or PDFs and returns the clean relative path for database storage.
 */
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 1. Authenticate Request
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Only POST is supported.']);
    exit(0);
}

// 2. Validate Type & File
$type = isset($_POST['type']) ? trim($_POST['type']) : '';
if (empty($type)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing upload type parameter (category, banners, product, gallery, catalogue_preview, catalogue_pdf).']);
    exit(0);
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errCode = isset($_FILES['file']['error']) ? $_FILES['file']['error'] : UPLOAD_ERR_NO_FILE;
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload error code: ' . $errCode]);
    exit(0);
}

// 3. Define Mappings & Directories
$allowedImageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$mappings = [
    'category' => [
        'dir' => '../../assets/imag/category/',
        'prefix' => 'cat_',
        'exts' => $allowedImageExts,
        'db_prefix' => 'assets/imag/category/'
    ],
    'banners' => [
        'dir' => '../../assets/imag/banners/',
        'prefix' => 'banner_',
        'exts' => $allowedImageExts,
        'db_prefix' => 'assets/imag/banners/'
    ],
    'product' => [
        'dir' => '../../assets/imag/product/',
        'prefix' => 'prod_',
        'exts' => $allowedImageExts,
        'db_prefix' => 'assets/imag/product/'
    ],
    'gallery' => [
        'dir' => '../../assets/imag/product/gallery/',
        'prefix' => 'gal_',
        'exts' => $allowedImageExts,
        'db_prefix' => 'assets/imag/product/gallery/'
    ],
    'catalogue_preview' => [
        'dir' => '../../assets/imag/catalogue/',
        'prefix' => 'cover_',
        'exts' => $allowedImageExts,
        'db_prefix' => 'assets/imag/catalogue/'
    ],
    'catalogue_pdf' => [
        'dir' => '../../assets/pdf/catalogue/',
        'prefix' => 'brochure_',
        'exts' => ['pdf'],
        'db_prefix' => 'assets/pdf/catalogue/'
    ]
];

if (!array_key_exists($type, $mappings)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid upload type: ' . htmlspecialchars($type)]);
    exit(0);
}

$map = $mappings[$type];
$fileName = $_FILES['file']['name'];
$fileTmp  = $_FILES['file']['tmp_name'];
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// 4. Validate Extensions
if (!in_array($ext, $map['exts'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid file extension. Allowed formats: ' . implode(', ', $map['exts'])]);
    exit(0);
}

// 5. Ensure Upload Directory Exists
if (!is_dir($map['dir'])) {
    if (!mkdir($map['dir'], 0777, true)) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to create upload destination directory on server.']);
        exit(0);
    }
}

// 6. Generate Unique File Name & Save
$newFileName = $map['prefix'] . time() . '_' . md5(uniqid()) . '.' . $ext;
$destPath = $map['dir'] . $newFileName;
$relativeDBPath = $map['db_prefix'] . $newFileName;

if (move_uploaded_file($fileTmp, $destPath)) {
    echo json_encode([
        'status' => 'success',
        'message' => 'File uploaded successfully.',
        'data' => [
            'name' => $newFileName,
            'path' => $relativeDBPath
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error moving the uploaded file on the server.']);
}
?>
