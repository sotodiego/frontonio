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

require_once dirname(__FILE__).'/../../ecommerce_common_lib/Correos/CorreosDaoObject.php';
require_once dirname(__FILE__).'/../../ecommerce_common_lib/Dao/CorreosOficialDAO.php';
require_once dirname(__FILE__).'/../../ecommerce_common_lib/CorreosOficialUtils.php';

/**
 * Clase CorreosOficialDao
 */

class CorreosOficialOrderDao extends CorreosOficialDAO
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Recupera el código de envío según el pedido
     *
     * @param int $order_id Id del pedido
     * @return string codigo de envío guardado
     */
    public function getShippingNumberByOrderId($order_id, $fields = null)
    {
        $table='correos_oficial_saved_orders';
        return $this->readRecord($table, "WHERE id_order=$order_id ORDER BY id DESC", $fields!=null?:" exp_number, shipping_number", '');
    }

    /**
     * Obtiene si existe recogida de una devolución
     *
     * @param int $order_id Id del pedido
     * @return array campos devolución
     */
    public function getPickupReturn($order_id)
    {
        $table='correos_oficial_pickups_returns';
        return $this->readRecord($table, "WHERE id_order='$order_id'");
    }
}
