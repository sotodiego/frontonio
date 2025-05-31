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

$tableConfiguration = "{$wpdb->prefix}correos_oficial_configuration";
$tableOrders = "{$wpdb->prefix}correos_oficial_orders";
$tableSavedOrders = "{$wpdb->prefix}correos_oficial_saved_orders";



// Comprobamos que los registros necesarios existen en configuration
$checkConfiguration = $wpdb->get_row($wpdb->prepare(
	'SELECT `value` FROM %i WHERE `name` = "ActivateDimensionsByDefault"', $tableConfiguration)
);

if (null == $checkConfiguration) {
	$wpdb->query($wpdb->prepare(
		"INSERT INTO %i (name, value, type) VALUES
		('ActivateDimensionsByDefault', '', 'checkbox'),
		('DimensionsByDefaultHeight', '0', 'number'),
		('DimensionsByDefaultLarge', '0', 'number'),
		('DimensionsByDefaultWidth', '0', 'number');", $tableConfiguration));
}

// Comprobamos los nuevos campos en la tabla orders
$fieldsToCheckOrders = array(
	'added_values_cash_on_delivery' => "int(1) DEFAULT '0'",
	'added_values_insurance' => "int(1) DEFAULT '0'",
	'added_values_partial_delivery' => "int(1) DEFAULT '0'",
	'added_values_delivery_saturday' => "int(1) DEFAULT '0'",
);

foreach ($fieldsToCheckOrders as $key => $value) {
	$checkField = $wpdb->query($wpdb->prepare(
		'SHOW COLUMNS FROM %i WHERE Field = %s' , $tableOrders, $key
	));
	if (!$checkField) {
		$wpdb->query($wpdb->prepare(
			"ALTER TABLE %i ADD COLUMN %i int(1) DEFAULT '0'", $tableOrders, $key));
	}
}

$checkField = $wpdb->query($wpdb->prepare(
	"SHOW COLUMNS FROM %i WHERE Field = 'added_values_cash_on_delivery_iban'" , $tableOrders
));
if (!$checkField) {
	$wpdb->query($wpdb->prepare(
		'ALTER TABLE %i ADD COLUMN `added_values_cash_on_delivery_iban` varchar(50) DEFAULT NULL', $tableOrders));
}

$fieldsToCheckOrders = array(
	'added_values_cash_on_delivery_value' => 'FLOAT DEFAULT NULL',
	'added_values_insurance_value' => 'FLOAT DEFAULT NULL',
);

foreach ($fieldsToCheckOrders as $key => $value) {
	$checkField = $wpdb->query($wpdb->prepare(
		'SHOW COLUMNS FROM %i WHERE Field = %s' , $tableOrders, $key
	));
	if (!$checkField) {
		$wpdb->query($wpdb->prepare(
			'ALTER TABLE %i ADD COLUMN %i FLOAT DEFAULT NULL', $tableOrders, $key));
	}
}

// Comprobamos los nuevos campos en la tabla saved orders
$fieldsToCheckSavedOrders = array(
	'height' => 'int(11) DEFAULT NULL',
	'width' => 'int(11) DEFAULT NULL',
	'large' => 'int(11) DEFAULT NULL',
	'weight' => 'int(11) DEFAULT NULL',
);

foreach ($fieldsToCheckSavedOrders as $key => $value) {
	$checkField = $wpdb->query($wpdb->prepare(
		'SHOW COLUMNS FROM %i WHERE Field = %s' , $tableSavedOrders, $key
	));
	if (!$checkField) {
		$wpdb->query($wpdb->prepare(
			'ALTER TABLE %i ADD COLUMN %i int(11) DEFAULT NULL', $tableSavedOrders, $key));
	}
}

$fieldsToCheckSavedOrders = array(
	'reference' => 'varchar(100) DEFAULT NULL',
	'observations' => 'varchar(100) DEFAULT NULL',
);

foreach ($fieldsToCheckSavedOrders as $key => $value) {
	$checkField = $wpdb->query($wpdb->prepare(
		'SHOW COLUMNS FROM %i WHERE Field = %s' , $tableSavedOrders, $key
	));
	if (!$checkField) {
		$wpdb->query($wpdb->prepare(
			'ALTER TABLE %i ADD COLUMN %i varchar(100) DEFAULT NULL', $tableSavedOrders, $key));
	}
}
