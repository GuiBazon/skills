<?php
require_once 'conexao.php';
$usuario = validarToken($conexao);

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $resultado = $conexao->query("SELECT * FROM livros");
    $livros = [];
    while ($linha = $resultado->fetch_assoc()) {
        $livros[] = $linha;
    }
    http_response_code(200);
    echo json_encode($livros);
    exit;
}

if ($metodo === 'POST') {
    if ($usuario['tipo'] !== 'admin') {
        http_response_code(401);
        echo json_encode(["mensagem" => "Acesso negado. Apenas admin."]);
        exit;
    }

    $dados = lerInputJSON();
    $titulo = $dados['titulo'] ?? '';
    $autor = $dados['autor'] ?? '';
    $categoria = $dados['categoria'] ?? '';
    $ano = $dados['ano_publicacao'] ?? null;
    $isbn = $dados['isbn'] ?? '';
    $estoque = $dados['quantidade_estoque'] ?? 0;
    $descricao = $dados['descricao'] ?? '';
    $disponivel = isset($dados['disponivel']) && $dados['disponivel'] ? 1 : 0;

    $id_livro = $_GET['id'] ?? null;

    if ($id_livro) {
        $stmt = $conexao->prepare("UPDATE livros SET titulo=?, autor=?, categoria=?, ano_publicacao=?, isbn=?, quantidade_estoque=?, descricao=?, disponivel=? WHERE id_livro=?");
        $stmt->bind_param("sssisssii", $titulo, $autor, $categoria, $ano, $isbn, $estoque, $descricao, $disponivel, $id_livro);
    } else {
        $stmt = $conexao->prepare("INSERT INTO livros (titulo, autor, categoria, ano_publicacao, isbn, quantidade_estoque, descricao, disponivel) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisssi", $titulo, $autor, $categoria, $ano, $isbn, $estoque, $descricao, $disponivel);
    }

    $stmt->execute();
    http_response_code(200);

    echo json_encode([
        "id" => $id_livro ? $id_livro : $conexao->insert_id,
        "titulo" => $titulo,
        "autor" => $autor,
        "categoria" => $categoria,
        "ano_publicacao" => $ano,
        "isbn" => $isbn,
        "quantidade_estoque" => $estoque,
        "descricao" => $descricao,
        "disponivel" => (bool)$disponivel,
        "created_at" => date('Y-m-d H:i:s')
    ]);
}
