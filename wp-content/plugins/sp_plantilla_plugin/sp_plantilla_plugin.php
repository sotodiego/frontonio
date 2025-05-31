<?php

/*
* Plugin Name: SP - Plantilla Plugin
* Plugin URI: https://www.agenciasp.com
* Description: Sé ordenado y todo irá bien.
* Version: 1.0.0
* Author: AgenciaSP
* Author URI: https://www.agenciasp.com
* License: AgenciaSP
* Text Domain: sp_plantilla_plugin
* Domain Path: /languages
*/


if ( ! defined( 'ABSPATH' ) ) exit;

define('sp_plantilla_plugin_PATH', realpath( dirname(__FILE__) ) );
define('sp_plantilla_plugin_URL', plugins_url('/', __FILE__) );

if ( !class_exists('sp_plantilla_plugin')){
	class sp_plantilla_plugin {
		public function __construct(){
			
        }
        
    }
}



$GLOBALS['sp_plantilla_plugin'] = new sp_plantilla_plugin();