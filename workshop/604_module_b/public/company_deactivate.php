<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT deactivated FROM companies WHERE id = ?");
$stmt->execute([$id]);
$company = $stmt->fetch();

if (!$company) {
    die("Empresa não encontrada.");
}

if ($company['deactivated'] == 1) {
    header("Location: companies.php");
    exit;
}

$stmt = $pdo->prepare("UPDATE companies SET deactivated = 1 WHERE id = ?");
$stmt->execute([$id]);

$stmt = $pdo->prepare("UPDATE products SET hidden = 1 WHERE company_id = ?");
$stmt->execute([$id]);

header("Location: companies.php");
exit;
