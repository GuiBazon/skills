<?php
require_once '../config/database.php';
$gtin = $_GET['gtin'] ?? '';
$lang = $_GET['lang'] ?? 'en';
if (!in_array($lang, ['en', 'fr'])) $lang = 'en';

$stmt = $pdo->prepare("SELECT p.*, c.name as company_name FROM products p 
                       LEFT JOIN companies c ON p.company_id = c.id
                       WHERE p.gtin = :gtin AND p.hidden = 0");
$stmt->execute([':gtin' => $gtin]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    echo "Produto não encontrado";
    exit;
}

$productName = $lang === 'en' ? $product['name_en'] : $product['name_fr'];
$productDesc = $lang === 'en' ? $product['description_en'] : $product['description_fr'];
$langAttr = $lang === 'en' ? 'en' : 'fr';
?>
<!DOCTYPE html>
<html lang="<?= $langAttr ?>">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($productName) ?></title>
    <style>
        body {
            font-family: Arial;
            margin: 0;
            padding: 20px;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .lang-switch {
            margin-bottom: 20px;
        }
    </style>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div style="background:#2c3e50; padding:10px; margin-bottom:20px;">
        <a href="home.php" style="color:white; margin-right:15px;">🏠 Home</a>
        <a href="companies.php" style="color:white; margin-right:15px;">🏢 Empresas</a>
        <a href="products.php" style="color:white; margin-right:15px;">📦 Produtos</a>
        <a href="gtin_verify.php" style="color:white; margin-right:15px;">🔍 Verificar GTIN</a>
        <a href="logout.php" style="color:white;">🚪 Sair</a>
    </div>
    <div class="lang-switch">
        <a href="?gtin=<?= $gtin ?>&lang=en">English</a> |
        <a href="?gtin=<?= $gtin ?>&lang=fr">Français</a>
    </div>
    <h1><?= htmlspecialchars($productName) ?></h1>
    <p><strong>Empresa:</strong> <?= htmlspecialchars($product['company_name']) ?></p>
    <p><strong>GTIN:</strong> <?= htmlspecialchars($product['gtin']) ?></p>
    <p><strong><?= $lang === 'en' ? 'Description' : 'Description' ?>:</strong> <?= nl2br(htmlspecialchars($productDesc)) ?></p>
    <p><strong><?= $lang === 'en' ? 'Gross weight' : 'Poids brut' ?>:</strong> <?= $product['gross_weight'] ?> <?= $product['weight_unit'] ?></p>
    <p><strong><?= $lang === 'en' ? 'Net weight' : 'Poids net' ?>:</strong> <?= $product['net_weight'] ?> <?= $product['weight_unit'] ?></p>
    <?php if ($product['image_path']): ?>
        <img src="../assets/<?= $product['image_path'] ?>" alt="Product image">
    <?php else: ?>
        <img src="../assets/uploads/default.png" alt="Placeholder">
    <?php endif; ?>
</body>

</html>