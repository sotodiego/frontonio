<?php 
	date_default_timezone_set('Europe/Madrid');
	if ( isset($_REQUEST) && get_current_user_id() ){

		#basicos
		require_once ERP_FRONTONIO_PATH . "/inc/bd_list.php";
		require_once ADPNSY_PATH . "/admin_files.php";
		$files = new admin_panel_files();






		if(isset($_REQUEST['perfil_socio']) && isset($_REQUEST['unik']) && wp_verify_nonce($_REQUEST['unik'], 'aje_noc')){
			$operacion = $_REQUEST['perfil_socio'];

			if($operacion == 0){
				$socio = json_decode(stripcslashes($_REQUEST['data']), true);
				$socio["id"] = get_current_user_id();
				
				if($socio["pass2"] && $socio["pass1"] && $socio["pass2"] == $socio["pass1"]){
					wp_set_password($socio["pass1"], $socio["id"]);
				}
				
				unset($socio["pass1"]);
				unset($socio["pass2"]);

				if(isset($socio["fecha_nacimiento"])  && $socio["fecha_nacimiento"]){
					$fecha = $this->validar_fecha_crud($socio["fecha_nacimiento"]);
					if(!$fecha) $this->send_json_error("La fecha de nacimiento introducida no es válida.");
					$socio["fecha_nacimiento"] = $fecha;
				}
				
				if($wpdb->update($tabla_socios, $socio, ["id" => $socio['id']]) === false)
					$this->send_json_error("No se ha podido guardar sus datos. Por favor, inténtelo más tarde", 500);

				if( $this->verificar_datos_obligatorios_publicos( get_current_user_id() ) && $this->verificar_datos_obligatorios_privados( get_current_user_id() ) ){
					$wpdb->update($tabla_socios, ["estado_perfil" => "1"], ["id" => $socio['id']]);
				}

				$mensaje = "Tus datos privados han sido guardados.";
				if( !$this->verificar_datos_obligatorios_privados( get_current_user_id() ) )
					$mensaje = "Tus datos privados han sido guardados como borrador. Por favor, finaliza este formulario antes de continuar.";

				if( $this->verificar_datos_obligatorios_privados( get_current_user_id() ) && !$this->verificar_datos_obligatorios_publicos( get_current_user_id() ) )
					$mensaje = "Tus datos privados han sido guardados. Por favor, accede a tu Perfil Público y finaliza tu ficha pública de socio.";

				$this->send_json_success($mensaje);
			}

			if($operacion == 1){
				$socio = json_decode(stripcslashes($_REQUEST['data']), true);
				$socio["id"] = get_current_user_id();

				unset($socio["foto_socio"]);
				unset($socio["logo_empresa"]);

				if(isset( $socio["nicename"] ) && $socio["nicename"]){
					$nicename = sanitize_title($socio["nicename"]);
					unset($socio["nicename"]);

					$userdata = [
						'ID'            => $user_id,
						'user_nicename' => $nicename,
					];
					$result = wp_update_user( $userdata );
					if ( is_wp_error( $result ) ) {
						$this->send_json_error(
							"No se pudo actualizar tu URL: " . $result->get_error_message(),
							500
						);
					}
				}

				if(isset($socio["fecha_creacion_empresa"])  && $socio["fecha_creacion_empresa"]){
					$fecha = $this->validar_fecha_crud($socio["fecha_creacion_empresa"]);
					if(!$fecha) $this->send_json_error("La fecha de creación de la empresa no es válida.");
					$socio["fecha_creacion_empresa"] = $fecha;
				}

				if($wpdb->update($tabla_socios, $socio, ["id" => $socio['id']]) === false)
				$this->send_json_error("No se ha podido guardar sus datos. Por favor, inténtelo más tarde", 500);

				if(isset($_FILES['foto_socio'])){
					$imagen = $_FILES['foto_socio'];
					if($imagen['error'] !== UPLOAD_ERR_OK) $this->send_json_error("La imagen enviada (foto de socio) tiene errores", 400);
					if(!$this->subir_imagen("foto_socio", $imagen, $tabla_socios, $socio, "foto_socio", "id")) $this->send_json_error( "Ha habido un error al subir la imagen (foto de socio)", 500);
				}
		
				if(isset($_FILES['logo_empresa'])){
					$imagen = $_FILES['logo_empresa'];
					if($imagen['error'] !== UPLOAD_ERR_OK) $this->send_json_error("La imagen enviada (logotipo de empresa) tiene errores", 400);
					if(!$this->subir_imagen("logo_empresa", $imagen, $tabla_socios, $socio, "logo_empresa", "id")) $this->send_json_error( "Ha habido un error al subir la imagen (logotipo de empresa)", 500);
				}

				if( $this->verificar_datos_obligatorios_publicos( get_current_user_id() ) && $this->verificar_datos_obligatorios_privados( get_current_user_id() ) ){
					$wpdb->update($tabla_socios, ["estado_perfil" => "1"], ["id" => $socio['id']]);
				}

				$mensaje = "Tus datos publicos han sido guardados.";
				if( !$this->verificar_datos_obligatorios_publicos( get_current_user_id() ) )
					$mensaje = "Tus datos publicos han sido guardados como borrador. Por favor, finaliza este formulario antes de continuar.";

				$this->send_json_success($mensaje);
			}
			
			$this->send_json_error("Recurso no encontrado.");
		}


		if(isset($_REQUEST['facturas_admin']) && isset($_REQUEST['unik']) && wp_verify_nonce( $_REQUEST['unik'], 'aje_noc' )){
			$operacion = $_REQUEST['facturas_admin'];
			#Listar ordenes
			if($operacion == 0){
				///datatable
				$draw = $_POST['draw'];
				$row = $_POST['start'];
				$rowperpage = $_POST['length']; 
				$columnIndex = $_POST['order'][0]['column'];
				$columnName = $_POST['columns'][$columnIndex]['data'];
				$columnSortOrder = $_POST['order'][0]['dir'];
				$searchValue = esc_sql($_POST['search']['value']);
	
				$s = "";
				$extra = "";

				$order_statuses = [];

				$order_statuses['wc-pending']    = 'Pendiente de pago';
				$order_statuses['wc-processing'] = 'Procesando';
				$order_statuses['wc-sp-enviado']    = 'Enviado';
				$order_statuses['wc-on-hold']    = 'En espera';
				$order_statuses['wc-completed']  = 'Completado';
				$order_statuses['wc-cancelled']  = 'Cancelado';
				$order_statuses['wc-refunded']   = 'Reembolsado';
				$order_statuses['wc-failed']     = 'Fallido';
			

				if($searchValue != ""){

					$statusKey = $searchValue;

					foreach ($order_statuses as $key => $status) {
						if (stripos($status, $searchValue) !== false) {
							$statusKey = $key; 
							break;
						}
					}

					$s = " AND (
						o.id LIKE '%$searchValue%' OR
						o.billing_email LIKE '%$searchValue%' OR
						o.status LIKE '%$statusKey%'
					)";
				}

				$totalRecords = $wpdb->get_var("SELECT count(id) FROM $tabla_ordenes");
				$totalRecords_filter = $wpdb->get_var("SELECT count(id) FROM $tabla_ordenes");

				switch ($columnName) {
					case 'fecha': $extra .= " order by fecha $columnSortOrder limit $row,$rowperpage"; break;
					case 'usuario': $extra .= " order by usuario $columnSortOrder limit $row,$rowperpage"; break;
					case 'numero': $extra .= " order by numero $columnSortOrder limit $row,$rowperpage"; break;
					case 'total': $extra .= " order by total $columnSortOrder limit $row,$rowperpage"; break;
					case 'estado': $extra .= " order by estado $columnSortOrder limit $row,$rowperpage"; break;
					default: $extra .= " order by fecha $columnSortOrder limit $row,$rowperpage"; break;
				}


				$_res = $wpdb->get_results(
					"
					SELECT 
						o.date_created_gmt AS fecha,
						o.total_amount AS total,
						o.status AS estado,
						o.billing_email AS usuario,
						o.type AS tipo,
						o.id AS numero
					FROM 
						$tabla_ordenes AS o
					WHERE 
						1=1
						AND o.status NOT IN ( 'wc-checkout-draft', 'trash', 'auto-draft' )
						AND o.type LIKE 'shop_order'
						$s
						GROUP BY o.id
						$extra
					",
					ARRAY_A
				);

				$orders = [];

				foreach ($_res as $row) {
					// Inicializa el pedido

					$wc_order = wc_get_order($row['numero']);
					$url_factura = $this->obtener_factura($wc_order);
					
					$order = [
						'fecha'    => $row['fecha'],
						'total'    => $row['total'],
						'usuario'    => $row['usuario'],
						'estado'   => isset($order_statuses[$row['estado']]) ? $order_statuses[$row['estado']] : $row['estado'],
						'numero'   => $row['numero'],
						'url_factura'   => $url_factura,
					];

					$orders[] = $order;
				}

				$data =  array(
					"draw" => intval($draw),
						"iTotalRecords" => intval($totalRecords),
						"iTotalDisplayRecords" => intval($totalRecords_filter),
						"aaData" => $orders,
				);
				$this->send_json_success("Completado", $data);
			}
		}



		if(isset($_REQUEST['facturas_socios']) && isset($_REQUEST['unik']) && wp_verify_nonce( $_REQUEST['unik'], 'aje_noc' )){
			$operacion = $_REQUEST['facturas_socios'];
			#Listar ordenes
			if($operacion == 0){
				///datatable
				$draw = $_POST['draw'];
				$row = $_POST['start'];
				$rowperpage = $_POST['length']; 
				$columnIndex = $_POST['order'][0]['column'];
				$columnName = $_POST['columns'][$columnIndex]['data'];
				$columnSortOrder = $_POST['order'][0]['dir'];
				$searchValue = esc_sql($_POST['search']['value']);
	
				$s = "";
				$extra = "";

				$order_statuses = [];

				$order_statuses['wc-pending']    = 'Pendiente de pago';
				$order_statuses['wc-processing'] = 'Procesando';
				$order_statuses['wc-sp-enviado']    = 'Enviado';
				$order_statuses['wc-on-hold']    = 'En espera';
				$order_statuses['wc-completed']  = 'Completado';
				$order_statuses['wc-cancelled']  = 'Cancelado';
				$order_statuses['wc-refunded']   = 'Reembolsado';
				$order_statuses['wc-failed']     = 'Fallido';
			

				if($searchValue != ""){

					$statusKey = $searchValue;

					foreach ($order_statuses as $key => $status) {
						if (stripos($status, $searchValue) !== false) {
							$statusKey = $key; 
							break;
						}
					}

					$s = " AND (
						o.id LIKE '%$searchValue%' OR
						o.billing_email LIKE '%$searchValue%' OR
						o.status LIKE '%$statusKey%' OR
						woim.meta_value LIKE '%$searchValue%'
					)";
				}

				$totalRecords = $wpdb->get_var("SELECT count(id) FROM $tabla_ordenes WHERE customer_id = " . get_current_user_id());
				$totalRecords_filter = $wpdb->get_var("SELECT count(id) FROM $tabla_ordenes WHERE customer_id = " . get_current_user_id());

				switch ($columnName) {
					case 'fecha': $extra .= " order by fecha $columnSortOrder limit $row,$rowperpage"; break;
					case 'numero': $extra .= " order by numero $columnSortOrder limit $row,$rowperpage"; break;
					case 'total': $extra .= " order by total $columnSortOrder limit $row,$rowperpage"; break;
					case 'estado': $extra .= " order by estado $columnSortOrder limit $row,$rowperpage"; break;
					default: $extra .= " order by fecha $columnSortOrder limit $row,$rowperpage"; break;
				}


				$_res = $wpdb->get_results(
					"
					SELECT 
						o.date_created_gmt AS fecha,
						o.total_amount AS total,
						o.status AS estado,
						o.type AS tipo,
						o.id AS numero
					FROM 
						$tabla_ordenes AS o
					WHERE 
						1=1
						AND o.status NOT IN ( 'wc-checkout-draft', 'trash', 'auto-draft' )
						AND o.type LIKE 'shop_order'
						AND o.customer_id = " . get_current_user_id() . "
						$s
						GROUP BY o.id
						$extra
					",
					ARRAY_A
				);


				$orders = [];

				foreach ($_res as $row) {
					// Inicializa el pedido

					$wc_order = wc_get_order($row['numero']);
					$url_factura = $this->obtener_factura($wc_order);
					
					$order = [
						'fecha'    => $row['fecha'],
						'total'    => $row['total'],
						'estado'   => isset($order_statuses[$row['estado']]) ? $order_statuses[$row['estado']] : $row['estado'],
						'numero'   => $row['numero'],
						'url_factura'   => $url_factura,
					];

					$orders[] = $order;
				}

				$data =  array(
					"draw" => intval($draw),
						"iTotalRecords" => intval($totalRecords),
						"iTotalDisplayRecords" => intval($totalRecords_filter),
						"aaData" => $orders,
				);
				$this->send_json_success("Completado", $data);
			}
		}





		



	}

	if(!get_current_user_id()) $this->send_json_error("Acceso denegado.");
	$this->send_json_error("Recurso no encontrado.");