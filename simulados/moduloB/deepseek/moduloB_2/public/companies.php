<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/db.php'; ?>
<h2>Empresas ativas</h2>
<ul>
    <?php foreach ($pdo->query('SELECT * FROM companies WHERE deactivated=0') as $c): ?>
        <li>
            <?= $c['name'] ?>
            <a href="company_view.php?id=<?= $c['id'] ?>">ver</a>
            <a href="company_edit.php?id=<?= $c['id'] ?>">editar</a>
            <a href="company_deactivate.php?id=<?= $c['id'] ?>">desativar</a>
        </li>
    <?php endforeach; ?>
</ul>
<a href="company_create.php">nova empresa</a>
<hr>
<h2>Desativadas</h2>
<ul>
    <?php foreach ($pdo->query('SELECT * FROM companies WHERE deactivated=1') as $c): ?>
        <li><?= $c['name'] ?></li>
    <?php endforeach; ?>
</ul>