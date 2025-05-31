<?php

/*
* Plugin Name: SP - Elementor Widgets
* Plugin URI: https://www.agenciasp.com
* Description: Añade Widgets programados a medida para Elementor. 
* Version: 1.0.0
* Author: Agencia Digital SP
* Author URI: https://www.agenciasp.com
* License: AgenciaSP
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define('SPEW_VER', '1.0.0');
define('SPEW_PATH', realpath( dirname(__FILE__) )."/" );
define('SPEW_URL', plugins_url('/', __FILE__) );


if ( !class_exists('base_class')){
	class base_class {

		private static $info_data = [];
		private static $info_status = null;

		public function __construct(){
			self::$info_status = self::info(true);
			if(self::$info_status){
				self::$info_data = self::info();
				self::load_data();
				self::make_data();
			}
			add_action( 'admin_menu', array($this, 'menu_register'));




			add_action( 'plugins_loaded', [ $this, 'sp_comprobar_elementor' ] );

			add_action( 'elementor/elements/categories_registered', [ $this, 'sp_registrar_categorias' ] );
			add_action( 'elementor/widgets/register', [ $this, 'sp_registrar_widgets' ] );
	
		}

		function sp_comprobar_elementor() {
			if ( ! did_action( 'elementor/loaded' ) ) {
				add_action( 'admin_notices', array($this, 'sp_elementor_inactivo') );
				return;
			}
		}

		function sp_elementor_inactivo() {
			?>
			<div class="notice notice-warning is-dismissible">
				<p>Elementor debe estar activo para que "SP Elementor Widgets" funcione</p>
			</div>
			<?php
		}

		public function sp_registrar_plantillas( $templates, $args ) {
			$custom_templates = include( SPEW_PATH . 'core/templates.php' );
			return array_merge( $templates, $custom_templates );
		}
	

		public function sp_registrar_categorias( $elements_manager ) {
			include_once( SPEW_PATH . 'core/categories.php' );
		}

		public function sp_registrar_widgets( $widgets_manager ) {
			include_once( SPEW_PATH . 'core/widgets.php' );
		}




















		public function menu_register() {
			add_submenu_page('options-general.php', 'SPEW-ACTIVAR', 'SPEW-ACTIVAR', 'manage_options', 'panel_admin_master', array($this, 'admin'), 20); 
		}

		public function admin(){
			if($_SERVER['REQUEST_METHOD'] === 'POST') self::activar();
			if(self::$info_status === true) self::Noticia("notice-success", "Licencia Activada", "");
			self::admin_activar_panel();
		}

		public function info($key = false){
			$keyuse = get_option('kwkrs_lic');
			if($keyuse){
				$keyuse = substr($keyuse, 3);
				$keyuse = base64_decode($keyuse);
				$keyuse = json_decode($keyuse);
				$site = get_option('siteurl');
				if(crypt($site, 'AN') == $keyuse->key){
					if(!isset($keyuse->key_last) || $keyuse->key_last < time() ){
						$nw = $ac = time() + 604800;
						$vl = self::comprobar($keyuse->key_id);
						if($vl)
							self::save($keyuse->key, $keyuse->key_id, $nw, $keyuse->opt, true);
						else
							self::save("", $keyuse->key_id, $nw, $keyuse->opt, true);
					}
					if($key){
						return true;
					}else{
						return $keyuse->opt;
					}
				}else{
					if(!$key) wp_die( __("No esta autorizado para ingresar a este enlace", "kwkrs") );
					return false;
				}
			}else{
				if($key) return false;
			}
		}

		public function Noticia($class, $mensaje, $dis = 'is-dismissible'){ ?>
			<div class="notice <?=$class;?> <?=$dis;?>">
		        <p><?=$mensaje;?></p>
		    </div>
		<?php }

		public function make_data(){
			require_once SPEW_PATH . '/inc/bd.php';
		}

		public function load_data(){
			$data = file_get_contents(SPEW_PATH . '/app-assets/json/data.json');
			if($data){
				$cont = json_decode($data);
				foreach ($cont as $element) {
					if($element->typ == 1) $this->load_act($element);
					if($element->typ == 2) $this->load_sht($element);
					if($element->typ == 3) $this->load_fil($element);
				}
			}
		}

		public function ajax(){
			include_once SPEW_PATH . "/inc/ajax.php";
		}

		public function mail($correo, $asunto, $template, $_data){
	    	$_data = (object) $_data;
	    	$_base = file_get_contents(SPEW_PATH . "/templates_email/base.html");
	    	include(SPEW_PATH . "/templates_email/" . $template . ".php");
	    	$_head[] = 'Content-Type: text/html; charset=UTF-8';
	    	if(isset($_config) && is_array($_config) && isset($_config['Correo']) && isset($_config['Nombre'])){
	    		$_head[] = "From: ".$_config['Nombre']." <".$_config['Correo'].">";
	    	}
	    	if(isset($remplace) && is_array($remplace)){
	    		foreach ($remplace as $key => $value) {
		    		$_base = str_replace($key, $value, $_base);
		    	}
	    	}
	    	//file_put_contents(SPEW_PATH . "/{$template}.html", $_base);
	    	return wp_mail( $correo, $asunto, $_base, $_head );
	    }

	    /* Esto es un ejemplo de uso para integrar JS / CSS de manera oculta */
	    public function test_use(){
        	$data = file_get_contents(SPEW_PATH . '/app-assets/json/test.json');
			if($data){
				$cont = json_decode($data);
				foreach ($cont as $element) {
					if($element->typ == 1) $this->load_sct($element);
					if($element->typ == 2) $this->load_css($element);
				}
			}
	    }

		private function admin_activar_panel(){ ?>
    		<style type="text/css">
    			.head-config div {
				    margin: auto;
				    width: 100%;
				    padding: 10px;
				}

				.head-config h1 {
				    font-size: 35px;
				    font-weight: 800;
				    vertical-align: middle;
				    margin: 0;
				}

				.head-config p {
				    margin: 5px 0;
				}

				.head-config span {
				    background-color: #222222;
				    display: inline-flex;
				    padding: 20px;
				    border-radius: 5px;
				}

				.head-config {
				    max-width: 730px;
				    padding: 20px;
				    margin-left: 0;
				    position: relative;
				    display: flex;
				}

				.head-config img {
				    display: inline;
				}
    		</style>
    		<div class="head-config">
    			<span>
    				<img src="<?=SPEW_URL;?>app-assets/img/logo.jpg">
    			</span>
    			<div>
    				<h1>Elementos para Elementor</h1>
    				<p>Diseñado y desarrollado por <a target="_blank" href="https://agenciasp.com/">Agencia Digital SP</a></p>
    			</div>
    		</div>
			<div class="wrap">
				<h1>Activación de plugin</h1>
				<form method="post">
					<table class="form-table">
						<tbody>
							<tr>
								<th scope="row">
									<label for="licencia">Licencia</label>
								</th>
								<td>
									<input autocomplete="off" required name="licencia" type="password" id="licencia" value="" class="regular-text">
								</td>
							</tr>
						</tbody>
					</table>
					<input class="button button-primary" type="submit" name="activar" value="Activar">
				</form>
			</div>
		<?php }

		private function save($key, $key_id, $key_last, $opt, $nm = false){
			$bse = new stdClass();
			$bse->key = $key;
			$bse->key_id = $key_id;
			$bse->key_last = $key_last;
			$bse->opt = $opt;
			$bse = json_encode($bse);
			$bse = base64_encode($bse);
			$bse = 'and' . $bse;
			if(update_option('kwkrs_lic', $bse)){
				if($key != "" && !$nm) self::Noticia("notice-success", __("Datos guardados satisfactoriamente.", "kwkrs"));
				return true;
			}else{
				if($key != "" && !$nm) self::Noticia("notice-error", __("Error al procesar los datos.", "kwkrs"));
				return false;
			}
		}

		private function activar(){
	    	$site = get_option('siteurl');
	    	$key = crypt($site, 'AN');
			if(isset($_POST['activar'])){
				if(!self::comprobar(sanitize_text_field($_POST['licencia']), false, 'slm_activate')){
					self::Noticia("notice-error", "Licencia Invalida");
				}else{
					$licence = get_option('kwkrs_lic');
					if($licence){
						$licence = substr($licence, 3);
						$licence = base64_decode($licence);
						$licence = json_decode($licence);
						$opt = $licence->opt;
					}else{
						$opt = array();
					}

					self::Noticia("notice-success", "Licencia Registrada");
					$bse = new stdClass();
					$bse->key = $key;
					$bse->key_id = sanitize_text_field($_POST['licencia']);
					$bse->key_last = time() + 604800;
					$bse->opt = $opt;
					$bse = json_encode($bse);
					$bse = base64_encode($bse);
					$bse = 'and' . $bse;
					update_option('kwkrs_lic', $bse);
				}
			}else{
				$key_id = self::info(true);
				self::save($key, $key_id, time() + 86400, $_POST );
			}
	    }

	    private function comprobar($lic = "", $df = true, $fc = 'slm_check'){
	    	$api_params = array(
				'slm_action' 	=> $fc,
				'secret_key' 	=> '5e84c405466b11.57304054',
				'license_key' 	=> $lic,
				'registered_domain' => $_SERVER['SERVER_NAME']
			);
			$response = wp_remote_get(add_query_arg($api_params, 'http://agenciasp.com/'), array('timeout' => 20, 'sslverify' => false));
			if(is_wp_error( $response )) return $df;
			try {
			    $data = json_decode( $response['body'] );
			} catch ( Exception $ex ) {
			    $data = null;
			}
			if(is_null($data)) return $df;
			if($data->result != "success") return false;
			if($fc == 'slm_check' && $data->status != "active") return false;
			return true;
	    }

	    private function load_act($element){
	    	$keyuse = substr($element->fun, 3);
			$keyuse = base64_decode($keyuse);

			$keyloc = substr($element->loc, 3);
			$keyloc = base64_decode($keyloc);

			if(md5($keyuse) === $element->chk){
				add_action($keyloc, [$this, $keyuse], $element->prt, $element->act);
			}
	    }

	    private function load_fil($element){
	    	$keyuse = substr($element->fun, 3);
			$keyuse = base64_decode($keyuse);

			$keyloc = substr($element->loc, 3);
			$keyloc = base64_decode($keyloc);

			if(md5($keyuse) === $element->chk){
				add_filter($keyloc, [$this, $keyuse], $element->prt, $element->act);
			}
	    }

	    private function load_sht($element){
	    	$keyuse = substr($element->fun, 3);
			$keyuse = base64_decode($keyuse);

			$keyloc = substr($element->loc, 3);
			$keyloc = base64_decode($keyloc);

			if(md5($keyuse) === $element->chk){
				add_shortcode($keyloc, [$this, $keyuse]);
			}
	    }

	    private function load_sct($element){
	    	$keyuse = substr($element->fun, 3);
			$keyuse = base64_decode($keyuse);

			$keyloc = substr($element->loc, 3);
			$keyloc = base64_decode($keyloc);

			$depuse = substr($element->dep, 3);
			$depuse = base64_decode($depuse);
			$depuse = json_decode($depuse, true);
			$depuse = array_filter($depuse);

			if(md5($keyuse) === $element->chk){
				wp_enqueue_script( $keyloc, SPEW_URL . $keyuse, $depuse, filemtime(SPEW_PATH . $keyuse));
			}
	    }

	    private function load_css($element){
	    	$keyuse = substr($element->fun, 3);
			$keyuse = base64_decode($keyuse);

			$keyloc = substr($element->loc, 3);
			$keyloc = base64_decode($keyloc);

			$depuse = substr($element->dep, 3);
			$depuse = base64_decode($depuse);
			$depuse = json_decode($depuse, true);
			$depuse = array_filter($depuse);

			if(md5($keyuse) === $element->chk){
				wp_enqueue_style( $keyloc, SPEW_URL . $keyuse, $depuse, filemtime(SPEW_PATH . $keyuse));
			}
	    }

	    protected function table($name, $data){
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			global $wpdb, $charset_collate;

			if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$name}'") != $wpdb->prefix.$name) {
				$sql = "CREATE TABLE {$wpdb->prefix}{$name} (" . implode("\n,", $data ) . ") $charset_collate;";
				if(dbDelta($sql)){
					error_log("Tabla $name creada");
				}else{
					error_log("Tabla $name error al crear: $sql");
				}
			}else{
				error_log("Tabla $name ya existe");
			}
		}

	 	protected function alter($tabla, $columna, $tipo){
			global $wpdb;
			if($wpdb->query("ALTER TABLE {$wpdb->prefix}{$tabla} ADD COLUMN `{$columna}` {$tipo}") === true){
				error_log("Alter de {$tabla} con la columna {$columna} exitoso");
			}else{
				error_log("Error al alterar la tabla {$tabla} con la coumna {$columna} de tipo {$tipo}");
			};
		}

		protected function send_json_error($error_msg, $code = 404) {
	        $data = array(
	            'r' => false,
	            'm' => $error_msg
	        );
	        header('Content-Type: application/json');
	        http_response_code($code);
	        echo json_encode($data);
	        exit;
	    }

	    protected function send_json_success($success_msg, $array = []) {
	        $data = array_merge(array(
	            'r' => true,
	            'm' => $success_msg
	        ), $array);
	        header('Content-Type: application/json');
	        http_response_code(200);
	        echo json_encode($data);
	        exit;
	    }
	}
}

$GLOBALS['base_class'] = new base_class();