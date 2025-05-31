<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if (!defined('HOME_URL')) define('HOME_URL', home_url());
if (!defined('THEME_PATH')) define('THEME_PATH', get_stylesheet_directory());
if (!defined('THEME_URL')) define('THEME_URL', get_stylesheet_directory_uri());

require_once THEME_PATH . '/inc/scripts.php';
require_once THEME_PATH . '/inc/shortcodes.php';