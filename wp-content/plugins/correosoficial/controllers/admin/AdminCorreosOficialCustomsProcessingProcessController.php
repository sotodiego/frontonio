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
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialCustomsProcessingDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/functions.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/CorreosOficialUtils.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminCorreosOficialCustomsProcessingProcessController {


	private $custom_processing_dao;

	public function __construct() {
		$this->getCustomFormData();
	}

	public function getCustomFormData() {
		// Obtenemos un objetoDao
		$this->custom_processing_dao = new CorreosOficialCustomsProcessingDao();

		// Obtenemos campos de los formularios
		$DefaultCustomsDescription = Normalization::normalizeData('DefaultCustomsDescription');
		$TranslatableInput = Normalization::normalizeData('TranslatableInput');
		$FormSwitchLanguage = Normalization::normalizeData('FormSwitchLanguage');
		$Tariff = Normalization::normalizeData('Tariff');
		$TariffDescription = Normalization::normalizeData('TariffDescription');
		$MessageToWarnBuyer = Normalization::normalizeData('MessageToWarnBuyer');
		$CustomsDesriptionAndTariff = Normalization::normalizeData('CustomsDesriptionAndTariff');
		$ShippCustomsReference = Normalization::normalizeData('ShippCustomsReference');

		// Traducción de los campos.
		$string_from_db = $this->custom_processing_dao->getField('TranslatableInput');
		$TranslatableInput = CorreosOficialUtils::translateStringsToDB(
			$string_from_db->value,
			$FormSwitchLanguage,
			$TranslatableInput
		);

		// Los metemos en un array
		$fields = array(
			'DefaultCustomsDescription' => $DefaultCustomsDescription,
			'TranslatableInput' => $TranslatableInput,
			'FormSwitchLanguage' => $FormSwitchLanguage,
			'Tariff' => $Tariff,
			'TariffDescription' => $TariffDescription,
			'MessageToWarnBuyer' => $MessageToWarnBuyer,
			'ShippCustomsReference' => $ShippCustomsReference,

		);

		if ($CustomsDesriptionAndTariff[0] == 0) {
			$fields['DescriptionRadio'] = 'on';
			$fields['TariffRadio'] = '';
		} else if ($CustomsDesriptionAndTariff[0] == 1) {
			$fields['TariffRadio'] = 'on';
			$fields['DescriptionRadio'] = '';
		}

		// Clave del registro
		$this->custom_processing_dao->updateFieldsSetRecord($fields);
	}
}
