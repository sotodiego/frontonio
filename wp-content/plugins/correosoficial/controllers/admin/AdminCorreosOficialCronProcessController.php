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

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/DetectPlatform.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/config.inc.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialCronDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/functions.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Cron/CronCorreosOficial.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/CorreosOficialLog.php';

class AdminCorreosOficialCronProcessController {



	private $cron_dao;

	public function __construct() {
		$operation = Normalization::normalizeData('operation');

		if ($operation === 'CronForm') {
			$this->updateCronSettings();
		}
	}

	/**
	 * Se guardan los ajustes del cron.
	 */
	public function updateCronSettings() {

		// Obtenemos campos de los formularios
		$ActivateOrderStatusChangeAfterSave = Normalization::normalizeData('ActivateOrderStatusChangeAfterSave');
		$StatusSelector = Normalization::normalizeData('StatusSelector');
		$ActivateAutomaticTracking = Normalization::normalizeData('ActivateAutomaticTracking');
		$ActivateOrderStatusChange = Normalization::normalizeData('ActivateOrderStatusChange');
		$CurrentState = Normalization::normalizeData('CurrentState');
		$DeliveredState = Normalization::normalizeData('DeliveredState');
		$CancelledStateValue = Normalization::normalizeData('CancelledStateValue');
		$ReturnedState = Normalization::normalizeData('ReturnedState');
		$CronInterval = Normalization::normalizeData('CronInterval');

		// Los metemos en un array
		$fields = array(
			'ActivateOrderStatusChangeAfterSave' => $ActivateOrderStatusChangeAfterSave,
			'StatusSelector' => $StatusSelector,
			'ActivateAutomaticTracking' => $ActivateAutomaticTracking,
			'ActivateOrderStatusChange' => $ActivateOrderStatusChange,
			'CurrentState' => $CurrentState,
			'DeliveredState' => $DeliveredState,
			'CancelledStateValue' => $CancelledStateValue,
			'ReturnedState' => $ReturnedState,
			'CronInterval' => $CronInterval,
		);

		// Obtenemos un objetoDao
		$this->cron_dao = new CorreosOficialCronDao();

		// Clave del registro
		$this->cron_dao->updateFieldsSetRecord($fields);
		die;
	}

	public static function updateCronInterval( $schedules ) {
		$schedules['correosoficial_cron'] = array(
			'interval' => 3600 * CorreosOficialConfigDao::getConfigValue('CronInterval'),
			'display'  => __('Cada ' . CorreosOficialConfigDao::getConfigValue('CronInterval') . ' Horas'),
		);
		return $schedules;
	}

	/**
	 * Función de Cron desde el controlador. Ejecuta el Cron de Ajustes.
	 */
	public static function cronExecute() {
		$ini_time = CorreosOficialLog::logDate();

		try {
			$cron = new CronCorreosOficial();
			$cron->cronInit();
		} catch (Exception $e) {
			$cron_error_log = __DIR__ . '/../../log/cron_error_log.txt';

			file_put_contents($cron_error_log, '[' . $ini_time . '] ', FILE_APPEND);
			file_put_contents($cron_error_log, $e->getMessage(), FILE_APPEND);

			$end_time = CorreosOficialLog::logDate();

			file_put_contents($cron_error_log, ' [' . $end_time . ']' . PHP_EOL . PHP_EOL, FILE_APPEND);
			error_log('Excepción capturada 15500: ' . $e->getMessage() . "\n");
			die ('Excepción capturada 15500: ' . esc_html($e->getMessage()) . "\n");
		}
	}
}
