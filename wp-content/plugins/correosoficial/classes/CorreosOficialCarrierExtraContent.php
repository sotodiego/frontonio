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

require_once __DIR__ . '../../vendor/ecommerce_common_lib/Commons/NeedCustoms.php';
require_once __DIR__ . '../../vendor/ecommerce_common_lib/Commons/BridgeWCLanguage.php';

class CorreosOficialCarrierExtraContent {

	private $smarty;

	public function __construct( $session_cart_params, $smarty ) {
		$this->hookDisplayCarrierExtraContent($session_cart_params, $smarty);
	}

	public function hookDisplayCarrierExtraContent( $session_cart_params, $smarty ) {
		global $co_page;
		global $co_base_dir;
		global $co_plugin_dir;
		global $woocommerce;
		global $correos_oficial_cart;

		$this->smarty = $smarty;

		$co_page = 'checkout';
		$plugin_dir = WP_PLUGIN_DIR . '/correosoficial/';

		$google_api_key = CorreosOficialConfigDao::getConfigValue('GoogleMapsApi');
		$show_maps = false;
		$defined_google_api_key = false;

		if (!empty($google_api_key)) {
			$show_maps = true;
			$defined_google_api_key = true;
		}

		$carrier['id_reference'] = $session_cart_params->get_instance_id();

		// con los nuevos id de transportistas no funciona deprecado
		// $chosen_method_id = preg_replace('/[^0-9.]+/', '', $woocommerce->session->chosen_shipping_methods[0]);

		// Conseguimos el id del método de envío elegido
		$chosen_method = explode(':', $woocommerce->session->chosen_shipping_methods[0]);
		$chosen_method_id = $chosen_method[1];

		/**
		 * Salimos del hook si el id_carrier no es el id del método de envío elegido
		 */
		if ($chosen_method_id != $carrier['id_reference']) {
			return false;
		}

		$result = CorreosOficialCheckoutDao::getCarrierParams($carrier);

		if (!isset($result)) {
			return false;
		}

		$aviso_aduanas_interiores = CorreosOficialCheckoutDao::getValueConf('MessageToWarnBuyer');
		$aviso_aduanas_interiores = $aviso_aduanas_interiores['value'];
		$customsMessage = CorreosOficialCheckoutDao::getValueConf('TranslatableInput');
		$iso_code = get_locale();
		$id_lang = BridgeWCLanguage::getIdLanguageByIsoCode($iso_code);
		$string_translated = CorreosOficialUtils::translateStringsFromDB($customsMessage['value'], $id_lang);

		$default_sender = CorreosOficialSendersDao::getDefaultSender();

		// Si no tenemos remitente por defecto configurado en Ajustes -> Remitentes, ponemos valores por defecto
		if (empty($default_sender)) {
			$sender_country = 'ES';
			$sender_postal_code = '';
		} else {
			$sender_country = $default_sender['sender_iso_code_pais'];
			$sender_postal_code = $default_sender['sender_cp'];
		}

		$customer_postal_code = $woocommerce->customer->get_shipping_postcode();
		$customer_country = $woocommerce->customer->get_shipping_country();

		$require_customs_doc = NeedCustoms::isCustomsRequired(
			$sender_postal_code,
			$customer_postal_code,
			$sender_country,
			$customer_country
		);

		$this->smarty->assign('aviso_aduanas_interiores', $aviso_aduanas_interiores);
		$this->smarty->assign('string_translated', $string_translated);
		$this->smarty->assign('require_customs_doc', $require_customs_doc);

		switch ($result['product_type']) {
			case 'homedelivery':
				$params_tpl = array(
				'carrier_type' => 'homedelivery',
				);
				$this->smarty->assign(array( 'params' => $params_tpl ));

				break;
			case 'office':
				$params_tpl = array(
				'carrier_type' => 'office',
				);
				$this->smarty->assign(array( 'params' => $params_tpl ));

				if (isset($data->cp) && !empty($data->cp)) {
					$postal_code = $data->cp;
					$this->smarty->assign('office_postal_code', $postal_code);
					$this->smarty->assign('selected_office', CorreosOficialUtils::replaceUnicodeCharacters($data->nombre));
				} else {
					$this->smarty->assign('office_postal_code', '');
					$this->smarty->assign('selected_office', '');
				}
				break;
			case 'citypaq':
				$params_tpl = array(
				'carrier_type' => 'citypaq',
				);
				$this->smarty->assign(array( 'params' => $params_tpl ));

				if (isset($data->cod_postal) && !empty($data->cod_postal)) {
					$postal_code = $data->cod_postal;
					$this->smarty->assign('citypaq_postal_code', $postal_code);
					$this->smarty->assign('selected_citypaq', $data->det_canal);
				} else {
					$this->smarty->assign('citypaq_postal_code', '');
					$this->smarty->assign('selected_citypaq', '');
				}
				break;
			case 'international':
				$params_tpl = array(
				'carrier_type' => 'international',
				);
				$this->smarty->assign(array( 'params' => $params_tpl ));
				$customsMessage = CorreosOficialCheckoutDao::getValueConf('TranslatableInput');
				$string_translated = CorreosOficialUtils::translateStringsFromDB($customsMessage['value'], $id_lang);
				$this->smarty->assign(array( 'string_translated' => $string_translated ));
				break;
			default:
				$params_tpl = array(
				'carrier_type' => '',
				);
				$this->smarty->assign(array( 'params' => $params_tpl ));

				break;
		}

		include_once WP_PLUGIN_DIR . '/correosoficial/langs/checkoutLang.php';

		$this->smarty->registerFilter('pre', 'Prefilter::preFilterConstants');
		$params = CorreosOficialParamsAdapter::getParams($result['product_type'], $carrier['id_reference']);

		// Conseguimos el código postal de la dirección de envío
		$params['postcode'] = WC()->customer->get_shipping_postcode();

		$this->smarty->assign('params', $params);
		$this->smarty->assign('show_maps', $show_maps);
		$this->smarty->assign('defined_google_api_key', $defined_google_api_key);

		$shippingMethodZone = new ShippingMethodZoneRules();

		$this->smarty->assign(array(
			'id_carrier' => $result['id_carrier'],
			'includeCountries' => $shippingMethodZone->getIsoS0360(),
			'codigoProducto' => $result['codigoProducto'],
			'co_base_dir' => $plugin_dir,
		));
		$this->smarty->display($plugin_dir . 'views/templates/hook/forbidenCountry.tpl');
		
		$this->smarty->display($plugin_dir . 'views/templates/hook/checkout_hide_maps.tpl');
		return true;
	}
}
