<?php
/**
 * Template para exibição individual de um Projeto Realizado
 *
 * @package AndorinhaStarter
 */

get_header();
?>

<main id="primary" class="site-main py-5" style="margin-top: 100px;">
    <div class="container">
        <?php
        while ( have_posts() ) :
            the_post();
            $pdf_url     = get_post_meta( get_the_ID(), '_andorinha_projeto_pdf', true );
            $galeria_ids = get_post_meta( get_the_ID(), '_andorinha_projeto_galeria', true );
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'projeto-single-item' ); ?>>
                
                <!-- Cabeçalho do Projeto -->
                <header class="entry-header mb-4 text-center">
                    <h1 class="entry-title display-2 fw-bold text-dark mb-3"><?php the_title(); ?></h1>
                    <div class="entry-meta text-muted display-7">
                        <span>📅 <?php echo get_the_date(); ?></span>
                    </div>
                </header>

                <!-- Imagem Destacada (Capa do Projeto) -->
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="projeto-featured-image mb-5 text-center">
                        <?php the_post_thumbnail( 'large', array( 'class' => 'img-fluid rounded shadow-sm', 'style' => 'max-height: 500px; width: auto;' ) ); ?>
                    </div>
                <?php endif; ?>

                <!-- Conteúdo / Detalhes do Projeto -->
                <div class="entry-content display-7 mb-5" style="font-size: 1.1rem; line-height: 1.8;">
                    <?php the_content(); ?>
                </div>

                <!-- Botão de Download do PDF -->
                <?php if ( ! empty( $pdf_url ) ) : ?>
                    <div class="projeto-pdf-download mb-5 p-4 bg-light rounded text-center border">
                        <h4 class="mb-3 display-5">📄 Documento do Projeto</h4>
                        <p class="text-muted display-4 mb-3">Baixe o relatório ou documento oficial em formato PDF.</p>
                        <a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" class="btn btn-primary display-4 px-4 py-2">
                            Download do PDF
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Galeria de Fotos do Projeto -->
                <?php
                if ( ! empty( $galeria_ids ) ) :
                    $ids = array_filter( explode( ',', $galeria_ids ) );
                    if ( ! empty( $ids ) ) :
                        ?>
                        <div class="projeto-galeria mb-5">
                            <h3 class="display-5 fw-bold mb-4 text-center">Galeria de Fotos</h3>
                            <div class="row g-3">
                                <?php foreach ( $ids as $img_id ) : 
                                    $img_full = wp_get_attachment_image_url( trim( $img_id ), 'full' );
                                    $img_thumb = wp_get_attachment_image_url( trim( $img_id ), 'medium' );
                                    if ( $img_thumb ) :
                                        ?>
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <a href="<?php echo esc_url( $img_full ); ?>" target="_blank" class="d-block text-decoration-none">
                                                <img src="<?php echo esc_url( $img_thumb ); ?>" class="img-fluid rounded shadow-sm w-100" style="height: 200px; object-fit: cover;" alt="<?php the_title_attribute(); ?>" />
                                            </a>
                                        </div>
                                    <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    <?php 
                    endif;
                endif; 
                ?>

                <!-- Navegação entre projetos -->
                <footer class="entry-footer pt-4 border-top">
                    <div class="d-flex justify-content-between">
                        <div><?php previous_post_link( '%link', '&laquo; %title' ); ?></div>
                        <div><?php next_post_link( '%link', '%title &raquo;' ); ?></div>
                    </div>
                </footer>

            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
