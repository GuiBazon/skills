<?php
include 'config.php';
checkAuth();

if (isset($_GET['disable'])) {
    $id = $_GET['disable'];

    $pdo->prepare("UPDATE companies SET is_active = 0 WHERE id = ?")->execute([$id]);

    $pdo->prepare("UPDATE products SET is_hidden = 1 WHERE company_id = ?")->execute([$id]);
    header("Location: admin_companies.php");
}
