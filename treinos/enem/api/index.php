<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'];

$uri = str_replace('/enem/api', '', $uri);

if ($uri === '/registro' && $metodo === 'POST') {
  require 'registro.php';
} elseif ($uri === '/login' && $metodo === 'POST') {
  require 'login.php';
} elseif ($uri === '/logout' && $metodo === 'GET') {
  require 'logout.php';
} else {
  http_response_code(404);
  echo json_encode(['mensagem' => 'Rota não encontrada']);
}
