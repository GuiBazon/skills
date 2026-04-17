<?php

session_start();

if (isset($nome, $senha)) {
    $nome = $_POST["nome"];
    $senha = $_POST["senha"];

    if ($nome === null || $senha === null) {
        echo "Nome ou Senha nulos";
    } else {
        var_dump($nome, $senha);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sei la</title>
</head>

<body>
    <form method="POST">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" required>

        <label for="senha">Senha:</label>
        <input type="pass" name="senha" required>

        <button type="submit">Entrar</button>
    </form>
</body>

</html>