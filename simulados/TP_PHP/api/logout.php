<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') exit;

$usuario = validarToken($conexao);

http_response_code(200);
echo json_encode(["mensagem" => "Logout realizado com sucesso"]);
