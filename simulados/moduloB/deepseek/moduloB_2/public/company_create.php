<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/db.php'; ?>
<?php if ($_POST) {
    $stmt = $pdo->prepare('INSERT INTO companies (name) VALUES (?)');
    $stmt->execute([$_POST['name']]);
    header('Location: companies.php');
    exit;
} ?>
<form method="post">
    <input name="name" placeholder="Nome da empresa">
    <button>Salvar</button>
</form>