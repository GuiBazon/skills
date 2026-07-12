<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM animals WHERE id = ?');
$stmt->execute([$id]);
$animal = $stmt->fetch();

if (!$animal) {
    http_response_code(404);
    echo json_encode(['error' => 'Animal não encontrado']);
    exit;
}

$imgStmt = $pdo->prepare('SELECT filename FROM animal_images WHERE animal_id = ? ORDER BY position');
$imgStmt->execute([$id]);
$images = array_column($imgStmt->fetchAll(), 'filename');

$ins = $pdo->prepare('INSERT INTO excluded_animals
    (original_id, name, scientific_name, description, size, weight, feed_class_id, extinction_risk_id, operation_status, category_id, visits, images)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$ins->execute([
    $animal['id'], $animal['name'], $animal['scientific_name'], $animal['description'],
    $animal['size'], $animal['weight'], $animal['feed_class_id'], $animal['extinction_risk_id'],
    $animal['operation_status'], $animal['category_id'], $animal['visits'], json_encode($images)
]);

$slug = slugify($animal['name']);
move_animal_folder($slug);

$pdo->prepare('DELETE FROM animals WHERE id = ?')->execute([$id]);

echo json_encode(['success' => true]);
