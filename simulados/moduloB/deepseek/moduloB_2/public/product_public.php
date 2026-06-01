<?php require_once '../config/db.php'; ?>
<?php
$gtin = $_GET['gtin'];
$lang = $_GET['lang'] ?? 'en';
if (!in_array($lang, ['en', 'fr'])) $lang = 'en';

$stmt = $pdo->prepare('SELECT p.*, c.name as company_name FROM products p JOIN companies c ON p.company_id=c.id WHERE p.gtin=? AND p.hidden=0');
$stmt->execute([$gtin]);
$p = $stmt->fetch();
if (!$p) {
    http_response_code(404);
    die('Produto não encontrado');
}
$name = $lang === 'en' ? $p['name_en'] : $p['name_fr'];
$desc = $lang === 'en' ? $p['description_en'] : $p['description_fr'];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<body>
    <div>
        <a href="?gtin=<?= $gtin ?>&lang=en">English</a> |
        <a href="?gtin=<?= $gtin ?>&lang=fr">Français</a>
    </div>
    <h1><?= htmlspecialchars($name) ?></h1>
    <p>Empresa: <?= htmlspecialchars($p['company_name']) ?></p>
    <p>GTIN: <?= htmlspecialchars($p['gtin']) ?></p>
    <p>Descrição: <?= nl2br(htmlspecialchars($desc)) ?></p>
    <?php if ($p['image_path']): ?>
        <img src="../<?= $p['image_path'] ?>" width="200">
    <?php else: ?>
        <img src="https://via.placeholder.com/200?text=No+Image">
    <?php endif; ?>
</body>

</html>