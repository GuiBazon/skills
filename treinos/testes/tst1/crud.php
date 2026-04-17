<?php

$pdo = conectar();

// ─── INSERT (Cadastrar usuário) ─────────────────
$stmt = $pdo->prepare(
  "INSERT INTO users (nome, username, senha) VALUES (?, ?, ?)"
);
$senhaHash = password_hash($senha, PASSWORD_BCRYPT);
$stmt->execute([$nome, $username, $senhaHash]);


// ─── SELECT (Buscar usuário pelo username) ──────
$stmt = $pdo->prepare(
  "SELECT * FROM users WHERE username = ?"
);
$stmt->execute([$username]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
// $usuario agora é um array: ['id'=>1, 'nome'=>'João', ...]


// ─── SELECT múltiplos (listar questões) ─────────
$stmt = $pdo->prepare(
  "SELECT * FROM questoes WHERE dificuldade = ? AND materia = ?"
);
$stmt->execute([$dificuldade, $materia]);
$questoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
// $questoes é um array de arrays


// ─── UPDATE (salvar token no login) ─────────────
$token = md5($username);
$stmt = $pdo->prepare(
  "UPDATE users SET token = ? WHERE id = ?"
);
$stmt->execute([$token, $usuario['id']]);


// ─── DELETE (logout — apagar token) ─────────────
$stmt = $pdo->prepare(
  "UPDATE users SET token = NULL WHERE token = ?"
);
$stmt->execute([$token]);

?>