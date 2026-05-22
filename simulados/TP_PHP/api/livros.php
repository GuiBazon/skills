<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$usuario = validarToken($conexao);
$dados = lerInputJSON();

$titulo = $dados['titulo'] ?? '';
$autor = $dados['autor'] ?? '';
$qtd = $dados['quantidade_livros'] ?? '';

if (empty($titulo) || empty($qtd) || empty($autor)) {
    http_response_code(400);
    echo json_encode(["mensagem" => "Dados obrigatórios faltando"]);
    exit;
}

$stmt = $conexao->prepare("SELECT id_livro, quantidade_estoque FROM livros WHERE titulo = ? AND autor = ? AND disponivel = 1");
$stmt->bind_param("ss", $titulo, $autor);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["mensagem" => "Livro não encontrado ou indisponível"]);
    exit;
}

$livro = $resultado->fetch_assoc();

if ($livro['quantidade_estoque'] < $qtd) {
    http_response_code(400);
    echo json_encode(["mensagem" => "Estoque insuficiente"]);
    exit;
}

$novo_estoque = $livro['quantidade_estoque'] - $qtd;
$disponivel = $novo_estoque > 0 ? 1 : 0;

$stmtUpdate = $conexao->prepare("UPDATE livros SET quantidade_estoque = ?, disponivel = ? WHERE id_livro = ?");
$stmtUpdate->bind_param("iii", $novo_estoque, $disponivel, $livro['id_livro']);
$stmtUpdate->execute();

$stmtHist = $conexao->prepare("INSERT INTO historico (id_usuario, id_livro, tipo_transacao) VALUES (?, ?, 'emprestimo')");
$stmtHist->bind_param("ii", $usuario['id_usuario'], $livro['id_livro']);
$stmtHist->execute();

http_response_code(200);
echo json_encode(["mensagem" => "Empréstimo realizado com sucesso"]);

