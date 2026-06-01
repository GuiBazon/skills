<<<<<<< HEAD:simulados/moduloB/deepseek/moduloB_1/config/database.php
<?php
$host = 'localhost';
$dbname = 'skills17';
$user = 'competidor';
$pass = 'senaisp';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro no banco: " . $e->getMessage());
}
=======
<?php
$host = 'localhost';
$dbname = 'skills17';
$user = 'competidor';
$pass = 'senaisp';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erro no banco: " . $e->getMessage());
}
>>>>>>> 30187337ebbbdf044ab033384716451ca30e67fd:simulados/deepseek/moduloB_1/config/database.php
?>