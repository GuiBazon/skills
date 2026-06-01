<?php require_once '../includes/auth.php'; ?>
<?php require_once '../config/db.php'; ?>
<?php
$gtin = $_GET['gtin'];
if ($_POST) {
    if (isset($_POST['delete']) && $_POST['delete'] == '1') {
        // exclusão permanente
        $pdo->prepare('DELETE FROM products WHERE gtin=?')->execute([$gtin]);
        header('Location: products.php');
        exit;
    } else {
        $hidden = isset($_POST['hidden']) ? 1 : 0;
        $stmt = $pdo->prepare('UPDATE products SET name_en=?, name_fr=?, description_en=?, description_fr=?, hidden=?, company_id=? WHERE gtin=?');
        $stmt->execute([$_POST['name_en'], $_POST['name_fr'], $_POST['description_en'], $_POST['description_fr'], $hidden, $_POST['company_id'], $gtin]);
        header('Location: products.php');
        exit;
    }
}
$prod = $pdo->prepare('SELECT * FROM products WHERE gtin=?');
$prod->execute([$gtin]);
$p = $prod->fetch();
$companies = $pdo->query('SELECT id, name FROM companies WHERE deactivated=0')->fetchAll();
?>
<form method="post">
    <input name="name_en" value="<?= $p['name_en'] ?>">
    <input name="name_fr" value="<?= $p['name_fr'] ?>">
    <textarea name="description_en"><?= $p['description_en'] ?></textarea>
    <textarea name="description_fr"><?= $p['description_fr'] ?></textarea>
    <select name="company_id">
        <?php foreach ($companies as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $c['id'] == $p['company_id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
        <?php endforeach; ?>
    </select>
    <label><input type="checkbox" name="hidden" <?= $p['hidden'] ? 'checked' : '' ?>> Oculto</label>
    <button type="submit">Salvar</button>
</form>
<?php if ($p['hidden']): ?>
    <form method="post" onsubmit="return confirm('Excluir permanentemente?')">
        <input type="hidden" name="delete" value="1">
        <button type="submit" style="color:red">Excluir permanentemente</button>
    </form>
<?php endif; ?>