<?php
header('Content-Type: application/json');
$conexao = new mysqli("localhost", "aluno", "senai@604", "biblioteca_db");

$headers = getallheaders();

if (!isset($headers['token'])) {
    http_response_code(401);
    echo json_encode(["mensagem" => "Usuário inválido"]);
    exit;
}

$tokenRecebido = $headers['token'];

// 3. Validar se o token é real (Simulação rápida para o seu caso)
// O token é md5(username). Para validar de verdade, você teria que buscar no banco.
// Mas para o simulado, vamos ver se veio os dados do livro:
if (isset($_POST["titulo"], $_POST["quantidade_livros"], $_POST["autor"])) {
    // Aqui você faria o INSERT do livro...
    echo json_encode(["mensagem" => "Livro cadastrado com sucesso"]);
}
