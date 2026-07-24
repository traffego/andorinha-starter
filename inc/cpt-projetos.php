<?php
/**
 * Registro de Custom Post Type: Projetos Realizados
 *
 * @package AndorinhaStarter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// =============================================
// 1. REGISTRAR CPT 'projetos'
// =============================================
function andorinha_register_cpt_projetos() {
    $labels = array(
        'name'               => __( 'Projetos & Eventos', 'andorinha-starter' ),
        'singular_name'      => __( 'Projeto / Evento', 'andorinha-starter' ),
        'add_new'            => __( 'Adicionar Novo', 'andorinha-starter' ),
        'add_new_item'       => __( 'Adicionar Novo Projeto / Evento', 'andorinha-starter' ),
        'edit_item'          => __( 'Editar Projeto / Evento', 'andorinha-starter' ),
        'new_item'           => __( 'Novo Projeto / Evento', 'andorinha-starter' ),
        'view_item'          => __( 'Ver Projeto / Evento', 'andorinha-starter' ),
        'search_items'       => __( 'Buscar Projetos / Eventos', 'andorinha-starter' ),
        'not_found'          => __( 'Nenhum item encontrado', 'andorinha-starter' ),
        'not_found_in_trash' => __( 'Nenhum item na lixeira', 'andorinha-starter' ),
        'menu_name'          => __( 'Projetos & Eventos', 'andorinha-starter' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'projetos' ),
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array( 'title', 'thumbnail', 'revisions' ),
    );

    register_post_type( 'projetos', $args );
}
add_action( 'init', 'andorinha_register_cpt_projetos' );


// =============================================
// 2. ADICIONAR COLUNA "TIPO" NA LISTAGEM DO ADMIN
// =============================================
function andorinha_projetos_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( $key === 'title' ) {
            $new['tipo'] = __( 'Tipo', 'andorinha-starter' );
        }
    }
    return $new;
}
add_filter( 'manage_projetos_posts_columns', 'andorinha_projetos_columns' );

function andorinha_projetos_column_content( $column, $post_id ) {
    if ( $column === 'tipo' ) {
        $tipo = get_post_meta( $post_id, '_andorinha_tipo', true );
        echo $tipo === 'evento' ? '<span style="color:#0073aa;font-weight:600;">🎯 Evento</span>' : '<span style="color:#46b450;font-weight:600;">📋 Projeto</span>';
    }
}
add_action( 'manage_projetos_posts_custom_column', 'andorinha_projetos_column_content', 10, 2 );


// =============================================
// 3. META BOXES
// =============================================
function andorinha_add_projetos_metaboxes() {
    // Meta Box: Tipo do Registro
    add_meta_box(
        'andorinha_projeto_tipo',
        __( '⚙️ Tipo e Dados do Registro', 'andorinha-starter' ),
        'andorinha_render_tipo_metabox',
        'projetos',
        'normal',
        'high'
    );

    // Meta Box: Arquivos (PDF + Galeria)
    add_meta_box(
        'andorinha_projeto_midia',
        __( '📁 Arquivos e Mídia', 'andorinha-starter' ),
        'andorinha_render_midia_metabox',
        'projetos',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'andorinha_add_projetos_metaboxes' );


// =============================================
// 4. RENDERIZAR META BOX: TIPO + CAMPOS
// =============================================
function andorinha_render_tipo_metabox( $post ) {
    wp_nonce_field( 'andorinha_save_projeto_meta', 'andorinha_projeto_meta_nonce' );

    $tipo          = get_post_meta( $post->ID, '_andorinha_tipo',          true ) ?: 'projeto';
    $descricao     = get_post_meta( $post->ID, '_andorinha_descricao',     true );
    $termo         = get_post_meta( $post->ID, '_andorinha_termo_fomento', true );
    $cod_objeto    = get_post_meta( $post->ID, '_andorinha_cod_objeto',    true );
    $cod_programa  = get_post_meta( $post->ID, '_andorinha_cod_programa',  true );
    $nome_programa = get_post_meta( $post->ID, '_andorinha_nome_programa', true );
    ?>
    <style>
        .andorinha-meta-box { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .andorinha-tipo-selector { display: flex; gap: 12px; margin-bottom: 24px; }
        .andorinha-tipo-btn { flex: 1; padding: 14px; border: 2px solid #ddd; border-radius: 8px; background: #f9f9f9; cursor: pointer; text-align: center; font-size: 15px; font-weight: 600; transition: all 0.2s; }
        .andorinha-tipo-btn:hover { border-color: #0073aa; background: #f0f7ff; }
        .andorinha-tipo-btn.active { border-color: #0073aa; background: #e8f4fb; color: #0073aa; }
        .andorinha-tipo-btn span { display: block; font-size: 28px; margin-bottom: 4px; }
        .andorinha-field { margin-bottom: 18px; }
        .andorinha-field label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; font-size: 13px; }
        .andorinha-field label em { font-weight: 400; color: #888; margin-left: 4px; }
        .andorinha-field input[type="text"],
        .andorinha-field textarea { width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 8px 10px; font-size: 14px; }
        .andorinha-field textarea { height: 120px; resize: vertical; }
        .andorinha-field input:focus,
        .andorinha-field textarea:focus { border-color: #0073aa; outline: none; box-shadow: 0 0 0 2px rgba(0,115,170,.15); }
        .andorinha-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .andorinha-section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; color: #888; border-bottom: 1px solid #eee; padding-bottom: 6px; margin-bottom: 16px; letter-spacing: .5px; }
        #andorinha_tipo_hidden { display: none; }
    </style>

    <div class="andorinha-meta-box">
        <input type="hidden" id="andorinha_tipo_hidden" name="andorinha_tipo" value="<?php echo esc_attr( $tipo ); ?>" />

        <!-- Seletor de Tipo -->
        <div class="andorinha-tipo-selector">
            <div class="andorinha-tipo-btn <?php echo $tipo === 'projeto' ? 'active' : ''; ?>" data-tipo="projeto">
                <span>📋</span> Projeto
            </div>
            <div class="andorinha-tipo-btn <?php echo $tipo === 'evento' ? 'active' : ''; ?>" data-tipo="evento">
                <span>🎯</span> Evento
            </div>
        </div>

        <!-- Descrição -->
        <div class="andorinha-field">
            <label for="andorinha_descricao">
                <span class="andorinha-label-nome">Descrição do Projeto</span>
                <em>(opcional)</em>
            </label>
            <textarea id="andorinha_descricao" name="andorinha_descricao"><?php echo esc_textarea( $descricao ); ?></textarea>
        </div>

        <p class="andorinha-section-title">Dados de Fomento</p>

        <div class="andorinha-row">
            <div class="andorinha-field">
                <label for="andorinha_termo_fomento">Termo de Fomento <em>(opcional)</em></label>
                <input type="text" id="andorinha_termo_fomento" name="andorinha_termo_fomento" value="<?php echo esc_attr( $termo ); ?>" placeholder="Ex: 123456/2024" />
            </div>
            <div class="andorinha-field">
                <label for="andorinha_cod_objeto">Código do Objeto <em>(opcional)</em></label>
                <input type="text" id="andorinha_cod_objeto" name="andorinha_cod_objeto" value="<?php echo esc_attr( $cod_objeto ); ?>" placeholder="Ex: OBJ-001" />
            </div>
            <div class="andorinha-field">
                <label for="andorinha_cod_programa">Código do Programa <em>(opcional)</em></label>
                <input type="text" id="andorinha_cod_programa" name="andorinha_cod_programa" value="<?php echo esc_attr( $cod_programa ); ?>" placeholder="Ex: PROG-2024" />
            </div>
            <div class="andorinha-field">
                <label for="andorinha_nome_programa">Nome do Programa <em>(opcional)</em></label>
                <input type="text" id="andorinha_nome_programa" name="andorinha_nome_programa" value="<?php echo esc_attr( $nome_programa ); ?>" placeholder="Ex: Programa Nacional..." />
            </div>
        </div>
    </div>
    <?php
}


// =============================================
// 5. RENDERIZAR META BOX: ARQUIVOS E MÍDIA
// =============================================
function andorinha_render_midia_metabox( $post ) {
    $pdf_url     = get_post_meta( $post->ID, '_andorinha_projeto_pdf',    true );
    $galeria_ids = get_post_meta( $post->ID, '_andorinha_projeto_galeria', true );
    ?>
    <style>
        .andorinha-midia-field { margin-bottom: 20px; }
        .andorinha-midia-field label { font-weight: 600; display: block; margin-bottom: 6px; font-size: 13px; }
        .andorinha-preview-pdf { margin-top: 8px; font-weight: 500; }
        .andorinha-galeria-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
        .andorinha-galeria-item { position: relative; width: 100px; height: 100px; border: 1px solid #ccc; border-radius: 6px; overflow: hidden; background: #f0f0f0; }
        .andorinha-galeria-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .andorinha-galeria-item .remove-img { position: absolute; top: 3px; right: 3px; background: rgba(220,0,0,.85); color: white; border: none; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; font-size: 14px; line-height: 20px; text-align: center; }
    </style>

    <!-- PDF -->
    <div class="andorinha-midia-field">
        <label for="andorinha_projeto_pdf">Arquivo PDF:</label>
        <input type="text" id="andorinha_projeto_pdf" name="andorinha_projeto_pdf" value="<?php echo esc_url( $pdf_url ); ?>" class="widefat" style="max-width: calc(100% - 145px);" readonly />
        <button type="button" class="button button-secondary" id="andorinha_upload_pdf_btn">Selecionar PDF</button>
        <button type="button" class="button button-link-delete" id="andorinha_remove_pdf_btn" style="<?php echo empty( $pdf_url ) ? 'display:none;' : ''; ?>">Remover</button>
        <div class="andorinha-preview-pdf" id="andorinha_pdf_preview">
            <?php if ( $pdf_url ) : ?>
                <a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank">📄 <?php echo esc_html( basename( $pdf_url ) ); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <hr style="margin: 20px 0;">

    <!-- Galeria -->
    <div class="andorinha-midia-field">
        <label>Galeria de Fotos:</label>
        <input type="hidden" id="andorinha_projeto_galeria" name="andorinha_projeto_galeria" value="<?php echo esc_attr( $galeria_ids ); ?>" />
        <button type="button" class="button button-secondary" id="andorinha_upload_galeria_btn">Adicionar Fotos à Galeria</button>
        <div class="andorinha-galeria-grid" id="andorinha_galeria_container">
            <?php
            if ( ! empty( $galeria_ids ) ) {
                $ids = array_filter( explode( ',', $galeria_ids ) );
                foreach ( $ids as $img_id ) {
                    $img_id = trim( $img_id );
                    $thumb  = wp_get_attachment_image_url( $img_id, 'thumbnail' );
                    if ( $thumb ) {
                        echo '<div class="andorinha-galeria-item" data-id="' . esc_attr( $img_id ) . '">';
                        echo '<img src="' . esc_url( $thumb ) . '" />';
                        echo '<button type="button" class="remove-img">&times;</button>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
    </div>
    <?php
}


// =============================================
// 6. SALVAR TODOS OS META DADOS
// =============================================
function andorinha_save_projeto_meta( $post_id ) {
    if ( ! isset( $_POST['andorinha_projeto_meta_nonce'] ) || ! wp_verify_nonce( $_POST['andorinha_projeto_meta_nonce'], 'andorinha_save_projeto_meta' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $text_fields = array(
        'andorinha_tipo'          => '_andorinha_tipo',
        'andorinha_descricao'     => '_andorinha_descricao',
        'andorinha_termo_fomento' => '_andorinha_termo_fomento',
        'andorinha_cod_objeto'    => '_andorinha_cod_objeto',
        'andorinha_cod_programa'  => '_andorinha_cod_programa',
        'andorinha_nome_programa' => '_andorinha_nome_programa',
        'andorinha_projeto_galeria' => '_andorinha_projeto_galeria',
    );

    foreach ( $text_fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
        }
    }

    // Descrição permite mais caracteres
    if ( isset( $_POST['andorinha_descricao'] ) ) {
        update_post_meta( $post_id, '_andorinha_descricao', sanitize_textarea_field( $_POST['andorinha_descricao'] ) );
    }

    if ( isset( $_POST['andorinha_projeto_pdf'] ) ) {
        update_post_meta( $post_id, '_andorinha_projeto_pdf', esc_url_raw( $_POST['andorinha_projeto_pdf'] ) );
    }
}
add_action( 'save_post_projetos', 'andorinha_save_projeto_meta' );


// =============================================
// 7. ENFILEIRAR SCRIPTS DO ADMIN
// =============================================
function andorinha_admin_projetos_scripts( $hook ) {
    global $post;
    if ( ( $hook === 'post-new.php' || $hook === 'post.php' ) && isset( $post->post_type ) && $post->post_type === 'projetos' ) {
        wp_enqueue_media();
        wp_enqueue_script(
            'andorinha-admin-projetos',
            get_template_directory_uri() . '/assets/js/admin-projetos.js',
            array( 'jquery', 'wp-mediaelement' ),
            wp_get_theme()->get( 'Version' ),
            true
        );
    }
}
add_action( 'admin_enqueue_scripts', 'andorinha_admin_projetos_scripts' );
