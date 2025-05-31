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

class CorreosOficialCustomsProcessingDao extends CorreosOficialDao {
   private $dao;

   public function __construct() {
      $this->dao = new CorreosOficialDAO();
   }
   public function updateFieldsSetRecord($fields){
      
      foreach ($fields as $field => $value) {
        $this->dao->createSettingRecord($field, $value, 'correos_oficial_configuration');
      }
   }

   public function getField($field){
        return $this->dao->readSettings($field);
   }
}   