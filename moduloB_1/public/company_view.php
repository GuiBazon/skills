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

// Buscar produtos desta empresa (inclusive ocultos, para o admin ver)
$stmtProd = $pdo->prepare("SELECT * FROM products WHERE company_id = ?");
$stmtProd->execute([$id]);
$products = $stmtProd->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($company['name']) ?></title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
        }

        .info {
            background: #f0f0f0;
            padding: 15px;
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
    <a href="companies.php">← Voltar</a> | <a href="logout.php">Sair</a>
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