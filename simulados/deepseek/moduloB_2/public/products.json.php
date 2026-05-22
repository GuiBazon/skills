<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['query'] ?? '';

$sql = "SELECT gtin, name_en, name_fr, description_en FROM products WHERE hidden=0";
if ($search) {
    $sql .= " AND (name_en LIKE :s OR name_fr LIKE :s OR description_en LIKE :s OR description_fr LIKE :s)";
}
$total = $pdo->prepare(str_replace('SELECT gtin, name_en, name_fr, description_en', 'SELECT COUNT(*)', $sql));
if ($search) $total->execute([':s' => "%$search%"]);
else $total->execute();
$totalRows = $total->fetchColumn();

$sql .= " LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
if ($search) $stmt->execute([':s' => "%$search%"]);
else $stmt->execute();
$data = $stmt->fetchAll();

if ($page == 1 && empty($data)) {
    http_response_code(404);
    echo json_encode(['error' => 'No products found']);
    exit;
}

echo json_encode([
    'data' => $data,
    'pagination' => [
        'current_page' => (int)$page,
        'total_pages' => ceil($totalRows / $limit),
        'per_page' => $limit,
        'next_page_url' => $page < ceil($totalRows / $limit) ? "/products.json.php?page=" . ($page + 1) . ($search ? "&query=" . urlencode($search) : "") : null,
        'prev_page_url' => $page > 1 ? "/products.json.php?page=" . ($page - 1) . ($search ? "&query=" . urlencode($search) : "") : null
    ]
]);
