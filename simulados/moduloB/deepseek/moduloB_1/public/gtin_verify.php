<?php
require_once '../config/database.php';
$results = [];
$allValid = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gtins = explode("\n", trim($_POST['gtins']));
    $allValid = true;
    foreach ($gtins as $gtin) {
        $gtin = trim($gtin);
        if (empty($gtin)) continue;
        $stmt = $pdo->prepare("SELECT hidden FROM products WHERE gtin = ?");
        $stmt->execute([$gtin]);
        $product = $stmt->fetch();
        $valid = ($product && $product['hidden'] == 0);
        $results[] = ['gtin' => $gtin, 'valid' => $valid];
        if (!$valid) $allValid = false;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Verificador GTIN - Público</title>
</head>

<body>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>🔍 Verificador de GTIN em massa</h2>
        <a href="login.php" style="background: #2c3e50; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;">🔐 Admin Login</a>
    </div>
    <form method="post">
        <textarea name="gtins" rows="5" cols="40" placeholder="Digite um GTIN por linha"></textarea><br>
        <button type="submit">Verificar</button>
    </form>

    <?php if ($results): ?>
        <h3>Resultados</h3>
        <?php if ($allValid): ?>
            <p style="color:green; font-weight:bold;">✅ All corrects</p>
        <?php endif; ?>
        <ul>
            <?php foreach ($results as $r): ?>
                <li><?= htmlspecialchars($r['gtin']) ?>: <?= $r['valid'] ? '✅ Válido' : '❌ Inválido' ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>

</html>