<?php

if (isset($_POST["username"], $_POST["senha"])){
    $username = $_POST["username"];
    $senha = $_POST["senha"];

    $conexao = new mysqli("localhost", "aluno", "senai@604", "helpdesk_db");

    $sql = "SELECT * FROM usuarios WHERE login = '$username' AND senha = '$senha'";
    $resultado = $conexao->query($sql);

    if ($resultado->num_rows > 0) {
        echo "O usuário existe.";
    } else {
        echo "Login ou senha incorretos.";
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
</head>
<body>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username">
        <label for="senha">Senha:</label>
        <input type="password" name="senha">
        <button type="submit">Submit</button>
    </form>
</body>
</html>