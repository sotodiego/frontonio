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

require_once __DIR__ . '/../vendor/ecommerce_common_lib/Dao/CorreosOficialDAO.php';

/**
 * Clase referente a las zonas de Woocommerce.
 */
class CorreosOficialCustomerWC extends CorreosOficialDAO {


	public $email;

	public function __construct( $order ) {
		parent::__construct();
		$this->email = $order->get_billing_email();
	}
}
