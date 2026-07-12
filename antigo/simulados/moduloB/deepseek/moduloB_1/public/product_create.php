<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

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

    // 1. Validação do GTIN (13 ou 14 dígitos)
    if (!ctype_digit($gtin) || (strlen($gtin) != 13 && strlen($gtin) != 14)) {
        $error = "GTIN deve conter 13 ou 14 dígitos numéricos.";
    }
    // 2. Validação de campos obrigatórios
    elseif (empty($name_en) || empty($name_fr)) {
        $error = "Nome em inglês e francês são obrigatórios.";
    } else {
        // 3. Verificar se GTIN já existe (duplicidade)
        $check = $pdo->prepare("SELECT id FROM products WHERE gtin = ?");
        $check->execute([$gtin]);
        if ($check->fetch()) {
            $error = "Este GTIN já está cadastrado. Use um GTIN diferente.";
        } else {
            // 4. Upload da imagem
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png'];
                if (in_array($ext, $allowed)) {
                    $image_path = 'uploads/' . uniqid() . '.' . $ext;
                    $upload_dir = '../assets/' . $image_path;
                    // Garante que a pasta assets/uploads existe
                    if (!is_dir('../assets/uploads')) {
                        mkdir('../assets/uploads', 0777, true);
                    }
                    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir);
                } else {
                    $error = "Formato de imagem inválido. Use JPG ou PNG.";
                }
            }

            if (!$error) {
                try {
                    $sql = "INSERT INTO products (gtin, name_en, name_fr, description_en, description_fr, brand, country_of_origin, gross_weight, net_weight, weight_unit, image_path, company_id, hidden)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$gtin, $name_en, $name_fr, $description_en, $description_fr, $brand, $country, $gross, $net, $unit, $image_path, $company_id]);
                    $success = "Produto criado com sucesso!";
                    // Limpar formulário (opcional)
                    $_POST = [];
                } catch (PDOException $e) {
                    $error = "Erro ao salvar: " . $e->getMessage();
                }
            }
        }
    }
}

// Buscar empresas ativas para o select
$companies = $pdo->query("SELECT id, name FROM companies WHERE deactivated = 0 ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Novo Produto</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        label {
            display: inline-block;
            width: 180px;
            margin-top: 10px;
        }

        input,
        textarea,
        select {
            width: 300px;
            padding: 5px;
        }

        button {
            margin-top: 20px;
            padding: 8px 20px;
        }
    </style>
</head>

<body>
    <a href="products.php">← Voltar para lista de produtos</a> | <a href="logout.php">Sair</a>
    <h2>Criar Novo Produto</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>GTIN (13 ou 14 dígitos):</label>
        <input type="text" name="gtin" value="<?= htmlspecialchars($_POST['gtin'] ?? '') ?>" required><br>

        <label>Nome (EN):</label>
        <input type="text" name="name_en" value="<?= htmlspecialchars($_POST['name_en'] ?? '') ?>" required><br>

        <label>Nome (FR):</label>
        <input type="text" name="name_fr" value="<?= htmlspecialchars($_POST['name_fr'] ?? '') ?>" required><br>

        <label>Descrição (EN):</label>
        <textarea name="description_en"><?= htmlspecialchars($_POST['description_en'] ?? '') ?></textarea><br>

        <label>Descrição (FR):</label>
        <textarea name="description_fr"><?= htmlspecialchars($_POST['description_fr'] ?? '') ?></textarea><br>

        <label>Marca:</label>
        <input type="text" name="brand" value="<?= htmlspecialchars($_POST['brand'] ?? '') ?>"><br>

        <label>País de origem:</label>
        <input type="text" name="country_of_origin" value="<?= htmlspecialchars($_POST['country_of_origin'] ?? '') ?>"><br>

        <label>Peso bruto:</label>
        <input type="number" step="any" name="gross_weight" value="<?= htmlspecialchars($_POST['gross_weight'] ?? '') ?>"><br>

        <label>Peso líquido:</label>
        <input type="number" step="any" name="net_weight" value="<?= htmlspecialchars($_POST['net_weight'] ?? '') ?>"><br>

        <label>Unidade (kg, L, g, etc):</label>
        <input type="text" name="weight_unit" value="<?= htmlspecialchars($_POST['weight_unit'] ?? '') ?>"><br>

        <label>Empresa:</label>
        <select name="company_id" required>
            <option value="">Selecione uma empresa</option>
            <?php foreach ($companies as $c): ?>
                <option value="<?= $c['id'] ?>" <?= (isset($_POST['company_id']) && $_POST['company_id'] == $c['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Imagem (JPG/PNG):</label>
        <input type="file" name="image" accept="image/jpeg,image/png"><br>

        <button type="submit">Salvar Produto</button>
    </form>
</body>

</html>