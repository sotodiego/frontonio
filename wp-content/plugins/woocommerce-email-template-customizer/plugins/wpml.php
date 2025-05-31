<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class VIWEC_Plugins_Wpml extends VIWEC_Multi_Languages {
	public function __construct() {
		if ( ! is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			return;
		}
        parent::__construct();
	}
	/**
	 * @param $product WC_Product
	 *
	 */
	public function get_product_language($product, $language){
		if ($language){
			$product_id = $product->get_id();
			$language_object_id = apply_filters( 'wpml_object_id', $product_id, 'product', false, $language );
			if ($language_object_id != $product_id){
				$language_object = wc_get_product($language_object_id);
			}
			if (!empty($language_object)){
				$product = $language_object;
			}else {
				$product->viwec_product_permalink = apply_filters( 'wpml_permalink', $product->get_permalink(), $language );
			}
		}
		return $product;
	}
	/**
	 * @param $order WC_Order
	 *
	 */
	public function get_order_language($order){
		$order_id = $order->get_id();
		if (isset($this->cache['order_language'][$order_id])){
			return $this->cache['order_language'][$order_id];
		}
		if (!isset($this->cache['order_language'])){
			$this->cache['order_language'] =[];
		}
		$this->cache['order_language'][$order_id] = $order->get_meta('wpml_language',true);
		return $this->cache['order_language'][$order_id];
	}
	public function get_current_language() {
		if ( isset( $this->cache['current_language'] ) ) {
			return $this->cache['current_language'];
		}
		global $sitepress;
		$current_language = $sitepress->get_current_language();
        if ($current_language == $this->get_default_language()){
            $current_language ='';
        }
		$this->cache['current_language'] = $current_language;
		return $this->cache['current_language'];
	}
	public function get_default_language() {
		if ( isset( $this->cache['default_language']) ) {
			return $this->cache['default_language'];
		}
		global $sitepress;
		$this->cache['default_language'] = $sitepress->get_default_language();
		return $this->cache['default_language'];
	}
	public function get_languages_data() {
		if ( isset( $this->cache['languages_data'] ) ) {
			return $this->cache['languages_data'];
		}
		$this->cache['languages_data'] = icl_get_languages( 'skip_missing=N&orderby=KEY&order=DIR&link_empty_to=str' );
		return $this->cache['languages_data'];
	}
}