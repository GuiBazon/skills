<?php
include 'config.php';
checkAuth();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gtin = $_POST['gtin'];

    if (!preg_match('/^[0-9]{13,14}$/', $gtin)) {
        die("GTIN inválido!");
    }

    $imageName = "placeholder.png";
    if ($_FILES['image']['name']) {
        $imageName = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $imageName);
    }

    $sql = "INSERT INTO products (company_id, gtin, name_en, name_fr, image_path) VALUES (?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([$_POST['company_id'], $gtin, $_POST['name_en'], $_POST['name_fr'], $imageName]);

    echo "Produto cadastrado!";
}
?>
<form method="POST" enctype="multipart/form-data">
    <input name="gtin" placeholder="GTIN">
    <input name="name_en" placeholder="Name EN">
    <input name="name_fr" placeholder="Name FR">
    <input type="file" name="image">
    <button type="submit">Salvar</button>
</form>