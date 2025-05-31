<?php

	if(isset($_REQUEST['crud_adnsy']) && isset($_REQUEST['crud_unik']) && wp_verify_nonce( $_REQUEST['crud_unik'], $_REQUEST['crud'] )){
		global $wpdb;
		$operacion = $_REQUEST['crud_adnsy'];
		$crud = $_REQUEST['crud'];
		$archivo = ADPNSY_CRUD . "/{$crud}.json";
		$contenido = file_get_contents($archivo);
	    $data_crud = json_decode($contenido, true);
	    $db = $wpdb->prefix.$data_crud['data_base'];
	    $key = $data_crud["key"];
	    if (json_last_error() !== JSON_ERROR_NONE) $this->send_json_error("Error al decodificar el archivo JSON de este CRUD: " . json_last_error_msg());

	    #Extra Ajax
		if(file_exists(ADPNSY_CRUD . "/{$crud}.php")){
			include ADPNSY_CRUD . "/{$crud}.php";
		}

	    #Listar crud
		if ($operacion == 0) {
		    // datatable
		    $draw = $_POST['draw'];
		    $row = $_POST['start'];
		    $rowperpage = $_POST['length'];
		    $columnIndex = $_POST['order'][0]['column'];
		    $columnName = $_POST['columns'][$columnIndex]['data'];
		    $columnSortOrder = $_POST['order'][0]['dir'];
		    $searchValue = esc_sql($_POST['search']['value']);

			$_t = [];
		    $_s = "";
		    $_l = "";
			$_f = "";

		    $extra = " ORDER BY {$columnName} {$columnSortOrder} LIMIT %d, %d";

		    if ($searchValue != "") {
		        $list_search = [];
		        $special_search = [];

				$column_types = [];
				$columns_info = $wpdb->get_results("SHOW COLUMNS FROM {$db}", ARRAY_A);
				foreach ($columns_info as $col_info) {
					$column_types[$col_info['Field']] = $col_info['Type'];
				}

		        foreach ($data_crud["columns"] as $_id => $columna) {
					if (isset($columna["db"]) && $columna["search"] == true) {
						if (isset($columna["search_rule"]) && $columna["search_rule"]) {
							$special_search[] = str_replace(["__ID__", "__DATA__", "__BD__"], [$_id, $searchValue, $wpdb->prefix], $columna["search_rule"]);
						} else {
							// Detectar si el tipo es numérico y hacer CAST si es necesario
							$type = $column_types[$_id] ?? '';
							if (preg_match('/^(int|bigint|smallint|decimal|float|double)/', $type)) {
								$list_search[] = "CAST({$_id} AS CHAR)";
							} else {
								$list_search[] = $_id;
							}
						}
					}
				}
				
		        if (!empty($list_search) && !empty($special_search)) {
		        	$_s = " AND (" . implode(" LIKE '%" . $searchValue . "%' OR ", $list_search) . " LIKE '%" . $searchValue . "%'";
		        	$_s .= " OR " . implode(" OR ", $special_search) . ")";
		        }elseif (!empty($list_search)) {
		            $_s = " AND (" . implode(" LIKE '%" . $searchValue . "%' OR ", $list_search) . " LIKE '%" . $searchValue . "%')";
		        }elseif (!empty($special_search)) {
		        	$_s = " AND (" . implode(" OR ", $special_search) . ($_s ? "" : ")");
		        }
		    }

			if(isset($_POST['filtros'])){
				$current_filtros = $_POST['filtros'];
				// Convert keys with '|' into multidimensional arrays
				foreach ($current_filtros as $k => $v) {
					if (strpos($k, '|') !== false) {
						list($main, $sub) = explode('|', $k, 2);
						if (!isset($current_filtros[$main]) || !is_array($current_filtros[$main])) {
							$current_filtros[$main] = [];
						}
						$current_filtros[$main][$sub] = $v;
						unset($current_filtros[$k]);
					}
				}
				
				foreach ($data_crud["columns"] as $_id => $_col) {
					#verifica si tiene el campo
					if (!isset($current_filtros[$_id]) || !isset($_col["filter"]))
						continue;
					#verifica si lo puede usar
					if($_col["filter"] !== true && !is_array($_col["filter"]) && !array_intersect(wp_get_current_user()->roles, $_col["filter"]))
						continue;
					#aplica el filtro
					if ($_col["filter_type"] === 'date_range') {
						if(isset($current_filtros[$_id]['ini'])){
							$dateObj = DateTime::createFromFormat('d/m/Y', $current_filtros[$_id]['ini']);
							if ($dateObj) {
								$_f .= " AND DATE({$_id}) >= '" . $dateObj->format('Y-m-d') . "'";
							}
						}
						if(isset($current_filtros[$_id]['end'])){
							$dateObj = DateTime::createFromFormat('d/m/Y', $current_filtros[$_id]['end']);
							if ($dateObj) {
								$_f.= " AND DATE({$_id}) <= '". $dateObj->format('Y-m-d'). "'";
							}
						}
					}elseif ($_col["filter_type"] === 'date') {
						$dateObj = DateTime::createFromFormat('d/m/Y', $current_filtros[$_id]);
						if ($dateObj) {
							$_f.= " AND DATE({$_id}) = '". $dateObj->format('Y-m-d'). "'";
						}
					} else {
						$_f .= " AND {$_id} = '" . $current_filtros[$_id] . "'";
					}
				}
			}

		    if(isset($data_crud["limit"]) && isset($data_crud["limit"]["sql"])){
		    	if(isset($data_crud["limit"]["rol"])){
		    		$user = wp_get_current_user();
				    $current_roles = $user->roles;
				    $has_permission = array_intersect($data_crud["limit"]["rol"], $current_roles);
				    if($has_permission){
				    	$_l = " AND " . str_replace(["__CURRENT_USER_ID__", "__BD__"], [get_current_user_id(), $wpdb->prefix], $data_crud["limit"]["sql"]);
				    }
		    	}else{
		    		$_l = " AND " . str_replace(["__CURRENT_USER_ID__", "__BD__"], [get_current_user_id(), $wpdb->prefix], $data_crud["limit"]["sql"]);
		    	}
		    }

		    // Reemplazar LIMIT en la consulta utilizando prepare
		    $extra_prepared = $wpdb->prepare($extra, $row, $rowperpage);

		    $totalRecords = $wpdb->get_var("SELECT count(*) FROM {$db} WHERE 1 = 1 {$_l} {$_f}");
		    $totalRecordwithFilter = $wpdb->get_var("SELECT count(*) FROM {$db} WHERE 1 = 1 {$_s} {$_f} {$_l}");

		    // Consulta preparada final
		    $consulta = "SELECT * FROM {$db} WHERE 1 = 1 {$_s} {$_l} {$_f} {$extra_prepared}";
		    $_res = $wpdb->get_results($consulta, ARRAY_A);

		    foreach ($data_crud["columns"] as $_id => $_col) {
		        if (isset($_col["render"]) && isset($_col["render_option"])) {
		            $elements = [];
		            foreach ($_col["render_option"] as $k => $v) {
						if (strpos($k, "data_bd") !== false) {
		                    $elements[] = $wpdb->prefix . $v;
		                } else {
		                    $elements[] = "$k = '_{$v}_'";
		                }
		            }
		            foreach ($_res as $key_val => $val) {
		                $elements_temp = $elements;
		                foreach ($elements_temp as &$element) {
		                    $element = preg_replace_callback(
		                        '/_(\w+)_/',
		                        function ($matches) use ($val) {
		                            $key_m = $matches[1];
		                            return isset($val[$key_m]) ? $val[$key_m] : $matches[0];
		                        },
		                        $element
		                    );
		                }
		                unset($element);

		                $consulta_final = vsprintf($_col["render"], $elements_temp);
		                $val[$_id] = $wpdb->get_var($consulta_final);
		                $val[$_id."|sql"] = $consulta_final;
		                $_res[$key_val] = $val;
		            }
		        }
		    }

			if(isset($data_crud["capability"]["totales"]) && is_array($data_crud["capability"]["totales"])){
				foreach ($data_crud["capability"]["totales"] as $type => $dts) {
					$_sql = str_replace(["__PREFIX__", "__BD__", "__WHERE__"], [$wpdb->prefix, $db, "1 = 1 {$_f} {$_l}"], $dts["sql"]);
					$_t[$type] = $wpdb->get_var($_sql);
				}
			}

		    $data = array(
		        "draw" => intval($draw),
		        "iTotalRecords" => intval($totalRecords),
		        "iTotalDisplayRecords" => intval($totalRecordwithFilter),
		        "aaData" => $_res,
		        "_q" => $consulta,
				"_t" => $_t
		    );

		    $this->send_json_success("Completado", $data);
		}

		#Crear o editar registro
		if ($operacion == 1) {
			$elemento = json_decode(stripcslashes($_REQUEST['_data']), true);
			$nuevo = ($_REQUEST['_create'] == 1) ? true : false;

			#veifica los unicos y reglas
			foreach ($data_crud["columns"] as $_id => $columna) {
				if(isset($columna["save_rule"])){
					if(is_array($columna["save_rule"])){
						foreach($columna["save_rule"] as $rule => $parent){
							if($rule == 'sanitize_title') $elemento[$_id] = sanitize_title($elemento[$parent]);
							elseif (method_exists($this, $rule)){
								if(is_array($parent)){
									$args = array_map(fn($key) => $elemento[$key] ?? null, $parent);
									$elemento[$_id] = call_user_func_array([$this, $rule], $args);
								}else{
									$elemento[$_id] = $this->$rule($parent);
								}
							} 
						}
					}elseif($columna["save_rule"] == "time"){
						$elemento[$_id] = time();
					}
				}
				if(isset($columna["unik"]) && $columna["unik"] == true){
					$existe = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT {$key} FROM {$db} WHERE {$_id} = %s",
							$elemento[$_id]
						)
					);
					if($nuevo && $existe) $this->send_json_error( sprintf("Ya existe un registro con '%s' como '%s'", $elemento[$_id], $columna["name"]), 329);
					elseif(!$nuevo && $existe && $existe != $elemento[$key]) $this->send_json_error( sprintf("Ya existe un registro con '%s' como '%s'", $elemento[$_id], $columna["name"]), 329);
				}
			}

			#crear registro
			if($nuevo && $wpdb->insert($db, $elemento) === false)
				$this->send_json_error( "Lo siento, no se ha podido crear el regsitro", 500);

			#actualizar el regsitros
			if(!$nuevo && $wpdb->update($db, $elemento, [$key => $elemento[$key]]) === false)
				$this->send_json_error( "Lo siento, no se ha podido editar el regsitro", 500);


			///Aqui controlar las imagenes
			

			///Lista ajax
			$list = [];
			foreach($data_crud["columns"] as $_id => $columna){
				if($columna['tipo'] == 2 && isset($columna['option_list'])){
					$query = $columna['option_list'];
	        		if(isset($columna['option_list_render'])){
	        			foreach($columna['option_list_render'] as $_key => $_data){
	        				if(strpos($_key, "BD")) $query = str_replace($_key, $wpdb->prefix . $_data, $query);
	        				else $query = str_replace($_key, $_data, $query);
	        			}
	        		}
	        		$dts = $wpdb->get_results($query);
	        		if(isset($columna['option'])) $list[$_id] = $columna['option'] + $this->modo_list($dts);
	        		else $list[$_id] = $this->modo_list($dts);
				}
			}
			///verificar su ahy render

			$this->send_json_success("Registro creado", ["list" => $list]);
		}

		#enviar un registro
		if($operacion == 2){
			$id = $_REQUEST['_id'];
			$col = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$db} WHERE {$key} = %s",
					$id
				)
			);
			if($col) $this->send_json_success("Registro encontrado", ["data" => $col]);
			$this->send_json_error("Registro no encontrado", 500);
		}

		#borrar registro
		if($operacion == 3){
			$id = $_REQUEST['_id'];
			if($wpdb->delete($db, [$key => $id]) !== false) $this->send_json_success("Registro eliminado");
			$this->send_json_error("Error al eliminar el registro intente mas tarde", 500);
		}
	}

if(!get_current_user_id()) $this->send_json_error("Acceso denegado");
$this->send_json_error("Recurso inexistente");