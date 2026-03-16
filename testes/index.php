<?php

require_once 'conexao.php';

// var_dump($_POST);

// $sql = "INSERT INTO usuario (login, senha, nome) values ('usuariophp', '1234', 'usuario adicionado pelo php')";

// $mysqli->query($sql);


$query = "SELECT nome, senha FROM usuario ORDER BY id_usuario DESC";

$result = $mysqli->query($query);

/* fetch associative array */
while ($row = $result->fetch_assoc()) {
    printf("%s (%s)\n", $row["nome"], $row["senha"]);
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div>
        <form method="POST">
            <label for="login">Login:</label>
            <input type="text" name="login" required>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>