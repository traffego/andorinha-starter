<?php
/**
 * Template para listagem de Projetos Realizados (/projetos/)
 *
 * @package AndorinhaStarter
 */

get_header();
?>

<main id="primary" class="site-main py-5" style="margin-top: 100px;">
    <div class="container">
        
        <header class="page-header text-center mb-5">
            <h1 class="page-title display-2 fw-bold text-dark">Projetos Realizados</h1>
            <p class="lead text-muted display-7">Conheça as iniciativas e ações transformadoras da Andorinha Negócios Criativos.</p>
        </header>

        <?php if ( have_posts() ) : ?>
            <div class="row g-4">
                <?php
                while ( have_posts() ) :
                    the_post();
                    $pdf_url = get_post_meta( get_the_ID(), '_andorinha_projeto_pdf', true );
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                            
                            <!-- Imagem de Capa -->
                            <?php if ( has_post_thumbnail() ) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail( 'medium_large', array( 'class' => 'card-img-top', 'style' => 'height: 220px; object-fit: cover;' ) ); ?>
                                </a>
                            <?php else : ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 220px;">
                                    <span class="fs-4">📌 Projetos</span>
                                </div>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column p-4">
                                <h3 class="card-title display-5 fw-bold mb-3">
                                    <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <div class="card-text text-muted mb-4 display-4" style="flex-grow: 1;">
                                    <?php the_excerpt(); ?>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary display-4 btn-sm">
                                        Ver detalhes
                                    </a>
                                    <?php if ( ! empty( $pdf_url ) ) : ?>
                                        <a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" class="btn btn-light display-4 btn-sm text-danger" title="Download PDF">
                                            📄 PDF
                                        </a>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Paginação -->
            <div class="pagination-wrapper mt-5 d-flex justify-content-center">
                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => __( '&laquo; Anterior', 'andorinha-starter' ),
                    'next_text' => __( 'Próximo &raquo;', 'andorinha-starter' ),
                ) );
                ?>
            </div>

        <?php else : ?>
            <div class="alert alert-info text-center py-5">
                <h4>Nenhum projeto cadastrado no momento.</h4>
                <p>Em breve novos projetos estarão disponíveis aqui.</p>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php
get_footer();
