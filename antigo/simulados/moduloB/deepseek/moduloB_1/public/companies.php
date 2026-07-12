<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/database.php'; ?>

<!DOCTYPE html>
<html>

<head>
    <title>Empresas</title>
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
    <h2>Empresas Ativas</h2>
    <a href="company_create.php">+ Nova Empresa</a> | <a href="logout.php">Sair</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM companies WHERE deactivated = 0");
        $stmt->execute();
        while ($row = $stmt->fetch()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><a href="company_view.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></a></td>
                <td>
                    <a href="company_edit.php?id=<?= $row['id'] ?>">Editar</a>
                    <a href="company_deactivate.php?id=<?= $row['id'] ?>" onclick="return confirm('Desativar empresa? Os produtos ficarão ocultos.')">Desativar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>Empresas Desativadas</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome</th>
        </tr>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM companies WHERE deactivated = 1");
        $stmt->execute();
        while ($row = $stmt->fetch()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>

</html>