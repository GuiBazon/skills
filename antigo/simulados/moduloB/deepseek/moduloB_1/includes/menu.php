<?php
// Se não estiver logado, não exibe menu (opcional)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    return; // Não exibe menu para não logados
}
?>
<nav style="background:#2c3e50; padding:10px; margin-bottom:20px;">
    <a href="home.php" style="color:white; margin-right:15px;">🏠 Home</a>
    <a href="companies.php" style="color:white; margin-right:15px;">🏢 Empresas</a>
    <a href="products.php" style="color:white; margin-right:15px;">📦 Produtos</a>
    <a href="gtin_verify.php" style="color:white; margin-right:15px;">🔍 Verificar GTIN</a>
    <a href="logout.php" style="color:white;">🚪 Sair</a>
</nav>