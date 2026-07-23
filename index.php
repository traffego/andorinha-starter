<?php
/**
 * Main template file
 *
 * @package AndorinhaTheme
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;
    else :
        ?>
        <div class="container py-5 text-center">
            <h2>Nenhum conteúdo encontrado.</h2>
        </div>
        <?php
    endif;
    ?>
</main>

<?php
get_footer();
