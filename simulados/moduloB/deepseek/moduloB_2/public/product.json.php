<?php
header('Content-Type: application/json');
require_once '../config/db.php';
$gtin = $_GET['gtin'];
$stmt = $pdo->prepare('SELECT * FROM products WHERE gtin=? AND hidden=0');
$stmt->execute([$gtin]);
$p = $stmt->fetch();
if (!$p) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}
echo json_encode($p);
