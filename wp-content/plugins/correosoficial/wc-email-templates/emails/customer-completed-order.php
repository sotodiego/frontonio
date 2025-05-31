<?php
/*
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
 *
 *
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname(__DIR__) . '/../classes/CorreosOficialOrders.php';

/**
 * WC_Emails
 *
 * @hooked WC_Emails::email_header() Output the email header
 * @since 2.5.0
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php /* translators: %s: Customer first name */  printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php esc_html_e( 'We have finished processing your order.', 'woocommerce' ); ?></p>
<?php
	$correosOrder = CorreosOficialOrders::getCorreosOrder((int) $order->get_id());

if (!empty($correosOrder)) {
	if ($correosOrder['carrier_type'] === 'CEX') {
		$segUrl = str_replace('=@', '=' . $correosOrder['shipping_number'], $correosOrder['url']);
	} else {
		$shippingNumber = CorreosOficialOrders::getCorreosPackages((int) $order->get_id());
		$segUrl = str_replace('=@', '=' . $shippingNumber[0]['shipping_number'], $correosOrder['url']);
	}
		
	unset($shippingNumber, $correosOrder);
	?>
	<p> <?php esc_html_e( 'Track your order in any moment with this link:', 'correosoficial' ); ?> </p>
	<p> <?php echo '<a href="' . esc_url($segUrl) . '" target="_blank">' . esc_html($segUrl) . '</a>'; ?> </p>
<?php
}

	/**
	 * WC_Emails
	 *
	 * @hooked WC_Emails::order_details() Shows the order details table.
	 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
	 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
	 * @since 2.5.0
	 */
	do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

	/**
	 * WC_Emails
	 *
	 * @hooked WC_Emails::order_meta() Shows order meta data.
	 * @since 2.5.0
	 */
	do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

	/**
	 * WC_Emails
	 *
	 * @hooked WC_Emails::customer_details() Shows customer details
	 * @hooked WC_Emails::email_address() Shows email address
	 * @since 2.5.0
	 */
	do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

	/**
	 * Show user-defined additional content - this is set in each email's settings.
	 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

	/**
	 * WC_Emails
	 *
	 * @hooked WC_Emails::email_footer() Output the email footer
	 * @since 2.5.0
	 */
	do_action( 'woocommerce_email_footer', $email );
