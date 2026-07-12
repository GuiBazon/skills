// 1) mostrar/ocultar senha
function togglePass(id) {
    var i = document.getElementById(id);
    i.type = i.type === 'password' ? 'text' : 'password';
}

// 2) carrossel simples (troca display: none/block)
var carouselIndex = 0;
function carouselMove(dir) {
    var imgs = document.querySelectorAll('.carousel-img');
    if (!imgs.length) return;
    imgs[carouselIndex].style.display = 'none';
    carouselIndex = (carouselIndex + dir + imgs.length) % imgs.length;
    imgs[carouselIndex].style.display = 'block';
}

// 3) inatividade: só roda se existir o modal na página (páginas logadas)
(function () {
    var modal = document.getElementById('inactivityModal');
    if (!modal) return;

    var timer, count, secs;

    function reset() {
        clearTimeout(timer);
        timer = setTimeout(abrirModal, 60000); // 1 minuto
    }
    function abrirModal() {
        modal.classList.remove('hidden');
        secs = 10;
        document.getElementById('segundos').textContent = secs;
        count = setInterval(function () {
            secs--;
            document.getElementById('segundos').textContent = secs;
            if (secs <= 0) { clearInterval(count); window.location.href = 'logout'; }
        }, 1000);
    }

    document.getElementById('simBtn').onclick = function () {
        clearInterval(count);
        modal.classList.add('hidden');
        reset();
    };
    document.getElementById('naoBtn').onclick = function () { window.location.href = 'logout'; };

    document.onmousemove = document.onkeydown = document.onclick = function () {
        if (modal.classList.contains('hidden')) reset();
    };

    reset();
})();
