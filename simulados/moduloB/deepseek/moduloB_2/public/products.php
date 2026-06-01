<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/db.php'; ?>
<h2>Produtos visíveis</h2>
<ul>
    <?php foreach ($pdo->query('SELECT * FROM products WHERE hidden=0') as $p): ?>
        <li>
            <?= $p['name_en'] ?> (<?= $p['gtin'] ?>)
            <a href="product_edit.php?gtin=<?= $p['gtin'] ?>">editar</a>
            <a href="product_hide.php?gtin=<?= $p['gtin'] ?>">ocultar</a>
        </li>
    <?php endforeach; ?>
</ul>
<h2>Produtos ocultos</h2>
<ul>
    <?php foreach ($pdo->query('SELECT * FROM products WHERE hidden=1') as $p): ?>
        <li>
            <?= $p['name_en'] ?>
            <a href="product_edit.php?gtin=<?= $p['gtin'] ?>">editar</a>
            <a href="product_delete.php?gtin=<?= $p['gtin'] ?>">excluir</a>
        </li>
    <?php endforeach; ?>
</ul>
<a href="product_create.php">novo produto</a>