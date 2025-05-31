<?php 

	$adpnsy_db = get_option("adpnsy_db", 0);
	$adpnsy_db_fix = get_option("adpnsy_db_fix", 0);
	$adpnsy_dir = get_option("adpnsy_dir", 0);
	$adpnsy_roles = get_option("adpnsy_roles", 0);


	///BD
	// if($adpnsy_db < 1){
	// 	$this->table(
    // 		'sp_disponibilidad_actividades',
    // 		[
	// 			"`id` bigint(20) AUTO_INCREMENT NOT NULL",
	// 			"`id_reserva` bigint(20) NOT NULL",
	// 			"`id_actividad` bigint(20) NOT NULL",
	// 			"`fecha` DATE NOT NULL DEFAULT CURRENT_DATE",
	// 			"`numero_personas` int NOT NULL default 0",
	// 			"PRIMARY KEY (`id`)"
	// 		]
    // 	);
	// 	update_option("adpnsy_db", 2);
	// }


	//FIX
	// if($adpnsy_db_fix < 1){
	// 	update_option("adpnsy_db_fix", 1);
	// }


	//DIR
	// if($adpnsy_dir < 1){
	// 	require_once ADPNSY_PATH . "/admin_files.php";
	// 	$files = new admin_panel_files();
	// 	$files->crearCarpeta(WP_CONTENT_DIR . "/erp");
	// 	$files->crearCarpeta(WP_CONTENT_DIR . "/erp/imagenes");
	// 	update_option("adpnsy_dir", 1);
	// }

	
	//ROLES

	if($adpnsy_roles  < 1 ) {
		add_role( 'socio', 'Socio', array( 'read' => true, 'level_0' => true ) );
		update_option("adpnsy_roles", 1);
    }