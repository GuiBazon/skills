<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/database.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Produtos</title>
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
    <h2>Produtos</h2>
    <a href="product_create.php">+ Novo Produto</a> | <a href="companies.php">Empresas</a> | <a href="logout.php">Sair</a>

    <h3>Produtos Visíveis</h3>
    <table border="1">
        <tr>
            <th>GTIN</th>
            <th>Nome (EN)</th>
            <th>Marca</th>
            <th>Empresa</th>
            <th>Ações</th>
        </tr>
        <?php
        $stmt = $pdo->prepare("SELECT p.*, c.name as company_name FROM products p LEFT JOIN companies c ON p.company_id = c.id WHERE p.hidden = 0");
        $stmt->execute();
        while ($row = $stmt->fetch()): ?>
            <tr>
                <td><?= htmlspecialchars($row['gtin']) ?></td>
                <td><?= htmlspecialchars($row['name_en']) ?></td>
                <td><?= htmlspecialchars($row['brand']) ?></td>
                <td><?= htmlspecialchars($row['company_name']) ?></td>
                <td>
                    <a href="product_edit.php?gtin=<?= $row['gtin'] ?>">Editar</a>
                    | <a href="product_hide.php?gtin=<?= $row['gtin'] ?>" onclick="return confirm('Ocultar produto?')">Ocultar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h3>Produtos Ocultos</h3>
    <table border="1">
        <tr>
            <th>GTIN</th>
            <th>Nome (EN)</th>
            <th>Marca</th>
            <th>Empresa</th>
            <th>Ações</th>
        </tr>
        <?php
        $stmt = $pdo->prepare("SELECT p.*, c.name as company_name FROM products p LEFT JOIN companies c ON p.company_id = c.id WHERE p.hidden = 1");
        $stmt->execute();
        while ($row = $stmt->fetch()): ?>
            <tr>
                <td><?= htmlspecialchars($row['gtin']) ?></td>
                <td><?= htmlspecialchars($row['name_en']) ?></td>
                <td><?= htmlspecialchars($row['brand']) ?></td>
                <td><?= htmlspecialchars($row['company_name']) ?></td>
                <td>
                    <a href="product_edit.php?gtin=<?= $row['gtin'] ?>">Editar</a>
                    | <a href="product_delete.php?gtin=<?= $row['gtin'] ?>" onclick="return confirm('Excluir permanentemente?')">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </>
</body>

</html>