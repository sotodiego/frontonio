<?php
//SE CREA UNA FUNCION PARA CREAR TODAS LAS PAGINAS DEL MENU
function addMenusPage($menus){
	if(current_user_can('edit_others_posts')){ //validacion de usuarios de que puedan editar
		if(is_array($menus)){
			for($i = 0;$i<count($menus);$i++){
				add_menu_page(
					$menus[$i]['page_title'],
					$menus[$i]['menu_title'],
					$menus[$i]['capability'],
					$menus[$i]['menu_slug'],
					$menus[$i]['functionName'],
					$menus[$i]['icon_url'],
					$menus[$i]['position']
				);
			}
		}
	}
}

//SE CREA UNA FUNCION PARA CREAR TODAS LAS PAGINAS DEL SUBMENU
function addSubMenusPage($submenus){
	if(current_user_can('edit_others_posts')){  //validacion de usuarios de que puedan editar
		if(is_array($submenus)){
			for($i = 0;$i<count($submenus);$i++){
				add_submenu_page(
					$submenus[$i]['parent_slug'],
					$submenus[$i]['page_title'],
					$submenus[$i]['menu_title'],
					$submenus[$i]['capability'],
					$submenus[$i]['menu_slug'],
					$submenus[$i]['function']
				);
			}
		}
	}
}

function calcularPesoEnKilos($peso){	
    $peso = floatval($peso);
    $unidadMedida=get_option('woocommerce_weight_unit');
    $nuevoPeso=1;
    switch($unidadMedida){
        case 'g';
            $nuevoPeso=$peso/1000;        
            break;
        case 'kg';
            $nuevoPeso=$peso;
            break;
        case 'oz';
            $nuevoPeso=$peso*0.02835;
            break;
        case 'lbs';
            $nuevoPeso=$peso*0.453592;
            break;
        default:
            $nuevoPeso=$peso;
            break;
    }
    return round($nuevoPeso,3, PHP_ROUND_HALF_UP);
}

function cex_rellenar_ceros($valor, $longitud)
{
    $res = str_pad($valor, $longitud, '0', STR_PAD_LEFT);
    return $res;
}

function cex_encrypt_decrypt($action,$pass){	
	global $wpdb;	
	$nP=false;
	$table = $wpdb->prefix.'cex_customer_options';	
	$results = $wpdb->get_var($wpdb->prepare("SELECT valor FROM $table where clave='MXPS_CRYPT'", null));	
	$encrypt_method='AES-256-CBC';
	$salt=base64_decode($results);	
	$salt1=hash('sha256',$salt);
	$salt2=substr(hash('sha256',$salt),0,16);	
	if ($action == 'encrypt') {
		$nP=base64_encode(openssl_encrypt($pass,$encrypt_method,$salt1,0,$salt2));
	} else if ($action == 'decrypt') {
		$nP=openssl_decrypt(base64_decode($pass),$encrypt_method,$salt1,0,$salt2);
	}
	return $nP;
}

function cex_quitar_ceros($valor)
{
    $res= ltrim($valor, "0");
    return $res;
}

function codificar_logo(){
	global $wpdb;
	$table = $wpdb->prefix.'cex_customer_options';	
	$comprobante=$wpdb->get_var($wpdb->prepare("SELECT valor FROM $table where clave='MXPS_CHECKUPLOADFILE'", null));	
	if($comprobante=='true'){
		$path=$wpdb->get_var($wpdb->prepare("SELECT valor FROM $table where clave='MXPS_UPLOADFILE'", null));
		$datos=file_get_contents($path);
		$retorno= base64_encode($datos);
	}else{
		$retorno = ""; 
	}
	
	return $retorno;
}

function retornar_logo(){
	global $wpdb;
	$table = $wpdb->prefix.'cex_customer_options';
	$path=$wpdb->get_var($wpdb->prepare("SELECT valor FROM $table where clave='MXPS_UPLOADFILE'", null));
	$datos=file_get_contents($path);
	return $datos;
}


function get_user_credentials()
{
    global $wpdb;
    $table = $wpdb->prefix.'cex_customer_options';
    //sacar de BBDD el usuario y contraseña para meterla en la peticion.
    $usuario = $wpdb->get_var("SELECT valor
        FROM $table
        WHERE clave= 'MXPS_USER'");

    $password = $wpdb->get_var("SELECT valor
        FROM $table
        WHERE clave= 'MXPS_PASSWD'");

    $credenciales =[
        'usuario'  => cex_encrypt_decrypt('decrypt',$usuario),
        'password' => cex_encrypt_decrypt('decrypt',$password),
    ];

    return $credenciales;
}

