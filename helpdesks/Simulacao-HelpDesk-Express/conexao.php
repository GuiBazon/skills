<?php
$host = "localhost"; 
$usuario = "aluno"; 
$senha = "senai@604"; 
$banco = "seletiva_web_bazon"; 

$conn = new mysqli($host, $usuario, $senha, $banco);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>