<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    redirect('animals');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user['password'] === hash('sha256', $pass)) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['last_activity'] = time();
        redirect('animals');
    } else {
        $error = 'Email ou Senha de acesso inválidos';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Acessar - ZooSystem Management</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="center-box">
    <h1>Acessar</h1>
    <?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?>

    <form method="POST" novalidate>
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Senha</label>
        <div class="pass-wrap">
            <input type="password" id="password" name="password" required>
            <button type="button" onclick="togglePass('password', this)">Mostrar</button>
        </div>

        <button class="btn" type="submit">Acessar</button>
    </form>
    <p><a href="register">Cadastrar</a></p>
</div>
<script src="assets/script.js"></script>
</body>
</html>
