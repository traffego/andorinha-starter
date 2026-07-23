<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
        <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon-128x128.png" type="image/x-icon">
        
        <script type="text/javascript">
            (function(){
                if (typeof emailjs !== 'undefined') {
                    emailjs.init({
                        publicKey: "82rOJk9K1ylMaxAL8",
                    });
                }
            })();
        </script>
        
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <?php wp_body_open(); ?>

        <section data-bs-version="5.1" class="menu menu3 cid-upKKNxOW0i" once="menu" id="menu03-0">
            <nav class="navbar navbar-dropdown navbar-fixed-top navbar-expand-lg">
                <div class="container">
                    <div class="navbar-brand">
                        <span class="navbar-logo">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <?php 
                                if ( has_custom_logo() ) {
                                    the_custom_logo();
                                } else {
                                    ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/loogo-marca-354x354.png"
                                         alt="<?php bloginfo( 'name' ); ?>"
                                         style="height: 10rem;">
                                    <?php
                                }
                                ?>
                            </a>
                        </span>
                    </div>
                    <button class="navbar-toggler" type="button"
                        data-toggle="collapse" data-bs-toggle="collapse"
                        data-target="#navbarSupportedContent"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarNavAltMarkup" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <div class="hamburger">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <?php
                        if ( has_nav_menu( 'primary' ) ) {
                            wp_nav_menu( array(
                                'theme_location' => 'primary',
                                'container'      => false,
                                'menu_class'     => 'navbar-nav nav-dropdown me-auto',
                                'fallback_cb'    => false,
                            ) );
                        } else {
                            ?>
                            <ul class="navbar-nav nav-dropdown me-auto" data-app-modern-menu="true">
                                <li class="nav-item">
                                    <a class="nav-link link text-black display-4" href="<?php echo esc_url( home_url( '/' ) ); ?>#content4-5">Quem Somos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link link text-black display-4" href="<?php echo esc_url( home_url( '/' ) ); ?>#content4-d">Soluções</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link link text-black display-4" href="<?php echo esc_url( home_url( '/' ) ); ?>#form3-g">Localização</a>
                                </li>
                            </ul>
                            <?php
                        }
                        ?>

                        <div class="navbar-buttons mbr-section-btn me-lg-2" style="margin-right: 1.3rem;">
                            <a class="btn btn-primary display-4" href="<?php echo esc_url( home_url( '/' ) ); ?>#form3-g">Fale com um Consultor</a>
                        </div>
                    </div>
                </div>
            </nav>
        </section>
