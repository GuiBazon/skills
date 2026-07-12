# ZooSystem Management — Guia do Treinador (versão decoreba)

Objetivo: fazer o competidor decorar UM fluxo único, sem variações, que já cobre 100% da pontuação (49.99 pts). Nada de arquitetura bonita. Nada de "solução ideal". Só o caminho mais curto que passa em todos os critérios.

---

## 1. Estrutura fixa (decorar exatamente assim, sempre)

```
/XXX_modulo_A
├── index.php
├── register.php
├── login.php
├── logout.php
├── animals.php          (lista /animals)
├── animal-new.php
├── animal-edit.php
├── animal-delete.php
├── animals.json.php      (API pública + busca)
├── db.php
├── functions.php
├── assets/
│   ├── style.css
│   └── script.js
├── animals/
│   └── excluded_animals/
└── README.md
```

Não criar `api/` separado, não criar `includes/` separado. Um `functions.php` só, um `db.php` só. Quanto menos arquivo, menos chance de esquecer de dar `require`.

---

## 2. Ordem de execução em prova (cronometrada, 3h)

| Tempo | Bloco | Pontos em jogo |
|---|---|---|
| 0:00–0:15 | Banco (importar script, criar `db.php`) | 3.00 |
| 0:15–0:40 | Cadastro de usuário completo | 6.50 |
| 0:40–1:10 | Login + sessão + modal de inatividade | 7.00 |
| 1:10–1:20 | index.php (tela inicial) | parte dos 2.50 |
| 1:20–2:30 | CRUD de animais (lista, novo, editar, excluir, upload) | 17.25 |
| 2:30–2:50 | Modal de visualização + filtros | 5.00 |
| 2:50–3:15 | API pública (JSON, paginação, ordenação, filtros) | 4.75 |
| 3:15–3:35 | Busca por nome (JSON + notice) | 4.00 |
| 3:35–3:45 | Revisão do README + estrutura de pastas | 2.50 |
| 3:45–fim | Sobra = revisão geral | — |

**Regra de ouro:** gerenciamento de animais vale mais que login + cadastro juntos. Se sobrar pouco tempo, sacrifique estética, nunca sacrifique CRUD de animais.

---

## 3. Simplificações que NÃO tiram pontos

- **Senha:** usar sempre `hash('sha256', $senha)`. Nunca `password_hash()` — é mais forte, mas o edital pede exatamente "SHA-256 ou superior"; SHA-256 puro é a opção mais rápida de digitar e já atende.
- **Sessão:** um único `session_start()` no topo de cada página protegida + um `require 'auth.php'` viraria arquivo a mais — em vez disso, cole o mesmo bloco de 3 linhas no topo de cada página. Repetir código aqui é mais rápido que abstrair.
- **Inatividade:** não usar bibliotecas de timer. É só `setTimeout` + `setInterval` em JS puro resetado em `document.onmousemove`/`onkeydown`. Um único bloco de ~15 linhas em `script.js`, reutilizado sem alteração.
- **Upload:** sempre salvar como `animals/{slug-do-nome}/1.png`, `2.png`... Nunca usar o nome original do arquivo. Isso já resolve "nomenclatura" e "índice de envio" ao mesmo tempo.
- **Slug:** uma função só, reaproveitada sempre:
  ```php
  function slugify($texto) {
      $texto = strtolower($texto);
      $texto = preg_replace('/[áàãâä]/u', 'a', $texto);
      $texto = preg_replace('/[éèêë]/u', 'e', $texto);
      $texto = preg_replace('/[íìîï]/u', 'i', $texto);
      $texto = preg_replace('/[óòõôö]/u', 'o', $texto);
      $texto = preg_replace('/[úùûü]/u', 'u', $texto);
      $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
      return trim($texto, '-');
  }
  ```
- **Cores de risco de extinção:** não usar classes CSS separadas por status. Usar UM array PHP de mapeamento e imprimir `style="background:{cor}"` direto na `<td>`. Menos CSS pra decorar.
  ```php
  $cores = [
    'Criticamente em perigo' => '#ff0000',
    'Em perigo' => '#ff8c00',
    'Vulnerável' => '#ffd700',
    'Seguro' => '#00a651'
  ];
  $acronimos = [
    'Criticamente em perigo' => 'CR',
    'Em perigo' => 'EN',
    'Vulnerável' => 'VU',
    'Seguro' => 'LC'
  ];
  ```
  Esses dois arrays resolvem legenda de cor E acrônimo da API ao mesmo tempo — decore só uma vez, use nos dois lugares.
- **Ordenação da listagem admin:** um único `ORDER BY` resolve tudo, sem PHP extra:
  ```sql
  ORDER BY 
    CASE status
      WHEN 'Em exposição' THEN 1
      WHEN 'Fora de exibição' THEN 2
      WHEN 'Em adaptação' THEN 3
    END,
    nome ASC
  ```
  (repare: o edital fala "alfabética e após status" mas o exemplo de exibição mostra status como agrupador — na dúvida, agrupe por status primeiro, depois alfabética dentro do grupo, que é o padrão mais comum pedido nesse tipo de prova. Se sobrar tempo, teste a leitura alternativa.)
- **Modal de visualização/carrossel:** não usar biblioteca de carrossel. É só um `<div>` com imagens e dois botões JS que trocam `display:none/block` num índice. 10 linhas de JS, reaproveitável.
- **Paginação da API:** usar sempre `LIMIT`/`OFFSET` com `page` fixo em 10 por página. Não complicar com parâmetro de tamanho de página — o edital não pede isso.
- **excluded_animals:** ao deletar, faça em 3 passos sempre na mesma ordem: (1) INSERT na tabela `excluded_animals` com os dados do animal, (2) mover a pasta com `rename()`, (3) `DELETE` do animal original. Decore essa ordem — nunca inverta, senão perde a referência do id antes de copiar.

---

## 4. Cola de código (2 páginas — o que decorar de olhos fechados)

### Conexão
```php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=zoodata;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

### Sessão / proteção de página
```php
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
```

### Login
```php
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && $user['senha'] === hash('sha256', $senha)) {
    $_SESSION['user_id'] = $user['id'];
    header('Location: animals.php');
    exit;
} else {
    $erro = 'Email ou Senha de acesso inválidos';
}
```

### Cadastro com verificação de e-mail duplicado
```php
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $erro = 'Esse e-mail já está cadastrado!';
} elseif ($senha !== $confirmar) {
    $erro = 'As senhas são divergentes';
} else {
    $stmt = $pdo->prepare('INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)');
    $stmt->execute([$nome, $email, hash('sha256', $senha)]);
    header('Location: login.php');
    exit;
}
```

### Upload múltiplo
```php
$slug = slugify($nome);
$pasta = "animals/$slug";
if (!is_dir($pasta)) mkdir($pasta, 0777, true);

foreach ($_FILES['imagens']['tmp_name'] as $i => $tmp) {
    $ext = pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION);
    move_uploaded_file($tmp, "$pasta/" . ($i + 1) . ".$ext");
}
```

### Listagem com filtros dinâmicos (admin)
```php
$where = [];
$params = [];

if (!empty($_GET['nome'])) {
    $where[] = '(nome LIKE ? OR nome_cientifico LIKE ?)';
    $params[] = '%' . $_GET['nome'] . '%';
    $params[] = '%' . $_GET['nome'] . '%';
}
if (!empty($_GET['categoria'])) {
    $where[] = 'category_id = ?';
    $params[] = $_GET['categoria'];
}
// repita o padrão acima para risco e status

$sql = 'SELECT * FROM animals';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY status_ordem, nome ASC'; // ajuste conforme item 3

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$animais = $stmt->fetchAll();
```

### API JSON pública (padrão de resposta)
```php
header('Content-Type: application/json');

$sort = ($_GET['sort_by'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';
$sql = "SELECT * FROM animals WHERE status != 'Fora de exibição'";
// adicionar filtros max_size, min_size, max_weight, min_weight, category_id, risk aqui
$sql .= " ORDER BY visitas $sort LIMIT ? OFFSET ?";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, 10, PDO::PARAM_INT);
$stmt->bindValue(2, ($page - 1) * 10, PDO::PARAM_INT);
$stmt->execute();
$animais = $stmt->fetchAll();

$resposta = ['data' => [], 'pagination' => [...]];
foreach ($animais as $a) {
    $item = [
        'name' => ['common' => $a['nome'], 'scientific' => $a['nome_cientifico']],
        'description' => $a['descricao'],
        'measures' => ['size' => (float)$a['tamanho'], 'weight' => (float)$a['peso']],
        'feed_class' => $a['classificacao'],
        'extinction_risk' => [
            'description' => $a['risco'],
            'acronym' => $acronimos[$a['risco']]
        ]
    ];
    $resposta['data'][] = $item;
}
echo json_encode($resposta);
```

### Busca por nome (com notice e incremento de visita)
```php
$stmt = $pdo->prepare('SELECT * FROM animals WHERE nome = ?');
$stmt->execute([$nomeComum]);
$a = $stmt->fetch();

$pdo->prepare('UPDATE animals SET visitas = visitas + 1 WHERE id = ?')->execute([$a['id']]);

$resp = [
    'name' => ['common' => $a['nome'], 'scientific' => $a['nome_cientifico']],
    'description' => $a['descricao'],
    'measures' => ['size' => (float)$a['tamanho'], 'weight' => (float)$a['peso']],
    'feed_class' => $a['classificacao'],
    'extinction_risk' => ['description' => $a['risco'], 'acronym' => $acronimos[$a['risco']]],
    'pictures' => glob("animals/{$slug}/*")
];

if ($a['risco'] === 'Criticamente em perigo') {
    $resp['notice'] = 'Este animal está criticamente ameaçado de extinção';
}
echo json_encode($resp);
```

### Modal de inatividade (JS puro)
```js
let timer;
function resetar() {
  clearTimeout(timer);
  timer = setTimeout(mostrarModal, 60000);
}
function mostrarModal() {
  document.getElementById('modalInativo').style.display = 'block';
  let t = 10;
  const contagem = setInterval(() => {
    t--;
    document.getElementById('contador').innerText = t;
    if (t <= 0) { clearInterval(contagem); window.location = 'logout.php'; }
  }, 1000);
}
document.onmousemove = resetar;
document.onkeydown = resetar;
resetar();
```

---

## 5. Sequência de decoreba (MEMORIZAR.md)

1. Importar banco `zoodata`
2. `db.php` → conexão PDO
3. `register.php` → validações + hash + redirect login
4. `login.php` → sessão + mensagem de erro
5. Bloco de proteção de sessão em toda página admin
6. JS de inatividade (colar sem alterar)
7. `index.php` → mensagem + botões
8. `animals.php` → lista + ordenação + cores + filtros
9. `animal-new.php` → form + upload + slug
10. `animal-edit.php` → form parcial + trocar imagens
11. `animal-delete.php` → confirmação + mover pasta + insert excluded_animals
12. Modal de visualização + carrossel + incrementa visita
13. `animals.json.php` → listagem pública + paginação + sort + filtros
14. Busca por nome + notice
15. README.md com instruções de execução

---

## 6. Onde competidor perde ponto fácil (revisar nos últimos 10 min)

- Esqueceu campo `notice` só para "Criticamente em perigo"
- Esqueceu de excluir "Fora de exibição" da API pública
- Esqueceu de mover a pasta de imagens ao excluir (não só o registro do banco)
- Editou campos que não deveriam ser editáveis (nome/nome científico no edit)
- Nome de pasta com acento ou maiúscula
- Senha salva em texto puro por esquecimento de `hash()`
- Banco de dados renomeado ou tabela alterada (nunca fazer isso)
- Faltou `header('Content-Type: application/json')` na API

---

Se seguir essa ordem, sem inventar nada além do pedido, dá pra fechar os 49.99 pontos com o mínimo de código possível.
