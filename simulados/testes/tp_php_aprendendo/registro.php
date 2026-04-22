<?php
header('Content-Type: application/json');

$conexao = new mysqli("localhost", "aluno", "senai@604", "biblioteca_db");

$jsonRecebido = file_get_contents("php://input");

$dados = json_decode($jsonRecebido, true);

if (isset($dados["nome"], $dados["username"], $dados["senha"])) {
    $nome = $dados["nome"];
    $user = $dados["username"];
    $pass = $dados["senha"];

    $sql = "INSERT INTO usuarios (nome, username, senha) values ('$nome', '$user', '$pass')";
    $res = $conexao->query($sql);

    if ($res == true) {
        echo json_encode(["mensagem" => "Cadastro realizado com sucesso"]);
    } else {
        http_response_code(422);
        echo json_encode(["mensagem" => "Erro ao cadastrar, verifique os dados"]);
    }
}
