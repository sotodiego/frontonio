<?php

require_once __DIR__ . '/../vendor/ecommerce_common_lib/Commons/NeedCustoms.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/Commons/Normalization.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/Commons/ShippingMethodZoneRules.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/CorreosOficialUtils.php';

/**
 * Gestión de los transportistas en WooCommerce mediante Ajustes->Productos
 */
class CorreosOficialCarrier extends CorreosOficialDAO {


	public function __construct() {
		// Métodos estáticos
		parent::__construct();
	}

	public function carrierExists( $new_carrier ) {
		// Comprobamos si tenemos el producto en la tabla pivote
		$sql = 'SELECT *, cocp.id_carrier as id_carrier FROM ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_carriers_products cocp
        LEFT JOIN ' . CorreosOficialUtils::getPrefix() . "correos_oficial_products cop ON (cocp.id_product = cop.id)
        WHERE cop.name='$new_carrier'";

		$result = self::getCarrierRecords($sql, true);

		if (count($result)) {
			// Actualiza los shippings methods desactivando el transportista si existe (NO ENTIENDO EL MOTIVO), ya que en este punto correos_ofial_products
			// No tiene seteado el id_carrier
			$sql = 'UPDATE ' . CorreosOficialUtils::getPrefix() . 'woocommerce_shipping_zone_methods wszm
            LEFT JOIN ' . CorreosOficialUtils::getPrefix() . "correos_oficial_products cop ON (wszm.instance_id = cop.id_carrier)
            SET wszm.is_enabled='0' WHERE cop.name='" . $new_carrier . "'";
			$this->executeQuery($sql);
		} else {
			return false;
		}
		if (isset($result[0])) {
			return $result[0]['id_carrier'];
		}
	}

	public static function carrierExistsInZone( $new_carrier, $id_zone ) {
		$sql = 'SELECT *, cocp.id_carrier as id_carrier FROM ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_carriers_products cocp
        LEFT JOIN ' . CorreosOficialUtils::getPrefix() . "correos_oficial_products cop ON (cocp.id_product = cop.id)
        WHERE cop.name='$new_carrier' AND id_zone = '$id_zone'";

		$result = self::getCarrierRecords($sql, true);

		if (isset($result[0])) {
			return $result[0]['id_carrier'];
		}
	}

	public static function getCarrier( $id_order ) {
		$sql = 'SELECT * FROM ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_carriers_products AS cocp
        JOIN ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_products cop ON (cocp.id_product = cop.id) AND id_zone = 1
        JOIN ' . CorreosOficialUtils::getPrefix() . "correos_oficial_orders coo ON (cocp.id_carrier = coo.id_carrier)
        WHERE coo.id_order='$id_order'";

		$record = self::getCarrierRecords($sql, true);

		if (isset($record[0])) {
			return $record[0];
		}
	}

	public static function getCarrierByProductId( $id_product, $id_zone = null ) {
		$by_zone = '';

		if ($id_zone != null) {
			$by_zone = " AND cocp.id_zone='$id_zone'";
		}

		$sql = '
        SELECT *, cop.id as id, cocp.id_carrier as id_carrier FROM ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_products cop
        JOIN ' . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products cocp ON (cocp.id_product = cop.id)
        WHERE cocp.id_product='" . $id_product . "' $by_zone LIMIT 1";

		$record = self::getCarrierRecords($sql, true);
		if (isset($record[0])) {
			return $record[0];
		}
	}

	public static function getSavedOrderProduct( $id_order ) {

		$sql = 'SELECT *, cop.id as id FROM ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_products cop JOIN '
		. CorreosOficialUtils::getPrefix() . "correos_oficial_orders coo ON cop.id = coo.id_product WHERE id_order = $id_order";

		$record = self::getCarrierRecords($sql, true);
		if (isset($record[0])) {
			return $record[0];
		}
	}

	public static function getCarriersProducts( $id_carrier_order, $id_zone ) {
		$sql = '
        SELECT id_product FROM ' . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products WHERE id_carrier='" . $id_carrier_order . "' AND id_zone='" . $id_zone . "'";
		return self::getCarrierRecords($sql, true);
	}

	public static function getClientCodeByCompany( $company ) {
		$dao = new CorreosOficialDao();
		$record = $dao->readRecord('correos_oficial_codes', "WHERE company='$company'", null, true);
		if (isset($record[0])) {
			return $record[0];
		}
	}

	public static function getDefaultClientCode( $company ) {
		return CorreosOficialDao::getDefaultSender($company);
	}

	public static function resetCarriers() {
		$dao = new CorreosOficialDAO();
		$sql = 'UPDATE ' . CorreosOficialUtils::getPrefix() . 'woocommerce_shipping_zone_methods wszm
                LEFT JOIN ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_carriers_products cocp
                ON (wszm.instance_id = cocp.id_carrier)
                LEFT JOIN ' . CorreosOficialUtils::getPrefix() . "correos_oficial_products cop
                ON (cop.id= cocp.id_product)
                SET cop.active='0'
                WHERE cocp.id_product = cop.id";
		$dao->executeQuery($sql);
	}

	/**
	 * Añade un producto de Corroes o CEX como transportista en la zona correspondiente
	 *
	 * @param  mixed @product Objeto con el producto a ser añadido como transportista
	 * @return int id del transportista añadido
	 */
	public function addCarrier( $product ) {
		$zones = WC_Shipping_Zones::get_zones();

		foreach ($zones as $zone) {

			$filtered_products = $this->getFilteredProducts($zone);

			if ($filtered_products != null && in_array($product->codigoProducto, $filtered_products)) {
				$this->addProductInZone($product, $zone, true);
			}
		}
	}

	public function addCarrierFromZone( $product, $id_zone ) {
		$zone_array = $this->getZonesArray($id_zone);

		$filtered_products = $this->getFilteredProducts($zone_array);

		if (in_array($product[0]->codigoProducto, $filtered_products)) {
			$this->addProductInZone($product[0], $zone_array, false);
		}
	}

	public function getFilteredProducts( $zone ) {
		$shipping_zone_rules = new ShippingMethodZoneRules();
		return $shipping_zone_rules->filterProducts($zone);
	}

	public function getZonesArray( $id_zone ) {
		$zone = WC_Shipping_Zones::get_zone($id_zone);
		$zone_array['id'] = $zone->get_id();
		$zone_array['zone_locations'] = $zone->get_zone_locations();
		return $zone_array;
	}

	public function addProductInZone( $product, $zone, $saveSettings ) {

		// Desde la página settings
		if ($saveSettings) {
			$instance_id = 0;

			// Comprobamos si el existe el shipping method para el producto en la zona
			$zone = new WC_Shipping_Zone($zone['id']);
			$shippingMethods = $zone->get_shipping_methods();

			foreach ($shippingMethods as $shippingMethod) {
				if (method_exists($shippingMethod, 'getPaqId') && $shippingMethod->getPaqId() == $product->id) {
					$instance_id = $shippingMethod->instance_id;
					break;
				}

			}

			// El producto no tiene shipping method creado en la zona
			if ($instance_id == 0) {

				// Obtenemos el último método de envío y su orden
				$sql = 'SELECT method_order FROM ' . CorreosOficialUtils::getPrefix() . 'woocommerce_shipping_zone_methods ORDER BY method_order DESC LIMIT 1';
				$method_order = self::getCarrierRecords($sql, true);
				$method_order = $method_order[0]['method_order'] + 1;

				// Añadimos el shipping method
				$sql = 'INSERT INTO ' . CorreosOficialUtils::getPrefix() . "woocommerce_shipping_zone_methods
					(zone_id, instance_id, method_id, method_order, is_enabled)
					VALUES ('" . $zone->get_id() . "', NULL, 'request_shipping_quote_$product->id', '$method_order', '0')";

				$this->executeQuery($sql);

				$instance_id = self::getLastInstanceId('woocommerce_shipping_zone_methods');

			}

			// Actualizamos tabla pivote carriers_products
			if ($instance_id != 0) {
				// Añadimos o actualizamos el producto a la tabla correos_oficial_carriers_products
				self::updateCorreosOficialCarriersProducts($instance_id, $product->id, $zone->get_id());
			}

			// Desde woocommerce
		} elseif (!self::carrierExistsInZone($product->name, $zone['id'])) {

				$zone = new WC_Shipping_Zone($zone['id']);

				$instance_id = self::getLastInstanceId('woocommerce_shipping_zone_methods');
				//$data = array('title' => $product->name, 'tax_status' => 'taxable', 'cost' => '0');
				// Actualizamos la descripción en la tabla postmeta de WordPress
				//update_option('woocommerce_request_shipping_quote_' . $instance_id . '_settings', $data);
				self::updateCorreosOficialCarriersProducts($instance_id, $product->id, $zone->get_id());

		}
	}

	public function updateCorreosOficialCarriersProducts( $id_carrier, $id_product, $id_zone ) {
		$result = $this->readRecord(
			'correos_oficial_carriers_products', "WHERE id_zone='$id_zone'
            AND id_product='$id_product'"
		);
		if (!count($result)) {
			$sql = 'INSERT INTO ' . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products
                (id, id_carrier, id_product, id_zone) values (null, '$id_carrier', '$id_product', '$id_zone') ";
		} else {
			$sql = 'UPDATE ' . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products SET id_carrier='$id_carrier',  id_zone='$id_zone'
                WHERE id=" . $result[0]->id;
		}
		$this->executeQuery($sql);
	}

	public static function launchQuery( $query ) {
		self::executeQuery($query);
	}

	public static function getCarrierRecords( $query, $as_array ) {
		$dao = new CorreosOficialDao();
		return $dao->getRecordsWithQuery($query, $as_array);
	}

	/**
	 * Función Genérica para conseguir un objeto de una tabla y devolver el último id
	 *
	 * @param $table: tabla en la que buscar
	 */
	public static function getLastInstanceId( $table ) {
		$sql = 'SELECT instance_id FROM ' . CorreosOficialUtils::getPrefix() . $table . ' ORDER BY instance_id DESC LIMIT 1';

		$record = self::getCarrierRecords($sql, true);

		if (isset($record)) {
			return $record[0]['instance_id'];
		} else {
			return null;
		}
	}

	public static function getCarriersByCompany( $company, $id_zone, $include_all = false ) {

		if ($company != 'both') {
			$sql = '
            SELECT  *,cop.id as my_id, wszm.instance_id as id_carrier  FROM ' . CorreosOficialUtils::getPrefix() . 'woocommerce_shipping_zone_methods as wszm
            LEFT JOIN ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_carriers_products cocp ON (cocp.id_carrier = wszm.instance_id)
            LEFT JOIN ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_products cop ON (cop.id = cocp.id_product)
            LEFT JOIN ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_codes coc ON (cop.company = coc.company)
            WHERE ' . " wszm.method_id!='local_pickup' and cop.company ='" . $company . "'
            and wszm.zone_id='$id_zone'";
		} else {
			$sql = '
            SELECT  *,cop.id as my_id, wszm.instance_id as id_carrier FROM ' . CorreosOficialUtils::getPrefix() . 'woocommerce_shipping_zone_methods as wszm
            LEFT JOIN ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_carriers_products cocp ON (cocp.id_carrier = wszm.instance_id)
            LEFT JOIN ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_products cop ON (cop.id = cocp.id_product)
            LEFT JOIN ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_codes coc ON (cop.company = coc.company)
            WHERE ' . " wszm.method_id!='local_pickup' and wszm.zone_id='$id_zone'";
		}

		return self::getCarrierRecords($sql, true);
	}

	public static function getCarriersByCompanyInOrder( $company, $id_zone ) {
		$sql = 'SELECT *,cop.id as my_id FROM ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_products cop';
		return self::getCarrierRecords($sql, true);
	}

	/**
	 * Consigue el id_zone de un transportista
	 *
	 * @param  object $order Objeto de tipo WC con el pedido
	 * @return int id_zona del transportista
	 */
	public static function getCarrierZone( $instance_id ) {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT zone_id FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE instance_id = %d", $instance_id
			)
		);
	}

	/**
	 * Consigue la compañia desde un pedido
	 *
	 * @param int el id del pedido
	 * @return string Correos o CEX
	 */
	public static function getCompanyByOrder( $id_order, $id_zone ) {

		// Obtenemos pedido de WC por su id
		$order = wc_get_order($id_order);

		if (empty($order)) {
			return false;
		}
		
		$shipping_methods = $order->get_items('shipping');

		// Si no tenemos métodos de envío devolvemos false
		if (empty($shipping_methods)) {
			return false;
		}

		// Obtenemos el primero de los métodos de envío
		$shipping_method = reset($shipping_methods);

		// Obtenemos el id del transportista
		$carrier_id = (int) $shipping_method->get_data()['instance_id'];

		// si no tenemos transportista devolvemos false
		if ($carrier_id) {

			$sql = 'SELECT * FROM ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_carriers_products AS cocp
			JOIN ' . CorreosOficialUtils::getPrefix() . "correos_oficial_products cop ON (cocp.id_product = cop.id) AND id_zone = '$id_zone'
			WHERE cocp.id_carrier='$carrier_id'";

			$record = self::getCarrierRecords($sql, true);

			// si tenemos resultados devolvemos la compañia
			if (isset($record[0])) {
				return $record[0]['company'];
			}

		}

		return false;
	}

	public static function getCarriersProductsList() {
		$sql = '
        SELECT *, cop.id as id, cocp.id_carrier as id_carrier FROM ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_products cop
        JOIN ' . CorreosOficialUtils::getPrefix() . 'correos_oficial_carriers_products cocp ON (cocp.id_product = cop.id);';

		return self::getCarrierRecords($sql, true);
	}
}
