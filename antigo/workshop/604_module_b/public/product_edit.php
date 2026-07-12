<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/database.php'; ?>

<?php
$gtin = $_GET['gtin'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM products WHERE gtin = ?");
$stmt->execute([$gtin]);
$product = $stmt->fetch();

if (!$product) {
    die("Produto não encontrado.");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        if ($product['hidden'] == 1) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE gtin = ?");
            $stmt->execute([$gtin]);
            header("Location: products.php");
            exit;
        } else {
            $error = "Produto visível não pode ser excluído. Oculte-o primeiro.";
        }
    } else {
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
        $hidden = isset($_POST['hidden']) ? 1 : 0;

        $new_gtin = trim($_POST['gtin']);
        if (!ctype_digit($new_gtin) || (strlen($new_gtin) != 13 && strlen($new_gtin) != 14)) {
            $error = "GTIN deve ter 13 ou 14 dígitos numéricos.";
        } else {
            $check = $pdo->prepare("SELECT id FROM products WHERE gtin = ? AND gtin != ?");
            $check->execute([$new_gtin, $product['gtin']]);
            if ($check->fetch()) {
                $error = "Este GTIN já está em uso.";
            } else {
                $image_path = $product['image_path'];
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $allowed = ['jpg', 'jpeg', 'png'];
                    if (in_array(strtolower($ext), $allowed)) {
                        $image_path = 'uploads/' . uniqid() . '.' . $ext;
                        move_uploaded_file($_FILES['image']['tmp_name'], '../assets/' . $image_path);
                    } else {
                        $error = "Formato de imagem inválido.";
                    }
                }
                if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
                    $image_path = '';
                }

                if (!$error) {
                    $sql = "UPDATE products SET gtin=?, name_en=?, name_fr=?, description_en=?, description_fr=?, brand=?, country_of_origin=?, gross_weight=?, net_weight=?, weight_unit=?, image_path=?, hidden=?, company_id=? WHERE gtin=?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$new_gtin, $name_en, $name_fr, $description_en, $description_fr, $brand, $country, $gross, $net, $unit, $image_path, $hidden, $company_id, $product['gtin']]);
                    $success = "Produto atualizado!";
                    $stmt = $pdo->prepare("SELECT * FROM products WHERE gtin = ?");
                    $stmt->execute([$new_gtin]);
                    $product = $stmt->fetch();
                    $gtin = $new_gtin;
                }
            }
        }
    }
}

$companies = $pdo->query("SELECT id, name FROM companies WHERE deactivated = 0")->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Editar Produto</title>
</head>

<body>
    <div>
        <a href="companies.php" >Empresas</a>
        <a href="products.php" >Produtos</a>
        <a href="gtin_verify.php" >Verificar GTIN</a>
    </div>
    <a href="products.php">Voltar</a>
    <h2>Editar Produto</h2>
    <?php if ($error) echo "<p>$error</p>"; ?>
    <?php if ($success) echo "<p>$success</p>"; ?>

    <form method="post" enctype="multipart/form-data">
        <label>GTIN (13/14 dígitos):</label> <input type="text" name="gtin" value="<?= htmlspecialchars($product['gtin']) ?>" required><br>
        <label>Nome (EN):</label> <input type="text" name="name_en" value="<?= htmlspecialchars($product['name_en']) ?>" required><br>
        <label>Nome (FR):</label> <input type="text" name="name_fr" value="<?= htmlspecialchars($product['name_fr']) ?>" required><br>
        <label>Descrição (EN):</label> <textarea name="description_en"><?= htmlspecialchars($product['description_en']) ?></textarea><br>
        <label>Descrição (FR):</label> <textarea name="description_fr"><?= htmlspecialchars($product['description_fr']) ?></textarea><br>
        <label>Marca:</label> <input type="text" name="brand" value="<?= htmlspecialchars($product['brand']) ?>"><br>
        <label>País de origem:</label> <input type="text" name="country_of_origin" value="<?= htmlspecialchars($product['country_of_origin']) ?>"><br>
        <label>Peso bruto:</label> <input type="number" step="any" name="gross_weight" value="<?= $product['gross_weight'] ?>"><br>
        <label>Peso líquido:</label> <input type="number" step="any" name="net_weight" value="<?= $product['net_weight'] ?>"><br>
        <label>Unidade:</label> <input type="text" name="weight_unit" value="<?= htmlspecialchars($product['weight_unit']) ?>"><br>
        <label>Empresa:</label>
        <select name="company_id">
            <?php foreach ($companies as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $c['id'] == $product['company_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select><br>
        <label>Oculto:</label> <input type="checkbox" name="hidden" value="1" <?= $product['hidden'] ? 'checked' : '' ?>><br>
        <label>Imagem atual:</label>
        <?php if ($product['image_path']): ?>
            <img src="../assets/<?= $product['image_path'] ?>" width="100"><br>
            <label><input type="checkbox" name="remove_image" value="1"> Remover imagem</label><br>
        <?php endif; ?>
        <label>Nova imagem (opcional):</label> <input type="file" name="image" accept="image/jpeg,image/png"><br>
        <button type="submit">Salvar alterações</button>
    </form>

    <hr>
    <h3>Excluir</h3>
    <form method="post" onsubmit="return confirm('Exclusão permanente! Tem certeza?')">
        <button type="submit" name="delete" value="1" <?= $product['hidden'] ? '' : 'disabled' ?>>
            Excluir produto permanentemente
        </button>
        <?php if (!$product['hidden']): ?>
            <p>Produto visível não pode ser excluído. Oculte-o primeiro.</p>
        <?php endif; ?>
    </form>
</body>

</html>