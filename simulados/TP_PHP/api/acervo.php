<?php
session_start();

if (!isset($_SESSION["usuario_logado"])) {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acervo</title>
</head>

<body>
    <h1>Acevo de livros</h1>
    <p>Bem vindo '<?php echo $_SESSION["usuario_logado"]; ?>'</p>

    <?php

    $conexao = new mysqli("localhost", "aluno", "senai@604", "tp_php");

    $sql = "SELECT * FROM livros";
    $resultado = $conexao->query($sql);

    while ($linha = $resultado->fetch_assoc()) {
        echo "<hr><p>";
        echo "Titulo: " . $linha["titulo"] . " <br>";
        echo "Quantidade" . $linha["quantidade_livros"] . " <br>";
        echo "Autor: " . $linha["autor"] . " <br>";
        echo "</p><hr>";
    }

    ?>

    <a href="registro.php">
        <button>Novo Usuario</button>
    </a>

    <a href="catalogo.php">
        <button>Novo Livro</button>
    </a>

</body>

</html>