<?php

$equipamento = $_POST["equipamento"];

var_dump($equipamento);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="POST">
        <label for="equipamento">equipamento:</label>
        <input type="text" name="equipamento" required>
        <button type="submit">Entrar</button>
    </form>
</body>

</html>