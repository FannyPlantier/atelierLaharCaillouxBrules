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