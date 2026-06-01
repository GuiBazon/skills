<?php

function conectar()
{
  $host   = 'localhost';
  $banco  = 'enem_db';
  $user   = 'aluno';
  $pass   = 'senai@604'; 

  try {
    $pdo = new PDO(
      "mysql:host=$host;dbname=$banco;charset=utf8",
      $user,
      $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['mensagem' => 'Erro de conexão']);
    exit();
  }
}
