<?php
// config.php
$host = 'localhost';
$dbname = 'crud_basico';
$username = 'alunods'; // Padrão do XAMPP
$password = 'senai@604'; // Padrão do XAMPP (geralmente vazio)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configura para dar erro se algo der errado (bom para debugar)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>