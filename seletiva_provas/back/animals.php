<?php
require 'init.php';
requireLogin();

// ---- filtros (todos GET, mesmo padrão) ----
$name   = trim($_GET['name'] ?? '');
$cat    = $_GET['category_id'] ?? '';
$risk   = $_GET['risk_id'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT a.*, c.name category_name, r.description risk_desc, r.acronym risk_acronym
        FROM animals a
        JOIN categories c ON c.id = a.category_id
        JOIN extinction_risks r ON r.id = a.extinction_risk_id
        WHERE 1=1";
$params = [];

if ($name)   { $sql .= " AND (a.name LIKE ? OR a.scientific_name LIKE ?)"; $params[] = "%$name%"; $params[] = "%$name%"; }
if ($cat)    { $sql .= " AND a.category_id=?"; $params[] = $cat; }
if ($risk)   { $sql .= " AND a.extinction_risk_id=?"; $params[] = $risk; }
if ($status) { $sql .= " AND a.operation_status=?"; $params[] = $status; }

$sql .= " ORDER BY FIELD(a.operation_status,'em_exposicao','fora_de_exibicao','em_adaptacao'), a.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$animals = $stmt->fetchAll();

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$risks      = $pdo->query('SELECT * FROM extinction_risks')->fetchAll();

// ---- "modal" de visualização: SEM AJAX, só PHP + CSS ----
// Se veio ?view=ID na URL, busca o animal, soma 1 visita e mostra a div abaixo.
$viewAnimal = null;
if (!empty($_GET['view'])) {
    $stmt = $pdo->prepare("SELECT a.*, c.name category_name, f.name feed_name, r.description risk_desc, r.acronym risk_acronym
        FROM animals a
        JOIN categories c ON c.id=a.category_id
        JOIN feed_classes f ON f.id=a.feed_class_id
        JOIN extinction_risks r ON r.id=a.extinction_risk_id
        WHERE a.id=?");
    $stmt->execute([$_GET['view']]);
    $viewAnimal = $stmt->fetch();
    if ($viewAnimal) {
        $pdo->prepare('UPDATE animals SET visits = visits + 1 WHERE id=?')->execute([$viewAnimal['id']]);
        $viewAnimal['visits']++;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head><meta charset="UTF-8"><title>Animais</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
<header class="topbar">
    <span>Olá, <?= e($_SESSION['user_name']) ?></span>
    <a href="logout">Sair</a>
</header>

<main class="container">
    <div class="row">
        <h1>Animais</h1>
        <a class="btn" href="animals/new">Novo animal</a>
    </div>

    <form method="GET" class="filtros">
        <input name="name" placeholder="Nome ou científico" value="<?= e($name) ?>">
        <select name="category_id">
            <option value="">Categoria</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="risk_id">
            <option value="">Risco</option>
            <?php foreach ($risks as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $risk == $r['id'] ? 'selected' : '' ?>><?= e($r['description']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status">
            <option value="">Status</option>
            <option value="em_exposicao" <?= $status == 'em_exposicao' ? 'selected' : '' ?>>Em exposição</option>
            <option value="fora_de_exibicao" <?= $status == 'fora_de_exibicao' ? 'selected' : '' ?>>Fora de exibição</option>
            <option value="em_adaptacao" <?= $status == 'em_adaptacao' ? 'selected' : '' ?>>Em adaptação</option>
        </select>
        <button class="btn" type="submit">Filtrar</button>
        <a class="btn btn2" href="animals">Limpar</a>
    </form>

    <table>
        <tr><th>Nome</th><th>Científico</th><th>Visitas</th><th>Categoria</th><th>Status</th><th>Risco</th><th>Ações</th></tr>
        <?php foreach ($animals as $a): ?>
        <tr>
            <td><a href="?view=<?= $a['id'] ?>"><?= e($a['name']) ?></a></td>
            <td><?= e($a['scientific_name']) ?></td>
            <td><?= (int)$a['visits'] ?></td>
            <td><?= e($a['category_name']) ?></td>
            <td><?= statusLabel($a['operation_status']) ?></td>
            <td><span class="badge" style="background:<?= riskColor($a['risk_acronym']) ?>"><?= e($a['risk_desc']) ?></span></td>
            <td>
                <a href="animals/edit/<?= $a['id'] ?>">Editar</a>
                <form method="POST" action="animals/delete/<?= $a['id'] ?>" style="display:inline" onsubmit="return confirm('Remover este animal?')">
                    <button type="submit" class="link-btn">Remover</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$animals): ?><tr><td colspan="7">Nenhum animal encontrado.</td></tr><?php endif; ?>
    </table>
</main>

<!-- MODAL: fica sempre no HTML, só aparece quando $viewAnimal existe -->
<div class="modal <?= $viewAnimal ? '' : 'hidden' ?>">
    <div class="modal-content">
        <a href="animals" class="modal-close">&times;</a>
        <?php if ($viewAnimal):
            $imgs = array_filter(explode(',', $viewAnimal['images']));
            $folder = slug($viewAnimal['name']);
        ?>
            <h2><?= e($viewAnimal['name']) ?></h2>
            <p><em><?= e($viewAnimal['scientific_name']) ?></em></p>

            <?php if ($imgs): ?>
            <div class="carousel">
                <?php foreach ($imgs as $i => $img): ?>
                    <img src="uploads/animals/<?= e($folder) ?>/<?= e($img) ?>" class="carousel-img" style="<?= $i === 0 ? '' : 'display:none' ?>">
                <?php endforeach; ?>
                <button type="button" onclick="carouselMove(-1)">&lt;</button>
                <button type="button" onclick="carouselMove(1)">&gt;</button>
            </div>
            <?php endif; ?>

            <p><?= nl2br(e($viewAnimal['description'])) ?></p>
            <p><strong>Categoria:</strong> <?= e($viewAnimal['category_name']) ?> — <strong>Alimentação:</strong> <?= e($viewAnimal['feed_name']) ?></p>
            <p><strong>Tamanho:</strong> <?= e($viewAnimal['size']) ?> m &nbsp; <strong>Peso:</strong> <?= e($viewAnimal['weight']) ?> kg</p>
            <p><strong>Status:</strong> <?= statusLabel($viewAnimal['operation_status']) ?></p>
            <p><strong>Risco:</strong> <span class="badge" style="background:<?= riskColor($viewAnimal['risk_acronym']) ?>"><?= e($viewAnimal['risk_desc']) ?></span></p>
            <p><strong>Visitas:</strong> <?= (int)$viewAnimal['visits'] ?></p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de inatividade: mesmo bloco copiado em toda página logada -->
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
