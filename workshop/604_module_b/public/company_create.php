<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
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
        $sql = "INSERT INTO companies (name, address, phone, email, owner_name, owner_phone, owner_email, contact_name, contact_phone, contact_email)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $address, $phone, $email, $owner_name, $owner_phone, $owner_email, $contact_name, $contact_phone, $contact_email]);
        $success = "Empresa criada com sucesso!";
        $_POST = [];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Nova Empresa</title>
</head>

<body>
    <div>
        <a href="companies.php" >Empresas</a>
        <a href="products.php" >Produtos</a>
        <a href="gtin_verify.php" >Verificar GTIN</a>
    </div>
    <a href="companies.php">Voltar</a>
    <h2>Nova Empresa</h2>
    <?php if ($error) echo "<p>$error</p>"; ?>
    <?php if ($success) echo "<p>$success</p>"; ?>
    <form method="post">
        <label>Nome da empresa *:</label> <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required><br>
        <label>Endereço:</label> <textarea name="address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea><br>
        <label>Telefone:</label> <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"><br>
        <label>Email:</label> <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"><br>
        <h3>Proprietário</h3>
        <label>Nome:</label> <input type="text" name="owner_name" value="<?= htmlspecialchars($_POST['owner_name'] ?? '') ?>"><br>
        <label>Celular:</label> <input type="text" name="owner_phone" value="<?= htmlspecialchars($_POST['owner_phone'] ?? '') ?>"><br>
        <label>Email:</label> <input type="email" name="owner_email" value="<?= htmlspecialchars($_POST['owner_email'] ?? '') ?>"><br>
        <h3>Contato</h3>
        <label>Nome:</label> <input type="text" name="contact_name" value="<?= htmlspecialchars($_POST['contact_name'] ?? '') ?>"><br>
        <label>Celular:</label> <input type="text" name="contact_phone" value="<?= htmlspecialchars($_POST['contact_phone'] ?? '') ?>"><br>
        <label>Email:</label> <input type="email" name="contact_email" value="<?= htmlspecialchars($_POST['contact_email'] ?? '') ?>"><br>
        <button type="submit">Salvar</button>
    </form>
</body>

</html>