<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
require_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $scientific  = trim($_POST['scientific_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $size        = $_POST['size'] ?? '';
    $weight      = $_POST['weight'] ?? '';
    $feedClass   = $_POST['feed_class_id'] ?? '';
    $risk        = $_POST['extinction_risk_id'] ?? '';
    $status      = $_POST['operation_status'] ?? '';
    $category    = $_POST['category_id'] ?? '';

    if ($name === '' || $scientific === '' || $description === '' || $size === '' || $weight === ''
        || $feedClass === '' || $risk === '' || $status === '' || $category === '') {
        $errors[] = 'Todos os campos são obrigatórios.';
    }
    if (mb_strlen($description) > 250) {
        $errors[] = 'A descrição deve ter no máximo 250 caracteres.';
    }
    if ($size !== '' && !preg_match('/^\d+(\.\d{1,2})?$/', $size)) {
        $errors[] = 'Tamanho deve ter no máximo 2 casas decimais.';
    }
    if ($weight !== '' && !preg_match('/^\d+(\.\d{1,2})?$/', $weight)) {
        $errors[] = 'Peso deve ter no máximo 2 casas decimais.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM animals WHERE name = ? OR scientific_name = ?');
        $stmt->execute([$name, $scientific]);
        if ($stmt->fetch()) {
            $errors[] = 'Já existe um animal com este nome ou nome científico.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO animals (name, scientific_name, description, size, weight, feed_class_id, extinction_risk_id, operation_status, category_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $scientific, $description, $size, $weight, $feedClass, $risk, $status, $category]);
        $animalId = $pdo->lastInsertId();

        $slug = slugify($name);
        $images = save_animal_images($_FILES['images'] ?? [], $slug);
        $pos = 1;
        foreach ($images as $img) {
            $ins = $pdo->prepare('INSERT INTO animal_images (animal_id, filename, position) VALUES (?, ?, ?)');
            $ins->execute([$animalId, $img, $pos++]);
        }

        redirect('animals');
    }
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$feedClasses = $pdo->query('SELECT * FROM feed_classes ORDER BY name')->fetchAll();
$risks = $pdo->query('SELECT * FROM extinction_risks')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Novo animal - ZooSystem Management</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <span>Olá, <?= e($_SESSION['user_name']) ?></span>
    <a href="logout">Sair</a>
</header>
<main class="container">
    <h1>Novo animal</h1>

    <?php foreach ($errors as $err): ?><p class="error"><?= e($err) ?></p><?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nome</label>
        <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>

        <label>Nome científico</label>
        <input type="text" name="scientific_name" value="<?= e($_POST['scientific_name'] ?? '') ?>" required>

        <label>Descrição</label>
        <textarea name="description" maxlength="250" required><?= e($_POST['description'] ?? '') ?></textarea>

        <label>Tamanho (m)</label>
        <input type="number" step="0.01" name="size" required>

        <label>Peso (kg)</label>
        <input type="number" step="0.01" name="weight" required>

        <label>Classificação alimentar</label>
        <select name="feed_class_id" required>
            <option value="">Selecione</option>
            <?php foreach ($feedClasses as $f): ?>
                <option value="<?= $f['id'] ?>"><?= e($f['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Risco de extinção</label>
        <select name="extinction_risk_id" required>
            <option value="">Selecione</option>
            <?php foreach ($risks as $r): ?>
                <option value="<?= $r['id'] ?>"><?= e($r['description']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Status atual de operação</label>
        <select name="operation_status" required>
            <option value="">Selecione</option>
            <option value="em_exposicao">Em exposição</option>
            <option value="fora_de_exibicao">Fora de exibição</option>
            <option value="em_adaptacao">Em adaptação</option>
        </select>

        <label>Categoria do Animal</label>
        <select name="category_id" required>
            <option value="">Selecione</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Imagens (máximo 5)</label>
        <input type="file" name="images[]" multiple accept="image/*">

        <button class="btn" type="submit">Salvar</button>
        <a class="btn btn-secondary" href="animals">Cancelar</a>
    </form>
</main>
<script src="assets/script.js"></script>
</body>
</html>
