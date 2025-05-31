<?php
/*
 * Plugin Name: SP - Menu movil
 * Plugin URI: http://www.agenciasp.com
 * Description: Utiliza [sp_menu_movil] para añadir un menú móvil
 * Version: 1.0.0
 * Author: Agencia SP
 * Author URI: http://www.agenciasp.com
 * Text-domain: sp_menu_movil
 */

if (!defined('ABSPATH')) exit;

// Función para el botón del menú móvil
function sp_menu_movil_button() {
    ob_start();
    ?>
        <div class="hamburger-toggle" title="<?=__("Menú","sp_menu_movil")?>">
            <div class="user_icon_header"></div>
            <span><?= __("Menú","sp_menu_movil"); ?></span>
        </div>
    <?php
    return ob_get_clean();
}
add_shortcode('sp_menu_movil', 'sp_menu_movil_button');

function sp_menu_movil_content() {
    $header_nav_menu = wp_nav_menu( [
        'theme_location' => 'header-menu-movil',
        'fallback_cb' => false,
        'container' => false,
        'echo' => false,
        'menu_class' => 'sp-hamburger-menu', 
        'items_wrap' => '<ul class="%2$s">%3$s</ul>',
    ] );
    ?>
    <div class="menu_movil">
        <div class="hamburger-container">
            <div class="menu-header">
                <span class="menu-title"><?=__("Menú","sp_menu_movil")?></span>
                <button class="back-arrow"><span class="icono_cerrar">←</span> <span class="txt_cerrar"><?=__("Atrás","sp_menu_movil")?></span></button>
                <button class="menu-close"><span class="txt_cerrar"><?=__("Cerrar","sp_menu_movil")?></span> <span class="icono_cerrar">×</span></button>
            </div>
            <div class="menu-content">
                <?php echo $header_nav_menu; ?>
            </div>
        </div>
        <div class="overlay"></div>
    </div>
    <?php
}
add_action('wp_footer', 'sp_menu_movil_content');


function sp_enqueue_menu_movil() {
    wp_enqueue_style('sp-menu-movil-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_script('sp-menu-movil-scripts', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'sp_enqueue_menu_movil');

function sp_registrar_menu_movil(){
    if ( function_exists( 'register_nav_menus' ) ) {
        register_nav_menus(
            array(
              'header-menu-movil' => 'Header Menu Movil',
            )
        );
    }
}
add_action('init', 'sp_registrar_menu_movil');




add_filter( 'wp_nav_menu', function( $nav_menu, $args ) {
    if ( $args->theme_location === 'header-menu-movil' ) {
        // Encuentra cada <ul> asociado a un <li> con texto o enlace
        $nav_menu = preg_replace_callback(
            '/<li[^>]*>\s*(<a[^>]*>(.*?)<\/a>)?\s*<ul/',
            function( $matches ) {
                // Extrae el texto del enlace o el contenido del <li>
                $text = isset($matches[2]) ? strip_tags($matches[2]) : 'submenu';
                return str_replace(
                    '<ul',
                    '<ul data-currentTitle="' . esc_attr( $text ) . '"',
                    $matches[0]
                );
            },
            $nav_menu
        );
    }
    return $nav_menu;
}, 10, 2 );