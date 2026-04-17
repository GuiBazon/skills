<?php

// CRUD DE USUÁRIOS COM PDO (PHP Data Objects, é uma extensão da linguagem PHP voltada para acesso a bancos de dados)
// Autor: Guilherme Bazon

// 1. CONEXÃO COM O BANCO DE DADOS
function conectar()
{
    $host = 'localhost';
    $db = 'meu_banco';
    $user = 'meu_usuario';
    $pass = 'minha_senha';
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }
}
$pdo = conectar();
// 2. FUNÇÕES CRUD
function criarUsuario($pdo, $nome, $email)
{
    $stmt = $pdo->prepare('INSERT INTO usuarios (nome, email) VALUES (?, ?)');
    $stmt->execute([$nome, $email]);
    return $pdo->lastInsertId();
}
function lerUsuarios($pdo)
{
    $stmt = $pdo->query('SELECT * FROM usuarios');
    return $stmt->fetchAll();
}

function atualizarUsuario($pdo, $id, $nome, $email)
{
    $stmt = $pdo->prepare('UPDATE usuarios SET nome = ?, email = ? WHERE id = ?');
    return $stmt->execute([$nome, $email, $id]);
}
function deletarUsuario($pdo, $id)
{
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
    return $stmt->execute([$id]);
}

// login do usuário
function login($pdo, $email)
{
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    return $stmt->fetch();
}

// Exemplo de uso das funções CRUD
// Criar um novo usuário
$novoId = criarUsuario($pdo, 'João Silva', 'joao.silva@example.com');
echo "Novo usuário criado com ID: $novoId<br>";
// Ler todos os usuários
$usuarios = lerUsuarios($pdo);
echo "<h2>Lista de Usuários:</h2><ul>";
foreach ($usuarios as $usuario) {
    echo "<li>{$usuario['id']}: {$usuario['nome']} ({$usuario['email']})</li>";
}
echo "</ul>";
// Atualizar um usuário
atualizarUsuario($pdo, $novoId, 'João S. Silva', 'joao.s.silva@example.com');
echo "Usuário com ID $novoId atualizado.<br>";
// Deletar um usuário
deletarUsuario($pdo, $novoId);
echo "Usuário com ID $novoId deletado.<br>";
// Login do usuário
$usuarioLogado = login($pdo, 'joao.s.silva@example.com');
if ($usuarioLogado) {
    echo "Usuário logado: {$usuarioLogado['nome']}<br>";
} else {
    echo "Usuário não encontrado.<br>";
}

// historia do php
// PHP foi criado por Rasmus Lerdorf em 1994 como um conjunto de scripts CGI para monitorar visitas ao seu currículo online. Originalmente chamado de "Personal Home Page Tools", o PHP evoluiu rapidamente com a contribuição de outros desenvolvedores, tornando-se uma linguagem de script de código aberto amplamente utilizada para desenvolvimento web. Em 1995, o PHP/FI (Forms Interpreter) foi lançado, introduzindo recursos como suporte a bancos de dados e manipulação de formulários. Ao longo dos anos, o PHP passou por várias versões importantes, incluindo o PHP 3 em 1998, que estabeleceu a base para a linguagem moderna, e o PHP 4 em 2000, que trouxe melhorias significativas no desempenho e na funcionalidade orientada a objetos. O PHP 5, lançado em 2004, introduziu um modelo de objetos mais robusto e suporte a exceções. A versão mais recente, PHP 7, lançada em 2015, trouxe melhorias substanciais de desempenho e novas funcionalidades, consolidando ainda mais o papel do PHP no desenvolvimento web moderno.   

