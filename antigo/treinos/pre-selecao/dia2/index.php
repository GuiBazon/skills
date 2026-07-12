<?php

$host = 'localhost';
$dbname = 'crud_basico'; 
$user = 'root'; 
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

$id = '';
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $id = $_POST['id'];

    if ($id) {
        $sql = $pdo->prepare("UPDATE usuarios SET nome = :n, email = :e WHERE id = :id");
        $sql->bindValue(':id', $id);
    } else {
        $sql = $pdo->prepare("INSERT INTO usuarios (nome, email) VALUES (:n, :e)");
    }

    $sql->bindValue(':n', $nome);
    $sql->bindValue(':e', $email);
    $sql->execute(); 

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $pdo->prepare("DELETE FROM usuarios WHERE id = :id")->execute([':id' => $id]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $sql->execute([':id' => $id]);

    $usuario = $sql->fetch(PDO::FETCH_ASSOC);

    $nome = $usuario['nome'];
    $email = $usuario['email'];
}

$sql = $pdo->query("SELECT * FROM usuarios");
$lista = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Sistema de Usuários</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            background: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>

    <h1><?php echo $id ? 'Editar Usuário' : 'Cadastrar Novo Usuário'; ?></h1>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo $nome; ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $email; ?>" required>

        <button type="submit"><?php echo $id ? 'Atualizar' : 'Salvar'; ?></button>

        <?php if ($id): ?>
            <a href="index.php">Cancelar</a>
        <?php endif; ?>
    </form>

    <hr>

    <h2>Usuários Cadastrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Ações</th>
        </tr>

        <?php foreach ($lista as $u): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo $u['nome']; ?></td>
                <td><?php echo $u['email']; ?></td>
                <td>
                    <a href="?editar=<?php echo $u['id']; ?>">Editar</a>

                    <a href="?excluir=<?php echo $u['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>