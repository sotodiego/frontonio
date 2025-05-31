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

$tableOrders = "{$wpdb->prefix}correos_oficial_saved_orders";
$field = 'weight';

// Comprobar si el campo existe y es de tipo INT
$checkField = $wpdb->get_results($wpdb->prepare('SHOW COLUMNS FROM %i LIKE %s', $tableOrders, $field));

if (!empty($checkField)) {
	$fieldInfo = $checkField[0];
	if (strpos($fieldInfo->Type, 'int') !== false) {
		$wpdb->query($wpdb->prepare('ALTER TABLE %i MODIFY COLUMN %i FLOAT', $tableOrders, $field));
	}
}

// ACTUALIZAR CLASES DE ENVIO EN correos_oficial_shipping_method_rules

$shippingClassTaxonomy = 'product_shipping_class';

// Consulta SQL para obtener las clases de envío de WooCommerce
$shippingClassesWC = $wpdb->get_results($wpdb->prepare("SELECT t.term_id, t.name, t.slug
    FROM {$wpdb->terms} t INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
    WHERE tt.taxonomy = %s", $shippingClassTaxonomy
));

$shippingClassesCorreos = $wpdb->get_results("SELECT `class` FROM {$wpdb->prefix}correos_oficial_shipping_method_rules");

$shippingClassesCorreos = array_map(function ( $row ) {
	return $row->class;
}, $shippingClassesCorreos);

// Función para normalizar las cadenas
function normalizeString( $string ) {
	return preg_replace('/[^A-Za-z0-9]/', '', strtolower($string));
}

// Comparar y actualizar si es necesario
foreach ($shippingClassesWC as $classWC) {
	$classSlugWC = $classWC->slug;
	$normalizedClassSlugWC = normalizeString($classSlugWC);

	$correosSimilarClass = matchWord($normalizedClassSlugWC, $shippingClassesCorreos);

	// Si la clase encontrada es exactamente igual, no hacer nada y continuar
	if ($classWC->term_id == $correosSimilarClass ||
		$correosSimilarClass == 'productswithoutclass' ||
		$correosSimilarClass == 'allproducts') {
		continue;
	}

	// Si no son iguales, actualizar la clase en wp_correos_oficial_shipping_method_rules
	$wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}correos_oficial_shipping_method_rules SET `class` = %s
         WHERE `class` = %s", $classWC->term_id, $correosSimilarClass
	));
}

function matchWord( $normalizedChain, $wordList ) {
	$highestSimilarity = 0;
	$mostSimilarWord = '';

	foreach ($wordList as $word) {
		$normalizedWord = normalizeString($word);
		$similarity = similar_text($normalizedChain, $normalizedWord);

		if ($similarity > $highestSimilarity) {
			$highestSimilarity = $similarity;
			$mostSimilarWord = $word;
		}
	}

	return $mostSimilarWord;
}
