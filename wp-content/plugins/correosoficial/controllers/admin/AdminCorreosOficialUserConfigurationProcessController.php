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
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialUserConfigurationDao.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/functions.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/CorreosOficialUtils.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Dao/CorreosOficialConfigDao.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';
use CorreosOficialCommonLib\Commons\CorreosOficialCrypto;

class AdminCorreosOficialUserConfigurationProcessController {

	private $user_configuration_dao;

	const MAX_LOGO_SIZE = 200000;

	public function __construct() {
		global $wpdb;
		try {
			$betatester = false;
			if (isset($_POST['dispatcher']['betatester']) && $_POST['dispatcher']['betatester'] === 'on') {
				$betatester = true;

				$wpdb->update(
					$wpdb->prefix . 'correos_oficial_configuration',
					array( 'value' => 1 ),
					array( 'name' => 'betatester', 'type' => 'analitica' )
				);
			} else {
				$wpdb->update(
					$wpdb->prefix . 'correos_oficial_configuration',
					array( 'value' => 0 ),
					array( 'name' => 'betatester', 'type' => 'analitica' )
				);
			}

			// Obtenemos un objetoDao
			$this->user_configuration_dao = new CorreosOficialUserConfigurationDao();

			$file = isset($_FILES['dispatcher']) ? CorreosOficialUtils::sanitize($_FILES['dispatcher']) : array(); // phpcs:ignoreFile
			$fileSize = isset($file['size']['UploadLogoLabels']) ? intval($file['size']['UploadLogoLabels']) : '';

			if ($fileSize > self::MAX_LOGO_SIZE) {
				throw new LogicException(__('Image too big', 'correosoficial'));
			}

			if (!empty($file)) {
				$file_name = $file['name']['UploadLogoLabels'];
			}

			// Obtenemos campos de los formularios
			$DefaultPackages = Normalization::normalizeData('DefaultPackages');
			$CashOnDeliveryMethod = Normalization::normalizeData('CashOnDeliveryMethod');
			$DefaultLabel = Normalization::normalizeData('DefaultLabel');

			if (substr(Normalization::normalizeData('BankAccNumberAndIBAN'), 0, 4) == '****') {
				$BankAccNumberAndIBAN = Normalization::normalizeData('BankAccNumberAndIBAN_hidden','nospaces');
			} else {
				$BankAccNumberAndIBAN = Normalization::normalizeData('BankAccNumberAndIBAN','nospaces');
			}

			$ActivateTrackingLink = Normalization::normalizeData('ActivateTrackingLink');
			$ActivateWeightByDefault = Normalization::normalizeData('ActivateWeightByDefault');
			$WeightByDefault = Normalization::normalizeData('WeightByDefault');
			$ActivateDimensionsByDefault = Normalization::normalizeData('ActivateDimensionsByDefault');
			$DimensionsByDefaultHeight = Normalization::normalizeData('DimensionsByDefaultHeight');
			$DimensionsByDefaultWidth = Normalization::normalizeData('DimensionsByDefaultWidth');
			$DimensionsByDefaultLarge = Normalization::normalizeData('DimensionsByDefaultLarge');
			$AgreeToAlterReferences = Normalization::normalizeData('AgreeToAlterReferences');
			$ShowLabelData = Normalization::normalizeData('ShowLabelData');
			$CustomerAlternativeText = Normalization::normalizeData('CustomerAlternativeText');
			$LabelAlternativeText = Normalization::normalizeData('LabelAlternativeText');
			$GoogleMapsApi = Normalization::normalizeData('GoogleMapsApi', 'no_uppercase');
			$ChangeLogoOnLabel = Normalization::normalizeData('ChangeLogoOnLabel');
			$FormSwitchLanguage = Normalization::normalizeData('FormSwitchLanguage');
			$LabelObservations = Normalization::normalizeData('LabelObservations');
			$SSLAlternative = Normalization::normalizeData('SSLAlternative');
			$ShowShippingStatusProcess = Normalization::normalizeData('ShowShippingStatusProcess');
			$ShipmentPreregistered = Normalization::normalizeData('ShipmentPreregistered', 'no_uppercase');
			$ShipmentInProgress = Normalization::normalizeData('ShipmentInProgress', 'no_uppercase');
			$ShipmentDelivered = Normalization::normalizeData('ShipmentDelivered', 'no_uppercase');
			$ShipmentCanceled = Normalization::normalizeData('ShipmentCanceled', 'no_uppercase');
			$ShipmentReturned = Normalization::normalizeData('ShipmentReturned', 'no_uppercase');
			$CronInterval = Normalization::normalizeData('CronInterval');
			$ActivateAutomaticTracking = Normalization::normalizeData('ActivateAutomaticTracking');
			$ActivateNifFieldCheckout = Normalization::normalizeData('ActivateNifFieldCheckout');
			$NifFieldRadio = Normalization::normalizeData('NifFieldRadio');
			$NifFieldPersonalizedValue = Normalization::normalizeData('NifFieldPersonalizedValue', 'no_uppercase');

			if (CorreosOficialUtils::sislogModuleIsActive()) {
				$LabelObservations = '';
				$CustomerAlternativeText = '';
				$ActivateWeightByDefault = '';
				$ActivateDimensionsByDefault = '';
				$ChangeLogoOnLabel = '';
				$SSLAlternative = '';
			}

			if (isset($file) && !empty($file)) {
				$UploadLogoLabels = $file_name;
			}

			// Los metemos en un array
			$fields = array(
				'DefaultPackages' => $DefaultPackages,
				'CashOnDeliveryMethod' => strtolower($CashOnDeliveryMethod),
				'DefaultLabel' => $DefaultLabel,
				'BankAccNumberAndIBAN' => $BankAccNumberAndIBAN,
				'ActivateTrackingLink' => $ActivateTrackingLink,
				'ActivateWeightByDefault' => $ActivateWeightByDefault,
				'WeightByDefault' => $WeightByDefault,
				'ActivateDimensionsByDefault' => $ActivateDimensionsByDefault,
				'DimensionsByDefaultHeight' => ( $ActivateDimensionsByDefault == 'on' ) ? $DimensionsByDefaultHeight : 0,
				'DimensionsByDefaultWidth' => ( $ActivateDimensionsByDefault == 'on' ) ? $DimensionsByDefaultWidth : 0,
				'DimensionsByDefaultLarge' => ( $ActivateDimensionsByDefault == 'on' ) ? $DimensionsByDefaultLarge : 0,
				'AgreeToAlterReferences' => $AgreeToAlterReferences,
				'ShowLabelData' => $ShowLabelData,
				'CustomerAlternativeText' => $CustomerAlternativeText,
				'LabelAlternativeText' => $LabelAlternativeText,
				'GoogleMapsApi' => $GoogleMapsApi,
				'ChangeLogoOnLabel' => $ChangeLogoOnLabel,
				'FormSwitchLanguage' => $FormSwitchLanguage,
				'LabelObservations' => $LabelObservations,
				'SSLAlternative' => $SSLAlternative,
				'ShowShippingStatusProcess' => $ShowShippingStatusProcess,
				'ShipmentPreregistered' => $ShipmentPreregistered,
				'ShipmentInProgress' => $ShipmentInProgress,
				'ShipmentDelivered' => $ShipmentDelivered,
				'ShipmentCanceled' => $ShipmentCanceled,
				'ShipmentReturned' => $ShipmentReturned,
				'CronInterval' => $CronInterval,
				'ActivateAutomaticTracking' => $ActivateAutomaticTracking,
				'ActivateNifFieldCheckout' => $ActivateNifFieldCheckout,
				'NifFieldRadio' => $NifFieldRadio,
				'NifFieldPersonalizedValue' => $NifFieldPersonalizedValue,
			);

			if (substr(Normalization::normalizeData('BankAccNumberAndIBAN'), 0, 4) != '****') {
				$fields['BankAccNumberAndIBAN'] = CorreosOficialCrypto::encrypt(Normalization::normalizeData('BankAccNumberAndIBAN','nospaces'));
			}

			// Obtenemos un objetoDao
			$this->user_configuration_dao = new CorreosOficialUserConfigurationDao();

			if (isset($file) && !empty($file)) {
				$fields['UploadLogoLabels'] = $UploadLogoLabels;
			}

			$fields['ChangeLogoOnLabel'] = !isset($_REQUEST['ChangeLogoOnLabel']) ? '' : 'on';
			$fields['ActivateWeightByDefault'] = !isset($_REQUEST['ActivateWeightByDefault']) ? '' : 'on';
			$fields['ActivateDimensionsByDefault'] = !isset($_REQUEST['ActivateDimensionsByDefault']) ? '' : 'on';
			$fields['AgreeToAlterReferences'] = !isset($_REQUEST['AgreeToAlterReferences']) ? '' : 'on';
			$fields['ActivateTrackingLink'] = !isset($_REQUEST['ActivateTrackingLink']) ? '' : 'on';
			$fields['CustomerAlternativeText'] = !isset($_REQUEST['CustomerAlternativeText']) ? '' : 'on';
			$fields['SSLAlternative'] = !isset($_REQUEST['SSLAlternative']) ? '' : 'on';
			$fields['ShowShippingStatusProcess'] = !isset($_REQUEST['ShowShippingStatusProcess']) ? '' : 'on';

			$fields['ErrorLogoLabels'] = '';

			$tmpFileName = sanitize_text_field($file['tmp_name']['UploadLogoLabels']);
			$getUserLogo = CorreosOficialConfigDao::getConfigValue('UploadLogoLabels');

			if ($tmpFileName != '' && wp_is_file_mod_allowed('file_upload') || $getUserLogo == 'default.jpg') {
				$sourcePath = $tmpFileName;
				$result = Normalization::filterFiles($file['name']['UploadLogoLabels']);

				if (!str_contains($result, 'ERROR:  12010')) {

					$uploadDir = wp_upload_dir();
					$targetDir = $uploadDir['path'];
					$targetFile = $targetDir . '/' . $result;

					if(!move_uploaded_file($sourcePath, $targetFile)) {
						throw new LogicException(__('Could not upload logo. Check permissions of the wordpress upload directory', 'correosoficial'));
					}

					$fields['UploadLogoLabels'] = $uploadDir['url'] . '/' . $result;
				
				} else {
					$fields['ErrorLogoLabels'] = $result;
				}
			} elseif($getUserLogo != '') {
				$fields['UploadLogoLabels'] = $getUserLogo;
			}
			// Clave del registro
			$this->user_configuration_dao->updateFieldsSetRecord($fields);

			$obj = array(
				'savedLogo' => $fields['UploadLogoLabels']
			);

			( new Analitica() )->configurationCall(false);

			die(json_encode($obj));
		} catch (Exception $e) {
			$obj = array(
				'error' => 'Error',
				'desc' => __('ERROR 12040: Han error has ocurred when submitting data. ' . $e->getMessage(), 'correosoficial'),
			);

			die(json_encode($obj));
		}
	}
}
