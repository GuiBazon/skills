<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$id]);
$company = $stmt->fetch();

if (!$company) {
    die("Empresa não encontrada.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $owner_name = trim($_POST['owner_name']);
    $owner_phone = trim($_POST['owner_phone']);
    $owner_email = trim($_POST['owner_email']);
    $contact_name = trim($_POST['contact_name']);
    $contact_phone = trim($_POST['contact_phone']);
    $contact_email = trim($_POST['contact_email']);

    if (empty($name)) {
        $error = "Nome da empresa é obrigatório.";
    } else {
        $sql = "UPDATE companies SET name=?, address=?, phone=?, email=?, owner_name=?, owner_phone=?, owner_email=?, contact_name=?, contact_phone=?, contact_email=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $address, $phone, $email, $owner_name, $owner_phone, $owner_email, $contact_name, $contact_phone, $contact_email, $id]);
        $success = "Empresa atualizada!";
        $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
        $stmt->execute([$id]);
        $company = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Editar Empresa</title>
</head>

<body>
    <div>
        <a href="companies.php" >Empresas</a>
        <a href="products.php" >Produtos</a>
        <a href="gtin_verify.php" >Verificar GTIN</a>
    </div>
    <a href="company_view.php?id=<?= $id ?>">Voltar</a>
    <h2>Editar Empresa</h2>
    <?php if ($error) echo "<p>$error</p>"; ?>
    <?php if ($success) echo "<p>$success</p>"; ?>
    <form method="post">
        <label>Nome da empresa *:</label> <input type="text" name="name" value="<?= htmlspecialchars($company['name']) ?>" required><br>
        <label>Endereço:</label> <textarea name="address"><?= htmlspecialchars($company['address']) ?></textarea><br>
        <label>Telefone:</label> <input type="text" name="phone" value="<?= htmlspecialchars($company['phone']) ?>"><br>
        <label>Email:</label> <input type="email" name="email" value="<?= htmlspecialchars($company['email']) ?>"><br>
        <h3>Proprietário</h3>
        <label>Nome:</label> <input type="text" name="owner_name" value="<?= htmlspecialchars($company['owner_name']) ?>"><br>
        <label>Celular:</label> <input type="text" name="owner_phone" value="<?= htmlspecialchars($company['owner_phone']) ?>"><br>
        <label>Email:</label> <input type="email" name="owner_email" value="<?= htmlspecialchars($company['owner_email']) ?>"><br>
        <h3>Contato</h3>
        <label>Nome:</label> <input type="text" name="contact_name" value="<?= htmlspecialchars($company['contact_name']) ?>"><br>
        <label>Celular:</label> <input type="text" name="contact_phone" value="<?= htmlspecialchars($company['contact_phone']) ?>"><br>
        <label>Email:</label> <input type="email" name="contact_email" value="<?= htmlspecialchars($company['contact_email']) ?>"><br>
        <button type="submit">Salvar</button>
    </form>
</body>

</html>