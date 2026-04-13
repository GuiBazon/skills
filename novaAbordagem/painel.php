<?php
session_start();

if (!isset($_SESSION["usuario_logado"])) {
    // echo "Deu errado";
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel HelpDesk</title>
</head>

<body>
    <h1>Painel de chamados</h1>
    <p>Bem vindo '<?php echo $_SESSION["usuario_logado"]; ?>'</p>

    <?php

    $conexao = new mysqli("localhost", "aluno", "senai@604", "helpdesk_db");

    $sql = "SELECT * FROM chamados";
    $resultado = $conexao->query($sql);

    while ($linha = $resultado->fetch_assoc()) {
        echo "<hr><p>";
        echo "Solicitante: " . $linha["solicitante"] . " <br>";
        echo "Email: " . $linha["email"] . " <br>";
        echo "Equipamento: " . $linha["equipamento"] . " <br>";
        echo "Problema: " . $linha["descricao"] . " <br>";
        echo "Data_registro: " . $linha["data_registro"] . " <br>";
        echo "</p><hr>";
    }

    ?>

    <a href="novo_chamado.php">
        <button>Novo chamado</button>
    </a>
</body>

</html>