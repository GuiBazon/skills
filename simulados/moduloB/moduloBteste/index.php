<?php
include 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['password'] === 'admin') {
        $_SESSION['admin'] = true;
        header("Location: admin_products.php");
    } else {
        echo "Senha errada!";
    }
}
?>
<form method="POST">
    <input type="password" name="password" placeholder="Senha">
    <button type="submit">Entrar</button>
</form>