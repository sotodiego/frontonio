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

require_once dirname(__FILE__) . '/../../ecommerce_common_lib/Dao/CorreosOficialDAO.php';

/**
 * Clase genérica para lo relacionado con pedidos o recogidas de Correos o CEX
 */
class CorreosOficialCheckoutDao extends CorreosOficialDAO
{
    /**
     * Consigue un campo de la base de datos.
     * @param string $value valor que devuelvo: * para devolver el conjunto
     * @param string $key lo que busco
     * @param string $field el campo
     * @param table: tabla
     */
    public static function getValue($value, $key, $field, $table)
    {
        $dao = new CorreosOficialDAO();
        $record = $dao->readRecord($table, " WHERE $field='$key'", $value, true);

        return $record[0];
    }

    public static function getValueConf($key)
    {
        $dao = new CorreosOficialDAO();

        return $dao->readSettings($key, true);
    }

    /**
     * @param $carrier: el id_reference del transportista asociado a la tabla
     * correos_oficial_products.
     * @return array Array con el registro del producto.
     */
    public static function getCarrierParams($carrier)
    {
        $dao = new CorreosOficialDAO();

        $sql = "SELECT * FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_products cop
        JOIN `" . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products` cocp ON cop.id = cocp.id_product
        WHERE cocp.id_carrier='" . $carrier['id_reference'] . "'";
        $result = $dao->getRecordsWithQuery($sql, true);

        if ($result) {
            return $result[0];
        }
    }

    /**
     * @param $carrier: el id_carrier del transportista asociado a la tabla
     * correos_oficial_products.
     * @return array Array con el tipo del producto.
     */
    public static function getProductType($id_carrier)
    {
        $dao = new CorreosOficialDAO();

        $sql = "SELECT product_type FROM " . CorreosOficialUtils::getPrefix() . "correos_oficial_products cop
        JOIN " . CorreosOficialUtils::getPrefix() . "correos_oficial_carriers_products cocp ON cop.id = cocp.id_product
        WHERE cocp.id_carrier='" . $id_carrier . "'";
        $result = $dao->getRecordsWithQuery($sql, true);
        if ($result) {
            return $result[0]['product_type'];
        }
    }

    public static function insertCartIntoRequests($id_cart)
    {
        return self::getValue('count(id_cart) as id_cart', $id_cart, 'id_cart', 'correos_oficial_requests');
    }

    public static function insertReferenceCodeWithOrderId($id_cart, $reference_code, $data, $id_order)
    {
        $count = self::getValue('count(id_order) as id_order', $id_order, 'id_order', 'correos_oficial_requests');

        if (empty($count['id_order'])) {
            $data = array(
                'id_cart' => $id_cart,
                'reference_code' => $reference_code,
                'data' => $data,
                'id_order' => $id_order
            );
            $dao = new CorreosOficialDAO();
            $dao->insertRecord('correos_oficial_requests', $data);
        }

    }

}
