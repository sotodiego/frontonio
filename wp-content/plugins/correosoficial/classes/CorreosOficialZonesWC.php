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

require_once __DIR__ . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';

/**
 * Clase referente a las zonas de Woocommerce.
 */
class CorreosOficialZonesWC extends CorreosOficialDAO {


	public function __construct() {
		parent::__construct();
	}

	/**
	 * Consigue id de zona del pedido
	 */
	public static function getShippingZone( $location_code1, $postcode ) {

		$dao = new CorreosOficialDAO();

		$location_code_ISO = substr($location_code1, 0, 2);

		$where = "WHERE location_code = '$location_code1' GROUP BY zone_id";
		$record = $dao->readRecord('woocommerce_shipping_zone_locations', $where, 'zone_id', true);

		if (!$record) {
			$where = "WHERE location_code = '$location_code_ISO' GROUP BY zone_id";
			$record = $dao->readRecord('woocommerce_shipping_zone_locations', $where, 'zone_id', true);
		}
		if (!$record) {
			$where = "WHERE location_code = '$postcode' GROUP BY zone_id";
			$record = $dao->readRecord('woocommerce_shipping_zone_locations', $where, 'zone_id', true);
		}

		return $record[0]['zone_id'];
	}

	public function getZones( $table ) {
		return $this->getRecords($table, true);
	}

	/**
	 * Solo para WC
	 */
	public function getCarriersByZone( $id_zone, $table ) {
		$sql = 'SELECT instance_id, method_id, is_enabled
                FROM ' . CorreosOficialUtils::getPrefix() . 'woocommerce_shipping_zone_methods wszm LEFT OUTER
                JOIN ' . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products cocp ON cocp.id_carrier = wszm.instance_id
                WHERE zone_id='$id_zone'
                UNION
                SELECT instance_id, method_id, is_enabled FROM " . CorreosOficialUtils::getPrefix() . 'woocommerce_shipping_zone_methods wszm
                LEFT OUTER JOIN ' . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products cocp ON cocp.id_carrier = wszm.instance_id
                WHERE id_carrier IS NULL AND zone_id='$id_zone'";

		return $this->getRecordsWithQuery($sql, true);
	}
}
