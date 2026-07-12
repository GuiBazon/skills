<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$name = trim($_GET['name'] ?? '');
$name = str_replace('-', ' ', $name);

$stmt = $pdo->prepare("SELECT a.*, r.description AS risk_desc, r.acronym AS risk_acronym, f.name AS feed_name
    FROM animals a
    JOIN extinction_risks r ON r.id = a.extinction_risk_id
    JOIN feed_classes f ON f.id = a.feed_class_id
    WHERE a.name LIKE ?
    LIMIT 1");
$stmt->execute(["%$name%"]);
$animal = $stmt->fetch();

if (!$animal) {
    http_response_code(404);
    echo json_encode(['error' => 'Animal não encontrado']);
    exit;
}

$pdo->prepare('UPDATE animals SET visits = visits + 1 WHERE id = ?')->execute([$animal['id']]);

$slug = slugify($animal['name']);
$imgStmt = $pdo->prepare('SELECT filename FROM animal_images WHERE animal_id = ? ORDER BY position');
$imgStmt->execute([$animal['id']]);
$images = $imgStmt->fetchAll();

$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$pictures = array_map(function ($img) use ($baseUrl, $slug) {
    return "$baseUrl/animals/$slug/{$img['filename']}";
}, $images);

$response = [
    'name' => [
        'common' => $animal['name'],
        'scientific' => $animal['scientific_name'],
    ],
    'description' => $animal['description'],
    'measures' => [
        'size' => (float)$animal['size'],
        'weight' => (float)$animal['weight'],
    ],
    'feed_class' => $animal['feed_name'],
    'extinction_risk' => [
        'description' => $animal['risk_desc'],
        'acronym' => $animal['risk_acronym'],
    ],
    'pictures' => $pictures,
];

if ($animal['risk_acronym'] === 'CR') {
    $response['notice'] = 'Este animal está criticamente ameaçado de extinção';
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
