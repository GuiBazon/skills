<?php
require 'init.php';
if (isset($_SESSION['user_id'])) redirect('animals');

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email=?');
    $stmt->execute([trim($_POST['email'])]);
    $user = $stmt->fetch();

    if ($user && $user['password'] === hash('sha256', $_POST['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        redirect('animals');
    } else {
        $erro = 'Email ou Senha de acesso inválidos';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Acessar</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
<div class="box">
    <h1>Acessar</h1>
    <?php if ($erro): ?><p class="erro"><?= e($erro) ?></p><?php endif; ?>
    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Senha</label>
        <div class="pass">
            <input type="password" id="p1" name="password" required>
            <button type="button" onclick="togglePass('p1')">Ver</button>
        </div>

        <button class="btn" type="submit">Acessar</button>
    </form>
</div>
<script src="assets/script.js"></script>
</body>
</html>
