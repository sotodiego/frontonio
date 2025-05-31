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

class CorreosOficialUtilitiesDao extends CorreosOficialDAO
{
    private $orders_table = 'correos_oficial_orders';
    private $pickup_returns_table = 'correos_oficial_pickups_returns';
    private $saved_orders_table = 'correos_oficial_saved_orders';

    public function __construct()
    {
        parent::__construct();
    }

    /* **********************************************************************************************************
     * UTILIDADES - GESTÍON MASIVA PEDIDOS - IMPRESIÓN ETIQUETAS - RESUMEN - REGOGIDAS - DOC. ADUANERA
     ********************************************************************************************************* */

    /**
     * Función para obtener pedidos (all) para datatable gestíon masiva
     * @param $fecha desde:
     * @param $fecha hasta:
     */
    public function getOrdersForMassManagement($date_from, $date_to)
    {
        $default_packages = $this->readSettings('DefaultPackages');

        if ($date_from != null && $date_to != null) {
            $sql = "SELECT po.id_order,
			po.reference,
            po.id_carrier,
            po.current_state,
            po.date_add,

			coo.shipping_number,
            coo.AT_code as AT_code,
            IF (coo.id_product IS NULL, (SELECT id FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_products WHERE id_carrier = po.id_carrier LIMIT 1), coo.id_product) as id_product,

            cos.exp_number as first_shipping_number,
            cor.reference_code as office,
            osl.name as order_state,

            IF (prd.company IS NULL, (SELECT name FROM " . CorreosOficialUtils::getPrefix() . "carrier WHERE id_carrier = cprd.id_carrier), prd.company) as carrier_type,
            IF (prd.name IS NULL, (SELECT name FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_products WHERE id = cprd.id_product), prd.name) as name,
            prd.company as company,
			prd.max_packages,
            prd.codigoProducto,
            prd.product_type,

            cprd.id_product as id_product_custom,
            IF (prd.max_packages IS NULL, (SELECT max_packages FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_products WHERE id = cprd.id_product), null) as max_packages_custom,

			concat(a.firstname, ' ', a.lastname) as cliente,
            c.iso_code as delivery_iso_code,
            c.id_zone,
            IF(coo.shipping_number !='',
            (SELECT sender_iso_code_pais FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders WHERE id=coo.id_sender),
            (SELECT sender_iso_code_pais FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders WHERE sender_default=1)
            ) as sender_iso_code,

            IFNULL(coo.bultos,$default_packages->value) as bultos

            FROM " . CorreosOficialUtils::getPrefix() . "orders po

            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "address a ON (a.id_address = po.id_address_delivery)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "country c ON (c.id_country = a.id_country)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders coo ON (po.id_order = coo.id_order)
			LEFT JOIN " . CorreosOficialUtils::getPrefix() . "customer cus ON (cus.id_customer = po.id_customer)
			LEFT JOIN " . CorreosOficialUtils::getPrefix() . "order_state_lang osl ON (osl.id_order_state = po.current_state)
			LEFT JOIN " . CorreosOficialUtils::getPrefix() . "lang la ON (la.id_lang = osl.id_lang)
			LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_products prd ON ( IF (coo.id_product IS NULL, po.id_carrier = prd.id_carrier, coo.id_product = prd.id) )
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products cprd ON ( cprd.id_carrier = po.id_carrier ) AND ( cprd.id_zone = c.id_zone )
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders cose ON (cose.id = coo.id_sender)
            LEFT JOIN  " . CorreosOficialUtils::getPrefix() . "correos_oficial_requests cor ON (cor.id_cart = po.id_cart)

            WHERE date(po.date_add) BETWEEN '$date_from' AND '$date_to' AND la.iso_code = 'es' GROUP BY po.id_order";

            $this->executeQuery("SET sql_mode=''");
            $result = $this->getRecordsWithQuery($sql, true);
        }
        $orders = [];
        foreach ($result as $order) {
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
        if ($date_from != null && $date_to != null) {
            $sql = "SELECT po.id_order,
         po.reference,
         coo.shipping_number,
         prd.company,
         concat(adr.firstname,' ',adr.lastname) as customer_name,
         adr.address1 as customer_address,
         po.date_add,
         coo.id_product,
         prd.codigoProducto,
         coo.bultos,
         coo.pickup,
         coo.print_label,
         coo.package_size,
         cos.exp_number as first_shipping_number,
         coo.pickup_number as pickup_number,
         coo.last_status as last_status,
         coo.status as status
         FROM " . CorreosOficialUtils::getPrefix() . "orders po
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders coo ON (po.id_order = coo.id_order)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "address adr ON (adr.id_address = po.id_address_delivery)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_products prd ON (coo.id_product = prd.id)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
            WHERE date(po.date_add) BETWEEN '$date_from' AND '$date_to' AND coo.shipping_number != ''
         GROUP BY po.id_order";
            $result = $this->getRecordsWithQuery($sql, true);
        }
        $shippings = [];
        foreach ($result as $shipping) {
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
        if ($date_from != null && $date_to != null) {
            $sql = "SELECT po.id_order,
         po.reference,
         coo.shipping_number,
         prd.company,
         concat(adr.firstname,' ',adr.lastname) as customer_name,
         adr.address1 as customer_address,
         ctry.iso_code as customer_country,
         po.date_add,
         cos.exp_number as first_shipping_number,
         coo.bultos,
         coo.require_customs_doc
         FROM " . CorreosOficialUtils::getPrefix() . "orders po
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders coo ON (po.id_order = coo.id_order)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "address adr ON (adr.id_address = po.id_address_delivery)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "country ctry ON (ctry.id_country = adr.id_country)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_products prd ON (coo.id_product = prd.id)
            LEFT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_orders cos ON (coo.shipping_number = cos.exp_number)
         WHERE date(po.date_add) BETWEEN '$date_from' AND '$date_to' AND ( coo.require_customs_doc = 1) AND coo.shipping_number != '' AND prd.company='Correos'
         GROUP BY po.id_order";
            $result = $this->getRecordsWithQuery($sql, true);
        }
        $shippings = [];
        foreach ($result as $shipping) {
            $shippings[] = $shipping;
        }
        return $shippings;
    }

    public function getShippingNumbersByExpediton($expedition)
    {
        $shipping_number_array = array();

        $expeditions = $this->readRecord($this->saved_orders_table, "WHERE exp_number='$expedition'", 'shipping_number');
        foreach ($expeditions as $expedition) {
            $shipping_number_array['shipping_number'] = $expedition->shipping_number;
        }
        return $shipping_number_array;
    }

    public function getCarrierTypeByOrderId($order_id) {
        $orderInfo = $this->readRecord($this->orders_table, "WHERE id_order='$order_id'", 'carrier_type');
        if ($orderInfo) {
            $carrier_type = $orderInfo[0]->carrier_type; // Acceder a la propiedad como objeto
            return $carrier_type;
        }
    }

    /* **********************************************************************************************************
     *  COMÚN
     ********************************************************************************************************* */
    public function insertOrder($data)
    {
        $this->insertRecord($this->orders_table, $data);
    }

    /**
     * Función que guarda datos del envio preregistrado: id_order, expedition_number, shipping_numbers, labels.
     * @param $table 'orders/returns'
     * @param $data 'datos del pedido devueltos por el WS'
     */
    public function insertDataOrder($table, $data)
    {
        $this->insertRecord($table, $data);
    }

    /**
     * Función que obtiene el número de envío mediante el número de expedición.
     * @param $expedition_number
     * @param $mode_pickup
     * @return $shipping_number 'numero de envio'
     */
    public function getShippingNumbersByExp($expedition_number, $mode_pickup = null)
    {

        if ($mode_pickup == 'pickup' || $mode_pickup == null) {
            $sql = "SELECT coso.shipping_number FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_orders coso
        RIGHT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders coo ON (coo.shipping_number = coso.exp_number)
        WHERE coso.exp_number = '" . $expedition_number . "'";
        } elseif ($mode_pickup == 'return') {
            $sql = "SELECT shipping_number FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_returns
            WHERE exp_number='$expedition_number'";
        }

        return $this->getRecordsWithQuery($sql, true);
    }

    /* **********************************************************************************************************
     *  RECOGIDAS
     ********************************************************************************************************* */

    public function savePickup($data)
    {
        $query = "UPDATE " . CorreosOficialUtils::getPrefix() . $this->orders_table . " SET
         pickup = '1',
         pickup_number = '$data[pickup_number]',
         pickup_date = '$data[pickup_date]',
         pickup_from_hour = '$data[pickup_from_hour]',
         pickup_to_hour = '$data[pickup_to_hour]',
         package_size = '$data[package_size]',
         print_label = '$data[print_label]',
         pickup_status = '$data[pickup_status]'
         WHERE id_order = '$data[id_order]'";
        $this->launchQuery($query);
    }

    public function cancelPickup($id_order)
    {
        $query = "UPDATE " . CorreosOficialUtils::getPrefix() . $this->orders_table . " SET
         pickup = 0,
         pickup_number = '',
         package_size = 0,
         print_label = 'N',
         pickup_status = 'Anulado'
         WHERE id_order = $id_order";
        $this->launchQuery($query);
    }

    /* **********************************************************************************************************
     *  RECOGIDAS DEVOLUCIONES
     ********************************************************************************************************* */

    public function saveReturnPickup($data)
    {
        $this->insertRecord($this->pickup_returns_table, $data);
    }

    public function cancelReturnPickup($id_order)
    {
        $table = 'correos_oficial_pickups_returns';
        $query = "DELETE FROM " . CorreosOficialUtils::getPrefix() . $table . " WHERE id_order = '$id_order'";
        $this->launchQuery($query);
    }

    /* **********************************************************************************************************
     *  PEDIDO
     ********************************************************************************************************* */
    public function cancelOrder($expedition_number)
    {
        $query = "DELETE FROM " . CorreosOficialUtils::getPrefix() . $this->orders_table . " WHERE shipping_number = '$expedition_number'";
        $this->launchQuery($query);

        $query2 = "DELETE FROM " . CorreosOficialUtils::getPrefix() . $this->saved_orders_table . " WHERE exp_number = '$expedition_number'";
        $this->launchQuery($query2);
    }

    public function getShippingNumbersByIdOrderForSavedOrder($id_order) {

        $table = CorreosOficialUtils::getPrefix() . 'correos_oficial_saved_orders';
        $final = $this->selectOnlyLastOrder($table, $id_order);
        $sql = "";
        
        if (!empty($final)) {
            $sql = "SELECT shipping_number FROM $table WHERE id IN ($final)";
        } else {
            $sql = "SELECT shipping_number FROM $table WHERE id_order = $id_order";
        }

        return $this->getRecordsWithQuery($sql, true);
    }

    public function selectOnlyLastOrder($table, $id_order) {
        $bad_ones = array();

        /**
         * Devuelve los registros duplicados de cada envío
         */
        $sql = "SELECT * FROM $table WHERE id_order = $id_order ORDER BY id DESC";

        $records2 = $this->getRecordsWithQuery($sql, true);

        $i = 0;
        foreach ($records2 as $record) {

            if ($i > 0 && $records2[0]['exp_number'] != $record['exp_number']) {
                array_push($bad_ones, $record['id']);
            }
            $i++;
        }

        return join(",", $bad_ones);
    }

    /* **********************************************************************************************************
     *  DEVOLUCIONES
     ********************************************************************************************************* */

    public function deleteReturns($id_order)
    {
        $table = 'correos_oficial_returns';
        $query = "DELETE FROM " . CorreosOficialUtils::getPrefix() . $table . " WHERE id_order = '$id_order'";
        $this->launchQuery($query);

        $table2 = 'correos_oficial_saved_returns';
        $query2 = "DELETE FROM " . CorreosOficialUtils::getPrefix() . $table2 . " WHERE id_order = '$id_order'";
        $this->launchQuery($query2);
    }

    public function insertReturn($data)
    {
        $table = 'correos_oficial_returns';

        $this->insertRecord($table, $data);
    }

    public function getShippingNumbersByIdOrderForReturns($id_order)
    {
        $sql = "SELECT cosr.shipping_number FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_returns cosr
      RIGHT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_returns cor ON (cor.shipping_number = cosr.exp_number)
      WHERE cosr.id_order = '" . $id_order . "'";
        return $this->getRecordsWithQuery($sql, true);
    }

    public function getExpeditionNumberByIdOrderForReturn($id_order) {
        global $wpdb;
        $table = $wpdb->prefix . 'correos_oficial_saved_returns';
        $query = $wpdb->prepare("SELECT exp_number FROM $table WHERE id_order = %d", $id_order);
        return $wpdb->get_var($query);
    }

    public function getExpeditionNumberByIdOrderForSavedOrder($id_order) {
        global $wpdb;
        $table = $wpdb->prefix . 'correos_oficial_saved_orders';
        $final = $this->selectOnlyLastOrder($table, $id_order);

        if (!empty($final)) {
            return $wpdb->get_var("SELECT exp_number FROM $table WHERE id IN ($final)");
        } else {
            $query = $wpdb->prepare("SELECT exp_number FROM $table WHERE id_order = %d", $id_order);
            return $wpdb->get_var($query);
        }
    }

    /* **********************************************************************************************************
     *  REVISAR
     ********************************************************************************************************* */

    public function getCompany($id_product)
    {

        if (empty($id_product)) {
			return false;
		}

        $table = "correos_oficial_products";
        $sql = "SELECT company FROM " . CorreosOficialUtils::getPrefix() . $table . " WHERE id = '" . $id_product . "'";
        $record = $this->getRecordsWithQuery($sql, true);
        return $record[0];
    }

    public function getCompanyReturn($order_id)
    {
        $table = "correos_oficial_returns";
        $sql = "SELECT carrier_type FROM " . CorreosOficialUtils::getPrefix() . $table . " WHERE id_order = '" . $order_id . "'";
        $record = $this->getRecordsWithQuery($sql, true);
        return $record[0];
    }

    public function getDataClient($company, $id_order = false, $id_sender = false)
    {

		if (empty($company)) {
			return false;
		}

        $result = [];
        if ($id_order) {
            $sql = "
                SELECT
                    c.*
                FROM
                    " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders a
                LEFT JOIN
                    " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders b ON a.id_sender = b.id
                LEFT JOIN
                    " . CorreosOficialUtils::getPrefix() . "correos_oficial_codes c ON b." . strtolower($company) . "_code = c.id
                WHERE
                    a.id_order = " . (int) $id_order;

            $result = $this->getRecordsWithQuery($sql, true);
        } elseif (!$id_order && $id_sender) {
            $sql = "
                SELECT
                    b.*
                FROM
                    " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders a
                LEFT JOIN
                    " . CorreosOficialUtils::getPrefix() . "correos_oficial_codes b ON a." . strtolower($company) . "_code = b.id
                WHERE
                    a.id = " . (int) $id_sender;
        $result = $this->getRecordsWithQuery($sql, true);
        }

        if ((!$id_order && !$id_sender) || empty($result)) {
            $sql = "
                SELECT
                    b.*
                FROM
                    " . CorreosOficialUtils::getPrefix() . "correos_oficial_senders a
                LEFT JOIN
                    " . CorreosOficialUtils::getPrefix() . "correos_oficial_codes b ON a." . strtolower($company) . "_code = b.id
                WHERE
                    a.sender_default = 1";

            $result = $this->getRecordsWithQuery($sql, true);
        }
        return $result;
    }

    /**
     * Obtiene etiqueta
     * @param $shipping_number
     * @return $label 'etiqueta'
     */
    /*public function getLabel($shipping_number)
    {
        $sql = "SELECT coso.label FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_saved_orders coso
      RIGHT JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_orders coo ON (coo.shipping_number = coso.exp_number)
      WHERE coso.exp_number = '" . $shipping_number . "'";
        $class = new CorreosSoap();
        $class->SolicitudEtiquetaOp($shipping_number);
        return $this->getRecordsWithQuery($sql, true);
    }
*/
    /**
     * Ejecuta la consulta
     * @param $query Consulta SQL a ser ejecutada.
     */
    public function launchQuery($query)
    {
        $this->executeQuery($query);
    }
}
