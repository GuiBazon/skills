# ZooSystem Management — Módulo B (Tecnologias Web)

## Como rodar
1. Suba o `schema.sql` (pasta `database/`) no MySQL — cria o banco **zoodata**, tabelas e seeds de categorias/classificação alimentar/riscos.
2. Ajuste credenciais em `config/db.php` se necessário.
3. Coloque a pasta do projeto em `htdocs`/`www` como `XXX_modulo_A` (troque `XXX` pelo CFP da unidade). É necessário **mod_rewrite** habilitado (`.htaccess` já incluso) — no XAMPP/WAMP habilite `AllowOverride All`.
4. `uploads/animals` e `uploads/animals/excluded_animals` precisam de permissão de escrita.
5. Acesse `http://{ip}/XXX_modulo_A/`.

## Rotas implementadas
- `/` — página inicial (Acessar / Cadastrar)
- `/register` — cadastro (POST valida tudo no servidor)
- `/login` — autenticação, sessão PHP, redireciona se já autenticado
- `/logout` — encerra sessão
- `/animals` — painel (lista, filtros, ordenação)
- `/animals/new` — cadastro de animal (todos os campos)
- `/animals/edit/{id}` — edição (apenas Descrição, Tamanho, Peso, Status, Imagens)
- `/animals/view/{id}` (AJAX) — modal com carrossel, incrementa visita
- `/animals/delete/{id}` (POST) — remove com confirmação, move para `excluded_animals`
- `/animals.json` — API pública paginada (10/página), filtros (`max_size`, `min_size`, `max_weight`, `min_weight`, `category_id`, `risk`), ordenação por visitas via `sort_by=asc|desc`
- `/animals.json/{nome-comum}` — busca por nome, incrementa visita, `notice` quando `risk = CR`

> O documento da prova cita `XXX_modulo_A` para o site/admin e `XX_modulo_zoo` para a API pública — tratamos como o **mesmo projeto** (mesma pasta), já que a API é parte do mesmo sistema REST descrito na introdução. Se a banca exigir pastas físicas separadas, basta duplicar o conteúdo de `api/` para uma segunda pasta `XX_modulo_zoo/` com seu próprio `.htaccess`.

## Decisões técnicas (para pontuar rápido, sem framework)
- **Roteamento**: `.htaccess` + arquivos "flat" (sem router customizado) — simples e direto.
- **Senha**: `hash('sha256', $senha)` — segue literalmente o enunciado ("Sha-256, ou superior").
- **Slug de imagens**: minúsculas, sem acento, espaço vira `-` (`slugify()` em `includes/functions.php`).
- **Ordenação da listagem admin**: `ORDER BY FIELD(status, 'em_exposicao','fora_de_exibicao','em_adaptacao'), nome ASC`.
- **Cores de risco**: CR vermelho, EN laranja, VU amarelo, LC verde (`risk_color()`).
- **Inatividade**: JS puro, 60s sem interação → modal com timer de 10s (`assets/script.js`).
- **Exclusão**: registro copiado para `excluded_animals` (com `images` em JSON) e pasta física movida para `uploads/animals/excluded_animals/{slug}`.

## Estrutura
```
index.php / login.php / register.php / logout.php
animals.php / new-animal.php / edit-animal.php / view-animal.php / delete-animal.php
api/animals.php  (listagem pública)
api/animal.php   (busca por nome)
config/db.php
includes/functions.php
database/schema.sql
assets/style.css / assets/script.js
uploads/animals/{slug}/...
uploads/animals/excluded_animals/{slug}/...
```
