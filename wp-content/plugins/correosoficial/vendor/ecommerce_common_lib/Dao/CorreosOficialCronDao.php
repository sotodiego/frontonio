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

/**
 * Dao para el CRON
 */
class CorreosOficialCronDao extends CorreosOficialDAO
{

    public function __construct()
    {
       parent::__construct();
    }

    public function updateFieldsSetRecord($fields)
    {
        foreach ($fields as $field => $value) {
            $this->createSettingRecord($field, $value, 'correos_oficial_configuration');
        }
    }

    public function getOrderIdByShippingNumberDao($tracking_number)
    {
        $query = "SELECT coo.id_order FROM " . CorreosOficialUtils::getPrefix() . 'correos_oficial_orders' .
            " as coo JOIN " . CorreosOficialUtils::getPrefix() . 'correos_oficial_saved_orders' .
            " as cos on coo.shipping_number=cos.exp_number WHERE cos.shipping_number= '$tracking_number' or cos.exp_number='$tracking_number'";

        $record=$this->getRecordsWithQuery($query);
        return $record[0]->id_order;
    }
    
    public function getOrdersForTrackingDao($query)
    {
        return $this->getRecordsWithQuery($query, true);
    }

    public function changeShippingStatusDao($table, $record, $where)
    {
       return $this->updateRecord($table, $record, $where);
    }

    public function getStatus($event)
    {
        $table ='correos_oficial_ws_status';
        $record = $this->readRecord($table, " WHERE event='$event'", 'description');

        if (!$record) {
            return false;
        }
        return $record[0]->description;
    }

}
