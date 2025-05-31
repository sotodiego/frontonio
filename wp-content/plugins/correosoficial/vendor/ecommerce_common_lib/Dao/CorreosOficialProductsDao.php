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
 * Clase CorreosOficialProductsDao
 */

require_once dirname(__FILE__).'/../../ecommerce_common_lib/Dao/CorreosOficialDAO.php';

class CorreosOficialProductsDao extends CorreosOficialDAO {

	public $id;
	public $name;
	public $product_type;

   public function __construct() {
      parent::__construct();
   }

   public function updateProducts($products){
      $this->updateProductsDao($products);
   }
   public function resetProducts(){
      $this->resetProductsDao();
   }

   public function getProduct($id, $table){
      return $this->readRecord($table, "WHERE id=$id");
   }

   public function getActiveProducts($where){
      return $this->getActiveProductsDao($where);
   }
}
