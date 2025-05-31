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

class CorreosOficialUserConfigurationDao extends CorreosOficialDAO
{

    public function __construct()
    {
        parent::__construct();
    }

    public function updateFieldsSetRecord($fields)
    {

        foreach ($fields as $field => $value) {

            if ($field == "ActivateNifFieldCheckout") {
                // Fijamos el tipo de dato en DB cuando es un checkbox | radio
                $this->createSettingRecord($field, $value, 'correos_oficial_configuration', 'checkbox');
            } else {
                // Resto de casos, el tipo es text
                $this->createSettingRecord($field, $value, 'correos_oficial_configuration');
            }
        }
    }

    public function getField($field)
    {
        return $this->readSettings($field);
    }

    public function getStatus($id_lang)
    {
        $query = "SELECT id_order_state, name FROM " . CorreosOficialUtils::getPrefix() . "order_state_lang WHERE id_lang = '$id_lang'";
        return $this->getRecordsWithQuery($query, true);
    }
}
