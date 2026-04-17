<?php
require 'config.php';

$id = $_GET['id']; // Pega o ID da URL

// Lógica de Atualização (Quando clica em Salvar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    $sql = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id");
    $sql->bindValue(':nome', $nome);
    $sql->bindValue(':email', $email);
    $sql->bindValue(':id', $id);
    $sql->execute();

    header("Location: index.php");
    exit;
}

// Lógica de Leitura (Para preencher o formulário)
$sql = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$sql->bindValue(':id', $id);
$sql->execute();
$usuario = $sql->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head><title>Editar Usuário</title></head>
<body>
    <h1>Editar Usuário</h1>
    <form method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo $usuario['nome']; ?>" required>
        <br><br>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required>
        <br><br>
        <button type="submit">Atualizar</button>
    </form>
</body>
</html>