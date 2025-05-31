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

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

if (!defined('WPINC')) {
	die;
}

class CorreosDataTableController {

	public $db;
	public $dt_pref;

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->dt_pref = $wpdb->prefix;
	}

	public function dataTable_callback() {
		check_ajax_referer('dataTableNonce', 'nonce');

		$data = array();
		$from = gmdate('Y-m-d');
		$to = gmdate('Y-m-d');
		$tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : '';
		$init = 0;
		$perPage = 10;
		$onlyCorreos = false;

		if (isset($_POST['onlyCorreos']) && $_POST['onlyCorreos'] == 'active') {
			$onlyCorreos = true;
		}

		if (isset($_POST['FromDateOrdersReg']) || !empty($_POST['FromDateOrdersReg'])) {
			$from = gmdate('Y-m-d', strtotime(sanitize_text_field($_POST['FromDateOrdersReg'])));
		}
		if (isset($_POST['ToDateOrdersReg']) || !empty($_POST['ToDateOrdersReg'])) {
			$to = gmdate('Y-m-d', strtotime(sanitize_text_field($_POST['ToDateOrdersReg'])));
		}

		if (isset($_POST['start'])) {
			$init = sanitize_text_field($_POST['start']);
		}
		if (isset($_POST['length'])) {
			$perPage = sanitize_text_field($_POST['length']);
		}
		$like = '';
		$general = false;
		if (isset($_POST['search']) && isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
			$general = sanitize_text_field($_POST['search']['value']);
			$like .= $this->globlaSearch($general, $tab);
		}

		if (isset($_POST['columns'])) {
			$search_array = array();
			
			foreach (CorreosOficialUtils::sanitize($_POST['columns']) as $column) { // phpcs:ignore
				if (!empty($column['search']['value'])) {
					$search_array[] = $this->getDataLike($column['data'], $column['search']['value']);
				}
			}

			// comprobamos si tenemos resultados en search_array
			if (count($search_array) > 0) {
				$like .= ' AND ';

				foreach ($search_array as $index => $condition) {
					$column = key($condition); // Obtenemos la clave del primer nivel del array
					$values = current($condition); // Obtenemos los valores asociados a esa clave
				
					if ($index > 0) {
						$like .= ' AND ';
					}
				
					$like .= '(';
					foreach ($values as $valueIndex => $value) {
						if ($valueIndex > 0) {
							$like .= ' OR ';
						}
						$like .= $column . ' LIKE "%' . $value . '%"';
					}
					$like .= ')';
				}
			}
		}

		$result = $this->gestionTab($tab, $from, $to, $like, $init, $perPage, $onlyCorreos);
		if (isset($result) && !empty($result)) {
			$data['data'] = $result['datas'];
			$data['recordsFiltered'] = $result['total'];
			$data['recordsTotal'] = $result['total'];
			$data['lengthMenu'] = array( 10, 25, 50, 100 );
			$data['pageLength'] = $perPage;
			if (empty($data['data'])) {
				$data['data'] = array();
			}
			echo json_encode($data);
		} else {
			$data['data'] = array();
			echo json_encode($data);
		}

		wp_die();
	}

	private function sqlGestionDataTableHPOS( $from, $to, $like, $init, $perPage ) {
		$order_key = '(SELECT order_key FROM ' . $this->dt_pref . 'wc_order_operational_data WHERE order_id=wc.id)';
		$cart_hash = '(SELECT cart_hash FROM ' . $this->dt_pref . 'wc_order_operational_data WHERE order_id=wc.id)';

		$order_item_meta = 'SELECT meta_value FROM ' . $this->dt_pref . 'woocommerce_order_itemmeta woim
                            JOIN ' . $this->dt_pref . "woocommerce_order_items woi ON woim.order_item_id = woi.order_item_id
                            WHERE order_id=wc.id and meta_key = 'instance_id' LIMIT 1)";

		$sql = 'SELECT
                    wc.id as id_order,
                    wc.date_created_gmt as date_add,
                    SUBSTRING(' . $order_key . ",10) as reference,
                    cocp.id_carrier as id_carrier,
                    '10' as 'current_sate',
                    wc.date_created_gmt as date_add,
                    coo.shipping_number as shipping_number,
                    coo.AT_code as AT_code,
                    coo.carrier_type as saved_carrier_type,
                    coo.id_product as saved_id_product,
                    GROUP_CONCAT(DISTINCT prp.post_title  SEPARATOR ', ') as products,
                    cop.id as id_product,
                    cop.company as company,
                    cop.max_packages as max_packages,
                    cop.codigoProducto as codigoProducto,
                    cop.product_type as product_type,
                    cos.exp_number as first_shipping_number,
                    cor.reference_code as office,
                    IF (coo.last_status IS NULL, (SELECT status FROM " . $this->dt_pref . 'wc_orders WHERE id = wc.id  LIMIT 1),coo.last_status) as order_state,
                    (SELECT order_item_name FROM ' . $this->dt_pref . "woocommerce_order_items WHERE order_id = wc.id AND order_item_type = 'shipping'  LIMIT 1) as carrier_type,
                    IF (cop.name IS NULL, (SELECT name FROM " . $this->dt_pref . 'correos_oficial_products WHERE id = cocp.id_product LIMIT 1), cop.name) as name,
                    IF (cop.max_packages IS NULL, (SELECT max_packages FROM ' . $this->dt_pref . "correos_oficial_products WHERE id = cocp.id_product LIMIT 1), null) as max_packages_custom,
                    concat(woa.first_name, ' ', woa.last_name) as cliente,
                    (SELECT country FROM " . $this->dt_pref . "wc_order_addresses WHERE order_id = wc.id AND address_type = 'shipping'  LIMIT 1) as delivery_iso_code,
                    IF(coo.shipping_number !='', (SELECT sender_iso_code_pais FROM " . $this->dt_pref . 'correos_oficial_senders WHERE id=coo.id_sender), (SELECT sender_iso_code_pais FROM ' . $this->dt_pref . 'correos_oficial_senders WHERE sender_default=1)) as sender_iso_code,
                    IFNULL(coo.bultos,' . $this->settingVar('DefaultPackages') . ') as bultos
                FROM
                    ' . $this->dt_pref . 'wc_orders wc
                LEFT JOIN
                    ' . $this->dt_pref . 'wc_order_product_lookup wopl ON (wopl.order_id = wc.id)
                LEFT JOIN
                    ' . $this->dt_pref . 'posts prp ON (prp.ID = wopl.product_id)
                LEFT JOIN
                    ' . $this->dt_pref . 'correos_oficial_carriers_products cocp ON (cocp.id_carrier = (' . $order_item_meta . ')
                LEFT JOIN
                    ' . $this->dt_pref . 'correos_oficial_orders coo ON (wc.id = coo.id_order)
                LEFT JOIN
                    ' . $this->dt_pref . 'correos_oficial_products cop ON (cop.id = cocp.id_product)
                LEFT JOIN
                    ' . $this->dt_pref . 'correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
                LEFT JOIN
                    ' . $this->dt_pref . 'correos_oficial_requests cor ON (
                        (CASE WHEN cor.id_order IS NULL
                        THEN cor.id_cart = ' . $cart_hash . '
                        ELSE cor.id_order = wc.id
                        END)
                    )
                LEFT JOIN
                    ' . $this->dt_pref . "wc_order_addresses woa ON (woa.order_id = wc.id)
                WHERE
                    date(wc.date_created_gmt)  BETWEEN '" . $from . "' AND '" . $to . "' AND wc.status != 'trash' AND wc.status != 'auto-draft'" . $like . '
                GROUP BY
                    wc.id, cocp.id_carrier
                ORDER BY
                    wc.id DESC
                LIMIT ' . $init . ',' . $perPage;

		$values = $this->db->get_results($sql, ARRAY_A);

		// Lista de remitentes
		$senders = array();
		$sender_default = '';
		foreach (CorreosOficialSendersDao::getSendersWithCodes() as $sender) {

			$senderCompany = '';
			if ($sender['correos_code'] != 0 && $sender['cex_code'] == 0) {
				$senderCompany = 'Correos';
			} elseif ($sender['cex_code'] != 0 && $sender['correos_code'] == 0) {
				$senderCompany = 'CEX';
			} elseif ($sender['correos_code'] != 0 && $sender['cex_code'] != 0) {
				$senderCompany = 'all';
			}

			$senders[] = array(
				'sender_id' => $sender['id'],
				'sender_data' => array(
					'name' => $sender['sender_name'],
					'company' => $senderCompany,
					'sender_iso_code' => $sender['sender_iso_code_pais'],
					'default' => $sender['sender_default'],
				),
			);

			if ($sender['sender_default'] == 1) {
				$sender_default = $sender['id'];
			}
		}

		foreach ($values as $key => $value) {

			// Si es un pedido preregistrado obtenmos información de correos_oficial_orders
			if ($values[$key]['saved_carrier_type']) {
				$values[$key]['carrier_type'] = $value['saved_carrier_type'];
				$values[$key]['company'] = $value['saved_carrier_type'];

				// Obtenemos nombre del producto por su id
				$productsDao = new CorreosOficialProductsDao();
				$productsDaoResult = $productsDao->getProduct($value['saved_id_product'], 'correos_oficial_products');
				$product = (array) reset($productsDaoResult);

				$values[$key]['name'] = $product['name'];


			} else {
				$values[$key]['carrier_type'] = $value['company'];
			}
			

			// Senders
			$values[$key]['senders'] = $senders;
			$values[$key]['sender_default'] = $sender_default;

		}

		unset($sql);

		$orders = array();
		foreach ($values as $order) {

			$sqlGetProductNames = 'SELECT
            CASE
                WHEN LENGTH(GROUP_CONCAT(woi.order_item_name SEPARATOR ", ")) > 50
                THEN CONCAT(LEFT(GROUP_CONCAT(woi.order_item_name SEPARATOR ", "), 50), "...")
            ELSE
                GROUP_CONCAT(woi.order_item_name SEPARATOR ", ")
            END AS concated
            FROM
            ' . $this->dt_pref . 'wc_orders wc
            LEFT JOIN
            ' . $this->dt_pref . 'woocommerce_order_items woi ON (woi.order_id = wc.id)
            WHERE wc.id = ' . (int) $order['id_order'] . ' AND woi.order_item_type = "line_item"
            GROUP BY
            wc.id;';

			$productsName = $this->db->get_var($sqlGetProductNames);

			$order['products'] = $productsName;

			$order = $this->setOrderNumber($order);
			$order['reference'] = $order['order_number'] . ' ' . str_replace('wc_order_', '', $order['reference']);
			$order['order_state'] = __(ucFirst(str_replace('wc-', '', $order['order_state'])), 'woocommerce');
			$orders[] = $order;
		}

		unset($values);
		$result['datas'] = $orders;

		$sqlTotal ='SELECT
                        wc.id as id_order
                    FROM
                        ' . $this->dt_pref . 'wc_orders wc
                    LEFT JOIN
                        ' . $this->dt_pref . 'correos_oficial_orders coo ON (wc.id = coo.id_order)
                    LEFT JOIN
                        ' . $this->dt_pref . 'wc_orders_meta wpm ON (wc.id = wpm.order_id)
                    LEFT JOIN
                        ' . $this->dt_pref . 'correos_oficial_carriers_products cocp ON (cocp.id_carrier = (' . $order_item_meta . ')
                    LEFT JOIN
                        ' . $this->dt_pref . 'correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
                    LEFT JOIN
                        ' . $this->dt_pref . 'correos_oficial_requests cor ON (
                            (CASE WHEN cor.id_order IS NULL
                            THEN cor.id_cart = ' . $cart_hash . '
                            ELSE cor.id_order = wc.id
                            END)
                        )
                    LEFT JOIN
                        ' . $this->dt_pref . "correos_oficial_products cop ON (cop.id = cocp.id_product)
                    WHERE
                        date(wc.date_created_gmt)  BETWEEN '" . $from . "' AND '" . $to . "' AND wc.status != 'trash'" . $like . '
                    GROUP BY
                        wc.id, cocp.id_carrier';

		$result['total'] = count($this->db->get_results($sqlTotal, ARRAY_A));

		return $result;
	}

	private function sqlGestionDataTable( $from, $to, $like, $init, $perPage ) {
		$order_key = $this->getMetaKeyValue('_order_key');
		$shipping_firstname = $this->getMetaKeyValue('_shipping_first_name');
		$shipping_lastname = $this->getMetaKeyValue('_shipping_last_name');
		$billing_country = $this->getMetaKeyValue('_billing_country');
		$cart_hash = $this->getMetaKeyValue('_cart_hash');
		$default_packages = $this->settingVar('DefaultPackages');
		$result = array();

		$sql = '
        SELECT
            wp.ID as id_order,
            SUBSTRING(' . $order_key . ",10) as reference,
            cocp.id_carrier as id_carrier,
            '10' as 'current_sate',
            wp.post_date as date_add,
            coo.shipping_number as shipping_number,
            coo.AT_code as AT_code,
            coo.id_sender as sender_selected,
            coo.carrier_type as saved_carrier_type,
            coo.id_product as saved_id_product,
            GROUP_CONCAT(DISTINCT prp.post_title  SEPARATOR ', ') as products,
            cop.id as id_product,
            cos.exp_number as first_shipping_number,
            cor.reference_code as office,
            IF (coo.last_status IS NULL, (SELECT status FROM " . $this->dt_pref . 'wc_order_stats WHERE order_id = wp.ID  LIMIT 1),coo.last_status) as order_state,
            (SELECT order_item_name FROM ' . $this->dt_pref . "woocommerce_order_items WHERE order_id = wp.ID AND order_item_type = 'shipping'  LIMIT 1) as carrier_type,
            IF (cop.name IS NULL, (SELECT name FROM " . $this->dt_pref . 'correos_oficial_products WHERE id = cocp.id_product LIMIT 1), cop.name) as name,
            cop.company as company,
            cop.max_packages as max_packages,
            cop.codigoProducto as codigoProducto,
            cop.product_type as product_type,
            cocp.id_product as id_product_custom,
            IF (cop.max_packages IS NULL, (SELECT max_packages FROM ' . $this->dt_pref . 'correos_oficial_products WHERE id = cocp.id_product LIMIT 1), null) as max_packages_custom,
            concat(' . $shipping_firstname . ", ' ', " . $shipping_lastname . ') as cliente,
            ' . $billing_country . " as delivery_iso_code,
            IF(coo.shipping_number !='', (SELECT sender_iso_code_pais FROM " . $this->dt_pref . 'correos_oficial_senders WHERE id=coo.id_sender), (SELECT sender_iso_code_pais FROM ' . $this->dt_pref . 'correos_oficial_senders WHERE sender_default=1)) as sender_iso_code,
            IFNULL(coo.bultos,' . $default_packages . ') as bultos
        FROM
            ' . $this->dt_pref . 'posts wp
        LEFT JOIN
            ' . $this->dt_pref . 'wc_order_product_lookup wopl ON (wopl.order_id = wp.ID)
        LEFT JOIN
            ' . $this->dt_pref . 'posts prp ON (prp.ID = wopl.product_id)
        LEFT JOIN
            ' . $this->dt_pref . 'wc_order_stats os ON (os.order_id = wp.ID)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_orders coo ON (wp.ID = coo.id_order)
        LEFT JOIN
            ' . $this->dt_pref . 'postmeta wpm ON (wp.ID = wpm.post_id)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_carriers_products cocp ON (cocp.id_carrier = (' . $this->getOrderItemMeta() . ')
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_requests cor ON (
                (CASE WHEN cor.id_order IS NULL
                THEN cor.id_cart = ' . $cart_hash . '
                ELSE cor.id_order = wp.ID
                END)
            )
        LEFT JOIN
            ' . $this->dt_pref . "correos_oficial_products cop ON (cop.id = cocp.id_product)
        WHERE
            date(wp.post_date)  BETWEEN '" . $from . "' AND '" . $to . "' AND wp.post_type = 'shop_order' AND wp.post_status != 'trash' AND wp.post_status != 'auto-draft'" . $like . '
        GROUP BY
            wp.ID, cocp.id_carrier
        ORDER BY
            wp.ID DESC
        LIMIT ' . $init . ',' . $perPage;
		$values = $this->db->get_results($sql, ARRAY_A);

		// Lista de remitentes
		$senders = array();
		$sender_default = '';
		foreach (CorreosOficialSendersDao::getSendersWithCodes() as $sender) {

			$senderCompany = '';
			if ($sender['correos_code'] != 0 && $sender['cex_code'] == 0) {
				$senderCompany = 'Correos';
			} elseif ($sender['cex_code'] != 0 && $sender['correos_code'] == 0) {
				$senderCompany = 'CEX';
			} elseif ($sender['correos_code'] != 0 && $sender['cex_code'] != 0) {
				$senderCompany = 'all';
			}

			$senders[] = array(
				'sender_id' => $sender['id'],
				'sender_data' => array(
					'name' => $sender['sender_name'],
					'company' => $senderCompany,
					'sender_iso_code' => $sender['sender_iso_code_pais'],
					'default' => $sender['sender_default'],
				),
			);

			if ($sender['sender_default'] == 1) {
				$sender_default = $sender['id'];
			}
		}


		
		foreach ($values as $key => $value) {

			// Si es un pedido preregistrado obtenmos información de correos_oficial_orders
			if ($values[$key]['saved_carrier_type']) {
				$values[$key]['carrier_type'] = $value['saved_carrier_type'];
				$values[$key]['company'] = $value['saved_carrier_type'];

				// Obtenemos nombre del producto por su id
				$productsDao = new CorreosOficialProductsDao();
				$product = (array) reset($productsDao->getProduct($value['saved_id_product'], 'correos_oficial_products'));

				$values[$key]['name'] = $product['name'];


			} else {
				$values[$key]['carrier_type'] = $value['company'];
			}
			

			// Senders
			$values[$key]['senders'] = $senders;
			$values[$key]['sender_default'] = $sender_default;

		}

		unset($sql);
		$orders = array();
		foreach ($values as $order) {

			$sqlGetProductNames = '
            SELECT
                CASE
                    WHEN LENGTH(GROUP_CONCAT(prp.post_title SEPARATOR ", ")) > 50
                    THEN CONCAT(LEFT(GROUP_CONCAT(prp.post_title SEPARATOR ", "), 50), "...")
                ELSE
                    GROUP_CONCAT(prp.post_title SEPARATOR ", ")
                END AS concated
            FROM
                ' . $this->dt_pref . 'posts wp
            LEFT JOIN
                ' . $this->dt_pref . 'wc_order_product_lookup wopl ON (wopl.order_id = wp.ID)
            LEFT JOIN
                ' . $this->dt_pref . 'posts prp ON (prp.ID = wopl.product_id)
            WHERE wp.ID = ' . (int) $order['id_order'] . '
            GROUP BY
                wp.ID';
			$productsName = $this->db->get_var($sqlGetProductNames);

			$order['products'] = $productsName;

			$order = $this->setOrderNumber($order);
			$order['reference'] = $order['order_number'] . ' ' . str_replace('wc_order_', '', $order['reference']);
			$order['order_state'] = __(ucFirst(str_replace('wc-', '', $order['order_state'])), 'woocommerce');
			$orders[] = $order;
		}
		unset($values);
		$result['datas'] = $orders;

		$sqlTotal = '
        SELECT
            wp.ID as id_order
        FROM
            ' . $this->dt_pref . 'posts wp
        LEFT JOIN
            ' . $this->dt_pref . 'wc_order_product_lookup wopl ON (wopl.order_id = wp.ID)
        LEFT JOIN
            ' . $this->dt_pref . 'posts prp ON (prp.ID = wopl.product_id)
        LEFT JOIN
            ' . $this->dt_pref . 'wc_order_stats os ON (os.order_id = wp.ID)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_orders coo ON (wp.ID = coo.id_order)
        LEFT JOIN
            ' . $this->dt_pref . 'postmeta wpm ON (wp.ID = wpm.post_id)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_carriers_products cocp ON (cocp.id_carrier = (' . $this->getOrderItemMeta() . ')
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_requests cor ON (
                (CASE WHEN cor.id_order IS NULL
                THEN cor.id_cart = ' . $cart_hash . '
                ELSE cor.id_order = wp.ID
                END)
            )
        LEFT JOIN
            ' . $this->dt_pref . "correos_oficial_products cop ON (cop.id = cocp.id_product)
        WHERE
            date(wp.post_date)  BETWEEN '" . $from . "' AND '" . $to . "' AND wp.post_type = 'shop_order' AND wp.post_status != 'trash'" . $like . '
        GROUP BY
            wp.ID, cocp.id_carrier';

		$result['total'] = count($this->db->get_results($sqlTotal, ARRAY_A));

		return $result;
	}

	private function sqlEtiquetasDataTableHPOS( $from, $to, $like, $init, $perPage, $onlyCorreos = false ) {
		$order_key = '(SELECT order_key FROM ' . $this->dt_pref . 'wc_order_operational_data WHERE order_id=wc.id)';
		$default_packages = $this->settingVar('DefaultPackages');

		$customer_first_name = '(SELECT first_name FROM ' . $this->dt_pref . "wc_order_addresses WHERE order_id=wc.id AND address_type='shipping')";
		$customer_last_name = '(SELECT last_name FROM ' . $this->dt_pref . "wc_order_addresses WHERE order_id=wc.id AND address_type='shipping')";
		$customer_address = $this->getMetaKeyValue('_shipping_address_index');

		$pickupMode = '';
		if ($onlyCorreos) {
			$pickupMode = ' AND coo.carrier_type = "Correos"';
		}

		$result = array();

		$sql = '
        SELECT
            wc.id as id_order,
            ' . $order_key . ' as reference,
            coo.shipping_number as shipping_number,
            cop.company as company,
            concat(' . $customer_first_name . ", ' '," . $customer_last_name . ') as customer_name,
            ' . $customer_address . ' as customer_address,
            wc.date_created_gmt as date_add,
            cop.id as id_product,
            cop.codigoProducto as codigoProducto,
            IFNULL(coo.bultos,' . $default_packages . ') as bultos,
            coo.pickup as pickup,
            coo.print_label as print_label,
            coo.package_size as package_size,
            cos.exp_number as first_shipping_number,
            coo.pickup_number as pickup_number,
            coo.last_status as last_status,
            coo.status as status
        FROM
            ' . $this->dt_pref . 'wc_orders wc
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_orders coo ON (wc.id = coo.id_order)
        LEFT JOIN
            ' . $this->dt_pref . 'wc_orders_meta wcom ON (wc.id = wcom.order_id)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_products cop ON (coo.id_product = cop.id)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
        LEFT JOIN
            ' . $this->dt_pref . "wc_order_addresses woa ON (woa.order_id = wc.id)
        WHERE
            date(wc.date_created_gmt) BETWEEN '" . $from . "' AND '" . $to . "' AND coo.shipping_number != '' AND wc.type = 'shop_order' AND wc.status != 'wc-trash'" . $like . $pickupMode . '
        GROUP BY
            wc.id
        ORDER BY
            wc.id DESC
        LIMIT ' . $init . ',' . $perPage;

		$datas = $this->db->get_results($sql, ARRAY_A);

		$shippings = array();
		foreach ($datas as $shipping) {
			$sqlGetProductNames = 'SELECT
            CASE
                WHEN LENGTH(GROUP_CONCAT(woi.order_item_name SEPARATOR ", ")) > 50
                THEN CONCAT(LEFT(GROUP_CONCAT(woi.order_item_name SEPARATOR ", "), 50), "...")
            ELSE
                GROUP_CONCAT(woi.order_item_name SEPARATOR ", ")
            END AS concated
            FROM
            ' . $this->dt_pref . 'wc_orders wc
            LEFT JOIN
            ' . $this->dt_pref . 'woocommerce_order_items woi ON (woi.order_id = wc.id)
            WHERE wc.id = ' . (int) $shipping['id_order'] . ' AND woi.order_item_type = "line_item"
            GROUP BY
            wc.id;';
			$productsName = $this->db->get_var($sqlGetProductNames);

			$shipping['products'] = $productsName;

			$shipping = $this->setOrderNumber($shipping);
			$shipping['reference'] = $shipping['order_number'] . ' ' . str_replace('wc_order_', '', $shipping['reference']);
			$shippings[] = $shipping;
		}

		$result['datas'] = $shippings;
		unset($sql, $datas);

		$sqlTotal = '
        SELECT
            wc.id as id_order
        FROM
            ' . $this->dt_pref . 'wc_orders wc
        LEFT JOIN
            ' . $this->dt_pref . 'wc_orders_meta wcom ON (wc.id = wcom.order_id)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_orders coo ON (wc.id = coo.id_order)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_products cop ON (coo.id_product = cop.id)
        LEFT JOIN
            ' . $this->dt_pref . "correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
        WHERE
            date(wc.date_created_gmt) BETWEEN '" . $from . "' AND '" . $to . "' AND coo.shipping_number != '' AND wc.type = 'shop_order' AND wc.status != 'wc-trash'" . $like . $pickupMode . '
        GROUP BY
            wc.id
        ORDER BY
            wc.id DESC';

		$result['total'] = count($this->db->get_results($sqlTotal, ARRAY_A));

		return $result;
	}

	private function sqlEtiquetasDataTable( $from, $to, $like, $init, $perPage, $onlyCorreos = false ) {
		$order_key = $this->getMetaKeyValue('_order_key');
		$default_packages = $this->settingVar('DefaultPackages');
		$customer_first_name = $this->getMetaKeyValue('_shipping_first_name');
		$customer_last_name = $this->getMetaKeyValue('_shipping_last_name');
		$customer_address = $this->getMetaKeyValue('_shipping_address_1');

		$pickupMode = '';
		if ($onlyCorreos) {
			$pickupMode = ' AND coo.carrier_type = "Correos"';
		}

		$result = array();

		$sql = "SELECT 
        coo.id_order AS id_order,
        coo.reference AS reference,
        coo.shipping_number AS shipping_number,
        coo.carrier_type AS company,
        coo.pickup AS pickup,
        coo.print_label AS print_label,
        coo.package_size AS package_size,
        coo.pickup_number AS pickup_number,
        coo.last_status AS last_status,
        coo.status AS status,
        IFNULL(coo.bultos, $default_packages) AS bultos,
        wpo.post_date AS date_add,
        cop.id AS id_product,
        cop.codigoProducto AS codigoProducto,
        cos.exp_number AS first_shipping_number,
        CONCAT(
            (SELECT meta_value FROM {$this->dt_pref}postmeta WHERE post_id = wpo.ID AND meta_key = '_shipping_first_name'), ' ',
            (SELECT meta_value FROM {$this->dt_pref}postmeta WHERE post_id = wpo.ID AND meta_key = '_shipping_last_name')
        ) AS customer_name,
        (SELECT meta_value FROM {$this->dt_pref}postmeta WHERE post_id = wpo.ID AND meta_key = '_shipping_address_1') AS customer_address
        FROM  
            {$this->dt_pref}correos_oficial_orders AS coo
        LEFT JOIN  
            {$this->dt_pref}posts AS wpo ON wpo.ID = coo.id_order
        LEFT JOIN 
            {$this->dt_pref}correos_oficial_products AS cop ON cop.id = coo.id_product 
        LEFT JOIN {$this->dt_pref}correos_oficial_saved_orders cos ON coo.shipping_number = cos.exp_number
        LEFT JOIN {$this->dt_pref}wc_order_stats os ON os.order_id = wpo.ID
        WHERE DATE(wpo.post_date) BETWEEN '$from' AND '$to' 
        AND coo.shipping_number != '' 
        AND wpo.post_type = 'shop_order' 
        AND os.status != 'wc-trash' $like $pickupMode
        GROUP BY wpo.ID
        ORDER BY wpo.ID DESC
        LIMIT $init, $perPage";


		$datas = $this->db->get_results($sql, ARRAY_A);

		$shippings = array();
		foreach ($datas as $shipping) {
			$sqlGetProductNames = '
            SELECT
                CASE
                    WHEN LENGTH(GROUP_CONCAT(prp.post_title SEPARATOR ", ")) > 50
                    THEN CONCAT(LEFT(GROUP_CONCAT(prp.post_title SEPARATOR ", "), 50), "...")
                ELSE
                    GROUP_CONCAT(prp.post_title SEPARATOR ", ")
                END AS concated
            FROM
                ' . $this->dt_pref . 'posts wp
            LEFT JOIN
                ' . $this->dt_pref . 'wc_order_product_lookup wopl ON (wopl.order_id = wp.ID)
            LEFT JOIN
                ' . $this->dt_pref . 'posts prp ON (prp.ID = wopl.product_id)
            WHERE wp.ID = ' . (int) $shipping['id_order'] . '
            GROUP BY
                wp.ID';
			$productsName = $this->db->get_var($sqlGetProductNames);

			$shipping['products'] = $productsName;

			$shipping = $this->setOrderNumber($shipping);
			$shipping['reference'] = $shipping['order_number'] . ' ' . str_replace('wc_order_', '', $shipping['reference']);
			$shippings[] = $shipping;
		}

		$result['datas'] = $shippings;
		unset($sql, $datas);

		$sqlTotal = '
        SELECT
            wp.ID as id_order
        FROM
            ' . $this->dt_pref . 'posts wp
        LEFT JOIN
            ' . $this->dt_pref . 'wc_order_product_lookup wopl ON (wopl.order_id = wp.ID)
        LEFT JOIN
            ' . $this->dt_pref . 'posts prp ON (prp.ID = wopl.product_id)
        LEFT JOIN
            ' . $this->dt_pref . 'postmeta wpm ON (wp.ID = wpm.post_id)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_orders coo ON (wp.ID = coo.id_order)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_products cop ON (coo.id_product = cop.id)
        LEFT JOIN
            ' . $this->dt_pref . 'correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
        LEFT JOIN
            ' . $this->dt_pref . "wc_order_stats os ON (os.order_id = wp.ID)
        WHERE
            date(wp.post_date) BETWEEN '" . $from . "' AND '" . $to . "' AND coo.shipping_number != '' AND wp.post_type = 'shop_order' AND os.status != 'wc-trash'" . $like . $pickupMode . '
        GROUP BY
            wp.ID
        ORDER BY
            wp.ID DESC';

		$result['total'] = count($this->db->get_results($sqlTotal, ARRAY_A));

		return $result;
	}

	private function sqlShippingCustomDocsHPOS( $from, $to, $like, $init, $perPage ) {

		$order_key = '(SELECT order_key FROM ' . $this->dt_pref . 'wc_order_operational_data WHERE order_id=wc.id)';
		$default_packages = $this->settingVar('DefaultPackages');
		$shipping_country = '(SELECT country FROM ' . $this->dt_pref . "wc_order_addresses WHERE order_id=wc.id AND address_type='shipping')";
		$customer_first_name = '(SELECT first_name FROM ' . $this->dt_pref . "wc_order_addresses WHERE order_id=wc.id AND address_type='shipping')";
		$customer_last_name = '(SELECT last_name FROM ' . $this->dt_pref . "wc_order_addresses WHERE order_id=wc.id AND address_type='shipping')";
		$customer_address = $this->getMetaKeyValue('_shipping_address_index');

		$result = array();

		$sql = '
            SELECT
                wc.id as id_order,
                ' . $order_key . ' as reference,
                coo.shipping_number as shipping_number,
                cop.company as company,
                concat(' . $customer_first_name . ", ' ', " . $customer_last_name . ') as customer_name,
                ' . $customer_address . ' as customer_address,
                ' . $shipping_country . ' as customer_country,
                wc.date_created_gmt as date_add,
                cos.exp_number as first_shipping_number,
                IFNULL(coo.bultos,' . $default_packages . ') as bultos,
                coo.require_customs_doc as require_customs_doc
            FROM
                ' . $this->dt_pref . 'wc_orders wc
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_orders coo ON (wc.id = coo.id_order)
            LEFT JOIN
                ' . $this->dt_pref . 'wc_orders_meta wcom ON (wc.id = wcom.order_id)
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_products cop ON (coo.id_product = cop.id)
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
            LEFT JOIN
                ' . $this->dt_pref . "wc_order_stats os ON (os.order_id = wc.id)
            WHERE
                date(wc.date_created_gmt) BETWEEN '" . $from . "' AND '" . $to . "' AND ( coo.require_customs_doc = 1) AND coo.shipping_number != '' AND cop.company='Correos'
                AND wc.type = 'shop_order' AND os.status != 'wc-trash'" . $like . '
            GROUP BY
            wc.id
            ORDER BY wc.id DESC
            LIMIT ' . $init . ',' . $perPage;

		$datas = $this->db->get_results($sql, ARRAY_A);

		$shippings = array();
		foreach ($datas as $shipping) {
			$shipping = $this->setOrderNumber($shipping);
			$shipping['reference'] = $shipping['order_number'] . ' ' . str_replace('wc_order_', '', $shipping['reference']);
			$shippings[] = $shipping;
		}

		$result['datas'] = $shippings;
		unset($sql, $datas);

		$totalSql = '
        SELECT
                wc.id as id_order
            FROM
                ' . $this->dt_pref . 'wc_orders wc
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_orders coo ON (wc.id = coo.id_order)
            LEFT JOIN
                ' . $this->dt_pref . 'wc_orders_meta wcom ON (wc.id = wcom.order_id)
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_products cop ON (coo.id_product = cop.id)
            LEFT JOIN
                ' . $this->dt_pref . "correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
            WHERE
                date(wc.date_created_gmt) BETWEEN '" . $from . "' AND '" . $to . "' AND ( coo.require_customs_doc = 1) AND coo.shipping_number != '' AND cop.company='Correos'
                AND wc.type = 'shop_order' AND wc.status != 'wc-trash'" . $like . '
            GROUP BY
                wc.id';

		$result['total'] = count($this->db->get_results($totalSql, ARRAY_A));

		return $result;
	}

	private function sqlShippingCustomDocs( $from, $to, $like, $init, $perPage ) {
		$order_key = $this->getMetaKeyValue('_order_key');
		$default_packages = $this->settingVar('DefaultPackages');
		$shipping_country = $this->getMetaKeyValue('_shipping_country');
		$customer_first_name = $this->getMetaKeyValue('_shipping_first_name');
		$customer_last_name = $this->getMetaKeyValue('_shipping_last_name');
		$customer_address = $this->getMetaKeyValue('_shipping_address_1');

		$result = array();

		$sql = '
            SELECT
                wp.ID as id_order,
                ' . $order_key . ' as reference,
                coo.shipping_number as shipping_number,
                cop.company as company,
                concat(' . $customer_first_name . ", ' ', " . $customer_last_name . ') as customer_name,
                ' . $customer_address . ' as customer_address,
                ' . $shipping_country . ' as customer_country,
                wp.post_date as date_add,
                cos.exp_number as first_shipping_number,
                IFNULL(coo.bultos,' . $default_packages . ') as bultos,
                coo.require_customs_doc as require_customs_doc
            FROM
                ' . $this->dt_pref . 'posts wp
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_orders coo ON (wp.ID = coo.id_order)
            LEFT JOIN
                ' . $this->dt_pref . 'postmeta wpm ON (wp.ID = wpm.post_id)
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_products cop ON (coo.id_product = cop.id)
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
            LEFT JOIN
                ' . $this->dt_pref . "wc_order_stats os ON (os.order_id = wp.ID)
            WHERE
                date(wp.post_date) BETWEEN '" . $from . "' AND '" . $to . "' AND ( coo.require_customs_doc = 1) AND coo.shipping_number != '' AND cop.company='Correos'
                AND wp.post_type = 'shop_order' AND os.status != 'wc-trash'" . $like . '
            GROUP BY
                wp.ID
            ORDER BY wp.ID DESC
            LIMIT ' . $init . ',' . $perPage;

		$datas = $this->db->get_results($sql, ARRAY_A);

		$shippings = array();
		foreach ($datas as $shipping) {
			$shipping = $this->setOrderNumber($shipping);
			$shipping['reference'] = $shipping['order_number'] . ' ' . str_replace('wc_order_', '', $shipping['reference']);
			$shippings[] = $shipping;
		}

		$result['datas'] = $shippings;
		unset($sql, $datas);

		$totalSql = '
        SELECT
                wp.ID as id_order
            FROM
                ' . $this->dt_pref . 'posts wp
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_orders coo ON (wp.ID = coo.id_order)
            LEFT JOIN
                ' . $this->dt_pref . 'postmeta wpm ON (wp.ID = wpm.post_id)
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_products cop ON (coo.id_product = cop.id)
            LEFT JOIN
                ' . $this->dt_pref . 'correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
            LEFT JOIN
                ' . $this->dt_pref . "wc_order_stats os ON (os.order_id = wp.ID)
            WHERE
                date(wp.post_date) BETWEEN '" . $from . "' AND '" . $to . "' AND ( coo.require_customs_doc = 1) AND coo.shipping_number != '' AND cop.company='Correos'
                AND wp.post_type = 'shop_order' AND os.status != 'wc-trash'" . $like . '
            GROUP BY
                wp.ID';

		$result['total'] = count($this->db->get_results($totalSql, ARRAY_A));

		return $result;
	}
	private function getDataLike( $name, $value ) {

		$columnMappings = array(
			//Gestion masiva envios.
			'id_order' => 'wp.ID',
			'reference' => 'wp.post_password',
			'products' => 'prp.post_title',
			'first_shipping_number' => 'coo.shipping_number',
			'shipping_number' => 'coo.shipping_number',
			'carrier_type' => 'cop.name',
			'order_state' => 'os.status',
			'cliente' => 'wpm.meta_value',
			'date_add' => 'wp.post_date',
			'office' => 'cor.reference_code',
			'name' => 'cop.name',
			'id_product' => 'cop.name',
			'bultos' => 'coo.bultos',
			'AT_code' => 'coo.AT_code',
			//Reimpresion de etiquetas
			'company' => 'cop.company',
			'customer_name' => 'wpm.meta_value',
			'customer_address' => 'wpm.meta_value',
			'print_label' => 'coo.print_label',
			//Recogidas
			'package_size' => 'coo.package_size',
			'pickup' => 'coo.pickup_number',
			//Doc Aduanera
			'customer_country' => $this->getMetaKeyValue('_shipping_country'),
		);

		$output = array();

		if (isset($columnMappings[$name])) {
			if ($name === 'order_state') {
				$order_statuses = wc_get_order_statuses();
				foreach ($order_statuses as $status_key => $status_label) {
					if (stripos($status_label, $value) !== false) {
						$output[$columnMappings[$name]][] = $status_key;
					}
				}
			} elseif ($name === 'print_label') {
				$output[$columnMappings[$name]][] = ( strtolower($value) === 'si' ) ? 'S' : 'N';
			} else {
				$output[$columnMappings[$name]][] = $value;
			}
		}

		return $output;
	}

	protected function gestionTab( $tab, $from, $to, $like, $init, $perPage, $onlyCorreos = false ) {

		if (wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()) {

			switch ($tab) {
				case 'GestionDataTable':
					$return = $this->sqlGestionDataTableHPOS($from, $to, $like, $init, $perPage);
					break;
				case 'EtiquetasDataTable':
					$return = $this->sqlEtiquetasDataTableHPOS($from, $to, $like, $init, $perPage, $onlyCorreos);
					break;
				case 'DocAduaneraDataTable':
					$return = $this->sqlShippingCustomDocsHPOS($from, $to, $like, $init, $perPage);
					break;
			}

		} else {

			switch ($tab) {
				case 'GestionDataTable':
					$return = $this->sqlGestionDataTable($from, $to, $like, $init, $perPage);
					break;
				case 'EtiquetasDataTable':
					$return = $this->sqlEtiquetasDataTable($from, $to, $like, $init, $perPage, $onlyCorreos);
					break;
				case 'DocAduaneraDataTable':
					$return = $this->sqlShippingCustomDocs($from, $to, $like, $init, $perPage);
					break;
			}

		}

		return $return;
	}

	private function getMetaKeyValue( $meta_key ) {

		return wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
		? '(SELECT meta_value  FROM ' . $this->dt_pref . "wc_orders_meta WHERE order_id=wc.id AND meta_key='" . $meta_key . "')"
		: '(SELECT meta_value  FROM ' . $this->dt_pref . "postmeta WHERE post_id=wp.ID AND meta_key='" . $meta_key . "')";
		// return "(SELECT meta_value  FROM " . $this->dt_pref . "postmeta WHERE post_id=wp.ID AND meta_key='" . $meta_key . "')";
	}

	public function settingVar( $name ) {
		if (isset($name) && !empty($name)) {
			$query = 'SELECT value FROM ' . $this->dt_pref . 'correos_oficial_configuration' . " WHERE name = '" . $name . "'";
			return $this->db->get_var($query);
		}

		return false;
	}

	private function getOrderItemMeta() {
		return 'SELECT meta_value FROM ' . $this->dt_pref . 'woocommerce_order_itemmeta woim
            JOIN ' . $this->dt_pref . "woocommerce_order_items woi ON woim.order_item_id = woi.order_item_id
            WHERE order_id=wp.ID and meta_key = 'instance_id' LIMIT 1)";
	}

	public function setOrderNumber( $order ) {
		$order2 = wc_get_order($order['id_order']);
		$order_number = $order2->get_order_number();
		$order['order_number'] = $order_number;
		$order['post_id']=$order['id_order'];

		return $order;
	}
}

/*$dt = new CorreosDataTableController();

add_action('wp_ajax_dataTableAjax', array( $dt, 'dataTable_callback' ));
add_action('wp_ajax_nopriv_dataTableAjax', array( $dt, 'dataTable_callback' ));*/
