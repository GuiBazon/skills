<?php
require 'includes/auth.php';
require 'config/db.php';

$empresas = $pdo->query("SELECT * FROM companies")->fetchAll();
?>

<h2>Ativas</h2>
<?php foreach ($empresas as $e): if ($e['is_active'] == 1): ?>
    <p>
        <?= $e['company_name'] ?> - 
        <a href="company_view.php?id=<?= $e['company_id'] ?>">Ver</a> | 
        <a href="company_deactivate.php?id=<?= $e['company_id'] ?>">Desativar</a>
    </p>
<?php endif; endforeach; ?>

<h2>Inativas</h2>
<?php foreach ($empresas as $e): if ($e['is_active'] == 0): ?>
    <p><?= $e['company_name'] ?></p>
<?php endif; endforeach; ?>