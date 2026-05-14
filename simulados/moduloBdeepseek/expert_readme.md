# Módulo B - Sistema de Gestão de Produtos

## Como executar:
1. Importe o arquivo sql/database_dump.sql no phpMyAdmin (banco: skill17)
2. Configure a conexão em config/database.php (usuário root, senha vazia ou conforme ambiente)
3. Acesse http://localhost/01_module_b/public/login.php
4. Senha: admin

## Funcionalidades implementadas:
- Login com proteção 401
- CRUD de empresas (sem exclusão, com desativação em cascata)
- CRUD de produtos (validação GTIN server-side, upload de imagem, hidden e exclusão permanente)
- API JSON com paginação, busca e 404 correto
- Página pública de verificação em massa de GTIN
- Página pública de produto com troca de idioma (en/fr) e layout responsivo

## Observações:
- Produtos ocultos não aparecem na API e na página pública.
- Desativar empresa oculta todos os seus produtos.