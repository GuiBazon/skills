<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/db.php'; ?>
<?php
// só permite se o produto já estiver oculto
$pdo->prepare('DELETE FROM products WHERE gtin=? AND hidden=1')->execute([$_GET['gtin']]);
header('Location: products.php');
