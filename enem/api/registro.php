<?php

require_once '../config/database.php';

// 1. Ler os dados enviados
$body     = json_decode(file_get_contents('php://input'), true);
$nome     = $body['nome'] ?? null;
$username = $body['username'] ?? null;
$senha    = $body['senha'] ?? null;

// 2. Validar campos obrigatórios
if (!$nome || !$username || !$senha) {
  http_response_code(422);
  echo json_encode(['mensagem' => 'Erro ao cadastrar, verifique os dados']);
  exit();
}

$pdo = conectar();

// 3. Verificar se username já existe
$check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$check->execute([$username]);
if ($check->fetch()) {
  http_response_code(422);
  echo json_encode(['mensagem' => 'Erro ao cadastrar, verifique os dados']);
  exit();
}

// 4. Criar usuário
$hash = password_hash($senha, PASSWORD_BCRYPT);
$stmt = $pdo->prepare(
  "INSERT INTO users (nome, username, senha) VALUES (?, ?, ?)"
);
$stmt->execute([$nome, $username, $hash]);

// 5. Retornar sucesso
http_response_code(200);
echo json_encode(['mensagem' => 'Cadastro realizado com sucesso']);

?>