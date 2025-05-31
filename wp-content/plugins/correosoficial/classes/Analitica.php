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

require_once __DIR__ . '/../vendor/ecommerce_common_lib/config.inc.php';

class Analitica {

	protected $host;
	protected $api_endpoint;
	protected $url_shop_record;
	protected $url_module_record;
	protected $url_external_module_record;
	protected $url_uninstall_record;
	protected $url_deactivate_record;
	protected $url_notification_list;
	protected $url_notification_check;
	protected $url_module_config;
	protected $url_module_config_sender;
	protected $db;
	public $shop_url;
	public $shop_name;
	
	
	public function __construct() {
		global $wpdb;

		$this->db = $wpdb;

		$this->host = Config::getAnaliticaHost();

		$this->url_shop_record            = 'https://' . $this->host . '/logistics/accregavex/api/v1/shop/record';
		$this->url_external_module_record = 'https://' . $this->host . '/logistics/accregavex/api/v1/shop/external-modules';
		$this->url_module_record          = 'https://' . $this->host . '/logistics/accregavex/api/v1/module/record';
		$this->url_uninstall_record       = 'https://' . $this->host . '/logistics/accregavex/api/v1/module/uninstall';
		$this->url_deactivate_record      = 'https://' . $this->host . '/logistics/accregavex/api/v1/module/deactivate';
		$this->url_module_config          = 'https://' . $this->host . '/logistics/accregavex/api/v1/module/configuration';
		$this->url_module_config_sender   = 'https://' . $this->host . '/logistics/accregavex/api/v1/module/configuration-sender';
		$this->url_notification_list      = 'https://' . $this->host . '/logistics/accregavex/api/v1/notification/list';
		$this->url_notification_check     = 'https://' . $this->host . '/logistics/accregavex/api/v1/notification/check';

		// Shop URL
		$shop = get_home_url();
		$shop = strpos($shop, 'localhost') == false ? $shop : 'https://' . get_bloginfo('name') . '.es';
		$this->shop_url = $shop;

		// Shop Name
		$this->shop_name = get_bloginfo('name');
		if (empty($this->shop_name)) {
			$this->shop_name = $this->shop_url;
		}
	}

	public function gdpr( $vars ) {

		if (isset($vars['correos-gdpr-check']) &&
			$vars['correos-gdpr-check'] === 'on' &&
			isset($vars['correos-dataProtect-check']) &&
			$vars['correos-dataProtect-check'] === 'on'
		) {

			$isRegistered = $this->shopRecord();

			if ($isRegistered['status'] == 200 || $isRegistered['status'] == 201) {

				$thisMoment = gmdate('Y-m-d H:i:s');
				$fields = array(
					'GDPR' => 1,
					'Analitica_date' => $thisMoment,
				);
				foreach ($fields as $fk => $fv) {
					$this->db->update(
						$this->db->prefix . 'correos_oficial_configuration', array( 'value' => $fv ), array( 'name' => $fk ), array( '%s', '%d' )
					);
				}

			}
			unset($thisMoment, $fields, $vars['correos-gdpr-check'], $vars['correos-betatester-check']);
			
			$this->moduleRecord();
			$this->externalModulesRecord();
			$this->configurationCall('undefined');

		}
		$gdpr = (int) $this->db->get_var('
            SELECT
                `value`
            FROM
                ' . $this->db->prefix . 'correos_oficial_configuration
            WHERE
                `name` = "GDPR"
        ');

		if ($gdpr === 0) {
				return true;
		}
			return false;
	}

	public function analiticaApi( $url, $method, $body ) {
		$contentLength = strlen(json_encode($body, JSON_UNESCAPED_SLASHES));

		$headers = array(
			'Content-Type: application/json',
			'Content-Length: ' . $contentLength,
			'User-Agent: PHP-Prestashop',
			'Host: ' . $this->host,
			'client_id: ' . ANALYTICS_CLIENT_ID,
			'client_secret: ' . ANALYTICS_CLIENT_SECRET,
		);

		if (strtoupper($method) === 'GET') {
			unset($headers[1]);
			$first = true;
			foreach ($body as $key => $param) {
				if ($first) {
					$url .= '?';
					$first = false;
				} else {
					$url .= '&';
				}
				$url .= $key . '=' . $param;
			}
		}

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		if (strtoupper($method) === 'POST') {
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_SLASHES));
		}
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		
		$response = curl_exec($curl);
		
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		$output = array(
			'output' => json_decode($response),
			'status' => $httpCode,
		);

		$logData = array(
			'url' => $url,
			'method' => $method,
			'body' => $body,
			'response' => $output,
		);

		// Para debug en el archivo de debug.log de Wordpress
		// error_log('Analitica API: ' . print_r($logData, true));

		return $output;
	}

	protected function getVersions( $type ) {
		$return = false;
		switch ($type) {
			case 'module':
				$configFile = file_get_contents(WP_PLUGIN_DIR . '/correosoficial/config.xml');
				$module = new SimpleXMLElement($configFile);
				$return = $module->version;
				break;
			case 'db':
				$return = $this->db->get_var('SELECT VERSION()');
				break;
			default:
				break;
		}

		return $return;
	}

	public function shopRecord() {
		$body = array(
			'shopDistinctive' => $this->shop_url,
			'shopName' => $this->shop_name,
		);
		return $this->analiticaApi($this->url_shop_record, 'POST', $body);
	}

	public function moduleRecord() {
		$moduleVersion = $this->getVersions('module');
		$dbVersion = $this->getVersions('db');
		$body = array(
			'shopDistinctive' => $this->shop_url,
			'moduleCode' => 'WOOT',
			'moduleVersion' =>  (string) $moduleVersion,
			'databaseCode' => 'MYSQL',
			'databaseVersion' => $dbVersion,
			'techVersion' => phpversion(),
			'platformVersion' => get_option('woocommerce_version'), 
		);
		$this->analiticaApi($this->url_module_record, 'POST', $body);
	}

	public function externalModulesRecord() {
		$finalModules = array();
		
		$modulesArray = get_option('active_plugins', array());
		foreach ($modulesArray as $module) {
			$plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . $module);
			$finalModules[]['name'] = $plugin_info['Name'] . ' ' . $plugin_info['Version']; 
		}

		$body = array(
			'shopDistinctive' => $this->shop_url,
			'externalModules' => $finalModules,
		);

		$this->analiticaApi($this->url_external_module_record, 'POST', $body);
	}

	public function configurationCall( $betatester ) {

		if ($betatester == 'undefined') {
			$betatester = (bool) $this->db->get_var('
                SELECT
                    value
                FROM
                    ' . $this->db->prefix . 'correos_oficial_configuration
                WHERE
                    name = "betatester"
                AND
                    type = "analitica"
            ');
		}

		$body = array(
			'shopDistinctive' => $this->shop_url,
			'moduleCode' => 'WOOT',
			'isBetaTester' => false, //$betatester
			'sender' => array(),
		);

		// Información remitente por defecto
		$sqlSender = 'SELECT * FROM ' . $this->db->prefix . 'correos_oficial_senders';
		$senders = $this->db->get_results($sqlSender, ARRAY_A);

		foreach ($senders as $sender) {

			$accounts = array();

			// Contrato correos asociado
			if ($sender['correos_code']) {
				$sqlCorreosCode = 'SELECT * FROM ' . $this->db->prefix . 'correos_oficial_codes WHERE id = ' . $sender['correos_code'];
				$correosCode = $this->db->get_row($sqlCorreosCode, ARRAY_A);
				if (!empty($correosCode)) {
					$accounts[0]['correosCustomerCode'] = $correosCode['CorreosCustomer'];
					$accounts[0]['contractNumber'] = $correosCode['CorreosContract'];
					$accounts[0]['labellerCode'] = $correosCode['CorreosKey'];
				}
			}

			// Contrato CEX asociado
			if ($sender['cex_code']) {
				$sqlCexCode = 'SELECT * FROM ' . $this->db->prefix . 'correos_oficial_codes WHERE id = ' . $sender['cex_code'];
				$cexCode = $this->db->get_row($sqlCexCode, ARRAY_A);
				if (!empty($cexCode)) {
					$accounts[0]['cexCustomerCode'] = $cexCode['CEXCustomer'];
				}
			}

			$body['sender'][] = array(
				'isDefault' => $sender['sender_default'] === '1' ? true : false,
				'countryCode' => $sender['sender_iso_code_pais'],
				'postalCode' => $sender['sender_cp'],
				'account' => $accounts,
			);

		}

		$this->analiticaApi($this->url_module_config_sender, 'POST', $body);
	}

	public function uninstallCall() {
		$body = array(
			'shopDistinctive' => $this->shop_url,
			'moduleCode' => 'WOOT',
		);

		$this->analiticaApi($this->url_uninstall_record, 'POST', $body);
	}

	public function disableCall() {
		$body = array(
			'shopDistinctive' => $this->shop_url,
			'moduleCode' => 'WOOT',
		);

		$this->analiticaApi($this->url_deactivate_record, 'POST', $body);
	}

	public function getNotifications() {
		$params = array(
			'shopDistinctive' => base64_encode($this->shop_url),
			'moduleCode' => 'WOOT',
		);
		return $this->analiticaApi($this->url_notification_list, 'GET', $params);
	}

	public function checkNotifications( $id ) {
		$body = array(
			'shopDistinctive' => $this->shop_url,
			'moduleCode' => 'WOOT',
			'notificationId' => (int) $id,
		);

		$this->analiticaApi($this->url_notification_check, 'POST', $body);
	}

	public static function gdprAccepted() {

		global $wpdb;

		$tableExists = $wpdb->get_var('SHOW TABLES LIKE "' . $wpdb->prefix . 'correos_oficial_configuration"');

		if (!$tableExists) {
			return false;
		}

		$isAccepted = (int) $wpdb->get_var('SELECT `value` FROM' . $wpdb->prefix . 'correos_oficial_configuration
			WHERE `name` = "GDPR" AND `type` = "analitica"');

		if ($isAccepted === 1) {
			return true;
		}
		return false;
	}

	public function lastHour() {
		$sql = '
        SELECT
            `value`
        FROM
            ' . $this->db->prefix . 'correos_oficial_configuration
        WHERE
            `name` = "Analitica_date"';

		return $this->db->get_var($sql);
	}

	public function updateTime() {
		$now = gmdate('Y-m-d H:i:s');
		$this->db->update($this->db->prefix . 'correos_oficial_configuration', array( 'value' => $now ), array( 'name' => 'Analitica_date' ), array( '%s', '%d' ));
	}
}
