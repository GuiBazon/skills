// ---------- Toggle de senha ----------
function togglePass(id, btn) {
    var input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = 'Ocultar';
    } else {
        input.type = 'password';
        btn.textContent = 'Mostrar';
    }
}

// ---------- Modal de visualização ----------
function viewAnimal(id) {
    var modal = document.getElementById('viewModal');
    var body = document.getElementById('viewModalBody');
    body.innerHTML = 'Carregando...';
    modal.classList.remove('hidden');
    fetch('view-animal.php?id=' + id)
        .then(function (r) { return r.text(); })
        .then(function (html) { body.innerHTML = html; })
        .catch(function () { body.innerHTML = 'Erro ao carregar animal.'; });
}
function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

var carouselIndex = 0;
function carouselMove(dir) {
    var imgs = document.querySelectorAll('.carousel-img');
    if (!imgs.length) return;
    imgs[carouselIndex].style.display = 'none';
    carouselIndex = (carouselIndex + dir + imgs.length) % imgs.length;
    imgs[carouselIndex].style.display = 'block';
}

// ---------- Modal de remoção ----------
var deleteTargetId = null;
function deleteAnimal(id) {
    deleteTargetId = id;
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteTargetId = null;
}
document.addEventListener('DOMContentLoaded', function () {
    var confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (!deleteTargetId) return;
            fetch('animals/delete/' + deleteTargetId, { method: 'POST' })
                .then(function (r) { return r.json(); })
                .then(function () { window.location.reload(); });
        });
    }
});

// ---------- Inatividade (apenas em páginas autenticadas) ----------
(function () {
    var inactivityModal = document.getElementById('inactivityModal');
    if (!inactivityModal) return;

    var INACTIVITY_LIMIT = 60000; // 1 minuto
    var COUNTDOWN = 10;
    var inactivityTimer, countdownInterval;
    var secondsLeft = COUNTDOWN;

    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(showInactivityModal, INACTIVITY_LIMIT);
    }

    function showInactivityModal() {
        inactivityModal.classList.remove('hidden');
        secondsLeft = COUNTDOWN;
        document.getElementById('inactivityTimer').textContent = secondsLeft;
        countdownInterval = setInterval(function () {
            secondsLeft--;
            document.getElementById('inactivityTimer').textContent = secondsLeft;
            if (secondsLeft <= 0) {
                clearInterval(countdownInterval);
                logoutNow();
            }
        }, 1000);
    }

    function logoutNow() {
        window.location.href = 'logout';
    }

    document.getElementById('inactivityYes').addEventListener('click', function () {
        clearInterval(countdownInterval);
        inactivityModal.classList.add('hidden');
        resetInactivityTimer();
    });
    document.getElementById('inactivityNo').addEventListener('click', logoutNow);

    ['mousemove', 'keydown', 'click', 'scroll'].forEach(function (evt) {
        document.addEventListener(evt, function () {
            if (inactivityModal.classList.contains('hidden')) {
                resetInactivityTimer();
            }
        });
    });

    resetInactivityTimer();
})();
