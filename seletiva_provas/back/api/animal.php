<?php
require '../init.php';
header('Content-Type: application/json; charset=utf-8');

$name = str_replace('-', ' ', trim($_GET['name'] ?? ''));

$stmt = $pdo->prepare("SELECT a.*, r.description risk_desc, r.acronym risk_acronym, f.name feed_name
    FROM animals a
    JOIN extinction_risks r ON r.id = a.extinction_risk_id
    JOIN feed_classes f ON f.id = a.feed_class_id
    WHERE a.name LIKE ? LIMIT 1");
$stmt->execute(["%$name%"]);
$a = $stmt->fetch();

if (!$a) {
    http_response_code(404);
    echo json_encode(['error' => 'Animal não encontrado']);
    exit;
}

$pdo->prepare('UPDATE animals SET visits = visits + 1 WHERE id=?')->execute([$a['id']]);

$folder = slug($a['name']);
$base = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$pictures = array_map(fn($img) => "$base/animals/$folder/$img", array_filter(explode(',', $a['images'])));

$res = [
    'name' => ['common' => $a['name'], 'scientific' => $a['scientific_name']],
    'description' => $a['description'],
    'measures' => ['size' => (float)$a['size'], 'weight' => (float)$a['weight']],
    'feed_class' => $a['feed_name'],
    'extinction_risk' => ['description' => $a['risk_desc'], 'acronym' => $a['risk_acronym']],
    'pictures' => array_values($pictures),
];

if ($a['risk_acronym'] === 'CR') {
    $res['notice'] = 'Este animal está criticamente ameaçado de extinção';
}

echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
