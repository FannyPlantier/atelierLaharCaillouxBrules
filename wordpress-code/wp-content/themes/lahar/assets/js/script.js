/* ==========================================================================
   GESTION DU SCROLL (NAVBAR)
   ========================================================================== */

   document.addEventListener('DOMContentLoaded', function() {
    // On récupère l'élément header
    const header = document.querySelector('.site-header');

    // Fonction qui vérifie la position du scroll
    function handleScroll() {
        if (window.scrollY > 50) {
            // Ajoute la classe si on a scrollé de plus de 50px
            header.classList.add('scrolled');
        } else {
            // Retire la classe si on est tout en haut
            header.classList.remove('scrolled');
        }
    }

    // On écoute l'événement scroll sur la fenêtre
    window.addEventListener('scroll', handleScroll);

    // On l'exécute une fois au chargement au cas où la page soit déjà scrollée
    handleScroll();
});

/* ==========================================================================
   GALERIE LIGHTBOX
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function () {
    var items = document.querySelectorAll('.galerie-item');
    if (!items.length) return;

    // Création du lightbox dans le DOM
    var lb = document.createElement('div');
    lb.id = 'galerie-lightbox';
    lb.innerHTML =
        '<div class="lb-backdrop"></div>' +
        '<div class="lb-inner">' +
            '<button class="lb-close" aria-label="Fermer">&times;</button>' +
            '<button class="lb-prev" aria-label="Précédent">&#8249;</button>' +
            '<img class="lb-img" src="" alt="">' +
            '<button class="lb-next" aria-label="Suivant">&#8250;</button>' +
        '</div>';
    document.body.appendChild(lb);

    var lbImg   = lb.querySelector('.lb-img');
    var arr     = Array.from(items);
    var current = 0;

    function open(i) {
        current     = i;
        lbImg.src   = '';
        lbImg.src   = arr[i].getAttribute('href');
        lbImg.alt   = arr[i].querySelector('img').alt;
        lb.classList.add('lb-active');
        document.body.style.overflow = 'hidden';
    }

    function close() {
        lb.classList.remove('lb-active');
        document.body.style.overflow = '';
        lbImg.src = '';
    }

    function prev() { open((current - 1 + arr.length) % arr.length); }
    function next() { open((current + 1) % arr.length); }

    arr.forEach(function (item, i) {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            open(i);
        });
    });

    lb.querySelector('.lb-close').addEventListener('click', close);
    lb.querySelector('.lb-backdrop').addEventListener('click', close);
    lb.querySelector('.lb-prev').addEventListener('click', function (e) { e.stopPropagation(); prev(); });
    lb.querySelector('.lb-next').addEventListener('click', function (e) { e.stopPropagation(); next(); });

    document.addEventListener('keydown', function (e) {
        if (!lb.classList.contains('lb-active')) return;
        if (e.key === 'ArrowLeft')  prev();
        if (e.key === 'ArrowRight') next();
        if (e.key === 'Escape')     close();
    });

    // Navigation tactile mobile
    var touchStartX = 0;
    lb.addEventListener('touchstart', function (e) {
        touchStartX = e.touches[0].clientX;
    }, { passive: true });
    lb.addEventListener('touchend', function (e) {
        var diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) { diff > 0 ? next() : prev(); }
    });
});

/* ==========================================================================
    GESTION DU MENU BURGER
   ========================================================================== */
   document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-navigation');
    
    // Si l'un des éléments manque, on arrête tout
    if (!menuToggle || !mainNav) return;

    menuToggle.addEventListener('click', function() {
        // Au clic, on coller/enlever l'étiquette 'menu-opened' sur le body
        // C'est le CSS qui gèrera l'animation et la bordure grâce à ça.
        document.body.classList.toggle('menu-opened');
    });

    // Sélectionne TOUS les liens du menu pour fermer l'overlay au clic
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            // Un clic sur un lien ferme le rideau noir
            document.body.classList.remove('menu-opened');
        });
    });
});