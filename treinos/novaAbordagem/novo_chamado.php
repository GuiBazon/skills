<?php

session_start();

if (!isset($_SESSION["usuario_logado"])) {
    header("location: index.php");
    exit;
};

if (isset($_POST["solicitante"], $_POST["email"], $_POST["equipamento"], $_POST["descricao"])) {
    $solicitante = $_POST["solicitante"];
    $email = $_POST["email"];
    $equipamento = $_POST["equipamento"];
    $descricao = $_POST["descricao"];

    $conexao = new mysqli("localhost", "aluno", "senai@604", "helpdesk_db");
    $sql = "INSERT INTO chamados (solicitante, email, equipamento, descricao) values ('$solicitante', '$email', '$equipamento', '$descricao')";
    $resultado = $conexao->query($sql);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Chamado</title>
</head>

<body>
    <h1>Abrir novo chamado</h1>
    <form method="POST">
        <label for="solicitante">Solicitante:</label>
        <input type="text" name="solicitante">

        <label for="email">Email:</label>
        <input type="text" name="email">

        <label for="equipamento">Equipamento:</label>
        <input type="text" name="equipamento">

        <label for="descricao">Descricao:</label>
        <input type="text" name="descricao">

        <button type="submit">Enviar</button>

        <br> <br>
        <a href="painel.php">Voltar para o painel</a>
    </form>
</body>

</html>