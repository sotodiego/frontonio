<?php
/**
* Plugin Name: Correos Express Plugin
* Plugin URI:
* Description: Module for the management of shipments and synchronization with CorreosExpress
* Version: 3.1.0
* Author: Correos Express
* Author URI:
* License: GPL2
* License URI:
* Text Domain: CorreosExpress - Shipping Management - Tags
* Domain Path: /languages
*$tabla29 = cex_migration46($wpdb);
* @package CorreosExpress
*
* Correos Express Plugin is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 2 of the License, or
* any later version.
*
* Correos Express Plugin is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY;without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Correos Express Plugin. If not, see {License URI}.
*/
/**
 * Returns the main instance of WC.
 *
 * @since  2.0
 * @return CorreosExpress
 */

defined('ABSPATH') || exit;

class CorreosExpress
{
    public function __construct()
    {
        $this->cex_includes();
        $this->cex_init_hooks();
    }
    public function cex_includes()
    {
        //CREACION DE NUESTRAS PROPIAS LIBRERIAS EXTERNAS
        require_once plugin_dir_path(__FILE__).'lib/helpers.php';
        require_once plugin_dir_path(__FILE__).'lib/soap.php';
        require_once plugin_dir_path(__FILE__).'lib/rest.php';
        require_once plugin_dir_path(__FILE__).'lib/class_etiquetas_vuelo.php';
        require_once plugin_dir_path(__FILE__).'lib/migrations.php';
        require_once plugin_dir_path(__FILE__).'lib/seeding.php';        
        require_once plugin_dir_path(__FILE__).'cron.php';
    }

    private function cex_init_hooks()
    {
        register_activation_hook(__FILE__, array('CorreosExpress','cex_install'));
        register_deactivation_hook(__FILE__, array('CorreosExpress','cex_deactivation'));
        register_uninstall_hook(__FILE__, array('CorreosExpress','cex_unistall'));


        add_action('plugins_loaded', array($this,'cex_load_plugin_textdomain'));
        if (is_admin()) {
            if (isset($_GET['post']) && intval($_GET['post'])) {
                $cexPost=get_post(intval($_GET['post']));
                $cexPostType=$cexPost->post_type;
            }
            if ((isset($cexPostType) && $cexPostType=='shop_order') || (isset($_GET['page']) && strpos(sanitize_text_field($_GET['page']), 'correosexpress')!==false)) {
                add_action('admin_enqueue_scripts', array($this,'CEX_stylesBootstrap'), 2);
                add_action('admin_enqueue_scripts', array($this,'CEX_styles'), 2);
                add_action('admin_enqueue_scripts', array($this,'CEX_scripts'));
                add_action('wc-admin-order-meta-boxes', array($this,'CEX_scripts_datepicker'));
                add_action('admin_enqueue_scripts', array($this,'CEX_styles_datepicker'));
                add_action('wc-admin-order-meta-boxes', array($this,'CEX_scripts_datatable'));
                add_action('admin_enqueue_scripts', array($this,'CEX_styles_datatable'), 1);
                add_action('wp_enqueue_script', array($this,'CEX_introJS'));
            }
            add_action('admin_enqueue_scripts', array($this,'CEX_styles_menu'));
        }
        if (!is_admin()) {
            add_action('admin_enqueue_scripts', array($this,'CEX_styles_front'));
            add_action('admin_enqueue_scripts', array($this,'CEX_scripts_contador'));
        }
        //MENU ADMIN
        add_action('admin_menu', array($this, 'cex_option_page'));
        add_action('init', array($this,'cex_register_preparacion_envio_order_status'));
        add_filter('wc_order_statuses', array($this,'cex_add_preparacion_envio_to_order_statuses'));
        add_action('add_meta_boxes', array($this,'cex_shop_order_formulario_config'));
        //add_filter('woocommerce_reports_order_statuses', array($this,'cex_include_custom_order_status_to_reports'), 20, 1);
        add_filter('cron_schedules', array($this,'cex_interval_function'));
        add_action('cex_cron', array($this,'cex_cron_function'));
        //A?ADIMOS UNA TAREA Y ASIGNAMOS EL INTERVALO        
        if (!wp_next_scheduled('cex_cron')) {            
            wp_schedule_event(time(), 'cex_interval', 'cex_cron');
        }
        add_filter('woocommerce_locate_template', array($this,'cex_woo_adon_plugin_template'), 1, 3);
        add_action('woocommerce_after_order_notes', array($this,'cex_entrega_oficina'));
        add_action('woocommerce_checkout_update_order_meta', array($this,'cex_entrega_oficina_update_order_meta'));
        add_action('woocommerce_admin_order_data_after_billing_address', array($this,'cex_entrega_oficina_display_admin_order_meta'), 10, 1);
        add_action('wp_ajax_cex_form_comercial', array($this,'cex_form_comercial'));
        add_action('wp_ajax_nopriv_cex_form_comercial', array($this,'cex_form_comercial'));
        add_action('wp_ajax_cex_get_init_form', array($this,'cex_get_init_form'));
        add_action('wp_ajax_nopriv_cex_get_init_form', array($this,'cex_get_init_form'));
        add_action('wp_ajax_cex_retornar_codigo_cliente', array($this,'cex_retornar_codigo_cliente'));
        add_action('wp_ajax_nopriv_cex_retornar_codigo_cliente', array($this,'cex_retornar_codigo_cliente'));
        add_action('wp_ajax_cex_guardar_codigo_cliente', array($this,'cex_guardar_codigo_cliente'));
        add_action('wp_ajax_nopriv_cex_guardar_codigo_cliente', array($this,'cex_guardar_codigo_cliente'));
        add_action('wp_ajax_cex_actualizar_codigo_cliente', array($this,'cex_actualizar_codigo_cliente'));
        add_action('wp_ajax_nopriv_cex_actualizar_codigo_cliente', array($this,'cex_actualizar_codigo_cliente'));
        add_action('wp_ajax_cex_borrar_codigo_cliente', array($this,'cex_borrar_codigo_cliente'));
        add_action('wp_ajax_nopriv_cex_borrar_codigo_cliente', array($this,'cex_borrar_codigo_cliente'));
        add_action('wp_ajax_cex_retornar_remitente', array($this,'cex_retornar_remitente'));
        add_action('wp_ajax_nopriv_cex_retornar_remitente', array($this,'cex_retornar_remitente'));
        add_action('wp_ajax_cex_guardar_remitente', array($this,'cex_guardar_remitente'));
        add_action('wp_ajax_nopriv_cex_guardar_remitente', array($this,'cex_guardar_remitente'));
        add_action('wp_ajax_cex_actualizar_remitente', array($this,'cex_actualizar_remitente'));
        add_action('wp_ajax_nopriv_cex_actualizar_remitente', array($this,'cex_actualizar_remitente'));
        add_action('wp_ajax_cex_borrar_remitente', array($this,'cex_borrar_remitente'));
        add_action('wp_ajax_nopriv_cex_borrar_remitente', array($this,'cex_borrar_remitente'));
        add_action('wp_ajax_cex_guardarRemitenteDefecto', array($this,'cex_guardarRemitenteDefecto'));
        add_action('wp_ajax_nopriv_cex_guardarRemitenteDefecto', array($this,'cex_guardarRemitenteDefecto'));
        add_action('wp_ajax_cex_get_user_config', array($this,'cex_get_user_config'));
        add_action('wp_ajax_nopriv_cex_get_user_config', array($this,'cex_get_user_config'));
        add_action('wp_ajax_cex_guardar_customer_options', array($this,'cex_guardar_customer_options'));
        add_action('wp_ajax_nopriv_cex_guardar_customer_options', array($this,'cex_guardar_customer_options'));
        add_action('wp_ajax_cex_guardar_credenciales', array($this,'cex_guardar_credenciales'));
        add_action('wp_ajax_nopriv_cex_guardar_credenciales', array($this,'cex_guardar_credenciales'));
        add_action('wp_ajax_cex_guardar_options_cron', array($this,'cex_guardar_options_cron'));
        add_action('wp_ajax_nopriv_cex_guardar_options_cron', array($this,'cex_guardar_options_cron'));
        add_action('wp_ajax_cex_guardar_productos', array($this,'cex_guardar_productos'));
        add_action('wp_ajax_nopriv_cex_guardar_productos', array($this,'cex_guardar_productos'));
        add_filter('wc_get_order_statuses', array($this,'cex_retornar_estados_productos'));
        add_action('wp_ajax_cex_retornar_mapeo_transportistas', array($this,'cex_retornar_mapeo_transportistas'));
        add_action('wp_ajax_nopriv_cex_retornar_mapeo_transportistas', array($this,'cex_retornar_mapeo_transportistas'));
        add_action('wp_ajax_cex_guardar_mapeo_transportistas', array($this,'cex_guardar_mapeo_transportistas'));
        add_action('wp_ajax_nopriv_cex_guardar_mapeo_transportistas', array($this,'cex_guardar_mapeo_transportistas'));
        add_action('wp_ajax_cex_form_pedido', array($this,'cex_form_pedido'));
        add_action('wp_ajax_nopriv_cex_form_pedido', array($this,'cex_form_pedido'));
        add_action('wp_ajax_cex_form_pedidos', array($this,'cex_form_pedidos'));
        add_action('wp_ajax_nopriv_cex_form_pedidos', array($this,'cex_form_pedidos'));
        add_action('wp_ajax_cex_form_order_template', array($this,'cex_form_order_template'));
        add_action('wp_ajax_nopriv_cex_form_order_template', array($this,'cex_form_order_template'));
        add_action('wp_ajax_cex_obtener_Productos_Cex', array($this,'cex_obtener_Productos_Cex'));
        add_action('wp_ajax_nopriv_cex_obtener_Productos_Cex', array($this,'cex_obtener_Productos_Cex'));
        add_action('wp_ajax_cex_retornar_precio_pedido', array($this, 'cex_retornar_precio_pedido'));
        add_action('wp_ajax_nopriv_cex_retornar_precio_pedido', array($this, 'cex_retornar_precio_pedido'));
        add_action('wp_ajax_cex_retornar_savedships_orden_id', array($this, 'cex_retornar_savedships_orden_id'));
        add_action('wp_ajax_nopriv_cex_retornar_savedships_orden_id', array($this, 'cex_retornar_savedships_orden_id'));
        add_action('wp_ajax_cex_retornar_refencias_dia', array($this, 'cex_retornar_refencias_dia'));
        add_action('wp_ajax_nopriv_cex_retornar_refencias_dia', array($this, 'cex_retornar_refencias_dia'));
        add_action('wp_ajax_cex_generar_etiquetas', array($this, 'cex_generar_etiquetas'));
        add_action('wp_ajax_nopriv_cex_generar_etiquetas', array($this, 'cex_generar_etiquetas'));
        add_action('wp_ajax_cex_generar_resumen', array($this, 'cex_generar_resumen'));
        add_action('wp_ajax_nopriv_cex_generar_resumen', array($this, 'cex_generar_resumen'));
        add_action('wp_ajax_cex_get_init_utilities_form', array($this, 'cex_get_init_utilities_form'));
        add_action('wp_ajax_nopriv_cex_get_init_utilities_form', array($this, 'cex_get_init_utilities_form'));
        add_action('wp_ajax_cex_sacar_transportistas_oficina', array($this, 'cex_sacar_transportistas_oficina'));
        add_action('wp_ajax_nopriv_cex_sacar_transportistas_oficina', array($this, 'cex_sacar_transportistas_oficina'));
        add_action('wp_ajax_cex_obtener_pedidos_busqueda', array($this, 'cex_obtener_pedidos_busqueda'));
        add_action('wp_ajax_nopriv_cex_obtener_pedidos_busqueda', array($this, 'cex_obtener_pedidos_busqueda'));
        add_action('wp_ajax_cex_soft_delete_savedShip', array($this, 'cex_soft_delete_savedShip'));
        add_action('wp_ajax_nopriv_cex_soft_delete_savedShip', array($this, 'cex_soft_delete_savedShip'));
        add_action('wp_ajax_cex_form_pedido_borrar', array($this, 'cex_form_pedido_borrar'));
        add_action('wp_ajax_cex_retornar_destinatario', array($this, 'cex_retornar_destinatario'));
        add_action('wp_ajax_nopriv_cex_retornar_destinatario', array($this, 'cex_retornar_destinatario'));
        add_action('wp_ajax_cex_validar_credenciales', array($this, 'cex_validar_credenciales'));
        add_action('wp_ajax_nopriv_cex_validar_credenciales', array($this, 'cex_validar_credenciales'));
        add_action('wp_ajax_generarLogSoporte', array($this, 'generarLogSoporte'));
        add_action('wp_ajax_nopriv_generarLogSoporte', array($this, 'generarLogSoporte'));
        add_action('wp_ajax_cex_eliminar_logo', array($this, 'cex_eliminar_logo'));
        add_action('wp_ajax_nopriv_cex_eliminar_logo', array($this, 'cex_eliminar_logo'));
        add_action('wp_ajax_cex_guardar_imagen_logo', array($this, 'cex_guardar_imagen_logo'));
        add_action('wp_ajax_nopriv_cex_guardar_imagen_logo', array($this, 'cex_guardar_imagen_logo'));
        add_action('wp_ajax_cex_cron_function', array($this, 'cex_cron_function'));
        add_action('wp_ajax_nopriv_cex_cron_function', array($this, 'cex_cron_function'));        
        add_action( 'upgrader_process_complete', array($this,'cex_ejecutarUpdateDB') );
        add_action('wp_ajax_cex_generar_informe_cron', array($this,'cex_generar_informe_cron'));
        add_action('wp_ajax_nopriv_cex_generar_informe_cron', array($this,'cex_generar_informe_cron'));

        add_action('wp_ajax_cex_borrar_carpeta_log', array($this,'cex_borrar_carpeta_log'));
        add_action('wp_ajax_nopriv_cex_borrar_carpeta_log', array($this,'cex_borrar_carpeta_log'));




    }

    public function cex_notice()
    {
        /* translators: 1. URL link. */
        echo '<div class="error"><p><strong>' . sprintf(esc_html__('Correos Express requiere que WooCommerce este instalado y activo. Puedes descargar %s aqu&iacute;.', 'cex_pluggin'), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>') . '</strong></p></div>';
    }

    public static function cex_install()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        if (install_db()) {
            return true;
        }
        return false;
    }

    public static function cex_deactivation()
    {
        //limpiador de enlaces permanentes
        flush_rewrite_rules();
    }

    public static function cex_unistall()
    {
        global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        //borrar tablas en este caso las migraciones de arriba
        $nombreTabla = $wpdb->prefix.'cex_migrations';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);

        $nombreTabla = $wpdb->prefix.'cex_savedships';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);

        $nombreTabla = $wpdb->prefix.'cex_savedsenders';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);

        $nombreTabla = $wpdb->prefix.'cex_savedmodeships';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);

        $nombreTabla = $wpdb->prefix.'cex_officedeliverycorreo';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);

        $nombreTabla = $wpdb->prefix.'cex_customer_options';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);

        $nombreTabla = $wpdb->prefix.'cex_customer_codes';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);

        $nombreTabla = $wpdb->prefix.'cex_history';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);

        $nombreTabla = $wpdb->prefix.'cex_envios_bultos';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);

        $nombreTabla = $wpdb->prefix.'cex_respuesta_cron';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);

        $nombreTabla = $wpdb->prefix.'cex_envio_cron';
        $sql = "DROP TABLE IF EXISTS $nombreTabla";
        $deleted = $wpdb->query($sql);
    }

    public function cex_load_plugin_textdomain()
    {
        load_plugin_textdomain('cex_pluggin', false, basename(dirname(__FILE__)) . '/languages/');
        if (! class_exists('WooCommerce')) {
            add_action('admin_notices', array($this,'cex_notice' ));
            return;
        }
    }

    public function CEX_stylesBootstrap()
    {
        // Register our script.
        if (is_admin()) {
            wp_enqueue_style('correosexpress-bootstrapCSS', plugins_url('/views/css/bootstrap.min.css', __FILE__));
            wp_enqueue_style('correosexpress-bootstrapTheme', plugins_url('/views/css/bootstrap-theme.css', __FILE__));
        }
    }

    public function CEX_styles()
    {
        //wp_enqueue_style('correosexpress-bootstrapCSS', plugins_url('/views/css/bootstrap.min.css',__FILE__));
        wp_enqueue_style('correosexpress-fontawesome', plugins_url('/views/css/fontawesome.min.css', __FILE__));
        //wp_enqueue_style('correosexpress-bootstrapTheme', plugins_url('/views/css/bootstrap-theme.css',__FILE__));
        wp_enqueue_style('correosexpress-pnotify', plugins_url('/views/css/pnotify.custom.min.css', __FILE__));
        wp_enqueue_style('correosexpress-introjsCSS', plugins_url('/views/css/introjs.css', __FILE__));
        wp_enqueue_style('correosexpress-introjsTheme', plugins_url('/views/css/introjs_modern_theme.css', __FILE__));
        wp_enqueue_style('correosexpress', plugins_url('/views/css/correosexpress.css', __FILE__));
    }

    public function CEX_styles_front()
    {
        wp_enqueue_style('correosexpress', plugins_url('/views/css/correosexpress.css', __FILE__));
    }
    
    public function CEX_scripts()
    {
        wp_enqueue_script('correosexpress-bootstrapJS', plugins_url('views/js/bootstrap.min.js', __FILE__));
        wp_enqueue_script('correosexpress-pnotifyJS', plugins_url('views/js/pnotify.custom.min.js', __FILE__));
    }

    public function CEX_scripts_contador()
    {
        wp_enqueue_script('correosexpress-contadorText', plugins_url('views/js/textareaCounter.js', __FILE__),array(),false,true);
    }

    public function CEX_scripts_datepicker()
    {
        //wp_enqueue_script( array('jquery', 'moment'));


        //wp_enqueue_script('correosexpress-jquery',  plugins_url('views/js/jquery-3.5.1.min.js', __FILE__));
        wp_enqueue_script('correosexpress-momentjs',  plugins_url('views/js/moment-with-locales.min.js', __FILE__));
        wp_enqueue_script('correosexpress-tempus-dominus', plugins_url( 'views/js/tempusdominus-bootstrap-4.js', __FILE__ ));

        //wp_enqueue_script('correosexpress-momentjs',    plugins_url('lib/bootstrap4datepicker/moment-with-locales.js', __FILE__));
        //wp_enqueue_script('correosexpress-tempusdominus', plugins_url('lib/bootstrap4datepicker/tempusdominus-bootstrap-4.js', __FILE__));
        //wp_enqueue_script( 'wp-tempus-dominus-auto', plugins_url( 'lib/wp-tempus-dominus-auto.js', __FILE__ ), array('jquery', 'jquery-core'), 100 );


    }

    public function CEX_styles_datepicker()
    {
        //wp_enqueue_style('correosexpress-datepicker', plugins_url('/views/css/gijgo.min.css', __FILE__));
        //wp_enqueue_style( 'correosexpress-tempusdominus-css', plugins_url('lib/bootstrap4datepicker/tempusdominus-bootstrap-4.css', __FILE__));
        wp_enqueue_style( 'correosexpress-tempusdominus-css', plugins_url('views/css/tempusdominus-bootstrap-4.css', __FILE__));

    }

    public function CEX_scripts_datatable()
    {
        wp_enqueue_script('correosexpress-datatableJS', plugins_url('views/datatables/datatables.min.js', __FILE__));
        wp_enqueue_script('correosexpress-datatableButtonsJS', plugins_url('views/datatables/Buttons-1.5.6/js/dataTables.buttons.min.js', __FILE__));
        wp_enqueue_script('correosexpress-datatableButtonsColvisJS', plugins_url('views/datatables/Buttons-1.5.6/js/buttons.colVis.min.js', __FILE__));


        wp_register_script( 'dataTables.bootstrap4', 'https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js', null, null, true );
        wp_enqueue_script('dataTables.bootstrap4');
    }


    public function CEX_styles_datatable()
    {
        wp_enqueue_style('correosexpress-datatable', plugins_url('/views/datatables/datatables.min.css', __FILE__));
        wp_enqueue_style('correosexpress-datatable-Buttons', plugins_url('/views/datatables/Buttons-1.5.6/css/buttons.dataTables.min.css', __FILE__));

    }

    public function CEX_introJS($view)
    {
        wp_enqueue_script('correosexpress-introJS', plugins_url('views/js/intro.js', __FILE__));
        if ($view!='') {
            $viewUrl='views/js/introJS-'.$view.'.js';
            wp_enqueue_script('correosexpress-introJS-'.$view, plugins_url($viewUrl, __FILE__));
        }
    }

    public function CEX_styles_menu()
    {
        if (is_admin()) {
            wp_enqueue_style('correosexpress-menu', plugins_url('/views/css/correosexpress-menu.css', __FILE__));
        }
    }

    //SE A?ADE UNA OPCION AL MENU ==> cex_option_page
    public function cex_option_page()
    {
        $menus = [];
        $submenus = [];

        $menus[] = [
            'page_title'        => esc_html(__('CEX Opciones', 'cex_pluggin')),
            'menu_title'        =>'Correos Express',
            'capability'        =>'manage_options',
                //'menu_slug'         => plugin_dir_path(__FILE__).'views/templates/formulario_inicial.php',
            'menu_slug'         => 'correosexpress',
            'functionName'      => array($this, 'cex_url_page_inicio'),
            'icon_url'          => plugin_dir_url(__FILE__).'views/img/favicon.ico',
            'position'          => 15
        ];
        add_action('admin_menu', array($this, 'extra_post_info_menu'));
        function extra_post_info_menu()
        {
            $page_title = 'WordPress Extra Post Info';
            $menu_title = 'Extra Post Info';
            $capability = 'manage_woocommerce';
            $menu_slug  = 'extra-post-info';
            $function   = 'extra_post_info_page';
            $icon_url   = 'dashicons-media-code';
            $position   = 4;
            add_menu_page(
                $page_title,
                $menu_title,
                $capability,
                $menu_slug,
                $function,
                $icon_url,
                $position
            );
        }
        $submenu1[] = [
                //'parent_slug'       => plugin_dir_path(__FILE__).'views/templates/formulario_inicial.php',
            'parent_slug'       => 'correosexpress',
            'page_title'        => esc_html(__('CEX Ajustes', 'cex_pluggin')),
            'menu_title'        => esc_html(__('Ajustes', 'cex_pluggin')),
            'capability'        =>'manage_options',
                //'menu_slug'         => plugin_dir_path(__FILE__).'views/templates/formulario_ajustes.php',
            'menu_slug'         => 'correosexpress-ajustes',
            'function'          => array($this, 'cex_url_page_ajustes')

        ];

        $submenu2[] = [
                //'parent_slug'       => plugin_dir_path(__FILE__).'views/templates/formulario_inicial.php',
            'parent_slug'       => 'correosexpress',
            'page_title'        => esc_html(__('CEX Utilidades', 'cex_pluggin')),
            'menu_title'        => esc_html(__('Utilidades', 'cex_pluggin')),
            'capability'        =>'manage_woocommerce',
                //'menu_slug'         => plugin_dir_path(__FILE__).'views/templates/utilidades.php',
            'menu_slug'         => 'correosexpress-utilidades',
            'function'          => array($this, 'cex_url_page_utilidades')
        ];

        addMenusPage($menus);
        addSubMenusPage($submenu1);
        addSubMenusPage($submenu2);
    }

    public function cex_url_page_inicio()
    {
        $url='views/templates/formulario_inicial.php';
        require_once plugin_dir_path(__FILE__) . $url;
    }
    public function cex_url_page_ajustes()
    {
        $url='views/templates/formulario_ajustes.php';
        require_once plugin_dir_path(__FILE__) . $url;
    }
    public function cex_url_page_utilidades()
    {
        $url='views/templates/utilidades.php';

        require_once plugin_dir_path(__FILE__) . $url;
    }

    //Crear un nuevo estado para a?adir

    public function cex_register_preparacion_envio_order_status()
    {
        register_post_status('wc-sending-cex', array(
            'label'                     => esc_html(__('En curso cex', 'cex_pluggin')),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('En curso cex <span class="count">(%s)</span>', 'En curso cex <span class="count">(%s)</span>')
        ));
        register_post_status('wc-cancelled-cex', array(
            'label'                     => esc_html(__('Anulado cex', 'cex_pluggin')),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Anulado cex <span class="count">(%s)</span>', 'Anulado cex <span class="count">(%s)</span>')
        ));
        register_post_status('wc-returned-cex', array(
            'label'                     => esc_html(__('Devuelto cex', 'cex_pluggin')),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Devuelto cex <span class="count">(%s)</span>', 'Entregado cex <span class="count">(%s)</span>')
        ));
        register_post_status('wc-delivered-cex', array(
            'label'                     => esc_html(__('Entregado cex', 'cex_pluggin')),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Entregado cex <span class="count">(%s)</span>', 'Entregado cex <span class="count">(%s)</span>')
        ));
    }

    // A?adir el estado nuevo al desplegable los ya existentes
    public function cex_add_preparacion_envio_to_order_statuses($order_statuses)
    {
        $new_order_statuses = array();

        // A?adir el estado despues de en espera
        foreach ($order_statuses as $key => $status) {
            $new_order_statuses[ $key ] = $status;

            if ('wc-on-hold' === $key) {
                $new_order_statuses['wc-sending-cex'] = esc_html(__('En curso cex', 'cex_pluggin'));
            }
            if ('wc-completed' === $key) {
                $new_order_statuses['wc-delivered-cex'] = esc_html(__('Entregado cex', 'cex_pluggin'));
            }
            if ('wc-cancelled' === $key) {
                $new_order_statuses['wc-cancelled-cex'] = esc_html(__('Anulado cex', 'cex_pluggin'));
            }
            if ('wc-refunded' === $key) {
                $new_order_statuses['wc-returned-cex'] = esc_html(__('Devuelto cex', 'cex_pluggin'));
            }
        }

        return $new_order_statuses;
    }

   /* public function cex_include_custom_order_status_to_reports($statuses)
    {
        return array('processing', 'shipped', 'completed', 'on-hold', 'delivered-cex', 'sending-cex');
    }*/


    public function cex_shop_order_formulario_config($post)
    {

        $retorno;
        $id_order = get_the_id();
        global $wpdb;
        $table = $wpdb->prefix.'cex_savedships';   
        $results = $wpdb->get_var($wpdb->prepare(" SELECT id_order
            FROM $table 
            WHERE id_order =  $id_order
            AND deleted_at is NULL",null));

        
        $historico='';
        $registro='';
        $registro = esc_html(__('Registro Env&iacute;o Correos Express', 'cex_pluggin'));
        add_meta_box(
            'woocommerce-order-cex',
            $registro,
            array($this,'cex_formulario_order'),
            'shop_order',
            'advanced',
            'default'
        );
        
        if ($results!=NULL){
            $historico = esc_html(__('Hist&oacute;rico Env&iacute;o Correos Express', 'cex_pluggin'));
            add_meta_box(
                'woocommerce-history-cex',
                $historico,
                array($this,'cex_historico_order'),
                'shop_order',
                'advanced',
                'default'
            );
        }
    }

    public function cex_shop_order_manual()
    {
        $manual='<div id="CEX"><div id="ManualOrder" class="CEX-manual mb-3">
        <fieldset class="rounded CEX-background-white border CEX-border-blue px-3">
        <legend
        class="p-2 ml-2 CEX-background-blue CEX-text-white rounded-2 w-auto border-0 mb-3">
        '.esc_html(__('Manual interactivo Grabaci&oacute;n del pedido', 'cex_pluggin')).'
        </legend>
        <div id="contenidoManual" class="form-group mb-3 w-auto d-flex">
        <input id="toggleOrderIntroJS" type="checkbox" class="form-control mt-1 my-auto"
        onchange="checkIntroJS();">
        <label for="toggleOrderIntroJS"
        class="m-0 my-auto mr-4 mr-sm-5 CEX-text-blue">'.esc_html(__('Activar / Desactivar', 'cex_pluggin')).'</label>
        <a id="manualInteractivoOrder"
        class="px-2 CEX-btn btn-large CEX-button-info my-auto d-none"
        href="javascript:void(0)" onclick="abrirPostBox(),introjsOrder();">
        '.esc_html(__('Manual interactivo', 'cex_pluggin')).'
        </a>
        </div>
        </fieldset>
        </div></div>';
        return $manual;
    }

    public function cex_retornar_referencia_order($postId) 
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_referencia_order');
        }
        global $wpdb;
        // buscar los estados sobre los que desencadenamos acciones.
        $table = $wpdb->prefix.'cex_customer_options';
        $results = $wpdb->get_var($wpdb->prepare("SELECT valor FROM $table where clave='MXPS_REFETIQUETAS'", null));
        $retorno='';
        $id= $postId;        
        if (isset($results) && $results!='') {
            intval($results);            
            if (isset($results) || $results>=0) {                ;
                switch ($results) {
                    case 0: //Woocommerce default
                    $retorno=$postId;                       
                    break;
                    case 1: //WooCommerce Sequential Order Numbers                        
                    if (class_exists('WC_Seq_Order_Number')) {
                        $seqOrder= new WC_Seq_Order_Number();
                        $order = wc_get_order($postId);                                                        
                        $seqOrderNumber=$order->get_meta('_order_number', true, 'edit');
                        $retorno=$seqOrderNumber;
                    } else {
                        if ($this->cex_actualizar_customer_options_interno('MXPS_REFETIQUETAS', 0)) {
                            $retorno=$postId;
                        }
                    }                        
                    break;
                    case 2: //WooCommerce Sequential Order Numbers PRO                        
                    if (class_exists('WC_Seq_Order_Number_Pro')) {
                        $seqOrder= new WC_Seq_Order_Number_Pro();
                        $order = wc_get_order($postId);                           
                        $seqOrderNumber=$order->get_meta('_order_number', true, 'edit');
                        $retorno=$seqOrderNumber;
                    } else {
                        if ($this->cex_actualizar_customer_options_interno('MXPS_REFETIQUETAS', 0)) {
                            $retorno=$postId;
                        }
                    }                        
                    break;
                    default: //Woocommerce default                       
                    $retorno=$postId;
                    break;
                }
            }
        } else {
            cex_migracion28($wpdb);
            //$wpdb->insert($table, array( 'clave' => "MXPS_REFETIQUETAS", 'valor' => '0'));
            $this->cex_retornar_referencia_order($postId);
        }        
        return $retorno;
    }

    // Custom metabox content
    public function cex_formulario_order()
    {
        wp_nonce_field('cex-nonce', 'cex-nonce');
        wp_nonce_field('cex-nonce-user', 'cex-nonce-user');
        $template=plugin_dir_path(__FILE__).'/views/templates/template_order.php';
        load_template($template);
    }

    public function cex_historico_order()
    {
        wp_nonce_field('cex-nonce', 'cex-nonce');
        $template=plugin_dir_path(__FILE__).'/views/templates/template_history.php';
        load_template($template) ;
    }

    public function cex_get_customer_options()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_get_customer_options');
        }

        $retorno;
        global $wpdb;
        // buscar los estados sobre los que desencadenamos acciones.
        $table = $wpdb->prefix.'cex_customer_options';
        $results = $wpdb->get_results($wpdb->prepare("SELECT clave, valor FROM $table", null));
        foreach ($results as $result) {
            $variable = $result;
            $valor = $variable->valor;
            $retorno[$variable->clave] = $variable->valor;
        }
        return $retorno;
    }

    public function cex_get_customer_option($campo)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_get_customer_option');
        }

        $retorno;
        global $wpdb;
        // buscar los estados sobre los que desencadenamos acciones.
        $table = $wpdb->prefix.'cex_customer_options';
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table where clave='$campo'", null));
        foreach ($results as $result) {
            $variable = $result;
            $valor = $variable->valor;
            $retorno[$variable->clave] = $variable->valor;
        }
        return $retorno;
    }

    public function cex_get_saved_sender($idSender)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_get_saved_sender');
        }

        global $wpdb;
        $table1 = $wpdb->prefix.'cex_savedsenders';
        $table2 = $wpdb->prefix.'cex_customer_codes';
        $results = $wpdb->get_row($wpdb->prepare("SELECT *
            FROM $table1 sa
            LEFT JOIN $table2 cu
            ON sa.id_cod_cliente = cu.id
            WHERE sa.id_sender = %d", $idSender));
        return $results;
    }

    //Funcion para sobrescribir las plantillas de woocommerce
    public function cex_woo_adon_plugin_template($template, $template_name, $template_path)
    {
        global $woocommerce;
        $_template = $template;
        if (!$template_path) {
            $template_path = $woocommerce->template_url;
        }

        $plugin_path  = untrailingslashit(plugin_dir_path(__FILE__))  . '/woocommerce/';

        // Look within passed path within the theme - this is priority
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );

        if (! $template && file_exists($plugin_path . $template_name)) {
            $template = $plugin_path . $template_name;
        }

        if (!$template) {
            $template = $_template;
        }
        return $template;
    }

    public function cex_entrega_oficina($checkout)
    {        
        echo '<div id="oficina_cex" style="display:none;">';

        woocommerce_form_field('codigo_oficina', array(
            'type'          => 'text',
            'class'         => array('my-field-class form-row-wide'),
            //'label'         => __('Fill in this field'),
            //'placeholder'   => __('Enter something'),
        ), $checkout->get_value('codigo_oficina'));

        echo '</div>';
    }

    //Funcion para guardar el campo personalizado como dato de la orden

    public function cex_entrega_oficina_update_order_meta($order_id)
    {
        if (!empty($_POST['codigo_oficina'])) {
            //A?adir _ al nombre del campo para que sea privado y no lo pueda modificar el cliente
            update_post_meta($order_id, '_Oficina', sanitize_text_field($_POST['codigo_oficina']));
        }
    }

    //Mostrar campo personalizado en el backoffice
    public function cex_entrega_oficina_display_admin_order_meta($order)
    {
        echo '<p><strong>'.esc_html(__('Oficina', 'cex_pluggin')).':</strong> ' . get_post_meta($order->get_id(), '_Oficina', true) . '</p>';
    }

    /**   FUNCIONES AJAX !!!   **/

    //FORMULARIO INICIAL

    public function cex_form_comercial()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_form_comercial');
        }

        //die(var_dump($_REQUEST));

        //hacer la llamada al webservice con los datos
        exit;
    }

    public function cex_ejecutarUpdate()
    {
        install_db();

    }


    public function cex_ejecutarUpdateDB()
    {
        install_db();

        /*global $wpdb;
        $table = $wpdb->prefix.'cex_migrations';
        $resultados = $wpdb->get_results("SELECT * FROM $table");

        die(var_export($resultados));*/
    }
    
    //FORMULARIO AJUSTES
    public function cex_get_init_form()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_get_init_form');
        }
        $retorno = [
            'codigos'               => $this->cex_retornar_codigos_cliente(),
            'selectCodCliente'      => $this->cex_retornar_select_codigos_cliente(),
            'remitentes'            => $this->cex_retornar_remitentes(),
            'selectRemitentes'      => $this->cex_retornar_select_remitentes(),
            'selectDestinatarios'   => $this->cex_retornar_select_destinatario(),
            'productos'             => $this->cex_retornar_productos(),
            'selectEstados'         => $this->cex_retornar_estados_productos(),
            'selectTransportistas'  => $this->cex_retornar_mapeo_transportistas(),
            'active_gateways'       => $this->cex_retornar_select_metodos_pago_activos(),
            'botonUpdate'           => $this->cex_retornar_boton_update(),
            'unidadMedida'          => get_option('woocommerce_weight_unit'),
            'selectReferencias'     => $this->cex_retornar_select_referencia_orden(),
            'retornarIntervalo'     => $this->cex_retornar_intervalo()
        ];

        echo json_encode($retorno);
        exit;
    }

    public function cex_retornar_boton_update()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_boton_update');
        }
        global $wpdb;
        $table = $wpdb->prefix.'cex_migrations';
        $cuantos = $wpdb->get_var("SELECT count(*) FROM $table");

        /*if ($cuantos < 21) {
            $result = 'actualizar';
        }else{*/
            $result = '';
        //}

            return $result;
        }

    public function cex_retornar_codigos_cliente()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_codigos_cliente');
        }

        global $wpdb;
        $table      = $wpdb->prefix.'cex_customer_codes';
        $contenido  = '';
        $cabecera   = '';
        $results    = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table", null));
        if (sizeof($results)==0) {
            return null;
        }
        /*$cabecera ="<thead>
        <tr>
        <th align='center'></th>
        <th style='display: none;'>ID</th>
        <th>".esc_html(__('C&oacute;digo cliente', 'cex_pluggin'))."</th>
        <th>".esc_html(__('C&oacute;digo solicitante', 'cex_pluggin'))."</th>
        </tr>
        </thead>
        <tbody>";*/
        $container ='<div class="container-fluid">';
        $cabecera ='<div class="row-fluid CEX-background-blue CEX-text-white p-1 d-flex align-items-center">
        <div class="col-8 col-sm-8 my-auto">'.esc_html(__('C&oacute;digo cliente', 'cex_pluggin')).'</div>
        <div class="col-4 col-sm-4 d-none my-auto">'.esc_html(__('C&oacute;digo solicitante', 'cex_pluggin')).'</div>
        </div>';
        $contenido = '';
        $i=1;
        $clase='';
        foreach ($results as $result) {
            if ($i%2!=0) {
                $clase='';
            } else {
                $clase='';
            }
            
            $contenido.='<div id="cc'.$result->id.'" class="row '.$clase.' p-3 d-flex align-items-center">
            <div class="col-6 col-sm-8 my-auto">'.$result->customer_code.'</div>        
            <div class="col-6 col-sm-4 d-none my-auto">'.$result->code_demand.'</div>
            <div class="col-6 col-sm-4 my-auto">
            <a id="'.$result->id.'" tabindex="" class="CEX-btn CEX-button-blue cex_actualizar_codigo_cliente d-inline-block" onclick="pedirCodigoCliente(this.id);">
            <i class="fas fa-pencil-alt"></i>
            </a>
            <a id="'.$result->id.'" tabindex="" class="CEX-btn CEX-button-cancel cex_borrar_codigo_cliente d-inline-block" onclick="borrarCodigoCliente(this.id)">
            <i class="fas fa-times"></i>
            </a>
            </div></div>';
            $i++;
        }
        //$footer = "</tbody></table>";
        //$tabla = $cabecera.$contenido.$footer;
        $container .="</div>";
        $codigos = $cabecera.$contenido.$container;
        return $codigos;
    }

    public function cex_retornar_select_codigos_cliente()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_select_codigos_cliente');
        }

        global $wpdb;
        $table      = $wpdb->prefix.'cex_customer_codes';
        $select     = '';
        $contenido  = '';
        $cabecera   = '';
        $results    = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table", null));
        if (sizeof($results)==0) {
            $select =" <select id='codigo_cliente' name='codigo_cliente' class='form-control' disabled> 
            <option value=' '>".esc_html(__('No hay c&oacute;digos de cliente disponibles', 'cex_pluggin'))."</option>
            </select>";
        } else {
            $cabecera = "<select id='codigo_cliente' name='codigo_cliente' class='form-control rounded-right'>";

            $contenido = '';
            foreach ($results as $result) {
                $contenido .= " <option value='$result->id'>$result->customer_code</option>";
            }
            $footer = "</select>";
            $select = $cabecera.$contenido.$footer;
        }
        return $select;
    }

    public function cex_retornar_codigo_cliente()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_codigo_cliente');
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_codes';
        $codigo = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d ", intval($_POST['id'])));
        echo json_encode($codigo);
        exit;
    }

    public function cex_guardar_codigo_cliente()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_guardar_codigo_cliente');
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_codes';
        $retorno;

        $data = array(
            'customer_code' => sanitize_text_field($_POST['customer_code']),
            'code_demand'   => sanitize_text_field($_POST['code_demand'])
        );

        if ($wpdb->insert($table, $data)) {
            $mensaje= array(
                'type'      => 'success',
                'title'     => esc_html(__('Crear c&oacute;digo de cliente', 'cex_pluggin')),
                'mensaje'   => sprintf(esc_html(__('El c&oacute;digo %s se ha agregado correctamente', 'cex_pluggin')), sanitize_text_field($_POST['customer_code'])),
            );
        } else {
            $mensaje= array(
                'type'      => 'error',
                'title'     => esc_html(__('Crear c&oacute;digo de cliente', 'cex_pluggin')),
                'mensaje'   => sprintf(esc_html(__('El c&oacute;digo %s no se ha podido crear', 'cex_pluggin')), sanitize_text_field($_POST['customer_code'])),
            );
        }

        $retorno = array(
            'mensaje'               => $mensaje,
            'remitentes'            => $this->cex_retornar_remitentes(),
            'codigos'               => $this->cex_retornar_codigos_cliente(),
            'selectCodCliente'      => $this->cex_retornar_select_codigos_cliente(),
            'selectRemitentes'      => $this->cex_retornar_select_remitentes(),
        );
        echo json_encode($retorno);
        exit;
    }

    public function cex_actualizar_codigo_cliente()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_actualizar_codigo_cliente');
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_codes';

        $code_demand = 'w'.sanitize_text_field($_POST["customer_code"]);
        $comprobante = $wpdb->update(
            $table,
            array(
                'customer_code'         => sanitize_text_field($_POST["customer_code"]),
                'code_demand'           => $code_demand
            ),
            array('id' => intval($_POST['id'])),
            array('%s',  //'customer_code'
                '%s'),    //'code_demand'
            array( '%d' )
        );

        if ($comprobante) {
            $mensaje= array(
                'type'      => 'success',
                'title'     => esc_html(__('Actualizar c&oacute;digo de cliente', 'cex_pluggin')),
                //'mensaje'     => 'El c&oacute;digo de cliente '.$_POST["name"].'se ha actualizado correctamente',
                'mensaje'   => sprintf(esc_html(__('El c&oacute;digo de cliente %s se ha actualizado correctamente', 'cex_pluggin')), sanitize_text_field($_POST['name'])),

            );
        } else {
            $mensaje= array(
                'type'      => 'error',
                'title'     => esc_html(__('Actualizar c&oacute;digo de cliente', 'cex_pluggin')),
            //'mensaje'     => 'El c&oacute;digo de cliente '.$_POST['name'].' no se ha podido actualizar',
                'mensaje'   => sprintf(esc_html(__('El c&oacute;digo de cliente %s no ha sido actualizado', 'cex_pluggin')), sanitize_text_field($_POST['name'])),

            );
        }
        $retorno = array(
            'mensaje'               => $mensaje,
            'remitentes'            => $this->cex_retornar_remitentes(),
            'codigos'               => $this->cex_retornar_codigos_cliente(),
            'selectCodCliente'      => $this->cex_retornar_select_codigos_cliente(),
            'selectRemitentes'      => $this->cex_retornar_select_remitentes(),
        );
        echo json_encode($retorno);
        exit;
    }

    public function cex_borrar_codigo_cliente()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_borrar_codigo_cliente');
        }

        global $wpdb;
        $mensaje = '';
        $id =   intval($_POST['id']);

        $table = $wpdb->prefix.'cex_customer_codes';
        $comprobante = $wpdb->delete($table, array( 'id' => $id ), array( '%d' ));

        //si hay remitentes con ese codigo, borrar.
        $table = $wpdb->prefix.'cex_savedsenders';
        $comprobante2 = $wpdb->delete($table, array( 'id_cod_cliente' => $id ), array( '%d' ));

        if ($comprobante) {
            $mensaje= array(
                'type'      => 'success',
                'title'     => esc_html(__('Borrar c&oacute;digo de cliente', 'cex_pluggin')),
                'mensaje'   => esc_html(__('El c&oacute;digo de cliente y los remitentes asociados han sido borrado correctamente', 'cex_pluggin')),
            );
        } else {
            $mensaje= array(
                'type'      => 'error',
                'title'     => esc_html(__('Borrar c&oacute;digo de cliente', 'cex_pluggin')),
                'mensaje'   => esc_html(__('El c&oacute;digo no podido ser borrado', 'cex_pluggin')),
            );
        }
        $retorno = array(
            'mensaje'               => $mensaje,
            'remitentes'            => $this->cex_retornar_remitentes(),
            'codigos'               => $this->cex_retornar_codigos_cliente(),
            'selectCodCliente'      => $this->cex_retornar_select_codigos_cliente(),
            'selectRemitentes'      => $this->cex_retornar_select_remitentes(),
        );

        echo json_encode($retorno);
        exit;
    }

    public function cex_retornar_select_destinatario()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_select_destinatario');
        }

        $configuracion = $this->cex_get_customer_options();
        $retorno = '';
        if ($configuracion['MXPS_DEFAULTDELIVER'] == 'FACTURACION') {
            $retorno = "  <option value='FACTURACION' selected >Facturacion</option>";
            $retorno .= "  <option value='ENVIO'>Envio</option>";
        } else {
            $retorno = "  <option value='FACTURACION'  >Facturacion</option>";
            $retorno .= "  <option value='ENVIO' selected>Envio</option>";
        }

        return $retorno;
    }

    public function cex_retornar_select_remitentes()
    {
        error_reporting(0);
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_select_remitentes');
        }

        global $wpdb;
        $table      = $wpdb->prefix.'cex_savedsenders';
        $select     = '';
        $contenido  = '';
        $cabecera   = '';
        $results    = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table", null));
        if (sizeof($results)==0) {
            $select =" <select id='MXPS_DEFAULTSEND' name='MXPS_DEFAULTSEND' class='form-control' disabled> 
            <option disabled='disabled' selected>".esc_html(__('No hay remitentes dados de alta', 'cex_pluggin'))."</option>
            </select>";
        } else {
            $remitenteDefecto=$this->cex_recuperarRemitenteDefecto();
            $cabecera = "<select id='MXPS_DEFAULTSEND' name='MXPS_DEFAULTSEND' class='form-control'>";
            $contenido = '';
            $existeRemitenteDefecto=0;
            foreach ($results as $result) {
                if (!empty($result->id_sender) && $result->id_sender==$remitenteDefecto['id_sender']) {
                    $contenido .= " <option value='$result->id_sender' selected>$result->name</option>";
                    $existeRemitenteDefecto=1;

                } else {
                    $contenido .= " <option value='$result->id_sender'>$result->name</option>";
                }
            }
            if ($existeRemitenteDefecto==0) {
                $contenido = " <option value='0' disabled='disabled' selected>".esc_html(__('Seleccionar remitente por defecto ', 'cex_pluggin'))."</option>".$contenido;
            }
            $footer = "</select>";
            $select = $cabecera.$contenido.$footer;
        }
        return $select;
    }

    public function cex_retornar_remitentes()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_remitentes');
        }

        global $wpdb;
        $table1 = $wpdb->prefix.'cex_customer_codes';
        $table2 = $wpdb->prefix.'cex_savedsenders';

        $registros = $wpdb->get_results($wpdb->prepare("SELECT se.id_sender, se.name, se.address, se.postcode, se.city,se.iso_code_pais, se.contact, se.phone, se.email, se.from_hour,se.from_minute, se.to_hour, se.to_minute, co.customer_code FROM $table1 co right join $table2 se on co.id = se.id_cod_cliente", null));
        $retorno = "<thead>
        <tr class='CEX-background-yellow'>
        <th align='center'></th>
        <th align='center'></th>
        <th>".esc_html(__('Nombre', 'cex_pluggin'))."</th>
        <th>".esc_html(__('Cod. Cli.', 'cex_pluggin'))."</th>
        <th>".esc_html(__('Direcci&oacute;n', 'cex_pluggin'))."</th>
        <th>".esc_html(__('CP', 'cex_pluggin'))."</th>
        <th>".esc_html(__('Ciudad', 'cex_pluggin'))."</th>
        <th>".esc_html(__('Pa&iacute;s', 'cex_pluggin'))."</th>
        <th>".esc_html(__('Contacto', 'cex_pluggin'))."</th>
        <th>".esc_html(__('Tel&eacute;fono', 'cex_pluggin'))."</th>
        <th>".esc_html(__('Email', 'cex_pluggin'))."</th>
        <th>".esc_html(__('Desde', 'cex_pluggin'))."</th>
        <th>".esc_html(__('Hasta', 'cex_pluggin'))."</th>
        </tr>
        </thead>
        <tbody>";
        foreach ($registros as $registro) {
            $desde = sprintf("%02d", $registro->from_hour).':'.sprintf("%02d", $registro->from_minute);
            $hasta = sprintf("%02d", $registro->to_hour).':'.sprintf("%02d", $registro->to_minute);

            $linea = "<tr class='fila'>
            <td> 
            <a id='".$registro->id_sender."' tabindex='' class='CEX-btn CEX-button-blue cex_actualizar_remitente d-inline-block' onclick='pedirRemitente(this.id);'>
            <i class='fas fa-pencil-alt'></i>
            </a>       
            </td>        
            <td>
            <a id='".$registro->id_sender."' tabindex='' class='CEX-btn CEX-button-cancel cex_borrar_remitente d-inline-block' onclick='borrarRemitente(this.id);'>
            <i class='fas fa-times'></i>
            </a>
            </td>        
            <td>".esc_html(__($registro->name))."</td>
            <td>".esc_html(__($registro->customer_code))."</td>
            <td>".esc_html(__($registro->address))."</td>
            <td>".esc_html(__($registro->postcode))."</td>
            <td>".esc_html(__($registro->city))."</td>
            <td>".esc_html(__($registro->iso_code_pais))."</td>
            <td>".esc_html(__($registro->contact))."</td>
            <td>".esc_html(__($registro->phone))."</td>
            <td>".esc_html(__($registro->email))."</td>
            <td>".esc_html(__($desde))."</td>
            <td>".esc_html(__($hasta))."</td>
            </tr>";

            $retorno .= $linea;
        }

        $retorno.= "</tbody>";
        return $retorno;
    }

    public function cex_retornar_remitente()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_remitente');
        }

        global $wpdb;
        $table1 = $wpdb->prefix.'cex_customer_codes';
        $table2 = $wpdb->prefix.'cex_savedsenders';
        $remitente = $wpdb->get_row($wpdb->prepare("SELECT se.id_sender, se.name, se.address, se.postcode, se.city,se.iso_code_pais, se.contact, se.phone, se.email,
            se.from_hour,se.from_minute, se.to_hour,
            se.to_minute, co.customer_code, se.id_cod_cliente
            FROM $table1 co right join 
            $table2 se on co.id = se.id_cod_cliente
            WHERE se.id_sender = %d", intval($_POST['id'])));

        $remitente = json_encode($remitente);
        $remitente = str_replace("\'", "'", $remitente);
        echo $remitente;
        exit;
    }

    public function cex_guardar_remitente()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_guardar_remitente');
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_savedsenders';
        $retorno;
        //recuperar esto de bbdd
        $_POST = str_replace("\'", "'", $_POST);

        $data = array(
            'name'          => sanitize_text_field($_POST["name"]),
            'address'       => sanitize_text_field($_POST["address"]),
            'postcode'      => sanitize_text_field($_POST["postcode"]),
            'city'          => sanitize_text_field($_POST["city"]),
            'contact'       => sanitize_text_field($_POST["contact"]),
            'phone'         => sanitize_text_field($_POST["phone"]),
            'from_hour'     => intval($_POST["from_hour"]),
            'from_minute'   => intval($_POST["from_minute"]),
            'to_hour'       => intval($_POST["to_hour"]),
            'to_minute'     => intval($_POST["to_minute"]),
            'iso_code_pais' => sanitize_text_field($_POST["iso_code"]),
            'email'         => sanitize_email($_POST["email"]),
            'id_cod_cliente'=> intval($_POST['codigo_cliente']),
        );

        $sql=$wpdb->prepare("INSERT INTO $table (name, address, postcode, city, contact, phone, from_hour, from_minute, to_hour, to_minute, iso_code_pais, email, id_cod_cliente) VALUES (%s,%s,%s,%s,%s,%s,%d,%d,%d,%d,%s,%s,%d) ", $data);
        //die($wpdb->query($sql));

        if ($wpdb->query($sql, $data)) {
            $mensaje= array(
                'type'      => 'success',
                'title'     => esc_html(__('Crear remitente', 'cex_pluggin')),
                'mensaje'   => sprintf(esc_html(__('El remitente  %s se ha agregado correctamente', 'cex_pluggin')), sanitize_text_field($_POST['name'])),

            );
        } else {
            $mensaje= array(
                'type'      => 'error',
                'title'     => esc_html(__('Crear remitente', 'cex_pluggin')),
                'mensaje'   => sprintf(esc_html(__('El remitente  %s no se ha podido crear', 'cex_pluggin')), sanitize_text_field($_POST['name'])),
            );
        }

        $retorno = array(
            'mensaje'               => $mensaje,
            'remitentes'            => $this->cex_retornar_remitentes(),
            'selectRemitentes'      => $this->cex_retornar_select_remitentes(),
        );

        echo json_encode($retorno);

        exit;
    }

    public function cex_actualizar_remitente()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_actualizar_remitente');
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_savedsenders';

        //$id_cod_cliente = retornar_id_cod_cliente(POST_['codigo_cliente']);

        $_POST = str_replace("\'", "'", $_POST);
        //$id_sender = $_POST['id'];
        
        $data = array(
            'name'          => sanitize_text_field($_POST["name"]),
            'address'       => sanitize_text_field($_POST["address"]),
            'postcode'      => sanitize_text_field($_POST["postcode"]),
            'city'          => sanitize_text_field($_POST["city"]),
            'iso_code_pais' => sanitize_text_field($_POST["iso_code"]),
            'contact'       => sanitize_text_field($_POST["contact"]),
            'phone'         => sanitize_text_field($_POST["phone"]),
            'email'         => sanitize_email($_POST["email"]),
            'from_hour'     => intval($_POST["from_hour"]),
            'from_minute'   => intval($_POST["from_minute"]),
            'to_hour'       => intval($_POST["to_hour"]),
            'to_minute'     => intval($_POST["to_minute"]),
        );

        //$sql=$wpdb->prepare("UPDATE $table SET name=%s, address=%s, postcode=%s, city=%s, contact=%s, phone=%i, from_hour=%i, from_minute=%i, to_hour=%i, to_minute=%i, iso_code_pais=%s, email=%s, id_cod_cliente=%i WHERE id_sender=%i;",$data,$id_sender);
        //$comprobante=$wpdb->query($sql);
        $comprobante = $wpdb->update(
            $table,
            array(
                'name'          => sanitize_text_field($_POST["name"]),
                'address'       => sanitize_text_field($_POST["address"]),
                'postcode'      => sanitize_text_field($_POST["postcode"]),
                'city'          => sanitize_text_field($_POST["city"]),
                'iso_code_pais' => sanitize_text_field($_POST["iso_code"]),
                'contact'       => sanitize_text_field($_POST["contact"]),
                'phone'         => sanitize_text_field($_POST["phone"]),
                'email'         => sanitize_email($_POST["email"]),
                'from_hour'     => intval($_POST["from_hour"]),
                'from_minute'   => intval($_POST["from_minute"]),
                'to_hour'       => intval($_POST["to_hour"]),
                'to_minute'     => intval($_POST["to_minute"]),
            ),
            array('id_sender' => intval($_POST['id'])),
            array('%s',  //'name'
                                                        '%s', //'address'
                                                        '%s', //'postcode'
                                                        '%s', //'city'
                                                        '%s', //iso_code
                                                        '%s', //'contact'
                                                        '%d', //'phone'
                                                        '%s', //'email'
                                                        '%d', //'from_hour'
                                                        '%d', //'from_minute'
                                                        '%d', //'to_hour'
                                                        '%d', //'to_minute'
                                                    ),
            array( '%d' )
        );

        if ($comprobante) {
            $mensaje= array(
                'type'      => 'success',
                'title'     => esc_html(__('Actualizar remitente', 'cex_pluggin')),
                //'mensaje'     => 'Los datos de '.$_POST["name"].'se han actualizado correctamente',
                'mensaje'   => sprintf(esc_html(__('Los datos de %s se han actualizado correctamente', 'cex_pluggin')), sanitize_text_field($_POST['name'])),
            );
        } else {
            $mensaje= array(
                'type'      => 'error',
                'title'     => esc_html(__('Actualizar remitente', 'cex_pluggin')),
                'mensaje'   => sprintf(esc_html(__('Los datos de  %s no ha sido actualizado', 'cex_pluggin')), sanitize_text_field($_POST['name'])),
            );
        }
        $retorno = array(
            'mensaje'               => $mensaje,
            'remitentes'            => $this->cex_retornar_remitentes(),
            'selectRemitentes'      => $this->cex_retornar_select_remitentes(),
        );
        echo json_encode($retorno);
        exit;
    }

    public function cex_borrar_remitente()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);
        $id_sender=intval($_POST['id']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_borrar_remitente');
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_savedsenders';
        $mensaje = '';

        $comprobante = $wpdb->delete($table, array( 'id_sender' =>$id_sender ), array( '%d' ));

        if ($comprobante) {
            $campo='MXPS_DEFAULTSEND';
            $remitenteDefecto=$this->cex_get_customer_option($campo);
            if ($remitenteDefecto["$campo"]==$id_sender) {
                if ($this->cex_actualizar_customer_options_interno($campo)) {
                    $mensaje= array(
                        'type'      => 'success',
                        'title'     => esc_html(__('Borrar remitente', 'cex_pluggin')),
                        'mensaje'   => esc_html(__('El remitente ha sido borrado correctamente. Ahora no tiene remitentes por defecto', 'cex_pluggin')),
                    );
                } else {
                    $mensaje= array(
                        'type'      => 'success',
                        'title'     => esc_html(__('Borrar remitente', 'cex_pluggin')),
                        'mensaje'   => esc_html(__('El remitente ha sido borrado correctamente', 'cex_pluggin')),
                    );
                }
            } else {
                $mensaje= array(
                    'type'      => 'success',
                    'title'     => esc_html(__('Borrar remitente', 'cex_pluggin')),
                    'mensaje'   => esc_html(__('El remitente ha sido borrado correctamente', 'cex_pluggin')),
                );
            }
        } else {
            $mensaje= array(
                'type'      => 'error',
                'title'     => esc_html(__('Borrar remitente', 'cex_pluggin')),
                'mensaje'   => esc_html(__('El remitente no ha podido ser borrado', 'cex_pluggin')),
            );
        }
        $retorno = array(
            'mensaje'               => $mensaje,
            'remitentes'            => $this->cex_retornar_remitentes(),
            'selectRemitentes'      => $this->cex_retornar_select_remitentes(),
        );

        echo json_encode($retorno);
        exit;
    }

    public function cex_guardarRemitenteDefecto()
    {
        $id= intval($_POST["MXPS_DEFAULTSEND"]);
        $nonce = sanitize_text_field($_POST['nonce']);
        $campo = sanitize_text_field($_POST['campo']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_guardarRemitenteDefecto');
        }
        if ($id > 0 || $id != '') {
            if ($this->cex_actualizar_customer_options($campo)) {
                $mensaje= array(
                    'type'      => 'success',
                    'title'     => esc_html(__('Actualizar remitente defecto', 'cex_pluggin')),
                    'mensaje'   => esc_html(__('El remitente por defecto se ha actualizado correctamente', 'cex_pluggin'))
                );
            } else {
                $mensaje= array(
                    'type'      => 'error',
                    'title'     => esc_html(__('Actualizar remitente defecto', 'cex_pluggin')),
                    'mensaje'   => esc_html(__('El remitente por defecto no es v&aacute;lido', 'cex_pluggin'))
                );
            }
        } else {
            $mensaje= array(
                'type'      => 'error',
                'title'     => esc_html(__('Actualizar configuraci&oacute;n de usuario', 'cex_pluggin')),
                'mensaje'   => esc_html(__('No se ha podido establecer el remitente por defecto', 'cex_pluggin'))
            );
        }
        $retorno = array(
            'mensaje'       => $mensaje,
        );
        echo json_encode($retorno);
        exit;
    }

    //PAGINA DE CONFIGURACION DE USUARIO

    public function cex_get_user_config()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_get_user_config');
        }

        global $wpdb;
        $ast='*****';
        $table = $wpdb->prefix.'cex_customer_options';
        $sql=$wpdb->prepare("SELECT clave, valor FROM $table where clave NOT IN ('MXPS_PASSWD', 'MXPS_CRYPT')", null);
        $results = $wpdb->get_results($sql);

        if (sizeof($results)==0) {
            return null;
        }
        $retorno;
        $newObject=new stdClass();
        $newObject->clave='MXPS_PASSWD';
        $newObject->valor=$ast;
        array_push($results, $newObject);        
        foreach ($results as $result) {                   
            if ($result->clave!='') {
                if ($result->clave=='MXPS_USER') {
                    //echo $result->valor;
                    //$valorActual=cex_encrypt_decrypt('decrypt',$result->valor);
                    //echo '$valorActual: '.$valorActual;
                    //$nuevoValor=substr($valorActual,0,1);
                    //echo ' $nuevoValor: '.$nuevoValor;
                    $result->valor=substr(cex_encrypt_decrypt('decrypt',$result->valor),0,1).$ast;
                }
                $retorno["$result->clave"] = $result->valor;
            }            
        }
        echo json_encode($retorno);
        exit;
    }

    public function cex_actualizar_customer_options($campo)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_actualizar_customer_options');
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';
        $valorCampo=sanitize_text_field($_POST["$campo"]);      
        $sqlQuery = $wpdb->prepare("SELECT * FROM $table WHERE valor=%s AND clave=%s", array($valorCampo, $campo));
        $comprobante=false;
        if ($sqlQuery) {
            $comprobante = $wpdb->update(
                $table,
                array('valor'=>sanitize_text_field($_POST["$campo"])),
                array('clave'=>$campo),
                array('%s'),
                array('%s')
            );
        }
        return $comprobante;
    }

    public function cex_actualizar_customer_options_interno($campo, $valor = '')
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_actualizar_customer_options_interno');
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';
        $comprobante = $wpdb->update(
            $table,
            array('valor'=>$valor),
            array('clave'=>$campo),
            array('%s'),
            array('%s')
        );
        return $comprobante;
    }
    
    public function cex_guardar_credenciales()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        
        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_guardar_credenciales');
        }

        $sumatorio = 0;       

        if (sanitize_text_field($_POST['MXPS_USER']) != '' && sanitize_text_field($_POST['MXPS_PASSWD']) != '') { 

            foreach ($_POST as $key => $value) {
                if ($key != 'action') {
                    if ($key == 'MXPS_PASSWD'){                       
                        $_POST['MXPS_PASSWD']=cex_encrypt_decrypt('encrypt',$_POST['MXPS_PASSWD']);                        
                    } else if ($key == 'MXPS_USER'){                        
                        $_POST['MXPS_USER']=cex_encrypt_decrypt('encrypt',$_POST['MXPS_USER']);                        
                    }                    
                    $sumatorio+= $this->cex_actualizar_customer_options($key);
                }
            }
            if ($sumatorio>=1) {
                $mensaje= array(
                    'type'      => 'success',
                    'title'     => esc_html(__('Actualizar datos del usuario', 'cex_pluggin')),
                    'mensaje'   => esc_html(__('Los datos se han actualizado correctamente', 'cex_pluggin')),
                );
            } else {
                $mensaje= array(
                    'type'      => 'error',
                    'title'     => esc_html(__('Actualizar datos del usuario', 'cex_pluggin')),
                    'mensaje'   => esc_html(__('Los datos no han sido modificados', 'cex_pluggin')),
                );
            }            
        } else {
            $mensaje= array(
                'type'      => 'error',
                'title'     => esc_html(__('Actualizar datos del usuario', 'cex_pluggin')),
                'mensaje'   => esc_html(__('Los datos no han sido guardados', 'cex_pluggin')),
            );
        }
        $retorno = array(
            'mensaje'       => $mensaje,
        );
        echo json_encode($retorno);
        exit;
    }

    public function cex_guardar_logo($fileName){        
        global $wp;
        $path = plugin_dir_url( __FILE__ ).'views/img/'.$fileName;
        $comprobante = move_uploaded_file($_FILES["MXPS_UPLOADFILE"]["tmp_name"], dirname( __FILE__ ).'/views/img/'.$fileName);
        if($comprobante ){
            $this->cex_actualizar_customer_options_interno("MXPS_UPLOADFILE",$path);
            $this->cex_actualizar_customer_options_interno("MXPS_CHECKUPLOADFILE",'true');
        }
        $mensaje= array(
            'type'      => 'success',
            'title'     => esc_html(__('Actualizar datos del usuario', 'cex_pluggin')),
            'mensaje'   => esc_html(__('Logo para las etiquetas guardado', 'cex_pluggin'))
        );  
        $retorno = array(
            'mensaje' => $mensaje,
            'imagenLogo' => $path
        );
        return $retorno;     
    }

    public function cex_guardar_imagen_logo(){ 
        $retorno = array(
            'mensaje' => '',
            'imagenLogo' => ''
        );        
        if(strcmp($_POST['MXPS_CHECKUPLOADFILE'], 'true') ==0){
            if(!empty($_FILES['MXPS_UPLOADFILE'])){
                $this->cex_eliminar_logo(true);
                if ($_FILES['MXPS_UPLOADFILE']['size'] >= 400000) {
                    $mensaje= array(
                        'type'      => 'error',
                        'title'     => esc_html(__('Actualizar configuraci&oacute;n de usuario', 'cex_pluggin')),
                        'mensaje'   => esc_html(__('El logo es demasiado grande, debe ser menor de 400 kB', 'cex_pluggin')),
                    );   
                    $retorno['mensaje'] = $mensaje;
                } else {
                    switch ($_FILES['MXPS_UPLOADFILE']['type']) {
                        case 'image/png':
                            $retorno = $this->cex_guardar_logo("LogoImagen.png");
                            break;
                        case 'image/jpg':
                        case 'image/jpeg':
                            $retorno = $this->cex_guardar_logo("LogoImagen.jpg");
                            break;
                        case 'image/gif':
                            $retorno = $this->cex_guardar_logo("LogoImagen.gif");
                            break;   
                        default:
                            $mensaje= array(
                                'type'      => 'error',
                                'title'     => esc_html(__('Actualizar configuraci&oacute;n de usuario', 'cex_pluggin')),
                                'mensaje'   => esc_html(__('Formato Inv&aacute;lido (JPG/PNG)', 'cex_pluggin')),
                            );   
                            $retorno['mensaje'] = $mensaje;    
                            break;
                    }
                }
            }else{
                $mensaje= array(
                    'type'      => 'error',
                    'title'     => esc_html(__('Actualizar configuraci&oacute;n de usuario', 'cex_pluggin')),
                    'mensaje'   => esc_html(__('Es obligatorio subir un archivo', 'cex_pluggin')),
                );   
                $retorno['mensaje'] = $mensaje;
            }
        }else{
            $retorno = $this->cex_eliminar_logo(true);
        }
        echo json_encode($retorno);
        exit; 
    }

    public function cex_eliminar_logo($ajax = false){
        $this->cex_actualizar_customer_options_interno("MXPS_UPLOADFILE"," ");
        $this->cex_actualizar_customer_options_interno("MXPS_CHECKUPLOADFILE","false");
        $archivos = array("LogoImagen.gif","LogoImagen.png","LogoImagen.jpeg","LogoImagen.jpg");        
        foreach ($archivos as $archivo) {
            $url = plugin_dir_path(__FILE__).'views/img/'.$archivo;
            if(file_exists($url)){
                unlink($url);
            }
        }
        $mensaje= array(
            'type'      => 'error',
            'title'     => esc_html(__('Actualizar configuraci&oacute;n de usuario', 'cex_pluggin')),
            'mensaje'   => esc_html(__('Imagen eliminada', 'cex_pluggin')),
        );

        $retorno = array(
            'mensaje' => $mensaje,
            'imagenLogo' => ''
        );

        if($ajax === true){
            return $retorno;
        }

        echo json_encode($retorno);
        exit;     
    }

    public function cex_guardar_customer_options()
    {
        global $wp;
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_guardar_customer_options');
        }
        $sumatorio = 0;                       


        if (intval($_REQUEST['MXPS_DEFAULTBUL']) > 0 && intval($_REQUEST['MXPS_DEFAULTBUL']) != '') {
            foreach ($_POST as $key => $value) {
                if ($key != 'action' || $key!='MXPS_UPLOADFILE') {
                    $sumatorio+= $this->cex_actualizar_customer_options($key);
                }
            }
        
            if ($sumatorio>=1) {
                $mensaje= array(
                    'type'      => 'success',
                    'title'     => esc_html(__('Actualizar configuraci&oacute;n de usuario', 'cex_pluggin')),
                    'mensaje'   => esc_html(__('Los datos se han actualizado correctamente', 'cex_pluggin')),
                );
            } else {
                $mensaje= array(
                    'type'      => 'error',
                    'title'     => esc_html(__('Actualizar configuraci&oacute;n de usuario', 'cex_pluggin')),
                    'mensaje'   => esc_html(__('Los datos no han sido modificados', 'cex_pluggin')),
                );
            }
        } else {
        $mensaje= array(
            'type'      => 'error',
            'title'     => esc_html(__('Actualizar configuraci&oacute;n de usuario', 'cex_pluggin')),
            'mensaje'   => esc_html(__('N&uacute;mero de bultos no permitido', 'cex_pluggin')),
        );
    }
    $retorno = array(
        'mensaje'       => $mensaje,
    );
   
    echo json_encode($retorno);
    exit;
}

//FORMULARIO DE CONFIGURACION PRODUCTOS CEX
public function cex_retornar_productos()
{
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_retornar_productos');
    }

    global $wpdb;
    $table = $wpdb->prefix.'cex_savedmodeships';
    $productos = $wpdb->get_results($wpdb->prepare("SELECT name, id_bc, checked FROM $table", null));
    $retorno = '';

    $container = '<div class="row">';
    $row1 = '<div class="col-12 col-sm-6 col-lg-6">';
    $row2 = '<div class="col-12 col-sm-6 col-lg-6">';
    $endRow = '</div>';

    $iterador=0;
    foreach ($productos as $producto) {
        $elemento = '<div class="form-group my-2 d-flex">';
        if ($producto->checked) {
            $elemento .= '<input type="checkbox" id="prod'.$producto->id_bc.'" name="MXPS_SELMODESHIP" class="form-control m-1 d-inline-block check_productos" value="'.$producto->id_bc.'" checked><label for="prod'.$producto->id_bc.'" class="d-inline-block ml-2">'.$producto->name.'</label>';
        } else {
            $elemento .= '<input type="checkbox" id="prod'.$producto->id_bc.'" name="MXPS_SELMODESHIP" class="form-control m-1 d-inline-block check_productos" value="'.$producto->id_bc.'"><label for="prod'.$producto->id_bc.'" class="d-inline-block ml-2">'.$producto->name.'</label>';
        }
        $elemento.='</div>';
        if ($iterador <= (sizeof($productos)/2)) {
            $row1.=$elemento;
        } else {
            $row2.=$elemento;
        }

        $iterador++;
    }

    $retorno = $container.$row1.$endRow.$row2.$endRow.$endRow;
    return $retorno;
}

public function cex_guardar_productos()
{
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_guardar_productos');
    }

    global $wpdb;
    $table = $wpdb->prefix.'cex_savedmodeships';
    $productos = sanitize_text_field($_POST['productos']);

    $productos = explode(';', $productos);
    $where_checked='';
    $where_no_checked='';

        //preparamos el where para el update
        //un where para los k si
        //un where para los k no
    foreach ($productos as $producto) {
        $producto=sanitize_text_field($producto);
        $where_checked .=  "id_bc = $producto or ";
        $where_no_checked .= "id_bc!= $producto and ";
    }

    if ($where_checked!= '') {
        $where_checked = substr($where_checked, 0, -3);
    }

    if ($where_no_checked != '') {
        $where_no_checked = substr($where_no_checked, 0, -4);
    }

    $update_check       = $wpdb->prepare("UPDATE $table SET checked=1 WHERE $where_checked", null);
    $update_no_checked  = $wpdb->prepare("UPDATE $table SET checked=0 WHERE $where_no_checked", null);

    $comprobante1 = $wpdb->query($update_check);
    $comprobante2 = $wpdb->query($update_no_checked);
        //Return #Return (int|false) Number of rows affected/selected or false on error

    $mensaje;
    $comprobante = $comprobante1 + $comprobante2;
        //echo $comprobante;

    if ($comprobante >= 1) {
        $mensaje= array(
            'type'      => 'success',
            'title'     => esc_html(__('Guardar productos', 'cex_pluggin')),
            'mensaje'   => esc_html(__('Los productos se han actualizado correctamente', 'cex_pluggin')),
        );
    } else {
        $mensaje= array(
            'type'      => 'error',
            'title'     => esc_html(__('Guardar productos', 'cex_pluggin')),
            'mensaje'   => esc_html(__('Los productos no se han actualizado', 'cex_pluggin')),
        );
    }
    $retorno = array(
        'mensaje'               => $mensaje,
        'selectTransportistas'  => $this->cex_retornar_mapeo_transportistas(),
    );
    echo json_encode($retorno);
    exit;
}

// OPCIONES DEL CRON
public function cex_retornar_estados_productos()
{
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_retornar_estados_productos');
    }

        //die(var_dump(wc_get_order_statuses()));
    $estados = wc_get_order_statuses();

    $retorno = '<option value="" disabled  checked>'.esc_html(__('Selecciona un estado', 'cex_pluggin')).'</option>';

    foreach ($estados as $clave => $valor) {
        $retorno .= "<option value='$clave'>$valor</option>";
    }
    return $retorno;
}

public function cex_guardar_options_cron()
{
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_guardar_options_cron');
    }
    $savedStatus=false;
    $trackingCex=false;
    $changeStatus=false;
    $sumatorio = 0;
    foreach ($_POST as $key => $value) {            
        switch ($key) {
            case 'MXPS_SAVEDSTATUS':
            if ($value==true) {
                $savedStatus=true;
            }
            break;
            case 'MXPS_RECORDSTATUS':
            if ($savedStatus==false) {
                $value='';
            }
            break;
            case 'MXPS_TRACKINGCEX':
            if ($value==true) {
                $trackingCex=true;
            }
            break;
            case 'MXPS_CHANGESTATUS':
            if ($trackingCex==false) {
                $value=false;
            } elseif ($value==true) {
                $changeStatus=true;
            }
            break;
            case ('MXPS_SENDINGSTATUS' || 'MXPS_DELIVEREDSTATUS' || 'MXPS_CANCELEDSTATUS' || 'MXPS_RETURNEDSTATUS'):

            if ($trackingCex==false && $changeStatus==false) {
                $value='';
            }                        
            break;
        }            
        if ($key != 'action') {
            $sumatorio+= $this->cex_actualizar_customer_options($key);
        }
    }

    if ($sumatorio>=1) {
        $mensaje= array(
            'type'      => 'success',
            'title'     => esc_html(__('Actualizar configuraci&oacute;n de usuario', 'cex_pluggin')),
            'mensaje'   => esc_html(__('Los datos se han actualizado correctamente', 'cex_pluggin')),
        );
    } else {
        $mensaje= array(
            'type'      => 'error',
            'title'     => esc_html(__('Actualizar configuraci&oacute;n de usuario', 'cex_pluggin')),
            'mensaje'   => esc_html(__('Los datos no han sido modificados', 'cex_pluggin')),
        );
    }
    $retorno = array(
        'mensaje'       => $mensaje,
    );
    echo json_encode($retorno);
    exit;
}

//CREAMOS EL INTERVALO
public function cex_retornar_intervalo(){
    global $wpdb;
    $table1 = $wpdb->prefix.'cex_customer_options';       
    $results = $wpdb->get_row($wpdb->prepare("SELECT valor
        FROM $table1
        WHERE clave='MXPS_CRONINTERVAL'",null));        
    return $results;
}

public function cex_get_interval(){    
    $results = $this->cex_retornar_intervalo();    
    switch ($results->valor) {
        case '2':
        $results=7200;
        break;
        case '3':
        $results=10800;
        break;
        case '4':
        $results=14400;
        break;
        case '5':
        $results=18000;
        break;
        case '6':
        $results=21600;
        break;
        case '7':
        $results=25200;
        break;
        case '8':
        $results=28800;
        break;
        
        default:
        $results=14400;
        break;
    }        
    return $results;
        
}

public function cex_interval_function($schedules)
{
    //el intervalo es en segundos
    $schedules['cex_interval'] = array(
        'interval' => $this->cex_get_interval(),
        'display'  => esc_html(__('tarea cex'))
    );        
    return $schedules;
}

//A?ADIMOS FUNCION CALLBACK A LA TAREA

public function cex_cron_function()
{
    cex_cron_ejecutar();
}


public function cex_generar_informe_cron()
{
    global $wpdb;
    $table1         = $wpdb->prefix.'cex_envio_cron';
    $nombreArchivo  = $_POST['nombre'];
    $retorno        ='';
    $listaIds       = $wpdb->get_results(
                                $wpdb->prepare("SELECT id_envio_cron AS id
                                                    FROM $table1 e
                                                    WHERE e.deleted_at is null")
                            );   
    switch($nombreArchivo){
        case 'log_cron':
            $retorno = $this->generarLogCron($listaIds);
        break;
        case 'peticion':
            $retorno = $this->generarLogPeticionCron($listaIds);
        break;
        case 'respuesta':
            $retorno = $this->generarLogRespuestaCron($listaIds);
        break;
        default:
        break;

    }   
    echo $retorno;
    exit();
}

public function generarLogCron($listaIds){
    global $wpdb;
    $respuesta  = "";
    $table1     = $wpdb->prefix.'cex_envio_cron';
    $table2     = $wpdb->prefix.'cex_respuesta_cron';
    $table3     = $wpdb->prefix.'cex_savedships';

    foreach($listaIds as $id){
        $id     = $id->id;
        $data   = $wpdb->get_results(
                    $wpdb->prepare("SELECT *, e.created_at as fcreacion
                        FROM $table1 e
                        RIGHT JOIN  $table2 res
                            ON e.id_envio_cron = res.id_envio_cron
                        RIGHT JOIN  $table3 sav
                            ON res.nEnvioCliente = sav.numship
                        WHERE e.deleted_at is null
                        AND e.id_envio_cron = $id
                        AND sav.type='Envio'"
                    )
                );
        foreach($data as $item){
            $respuesta .= "\n\n ORDEN: ". $item->id_ship;
            $respuesta .= "\n CÓDIGO DE CLIENTE: ". $item->codCliente."\n";
            $respuesta .= "\t- Fecha de ejecución del cron -> ".$item->fcreacion."\n";
            $respuesta .= "\t- Referencia -> ".$item->numcollect."\n";
            $respuesta .= "\t- Código de estado antes del Cron -> ".$item->estadoAntiguo." con estado -> ".$item->status."\n";
            $respuesta .= "\t- Estado Actual en WS -> ".$item->codigoEstado." .con el estado -> ".$item->descripcionEstado."\n";
        }        
    }
    $respuesta .= "\n\n\t\t\tFIN DE EJECUCIÓN DEL CRON";
    return $respuesta;
}

public function generarLogPeticionCron($listaIds){
    global $wpdb;
    $respuesta  = "";
    $table1     = $wpdb->prefix.'cex_envio_cron';
    $table2     = $wpdb->prefix.'cex_respuesta_cron';
    $table3     = $wpdb->prefix.'cex_savedships';
    $respuesta .= "######################################################################################################\n";

    foreach($listaIds as $id){
        $id     = $id->id;
        $data   = $wpdb->get_results(
                    $wpdb->prepare("SELECT *, e.created_at as fcreacion
                        FROM $table1 e
                        RIGHT JOIN  $table2 res
                            ON e.id_envio_cron = res.id_envio_cron
                        RIGHT JOIN  $table3 sav
                            ON res.nEnvioCliente = sav.numship
                        WHERE e.deleted_at is null
                        AND e.id_envio_cron = $id
                        AND sav.type='Envio'"
                    )
                );
        $respuesta .="\n ********************CÓDIGO DE CLIENTE: ". $data[0]->codCliente."\n";
        $respuesta .="\n ********************PETICIÓN DE ENVÍO: \n\t". $data[0]->peticion_envio."\n";
        foreach($data as $item){
            $respuesta .="\n\n\t ORDEN: ". $item->id_ship;
            $respuesta .= "\t- Fecha de ejecución del cron -> ".$item->fcreacion."\n";
            $respuesta .= "\t- Referencia -> ".$item->numcollect."\n";
            $respuesta .= "\t- Número de envío -> ".$item->nEnvioCliente."\n";
        }       
        $respuesta .= "\n######################################################################################################\n";
     
    }
    return $respuesta;

}

public function generarLogRespuestaCron($listaIds){
    global $wpdb;
    $respuesta  = "";
    $table1     = $wpdb->prefix.'cex_envio_cron';
    $table2     = $wpdb->prefix.'cex_respuesta_cron';
    $table3     = $wpdb->prefix.'cex_savedships';

    foreach($listaIds as $id){
        $id     = $id->id;
        $data   = $wpdb->get_results(
                    $wpdb->prepare("SELECT *
                        FROM $table1 e                       
                        WHERE e.deleted_at is null
                        AND e.id_envio_cron = $id"
                    )
                );
        $respuesta .="\n ********************CÓDIGO DE CLIENTE: ". $data[0]->codCliente."\n";
        $respuesta .="\n ********************RESPUESTA DEL WS: \n\t". print_r(json_decode($data[0]->respuesta_envio),true)."\n";        
        $respuesta .= "\n######################################################################################################\n";
     
    }
    return $respuesta;

}

public function cex_borrar_carpeta_log(){    
        $dirPath = dirname(__FILE__).'/log/';        
        $lleno = glob($dirPath.'/*');        
        $comprobante =false;
        $mensaje = array();
        if(count($lleno) == 0){
            $comprobante = rmdir($dirPath);
        }else{
            foreach($lleno as $archivo){
                $comprobante = unlink($archivo);
            }
            $comprobante = rmdir($dirPath);
        }

        if($comprobante){
            $mensaje= array(
                    'type'      => 'success',
                    'title'     => 'Delete Folder',
                    'mensaje'   => 'Folder deleted properly'
                );
        }else{
           $mensaje= array(
                    'type'      => 'error',
                    'title'     => 'Delete Folder',
                    'mensaje'   => 'Something wrong has happened. Check /modules/correosexpress folder'
                ); 
        }
        $retorno = array(
            'mensaje'       => $mensaje,
        );
        echo json_encode($retorno);
        exit;
    }

    //TRANSPORTISTAS
public function cex_retornar_select_productosActivosZonas($id)
{
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_retornar_select_productosActivosZonas');
    }

    global $wpdb;
    $table      = $wpdb->prefix.'cex_savedmodeships';
    $select     = '';
    $contenido  = '';
    $cabecera   = '';
    $results    = $wpdb->get_results($wpdb->prepare("SELECT *
        FROM $table
        WHERE checked = '1'", null));

    $name = $id;
    $id = ';'.$id.';';

    if (sizeof($results)==0) {
        $select =" <select id='nombreProductos' name='$name' class='form-control rounded-left-0 rounded-right' disabled> 
        <option value=' '>".esc_html(__('No hay productos CEX activos', 'cex_pluggin'))."</option></select>";
    } else {
        $cabecera = "<select id='nombreProductos' name='$name' class='form-control rounded-left-0 rounded-right'>";
        $contenido = '';
        $contenido .= " <option value='0'>".esc_html(__('No corresponde a productos CEX', 'cex_pluggin'))."</option>";
        $productosCEX = $results;
        foreach ($productosCEX as $result) {
            if (is_numeric(strpos($result->id_carrier, $id))) {
                $contenido .= " <option value='$result->id_bc' selected >".esc_html(__($result->name))."</option>";
            } else {
                $contenido .= " <option value='$result->id_bc' >".esc_html(__($result->name))."</option>";
            }
        }

        $footer = "</select>";
        $select = $cabecera.$contenido.$footer;
    }
    return $select;
}


public function cex_retornar_select_productosEnvio_orden($id_orden){

    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_retornar_select_productosEnvio_orden');
    }

    $metodoEnvioOrden =  $this->cex_get_shipping_data_by_order($id_orden);

    $id = $metodoEnvioOrden["instance_id"];

    global $wpdb;
    $table      = $wpdb->prefix.'cex_savedmodeships';
    $select     = '';
    $contenido  = '';
    $cabecera   = '';
    $results    = $wpdb->get_results($wpdb->prepare("SELECT *
      FROM $table
      WHERE checked = '1'", null));

    $name = $id;
    $id = ';'.$id.';';

    if (sizeof($results)==0) {
        $select =" <select id='nombreProductos' name='$name' class='form-control rounded-left-0 rounded-right' disabled> 
        <option value=' '>".esc_html(__('No hay productos CEX activos', 'cex_pluggin'))."</option></select>";
    } else {
        $cabecera = "<select id='nombreProductos' name='$name' class='form-control rounded-left-0 rounded-right'>";
        $contenido = '';
        $contenido .= " <option value='0'>".esc_html(__('No corresponde a productos CEX', 'cex_pluggin'))."</option>";
        $productosCEX = $results;
        foreach ($productosCEX as $result) {
            if (is_numeric(strpos($result->id_carrier, $id))) {
                $contenido .= " <option value='$result->id_bc' selected >".esc_html(__($result->name))."</option>";
            } else {
                $contenido .= " <option value='$result->id_bc' >".esc_html(__($result->name))."</option>";
            }
        }

        $footer = "</select>";
        $select = $cabecera.$contenido.$footer;
    }
    return $select;
}

    //esto ya esta internacionalizado, no meter funciones!
public function cex_retornar_mapeo_transportistas()
{
    error_reporting(0);
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_retornar_mapeo_transportistas');
    }

    $instancia = new WC_Shipping_Zones();
    $zonas = $instancia->get_zones();

    $ayuda='<div class="row CEX-background-gris-normal p-3 mt-0">
    <div class="col-12 col-xs-12 col-md-12 col-lg-12">
    <p class="mb-1">'.__('Las zonas se listan por <strong>orden de preferencia de uso.</strong>', 'cex_pluggin').'</p>
    <p>'.__('Si hay varias zonas con un valor de 0 en el orden, el orden de preferencia es de arriba a abajo.', 'cex_pluggin').'</div></div>';

    $outerHTML = $ayuda.'<div class="row">';
    $aux = '';
    $i=0;
    foreach ($zonas as $zona) {
        if ($i==0) {
            $bg='';
            $i++;
        } else {
            $bg='CEX-background-bluelight2';
            $i=0;
        }

        $rowStart = '<div class="col-12 col-xs-12 col-md-12 col-lg-12 py-2 my-3 '.$bg.'">';
        $nombreZona =  $zona['zone_name'].'-'.$zona['zone_order'];

        $outerZona = '<p class="mb-0">'.esc_html(__('Orden:', 'cex_pluggin')).' '.$zona['zone_order'].'</p><h4 class="mt-0 mb-1">'.esc_html(__('Zona:', 'cex_pluggin')).' '.$zona['zone_name'].'</h4><input readonly="" type="hidden" class="form-control" aria-label="'.$nombreZona.'" value="'.$nombreZona.'">';

        $metodos = $zona['shipping_methods'];

        foreach ($metodos as $metodo) {
            $aux .= '<div id="nombreCarriersProductos" class="input-group my-0 mb-3">
            <div  id="nombreCarriers" class="input-group-prepend">
            <span class="input-group-text">'.$metodo->get_title().'</span></div>
            '.$this->cex_retornar_select_productosActivosZonas($metodo->get_instance_id()).'
            </div>';
        }
        $rowEnd = '</div>';
        $outerHTML .= $rowStart.$outerZona.$aux.$rowEnd;
        $aux = '';
    }
        //Otras zonas
    global $wpdb;
    $table = $wpdb->base_prefix.'woocommerce_shipping_zone_methods';
    $metodos = $wpdb->get_results($wpdb->prepare("SELECT instance_id from $table
        where zone_id = 0", null));

    $rowStart = '<div class="col-12 col-xs-12 col-md-12 col-lg-12 py-2 my-3 '.$bg.'">';
    $nombreZona =  $zona['zone_name'].'-'.$zona['zone_order'];

    $outerZona = '<p class="mb-0">'.esc_html(__('Orden: Otras zonas', 'cex_pluggin')).'</p><h4 class="mt-0 mb-1">'.esc_html(__('Zona: Otras zonas', 'cex_pluggin')).'</h4><input readonly="" type="hidden" class="form-control" aria-label="Otras zonas" value="Otras zonas">';
    $table2 = $wpdb->base_prefix."options";
    foreach ($metodos as $metodo) {
            //die(var_export($metodo));
        $name='woocommerce_flat_rate_'.$metodo->instance_id.'_settings';
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table2 WHERE option_name ='%s'", $name));
        
        try {
            $options= unserialize($results[0]->option_value);
            //die(var_export($options));
            $aux .= '<div id="nombreCarriersProductos" class="input-group my-0 mb-3">
            <div id="nombreCarriers" class="input-group-prepend">
            <span class="input-group-text">'.$options['title'].'</span></div>
            '.$this->cex_retornar_select_productosActivosZonas($metodo->instance_id).'</div>';
        } catch (Exception $e) {
            
        }
    }

    $rowEnd = '</div>';
    $outerHTML .= $rowStart.$outerZona.$aux.$rowEnd;
    $aux = '';

        //Advance free shipping
    $table = $wpdb->prefix.'posts';
    $cuantos = $wpdb->get_var($wpdb->prepare("SELECT count(*)
        from $table
        where  post_type like 'wafs'", null));
    if ($cuantos != 0) {
        $rowStart = '<div class="col-12 col-xs-12 col-md-12 col-lg-12 py-2 my-3 '.$bg.'">';
        $nombreZona =  $zona['zone_name'].'-'.$zona['zone_order'];

        $outerZona = '<p class="mb-0">'.esc_html(__('Orden: Extra', 'cex_pluggin')).'</p><h4 class="mt-0 mb-1">'.esc_html(__('Zona:', 'cex_pluggin')).' '.$zona['zone_name'].'</h4><input readonly="" type="hidden" class="form-control" aria-label="Advanced Free Shipping" value="'.$nombreZona.'">';

        $aux .= '<div id="nombreCarriersProductos" class="input-group my-0 mb-3">
        <div id="nombreCarriers" class="input-group-prepend">
        <span class="input-group-text">'.esc_html(__('Transportistas Advanced Free Shipping', 'cex_pluggin')).'</span></div>
        '.$this->cex_retornar_select_productosActivosZonas(0).'
        </div>';

        $rowEnd = '</div>';
        $outerHTML .= $rowStart.$outerZona.$aux.$rowEnd;
        $aux = '';
    }
    $outerHTML .= '</div>';
    return $outerHTML;
}

public function sanitize_array_form_transportistas($old_array)
{
    $new_array=array();
    $i=0;
    foreach ($old_array as $campo) {
        $new_array[$i]['name'] = sanitize_text_field($campo['name']);
        $new_array[$i]['value'] = intval($campo['value']);
        $i++;
    }
    return $new_array;
}

public function cex_guardar_mapeo_transportistas()
{
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_guardar_mapeo_transportistas');
    }

    global $wpdb;
    $table = $wpdb->prefix.'cex_savedmodeships';
    $resultado = '';

    $formulario = $this->sanitize_array_form_transportistas($_POST['formulario']);
    $comprobante = 0;
    $productosCEX = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ", null));

    foreach ($productosCEX as $producto) {
        $producto->id_carrier = ';';
        foreach ($formulario as $campo) {
            $valor_idbc = intval($campo['value']);
            $id_carrier = sanitize_text_field($campo['name']);
            if ($producto->id_bc == $valor_idbc) {
                $producto->id_carrier .=$id_carrier.';';
            }
        }

        $comprobante += $wpdb->update(
            $table,
            array('id_carrier'  =>$producto->id_carrier),
            array('id_bc'       =>$producto->id_bc),
            array('%s'),
            array('%s')
        );
    }

    if ($comprobante >= 1) {
        $mensaje= array(
            'type'      => 'success',
            'title'     => esc_html(__('Guardar zonas de env&iacute;o y transportistas', 'cex_pluggin')),
            'mensaje'   => esc_html(__('Los datos se han actualizado correctamente', 'cex_pluggin')),
        );
    } else {
        $mensaje= array(
            'type'      => 'error',
            'title'     => esc_html(__('Guardar zonas de env&iacute;o y transportistas', 'cex_pluggin')),
            'mensaje'   => esc_html(__('Los datos no se han actualizado', 'cex_pluggin')),
        );
    }
    $retorno = array(
        'mensaje'               => $mensaje,
    );
    echo json_encode($retorno);
    exit();
}

public function cex_retornar_select_metodos_pago_activos()
{
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_retornar_select_metodos_pago_activos');
    }

    WC_Payment_Gateways::instance();
    $gateways = WC_Payment_Gateways::instance()->payment_gateways;
    $retorno = '';
    $retorno .=  "<option value='Ninguno'>".esc_html(__('Ninguno', 'cex_pluggin'))."</option>";
    foreach ($gateways as $metodoEnvio) {
        if ($metodoEnvio->enabled != 'no') {
            $retorno .=  "<option value='".$metodoEnvio->id."'>".$metodoEnvio->title."</option>";
        }
    }
    return $retorno;
}

public function cex_retornar_select_referencia_orden()
{
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_retornar_select_referencia_orden');
    }

    global $wpdb;
    $configuracion = $this->cex_get_customer_options();
    $retorno = '';

    if (isset($configuracion['MXPS_REFETIQUETAS']) && $configuracion['MXPS_REFETIQUETAS']!='') {
        if (class_exists('WC_Seq_Order_Number') || class_exists('WC_Seq_Order_Number_Pro')) {
            switch ($configuracion['MXPS_REFETIQUETAS']) {
                case 0:
                        # por defecto
                $retorno .=  "<option value='0' selected >WooCommerce</option>";
                if (class_exists('WC_Seq_Order_Number')) {
                    $retorno .=  "<option value='1'>WooCommerce Sequential Order Numbers</option>";
                }
                if (class_exists('WC_Seq_Order_Number_Pro')) {
                    $retorno .=  "<option value='2'>WooCommerce Sequential Order Numbers PRO</option>";
                }
                break;
                case 1:
                        # WC_Seq_Order_Number
                $retorno .=  "<option value='0'>WooCommerce</option>";
                $retorno .=  "<option value='1' selected >WooCommerce Sequential Order Numbers</option>";
                if (class_exists('WC_Seq_Order_Number_Pro')) {
                    $retorno .=  "<option value='2'>WooCommerce Sequential Order Numbers PRO</option>";
                }

                break;
                case 2:
                        # WC_Seq_Order_Number_Pro
                $retorno .=  "<option value='0'>WooCommerce</option>";
                if (class_exists('WC_Seq_Order_Number')) {
                    $retorno .=  "<option value='1'>WooCommerce Sequential Order Numbers</option>";
                }
                $retorno .=  "<option value='2' selected >WooCommerce Sequential Order Numbers PRO</option>";

                break;
                default:
                        # por defecto
                break;
            }
        } else {
            $retorno .=  "<option value='0' selected >WooCommerce</option>";
        }
    } else {
        if (cex_migracion28($wpdb)) {
            $this->cex_retornar_select_referencia_orden();
        }
    }
    return $retorno;
}

public function cex_comprobar_idpedido_numShip($numcollect, $type)
{
    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('Security cex_comprobar_idpedido_numShip');
    }

    global $wpdb;
    $table= $wpdb->prefix.'cex_savedships';
    $numship = $wpdb->get_var($wpdb->prepare(
        "SELECT  numship
        FROM $table
        WHERE type= %s 
        AND numcollect= %s 
        AND status='Grabado' AND deleted_at is null ",
        $type,
        $numcollect
    ));

    if (empty($numship)) {
        return true;
    } else {
        return false;
    }
}


public function generarLogSoporte(){

    $nonce = sanitize_text_field($_REQUEST['nonce']);

    if (! wp_verify_nonce($nonce, 'cex-nonce')) {
        die('generarLogSoporte');
    }

    $fileName = $_POST['nombre'];        
    switch ($fileName){
        case "cex_savedships":
            $retorno = $this->generarSoporteSavedShips($fileName);            
            break;
        case "cex_history":
           $retorno = $this->generarSoporteHistorico($fileName);            
            break;
        case "cex_migrations":
            $retorno = $this->generarSoporteMigrations($fileName);            
            break;
    }
    echo $retorno;
    exit();
}

public function generarSoporteHistorico($fileName){
    global $wpdb;
    $table= $wpdb->prefix.$fileName;
    $limit = 25;    
    $historico = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table . " ORDER BY id DESC LIMIT ". $limit,null));   
    $result ='';
    foreach ($historico as $his) {
            $result.= "ID HISTÓRICO: ".$his->id."\n";            
            $result.= "\tID ORDEN: ".$his->numCollect."\n";
            $result.= "\tTIPO:  ".$his->type."\n";
            $result.= "\tNÚMERO DE ENVÍO:  ".$his->numShip."\n";
            $result.= "\tRESULTADO:  ".$his->resultado."\n";
            $result.= "\tMENSAJE ERROR WS:  ".$his->mensajeRetorno."\n";
            $result.= "\tCÓDIGO DE RETORNO:  ".$his->codigoRetorno."\n";
            $result.= "\tPETICIÓN DE ENVÍO: \n\t\t ".$his->envioWS."\n";
            $result.= "\tRESPUESTA WS: \n\t\t ".$his->respuestaWS."\n";
            $result.= "\tFECHA DE GRABACIÓN:  ".$his->fecha."\n";
            $result.= "\tFECHA RECOGIDA:  ".$his->fecha_recogida."\n";
            $result.= "\tHORA RECOGIDA DESDE:  ".$his->hora_recogida_desde."\n";
            $result.= "\tHORA RECOGIDA HASTA:  ".$his->hora_recogida_hasta."\n";
            $result.= "\tCÓDIGO PRODUCTO WS:  ".$his->id_bc_ws."\n";
            $result.= "\tNOMBRE PRODUCTO WS:  ".$his->mode_ship_name_ws."\n\n";
            $result.= "________________________________________________________________________________________________________________________________\n\n";          
        }
    return $result;
}

public function generarSoporteSavedShips($fileName){

    global $wpdb;
    $table= $wpdb->prefix.$fileName;

    $limit = 25;
    $savedships = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table . " ORDER BY id_ship DESC LIMIT ". $limit,null));

    $result         = '';  

    foreach($savedships as $savedship){
        $result.= "ID ENVIO: ".$savedship->id_ship."\n";
        $result.= "\tID ORDEN: ".$savedship->id_order."\n";
        $result.= "\tREFERENCIA ENVÍO: ".$savedship->numcollect."\n";
        $result.= "\tTIPO:  ".$savedship->type."\n";
        $result.= "\tNÚMERO DE ENVÍO:  ".$savedship->numship."\n";
        $result.= "\tID REMITENTE:  ".$savedship->id_sender."\n";
        $result.= "\tREMITENTE:  ".$savedship->collectfrom."\n";
        $result.= "\tCÓDIGO POSTAL:  ".$savedship->postalcode."\n";
        $result.= "\tPESO: ".$savedship->kg."\n";
        $result.= "\tBULTOS:  ".$savedship->package."\n";
        $result.= "\tCONTRARREEMBOLSO:  ".$savedship->payback_val."\n";
        $result.= "\tVALOR ASEGURADO:  ".$savedship->insured_value."\n";
        $result.= "\tCODIGO PRODUCTO:  ".$savedship->id_bc."\n";
        $result.= "\tNOMBRE PRODUCTO:  ".$savedship->mode_ship_name."\n";
        $result.= "\tESTADO:  ".$savedship->status."\n";
        $result.= "\tISO PAIS:  ".$savedship->iso_code."\n";
        $result.= "\tDEVOLUCIÓN ??:  ".$savedship->devolution."\n";
        $result.= "\tENTREGA SABADO ??:  ".$savedship->deliver_sat."\n";
        $result.= "\tDESTINATARIO:  ".$savedship->receiver_name."\n";
        $result.= "\tCÓDIGO POSTAL DESTINO:  ".$savedship->receiver_postcode."\n";
        $result.= "\tCÓDIGO CLIENTE:  ".$savedship->codigo_cliente."\n";
        $result.= "\tOFICINA CORREOS:  ".$savedship->oficina_entrega."\n";
        $result.= "\tESTADO DEL ENVÍO:  ".$savedship->WS_ESTADO_TRACKING."\n";
        $result.= "\tID TIENDA:  ".$savedship->id_shop."\n";
        $result.= "\tNOMBRE TIENDA:  ".$savedship->name_shop."\n";
        $result.= "\tCÓDIGO AT PORTUGAL:  ".$savedship->at_portugal."\n";
        $result.= "________________________________________________________________________________________________________________________________\n\n";
        }           
        return $result;
}

public function generarSoporteMigrations($fileName){

    global $wpdb;
    $table= $wpdb->prefix.$fileName;
    $limit = 25;
    $migrations = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table . " ORDER BY id DESC",null));    
    $result='';
    foreach ($migrations as $migration) {
        $result.= "ID MIGRACION: ".$migration->id."\n";
            $result.= "\tNOMBRE MIGRACION: ".$migration->metodoEjecutado."\n";
            $result.= "\tFECHA CREACIÓN: ".$migration->created_at."\n";
            $result.= "________________________________________________________________________________________________________________________________\n\n";
    }
    return $result;
}
    //Funcion para soap

public function cex_soap()
{
        /*$nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_form_pedido');
        }*/

        $opciones = $this->cex_get_customer_options();
        // usa el lib/soap.php (todas las funciones que no tan aqui, tan alli)
        global $wpdb;
        $request = array_map('sanitize_text_field', $_REQUEST = str_replace("\'", "'", $_REQUEST));
        $tipo_peticion ='';
        //die(var_dump($request));
        //$numcollect = ($_POST['loadReceiver']=='true') ? $_POST['id'].'d' : $_POST['id'];
        $numship = $this->sanitize_checkbox($request['grabar_recogida'])=='false' ? 'Automatico' : '';
        $numcollect = sanitize_text_field($request['ref_ship']);
        //el check de marcar recogida
        //$this->cex_guardar_datos_orden($_REQUEST);
        //inicializamos la orden
        $order = new WC_Order(intval($request['id']));
        //RECOGIDA
        $respuesta_recogida;
        $type = 'Recogida';
        if ($this->cex_comprobar_idpedido_numShip($numcollect, 'Recogida')) {
            $this->cex_guardar_savedships($request, $numship, 'Recogida');
            if ($numship != 'Automatico') {
                $soap =  enviar_peticion_recogida_soap_cex($request);                
                $retorno = cex_procesar_curl($soap);                
                $respuesta_recogida = procesar_peticion_recogida_soap_cex($soap['soap'], $retorno, intval($request['id']), $type, $numcollect);                
                //la peticion fue erronea ( faltan campos por lo general)
                $respuesta_recogida = [
                    'mensError'                     => $respuesta_recogida['mensajeRetorno'],
                    'numRecogida'                   => $respuesta_recogida['numShip'],
                    'resultado'                     => $respuesta_recogida['resultado']
                ];
                if ($respuesta_recogida['resultado'] == 0) {
                    $this->cex_deleteSavedShip($numcollect);
                    //En el caso de que de un error de recogida, no hacemos la petici&oacute;n de env&iacute;o.
                    $respuesta_compuesta = array(
                        'recogida'                         => $respuesta_recogida,
                        'envio'                            => null,
                    );
                    echo json_encode($respuesta_compuesta);
                    exit;
                } else {
                    $this->cex_guardar_savedships($request, $respuesta_recogida['numRecogida'], $type);
                }
            } else {
                //para los automaticos
                $respuesta_recogida = array(
                    'mensError'           => __('La recogida ser&aacute; autom&aacute;tica', 'cex_pluggin'),
                    'numRecogida'                  => 'Automatico',
                    'resultado'                => '1',
                );
            }
        } else {
            //para los duplicados
            $respuesta_recogida = array(
                'mensError'           => esc_html(__('Error [PSDBE] al guardar la recogida, referencia duplicada en la BD [SAVED]. Cambie la referencia y vuelva a intentarlo ', 'cex_pluggin')),
                'numRecogida'                 => '',
                'resultado'               => '0',
            );

            //En el caso de que de un error de recogida, no hacemos la petici&oacute;n de env&iacute;o.
            $respuesta_compuesta = array(
                'recogida'                         => $respuesta_recogida,
                'envio'                            => null,
            );
            echo json_encode($respuesta_compuesta);
            exit;
        }

        //ENVIO
        $respuesta_envio;
        $numship = '';
        if ($this->cex_comprobar_idpedido_numShip($numcollect, 'Envio')) {
            /*if ($_POST['entrega_oficina']=='true') {
                $type = 'Envio Recogida Oficina';
            }else {
            }*/
            $type = 'Envio';
            $this->cex_guardar_savedships($request, $numship, $type);
            $soap =  cex_enviar_peticion_envio_soap($request);
            $retorno = cex_procesar_curl($soap);
            $respuesta_envio = cex_procesar_peticion_envio_soap($soap['soap'], $retorno, intval($request['id']), $type, $numcollect);
            $this->cex_guardar_savedships($request, $respuesta_envio['numShip'], $type);

            $respuesta_envio = [
                'mensError'                     => $respuesta_envio['mensajeRetorno'],
                'datosResultado'                   => $respuesta_envio['numShip'],
                'resultado'                     => $respuesta_envio['resultado']
            ];
            //si todo correcto generamos las etiquetas de esta peticion
            if (!empty($respuesta_envio['datosResultado'])) {
                //iniciarCreacionEtiquetas($numcollect);
                //notiticar al de la tienda.
                //$order->add_order_note("Ya tiene asignado numero de envio y sus etiquetas ", 0, "Correos Express");
                if (strcmp($opciones['MXPS_ENABLESHIPPINGTRACK'],'true') == 0) {
                    //a?adir un mensaje al cliente
                    $nota = esc_html(__("Ya puede hacer seguimiento de su pedido con referencia ", "cex_pluggin"));
                    $order->add_order_note($nota.$respuesta_envio['numShip']." <a   href='https://s.correosexpress.com/c?n=".$respuesta_envio['numShip']."' target='blank'>".esc_html(__('aqui', 'cex_pluggin'))."</a>", 1, "Correos Express");
                }
            } else {
                //si no recibimos para generar las etiquetas, notificamos el error y borramos la peticion de savedships para que no salga en el historico.
                $this->cex_deleteSavedShip($numcollect);
            }
        } else {
            //para los automaticos
            $respuesta_envio = array(
                'mensError'          => esc_html(__('Error [PSDBE] al guardar el env&iacute;o, referencia duplicada en la BD [SAVED]. Cambie la referencia y vuelva a intentarlo ', 'cex_pluggin')),
                'datosResultado'             => '',
                'resultado'               => '0',
            );
        }

        //Para las recogidas Autom&aacute;ticas: Si env&iacute;o duplicado: no mostrar texto de recogida autom&aacute;tica
        if ($respuesta_envio['datosResultado'] == '') {
            if ($respuesta_recogida['numRecogida']=='Automatico') {
                $respuesta_compuesta = array(
                    'recogida'              => null,
                    'envio'                 => $respuesta_envio,
                );

                echo json_encode($respuesta_compuesta);
                exit;
            }
        }

        //Si envio correcto cambiamos estado del pedido a completado para empezar las gestiones
        if ($opciones['MXPS_SAVEDSTATUS']=='true') {
            $this->cex_cambiar_estado_orden(intval($request['id']));
        }
        
        //Si env&iacute;o correcto: mostramos ambos
        $respuesta_compuesta = array(
            'recogida'             => $respuesta_recogida,
            'envio'                => $respuesta_envio,

        );

        echo json_encode($respuesta_compuesta);
        exit;  
    }

    //Funcion para el rest
    public function cex_rest(){
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_form_pedido');
        }

        $opciones = $this->cex_get_customer_options();
        // usa el lib/rest.php (todas las funciones que no tan aqui, tan alli)
        global $wpdb;
        $request = array_map('sanitize_text_field', $_REQUEST = str_replace("\'", "'", $_REQUEST));
        $tipo_peticion ='';
        //die(var_dump($request));
        //$numcollect = ($_POST['loadReceiver']=='true') ? $_POST['id'].'d' : $_POST['id'];
        $numship = $this->sanitize_checkbox($request['grabar_recogida'])=='false' ? 'Automatico' : '';
        $numcollect = sanitize_text_field($request['ref_ship']);
        //el check de marcar recogida
        //$this->cex_guardar_datos_orden($_REQUEST);
        //inicializamos la orden
        $order = new WC_Order(intval($request['id']));

        //ENVIO
        $respuesta_envio;
        $numship = '';
        if ($this->cex_comprobar_idpedido_numShip($numcollect, 'Envio')) {
            /*if ($_POST['entrega_oficina']=='true') {
                $type = 'Envio Recogida Oficina';
            }else {
            }*/
            $type = 'Envio';
            $this->cex_guardar_savedships($request, $numship, $type);
            $rest =  cex_enviar_peticion_envio_rest($request);                     
            $retorno = cex_procesar_curl_rest($rest); 

            $respuesta_envio = cex_procesar_peticion_envio_rest($rest['peticion'], $retorno, intval($request['id']), $type, $numcollect);
            $this->cex_guardar_savedships($request, $respuesta_envio['numShip'], $type);            
            if($respuesta_envio['numRecogida'] != ''){
                $this->cex_guardar_savedships($request, $respuesta_envio['numRecogida'], 'Recogida');
            }else{
                $this->cex_guardar_savedships($request, 'Recogida Erronea', 'Recogida');
            }           

            //si todo correcto generamos las etiquetas de esta peticion
            if (!empty($respuesta_envio['numShip'])) {
                //iniciarCreacionEtiquetas($numcollect);
                //notiticar al de la tienda.
                //$order->add_order_note("Ya tiene asignado numero de envio y sus etiquetas ", 0, "Correos Express");
                if (strcmp($opciones['MXPS_ENABLESHIPPINGTRACK'],'true') == 0) {
                    //a?adir un mensaje al cliente
                    $nota = esc_html(__("Ya puede hacer seguimiento de su pedido con referencia ", "cex_pluggin"));
                    $order->add_order_note($nota.$respuesta_envio['numShip']." <a   href='https://s.correosexpress.com/c?n=".$respuesta_envio['numShip']."' target='blank'>".esc_html(__('aqui', 'cex_pluggin'))."</a>", 1, "Correos Express");
                } 
                if ($opciones['MXPS_SAVEDSTATUS']=='true') {
                    $this->cex_cambiar_estado_orden(intval($request['id']));
                }
            } else {
                //si no recibimos para generar las etiquetas, notificamos el error y borramos la peticion de savedships para que no salga en el historico.
                $this->cex_deleteSavedShip($numcollect);
            }
        } else {
            //para los automaticos
            $respuesta_envio = array(
                'mensajeRetorno'          => esc_html(__('Error [PSDBE] al guardar el env&iacute;o, referencia duplicada en la BD [SAVED]. Cambie la referencia y vuelva a intentarlo ', 'cex_pluggin')),
                'numShip'                 => '',
                'resultado'               => '0',
            );
        }


        echo json_encode($respuesta_envio);
        exit;
    }

    //Recepcion del formulario de la orden
    public function cex_form_pedido()
    {
        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';        
        $valor = $wpdb->get_var("SELECT valor
            FROM $table
            WHERE clave= 'MXPS_DEFAULTWS'");

        if (strcmp($valor, 'SOAP')==0){            
            $this->cex_soap();
        }
        else{
            $this->cex_rest();
        }
    }

    public function sanitize_checkbox($input)
    {
        //returns true if checkbox is checked
        return (strcmp($input, 'false') == 0  ?  'false' : 'true');
    }
    
    public function sanitize_array_ordenes($old_array)
    {
        $i=0;
        foreach ($old_array as $campo) {
            $new_array[$i]['id'] = intval($campo['id']);
            $new_array[$i]['bultos'] = intval($campo['bultos']);
            $new_array[$i]['productosCEX'] = intval($campo['productosCEX']);
            $i++;
        }
        return $new_array;
    }

    public function cex_masiva_soap(){
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_form_pedidos');
        }
        $ordenes = $this->sanitize_array_ordenes($_POST['ordenes']);

        $retorno='';
        $opciones = $this->cex_get_customer_options();
        $respuesta_envio;
        //por cada orden todo el proceso
        foreach ($ordenes as $orden) {
            $order = new WC_Order(intval($orden['id']));            
            $numShip = '';
            $oficina = get_post_meta($order->get_id(), '_Oficina', true);
            //numeroRefencia = numeroOrden + ga (generacion automatica)           
            $numcollect = $this->cex_retornar_referencia_order(intval($orden['id']));
            $numcollect .= 'ga';            
            $datosOrden = $this->cex_obtenerDatosRequestSavedShips(intval($orden['id']), $numcollect, intval($orden['bultos']),$orden['productosCEX'], $oficina);                  
            $datosOrden = str_replace("\'", "'", $datosOrden);                
            if ($this->cex_comprobar_idpedido_numShip($numcollect, 'Envio')) {
                $type = 'Envio';
                $this->cex_guardar_savedships($datosOrden, $numShip, $type);
                $datos_peticion= $this->cex_retornar_savedships($orden['id']);                
                $soap =  cex_enviar_peticion_envio_soap($datosOrden);
                $retorno = cex_procesar_curl($soap);
                $respuesta_envio = cex_procesar_peticion_envio_soap($soap['soap'], $retorno, intval($orden['id']), $type, $numcollect);
                $this->cex_guardar_savedships($datosOrden, $respuesta_envio['numShip'], $type);

                if ($this->cex_comprobar_idpedido_numShip($numcollect, 'Envio')==false) {
                    if (strcmp($opciones['MXPS_ENABLESHIPPINGTRACK'],'true') == 0) {
                        //a?adir un mensaje al cliente
                        $order = new WC_Order(intval($orden['id']));
                        $nota = esc_html(__("Ya puede hacer seguimiento de su pedido con referencia ", "cex_pluggin"));
                        $order->add_order_note($nota.$respuesta_envio['numShip']." <a   href='https://s.correosexpress.com/c?n=".$respuesta_envio['numShip']."' target='blank'>".esc_html(__('aqui', 'cex_pluggin'))."</a>", 1, "Correos Express");
                    }
                    if ($opciones['MXPS_SAVEDSTATUS']=='true') {
                        $this->cex_cambiar_estado_orden(intval($orden['id']));
                    }
                } else {
                    // si no recibimos para generar las etiquetas, notificamos el error y borramos la peticion de cex_savedships para que no salga en el historico.
                    $this->cex_deleteSavedShip($numcollect);
                }

                /*
                    Controlo si se ha realizado cambio en BBDD o solo en la asignacion del producto
                */
                    switch ($datosOrden['modificacionAutomatica']) {
                        case 1:
                        $respuesta_envio = array(
                            'mensajeRetorno'          => esc_html(__('Modificada relaci&ooacute;n transportista, se asigna PAQ 24')),
                            'numShip'                 => '',
                            'resultado'               => '0',
                            'numCollect'              => $numcollect,
                            'id_order'                => intval($orden['id'])
                        );
                        break;
                        case 2:
                        $respuesta_envio = array(
                            'mensajeRetorno'          =>  esc_html(__('Modificados productos configurados, se asigna y a&ntilde;ade PAQ 24')),
                            'numShip'                 => '',
                            'resultado'               => '0',
                            'numCollect'              => $numcollect,
                            'id_order'                => intval($orden['id'])

                        );
                        break;
                        default:
                    }
                //Si envio correcto cambiamos estado del pedido a completado para empezar las gestiones
                } else {
                //para los automaticos
                    $respuesta_envio = array(
                        'mensajeRetorno'          => esc_html(__('Error [PSDBE] al guardar el envio, referencia duplicada en la BD [SAVED]. Cambie la referencia y vuelva a intentarlo ', 'cex_pluggin')),
                        'numShip'                 => '',
                        'resultado'               => '0',
                        'numCollect'              => $numcollect,
                        'id_order'                => intval($orden['id'])
                    );
                }
                $retorno2[] = $respuesta_envio;
            }        
            echo json_encode($retorno2);
            exit;
        }

        public function cex_masiva_rest(){
         $nonce = sanitize_text_field($_REQUEST['nonce']);

         if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_form_pedidos');
        }
        $ordenes = $this->sanitize_array_ordenes($_POST['ordenes']);        
        

        $retorno='';
        $opciones = $this->cex_get_customer_options();
        $respuesta_envio;

        //por cada orden todo el proceso
        foreach ($ordenes as $orden) {
            $order = new WC_Order(intval($orden['id']));            
            $numShip = '';
            $oficina = get_post_meta($order->get_id(), '_Oficina', true);
            //numeroRefencia = numeroOrden + ga (generacion automatica)           
            $numcollect = $this->cex_retornar_referencia_order(intval($orden['id']));
            $numcollect .= 'ga';            

            $datosOrden = $this->cex_obtenerDatosRequestSavedShips(intval($orden['id']), $numcollect, intval($orden['bultos']),$orden['productosCEX'], $oficina);       

            $datosOrden = str_replace("\'", "'", $datosOrden);   
       
            if ($this->cex_comprobar_idpedido_numShip($numcollect, 'Envio')) {
                $type = 'Envio';
                $this->cex_guardar_savedships($datosOrden, $numShip, $type);

                //$datos_peticion= $this->cex_retornar_savedships($datosOrden['id']);
                $rest = enviar_peticion_masivas_rest($datosOrden); 
                $retorno = cex_procesar_curl_rest($rest);
                $respuesta_envio = cex_procesar_peticion_envio_rest($rest['peticion'], $retorno, intval($orden['id']), $type, $numcollect);

                $this->cex_guardar_savedships($datosOrden, $respuesta_envio['numShip'], $type);

                if ($this->cex_comprobar_idpedido_numShip($numcollect, 'Envio')==false) {
                    if (strcmp($opciones['MXPS_ENABLESHIPPINGTRACK'],'true') == 0) {
                        //a?adir un mensaje al cliente
                        $order = new WC_Order(intval($orden['id']));
                        $nota = esc_html(__("Ya puede hacer seguimiento de su pedido con referencia ", "cex_pluggin"));
                        $order->add_order_note($nota.$respuesta_envio['numShip']." <a   href='https://s.correosexpress.com/c?n=".$respuesta_envio['numShip']."' target='blank'>".esc_html(__('aqui', 'cex_pluggin'))."</a>", 1, "Correos Express");
                    }
                    if ($opciones['MXPS_SAVEDSTATUS']=='true') {
                        $this->cex_cambiar_estado_orden(intval($orden['id']));
                    }
                } else {
                    // si no recibimos para generar las etiquetas, notificamos el error y borramos la peticion de cex_savedships para que no salga en el historico.
                    $this->cex_deleteSavedShip($numcollect);

                    if(empty($datosOrden["iso_code_remitente"])){
                        $respuesta_envio = array(
                            'mensajeRetorno'          => esc_html(__('Error no hay guardado ningún remitente por defecto', 'cex_pluggin')),
                            'numShip'                 => '',
                            'resultado'               => '99',
                            'numCollect'              => $numcollect,
                            'id_order'                => intval($orden['id'])
                        );
                    }
                }

                /*
                    Controlo si se ha realizado cambio en BBDD o solo en la asignacion del producto
                */
                    switch ($datosOrden['modificacionAutomatica']) {
                        case 1:
                        $respuesta_envio = array(
                            'mensajeRetorno'          => esc_html(__('Modificada relaci&ooacute;n transportista, se asigna PAQ 24')),
                            'numShip'                 => '',
                            'resultado'               => '0',
                            'numCollect'              => $numcollect,
                            'id_order'                => intval($orden['id'])
                        );
                        break;
                        case 2:
                        $respuesta_envio = array(
                            'mensajeRetorno'          =>  esc_html(__('Modificados productos configurados, se asigna y a&ntilde;ade PAQ 24')),
                            'numShip'                 => '',
                            'resultado'               => '0',
                            'numCollect'              => $numcollect,
                            'id_order'                => intval($orden['id'])

                        );
                        break;
                        default:
                    }
                //Si envio correcto cambiamos estado del pedido a completado para empezar las gestiones
                } else {
                //para los automaticos
                    $respuesta_envio = array(
                        'mensajeRetorno'          => esc_html(__('Error [PSDBE] al guardar el envio, referencia duplicada en la BD [SAVED]. Cambie la referencia y vuelva a intentarlo ', 'cex_pluggin')),
                        'numShip'                 => '',
                        'resultado'               => '0',
                        'numCollect'              => $numcollect,
                        'id_order'                => intval($orden['id'])
                    );
                }
                $retorno2[] = $respuesta_envio;
            }        
            echo json_encode($retorno2);
            exit;
        }

    //solo se graban envios a granel
        public function cex_form_pedidos()
        {
            global $wpdb;
            $table = $wpdb->prefix.'cex_customer_options';        
            $valor = $wpdb->get_var("SELECT valor
                FROM $table
                WHERE clave= 'MXPS_DEFAULTWS'");

            if (strcmp($valor, 'SOAP')==0){            
                $this->cex_masiva_soap();
            }
            else{
                $this->cex_masiva_rest();
            } 
        }

        public function cex_obtenerDatosRequestSavedShips($idOrder, $numcollect, $bultos, $productoCEX, $oficina)
        {

            $order          = new WC_Order($idOrder);
            $opciones       = $this->cex_get_customer_options();
            $remitente      = $this->cex_get_saved_sender($opciones['MXPS_DEFAULTSEND']);

            $metodo         = $this->cex_retornar_metodo_envio_y_texto($idOrder,$productoCEX);
            //$metodo         = $envio_idbc;
            $iso_code       = $order->get_billing_country();


            //$bultos         = sacarBultos($idOrder);
            $datosEnvio =   ($opciones['MXPS_DEFAULTDELIVER'] == 'FACTURACION') ? $this->cex_datos_facturacion($idOrder) : $this->cex_datos_envio($idOrder);
            $postcode_rec = $datosEnvio['postcode'];
            $peso = $this->cex_retornar_peso_orden($idOrder);
            $entrega_oficina = '';
            $codigo_oficina= '';
            if ($oficina!='') {
                $splitofi=explode("#!#", $oficina);
                $entrega_oficina=true;
                $codigo_oficina=$splitofi[0];
            } else {
                $entrega_oficina=false;
            }

            $country='';
            if ($iso_code == 'ES') {
                $country = 'España';
                $codigo_postal_destino = $postcode_rec;
            } elseif ($iso_code == 'PT') {
                $country = 'Portugal';
                if (strpos($postcode_rec, '-')) {
                    $postcode_rec = explode('-', $postcode_rec);
                    $codigo_postal_destino = $postcode_rec[0];
                } else {
                    $codigo_postal_destino = $postcode_rec;
                }
            } elseif ($iso_code == 'AD') {
                $country = 'Andorra';
                $codigo_postal_destino = $postcode_rec;
            }else{
                $codigo_postal_destino = $postcode_rec;
            }

            $modificacionAutomatica = 0;
            $id_bc              = $metodo->id_bc;
            $nombreMetodo       = $metodo->name;
            
            //coger el savedsender  ==> remitente por defecto
            $contact='';
            $company=$order->get_shipping_company();
            if ($company =='' || $company==null || $company ==' ') {
                $contact = $order->get_shipping_first_name().' '.$order->get_shipping_last_name();
                if( $contact == '' || $contact==null || $contact ==' ') {
                    $contact = $order->get_shipping_first_name().' '.$order->get_shipping_last_name();
                }
            } else {
                $contact = $company;
            }

            $datosEnvio['contact'] = $contact;

            if ($order->get_payment_method() == $opciones['MXPS_DEFAULTPAYBACK']) {
                $contrareembolso = $order->total;
            } else {
                $contrareembolso = '';
            }

            $ref_ship = $this->cex_retornar_referencia_order($idOrder).'ga';

            $aux = [
                'id'                        => $idOrder,
            //primera columna
                'loadSender'                => $opciones['MXPS_DEFAULTSEND'],
                'name_sender'               => $remitente->name,
                'contact_sender'            => $remitente->contact,
                'address_sender'            => $remitente->address,
                'postcode_sender'           => $remitente->postcode,
                'city_sender'               => $remitente->city,
                'country_sender'            => $country,
                'iso_code_remitente'        => $remitente->iso_code_pais,
                'phone_sender'              => $remitente->phone,
                'email_sender'              => $remitente->email,
                'grabar_recogida'           => 'false',
                'note_collect'              => '',
            //segunda columna
                'loadReceiver'              => 'false',
                'name_receiver'             => $datosEnvio['name'],
                'contact_receiver'          => $datosEnvio['contact'],
                'address_receiver'          => $datosEnvio['address'],
                'postcode_receiver'         => $codigo_postal_destino,
                'city_receiver'             => $datosEnvio['city'],
                'phone_receiver1'           => $datosEnvio['phone'],
                'phone_receiver2'           => '',
                'email_receiver'            => $datosEnvio['email'],
                'country_receiver'          => $country,
                'note_deliver'              => substr($order->get_customer_note(), 0, 70),
            //tercera columna
                'id_codigo_cliente'         => $remitente->id_cod_cliente,
                'codigo_cliente'            => $remitente->customer_code,
                'codigo_solicitante'        => $remitente->code_demand,
                'datepicker'                => date("Y-m-d"),
                'fromHH_sender'             => $remitente->from_hour,
                'fromMM_sender'             => $remitente->from_minute,
                'toHH_sender'               => $remitente->to_hour,
                'toMM_sender'               => $remitente->to_minute,
                'ref_ship'                  => $ref_ship,
                'desc_ref_1'                => '',
                'desc_ref_2'                => '',
                'selCarrier'                => $id_bc,
                'nombre_modalidad'          => $nombreMetodo,
                'deliver_sat'               => '',
                'iso_code'                  => $iso_code,
                'entrega_oficina'           => $entrega_oficina,
                'codigo_oficina'            => $codigo_oficina,
                'text_oficina'              => $oficina,
                'payback_val'               => $contrareembolso,
                'insured_value'             => '',
                'bultos'                    => $bultos,
                'kilos'                     => $peso,
                'modificacionAutomatica'    => $modificacionAutomatica,
            ];
            return $aux;
        }

        public function cex_retornar_metodo_envio_y_texto($id, $envio_idbc=false)
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_retornar_metodo_envio_y_texto');
            }

        //inicializamos la orden
            $order = new WC_Order($id);           
            $shipping_item_data = $this->cex_get_shipping_data_by_order($id);
            $id_shiping = $shipping_item_data['instance_id'];

            global $wpdb;
            $table = $wpdb->prefix.'cex_savedmodeships';
            if(empty($envio_idbc)){
                $metodo_envio = $wpdb->get_row($wpdb->prepare(" SELECT id_bc, name
                    FROM $table 
                    WHERE id_carrier LIKE '%;$id_shiping;%' ", null));
            }else{
                $metodo_envio = $wpdb->get_row($wpdb->prepare("SELECT id_bc, name
                    FROM $table 
                    WHERE id_bc = $envio_idbc ", null));
            }
            return $metodo_envio;
        }

        public function cex_retornar_savedships($id)
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_retornar_savedships');
            }
            global $wpdb;
            $table = $wpdb->prefix.'cex_savedships';
            $tableHist = $wpdb->prefix.'cex_history';

            $results = $wpdb->get_results($wpdb->prepare("SELECT save.*,hist.fecha_recogida,hist.hora_recogida_desde,hist.hora_recogida_hasta
                FROM $table as save
                LEFT JOIN $tableHist as hist
                ON save.numship=hist.numShip
                AND save.numcollect = hist.numCollect
                AND (hist.type = 'Envio' OR hist.type = 'Recogida')
                WHERE save.id_order = %d 
                AND deleted_at is null
                ORDER BY save.created_at DESC
                LIMIT 1", $id));

            return $results;
        }



        public function cex_guardar_datos_orden($datos)
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_guardar_datos_orden');
            }

            $order_id = $datos[ 'id' ];
            $order = wc_update_order(array('order_id' => $order_id ));
            $nombre_destinatario = $datos['name_receiver'];
            $nombreapellidodest= explode(' ', $nombre_destinatario);
            $nombre_remitente = $datos['name_sender'];
            $nombreapellidorem= explode(' ', $nombre_remitente);

        //$longitud = 5;
        //$cpdest = $datos['postcode_receiver'];
        //$postcode_receiver = cex_rellenar_ceros($cpdest, $longitud);
            $postcode_receiver =$datos['postcode_receiver'];

            $address_destino = array(
                'first_name' => $nombreapellidodest[0],
                'last_name'  => $nombreapellidodest[1],
            //'company'    => '',
                'email'      => $datos['email_receiver'],
                'phone'      => $datos['phone_receiver1'],
                'address_1'  => $datos['address_receiver'],
                'address_2'  => '',
                'city'       => $datos['city_receiver'],
            //'state'      => '',
                'postcode'   => $postcode_receiver,
            //'country'    => ''
            );

        //observaciones
        //$order->update_status('completed-cex', 'Correos express:');-> CAmbiar el estado del pedido
        //$order->set_address($address_remitente, 'billing');
            $order->set_address($address_destino, 'shipping');
        }

        public function cex_guardar_savedships($datos, $num_ship, $type = '')
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_guardar_savedships');
            }

            global $wpdb;
            $nombreTabla = $wpdb->prefix."cex_savedships";
            $status = '';
            /*if ($type == 'Envio Recogida Oficina') {
                $type = 'Envio';
            }*/
            //con saber si hay mas de uno, ya sabemos que es un insert, envio siempre tiene que haber
            $numcollect = $datos['ref_ship'];
            $cuantos = $wpdb->get_var($wpdb->prepare("SELECT  count(*)
                FROM $nombreTabla
                WHERE type= %s AND numcollect= %s AND deleted_at is null", $type, $numcollect));

            $tipo_peticion ='';
            if ($cuantos == 0) {
                $tipo_peticion ='create';
            } else {
                $tipo_peticion ='update';
            }

            //cuidado con los booleanos, vienen como strings
            $numship    = ($datos['grabar_recogida']=='false' && $type=='Recogida') ? 'Automatico' : '';
            $devolucion=0;

            $numship = $num_ship;
            $status = 'Grabado';

            if ($datos['loadReceiver']=='true') {
                $devolucion = 1;
            } else {
                $devolucion = 0;
            }
            if ($datos['deliver_sat']=='true') {
                $entrega_sabado = 1;
            } else {
                $entrega_sabado = 0;
            }
            /*
                $longitud = 5;
                $cprem = $datos['postcode_sender'];
                $cpdest = $datos['postcode_receiver'];

                $postcode_sender = cex_rellenar_ceros($cprem, $longitud);
                $postcode_receiver = cex_rellenar_ceros($cpdest, $longitud);*/

        //Peso


            $savedship = array(
                'date'                  =>sanitize_text_field($datos['datepicker']),
                'numcollect'            =>sanitize_text_field($numcollect),
                'numship'               =>sanitize_text_field($num_ship),
                'collectfrom'           =>sanitize_text_field($datos['name_sender']),
                'postalcode'            =>sanitize_text_field($datos['postcode_sender']),
                'id_order'              =>intval($datos['id']),
                'id_mode'               =>null,
                'id_sender'             =>intval($datos['loadSender']),
                'type'                  =>$type,
                'kg'                    =>calcularPesoEnKilos($datos['kilos']),
                'package'               =>intval($datos['bultos']),
                'payback_val'           =>sanitize_text_field($datos['payback_val']),
                'insured_value'         =>intval($datos['insured_value']),
                'id_bc'                 =>sanitize_text_field($datos['selCarrier']),
                'mode_ship_name'        =>sanitize_text_field($datos['nombre_modalidad']),
                'status'                =>$status,
                'note_collect'          =>sanitize_text_field($datos['note_collect']),
                'note_deliver'          =>sanitize_text_field($datos['note_deliver']),
                'iso_code'              =>sanitize_text_field($datos['iso_code']),
                'devolution'            =>$devolucion,
                'deliver_sat'           =>$entrega_sabado,
                'mailLabel'             =>0,
                'at_portugal'           =>sanitize_text_field($datos['at_portugal']),
                'desc_ref_1'            =>sanitize_text_field($datos['desc_ref_1']),
                'desc_ref_2'            =>sanitize_text_field($datos['desc_ref_2']),
                'from_hour'             =>intval($datos['fromHH_sender']),
                'from_minute'           =>intval($datos['fromMM_sender']),
                'to_hour'               =>intval($datos['toHH_sender']),
                'to_minute'             =>intval($datos['toMM_sender']),
                'sender_name'           =>sanitize_text_field($datos['name_sender']),
                'sender_contact'        =>sanitize_text_field($datos['contact_sender']),
                'sender_address'        =>sanitize_text_field($datos['address_sender']),
                'sender_postcode'       =>sanitize_text_field($datos['postcode_sender']),
                'sender_city'           =>sanitize_text_field($datos['city_sender']),
                'sender_phone'          =>intval($datos['phone_sender']),
                'sender_country'        =>sanitize_text_field($datos['country_sender']),
                'sender_email'          =>sanitize_email($datos['email_sender']),
                'receiver_name'         =>sanitize_text_field($datos['name_receiver']),
                'receiver_contact'      =>sanitize_text_field($datos['contact_receiver']),
                'receiver_address'      =>sanitize_text_field($datos['address_receiver']),
                'receiver_postcode'     =>sanitize_text_field($datos['postcode_receiver']),
                'receiver_city'         =>sanitize_text_field($datos['city_receiver']),
                'receiver_phone'        =>intval($datos['phone_receiver1']),
                'receiver_phone2'       =>intval($datos['phone_receiver2']),
                'receiver_email'        =>sanitize_email($datos['email_receiver']),
                'receiver_country'      =>sanitize_text_field($datos['country_receiver']),
                'codigo_cliente'        =>$datos['codigo_cliente'],
                'oficina_entrega'       =>sanitize_text_field($datos['text_oficina']),
            );

            switch ($tipo_peticion) {
                case 'create':
                $savedship['created_at'] = date("Y-m-d H:i:s");
                $savedship['updated_at'] = date("Y-m-d H:i:s");
                $savedship['modificacionAutomatica']=$datos['modificacionAutomatica'];
                $sql=$wpdb->prepare("INSERT INTO $nombreTabla (date, numcollect, numship, collectfrom, postalcode, id_order, id_mode, id_sender, type, kg, package, payback_val, insured_value, id_bc, mode_ship_name, status, id_ship_expired, id_group, note_collect, note_deliver, iso_code, devolution, deliver_sat, mailLabel, at_portugal,desc_ref_1, desc_ref_2, from_hour, from_minute, to_hour, to_minute, sender_name, sender_contact, sender_address, sender_postcode, sender_city, sender_phone, sender_country, sender_email, receiver_name, receiver_contact, receiver_address, receiver_postcode, receiver_city, receiver_phone, receiver_phone2, receiver_email, receiver_country, codigo_cliente, oficina_entrega, created_at, updated_at, WS_ESTADO_TRACKING, deleted_at, modificacionAutomatica) VALUES (%s,%s,%s,%s,%s,%d,%d,%d,%s,%d,%d,%d,%d,%d,%s,%s,NULL,NULL,%s,%s,%s,%d,%d,%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,'',NULL,%s)", $savedship);
                $wpdb->query($sql);
                break;
                case 'update':
                $savedship['updated_at'] = date("Y-m-d H:i:s");
                $where =[
                    'type'=> $type,
                    'numcollect'=> $numcollect,
                    'deleted_at'=> null
                ];
                $wpdb->update($nombreTabla, $savedship, $where);
                break;
            }
        }

        public function cex_deleteSavedShip($numCollect)
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_deleteSavedShip');
            }

            global $wpdb;
            $table = $wpdb->prefix.'cex_savedships';
            $where = array('numcollect' => $numCollect);
        //php $wpdb->delete( $table, $where, $where_format = null );
            $wpdb->delete($table, $where, $where_format = null);
        }

        public function cex_form_order_template()
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_form_order_template');
            }

            $id = intval($_POST['id']);
            $retorno = array(
                'etiquetaDefecto'       => $this->cex_retornar_etiqueta(),
                'metodoEnvio'           => $this->cex_retornar_metodo_envio($id),
                'esCEX'                 => $this->cex_retornar_bool_pedido($id),
                'selectCodCliente'      => $this->cex_retornar_select_codigos_cliente(),
                'selectRemitentes'      => $this->cex_retornar_select_remitentes(),
                'selectDestinatarios'   => $this->cex_retornar_select_destinatario(),
                'productos'             => $this->cex_retornar_select_productosActivos($id),
                'datosRemitente'        => $this->cex_recuperarRemitenteDefecto(),
                'datosEnvio'            => $this->cex_recuperarDatosEnvio($id),
                'paises'                => $this->cex_retornar_paises(),
                'contrareembolso'       => $this->cex_comprobar_contrareembolso($id),
                'peso'                  => $this->cex_retornar_peso_orden($id),/**/
                'manual'                => $this->cex_shop_order_manual(),
                'referenciaOrder'       => $this->cex_retornar_referencia_order($id),
                'unidadMedida'          => get_option('woocommerce_weight_unit')
            );
            echo json_encode($retorno);
            exit;
        }

        public function cex_obtener_Productos_Cex($id_order = 0)
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_obtener_Productos_Cex');
            }

            global $wpdb;
        /*
        Compruebo desde donde recibo la peticion
            Pedido
            Utilidades
        */
            $update         = false;
            $contenido      = false;
            $table          = $wpdb->prefix.'cex_savedmodeships';

            $sql = "SELECT checked FROM $table WHERE id_bc='63'";
            $results = $wpdb->get_var($sql);
        /*
            En caso de que ya este checkado el producto
        */
            if (strcmp($results, "1") == 0) {
                $contenido  = true;
            } else {
                $table      = $wpdb->prefix.'cex_savedmodeships';

                $sql = "UPDATE $table SET checked = 1
                WHERE id_customer_code = $id_customer_code AND id_bc='63'";
                $results =$wpdb->query($wpdb->prepare($sql, null));

                if ($results) {
                    $update = true;
                }
            }
            $retorno = array(
                'contenido' => $contenido,
                'update'    => $update,
            );

            return $retorno;
        }

    /*public function cex_convertir_medida_peso_tienda_kg($peso)
    {
        switch (get_option('woocommerce_weight_unit')) {
            case 'kg':
                $conv=1;
                break;
            case 'g':
                $conv=floatval(0.001);
                break;
            case 'lb':
                $conv=1;
                break;
            case 'oz':
                $conv=floatval(0.001);
                break;
            default:
                $conv=1;
                break;
        }
        $peso=floatval($peso)*$conv;
        return $peso;
    }*/

    public function cex_retornar_peso_orden($id)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_peso_orden');
        }

        $order = wc_get_order($id);
        $pesoTotal= array();
        $pesoRetorno = 0;
        foreach ($order->get_items() as $item_id => $item_data) {
            $product = $item_data->get_product();
            //$product_name = $product->get_name();// Get the product name
            $pesoTotal[] = [
                'nombre'    => $product->get_name(),
                'peso'      => floatval($product->get_weight()),
                'cuantos'   => floatval($item_data->get_quantity()),
            ];
        }

        foreach ($pesoTotal as $elementos) {
            $pesoRetorno += floatval($elementos['peso']) * floatval($elementos['cuantos']);
        }
        
        $pesoRetorno = round($pesoRetorno, 2, PHP_ROUND_HALF_UP);
        //die("pesoRetorno: ".$pesoRetorno);
        $opciones       = $this->cex_get_customer_options();
        if ($opciones['MXPS_ENABLEWEIGHT']!='false' && $opciones['MXPS_ENABLEWEIGHT']!= false) {
            if ($opciones['MXPS_DEFAULTKG'] != 0) {
                return $opciones['MXPS_DEFAULTKG'];
            } else {
                return 1;
            }
        } else {
            if ($pesoRetorno != 0) {
                return json_encode($pesoRetorno);
            } else {
                return 1;
            }
        }
    }

    public function cex_comprobar_contrareembolso($idOrder)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_comprobar_contrareembolso');
        }

        $retorno = null;
        $order          = new WC_Order($idOrder);
        $opciones       = $this->cex_get_customer_options();

        if ($order->get_payment_method() == $opciones['MXPS_DEFAULTPAYBACK']) {
            $contrareembolso = $order->total;
        } else {
            $contrareembolso = null;
        }

        return $contrareembolso;
    }

    public function cex_retornar_bool_pedido($id)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_bool_pedido');
        }

        //inicializamos la orden
        $order = new WC_Order($id);
        foreach ($order->get_items('shipping') as $item_id => $shipping_item_obj) {
            // Get the data in an unprotected array
            $shipping_item_data = $shipping_item_obj->get_data();
            //die(var_export($shipping_item_data));

            $shipping_data_id           = $shipping_item_data['instance_id'];
            $shipping_data_order_id     = $shipping_item_data['order_id'];
            $shipping_data_name         = $shipping_item_data['name'];
            $shipping_data_method_title = $shipping_item_data['method_title'];
            $shipping_data_method_id    = $shipping_item_data['method_id'];
            if (!empty($shipping_data_id)) {
                $aux = $shipping_data_id;
            } else {
                if (strpos($shipping_data_method_id, ':')) {
                    $explode = explode(':', $shipping_data_method_id);
                    $aux = $explode[1];
                } elseif ($shipping_data_method_id == 'advanced_free_shipping') {
                    $aux = "0";
                } else {
                    $metaData = $shipping_item_obj->get_meta_data();
                    //die(var_export($metaData));
                    $objetoAux = $metaData[0];
                    $aux = $objetoAux->value;
                }
            }
        }
        /*$metodo = $order->get_items('shipping');
        $aux = reset($metodo)->get_method_id();*/
        if (strpos($aux, ':') != false) {
            $shiping_method = explode(':', $aux);
            $id_shiping = $shiping_method[1];
        } else {
            $id_shiping = $aux;
        }


        global $wpdb;
        $table = $wpdb->prefix.'cex_savedmodeships';
        $results = $wpdb->get_results($wpdb->prepare(" SELECT id_carrier
            FROM $table", null));

        foreach ($results as $result) {
            if (!empty($result)) {
                //quitamos el ;inicial
                $id_carrier = substr($result->id_carrier, 1);
                $id_carrier = explode(';', $id_carrier);
                if (!empty($id_carrier) || $id_carrier==0) {
                    foreach ($id_carrier as $carrier) {
                        if ((!empty($carrier) || $carrier==0) && strcmp($carrier, $id_shiping) == 0) {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function cex_retornar_metodo_envio($id)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_metodo_envio');
        }

        //inicializamos la orden
        $order = new WC_Order($id);
        $aux='';
        
        foreach ($order->get_items('shipping') as $item_id => $shipping_item_obj) {
            // Get the data in an unprotected array
            $shipping_item_data = $shipping_item_obj->get_data();
            //die(var_dump($shipping_item_data));

            $shipping_data_id           = $shipping_item_data['instance_id'];
            $shipping_data_order_id     = $shipping_item_data['order_id'];
            $shipping_data_name         = $shipping_item_data['name'];
            $shipping_data_method_title = $shipping_item_data['method_title'];
            $shipping_data_method_id    = $shipping_item_data['method_id'];
            if (!empty($shipping_data_id)) {
                $aux = $shipping_data_id;
            } else {
                if (strpos($shipping_data_method_id, ':')) {
                    $explode = explode(':', $shipping_data_method_id);
                    $aux = $explode[1];
                } elseif ($shipping_data_method_id == 'advanced_free_shipping') {
                    $aux = "0";
                } else {
                    $metaData = $shipping_item_obj->get_meta_data();
                    $objetoAux = $metaData[0];
                    $aux = $objetoAux->value;
                }
            }
        }
        //die(var_export($aux));
        /*$a = 'betrs_shipping_47-1';
        $c = strpos($a, ':');
        $b = explode('-', $a);
        $d = explode('_', $b[0]);
        die(var_export($d));*/
        $comprobante = strpos($aux, ':');
        /*$metodo = $order->get_items('shipping');
        $aux = reset($metodo)->get_method_id();*/
        if ($comprobante == false) {
            if (strpos($aux, '-') == 0) {
                $id_shiping = "'%;$aux;%'";
            } else {
                $shipping_method = explode('-', $aux);
                $flat_rate = explode('_', $shipping_method[0]);
                $id_shiping = "'%;$flat_rate[2];%'";
            }
        } else {
            $shiping_method = explode(':', $aux);
            $id_shiping = "'%;$shiping_method[1];%'";
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_savedmodeships';
        $results = $wpdb->get_var($wpdb->prepare("SELECT id_bc
            FROM $table WHERE id_carrier LIKE $id_shiping", null));

        return $results;
    }

    public function cex_retornar_etiqueta()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_etiqueta');
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';
        $results = $wpdb->get_var($wpdb->prepare(" SELECT valor
            FROM $table WHERE clave ='MXPS_DEFAULTPDF'", null));

        return $results;
    }

    //Funcion para sacar los paises
    public function cex_retornar_paises()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_paises');
        }

        global $woocommerce;
        $countries_obj   = new WC_Countries();
        $countries_obj = $countries_obj->get_allowed_countries();
        $retorno ='';
        //$countries   = $countries_obj->__get('countries');
        foreach ($countries_obj as $key => $value) {
            $retorno.=  "<option value='$key'>$value</option>";
        }//
        //return $retorno;
        //$retorno = '';
        //$retorno.=  "<option value='ES'>Espa?a</option>";
        //$retorno.=  "<option value='PT'>Portugal</option>";
        return $retorno;
    }

    public function cex_retornar_select_productosActivos($id)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_form_comercial');
        }

        //el id es de la orden, hay que sacar la instancia del metodo de envio
        global $wpdb;
        $table      = $wpdb->prefix.'cex_savedmodeships';
        $select     = '';
        $contenido  = '';
        $cabecera   = '';
        $results    = $wpdb->get_results($wpdb->prepare(" SELECT *
            FROM $table
            where checked = '1'", null));

        $id = $id.'';
        if (sizeof($results)==0) {
            $select =" <select id='select_modalidad_envio' 
            name='select_modalidad_envio' class='form-control' disabled> 
            <option value=' '>".esc_html(__('No hay productos CEX activos', 'cex_pluggin'))."</option>
            </select>";
        } else {
            $cabecera = "<select id='select_modalidad_envio' name='select_modalidad_envio' class='form-control'>";
            $contenido = '';
            $productosCEX = $results;
            foreach ($productosCEX as $result) {
                if (is_numeric(strpos($result->id_carrier, $id))) {
                    $contenido .= " <option value='$result->id_bc' selected >".esc_html(__($result->name))."</option>";
                } else {
                    $contenido .= " <option value='$result->id_bc' >".esc_html(__($result->name))."</option>";
                }
            }

            $footer = "</select>";
            $select = $cabecera.$contenido.$footer;
        }
        return $select;
    }


    public function cex_recuperarRemitenteDefecto()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_recuperarRemitenteDefecto');
        }

        global $wpdb;
        $table = $wpdb->prefix.'cex_customer_options';
        $column  = 'MXPS_DEFAULTSEND';
        $id_remitente_defecto = $wpdb->get_var($wpdb->prepare(" SELECT valor
            FROM $table 
            WHERE clave = %s ", $column));

        //if ($id_remitente_defecto == '') {
        if (!isset($id_remitente_defecto) || empty($id_remitente_defecto)) {
            return null;
        }

        $column  = 'MXPS_DEFAULTBUL';
        $bultos_defecto= $wpdb->get_var($wpdb->prepare(" SELECT valor
            FROM $table 
            WHERE clave = '".$column."'", null));

        $column  = 'MXPS_DEFAULTKG';
        $kg_defecto = $wpdb->get_var($wpdb->prepare(" SELECT valor
            FROM $table 
            WHERE clave = '".$column."'", null));

        $table = $wpdb->prefix.'cex_savedsenders';
        $result = $wpdb->get_row($wpdb->prepare("SELECT *
            FROM $table
            where id_sender = $id_remitente_defecto", null));

        $retorno = array(
            'name'                          =>$result->name,
            'contact'                       =>$result->contact,
            'address'                       =>$result->address,
            'city'                          =>$result->city,
            'postcode'                      =>$result->postcode,
            'email'                         =>$result->email,
            'phone'                         =>$result->phone,
            'from_hour'                     =>$result->from_hour,
            'from_minute'                   =>$result->from_minute,
            'to_hour'                       =>$result->to_hour,
            'to_minute'                     =>$result->to_minute,
            'id_sender'                     =>$result->id_sender,
            'id_cod_cliente'                =>$result->id_cod_cliente,
            'bultos_defecto'                =>$bultos_defecto,
            'kg_defecto'                    =>$kg_defecto,
            'iso_code'                      =>$result->iso_code_pais
        );

        return $retorno;
    }

    public function cex_recuperarDatosEnvio($id)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_recuperarDatosEnvio');
        }

        $order = new WC_Order($id);
        $oficina = $this->cex_obtenerOficinaOrden($id);
        /*
            $contact='';
            $company=$order->get_shipping_company();
            if ($company =='' || $company==null) {
                $contact = $order->get_shipping_first_name().' '.$order->get_shipping_last_name();
            }else {
                $contact = $company;
            }
            $retorno = array(
                'first_name'        => $order->get_shipping_first_name(),
                'last_name'         => $order->get_shipping_last_name(),
                'company'           => $contact,
                'address'           => $order->get_shipping_address_1(),
                'address2'          => $order->get_shipping_address_2(),
                'city'              => $order->get_shipping_city(),
                'state'             => $order->get_shipping_state(),
                'postcode'          => $order->get_shipping_postcode(),
                'country'           => $order->get_shipping_country(),
                'telf'              => $order->get_billing_phone(),
                'email'             => $order->get_billing_email(),
                'customer_message'  => $order->get_customer_note(),
                'oficina'           => $oficina,
            );
            return $retorno;
        */
            $opciones = $this->cex_get_customer_options();
            $datosEnvio =   ($opciones['MXPS_DEFAULTDELIVER'] == 'FACTURACION') ? $this->cex_datos_facturacion($id) : $this->cex_datos_envio($id);
            $datosEnvio['oficina'] = $oficina;
            $datosEnvio['customer_message'] =  substr($order->get_customer_note(), 0, 70);
            return $datosEnvio;
        }

        public function cex_retornar_precio_pedido()
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_retornar_precio_pedido');
            }

            $id = intval($_POST['id']);
            $order = new WC_Order($id);
            $precio = $order->get_total();
            echo $precio;
            exit;
        }

        public function cex_retornar_savedships_orden_id()
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_retornar_savedships_orden_id');
            }

            global $wpdb;
            $id = intval($_POST['id']);
            $table = $wpdb->prefix.'cex_savedships';
            $tableHist = $wpdb->prefix.'cex_history';

        //si mayor que uno, retornar elemento HTML, con cabecera.
            $results = $wpdb->get_results($wpdb->prepare("SELECT save.*,hist.fecha_recogida,hist.hora_recogida_desde,hist.hora_recogida_hasta
                FROM $table as save
                LEFT JOIN $tableHist as hist
                ON save.numship=hist.numShip
                AND save.numcollect = hist.numCollect
                AND (hist.type = 'Envio' OR hist.type = 'Recogida')
                WHERE save.id_order = %d
                AND deleted_at IS NULL
                ORDER BY save.created_at ", $id));

            $row ='';
            $retorno = '<thead><tr>';
            $retorno .= '<th>'.esc_html(__("Seguimiento", "cex_pluggin")).'</th>';
            $retorno .= '<th>'.esc_html(__("Fecha", "cex_pluggin")).'</th>';
            $retorno .= '<th>'.esc_html(__("Ref.Pedido", "cex_pluggin")).'</th>';
            $retorno .= '<th>'.esc_html(__("Tipo", "cex_pluggin")).'</th>';
            $retorno .= '<th>'.esc_html(__("Identificador", "cex_pluggin")).'</th>';
            $retorno .= '<th>'.esc_html(__("Recogida desde", "cex_pluggin")).'</th>';
            $retorno .= '<th>'.esc_html(__("Fecha de Recogida", "cex_pluggin")).'</th>';
            $retorno .= '<th>'.esc_html(__("Hora Recogida desde", "cex_pluggin")).'</th>';
            $retorno .= '<th>'.esc_html(__("Hora Recogida hasta", "cex_pluggin")).'</th>';
            $retorno .= '<th>'.esc_html(__("Estado", "cex_pluggin")).'</th>';
            $retorno .= '<th>'.esc_html(__("Acciones", "cex_pluggin")).'</th>';
            $retorno .= '</tr></thead>';
        //$retorno .= '<th>Etiquetas</th>';
            $footer = '<tfoot><tr>'
            .'<th>'.esc_html__("Seguimiento", "cex_pluggin").'</th>'
            .'<th>'.esc_html__("Fecha", "cex_pluggin").'</th>'
            .'<th>'.esc_html__("Ref.Pedido", "cex_pluggin").'</th>'
            .'<th>'.esc_html__("Tipo", "cex_pluggin").'</th>'
            .'<th>'.esc_html__("Identificador", "cex_pluggin").'</th>'
            .'<th>'.esc_html__("Recogida desde", "cex_pluggin").'</th>'
            .'<th>'.esc_html__("Fecha de Recogida", "cex_pluggin").'</th>'
            .'<th>'.esc_html__("Hora Recogida desde", "cex_pluggin").'</th>'
            .'<th>'.esc_html__("Hora Recogida hasta", "cex_pluggin").'</th>'
            .'<th>'.esc_html__("Estado", "cex_pluggin").'</th>'
            .'<th>'.esc_html__("Acciones", "cex_pluggin").'</th>'
            .'</tr></tfoot>';
            $retorno.='<tbody>';
            if ($results) {
                foreach ($results as $result) {
                    if($result->fecha_recogida =='0000-00-00'){
                        $result->fecha_recogida=null;
                        $result->hora_recogida_desde=null;
                        $result->hora_recogida_hasta=null;
                    }
                    $row = '<tr>';
                    $row .= '<td><a href="https://s.correosexpress.com/c?n='.$result->numship.'" target="blank">'.esc_html(__("Correos Express", "cex_pluggin")).'</a></td>';
                    $row .= '<td>'.esc_html(__($result->date)).'</td>';
                    $row .= '<td>'.esc_html(__($result->numcollect)).'</td>';
                    $row .= '<td>'.esc_html(__($result->type)).'</td>';
                    $row .= '<td>'.esc_html(__($result->numship)).'</td>';
                    $row .= '<td>'.esc_html(__($result->collectfrom)).'</td>';
                    $row .= '<td>'.esc_html(__($result->fecha_recogida)).'</td>';
                    $row .= '<td>'.esc_html(__($result->hora_recogida_desde)).'</td>';
                    $row .= '<td>'.esc_html(__($result->hora_recogida_hasta)).'</td>';
                    $row .= '<td>'.esc_html(__($result->status)).'</td>';
                    $row .= '<td><a href="" title="'.esc_html(__('Eliminar seguimiento')).'" class="fa fa-trash"
                    onclick="borrarPeticionEnvio('."'".$result->numship."', '".$result->numcollect."'".',event)"></a></td>'; 
                    $row .= '</tr>';

                    $retorno.= $row;
                }   
            }
            $retorno.='</tbody>'.$footer;
            echo json_encode($retorno);
            exit;
        }
    //FORMULARIO DE UTILIDADES
        public function cex_retornar_refencias_dia()
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);
            global $wpdb;

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_retornar_refencias_dia');
            }

        //$fecha == Y-m-d
            $fecha = preg_replace("([^0-9-])", "", $_POST['fecha']);

            $retorno = array();
        // buscar los estados sobre los que desencadenamos acciones.
            $table = $wpdb->prefix.'cex_savedships';
            $results = $wpdb->get_results($wpdb->prepare("SELECT *
                FROM $table
                WHERE Date_format(created_at,'%%Y-%%m-%%d') = '$fecha'
                AND type='Envio' AND deleted_at is null ", null));
        //die(var_export($results));
        //die("SELECT * FROM $table WHERE Date_format(created_at,'%Y-%m-%d') = '$fecha' AND type='Envio' AND deleted_at is null ");

            foreach ($results as $result) {
                $aux = array(
                    'idOrden'                   => $result->id_order,
                    'numCollect'                => $result->numcollect,
                    'numShip'                   => $result->numship,
                    'NombreDestinatario'        => $result->receiver_name,
                    'direccionDestino'          => $result->receiver_address,
                    'fecha'                     => $result->created_at,
                );
                $retorno[] = $aux;
            }

            $retorno = json_encode($retorno);
            echo $retorno;
            exit;
        }

        public function sanitize_array($old_array)
        {
            $new_array=array();
            foreach ($old_array as $key => $val) :
                $new_array[$key]=sanitize_text_field($val);
            endforeach;

            return $new_array;
        }

        public function cex_generar_etiquetas()
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_generar_etiquetas');
            }

        //este puede ser un array
            if (is_array($_POST['numCollect'])) {
                $numCollect     = $this->sanitize_array($_POST['numCollect']);
            } else {
                $numCollect     = sanitize_text_field($_POST['numCollect']);
            }

            $tipoEtiqueta   = intval($_POST['tipoEtiqueta']);
            $posicion       = intval($_POST['posicion']);

            $etiqueta = new EtiquetasVuelo();
        //depurar("INSTANCIAMOS");
            
            $pdf = $etiqueta->cex_generarEtiquetas($numCollect, $tipoEtiqueta, $posicion);

            echo $pdf;
            exit;
        }

        public function cex_generar_resumen()
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_generar_resumen');
            }
            $etiqueta = new EtiquetasVuelo();
        //este puede ser un array
            $date = preg_replace("([^0-9-])", "", $_POST['date']);

            $numCollect = $this->sanitize_array($_POST['numCollect']);

            $pdf = $etiqueta->cex_generarResumenPdf($numCollect, $date);
            $Output = $pdf->Output('lista_envios_'.$date.'.pdf', 'E');
            echo $Output;
            exit;
        }

        public function cex_retornar_transportistas()
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_retornar_transportistas');
            }

            global $wpdb;
            $table=$wpdb->prefix.'cex_savedmodeships';
            $retorno= array();

            $metodos = $wpdb->get_results($wpdb->prepare("SELECT id_carrier from $table where id_carrier NOT LIKE ';'", null));

            $cuantos = $wpdb->get_var($wpdb->prepare("SELECT count(*)
                from $table
                where  checked = '1'
                and id_carrier not like ''
                and id_carrier not like ';'", null));

            if ($cuantos==0) {
                $retorno[] = [
                    'id_bc' => esc_html(__('No configurado', 'cex_pluggin')),
                    'nombre'=> esc_html(__('No hay m&eacute;todos mapeados', 'cex_pluggin'))
                ];
            } else {
                foreach ($metodos as $metodo) {
                    $id_metodo=explode(';', $metodo->id_carrier);

                    for ($i=1; $i < count($id_metodo)-1; $i++) {
                    //$flat_rate='flat_rate:'.$id_metodo[$i];
                    //die($flat_rate);
                        $retorno[] = [
                            'id_bc' => $id_metodo[$i],
                            'nombre' => $this->cex_get_title_shipping_method_from_method_id($id_metodo[$i])
                        ];
                    }
                }
            }
            return $retorno;
        }

        public function cex_get_title_shipping_method_from_method_id($id = '')
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_get_title_shipping_method_from_method_id');
            }

            if ($id == 0) {
                $nombreMetodo = 'Advanced Free Shipping';
                return $nombreMetodo;
            }

            $instancia = new WC_Shipping_Zones();

            $zonas = $instancia->get_zones();

            foreach ($zonas as $zona) {
                $nombreZona =  $zona['zone_name'].'-'.$zona['zone_order'];
                $metodos = $zona['shipping_methods'];
            //die(var_export($metodos));
                foreach ($metodos as $metodo) {
                //die(var_export($metodo));
                    $nombreMetodo= $metodo->get_title();
                //die($nombreMetodo);
                    $rate= $metodo->get_instance_id();
                    if ($rate == $id) {
                        return $nombreMetodo;
                    }
                //die(var_export($rate));
                }
            }
        }

        public function cex_get_init_utilities_form()
        {
            $nonce = sanitize_text_field($_REQUEST['nonce']);

            if (! wp_verify_nonce($nonce, 'cex-nonce')) {
                die('Security cex_get_init_utilities_form');
            }

        /*
        no hay filtrado por transportista
        $retorno = array(
            'selectEstados'         => $this->cex_retornar_estados_productos(),
            'selectTransportistas'  => $this->cex_retornar_transportistas(),
        );
        echo json_encode($retorno);
        */

        $retorno = array(
            'tipoEtiqueta' => $this->cex_get_customer_option('MXPS_DEFAULTPDF'),
        );

        echo json_encode($retorno);

        exit;
    }

    public function cex_sacar_transportistas_oficina()
    {        
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce-user')) {
            die('Security cex_sacar_transportistas_oficina');
        }
        global $wpdb;
        $table1 = $wpdb->prefix.'cex_savedmodeships';
        $table2 = $wpdb->prefix."woocommerce_shipping_zone_methods";
        $retorno= array();
        
        $transportistas = $wpdb->get_var("SELECT id_carrier from $table1 where name = 'Entrega en Oficina' and checked = '1'");
        //die(var_export($transportistas));
        $id_transportista=explode(';', $transportistas);
        for ($i=1; $i < count($id_transportista)-1; $i++) {
            $method_id = $wpdb->get_var("SELECT method_id from $table2 where instance_id = $id_transportista[$i]");
            $flat_rate =$method_id.":".$id_transportista[$i];
            $retorno[] = [
                'id_bc' => $flat_rate,
            ];
        }
        echo json_encode($retorno);
        exit;
    }

    //Funcion para recuperar el campo personalizado Oficina en la orden
    public function cex_obtenerOficinaOrden($idOrden)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_obtenerOficinaOrden');
        }
        
        $order = wc_get_order($idOrden);
        $metadatos = $order->get_meta_data();
        if (isset($metadatos)) {
            foreach ($metadatos as $metadato) {
                if ($metadato->key == '_Oficina') {
                    return $metadato->value;
                }
            }
        }
        return '';
    }

    public function cex_obtener_pedidos_busqueda()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_obtener_pedidos_busqueda');
        }

        
        $opciones = $this->cex_get_customer_options();
        $bultos = $opciones['MXPS_DEFAULTBUL'];
        $fechadesde = new DateTime($_POST['desde'].' 00:00:00');
        $fechahasta = new DateTime($_POST['hasta'].' 23:59:59');
        $desde = $fechadesde->format('Y-m-d H:i:s');
        $hasta = $fechahasta->format('Y-m-d H:i:s');

        $retorno = array();
        $aux = '';
        global $wpdb;
        $table1 = $wpdb->prefix.'posts';
        $table2 = $wpdb->prefix.'cex_savedships';


        //obtenemos las ordenes en un rango
    
        $ordenes = $wpdb->get_results($wpdb->prepare("SELECT  IFNULL(numship,'') as numship, IFNULL(numcollect,'') as numcollect, p.ID
            FROM $table1 p left join (SELECT *    
            FROM $table2
            WHERE type != 'Recogida' and deleted_at is null) s ON p.ID = s.id_order
            WHERE p.post_date >=  '$desde' AND p.post_date <= '$hasta' 
            AND p.post_type = 'shop_order' ", null));

        //obtenemos el method shipping y hacemos segundo filtrado
        foreach ($ordenes as $orden) {
            $id                 = $orden->ID;
            $order              = new WC_Order($id);
            $datos              = $order->get_data();                    
            $shipping_data_id   = '';
            $postStatusArray    =array("trash","draft","auto-draft");
            $search             =array_search($datos['status'], $postStatusArray);

            if($search === false){

                $transportistaOrden     = $this->cex_get_shipping_data_by_order($id);
                $productoSeleccionado   = $this->cex_get_delivery_method_by_carrier( $transportistaOrden["instance_id"]);
                if($transportistaOrden["instance_id"]!=99 ||$transportistaOrden["method_id"] !=99){
                    $metodoEnvio = $transportistaOrden["name"].' - '.$productoSeleccionado->name;
                }else{
                    $metodoEnvio="No enviable";
                }                
                

                if($opciones['MXPS_DEFAULTDELIVER'] =='FACTURACION'){
                    $aux = array(
                        'idOrden'               => $order->get_id(),
                        'estado'                => $this->cex_obtener_texto_status($order->get_status()),
                        'cliente'               => $order->get_billing_first_name().' '.$order->get_billing_last_name(),
                        'fecha'                 => $order->get_date_created(),
                        'bultos'                => $bultos,
                        'codigoOficina'         => $this->cex_obtenerOficinaOrden($id),
                        'selectProductos'       => $this->cex_retornar_select_productosEnvio_orden($id),
                        'numeroEnvio'           => $orden->numship,
                        'numCollect'            => $orden->numcollect,
                        'productoSeleccionado'  => $metodoEnvio
                    );
                    $retorno[] = $aux;
                }
                if($opciones['MXPS_DEFAULTDELIVER'] =='ENVIO'){
                    $aux = array(
                        'idOrden'               => $order->get_id(),
                        'estado'                => $this->cex_obtener_texto_status($order->get_status()),
                        'cliente'               => $order->get_shipping_first_name(),
                        'fecha'                 => $order->get_date_created(),
                        //'precio'              => $order->get_total(),
                        'bultos'                => $bultos,
                        'codigoOficina'         => $this->cex_obtenerOficinaOrden($id),
                        'selectProductos'       => $this->cex_retornar_select_productosEnvio_orden($id),
                        'numeroEnvio'           => $orden->numship,
                        'numCollect'            => $orden->numcollect,
                        'productoSeleccionado'  => $metodoEnvio

                    );
                    $retorno[] = $aux;
                }
            }
        }
        echo json_encode($retorno);
        exit;
    }

    //recibe el estado de WC y devuelve el texto en el idioma
    public function cex_obtener_texto_status($constante)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_obtener_texto_status');
        }
        $estados = wc_get_order_statuses();
        foreach ($estados as $clave => $valor) {
            $estadoAux = 'wc-'.$constante;
            if (strcmp($estadoAux, $clave)==0) {
                return $valor;
            }
        }
        return '';
    }

    public function cex_cambiar_estado_orden($id_order)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_cambiar_estado_orden');
        }
        $order= new WC_Order($id_order);
        $opciones = $this->cex_get_customer_options();
        $order->update_status($opciones['MXPS_RECORDSTATUS']);
    }


    public function cex_form_pedido_borrar()
    { 
        $nonce = sanitize_text_field($_REQUEST['nonce']);
        //die(var_export($nonce));
        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_form_pedido_borrar');
        }
        $resultado = cex_gestionar_borrado_pedido();
        echo json_encode($resultado);
        exit;
    }
    
    public function cex_soft_delete_savedShip()
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);
        //die(var_export($nonce));
        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_soft_delete_savedShip');
        }
        
        global $wpdb;
        $nombreTabla= $wpdb->prefix.'cex_savedships';
        $numship= sanitize_text_field($_POST['numship']);        
        $numcollect= sanitize_text_field($_POST['numcollect']);
        $results = $wpdb->get_results($wpdb->prepare("SELECT numship,codigo_cliente,type 
            FROM $nombreTabla 
            WHERE numship = '$numship'
            AND numcollect = '$numcollect'",null));
        $tipo           = $results[0]->type;      
        $codigo_cliente = $results[0]->codigo_cliente;       
        if(strcmp($tipo,"Recogida")==0){ 
            if(strcmp($numship,"Automatica")==0){
                $savedship = array('deleted_at' => date("Y-m-d H:i:s"));
                $where = array('numship' => $numship , 'numcollect' => $numcollect);
                $wpdb->update($nombreTabla, $savedship, $where);
                $literalRecogidaAuto = esc_html(__('La recogida '.$numship.' ha sido borrado correctamente'));
                $retorno = [
                    'codigoError' => '',
                    'mensaje'     => $literalRecogidaAuto,
                ]; 
                echo json_encode($retorno);
                exit;
            }else{        
                $rest = enviar_peticion_borrado_recogida($numship,$codigo_cliente);        
                $curl = procesar_curl_borrado_recogida($rest);          
                $retorno = procesar_peticion_borrado($curl,$numship,$tipo,$rest,$numcollect);
            }
            
        }
        else{
            $literalEnvio = esc_html(__('La grabacion con numero: '.$numship.' y referencia: '.$numcollect.' ha sido borrado correctamente'));
            $retorno = [
                'codigoError' => '',
                'mensaje'     => $literalEnvio,
            ]; 
        }

        //die($numcollect);
        $savedship = array('deleted_at' => date("Y-m-d H:i:s"));
        $where = array('numship' => $numship , 'numcollect' => $numcollect);
        $wpdb->update($nombreTabla, $savedship, $where);

        $nombreTabla= $wpdb->prefix.'cex_envios_bultos';
        $bultos = array('deleted_at' => date("Y-m-d H:i:s"));
        $where = array('numShip' => $numship , 'numcollect' => $numcollect);
        $wpdb->update($nombreTabla, $bultos, $where);
        echo json_encode($retorno);
        exit;
    }

    public function cex_datos_facturacion($idOrden)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_datos_facturacion');
        }
        $order= new WC_Order($idOrden);
        $company=$order->get_shipping_company();
        $contact = '';
        if ($company =='' || $company==null || $company==' ') {
            $contact = $order->get_billing_first_name().' '.$order->get_billing_last_name();
        } else {
            $contact = $company;
        }

        $order_billing_data = array(
            "name"                => $order->get_billing_first_name().' '.$order->get_billing_last_name(),
            "contact"             => $contact,
            //"company"             => $order->get_billing_company(),
            "address"             => $order->get_billing_address_1().' '.$order->get_billing_address_2(),
            "city"                => $order->get_billing_city(),
            "postcode"            => $order->get_billing_postcode(),
            "country"             => $order->get_billing_country(),
            "email"               => $order->get_billing_email(),
            "phone"               => $order->get_billing_phone()
        );
        return $order_billing_data;
    }

    public function cex_datos_envio($idOrden)
    {
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_datos_envio');
        }
        $order= new WC_Order($idOrden);
        $company=$order->get_shipping_company();
        $contact = '';
        if ($company =='' || $company==null || $company==' ') {
            $contact = $order->get_shipping_first_name().' '.$order->get_shipping_last_name();
        } else {
            $contact = $company;
        }

        $order_shipping_data = array(
            "name"                => $order->get_shipping_first_name().' '.$order->get_shipping_last_name(),
            "contact"             => $contact,
            //"company"             => $order->get_shipping_company(),
            "address"             => $order->get_shipping_address_1().' '.$order->get_shipping_address_2(),
            "city"                => $order->get_shipping_city(),
            "postcode"            => $order->get_shipping_postcode(),
            "country"             => $order->get_shipping_country(),
            "email"               => $order->get_billing_email(),
            "phone"               => $order->get_billing_phone()
        );
        return $order_shipping_data;
    }

    public function cex_retornar_destinatario(){
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_retornar_destinatario');
        }
        $retorno = '';
        $tipo = intval($_REQUEST['tipo']);
        $idOrden = intval($_REQUEST['id']);
        if ($tipo == 'FACTURACION') {
            $retorno = $this->cex_datos_facturacion($idOrden);
        } else {
            $retorno = $this->cex_datos_envio($idOrden);
        }
        
        echo json_encode($retorno);
        exit;
    }

    public function cex_validar_credenciales(){
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_validar_credenciales');
        }
        $mensaje = validar_credenciales_rest();
        echo $mensaje;
        exit;
    }


    public function cex_get_shipping_data_by_order($id_orden){
        global $wpdb;
        $nonce = sanitize_text_field($_REQUEST['nonce']);

        if (! wp_verify_nonce($nonce, 'cex-nonce')) {
            die('Security cex_get_shipping_data_by_order');
        }
       
        $nombreTabla= $wpdb->prefix.'woocommerce_order_itemmeta';
        $nombreTabla2= $wpdb->prefix.'woocommerce_order_items';        
        $shipping_item_data = $wpdb->get_results($wpdb->prepare("SELECT *
            from $nombreTabla meta 
            LEFT JOIN $nombreTabla2 items
            ON meta.order_item_id = items.order_item_id
            WHERE items.order_id = %d
            AND items.order_item_type='shipping'", $id_orden));


        $respuesta = array();

        $respuesta['name']=$shipping_item_data[0]->order_item_name;
        $respuesta['method_title']=$shipping_item_data[0]->order_item_name;
        foreach ($shipping_item_data as $key) {

            if(strcmp("instance_id", $key->meta_key)==0){
                $respuesta['instance_id']=$key->meta_value;
            }

            if(strcmp("method_id", $key->meta_key)==0){
                $respuesta['method_id']=$key->meta_value;
            }
        }

        if (strcmp("", $respuesta['method_id'])==0){
            $respuesta['method_id']=99;
        }
        if (strcmp("", $respuesta['instance_id'])==0){
            $respuesta['instance_id']=99;
        }
        $respuesta['order_id']=$id_orden;       


    return $respuesta;
}

public function cex_get_delivery_method_by_carrier($id_carrier){

    global $wpdb;
    $nombreTabla= $wpdb->prefix.'cex_savedmodeships';
    $results = $wpdb->get_row($wpdb->prepare(" SELECT name,id_carrier
        FROM $nombreTabla 
        WHERE id_carrier LIKE '%;$id_carrier;%'",null));

    return $results;
}


}
new CorreosExpress();
