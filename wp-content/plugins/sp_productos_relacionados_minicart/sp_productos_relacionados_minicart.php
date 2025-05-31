<?php

/*
* Plugin Name: SP - Productos relacionados Minicart
* Plugin URI: https://www.agenciasp.com
* Description: Utiliza el Hook del minicart para añadir un carrusel en la parte inferior.
* Version: 1.0.0
* Author: AgenciaSP
* Author URI: https://www.agenciasp.com
* License: AgenciaSP
* Text Domain: sp_productos_relacionados_minicart
* Domain Path: /languages
*/


if ( ! defined( 'ABSPATH' ) ) exit;

define('sp_productos_relacionados_minicart_PATH', realpath( dirname(__FILE__) ) );
define('sp_productos_relacionados_minicart_URL', plugins_url('/', __FILE__) );

if ( !class_exists('sp_productos_relacionados_minicart')){
	class sp_productos_relacionados_minicart {
        const ELEMENTOR_LOOP_TEMPLATE_ID = 1234;

        public function __construct() {
            add_action( 'woocommerce_after_mini_cart', [ $this, 'print_related_products_carousel' ], 20 );
        }

        protected function print_related_products_carousel() {
            $ids_query = new WP_Query( [
                'post_type'              => 'product',
                'posts_per_page'         => 16,
                'orderby'                => 'meta_value_num',
                'meta_key'               => 'total_sales',
                'order'                  => 'DESC',
                'fields'                 => 'ids',
                'no_found_rows'          => true,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
                'cache_results'          => true,
            ] );
            $all_ids = $ids_query->posts;
            if ( empty( $all_ids ) ) {
                return;
            }
            shuffle( $all_ids );
            $selected_ids = array_slice( $all_ids, 0, 8 );

            $products_query = new WP_Query( [
                'post_type'              => [ 'product', 'product_variation' ],
                'post__in'               => $selected_ids,
                'posts_per_page'         => 8,
                'orderby'                => 'post__in',
                'post_status'            => 'publish',
                'no_found_rows'          => true,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
                'cache_results'          => true,
            ] );
            if ( ! $products_query->have_posts() ) {
                return;
            }

            ob_start();
            ?>
            <section class="sp-mini-cart-carousel observer_fade_transform">
                <div class="sp-section-title">
                    <h2>Productos relacionados</h2>
                </div>
                <div
                    class="splide sp_carrusel"
                    id="mini-cart-related-carousel"
                    data-perPage="4"
                    data-gap="20"
                    data-autoplay="true"
                    data-breakpoints='{"1199":{"perPage":3},"767":{"perPage":2},"480":{"perPage":1}}'
                >
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php
                            // Añadimos la clase de slide a cada <li>
                            while ( $products_query->have_posts() ) :
                                $products_query->the_post();
                                ?>
                                <li class="splide__slide">
                                    <?php
                                    // Renderiza tu Loop Item de Elementor para este producto
                                    echo \Elementor\Plugin::instance()
                                          ->frontend
                                          ->get_builder_content_for_display( self::ELEMENTOR_LOOP_TEMPLATE_ID );
                                    ?>
                                </li>
                            <?php
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </ul>
                    </div>
                </div>
            </section>
            <script>
            document.addEventListener( 'DOMContentLoaded', function() {
                new Splide( '#mini-cart-related-carousel' ).mount();
            } );
            </script>
            <?php

            return ob_get_clean();
        }
    }
}



$GLOBALS['sp_productos_relacionados_minicart'] = new sp_productos_relacionados_minicart();