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

/**
 * Clase referente a la Tramitación Aduanera en Ajustes.
 * Se recuperan datos que no están en la tabla de correos_oficial_configuration
 */
class CustomsProcessingDao extends CorreosOficialDao {
    
    /**
     * Asigna la descripción tarifaria por defecto
     * @return array_desc array con la descripción
     */
    public static function getDefaultCustomsDescription(){
        
        $dao = new CorreosOficialDao();
        $records = $dao->readRecord('correos_oficial_customs_description', " ORDER BY code ASC", 
            "code, description");

        $array_desc = array();

        foreach ($records as $desc){
            $array_desc[$desc->code] = $desc->description;
        }
        return $array_desc;

    }
    
}
