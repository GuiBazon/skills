<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
$gtin = $_GET['gtin'] ?? '';

// Verificar se produto está oculto
$stmt = $pdo->prepare("SELECT hidden FROM products WHERE gtin = ?");
$stmt->execute([$gtin]);
$prod = $stmt->fetch();

if ($prod && $prod['hidden'] == 1) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE gtin = ?");
    $stmt->execute([$gtin]);
}

header("Location: products.php");
exit;
