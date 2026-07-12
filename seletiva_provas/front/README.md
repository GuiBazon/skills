# Gestão de Treinos — Módulo A/B (SPSkills 2026)

Implementação enxuta em **HTML + CSS + JS puro**, pensada para ser reproduzida do zero em até 3h de prova.

## Como rodar
Abra `index.html` direto no navegador (Firefox Developer Edition ou Chrome). Não precisa de servidor, build ou dependências.

## Estrutura (3 arquivos, sem subpastas)
```
index.html   -> estrutura (cabeçalho, painel de fotos, heatmap, sidebar)
style.css    -> tudo estilizado, variáveis --cor1/--cor2/--cor3 controlam o tema
script.js    -> toda a lógica (tema, fotos, grid, check-in, heatmap)
```

## Checklist de requisitos (todos implementados)
1. [x] Seletor de modalidade (Ballet/Natação/Tênis) — troca cor do site na hora
2. [x] Paleta de cada esporte aplicada via `--cor1/--cor2/--cor3`
3. [x] Upload de fotos por clique (botão) e por drag-and-drop nativo
4. [x] Grid CSS de 5 slots (grande/largo/pequeno/alto/médio) nas posições exatas do PDF
5. [x] Estilo por tema: Tênis = rotação -5°/5° + legenda sobreposta; Ballet = rotação + borda branca + legenda abaixo; Natação = só cantos arredondados, sem legenda, sem rotação
6. [x] Sidebar retrátil à direita com aba "CHECK-IN" sempre visível
7. [x] Formulário de check-in: Modalidade, Data, Data Hora Início, Data Hora Fim, Local
8. [x] Validação: início/fim/data no mesmo dia (mensagem de erro em vermelho)
9. [x] Sucesso salvo em verde + gravação no LocalStorage
10. [x] Lista "Últimos registros" + botão "Limpar registros" com mensagem de vazio
11. [x] Heatmap estático de 365 dias de 2026, 7 linhas (Seg a Dom), sempre visível mesmo sem dados
12. [x] 3 níveis de cor (até 4h / 4h-8h / +8h) + legenda
13. [x] Seletor de cor base do heatmap (padrão / cor1 / cor2 / cor3 da paleta)
14. [x] Tooltip ao passar o mouse: data em DD/MM/AAAA + horas do dia

## Fluxo do JavaScript
- **Tema**: `trocaTema()` seta as 3 CSS vars no `:root`, troca textos e chama `desenharGrid()`.
- **Fotos**: `adicionarFoto()` lê o arquivo com `FileReader` e guarda em `fotos[proximoSlot]` (array circular de 5 posições). `desenharGrid()` redesenha os 5 `<div class="slot">` aplicando rotação/borda/legenda conforme `temaAtivo`.
- **Check-in**: `btnSalvarCheckin` valida (mesmo dia + fim > início), calcula horas com `(new Date(fim) - new Date(inicio)) / 3600000`, e grava um array JSON em `localStorage.checkins`.
- **Heatmap**: `criarHeatmap()` gera 365 `<div class="dia">` com `grid-auto-flow: column` (7 linhas fixas = 7 dias da semana, colunas = semanas). `atualizarHeatmap()` soma as horas de cada check-in por data e aplica a classe `nivel1/2/3`, usando a cor oficial da modalidade daquele treino.

## Trechos mais importantes para memorizar
- **Grid mosaico** (a parte que mais pontua, 11.60):
  ```css
  .slot-grande  { grid-column: 1;     grid-row: 1 / 3; }
  .slot-largo   { grid-column: 2 / 4; grid-row: 1;     }
  .slot-pequeno { grid-column: 2;     grid-row: 2;     }
  .slot-alto    { grid-column: 4;     grid-row: 1 / 3; }
  .slot-medio   { grid-column: 1 / 3; grid-row: 3;     }
  ```
- **Heatmap com grid-auto-flow** (12.15 pontos, a maior nota):
  ```css
  #heatmapGrid { display:grid; grid-template-rows: repeat(7,14px); grid-auto-flow: column; grid-auto-columns: 14px; }
  ```
- **Cálculo de horas entre dois datetime-local**:
  ```js
  var horas = (new Date(fim) - new Date(inicio)) / 3600000;
  ```
- **Troca de tema via CSS custom properties**:
  ```js
  document.documentElement.style.setProperty('--cor1', t.cor1);
  ```
- **Padrão de validação "mesmo dia"**: comparar os 10 primeiros caracteres (`YYYY-MM-DD`) do `datetime-local` com o campo `date`.

## Observação sobre publicação
Segundo o PDF, o projeto deve ficar acessível em `http://xxx/XXX_module_B/` (substituindo XXX pelo CFP da unidade). Bastaria colocar estes 3 arquivos nessa pasta no servidor da prova — não há dependência de porta nem de build.
