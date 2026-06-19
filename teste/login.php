<?php

session_start();

if (isset($_POST["senha"])) {
    $senha = $_POST["senha"];
    if ($senha == "admin") {
        header("location: home.php");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form method="POST">
        <label for="senha">Senha:</label>
        <input type="text" name="senha">
        <button type="submit">Entrar</button>
    </form>
</body>
</html>