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
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialSendersDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/functions.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminCorreosOficialSendersProcessController {


	private $sender_dao;

	public function __construct() {
		$this->sender_dao = new CorreosOficialSendersDao();

		$action = sanitize_text_field(isset($_REQUEST['action']) ? $_REQUEST['action'] : '');

		$sender_from_time = Normalization::normalizeData('sender_from_time');
		$sender_to_time = Normalization::normalizeData('sender_to_time');

		$correos_code = Normalization::normalizeData('correos_code');
		$cex_code = Normalization::normalizeData('cex_code');

		switch ($action) {
			case 'CorreosSendersInsertForm':
				$fields = array(
				'sender_name' => Normalization::normalizeData('sender_name'),
				'sender_address' => Normalization::normalizeData('sender_address'),
				'sender_cp' => Normalization::normalizeData('sender_cp'),
				'sender_nif_cif' => Normalization::normalizeData('sender_nif_cif'),
				'sender_city' => Normalization::normalizeData('sender_city'),
				'sender_contact' => Normalization::normalizeData('sender_contact'),
				'sender_phone' => Normalization::normalizeData('sender_phone'),
				'sender_from_time'     => $sender_from_time != '' ? $sender_from_time : '00:00',
				'sender_to_time'       => $sender_to_time != '' ? $sender_to_time : '00:00',
				'sender_iso_code_pais' => Normalization::normalizeData('sender_iso_code_pais'),
				'sender_email' => Normalization::normalizeData('sender_email', 'email'),
				'sender_default' => '0',
				'correos_code'         => $correos_code != '' ? $correos_code : 0,
				'cex_code'             => $cex_code != '' ? $cex_code : 0,
				);

				$this->sender_dao->insertFieldsSetRecord($fields);
				break;

			case 'CorreosSendersUpdateForm':
				$fields = array(
				'id' => Normalization::normalizeData('sender_id'),
				'sender_name' => Normalization::normalizeData('sender_name'),
				'sender_address' => Normalization::normalizeData('sender_address'),
				'sender_cp' => Normalization::normalizeData('sender_cp'),
				'sender_nif_cif' => Normalization::normalizeData('sender_nif_cif'),
				'sender_city' => Normalization::normalizeData('sender_city'),
				'sender_contact' => Normalization::normalizeData('sender_contact'),
				'sender_phone' => Normalization::normalizeData('sender_phone'),
				'sender_from_time'     => $sender_from_time != '' ? $sender_from_time : '00:00',
				'sender_to_time'       => $sender_to_time != '' ? $sender_to_time : '00:00',
				'sender_iso_code_pais' => Normalization::normalizeData('sender_iso_code_pais'),
				'sender_email' => Normalization::normalizeData('sender_email', 'email'),
				'correos_code'         => $correos_code != '' ? $correos_code : 0,
				'cex_code'             => $cex_code != '' ? $cex_code : 0,
				);

				$this->sender_dao->updateFieldsSetRecord($fields);
				break;

			case 'CorreosSenderSaveDefaultForm':
				$sender_default_id = Normalization::normalizeData('sender_default_id');
				$this->sender_dao->updateFieldSetRecord($sender_default_id);
				break;

			case 'CorreosSendersDeleteForm':
				$sender_id = Normalization::normalizeData('sender_id');
				$this->sender_dao->deleteFieldsSetRecord($sender_id);
				break;
			default:
				die( 'ERROR 11010: Action no válido' );
		}

		// Actualizamos analitica
		( new Analitica() )->configurationCall('undefined');
	}
}
