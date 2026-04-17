<?php

session_start();

if (!isset($_SESSION["usuario_logado"])) {
    header("location: index.php");
    exit;
};

if (isset($_POST["titulo"], $_POST["quantidade_livros"], $_POST["autor"])) {
    $titulo = $_POST["titulo"];
    $quantidade_livros = $_POST["quantidade_livros"];
    $autor = $_POST["autor"];

    $conexao = new mysqli("localhost", "aluno", "senai@604", "tp_php");
    $sql = "INSERT INTO livros (titulo, quantidade_livros, autor) values ('$titulo', '$quantidade_livros', '$autor')";
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
        <label for="titulo">Titulo:</label>
        <input type="text" name="titulo">

        <label for="quantidade_livros">Quantidade:</label>
        <input type="text" name="quantidade_livros">

        <label for="autor">Autor:</label>
        <input type="text" name="autor">

        <button type="submit">Enviar</button>

        <br> <br>
        <a href="acervo.php">Voltar para o acervo</a>
    </form>
</body>

</html>