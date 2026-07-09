<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header class="site-header">
    <div class="header-container">
        <div class="site-logo">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/monogramme.webp" alt="Logo Atelier" class="custom-logo-img">
                <span class="logo-text">Atelier Cailloux Brûlés - Pierre-Nicolas Rauzy</span>
            </a>
        </div>

        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>

        <nav id="site-navigation" class="main-navigation">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-menu',
            ) );
            ?>
        </nav>
    </div>
</header>

<?php if ( is_front_page() ) : ?>
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-badge">
                <a href="<?php echo get_permalink(22); ?>" class="hero-badge-link">Céramiques</a>
            </div>

            <div class="hero-main-group">
                <div class="hero-title-container">
                    <p class="hero-title">Atelier<br class="hero-title-br"> Cailloux Brûlés</p>
                </div>
                <p class="hero-subtitle">Créations artisanales, brutes, craquelées et vulcanistes</p>
            </div>

            <div class="hero-scroll">
                <a href="#apropos">
                    <span>DÉCOUVRIR</span>
                    <i class="fa-solid fa-chevron-down fa-beat icone-scroll"></i>                
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>