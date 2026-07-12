<div class="cover-container"><img src="<?= $coverUrl ?>" class="cover-image" id="coverImage"></div>
<h1 class="article-title"><?= htmlspecialchars($titulo) ?></h1>
<aside class="aside-info">
    <div>Data: <?= $data ?></div>
    <div>Tags: <?php foreach($tags as $t): ?><a href="<?= $BASE ?>/tags/<?= urlencode($t) ?>"><?= $t ?></a> <?php endforeach; ?></div>
    <?php if($draft): ?><div class="draft-badge">RASCUNHO</div><?php endif; ?>
</aside>
<div class="main-content"><?= $conteudoHtml ?></div>
<script src="<?= $BASE ?>/js/script.js"></script>