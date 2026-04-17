<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    // Prepara e insere no banco (seguro contra invasão)
    $sql = $pdo->prepare("INSERT INTO usuarios (nome, email) VALUES (:nome, :email)");
    $sql->bindValue(':nome', $nome);
    $sql->bindValue(':email', $email);
    $sql->execute();

    header("Location: index.php"); // Volta para a lista
    exit;
}
?>

<!DOCTYPE html>
<html>
<head><title>Adicionar Usuário</title></head>
<body>
    <h1>Adicionar Usuário</h1>
    <form method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <br><br>
        <label>Email:</label>
        <input type="email" name="email" required>
        <br><br>
        <button type="submit">Salvar</button>
    </form>
</body>
</html>