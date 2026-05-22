<?php

require_once '../config/database.php';

$body     = json_decode(file_get_contents('php://input'), true);
$nome     = $body['nome'] ?? null;
$username = $body['username'] ?? null;
$senha    = $body['senha'] ?? null;

if (!$nome || !$username || !$senha) {
  http_response_code(422);
  echo json_encode(['mensagem' => 'Erro ao cadastrar, verifique os dados']);
  exit();
}

$pdo = conectar();

$check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$check->execute([$username]);
if ($check->fetch()) {
  http_response_code(422);
  echo json_encode(['mensagem' => 'Erro ao cadastrar, verifique os dados']);
  exit();
}

$hash = password_hash($senha, PASSWORD_BCRYPT);
$stmt = $pdo->prepare(
  "INSERT INTO users (nome, username, senha) VALUES (?, ?, ?)"
);
$stmt->execute([$nome, $username, $hash]);

http_response_code(200);
echo json_encode(['mensagem' => 'Cadastro realizado com sucesso']);
