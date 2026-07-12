<?php
require 'init.php';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $conf  = $_POST['confirm_password'];

    if (!$name || !$email || !$pass || !$conf) {
        $erro = 'Todos os campos são obrigatórios.';
    } elseif (mb_strlen($name) > 100) {
        $erro = 'Nome deve ter no máximo 100 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 50) {
        $erro = 'Email inválido.';
    } elseif (mb_strlen($pass) > 16) {
        $erro = 'Senha deve ter no máximo 16 caracteres.';
    } elseif ($pass !== $conf) {
        $erro = 'As senhas são divergentes';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE email=?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $erro = 'Esse e-mail já está cadastrado!';
        } else {
            $stmt = $pdo->prepare('INSERT INTO users (name,email,password) VALUES (?,?,?)');
            $stmt->execute([$name, $email, hash('sha256', $pass)]);
            redirect('login');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Cadastrar</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
<div class="box">
    <h1>Cadastrar</h1>
    <?php if ($erro): ?><p class="erro"><?= e($erro) ?></p><?php endif; ?>
    <form method="POST">
        <label>Nome</label>
        <input name="name" maxlength="100" required>

        <label>Email</label>
        <input type="email" name="email" maxlength="50" required>

        <label>Senha</label>
        <div class="pass">
            <input type="password" id="p1" name="password" maxlength="16" required>
            <button type="button" onclick="togglePass('p1')">Ver</button>
        </div>

        <label>Confirmar Senha</label>
        <div class="pass">
            <input type="password" id="p2" name="confirm_password" maxlength="16" required>
            <button type="button" onclick="togglePass('p2')">Ver</button>
        </div>

        <button class="btn" type="submit">Cadastrar</button>
    </form>
</div>
<script src="assets/script.js"></script>
</body>
</html>
