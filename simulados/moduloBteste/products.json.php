<?php
include 'config.php';
header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query = isset($_GET['query']) ? "%" . $_GET['query'] . "%" : "%";

$sql = "SELECT * FROM products WHERE is_hidden = 0 
        AND (name_en LIKE :q OR description_en LIKE :q) 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':q', $query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
