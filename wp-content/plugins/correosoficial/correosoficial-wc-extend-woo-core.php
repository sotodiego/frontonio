<?php
use Automattic\WooCommerce\Blocks\Package;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CartSchema;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CheckoutSchema;

/**
 * Shipping Workshop Extend WC Core.
 */
class CorreosOficial_Wc_Extend_Woo_Core {

	/**
	 * Plugin Identifier, unique to each plugin.
	 *
	 * @var string
	 */
	private $name = 'correosoficial';

	/**
	 * Bootstraps the class and hooks required data.
	 */
	public function init() {
		$this->save_pickup_location();
		// $this->show_shipping_instructions_in_order();
		// $this->show_pickup_location_confirmation();
		// $this->show_shipping_instructions_in_order_email();
	}


	/**
	 * Register shipping workshop schema into the Checkout endpoint.
	 *
	 * @return array Registered schema.
	 */
	public function extend_checkout_schema() {

		return array(
			'selectedPickupLocationOption' => array(
				'description' => 'Pickup location selected by the user',
				'type' => 'object',
				'context' => array( 'view', 'edit' ),
				'readonly' => true,
				'arg_options' => array(
					'validate_callback' => function ( $value ) {
						return true;
					},
				),
			),
			'nifCode' => array(
				'description' => 'Cutomer nif code',
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
				'readonly' => true,
				'arg_options' => array(
					'validate_callback' => function ( $value ) {
						return true;
					},
				),
			),
		);
	}

	/**
	 * Saves the shipping instructions to the order's metadata.
	 *
	 * @return void
	 */
	private function save_pickup_location() {
		add_action(
			'woocommerce_store_api_checkout_update_order_from_request',
			function ( \WC_Order $order, \WP_REST_Request $request ) {

				$id_order = $order->get_id();
				$id_cart = $order->get_cart_hash();

				$nifCode = isset($request['extensions'][$this->name]['nifCode']) ? $request['extensions'][$this->name]['nifCode'] : '';
				$selectedReference = isset($request['extensions'][$this->name]['selectedPickupLocationOption']['reference']) ? $request['extensions'][$this->name]['selectedPickupLocationOption']['reference'] : '';

				if ($order->save()) {

					if ($nifCode) {
						$order->update_meta_data('NIF', $nifCode);
						update_post_meta($id_order, 'NIF', $nifCode);
					}

					if ($selectedReference) {
						$selectedReferenceData = json_encode($request['extensions'][$this->name]['selectedPickupLocationOption']['data']);
						CorreosOficialCheckoutDao::insertReferenceCodeWithOrderId($id_cart, $selectedReference, $selectedReferenceData, $id_order);
					}

				}
			},
			10,
			2
		);
	}

	// /**
	//  * Adds the address on the order confirmation page.
	//  */
	// private function show_pickup_location_confirmation() {

	//  add_action(
	//      'woocommerce_thankyou',
	//      function( int $order_id ) {
	//          $order = wc_get_order( $order_id );
	//          // $shipping_workshop_alternate_shipping_instruction            = $order->get_meta( 'shipping_workshop_alternate_shipping_instruction' );
	//          // $shipping_workshop_alternate_shipping_instruction_other_text = $order->get_meta( 'shipping_workshop_alternate_shipping_instruction_other_text' );

	//          // if ( '' !== $shipping_workshop_alternate_shipping_instruction ) {
	//          //  echo '<h2>' . esc_html__( 'Shipping Instructions', 'shipping-workshop' ) . '</h2>';
	//          //  echo '<p>' . esc_html( $shipping_workshop_alternate_shipping_instruction ) . '</p>';

	//          //  if ( '' !== $shipping_workshop_alternate_shipping_instruction_other_text ) {
	//          //      echo '<p>' . esc_html( $shipping_workshop_alternate_shipping_instruction_other_text ) . '</p>';
	//          //  }
	//          // }

	//      }
	//  );
	// }

	// /**
	//  * Adds the address in the order page in WordPress admin.
	//  */
	// private function show_shipping_instructions_in_order() {
	//  add_action(
	//      'woocommerce_admin_order_data_after_shipping_address',
	//      function( \WC_Order $order ) {
	//          $alternate_shipping_instruction            = $order->get_meta( 'shipping_workshop_alternate_shipping_instruction' );
	//          $alternate_shipping_instruction_other_text = $order->get_meta( 'shipping_workshop_alternate_shipping_instruction_other_text' );

	//          echo '<div>';
	//          echo '<strong>' . esc_html__( 'Shipping Instructions', 'shipping-workshop' ) . '</strong>';
	//          /** 📝 Output the alternate shipping instructions here! */
	//          printf( '<p>%s</p>', esc_html( $alternate_shipping_instruction ) );
	//          if ( 'other' === $alternate_shipping_instruction ) {
	//              printf( '<p>%s</p>', esc_html( $alternate_shipping_instruction_other_text ) );
	//          }
	//          echo '</div>';
	//      }
	//  );
	// }

	// /**
	//  * Adds the address on the order confirmation page.
	//  */
	// private function show_shipping_instructions_in_order_confirmation() {
	//  add_action(
	//      'woocommerce_thankyou',
	//      function( int $order_id ) {
	//          $order = wc_get_order( $order_id );
	//          $shipping_workshop_alternate_shipping_instruction            = $order->get_meta( 'shipping_workshop_alternate_shipping_instruction' );
	//          $shipping_workshop_alternate_shipping_instruction_other_text = $order->get_meta( 'shipping_workshop_alternate_shipping_instruction_other_text' );

	//          if ( '' !== $shipping_workshop_alternate_shipping_instruction ) {
	//              echo '<h2>' . esc_html__( 'Shipping Instructions', 'shipping-workshop' ) . '</h2>';
	//              echo '<p>' . esc_html( $shipping_workshop_alternate_shipping_instruction ) . '</p>';

	//              if ( '' !== $shipping_workshop_alternate_shipping_instruction_other_text ) {
	//                  echo '<p>' . esc_html( $shipping_workshop_alternate_shipping_instruction_other_text ) . '</p>';
	//              }
	//          }
	//      }
	//  );
	// }

	// /**
	//  * Adds the address on the order confirmation email.
	//  */
	// private function show_shipping_instructions_in_order_email() {
	//  add_action(
	//      'woocommerce_email_after_order_table',
	//      function( $order, $sent_to_admin, $plain_text, $email ) {
	//          $shipping_workshop_alternate_shipping_instruction            = $order->get_meta( 'shipping_workshop_alternate_shipping_instruction' );
	//          $shipping_workshop_alternate_shipping_instruction_other_text = $order->get_meta( 'shipping_workshop_alternate_shipping_instruction_other_text' );

	//          if ( '' !== $shipping_workshop_alternate_shipping_instruction ) {
	//              echo '<h2>' . esc_html__( 'Shipping Instructions', 'shipping-workshop' ) . '</h2>';
	//              echo '<p>' . esc_html( $shipping_workshop_alternate_shipping_instruction ) . '</p>';

	//              if ( '' !== $shipping_workshop_alternate_shipping_instruction_other_text ) {
	//                  echo '<p>' . esc_html( $shipping_workshop_alternate_shipping_instruction_other_text ) . '</p>';
	//              }
	//          }
	//      },
	//      10,
	//      4
	//  );
	// }
}
