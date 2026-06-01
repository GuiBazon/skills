<h1>Patrimônios de Lyon</h1>
<div class="busca"><input type="text" id="searchInput" placeholder="Pesquisar (use / para OR)"></div>

<?php if($pastas): ?>
    <h2>Pastas</h2>
    <?php foreach($pastas as $p): ?>
        <div class="folder-item"><a href="<?= $BASE ?>/heritages/<?= $relPath ? "$relPath/" : "" ?><?= $p ?>"><?= $p ?>/</a></div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if($artigos): ?>
    <h2>Artigos</h2>
    <?php foreach($artigos as $a): ?>
        <div class="page-card" data-title="<?= strtolower($a['titulo']) ?>" data-resumo="<?= strtolower($a['resumo']) ?>">
            <a href="<?= $BASE ?>/heritages/<?= $relPath ? "$relPath/" : "" ?><?= $a['slug'] ?>">
                <h3><?= htmlspecialchars($a['titulo']) ?></h3>
                <p><?= htmlspecialchars($a['resumo']) ?></p>
            </a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script src="<?= $BASE ?>/js/script.js"></script>