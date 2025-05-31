<?php

/*
* Plugin Name: ERP - FRONTONIO
* Plugin URI: http://www.agenciasp.com
* Description: Sistema de gestión.
* Version: 1.0.0
* Author: Agencia SP
* Author URI: http://www.agenciasp.com
* License:
*/

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! defined( 'ADPNSY_VER' ) ) exit;
if ( ! defined( 'ADPNSY_PATH' ) ) exit;
if ( ! defined( 'ADPNSY_URL' ) ) exit;

define('ERP_FRONTONIO_VER', '1.0.0');
define('ERP_FRONTONIO_PATH', realpath( dirname(__FILE__) ) );
define('ERP_FRONTONIO_URL', plugins_url('/', __FILE__) );

define('ADPNSY_CRUD', realpath( dirname(__FILE__) ) . "/assets/crud/" );

define('ADPNSY_FORMS', ERP_FRONTONIO_PATH . "/assets/forms/" );
define('ADPNSY_FORMS_URL', ERP_FRONTONIO_URL . "/assets/forms/" );

define('ADPNSY_TWILLO_ID', '');
define('ADPNSY_TWILLO_TOKEN', '' );
define('ADPNSY_TWILLO_SERVICIOS', '' );
define('ADPNSY_TWILLO_WHATSAPP', '' );

if ( class_exists('admin_panel_system') ){
	class adpnsy extends admin_panel_system {
		public function __construct(){
			add_action( 'admin_menu', array($this, 'menu_register'));
			add_action( 'wp_ajax_admin_panel', array($this, 'ajax'));
			add_action( 'wp_ajax_nopriv_admin_panel', array($this, 'ajax'));

			///perfil	
			register_nav_menu( 'perfil_admin' , 	__( 'Perfil Administrador' , 'adpnsy' ) );
			register_nav_menu( 'perfil_socio' , 	__( 'Perfil Socio' , 'adpnsy' ) );

			///principal
			register_nav_menu( 'administracion_admin' , 	__( 'Adminsitracion Admin' , 'adpnsy' ) );
			register_nav_menu( 'administracion_socio' , 	__( 'Adminsitracion Socio' , 'adpnsy' ) );


			///registro
			require_once "inc/bd_register.php";
			add_filter( 'template_adsp', array($this, 'template_filter'), 10, 1 );
			add_filter( 'template_mail_adsp', array($this, 'template_filter'), 10, 1 );
			add_action( 'footer_adsp' , array($this, 'footer_action'), 10, 1 );
			add_action( 'header_adsp' , array($this, 'header_action'), 20, 1 );
			add_action( 'menu_adps' , array($this, 'menu_load'), 10, 1 );
			add_action( 'menu_perfil_adps' , array($this, 'menu_perfil_load'), 10, 1 );
			
			///limitadores
			add_action('admin_init', array($this, 'acceso_wp'), 10, 1 );

			add_filter( 'authenticate', array( $this, 'validar_estado_cuentas' ), 30, 3 );

			add_action( 'sp_mail', [$this, 'mail'], 10, 4);
			add_action( 'sp_whatsapp', [$this, 'whatsapp'], 10, 2);

			add_action( 'init', [$this, 'session_start'] );

			add_action( 'sp_enviar_notificacion', [$this, 'enviar_notificacion'], 10, 3);
			add_action( 'woocommerce_order_status_changed', [ $this, 'on_order_status_changed' ], 10, 4 );





			/* SHORTCODES FICHA PRODUCTO */
			add_shortcode('video_desenfocado_socios', [ $this, 'shortcode_video_desenfocado_socios' ]);
			add_shortcode('pdf_desenfocado_socios', [ $this, 'shortcode_pdf_desenfocado_socios' ]);
			



			///iniciar
			self::init();
		}

		function session_start(){
			if ( ! session_id() ) {
				session_start();
			}
		}






		

		function validar_estado_cuentas( $user, $username, $password ) {
			require_once "inc/bd_list.php";

			if ( is_a( $user, 'WP_User' ) ) {
				if ( in_array( 'socio', (array) $user->roles )  ) {
					$email = $user->user_email;
					$estado = $wpdb->get_var("SELECT estado FROM $tabla_socios WHERE email = '$email'");
					
					if ( $estado === '0' || $estado === 0 ) {
						return new WP_Error( 'cuenta_desactivada', __( 'Cuenta desactivada', 'adpnsy' ) );
					}
				}
			}

			return $user;
		}

		public function menu_perfil_load(){
			if($this->validar_rol("administrator")) 
				$this->menu("perfil_admin", "dropdown-content", "profile-dropdown");
			if($this->validar_rol("socio")) 
				$this->menu("perfil_socio", "dropdown-content", "profile-dropdown");
		}

		public function menu_load(){
			if($this->validar_rol("administrator")) 
				$this->menu("administracion_admin", "sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow", "slide-out");
			if($this->validar_rol("socio")) 
				$this->menu("administracion_socio", "sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow", "slide-out");
		}

		public function acceso_wp() {
		    if (is_admin() && !current_user_can('administrator') && !wp_doing_ajax()) {
		        wp_redirect(home_url());
		        exit;
		    }
		}

		public function template_filter($path){
			$newpath = str_replace(ADPNSY_PATH, ERP_FRONTONIO_PATH, $path);

			if(file_exists($newpath)){
				return $newpath;
			}else{
				return $path;
			}
		}

		public function footer_action(){ 

			// GLOBALES
			?>

			<script src="<?=ERP_FRONTONIO_URL;?>assets/js/global.js?<?=filemtime(ERP_FRONTONIO_PATH . "/assets/js/global.js");?>"></script>
			<script src="<?=ERP_FRONTONIO_URL;?>assets/js/sp_popup.js?<?=filemtime(ERP_FRONTONIO_PATH . "/assets/js/sp_popup.js");?>"></script>

			<?php
			if($this->validar_rol("administrator")){ ?>
				<script src="<?=ERP_FRONTONIO_URL;?>assets/js/erp-admin.js?<?=filemtime(ERP_FRONTONIO_PATH . "/assets/js/erp-admin.js");?>"></script>
			<?php }

			if($this->validar_rol("socio")){ ?>
				<script src="<?=ERP_FRONTONIO_URL;?>assets/js/erp-socio.js?<?=filemtime(ERP_FRONTONIO_PATH . "/assets/js/erp-socio.js");?>"></script>
			<?php }

		}

		public function header_action(){ ?>
			<link rel="stylesheet" type="text/css" href="<?=ERP_FRONTONIO_URL;?>assets/css/base.css?<?=filemtime(ERP_FRONTONIO_PATH . '/assets/css/base.css');?>">
			<link rel="stylesheet" type="text/css" href="<?=ERP_FRONTONIO_URL;?>assets/css/style.css?<?=filemtime(ERP_FRONTONIO_PATH . '/assets/css/style.css');?>">
			<link rel="stylesheet" type="text/css" href="<?=ERP_FRONTONIO_URL;?>assets/css/erp.css?<?=filemtime(ERP_FRONTONIO_PATH . '/assets/css/erp.css');?>">
			<link rel="stylesheet" type="text/css" href="<?=ERP_FRONTONIO_URL;?>assets/css/sp_popup.css?<?=filemtime(ERP_FRONTONIO_PATH . '/assets/css/sp_popup.css');?>">
		<?php }


















		public function load_files($wpdb, $files, $file, $name, $realpath, $path, $id, $column, $bd){
			if(isset($_FILES[$file])){
				if($_FILES[$file]['error'] !== 0)
					return "Los cambios se realizaron, pero el archivo enviado contiene errores.";

				if(!$files->NuevoArchivo("{$realpath}/{$path}", $name, $_FILES[$file]['tmp_name']))
					return "Los cambios se realizaron, pero el archivo enviado no pudo ser almacenado.";

				if(!$wpdb->update($bd, [$column => "{$path}{$name}"], ["id" => $id]))
					return "Los cambios se realizaron, pero el archivo no pudo ser vinculado. Contacte con el administrador.";

				return true;
			}
			return false;
		}

		public function sanitize_file_name($file_name) {
		    $file_parts = pathinfo($file_name);
		    $name = $file_parts['filename'];
		    $extension = isset($file_parts['extension']) ? '.' . $file_parts['extension'] : '';

		    $sanitized_name = sanitize_title($name);

		    $sanitized_file_name = $sanitized_name . $extension;

		    return $sanitized_file_name;
		}

		public function menu_register() {
			add_submenu_page('options-general.php', 'SP Admin', 'SP Admin', 'manage_options', 'panel_admin_system', array($this, 'admin'), 20); 
		}

		public function ajax(){
			include 'inc/ajax.php';
		}

		public function install(){

			//Pages
			$login_ar = array("post_title" => "Login", "post_status" => "publish", "post_type" => "page");
			$login = wp_insert_post($login_ar);
			update_post_meta( $login, '_wp_page_template', 'admin_login.php' );

			$dashboard_ar = array("post_title" => "Dashboard", "post_status" => "publish", "post_type" => "page");
			$dashboard = wp_insert_post($dashboard_ar);
			update_post_meta( $dashboard, '_wp_page_template', 'admin_dashboard.php' );

			$recovery_ar = array("post_title" => "Recuperar Contraseña", "post_status" => "publish", "post_type" => "page");
			$recovery = wp_insert_post($recovery_ar);
			update_post_meta( $recovery, '_wp_page_template', 'admin_recovery.php' );

			$register_ar = array("post_title" => "Registro", "post_status" => "publish", "post_type" => "page");
			$register = wp_insert_post($register_ar);
			update_post_meta( $register, '_wp_page_template', 'admin_register.php' );

			$logout_ar = array("post_title" => "Salir", "post_status" => "publish", "post_type" => "page");
			$logout = wp_insert_post($logout_ar);
			update_post_meta( $logout, '_wp_page_template', 'admin_logout.php' );

			$perfil_ar = array("post_title" => "Perfil", "post_status" => "publish", "post_type" => "page");
			$perfil = wp_insert_post($perfil_ar);
			update_post_meta( $perfil, '_wp_page_template', 'admin_perfil.php' );

			$opciones = json_decode('{
				"titulo":"SP Admin",
				"description":"Instación basica de SP Admin",
				"autor":"Santiago Ponce",
				"logo":"'.ADPNSY_URL.'\/app-assets\/img\/logo_c.png",
				"logo_vn":"0",
				"logo_blanco":"'.ADPNSY_URL.'\/app-assets\/img\/logo_b.png",
				"logo_blanco_vn":"0",
				"logo_texto":"Santiago Ponce",
				"icon_apple":"'.ADPNSY_URL.'\/app-assets\/img\/logo.png",
				"icon_apple_vn":"0",
				"favicon":"'.ADPNSY_URL.'\/app-assets\/img\/logo_c.png",
				"favicon_vn":"0",
				"bglogin_vn":"'.ADPNSY_URL.'\/app-assets\/img\/login.jpg",
				"bglogin":"0",
				"lglogin_vn":"'.ADPNSY_URL.'\/app-assets\/img\/logo.png",
				"lglogin":"0",
				"login":"'.$dashboard.'",
				"Register":"'.$register.'",
				"Recovery":"'.$recovery.'",
				"Politicas":"",
				"botton_fondo":"red",
				"botton_tono":"darken-1",
				"botton_color":"white-text",
				"botton_color_t":"",
				"demo":"1"
			}');

			$menu_Admin_id = wp_create_nav_menu("SP Admin menu");
			$menu_User_id = wp_create_nav_menu("SP Admin menu user");
			wp_update_nav_menu_item($menu_Admin_id, 0, array(
		        'menu-item-title' => 'Dashboard',
			    'menu-item-object-id' => $dashboard,
			    'menu-item-object' => 'page',
			    'menu-item-status' => 'publish',
			    'menu-item-type' => 'post_type',
			));
			wp_update_nav_menu_item($menu_User_id, 0, array(
		        'menu-item-title' => 'Perfil',
			    'menu-item-object-id' => $perfil,
			    'menu-item-object' => 'page',
			    'menu-item-status' => 'publish',
			    'menu-item-type' => 'post_type',
			));
			wp_update_nav_menu_item($menu_User_id, 0, array(
		        'menu-item-title' => 'Salir',
			    'menu-item-object-id' => $logout,
			    'menu-item-object' => 'page',
			    'menu-item-status' => 'publish',
			    'menu-item-type' => 'post_type',
			));

			$locations['admin_system'] = $menu_Admin_id;
			$locations['perfil_admin_system'] = $menu_User_id;        	
        	set_theme_mod( 'nav_menu_locations', $locations );

			return $opciones;
		}

		public function status($type){
			return [
				0 => "<span title='Desactivar' class='chip green lighten-4 green-text'>Activo</span>",
				1 => "<span title='Activar' class='chip red lighten-4 red-text'>Desactivado</span>"
			];
		}

		public function get_json($consulta) {
		    $response = wp_remote_get(ERP_MIGRATE_URL . "&_op={$consulta}");

		    if (is_wp_error($response)) {
		        return 'Error a la solicitud: ' . $response->get_error_message();
		    }

		    $body = wp_remote_retrieve_body($response);

		    $data = json_decode($body);

		    if (json_last_error() !== JSON_ERROR_NONE) {
		        return 'Error en descodificar JSON: ' . json_last_error_msg();
		    }

		    return $data;
		}

		public function set_json($consulta, $elemento) {
			require_once ERP_FRONTONIO_PATH . "/inc/bd_list.php";
			$send = ["_elm" => $elemento];
			$body = http_build_query($send);
			$args = array(
		        'body' => $body,
		        'timeout' => 45,
		        'redirection' => 5,
		        'httpversion' => '1.0',
		        'blocking' => true,
		        'headers' => array(),
		        'cookies' => array(),
		    );
		    $response = wp_remote_post(ERP_MIGRATE_URL . "&_set={$consulta}", $args);

		    if (is_wp_error($response)) {
		        return 'Error a la sol·licitud: ' . $response->get_error_message();
		    }

		    $body = wp_remote_retrieve_body($response);

		    $data = json_decode($body);

		    if (json_last_error() !== JSON_ERROR_NONE) {
		        return 'Error en descodificar JSON en set_json: ' . json_last_error_msg() . "\nBODY: {$body}";
		    }

		    return $data;
		}

		public function del_json($consulta, $elemento) {
		    $response = wp_remote_get(ERP_MIGRATE_URL . "&_del={$consulta}&_elm={$elemento}");

		    if (is_wp_error($response)) {
		        return 'Error a la sol·licitud: ' . $response->get_error_message();
		    }

		    $body = wp_remote_retrieve_body($response);

		    $data = json_decode($body);

		    if (json_last_error() !== JSON_ERROR_NONE) {
		        return 'Error en descodificar JSON: ' . json_last_error_msg();
		    }

		    return $data;
		}

		public function set_img($tipo, $data) {
			require_once ERP_FRONTONIO_PATH . "/inc/bd_list.php";
			$send = ["_elm" => $data];
			$body = http_build_query($send);
			$args = array(
		        'body' => $body,
		        'timeout' => 45,
		        'redirection' => 5,
		        'httpversion' => '1.0',
		        'blocking' => true,
		        'headers' => array(),
		        'cookies' => array(),
		    );
		    $response = wp_remote_post(ERP_MIGRATE_URL . "&_img={$tipo}", $args);

		    if (is_wp_error($response)) {
		        return 'Error a la solicitud: ' . $response->get_error_message();
		    }

		    $body = wp_remote_retrieve_body($response);

		    $data = json_decode($body);

		    if (json_last_error() !== JSON_ERROR_NONE) {
		        return 'Error en descodificar JSON: ' . json_last_error_msg();
		    }

		    return $data;
		}




		public function whatsapp($numeroDestino, $mensaje) {
			require_once ERP_FRONTONIO_PATH . "/twilio_whatsapp.php";
		
			$whatsapp = new twilio_whatsapp();
			$resultado = $whatsapp->enviarMensajeWhatsApp($numeroDestino, $mensaje);
		
			// Intentamos decodificar la respuesta JSON
			$resultado_json = json_decode($resultado, true);
		
			// Si no se pudo decodificar el JSON, se devuelve el error original
			if ($resultado_json === null) {
				return [
					"r" => 0,
					"m" => "Error con Whatsapp: " . $resultado
				];
			}

			if (isset($resultado_json["status"])) {
				if ($resultado_json["status"] == "queued" || $resultado_json["status"] == 200) {
					return [
						"r" => 1
					];
				} else {
					return [
						"r" => 0,
						"m" => "Error con Whatsapp: " . $resultado_json["message"]
					];
				}
			}
			else {
				error_log(print_r($resultado_json, true));
				return [
					"r" => 0,
					"m" => "Error con Whatsapp: consulte con el administrador."
				];
			}
		}

        function obtener_factura($order){
			if( !$order ){
				$this->send_json_error( "Pedido no válido.", 500);
			}
			
			if( !function_exists("wpo_wcpdf") ){
				$this->send_json_error( "El gestor de facturas no está instalado. Por favor, contacte con administración.", 500);
			}

			$url = add_query_arg( array(
				'action'        => 'generate_wpo_wcpdf',
				'document_type' => 'invoice',
				'order_ids'     => $order->get_id(),
				'order_key'     => $order->get_order_key(),
			), admin_url( 'admin-ajax.php' ) );

			if(!$url){
				$this->send_json_error( "No se pudo encontrar la URL de la factura. Por favor, contacte con administración.", 500);
			}

			return $url;
		}

		function subir_imagen($carpeta, $imagen, $db, $elemento, $nombre_campo, $key){
			include  ERP_FRONTONIO_PATH . "/inc/bd_list.php";
			require_once ADPNSY_PATH . "/admin_files.php";
            $files = new admin_panel_files();
            $carpeta = ERP_FRONTONIO_PATH . "/img_files/{$carpeta}/{$elemento['id']}/";
            $extension = strtolower(pathinfo($imagen['name'], PATHINFO_EXTENSION));
            $nuevo_nombre = md5(time()) . "." . $extension;
            $subido = $files->NuevoArchivo($carpeta, $nuevo_nombre, $imagen['tmp_name']);

            if(!$subido) return false;

            #eliminar imagen anterior
            $anterior = $wpdb->get_var("SELECT {$nombre_campo} FROM {$db} WHERE id = {$elemento['id']}");
            if($anterior) $files->eliminarElemento($carpeta, basename($anterior));

            #actualizar url de imagen en elemento
            $url = $this->sp_path_to_content_url($carpeta . $nuevo_nombre);

            if( $url ) {
                $elemento[$nombre_campo] = $url;
                $wpdb->update($db, [$nombre_campo => $url], [$key => $elemento['id']]);
				
                return true;
            }
            else{
                return false;
            }
		}

		function sp_path_to_content_url( $file_path ) {
			$file_path    = wp_normalize_path( $file_path );
			$content_dir  = wp_normalize_path( WP_CONTENT_DIR );
			$content_url  = trailingslashit( content_url() );
		
			if ( strpos( $file_path, $content_dir ) !== 0 ) {
				return false;
			}
		
			return $content_url . ltrim( substr( $file_path, strlen( $content_dir ) ), '/' );
		}

		function validar_fecha_crud( $fecha ) {
			$fecha = trim( $fecha );

			$dt = DateTime::createFromFormat( 'd/m/Y', $fecha );
			if ( ! $dt || $dt->format( 'd/m/Y' ) !== $fecha ) {
				return false;
			}
			return $dt->format( 'Y-m-d' );
		}
	
		function limpiar_url_youtube($url){
			if (strpos($url, '/embed/') === false) {
				if (preg_match('/youtu(?:\.be|be\.com)\/(?:watch\?v=|embed\/|)([a-zA-Z0-9_-]+)/', $url, $matches)) {
					$video_id = $matches[1];
					$url = "https://www.youtube.com/embed/" . $video_id; 
				}
			}
			return $url;
		}





		function shortcode_video_desenfocado_socios(){
			if ( !is_product() ) {
				return '';
			}

			global $product;
			$url = $product->get_meta( 'video_youtube' ); 

			if ( !$url ) {
				return '';
			}

			if ( $this->validar_rol("socio") || $this->validar_rol("administrator") ) {
				if (strpos($url, '/embed/') === false) {
					if (preg_match('/youtu(?:\.be|be\.com)\/(?:watch\?v=|embed\/|)([a-zA-Z0-9_-]+)/', $url, $matches)) {
						$video_id = $matches[1]; 
						$url = "https://www.youtube.com/embed/" . $video_id; 
					}
				}

				ob_start();
				?>
				<div class="video-socios">
					<div class="video-container">
						<div class="video-wrapper">
							<iframe src="<?= esc_attr($url); ?>" frameborder="0" allowfullscreen></iframe>
						</div>
					</div>
				</div>
				<?php
				return ob_get_clean();
			}
			else{

				ob_start();
				?>
				<div class="video-desenfocado-socios">
					<div class="video-container">
						<img src="placeholder" alt="Necesitas ser socio">
					</div>
				</div>
				<?php
				return ob_get_clean();

			}
		}

		function shortcode_pdf_desenfocado_socios(){
			if ( !is_product() ) {
				return '';
			}

			global $product;
			$url = $product->get_meta( 'ficha_pdf' ); 

			if ( !$url ) {
				return '';
			}

			if ( $this->validar_rol("socio") || $this->validar_rol("administrator") ) {
				ob_start();
				?>
				<div class="boton_descargar_pdf">
					<a role="button" href="<?=esc_url($url)?>">Descargar ficha</a>
				</div>
				<?php
				return ob_get_clean();
			}
			else{

				ob_start();
				?>
				<div class="boton_descargar_pdf">
					<img src="placeholder" alt="Necesitas ser socio">
				</div>
				<?php
				return ob_get_clean();

			}
		}




	}

	$GLOBAL['adpnsy'] = new adpnsy();
}




