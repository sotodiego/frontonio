<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once SPEW_PATH . 'core/widgets/widget-ejemplo.php';
$widgets_manager->register( new \SP_Widget_Ejemplo() );

require_once SPEW_PATH . 'core/widgets/widget-antes-despues.php';
$widgets_manager->register( new \SP_Widget_Antes_Despues() );






?>



