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

require_once dirname(__FILE__).'/../../ecommerce_common_lib/Dao/CorreosOficialDAO.php';

class CorreosOficialConfigDao extends CorreosOficialDao {

    private $dao;

    public static function getConfig()
    {
        // as_array=true para que se devuelva la salida como un array
        return (new CorreosOficialConfigDao)->getRecords('correos_oficial_configuration', true);
    }

    public static function getLabelAlternartiveText(){

        $result=self::getConfigValue('CustomerAlternativeText');
 
         if ($result == 'on'){
             return self::getConfigValue('LabelAlternativeText');
         }
         else {
             return false;
         }
     }   

    public static function getDefaultCustomsDescription(){
        $query = "SELECT code,description FROM ".CorreosOficialUtils::getPrefix()."correos_oficial_customs_description";
       
        $dao = new CorreosOficialDAO();
        $result = $dao->getRecords('correos_oficial_customs_description', true);

        
        $array_desc = array();
        foreach ($result as $desc) {
            $array_desc[$desc['code']] = $desc['description'];
        }
        return $array_desc;
    }

    public static function checkDimensionsByDefaultActivated() 
    {
		return (self::getConfigValue('ActivateDimensionsByDefault') == 'on' ||
            ((int)self::getConfigValue('DimensionsByDefaultHeight') > 0 &&
            (int)self::getConfigValue('DimensionsByDefaultLarge')  > 0 &&
            (int)self::getConfigValue('DimensionsByDefaultWidth')  > 0)) 
        ? true : false;
    }

}
