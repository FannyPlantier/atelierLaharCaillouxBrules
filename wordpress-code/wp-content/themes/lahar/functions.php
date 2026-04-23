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
   function lahar_liste_evenements_shortcode( $atts ) {
    if ( !function_exists('get_field') ) return '';

    // Option pour limiter le nombre d'événements si besoin : [mon_agenda limit="4"]
    $atts = shortcode_atts( array( 'limit' => -1 ), $atts );

    $args = array(
        'post_type'      => 'evenement',
        'posts_per_page' => intval( $atts['limit'] ),
        'meta_key'       => 'eventbeginningdate',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    );

    $query = new WP_Query($args);
    
    // Formateur pour les dates en français
    $fmt = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
    
    $output = '<div class="agenda-grid">';

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            
            // Récupération des champs
            $nom_custom   = get_field('eventname');
            $date_deb_raw = get_field('eventbeginningdate');
            $date_fin_raw = get_field('eventenddate');
            $heure_deb    = get_field('eventbeginningtime');
            $heure_fin    = get_field('eventendtime');
            $details      = get_field('eventdetail');
            $adresse      = get_field('eventaddress');
            $lien         = get_field('eventlink');

            $titre = $nom_custom ? $nom_custom : get_the_title();

            // Début de la carte
            $output .= '<article class="event-card">';

            // Image à la une
            if ( has_post_thumbnail() ) {
                $output .= '<div class="event-image">' . get_the_post_thumbnail( get_the_ID(), 'medium' ) . '</div>';
            }

            // Titre avec classe dédiée
            $output .= '<h2 class="event-title">' . esc_html($titre) . '</h2>';

            // Gestion des Dates
            $dt_deb = $date_deb_raw ? DateTime::createFromFormat('Ymd', $date_deb_raw) : null;
            if (!$dt_deb && $date_deb_raw) { $dt_deb = DateTime::createFromFormat('d/m/Y', $date_deb_raw); }
            
            $dt_fin = $date_fin_raw ? DateTime::createFromFormat('Ymd', $date_fin_raw) : null;
            if (!$dt_fin && $date_fin_raw) { $dt_fin = DateTime::createFromFormat('d/m/Y', $date_fin_raw); }

            if ( $dt_deb ) {
                $output .= '<div class="event-info">';
                $output .= '<p><strong>📅 Date :</strong> ';
                if ( $dt_fin && $date_deb_raw !== $date_fin_raw ) {
                    $output .= 'Du ' . $fmt->format( $dt_deb->getTimestamp() ) . ' au ' . $fmt->format( $dt_fin->getTimestamp() );
                } else {
                    $output .= 'Le ' . $fmt->format( $dt_deb->getTimestamp() );
                }
                $output .= '</p>';
                
                if ( $heure_deb ) {
                    $output .= '<p><strong>⏰ Horaire :</strong> ' . esc_html($heure_deb);
                    if ($heure_fin) $output .= ' - ' . esc_html($heure_fin);
                    $output .= '</p>';
                }
                $output .= '</div>';
            }

            // Lieu
            if ( $adresse ) {
                $addr_text = is_array($adresse) ? $adresse['address'] : $adresse;
                $output .= '<p class="event-location"><strong>📍 Lieu :</strong> ' . esc_html($addr_text) . '</p>';
            }

            // Description
            if ( $details ) {
                $output .= '<div class="event-details">' . nl2br( esc_html($details) ) . '</div>';
            }

            // Bouton
            if ( $lien ) {
                $output .= '<a href="' . esc_url($lien) . '" target="_blank" class="event-link">En savoir plus</a>';
            }

            $output .= '</article>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<p class="no-event">Aucun événement prévu pour le moment.</p>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('mon_agenda', 'lahar_liste_evenements_shortcode');