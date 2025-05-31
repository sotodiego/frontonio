<?php

/*
* Plugin Name: SP - Dependencias
* Plugin URI: https://www.agenciasp.com
* Description: Plugin para mantener todo lo global centralizado.
* Version: 1.0.0
* Author: AgenciaSP
* Author URI: https://www.agenciasp.com
* License: AgenciaSP
* Text Domain: sp_dependencias
* Domain Path: /languages
*/


if ( ! defined( 'ABSPATH' ) ) exit;

define('sp_dependencias_PATH', realpath( dirname(__FILE__) ) );
define('sp_dependencias_URL', plugins_url('/', __FILE__) );

if ( !class_exists('sp_dependencias')){
	class sp_dependencias {
		public function __construct(){
			add_action( 'wp_enqueue_scripts', [$this, 'sp_dependencias_encolar_scripts'] );
			add_action( 'admin_enqueue_scripts', [$this, 'sp_dependencias_encolar_scripts_admin'] );
			add_shortcode('copyright', [$this, 'sp_copyright_shortcode']);
			add_shortcode('redes_sociales', [$this, 'sp_redes_shortcode']);
        }

		function sp_dependencias_encolar_scripts_admin() {
			wp_enqueue_style('sp_popup', sp_dependencias_URL.'/assets/css/sp_popup.css');
            wp_enqueue_script('sp_popup', sp_dependencias_URL.'/assets/js/sp_popup.js', array('jquery'), '1.0.0', true);
		}

        
        function sp_dependencias_encolar_scripts() {
			wp_enqueue_style('sp_popup', sp_dependencias_URL.'/assets/css/sp_popup.css');
            wp_enqueue_script('sp_popup', sp_dependencias_URL.'/assets/js/sp_popup.js', array('jquery'), '1.0.0', true);

			wp_enqueue_script('sp_cookie', sp_dependencias_URL.'/assets/js/sp_cookie.js', array('jquery'), '1.0.0', true);

			wp_enqueue_style('sp_carrusel', sp_dependencias_URL.'/assets/css/sp_carrusel.css');
			wp_enqueue_script('sp_carrusel', sp_dependencias_URL.'/assets/js/sp_carrusel.js', array('jquery'), '1.0.0', true);

			wp_enqueue_script('sp_header', sp_dependencias_URL.'/assets/js/sp_header.js', array('jquery'), '1.0.0', true);
			wp_enqueue_style('sp_dependencias', sp_dependencias_URL.'/assets/css/sp_dependencias.css');

			wp_enqueue_style('splide_css', sp_dependencias_URL.'/assets/vendor/splide/splide.min.css');
			wp_enqueue_script('splide_js', sp_dependencias_URL.'/assets/vendor/splide/splide.min.js', array('jquery'), '3.6.9', true);

			wp_enqueue_style('sp_menus_plegables_footer', sp_dependencias_URL.'/assets/css/sp_menus_plegables_footer.css');
			wp_enqueue_script('sp_menus_plegables_footer', sp_dependencias_URL.'/assets/js/sp_menus_plegables_footer.js', array('jquery'), '1.0.0', true);
		}

		function sp_copyright_shortcode() {
			$year = date('Y');
			$blog_name = get_bloginfo('name');
			$copy_text = sprintf('© %s %s. ', $year, $blog_name);
			return $copy_text;
		}

		function sp_redes_shortcode() {
			ob_start();
			?>
			<ul style="display: flex;list-style-type: none;padding: 0;flex-wrap: nowrap;align-items: center;justify-content: center;gap: 10px;line-height: 0;">
				<li style="padding: 8px; width: 36px; background: #F56E28; border-radius: 100px;">
					<a target="blank" style="color: #fff;" title="Instagram <?=get_bloginfo('name')?>" href="#"><img src="<?=sp_dependencias_URL?>/assets/img/redes/instagram.png" alt="Instagram"></a>
				</li>
				<li style="padding: 8px; width: 36px; background: #F56E28; border-radius: 100px;">
					<a target="blank" style="color: #fff;" title="Facebook <?=get_bloginfo('name')?>" href="#"><img src="<?=sp_dependencias_URL?>/assets/img/redes/facebook.png" alt="Facebook"></a>
				</li>
				<li style="padding: 8px; width: 36px; background: #F56E28; border-radius: 100px;">
					<a target="blank" style="color: #fff;" title="Youtube <?=get_bloginfo('name')?>" href="#"><img src="<?=sp_dependencias_URL?>/assets/img/redes/youtube.png" alt="Youtube"></a>
				</li>
			</ul>
			<?php
			return ob_get_clean();
		}
		
    }

	
}



$GLOBALS['sp_dependencias'] = new sp_dependencias();