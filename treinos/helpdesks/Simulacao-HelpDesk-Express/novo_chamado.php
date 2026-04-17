<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $solicitante = $_POST['solicitante'];
    $email = $_POST['email'];
    $equipamento = $_POST['equipamento'];
    $descricao = $_POST['descricao'];

    $stmt = $conn->prepare("INSERT INTO Chamado (solicitante, email, equipamento, descricao) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $solicitante, $email, $equipamento, $descricao);
    
    if ($stmt->execute()) {
        header("Location: painel.php"); // Volta pro painel após cadastrar
        exit;
    } else {
        $erro = "Erro ao cadastrar chamado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Chamado</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h2>HelpDesk Express</h2>
        <div>
            <span>Bem-vindo, <strong><?php echo $_SESSION['usuario_nome']; ?></strong></span>
            <a href="logout.php" style="margin-left: 15px;">Sair</a>
        </div>
    </header>

    <div class="container">
        <h3>Abrir Novo Chamado</h3>
        <br>
        <?php if(isset($erro)): ?> <p class="erro"><?php echo $erro; ?></p> <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Solicitante:</label>
                <input type="text" name="solicitante" required>
            </div>
            <div class="form-group">
                <label>E-mail:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Equipamento:</label>
                <input type="text" name="equipamento" required>
            </div>
            <div class="form-group">
                <label>Descrição do Problema:</label>
                <textarea name="descricao" rows="5" required></textarea>
            </div>
            <button type="submit">Salvar Chamado</button>
            <a href="painel.php" style="display:block; text-align:center; margin-top:15px; text-decoration:none; color:#0056b3;">Cancelar e Voltar</a>
        </form>
    </div>
</body>
</html>