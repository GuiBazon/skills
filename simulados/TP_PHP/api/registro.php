<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$dados = lerInputJSON();
$nome = $dados['nome'] ?? '';
$username = $dados['username'] ?? '';
$senha = $dados['senha'] ?? '';

$codigo_secreto = $dados['codigo_secreto'] ?? '';
$tipo_usuario = ($codigo_secreto === 'slakk') ? 'admin' : 'comum';

if (empty($nome) || empty($username) || empty($senha)) {
    http_response_code(422);
    echo json_encode(["mensagem" => "Erro ao cadastrar, verifique os dados"]);
    exit;
}

$stmt = $conexao->prepare("SELECT id_usuario FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    http_response_code(422);
    echo json_encode(["mensagem" => "Erro ao cadastrar, verifique os dados"]);
    exit;
}

$stmt = $conexao->prepare("INSERT INTO usuarios (nome, username, senha, tipo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nome, $username, $senha, $tipo_usuario);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(["mensagem" => "Cadastro realizado com sucesso"]);
} else {
    http_response_code(422);
    echo json_encode(["mensagem" => "Erro ao cadastrar, verifique os dados"]);
}
