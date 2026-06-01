// Efeito spotlight na capa
document.addEventListener('DOMContentLoaded', function() {
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
    function criarModal(imgSrc) {
        const modal = document.createElement('div');
        modal.className = 'image-modal';
        const bigImg = document.createElement('img');
        bigImg.src = imgSrc;
        modal.appendChild(bigImg);
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        const fechar = function() {
            modal.remove();
            document.body.style.overflow = '';
            window.removeEventListener('scroll', fechar);
        };
        modal.addEventListener('click', fechar);
        window.addEventListener('scroll', fechar, { once: true });
    }

    const imagens = document.querySelectorAll('.content-img');
    imagens.forEach(img => {
        img.addEventListener('click', function(e) {
            e.stopPropagation();
            criarModal(this.src);
        });
    });

    // Busca com lógica OR (palavras separadas por / )
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
                const titulo = card.getAttribute('data-title') || '';
                const resumo = card.getAttribute('data-resumo') || '';
                const match = termos.some(term => titulo.includes(term) || resumo.includes(term));
                card.style.display = match ? '' : 'none';
            });
        });
    }
});