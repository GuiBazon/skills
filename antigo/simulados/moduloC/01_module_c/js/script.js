/**
 * Módulo C – Lyon Heritage Sites
 * WorldSkills Web Technologies
 *
 * Funcionalidades do lado do cliente:
 *   - Busca em tempo real com operador OR (/) – C2
 *   - Efeito spotlight na imagem de capa – C4
 *   - Modal de imagens do conteúdo – C4
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ═══════════════════════════════════════════════════════════════════════
       1. Busca em tempo real (client-side)
          O usuário digita palavras separadas por "/".
          A busca filtra artigos cujo título OU resumo contenha
          QUALQUER UMA (OR) dos termos informados.
       ═══════════════════════════════════════════════════════════════════════ */

    var searchInput = document.getElementById('search-input');
    var articlesList = document.getElementById('articles-list');
    var noResultsMsg = document.getElementById('no-search-results');

    if (searchInput && articlesList) {
        var items = articlesList.querySelectorAll('.article-item');

        searchInput.addEventListener('input', function () {
            var raw = searchInput.value.trim();
            var terms = raw ? raw.split('/').map(function (t) { return t.trim().toLowerCase(); }).filter(Boolean) : [];
            var hasVisible = false;

            items.forEach(function (item) {
                // Pastas (folder-item) não possuem data-title – são sempre exibidas
                if (item.classList.contains('folder-item')) {
                    item.style.display = '';
                    return;
                }

                var title = (item.getAttribute('data-title') || '').toLowerCase();
                var summary = (item.getAttribute('data-summary') || '').toLowerCase();

                if (terms.length === 0) {
                    item.style.display = '';
                    hasVisible = true;
                    return;
                }

                // Lógica OR: mostra o item se qualquer termo for encontrado
                var match = terms.some(function (term) {
                    return title.indexOf(term) !== -1 || summary.indexOf(term) !== -1;
                });

                item.style.display = match ? '' : 'none';
                if (match) hasVisible = true;
            });

            if (noResultsMsg) {
                noResultsMsg.style.display = (terms.length > 0 && !hasVisible) ? '' : 'none';
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════════════════
       2. Efeito Spotlight na imagem de capa
          O centro do gradiente radial acompanha a posição do mouse.
       ═══════════════════════════════════════════════════════════════════════ */

    var coverImage = document.getElementById('cover-image');

    if (coverImage) {
        var coverContainer = document.getElementById('article-cover');

        coverContainer.addEventListener('mousemove', function (e) {
            var rect = coverContainer.getBoundingClientRect();
            var x = ((e.clientX - rect.left) / rect.width) * 100;
            var y = ((e.clientY - rect.top) / rect.height) * 100;
            coverImage.style.webkitMaskImage = 'radial-gradient(circle 300px at ' + x + '% ' + y + '%, black 30%, transparent 100%)';
            coverImage.style.maskImage = 'radial-gradient(circle 300px at ' + x + '% ' + y + '%, black 30%, transparent 100%)';
        });

        coverContainer.addEventListener('mouseleave', function () {
            coverImage.style.webkitMaskImage = '';
            coverImage.style.maskImage = '';
        });
    }

    /* ═══════════════════════════════════════════════════════════════════════
       3. Modal de imagem no conteúdo
          Clicar numa .content-img abre o modal.
          Clicar na imagem ampliada ou rolar a página fecha o modal.
       ═══════════════════════════════════════════════════════════════════════ */

    var modalOverlay = document.getElementById('modal-overlay');
    var modalImage = document.getElementById('modal-image');

    function openModal(src) {
        if (!modalOverlay || !modalImage) return;
        modalImage.src = src;
        modalOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    window.openModal = openModal;

    window.closeModal = function () {
        if (!modalOverlay) return;
        modalOverlay.style.display = 'none';
        document.body.style.overflow = '';
    };

    // Abre o modal ao clicar em qualquer imagem .content-img
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('content-img')) {
            openModal(e.target.src);
        }
    });

    // Fecha o modal ao rolar a página
    window.addEventListener('scroll', function () {
        if (modalOverlay && modalOverlay.style.display === 'flex') {
            closeModal();
        }
    });

    // Fecha com a tecla Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalOverlay && modalOverlay.style.display === 'flex') {
            closeModal();
        }
    });

});
