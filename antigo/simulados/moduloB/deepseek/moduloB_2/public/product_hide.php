<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/db.php'; ?>
<?php
$pdo->prepare('UPDATE products SET hidden=1 WHERE gtin=?')->execute([$_GET['gtin']]);
header('Location: products.php');
