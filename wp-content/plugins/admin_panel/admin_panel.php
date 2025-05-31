<?php

/*
* Plugin Name: Admin Panel System
* Plugin URI: https://www.agenciasp.com
* Description: Intranet SP
* Version: 2.1.0
* Author: Agencia SP S.L.
* Author URI: https://www.agenciasp.com
* Text Domain: adpnsy
* Domain Path: /languages
* License: Agencia SP S.L.
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define('ADPNSY_VER', '2.1.0');
define('ADPNSY_PATH', realpath( dirname(__FILE__) ) );
define('ADPNSY_URL', plugins_url('/', __FILE__) );

require_once 'admin_license.php';