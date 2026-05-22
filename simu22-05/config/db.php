<?php
try {
    $pdo = new PDO("mysql:host=10.89.234.142;dbname=backend22_05;charset=utf8mb4", "competidor", "senaisp");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die($e->getMessage());
}