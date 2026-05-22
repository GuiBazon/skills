<?php

session_start();

if (isset($_POST["nome"], $_POST["senha"])) {
    $nome = $_POST["nome"];
    $senha = $_POST["senha"];

    var_dump($nome, $senha);
}

?>



<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index</title>
</head>

<body>
    <form method="POST">
        <label for="nome">Nome</label>
        <input type="text" name="nome" required>
        <label for="senha">Senha</label>
        <input type="pass" name="senha" required>
        <button type="submit">Botao</button>
    </form>
</body>

</html>