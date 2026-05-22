<?php
session_start();
$logged = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
?>
<!DOCTYPE html>
<html>

<head>
    <title>Home - Módulo B</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php if ($logged): ?>
        <?php require_once '../includes/menu.php'; ?>
        <h2>Bem-vindo, Admin</h2>
        <p>Use o menu acima para gerenciar empresas, produtos, etc.</p>
    <?php else: ?>
        <p>Você não está logado. <a href="login.php">Faça login</a></p>
    <?php endif; ?>
</body>

</html>