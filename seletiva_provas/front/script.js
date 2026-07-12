// ===== dados dos 3 temas (paleta oficial de cada esporte) =====
var temas = {
  tenis:   { nome: 'Tênis',   cor1: '#9DE00E', cor2: '#183059', cor3: '#F4F5F7' },
  ballet:  { nome: 'Ballet',  cor1: '#F2C1C1', cor2: '#693e65', cor3: '#FDF7F7' },
  natacao: { nome: 'Natação', cor1: '#00B4D8', cor2: '#03045E', cor3: '#F0F8FF' }
};

var temaAtivo = 'tenis';

// fotos dos 5 slots do mosaico, na ordem: grande, largo, pequeno, alto, medio
var fotos = [null, null, null, null, null];
var proximoSlot = 0;

// ===================================================================
// troca o tema
// muda as variáveis CSS de cor, o título e redesenha o grid de fotos
// ===================================================================
function trocaTema(tema) {
  temaAtivo = tema;
  var t = temas[tema];

  document.documentElement.style.setProperty('--cor1', t.cor1);
  document.documentElement.style.setProperty('--cor2', t.cor2);
  document.documentElement.style.setProperty('--cor3', t.cor3);

  document.getElementById('nomeEsporte').textContent = t.nome;
  document.getElementById('nomeEsporte2').textContent = t.nome;
  document.getElementById('selectModalidade').value = tema;

  var botoes = document.querySelectorAll('#seletorModalidade button');
  for (var i = 0; i < botoes.length; i++) {
    botoes[i].classList.toggle('ativo', botoes[i].dataset.tema === tema);
  }

  desenharGrid();
}

var botoesTema = document.querySelectorAll('#seletorModalidade button');
for (var i = 0; i < botoesTema.length; i++) {
  botoesTema[i].addEventListener('click', function () {
    trocaTema(this.dataset.tema);
  });
}

// ===================================================================
// adiciona imagem
// lê o arquivo escolhido/arrastado e guarda no próximo slot livre
// ===================================================================
function adicionarFoto(arquivo) {
  var leitor = new FileReader();
  leitor.onload = function (e) {
    fotos[proximoSlot] = {
      url: e.target.result,
      nome: arquivo.name.replace(/\.[^.]+$/, '')
    };
    proximoSlot = (proximoSlot + 1) % 5;
    desenharGrid();
  };
  leitor.readAsDataURL(arquivo);
}

// botão "Adicionar fotos" abre o seletor de arquivos escondido
document.getElementById('btnAdicionarFotos').addEventListener('click', function () {
  document.getElementById('inputArquivo').click();
});
document.getElementById('inputArquivo').addEventListener('change', function (e) {
  for (var i = 0; i < e.target.files.length; i++) adicionarFoto(e.target.files[i]);
});

// drag and drop nativo na área do painel
var dropzone = document.getElementById('dropzone');
dropzone.addEventListener('dragover', function (e) { e.preventDefault(); });
dropzone.addEventListener('drop', function (e) {
  e.preventDefault();
  for (var i = 0; i < e.dataTransfer.files.length; i++) adicionarFoto(e.dataTransfer.files[i]);
});

// ===================================================================
// cria o grid
// desenha os 5 slots do mosaico, aplicando o estilo do tema ativo
// (rotação, borda e posição da legenda mudam de acordo com o esporte)
// ===================================================================
function desenharGrid() {
  var nomesSlots = ['grande', 'largo', 'pequeno', 'alto', 'medio'];
  var grid = document.getElementById('gridFotos');
  grid.innerHTML = '';

  for (var i = 0; i < nomesSlots.length; i++) {
    var slotEl = document.createElement('div');
    slotEl.className = 'slot slot-' + nomesSlots[i];
    var foto = fotos[i];

    if (foto) {
      var img = document.createElement('img');
      img.src = foto.url;

      // tênis e ballet: leve rotação aleatória entre -5 e 5 graus
      if (temaAtivo === 'tenis' || temaAtivo === 'ballet') {
        var angulo = (Math.random() * 10 - 5).toFixed(1);
        img.style.transform = 'rotate(' + angulo + 'deg)';
      }
      // ballet: borda branca ao redor da foto
      if (temaAtivo === 'ballet') {
        img.style.border = '6px solid #fff';
      }
      slotEl.appendChild(img);

      // natação não tem legenda; tênis sobrepõe; ballet fica abaixo
      if (temaAtivo !== 'natacao') {
        var legenda = document.createElement('span');
        legenda.className = 'legenda-foto ' + (temaAtivo === 'tenis' ? 'sobreposta' : 'abaixo');
        legenda.textContent = foto.nome;
        slotEl.appendChild(legenda);
      }
    } else {
      slotEl.textContent = nomesSlots[i].toUpperCase();
    }
    grid.appendChild(slotEl);
  }
}

// ===================================================================
// sidebar retrátil de check-in
// ===================================================================
document.getElementById('abaCheckin').addEventListener('click', function () {
  document.getElementById('sidebarCheckin').classList.toggle('aberta');
});

// ===================================================================
// valida formulário e salva no localStorage
// ===================================================================
function mostrarMensagem(texto, tipo) {
  var msg = document.getElementById('mensagemCheckin');
  msg.textContent = texto;
  msg.className = tipo;
}

document.getElementById('btnSalvarCheckin').addEventListener('click', function () {
  var modalidade = document.getElementById('selectModalidade').value;
  var data = document.getElementById('inputData').value;
  var inicio = document.getElementById('inputInicio').value;
  var fim = document.getElementById('inputFim').value;
  var local = document.getElementById('inputLocal').value;

  if (!data || !inicio || !fim || !local) {
    mostrarMensagem('Preencha todos os campos.', 'erro');
    return;
  }

  var diaInicio = inicio.slice(0, 10);
  var diaFim = fim.slice(0, 10);

  // início, fim e data precisam ser o mesmo dia, e o fim não pode vir antes do início
  if (diaInicio !== data || diaFim !== data || fim <= inicio) {
    mostrarMensagem('Início, Fim e data precisam pertencer ao mesmo dia.', 'erro');
    return;
  }

  var horas = (new Date(fim) - new Date(inicio)) / 3600000;

  // salva no localStorage
  var registros = JSON.parse(localStorage.getItem('checkins') || '[]');
  registros.unshift({ modalidade: modalidade, data: data, inicio: inicio, fim: fim, local: local, horas: horas });
  localStorage.setItem('checkins', JSON.stringify(registros));

  mostrarMensagem('Check-in salvo com sucesso no LocalStorage.', 'sucesso');
  desenharRegistros();
  atualizarHeatmap();
});

document.getElementById('btnLimparRegistros').addEventListener('click', function () {
  localStorage.removeItem('checkins');
  desenharRegistros();
  atualizarHeatmap();
});

function formatarDataBR(iso) {
  var partes = iso.split('-');
  return partes[2] + '/' + partes[1] + '/' + partes[0];
}

function desenharRegistros() {
  var registros = JSON.parse(localStorage.getItem('checkins') || '[]');
  var lista = document.getElementById('listaRegistros');
  lista.innerHTML = '';

  if (registros.length === 0) {
    lista.textContent = 'Nenhum check-in salvo ainda.';
    return;
  }

  for (var i = 0; i < registros.length; i++) {
    var r = registros[i];
    var item = document.createElement('div');
    item.className = 'registro';
    item.innerHTML =
      '<strong>' + temas[r.modalidade].nome + ' • ' + formatarDataBR(r.data) + '</strong><br>' +
      r.horas.toFixed(2) + 'h em ' + r.local + '<br>' +
      r.inicio.replace('T', ' ') + ' até ' + r.fim.replace('T', ' ');
    lista.appendChild(item);
  }
}

// ===================================================================
// desenha heatmap
// monta a grade estática dos 365 dias de 2026 (7 linhas: Seg a Dom)
// ===================================================================
function pad(n) { return n < 10 ? '0' + n : '' + n; }

var listaCelulas = [];

function criarHeatmap() {
  var container = document.getElementById('heatmapGrid');
  container.innerHTML = '';
  listaCelulas = [];

  var primeiroDia = new Date(2026, 0, 1);
  // getDay: 0 = domingo ... 6 = sábado -> convertendo para 0 = segunda ... 6 = domingo
  var offset = (primeiroDia.getDay() + 6) % 7;

  // células vazias antes do dia 1º de janeiro, para alinhar a primeira semana
  for (var i = 0; i < offset; i++) {
    var vazio = document.createElement('div');
    vazio.className = 'dia vazio';
    container.appendChild(vazio);
  }

  // um quadradinho para cada um dos 365 dias de 2026
  for (var d = 0; d < 365; d++) {
    var data = new Date(2026, 0, 1 + d);
    var iso = data.getFullYear() + '-' + pad(data.getMonth() + 1) + '-' + pad(data.getDate());

    var cel = document.createElement('div');
    cel.className = 'dia';
    cel.dataset.data = iso;
    cel.addEventListener('mouseenter', mostrarTooltip);
    cel.addEventListener('mousemove', moverTooltip);
    cel.addEventListener('mouseleave', esconderTooltip);

    container.appendChild(cel);
    listaCelulas.push(cel);
  }
}

// recalcula as horas por dia e pinta cada quadradinho do heatmap
function atualizarHeatmap() {
  var registros = JSON.parse(localStorage.getItem('checkins') || '[]');
  var horasPorDia = {};
  var modalidadePorDia = {};

  for (var i = 0; i < registros.length; i++) {
    var r = registros[i];
    horasPorDia[r.data] = (horasPorDia[r.data] || 0) + r.horas;
    modalidadePorDia[r.data] = r.modalidade;
  }

  var baseSelecionada = document.getElementById('selectCorHeatmap').value;

  for (var c = 0; c < listaCelulas.length; c++) {
    var cel = listaCelulas[c];
    var iso = cel.dataset.data;
    var horas = horasPorDia[iso] || 0;
    var modalidade = modalidadePorDia[iso];

    cel.classList.remove('nivel1', 'nivel2', 'nivel3');
    cel.style.removeProperty('--cor-dia');

    if (horas > 0 && modalidade) {
      // a cor do dia vem da paleta oficial da modalidade daquele treino
      var cor = baseSelecionada === 'padrao' ? temas[modalidade].cor1 : temas[modalidade][baseSelecionada];
      cel.style.setProperty('--cor-dia', cor);

      if (horas <= 4) cel.classList.add('nivel1');
      else if (horas <= 8) cel.classList.add('nivel2');
      else cel.classList.add('nivel3');
    }
  }
}

document.getElementById('selectCorHeatmap').addEventListener('change', atualizarHeatmap);

// tooltip flutuante com a data e o total de horas do dia
function mostrarTooltip(e) {
  var iso = e.target.dataset.data;
  var registros = JSON.parse(localStorage.getItem('checkins') || '[]');
  var horas = 0;
  for (var i = 0; i < registros.length; i++) {
    if (registros[i].data === iso) horas += registros[i].horas;
  }
  var tooltip = document.getElementById('tooltip');
  tooltip.textContent = formatarDataBR(iso) + ' • ' + horas.toFixed(1) + 'h registradas';
  tooltip.classList.remove('escondido');
  moverTooltip(e);
}
function moverTooltip(e) {
  var tooltip = document.getElementById('tooltip');
  tooltip.style.left = (e.pageX + 12) + 'px';
  tooltip.style.top = (e.pageY + 12) + 'px';
}
function esconderTooltip() {
  document.getElementById('tooltip').classList.add('escondido');
}

// ===================================================================
// inicialização da página
// ===================================================================
criarHeatmap();
atualizarHeatmap();
desenharRegistros();
trocaTema('tenis');
