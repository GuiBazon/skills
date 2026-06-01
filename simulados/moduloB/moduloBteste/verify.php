<?php
include 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lines = explode("\n", $_POST['gtins']);
    foreach ($lines as $gtin) {
        $gtin = trim($gtin);
        $stmt = $pdo->prepare("SELECT * FROM products WHERE gtin = ? AND is_hidden = 0");
        $stmt->execute([$gtin]);
        if ($stmt->fetch()) {
            echo "GTIN $gtin: VÁLIDO<br>";
        } else {
            echo "GTIN $gtin: INVÁLIDO ou OCULTO<br>";
        }
    }
}
?>
<form method="POST">
    <textarea name="gtins"></textarea>
    <button type="submit">Verificar</button>
</form>