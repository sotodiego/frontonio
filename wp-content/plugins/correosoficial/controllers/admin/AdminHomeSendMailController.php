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

if (!defined('WC_VERSION')) {
	die;
}

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/config.inc.php';
require_once __DIR__ . '/../../vendor/ecommerce_common_lib/SendEmail.inc.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/config.inc.php';

require_once __DIR__ . '/../../vendor/ecommerce_common_lib/Commons/Normalization.php';

class AdminHomeSendMailController {

	public function __construct() {
		global $co_signup_customers_from;
		global $co_signup_customers_cc;

		$this->bootstrap = true;
		$this->display = 'view';

		$inputCompany = Normalization::normalizeData('input_company');
		$inputCif = Normalization::normalizeData('input_cif');
		$inputContactName = Normalization::normalizeData('input_contact_name');
		$inputPhoneMobile = Normalization::normalizeData('input_mobile_phone');
		$inputPhone = Normalization::normalizeData('input_phone');
		$inputEmail = Normalization::normalizeData('input_email');
		$productCategory = Normalization::normalizeData('product_category');

		$platform_and_version = PLATFORM_AND_VERSION;

		$body_to_Customer =
		__('Dear Customer, we have receive your request from module CorreosOficial for ', 'correosoficial') . __PLATFORM__ . ".\r\n\r\n" .
		__('We will contact you as soon as possible.', 'correosoficial') . "\r\n\r\n" .
			"$inputCompany\r\n" .
			"$inputCif\r\n" .
			"$inputContactName\r\n" .
			"$inputPhoneMobile\r\n" .
			"$inputPhone\r\n" .
			"$inputEmail\r\n" .
			"$productCategory\r\n\r\n" .
			"--\r\n\r\nMódulo E-COMMERCE CorreosOficial\r\n\r\n";

		$body_to_CorreosGroup =
			'Se ha recibido una solicitud desde ' . $platform_and_version . ": \r\n\r\n" .
			"Compañía: $inputCompany\r\n" .
			"CIF: $inputCif\r\n" .
			"Persona de contacto: $inputContactName\r\n" .
			"Teléfono Móvil: $inputPhoneMobile\r\n" .
			"Teléfono fijo: $inputPhone\r\n" .
			"Email: $inputEmail\r\n" .
			"Categoría de producto: $productCategory\r\n\r\n" .
			"--\r\n\r\nMódulo E-COMMERCE CorreosOficial\r\n\r\n";

		// Email al cliente
		$result1 = $this->SendEMail(
			$inputEmail, __('Sign up in CorreosOficial: You will receive an answer soon', 'correosoficial'),
			$body_to_Customer, $co_signup_customers_from
		);

		// Email a Grupo Correos
		$result2 = $this->SendEMail(
			$co_signup_customers_cc, __('New lead from CorreosOficial E-COMMERCE: ', 'correosoficial') . $platform_and_version,
			$body_to_CorreosGroup, $co_signup_customers_from, $co_signup_customers_cc
		);
		$result = array( $result1, $result2 );
		CorreosOficialUtils::varDump('ENVIO DE CORREO', $result);
		die(esc_html($result1));
	}

	public function SendEMail( $email, $subject, $message, $from, $cc = null ) {
		$mail = new SendMail($email, $subject, $message, $from, $cc);
		return $mail->sendEmail();
	}
}
