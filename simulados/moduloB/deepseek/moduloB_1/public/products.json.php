<?php
header('Content-Type: application/json');
require_once '../config/database.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$limit = 10;
$offset = ($page - 1) * $limit;

$sql = "SELECT p.gtin, p.name_en, p.name_fr, p.description_en, p.description_fr, p.brand, p.image_path, c.name as company_name
        FROM products p
        LEFT JOIN companies c ON p.company_id = c.id
        WHERE p.hidden = 0";
$params = [];

if (!empty($query)) {
    $sql .= " AND (p.name_en LIKE :q OR p.name_fr LIKE :q OR p.description_en LIKE :q OR p.description_fr LIKE :q)";
    $params[':q'] = '%' . $query . '%';
}

// Contar total
$countSql = str_replace("SELECT p.gtin, p.name_en, p.name_fr, p.description_en, p.description_fr, p.brand, p.image_path, c.name as company_name",
                         "SELECT COUNT(*) as total", $sql);
$stmtCount = $pdo->prepare($countSql);
foreach ($params as $k => $v) $stmtCount->bindValue($k, $v);
$stmtCount->execute();
$total = $stmtCount->fetch()['total'];
$totalPages = ceil($total / $limit);

if ($total == 0) {
    http_response_code(404);
    echo json_encode(['error' => 'No products found']);
    exit;
}

$sql .= " LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

$baseUrl = "/01_module_b/public/products.json.php";
$nextUrl = ($page < $totalPages) ? $baseUrl . "?page=" . ($page+1) . (!empty($query) ? "&query=" . urlencode($query) : "") : null;
$prevUrl = ($page > 1) ? $baseUrl . "?page=" . ($page-1) . (!empty($query) ? "&query=" . urlencode($query) : "") : null;

$response = [
    'data' => $products,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'per_page' => $limit,
        'next_page_url' => $nextUrl,
        'prev_page_url' => $prevUrl
    ]
];
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>