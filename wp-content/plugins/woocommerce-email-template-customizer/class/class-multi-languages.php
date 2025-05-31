<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class VIWEC_Multi_Languages {
	public $cache = [];

	public function __construct() {
		add_filter( 'viwec_woocommerce_order_item_get_product', array( $this, 'woocommerce_order_item_get_product' ), 10, 2 );
	}
	/**
	 * @param $product WC_Product
	 * @param $order_item WC_Order_Item_Product
	 *
	 */
	public function woocommerce_order_item_get_product($product, $order_item){
		$order_id = $order_item->get_order_id();
		$order = wc_get_order($order_id);
		$order_language = $order ? $this->get_order_language($order) : '';
		return $this->get_product_language($product,$order_language);
	}

	public function print_default_country_flag() {
		$languages        = $this->get_languages();
		$languages_data   = $this->get_languages_data();
		$default_language = $this->get_default_language();
		if ( count( $languages ) ) {
			?>
            <p>
                <label><?php
					if ( isset( $languages_data[ $default_language ]['country_flag_url'] ) && $languages_data[ $default_language ]['country_flag_url'] ) {
						?>
                        <img src="<?php echo esc_url( $languages_data[ $default_language ]['country_flag_url'] ); ?>">
						<?php
					}
					echo esc_html( $default_language );
					if ( isset( $languages_data[ $default_language ]['translated_name'] ) ) {
						echo esc_html( '(' . $languages_data[ $default_language ]['translated_name'] . '):' );
					}
					?></label>
            </p>
			<?php
		}
	}

	public function print_other_country_flag( $param, $lang, $tag = 'p', $echo_lang = true, $echo = true ) {
		if ( ! $lang ) {
			return '';
		}
		$languages_data = $this->get_languages_data();
		if ( ! $echo ) {
			ob_start();
		}
		printf( '<%s>', esc_attr( $tag ) );
		?>
        <label for="<?php echo esc_attr( "{$param}_{$lang}" ); ?>"><?php
			if ( ! empty( $languages_data[ $lang ]['country_flag_url'] ) ) {
				?>
                <img src="<?php echo esc_url( $languages_data[ $lang ]['country_flag_url'] ); ?>">
				<?php
			}
			if ( $echo_lang ) {
				echo wp_kses_post( $lang );
				if ( isset( $anguages_data[ $lang ]['translated_name'] ) ) {
					echo wp_kses_post( '(' . $languages_data[ $lang ]['translated_name'] . ')' );
				}
				echo esc_html( ' : ' );
			}
			?></label>
		<?php
		printf( '</%s>', esc_attr( $tag ) );
		if ( ! $echo ) {
			return ob_get_clean();
		}
	}

	public function get_languages() {
		if ( isset( $this->cache['languages'] ) ) {
			return $this->cache['languages'];
		}
		$default_language = $this->get_default_language();
		$languages_data   = $this->get_languages_data();
		$languages        = [];
		if ( is_array( $languages_data ) && count( $languages_data ) ) {
			foreach ( $languages_data as $key => $language ) {
				if ( $key != $default_language ) {
					$languages[] = $key;
				}
			}
		}
		$this->cache['languages'] = $languages;

		return $this->cache['languages'];
	}

	abstract protected function get_product_language($product, $language);
	abstract protected function get_order_language($order);
	abstract protected function get_languages_data();

	abstract protected function get_current_language();

	abstract protected function get_default_language();
}