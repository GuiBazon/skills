<?php
require 'init.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM animals WHERE id=?');
$stmt->execute([$id]);
$a = $stmt->fetch();
if (!$a) redirect('animals');

$erro = '';
$folder = slug($a['name']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc   = trim($_POST['description']);
    $size   = dec2($_POST['size']);
    $weight = dec2($_POST['weight']);
    $status = $_POST['operation_status'];

    if (!$desc || !$status) {
        $erro = 'Todos os campos são obrigatórios.';
    } elseif (mb_strlen($desc) > 250) {
        $erro = 'Descrição deve ter no máximo 250 caracteres.';
    } else {
        // imagens: parte da lista atual + remove marcadas + adiciona novas
        $imgs = array_filter(explode(',', $a['images']));
        foreach ($_POST['remove'] ?? [] as $rm) {
            $k = array_search($rm, $imgs);
            if ($k !== false) {
                @unlink(__DIR__ . "/uploads/animals/$folder/$rm");
                unset($imgs[$k]);
            }
        }
        $imgs = array_values($imgs);
        $novas = uploadImages($_FILES['images'] ?? [], $folder, count($imgs) + 1);
        if ($novas) $imgs = array_merge($imgs, explode(',', $novas));
        $imgs = array_slice($imgs, 0, 5);

        $stmt = $pdo->prepare('UPDATE animals SET description=?, size=?, weight=?, operation_status=?, images=? WHERE id=?');
        $stmt->execute([$desc, $size, $weight, $status, implode(',', $imgs), $id]);
        redirect('animals');
    }
}

$imgs = array_filter(explode(',', $a['images']));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Editar <?= e($a['name']) ?></title><link rel="stylesheet" href="assets/style.css"></head>
<body>
<header class="topbar"><span>Olá, <?= e($_SESSION['user_name']) ?></span><a href="logout">Sair</a></header>
<main class="container">
    <h1>Editar <?= e($a['name']) ?></h1>
    <?php if ($erro): ?><p class="erro"><?= e($erro) ?></p><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Descrição</label>
        <textarea name="description" maxlength="250" required><?= e($a['description']) ?></textarea>

        <label>Tamanho (m)</label>
        <input type="number" step="0.01" name="size" value="<?= e($a['size']) ?>" required>

        <label>Peso (kg)</label>
        <input type="number" step="0.01" name="weight" value="<?= e($a['weight']) ?>" required>

        <label>Status atual de operação</label>
        <select name="operation_status" required>
            <option value="em_exposicao" <?= $a['operation_status'] == 'em_exposicao' ? 'selected' : '' ?>>Em exposição</option>
            <option value="fora_de_exibicao" <?= $a['operation_status'] == 'fora_de_exibicao' ? 'selected' : '' ?>>Fora de exibição</option>
            <option value="em_adaptacao" <?= $a['operation_status'] == 'em_adaptacao' ? 'selected' : '' ?>>Em adaptação</option>
        </select>

        <label>Imagens atuais</label>
        <div class="img-list">
            <?php foreach ($imgs as $img): ?>
                <label class="img-item">
                    <img src="uploads/animals/<?= e($folder) ?>/<?= e($img) ?>" width="80">
                    <span><input type="checkbox" name="remove[]" value="<?= e($img) ?>"> Remover</span>
                </label>
            <?php endforeach; ?>
        </div>

        <label>Adicionar imagens</label>
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
