<?php
/**
 * Elementor Widget & Shortcode: Lista de Projetos Realizados
 *
 * @package AndorinhaStarter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Função de renderização HTML da lista de projetos
function andorinha_render_projetos_grid( $posts_per_page = 6, $columns = 3 ) {
    $query = new WP_Query( array(
        'post_type'      => 'projetos',
        'posts_per_page' => intval( $posts_per_page ),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    if ( ! $query->have_posts() ) {
        return '<div class="alert alert-info text-center display-7">Nenhum projeto cadastrado no momento.</div>';
    }

    $col_class = 'col-12 col-md-6 col-lg-4';
    if ( $columns == 2 ) {
        $col_class = 'col-12 col-md-6';
    } elseif ( $columns == 4 ) {
        $col_class = 'col-12 col-md-6 col-lg-3';
    }

    ob_start();
    ?>
    <div class="andorinha-projetos-widget-wrapper">
        <div class="row g-4">
            <?php
            while ( $query->have_posts() ) :
                $query->the_post();
                $pdf_url = get_post_meta( get_the_ID(), '_andorinha_projeto_pdf', true );
                ?>
                <div class="<?php echo esc_attr( $col_class ); ?>">
                    <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden">
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail( 'medium_large', array( 'class' => 'card-img-top', 'style' => 'height: 220px; object-fit: cover;' ) ); ?>
                            </a>
                        <?php else : ?>
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 220px;">
                                <span class="fs-4">📌 Projeto</span>
                            </div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column p-4">
                            <h4 class="card-title display-5 fw-bold mb-3" style="font-family: 'Epilogue', sans-serif;">
                                <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark">
                                    <?php the_title(); ?>
                                </a>
                            </h4>

                            <div class="card-text text-muted mb-4 display-4" style="flex-grow: 1; font-family: 'Epilogue', sans-serif;">
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
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 2. Shortcode [andorinha_projetos posts="6" columns="3"]
function andorinha_projetos_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'posts'   => 6,
        'columns' => 3,
    ), $atts, 'andorinha_projetos' );

    return andorinha_render_projetos_grid( $atts['posts'], $atts['columns'] );
}
add_shortcode( 'andorinha_projetos', 'andorinha_projetos_shortcode' );


// 3. Registrar Widget do Elementor apenas quando a classe base existir
add_action( 'elementor/widgets/register', 'andorinha_register_elementor_projetos_widget' );

function andorinha_register_elementor_projetos_widget( $widgets_manager ) {
    if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
        return;
    }

    if ( ! class_exists( 'Andorinha_Projetos_Elementor_Widget' ) ) {
        class Andorinha_Projetos_Elementor_Widget extends \Elementor\Widget_Base {

            public function get_name() {
                return 'andorinha_projetos_grid';
            }

            public function get_title() {
                return __( 'Lista de Projetos (Andorinha)', 'andorinha-starter' );
            }

            public function get_icon() {
                return 'eicon-posts-grid';
            }

            public function get_categories() {
                return array( 'general' );
            }

            protected function register_controls() {
                $this->start_controls_section(
                    'content_section',
                    array(
                        'label' => __( 'Configurações de Exibição', 'andorinha-starter' ),
                        'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                    )
                );

                $this->add_control(
                    'posts_per_page',
                    array(
                        'label'   => __( 'Quantidade de Projetos', 'andorinha-starter' ),
                        'type'    => \Elementor\Controls_Manager::NUMBER,
                        'min'     => 1,
                        'max'     => 24,
                        'step'    => 1,
                        'default' => 6,
                    )
                );

                $this->add_control(
                    'columns',
                    array(
                        'label'   => __( 'Colunas na Grade', 'andorinha-starter' ),
                        'type'    => \Elementor\Controls_Manager::SELECT,
                        'default' => '3',
                        'options' => array(
                            '2' => __( '2 Colunas', 'andorinha-starter' ),
                            '3' => __( '3 Colunas', 'andorinha-starter' ),
                            '4' => __( '4 Colunas', 'andorinha-starter' ),
                        ),
                    )
                );

                $this->end_controls_section();
            }

            protected function render() {
                $settings       = $this->get_settings_for_display();
                $posts_per_page = ! empty( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : 6;
                $columns        = ! empty( $settings['columns'] ) ? $settings['columns'] : 3;

                echo andorinha_render_projetos_grid( $posts_per_page, $columns );
            }
        }
    }

    if ( method_exists( $widgets_manager, 'register' ) ) {
        $widgets_manager->register( new Andorinha_Projetos_Elementor_Widget() );
    } elseif ( method_exists( $widgets_manager, 'register_widget_type' ) ) {
        $widgets_manager->register_widget_type( new Andorinha_Projetos_Elementor_Widget() );
    }
}
