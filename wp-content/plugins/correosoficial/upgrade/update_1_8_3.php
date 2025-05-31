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

// añadimos campo manifest_date en wp_correos_oficial_orders
$tableOrders = "{$wpdb->prefix}correos_oficial_orders";
$field = 'manifest_date';

$checkField = $wpdb->query($wpdb->prepare('SHOW COLUMNS FROM %i WHERE FIELD = %s', $tableOrders, $field));

if (!$checkField) {
	$wpdb->query($wpdb->prepare('ALTER TABLE %i ADD COLUMN %i datetime DEFAULT NULL', $tableOrders, $field));
}
