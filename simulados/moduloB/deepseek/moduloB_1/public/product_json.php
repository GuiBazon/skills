<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$gtin = $_GET['gtin'] ?? '';
if (!$gtin) {
    http_response_code(400);
    echo json_encode(['error' => 'GTIN required']);
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, c.name as company_name FROM products p 
                       LEFT JOIN companies c ON p.company_id = c.id
                       WHERE p.gtin = :gtin AND p.hidden = 0");
$stmt->execute([':gtin' => $gtin]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found or hidden']);
    exit;
}
echo json_encode($product, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>