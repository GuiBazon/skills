document.addEventListener('DOMContentLoaded', function() {
    // Spotlight na capa
    const cover = document.getElementById('coverImage');
    if (cover) {
        cover.addEventListener('mousemove', function(e) {
            const rect = cover.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            cover.style.maskImage = `radial-gradient(circle at ${x}px ${y}px, black, transparent 300px)`;
        });
    }

    // Modal de imagem ampliada (clique + scroll fecha)
    function criarModal(src) {
        const modal = document.createElement('div');
        modal.className = 'image-modal';
        const img = document.createElement('img');
        img.src = src;
        modal.appendChild(img);
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        const fechar = () => {
            modal.remove();
            document.body.style.overflow = '';
            window.removeEventListener('scroll', fechar);
        };
        modal.addEventListener('click', fechar);
        window.addEventListener('scroll', fechar, { once: true });
    }

    document.querySelectorAll('.content-img').forEach(img => {
        img.addEventListener('click', (e) => {
            e.stopPropagation();
            criarModal(img.src);
        });
    });

    // Busca com OR (palavras separadas por /)
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const termos = this.value.toLowerCase().split('/').map(t => t.trim()).filter(t => t);
            const cards = document.querySelectorAll('.page-card');
            cards.forEach(card => {
                if (termos.length === 0) {
                    card.style.display = '';
                    return;
                }
                const titulo = (card.dataset.titulo || '').toLowerCase();
                const resumo = (card.dataset.resumo || '').toLowerCase();
                const match = termos.some(term => titulo.includes(term) || resumo.includes(term));
                card.style.display = match ? '' : 'none';
            });
        });
    }
});