<?php
require 'config.php';

// Busca os usuários no banco
$sql = $pdo->query("SELECT * FROM usuarios");
$usuarios = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuários</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div>
        <h1>Usuários</h1>
        <a href="create.php">Adicionar Novo Usuário</a>
        <br><br>

        <table width="100%">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Senha</th>
                <th>Ações</th>
            </tr>
            <?php foreach($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['id']; ?></td>
                <td><?php echo $usuario['nome']; ?></td>
                <td><?php echo $usuario['email']; ?></td>
                <td><?php echo $usuario['senha']; ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $usuario['id']; ?>">Editar</a>
                    <a href="delete.php?id=<?php echo $usuario['id']; ?>" onclick="return confirm('tem certeza que dezeja excluir usuario: <?php echo $usuario['nome']; ?>?');">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>