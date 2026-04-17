<?php
require 'config.php';

$id = $_GET['id']; // Pega o ID da URL

if ($id) {
    $sql = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
    $sql->bindValue(':id', $id);
    $sql->execute();
}

header("Location: index.php");
exit;
?>