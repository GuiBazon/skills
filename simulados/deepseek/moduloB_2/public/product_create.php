<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/db.php'; ?>
<?php
$error = '';
if ($_POST) {
    $gtin = $_POST['gtin'];
    // validação GTIN: 13 ou 14 dígitos
    if (!preg_match('/^\d{13,14}$/', $gtin)) {
        $error = 'GTIN deve ter 13 ou 14 dígitos';
    } else {
        // verifica duplicidade
        $check = $pdo->prepare('SELECT id FROM products WHERE gtin=?');
        $check->execute([$gtin]);
        if ($check->fetch()) {
            $error = 'GTIN já existe';
        } else {
            $image_path = '';
            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                if (in_array(strtolower($ext), ['jpg', 'png'])) {
                    $image_path = 'uploads/' . uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], '../' . $image_path);
                } else {
                    $error = 'Formato inválido (use JPG/PNG)';
                }
            }
            if (!$error) {
                $stmt = $pdo->prepare('INSERT INTO products (gtin, name_en, name_fr, description_en, description_fr, image_path, company_id) VALUES (?,?,?,?,?,?,?)');
                $stmt->execute([$gtin, $_POST['name_en'], $_POST['name_fr'], $_POST['description_en'], $_POST['description_fr'], $image_path, $_POST['company_id']]);
                header('Location: products.php');
                exit;
            }
        }
    }
}
$companies = $pdo->query('SELECT id, name FROM companies WHERE deactivated=0')->fetchAll();
?>
<form method="post" enctype="multipart/form-data">
    <?php if ($error) echo "<p>$error</p>"; ?>
    <input name="gtin" placeholder="GTIN (13/14 dígitos)" required>
    <input name="name_en" placeholder="Nome EN" required>
    <input name="name_fr" placeholder="Nome FR" required>
    <textarea name="description_en" placeholder="Descrição EN"></textarea>
    <textarea name="description_fr" placeholder="Descrição FR"></textarea>
    <select name="company_id" required>
        <option value="">Empresa</option>
        <?php foreach ($companies as $c): ?>
            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
        <?php endforeach; ?>
    </select>
    <input type="file" name="image">
    <button>Salvar</button>
</form>