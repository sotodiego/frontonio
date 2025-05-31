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

if (!defined('WC_VERSION')) {
	die;
}

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/config.inc.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialZonesCarriersDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/functions.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminCorreosOficialZonesCarriersProcessController {

	public $module;

	public function __construct() {

		$dao = new CorreosOficialDAO();

		if (isset($_POST['_nonce'])) {
			$nonce = sanitize_text_field($_POST['_nonce']);
			if (!wp_verify_nonce(wp_unslash($nonce), 'correosoficial_nonce')) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wp_send_json_error( 'bad_nonce' );
				wp_die();
			}
		}

		$dispatcherData = isset($_POST['dispatcher']) ?
			array_map('sanitize_text_field', (array) $_POST['dispatcher']) : array();

		if ($dispatcherData == null) {
			return;
		}

		$formDataArray = array();
		$formDataArray = $dispatcherData;

		foreach ($formDataArray as $input => $value) {

			if ($input == 'controller') {
				continue;
			}

			$id_product = Normalization::normalizeData($value, 'value');
			$data_explode_from_input = explode('_', $input);
			$id_zone = Normalization::normalizeData($data_explode_from_input[1], 'value');
			$id_carrier = Normalization::normalizeData($data_explode_from_input[2], 'value');

			if (!empty($id_product)) {
				$dao->updateCarrierProduct($id_product, $id_zone, $id_carrier);
			} else {
				$dao->deleteCarrierProductsById($id_carrier, $id_zone);
			}
		}

		// Asignación automática del transportista en relación con Channable

		$configuration_dao  = new CorreosOficialUserConfigurationDao();
		$carrierName_ant    = CorreosOficialConfigDao::getConfigValue('AutomaticProductAssignmentText');
		$productId_ant      = CorreosOficialConfigDao::getConfigValue('AutomaticProductAssignmentProduct');
		
		$carrierName    = sanitize_text_field(isset($_POST['dispatcher']['AutomaticProductAssignmentText']) ? $_POST['dispatcher']['AutomaticProductAssignmentText'] : '');
		$productId      = sanitize_text_field(isset($_POST['dispatcher']['AutomaticProductAssignmentProduct']) ? $_POST['dispatcher']['AutomaticProductAssignmentProduct'] : '');

		$configuration_dao->createSettingRecord('AutomaticProductAssignmentText', $carrierName, 'correos_oficial_configuration');
		$configuration_dao->createSettingRecord('AutomaticProductAssignmentProduct', $productId, 'correos_oficial_configuration');
		
		$objProduct     = new CorreosOficialProductsDao();
		$product        = $objProduct->getProduct($productId, 'correos_oficial_products');
		$productName    = $product[0]->name;

		//guardamos en el log
		if ( ( $carrierName_ant<>$carrierName ) || ( $productId_ant<>$productId ) ) {
			$filename = WP_PLUGIN_DIR . '/correosoficial/log/log_automatic_product_assignment.txt';
			file_put_contents($filename,
				gmdate('Y-m-d H:i:s') . " Se ha modificado la asignación del transportista de origen '{$carrierName}' al producto '{$productName}'\r\n",
				FILE_APPEND
			);
		}
	}
}
