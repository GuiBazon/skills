<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$sortBy  = strtolower($_GET['sort_by'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$where  = ["operation_status != 'fora_de_exibicao'"];
$params = [];

if (isset($_GET['max_size'])) { $where[] = 'size <= ?'; $params[] = $_GET['max_size']; }
if (isset($_GET['min_size'])) { $where[] = 'size >= ?'; $params[] = $_GET['min_size']; }
if (isset($_GET['max_weight'])) { $where[] = 'weight <= ?'; $params[] = $_GET['max_weight']; }
if (isset($_GET['min_weight'])) { $where[] = 'weight >= ?'; $params[] = $_GET['min_weight']; }
if (isset($_GET['category_id']) && $_GET['category_id'] !== '') { $where[] = 'a.category_id = ?'; $params[] = $_GET['category_id']; }
if (isset($_GET['risk']) && $_GET['risk'] !== '') { $where[] = 'r.acronym = ?'; $params[] = strtoupper($_GET['risk']); }

$whereSql = 'WHERE ' . implode(' AND ', $where);

$countStmt = $pdo->prepare("SELECT COUNT(*) c FROM animals a JOIN extinction_risks r ON r.id = a.extinction_risk_id $whereSql");
$countStmt->execute($params);
$total = (int)$countStmt->fetch()['c'];
$totalPages = max(1, (int)ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$sql = "SELECT a.*, r.description AS risk_desc, r.acronym AS risk_acronym, f.name AS feed_name
        FROM animals a
        JOIN extinction_risks r ON r.id = a.extinction_risk_id
        JOIN feed_classes f ON f.id = a.feed_class_id
        $whereSql
        ORDER BY a.visits $sortBy
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$data = array_map(function ($a) {
    return [
        'name' => [
            'common' => $a['name'],
            'scientific' => $a['scientific_name'],
        ],
        'description' => $a['description'],
        'measures' => [
            'size' => (float)$a['size'],
            'weight' => (float)$a['weight'],
        ],
        'feed_class' => $a['feed_name'],
        'extinction_risk' => [
            'description' => $a['risk_desc'],
            'acronym' => $a['risk_acronym'],
        ],
    ];
}, $rows);

$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');
$qs = $_GET;

$qs['page'] = $page + 1;
$nextUrl = $page < $totalPages ? $baseUrl . '?' . http_build_query($qs) : null;

$qs['page'] = $page - 1;
$prevUrl = $page > 1 ? $baseUrl . '?' . http_build_query($qs) : null;

echo json_encode([
    'data' => $data,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'per_page' => $perPage,
        'next_page_url' => $nextUrl,
        'prev_page_url' => $prevUrl,
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
