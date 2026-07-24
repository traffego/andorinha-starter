<?php
/**
 * Functions and definitions for Andorinha Starter Theme
 *
 * @package AndorinhaStarter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* =============================================
   SETUP DO TEMA
   ============================================= */
function andorinha_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', array(
        'height'      => 354,
        'width'       => 354,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'script', 'style',
    ) );
    register_nav_menus( array(
        'primary' => __( 'Menu Principal', 'andorinha-starter' ),
    ) );
    add_theme_support( 'elementor' );
}
add_action( 'after_setup_theme', 'andorinha_theme_setup' );

/* =============================================
   ENFILEIRAR ESTILOS (frontend + elementor preview)
   ============================================= */
function andorinha_enqueue_styles() {
    $uri = get_template_directory_uri();
    $v   = wp_get_theme()->get( 'Version' );

    // 1. Google Fonts - Epilogue (fonte principal do tema)
    wp_enqueue_style( 'google-font-epilogue',
        'https://fonts.googleapis.com/css?family=Epilogue:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i&display=swap',
        array(), null
    );

    // 1b. Font Awesome 6 Free
    wp_enqueue_style( 'font-awesome-6',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
        array(), '6.5.2'
    );

    // 2. Bootstrap 5
    wp_enqueue_style( 'bootstrap',          $uri . '/assets/bootstrap/css/bootstrap.min.css',       array(), $v );
    wp_enqueue_style( 'bootstrap-grid',     $uri . '/assets/bootstrap/css/bootstrap-grid.min.css',  array(), $v );
    wp_enqueue_style( 'bootstrap-reboot',   $uri . '/assets/bootstrap/css/bootstrap-reboot.min.css',array(), $v );

    // 3. Mobirise Icons
    wp_enqueue_style( 'mobirise2-icons',    $uri . '/assets/web/assets/mobirise-icons2/mobirise2.css', array(), $v );

    // 4. Parallax / Jarallax
    wp_enqueue_style( 'jarallax-css',       $uri . '/assets/parallax/jarallax.css',    array(), $v );

    // 5. Animate.css
    wp_enqueue_style( 'animate-css',        $uri . '/assets/animatecss/animate.css',   array(), $v );

    // 6. Dropdown navbar
    wp_enqueue_style( 'dropdown-css',       $uri . '/assets/dropdown/css/style.css',   array(), $v );

    // 7. Mobirise theme style (classes .display-1..7, .mbr-*, sections, etc)
    wp_enqueue_style( 'mobirise-theme-css', $uri . '/assets/theme/css/style.css',      array(), $v );

    // 8. Mobirise additional (tipografia, cores, seções, botões Mobirise)
    wp_enqueue_style( 'mbr-additional-css', $uri . '/assets/mobirise/css/mbr-additional.css', array(), $v );

    // 9. style.css do tema (variáveis + overrides)
    wp_enqueue_style( 'andorinha-main',     get_stylesheet_uri(),                      array(), $v );
}
add_action( 'wp_enqueue_scripts', 'andorinha_enqueue_styles' );

/* =============================================
   ENFILEIRAR SCRIPTS (frontend)
   ============================================= */
function andorinha_enqueue_scripts() {
    $uri = get_template_directory_uri();
    $v   = wp_get_theme()->get( 'Version' );

    // EmailJS
    wp_enqueue_script( 'emailjs', 'https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js', array(), null, false );

    // Bootstrap bundle
    wp_enqueue_script( 'bootstrap-bundle',      $uri . '/assets/bootstrap/js/bootstrap.bundle.min.js', array('jquery'), $v, true );

    // Parallax / Jarallax
    wp_enqueue_script( 'jarallax-js',           $uri . '/assets/parallax/jarallax.js',                 array('jquery'), $v, true );

    // Smooth scroll
    wp_enqueue_script( 'smooth-scroll',         $uri . '/assets/smoothscroll/smooth-scroll.js',        array('jquery'), $v, true );

    // YT Player
    wp_enqueue_script( 'ytplayer',              $uri . '/assets/ytplayer/index.js',                    array('jquery'), $v, true );

    // Vimeo Player
    wp_enqueue_script( 'vimeoplayer',           $uri . '/assets/vimeoplayer/player.js',                array('jquery'), $v, true );

    // Navbar dropdown
    wp_enqueue_script( 'navbar-dropdown',       $uri . '/assets/dropdown/js/navbar-dropdown.js',       array('jquery'), $v, true );

    // Mobirise theme script
    wp_enqueue_script( 'mobirise-theme-script', $uri . '/assets/theme/js/script.js',                   array('jquery'), $v, true );

    // Formoid
    wp_enqueue_script( 'formoid',               $uri . '/assets/formoid/formoid.min.js',               array('jquery'), $v, true );
}
add_action( 'wp_enqueue_scripts', 'andorinha_enqueue_scripts' );

/* =============================================
   CARREGAR ESTILOS NO EDITOR ELEMENTOR
   Para que o preview dentro do Elementor
   renderize com os mesmos estilos do frontend
   ============================================= */
function andorinha_elementor_preview_styles() {
    andorinha_enqueue_styles();
}
add_action( 'elementor/preview/enqueue_styles', 'andorinha_elementor_preview_styles' );

function andorinha_elementor_editor_styles() {
    $uri = get_template_directory_uri();
    $v   = wp_get_theme()->get( 'Version' );

    // Carregar estilos essenciais no iframe do editor
    wp_enqueue_style( 'google-font-epilogue-editor',
        'https://fonts.googleapis.com/css?family=Epilogue:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i&display=swap',
        array(), null
    );
    wp_enqueue_style( 'font-awesome-6-editor',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
        array(), '6.5.2'
    );
    wp_enqueue_style( 'bootstrap-editor',       $uri . '/assets/bootstrap/css/bootstrap.min.css',         array(), $v );
    wp_enqueue_style( 'mobirise-theme-editor',  $uri . '/assets/theme/css/style.css',                     array(), $v );
    wp_enqueue_style( 'mbr-additional-editor',  $uri . '/assets/mobirise/css/mbr-additional.css',         array(), $v );
    wp_enqueue_style( 'andorinha-main-editor',  get_stylesheet_uri(),                                     array(), $v );
}
add_action( 'elementor/editor/after_enqueue_styles', 'andorinha_elementor_editor_styles' );


/* =============================================
   CLASSES CSS PARA ITENS DO MENU WP
   ============================================= */
function andorinha_menu_link_class( $atts, $item, $args ) {
    if ( isset( $args->theme_location ) && $args->theme_location === 'primary' ) {
        $atts['class'] = 'nav-link link text-black display-4';
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'andorinha_menu_link_class', 10, 3 );

function andorinha_menu_item_class( $classes, $item, $args ) {
    if ( isset( $args->theme_location ) && $args->theme_location === 'primary' ) {
        $classes[] = 'nav-item';
    }
    return $classes;
}
add_filter( 'nav_menu_css_class', 'andorinha_menu_item_class', 10, 3 );

/* =============================================
   CUSTOM POST TYPE: PROJETOS REALIZADOS
   ============================================= */
require_once get_template_directory() . '/inc/cpt-projetos.php';

/* =============================================
   ELEMENTOR WIDGET: LISTA DE PROJETOS
   ============================================= */
require_once get_template_directory() . '/inc/elementor-widget-projetos.php';


