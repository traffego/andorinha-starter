<?php
/**
 * Registro de Custom Post Type: Projetos Realizados
 * 
 * @package AndorinhaStarter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Registrar Custom Post Type 'projetos'
function andorinha_register_cpt_projetos() {
    $labels = array(
        'name'               => __( 'Projetos Realizados', 'andorinha-starter' ),
        'singular_name'      => __( 'Projeto', 'andorinha-starter' ),
        'add_new'            => __( 'Adicionar Novo', 'andorinha-starter' ),
        'add_new_item'       => __( 'Adicionar Novo Projeto', 'andorinha-starter' ),
        'edit_item'          => __( 'Editar Projeto', 'andorinha-starter' ),
        'new_item'           => __( 'Novo Projeto', 'andorinha-starter' ),
        'view_item'          => __( 'Ver Projeto', 'andorinha-starter' ),
        'search_items'       => __( 'Buscar Projetos', 'andorinha-starter' ),
        'not_found'          => __( 'Nenhum projeto encontrado', 'andorinha-starter' ),
        'not_found_in_trash' => __( 'Nenhum projeto na lixeira', 'andorinha-starter' ),
        'menu_name'          => __( 'Projetos Realizados', 'andorinha-starter' ),
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true, // Suporte ao editor de blocos (Gutenberg) e REST API
        'query_var'           => true,
        'rewrite'             => array( 'slug' => 'projetos' ),
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-portfolio',
        'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
    );

    register_post_type( 'projetos', $args );
}
add_action( 'init', 'andorinha_register_cpt_projetos' );

// 2. Adicionar Meta Boxes para PDF e Fotos Adicionais
function andorinha_add_projetos_metaboxes() {
    add_meta_box(
        'andorinha_projeto_details',
        __( 'Arquivos e Mídia do Projeto', 'andorinha-starter' ),
        'andorinha_render_projeto_metabox',
        'projetos',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'andorinha_add_projetos_metaboxes' );

// Renderizar HTML dos Meta Boxes no Admin
function andorinha_render_projeto_metabox( $post ) {
    wp_nonce_field( 'andorinha_save_projeto_meta', 'andorinha_projeto_meta_nonce' );

    $pdf_url     = get_post_meta( $post->ID, '_andorinha_projeto_pdf', true );
    $galeria_ids = get_post_meta( $post->ID, '_andorinha_projeto_galeria', true );
    ?>
    <style>
        .andorinha-meta-field { margin-bottom: 20px; }
        .andorinha-meta-field label { font-weight: bold; display: block; margin-bottom: 5px; }
        .andorinha-preview-pdf { margin-top: 8px; font-weight: 500; }
        .andorinha-galeria-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .andorinha-galeria-item { position: relative; width: 100px; height: 100px; border: 1px solid #ccc; border-radius: 4px; overflow: hidden; background: #f0f0f0; }
        .andorinha-galeria-item img { width: 100%; height: 100%; object-fit: cover; }
        .andorinha-galeria-item .remove-img { position: absolute; top: 2px; right: 2px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px; line-height: 18px; text-align: center; }
    </style>

    <!-- Campo PDF do Projeto -->
    <div class="andorinha-meta-field">
        <label for="andorinha_projeto_pdf"><?php _e( 'Arquivo PDF do Projeto:', 'andorinha-starter' ); ?></label>
        <input type="text" id="andorinha_projeto_pdf" name="andorinha_projeto_pdf" value="<?php echo esc_url( $pdf_url ); ?>" class="widefat" style="max-width: calc(100% - 130px);" readonly />
        <button type="button" class="button button-secondary" id="andorinha_upload_pdf_btn"><?php _e( 'Selecionar PDF', 'andorinha-starter' ); ?></button>
        <button type="button" class="button button-link-delete" id="andorinha_remove_pdf_btn" style="<?php echo empty($pdf_url) ? 'display:none;' : ''; ?>"><?php _e( 'Remover PDF', 'andorinha-starter' ); ?></button>
        <div class="andorinha-preview-pdf" id="andorinha_pdf_preview">
            <?php if ( $pdf_url ) : ?>
                <a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank">📄 <?php echo esc_html( basename( $pdf_url ) ); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <hr>

    <!-- Galeria de Fotos Adicionais -->
    <div class="andorinha-meta-field">
        <label><?php _e( 'Galeria de Fotos do Projeto:', 'andorinha-starter' ); ?></label>
        <input type="hidden" id="andorinha_projeto_galeria" name="andorinha_projeto_galeria" value="<?php echo esc_attr( $galeria_ids ); ?>" />
        <button type="button" class="button button-secondary" id="andorinha_upload_galeria_btn"><?php _e( 'Adicionar Fotos à Galeria', 'andorinha-starter' ); ?></button>
        
        <div class="andorinha-galeria-grid" id="andorinha_galeria_container">
            <?php
            if ( ! empty( $galeria_ids ) ) {
                $ids = explode( ',', $galeria_ids );
                foreach ( $ids as $img_id ) {
                    $img_id = trim( $img_id );
                    if ( $img_id ) {
                        $thumb = wp_get_attachment_image_url( $img_id, 'thumbnail' );
                        if ( $thumb ) {
                            echo '<div class="andorinha-galeria-item" data-id="' . esc_attr( $img_id ) . '">';
                            echo '<img src="' . esc_url( $thumb ) . '" />';
                            echo '<button type="button" class="remove-img">&times;</button>';
                            echo '</div>';
                        }
                    }
                }
            }
            ?>
        </div>
    </div>
    <?php
}

// 3. Salvar Meta Dados
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

    if ( isset( $_POST['andorinha_projeto_pdf'] ) ) {
        update_post_meta( $post_id, '_andorinha_projeto_pdf', esc_url_raw( $_POST['andorinha_projeto_pdf'] ) );
    }

    if ( isset( $_POST['andorinha_projeto_galeria'] ) ) {
        update_post_meta( $post_id, '_andorinha_projeto_galeria', sanitize_text_field( $_POST['andorinha_projeto_galeria'] ) );
    }
}
add_action( 'save_post_projetos', 'andorinha_save_projeto_meta' );

// 4. Carregar Scripts do WordPress Media Uploader no Admin
function andorinha_admin_projetos_scripts( $hook ) {
    global $post;
    if ( ( $hook === 'post-new.php' || $hook === 'post.php' ) && isset( $post->post_type ) && $post->post_type === 'projetos' ) {
        wp_enqueue_media();
        ?>
        <script>
        jQuery(document).ready(function($){
            // Upload PDF
            var pdfFrame;
            $('#andorinha_upload_pdf_btn').on('click', function(e){
                e.preventDefault();
                if (pdfFrame) { pdfFrame.open(); return; }
                pdfFrame = wp.media({
                    title: 'Selecionar PDF do Projeto',
                    button: { text: 'Usar este PDF' },
                    library: { type: 'application/pdf' },
                    multiple: false
                });
                pdfFrame.on('select', function(){
                    var attachment = pdfFrame.state().get('selection').first().toJSON();
                    $('#andorinha_projeto_pdf').val(attachment.url);
                    $('#andorinha_pdf_preview').html('<a href="'+attachment.url+'" target="_blank">📄 '+attachment.filename+'</a>');
                    $('#andorinha_remove_pdf_btn').show();
                });
                pdfFrame.open();
            });

            $('#andorinha_remove_pdf_btn').on('click', function(e){
                e.preventDefault();
                $('#andorinha_projeto_pdf').val('');
                $('#andorinha_pdf_preview').empty();
                $(this).hide();
            });

            // Upload Galeria
            var galeriaFrame;
            $('#andorinha_upload_galeria_btn').on('click', function(e){
                e.preventDefault();
                if (galeriaFrame) { galeriaFrame.open(); return; }
                galeriaFrame = wp.media({
                    title: 'Selecionar Fotos do Projeto',
                    button: { text: 'Adicionar à Galeria' },
                    library: { type: 'image' },
                    multiple: true
                });
                galeriaFrame.on('select', function(){
                    var selection = galeriaFrame.state().get('selection');
                    var currentIds = $('#andorinha_projeto_galeria').val() ? $('#andorinha_projeto_galeria').val().split(',') : [];
                    
                    selection.each(function(attachment){
                        attachment = attachment.toJSON();
                        if (currentIds.indexOf(attachment.id.toString()) === -1) {
                            currentIds.push(attachment.id);
                            var thumb = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                            $('#andorinha_galeria_container').append(
                                '<div class="andorinha-galeria-item" data-id="'+attachment.id+'">'+
                                '<img src="'+thumb+'" />'+
                                '<button type="button" class="remove-img">&times;</button>'+
                                '</div>'
                            );
                        }
                    });
                    $('#andorinha_projeto_galeria').val(currentIds.join(','));
                });
                galeriaFrame.open();
            });

            // Remover Foto da Galeria
            $('#andorinha_galeria_container').on('click', '.remove-img', function(e){
                e.preventDefault();
                var item = $(this).closest('.andorinha-galeria-item');
                var idToRemove = item.data('id').toString();
                item.remove();

                var currentIds = $('#andorinha_projeto_galeria').val().split(',');
                var newIds = currentIds.filter(function(id){ return id !== idToRemove && id !== ''; });
                $('#andorinha_projeto_galeria').val(newIds.join(','));
            });
        });
        </script>
        <?php
    }
}
add_action( 'admin_enqueue_scripts', 'andorinha_admin_projetos_scripts' );
