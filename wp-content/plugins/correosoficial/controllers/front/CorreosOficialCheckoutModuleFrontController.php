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

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Correos/CorreosSoap.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class CorreosOficialCheckoutModuleFrontController {

	public $cart;

	public function __construct( $action ) {
		$this->initContent($action);
	}

	public function initContent( $action ) {

		switch ($action) {

			case 'SearchCityPaqByPostalCode':
				$postcode = Normalization::normalizeData('postcode');
				$correos_soap = new CorreosSoap();
				$correos_soap->homePaqConsultaCP1($postcode);
				break;
			case 'SearchOfficeByPostalCode':
				$postcode = Normalization::normalizeData('postcode');
				$correos_soap = new CorreosSoap();
				$correos_soap->localizadorConsulta($postcode);
				break;
			// Los insert Citypaq y Office no aplican(se hace con el action woocommerce_checkout_order_created)
			case 'insertCityPaq':
			case 'insertOffice':
				break;
			default:
				throw new LogicException('Error 21000: No se ha indicado un "action" para el formulario.');
		}
	}
}
