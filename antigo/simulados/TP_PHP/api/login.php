<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$dados = lerInputJSON();
$username = $dados['username'] ?? '';
$senha = $dados['senha'] ?? '';

if (empty($username) || empty($senha)) {
    http_response_code(401);
    echo json_encode(["mensagem" => "Dados incorretos"]);
    exit;
}

$stmt = $conexao->prepare("SELECT * FROM usuarios WHERE username = ? AND senha = ?");
$stmt->bind_param("ss", $username, $senha);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    http_response_code(200);
    echo json_encode(["token" => md5($username)]);
} else {
    http_response_code(401);
    echo json_encode(["mensagem" => "Dados incorretos"]);
}
