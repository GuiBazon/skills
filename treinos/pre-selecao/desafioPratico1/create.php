<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Prepara e insere no banco (seguro contra invasão)
    $sql = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
    $sql->bindValue(':nome', $nome);
    $sql->bindValue(':email', $email);
    $sql->bindValue(':senha', $senha);
    $sql->execute();

    header("Location: index.php"); // Volta para a lista
    exit;
}   
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Usuário</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="formulario">
        <h1>Adicionar Usuário</h1>
        <form method="POST">
            <label>Nome:</label>
            <input type="text" name="nome" required>
            <br><br>
            <label>Email:</label>
            <input type="email" name="email" required>
            <br><br>
            <label>Senha:</label>
            <input type="password" name="senha" minlength="6" required>
            <br><br>
          <button type="submit">Salvar</button>
        </form>
    </div>
</body>
</html>