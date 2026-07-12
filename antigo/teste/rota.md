# 🚀 Rota de Estudos Super Otimizada: Prova TP_B (Módulo B)
## Como Memorizar e Fazer a Prova Inteira de Cabeça em 6 Dias

Para passar na prova fazendo tudo de cabeça, o segredo é **escrever o mínimo de código possível**. Este roteiro removeu todo o código desnecessário, estilos complexos e lógicas redundantes, focando unicamente no que o avaliador (CIS) vai testar. 

---

## 📅 Cronograma Ultra Rápido

- **Dia 1:** Banco de Dados MySQL (`db.sql`) e Conexão PDO (`db.php`)
- **Dia 2:** Roteamento Inteligente Sem Regex (`.htaccess` e `index.php`)
- **Dia 3:** Painel de Empresas & Soft Delete (`companies.php` e `company_new.php`)
- **Dia 4:** Painel de Produtos & Upload (`products.php` e `product_new.php`)
- **Dia 5:** APIs JSON em 15 linhas (`api_list.php` e `api_detail.php`)
- **Dia 6:** Páginas Públicas (`public_verify.php` e `public_detail.php`)
- **Dia 7:** Entrega e Documentação (`expert_readme.txt` e Diagrama ER)

---

## 🗄️ DIA 1: Banco de Dados MySQL e Conexão PDO
**Objetivo:** Criar a estrutura mínima com relacionamentos e a conexão em apenas 5 linhas.

### 📝 Código para memorizar:

#### 1. Banco de Dados (`db.sql`)
```sql
CREATE DATABASE IF NOT EXISTS ws_module_b;
USE ws_module_b;

CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255), address TEXT, telephone VARCHAR(50), email VARCHAR(255),
    owner_name VARCHAR(255), owner_mobile VARCHAR(50), owner_email VARCHAR(255),
    contact_name VARCHAR(255), contact_mobile VARCHAR(50), contact_email VARCHAR(255),
    deactivated TINYINT(1) DEFAULT 0
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT,
    gtin VARCHAR(14) UNIQUE,
    name_en VARCHAR(255), name_fr VARCHAR(255),
    description_en TEXT, description_fr TEXT,
    brand VARCHAR(255), country VARCHAR(100) DEFAULT 'France',
    weight_gross DECIMAL(10,2), weight_net DECIMAL(10,2), weight_unit VARCHAR(10),
    image_path VARCHAR(255) DEFAULT 'placeholder.png',
    hidden TINYINT(1) DEFAULT 0,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX (gtin)
);
```

#### 2. Conexão PDO (`db.php`)
```php
<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=ws_module_b", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die($e->getMessage());
}
```

---

## 🌐 DIA 2: Roteamento Sem Regex (Fácil de Lembrar)
**Objetivo:** Redirecionar todas as requisições para `index.php` e tratar rotas dinâmicas dividindo a URL em partes com a função `explode()`.

### 📝 Código para memorizar:

#### 1. `.htaccess`
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### 2. Roteador Central (`index.php`)
```php
<?php
session_start();
require 'db.php';

// Limpa o caminho removendo a subpasta de execução
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $path);
$path = '/' . trim($path, '/');

// Proteção de rotas admin (B1/B4) -> Retorna 401
function check_auth() {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        exit("401 Unauthorized");
    }
}

switch ($path) {
    case '/login': require 'login.php'; break;
    case '/logout': session_destroy(); header('Location: login'); exit;
    case '/companies': check_auth(); require 'companies.php'; break;
    case '/companies/new': check_auth(); require 'company_new.php'; break;
    case '/products': check_auth(); require 'products.php'; break;
    case '/products/new': check_auth(); require 'product_new.php'; break;
    case '/products.json': require 'api_list.php'; break;
    case '/gtin-verify': require 'public_verify.php'; break;
    default:
        // Divide o path em partes usando a barra: /products/1234567890123 -> ["", "products", "1234567890123"]
        $parts = explode('/', $path);
        
        if ($parts[1] === 'products' && isset($parts[2])) {
            $gtin = $parts[2];
            if (str_contains($gtin, '.json')) {
                $_GET['gtin'] = str_replace('.json', '', $gtin);
                require 'api_detail.php';
            } else {
                check_auth();
                $_GET['gtin'] = $gtin;
                require 'product_detail.php';
            }
        } elseif ($parts[1] === '01' && isset($parts[2])) {
            $_GET['gtin'] = $parts[2];
            require 'public_detail.php';
        } else {
            header('Location: login');
        }
        break;
}
```

#### 3. Autenticação Simples (`login.php`)
```php
<?php
if (isset($_POST['pass']) && $_POST['pass'] === 'admin') {
    $_SESSION['user'] = true;
    header('Location: companies');
    exit;
}
?>
<form method="POST">
    Senha: <input type="password" name="pass">
    <button>Entrar</button>
</form>
```

---

## 🏢 DIA 3: Empresas & Transação de Soft Delete
**Objetivo:** Listar e criar empresas. Desativar empresas aplicando transações (deactivating a company hides all its products).

### 📝 Código para memorizar:

#### 1. Painel de Empresas (`companies.php`)
```php
<?php
$deact = isset($_GET['type']) && $_GET['type'] === 'deactivated' ? 1 : 0;
$q = $db->prepare("SELECT * FROM companies WHERE deactivated = ?");
$q->execute([$deact]);
$list = $q->fetchAll();

// Lógica de Desativação em Lote (Transactions)
if (isset($_GET['deactivate'])) {
    try {
        $db->beginTransaction();
        $db->prepare("UPDATE companies SET deactivated = 1 WHERE id = ?")->execute([$_GET['deactivate']]);
        $db->prepare("UPDATE products SET hidden = 1 WHERE company_id = ?")->execute([$_GET['deactivate']]);
        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
    }
    header('Location: companies');
    exit;
}
?>
<h1>Empresas</h1>
<a href="companies/new">Nova</a> | 
<a href="companies">Ativas</a> | 
<a href="companies?type=deactivated">Desativadas</a>
<table border="1">
    <?php foreach ($list as $c): ?>
        <tr>
            <td><?= $c['name'] ?></td>
            <td><?= $c['telephone'] ?></td>
            <td>
                <?php if (!$deact): ?>
                    <a href="companies?deactivate=<?= $c['id'] ?>">Desativar (Soft Delete)</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
```

#### 2. Cadastro de Empresa (`company_new.php`)
```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO companies (name, address, telephone, email, owner_name, owner_mobile, owner_email, contact_name, contact_mobile, contact_email) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $db->prepare($sql)->execute([
        $_POST['n'], $_POST['a'], $_POST['t'], $_POST['e'],
        $_POST['on'], $_POST['om'], $_POST['oe'],
        $_POST['cn'], $_POST['cm'], $_POST['ce']
    ]);
    header('Location: companies');
    exit;
}
?>
<form method="POST">
    Nome: <input name="n"><br> Endereço: <input name="a"><br> Tel: <input name="t"><br> Email: <input name="e"><br>
    Dono Nome: <input name="on"><br> Dono Cel: <input name="om"><br> Dono Email: <input name="oe"><br>
    Contato Nome: <input name="cn"><br> Contato Cel: <input name="cm"><br> Contato Email: <input name="ce"><br>
    <button>Salvar</button>
</form>
```

---

## 📦 DIA 4: CRUD de Produtos, Imagem e Validação
**Objetivo:** Cadastro de produtos com validação de GTIN e upload de imagem, e exclusão permitida apenas se o produto estiver oculto.

### 📝 Código para memorizar:

#### 1. Painel de Produtos (`products.php`)
```php
<?php
$list = $db->query("SELECT * FROM products")->fetchAll();

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $p = $db->prepare("SELECT * FROM products WHERE id = ?");
    $p->execute([$id]);
    $prod = $p->fetch();

    // Regra: Só deleta fisicamente se estiver oculto/empresa inativa (hidden = 1)
    if ($prod && $prod['hidden'] == 1) {
        if ($prod['image_path'] !== 'placeholder.png' && file_exists($prod['image_path'])) {
            unlink($prod['image_path']);
        }
        $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    }
    header('Location: products');
    exit;
}
?>
<h1>Produtos</h1>
<a href="products/new">Novo Produto</a>
<table border="1">
    <?php foreach ($list as $p): ?>
        <tr>
            <td><?= $p['name_en'] ?></td>
            <td><?= $p['gtin'] ?></td>
            <td>
                <?php if ($p['hidden']): ?>
                    <a href="products?delete=<?= $p['id'] ?>">Excluir Permanentemente</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
```

#### 2. Criação de Produto (`product_new.php`)
```php
<?php
$companies = $db->query("SELECT id, name FROM companies WHERE deactivated = 0")->fetchAll();
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gtin = trim($_POST['gtin']);
    
    // Validação GTIN (Somente números, 13 ou 14 dígitos e único)
    if (!ctype_digit($gtin) || !in_array(strlen($gtin), [13, 14])) {
        $err = "GTIN inválido.";
    } else {
        $check = $db->prepare("SELECT id FROM products WHERE gtin = ?");
        $check->execute([$gtin]);
        if ($check->fetch()) $err = "GTIN duplicado.";
    }

    if (!$err) {
        $img = 'placeholder.png';
        if (isset($_FILES['img']) && $_FILES['img']['error'] === 0) {
            $img = 'uploads/' . time() . '_' . $_FILES['img']['name'];
            move_uploaded_file($_FILES['img']['tmp_name'], $img);
        }

        $sql = "INSERT INTO products (company_id, gtin, name_en, name_fr, description_en, description_fr, brand, weight_gross, weight_net, weight_unit, image_path) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $db->prepare($sql)->execute([
            $_POST['company_id'], $gtin, $_POST['name_en'], $_POST['name_fr'], $_POST['desc_en'], $_POST['desc_fr'],
            $_POST['brand'], $_POST['weight_gross'], $_POST['weight_net'], $_POST['weight_unit'], $img
        ]);
        header('Location: products');
        exit;
    }
}
?>
<?php if ($err) echo "<p style='color:red'>$err</p>"; ?>
<form method="POST" enctype="multipart/form-data">
    Empresa: <select name="company_id"><?php foreach($companies as $c): ?><option value="<?=$c['id']?>"><?=$c['name']?></option><?php endforeach; ?></select><br>
    GTIN: <input name="gtin"><br>
    Nome (EN): <input name="name_en"><br> Nome (FR): <input name="name_fr"><br>
    Desc (EN): <textarea name="desc_en"></textarea><br> Desc (FR): <textarea name="desc_fr"></textarea><br>
    Marca: <input name="brand"><br>
    Peso Bruto: <input name="weight_gross"><br> Peso Líquido: <input name="weight_net"><br>
    Unidade Peso: <input name="weight_unit"><br>
    Imagem: <input type="file" name="img"><br>
    <button>Salvar</button>
</form>
```

---

## 🔌 DIA 5: APIs JSON com Paginação e Filtros
**Objetivo:** Retornar os produtos da API respeitando filtros e a paginação obrigatória de 10 por página, e erro 404 para produtos ocultos/inexistentes.

### 📝 Código para memorizar:

#### 1. API Lista (`api_list.php`)
```php
<?php
header('Content-Type: application/json');

$query = $_GET['query'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$sql = "SELECT p.*, c.name as c_name, c.address as c_addr, c.telephone as c_tel, c.email as c_email,
               c.owner_name, c.owner_mobile, c.owner_email, c.contact_name, c.contact_mobile, c.contact_email
        FROM products p JOIN companies c ON p.company_id = c.id WHERE p.hidden = 0";

$params = [];
if ($query) {
    $sql .= " AND (p.name_en LIKE ? OR p.name_fr LIKE ? OR p.description_en LIKE ? OR p.description_fr LIKE ?)";
    $params = array_fill(0, 4, "%$query%");
}

// Total de itens para a paginação
$stmtCount = $db->prepare("SELECT COUNT(*) FROM ($sql) as temp");
$stmtCount->execute($params);
$total = (int)$stmtCount->fetchColumn();
$totalPages = ceil($total / $limit);

$sql .= " LIMIT $limit OFFSET $offset";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$data = [];
foreach ($products as $p) {
    $data[] = [
        "name" => ["en" => $p['name_en'], "fr" => $p['name_fr']],
        "description" => ["en" => $p['description_en'], "fr" => $p['description_fr']],
        "gtin" => $p['gtin'], "brand" => $p['brand'], "countryOfOrigin" => $p['country'],
        "weight" => ["gross" => (float)$p['weight_gross'], "net" => (float)$p['weight_net'], "unit" => $p['weight_unit']],
        "company" => [
            "companyName" => $p['c_name'], "companyAddress" => $p['c_addr'], "companyTelephone" => $p['c_tel'], "companyEmail" => $p['c_email'],
            "owner" => ["name" => $p['owner_name'], "mobileNumber" => $p['owner_mobile'], "email" => $p['owner_email']],
            "contact" => ["name" => $p['contact_name'], "mobileNumber" => $p['contact_mobile'], "email" => $p['contact_email']]
        ]
    ];
}

$url = "http://$_SERVER[HTTP_HOST]" . explode('?', $_SERVER['REQUEST_URI'])[0];
echo json_encode([
    "data" => $data,
    "pagination" => [
        "current_page" => $page, "total_pages" => $totalPages, "per_page" => $limit,
        "next_page_url" => $page < $totalPages ? "$url?page=" . ($page+1) . ($query ? "&query=$query" : "") : null,
        "prev_page_url" => $page > 1 ? "$url?page=" . ($page-1) . ($query ? "&query=$query" : "") : null
    ]
], JSON_PRETTY_PRINT);
```

#### 2. API Detalhes por GTIN (`api_detail.php`)
```php
<?php
header('Content-Type: application/json');
$gtin = $_GET['gtin'] ?? '';

$stmt = $db->prepare("SELECT p.*, c.name as c_name, c.address as c_addr, c.telephone as c_tel, c.email as c_email,
                              c.owner_name, c.owner_mobile, c.owner_email, c.contact_name, c.contact_mobile, c.contact_email
                       FROM products p JOIN companies c ON p.company_id = c.id WHERE p.gtin = ? AND p.hidden = 0");
$stmt->execute([$gtin]);
$p = $stmt->fetch();

if (!$p) {
    http_response_code(404);
    echo json_encode(["error" => "Product not found"]);
    exit;
}

echo json_encode([
    "name" => ["en" => $p['name_en'], "fr" => $p['name_fr']],
    "description" => ["en" => $p['description_en'], "fr" => $p['description_fr']],
    "gtin" => $p['gtin'], "brand" => $p['brand'], "countryOfOrigin" => $p['country'],
    "weight" => ["gross" => (float)$p['weight_gross'], "net" => (float)$p['weight_net'], "unit" => $p['weight_unit']],
    "company" => [
        "companyName" => $p['c_name'], "companyAddress" => $p['c_addr'], "companyTelephone" => $p['c_tel'], "companyEmail" => $p['c_email'],
        "owner" => ["name" => $p['owner_name'], "mobileNumber" => $p['owner_mobile'], "email" => $p['owner_email']],
        "contact" => ["name" => $p['contact_name'], "mobileNumber" => $p['contact_mobile'], "email" => $p['contact_email']]
    ]
], JSON_PRETTY_PRINT);
```

---

## 👥 DIA 6: Páginas Públicas e Idiomas
**Objetivo:** Implementar a validação em massa via Textarea e a página de visualização do produto com layout responsivo mínimo e chaveamento dinâmico de idioma (`<html lang="...">`).

### 📝 Código para memorizar:

#### 1. Verificação Pública em Lote (`public_verify.php`)
```php
<?php
$results = [];
$allValid = true;
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;
    $lines = explode("\n", $_POST['gtins'] ?? '');
    foreach ($lines as $line) {
        $gtin = trim($line);
        if (!$gtin) continue;

        $stmt = $db->prepare("SELECT id FROM products WHERE gtin = ? AND hidden = 0");
        $stmt->execute([$gtin]);
        $valid = (bool)$stmt->fetch();
        
        if (!$valid) $allValid = false;
        $results[$gtin] = $valid;
    }
}
?>
<form method="POST">
    GTINs (um por linha):<br>
    <textarea name="gtins" rows="5"></textarea><br>
    <button>Validar</button>
</form>

<?php if ($submitted): ?>
    <?php if ($allValid && $results): ?>
        <h2 style="color:green">✔ Todos os válidos</h2>
    <?php endif; ?>
    <ul>
        <?php foreach ($results as $gtin => $val): ?>
            <li><?= htmlspecialchars($gtin) ?>: <span style="color:<?= $val ? 'green' : 'red' ?>"><?= $val ? 'Válido' : 'Inválido' ?></span></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
```

#### 2. Visualização do Produto Pública (`public_detail.php`)
```php
<?php
$gtin = $_GET['gtin'] ?? '';
$lang = $_GET['lang'] ?? 'en';
if ($lang !== 'en' && $lang !== 'fr') $lang = 'en';

$stmt = $db->prepare("SELECT p.*, c.name as c_name FROM products p JOIN companies c ON p.company_id = c.id WHERE p.gtin = ? AND p.hidden = 0");
$stmt->execute([$gtin]);
$p = $stmt->fetch();

if (!$p) {
    http_response_code(404);
    exit("404 - Not Found");
}

$name = $lang === 'fr' ? $p['name_fr'] : $p['name_en'];
$desc = $lang === 'fr' ? $p['description_fr'] : $p['description_en'];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($name) ?></title>
</head>
<body>
    <a href="?lang=en">EN</a> | <a href="?lang=fr">FR</a>
    <div style="max-width:500px; margin:auto">
        <img src="<?= htmlspecialchars($p['image_path']) ?>" style="width:100%"><br>
        <h1><?= htmlspecialchars($name) ?></h1>
        <p>GTIN: <?= htmlspecialchars($p['gtin']) ?></p>
        <p>Marca: <?= htmlspecialchars($p['brand']) ?></p>
        <p>Empresa: <?= htmlspecialchars($p['c_name']) ?></p>
        <p>Descrição: <?= htmlspecialchars($desc) ?></p>
        <p>Peso Líquido: <?= (float)$p['weight_net'] ?> <?= htmlspecialchars($p['weight_unit']) ?></p>
        <p>Peso Bruto:  <?= (float)$p['weight_gross'] ?> <?= htmlspecialchars($p['weight_unit']) ?></p>
    </div>
</body>
</html>
```

---

## 📝 DIA 7: Entregáveis e Checklist de Ouro

1. **`expert_readme.txt` na Raiz:**
   ```text
   Candidato: [Nome]
   Assento: XX
   Importe o 'db.sql' e configure o 'db.php' (host=localhost, user=root, pass="").
   Login admin em /login usando senha 'admin'.
   ```
2. **Diagrama ER:**
   Use o MySQL Workbench para gerar a imagem a partir das tabelas físicas (`Database -> Reverse Engineer`) e salve em `docs/diagrama_er.png`.
3. **Simulado Contra o Relógio (Meta: < 2 Horas):**
   Delete tudo (menos a pasta `docs` e `rota.md`) e tente refazer o roteador (`index.php`), conexões, listagem de empresas, produtos e a API JSON de cabeça. Repita esse processo de reconstrução do zero pelo menos 3 vezes antes do dia da prova.
