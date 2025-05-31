<?php
/**
 * This program is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see https://www.gnu.org/licenses/.
 */

if (!defined('WPINC')) {
	die;
}

global $wpdb;

// $query = "SELECT count(*) as c FROM {$wpdb->prefix}correos_oficial_products WHERE (id='26' OR id='27');";

if ($wpdb->get_var($wpdb->prepare("SELECT count(*) as c FROM {$wpdb->prefix}correos_oficial_products WHERE (id='26' OR id='27');")) == 0) {
		// campo sender_phone ahora permite NULL
		$tableSenders = "{$wpdb->prefix}correos_oficial_senders";
		$wpdb->query($wpdb->prepare(
			'ALTER TABLE %i MODIFY `sender_phone` varchar(14) NULL', $tableSenders));

		// insertamos dos nuevos productos
		$tableProducts = "{$wpdb->prefix}correos_oficial_products";
		$wpdb->query($wpdb->prepare(
			"INSERT INTO %i (id, name, active, delay, company, url, codigoProducto, id_carrier, product_type, max_packages, max_weight) VALUES
			(26, 'Carta Certificada Internacional', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0004', 0, 'international', 1, 2),
			(27, 'Paquete Postal Económico Internacional', 0, 'Envíos con Correos OFICIAL', 'Correos', 'https://www.correos.es/es/es/herramientas/localizador/envios/detalle?tracking-number=@', 'S0031', 0, 'international', 1, 2);"
			, $tableProducts));
}
