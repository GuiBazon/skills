<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$usuario = validarToken($conexao);

$stmt = $conexao->prepare("
    SELECT h.id_historico, h.tipo_transacao, h.data_transacao, l.titulo, l.autor 
    FROM historico h 
    JOIN livros l ON h.id_livro = l.id_livro 
    WHERE h.id_usuario = ?
    ORDER BY h.data_transacao DESC
");
$stmt->bind_param("i", $usuario['id_usuario']);
$stmt->execute();
$resultado = $stmt->get_result();

$historico = [];
while ($linha = $resultado->fetch_assoc()) {
    $historico[] = $linha;
}

http_response_code(200);
echo json_encode($historico);
