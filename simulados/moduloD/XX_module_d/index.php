<?php
/**
 * index.php – Lyon Mobile Web Service
 * WorldSkills Module D
 *
 * Gera a página HTML completa com CSS e JS embutidos.
 * O front-end consome a API mock em api.php via fetch.
 */

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no,viewport-fit=cover">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Lyon MWS">
<link rel="manifest" href="<?= $basePath ?>/manifest.json">
<title>Lyon Mobile Web Service</title>
<style>
/* ==========================================================================
   Reset & Variáveis CSS
   ========================================================================== */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root{
  --primary:#1c3e60;
  --primary-light:#2a5a8a;
  --bg:#ffffff;
  --text:#1a1a1a;
  --card-bg:#f5f5f5;
  --border:#e0e0e0;
  --shadow:0 2px 8px rgba(0,0,0,.08);
  --radius:12px;
  --header-h:56px;
  --nav-h:56px;
  --safe-bottom:env(safe-area-inset-bottom,0px);
}

body.dark-theme{
  --bg:#121212;
  --text:#e0e0e0;
  --card-bg:#1e1e1e;
  --border:#333;
  --shadow:0 2px 8px rgba(0,0,0,.3);
}

/* ==========================================================================
   Layout Base
   ========================================================================== */
html,body{height:100%;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;background:var(--bg);color:var(--text);overflow:hidden;transition:background .3s,color .3s}

/* Header fixo */
#app-header{
  position:fixed;top:0;left:0;right:0;height:var(--header-h);
  background:var(--primary);color:#fff;
  display:flex;align-items:center;padding:0 16px;gap:10px;
  z-index:100;
}

#back-btn{
  display:none;align-items:center;justify-content:center;
  width:36px;height:36px;border:none;border-radius:50%;
  background:rgba(255,255,255,.2);color:#fff;
  font-size:20px;cursor:pointer;flex-shrink:0;
  transition:background .2s;
}
#back-btn:focus-visible{outline:2px solid #fff;outline-offset:2px}
#back-btn:hover{background:rgba(255,255,255,.35)}

#view-title{font-size:18px;font-weight:600;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* Main content – scrollável */
#main-content{
  position:fixed;top:var(--header-h);left:0;right:0;
  bottom:calc(var(--nav-h) + var(--safe-bottom));
  overflow-y:auto;overflow-x:hidden;-webkit-overflow-scrolling:touch;
  scroll-behavior:smooth;
}

/* Bottom navigation */
#bottom-nav{
  position:fixed;bottom:0;left:0;right:0;
  height:calc(var(--nav-h) + var(--safe-bottom));
  padding-bottom:var(--safe-bottom);
  background:var(--primary);display:flex;justify-content:space-around;align-items:center;
  z-index:100;
}
#bottom-nav button{
  flex:1;height:100%;border:none;background:transparent;color:rgba(255,255,255,.6);
  font-size:12px;font-weight:500;cursor:pointer;display:flex;flex-direction:column;
  align-items:center;justify-content:center;gap:2px;transition:color .2s,background .2s;
  position:relative;
}
#bottom-nav button::before{
  content:'';display:block;width:24px;height:24px;border-radius:50%;
  background:currentColor;mask-size:contain;mask-repeat:no-repeat;mask-position:center;
  opacity:.7;
}
#bottom-nav button[data-view="carparks"]::before{mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M5 13h14v8H5zm0-2l7-9 7 9H5z'/%3E%3C/svg%3E") center/contain no-repeat}
#bottom-nav button[data-view="events"]::before{mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z'/%3E%3C/svg%3E") center/contain no-repeat}
#bottom-nav button[data-view="weather"]::before{mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79 1.42-1.41zM4 10.5H1v2h3v-2zm9-9.95h-2V3.5h2V.55zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41 1.79-1.79zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79-1.4 1.4zM20 10.5v2h3v-2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h2V19.5h-2v2.95zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41-1.79 1.8z'/%3E%3C/svg%3E") center/contain no-repeat}
#bottom-nav button[data-view="settings"]::before{mask:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58a.49.49 0 0 0 .12-.61l-1.92-3.32a.49.49 0 0 0-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54a.484.484 0 0 0-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58a.49.49 0 0 0-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z'/%3E%3C/svg%3E") center/contain no-repeat}

#bottom-nav button.active{color:#fff}
#bottom-nav button.active::before{opacity:1}
#bottom-nav button:focus-visible{outline:2px solid #fff;outline-offset:-2px}

/* ==========================================================================
   Views
   ========================================================================== */
.view{display:none;padding:16px;min-height:100%}
.view.active{display:block}

/* ==========================================================================
   D2 – Carpark Cards
   ========================================================================== */
.carpark-card{
  display:flex;align-items:center;gap:12px;
  background:var(--card-bg);border-radius:var(--radius);
  padding:14px 16px;margin-bottom:10px;
  box-shadow:var(--shadow);cursor:pointer;
  transition:transform .2s,box-shadow .2s;
}
.carpark-card:focus-within{outline:2px solid var(--primary);outline-offset:2px}
.carpark-card:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.12)}

.carpark-info{flex:1;min-width:0}
.carpark-info h3{font-size:15px;font-weight:600;margin-bottom:4px;color:var(--text)}
.carpark-info p{font-size:13px;color:var(--text);opacity:.75}
.carpark-info .distance{font-size:12px;margin-top:2px}

.pin-btn{
  width:40px;height:40px;border:none;border-radius:50%;
  background:transparent;font-size:20px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:transform .2s,background .2s;flex-shrink:0;
}
.pin-btn:hover{background:rgba(0,0,0,.05);transform:scale(1.15)}
.pin-btn.pinned{transform:scale(1.1)}
.pin-btn:focus-visible{outline:2px solid var(--primary);outline-offset:2px}

/* ==========================================================================
   D2 – Focus Mode
   ========================================================================== */
.focus-card{
  background:var(--card-bg);border-radius:var(--radius);
  padding:24px;box-shadow:var(--shadow);text-align:center;
}
.focus-card h2{font-size:22px;font-weight:700;margin-bottom:16px;color:var(--text)}
.focus-card p{font-size:16px;margin-bottom:10px;color:var(--text);opacity:.85}
.focus-bar{
  width:100%;height:24px;background:var(--border);
  border-radius:12px;overflow:hidden;margin-top:16px;
}
.focus-bar-fill{
  height:100%;background:var(--primary);
  border-radius:12px;transition:width 1s ease;
}

/* ==========================================================================
   D3 – Events
   ========================================================================== */
.date-filters{
  display:flex;gap:12px;margin-bottom:16px;
}
.date-filters label{
  flex:1;font-size:13px;color:var(--text);opacity:.8;
}
.date-filters input{
  display:block;width:100%;margin-top:4px;
  padding:8px 10px;border:1px solid var(--border);
  border-radius:8px;font-size:14px;
  background:var(--bg);color:var(--text);
}
.date-filters input:focus-visible{outline:2px solid var(--primary);outline-offset:2px}

.event-card{
  background:var(--card-bg);border-radius:var(--radius);
  overflow:hidden;margin-bottom:12px;box-shadow:var(--shadow);
}
.event-card img{
  width:100%;height:160px;object-fit:cover;display:block;
}
.event-info{padding:12px 14px}
.event-info h3{font-size:15px;font-weight:600;margin-bottom:4px;color:var(--text)}
.event-date{font-size:13px;color:var(--text);opacity:.7}

/* Loader */
.loader{
  text-align:center;padding:16px;font-size:14px;color:var(--text);opacity:.5;
}
.loader::after{
  content:'';display:inline-block;width:20px;height:20px;
  margin-left:8px;border:2px solid var(--primary);
  border-top-color:transparent;border-radius:50%;
  animation:spin .8s linear infinite;vertical-align:middle;
}
@keyframes spin{to{transform:rotate(360deg)}}

/* ==========================================================================
   D4 – Weather
   ========================================================================== */
.weather-container{
  display:flex;gap:10px;padding:4px 0 12px;
  overflow-x:auto;scroll-snap-type:x mandatory;
  -webkit-overflow-scrolling:touch;
}
.weather-day{
  flex:0 0 120px;scroll-snap-align:start;
  background:var(--card-bg);border-radius:var(--radius);
  padding:16px 10px;text-align:center;box-shadow:var(--shadow);
}
.weather-date{font-size:12px;font-weight:600;margin-bottom:8px;color:var(--text)}
.weather-temp{font-size:24px;font-weight:700;margin:6px 0 4px;color:var(--text)}
.weather-condition{font-size:11px;color:var(--text);opacity:.7;line-height:1.2}

/* Animação SVG do clima (D4) */
@keyframes dash{
  0%{stroke-dasharray:50;stroke-dashoffset:200}
  100%{stroke-dasharray:200;stroke-dashoffset:0}
}
.weather-day svg{
  display:block;margin:0 auto;
  stroke:#1c3e60;stroke-width:1;fill:none;
}
.weather-day:hover svg,
.weather-day:focus-within svg{
  animation:dash 2s ease forwards;
}

/* ==========================================================================
   D5 – Settings
   ========================================================================== */
.setting-group{
  margin-bottom:24px;
}
.setting-group label{
  display:block;font-size:14px;font-weight:600;margin-bottom:8px;color:var(--text);
}
.setting-group select{
  width:100%;padding:10px 12px;border:1px solid var(--border);
  border-radius:8px;font-size:14px;background:var(--bg);color:var(--text);
  cursor:pointer;appearance:auto;
}
.setting-group select:focus-visible{outline:2px solid var(--primary);outline-offset:2px}

/* ==========================================================================
   Acessibilidade
   ========================================================================== */
:focus-visible{outline:2px solid var(--primary);outline-offset:2px}
::selection{background:var(--primary);color:#fff}
@media(prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:.01ms!important;transition-duration:.01ms!important}}
</style>
</head>
<body>

<!-- ======== HEADER FIXO ======== -->
<header id="app-header">
  <button id="back-btn" aria-label="Voltar">←</button>
  <h1 id="view-title">Estacionamentos</h1>
</header>

<!-- ======== CONTEÚDO PRINCIPAL ======== -->
<main id="main-content">

  <!-- D2 – Estacionamentos -->
  <section id="view-carparks" class="view active" aria-label="Lista de estacionamentos">
    <div id="carparks-list"></div>
  </section>

  <!-- D3 – Eventos -->
  <section id="view-events" class="view" aria-label="Lista de eventos">
    <div class="date-filters">
      <label>Início
        <input type="date" id="date-start" aria-label="Data inicial">
      </label>
      <label>Fim
        <input type="date" id="date-end" aria-label="Data final">
      </label>
    </div>
    <div id="events-list"></div>
    <div id="events-loader" class="loader" style="display:none">Carregando</div>
  </section>

  <!-- D4 – Clima -->
  <section id="view-weather" class="view" aria-label="Previsão do tempo">
    <div id="weather-container" class="weather-container"></div>
  </section>

  <!-- D5 – Configurações -->
  <section id="view-settings" class="view" aria-label="Configurações">
    <div class="setting-group">
      <label for="theme-select">Tema</label>
      <select id="theme-select">
        <option value="light">Claro</option>
        <option value="dark">Escuro</option>
        <option value="system">Seguir sistema</option>
      </select>
    </div>
    <div class="setting-group">
      <label for="sort-select">Ordenação dos estacionamentos</label>
      <select id="sort-select">
        <option value="alpha">Alfabética</option>
        <option value="distance">Distância</option>
      </select>
    </div>
  </section>

  <!-- D2 – Modo Foco -->
  <section id="view-focus" class="view" aria-label="Detalhes do estacionamento">
    <div id="focus-content"></div>
  </section>

</main>

<!-- ======== BOTTOM NAVIGATION ======== -->
<nav id="bottom-nav" aria-label="Navegação principal">
  <button data-view="carparks" class="active" aria-label="Estacionamentos">Estacionamentos</button>
  <button data-view="events"     aria-label="Eventos">Eventos</button>
  <button data-view="weather"    aria-label="Clima">Clima</button>
  <button data-view="settings"   aria-label="Configurações">Configurações</button>
</nav>

<script>
/* ==========================================================================
   Lyon Mobile Web Service – Front-end
   WorldSkills Module D
   ========================================================================== */

// ---- Constantes ----
const BASE_PATH = <?= json_encode($basePath) ?>;

// ---- Função Haversine (D2) ----
function getDistance(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

// ---- Aplicação Principal ----
const App = {
  /* Estado */
  currentView: 'carparks',
  userLat: 45.764043,   // fallback
  userLng: 4.835659,    // fallback
  carparks: [],
  eventsPage: 1,
  pagesNext: null,
  loadedEventIds: new Set(),
  isLoadingEvents: false,
  focusCarparkId: null,
  focusInterval: null,

  // ---- Inicialização (D1) ----
  async init() {
    await this.determineLocation();
    this.loadPreferences();
    this.setupListeners();
    await this.loadCarparks();
  },

  // ---- Geolocalização (D2) ----
  async determineLocation() {
    // 1) Parâmetros na URL
    const params = new URLSearchParams(location.search);
    const lat = params.get('latitude');
    const lng = params.get('longitude');
    if (lat && lng) {
      this.userLat = parseFloat(lat);
      this.userLng = parseFloat(lng);
      return;
    }
    // 2) navigator.geolocation
    if (navigator.geolocation) {
      try {
        const pos = await new Promise((resolve, reject) =>
          navigator.geolocation.getCurrentPosition(resolve, reject, {
            timeout: 5000, enableHighAccuracy: false
          })
        );
        this.userLat = pos.coords.latitude;
        this.userLng = pos.coords.longitude;
      } catch (_) { /* fallback mantido */ }
    }
  },

  // ---- Event Listeners ----
  setupListeners() {
    // Bottom nav
    document.getElementById('bottom-nav').addEventListener('click', e => {
      const btn = e.target.closest('button');
      if (btn) this.switchView(btn.dataset.view);
    });

    // Botão voltar (modo foco)
    document.getElementById('back-btn').addEventListener('click', () => {
      this.switchView('carparks');
    });

    // Filtros de data dos eventos
    document.getElementById('date-start').addEventListener('change', () => this.resetEvents());
    document.getElementById('date-end').addEventListener('change', () => this.resetEvents());

    // Scroll infinito para eventos
    document.getElementById('main-content').addEventListener('scroll', () => {
      if (this.currentView !== 'events' || this.isLoadingEvents || !this.pagesNext) return;
      const el = document.getElementById('main-content');
      if (el.scrollHeight - el.scrollTop - el.clientHeight < 200) {
        this.loadEvents();
      }
    });

    // Tema: seguir sistema
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      const saved = localStorage.getItem('theme') || 'light';
      if (saved === 'system') this.applyTheme('system');
    });

    // Configurações – tema
    document.getElementById('theme-select').addEventListener('change', e => {
      const val = e.target.value;
      localStorage.setItem('theme', val);
      this.applyTheme(val);
    });

    // Configurações – ordenação
    document.getElementById('sort-select').addEventListener('change', e => {
      localStorage.setItem('sortMode', e.target.value);
      this.renderCarparks();
    });
  },

  // ---- Troca de Views (D1) ----
  switchView(view) {
    // Limpar intervalo do foco ao sair
    if (this.currentView === 'focus' && view !== 'focus') {
      clearInterval(this.focusInterval);
      this.focusInterval = null;
      this.focusCarparkId = null;
    }

    this.currentView = view;

    // Ativar view
    document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
    const target = document.getElementById('view-' + view);
    if (target) target.classList.add('active');

    // Atualizar bottom nav
    document.querySelectorAll('#bottom-nav button').forEach(b => b.classList.remove('active'));
    const navBtn = document.querySelector(`#bottom-nav button[data-view="${view}"]`);
    if (navBtn) navBtn.classList.add('active');

    // Atualizar título e botão voltar
    const titles = {
      carparks: 'Estacionamentos',
      events: 'Eventos',
      weather: 'Clima',
      settings: 'Configurações',
      focus: 'Detalhes'
    };
    document.getElementById('view-title').textContent = titles[view] || 'Lyon MWS';
    document.getElementById('back-btn').style.display = view === 'focus' ? 'flex' : 'none';

    // Carregar dados da view
    if (view === 'carparks') this.renderCarparks();
    if (view === 'events') this.resetEvents();
    if (view === 'weather') this.loadWeather();
    if (view === 'settings') this.loadPreferences();

    document.getElementById('main-content').scrollTop = 0;
  },

  // ===================================================================
  // D2 – Carpark (3.0 pts)
  // ===================================================================
  async loadCarparks() {
    try {
      const r = await fetch(BASE_PATH + '/api.php/carparks.json');
      this.carparks = await r.json();
      this.renderCarparks();
    } catch (e) {
      console.error('Erro ao carregar estacionamentos:', e);
      document.getElementById('carparks-list').innerHTML =
        '<p style="text-align:center;padding:40px;opacity:.6">Erro ao carregar estacionamentos.</p>';
    }
  },

  renderCarparks() {
    const pinnedIds = this.getPinned();
    const sortMode = localStorage.getItem('sortMode') || 'alpha';

    // Adicionar distância
    let list = this.carparks.map(c => ({
      ...c,
      dist: getDistance(this.userLat, this.userLng, c.latitude, c.longitude)
    }));

    // Ordenar: fixados primeiro, depois pelo critério escolhido
    list.sort((a, b) => {
      const aP = pinnedIds.includes(a.id);
      const bP = pinnedIds.includes(b.id);
      if (aP && !bP) return -1;
      if (!aP && bP) return 1;
      return sortMode === 'alpha'
        ? a.name.localeCompare(b.name, 'fr')
        : a.dist - b.dist;
    });

    const html = list.map(c => `
      <div class="carpark-card" tabindex="0" role="button"
           data-id="${c.id}"
           onclick="App.openFocus(${c.id})"
           onkeydown="if(event.key==='Enter')App.openFocus(${c.id})"
           aria-label="${c.name}, ${c.available} de ${c.total} vagas disponíveis, ${c.dist.toFixed(1)} km">
        <div class="carpark-info">
          <h3>${c.name}</h3>
          <p>${c.available}/${c.total} disponíveis</p>
          <p class="distance">${c.dist.toFixed(1)} km</p>
        </div>
        <button class="pin-btn ${pinnedIds.includes(c.id) ? 'pinned' : ''}"
                onclick="event.stopPropagation();App.togglePin(${c.id})"
                aria-label="${pinnedIds.includes(c.id) ? 'Desafixar' : 'Fixar'} ${c.name}"
                title="${pinnedIds.includes(c.id) ? 'Desafixar' : 'Fixar'}">📌</button>
      </div>
    `).join('');

    document.getElementById('carparks-list').innerHTML =
      html || '<p style="text-align:center;padding:40px;opacity:.6">Nenhum estacionamento encontrado.</p>';
  },

  getPinned() {
    return JSON.parse(localStorage.getItem('pinnedIds') || '[]');
  },

  togglePin(id) {
    let pinned = this.getPinned();
    if (pinned.includes(id)) {
      pinned = pinned.filter(p => p !== id);
    } else {
      pinned.unshift(id);
    }
    localStorage.setItem('pinnedIds', JSON.stringify(pinned));
    this.renderCarparks();
  },

  // ---- Modo Foco (D2) ----
  openFocus(id) {
    const c = this.carparks.find(x => x.id === id);
    if (!c) return;

    this.focusCarparkId = id;
    this.renderFocus(c);
    this.switchView('focus');

    // Atualizar a cada 10 segundos
    if (this.focusInterval) clearInterval(this.focusInterval);
    this.focusInterval = setInterval(() => this.refreshFocus(), 10000);
  },

  renderFocus(c) {
    const dist = getDistance(this.userLat, this.userLng, c.latitude, c.longitude);
    const pct = Math.min(100, (c.available / c.total) * 100);
    document.getElementById('focus-content').innerHTML = `
      <div class="focus-card">
        <h2>${c.name}</h2>
        <p>${dist.toFixed(1)} km de distância</p>
        <p>${c.available} / ${c.total} vagas disponíveis</p>
        <div class="focus-bar" role="progressbar" aria-valuenow="${c.available}"
             aria-valuemin="0" aria-valuemax="${c.total}">
          <div class="focus-bar-fill" style="width:${pct}%"></div>
        </div>
      </div>
    `;
  },

  async refreshFocus() {
    if (!this.focusCarparkId) return;
    try {
      const r = await fetch(BASE_PATH + '/api.php/carparks.json');
      const data = await r.json();
      const updated = data.find(x => x.id === this.focusCarparkId);
      if (!updated) return;
      // Atualizar cache
      const idx = this.carparks.findIndex(x => x.id === this.focusCarparkId);
      if (idx !== -1) this.carparks[idx] = updated;
      this.renderFocus(updated);
    } catch (_) { /* silencioso */ }
  },

  // ===================================================================
  // D3 – Events (3.25 pts)
  // ===================================================================
  resetEvents() {
    this.eventsPage = 1;
    this.pagesNext = null;
    this.loadedEventIds = new Set();
    this.isLoadingEvents = false;
    document.getElementById('events-list').innerHTML = '';
    document.getElementById('events-loader').style.display = 'none';
    this.loadEvents();
  },

  async loadEvents() {
    if (this.isLoadingEvents) return;
    this.isLoadingEvents = true;

    const loader = document.getElementById('events-loader');
    loader.style.display = 'block';

    const start = document.getElementById('date-start').value;
    const end = document.getElementById('date-end').value;

    let url = BASE_PATH + `/api.php/events.json?page=${this.eventsPage}`;
    if (start) url += '&beginning_date=' + encodeURIComponent(start);
    if (end) url += '&ending_date=' + encodeURIComponent(end);

    try {
      const r = await fetch(url);
      const json = await r.json();

      // Filtrar duplicatas
      const novos = json.data.filter(e => !this.loadedEventIds.has(e.id));
      novos.forEach(e => this.loadedEventIds.add(e.id));

      this.pagesNext = json.pages.next;
      this.eventsPage++;

      const container = document.getElementById('events-list');
      for (const e of novos) {
        const div = document.createElement('div');
        div.className = 'event-card';
        const d = new Date(e.date);
        const dataStr = d.toLocaleDateString('fr-FR', {
          day: 'numeric', month: 'long', year: 'numeric'
        });
        div.innerHTML = `
          <img src="${e.image}" alt="${e.title}" loading="lazy">
          <div class="event-info">
            <h3>${e.title}</h3>
            <p class="event-date">${dataStr}</p>
          </div>
        `;
        container.appendChild(div);
      }

      if (!json.data.length && this.eventsPage === 2) {
        container.innerHTML =
          '<p style="text-align:center;padding:40px;opacity:.6">Nenhum evento encontrado.</p>';
      }
    } catch (e) {
      console.error('Erro ao carregar eventos:', e);
    } finally {
      this.isLoadingEvents = false;
      loader.style.display = 'none';
    }
  },

  // ===================================================================
  // D4 – Weather (1.75 pts)
  // ===================================================================
  async loadWeather() {
    try {
      const r = await fetch(BASE_PATH + '/api.php/weather.json');
      const data = await r.json();
      const container = document.getElementById('weather-container');
      container.innerHTML = data.map(d => {
        const dt = new Date(d.date);
        const label = dt.toLocaleDateString('fr-FR', { weekday: 'short', day: 'numeric' });
        return `
          <div class="weather-day" tabindex="0" aria-label="${d.condition}, ${d.temp}°C">
            <p class="weather-date">${label}</p>
            <svg viewBox="0 0 100 100" width="60" height="60"
                 stroke="#1c3e60" stroke-width="1" fill="none"
                 aria-hidden="true" focusable="false">
              <path d="${d.svgPath}" />
            </svg>
            <p class="weather-temp">${d.temp}°C</p>
            <p class="weather-condition">${d.condition}</p>
          </div>
        `;
      }).join('');
    } catch (e) {
      console.error('Erro ao carregar clima:', e);
    }
  },

  // ===================================================================
  // D5 – Settings (2.25 pts)
  // ===================================================================
  loadPreferences() {
    const theme = localStorage.getItem('theme') || 'light';
    document.getElementById('theme-select').value = theme;
    this.applyTheme(theme);

    const sort = localStorage.getItem('sortMode') || 'alpha';
    document.getElementById('sort-select').value = sort;
  },

  applyTheme(theme) {
    document.body.classList.remove('dark-theme', 'light-theme');

    if (theme === 'dark') {
      document.body.classList.add('dark-theme');
    } else if (theme === 'light') {
      document.body.classList.add('light-theme');
    } else {
      // Seguir sistema
      const dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      document.body.classList.add(dark ? 'dark-theme' : 'light-theme');
    }
  }
};

// ---- Inicializar ----
document.addEventListener('DOMContentLoaded', () => App.init());
</script>
</body>
</html>
