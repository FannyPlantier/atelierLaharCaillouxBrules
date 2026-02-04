<?php
/* ================= chargement des styles parent et enfant ================= */
add_action( 'wp_enqueue_scripts', function() {
  wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        filemtime( get_template_directory() . '/style.css' ) 
    );

    // charger la feuille de style du thème enfant
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style'),
        filemtime( get_stylesheet_directory() . '/style.css' ) 
    );

    // charger le fichier JavaScript 
    wp_enqueue_script(
        'custom-script',
        get_template_directory_uri() . '/script.js',
        array('jquery'),
        filemtime( get_template_directory() . '/script.js' ),
        true
    );

    // charger Font Awesome depuis CDN
    wp_enqueue_style(
        'fontawesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
        array(),
        '6.5.0'
    );
});