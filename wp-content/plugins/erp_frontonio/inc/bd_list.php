<?php 

	global $wpdb;

	#tablas
	$tabla_socios = $wpdb->prefix . "sp_socios";
	$tabla_prerregistros = $wpdb->prefix . "sp_prerregistros";
	$tabla_ordenes = $wpdb->prefix . "wc_orders";

	#directorios
	$path_imagenes = WP_CONTENT_DIR . "/erp/imagenes";

	#urls
	$url_imagenes = content_url("/erp/imagenes");
