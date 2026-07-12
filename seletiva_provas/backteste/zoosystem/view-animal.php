<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';
require_login();

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT a.*, c.name AS category_name, f.name AS feed_name, r.description AS risk_desc, r.acronym AS risk_acronym
    FROM animals a
    JOIN categories c ON c.id = a.category_id
    JOIN feed_classes f ON f.id = a.feed_class_id
    JOIN extinction_risks r ON r.id = a.extinction_risk_id
    WHERE a.id = ?');
$stmt->execute([$id]);
$animal = $stmt->fetch();

if (!$animal) {
    http_response_code(404);
    echo '<p>Animal não encontrado.</p>';
    exit;
}

$pdo->prepare('UPDATE animals SET visits = visits + 1 WHERE id = ?')->execute([$id]);

$slug = slugify($animal['name']);
$imgStmt = $pdo->prepare('SELECT * FROM animal_images WHERE animal_id = ? ORDER BY position');
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();
?>
<h2><?= e($animal['name']) ?></h2>
<p><em><?= e($animal['scientific_name']) ?></em></p>

<?php if ($images): ?>
<div class="carousel" id="carousel">
    <?php foreach ($images as $i => $img): ?>
        <img src="uploads/animals/<?= e($slug) ?>/<?= e($img['filename']) ?>" class="carousel-img" style="<?= $i === 0 ? '' : 'display:none;' ?>">
    <?php endforeach; ?>
    <?php if (count($images) > 1): ?>
        <button type="button" onclick="carouselMove(-1)">&lt;</button>
        <button type="button" onclick="carouselMove(1)">&gt;</button>
    <?php endif; ?>
</div>
<?php else: ?>
<p>Sem imagens cadastradas.</p>
<?php endif; ?>

<p><?= nl2br(e($animal['description'])) ?></p>
<p><strong>Categoria:</strong> <?= e($animal['category_name']) ?></p>
<p><strong>Classificação alimentar:</strong> <?= e($animal['feed_name']) ?></p>
<p><strong>Tamanho:</strong> <?= e($animal['size']) ?> m &nbsp; <strong>Peso:</strong> <?= e($animal['weight']) ?> kg</p>
<p><strong>Status:</strong> <?= status_label($animal['operation_status']) ?></p>
<p><strong>Risco de extinção:</strong> <span class="risk-badge" style="background:<?= risk_color($animal['risk_acronym']) ?>"><?= e($animal['risk_desc']) ?></span></p>
<p><strong>Visitas:</strong> <?= (int)$animal['visits'] + 1 ?></p>
