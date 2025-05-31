<?php

class admin_panel_system {

	public function init(){
		add_filter( 'theme_page_templates', array($this, 'include_templates'));
		add_filter( 'template_include', array($this, 'templates' ));
		add_action( 'after_setup_theme', array($this,'barra_admin'));
		add_filter( 'nav_menu_item_title', array($this, 'icons_menu'), 10, 4 );
		add_filter( 'template_adsp', array($this, 'template_normal'), 0, 1 );
		add_filter( 'avatar_adsp', array($this, 'template_normal'), 10, 1 );
		add_filter( 'template_mail_adsp', array($this, 'template_normal'), 0, 1 );
		add_filter( 'mime_types', array($this, 'webp'), 0, 1 );
		add_action( 'plugins_loaded', array($this, 'adpnsy_txtdomian'));
		add_action( 'init', array($this, 'ini_crud'), 10, 1 );
		add_action( 'wp_ajax_adpn_crud', array($this, 'ajax_crud'));
		add_action( 'wp_ajax_adpn_form', array($this, 'ajax_form'));
		add_action( 'wp_ajax_nopriv_adpn_form', array($this, 'ajax_form'));
		add_shortcode( 'adpnsy-form', array($this, 'forms'), 10, 1 );
	}

	public function icons_menu( $title, $item, $args, $depth ) {
	    if ( !empty( $item->description ) ) {
	        $title = '<i class="material-symbols-outlined icon-menu">'.$item->description.'</i> '.$title;
	    }
    	return $title;
	}

	public function webp($existing_mimes) {
	    $existing_mimes['webp'] = 'image/webp';
	    return $existing_mimes;
	}

	public function adpnsy_txtdomian(){
		load_plugin_textdomain('adpnsy', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	public function info($key = false){
		$licence = get_option('adpnsy_lic');
		if($licence){
			$licence = substr($licence, 3);
			$licence = base64_decode($licence);
			$licence = json_decode($licence);
			$site = get_option('siteurl');
			if(crypt($site, 'AN') == $licence->key){
				if(!isset($licence->key_last) || $licence->key_last < time() ){
					$nw = $ac = time() + 604800;
					$vl = self::comprobar($licence->key_id);
					if($vl)
						self::save($licence->key, $licence->key_id, $nw, $licence->opt, true);
					else
						self::save("", $licence->key_id, $nw, $licence->opt, true);
				}
				if($key){
					return $licence->key_id;
				}else{
					return $licence->opt;
				}
			}else{
				if(!$key) wp_die( __("No esta autorizado para ingresar a este enlace", "adpnsy") );
				return false;
			}
		}else{
			if($key) return false;
			exit;
		}
	}

/************************
*						*
*	Funciones Publicas	
*						*
************************/

	public function template_normal($path){
		return $path;
	}

	public function Noticia($class, $mensaje){ ?>
		<div class="notice <?=$class;?> is-dismissible">
	        <p><?=$mensaje;?></p>
	    </div>
	<?php }

	public function admin(){
		
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			self::activar();
		}

		$key = self::info(true);
		if($key){
			self::admin_config_panel();
		}else{
			self::admin_activar_panel();
		}
	}

	public function validar_rol($rol){
		$user = wp_get_current_user();
		return in_array($rol, $user->roles);
	}

	public function templates( $template ) {

		do_action("load_templates_adps");

		if ( is_page_template( 'admin_dashboard.php' ) || is_page_template( 'admin_perfil.php' ) ) {
			if(is_user_logged_in()){
				global $adpnsy, $info;
				$adpnsy = $this;
				$info = self::info();
				$template = ADPNSY_PATH . '/admin_dashboard.php';
			}else{
				self::to_login();
			}
		}

		if (is_page_template('admin_crud.php')) {
		    if (is_user_logged_in()) {
		        if (!defined('ADPNSY_CRUD')) {
		            self::to_dashboart();
		        }

		        global $adpnsy, $info, $post, $crud_adnsy, $data_crud;
		        $crud_adnsy = $post->post_excerpt;

		        // Ruta del archivo JSON
		        $archivo = ADPNSY_CRUD . "/{$crud_adnsy}.json";

		        // Verificar si el archivo existe antes de intentar leerlo
		        if (!file_exists($archivo)) {
		            wp_die(sprintf('No se encontró el archivo JSON para este CRUD: %s', $archivo));
		        }

		        // Leer contenido del archivo
		        $contenido = @file_get_contents($archivo);
		        if ($contenido === false) {
		        	wp_die(sprintf('No se pudo leer el archivo JSON: %s', $archivo));
		        }

		        // Decodificar JSON
		        $data_crud = json_decode($contenido, true);
		        if (json_last_error() !== JSON_ERROR_NONE) {
		        	wp_die(sprintf('Error al decodificar el archivo JSON de este CRUD: %s. Mensaje: %s', $archivo, json_last_error_msg()));
		        }

		        //permisos
		        if (!empty($data_crud['roles']) && is_array($data_crud['roles'])) {
				    $user = wp_get_current_user();
				    $current_roles = $user->roles;
				    $has_permission = array_intersect($data_crud['roles'], $current_roles);

				    if (!empty($has_permission)) {
				        $adpnsy = $this;
				        $info = self::info();
				        $template = ADPNSY_PATH . '/admin_dashboard.php';
				    } else {
				        wp_die('No tienes permiso para acceder a esta página.');
				    }
				} else {
				    wp_die('No se han definido roles permitidos.');
				}
		    } else {
		        self::to_login();
		    }
		}


		if ( is_page_template( 'admin_login.php' ) ) {
			if(is_user_logged_in()){
				self::to_dashboart();
			}else{
				global $adpnsy, $info, $mensaje;
				$adpnsy = $this;
				$info = self::info();
				$mensaje = self::conectar($info);
				$template = ADPNSY_PATH . '/admin_login.php';
			}
		}

		if ( is_page_template( 'admin_recovery.php' ) ) {
			if(is_user_logged_in()){
				self::to_dashboart();
			}else{
				global $adpnsy, $info, $mensaje;
				$adpnsy = $this;
				$info = self::info();
				$mensaje = self::recuperar($info);
				$template = ADPNSY_PATH . '/admin_recovery.php';
			}
		}

		if ( is_page_template( 'admin_register.php' ) ) {
			if(is_user_logged_in()){
				self::to_dashboart();
			}else{
				$template = ADPNSY_PATH . '/admin_register.php';
			}
		}

		if ( is_page_template( 'admin_logout.php' ) ) {
			self::logout();
		}
		
		return apply_filters('change_templates_adps', $template);
	}

	public function include_templates( $templates ) {
		$templates['admin_dashboard.php'] 	= 	__( 'SP Admin páginas', 'adpnsy' );
		$templates['admin_perfil.php'] 		= 	__( 'SP Admin perfil de usuario', 'adpnsy' );
	 	$templates['admin_login.php'] 		= 	__( 'SP Admin login', 'adpnsy' );
	 	$templates['admin_recovery.php'] 	= 	__( 'SP Admin recuperar contraseña', 'adpnsy' );
	 	$templates['admin_register.php'] 	= 	__( 'SP Admin registrar nuevo usuario', 'adpnsy' );
	 	$templates['admin_logout.php'] 		= 	__( 'SP Admin desconectar', 'adpnsy' );
	 	$templates['admin_crud.php'] 		= 	__( 'SP Admin páginas auto CRUD', 'adpnsy' );
	 	return apply_filters('add_templates_adps', $templates);
	}

    public function header($info, $login = true){
    	?>
    		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		    <meta http-equiv="X-UA-Compatible" content="IE=edge">
		    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		    <meta name="description" content="<?=$info->description;?>">
		    <meta name="author" content="<?=$info->autor;?>">
		    <title><?=$info->titulo;?></title>
		    <link rel="preconnect" href="https://fonts.googleapis.com">
			<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
			<link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap" rel="stylesheet">
			<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
		    <link rel="apple-touch-icon" href="<?=$info->icon_apple;?>">
			<link rel="apple-touch-icon" sizes="152x152" href="<?=$info->icon_apple;?>">
			<link rel="apple-touch-icon" sizes="180x180" href="<?=$info->icon_apple;?>">
			<link rel="apple-touch-icon" sizes="167x167" href="<?=$info->icon_apple;?>">
		    <link rel="shortcut icon" type="image/x-icon" href="<?=$info->favicon;?>">
		    <?php if($login){ ?>
		    	<link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>app-assets/css/themes/horizontal-menu-template/materialize.min.css">
		    	<link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>app-assets/css/themes/horizontal-menu-template/style.min.css">
		    	<link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>app-assets/css/layouts/style-horizontal.min.css">
		    	<link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>app-assets/css/login.min.css">
		    	<?php if($info->bglogin_vn){ ?>
			    	<style type="text/css">
			    		.login-bg-pre {
			    			background-image: url(<?=$info->bglogin_vn;?>);
			    		}
			    	</style>
		    	<?php } ?>
		    <?php }else{ ?>
		    	<link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>app-assets/css/themes/vertical-dark-menu-template/materialize.min.css">
        		<link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>app-assets/css/themes/vertical-dark-menu-template/style.min.css">
		    	<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
		    	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
		    	<link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>app-assets/vendors/data-tables/css/jquery.dataTables.min.css">
		    	<link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css">
		    	<style type="text/css">
			    	.sidenav-dark,
					.sidenav-dark .brand-sidebar  {
					    background-color: transparent;
					}
				</style>
		    <?php } ?>
		    <link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>app-assets/vendors/select2/select2-materialize.css">
		    <link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>app-assets/vendors/dropify/css/dropify.min.css">
		    <link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>css/base.css?<?=filemtime(ADPNSY_PATH . '/css/base.css');?>">
		    <link rel="stylesheet" type="text/css" href="<?=ADPNSY_URL;?>css/style.css?<?=filemtime(ADPNSY_PATH . '/css/style.css');?>">
		    <?php do_action("header_adsp"); ?>
		<?php
    }

    public function footer($login = true){ ?>
    	<script src="<?=ADPNSY_URL;?>app-assets/js/vendors.min.js"></script>
    	<script src="<?=ADPNSY_URL;?>app-assets/vendors/select2/select2.full.min.js"></script>
    	<?php if(!$login){ ?>
    		<script src="<?=ADPNSY_URL;?>app-assets/vendors/data-tables/js/jquery.dataTables.min.js"></script>
    		<script src="<?=ADPNSY_URL;?>app-assets/vendors/data-tables/js/dataTables.material.min.js"></script>
    		<script src="<?=ADPNSY_URL;?>app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js"></script>
			<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
			<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
			<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>	    		
    	<?php } ?>
    	<script type="text/javascript"> var AjaxUrl = "<?=admin_url( "admin-ajax.php" );?>";</script>
    	<script src="<?=ADPNSY_URL;?>app-assets/vendors/dropify/js/dropify.js"></script>
	    <script src="<?=ADPNSY_URL;?>app-assets/js/plugins.min.js"></script>
	    <script src="<?=ADPNSY_URL;?>js/base.js?<?=filemtime(ADPNSY_PATH . '/js/base.js');?>"></script>
	    <script src="<?=ADPNSY_URL;?>js/script.js?<?=filemtime(ADPNSY_PATH . '/js/script.js');?>"></script>
	    <?php do_action("footer_adsp"); ?>
	    <?php if ( is_page_template( 'admin_crud.php' ) ){ ?>
	    	<script src="<?=ADPNSY_URL;?>cruds/script.js?<?=filemtime(ADPNSY_PATH . '/cruds/script.js');?>"></script>
	    	<?php  global $crud_adnsy; if(defined( 'ADPNSY_CRUD' ) && $crud_adnsy && file_exists(ADPNSY_CRUD . "/{$crud_adnsy}.js")){ $js = file_get_contents(ADPNSY_CRUD . "/{$crud_adnsy}.js"); echo "<script>{$js}</script>"; } ?>
		<?php }
    }

    public function conectar($info){

    	if($_SERVER['REQUEST_METHOD'] === 'POST'){
	    	if($_REQUEST['login'] && $info){
				$creds = array(
			        'user_login'    => sanitize_text_field($_POST['user_login']),
			        'user_password' => sanitize_text_field($_POST['user_password']),
			        'remember'      => isset($_POST['remember'])
			    );
			    $user = wp_signon( $creds, is_ssl() );
			    if ( is_wp_error( $user ) ) {
			        return __("Comproveu usuari o contrasenya", "adpnsy");
			    }else{
			    	$_act = get_user_meta( $user->ID, '__active', true );
			    	if(!$_act){
			    		wp_logout();
			    		return __("Compte no actiu, es va enviar un correu electrònic amb un enllaç d'activació, si no el va rebre intenteu recuperar la contrasenya. Reviseu SPAM", "adpnsy");
			    	}else{
			    		$_desc = get_user_meta( $user->ID, '__unactive', true );	
			    		if($_desc){
			    			wp_logout();
			    			return __("Compte no actiu, si us plau contacteu al vostre administrador per a més informació.", "adpnsy");
			    		}else{
				    		self::to_dashboart($info);
				    	}
			    	}
			    }
			}
		}

		if($_SERVER['REQUEST_METHOD'] === 'GET'){
    		if(isset($_GET['key']) && $_GET['key'] != "" && isset($_GET['_s']) && $_GET['_s'] != "" && $info){
    			$us = urldecode(base64_decode(substr($_GET['_s'], 2)));
    			$ck = check_password_reset_key($_GET['key'], $us);
    			if(!is_wp_error( $ck )){
    				$user = get_user_by( "login", $us );
    				update_user_option( $user->ID, 'default_password_nag', false, true );
    				update_user_meta( $user->ID, '__active', true );
    				setcookie("_sp__m", base64_encode(__("Compte Activat", "adpnsy")), time()+1, "/");
    				self::to_login($info);
    			}else{
    				return __("Enllaç invàlid", "adpnsy");
    			}
    		}
    	}
    }

    public function to_login(){
    	$pgs = get_pages(array("meta_key" => "_wp_page_template", "meta_value" => "admin_login.php"));
    	if($pgs){
    		$pgs = array_shift($pgs);
    		wp_redirect(get_page_link($pgs->ID));
    	}else{
    		wp_redirect(get_option('siteurl'));
    	}
    	exit;
    }

    public function to_home(){
    	wp_redirect(home_url());
    	exit;
    }

    public function url_login(){
    	$pgs = get_pages(array("meta_key" => "_wp_page_template", "meta_value" => "admin_login.php"));
    	if($pgs){
    		$pgs = array_shift($pgs);
    		return get_page_link($pgs->ID);
    	}else{
    		return get_option('siteurl');
    	}
    }

    public function url_register(){
    	$pgs = get_pages(array("meta_key" => "_wp_page_template", "meta_value" => "admin_register.php"));
    	if($pgs){
    		$pgs = array_shift($pgs);
    		return get_page_link($pgs->ID);
    	}else{
    		return get_option('siteurl');
    	}
    }

    public function url_page($id){
    	if($id){
    		return get_page_link($id);
    	}else{
    		return get_option('siteurl');
    	}
    }

    public function url_politicas(){
    	$info = self::info(); 
    	if($info->Politicas){
    		return get_page_link($info->Politicas);
    	}else{
    		return get_option('siteurl');
    	}
    }
    
    public function to_dashboart($info = false){
    	if(!$info) $info = self::info(); 
    	if($info->login){
			wp_redirect(get_page_link($info->login));
		}else{
			$pgs = get_pages(array("meta_key" => "_wp_page_template", "meta_value" => "admin_dashboard.php"));
			if($pgs){
				$pgs = array_shift($pgs);
				wp_redirect(get_page_link($pgs->ID));
			}else{
				wp_redirect(get_option('siteurl'));
			}
		}
		exit;
    }

    public function dashboard_url(){
    	$info = self::info(); 
    	if($info->login){
			return get_page_link($info->login);
		}else{
			$pgs = get_pages(array("meta_key" => "_wp_page_template", "meta_value" => "admin_dashboard.php"));
			if($pgs){
				$pgs = array_shift($pgs);
				return get_page_link($pgs->ID);
			}else{
				return get_option('siteurl');
			}
		}
    }

    public function logout(){
    	wp_logout();
    	$pgs = get_pages(array("meta_key" => "_wp_page_template", "meta_value" => "admin_login.php"));
    	if($pgs){
    		$pgs = array_shift($pgs);
    		wp_redirect(get_page_link($pgs->ID));
    	}else{
    		wp_redirect(get_option('siteurl'));
    	}
    	exit;
    }

    public function avatar(){
    	$user = wp_get_current_user();
		if ( $user ) {
			$img = apply_filters('avatar_adsp', get_avatar_url( $user->ID ));
			return esc_url( $img );
		}else{
			return "";
		}
    }

    public function contenido(){
    	$adpnsy = $this;
		$user = wp_get_current_user();
		$info = self::info();
    	if(is_page_template( 'admin_perfil.php' )){
    		if(file_exists(ADPNSY_PATH . "/templates/perfil_usuario.php")){
    			$mensaje = self::perfil_user($info, $user);
				include ADPNSY_PATH . "/templates/perfil_usuario.php";
			}else{
				return "<p>El template de perfil fue eliminado</p>";
			}
		}elseif(is_page_template( 'admin_crud.php' )){
    		global $data_crud, $crud_adnsy;
			$file = apply_filters('page_crud_adsp', ADPNSY_PATH . "/cruds/page.php");
			include $file;
    	}else{
			global $post;
			$slug = $post->post_name;
			$file = apply_filters('template_adsp', ADPNSY_PATH . "/templates/" . $slug . ".php");
			if(file_exists($file)){
				include $file;
			}else{
				return "<p>No se ha generador el template {$file}</p>";
			}
    	}
    }

    public function menu($menu, $menu_class, $menu_id) {
	    $theme_locations = get_nav_menu_locations();
	    
	    if (isset($theme_locations[$menu])) {
	    	$menu_name = "";
	        $menu_obj = get_term($theme_locations[$menu], 'nav_menu');
	        if( $menu_obj && isset($menu_obj->name) ) $menu_name = $menu_obj->name;
	        
	        wp_nav_menu(array(
	            "menu" => $menu_name,
	            "menu_class" => $menu_class,
	            "menu_id" => $menu_id,
	            "container" => "ul"
	        ));
	    }
	}

    public function perfil_user($info, $user){

		$mensaje = null;
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
		    $data = $_REQUEST['act'];
		    $us = $user->user_login;
		    $em = $data['correo'];
		    if($user && $_userR){
		      ///actualizar nombre y apellido en wp
		      $_name = explode(" ", $data['nombre'])[0];

		      //cambio de correo
		      if($em != $user->user_email){
		        ///preguntar
		      }

		      if($_REQUEST['pass'] != ""){
		        $user_data = wp_update_user( 
		          array( 
		            'ID' => $user->ID, 
		            'first_name' => $_name,
		            'last_name' => $data['apellido1'],
		            'display_name' => $_name . " " . $data['apellido1'],
		            'user_email' => $em,
		            'user_pass' => $_REQUEST['pass']
		          ) 
		        );
		      }else{
		        $user_data = wp_update_user( 
		          array( 
		            'ID' => $user->ID, 
		            'first_name' => $_name,
		            'last_name' => $data['apellido1'],
		            'display_name' => $_name . " " . $data['apellido1'],
		            'user_email' => $em
		          ) 
		        );
		      }

		     
		      if(is_wp_error($user_data)){
		        $mensaje['e'] = "error";
		        $mensaje['m'] =  $user_data->get_error_message();
		      }else{
		      	if(isset($data['rol'])) $user->set_role( $data['rol'] );

		        //Registrar en base de datos personal
		        if($u_r !== false){
		          $mensaje['e'] = "exito";
		          $mensaje['m'] = __("Actualización exitosa", "adpnsy");
		        }else{
		          $mensaje['e'] = "error";
		          $mensaje['m'] = __("Error al actualizar los datos intente mas tarde", "adpnsy");
		        }
		      }
		    }else{
		      $mensaje['e'] = "error";
		      $mensaje['m'] = __("No hemos localizado la informacion del usuario", "adpnsy");
		    }
		}

    	return $mensaje;
    }

    public function recuperar($info){
    	if($_SERVER['REQUEST_METHOD'] === 'POST'){
    		if(isset($_REQUEST['recovery']) && $info){
    			if($user = get_user_by("email", $_REQUEST['mail_recovery'])){
    				$key = get_password_reset_key($user);
    				if(!is_wp_error($key)){
    					$_data = array(
    						"Link" 		=> self::url_page($info->Recovery) . "?key=" . $key . "&us=an" . urlencode(base64_encode($user->user_login)),
    						"Nombre"	=> $user->display_name,
    						"Key"		=> $key,
    						"User"		=> $user->user_login
    					);
    					if(self::mail($_REQUEST['mail_recovery'], __("Recuperación de contraseña", 'adpnsy'), "recuperacion", $_data)){
    						return array(
	    						"m" => __("Hemos procedido a enviarle un correo electrónico con las instrucciones. Revise SPAM", 'adpnsy' ),
	    						"c"	=> "exito"
	    					);
    					}else{
    						return array(
	    						"m" => __("Correo no enviado", 'adpnsy' ),
	    						"c"	=> "error"
	    					);
    					}
    				}else{
    					return array(
    						"m" => __("Key no generada", 'adpnsy' ),
    						"c"	=> "error"
    					);
    				}
    			}else{
    				return array(
						"m" => __("Usuario no encontrado", 'adpnsy' ),
						"c"	=> "error"
    				);
    			}
    		}else if(isset($_REQUEST['change']) && $info){
    			if(isset($_REQUEST['key']) && $_REQUEST['key'] != "" && 
    			   isset($_REQUEST['us']) && $_REQUEST['us'] != "" && 
    			   isset($_REQUEST['password1']) && $_REQUEST['password1'] != "" &&
				   isset($_REQUEST['password2']) && $_REQUEST['password2'] != "" && $info){
    			   	$us = urldecode(base64_decode(substr($_REQUEST['us'], 2)));
	    			$ck = check_password_reset_key($_REQUEST['key'], $us);
	    			if(!is_wp_error( $ck )){
						$error = [];
						$ps1 = $_REQUEST['password1'];
						$ps2 = $_REQUEST['password2'];
						if(!$ps1 || !$ps2) return ['m' => 'Por favor complete todo los campos para continuar', 'c' => 'Error'];
						if($ps1 !== $ps2) return ['m' => 'Las contraseñas no son iguales', 'c' => 'Error'];
						if(strlen($ps1) < 8) $error[] = 'al menos 8 dígitos';
						if(!preg_match('/[0-9]/', $ps1)) $error[] = 'al menos 1 numero';
						if(!preg_match('/[a-z]/', $ps1)) $error[] = 'al menos 1 letra minúscula';
						if(!preg_match('/[A-Z]/', $ps1)) $error[] = 'al menos 1 letra mayúscula';
						if(!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $ps1)) $error[] = 'al menos 1 carácter especial como !@#$%^&*(),.?":{}|<>';
						if(!empty($error)) return ['m' => ('Contraseña no segura debe tener:<br>'.(implode('<br>', $error))), 'c' => 'Error'];
	    				reset_password($ck, $_REQUEST['password1']);
	    				$user = get_user_by( "login", $us );
	    				setcookie("_sp__m", base64_encode(__("Nueva contraseña establecida", "adpnsy")), time()+1, "/");
	    				update_user_meta( $user->ID, '__active', true );
	    				self::to_login();
	    				exit;
	    			}else{
	    				return array(
		    				'm'	=> $ck->get_error_message()
		    			);
	    			}
	    		}else{
	    			return array(
	    				'm'	=> __("Datos insuficientes por favor rellene el formulario", "adpnsy")
	    			);
	    		}
    		}
    	}
    	if($_SERVER['REQUEST_METHOD'] === 'GET'){
    		if(isset($_GET['key']) && $_GET['key'] != "" && isset($_GET['us']) && $_GET['us'] != "" && $info){
    			$ck = check_password_reset_key($_GET['key'], urldecode(base64_decode(substr($_GET['us'], 2))));
    			if(!is_wp_error( $ck )){
    				return true;
    			}
    		}
    	}
    	return;
    }

    public function mail($correo, $asunto, $template, $_data){
    	global $info;
    	$info = self::info();
    	$adjunto = array();
    	if(isset($_data["__att"])){
    		$adjunto = $_data["__att"];
    		unset($_data["__att"]);
    	}
    	$_data = (object) $_data;
    	$_baseFile = apply_filters('template_mail_adsp', ADPNSY_PATH . "/templates_email/base.html");
    	$_base = file_get_contents($_baseFile);

    	$file = apply_filters('template_mail_adsp', ADPNSY_PATH . "/templates_email/" . $template . ".php");
		if(file_exists($file)){
			include($file);			
		}else{
			error_log("Template de email {$file} no existe");
			return false;
		}

    	$_head[] = 'Content-Type: text/html; charset=UTF-8';
			

    	if(isset($_config) && is_array($_config) && isset($_config['Correo']) && isset($_config['Nombre'])){
    		$_head[] = "From: ".$_config['Nombre']." <".$_config['Correo'].">";
    	}

    	if(isset($_config) && is_array($_config) && isset($_config['reply']) && $_config['reply']){
    		$_head[] = "Reply-To: {$_config['reply']}";
    	}

    	if(isset($remplace) && is_array($remplace)){
    		foreach ($remplace as $key => $value) {
	    		$_base = str_replace($key, $value, $_base);
	    	}
    	}
    	
    	file_put_contents(ADPNSY_PATH . "/$template.html", $_base);
    	return wp_mail( $correo, $asunto, $_base, $_head, $adjunto );
    }

    public function mensaje(){
    	if(isset($_COOKIE['_sp__m'])){
    		$mensaje = base64_decode(urldecode($_COOKIE['_sp__m']));
	    	unset($_COOKIE['_sp__m']);
			setcookie( "_sp__m", '', time() - 86400, "/" );?>
			<div class='exito-login'><?=$mensaje;?></div>
		<?php }
    }

	public function barra_admin() {
		if (!current_user_can('administrator') && !is_admin()) {
			add_filter( 'show_admin_bar', '__return_false' );
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

	protected function columns($tabla){
		global $wpdb;
		$columns = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}{$tabla}", ARRAY_A);
		$column_keys = array_column($columns, 'Field');
		if (empty($column_keys)) return false;
		return array_values($column_keys);
	}

 	protected function alter($tabla, $columna, $tipo){
		global $wpdb;
		if($wpdb->query("ALTER TABLE {$wpdb->prefix}{$tabla} ADD COLUMN `{$columna}` {$tipo}") === true){
			error_log("Alter de {$tabla} con la columna {$columna} exitoso");
		}else{
			error_log("Error al alterar la tabla {$tabla} con la coumna {$columna} de tipo {$tipo}");
		};
	}

	protected function modify_column($tabla, $columna, $tipo){
		global $wpdb;
		if($wpdb->query("ALTER TABLE {$wpdb->prefix}{$tabla} MODIFY COLUMN `{$columna}` {$tipo}") === true){
			error_log("Alter de {$tabla} con la columna {$columna} exitoso");
		}else{
			error_log("Error al alterar la tabla {$tabla} con la coumna {$columna} de tipo {$tipo}");
		};
	}

	protected function delete_column($tabla, $columna){
		global $wpdb;
		if($wpdb->query("ALTER TABLE {$wpdb->prefix}{$tabla} DROP COLUMN `{$columna}`") === true){
			error_log("Alter de {$tabla} con la columna {$columna} exitoso");
		}else{
			error_log("Error al alterar la tabla {$tabla} con la coumna {$columna}");
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

    public function error_pwa($error_msg, $code = 404) {
        $data = array(
            'r' => false,
            'm' => $error_msg
        );
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

	public function ini_crud() {
	    if (!defined('ADPNSY_CRUD')) return;
	    $carpeta = ADPNSY_CRUD;

	    if (is_dir($carpeta)) {
	        $archivos = glob($carpeta . '*.json');

	        if (!empty($archivos)) {
	            foreach ($archivos as $archivo) {
	                $crud = basename($archivo, '.json');
	                $version = filemtime($archivo);
	                $contenido = file_get_contents($archivo);
	                $datos = json_decode($contenido, true);
	                $db_version = get_option("adpnsy_db_{$crud}", 0);
	                $page_version = get_option("adpnsy_page_{$crud}", 0);
	                $folder_version = get_option("adpnsy_folder_{$crud}", 0);

	                if (json_last_error() !== JSON_ERROR_NONE) {
	                    error_log("Error al decodificar el archivo {$crud} JSON: " . json_last_error_msg());
	                    continue;
	                }

	                if (!isset($datos['columns']) || !is_array($datos['columns'])) {
	                    error_log("Error: 'columns' no está definido o no es un array en el archivo JSON: {$crud}");
	                    continue;
	                }

	                if ($db_version == 0) {
	                    $tabla = [];
	                    $key = $datos['key'];

	                    foreach ($datos['columns'] as $_id => $_tab) {
	                        if (!isset($_tab['db'])) continue;
	                        $tabla[] = "`$_id` {$_tab['db']}";
	                    }

	                    $tabla[] = "`create_adpn` datetime DEFAULT current_timestamp()";
	                    $tabla[] = "`update_adpn` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()";
	                    if ($key) {
	                        $tabla[] = "PRIMARY KEY (`{$key}`)";
	                    }

	                    $this->table($datos['data_base'], $tabla);
	                    add_option("adpnsy_db_{$crud}", $version);
	                } elseif ($db_version < $version) {
	                    $columns = $this->columns($datos['data_base']);
	                    $delete = $columns;

	                    foreach ($datos['columns'] as $_id => $_tab) {
	                        if (!isset($_tab['db'])) continue;
	                        if (in_array($_id, $columns)) {
	                            $this->modify_column($datos['data_base'], $_id, $_tab['db']);
	                            $delete = array_diff($delete, [$_id]);
	                        } else {
	                            $this->alter($datos['data_base'], $_id, $_tab['db']);
	                        }
	                    }

	                    foreach ($delete as $_col) {
	                    	if($_col != 'create_adpn' && $_col != 'update_adpn')
	                        	$this->delete_column($datos['data_base'], $_col);
	                    }

	                    update_option("adpnsy_db_{$crud}", $version);
	                }

	                if ($page_version == 0 && isset($datos["menu"]) && isset($datos["menu"]["active"]) && $datos["menu"]["active"] == true) {
	                    $new_page = [
	                        "post_title" => $datos['name'],
	                        "post_status" => "publish",
	                        "post_type" => "page",
	                        "post_excerpt" => $crud
	                    ];
	                    $page_id = wp_insert_post($new_page);

	                    if (is_wp_error($page_id)) {
	                        error_log($page_id->get_error_message());
	                        continue;
	                    }

	                    update_post_meta($page_id, '_wp_page_template', 'admin_crud.php');

	                    if (!empty($datos['menu']['active']) && !empty($datos['menu']['menus'])) {
	                        foreach ($datos['menu']['menus'] as $name_menu) {
	                            $id_menu = $this->id_menu($name_menu);
	                            if ($id_menu) {
	                                $this->add_menu_item($id_menu, $datos['name'], $page_id, $datos['menu']['icon'], 'adpnsy_crud');
	                            }
	                        }
	                    }

	                    add_option("adpnsy_page_{$crud}", $page_id);
	                }

	                // if($folder_version == 0){
	                // 	if(isset($datos["folder"]) && $datos["folder"] == 's3'){

	                // 	}else{
	                // 		require_once ADPNSY_PATH . "/admin_files.php";
	                // 		$files = new admin_panel_files();
	                		
	                // 	}
	                // }
	            }
	        }
	    }
	}

	public function ajax_crud(){
		include ADPNSY_PATH . '/cruds/ajax.php';
	}

	public function crud_tabla_header($data) {
		$extraText = "";
		if (isset($data['capability']['sub_title']) && isset($data['capability']['sub_title']["name"])) {
			$extraText = "<span>".$data['capability']['sub_title']["name"]."<span>";
			if(isset($data['capability']['sub_title']["name_render"])){
				global $wpdb;
				$sql = str_replace(["__BD__", "__CURRENT_USER_ID__"], [$wpdb->prefix, get_current_user_id()], $data['capability']['sub_title']["name_render"]);
				$dato = $wpdb->get_var($sql);
				$extraText = str_replace("%s", $dato, $extraText);
			}
		}
	    $output = "<h5>" . esc_html($data['name']) . $extraText . "</h5><div class='btns-crud'>";
	    if (isset($data['capability']['create'])) {
	    	$button_label = esc_html($data['capability']['create']);
	    	if(isset($data['capability']['create_rol'])){
	    		$user = wp_get_current_user();
			    $current_roles = $user->roles;
			    $has_permission = array_intersect($data['capability']['create_rol'], $current_roles);
			    if($has_permission){
			    	$output .= "<button id='add_element' class='btn waves-effect waves-light white-text'>" . $button_label . "</button>";
			    }
	    	}else{
	        	$output .= "<button id='add_element' class='btn waves-effect waves-light white-text'>" . $button_label . "</button>";
	    	}
	    }
	    if (isset($data['capability']['other_btns'])) {
	    	foreach($data['capability']['other_btns'] as $btn){
	    		if(isset($btn["rol"])){
	    			$user = wp_get_current_user();
				    $current_roles = $user->roles;
				    $has_permission = array_intersect($btn["rol"], $current_roles);
				    if(!$has_permission) continue;
	    		}
	    		$output .= "<a href='{$btn["url"]}' target='{$btn["target"]}'". ($btn["id"] ? "id='".$btn["id"]."'" : '' ) ." class='btn waves-effect waves-light white-text'>{$btn["name"]}</a>";
	    	}
	    }
	    return $output . "</div>";
	}

	public function crud_tabla_totales($data){
		if(isset($data["capability"]["totales"]) && is_array($data["capability"]["totales"])){
			$_rend = false;
			$output = '<div id="card-stats" class="pt-0"><div class="row">';
			foreach($data["capability"]["totales"] as $type => $total){
				if(isset($total["rol"]) && is_array($total["rol"])){
					$user = wp_get_current_user();
					$current_roles = $user->roles;
					if(!array_intersect($total["rol"], $current_roles))
					continue;
				}
				$_rend = true;
				$col = 12/($total["size"] ?? 1);
				$render = ($total["render"] ?? "");
				$output.= "<div class='col s12 m{$col}'>";
				$output.= "<div class='card animate fadeLeft'>";
				$output.= "<div class='card-content' style='background-color: {$total["color"]}; color: {$total["text-color"]}'>";
				$output.= "<p class='card-stats-title'><i class='material-symbols-outlined'>{$total["icon"]}</i> {$total["name"]}</p>";
				$output .= "<h4 class='card-stats-number white-text crud-totals-{$type}' data-render='{$render}'>---</h4>";
				$output.= "</div></div></div>";
			}
			$output.= "</div></div>";
			echo $_rend ? $output : "";
		}
	}

	public function crud_tabla($data){
		$output = "<tr>";
		$havkey = false;
		$otherActive = false;
		foreach($data["columns"] as $columa){
			if($columa["key"] == true) $havkey = true;
			if(isset($columa["table"]) && $columa["table"] == true){
				if(is_array($columa['table'])){
	        		$user = wp_get_current_user();
				    $current_roles = $user->roles;
				    $has_permission = array_intersect($columa['table'], $current_roles);
				    if(!$has_permission) continue;
	        	}
				$output .= "<th>{$columa["name"]}</th>";
			}
		}
		if ($havkey && ((isset($data['capability']['edit']) || 
			isset($data['capability']['delete'])))) {
			$output .=  "<th>Acciones</th>";
		}elseif ($havkey && isset($data['capability']['other'])) {
			foreach ($data['capability']['other'] as $oth) {
				if(isset($oth["rol"])){
					$user = wp_get_current_user();
				    $current_roles = $user->roles;
				    $has_permission = array_intersect($oth["rol"], $current_roles);
				    if($has_permission) $otherActive = true;
				}else{
					$otherActive = true;
				}
			}
			if($otherActive) 
			$output .= "<th>Acciones</th>" ;
		}

		return $output . "</tr>";
	}

	public function modo_list($list, $idiomas = false){
		$nlis = [];
		if($idiomas){
			if(is_array($list)) foreach($list as $l) $nlis[$l->id] = pll__($l->titulo);
		}else{
			if(is_array($list)) foreach($list as $l) $nlis[$l->id] = $l->titulo;
		}
		
		return $nlis;
	}

	public function modo_list_full($list){
		$nlis = [];
		if(is_array($list)) foreach($list as $l) $nlis[$l->id] = $l;
		return $nlis;
	}

	public function modo_list_full_map($list, $id){
		$nlis = [];
		if(is_array($list)) foreach($list as $l){
			if(!isset($nlis[$l->{$id}])) $nlis[$l->{$id}] = [];
			$nlis[$l->{$id}][] = $l;
		} 
		return $nlis;
	}

	public function modo_tree(array &$elements, $parentId = 0) {
	    $branch = [];
	    foreach ($elements as &$element) {
	        if ($element['id_padre'] == $parentId) {
	            $children = $this->modo_tree($elements, $element['id']);
	            if ($children) {
	                $element['child'] = $children;
	            } else {
	                $element['child'] = [];
	            }
	            $branch[$element['id']] = [
	                'value' => $element['titulo'],
	                'child' => $element['child']
	            ];
	        }
	    }
	    return $branch;
	}

	public function crud_js_tabla($data) {
		global $wpdb;
		$user = wp_get_current_user();
		$current_roles = $user->roles;
	    $key = $data['key'];
	    $extras = [];
	    $create = [];
		$filtros = [];
	    $tabla = [
	    	'responsive' => true,
	        'processing' => true,
	        'serverSide' => true,
	        'serverMethod' => 'post',
	        'pageLength' => 10,
	        'ajax' => [
	            'url' => '?action=adpn_crud'
	        ],
	        'order' => [[0, 'desc']],
	        'columns' => []
	    ];

		#tabla
		foreach ($data['columns'] as $_id => $columna) {
	        if (isset($columna['table']) && $columna['table'] == true){
	        	if(is_array($columna['table']) && !array_intersect($columna['table'], $current_roles)) 
					continue;
	        	if($columna['tipo'] != 2 && $columna['tipo'] != 3 && $columna['tipo'] != 8){
	        		$_colum = ['data' => $_id];
	        		if(!$columna['db']) $_colum['orderable'] = false;
	        		if(isset($columna['render_js'])){
	        			$render_function = new stdClass();
	        			$render_function->function = "(data, type, row) => " . $columna['render_js'];
	        			$_colum['render'] = $render_function;
	        		}
	        		$tabla['columns'][] = $_colum;
	        	}else{
	        		$render_function = new stdClass();
	        		$render_function->function = "(data, type, row) => adpnsy_{$_id}[data] ?? '---'";
	        		$tabla['columns'][] = ['data' => $_id, 'render' => $render_function];
	        	}
	        }
	    }

	    #extras - select
		foreach ($data['columns'] as $_id => $columna) {
			if($columna['tipo'] == 2 || $columna['tipo'] == 3 || $columna['tipo'] == 8){
				if(isset($columna['option_list'])){
					$query = $columna['option_list'];
					if(isset($columna['option_list_render'])){
						foreach($columna['option_list_render'] as $_key => $_data){
							if(strpos($_key, "BD")) $query = str_replace($_key, $wpdb->prefix . $_data, $query);
							else $query = str_replace($_key, $_data, $query);
						}
					}
					$dts = $wpdb->get_results($query);
					if(isset($columna['option'])) $columna['option'] = array_replace($columna['option'], $this->modo_list($dts));
					else $columna['option'] = $this->modo_list($dts);
				}      	
				$extras["adpnsy_{$id}"] = $columna['option'];
			}
		}

		#crud
	    foreach ($data['columns'] as $_id => $columna) {
	        if(isset($columna["crud"]) && $columna["crud"] == true){
				if(is_array($columna['crud']) && !array_intersect($columna['crud'], $current_roles)) 
					continue;
        		$create[$_id] = [
        			"name" => $columna['name'],
        			"tipo" => $columna['tipo'],
        			"icon" => $columna['icon'],
        			"size" => $columna['size'],
        			"attr_crud" => $columna['attr_crud'] ?? [],
        			"default" => $columna['default'],
        			"required" => $columna['required'] ?? false,
        		];
        	}
	    }

		#filtros
		foreach ($data['columns'] as $_id => $columna) {
	        if(isset($columna["filter"]) && $columna["filter"] == true){
				if(is_array($columna['filter']) && !array_intersect($columna['filter'], $current_roles)) 
					continue;
        		$filtros[$_id] = [
        			"name" => $columna['name'],
        			"filter_type" => $columna['filter_type']
        		];
        	}
	    }

	    if ($key) {
	        $elements = [];
	        if (isset($data['capability']['other'])) {
	            foreach ($data['capability']['other'] as $type => $act) {
					if(isset($act["rol"])){
						$user = wp_get_current_user();
						$current_roles = $user->roles;
						$has_permission = array_intersect($act["rol"], $current_roles);
						if(!$has_permission) continue;
					}

					if ($act['type'] == 'render') {
	                	if(isset($act['render_js'])){
							$elements[] = "\${{$act['render_js']}}";
						}
						continue;
	                }

	            	$icon = $act['icon'];
	            	if(is_array($act['icon'])){
	            		$icon = '';
	            		foreach($act['icon'] as $s => $i) $icon .= "\${(row.{$act['used']} == '$s') ? '$i': ''}";
	            	}
	            	$name = $act['name'];
	            	if(is_array($act['name'])){
	            		$name = '';
	            		foreach($act['name'] as $s => $i) $name .= "\${(row.{$act['used']} == '$s') ? '$i': ''}";
	            	}
	            	$data_act = "data-id='\${row.{$key}}'";
	            	if(isset($act['data']) && is_array($act['data'])){
	            		$data_act = '';
	            		foreach($act['data'] as $i) $data_act .= " data-{$i}='\${row.{$i}}'";
	            	}

	                if ($act['type'] == 'url') {
	                    $home = home_url('/');
	                    $elements[] = "<a href='{$home}{$act['attr']}\${row.{$act['used']}}' target='{$act['target']}' title='{$name}' class='{$type} material-symbols-outlined'>{$icon}</a>";
	                } elseif ($act['type'] == 'ajax') {
	                    $elements[] = "<a href='#' {$data_act} data-crud_action='{$act['action']}' title='{$name}' class='{$type} material-symbols-outlined'>{$icon}</a>";
	                } else {
	                    $elements[] = "<a href='#' {$data_act} title='{$name}' class='{$type} material-symbols-outlined'>{$icon}</a>";
	                }
	            }
	        }
	        if (isset($data['capability']['edit'])) {
	            $elements[] = "<a href='#' data-id='\${row.{$key}}' title='" . esc_attr($data['capability']['edit']['name']) . "' class='edit material-symbols-outlined'>" . esc_html($data['capability']['edit']['icon']) . "</a>";
	        }
	        if (isset($data['capability']['delete'])) {
	            $elements[] = "<a href='#' data-id='\${row.{$key}}' title='" . esc_attr($data['capability']['delete']['name']) . "' class='delete material-symbols-outlined'>" . esc_html($data['capability']['delete']['icon']) . "</a>";
	        }
	        if (!empty($elements)) {
	            $render_function = new stdClass();
	            $render_function->function = "(data, type, row) => `" . implode("\n", $elements) . "`";
	            $action = [
	                'data' => $key,
	                'orderable' => false,
	                'render' => $render_function
	            ];
	            $tabla['columns'][] = $action;
	        }
	    }

	    $json = json_encode($tabla, JSON_UNESCAPED_SLASHES);

	    // Reemplazar manualmente el render para que no esté entre comillas
	    $json = preg_replace('/"render":\{"function":"(.*?)"\}/', '"render":\1', $json);

	    if($key && !empty($create) && (isset($data["capability"]["create"]) || isset($data["capability"]["edit"]))) 
	    	$extras["crud_list"] = [
	    		"key" => $key,
	    		"columns" => $create,
	    		"base" => array_keys($create),
	    		"modal" => $data["modal"]
	    	];
		if(!empty($filtros) && isset($data["capability"]["filtro"])) 
			$extras["crud_sys_filter"] = [
				"data" => $filtros, 
				"btn" => $data["capability"]["filtro"]
			];
	    if(!isset($extras["crud_list"]) && isset($data["capability"]["delete"]))
	    	$extras["crud_list"] = [
	    		"modal" => $data["modal"]
	    	];

	    if(!empty($extras)) foreach($extras as $_key => $val) $json .= "; var {$_key} = " . json_encode($val);
	    return "let _table_ = {$json}";
	}

	public function ajax_form() {
		$form = sanitize_text_field($_REQUEST['form']);
		if(!isset($form) || empty($form)) 
		$this->send_json_error("Formulario no enviado");

		// Check and load PHP form file if exists
		$form_php = ADPNSY_FORMS . $form . '.php';
		if (file_exists($form_php)) {
			include($form_php);
		}else{
			$this->send_json_error("Formulario no encontrado");
		}
		
	}

	public function forms($atts) {
		// Extract shortcode attributes with defaults
		$attributes = shortcode_atts(array(
			'id' => ''
		), $atts);

		// Initialize output buffer
		ob_start();

		// Validate ADPNSY_FORMS constant
		if (!defined('ADPNSY_FORMS')) {
			echo 'Error: Directorio de formularios no configurado';
			return ob_get_clean();
		}

		// Validate ADPNSY_FORMS constant
		if (!defined('ADPNSY_FORMS_URL')) {
			echo 'Error: La url de formularios no configurado';
			return ob_get_clean();
		}

		// Check if form ID is provided
		if (empty($attributes['id'])) {
			echo 'Error: Se requiere ID del formulario';
			return ob_get_clean();
		}

		// Check for JSON file
		$json_file = ADPNSY_FORMS . $attributes['id'] . '.json';
		if (!file_exists($json_file)) {
			echo 'Error: No se encontró el archivo JSON';
			return ob_get_clean();
		}

		// Read JSON file
		$form_data = json_decode(file_get_contents($json_file), true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			echo 'Error: Formato de JSON inválido - ' . json_last_error_msg();
			return ob_get_clean();
		}

		// Check if form data is valid
		if (!is_array($form_data)) {
			echo 'Error: Datos del formulario inválidos';
			return ob_get_clean();
		}

		// Check for JS file
		$script_path = ADPNSY_PATH . '/forms/script.js';
		if(!file_exists($script_path)){
			echo 'Error: No se encontro el script principal';
			return ob_get_clean();
		}

		// Check for CSS file
		$style_path = ADPNSY_PATH . '/forms/style.css';
		if(!file_exists($style_path)){
			echo 'Error: No se encontró el archivo de estilos principal';
			return ob_get_clean();
		}

		// Load main forms style
		$style_version = file_exists($style_path) ? filemtime($style_path) : '1.0';
		wp_enqueue_style('material-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined', array());
		wp_enqueue_style('forms-adpnsy', ADPNSY_URL . 'forms/style.css', array(), $style_version);

		// Load main forms script
		$script_version = file_exists($script_path) ? filemtime($script_path) : '1.0';
		wp_enqueue_script('forms-adpnsy', ADPNSY_URL . 'forms/script.js', array('jquery'), $script_version, true);
		wp_localize_script('forms-adpnsy', 'form_ajax', array(
			'url' => admin_url('admin-ajax.php'),
			'data' => $form_data,
			'id' => $attributes['id'],
			'base' => ADPNSY_FORMS_URL. "/". $attributes['id']. "/"
		));

		// load custom code
		$form_js = ADPNSY_FORMS . $attributes['id'] . '.js';
		if (file_exists($form_js)) {
			$form_js_version = filemtime($form_js);
			wp_enqueue_script(
				'form-' . $attributes['id'], 
				ADPNSY_FORMS_URL . "/" . $attributes['id'] . '.js',
				array('forms-adpnsy'),
				$form_js_version,
				true
			);
		}

		// Load custom CSS file
		$css_path = ADPNSY_FORMS. $attributes['id']. '.css';
		if (file_exists($css_path)) {
			$css_version = filemtime($css_path);
			wp_enqueue_style('form-'. $attributes['id'], ADPNSY_FORMS_URL. "/". $attributes['id']. '.css', array(), $css_version);
		}
		
		//Agrega la informacion del json a procesar
		echo '<div class="adpnsy-form" id="adpnsy_form_'. $attributes['id']. '" data-form="'. $attributes['id']. '"><span class="adpnsy-form-load">Cargando...</span></div>';
		return ob_get_clean();
	}


/************************
*						*
*	Funciones Privadas	*
*						*
************************/

	private function save($key, $key_id, $key_last, $opt, $nm = false){
		$bse = new stdClass();
		$bse->key = $key;
		$bse->key_id = $key_id;
		$bse->key_last = $key_last;
		$bse->opt = $opt;
		$bse = json_encode($bse);
		$bse = base64_encode($bse);
		$bse = 'and' . $bse;
		if(update_option('adpnsy_lic', $bse)){
			if($key != "" && !$nm) self::Noticia("notice-success", __("Datos guardados satisfactoriamente.", "adpnsy"));
			return true;
		}else{
			if($key != "" && !$nm) self::Noticia("notice-error", __("Error al procesar los datos.", "adpnsy"));
			return false;
		}
	}

    private function activar(){
    	$site = get_option('siteurl');
    	$key = crypt($site, 'AN');
		if(isset($_POST['activar'])){
			///comprobar licencia
			if(!self::comprobar(sanitize_text_field($_POST['licencia']), false)){
				self::Noticia("notice-error", "Licencia Invalida");
			}else{
				/* En caso de reactivacion */
				$licence = get_option('adpnsy_lic');
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
				update_option('adpnsy_lic', $bse);
			}
		}else{
			$key_id = self::info(true);
			self::save($key, $key_id, time() + 86400, $_POST );
		}
    }

    private function admin_activar_panel(){
    	?>
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
    				<img src="<?=ADPNSY_URL;?>app-assets/img/logo.png">
    			</span>
    			<div>
    				<h1>SP Admin</h1>
    				<p>Diseñado y desarrollado por <a target="_blank" href="https://www.santipm.com/">Agencia Santiago Ponce</a></p>
    			</div>
    		</div>
			<div class="wrap">
				<h1>Activación de Panel</h1>
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
		<?php
    }

    private function admin_config_panel(){

    	if(isset($_GET['demo'])){
    		$info = self::info();
    		//if(!$info->demo){
    			$c = new adpnsy;
	    		$opt = $c->install();
	    		self::install_($opt);
    		//}
    	}

    	if(isset($_COOKIE['__sp_a'])){
			$mensaje =  json_decode(base64_decode(urldecode($_COOKIE['__sp_a'])), true);
			self::Noticia($mensaje[0], $mensaje[1]);
			unset($_COOKIE['__sp_a']);
			setcookie( "__sp_a", '', 1 );
		}

    	///WP
		wp_enqueue_media();
		///JS
		wp_enqueue_script( 'admin_panel_js', ADPNSY_URL . "js/admin.js", array('jquery'), "1.5");
		///S2
		wp_enqueue_script( 'select2', ADPNSY_URL . "app-assets/vendors/select2/select2.full.min.js", array('jquery'));
		wp_enqueue_style( 'select2', ADPNSY_URL . "app-assets/vendors/select2/select2.min.css");
		///Materialize
		wp_enqueue_style( 'materialize', ADPNSY_URL . "app-assets/css/themes/horizontal-menu-template/style.min.css");
		wp_enqueue_style( 'admin_panel_css', ADPNSY_URL . "css/admin.css");
		///Panel

		$pages = get_pages(array("meta_key" => "_wp_page_template", "meta_value" => "admin_dashboard.php"));

		$pages_rg = get_pages(array("meta_key" => "_wp_page_template", "meta_value" => "admin_register.php"));

		$pages_rc = get_pages(array("meta_key" => "_wp_page_template", "meta_value" => "admin_recovery.php"));

		$pages_al = get_pages();

		$info = self::info();
		echo '<div class="head-config"><span><img src="' . ADPNSY_URL . 'app-assets/img/favicon-asp.png"></span><div><h1>SP Admin</h1><p>Diseñado y desarrollado por <a target="_blank" href="https://www.santipm.com/">Agencia Santiago Ponce</a></p><a class="button button-primary" type="submit" href="' . admin_url("options-general.php?page=panel_admin_system&demo") . '">Instalar Demo</a></div></div>';
		
		include 'admin_config.php';
    }

    private function comprobar($lic = "", $df = true){
    	$api_params = array(
			'slm_action' => 'slm_check',
			'secret_key' => '5e84c405466b11.57304054',
			'license_key' => $lic
		);
		$response = wp_remote_get(add_query_arg($api_params, 'http://santipm.com/blog/'), array('timeout' => 20, 'sslverify' => false));
		if(is_wp_error( $response )) return $df;
		try {
		    $data = json_decode( $response['body'] );
		} catch ( Exception $ex ) {
		    $data = null;
		}
		if(is_null($data)) return $df;
		if($data->result != "success") return false;
		if($data->status != "active") return false;
		return true;
    }

	private function install_($opt){
    	$licence = get_option('adpnsy_lic');
		if($licence){
			$licence = substr($licence, 3);
			$licence = base64_decode($licence);
			$licence = json_decode($licence);
			$key = $licence->key;
			$key_id = $licence->key_id;
			$key_last = $licence->key_last;
			if(self::save($key, $key_id, $key_last, $opt)){
				setcookie("__sp_a", base64_encode(json_encode(array('notice-success', __('Demo instalado.', 'adpnsy')))) );
			}else{
				setcookie("__sp_a", base64_encode(json_encode(array('notice-error', __('Error al instalar Demo.', 'adpnsy')))) );
			}
		}else{
			setcookie("__sp_a", base64_encode(json_encode(array('notice-error', __('Error al procesar los datos.', 'adpnsy')))) );
		}
		wp_redirect(remove_query_arg("demo"));
		exit;
    }

    private function add_menu_item($menu_id, $title, $page_id, $description = '', $class = '') {
	    wp_update_nav_menu_item($menu_id, 0, [
	        'menu-item-title' => $title,
	        'menu-item-object-id' => $page_id,
	        'menu-item-object' => 'page',
	        'menu-item-status' => 'publish',
	        'menu-item-type' => 'post_type',
	        'menu-item-description' => $description,
	        'menu-item-classes' => $class
	    ]);
	}

	private function id_menu($menu) {
	    $ubicaciones_menus = get_nav_menu_locations();
	    if (isset($ubicaciones_menus[$menu])) {
	        return $ubicaciones_menus[$menu];
	    }
	    return null;
	}
}

?>