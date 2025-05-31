<?php
	include  ERP_FRONTONIO_PATH . "/inc/bd_list.php";
    $num_socios_dados_de_alta = $wpdb->get_var("SELECT count(id) FROM {$tabla_socios} WHERE estado NOT LIKE '%inactivo%'");
	
	$anio = date('Y');
    $num_socios_dados_de_alta_este_anio = $wpdb->get_var("SELECT count(id) FROM {$tabla_socios} WHERE estado NOT LIKE '%inactivo%' AND create_adpn LIKE '%{$anio}%'");

	$fecha_hoy = date('Y-m-d'); 
	$socios_proxima_renovacion = $wpdb->get_results("
		SELECT *
		FROM {$tabla_socios}
		WHERE estado NOT LIKE '%inactivo%'
			AND fecha_renovacion BETWEEN '{$fecha_hoy}' AND DATE_ADD('{$fecha_hoy}', INTERVAL 30 DAY)
		", ARRAY_A);
	?>




<div class="pt-0 card_dashboard_container">
	<div class="pt-0 card_dashboard_container">
		<div class="row">
			<div class="col s12 m4  card_dashboard_info">
				<div class="card margin_top_bottom gradient-shadow min-height-100 black-text animate fadeLeft" href>
					<div class="padding-4 content-info-card-panel">
						<div class="row">
							<div class="col s5">
								<i class="medium material-icons background-round icon_dashboard">person</i>
							</div>
							<div class="col s7 right-align">
								<p class="no-margin tittle_panel_card">Socios dados de alta (total)</p>
								<h5 class="mb-0"><?php echo ($num_socios_dados_de_alta);?></h5>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col s12 m4  card_dashboard_info">
				<div class="card margin_top_bottom gradient-shadow min-height-100 black-text animate fadeLeft ">
					<div class="padding-4 content-info-card-panel">
						<div class="row">
							<div class="col s5">
								<i class="medium material-icons background-round icon_dashboard">person_add</i>
							</div>
							<div class="col s7 right-align">
								<p class="no-margin tittle_panel_card">Socios dados de alta (este año)</p>
								<h5 class="mb-0"><?php echo ($num_socios_dados_de_alta_este_anio);?></h5>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col s12 m4  card_dashboard_info">
				<div class="card margin_top_bottom gradient-shadow min-height-100 black-text animate fadeLeft ">
					<div class="padding-4 content-info-card-panel">
						<div class="row">
							<div class="col s5">
								<i class="medium material-icons background-round icon_dashboard">priority_high</i>
							</div>
							<div class="col s7 right-align">
								<p class="no-margin tittle_panel_card">Socios pendientes de renovar</p>
								<h5 class="mb-0"><?php echo count($socios_proxima_renovacion);?></h5>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

		<div class="row">
			<!-- Reservas hoy -->
			<div class="col s12 l6">
				<div class="card margin_top_bottom recent-buyers-card animate fadeUp">
					<div class="card-content">
						<h4 class="card-title mb-0 tittle_panel_card">Socios pendientes de renovar</h4>
						<?php if(!empty($socios_proxima_renovacion)){ ?>
						<ul class="collection mb-0">
						<?php foreach($socios_proxima_renovacion as $socio){ ?>
							<li class="collection-item list-product content_info">
								<div class="row width-100">
									<?php echo $socio["id"] . " | " . $socio["nombre"] . " " . $socio["apellidos"] . " | " . $socio["nombre_empresa"];?>
								</div>
							</li>
						<?php } ?>
						</ul>
						<?php }else{ ?>
							<p class="mt-5">No hay socios pendientes de renovar</p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>


