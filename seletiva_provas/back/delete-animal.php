<?php
require 'init.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM animals WHERE id=?');
$stmt->execute([$id]);
$a = $stmt->fetch();

if ($a) {
    // copia pro histórico
    $ins = $pdo->prepare('INSERT INTO excluded_animals
        (name,scientific_name,description,size,weight,feed_class_id,extinction_risk_id,operation_status,category_id,images,visits)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)');
    $ins->execute([
        $a['name'], $a['scientific_name'], $a['description'], $a['size'], $a['weight'],
        $a['feed_class_id'], $a['extinction_risk_id'], $a['operation_status'], $a['category_id'],
        $a['images'], $a['visits'],
    ]);

    // move a pasta de imagens
    $folder = slug($a['name']);
    $from = __DIR__ . "/uploads/animals/$folder";
    $to   = __DIR__ . "/uploads/animals/excluded_animals/$folder";
    if (is_dir($from)) rename($from, $to);

    $pdo->prepare('DELETE FROM animals WHERE id=?')->execute([$id]);
}

redirect('animals');
