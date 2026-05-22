<?php

header('Content-Type: application/json');

require 'conexao.php';

$resposta = [
    "status" => "sucesso",
    "mensagem" => "Minha primeira API tá viva!",
    "banco" => "Conectado via PDO com sucesso"
];

echo json_encode($resposta);
