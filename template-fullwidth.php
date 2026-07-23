<?php
/**
 * Template Name: Elementor Largura Total
 * Template Post Type: page, post
 *
 * @package AndorinhaTheme
 */

get_header();
?>

<main id="primary" class="site-main elementor-fullwidth-container">
    <?php
    while ( have_posts() ) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php
get_footer();
