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

    // Paramètres : limit (-1 = tout afficher) et archive (oui/non)
    $atts = shortcode_atts( array( 
        'limit'   => -1,
        'archive' => 'non' 
    ), $atts );

    $today      = date('Ymd');
    $is_archive = ($atts['archive'] === 'oui');

    $args = array(
        'post_type'      => 'evenement',
        'posts_per_page' => intval( $atts['limit'] ),
        'meta_key'       => 'eventbeginningdate',
        'orderby'        => 'meta_value_num',
        // Futur : du plus proche au plus loin. Archive : du plus récent au plus vieux.
        'order'          => $is_archive ? 'DESC' : 'ASC', 
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'     => 'eventenddate', 
                'compare' => $is_archive ? '<' : '>=',
                'value'   => $today,               
                'type'    => 'NUMERIC',
            ),
        ),
    );

    $query = new WP_Query($args);
    $fmt = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);

    // Pré-chargement des images de fallback (hors mode archive)
    $fallback_images = array();
    $fallback_index  = 0;
    if ( !$is_archive ) {
        $all_img_ids = get_posts( array(
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ) );
        shuffle( $all_img_ids );
        $fallback_images = $all_img_ids;
    }

    $grid_class = $is_archive ? 'agenda-archive-list' : 'agenda-grid';
    $output = '<div class="' . $grid_class . '">';

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            
            // Données communes
            $nom_custom   = get_field('eventname');
            $date_deb_raw = get_field('eventbeginningdate');
            $date_fin_raw = get_field('eventenddate');
            $type         = get_field('eventtype');
            $titre        = $nom_custom ? $nom_custom : get_the_title();
            $dt_deb       = $date_deb_raw ? DateTime::createFromFormat('Ymd', $date_deb_raw) : null;
            if (!$dt_deb && $date_deb_raw) $dt_deb = DateTime::createFromFormat('d/m/Y', $date_deb_raw);

            if ( $is_archive ) {
                // --- MODE ARCHIVE : Ligne compacte sans image ---
                $output .= '<div class="archive-item">';
                    $output .= '<span class="archive-date">' . ($dt_deb ? $fmt->format($dt_deb->getTimestamp()) : '') . '</span>';
                    $output .= '<h3 class="archive-title">' . esc_html($titre) . '</h3>';
                    if ($type) {
                        $label = is_array($type) ? $type['label'] : $type;
                        $output .= '<span class="archive-tag">' . esc_html($label) . '</span>';
                    }
                    $output .= '<a href="' . get_permalink() . '" class="archive-link">Détails</a>';
                $output .= '</div>';
            } else {
                // --- MODE NORMAL : Cartes Dark Mode avec images ---
                $heure_deb = get_field('eventbeginningtime');
                $heure_fin = get_field('eventendtime');
                $adresse   = get_field('eventaddress');
                $lien      = get_field('eventlink');
                $details   = get_field('eventdetail');
                $image_acf = get_field('eventimage');

                $output .= '<article class="event-card">';
                    $output .= '<div class="event-header">';
                        if ( $image_acf ) {
                            $img_url = is_array($image_acf) ? $image_acf['url'] : $image_acf;
                            $output .= '<img src="' . esc_url($img_url) . '" class="event-img" alt="' . esc_attr($titre) . '">';
                        } elseif ( !empty($fallback_images) ) {
                            $img_id = $fallback_images[ $fallback_index % count($fallback_images) ];
                            $fallback_index++;
                            $output .= wp_get_attachment_image( $img_id, 'large', false, array('class' => 'event-img') );
                        }
                        $output .= '<div class="event-overlay"></div>';
                        if ($type) {
                            $label = is_array($type) ? $type['label'] : $type;
                            $output .= '<span class="event-badge">' . esc_html($label) . '</span>';
                        }
                    $output .= '</div>';
                    
                    $output .= '<div class="event-content">';
                        $output .= '<h2 class="event-title">' . esc_html($titre) . '</h2>';
                        $output .= '<div class="event-meta">';
                            if ($dt_deb) {
                                $dt_fin = $date_fin_raw ? DateTime::createFromFormat('Ymd', $date_fin_raw) : null;
                                $date_text = ($dt_fin && $date_deb_raw !== $date_fin_raw) ? 'Du '.$fmt->format($dt_deb->getTimestamp()).' au '.$fmt->format($dt_fin->getTimestamp()) : $fmt->format($dt_deb->getTimestamp());
                                $output .= '<p><i class="fa-regular fa-calendar"></i> ' . $date_text . '</p>';
                            }
                            if ($heure_deb) $output .= '<p><i class="fa-regular fa-clock"></i> ' . esc_html($heure_deb) . ($heure_fin ? ' - '.esc_html($heure_fin) : '') . '</p>';
                            if ($adresse) $output .= '<p><i class="fa-solid fa-location-dot"></i> ' . esc_html(is_array($adresse) ? $adresse['address'] : $adresse) . '</p>';
                        $output .= '</div>';
                        if ($details) $output .= '<div class="event-excerpt">' . wp_trim_words(esc_html($details), 18) . '</div>';
                        if ($lien) $output .= '<a href="'.esc_url($lien).'" target="_blank" class="event-button">En savoir plus</a>';
                    $output .= '</div>';
                $output .= '</article>';
            }
        }
        wp_reset_postdata();
    } else {
        $msg = $is_archive ? "Aucune archive pour le moment." : "Aucun événement prévu pour le moment.";
        $output .= '<p class="no-event">' . $msg . '</p>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode('mon_agenda', 'lahar_liste_evenements_shortcode');