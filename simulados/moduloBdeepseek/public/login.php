<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($password === 'admin') {
        $_SESSION['logged_in'] = true;
        header('Location: home.php');
        exit;
    } else {
        $error = "Senha incorreta.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post">
        <input type="password" name="password" placeholder="Senha" required>
        <button type="submit">Entrar</button>
    </form>
</body>

</html>