<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/db.php'; ?>
<?php
$id = $_GET['id'];
$c = $pdo->prepare('SELECT * FROM companies WHERE id=?');
$c->execute([$id]);
$company = $c->fetch();
echo "<h2>{$company['name']}</h2>";

$prods = $pdo->prepare('SELECT * FROM products WHERE company_id=?');
$prods->execute([$id]);
echo "<h3>Produtos</h3><ul>";
foreach ($prods as $p) {
    echo "<li>{$p['name_en']} - GTIN: {$p['gtin']}</li>";
}
echo "</ul>";
?>
<a href="companies.php">voltar</a>