<?php
/**
 * Elementor Widget & Shortcode: Lista de Projetos Realizados
 *
 * @package AndorinhaStarter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// =============================================
// 1. HELPER: obter imagem do projeto
// =============================================
function andorinha_get_projeto_image( $post_id, $size = 'medium_large' ) {
    if ( has_post_thumbnail( $post_id ) ) {
        return get_the_post_thumbnail_url( $post_id, $size );
    }
    $galeria_ids = get_post_meta( $post_id, '_andorinha_projeto_galeria', true );
    if ( ! empty( $galeria_ids ) ) {
        $ids = array_filter( explode( ',', $galeria_ids ) );
        if ( ! empty( $ids ) ) {
            $url = wp_get_attachment_image_url( trim( reset( $ids ) ), $size );
            if ( $url ) return $url;
        }
    }
    return '';
}

// =============================================
// 2. CSS EMBUTIDO
// =============================================
function andorinha_projetos_widget_css() {
    static $printed = false;
    if ( $printed ) return;
    $printed = true;
    ?>
    <style>
    /* Grid */
    .andorinha-projetos-grid { display: flex; flex-wrap: wrap; margin: -12px; }
    .andorinha-col { padding: 12px; box-sizing: border-box; }
    .andorinha-col-2 { width: 50%; }
    .andorinha-col-3 { width: 33.333%; }
    .andorinha-col-4 { width: 25%; }
    @media (max-width: 900px) { .andorinha-col-3,.andorinha-col-4 { width: 50%; } }
    @media (max-width: 600px) { .andorinha-col-2,.andorinha-col-3,.andorinha-col-4 { width: 100%; } }

    /* Card */
    .andorinha-projeto-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 14px rgba(0,0,0,.09);
        background: #fff;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform .25s, box-shadow .25s;
        font-family: 'Epilogue', sans-serif;
    }
    .andorinha-projeto-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 28px rgba(0,0,0,.13);
    }

    /* Imagem */
    .andorinha-card-img-wrap {
        position: relative;
        height: 210px;
        overflow: hidden;
        background: #f4f4f4;
    }
    .andorinha-card-img-wrap a { display: block; height: 100%; }
    .andorinha-card-img-wrap img {
        width: 100%; height: 100%;
        object-fit: cover; display: block;
        transition: transform .35s;
    }
    .andorinha-projeto-card:hover .andorinha-card-img-wrap img { transform: scale(1.05); }

    .andorinha-card-no-img {
        height: 100%;
        display: flex; align-items: center; justify-content: center;
        background: #f0f3f8;
        color: #b0b8c9;
        font-size: 48px;
    }

    /* Badge de tipo */
    .andorinha-tipo-badge {
        position: absolute; top: 12px; left: 12px;
        display: inline-flex; align-items: center; gap: 5px;
        color: #fff;
        font-size: 11px; font-weight: 700; letter-spacing: .4px;
        padding: 4px 11px;
        border-radius: 20px;
        font-family: 'Epilogue', sans-serif;
        line-height: 1.4;
    }
    .andorinha-tipo-badge i { font-size: 10px; }

    /* Corpo */
    .andorinha-card-body { padding: 20px; display: flex; flex-direction: column; flex: 1; }

    .andorinha-card-title {
        font-size: 1.05rem; font-weight: 700;
        margin: 0 0 10px; line-height: 1.35;
        font-family: 'Epilogue', sans-serif;
    }
    .andorinha-card-title a { text-decoration: none; color: #1a1a2e; }
    .andorinha-card-title a:hover { color: #020873; }

    .andorinha-card-desc {
        font-size: .92rem; color: #555;
        line-height: 1.65; margin: 0 0 14px;
        flex-grow: 1;
    }

    /* Campos resumo */
    .andorinha-card-meta {
        list-style: none; margin: 0 0 16px; padding: 0;
        border-top: 1px solid #f0f0f0; padding-top: 13px;
    }
    .andorinha-card-meta li {
        display: flex; align-items: flex-start; gap: 7px;
        font-size: .82rem; margin-bottom: 6px;
        line-height: 1.4; color: #444;
    }
    .andorinha-card-meta li i {
        font-size: 12px; color: #020873;
        margin-top: 2px; flex-shrink: 0; width: 14px; text-align: center;
    }
    .andorinha-meta-label {
        font-weight: 700; color: #666;
        white-space: nowrap; min-width: 90px;
    }
    .andorinha-meta-value { color: #232323; }

    /* Ações */
    .andorinha-card-actions {
        display: flex; gap: 8px; align-items: center;
        margin-top: auto; padding-top: 14px;
        border-top: 1px solid #f0f0f0;
    }
    .andorinha-btn-detail {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 18px;
        background: #020873; color: #fff !important;
        border-radius: 5px; font-size: .84rem; font-weight: 600;
        text-decoration: none;
        transition: background .2s;
        font-family: 'Epilogue', sans-serif;
    }
    .andorinha-btn-detail i { font-size: 12px; }
    .andorinha-btn-detail:hover { background: #030a8c; }

    .andorinha-btn-pdf {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px;
        background: #fef2f2; color: #c0392b !important;
        border-radius: 5px; font-size: .84rem; font-weight: 600;
        text-decoration: none;
        border: 1px solid #fecaca;
        transition: background .2s;
        font-family: 'Epilogue', sans-serif;
    }
    .andorinha-btn-pdf i { font-size: 12px; }
    .andorinha-btn-pdf:hover { background: #fee2e2; }
    </style>
    <?php
}

// =============================================
// 3. RENDERIZAR CARD INDIVIDUAL
// =============================================
function andorinha_render_projeto_card( $post_id, $col_class ) {
    $tipo          = get_post_meta( $post_id, '_andorinha_tipo',          true ) ?: 'projeto';
    $descricao     = get_post_meta( $post_id, '_andorinha_descricao',     true );
    $termo         = get_post_meta( $post_id, '_andorinha_termo_fomento', true );
    $cod_objeto    = get_post_meta( $post_id, '_andorinha_cod_objeto',    true );
    $cod_programa  = get_post_meta( $post_id, '_andorinha_cod_programa',  true );
    $nome_programa = get_post_meta( $post_id, '_andorinha_nome_programa', true );
    $pdf_url       = get_post_meta( $post_id, '_andorinha_projeto_pdf',   true );
    $img_url       = andorinha_get_projeto_image( $post_id );

    $tipo_label = $tipo === 'evento' ? 'Evento' : 'Projeto';
    $tipo_icon  = $tipo === 'evento' ? 'fa-solid fa-calendar-days' : 'fa-solid fa-diagram-project';
    $tipo_bg    = $tipo === 'evento' ? '#0073aa' : '#2e7d32';
    $no_img_icon = $tipo === 'evento' ? 'fa-solid fa-calendar-days' : 'fa-solid fa-diagram-project';

    // Mapa de ícones por campo
    $resumo_fields = array();
    if ( $termo )         $resumo_fields[] = array( 'icon' => 'fa-solid fa-file-contract',   'label' => 'Termo de Fomento', 'value' => $termo );
    if ( $cod_objeto )    $resumo_fields[] = array( 'icon' => 'fa-solid fa-barcode',          'label' => 'Cód. Objeto',     'value' => $cod_objeto );
    if ( $cod_programa )  $resumo_fields[] = array( 'icon' => 'fa-solid fa-hashtag',          'label' => 'Cód. Programa',   'value' => $cod_programa );
    if ( $nome_programa ) $resumo_fields[] = array( 'icon' => 'fa-solid fa-building-columns', 'label' => 'Programa',        'value' => $nome_programa );

    ob_start();
    ?>
    <div class="<?php echo esc_attr( $col_class ); ?>">
        <div class="andorinha-projeto-card">

            <!-- Imagem / placeholder -->
            <div class="andorinha-card-img-wrap">
                <?php if ( $img_url ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
                        <img src="<?php echo esc_url( $img_url ); ?>"
                             alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" />
                    </a>
                <?php else : ?>
                    <div class="andorinha-card-no-img">
                        <i class="<?php echo esc_attr( $no_img_icon ); ?>"></i>
                    </div>
                <?php endif; ?>

                <!-- Badge de tipo -->
                <div class="andorinha-tipo-badge" style="background:<?php echo esc_attr( $tipo_bg ); ?>;">
                    <i class="<?php echo esc_attr( $tipo_icon ); ?>"></i>
                    <?php echo esc_html( $tipo_label ); ?>
                </div>
            </div>

            <!-- Corpo -->
            <div class="andorinha-card-body">

                <h4 class="andorinha-card-title">
                    <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
                        <?php echo esc_html( get_the_title( $post_id ) ); ?>
                    </a>
                </h4>

                <?php if ( $descricao ) : ?>
                    <p class="andorinha-card-desc">
                        <?php echo esc_html( wp_trim_words( $descricao, 20, '...' ) ); ?>
                    </p>
                <?php endif; ?>

                <!-- Campos resumo -->
                <?php if ( ! empty( $resumo_fields ) ) : ?>
                    <ul class="andorinha-card-meta">
                        <?php foreach ( $resumo_fields as $f ) : ?>
                            <li>
                                <i class="<?php echo esc_attr( $f['icon'] ); ?>"></i>
                                <span class="andorinha-meta-label"><?php echo esc_html( $f['label'] ); ?>:</span>
                                <span class="andorinha-meta-value"><?php echo esc_html( $f['value'] ); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <!-- Botões -->
                <div class="andorinha-card-actions">
                    <a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="andorinha-btn-detail">
                        <i class="fa-solid fa-arrow-right"></i> Ver detalhes
                    </a>
                    <?php if ( ! empty( $pdf_url ) ) : ?>
                        <a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" class="andorinha-btn-pdf">
                            <i class="fa-solid fa-file-pdf"></i> PDF
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// =============================================
// 4. FUNÇÃO PRINCIPAL DA GRADE
// =============================================
function andorinha_render_projetos_grid( $posts_per_page = 6, $columns = 3, $tipo_filtro = 'todos' ) {
    $query_args = array(
        'post_type'      => 'projetos',
        'posts_per_page' => intval( $posts_per_page ),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if ( in_array( $tipo_filtro, array( 'projeto', 'evento' ), true ) ) {
        $query_args['meta_query'] = array(
            array(
                'key'     => '_andorinha_tipo',
                'value'   => $tipo_filtro,
                'compare' => '=',
            ),
        );
    }

    $query = new WP_Query( $query_args );

    if ( ! $query->have_posts() ) {
        return '<p style="text-align:center;color:#888;padding:30px 0;font-family:\'Epilogue\',sans-serif;"><i class="fa-solid fa-folder-open" style="margin-right:6px;"></i>Nenhum item encontrado.</p>';
    }

    $col_class = 'andorinha-col andorinha-col-' . intval( $columns );

    ob_start();
    andorinha_projetos_widget_css();
    echo '<div class="andorinha-projetos-widget-wrapper"><div class="andorinha-projetos-grid">';
    while ( $query->have_posts() ) {
        $query->the_post();
        echo andorinha_render_projeto_card( get_the_ID(), $col_class );
    }
    echo '</div></div>';
    wp_reset_postdata();
    return ob_get_clean();
}

// =============================================
// 5. SHORTCODE
// =============================================
function andorinha_projetos_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'posts'   => 6,
        'columns' => 3,
        'tipo'    => 'todos',
    ), $atts, 'andorinha_projetos' );
    return andorinha_render_projetos_grid( $atts['posts'], $atts['columns'], $atts['tipo'] );
}
add_shortcode( 'andorinha_projetos', 'andorinha_projetos_shortcode' );


// =============================================
// 6. CATEGORIA ELEMENTOR
// =============================================
add_action( 'elementor/elements/categories_registered', function( $elements_manager ) {
    $elements_manager->add_category( 'andorinha-category', array(
        'title' => __( 'Andorinha', 'andorinha-starter' ),
        'icon'  => 'fa fa-plug',
    ) );
} );


// =============================================
// 7. WIDGET ELEMENTOR
// =============================================
add_action( 'elementor/init', function() {

    if ( ! class_exists( '\Elementor\Widget_Base' ) ) return;

    if ( ! class_exists( 'Andorinha_Projetos_Elementor_Widget' ) ) {
        class Andorinha_Projetos_Elementor_Widget extends \Elementor\Widget_Base {

            public function get_name()       { return 'andorinha_projetos_grid'; }
            public function get_title()      { return __( 'Lista de Projetos (Andorinha)', 'andorinha-starter' ); }
            public function get_icon()       { return 'eicon-posts-grid'; }
            public function get_categories() { return array( 'andorinha-category', 'general' ); }
            public function get_keywords()   { return array( 'projetos', 'eventos', 'andorinha', 'lista', 'grid', 'portfolio' ); }

            protected function register_controls() {
                $this->start_controls_section( 'content_section', array(
                    'label' => __( 'Configurações', 'andorinha-starter' ),
                    'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
                ) );

                $this->add_control( 'tipo_filtro', array(
                    'label'   => __( 'Exibir', 'andorinha-starter' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => 'todos',
                    'options' => array(
                        'todos'   => __( 'Todos (Projetos + Eventos)', 'andorinha-starter' ),
                        'projeto' => __( 'Somente Projetos', 'andorinha-starter' ),
                        'evento'  => __( 'Somente Eventos', 'andorinha-starter' ),
                    ),
                ) );

                $this->add_control( 'posts_per_page', array(
                    'label'   => __( 'Quantidade', 'andorinha-starter' ),
                    'type'    => \Elementor\Controls_Manager::NUMBER,
                    'min'     => 1, 'max' => 24, 'step' => 1, 'default' => 6,
                ) );

                $this->add_control( 'columns', array(
                    'label'   => __( 'Colunas', 'andorinha-starter' ),
                    'type'    => \Elementor\Controls_Manager::SELECT,
                    'default' => '3',
                    'options' => array( '2' => '2 Colunas', '3' => '3 Colunas', '4' => '4 Colunas' ),
                ) );

                $this->end_controls_section();
            }

            protected function render() {
                $s = $this->get_settings_for_display();
                echo andorinha_render_projetos_grid(
                    $s['posts_per_page'] ?? 6,
                    $s['columns']        ?? 3,
                    $s['tipo_filtro']    ?? 'todos'
                );
            }
        }
    }

    $reg = function( $wm ) {
        $w = new Andorinha_Projetos_Elementor_Widget();
        if ( method_exists( $wm, 'register' ) )             $wm->register( $w );
        elseif ( method_exists( $wm, 'register_widget_type' ) ) $wm->register_widget_type( $w );
    };
    add_action( 'elementor/widgets/register',           $reg );
    add_action( 'elementor/widgets/widgets_registered', $reg );
} );
