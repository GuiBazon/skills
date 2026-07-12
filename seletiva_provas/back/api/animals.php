<?php
require '../init.php';
header('Content-Type: application/json; charset=utf-8');

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$sort    = strtolower($_GET['sort_by'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$sql = "SELECT a.*, r.description risk_desc, r.acronym risk_acronym, f.name feed_name
        FROM animals a
        JOIN extinction_risks r ON r.id = a.extinction_risk_id
        JOIN feed_classes f ON f.id = a.feed_class_id
        WHERE a.operation_status != 'fora_de_exibicao'";
$params = [];

if (isset($_GET['max_size']))   { $sql .= " AND a.size <= ?";   $params[] = $_GET['max_size']; }
if (isset($_GET['min_size']))   { $sql .= " AND a.size >= ?";   $params[] = $_GET['min_size']; }
if (isset($_GET['max_weight'])) { $sql .= " AND a.weight <= ?"; $params[] = $_GET['max_weight']; }
if (isset($_GET['min_weight'])) { $sql .= " AND a.weight >= ?"; $params[] = $_GET['min_weight']; }
if (!empty($_GET['category_id'])) { $sql .= " AND a.category_id = ?"; $params[] = $_GET['category_id']; }
if (!empty($_GET['risk']))      { $sql .= " AND r.acronym = ?"; $params[] = strtoupper($_GET['risk']); }

// total para paginação
$total = $pdo->prepare(str_replace('a.*, r.description risk_desc, r.acronym risk_acronym, f.name feed_name', 'COUNT(*) c', $sql));
$total->execute($params);
$totalRows = (int)$total->fetch()['c'];
$totalPages = max(1, (int)ceil($totalRows / $perPage));
$offset = ($page - 1) * $perPage;

$sql .= " ORDER BY a.visits $sort LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$data = array_map(function ($a) {
    return [
        'name' => ['common' => $a['name'], 'scientific' => $a['scientific_name']],
        'description' => $a['description'],
        'measures' => ['size' => (float)$a['size'], 'weight' => (float)$a['weight']],
        'feed_class' => $a['feed_name'],
        'extinction_risk' => ['description' => $a['risk_desc'], 'acronym' => $a['risk_acronym']],
    ];
}, $rows);

$base = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?');
$q = $_GET;
$q['page'] = $page + 1;
$next = $page < $totalPages ? $base . '?' . http_build_query($q) : null;
$q['page'] = $page - 1;
$prev = $page > 1 ? $base . '?' . http_build_query($q) : null;

echo json_encode([
    'data' => $data,
    'pagination' => [
        'current_page' => $page, 'total_pages' => $totalPages, 'per_page' => $perPage,
        'next_page_url' => $next, 'prev_page_url' => $prev,
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
