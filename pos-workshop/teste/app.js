// Estado reativo da aplicação
let photosList = [];
let currentIdx = 0;
let runMode = 'manual';
let slideTimer = null;
const intervalTime = 3000;

// Elementos DOM Chave
const stage = document.getElementById('stage');
const modeSelect = document.getElementById('mode-select');
const themeSelect = document.getElementById('theme-select');
const viewport = document.getElementById('slideshow-viewport');
const sortableUl = document.getElementById('photo-sortable-list');

// --- 1. CARREGAMENTO DE IMAGENS & BUFFER DE ARQUIVOS ---
function processFiles(files) {
    Array.from(files).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            photosList.push({ id: crypto.randomUUID(), src: e.target.result, name: file.name.split('.')[0] });
            renderSortingList();
            if (photosList.length === 1) changeSlide(0);
        };
        reader.readAsDataURL(file);
    });
}

// Eventos de Drag and Drop de Arquivos
const dropZone = document.getElementById('drop-zone');
dropZone.addEventListener('dragover', e => e.preventDefault());
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    processFiles(e.dataTransfer.files);
});
document.getElementById('file-fallback').addEventListener('change', e => processFiles(e.target.files));

// Carregamento de Amostras Online
document.getElementById('btn-samples').addEventListener('click', () => {
    photosList = [
        { id: 's1', src: 'https://picsum.photos/1200/800?random=11', name: 'Montanhas Alpinas' },
        { id: 's2', src: 'https://picsum.photos/1200/800?random=22', name: 'Metrópole Cyberpunk' },
        { id: 's3', src: 'https://picsum.photos/1200/800?random=33', name: 'Costa do Oceano' }
    ];
    renderSortingList();
    changeSlide(0);
});

// --- 2. RENDERIZADOR DOS TEMAS EM TEMPO REAL ---
function changeSlide(nextIndex) {
    if (photosList.length === 0) { stage.innerHTML = ''; return; }
    
    // Tratamento de loop/infinito com base no modo ativo
    if (runMode === 'random') {
        currentIdx = Math.floor(Math.random() * photosList.length);
    } else {
        currentIdx = (nextIndex + photosList.length) % photosList.length;
    }

    const activePhoto = photosList[currentIdx];
    const activeTheme = viewport.className;

    // Gerencia o nó antigo criando a transição de saída (.exit)
    const oldSlide = stage.querySelector('.slide-item');
    if (oldSlide) {
        oldSlide.classList.remove('enter');
        oldSlide.classList.add('exit');
        setTimeout(() => oldSlide.remove(), 700); // Garante a limpeza pós animação
    }

    // Criação dinâmica da árvore DOM para o Slide
    const slideItem = document.createElement('div');
    slideItem.classList.add('slide-item', 'enter');

    if (activeTheme === 'theme-c') {
        // Tema C exige processamento por palavras separadas
        slideItem.innerHTML = `<img src="${activePhoto.src}">`;
        const captionBox = document.createElement('div');
        captionBox.classList.add('caption-box');
        activePhoto.name.split(' ').forEach((word, index) => {
            const span = document.createElement('span');
            span.classList.add('word-span');
            span.style.setProperty('--w-idx', index);
            span.textContent = word;
            captionBox.appendChild(span);
        });
        slideItem.appendChild(captionBox);
    } 
    else if (activeTheme === 'theme-d') {
        // Tema D exige container próprio e ângulo randômico de rotação
        const randomRotation = (Math.random() * 10 - 5).toFixed(2);
        slideItem.style.setProperty('--rand-rot', `${randomRotation}deg`);
        slideItem.innerHTML = `
            <div class="slide-card-wrapper">
                <img src="${activePhoto.src}">
                <div class="caption-box">${activePhoto.name}</div>
            </div>
        `;
    } 
    else if (activeTheme === 'theme-e') {
        // Tema E exige o fatiamento em duas portas simétricas
        slideItem.innerHTML = `
            <div class="door-container">
                <div class="door-half door-left"><img src="${activePhoto.src}"></div>
                <div class="door-half door-right"><img src="${activePhoto.src}"></div>
            </div>
        `;
    } 
    else {
        // Padrão estrutural para Temas A, B e F
        slideItem.innerHTML = `
            <img src="${activePhoto.src}">
            <div class="caption-box">${activePhoto.name}</div>
        `;
    }

    stage.appendChild(slideItem);
}

// --- 3. CONTROLE DE EXECUÇÃO (LOOPS AUTOMÁTICOS) ---
function runTicker() {
    clearInterval(slideTimer);
    if (runMode === 'manual') return;
    slideTimer = setInterval(() => {
        changeSlide(currentIdx + 1);
    }, intervalTime);
}

modeSelect.addEventListener('change', (e) => { runMode = e.target.value; runTicker(); });
themeSelect.addEventListener('change', (e) => { viewport.className = e.target.value; changeSlide(currentIdx); });

// Teclas Direcionais (Modo Manual)
window.addEventListener('keydown', e => {
    if (runMode !== 'manual') return;
    if (e.key === 'ArrowRight') changeSlide(currentIdx + 1);
    if (e.key === 'ArrowLeft') changeSlide(currentIdx - 1);
});

// --- 4. LISTA ORDENÁVEL COM ARRRASTE NATIVO (HTML5 DRAG & DROP) ---
function renderSortingList() {
    sortableUl.innerHTML = '';
    photosList.forEach((photo, idx) => {
        const li = document.createElement('li');
        li.setAttribute('draggable', 'true');
        li.dataset.id = photo.id;
        li.innerHTML = `<span>${idx + 1}. ${photo.name}</span> <small>☰</small>`;
        
        // Listeners de ordenação mecânica
        li.addEventListener('dragstart', () => li.classList.add('dragging'));
        li.addEventListener('dragend', () => {
            li.classList.remove('dragging');
            rebuildPhotosArrayOrder();
        });
        sortableUl.appendChild(li);
    });
}

sortableUl.addEventListener('dragover', e => {
    e.preventDefault();
    const draggingItem = sortableUl.querySelector('.dragging');
    const siblings = [...sortableUl.querySelectorAll('li:not(.dragging)')];
    const nextSibling = siblings.find(sibling => e.clientY <= sibling.offsetTop + sibling.offsetHeight / 2);
    sortableUl.insertBefore(draggingItem, nextSibling);
});

function rebuildPhotosArrayOrder() {
    const currentOrderIds = [...sortableUl.querySelectorAll('li')].map(li => li.dataset.id);
    photosList = currentOrderIds.map(id => photosList.find(p => p.id === id));
    renderSortingList();
}

// --- 5. NAVEGAÇÃO DA BARRA DE COMANDOS (HUD INTERNO) ---
const hud = document.getElementById('command-hud');
const hudSearch = document.getElementById('hud-search');
let activeHudIdx = 0;

window.addEventListener('keydown', e => {
    if ((e.ctrlKey && e.key.toLowerCase() === 'k') || e.key === '/') {
        e.preventDefault();
        hud.classList.remove('hidden');
        hudSearch.focus();
        updateHudHighlight();
    }
    if (e.key === 'Escape') hud.classList.add('hidden');
});

hudSearch.addEventListener('keydown', e => {
    const options = [...document.querySelectorAll('#hud-options li:not(.hidden)')];
    if (options.length === 0) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeHudIdx = (activeHudIdx + 1) % options.length;
        updateHudHighlight(options);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeHudIdx = (activeHudIdx - 1 + options.length) % options.length;
        updateHudHighlight(options);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        const selectedCmd = options[activeHudIdx].dataset.cmd;
        triggerHudAction(selectedCmd);
        hud.classList.add('hidden');
    }
});

function updateHudHighlight(visibleOptions) {
    const list = visibleOptions || [...document.querySelectorAll('#hud-options li')];
    document.querySelectorAll('#hud-options li').forEach(li => li.classList.remove('active'));
    if (list[activeHudIdx]) list[activeHudIdx].classList.add('active');
}

function triggerHudAction(cmdString) {
    if (cmdString.startsWith('mode-')) {
        runMode = cmdString.replace('mode-', '');
        modeSelect.value = runMode;
        runTicker();
    } else if (cmdString.startsWith('theme-')) {
        const theme = cmdString;
        viewport.className = theme;
        themeSelect.value = theme;
        changeSlide(currentIdx);
    }
}

// Filtro em tempo real no HUD de comandos
hudSearch.addEventListener('input', () => {
    const filter = hudSearch.value.toLowerCase();
    const items = document.querySelectorAll('#hud-options li');
    let hasVisible = false;
    
    items.forEach(item => {
        if (item.textContent.toLowerCase().includes(filter)) {
            item.classList.remove('hidden');
            hasVisible = true;
        } else {
            item.classList.add('hidden');
        }
    });
    activeHudIdx = 0;
    const visible = [...document.querySelectorAll('#hud-options li:not(.hidden)')];
    updateHudHighlight(visible);
});

// --- 6. CONTROLE DE TELA CHEIA NATIVA ---
document.getElementById('btn-fullscreen').addEventListener('click', () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => console.log(err));
    } else {
        document.exitFullscreen();
    }
});