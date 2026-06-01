<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/db.php'; ?>
<?php
$id = $_GET['id'];
// desativa empresa
$pdo->prepare('UPDATE companies SET deactivated=1 WHERE id=?')->execute([$id]);
// oculta todos os produtos da empresa
$pdo->prepare('UPDATE products SET hidden=1 WHERE company_id=?')->execute([$id]);
header('Location: companies.php');
