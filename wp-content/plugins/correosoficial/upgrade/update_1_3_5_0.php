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

global $wpdb;

$table_name = "{$wpdb->prefix}correos_oficial_products";

$insert_data = array(
	'id' => 25,
	'name' => 'Paq 24 Oficina Elegida',
	'active' => 0,
	'delay' => 'Envíos con Correos OFICIAL',
	'company' => 'CEX',
	'url' => 'https://s.correosexpress.com/c?n=@',
	'codigoProducto' => '44',
	'id_carrier' => 0,
	'product_type' => 'office',
	'max_packages' => 99,
	'max_weight' => 30,
);

// Comprobar si el registro ya existe en la base de datos
$existing_record = $wpdb->get_row(
	$wpdb->prepare('SELECT * FROM %i WHERE id = %d', $table_name, $insert_data['id'])
);

if (!$existing_record) {
	$wpdb->insert($table_name, $insert_data);
}
