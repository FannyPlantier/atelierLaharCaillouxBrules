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