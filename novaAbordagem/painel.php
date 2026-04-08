<?php
session_start();

if (isset($_SESSION["usuario_logado"])) {
    
    echo $_SESSION["usuario_logado"];

} else {
    echo "Deu errado";
}


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
</head>
<body>
    <h1>Painel de chamados</h1>
    <h2></h2>

    <a href="novo_chamado.php">
        <button>Novo chamado</button>
    </a>
</body>
</html>
