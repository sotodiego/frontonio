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
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Correos/CorreosDaoObject.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__) . '/../../ecommerce_common_lib/CorreosOficialUtils.php';

class CorreosOficialUtilitiesDaoWC extends CorreosOficialUtilitiesDao
{

    public function __construct()
    {
        parent::__construct();
    }

    /* **********************************************************************************************************
     * UTILIDADES - GESTÍON MASIVA PEDIDOS - IMPRESIÓN ETIQUETAS - RESUMEN - REGOGIDAS - DOC. ADUANERA
     ********************************************************************************************************* */

    /**
     * Obtenemos datos de la tabla posts_meta
     * @param string $meta_key
     * @return subconsulta SQL
     */
    private static function getMetaKeyValue($meta_key)
    {
        return "(SELECT meta_value  FROM " . CorreosOficialUtils::getPRefix() . "postmeta WHERE post_id=wp.ID AND meta_key='" . $meta_key . "')";
    }

    /**
     * Obtenemos datos de la tabla wp_woocommerce_order_itemmeta
     * @param string $meta_key
     * @return subconsulta SQL
     */
    private static function getOrderItemMeta()
    {
        return "SELECT meta_value FROM " . CorreosOficialUtils::getPrefix() . "woocommerce_order_itemmeta woim
            JOIN " . CorreosOficialUtils::getPrefix() . "woocommerce_order_items woi ON woim.order_item_id = woi.order_item_id
            WHERE order_id=wp.ID and meta_key = 'instance_id' LIMIT 1)";
    }

    /**
     * Función para obtener pedidos (all) para datatable gestíon masiva
     * @param $fecha desde:
     * @param $fecha hasta:
     */
    public function getOrdersForMassManagement($date_from, $date_to)
    {

        $order_key = self::getMetaKeyValue('_order_key');
        $shipping_firstname = self::getMetaKeyValue('_shipping_first_name');
        $shipping_lastname = self::getMetaKeyValue('_shipping_last_name');
        $billing_country = self::getMetaKeyValue('_billing_country');
        $cart_hash = self::getMetaKeyValue('_cart_hash');

        $default_packages = $this->readSettings('DefaultPackages');

        if ($date_from != null && $date_to != null) {
            $sql = "SELECT
            wp.ID as id_order,
            " . $order_key . " as reference,
            cocp.id_carrier as id_carrier,
            '10' as 'current_sate',
            wp.post_date as date_add,
            coo.shipping_number as shipping_number,
            coo.AT_code as AT_code,
            cop.id as id_product,
            cos.exp_number as first_shipping_number,
            cor.reference_code as office,

            IF (coo.last_status IS NULL, (SELECT status FROM " . CorreosOficialUtils::getPrefix() . "wc_order_stats WHERE order_id = wp.ID  LIMIT 1),coo.last_status) as order_state,
            (SELECT order_item_name FROM " . CorreosOficialUtils::getPrefix() . "woocommerce_order_items WHERE order_id = wp.ID AND order_item_type = 'shipping'  LIMIT 1) as carrier_type,

            IF (cop.name IS NULL, (SELECT name FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_products WHERE id = cocp.id_product LIMIT 1), cop.name) as name,
            cop.company as company,
            cop.max_packages as max_packages,
            cop.codigoProducto as codigoProducto,
            cop.product_type as product_type,
            cocp.id_product as id_product_custom,

            IF (cop.max_packages IS NULL, (SELECT max_packages FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_products WHERE id = cocp.id_product LIMIT 1), null) as max_packages_custom,
            concat($shipping_firstname, ' ', $shipping_lastname) as cliente,
            " . $billing_country . " as delivery_iso_code,

            IF(coo.shipping_number !='',
            (SELECT sender_iso_code_pais FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders WHERE id=coo.id_sender),
            (SELECT sender_iso_code_pais FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders WHERE sender_default=1)
            ) as sender_iso_code,

            IFNULL(coo.bultos,$default_packages->value) as bultos

            FROM " . CorreosOficialUtils::getPrefix() . "posts wp

            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "wc_order_stats os ON (os.order_id = wp.ID)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders coo ON (wp.ID = coo.id_order)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products cocp ON (cocp.id_carrier = (" . $this->getOrderItemMeta() . ")
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_requests cor ON (
                (CASE WHEN cor.id_order IS NULL
                THEN cor.id_cart = " . $cart_hash . "
                ELSE cor.id_order = wp.ID
                END)
                )
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_products cop ON (cop.id = cocp.id_product)

            WHERE date(wp.post_date)  BETWEEN '$date_from' AND '$date_to' AND wp.post_type = 'shop_order' AND wp.post_status != 'trash'
            GROUP BY wp.ID, cocp.id_carrier";
            $this->executeQuery("SET sql_mode=''");

            $result = $this->getRecordsWithQuery($sql, true);
        }
        $orders = [];
        foreach ($result as $order) {
            $order = $this->setOrderNumber($order);
            $order['reference'] = $order['order_number']. " ".str_replace('wc_order_', '', $order['reference']);
            $order['order_state'] = __(ucFirst(str_replace('wc-', '', $order['order_state'])), 'woocommerce');
            $orders[] = $order;
        }
        return $orders;
    }

    /**
     * Función para obtener pedidos preregistrados para datatables Impresión de etiquetas - Resumen - Recogidas
     * @param $fecha desde:
     * @param $fecha hasta:
     */
    public function getShippings($date_from, $date_to)
    {
        $order_key = self::getMetaKeyValue('_order_key');
        $default_packages = $this->readSettings('DefaultPackages');
        $customer_first_name = self::getMetaKeyValue('_shipping_first_name');
        $customer_last_name = self::getMetaKeyValue('_shipping_last_name');
        $customer_address = self::getMetaKeyValue('_shipping_address_1');

        if ($date_from != null && $date_to != null) {
            $sql = "SELECT wp.ID as id_order,
            " . $order_key . " as reference,
            coo.shipping_number as shipping_number,
            cop.company as company,
            concat($customer_first_name, ' ', $customer_last_name) as customer_name,
            " . $customer_address . " as customer_address,
            wp.post_date as date_add,
            cop.id as id_product,
            cop.codigoProducto as codigoProducto,
            IFNULL(coo.bultos,$default_packages->value) as bultos,
            coo.pickup as pickup,
            coo.print_label as print_label,
            coo.package_size as package_size,
            cos.exp_number as first_shipping_number,
            coo.pickup_number as pickup_number,
            coo.last_status as last_status,
            coo.status as status

            FROM " . CorreosOficialUtils::getPrefix() . "posts wp
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders coo ON (wp.ID = coo.id_order)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_products cop ON (coo.id_product = cop.id)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "wc_order_stats os ON (os.order_id = wp.ID)
            WHERE date(wp.post_date) BETWEEN '$date_from' AND '$date_to' AND coo.shipping_number != '' AND wp.post_type = 'shop_order'
            AND os.status != 'wc-trash'
            GROUP BY wp.ID";
            $result = $this->getRecordsWithQuery($sql, true);
        }
        $shippings = [];
        foreach ($result as $shipping) {
            $shipping = $this->setOrderNumber($shipping);
            $shipping['reference'] = $shipping['order_number']. " ".str_replace('wc_order_', '', $shipping['reference']);
            $shippings[] = $shipping;
        }
        return $shippings;
    }

    /**
     * Función para obtener pedidos preregistrados que necesitan aduanas para datatables Gestión de Doc. Aduanera
     * @param $fecha desde:
     * @param $fecha hasta:
     */
    public function getShippingsCustomsDoc($date_from, $date_to)
    {
        $order_key = self::getMetaKeyValue('_order_key');
        $default_packages = $this->readSettings('DefaultPackages');
        $shipping_country = self::getMetaKeyValue('_shipping_country');
        $customer_first_name = self::getMetaKeyValue('_shipping_first_name');
        $customer_last_name = self::getMetaKeyValue('_shipping_last_name');
        $customer_address = self::getMetaKeyValue('_shipping_address_1');

        if ($date_from != null && $date_to != null) {
            $sql = "SELECT wp.ID as id_order,
            " . $order_key . " as reference,
            coo.shipping_number as shipping_number,
            cop.company as company,
            concat($customer_first_name, ' ', $customer_last_name) as customer_name,
            " . $customer_address . " as customer_address,
            " . $shipping_country . " as customer_country,
            wp.post_date as date_add,
            cos.exp_number as first_shipping_number,
            IFNULL(coo.bultos,$default_packages->value) as bultos,
            coo.require_customs_doc as require_customs_doc

            FROM " . CorreosOficialUtils::getPrefix() . "posts wp
                LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders coo ON (wp.ID = coo.id_order)
                LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_products cop ON (coo.id_product = cop.id)
                LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
                LEFT JOIN " . CorreosOficialUtils::getPrefix() . "wc_order_stats os ON (os.order_id = wp.ID)
                WHERE date(wp.post_date) BETWEEN '$date_from' AND '$date_to' AND ( coo.require_customs_doc = 1) AND coo.shipping_number != '' AND cop.company='Correos'
                AND wp.post_type = 'shop_order' AND os.status != 'wc-trash'
                GROUP BY wp.ID";
            $result = $this->getRecordsWithQuery($sql, true);
        }
        $shippings = [];
        foreach ($result as $shipping) {
            $shipping = $this->setOrderNumber($shipping);
            $shipping['reference'] = $shipping['order_number']. " ".str_replace('wc_order_', '', $shipping['reference']);
            $shippings[] = $shipping;
        }
        return $shippings;
    }

    /**
     * @param WC_Order $order objeto de tipo WC_Order
     * @return WC_Order $order El objeto de entrada pero con los índices ['order_number'] y ['post_id']
     * @since 1.3.0.7
     */
    public function setOrderNumber($order)
    {
        $order2 = wc_get_order($order['id_order']);
        $order_number = $order2->get_order_number();
        $order['order_number'] = $order_number;
        $order['post_id']=$order['id_order'];

        return $order;
    }

}
