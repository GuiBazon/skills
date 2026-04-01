<?php
// 1. Avisa para quem estiver lendo (como o Postman) que a resposta será em formato JSON
header('Content-Type: application/json');

// 2. Chama o arquivo de conexão que acabamos de criar
require 'conexao.php';

// 3. Monta uma resposta simulada
$resposta = [
    "status" => "sucesso",
    "mensagem" => "Minha primeira API tá viva!",
    "banco" => "Conectado via PDO com sucesso"
];

// 4. Converte o array do PHP em JSON e imprime na tela
echo json_encode($resposta);
?>