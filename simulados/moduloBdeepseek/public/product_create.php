<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gtin = trim($_POST['gtin']);
    $name_en = trim($_POST['name_en']);
    $name_fr = trim($_POST['name_fr']);
    $description_en = trim($_POST['description_en']);
    $description_fr = trim($_POST['description_fr']);
    $brand = trim($_POST['brand']);
    $country = trim($_POST['country_of_origin']);
    $gross = $_POST['gross_weight'];
    $net = $_POST['net_weight'];
    $unit = trim($_POST['weight_unit']);
    $company_id = $_POST['company_id'];

    // VALIDAÇÃO SERVIDOR: GTIN 13 ou 14 dígitos
    if (!ctype_digit($gtin) || (strlen($gtin) != 13 && strlen($gtin) != 14)) {
        $error = "GTIN deve conter 13 ou 14 dígitos numéricos.";
    } else {
        // Upload da imagem
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png'];
            if (in_array(strtolower($ext), $allowed)) {
                $image_path = 'uploads/' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../assets/' . $image_path);
            } else {
                $error = "Apenas JPG/PNG são permitidos.";
            }
        }
        if (!$error) {
            $sql = "INSERT INTO products (gtin, name_en, name_fr, description_en, description_fr, brand, country_of_origin, gross_weight, net_weight, weight_unit, image_path, company_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$gtin, $name_en, $name_fr, $description_en, $description_fr, $brand, $country, $gross, $net, $unit, $image_path, $company_id]);
            $success = "Produto criado com sucesso!";
        }
    }
}

// Buscar empresas para o select
$companies = $pdo->query("SELECT id, name FROM companies WHERE deactivated = 0")->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Novo Produto</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div style="background:#2c3e50; padding:10px; margin-bottom:20px;">
        <a href="home.php" style="color:white; margin-right:15px;">🏠 Home</a>
        <a href="companies.php" style="color:white; margin-right:15px;">🏢 Empresas</a>
        <a href="products.php" style="color:white; margin-right:15px;">📦 Produtos</a>
        <a href="gtin_verify.php" style="color:white; margin-right:15px;">🔍 Verificar GTIN</a>
        <a href="logout.php" style="color:white;">🚪 Sair</a>
    </div>
    <h2>Criar Produto</h2>
    <?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green'>$success</p>"; ?>
    <form method="post" enctype="multipart/form-data">
        <label>GTIN (13 ou 14 dígitos):</label> <input type="text" name="gtin" required><br>
        <label>Nome (EN):</label> <input type="text" name="name_en" required><br>
        <label>Nome (FR):</label> <input type="text" name="name_fr" required><br>
        <label>Descrição (EN):</label> <textarea name="description_en"></textarea><br>
        <label>Descrição (FR):</label> <textarea name="description_fr"></textarea><br>
        <label>Marca:</label> <input type="text" name="brand"><br>
        <label>País de origem:</label> <input type="text" name="country_of_origin"><br>
        <label>Peso bruto:</label> <input type="number" step="any" name="gross_weight"><br>
        <label>Peso líquido:</label> <input type="number" step="any" name="net_weight"><br>
        <label>Unidade:</label> <input type="text" name="weight_unit"><br>
        <label>Empresa:</label>
        <select name="company_id">
            <?php foreach ($companies as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select><br>
        <label>Imagem:</label> <input type="file" name="image" accept="image/jpeg,image/png"><br>
        <button type="submit">Salvar</button>
    </form>
</body>

</html>