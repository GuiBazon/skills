<?php
$dsn = "mysql:host=localhost;dbname=simulado_enem;charset=utf8mb4";
$usuario = "root"; 
$senha = ""; 

try {
    $pdo = new PDO($dsn, $usuario, $senha);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
