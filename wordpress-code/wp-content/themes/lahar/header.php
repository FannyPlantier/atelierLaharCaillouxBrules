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
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Atelier Cailloux Brûlés - Pierre-Nicolas Rauzy</a>
        </div>
        <nav class="main-navigation">
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
                <a href="<?php echo get_permalink(22); ?>" class="hero-badge-link">
                    Céramiques
                </a>
            </div>
            <div class="hero-title-container">
                <svg viewBox="0 0 1200 300" class="hero-title-svg">
                    <path id="curve" d="M50,250 Q600,20 1150,250" fill="transparent" />                   <text>
                        <textPath xlink:href="#curve" startOffset="50%" text-anchor="middle">
                            Atelier Cailloux Brûlés
                        </textPath>
                    </text>
                </svg>
            </div>
            <p class="hero-subtitle">Créations artisanales, brutes, craquelées et volcaniste</p>
            <div class="hero-scroll">
                <a href="#apropos">
                    <span>DÉCOUVRIR</span>
                    <span class="arrow-down"></span>
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>