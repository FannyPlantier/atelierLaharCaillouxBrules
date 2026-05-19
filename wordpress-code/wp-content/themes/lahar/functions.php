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
   3b. CORRECTION DES LIENS D'ANCRE DANS LE MENU
   ========================================================================== */
add_filter( 'wp_nav_menu_objects', function( $items ) {
    $home_url = trailingslashit( home_url() );
    foreach ( $items as $item ) {
        if ( isset( $item->url ) && str_starts_with( $item->url, '#' ) ) {
            $item->url = $home_url . $item->url;
        }
    }
    return $items;
} );

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
        'has_archive'        => false,
        'menu_icon'          => 'dashicons-calendar-alt', // Petite icône calendrier
        'supports'           => array('title', 'editor', 'thumbnail'), // Titre, texte, image
        'rewrite'            => array('slug' => 'evenement'),
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

    $atts = shortcode_atts( array(
        'limit'   => -1,
        'archive' => 'non'
    ), $atts );

    $today      = date('Ymd');
    $is_archive = ($atts['archive'] === 'oui');
    $fmt        = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE);

    $query_args = array(
        'post_type'      => 'evenement',
        'posts_per_page' => intval( $atts['limit'] ),
        'meta_key'       => 'eventbeginningdate',
        'orderby'        => 'meta_value_num',
        'order'          => $is_archive ? 'DESC' : 'ASC',
        'post_status'    => 'publish',
        'meta_query'     => array(
            'relation' => 'OR',
            // Cas 1 : date de fin renseignée et non vide → on s'en sert
            array(
                'relation' => 'AND',
                array(
                    'key'     => 'eventenddate',
                    'value'   => '',
                    'compare' => '!=',
                ),
                array(
                    'key'     => 'eventenddate',
                    'compare' => $is_archive ? '<' : '>=',
                    'value'   => $today,
                    'type'    => 'NUMERIC',
                ),
            ),
            // Cas 2 : date de fin absente ou vide → on utilise la date de début
            array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array( 'key' => 'eventenddate', 'compare' => 'NOT EXISTS' ),
                    array( 'key' => 'eventenddate', 'value' => '', 'compare' => '=' ),
                ),
                array(
                    'key'     => 'eventbeginningdate',
                    'compare' => $is_archive ? '<' : '>=',
                    'value'   => $today,
                    'type'    => 'NUMERIC',
                ),
            ),
        ),
    );

    // Utilitaire : parse une date ACF (Ymd ou d/m/Y)
    $parse_date = function( $raw ) {
        if ( !$raw ) return null;
        $d = DateTime::createFromFormat('Ymd', $raw);
        if ( !$d ) $d = DateTime::createFromFormat('d/m/Y', $raw);
        return $d ?: null;
    };

    // Utilitaire : extrait le texte d'un champ adresse (texte brut ou Google Map ACF)
    $parse_address = function( $raw ) {
        if ( !$raw ) return '';
        return is_array($raw) ? ( $raw['address'] ?? '' ) : (string) $raw;
    };

    /* =====================================================================
       MODE ARCHIVE : accordéon par année
       ===================================================================== */
    if ( $is_archive ) {
        $query = new WP_Query($query_args);

        static $archive_js_done = false;
        $js = '';
        if ( !$archive_js_done ) {
            $js = '<script>document.addEventListener("DOMContentLoaded",function(){'
                . 'var mainBtn=document.querySelector(".agenda-toggle-btn");'
                . 'if(mainBtn){mainBtn.addEventListener("click",function(){'
                .   'var list=document.getElementById("agenda-archive-list");'
                .   'var open=this.getAttribute("aria-expanded")==="true";'
                .   'list.style.display=open?"none":"block";'
                .   'this.setAttribute("aria-expanded",open?"false":"true");'
                .   'var ic=this.querySelector(".agenda-toggle-icon");if(ic)ic.style.transform=open?"":"rotate(180deg)";'
                . '});}'
                . 'document.querySelectorAll(".archive-year-btn").forEach(function(btn){'
                .   'btn.addEventListener("click",function(){'
                .     'var c=this.nextElementSibling;'
                .     'var open=this.getAttribute("aria-expanded")==="true";'
                .     'c.style.display=open?"none":"block";'
                .     'this.setAttribute("aria-expanded",open?"false":"true");'
                .     'var ic=this.querySelector(".archive-year-icon");if(ic)ic.style.transform=open?"":"rotate(180deg)";'
                .   '});'
                . '});'
                . '});</script>';
            $archive_js_done = true;
        }

        $output = $js
            . '<button class="agenda-toggle-btn" aria-expanded="false">'
            . 'Voir tous les événements passés&nbsp;<i class="fa-solid fa-chevron-down agenda-toggle-icon"></i>'
            . '</button>'
            . '<div id="agenda-archive-list" style="display:none;">';

        if ( $query->have_posts() ) {
            // Collecte groupée par année
            $events_by_year = array();
            while ( $query->have_posts() ) {
                $query->the_post();
                $nom_custom   = get_field('eventname');
                $date_deb_raw = get_field('eventbeginningdate');
                $date_fin_raw = get_field('eventenddate');
                $titre        = $nom_custom ? $nom_custom : get_the_title();
                $dt_deb       = $parse_date($date_deb_raw);
                $year         = $dt_deb ? $dt_deb->format('Y') : '0000';

                $events_by_year[$year][] = array(
                    'titre'        => $titre,
                    'dt_deb'       => $dt_deb,
                    'date_deb_raw' => $date_deb_raw,
                    'date_fin_raw' => $date_fin_raw,
                    'details'      => get_field('eventdetail'),
                    'adresse'      => $parse_address( get_field('eventaddress') ),
                );
            }
            wp_reset_postdata();

            krsort($events_by_year);
            foreach ( $events_by_year as $year => $events ) {
                $output .= '<div class="archive-year-group">';
                $output .= '<button class="archive-year-btn" aria-expanded="false">'
                    . esc_html($year)
                    . '&nbsp;<i class="fa-solid fa-chevron-down archive-year-icon"></i>'
                    . '</button>';
                $output .= '<div class="archive-year-content" style="display:none;">';

                foreach ( $events as $ev ) {
                    $dt_fin    = $parse_date($ev['date_fin_raw']);
                    $date_text = ($dt_fin && $ev['date_deb_raw'] !== $ev['date_fin_raw'])
                        ? 'Du ' . $fmt->format($ev['dt_deb']->getTimestamp()) . ' au ' . $fmt->format($dt_fin->getTimestamp())
                        : ($ev['dt_deb'] ? ucfirst($fmt->format($ev['dt_deb']->getTimestamp())) : '');

                    $output .= '<div class="archive-item">';
                        $output .= '<span class="archive-date">' . $date_text . '</span>';
                        $output .= '<div class="archive-body">';
                            $output .= '<h3 class="archive-title">' . esc_html($ev['titre']) . '</h3>';
                            if ($ev['adresse']) {
                                $output .= '<p class="archive-address">' . esc_html($ev['adresse']) . '</p>';
                            }
                            if ($ev['details']) {
                                $output .= '<div class="archive-pitch">' . nl2br(esc_html($ev['details'])) . '</div>';
                            }
                        $output .= '</div>';
                    $output .= '</div>';
                }

                $output .= '</div>'; // .archive-year-content
                $output .= '</div>'; // .archive-year-group
            }
        } else {
            $output .= '<p class="no-event">Aucune archive pour le moment.</p>';
        }

        $output .= '</div>'; // #agenda-archive-list
        return $output;
    }

    /* =====================================================================
       MODE NORMAL : grille de cartes (événements à venir)
       ===================================================================== */
    $query = new WP_Query($query_args);

    // Images de fallback persistantes
    $fallback_images = array();
    $fallback_index  = 0;
    $already_used    = array();
    foreach ( $query->posts as $p ) {
        $stored = get_post_meta( $p->ID, '_event_fallback_image_id', true );
        if ( $stored ) $already_used[] = (int) $stored;
    }
    $pool_args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    );
    if ( !empty($already_used) ) $pool_args['exclude'] = $already_used;
    $new_pool = get_posts($pool_args);
    shuffle($new_pool);
    $fallback_images = $new_pool;

    $output = '<div class="agenda-grid">';

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();

            $nom_custom   = get_field('eventname');
            $date_deb_raw = get_field('eventbeginningdate');
            $date_fin_raw = get_field('eventenddate');
            $type         = get_field('eventtype');
            $titre        = $nom_custom ? $nom_custom : get_the_title();
            $dt_deb       = $parse_date($date_deb_raw);
            $heure_deb    = get_field('eventbeginningtime');
            $heure_fin    = get_field('eventendtime');
            $adresse_txt  = $parse_address( get_field('eventaddress') );
            $lien         = get_field('eventlink');
            $details      = get_field('eventdetail');
            $image_acf    = get_field('eventimage');

            $output .= '<article class="event-card">';
                $output .= '<div class="event-header">';
                    if ( $image_acf ) {
                        $img_url = is_array($image_acf) ? $image_acf['url'] : $image_acf;
                        $output .= '<img src="' . esc_url($img_url) . '" class="event-img" alt="' . esc_attr($titre) . '">';
                    } else {
                        $stored_id = get_post_meta( get_the_ID(), '_event_fallback_image_id', true );
                        if ( $stored_id && wp_get_attachment_url($stored_id) ) {
                            $output .= wp_get_attachment_image( $stored_id, 'large', false, array('class' => 'event-img') );
                        } elseif ( !empty($fallback_images) ) {
                            $img_id = $fallback_images[ $fallback_index % count($fallback_images) ];
                            $fallback_index++;
                            update_post_meta( get_the_ID(), '_event_fallback_image_id', $img_id );
                            $output .= wp_get_attachment_image( $img_id, 'large', false, array('class' => 'event-img') );
                        }
                    }
                    $output .= '<div class="event-overlay"></div>';
                    if ($type) {
                        $label = is_array($type) ? $type['label'] : $type;
                        $slug  = sanitize_title($label);
                        $output .= '<span class="event-badge event-badge--' . esc_attr($slug) . '">' . esc_html($label) . '</span>';
                    }
                $output .= '</div>';

                $output .= '<div class="event-content">';
                    $output .= '<h2 class="event-title">' . esc_html($titre) . '</h2>';
                    $output .= '<div class="event-meta">';
                        if ($dt_deb) {
                            $dt_fin    = $parse_date($date_fin_raw);
                            $date_text = ($dt_fin && $date_deb_raw !== $date_fin_raw)
                                ? 'Du ' . $fmt->format($dt_deb->getTimestamp()) . ' au ' . $fmt->format($dt_fin->getTimestamp())
                                : $fmt->format($dt_deb->getTimestamp());
                            $output .= '<p><i class="fa-regular fa-calendar"></i> ' . $date_text . '</p>';
                        }
                        if ($heure_deb) {
                            $heure_text = $heure_fin
                                ? 'De ' . esc_html($heure_deb) . ' à ' . esc_html($heure_fin)
                                : 'À partir de ' . esc_html($heure_deb);
                            $output .= '<p><i class="fa-regular fa-clock"></i> ' . $heure_text . '</p>';
                        }
                        if ($adresse_txt) {
                            $output .= '<p><i class="fa-solid fa-location-dot"></i> ' . esc_html($adresse_txt) . '</p>';
                        }
                    $output .= '</div>';
                    if ($details) $output .= '<div class="event-excerpt">' . nl2br(esc_html($details)) . '</div>';
                    if ($lien) $output .= '<a href="' . esc_url($lien) . '" target="_blank" class="event-button">En savoir plus</a>';
                $output .= '</div>';
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


