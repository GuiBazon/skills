<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $pass === '' || $confirm === '') {
        $errors[] = 'Todos os campos são obrigatórios.';
    }
    if (mb_strlen($name) > 100) {
        $errors[] = 'O nome deve ter no máximo 100 caracteres.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 50) {
        $errors[] = 'Informe um e-mail válido com no máximo 50 caracteres.';
    }
    if (mb_strlen($pass) > 16) {
        $errors[] = 'A senha deve ter no máximo 16 caracteres.';
    }
    if ($pass !== $confirm) {
        $errors[] = 'As senhas são divergentes';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Esse e-mail já está cadastrado!';
        }
    }

    if (empty($errors)) {
        $hash = hash('sha256', $pass);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $hash]);
        redirect('login');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastrar - ZooSystem Management</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="center-box">
    <h1>Cadastrar</h1>

    <?php foreach ($errors as $err): ?>
        <p class="error"><?= e($err) ?></p>
    <?php endforeach; ?>

    <form method="POST" novalidate>
        <label>Nome</label>
        <input type="text" name="name" maxlength="100" value="<?= e($_POST['name'] ?? '') ?>" required>

        <label>Email</label>
        <input type="email" name="email" maxlength="50" value="<?= e($_POST['email'] ?? '') ?>" required>

        <label>Senha</label>
        <div class="pass-wrap">
            <input type="password" id="password" name="password" maxlength="16" required>
            <button type="button" onclick="togglePass('password', this)">Mostrar</button>
        </div>

        <label>Confirmar Senha</label>
        <div class="pass-wrap">
            <input type="password" id="confirm_password" name="confirm_password" maxlength="16" required>
            <button type="button" onclick="togglePass('confirm_password', this)">Mostrar</button>
        </div>

        <button class="btn" type="submit">Cadastrar</button>
    </form>
    <p><a href="login">Já tem conta? Acessar</a></p>
</div>
<script src="assets/script.js"></script>
</body>
</html>
