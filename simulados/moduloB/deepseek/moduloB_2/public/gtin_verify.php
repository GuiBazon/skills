<?php require_once '../config/db.php'; ?>
<?php
$results = [];
$allValid = false;
if ($_POST) {
    $gtins = explode("\n", trim($_POST['gtins']));
    $allValid = true;
    foreach ($gtins as $gtin) {
        $gtin = trim($gtin);
        if (!$gtin) continue;
        $stmt = $pdo->prepare('SELECT id FROM products WHERE gtin=? AND hidden=0');
        $stmt->execute([$gtin]);
        $valid = (bool)$stmt->fetch();
        $results[] = ['gtin' => $gtin, 'valid' => $valid];
        if (!$valid) $allValid = false;
    }
}
?>
<!DOCTYPE html>
<html>

<body>
    <a href="login.php">Admin Login</a>
    <form method="post">
        <textarea name="gtins" rows="5" cols="30" placeholder="Um GTIN por linha"></textarea><br>
        <button>Verificar</button>
    </form>
    <?php if ($results): ?>
        <?php if ($allValid): ?>
            <p>All corrects</p>
        <?php endif; ?>
        <ul>
            <?php foreach ($results as $r): ?>
                <li><?= $r['gtin'] ?>: <?= $r['valid'] ? 'válido' : 'inválido' ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>

</html>