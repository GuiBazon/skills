<?php

function autenticar($pdo) {
  $headers = getallheaders();
  $token   = $headers['Authorization'] ?? null;

  if (!$token) {
    http_response_code(401);
    echo json_encode(['mensagem' => 'Usuário inválido']);
    exit();
  }

  $stmt = $pdo->prepare("SELECT * FROM users WHERE token = ?");
  $stmt->execute([$token]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$usuario) {
    http_response_code(401);
    echo json_encode(['mensagem' => 'Usuário inválido']);
    exit();
  }

  return $usuario; 
}

function autenticarAdmin($pdo) {
  $usuario = autenticar($pdo);

  if (!$usuario['is_admin']) {
    http_response_code(401);
    echo json_encode(['mensagem' => 'Usuário inválido']);
    exit();
  }

  return $usuario;
}

?>