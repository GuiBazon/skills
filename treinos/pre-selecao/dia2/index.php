<?php

// 1. CONFIGURAÇÃO E CONEXÃO COM O BANCO DE DADOS


// Configurações básicas do banco (XAMPP padrão)
$host = 'localhost';
$dbname = 'crud_basico'; // Nome do banco que criamos
$user = 'root'; // Usuário padrão (XAMPP)
$pass = ''; // Senha padrão (vazia) (XAMPP)

try {
    // Tenta criar a conexão usando PDO (Biblioteca moderna do PHP)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    
    // Configura para o PHP avisar se houver erro no banco (Ajuda a achar bugs)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Se der erro na conexão, para tudo e mostra a mensagem
    die("Erro na conexão: " . $e->getMessage());
}


// 2. INICIALIZAÇÃO DE VARIÁVEIS


// Definimos essas variáveis como vazias para não dar erro de "variável não existe" quando a página carrega pela primeira vez (modo de cadastro).
$id = '';
$nome = '';
$email = '';


// 3. LÓGICA DE SALVAR (INSERT OU UPDATE)


// Verifica se o usuário clicou no botão "Salvar" (Enviou o formulário via POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Pega os dados que o usuário digitou nos campos
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $id = $_POST['id']; // Pega o ID escondido (se tiver valor, é edição)

    // Decisão: É para CRIAR ou ATUALIZAR?
    if ($id) {
        // Se TEM id, então é uma ATUALIZAÇÃO (UPDATE)
        // Usamos :n e :e como apelidos para os dados (segurança)
        $sql = $pdo->prepare("UPDATE usuarios SET nome = :n, email = :e WHERE id = :id");
        $sql->bindValue(':id', $id);
    } else {
        // Se NÃO tem id, é um NOVO CADASTRO (INSERT)
        $sql = $pdo->prepare("INSERT INTO usuarios (nome, email) VALUES (:n, :e)");
    }
    
    // Substitui os apelidos (:n, :e) pelos valores reais com segurança
    $sql->bindValue(':n', $nome);
    $sql->bindValue(':e', $email);
    $sql->execute(); // Executa o comando no banco
    
    // Recarrega a página para limpar o formulário e evitar salvar duplicado
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// 4. LÓGICA DE EXCLUIR


// Verifica se tem "excluir" na URL (ex: index.php?excluir=5)
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    // Prepara o comando de deletar e executa direto
    $pdo->prepare("DELETE FROM usuarios WHERE id = :id")->execute([':id' => $id]);
    
    // Recarrega a página para atualizar a lista
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


// 5. LÓGICA DE PREENCHER O FORMULÁRIO (PARA EDITAR)


// Verifica se tem "editar" na URL (ex: index.php?editar=2)
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    // Busca os dados daquele usuário específico no banco
    $sql = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $sql->execute([':id' => $id]);
    
    // Transforma o resultado em um array (lista de dados)
    $usuario = $sql->fetch(PDO::FETCH_ASSOC);
    
    // Preenche as variáveis globais com os dados do banco
    // Isso fará com que o formulário lá embaixo apareça preenchido
    $nome = $usuario['nome'];
    $email = $usuario['email'];
}


// 6. LÓGICA DE LISTAGEM (BUSCAR TODOS)


// Busca TODOS os usuários para mostrar na tabela lá embaixo
$sql = $pdo->query("SELECT * FROM usuarios");
$lista = $sql->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Usuários</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
    </style>
</head>
<body>

    <h1><?php echo $id ? 'Editar Usuário' : 'Cadastrar Novo Usuário'; ?></h1>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo $nome; ?>" required>
        
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $email; ?>" required>
        
        <button type="submit"><?php echo $id ? 'Atualizar' : 'Salvar'; ?></button>
        
        <?php if($id): ?>
            <a href="index.php">Cancelar</a>
        <?php endif; ?>
    </form>

    <hr>

    <h2>Usuários Cadastrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Ações</th>
        </tr>
        
        <?php foreach($lista as $u): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo $u['nome']; ?></td>
            <td><?php echo $u['email']; ?></td>
            <td>
                <a href="?editar=<?php echo $u['id']; ?>">Editar</a>
                
                <a href="?excluir=<?php echo $u['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>