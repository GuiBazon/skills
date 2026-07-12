<?php
require 'init.php';
requireLogin();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name']);
    $sci    = trim($_POST['scientific_name']);
    $desc   = trim($_POST['description']);
    $size   = dec2($_POST['size']);
    $weight = dec2($_POST['weight']);
    $feed   = $_POST['feed_class_id'];
    $risk   = $_POST['extinction_risk_id'];
    $status = $_POST['operation_status'];
    $cat    = $_POST['category_id'];

    if (!$name || !$sci || !$desc || !$feed || !$risk || !$status || !$cat) {
        $erro = 'Todos os campos são obrigatórios.';
    } elseif (mb_strlen($desc) > 250) {
        $erro = 'Descrição deve ter no máximo 250 caracteres.';
    } else {
        $check = $pdo->prepare('SELECT id FROM animals WHERE name=? OR scientific_name=?');
        $check->execute([$name, $sci]);
        if ($check->fetch()) {
            $erro = 'Já existe um animal com esse nome ou nome científico.';
        } else {
            $images = uploadImages($_FILES['images'] ?? [], slug($name));
            $stmt = $pdo->prepare('INSERT INTO animals
                (name,scientific_name,description,size,weight,feed_class_id,extinction_risk_id,operation_status,category_id,images)
                VALUES (?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$name, $sci, $desc, $size, $weight, $feed, $risk, $status, $cat, $images]);
            redirect('animals');
        }
    }
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$feeds      = $pdo->query('SELECT * FROM feed_classes ORDER BY name')->fetchAll();
$risks      = $pdo->query('SELECT * FROM extinction_risks')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Novo animal</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
<header class="topbar"><span>Olá, <?= e($_SESSION['user_name']) ?></span><a href="logout">Sair</a></header>
<main class="container">
    <h1>Novo animal</h1>
    <?php if ($erro): ?><p class="erro"><?= e($erro) ?></p><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nome</label>
        <input name="name" required>

        <label>Nome científico</label>
        <input name="scientific_name" required>

        <label>Descrição</label>
        <textarea name="description" maxlength="250" required></textarea>

        <label>Tamanho (m)</label>
        <input type="number" step="0.01" name="size" required>

        <label>Peso (kg)</label>
        <input type="number" step="0.01" name="weight" required>

        <label>Classificação alimentar</label>
        <select name="feed_class_id" required>
            <option value="">Selecione</option>
            <?php foreach ($feeds as $f): ?><option value="<?= $f['id'] ?>"><?= e($f['name']) ?></option><?php endforeach; ?>
        </select>

        <label>Risco de extinção</label>
        <select name="extinction_risk_id" required>
            <option value="">Selecione</option>
            <?php foreach ($risks as $r): ?><option value="<?= $r['id'] ?>"><?= e($r['description']) ?></option><?php endforeach; ?>
        </select>

        <label>Status atual de operação</label>
        <select name="operation_status" required>
            <option value="em_exposicao">Em exposição</option>
            <option value="fora_de_exibicao">Fora de exibição</option>
            <option value="em_adaptacao">Em adaptação</option>
        </select>

        <label>Categoria do Animal</label>
        <select name="category_id" required>
            <option value="">Selecione</option>
            <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
        </select>

        <label>Imagens (máximo 5)</label>
        <input type="file" name="images[]" multiple accept="image/*">

        <button class="btn" type="submit">Salvar</button>
        <a class="btn btn2" href="animals">Cancelar</a>
    </form>
</main>
<div id="inactivityModal" class="modal hidden">
    <div class="modal-content">
        <p>Você ainda está por aí?</p>
        <p>Tempo restante: <span id="segundos">10</span>s</p>
        <button class="btn" id="simBtn">Sim</button>
        <button class="btn btn2" id="naoBtn">Não</button>
    </div>
</div>

<script src="assets/script.js"></script>
</body>
</html>
