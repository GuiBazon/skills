<?php
session_start();
require_once 'conexao.php'; 

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, nome, senha FROM Usuario WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if ($senha === $usuario['senha']) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header("Location: painel.php"); 
            exit;
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        $erro = "Usuário não encontrado!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HelpDesk Express</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container login-container">
        <h2 style="text-align: center; margin-bottom: 20px;">HelpDesk Express</h2>
        <?php if ($erro): ?> <p class="erro"><?php echo $erro; ?></p> <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Login:</label>
                <input type="text" name="login" required>
            </div>
            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="senha" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>

</html>