<?php

session_start();

if (isset($_POST['login'])) {

    $login = $_POST['login'];
    $senha = $_POST['senha'];

    var_dump($login, $senha);



    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $mysqli = new mysqli('localhost', 'aluno', 'senai@604', 'bazon_teste');

    $mysqli->set_charset('utf8mb4');

    printf("Success... %s\n", $mysqli->host_info);



    $mysqli->query("INSERT INTO usuario(login, senha) values ($login, $senha)");
    printf("Insert feito na tabela usuario.\n");

    $result = $mysqli->query("SELECT * FROM usuario");
    printf("Select returned %d rows.\n", $result->num_rows);

    $result = $mysqli->query("SELECT * FROM usuario", MYSQLI_USE_RESULT);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="painel.php" method="POST">
        <label for="login">Login</label>
        <input type="text" name="login">
        <label for="senha">Senha</label>
        <input type="pass" name="senha">
        <button type="submit">Entrar</button>
    </form>
</body>

</html>