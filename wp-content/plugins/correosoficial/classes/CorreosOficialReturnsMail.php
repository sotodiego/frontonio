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

require_once __DIR__ . '/../vendor/ecommerce_common_lib/SendEmail.inc.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/CorreosOficialUtils.php';
require_once __DIR__ . '/../vendor/ecommerce_common_lib/CorreosOficialSmarty.php';

require_once __DIR__ . '/../vendor/smarty/Smarty.class.php';

class CorreosOficialReturnsMail {

	private $smarty;
	private $customer_email;
	private $sender_email = '';
	private $label = '';
	private $cn23 = '';
	private $company = '';
	private $text_body = '';
	private $subject = '';
	private $shipping_number;
	private $pickup_date;
	private $sender_from_time;
	private $pickup_number;
	private $order_id;
	private $shop_name;

	public function __construct( $returns_data ) {
		$this->customer_email = $returns_data['customer_email'];
		$this->sender_email = $returns_data['sender_email'];
		$this->label = $returns_data['label'];
		$this->cn23 = $returns_data['cn23'];
		$this->company = $returns_data['company'];
		$this->shipping_number = $returns_data['shipping_number'];
		$this->pickup_date = $returns_data['pickup_date'];
		$this->sender_from_time = $returns_data['sender_from_time'];
		$this->pickup_number = isset($returns_data['pickup_number']) ? $returns_data['pickup_number'] : '';
		$this->order_id = $returns_data['order_id'];
		$this->shop_name = $returns_data['shop_name'];
		$this->return_code_cex = $returns_data['return_code'];

		$this->subject = '[' . $this->shop_name . '] ' . __('Return package information', 'correosoficial') .
		' - ' . __('Order: ', 'correosoficial') . $this->order_id;

		$this->text_body = $this->prepareEmail();
	}

	public function sendEmail() {
		global $co_signup_customers_from;

		$body = $this->getBody($this->text_body, $this->label, $this->cn23, $this->shipping_number);

		$sendMail = new SendMail($this->customer_email, mb_encode_mimeheader($this->subject), $body, $this->sender_email, null, 'multipart');
		// Email al cliente
		$result = $sendMail->SendEmail();

		CorreosOficialUtils::varDump('ENVIO DE CORREO', $result);
		return $result;
	}

	public function prepareEmail() {
		$this->smarty = CorreosOficialSmarty::loadSmartyInstance();

		// Variables comunes del mensaje
		$recipient_return_hello = __('Hello', 'correosoficial');
		$recipient_return_thanks = __('Thank you', 'correosoficial');
		$recipient_return_bye = __('Sincerely', 'correosoficial');
		$recipient_return_footer = $this->shop_name;

		// Recipient Return variables Correos
		$recipient_return_doc_cn23 = __('CN23/CP71 Documentation', 'correosoficial');
		$recipient_return_text1 = __('Attached is a label that you can print out and attach to the package. If you prefer, you can write down the package code', 'correosoficial');
		$recipient_return_text2 = __('and provide it at your nearest post office or contact us to arrange collection. If in this email you receive the', 'correosoficial');
		$recipient_return_text3 = __('must accompany the shipment printed and signed by you', 'correosoficial');

		// Recipient Return variables Cex
		$recipient_return_pickup_date = $this->pickup_date;
		$recipient_return_pickup_time = $this->sender_from_time;
		$recipient_return_shop_name = $this->shop_name;
		$recipient_return_text1_cex = __('We would like to inform you that the', 'correosoficial');
		$recipient_return_text2_cex = __('from the', 'correosoficial');
		$recipient_return_text3_cex = __('Correos Express will proceed to carry out a collection requested by', 'correosoficial');
		$recipient_return_text4_cex = __('we kindly ask you, in order to avoid any unnecessary delays please have the shipment ready before the driver picks it up. Please find enclosed the label to be printed and attached to the package', 'correosoficial');
		$recipient_return_text5_cex = __('Once the shipment is made, you can track it using the following code:', 'correosoficial');
		$recipient_return_text6_cex = __('at', 'correosoficial');
		$recipient_return_text7_cex = __('Track your shipping - correosexpress.com', 'correosoficial');

		$recipient_return_recommendations = __('RECOMMENDATIONS', 'correosoficial');
		$recipient_return_recommendation_info = __('In order to ensure that the service is performed correctly and that your shipments are not delayed, we recommend that you', 'correosoficial');
		$recipient_return_recommendation1 = __('Have prepared any documentation accompanying the goods', 'correosoficial');
		$recipient_return_recommendation2 = __('The goods must be perfectly closed and sealed before the indicated collection time', 'correosoficial');
		$recipient_return_recommendation3 = __('On the outside of the box, in a visible place, attach the label included in this mailing', 'correosoficial');

		$this->smarty->assign('sender_email', $this->sender_email);
		$this->smarty->assign('shop_name', $this->shop_name);
		// Asignación de literales del cuerpo del email de devoluciones
		$this->smarty->assign('recipient_return_hello', $recipient_return_hello);
		$this->smarty->assign('recipient_return_doc_cn23', $recipient_return_doc_cn23);
		$this->smarty->assign('recipient_return_text1', $recipient_return_text1);
		$this->smarty->assign('recipient_return_text2', $recipient_return_text2);
		$this->smarty->assign('recipient_return_text3', $recipient_return_text3);
		$this->smarty->assign('recipient_return_thanks', $recipient_return_thanks);
		$this->smarty->assign('recipient_return_bye', $recipient_return_bye);
		$this->smarty->assign('recipient_return_footer', $recipient_return_footer);

		$this->smarty->assign('recipient_return_pickup_date', $recipient_return_pickup_date);
		$this->smarty->assign('recipient_return_pickup_time', $recipient_return_pickup_time);
		$this->smarty->assign('recipient_return_shop_name', $recipient_return_shop_name);
		$this->smarty->assign('recipient_return_text1_cex', $recipient_return_text1_cex);
		$this->smarty->assign('recipient_return_text2_cex', $recipient_return_text2_cex);
		$this->smarty->assign('recipient_return_text3_cex', $recipient_return_text3_cex);
		$this->smarty->assign('recipient_return_text4_cex', $recipient_return_text4_cex);
		$this->smarty->assign('recipient_return_text5_cex', $recipient_return_text5_cex);
		$this->smarty->assign('recipient_return_text6_cex', $recipient_return_text6_cex);
		$this->smarty->assign('recipient_return_text7_cex', $recipient_return_text7_cex);
		$this->smarty->assign('return_code_cex', $this->return_code_cex);
		$this->smarty->assign('recipient_return_recommendations', $recipient_return_recommendations);
		$this->smarty->assign('recipient_return_recommendation_info', $recipient_return_recommendation_info);
		$this->smarty->assign('recipient_return_recommendation1', $recipient_return_recommendation1);
		$this->smarty->assign('recipient_return_recommendation2', $recipient_return_recommendation2);
		$this->smarty->assign('recipient_return_recommendation3', $recipient_return_recommendation3);

		$this->smarty->assign('shipping_number', $this->shipping_number);

		$this->smarty->assign('company', $this->company);

		return $this->smarty->fetch(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/views/templates/mails/returns/to_recipient.tpl');
	}

	public function concatenateArrayToString( $array ) {
		return implode('_', $array);
	}

	public function getBody( $text_body, $label, $cn23, $shipping_number ) {
		$content1 = file_get_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/views/templates/mails/returns/partial_label.tpl');
		$content2 = file_get_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/views/templates/mails/returns/partial_cn23.tpl');
		$content3 = file_get_contents(get_real_path(MODULE_CORREOS_OFICIAL_PATH) . '/views/templates/mails/returns/partial_last.tpl');

		$label = $this->concatenateArrayToString($label);
		if (null == $cn23) {
			$content = $content1 . $content3;
			$body = sprintf($content, $text_body, $shipping_number, $shipping_number, $label);
		} else {
			$content = $content1 . $content2 . $content3;
			$body = sprintf($content, $text_body, $shipping_number, $shipping_number, $label, $shipping_number, $shipping_number, $cn23);
		}

		return CorreosOficialUtils::replaceCharacterWithEntities($body);
	}
}
