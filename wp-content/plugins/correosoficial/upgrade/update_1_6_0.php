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

$table_name = $wpdb->prefix . 'correos_oficial_configuration';

$datas = array(
	array(
		'name'  => 'GDPR',
		'value' => 0,
		'type'  => 'analitica',
	),
	array(
		'name'  => 'betatester',
		'value' => 0,
		'type'  => 'analitica',
	),
	array(
		'name'  => 'Analitica_date',
		'value' => gmdate('Y-m-d H:i:s'),
		'type'  => 'analitica',
	),
);

foreach ($datas as $data) {
	$name     = $data['name'];
	$value    = $data['value'];
	$dataType = $data['type'];

	// Consulta preparada para evitar inyección en los datos
	$exists = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$table_name} WHERE `name` = %s AND `type` = %s", $name, $dataType));	// phpcs:ignore

	if ( ! $exists ) {
		$wpdb->insert(
			$table_name,
			array(
				'name'  => $name,
				'value' => $value,
				'type'  => $dataType,
			),
			array( '%s', '%s', '%s' )
		);
	}
}
