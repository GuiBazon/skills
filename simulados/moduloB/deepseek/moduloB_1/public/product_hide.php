<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
$gtin = $_GET['gtin'] ?? '';
$stmt = $pdo->prepare("UPDATE products SET hidden = 1 WHERE gtin = ?");
$stmt->execute([$gtin]);

header("Location: products.php");
exit;