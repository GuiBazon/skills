<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
require_login();

// Filtros
$name     = trim($_GET['name'] ?? '');
$category = $_GET['category_id'] ?? '';
$risk     = $_GET['risk_id'] ?? '';
$status   = $_GET['status'] ?? '';

$where  = [];
$params = [];

if ($name !== '') {
    $where[] = '(a.name LIKE ? OR a.scientific_name LIKE ?)';
    $params[] = "%$name%";
    $params[] = "%$name%";
}
if ($category !== '') {
    $where[] = 'a.category_id = ?';
    $params[] = $category;
}
if ($risk !== '') {
    $where[] = 'a.extinction_risk_id = ?';
    $params[] = $risk;
}
if ($status !== '') {
    $where[] = 'a.operation_status = ?';
    $params[] = $status;
}

$sql = "SELECT a.*, c.name AS category_name, r.description AS risk_desc, r.acronym AS risk_acronym
        FROM animals a
        JOIN categories c ON c.id = a.category_id
        JOIN extinction_risks r ON r.id = a.extinction_risk_id";
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= " ORDER BY FIELD(a.operation_status,'em_exposicao','fora_de_exibicao','em_adaptacao'), a.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$animals = $stmt->fetchAll();

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$risks = $pdo->query('SELECT * FROM extinction_risks')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Animais - ZooSystem Management</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <span>Olá, <?= e($_SESSION['user_name']) ?></span>
    <a href="logout">Sair</a>
</header>

<main class="container">
    <div class="row-between">
        <h1>Animais</h1>
        <a class="btn" href="animals/new">Novo animal</a>
    </div>

    <form method="GET" class="filters">
        <input type="text" name="name" placeholder="Nome ou nome científico" value="<?= e($name) ?>">
        <select name="category_id">
            <option value="">Categoria</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $category == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="risk_id">
            <option value="">Risco de extinção</option>
            <?php foreach ($risks as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $risk == $r['id'] ? 'selected' : '' ?>><?= e($r['description']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status">
            <option value="">Status de operação</option>
            <option value="em_exposicao" <?= $status == 'em_exposicao' ? 'selected' : '' ?>>Em exposição</option>
            <option value="fora_de_exibicao" <?= $status == 'fora_de_exibicao' ? 'selected' : '' ?>>Fora de exibição</option>
            <option value="em_adaptacao" <?= $status == 'em_adaptacao' ? 'selected' : '' ?>>Em adaptação</option>
        </select>
        <button class="btn" type="submit">Filtrar</button>
        <a class="btn btn-secondary" href="animals">Limpar</a>
    </form>

    <table>
        <thead>
        <tr>
            <th>Nome</th>
            <th>Nome científico</th>
            <th>Visitas</th>
            <th>Categoria</th>
            <th>Status</th>
            <th>Risco de extinção</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($animals as $a): ?>
            <tr>
                <td><a href="#" onclick="viewAnimal(<?= $a['id'] ?>); return false;"><?= e($a['name']) ?></a></td>
                <td><?= e($a['scientific_name']) ?></td>
                <td><?= (int)$a['visits'] ?></td>
                <td><?= e($a['category_name']) ?></td>
                <td><?= status_label($a['operation_status']) ?></td>
                <td><span class="risk-badge" style="background:<?= risk_color($a['risk_acronym']) ?>"><?= e($a['risk_desc']) ?></span></td>
                <td>
                    <a href="animals/edit/<?= $a['id'] ?>">Editar</a>
                    <a href="#" onclick="deleteAnimal(<?= $a['id'] ?>); return false;">Remover</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$animals): ?>
            <tr><td colspan="7">Nenhum animal encontrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</main>

<!-- Modal visualização -->
<div id="viewModal" class="modal hidden">
    <div class="modal-content">
        <button class="modal-close" onclick="closeViewModal()">&times;</button>
        <div id="viewModalBody">Carregando...</div>
    </div>
</div>

<!-- Modal confirmação remoção -->
<div id="deleteModal" class="modal hidden">
    <div class="modal-content">
        <p>Tem certeza que deseja remover este animal?</p>
        <button class="btn" id="confirmDeleteBtn">Confirmar</button>
        <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
    </div>
</div>

<!-- Modal de inatividade -->
<div id="inactivityModal" class="modal hidden">
    <div class="modal-content">
        <p>Você ainda está por aí?</p>
        <p>Tempo restante: <span id="inactivityTimer">10</span>s</p>
        <button class="btn" id="inactivityYes">Sim</button>
        <button class="btn btn-secondary" id="inactivityNo">Não</button>
    </div>
</div>

<script src="assets/script.js"></script>
</body>
</html>
