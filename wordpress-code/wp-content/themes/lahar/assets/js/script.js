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

/* ==========================================================================
   LIGHTBOX GALERIE
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function() {
    const gallery = document.querySelector('.wp-block-gallery.has-nested-images');
    if (!gallery) return;

    const figures = Array.from(gallery.querySelectorAll('figure.wp-block-image'));
    if (!figures.length) return;

    const images = figures.map(function(fig) {
        const img = fig.querySelector('img');
        const caption = fig.querySelector('figcaption, .wp-element-caption');
        return {
            src: img ? img.src : '',
            alt: img ? (img.alt || '') : '',
            caption: caption ? caption.textContent.trim() : '',
        };
    });

    // Création de l'overlay
    const lb = document.createElement('div');
    lb.id = 'custom-lightbox';
    lb.innerHTML =
        '<div class="lb-backdrop"></div>' +
        '<button class="lb-close" aria-label="Fermer"><i class="fa-solid fa-xmark"></i></button>' +
        '<button class="lb-prev" aria-label="Précédent"><i class="fa-solid fa-chevron-left"></i></button>' +
        '<button class="lb-next" aria-label="Suivant"><i class="fa-solid fa-chevron-right"></i></button>' +
        '<div class="lb-content">' +
            '<img class="lb-img" src="" alt="">' +
            '<p class="lb-caption"></p>' +
        '</div>';
    document.body.appendChild(lb);

    const lbImg     = lb.querySelector('.lb-img');
    const lbCaption = lb.querySelector('.lb-caption');
    const lbPrev    = lb.querySelector('.lb-prev');
    const lbNext    = lb.querySelector('.lb-next');
    let current     = 0;

    function openLightbox(index) {
        current = index;
        update();
        lb.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lb.classList.remove('active');
        document.body.style.overflow = '';
    }

    function update() {
        var item = images[current];
        lbImg.src = item.src;
        lbImg.alt = item.alt;
        lbCaption.textContent = item.caption;
        lbCaption.style.display = item.caption ? 'block' : 'none';
        lbPrev.style.visibility = current === 0 ? 'hidden' : 'visible';
        lbNext.style.visibility = current === images.length - 1 ? 'hidden' : 'visible';
    }

    function prev() { if (current > 0) { current--; update(); } }
    function next() { if (current < images.length - 1) { current++; update(); } }

    // Clic sur les images de la galerie
    figures.forEach(function(fig, i) {
        fig.style.cursor = 'pointer';
        fig.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openLightbox(i);
        });
    });

    lb.querySelector('.lb-backdrop').addEventListener('click', closeLightbox);
    lb.querySelector('.lb-close').addEventListener('click', closeLightbox);
    lbPrev.addEventListener('click', function(e) { e.stopPropagation(); prev(); });
    lbNext.addEventListener('click', function(e) { e.stopPropagation(); next(); });

    // Clavier
    document.addEventListener('keydown', function(e) {
        if (!lb.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') prev();
        if (e.key === 'ArrowRight') next();
    });

    // Swipe mobile
    var touchStartX = 0;
    lb.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    lb.addEventListener('touchend', function(e) {
        var diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) { if (diff > 0) next(); else prev(); }
    });
});

/* ==========================================================================
   PLAYLIST VIDÉOS
   ========================================================================== */
document.addEventListener('DOMContentLoaded', function() {
    const player = document.getElementById('main-player');
    const mainTitle = document.getElementById('main-video-title');
    const items = document.querySelectorAll('.playlist-item');

    if (!player || !items.length) return;

    items.forEach(function(item) {
        item.addEventListener('click', function() {
            player.pause();
            player.src = this.dataset.src;
            if (this.dataset.poster) player.poster = this.dataset.poster;
            player.load();
            player.play();

            if (mainTitle) mainTitle.textContent = this.dataset.title;

            items.forEach(function(i) { i.classList.remove('active'); });
            this.classList.add('active');

            player.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });
});