<?php
// O DSN diz qual é o tipo de banco (mysql), onde ele está (localhost) e o nome dele (dbname)
$dsn = "mysql:host=localhost;dbname=simulado_enem;charset=utf8mb4";
$usuario = "root"; // Padrão do XAMPP
$senha = ""; // Padrão do XAMPP (vazio)

try {
    // Aqui nós "instanciamos" (criamos) a conexão usando Orientação a Objetos
    $pdo = new PDO($dsn, $usuario, $senha);
    
    // Essa linha é O SEGREDO DO PDO: Ela faz o PHP avisar se der algum erro de SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Para testar, você pode descomentar a linha abaixo depois
    // echo "Conectou bonito!";
} catch (PDOException $e) {
    // Se der erro, ele cai aqui e não "quebra" feio na tela do usuário
    echo "Erro de conexão: " . $e->getMessage();
}
?>