# ZooSystem Management — Versão de treino (decorável em 2 dias)

## O que mudou da versão "profissional" pra essa

| Antes | Agora | Por quê |
|---|---|---|
| `config/db.php` + `includes/functions.php` (2 arquivos) | `init.php` (1 arquivo) | Um único `require` pra memorizar em toda página |
| Tabela `animal_images` com JOIN | Coluna `images` (texto: `"1.jpg,2.jpg"`) | Menos SQL, menos JOIN, menos INSERT em loop |
| Modal via `fetch()` + JSON | Modal via `?view=id` (recarrega a página) | Zero JavaScript assíncrono pra lembrar |
| Exclusão via `fetch()` + JSON | `<form>` normal com `confirm()` do navegador | Mesmo resultado, sem JS extra |
| Slug com tabela de acentos (10+ linhas) | `iconv(...,'ASCII//TRANSLIT')` (1 linha) | Uma função pronta do PHP faz o trabalho |
| Validação de decimais com regex | `number_format()` | Não precisa lembrar regex sob pressão |

**O resultado:** o mesmo edital, os mesmos pontos, só que agora **90% do código é o mesmo bloco copiado e colado**.

## Os 5 blocos que você precisa saber de cor

### 1. Início de toda página
```php
<?php
require 'init.php';   // (ou '../init.php' dentro de /api)
requireLogin();       // só nas páginas do painel
```

### 2. Padrão de formulário (cadastro/login/novo animal/editar)
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. pega os campos do $_POST
    // 2. valida (if vazio / mb_strlen / filter_var)
    // 3. faz o INSERT ou UPDATE
    // 4. redirect('destino')
}
```
Esse `if` se repete em `register.php`, `login.php`, `new-animal.php`, `edit-animal.php`. **É sempre a mesma estrutura.**

### 3. Padrão de listagem com filtro
```php
$sql = "SELECT ... WHERE 1=1";
$params = [];
if ($filtro) { $sql .= " AND coluna = ?"; $params[] = $filtro; }
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
```
Usado em `animals.php` e nos dois arquivos da API.

### 4. Upload de imagem
```php
uploadImages($_FILES['images'], slug($nome));
// devolve "1.jpg,2.jpg" pra salvar direto na coluna images
```

### 5. Resposta JSON da API
```php
header('Content-Type: application/json; charset=utf-8');
echo json_encode([...], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
```

## Como treinar
1. **Dia 1**: decore `init.php` de cabo a rabo (é o único arquivo 100% original). Depois escreva `register.php` e `login.php` do zero, sem olhar — eles usam o mesmo padrão do bloco 2.
2. **Dia 1 (tarde)**: escreva `animals.php` (listagem + filtro + modal) e o `.htaccess`. Decore as 6 linhas de rewrite.
3. **Dia 2**: escreva `new-animal.php`, copie e adapte pra `edit-animal.php` (tire os campos que não podem ser editados). Depois os dois arquivos da API — são o bloco 3 + bloco 5.
4. Treine cronometrado: o projeto inteiro deve sair em menos de 90 minutos pra sobrar tempo de sobra pra debugar na hora da prova.

## Rotas (não muda nada da versão anterior)
```
/                          welcome
/register  /login  /logout
/animals                   lista + filtros + modal (?view=id)
/animals/new
/animals/edit/{id}
/animals/delete/{id}       (POST, form com confirm())
/animals.json               API pública paginada
/animals.json/{nome}        API pública por nome
```

## Rodando
1. Importe `database/schema.sql` (cria o banco `zoodata` já com os selects populados).
2. Ajuste usuário/senha do MySQL em `init.php` se precisar.
3. Copie a pasta pro `htdocs` como `XXX_modulo_A` (troque XXX pelo seu CFP), com `mod_rewrite` ligado.
4. `uploads/animals` precisa de permissão de escrita.

## O que continua igual e não pode ser cortado (pontuação)
- SHA-256 na senha, e-mail único, senhas mascaradas com botão de mostrar.
- Sessão ativa não repete login; modal de inatividade 60s + timer 10s.
- Ordenação alfabética + por status; cores de risco (CR vermelho / EN laranja / VU amarelo / LC verde).
- Campos editáveis restritos a Descrição, Tamanho, Peso, Status e Imagens.
- Exclusão grava em `excluded_animals` e move a pasta de imagens.
- API: paginação de 10, `sort_by`, filtros por tamanho/peso/categoria/risco, exclui "Fora de exibição", campo `notice` só pra CR.
