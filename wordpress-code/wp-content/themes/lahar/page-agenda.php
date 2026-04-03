<?php
/**
 * Template Name: Page Agenda
 */

get_header(); ?>

<main id="primary" class="site-main" style="width: 100%; padding: 5% 0;">
    <div class="container" style="width: 90%; margin: 0 auto;">
        
        <h1 style="margin-bottom: 5%; text-align: center;"><?php the_title(); ?></h1>

        <div class="agenda-grid" style="display: flex; flex-wrap: wrap; gap: 4%; width: 100%;">
            <?php
            // On récupère la date du jour (format Ymd pour comparer avec ACF)
            $today = date('Ymd');

            $args = array(
                'post_type'      => 'evenement',
                'posts_per_page' => -1,
                'meta_key'       => 'eventbeginningdate',
                'orderby'        => 'meta_value_num',
                'order'          => 'ASC',
                'meta_query'     => array(
                    array(
                        'key'     => 'eventbeginningdate',
                        'compare' => '>=',
                        'value'   => $today,
                    ),
                ),
            );

            $query = new WP_Query($args);

            if ($query->have_posts()) : 
                while ($query->have_posts()) : $query->the_post(); 

                    // Récupération des champs ACF (avec les 2 'd' à address)
                    $date_debut_raw = get_field('eventbeginningdate');
                    $date_fin_raw   = get_field('eventenddate');
                    $heure_debut    = get_field('eventbeginningtime');
                    $heure_fin      = get_field('eventendtime');
                    $nom_custom     = get_field('eventname');
                    $details        = get_field('eventdetail');
                    $adresse        = get_field('eventaddress'); 
                    $lien           = get_field('eventlink');

                    // Formatage des dates pour l'affichage
                    $date_debut = $date_debut_raw ? DateTime::createFromFormat('Ymd', $date_debut_raw)->format('d F Y') : '';
                    $date_fin   = $date_fin_raw ? DateTime::createFromFormat('Ymd', $date_fin_raw)->format('d F Y') : '';
            ?>

                <article class="event-card" style="width: 48%; margin-bottom: 5%; padding: 3%; border: 1px solid #f0f0f0; background: #fff; box-sizing: border-box;">
                    
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="event-image" style="width: 100%; margin-bottom: 15px;">
                            <?php the_post_thumbnail('medium_large', array('style' => 'width: 100%; height: auto; display: block;')); ?>
                        </div>
                    <?php endif; ?>

                    <h2 style="margin-top: 0;"><?php echo $nom_custom ? $nom_custom : get_the_title(); ?></h2>
                    
                    <div class="event-info" style="margin-bottom: 15px; font-size: 0.9em; color: #555;">
                        <p style="margin: 5px 0;"><strong>📅 Dates :</strong> Du <?php echo $date_debut; ?> au <?php echo $date_fin; ?></p>
                        <p style="margin: 5px 0;"><strong>⏰ Horaires :</strong> <?php echo $heure_debut; ?> - <?php echo $heure_fin; ?></p>
                        
                        <?php if($adresse): ?>
                            <p style="margin: 5px 0;"><strong>📍 Lieu :</strong> 
                                <?php echo is_array($adresse) ? $adresse['address'] : $adresse; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="event-excerpt" style="margin-bottom: 20px; line-height: 1.6;">
                        <?php echo nl2br($details); ?>
                    </div>

                    <?php if($lien): ?>
                        <a href="<?php echo $lien; ?>" target="_blank" style="display: inline-block; background: #000; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
                            En savoir plus
                        </a>
                    <?php endif; ?>

                </article>

            <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <p style="width: 100%; text-align: center;">Aucun événement n'est prévu pour le moment. Revenez bientôt !</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>