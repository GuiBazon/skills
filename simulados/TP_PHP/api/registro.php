<?php

session_start();

if (!isset($_SESSION["usuario_logado"])) {
    header("location: index.php");
    exit;
};

if (isset($_POST["nome"], $_POST["email"], $_POST["username"], $_POST["senha"])) {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $senha = $_POST["senha"];

    $conexao = new mysqli("localhost", "aluno", "senai@604", "tp_php");
    $sql = "INSERT INTO usuarios (nome, email, username, senha) values ('$nome', '$email', '$username', '$senha')";
    $resultado = $conexao->query($sql);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Usuario</title>
</head>

<body>
    <h1>Registro de usuario</h1>
    <form method="POST">
        <label for="nome">Nome:</label>
        <input type="text" name="nome">

        <label for="email">Email:</label>
        <input type="text" name="email">

        <label for="username">Username:</label>
        <input type="text" name="username">

        <label for="senha">Senha:</label>
        <input type="text" name="senha">

        <button type="submit">Enviar</button>

        <br> <br>
        <a href="acervo.php">Voltar para o acervo</a>
    </form>
</body>

</html>