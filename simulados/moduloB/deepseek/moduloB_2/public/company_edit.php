<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/db.php'; ?>
<?php
$id = $_GET['id'];
if ($_POST) {
    $stmt = $pdo->prepare('UPDATE companies SET name=? WHERE id=?');
    $stmt->execute([$_POST['name'], $id]);
    header('Location: companies.php');
    exit;
}
$c = $pdo->prepare('SELECT * FROM companies WHERE id=?');
$c->execute([$id]);
$company = $c->fetch();
?>
<form method="post">
    <input name="name" value="<?= $company['name'] ?>">
    <button>Salvar</button>
</form>