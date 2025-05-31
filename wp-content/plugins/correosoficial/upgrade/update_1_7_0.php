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

$tableSender = "{$wpdb->prefix}correos_oficial_senders";
$tableCodes = "{$wpdb->prefix}correos_oficial_codes";
$toUpdate = array(
	'correos_code' => null,
	'cex_code' => null,
);

$record4 = $wpdb->query($wpdb->prepare('SHOW INDEX FROM %i WHERE Key_name = "company";', $tableCodes));

if ($record4) {
	
	// Eliminamos la clave primaria de Codes
	$wpdb->query($wpdb->prepare('ALTER TABLE %i DROP INDEX company;', $tableCodes));

	// Añadimos columnas a tabla senders
	$senderTableCheck = $wpdb->query($wpdb->prepare('SHOW COLUMNS FROM %i WHERE Field = "correos_code";', $tableSender));
	if (!$senderTableCheck) {
		$wpdb->query(
			$wpdb->prepare('ALTER TABLE %i ADD COLUMN `correos_code` INT(11), ADD COLUMN `cex_code` INT(11) ', $tableSender));
	}
	unset($senderTableCheck);

	// Código de Correos
	$correosCode = $wpdb->get_row($wpdb->prepare(
		'SELECT `id` FROM %i WHERE `company` = "Correos"', $tableCodes), ARRAY_A
	);
	if ($correosCode) {
		$toUpdate['correos_code'] = $correosCode['id'];
		unset($correosCode);
	}

	$cexCode = $wpdb->get_row($wpdb->prepare(
		'SELECT `id` FROM %i WHERE `company` = "CEX"', $tableCodes), ARRAY_A
	);
	if ($cexCode) {
		$toUpdate['cex_code'] = $cexCode['id'];
		unset($cexCode);
	}

	// Obtenemos todos los remitentes
	$senders = $wpdb->get_results($wpdb->prepare('SELECT `id` FROM %i ', $tableSender), ARRAY_A);

	// Actualizamos tabla senders
	foreach ($senders as $sender) {
		$wpdb->update(
			$tableSender,
			array(
				'correos_code' => (int) $toUpdate['correos_code'],
				'cex_code' => (int) $toUpdate['cex_code'],
			),
			array( 'id' => (int) $sender['id'] )
		);
	}
	unset($senders);
}
