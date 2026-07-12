<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
require_login();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM animals WHERE id = ?');
$stmt->execute([$id]);
$animal = $stmt->fetch();

if (!$animal) {
    redirect('animals');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description'] ?? '');
    $size        = $_POST['size'] ?? '';
    $weight      = $_POST['weight'] ?? '';
    $status      = $_POST['operation_status'] ?? '';

    if ($description === '' || $size === '' || $weight === '' || $status === '') {
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
        $stmt = $pdo->prepare('UPDATE animals SET description=?, size=?, weight=?, operation_status=? WHERE id=?');
        $stmt->execute([$description, $size, $weight, $status, $id]);

        // Remover imagens marcadas
        $removeIds = $_POST['remove_images'] ?? [];
        if ($removeIds) {
            $slug = slugify($animal['name']);
            $in = implode(',', array_fill(0, count($removeIds), '?'));
            $q = $pdo->prepare("SELECT * FROM animal_images WHERE id IN ($in)");
            $q->execute($removeIds);
            foreach ($q->fetchAll() as $img) {
                @unlink(__DIR__ . '/uploads/animals/' . $slug . '/' . $img['filename']);
            }
            $del = $pdo->prepare("DELETE FROM animal_images WHERE id IN ($in)");
            $del->execute($removeIds);
        }

        // Adicionar novas imagens respeitando limite de 5
        $countStmt = $pdo->prepare('SELECT COUNT(*) c, COALESCE(MAX(position),0) maxpos FROM animal_images WHERE animal_id = ?');
        $countStmt->execute([$id]);
        $info = $countStmt->fetch();
        $slots = 5 - (int)$info['c'];

        if ($slots > 0 && !empty($_FILES['images']['name'][0])) {
            $slug = slugify($animal['name']);
            $dir = __DIR__ . '/uploads/animals/' . $slug;
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $pos = (int)$info['maxpos'] + 1;
            $n = min(count($_FILES['images']['name']), $slots);
            for ($i = 0; $i < $n; $i++) {
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
                $filename = $pos . '.' . $ext;
                move_uploaded_file($_FILES['images']['tmp_name'][$i], $dir . '/' . $filename);
                $ins = $pdo->prepare('INSERT INTO animal_images (animal_id, filename, position) VALUES (?, ?, ?)');
                $ins->execute([$id, $filename, $pos]);
                $pos++;
            }
        }

        redirect('animals');
    }
}

$imgStmt = $pdo->prepare('SELECT * FROM animal_images WHERE animal_id = ? ORDER BY position');
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();
$slug = slugify($animal['name']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar <?= e($animal['name']) ?> - ZooSystem Management</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <span>Olá, <?= e($_SESSION['user_name']) ?></span>
    <a href="logout">Sair</a>
</header>
<main class="container">
    <h1>Editar <?= e($animal['name']) ?></h1>

    <?php foreach ($errors as $err): ?><p class="error"><?= e($err) ?></p><?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Descrição</label>
        <textarea name="description" maxlength="250" required><?= e($animal['description']) ?></textarea>

        <label>Tamanho (m)</label>
        <input type="number" step="0.01" name="size" value="<?= e($animal['size']) ?>" required>

        <label>Peso (kg)</label>
        <input type="number" step="0.01" name="weight" value="<?= e($animal['weight']) ?>" required>

        <label>Status atual de operação</label>
        <select name="operation_status" required>
            <option value="em_exposicao" <?= $animal['operation_status']=='em_exposicao'?'selected':'' ?>>Em exposição</option>
            <option value="fora_de_exibicao" <?= $animal['operation_status']=='fora_de_exibicao'?'selected':'' ?>>Fora de exibição</option>
            <option value="em_adaptacao" <?= $animal['operation_status']=='em_adaptacao'?'selected':'' ?>>Em adaptação</option>
        </select>

        <label>Imagens atuais</label>
        <div class="image-list">
            <?php foreach ($images as $img): ?>
                <label class="image-item">
                    <img src="uploads/animals/<?= e($slug) ?>/<?= e($img['filename']) ?>" width="80">
                    <span><input type="checkbox" name="remove_images[]" value="<?= $img['id'] ?>"> Remover</span>
                </label>
            <?php endforeach; ?>
        </div>

        <label>Adicionar novas imagens</label>
        <input type="file" name="images[]" multiple accept="image/*">

        <button class="btn" type="submit">Salvar</button>
        <a class="btn btn-secondary" href="animals">Cancelar</a>
    </form>
</main>
<script src="assets/script.js"></script>
</body>
</html>
