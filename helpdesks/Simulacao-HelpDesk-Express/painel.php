<?php
session_start();
require_once 'conexao.php';

// Bloqueia o acesso se não estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Busca os chamados ordenados do mais recente para o mais antigo (DESC)
$sql = "SELECT * FROM Chamado ORDER BY data_registro DESC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Chamados</title>
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
        <h3>Lista de Chamados</h3>
        <br>
        <a href="novo_chamado.php" class="btn-link">+ Novo Chamado</a>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Solicitante</th>
                        <th>Equipamento</th>
                        <th>Descrição</th>
                        <th>Data Registro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($chamado = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $chamado['id']; ?></td>
                            <td><?php echo htmlspecialchars($chamado['solicitante']); ?></td>
                            <td><?php echo htmlspecialchars($chamado['equipamento']); ?></td>
                            <td><?php echo htmlspecialchars($chamado['descricao']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($chamado['data_registro'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($resultado->num_rows == 0): ?>
                        <tr><td colspan="5" style="text-align:center;">Nenhum chamado registrado ainda.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>