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
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialCustomerDataDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/functions.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminCorreosOficialCustomerDataProcessController extends CorreosOficialCustomerDataDao {

	private $dao;

	public function __construct() {
		// Obtenemos un objetoDao
		$this->dao = new CorreosOficialCustomerDataDao();

		parent::__construct();

		$action = sanitize_text_field(isset($_REQUEST['action']) ? $_REQUEST['action'] : null);
		$operation = sanitize_text_field(isset($_REQUEST['operation']) ? $_REQUEST['operation'] : null);

		if ($operation === 'CorreosCustomerDataForm') {
			$this->getCorreosData();
		} elseif ($operation === 'CEXCustomerDataForm') {
			$this->getCEXData();
		} elseif ($action === 'DeleteCustomerCode') {
			$CorreosOficialCustomerCode = Normalization::normalizeData('CorreosOficialCustomerCode');
			$this->deleteCustomerCode($CorreosOficialCustomerCode);
		} elseif ($action === 'getDataTableCustomerList') {
			$this->getDataTableCustomerList();
		} elseif ($action === 'getCustomerCode') {
			$this->getCode();
		} elseif ($action === 'getCustomerCodes') {
			$this->getCodes();
		} else {
			throw new LogicException('ERROR CORREOS OFICIAL 10508: No se ha indicado un "action" para el formulario.');
		}
	}

	public function getCorreosData() {
		// Obtenemos campos de los formularios
		$idCorreos = Normalization::normalizeData('idCorreos');
		$CorreosContract = Normalization::normalizeData('CorreosContract');
		$CorreosCustomer = Normalization::normalizeData('CorreosCustomer');
		$CorreosKey = Normalization::normalizeData('CorreosKey');
		$CorreosUser = Normalization::normalizeData('CorreosUser', 'user');
		$CorreosPassword = Normalization::normalizeData('CorreosPassword', 'password');
		$CorreosOv2Code = Normalization::normalizeData('CorreosOv2Code', 'email');
		$Company = Normalization::normalizeData('CorreosCompany');

		// Los metemos en un array
		$fields = array(
			// Correos
			'idCorreos' => $idCorreos,
			'CorreosContract' => $CorreosContract,
			'CorreosCustomer' => $CorreosCustomer,
			'CorreosKey' => $CorreosKey,
			'CorreosUser' => $CorreosUser,
			'CorreosPassword' => $CorreosPassword,
			'CorreosOv2Code' => $CorreosOv2Code,
			'Company' => $Company,

			'CEXCustomer' => 'n/a',
			'CEXUser' => 'n/a',
			'CEXPassword' => 'n/a',
		);

		( new Analitica() )->configurationCall('undefined');

		$result = $this->addCustomerCode($CorreosCustomer, $Company, $fields);
		$this->badResponse($result);
	}

	public function getCEXData() {
		$idCEX = Normalization::normalizeData('idCEX');
		$CEXCustomer = Normalization::normalizeData('CEXCustomer');
		$CEXUser = Normalization::normalizeData('CEXUser', 'user');
		$CEXPassword = Normalization::normalizeData('CEXPassword', 'password');
		$Company = Normalization::normalizeData('CEXCompany');

		$fields = array(
			'idCEX' => $idCEX,
			'CEXCustomer' => $CEXCustomer,
			'CEXUser' => $CEXUser,
			'CEXPassword' => $CEXPassword,
			'Company' => $Company,

			'CorreosContract' => 'n/a',
			'CorreosCustomer' => 'n/a',
			'CorreosKey' => 'n/a',
			'CorreosUser' => 'n/a',
			'CorreosPassword' => 'n/a',
			'CorreosOv2Code' => 'n/a',
		);

		( new Analitica() )->configurationCall('undefined');
		
		$result = $this->addCustomerCode($CEXCustomer, $Company, $fields);
		$this->badResponse($result);
	}

	// Obtenemos información de un contrato (AJAX)
	public function getCode() {
		$id = Normalization::normalizeData('id');
		$code = $this->dao->readRecord('correos_oficial_codes', 'WHERE id=' . $id . ' LIMIT 1');

		// si tenemos resultados obtenemos el primer registro
		if ($code) {
			die(json_encode($code[0]));
		} else {
			die(json_encode(array()));
		}
	}

	// Obtenemos los contratos (AJAX)
	public function getCodes() {

		$optionsCountsCorreos = $this->dao->readRecord(
			'correos_oficial_codes',
			"WHERE company='CORREOS'",
			'`id`, `CorreosContract`, `CorreosCustomer`',
			true
		);

		$optionsCountsCex = $this->dao->readRecord(
			'correos_oficial_codes',
			"WHERE company='CEX'",
			'`id`, `CEXCustomer`',
			true
		);

		// Componemos array de contratos
		$contracts = array(
			'correos' => $optionsCountsCorreos,
			'cex' => $optionsCountsCex,
		);

		die(json_encode($contracts));
	}

	public function badResponse( $result ) {
		if (!$result) {
			$mensajeRetorno = array(
				'codigoRetorno' => '10502',
				'mensajeRetorno' => __('The customer code you are triying to save, is already in use', 'correosoficial'),
				'status_code' => '409',
			);
	
			echo json_encode($mensajeRetorno);
			die();
		}
	}
}
