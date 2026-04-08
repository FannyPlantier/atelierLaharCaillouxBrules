<?php
/* ==========================================================================
   1. CONFIGURATION DE BASE
   ========================================================================== */
function lahar_setup() {
    // Déclare l'emplacement du menu
    register_nav_menus( array(
        'primary' => __( 'Menu Principal', 'lahar' ),
    ));

    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'menus' );
    
    remove_theme_support( 'block-templates' ); 
}
add_action( 'after_setup_theme', 'lahar_setup' );

/* ==========================================================================
   2. CHARGEMENT DES STYLES (Main, Navbar, Hero, Footer)
   ========================================================================== */
function lahar_enqueue_custom_styles() {
    $theme_dir = get_stylesheet_directory();
    $theme_uri = get_stylesheet_directory_uri();

    // 0. FontAwesome
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1' );

    // 1. Style principal
    wp_enqueue_style( 'lahar-main-style', $theme_uri . '/style.css', array(), filemtime($theme_dir . '/style.css') );

    // 2. Navbar
    $navbar_path = $theme_dir . '/assets/css/navbar.css';
    if ( file_exists($navbar_path) ) {
        wp_enqueue_style( 'lahar-navbar', $theme_uri . '/assets/css/navbar.css', array('lahar-main-style'), filemtime($navbar_path) );
    }

    // 3. Hero
    $hero_path = $theme_dir . '/assets/css/hero.css';
    if ( file_exists($hero_path) ) {
        wp_enqueue_style( 'lahar-hero', $theme_uri . '/assets/css/hero.css', array('lahar-main-style'), filemtime($hero_path) );
    }

    // 4. Footer
    $footer_path = $theme_dir . '/assets/css/footer.css';
    if ( file_exists($footer_path) ) {
        wp_enqueue_style( 'lahar-footer', $theme_uri . '/assets/css/footer.css', array('lahar-main-style'), filemtime($footer_path) );
    }

    //5. Scripts
    wp_enqueue_script( 'lahar-scripts', get_stylesheet_directory_uri() . '/assets/js/script.js', array(), '1.0.0', true );

}
// Priorité 20 pour s'assurer que tes styles passent après ceux des blocs par défaut
add_action( 'wp_enqueue_scripts', 'lahar_enqueue_custom_styles', 20 );

/* ==========================================================================
   3. NETTOYAGE SÉLECTIF
   ========================================================================== */
add_action( 'wp_enqueue_scripts', function() {
    // wp_dequeue_style( 'wp-block-library' ); // À décommenter si tu n'utilises aucun bloc WP
    wp_dequeue_style( 'global-styles' );
}, 100 );

/* ==========================================================================
   4. DÉCLARATION DES CPT
   ========================================================================== */
   // 1. Déclaration du Custom Post Type "Événements"
function lahar_register_events() {
    $labels = array(
        'name'               => 'Événements',
        'singular_name'      => 'Événement',
        'add_new'            => 'Ajouter un événement',
        'all_items'          => 'Tous les événements',
        'edit_item'          => 'Modifier l\'événement',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-calendar-alt', // Petite icône calendrier
        'supports'           => array('title', 'editor', 'thumbnail'), // Titre, texte, image
        'rewrite'            => array('slug' => 'agenda'),
        'show_in_rest'       => true, 
    );

    register_post_type('evenement', $args);
}
add_action('init', 'lahar_register_events');

/* ==========================================================================
   5. SHORTCODE POUR AFFICHER LES ÉVÉNEMENTS
   ========================================================================== */
function lahar_liste_evenements_shortcode() {
    if ( !function_exists('get_field') ) return '';

    $args = array(
        'post_type'      => 'evenement',
        'posts_per_page' => -1,
        'meta_key'       => 'eventbeginningdate',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    );

    $query = new WP_Query($args);
    $output = '<div class="agenda-grid" style="display: flex; flex-wrap: wrap; gap: 4%; width: 100%;">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            
            // 1. Récupération des champs
            $nom_custom     = get_field('eventname');
            $date_deb_raw   = get_field('eventbeginningdate');
            $date_fin_raw   = get_field('eventenddate');
            $heure_deb      = get_field('eventbeginningtime');
            $heure_fin      = get_field('eventendtime');
            $details        = get_field('eventdetail');
            $adresse        = get_field('eventaddress');
            $lien           = get_field('eventlink');

            $titre = $nom_custom ? $nom_custom : get_the_title();

            $output .= '<article class="event-card" style="width: 48%; margin-bottom: 5%; padding: 3%; border: 1px solid #f0f0f0; box-sizing: border-box; background:#fff;">';
            $output .= '<h2 style="margin-top:0; border-left: 5px solid #ff6600; padding-left: 15px; color: #ff6600;">' . esc_html($titre) . '</h2>';
            
            // 2. Gestion intelligente des DATES
            $time_deb = $date_deb_raw ? strtotime(str_replace('/', '-', $date_deb_raw)) : null;
            $time_fin = $date_fin_raw ? strtotime(str_replace('/', '-', $date_fin_raw)) : null;
            
            if ($time_deb) {
                // On traduit les mois en Français
                $fmt = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                
                $output .= '<p style="margin:5px 0;"><strong>📅 Date :</strong> ';
                
                if ($time_fin && $date_deb_raw !== $date_fin_raw) {
                    // Cas : Du ... au ...
                    $output .= 'Du ' . $fmt->format($time_deb) . ' au ' . $fmt->format($time_fin);
                } else {
                    // Cas : Le ... (même si date de fin est vide)
                    $output .= 'Le ' . $fmt->format($time_deb);
                }
                $output .= '</p>';
            }

            // 3. Gestion intelligente des HORAIRES
            if ($heure_deb) {
                $output .= '<p style="margin:5px 0;"><strong>⏰ Horaire :</strong> ' . esc_html($heure_deb);
                if ($heure_fin) { $output .= ' - ' . esc_html($heure_fin); }
                $output .= '</p>';
            }

            // 4. Gestion de l'ADRESSE (avec tes 2 "d")
            if ($adresse) {
                $addr_text = is_array($adresse) ? $adresse['address'] : $adresse;
                $output .= '<p style="margin:5px 0;"><strong>📍 Lieu :</strong> ' . esc_html($addr_text) . '</p>';
            }

            // 5. DETAILS et LIEN
            if ($details) {
                $output .= '<div style="margin-top:15px; line-height:1.6;">' . nl2br(esc_html($details)) . '</div>';
            }

            if ($lien) {
                $output .= '<a href="' . esc_url($lien) . '" target="_blank" style="display:inline-block; margin-top:15px; background:#000; color:#fff; padding:8px 15px; text-decoration:none;">En savoir plus</a>';
            }

            $output .= '</article>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<p>Aucun événement prévu pour le moment.</p>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('mon_agenda', 'lahar_liste_evenements_shortcode');