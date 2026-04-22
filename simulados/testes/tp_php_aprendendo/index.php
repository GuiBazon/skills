<?php
header('Content-Type: application/json');

$conexao = new mysqli("localhost", "aluno", "senai@604", "biblioteca_db");

$jsonRecebido = file_get_contents("php://input");

$dados = json_decode($jsonRecebido, true);

if (isset($dados["nome"], $dados["username"], $dados["senha"])) {
    $nome = $dados["nome"];
    $user = $dados["username"];
    $pass = $dados["senha"];
    
    $sql = "SELECT * FROM usuarios WHERE username = '$user' AND senha = '$pass'";
    $res = $conexao->query($sql);

    if ($res->num_rows > 0) {
        $token = md5($user);
        $resposta = ["token" => $token];
        echo json_encode($resposta);
    } else {
        http_response_code(401);
        echo json_encode(["mensagem" => "Dados incorretos"]);
    }
}
