<?php
header('Content-Type: application/json');
$db_host = "localhost";
$db_user = "aluno";
$db_pass = "senai@604";
$db_name = "tp_php";
$conexao = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conexao->connect_error) {
    http_response_code(500);
    echo json_encode(["mensagem" => "Erro de conexão com o banco de dados"]);
    exit;
}
function lerInputJSON()
{
    return json_decode(file_get_contents('php://input'), true);
}
function validarToken($conexao)
{
    $headers = getallheaders();
    $token = $headers['token'] ?? $_GET['token'] ?? null;

    if (!$token) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Usuário inválido"]);
        exit;
    }

    $stmt = $conexao->prepare("SELECT id_usuario, nome, username, tipo FROM usuarios WHERE MD5(username) = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Usuário inválido"]);
        exit;
    }

    return $resultado->fetch_assoc();
}
