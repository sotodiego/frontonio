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

// Instalación indices en caso de no existir.
function indexExists( $table, $index_name ) {
	global $wpdb;
	$result = $wpdb->get_results($wpdb->prepare('SHOW INDEX FROM %i WHERE Key_name = %s', $table, $index_name));
	return !empty($result);
}


function createIndexIfNotExists( $table, $index_name, $index_query ) {
	if (!indexExists($table, $index_name)) {
		global $wpdb;
		$wpdb->query($index_query); // phpcs:ignore
	}
}

$indexes = array(
	array(
		'table' => $wpdb->prefix . 'correos_oficial_orders',
		'index_name' => 'idx_correos_oficial_orders_id_order',
		'create_query' => 'CREATE INDEX idx_correos_oficial_orders_id_order ON ' . $wpdb->prefix . 'correos_oficial_orders (id_order)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_orders',
		'index_name' => 'idx_correos_oficial_orders_shipping_number',
		'create_query' => 'CREATE INDEX idx_correos_oficial_orders_shipping_number ON ' . $wpdb->prefix . 'correos_oficial_orders (shipping_number)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_orders',
		'index_name' => 'idx_correos_oficial_orders_date_add',
		'create_query' => 'CREATE INDEX idx_correos_oficial_orders_date_add ON ' . $wpdb->prefix . 'correos_oficial_orders (date_add)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_saved_orders',
		'index_name' => 'idx_correos_oficial_saved_orders_exp_number',
		'create_query' => 'CREATE INDEX idx_correos_oficial_saved_orders_exp_number ON ' . $wpdb->prefix . 'correos_oficial_saved_orders (exp_number)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_saved_orders',
		'index_name' => 'idx_correos_oficial_saved_orders_id_order',
		'create_query' => 'CREATE INDEX idx_correos_oficial_saved_orders_id_order ON ' . $wpdb->prefix . 'correos_oficial_saved_orders (id_order)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_saved_returns',
		'index_name' => 'idx_correos_oficial_saved_returns_exp_number',
		'create_query' => 'CREATE INDEX idx_correos_oficial_retuns_orders_exp_number ON ' . $wpdb->prefix . 'correos_oficial_saved_returns (exp_number)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_saved_returns',
		'index_name' => 'idx_correos_oficial_saved_returns_id_order',
		'create_query' => 'CREATE INDEX idx_correos_oficial_saved_returns_id_order ON ' . $wpdb->prefix . 'correos_oficial_saved_returns (id_order)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_products',
		'index_name' => 'idx_correos_oficial_products_id',
		'create_query' => 'CREATE INDEX idx_correos_oficial_products_id ON ' . $wpdb->prefix . 'correos_oficial_products (id)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_products',
		'index_name' => 'idx_correos_oficial_products_id_carrier',
		'create_query' => 'CREATE INDEX idx_correos_oficial_products_id_carrier ON ' . $wpdb->prefix . 'correos_oficial_products (id_carrier)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_products',
		'index_name' => 'idx_correos_oficial_products_company',
		'create_query' => 'CREATE INDEX idx_correos_oficial_products_company ON ' . $wpdb->prefix . 'correos_oficial_products (company)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_requests',
		'index_name' => 'idx_correos_oficial_requests_id_cart',
		'create_query' => 'CREATE INDEX idx_correos_oficial_requests_id_cart ON ' . $wpdb->prefix . 'correos_oficial_requests (id_cart)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_requests',
		'index_name' => 'idx_correos_oficial_requests_reference_code',
		'create_query' => 'CREATE INDEX idx_correos_oficial_requests_reference_code ON ' . $wpdb->prefix . 'correos_oficial_requests (reference_code)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_senders',
		'index_name' => 'idx_correos_oficial_senders_id',
		'create_query' => 'CREATE INDEX idx_correos_oficial_senders_id ON ' . $wpdb->prefix . 'correos_oficial_senders (id)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_senders',
		'index_name' => 'idx_correos_oficial_senders_sender_default',
		'create_query' => 'CREATE INDEX idx_correos_oficial_senders_sender_default ON ' . $wpdb->prefix . 'correos_oficial_senders (sender_default)',
	),
	array(
		'table' => $wpdb->prefix . 'correos_oficial_pickups_returns',
		'index_name' => 'idx_correos_oficial_pickups_returns_id_order',
		'create_query' => 'CREATE INDEX idx_correos_oficial_pickups_returns_id_order ON ' . $wpdb->prefix . 'correos_oficial_pickups_returns (id_order)',
	),
);

foreach ($indexes as $index) {
	createIndexIfNotExists($index['table'], $index['index_name'], $index['create_query']);
}
