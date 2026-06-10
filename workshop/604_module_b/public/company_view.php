<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$id]);
$company = $stmt->fetch();

if (!$company) {
    die("Empresa não encontrada.");
}

$stmtProd = $pdo->prepare("SELECT * FROM products WHERE company_id = ?");
$stmtProd->execute([$id]);
$products = $stmtProd->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($company['name']) ?></title>
</head>

<body>
    <div>
        <a href="companies.php" >Empresas</a>
        <a href="products.php" >Produtos</a>
        <a href="gtin_verify.php" >Verificar GTIN</a>
    </div>
    <a href="companies.php">Voltar</a>
    <h2><?= htmlspecialchars($company['name']) ?></h2>
    <div class="info">
        <p><strong>Endereço:</strong> <?= nl2br(htmlspecialchars($company['address'])) ?></p>
        <p><strong>Telefone:</strong> <?= htmlspecialchars($company['phone']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($company['email']) ?></p>
        <h3>Proprietário</h3>
        <p><?= htmlspecialchars($company['owner_name']) ?> | <?= htmlspecialchars($company['owner_phone']) ?> | <?= htmlspecialchars($company['owner_email']) ?></p>
        <h3>Contato</h3>
        <p><?= htmlspecialchars($company['contact_name']) ?> | <?= htmlspecialchars($company['contact_phone']) ?> | <?= htmlspecialchars($company['contact_email']) ?></p>
        <p><strong>Status:</strong> <?= $company['deactivated'] ? 'Desativada' : 'Ativa' ?></p>
        <a href="company_edit.php?id=<?= $company['id'] ?>">Editar empresa</a>
        <?php if (!$company['deactivated']): ?>
            | <a href="company_deactivate.php?id=<?= $company['id'] ?>" onclick="return confirm('Desativar empresa? Todos os produtos ficarão ocultos.')">Desativar</a>
        <?php endif; ?>
    </div>

    <h3>Produtos desta empresa</h3>
    <?php if (count($products) > 0): ?>
        <table>
            <tr>
                <th>GTIN</th>
                <th>Nome (EN)</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($products as $prod): ?>
                <tr>
                    <td><?= htmlspecialchars($prod['gtin']) ?></td>
                    <td><?= htmlspecialchars($prod['name_en']) ?></td>
                    <td><?= $prod['hidden'] ? 'Oculto' : 'Visível' ?></td>
                    <td>
                        <a href="product_edit.php?gtin=<?= $prod['gtin'] ?>">Editar</a>
                        <?php if (!$prod['hidden']): ?>
                            | <a href="product_hide.php?gtin=<?= $prod['gtin'] ?>" onclick="return confirm('Ocultar produto?')">Ocultar</a>
                        <?php else: ?>
                            | <a href="product_delete.php?gtin=<?= $prod['gtin'] ?>" onclick="return confirm('Excluir permanentemente?')">Excluir</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum produto cadastrado para esta empresa.</p>
    <?php endif; ?>
</body>

</html>